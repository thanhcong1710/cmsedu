<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

/*
* Created by HoiLN
*/
class LogStudentUpdateService
{
    public function createLogs($listsStd = [], $cmID)
    {
        if( empty($listsStd) )
            return true;

        $branchInfo = DB::table('term_student_user')->select(['branch_id','ceo_branch_id'])
                        ->where('student_id',$listsStd[0])->first();
        $dataInsert = [];
        foreach ($listsStd as $item) {
            $dataInsert[] = [
                'student_id' => $item,
                'content'    => 'Update thông tin CM',
                'status'     => 1,
                'cm_id'      => $cmID,
                'branch_id'  => $branchInfo->branch_id,
                'ceo_branch_id' => $branchInfo->ceo_branch_id,
                'updated_at' => date('Y-m-d h:i:s'),
            ];
        }
        DB::table('log_student_update')->insert($dataInsert);
        return true;
    }
}