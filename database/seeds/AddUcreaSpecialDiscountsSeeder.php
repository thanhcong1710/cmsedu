<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddUcreaSpecialDiscountsSeeder extends Seeder
{
    /**
     * Run the database seeds to add discounts for UCREA 2-session/week packages.
     *
     * @return void
     */
    public function run()
    {
        $productId = 1;
        $now = Carbon::now();
        $startDate = $now->format('Y-m-d');
        $endDate = $now->addYears(1)->format('Y-m-d'); // Mặc định hiệu lực trong 1 năm

        $discounts = [
            // Zone 1: Hà Nội
            ['months' => 2, 'zone_id' => 1, 'percent' => 0, 'type' => 'PF', 'name' => 'Ưu đãi Trả thẳng'],
            ['months' => 5, 'zone_id' => 1, 'percent' => 15, 'type' => 'PF', 'name' => 'Ưu đãi Trả thẳng'],
            ['months' => 5, 'zone_id' => 1, 'percent' => 5, 'type' => 'INS', 'name' => 'Ưu đãi Trả góp'],
            ['months' => 10, 'zone_id' => 1, 'percent' => 35, 'type' => 'PF', 'name' => 'Ưu đãi Trả thẳng'],
            ['months' => 10, 'zone_id' => 1, 'percent' => 28, 'type' => 'INS', 'name' => 'Ưu đãi Trả góp'],

            // Zone 2: Hà Nam, Thanh Hóa
            ['months' => 2, 'zone_id' => 2, 'percent' => 10, 'type' => 'PF', 'name' => 'Ưu đãi Trả thẳng'],
            ['months' => 5, 'zone_id' => 2, 'percent' => 15, 'type' => 'PF', 'name' => 'Ưu đãi Trả thẳng'],
            ['months' => 5, 'zone_id' => 2, 'percent' => 10, 'type' => 'INS', 'name' => 'Ưu đãi Trả góp'],
            ['months' => 10, 'zone_id' => 2, 'percent' => 40, 'type' => 'PF', 'name' => 'Ưu đãi Trả thẳng'],
            ['months' => 10, 'zone_id' => 2, 'percent' => 35, 'type' => 'INS', 'name' => 'Ưu đãi Trả góp'],
        ];

        foreach ($discounts as $item) {
            if ($item['percent'] <= 0)
                continue;

            // Tìm ID gói học phí tương ứng (UCREA 2 buổi/tuần)
            $pkg = DB::table('tuition_fee')
                ->where('product_id', $productId)
                ->where('number_of_months', $item['months'])
                ->where('name', 'like', '%(2 buổi/tuần)%')
                ->where('status', '>', 0)
                ->first();

            if (!$pkg) {
                $this->command->warn("Không tìm thấy gói UCREA {$item['months']} tháng (2 buổi/tuần) để áp mã.");
                continue;
            }

            $zoneName = $item['zone_id'] == 1 ? 'HN' : 'CNT'; // HN: Hà Nội, CNT: Chi nhánh tỉnh
            $code = "UCREA-2B-{$item['months']}T-{$zoneName}-{$item['type']}";
            $name = "{$item['name']} {$item['months']} tháng UCREA (2b/t) " . ($item['zone_id'] == 1 ? "Hà Nội" : "Hà Nam-Thanh Hóa");

            $discountAmount = round(((float) $item['percent'] * (float) $pkg->price) / 100);

            $data = [
                'code' => $code,
                'name' => $name,
                'percent' => $item['percent'],
                'discount' => $discountAmount,
                'price' => $pkg->price,
                'start_date' => $startDate,
                'end_date' => '2026-05-31',
                'status' => 1,
                'zone_id' => $item['zone_id'],
                'fee_ids' => (string) $pkg->id,
                'creator' => 1, // Mặc định admin
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            // Kiểm tra trùng lặp mã code
            $exists = DB::table('discount_codes')->where('code', $code)->exists();

            if (!$exists) {
                DB::table('discount_codes')->insert($data);
                $this->command->info("Đã tạo mã giảm giá: $code ({$item['percent']}%) cho gói ID: {$pkg->id}");
            } else {
                // Nếu tồn tại thì update lại fee_ids và price
                DB::table('discount_codes')
                    ->where('code', $code)
                    ->update([
                        'fee_ids' => (string) $pkg->id,
                        'price' => $pkg->price,
                        'discount' => $discountAmount,
                        'percent' => $item['percent'],
                        'zone_id' => $item['zone_id'],
                        'updated_at' => Carbon::now()
                    ]);
                $this->command->info("Đã cập nhật mã giảm giá: $code");
            }
        }
    }
}
