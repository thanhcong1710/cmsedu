<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\APICode;

class Enrolment extends Model
{
    protected $table = 'enrolments';
    // public $timestamps = false;

    //Kiểm tra số buổi còn lại có đủ học hết semester hay không
    public static function isFullSemester($student_id){
        return true;
    }

    public static function createEnrolment($data){
        $enrolment = new Enrolment();

        $enrolment->contract_id = $data['contract_id'];
        $enrolment->student_id = $data['student_id'];
        $enrolment->class_id = $data['class_id'];
        $enrolment->lms_cstd_id = $data['lms_cstd_id'];
        $enrolment->start_date = $data['start_date'];
        $enrolment->lms_start_cjrn_id = $data['lms_start_cjrn_id'];
        $enrolment->end_date = $data['end_date'];
        $enrolment->created_at = date('Y-m-d H:i:s');
        $enrolment->creator_id = $data['creator_id'];
        $enrolment->updated_at = date('Y-m-d H:i:s');
        $enrolment->editor_id = $data['editor_id'];
        $enrolment->status = $data['status'];

        if($enrolment->save()){
            return APICode::SUCCESS;
        }else{
            return false;
        }
    }

    public static function updateEnrolment($data, $enrol_id){
        $enrolmentObj = new Enrolment();
        $enrolment = $enrolmentObj->find($enrol_id);

        $enrolment->contract_id = $data['contract_id'];
        $enrolment->student_id = $data['student_id'];
        $enrolment->class_id = $data['class_id'];
        $enrolment->lms_cstd_id = $data['lms_cstd_id'];
        $enrolment->start_date = $data['start_date'];
        $enrolment->lms_start_cjrn_id = $data['lms_start_cjrn_id'];
        $enrolment->end_date = $data['end_date'];
        $enrolment->created_at = date('Y-m-d H:i:s');
        $enrolment->creator_id = $data['creator_id'];
        $enrolment->updated_at = date('Y-m-d H:i:s');
        $enrolment->editor_id = $data['editor_id'];
        $enrolment->status = $data['status'];

        if($enrolment->save()){
            return APICode::SUCCESS;
        }else{
            return false;
        }
    }

}
