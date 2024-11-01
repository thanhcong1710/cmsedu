<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 11/20/2018
 * Time: 1:20 AM
 */

namespace App\Models;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;

class Attendances extends Model
{
  protected $table = 'attendances';
  public $timestamps = false;

  const STATUS_VANG_MAT_DO_BAO_LUU_DU_CHO = 8;

  const statusOptions  =  [
      '1' => "Có đi học",
      '2' => "Vắng mặt",
      '3' => "Đi muộn",
      '4' => "Về sớm",
      '5' => "Vắng mặt có lý do",
      '6' => "Nghỉ lễ",
      '7' => "Nghỉ có phép",
      '8' => "Vắng mặt do bảo lưu dữ chỗ",
    ];

  public static function getSemesters()
  {
    $where = "id > 0 AND status > 0 AND end_date > CURDATE() AND type = 1";
    $query = "SELECT id, `name` FROM semesters WHERE $where ORDER BY end_date DESC";
    $data = u::query($query);

    return $data;
  }

  public static function insertAttendances($student, $currentUser) {
    $query = "INSERT INTO attendances
                (student_id, branch_id, class_id, product_id, program_id, created_at, creator_id, status, attendance_date, contract_id, enrolment_id, type, reason, note, homework)
              VALUES 
                ($student->student_id, $student->branch_id, $student->class_id, $student->product_id, $student->program_id, NOW(), $currentUser->id, $student->attendance_status_id, '$student->attendance_date', $student->contract_id, $student->enrolment_id, 0, '$student->reason', '$student->note', $student->homework)
              ";
    u::query($query);
  }

  public static function updateAttendances($student, $currentUser) {
    $params = array();
    $strParams = '';
    if ($student->attendance_status_id) {
      $params[] = "status = $student->attendance_status_id";
    } else {
      $params[] = "status = 0";
    }

    if ($student->reason) {
      $params[] = "reason = '$student->reason'";
    } else {
      $params[] = "reason = ''";
    }

    if ($student->note) {
      $params[] = "note = '$student->note'";
    } else {
      $params[] = "note = ''";
    }

    if ($student->homework) {
      $params[] = "homework = $student->homework";
    } else {
      $params[] = "homework = 0";
    }

    if ($params) {
      $strParams = implode(' , ', $params);
    }

    $query = "UPDATE attendances SET $strParams, editor_id = $currentUser->id, updated_at = NOW() WHERE id = $student->attendance_id";
    u::query($query);
  }

  /**
   * update status of student
   * @param $studentId
   * @param int $status
   * @param $editor_id
   * @return bool
   */

  public function updateStatusMakeup($classId, $status_make_up,$student_id,$attendance_date,$creatorId){
      u::query("UPDATE `attendances` att SET att.`status_make_up` = $status_make_up, att.updated_at = now(), att.editor_id = $creatorId WHERE att.`student_id` = $student_id AND att.`attendance_date` = '$attendance_date' and att.class_id = $classId");
  }

  public function updateNoteMakeup($classId,$note,$student_id,$attendance_date,$creatorId){
      u::query("UPDATE `attendances` att SET att.`note` = '$note', att.updated_at = now(), att.editor_id = $creatorId WHERE att.`student_id` = $student_id AND att.`attendance_date` = '$attendance_date' and att.class_id = $classId");
  }

  public function updateStatusAttendanceByStudentId($studentId=0, $status = -1, $editor_id=null, $attendanceId = 0){
    if(empty($studentId)||$status < 0){
      return false;
    }

    $update_editor_id = isset($editor_id)? "editor_id = $editor_id, ":"";
    $query = "UPDATE attendances SET status=$status, $update_editor_id updated_at = NOW() WHERE student_id = $studentId";
    if ($attendanceId)
        $query = "UPDATE attendances SET status=$status, $update_editor_id updated_at = NOW() WHERE id = $attendanceId";
    u::query($query);
    return true;
  }

  public function getClassAttendanceDetailByStudent($classId = -1,$studentId = -1)
  {
    if($classId<0 || $studentId<0){
      return null;
    }
    $query = "SELECT DISTINCT(a.id) AS attendances_id, a.attendance_date, a.status, a.homework, a.note, c.id,
              c.cls_name as class_name
              FROM attendances AS a
				      INNER JOIN classes AS c ON a.class_id = c.id
              WHERE a.student_id = $studentId AND a.class_id = ".(int)$classId."
              ORDER BY a.attendance_date DESC";
    $data = u::query($query);
    return $data;
  }

    public static function getIdAttendanceByStudent($classId, $studentId, $date){
        $query ="SELECT att.id FROM `attendances` att WHERE att.`class_id` = $classId AND att.`student_id` = $studentId AND att.`attendance_date` ='$date'";
        $data = u::query($query);
        return $data ? $data[0]->id : 0;

    }

    public function preInsertAttendances($studentId=0, $classId = 0, $status=0,$date,$creatorId){
        $query ="SELECT (SELECT program_id FROM `term_user_class` WHERE cls_name = c.cls_name LIMIT 1) program_id,
                (SELECT product_id FROM `term_user_class` WHERE cls_name = c.cls_name LIMIT 1) product_id,
                (SELECT branch_id FROM `term_user_class` WHERE cls_name = c.cls_name LIMIT 1) branch_id,
                (SELECT id FROM `contracts` WHERE class_id = $classId AND student_id =  $studentId AND STATUS = 6) enrolment_id,
                (SELECT id FROM `contracts` WHERE class_id = $classId AND student_id =  $studentId AND STATUS = 6) contract_id
                FROM `classes` c WHERE c.id = $classId";
        $data = u::query($query);

        if ($data){
            $attendance = $data[0];
            $attendance->attendance_status_id = $status;
            $attendance->student_id = $studentId;
            $attendance->class_id = $classId;
            $attendance->attendance_status_id = $status;
            $attendance->attendance_date = $date;
            $attendance->type = 0;
            $attendance->reason = '';
            $attendance->note = '';
            $attendance->homework = 0;
            $currentUser = (object)['id' =>$creatorId];
            static::insertAttendances($attendance, $currentUser);
        }
    }

}
