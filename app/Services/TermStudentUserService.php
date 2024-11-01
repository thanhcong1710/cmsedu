<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Enrolment;

/*
* Created by HoiLN
*/
class TermStudentUserService
{
    public function updateCmByClass($classID, $cmID)
    {
        // get Lists Students from Enrolment
        $listStd = Enrolment::select(['student_id'])
                            ->where('class_id',$classID)
                            ->where('status',1)
                            ->pluck('student_id');
        if( empty($listStd) )
            return true;
        //Update to term_student_user
        DB::table('term_student_user')
            ->whereIn('student_id',$listStd)
            ->update(['cm_id' => $cmID,
                     'updated_at' => date('Y-m-d h:i:s')]
                    );
        // Update to Student_Log_Update
        $logStdUpdateService = new LogStudentUpdateService();
        $logStdUpdateService->createLogs($listStd,$cmID);
        return true;
    }
}