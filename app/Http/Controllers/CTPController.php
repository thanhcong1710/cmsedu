<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\APICode;
use App\Models\Response;
use App\Models\Contract;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;

class CTPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getCTPByClass(Request $request, $class_id)
    {
        $q = "SELECT v.* FROM videos AS v WHERE v.class_id = $class_id";
        $videos = DB::select(DB::raw($q));

        return response()->json($videos);
    }

    public function getCTPInfo(Request $request, $student_id, $ctp_id)
    {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $student = u::first("SELECT id, stu_id lms_id, `name`, nick, avatar, phone, email FROM students WHERE stu_id = $student_id");
        $issue = u::query("SELECT * FROM issues WHERE student_id = $student->id");
        $ctp = u::first("SELECT * FROM ctp_videos WHERE id = $ctp_id");
        $students_list = $ctp->lms_student_ids ? json_decode($ctp->lms_student_ids) : [];
        $students_list = implode(',', $students_list);
        $students = u::query("SELECT id, stu_id lms_id, `name`, nick, avatar, phone, email FROM students WHERE stu_id IN ($students_list) AND stu_id <> $student_id");
        $data = (object)[];
        $data->students = $students;
        $data->student = $student;
        $data->issue = $issue;
        $data->video = $ctp;
        $code = APICode::SUCCESS;
      }
      return $response->formatResponse($code, $data);
    }
    public function loadClassInfo(Request $request, $class_id) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      $session = $request->users_data;
      $user_id = $session->id;
      $user_role = null;
      $role_query = "SELECT tb.role_id from term_user_branch as tb WHERE tb.user_id = $user_id";
      $role = DB::selectOne(DB::raw($role_query));
      $user_role = $role->role_id;
      if ($session = $request->users_data) {
        // $user_id = null;
        $class_id = (int)$class_id;
        $data = (Object)[];
        $class_info = u::first("SELECT
         c.id AS class_id_selected,
          c.cls_id AS class_id,
          c.cls_name AS class_name,
          c.product_id,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          COALESCE(e.total, 0) AS total_students,
          CONCAT(u.full_name, ' - ', u.username) AS cm_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', COALESCE(t.avatar, 'avatar.png'), '#', t.ins_name) SEPARATOR '@ ') FROM teachers AS t
            LEFT JOIN `sessions` AS s ON s.teacher_id = t.id WHERE s.class_id = $class_id) AS teachers_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', r.room_name) SEPARATOR '@ ') FROM rooms AS r
            LEFT JOIN `sessions` AS s ON s.room_id = r.id WHERE s.class_id = $class_id) AS rooms_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', s.class_day) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN classes AS c ON s.class_id = c.id WHERE s.class_id = $class_id) AS weekdays,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', f.start_time, '-', f.end_time) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN shifts AS f ON s.shift_id = f.id WHERE s.class_id = $class_id) AS shifts_name
        FROM classes AS c
          LEFT JOIN users AS u ON c.cm_id = u.id
          LEFT JOIN (SELECT COUNT(c.id) AS total, c.id FROM `contracts` AS c
            LEFT JOIN classes AS c ON c.class_id = c.id WHERE
            (c.enrolment_end_date >= CURDATE() AND c.enrolment_end_date <= c.cls_enddate)
            OR
            (c.enrolment_start_date <= c.cls_enddate AND c.enrolment_end_date >= c.cls_enddate)
            AND c.id = $class_id) AS v ON v.id = c.id
        WHERE c.id = $class_id");
        $classdays = u::getClassDays($class_id);
        $holidays = u::getPublicHolidays($class_id, 0, $class_info->product_id);
        $students_list = u::query("SELECT
            s.id AS student_id,
            s.stu_id as lms_id,
            s.name AS student_name,
            s.nick AS student_nick,
            t.name AS tuition_fee_name,
            t.receivable AS tuition_fee_price,
            t.`session` AS tuition_fee_sessions,
            c.enrolment_start_date AS start_date,
            c.enrolment_end_date AS end_date,
            c.total_sessions AS total_sessions,
            c.type AS customer_type,
            (c.total_discount + c.must_charge - c.debt_amount) AS charged_total,
            -- (CEIL((c.total_discount + c.must_charge - c.debt_amount) / (t.receivable / t.`session`))) AS available_sessions,
            c.enrolment_real_sessions AS available_sessions,
            c.status AS enrolment_status,
            c.enrolment_last_date,
            CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
            CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
            (SELECT 
            GROUP_CONCAT(CONCAT(id,'|',cjamvi_title,'|',cjamvi_thumbnail_image,'|',link_video) SEPARATOR ';') videos
            FROM ctp_videos WHERE 
                  lms_student_ids LIKE CONCAT('[', s.stu_id, ',%')
                OR lms_student_ids LIKE CONCAT('%,', s.stu_id, ',%')
                OR lms_student_ids LIKE CONCAT('%,', s.stu_id, ']')
                OR lms_student_ids LIKE CONCAT('[', s.stu_id, ']')
            ) videos
          FROM contracts AS c
            LEFT JOIN students AS s ON c.student_id = s.id
            LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
            LEFT JOIN users AS u1 ON c.ec_id = u1.id
            LEFT JOIN users AS u2 ON c.cm_id = u2.id
            -- LEFT JOIN (SELECT MAX(i.id) as issue_id FROM issues as i GROUP BY i.id) ON (i.student_id = s.id)
            -- LEFT JOIN (SELECT MAX(is.id) as issue_id FROM issues as is GROUP BY is.id) as sub_query ON (sub_query.student_id = s.id)
          WHERE c.class_id = $class_id AND
                ((c.enrolment_end_date >= CURDATE() AND c.enrolment_end_date <= c.end_date)
                OR
                (c.enrolment_start_date <= c.end_date AND c.enrolment_end_date >= c.end_date))
          GROUP BY s.id      
          ");
        // dd($students_list);
        foreach ($students_list as $st) {
          $st_id = $st->student_id;
          $lms_id = $st->lms_id;
          // dd($lms_id);

          $student = DB::selectOne(DB::raw("SELECT s.id, s.name, s.stu_id FROM students as s WHERE s.stu_id = '$lms_id'"));
          // dd($student);
          // SELECT * FROM videos WHERE students LIKE '%,168435]' OR students LIKE '%,168435,%' OR students LIKE '[168435,%'

          $ctp_list = DB::select(DB::raw(" SELECT * FROM videos WHERE students LIKE '%,$lms_id]' OR students LIKE '%,$lms_id,%' OR students LIKE '[$lms_id,%' "));

          $st->ctp_list = $ctp_list;
          // $ctp_list = json_decode(json_encode($ctp_list), FALSE);

          $arrDefault = [
            'A1' => null,
            'A2' => null,
            'A3' => null,
            'B1' => null,
            'B2' => null,
            'B3' => null
          ];
          //$qr = "SELECT i.* FROM issues as i where i.student_id = $st->student_id ORDER BY i.id DESC";
          // $qr = "
          //   SELECT b.name as book_name,i.id 
          //   from books as b 
          //   JOIN issues as i on i.book_id = b.id
          //   LEFT JOIN classes as cl on cl.cls_id = i.class_id 
          //   WHERE i.student_id = $st_id
          //   AND cl.id = $class_id
          // ";
          $qr = " SELECT * FROM videos WHERE students LIKE '%,$lms_id]' OR students LIKE '%,$lms_id,%' OR students LIKE '[$lms_id,%' ";
          $books = DB::select(DB::raw($qr));

          $arrValue = [];
          foreach( $books as $iss ) {
            $arrValue[$iss->book] = $iss->book;
          }
          $newArray = array_merge($arrDefault,$arrValue);
          $st->books = $newArray;
          // dd($newArray);
        }
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
        // if (count($students_list)) {
        //   foreach ($students_list as $student) {
        //     $information = u::getRealSessions($student->available_sessions, $classdays, $holidays);
        //     $student->available_sessions = $information && isset($information->total) ? $information->total : 0;
        //     $student->last_date = $information && isset($information->end_date) ? $information->end_date : '';
        //   }
        // }
        $class_info->total_students = count(apax_ada_unique_object_array($students_list, 'student_id'));
        $class_info->sessions = $sessions;
        $class_info->class_time = date('d/m/Y', strtotime($class_info->class_start_date)).' ~ '.date('d/m/Y', strtotime($class_info->class_end_date));
        $class_info->teachers_name = $teachers_text;
        $class_info->shifts_name = $nashifts_text;
        $class_info->rooms_name = $namrooms_text;
        $class_info->weekdays = $weekdays_text;
        $class_info->time_and_place = $time_place;
        $class_info->user_role = $user_role;
        $data->class = $class_info;
        $data->user_id = $user_id;
        $data->students = $students_list;
        $code = APICode::SUCCESS;
      }
      return $response->formatResponse($code, $data);
    }
}
