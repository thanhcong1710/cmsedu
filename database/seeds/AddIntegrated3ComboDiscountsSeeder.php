<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddIntegrated3ComboDiscountsSeeder extends Seeder
{
    /**
     * Run the database seeds to add Integrated 3 Combo discounts.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $startDate = $now->format('Y-m-d');
        $endDate = '2026-05-31';

        $comboData = [
            2 => [
                'total_price' => 17400000,
                'pf_percent' => 20,
                'ins_percent' => 15,
            ],
            5 => [
                'total_price' => 34800000,
                'pf_percent' => 30,
                'ins_percent' => 25,
            ]
        ];

        $g2ProductIds = [102, 103, 104, 105, 106, 107];

        foreach ($comboData as $months => $data) {
            // 1. Tìm hoặc tạo gói UCREA 2b/t tương ứng
            $ucPkg = DB::table('tuition_fee')
                ->where('product_id', 1)
                ->where('number_of_months', $months)
                ->where('name', 'like', '%(2 buổi/tuần)%')
                ->where('status', '>', 0)
                ->first();

            // 2. Tìm hoặc tạo gói G2 tương ứng
            $g2Pkgs = DB::table('tuition_fee')
                ->whereIn('product_id', $g2ProductIds)
                ->where('number_of_months', $months)
                ->where('status', '>', 0)
                ->get();

            if (!$ucPkg || $g2Pkgs->isEmpty()) {
                $this->command->warn("Thiếu gói học phí $months tháng cho Combo Tích hợp 3. Hãy đảm bảo đã chạy AddMonthlyTuitionFeesSeeder và AddUcreaDoubleSessionPackagesSeeder trước.");
                continue;
            }

            // Tạo mã cho UCREA 2b/t
            $this->createDiscount($ucPkg->id, $ucPkg->price, $data['pf_percent'], "COMBO{$months}-UC-PF", "Ưu đãi trả thẳng Combo tích hợp 3", $startDate, $endDate);
            $this->createDiscount($ucPkg->id, $ucPkg->price, $data['ins_percent'], "COMBO{$months}-UC-INS", "Ưu đãi trả góp Combo tích hợp 3", $startDate, $endDate);

            // Tạo mã cho G2 nhóm sản phẩm
            $g2Ids = $g2Pkgs->pluck('id')->toArray();
            $g2Price = $g2Pkgs->first()->price;
            $this->createDiscount(implode(',', $g2Ids), $g2Price, $data['pf_percent'], "COMBO{$months}-G2-PF", "Ưu đãi trả thẳng Combo tích hợp 3", $startDate, $endDate);
            $this->createDiscount(implode(',', $g2Ids), $g2Price, $data['ins_percent'], "COMBO{$months}-G2-INS", "Ưu đãi trả góp Combo tích hợp 3", $startDate, $endDate);
        }
    }

    private function createDiscount($feeIds, $price, $percent, $code, $name, $startDate, $endDate)
    {
        $discountAmount = round(((float) $percent * (float) $price) / 100);

        $exists = DB::table('discount_codes')->where('code', $code)->exists();

        $data = [
            'code' => $code,
            'name' => $name,
            'percent' => $percent,
            'discount' => $discountAmount,
            'price' => $price,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 1,
            'fee_ids' => (string) $feeIds,
            'creator' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        if (!$exists) {
            DB::table('discount_codes')->insert($data);
            $this->command->info("Đã tạo mã Combo: $code");
        } else {
            DB::table('discount_codes')->where('code', $code)->update($data);
            $this->command->info("Đã cập nhật mã Combo: $code");
        }
    }
}
