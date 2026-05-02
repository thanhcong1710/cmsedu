<?php

namespace App\Console\Commands;

use App\Http\Controllers\SessionsController;
use App\Models\Schedule;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class UpdateSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateSchedule:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'UpdateSchedule';

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
        $list_class = u::query("SELECT cl.* FROM classes AS cl LEFT JOIN sessions AS s ON s.class_id=cl.id WHERE cl.cls_iscancelled='no' AND s.class_day=6 AND product_id!=100");
       foreach($list_class AS $class){
            $check_exit = u::first("SELECT cjrn_id FROM schedules WHERE class_id=".data_get($class, 'id')." AND cjrn_classdate= '2026-05-02'");
            if(!$check_exit){
                $id = u::generateRandomStringOrNumber(9,true);
                $schedule = new Schedule();
                $schedule->cjrn_id = $id;
                $schedule->cls_id = 0;
                $schedule->cjrn_classdate = '2026-05-02';
                $schedule->class_id = data_get($class, 'id');
                $schedule->status = 1;
                $schedule->created_at = date('Y-m-d H:i:s');
                $schedule->updated_at = date('Y-m-d H:i:s');
                $schedule->save();
                echo data_get($class, 'id')."/";
            }
       }
        
        return "ok";
    }
    
}
