<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;

/**
 * Command: php artisan sync:student-fee-summary
 *
 * Tính toán và lưu số phí còn lại theo từng hợp đồng (contract) cho từng học sinh
 * vào bảng report_student_fee_summary.
 *
 * Logic tính left_amount và done_sessions:
 *  - Nếu contract có class_id và start_date:
 *      done_sessions = số ngày học từ start_date đến hôm qua (trừ ngày lễ và bảo lưu)
 *      left_amount   = ceil((total_charged / real_sessions) * (real_sessions - done_sessions))
 *  - Nếu contract chưa xếp lớp:
 *      done_sessions = 0, left_amount = total_charged
 *
 * Điều kiện: contracts.type > 0 AND contracts.status IN (2,3,4,5,6)
 */
class SyncStudentFeeSummary extends Command
{
    protected $signature = 'sync:student-fee-summary
                            {--branch= : Chỉ sync theo branch_id cụ thể}
                            {--truncate : Xóa dữ liệu cũ trước khi insert}
                            {--chunk=100 : Số bản ghi xử lý mỗi lần (mặc định: 100)}
                            {--memory=512M : Giới hạn bộ nhớ PHP}';

    protected $description = 'Sync dữ liệu thống kê phí còn lại từng học sinh vào bảng report_student_fee_summary';

    /** @var array Cache ngày lễ theo key "branch_id_product_id" — lưu dạng ['Y-m-d'=>true] */
    private $holidayCache = [];

    /** @var array Cache class_days theo class_id */
    private $classDaysCache = [];

