<?php

namespace App\Console\Commands;

use App\Http\Controllers\APAXAPIController;
use App\Http\Controllers\JobsController;
use App\Models\Student;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Data';

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
        // $apax = new APAXAPIController();
        // //update renew_report
        // $apax->getAllData();

        // u::query("INSERT INTO log_jobs (`action`, created_at) VALUES ('ImportData','".date('Y-m-d H:i:s')."')");
        // $import_students = u::query("SELECT * FROM hanam_import WHERE student_id IS NULL AND exit_phone IS NULL");
        // foreach ($import_students AS $row){
        //     $arr_student_name = u::explodeName($row->student_name);
        //     $student = new Student();
        //     $student->cms_id = 0;
        //     $student->crm_id = "LGL" . time();
        //     $student->name = $row->student_name;
        //     $student->firstname = $arr_student_name->firstname;
        //     $student->lastname = $arr_student_name->lastname;
        //     $student->midname = $arr_student_name->midname;
        //     $student->gud_mobile1 = $row->gud_mobile1;
        //     $student->created_at = date('Y-m-d H:i:s');
        //     $student->status = 1;
        //     $student->save();
        //     $lastInsertedId = $student->id;
        //     $lastCode = u::first("SELECT cms_id FROM students WHERE id < $lastInsertedId ORDER BY id DESC LIMIT 1");
        //     $cms_id = (int)data_get($lastCode, 'cms_id')+1;
        //     $crm_id = "LGL".str_pad($cms_id, 8, '0', STR_PAD_LEFT);
        //     u::query("UPDATE students SET cms_id = '$cms_id', crm_id = '$crm_id' WHERE id = $lastInsertedId");
            
        //     DB::table('term_student_user')->insert(
        //         [
        //             'student_id' => $lastInsertedId,
        //             'status' => 1,
        //             'branch_id' => 19,
        //             'created_at' => date('Y-m-d H:i:s'),
        //             'updated_at' => date('Y-m-d H:i:s')
        //         ]
        //     );
        //     u::query("UPDATE hanam_import SET student_id = '$lastInsertedId' WHERE id = $row->id");
        //     echo $row->id."/";
        // }
        $import_contracts = u::query("SELECT * FROM hanam_import WHERE student_id IS NOT NULL AND status=0");
        foreach ($import_contracts AS $row){
            $pre_contract = u::first("SELECT id, count_recharge FROM contracts WHERE student_id=$row->student_id ORDER BY count_recharge DESC LIMIT 1");
            $count_recharge= $pre_contract && isset($pre_contract->count_recharge) ? (int)$pre_contract->count_recharge + 1 : 0;
            $insert_query = "INSERT INTO contracts
          (`type`,
          `student_id`,
          `branch_id`,
          `product_id`,
          `debt_amount`,
          `start_date`,
          `end_date`,
          `total_sessions`,
          `real_sessions`,
          `status`,
          `count_recharge`,
          `note`,
          `bonus_sessions`,
          `summary_sessions`)
          VALUES
          ( 10, '$row->student_id', 19, '$row->product_id', 0, '2025-08-01', '2026-08-01', '$row->so_buoi', '$row->so_buoi', 4, $count_recharge,
          'chuyển dữ liệu hệ thống trung tâm hà nam', 0, '$row->so_buoi')";
          u::query($insert_query);
          $latest_contract = u::first("SELECT id, created_at, updated_at FROM contracts WHERE student_id = '$row->student_id' ORDER BY id DESC LIMIT 1");
          u::query("UPDATE hanam_import SET contract_id = '$latest_contract->id', status=1 WHERE id = $row->id");
          echo $row->id."/";
        }
        return "ok";
    }
    
}
