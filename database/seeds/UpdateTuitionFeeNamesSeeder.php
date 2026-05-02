<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateTuitionFeeNamesSeeder extends Seeder
{
    /**
     * Run the database seeds to update tuition_fee names for ID >= 367.
     *
     * @return void
     */
    public function run()
    {
        $fees = DB::table('tuition_fee')
            ->where('id', '>=', 367)
            ->get();

        foreach ($fees as $fee) {
            $product = DB::table('products')->where('id', $fee->product_id)->first();
            
            if ($product) {
                $newName = "{$product->name} - {$fee->number_of_months} tháng";
                
                DB::table('tuition_fee')
                    ->where('id', $fee->id)
                    ->update(['name' => $newName]);
                
                $this->command->info("Updated ID {$fee->id}: $newName");
            }
        }
    }
}
