<?php
/**
 * Created by PhpStorm.
 * User: TruongVu
 * Date: 4/15/2018
 * Time: 11:45 PM
 */
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider;
use App\Models\APICode;

class AutoCountDaysInSchoolController extends Controller
{
    public function updateCountDaysInSchool(){
        $date = date('Y-m-d');
        //$wday = getdate(strtotime(date('Y-m-d')))['wday'];
        $wday = UtilityServiceProvider::getWdayOfDate();

        //Get list students not reserved
        $list_student = DB::select(
            DB::raw("
                SELECT c.id, c.student_id, c.class_id, c.status as enrolments_status, r.status as reserves_status FROM contracts AS c
                LEFT JOIN reserves AS r ON c.student_id = r.student_id
                WHERE c.enrolment_end_date > :date_now AND c.enrolment_start_date < :date_now2 AND c.status = :status1
                AND r.end_date > :date_now3 AND r.start_date < :date_now4 AND r.status = :status2
            "),
            [
                'date_now' => $date,
                'date_now2' => $date,
                'date_now3' => $date,
                'date_now4' => $date,
                'status1' => 1,
                'status2' => 1
            ]
        );
        //echo("<pre>");var_dump($list_student);die("tvx");

        if (!in_array($list_student, array(null, '', '0'))){
            foreach($list_student as $key => $val){
                //Check date in holiday by class_id
                $check_holiday = UtilityServiceProvider::checkInHolydays($date, array(), $val->class_id);
                if($check_holiday == false){
                    //Get list class_day by class_id
                    $class_day = DB::table('sessions')->where('class_id','=',$val->class_id)
                        ->select(['class_day'])->pluck('class_day')->toArray();

                    if (!in_array($class_day, array(null, '', '0'))){
                        //Check current date in list class_day
                        if(in_array($wday, $class_day)){
                            //update done_sessions
                            $update_done_sessions = DB::update(
                                DB::raw("
                                        UPDATE contracts SET done_sessions = done_sessions+1
                                        WHERE student_id = :student_id1 AND id = :contract_id1
                                    "),
                                [
                                    'student_id1' => $val->student_id,
                                    'contract_id1' => $val->id
                                ]);

                        }
                    }
                }
            }
        }

        $response['code'] = APICode::SUCCESS;
        $response['message'] = 'Update successful';
        return response()->json($response);

    }

}