    public function handle()
    {
        $memoryLimit = $this->option('memory') ?: '512M';
        ini_set('memory_limit', $memoryLimit);

        $this->info('[SyncStudentFeeSummary] Bắt đầu... (memory_limit=' . $memoryLimit . ')');
        $startTime = microtime(true);

        $branchFilter = $this->option('branch');
        $doTruncate   = $this->option('truncate');
        $chunkSize    = max(1, (int) ($this->option('chunk') ?: 100));

        $branchCondition = $branchFilter ? 'AND c.branch_id = ' . (int) $branchFilter : '';

        if ($doTruncate) {
            $this->info('[SyncStudentFeeSummary] Xóa dữ liệu cũ...');
            if ($branchFilter) {
                DB::table('report_student_fee_summary')->where('branch_id', (int) $branchFilter)->delete();
            } else {
                DB::table('report_student_fee_summary')->truncate();
            }
        }

        // Đếm tổng
        $countRow = DB::selectOne(
            "SELECT COUNT(c.id) AS total FROM contracts c WHERE c.type > 0 AND c.status IN (2,3,4,5,6) $branchCondition"
        );
        $total = $countRow ? (int) $countRow->total : 0;
        $this->info("[SyncStudentFeeSummary] Tổng hợp đồng: {$total}");

        if ($total === 0) {
            $this->info('Không có dữ liệu. Kết thúc.');
            return 0;
        }

        $today     = date('Y-m-d', strtotime('-1 days'));
        $now       = date('Y-m-d H:i:s');
        $processed = 0;
        $inserted  = 0;
        $offset    = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        do {
            $sql = "
                SELECT
                    c.id                        AS contract_id,
                    c.student_id,
                    c.branch_id,
                    c.must_charge,
                    c.debt_amount,
                    c.summary_sessions,
                    c.real_sessions,
                    IF(c.debt_amount > 0, 0, c.bonus_sessions) AS bonus_sessions,
                    c.class_id,
                    c.total_charged,
                    IF(c.class_id IS NULL OR c.class_id = 0, c.start_date, c.enrolment_start_date) AS start_date,
                    c.product_id
                FROM contracts c
                WHERE c.type > 0
                  AND c.status IN (2, 3, 4, 5, 6)
                  $branchCondition
                ORDER BY c.id ASC
                LIMIT $chunkSize OFFSET $offset
            ";

            $contracts = DB::select($sql);

            if (empty($contracts)) {
                break;
            }

            $batchInsert = [];

            foreach ($contracts as $contract) {
                $doneSessions = 0;
                $leftAmount   = 0;

                if ($contract->class_id && (int) $contract->class_id > 0 && $contract->start_date) {
                    // Lấy ngày lễ (hash map để lookup O(1))
                    $holidayMap    = $this->getHolidayMap((int) $contract->branch_id, (int) $contract->product_id);
                    // Lấy reserved dates của contract này (thêm vào holiday map)
                    $reservedMap   = $this->getReservedDateMap((int) $contract->contract_id);
                    $mergedHoliday = $holidayMap + $reservedMap; // union, key = 'Y-m-d'

                    // Lấy class_days (array các số thứ trong tuần: 0=CN,1=T2,...,6=T7)
                    $classDays = $this->getClassDays((int) $contract->class_id, (int) $contract->product_id);

                    // Tính done_sessions bằng PHP thuần (không dùng Moment library)
                    $doneSessionsRaw = $this->calcDoneSessions(
                        $contract->start_date,
                        $today,
                        $classDays,
                        $mergedHoliday
                    );

                    $doneSessions = $contract->real_sessions > $doneSessionsRaw
                        ? $doneSessionsRaw
                        : (int) $contract->real_sessions;

                    $sessionsLeft = $contract->real_sessions > $doneSessionsRaw
                        ? ($contract->real_sessions - $doneSessionsRaw)
                        : 0;

                    $leftAmount = $contract->real_sessions
                        ? (int) ceil(($contract->total_charged / $contract->real_sessions) * $sessionsLeft)
                        : 0;
                } else {
                    $doneSessions = 0;
                    $leftAmount   = (int) $contract->total_charged;
                }

                $batchInsert[] = [
                    'student_id'       => (int) $contract->student_id,
                    'contract_id'      => (int) $contract->contract_id,
                    'must_charge'      => (int) $contract->must_charge,
                    'branch_id'        => (int) $contract->branch_id,
                    'debt_amount'      => (int) $contract->debt_amount,
                    'summary_sessions' => (int) $contract->summary_sessions,
                    'real_sessions'    => (int) $contract->real_sessions,
                    'bonus_sessions'   => (int) $contract->bonus_sessions,
                    'class_id'         => ($contract->class_id && (int) $contract->class_id > 0)
                                            ? (int) $contract->class_id : null,
                    'done_sessions'    => $doneSessions,
                    'left_amount'      => $leftAmount,
                    'created_at'       => $now,
                ];

                $processed++;
                $bar->advance();
            }

            if (!empty($batchInsert)) {
                $this->upsertBatch($batchInsert);
                $inserted += count($batchInsert);
            }

            $offset += $chunkSize;

            // Giải phóng bộ nhớ
            unset($contracts, $batchInsert);
            gc_collect_cycles();

        } while (true);

        $bar->finish();
        $this->line('');

        $elapsed = round(microtime(true) - $startTime, 2);
        $this->info("[SyncStudentFeeSummary] Hoàn thành! Xử lý: {$processed} | Insert/update: {$inserted} | Thời gian: {$elapsed}s");

        return 0;
    }

    // -----------------------------------------------------------------------
    // Core: Tính số buổi đã học thuần PHP (không dùng Moment library)
    // -----------------------------------------------------------------------

    /**
     * Đếm số ngày học từ $start đến $end (inclusive cả 2 đầu).
     * Chỉ tính những ngày trong $classDays (weekday number: 0=CN,1=T2,...,6=T7)
     * và KHÔNG nằm trong $holidayMap (key = 'Y-m-d').
     *
     * Dùng PHP DateTime + DateInterval thuần — không dùng Moment library.
     */
    private function calcDoneSessions(string $start, string $end, array $classDays, array $holidayMap): int
    {
        if (empty($classDays) || !strtotime($start) || !strtotime($end)) {
            return 0;
        }

        // Nếu start > end thì chưa học buổi nào
        if ($start > $end) {
            return 0;
        }

        // Chuyển classDays về dạng PHP (0=CN,1=T2,...,6=T7) — date('w')
        // Moment weekday: 1=T2,...,6=T7,7=CN  →  PHP date('w'): 0=CN,1=T2,...,6=T7
        // Mapping từ Moment sang PHP w:
        $classDaysPHP = [];
        foreach ($classDays as $d) {
            $d = (int) $d;
            // Moment: 1-7 (1=Mon,7=Sun) → PHP date('w'): 1=Mon,...,6=Sat,0=Sun
            if ($d === 7) {
                $classDaysPHP[] = 0; // CN
            } else {
                $classDaysPHP[] = $d; // T2-T7 đều khớp
            }
        }
        $classDaysPHP = array_unique($classDaysPHP);

        $count   = 0;
        $current = new \DateTime($start);
        $endDt   = new \DateTime($end);
        $oneDay  = new \DateInterval('P1D');

        while ($current <= $endDt) {
            $dateStr = $current->format('Y-m-d');
            $weekday = (int) $current->format('w'); // 0=CN,1=T2,...,6=T7

            if (in_array($weekday, $classDaysPHP) && !isset($holidayMap[$dateStr])) {
                $count++;
            }

            $current->add($oneDay);
        }

        return $count;
    }

