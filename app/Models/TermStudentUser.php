<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TermStudentUser extends Model
{
    protected $table = 'term_student_user';
    public $timestamps = false;

    public static function updateCmByClass($classID, $cmID, $teacherID)
    {
        // get lists Students from Contracts
        $listStd = Contract::select(['student_id'])
            ->where('class_id',$classID)
            ->where('status',6)
            ->pluck('student_id');

        $list = [];
        foreach ($listStd as $l){
            $list[] = $l;
        }
        if( !empty($list)){
            // update to term_student_user
            DB::table('term_student_user')
                ->whereIn('student_id',$list)
                ->update([
                    'cm_id' => $cmID,
                    'teacher_id' => $teacherID, 
                    'updated_at' => date('Y-m-d h:i:s')
                ]);
            // Update to Student_Log_Update
            LogStudentUpdate::createLogs($list, $cmID,$classID);
        }

        return true;
    }
}
