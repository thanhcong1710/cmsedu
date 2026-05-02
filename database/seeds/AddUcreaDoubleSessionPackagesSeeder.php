<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddUcreaDoubleSessionPackagesSeeder extends Seeder
{
    /**
     * Run the database seeds to add 2-session/week packages for UCREA.
     *
     * @return void
     */
    public function run()
    {
        $productId = 1; // UCREA
        $targetMonths = [2, 3, 5, 6, 10];
        $now = Carbon::now()->format('Y-m-d H:i:s');

        // Tìm gói 12 tháng đang active để lấy relations
        $twelveMonthPkg = DB::table('tuition_fee')
            ->where('product_id', $productId)
            ->where('number_of_months', 12)
            ->where('status', '>', 0)
            ->where('expired_date', '>', $now)
            ->orderBy('id', 'desc')
            ->first();

        foreach ($targetMonths as $months) {
            // Tìm gói chuẩn (1 buổi/tuần) vừa tạo ở bước trước
            $standardPkg = DB::table('tuition_fee')
                ->where('product_id', $productId)
                ->where('number_of_months', $months)
                ->where('name', 'not like', '%(2 buổi/tuần)%')
                ->where('status', '>', 0)
                ->orderBy('id', 'desc')
                ->first();

            if (!$standardPkg) {
                $this->command->warn("Không tìm thấy gói chuẩn $months tháng của UCREA để tham chiếu.");
                continue;
            }

            // Tạo data cho gói mới (2 buổi/tuần)
            $newPkgData = (array)$standardPkg;
            unset($newPkgData['id']);

            $newPkgData['name'] = "UCREA - $months tháng (2 buổi/tuần)";
            $newPkgData['session'] = $standardPkg->session * 2;
            $newPkgData['price'] = $standardPkg->price * 2;
            $newPkgData['receivable'] = $standardPkg->receivable * 2;
            $newPkgData['hash_key'] = md5($newPkgData['name'] . $newPkgData['price'] . microtime());
            
            if (isset($standardPkg->created_at)) {
                $newPkgData['created_at'] = $now;
            }
            if (isset($standardPkg->updated_at)) {
                $newPkgData['updated_at'] = $now;
            }

            // Kiểm tra trùng lặp
            $exists = DB::table('tuition_fee')
                ->where('name', $newPkgData['name'])
                ->where('product_id', $productId)
                ->where('status', '>', 0)
                ->exists();

            if (!$exists) {
                $newId = DB::table('tuition_fee')->insertGetId($newPkgData);
                $this->command->info("Đã tạo gói: {$newPkgData['name']} (ID: $newId)");

                // Copy relations từ gói 12 tháng
                if ($twelveMonthPkg) {
                    $relations = DB::table('tuition_fee_relation')
                        ->where('tuition_fee_id', $twelveMonthPkg->id)
                        ->get();

                    foreach ($relations as $rel) {
                        DB::table('tuition_fee_relation')->insert([
                            'tuition_fee_id' => $newId,
                            'exchange_tuition_fee_id' => $rel->exchange_tuition_fee_id,
                            'status' => $rel->status
                        ]);
                    }
                }
            }
        }
    }
}
