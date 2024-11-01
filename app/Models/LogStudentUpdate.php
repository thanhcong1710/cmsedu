<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LogStudentUpdate extends Model
{
    protected $table = 'log_student_update';
    public $timestamps = false;

    public static function createLogs($listsStd = [], $cmID, $classID)
    {
        if( empty($listsStd) )
            return true;

        $branchInfo = DB::select(DB::raw("
          SELECT 
            c.branch_id as branch_id,
            ( 
              SELECT u4.id FROM users u4 
              LEFT JOIN term_user_branch tu ON u4.id = tu.user_id 
              WHERE tu.branch_id = c.branch_id AND tu.role_id = '686868' AND  tu.status = 1 ORDER BY id DESC LIMIT 1) AS ceo_branch_id
          FROM classes as c WHERE c.id = $classID
        "));
        $dataInsert = [];
        foreach ($listsStd as $item) {
            $dataInsert[] = [
                'student_id' => $item,
                'content'    => 'Update thông tin CM',
                'status'     => 1,
                'cm_id'      => $cmID,
                'branch_id'  => $branchInfo[0]->branch_id,
                'ceo_branch_id' => $branchInfo[0]->ceo_branch_id,
                'updated_at' => date('Y-m-d h:i:s'),
            ];
        }
        DB::table('log_student_update')->insert($dataInsert);
        return true;
    }
}