    // -----------------------------------------------------------------------
    // Helpers - Holiday
    // -----------------------------------------------------------------------

    /**
     * Lấy holiday map theo branch_id + product_id.
     * Trả về hash ['Y-m-d' => true] để lookup O(1).
     * Cache để tránh query lại cho cùng branch+product.
     */
    private function getHolidayMap(int $branchId, int $productId): array
    {
        $key = "{$branchId}_{$productId}";
        if (!isset($this->holidayCache[$key])) {
            // Dùng trực tiếp query thay vì u::getPublicHolidays (tránh Moment)
            $rows = DB::select("
                SELECT p.start_date, p.end_date
                FROM public_holiday p
                WHERE p.status = 1
                  AND p.zone_id IN (SELECT zone_id FROM branches WHERE id = ?)
            ", [$branchId]);

            $map = [];
            foreach ($rows as $row) {
                $s = new \DateTime($row->start_date);
                $e = new \DateTime($row->end_date);
                $oneDay = new \DateInterval('P1D');
                while ($s <= $e) {
                    $map[$s->format('Y-m-d')] = true;
                    $s->add($oneDay);
                }
            }
            $this->holidayCache[$key] = $map;
        }

        return $this->holidayCache[$key];
    }

    /**
     * Lấy reserved date map cho 1 contract.
     * Trả về hash ['Y-m-d' => true].
     */
    private function getReservedDateMap(int $contractId): array
    {
        $rows = DB::select(
            "SELECT start_date, end_date FROM reserves WHERE status = 2 AND contract_id = ?",
            [$contractId]
        );

        $map = [];
        foreach ($rows as $row) {
            $s = new \DateTime($row->start_date);
            $e = new \DateTime($row->end_date);
            $oneDay = new \DateInterval('P1D');
            while ($s <= $e) {
                $map[$s->format('Y-m-d')] = true;
                $s->add($oneDay);
            }
        }

        return $map;
    }

    // -----------------------------------------------------------------------
    // Helpers - Class Days
    // -----------------------------------------------------------------------

    /**
     * Lấy class_days từ bảng sessions (cột class_day).
     * Cache theo class_id.
     */
    private function getClassDays(int $classId, int $productId): array
    {
        if (!isset($this->classDaysCache[$classId])) {
            $rows = DB::select(
                "SELECT DISTINCT class_day FROM sessions WHERE class_id = ? AND class_day IS NOT NULL",
                [$classId]
            );
            if (!empty($rows)) {
                $days = [];
                foreach ($rows as $r) {
                    $days[] = (int) $r->class_day;
                }
                $this->classDaysCache[$classId] = $days;
            } else {
                // Default: [2] = Thứ 3 (Moment weekday 2 = T3)
                $this->classDaysCache[$classId] = u::getDefaultClassDays($productId);
            }
        }
        return $this->classDaysCache[$classId];
    }

    // -----------------------------------------------------------------------
    // Helpers - DB
    // -----------------------------------------------------------------------

    /**
     * Upsert batch: xóa contract_ids cũ rồi insert mới.
     */
    private function upsertBatch(array $rows)
    {
        if (empty($rows)) {
            return;
        }
        $contractIds = array_column($rows, 'contract_id');
        DB::table('report_student_fee_summary')
            ->whereIn('contract_id', $contractIds)
            ->delete();

        DB::table('report_student_fee_summary')->insert($rows);
    }
}
