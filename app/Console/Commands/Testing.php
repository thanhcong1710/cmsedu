<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Http\Controllers\TestingController as t;
use App\Providers\UtilityServiceProvider as u;

class Testing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ada:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is command testing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
        $data = u::query("SELECT c.id,c.coupon FROM  contracts AS c LEFT JOIN discount_code_contracts AS dc ON dc.contract_id=c.id WHERE dc.id IS NULL AND coupon IS NOT NULL AND c.created_at >= '2025-01-01 00:00:00' ORDER BY c.id ASC");   
        foreach ($data AS $row){
            $discountCode = u::tachChuoiDiscountCode(data_get($row, 'coupon'));
            $discountCode = isset($discountCode['ma']) ? $discountCode['ma'] : '';
            if ($discountCode) {
              $discountInfo = u::first("SELECT * FROM discount_codes WHERE code='$discountCode'");
              if($discountInfo){
                u::query("INSERT INTO discount_code_contracts (contract_id,discount_code_id,discount_code,created_at) VALUES 
                ('".(int)data_get($row, 'id')."', '".data_get($discountInfo, 'id')."','$discountCode','".date('Y-m-d H:i:s')."')");
              }
            }
            echo "Processed contract ID: ".data_get($row, 'id')."\n";
        }
    }
}
