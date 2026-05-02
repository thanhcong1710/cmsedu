<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TuitionFee;
use Carbon\Carbon;

class AddMonthlyTuitionFeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productIds = [1, 2, 3, 102, 103, 104, 105, 106, 107];
        $targetMonths = [2, 3, 5, 6, 10];
        $now = Carbon::now()->format('Y-m-d H:i:s');

        foreach ($productIds as $productId) {
            // Lấy tên sản phẩm từ bảng products
            $product = DB::table('products')->where('id', $productId)->first();
            $productName = $product ? $product->name : "Sản phẩm $productId";

            // 1. Tìm gói 1 tháng đang active
            $oneMonthPkg = DB::table('tuition_fee')
                ->where('product_id', $productId)
                ->where('number_of_months', 1)
                ->where('status', '>', 0)
                ->where('expired_date', '>', $now)
                ->orderBy('id', 'desc')
                ->first();

            // 2. Tìm gói 12 tháng đang active để lấy relations
            $twelveMonthPkg = DB::table('tuition_fee')
                ->where('product_id', $productId)
                ->where('number_of_months', 12)
                ->where('status', '>', 0)
                ->where('expired_date', '>', $now)
                ->orderBy('id', 'desc')
                ->first();

            if (!$oneMonthPkg) {
                $this->command->warn("Không tìm thấy gói 1 tháng active cho sản phẩm: $productName");
                continue;
            }

            foreach ($targetMonths as $months) {
                // 3. Tạo data cho gói mới
                $newPkgData = (array)$oneMonthPkg;
                unset($newPkgData['id']); 

                $newPkgData['name'] = "$productName - $months tháng"; // Đổi tên theo chuẩn mới
                $newPkgData['session'] = $oneMonthPkg->session * $months;
                $newPkgData['price'] = $oneMonthPkg->price * $months;
                $newPkgData['receivable'] = $oneMonthPkg->receivable * $months;
                $newPkgData['number_of_months'] = $months;
                
                if (isset($oneMonthPkg->created_at)) {
                    $newPkgData['created_at'] = $now;
                }
                if (isset($oneMonthPkg->updated_at)) {
                    $newPkgData['updated_at'] = $now;
                }

                $newPkgData['hash_key'] = md5($newPkgData['name'] . $newPkgData['price'] . microtime());

                // Insert gói học phí mới
                $newId = DB::table('tuition_fee')->insertGetId($newPkgData);
                $this->command->info("Đã thêm gói: {$newPkgData['name']} (ID: $newId)");

                // 4. Copy tuition_fee_relation từ gói 12 tháng
                if ($twelveMonthPkg) {
                    $relations = DB::table('tuition_fee_relation')
                        ->where('tuition_fee_id', $twelveMonthPkg->id)
                        ->where('status', 1)
                        ->get();

                    if ($relations->count() > 0) {
                        $insertRelations = [];
                        foreach ($relations as $rel) {
                            $insertRelations[] = [
                                'tuition_fee_id' => $newId,
                                'exchange_tuition_fee_id' => $rel->exchange_tuition_fee_id,
                                'status' => 1
                            ];
                        }
                        DB::table('tuition_fee_relation')->insert($insertRelations);
                    }
                }
            }
        }
    }
}
