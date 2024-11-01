<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * ========================================================== *
 * --------------------- Apax ERP System --------------------
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package Apax ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */

class RegistersController extends Controller
{
    public function loadClassesSemesters(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = [];
            $code = APICode::SUCCESS;
            $user_id = $session->id;
            $role_id = $session->role_id;
            $branches = $session->branches_ids;
            $where = "id > 0 AND status > 0";
            $query = "SELECT id, `name`, product_id FROM semesters WHERE $where ORDER BY start_date DESC";
            $data = u::query($query);
        }
        return $response->formatResponse($code, $data);
    }
    public function loadAllClasses(Request $request, $branch_id, $semester_id) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = [];
            $code = APICode::SUCCESS;
            $user_id = $session->id;
            $role_id = $session->role_id;
            $branches = $session->branches_ids;
            $where = " status > 0 AND branch_id IN (0,$branch_id) AND semester_id = $semester_id";
            $query = "SELECT id, id AS item_id, 'program' AS item_type, 'no' AS cls_iscancelled, `name` AS `text`, parent_id, 'fa fa-folder' AS icon FROM programs WHERE $where
                  UNION ALL
                  SELECT CONCAT(999, c.id) AS id, c.id AS item_id, 'class' AS item_type, c.cls_iscancelled, c.cls_name AS `text`, c.program_id AS parent_id, IF(c.cm_id > 0, IF (c.cls_iscancelled = 'yes', 'fa fa-window-close-o', 'fa fa-file-text-o'), IF (c.cls_iscancelled = 'yes', 'fa fa-close', 'fa fa-user-times')) AS icon
                  FROM classes AS c INNER JOIN programs AS p ON c.program_id = p.id
                  WHERE p.status > 0 AND p.branch_id IN (0,$branch_id) AND p.semester_id = $semester_id";
            $class = u::query($query);
            if (count($class)) {
                foreach ($class as $item) {
                    $item->value = $item->id;
                    $item->opened = true;
                    $item->selected = false;
                    $item->disabled = false;
                    $item->loading = false;
                    $item->children = [];
                }
                $classes = apax_get_tree_data($class);
                
                if ($classes) {
                    foreach ($classes as $cls) {
                        if ($cls) {
                            $data[] = $cls;
                        }
                    }
                }
            }
        }
        return $response->formatResponse($code, $data);
    }
    public function loadClassExtendInfo(Request $request, $class_id) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $class_id = (int)$class_id;
            $data = (Object)[];
            $class_info = u::first("SELECT
          c.id AS class_id,
          c.product_id,
          c.program_id,
          c.cls_name AS class_name,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          IF(((c.cls_enddate < CURDATE()) OR (c.cls_iscancelled = 'yes')), 0, 1) as can_update,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          c.brch_id AS brch_id,
          c.branch_id as branch_id,
          CONCAT(u.full_name, ' - ', u.username) AS cm_name,
          u.status AS cm_status,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', COALESCE(t.avatar, 'avatar.png'), '#', t.ins_name) SEPARATOR '@ ') FROM teachers AS t
            LEFT JOIN `sessions` AS s ON s.teacher_id = t.user_id WHERE s.class_id = $class_id) AS teachers_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', r.room_name) SEPARATOR '@ ') FROM rooms AS r
            LEFT JOIN `sessions` AS s ON s.room_id = r.id WHERE s.class_id = $class_id) AS rooms_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', s.class_day) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN classes AS c ON s.class_id = c.id WHERE s.class_id = $class_id) AS weekdays,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', f.start_time, '-', f.end_time) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN shifts AS f ON s.shift_id = f.id WHERE s.class_id = $class_id) AS shifts_name,
          (SELECT t.id FROM `sessions` AS s LEFT JOIN `teachers` AS t ON s.teacher_id = t.user_id WHERE s.class_id = $class_id LIMIT 1) AS teacher_id,
          (SELECT s.room_id FROM `sessions` AS s WHERE s.class_id = $class_id LIMIT 1) AS room_id,
          (SELECT s.shift_id FROM `sessions` AS s WHERE s.class_id = $class_id LIMIT 1) AS shift_id,
          (SELECT GROUP_CONCAT(s.class_day SEPARATOR ',') FROM `sessions` AS s WHERE s.class_id = $class_id) AS arr_weekdays,
           (SELECT count(cjrn_id) FROM schedules WHERE class_id=$class_id) AS num_session
        FROM classes AS c
          LEFT JOIN users AS u ON c.cm_id = u.id
        WHERE c.id = $class_id");
            $students_list = u::query("SELECT
            e.id AS enrolment_id,
            e.student_id,
            s.name AS student_name,
            s.nick AS student_nick,
            s.stu_id AS lms_id,
            t.name AS tuition_fee_name,
            t.receivable AS tuition_fee_price,
            t.`session` AS tuition_fee_sessions,
            cl.cls_name AS class,
            e.start_date AS start_date,
            e.end_date AS end_date,
            c.total_sessions AS total_sessions,
            c.bill_info,
            p.method AS payment_method,
            c.must_charge,
            c.type AS customer_type,
            c.total_charged AS charged_total,
            IF(e.status = 1 AND e.last_date <= CURDATE(), 1, 0) as withdraw,
            IF(e.status = 1 AND e.last_date <= CURDATE(), 2, IF(e.status = 0, 3, 1)) ordering,
            e.real_sessions AS available_sessions,
            e.status AS enrolment_status,
            e.last_date,
            CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
            CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
            u3.hrm_id AS ec_leader_id
          FROM enrolments AS e
            LEFT JOIN students AS s ON e.student_id = s.id
            LEFT JOIN contracts AS c ON e.contract_id = c.id
            LEFT JOIN payment AS p ON c.payment_id = p.id
            LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
            LEFT JOIN classes AS cl ON e.class_id = cl.id
            LEFT JOIN users AS u1 ON c.ec_id = u1.id
            LEFT JOIN users AS u2 ON c.cm_id = u2.id
            LEFT JOIN users AS u3 ON c.ec_leader_id = u3.id
          WHERE e.class_id = $class_id AND e.start_date <= e.end_date ORDER BY ordering");
            $count_students = u::first("SELECT COUNT(*) AS total FROM (SELECT e.id AS total FROM enrolments AS e
          LEFT JOIN students AS s ON e.student_id = s.id
          LEFT JOIN contracts AS c ON e.contract_id = c.id
        WHERE e.class_id = $class_id
          AND e.end_date >= CURDATE() AND e.last_date >= CURDATE() AND e.status = 1
          AND e.start_date <= e.end_date AND e.last_date >= e.start_date GROUP BY e.student_id) AS a");
            $total_students = $count_students->total;
            $teachers = [];
            $weekdays = [];
            $namrooms = [];
            $nashifts = [];
            $sessions = (Object)[];
            $weekdays_text = '';
            $namrooms_text = '';
            $nashifts_text = '';
            $teachers_text = '';
            $displayed = [];
            if ($class_info->weekdays) {
                $weekdays = explode('@', $class_info->weekdays);
                if ($weekdays) {
                    $i = 0;
                    foreach ($weekdays as $weekday) {
                        $weekday_info = explode('|', $weekday);
                        $i++;
                        if ($weekday_info) {
                            $sid = "session_$weekday_info[0]";
                            $inf = $weekday_info[1];
                            $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                            $display = apax_ada_get_weekday($inf);
                            if (!in_array(md5($display), $displayed)) {
                                $sessions->$sid->weekday = apax_ada_get_weekday($inf);
                                $sessions->$sid->information = apax_ada_get_weekday($inf);
                                $weekdays_text.= $display;
                                $displayed[] = md5($display);
                                if ($i < count($weekdays)) {
                                    $weekdays_text.= ', ';
                                }
                            }
                        }
                    }
                }
            }
            if ($class_info->shifts_name) {
                $nashifts = explode('@', $class_info->shifts_name);
                if ($nashifts) {
                    $i = 0;
                    foreach ($nashifts as $nashift) {
                        $nashift_info = explode('|', $nashift);
                        $i++;
                        if ($nashift_info) {
                            $sid = "session_$nashift_info[0]";
                            $inf = $nashift_info[1];
                            $inf = explode('-', $inf);
                            $sta = $inf[0];
                            $end = $inf[1];
                            $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                            $lab = substr($sta, 0, 5).'~'.substr($end, 0, 5);
                            $sessions->$sid->shift = $lab;
                            $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '.$lab : $lab;
                            $nashifts_text.= $lab;
                            $displayed[] = md5($lab);
                            if ($i < count($nashifts)) {
                                $nashifts_text.= ', ';
                            }
                        }
                    }
                }
            }
            if ($class_info->rooms_name) {
                $namrooms = explode('@', $class_info->rooms_name);
                if ($namrooms) {
                    $i = 0;
                    foreach ($namrooms as $namroom) {
                        $namroom_info = explode('|', $namroom);
                        $i++;
                        if ($namroom_info) {
                            $sid = "session_$namroom_info[0]";
                            $inf = $namroom_info[1];
                            $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                            if (!in_array(md5($inf), $displayed)) {
                                $sessions->$sid->room = "Room: $inf";
                                $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '."Room: $inf" : "Room: $inf";
                                $namrooms_text.= $inf;
                                $displayed[] = md5($inf);
                                if ($i < count($namrooms)) {
                                    $namrooms_text.= ', ';
                                }
                            }
                        }
                    }
                }
            }
            if ($class_info->teachers_name) {
                $teachers = explode('@', $class_info->teachers_name);
                if ($teachers) {
                    $i = 0;
                    foreach ($teachers as $teacher) {
                        $teacher_info = explode('|', $teacher);
                        $i++;
                        if ($teacher_info) {
                            $sid = "session_$teacher_info[0]";
                            $inf = $teacher_info[1];
                            $inf = explode('#', $inf);
                            $ava = $inf[0];
                            $ten = $inf[1];
                            $lab = md5($ava) == md5('avatar.png') ? '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/avatar.png"/> '.$ten : '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/teachers/'.$ava.'"/> '.$ten;
                            $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                            if (!in_array(md5($lab), $displayed)) {
                                $sessions->$sid->teacher_avatar = $ava;
                                $sessions->$sid->teacher_name = $ten;
                                $sessions->$sid->teacher_label = $lab;
                                $teachers_text.= $lab;
                                $displayed[] = md5($lab);
                                if ($i < count($teachers)) {
                                    $teachers_text.= ', ';
                                }
                            }
                        }
                    }
                }
            }
            $time_place = '';
            if ($sessions) {
                $i = 0;
                foreach ($sessions as $s => $o) {
                    if (isset($o->information) && strlen($o->information) > 12) {
                        $i++;
                        $time_place.= $o->information;
                        if ($i < count((array)$sessions)) {
                            $time_place.= '<br/>';
                        }
                    }
                }
            }
            $class_info->total_students = $total_students;
            $class_info->sessions = $sessions;
            $class_info->class_time = "$class_info->class_start_date ~ $class_info->class_end_date";
            $class_info->teachers_name = $teachers_text;
            $class_info->shifts_name = $nashifts_text;
            $class_info->rooms_name = $namrooms_text;
            $class_info->weekdays = $weekdays_text;
            $class_info->time_and_place = $time_place;
            $class_info->arr_weekdays = explode(',', $class_info->arr_weekdays);
            $data->class = $class_info;
            $data->students = $students_list;
            $code = APICode::SUCCESS;
        }
        return $response->formatResponse($code, $data);
    }
    
}
