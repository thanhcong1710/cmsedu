<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use App\Models\DiscountCode;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class UpdateTuitionFee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateTuitionFee:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update TuitionFee';

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
    public function handle(Request $request)
    {
        $pre_date= '2025-03-15';
        $pre_start_date = date('Y-m-01',strtotime($pre_date));
        $pre_end_date = date('Y-m-t',strtotime($pre_date));
        $discount_codes = u::query("SELECT * FROM discount_codes WHERE start_date = '$pre_start_date' AND end_date= '$pre_end_date'");

        $process_date = '2025-04-15';
        $process_start_date = date('Y-m-01',strtotime($process_date));
        $process_end_date = date('Y-m-t',strtotime($process_date));

        foreach($discount_codes AS $row){
            $discountCode = new DiscountCode();
            $discountCode->code = str_replace(date('Ym',strtotime($pre_date)),date('Ym',strtotime($process_date)), data_get($row,'code'));
            $discountCode->name = str_replace('tháng '.(int)date('m',strtotime($pre_date)),'tháng '.(int)date('m',strtotime($process_date)), data_get($row,'name'));;
            $discountCode->percent = data_get($row, 'percent');
            $discountCode->start_date = $process_start_date;
            $discountCode->end_date = $process_end_date;
            $discountCode->status = 1;
            $discountCode->price =  data_get($row, 'price');
            $discountCode->discount = data_get($row, 'discount');
            $discountCode->zone_id = data_get($row, 'zone_id');
            $discountCode->fee_ids = !data_get($row, 'fee_ids');
            $discountCode->save();
        }
        return "ok";
    }
    
}
