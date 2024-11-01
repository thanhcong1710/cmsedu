<?php

namespace App\Models;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $table = 'issues';
    public $timestamps = false;

    public function getClassInfo($classId)
    {
        if (empty($classId)) {
            return null;
        }

        $query = "SELECT
          c.id AS class_id,
          c.cls_name AS class_name,
          c.product_id,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          u1.full_name as teacher_name,
          programs.name as program_name,
          COALESCE(h.total, 0) AS total_students,
          CONCAT(u.full_name, ' - ', u.username) AS cm_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', COALESCE(t.avatar, 'avatar.png'), '#', t.ins_name) SEPARATOR '@ ') FROM teachers AS t
            LEFT JOIN `sessions` AS s ON s.teacher_id = t.id WHERE s.class_id = $classId) AS teachers_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', r.room_name) SEPARATOR '@ ') FROM rooms AS r
            LEFT JOIN `sessions` AS s ON s.room_id = r.id WHERE s.class_id = $classId) AS rooms_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', s.class_day) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN classes AS c ON s.class_id = c.id WHERE s.class_id = $classId) AS weekdays,
          (SELECT GROUP_CONCAT(CONCAT(f.start_time, '-', f.end_time) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN shifts AS f ON s.shift_id = f.id WHERE s.class_id = $classId) AS shifts_name,
            (SELECT name FROM level WHERE id=c.level_id) AS level_name
        FROM classes AS c
          LEFT JOIN users AS u ON c.cm_id = u.id
          LEFT JOIN users AS u1 ON c.teacher_id = u1.id
          LEFT JOIN (SELECT COUNT(e.id) AS total, c.id FROM `contracts` AS e
            LEFT JOIN classes AS c ON e.class_id = c.id WHERE
            e.enrolment_last_date >= CURRENT_DATE AND e.status!=7 AND c.id = $classId) AS h ON h.id = c.id
            LEFT JOIN programs ON c.program_id = programs.id
        
        WHERE c.id = $classId";
        return u::first($query);
    }

    public function getStudentsByClass($classId, $date)
    {
        if (empty($classId)) {
            return null;
        }
        $select = "SELECT c.id AS enrolment_id,
                    s.id AS student_id,
                    s.name AS student_name,
                    s.crm_id AS crm_id,
                    s.nick AS student_nick,
                    s.gud_name1,
                    s.gud_mobile1,
                    c.class_id,
                    c.enrolment_start_date,
                    c.enrolment_end_date,
                    c.enrolment_last_date,
                    c.id AS contract_id,
                    c.branch_id,
                    c.product_id,
                    c.program_id,
                    c.type,
                    c.total_sessions,
                    c.enrolment_real_sessions,
                    IF(a.id, a.type, -1) AS attendance_type,
                    a.status AS attendance_status,
                    a.reason,
                    a.note,
                    a.attendance_date,
                    IF(a.homework,1,0) AS homework,
                    a.id AS attendance_id,
                    i.general_comment AS feedback_comment,
                    i.id AS feedback_id ";

        if (!empty($date)) {
            $query = "$select
              FROM contracts c
              LEFT JOIN students s ON c.student_id = s.id
              LEFT JOIN attendances a ON a.enrolment_id = c.id
              AND a.attendance_date = '$date'
              LEFT JOIN issues i ON i.student_id = c.student_id AND i.classdate = '$date' AND i.feedback_type = 0
              WHERE c.enrolment_start_date <= '$date'
                AND c.enrolment_last_date >= '$date'
                AND c.class_id = $classId AND s.status >0 ";
        } else {
            $query = "$select
              FROM contracts c
              LEFT JOIN students s ON c.student_id = s.id
              LEFT JOIN attendances a ON a.enrolment_id = c.id
              LEFT JOIN issues i ON i.student_id = c.student_id AND i.feedback_type = 1 AND i.class_id = $classId
              WHERE c.class_id = $classId AND s.status >0 ";
        }
        return u::query($query);
    }
}
