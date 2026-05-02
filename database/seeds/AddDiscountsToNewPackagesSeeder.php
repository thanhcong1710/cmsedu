<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddDiscountsToNewPackagesSeeder extends Seeder
{
    /**
     * Run the database seeds to apply existing discounts to new packages.
     *
     * @return void
     */
    public function run()
    {
        $productGroups = [
            'G1' => [1, 2, 3],
            'G2' => [102, 103, 104, 105, 106, 107]
        ];
        
        $mapping = [
            2 => 3,  // 2 tháng tham khảo từ 3 tháng
            5 => 6,  // 5 tháng tham khảo từ 6 tháng
            10 => 9  // 10 tháng tham khảo từ 9 tháng
        ];
        $now = Carbon::now()->format('Y-m-d');

        // Lấy tất cả mã chiết khấu đang active
        $discountCodes = DB::table('discount_codes')
            ->where('status', 1)
            ->where('end_date', '>=', $now)
            ->get();

        foreach ($productGroups as $groupKey => $groupIds) {
            foreach ($mapping as $targetMonth => $sourceMonth) {
                // Thu thập tất cả các source ID và target ID trong nhóm này
                $targetPkgs = DB::table('tuition_fee')
                    ->whereIn('product_id', $groupIds)
                    ->where('number_of_months', $targetMonth)
                    ->where('status', '>', 0)
                    ->get();
                
                $sourcePkgs = DB::table('tuition_fee')
                    ->whereIn('product_id', $groupIds)
                    ->where('number_of_months', $sourceMonth)
                    ->where('status', '>', 0)
                    ->get();
                
                if ($targetPkgs->isEmpty() || $sourcePkgs->isEmpty()) continue;

                $targetIdsMap = $targetPkgs->pluck('id')->toArray();
                $sourceIdsMap = $sourcePkgs->pluck('id')->toArray();
                $targetPrice = $targetPkgs->first()->price;

                foreach ($discountCodes as $discount) {
                    $feeIds = explode(',', $discount->fee_ids);
                    
                    // Kiểm tra xem mã giảm giá này có áp dụng cho bất kỳ gói source nào trong nhóm không
                    $applicableSourceIds = array_intersect($sourceIdsMap, $feeIds);
                    
                    if (!empty($applicableSourceIds)) {
                        // Tạo mã code mới với hậu tố nhóm
                        $oldCode = $discount->code;
                        $newCode = str_replace("C$sourceMonth", "C$targetMonth", $oldCode);
                        $newCode = str_replace("COMBO$sourceMonth", "COMBO$targetMonth", $newCode);
                        $newCode = str_replace("{$sourceMonth}T", "{$targetMonth}T", $newCode);
                        
                        $newCode .= "-$groupKey";
                        
                        // Chuẩn bị dữ liệu insert
                        $newDiscount = (array)$discount;
                        unset($newDiscount['id']); 
                        
                        $newDiscount['code'] = $newCode;
                        $newDiscount['fee_ids'] = implode(',', $targetIdsMap);
                        $newDiscount['price'] = $targetPrice;
                        $newDiscount['created_at'] = Carbon::now();
                        $newDiscount['updated_at'] = Carbon::now();
                        
                        // Kiểm tra trùng lặp trước khi insert
                        $exists = DB::table('discount_codes')
                            ->where('code', $newCode)
                            ->exists();

                        if (!$exists) {
                            DB::table('discount_codes')->insert($newDiscount);
                            $this->command->info("Đã tạo mã giảm giá nhóm $groupKey: $newCode cho " . count($targetIdsMap) . " gói sản phẩm.");
                        }
                    }
                }
            }
        }
    }
}
