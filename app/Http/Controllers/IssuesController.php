<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Classes;
use App\Models\Issue;
use App\Models\Mail;
use App\Models\Response;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TermStudentRank;
use App\Providers\UtilityServiceProvider as u;

use function _\ary;
use function Complex\csc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IssuesController extends Controller
{
    public function store(Request $request)
    {

        $user_data = $request->users_data;
        if ($user_data) {
            $user_id = (int)$user_data->id;
        }
        $role_query = "SELECT tb.role_id from term_user_branch as tb WHERE tb.user_id = $user_id";
        $role = DB::selectOne(DB::raw($role_query));
        $user_role = $role->role_id;
        $teacher_id = null;
        if ($user_role == APICode::ROLE_TEACHER) {
            $teacher_id = $user_id;
            $creator_id = $teacher_id;
        } else {
            $teacher_id = null;
        }
        $class = $request->class;
        $issuedata = $request->issue;
        // dd($class);
        $issue = $request->issue;
        $book_id = $request->book_id;
        $book_from = $issue['book_from'];
        $book_to = $issue['book_to'];

        $classdate = $class['class_start_date'];
        $cm_id = $class['cm_id'];
        $book_id = $book_id;
        // $teacher_id = $issue['teacher_id'];
        $student_id = $issue['student_id'];
        // $book_from = $issue['book_from'];
        // $book_to = $issue['book_to'];
        $class_id = $class['class_id'];
        $tc_gretest_strength = $issue['tc_gretest_strength'];
        $cm_gretest_strength = $issue['cm_gretest_strength'];
        $tc_need_improved = $issue['tc_need_improved'];
        $cm_need_improved = $issue['cm_need_improved'];
        $trail_report = $issue['trail_report'];
        // $cm_trail_report = $issue['cm_trail_report'];
        $class_term = $issue['class_term'];
        $summary = $issue['summary'];
        // $cm_summary = $issue['cm_summary'];
        $scoring_classroom_duty_attendance = $issue['scoring_classroom_duty_attendance'];
        $scoring_classroom_duty_punctuality = $issue['scoring_classroom_duty_punctuality'];
        $scoring_classroom_duty_homework = $issue['scoring_classroom_duty_homework'];
        $scoring_reading_comprehension = $issue['scoring_reading_comprehension'];
        $scoring_reading_fluency = $issue['scoring_reading_fluency'];
        $scoring_writing_sentence_structure = $issue['scoring_writing_sentence_structure'];
        $scoring_writing_creativity = $issue['scoring_writing_creativity'];
        $scoring_ctp_speaking_expression = $issue['scoring_ctp_speaking_expression'];
        $scoring_ctp_teamwork = $issue['scoring_ctp_teamwork'];
        $scoring_social_skills_behavior = $issue['scoring_social_skills_behavior'];
        $scoring_social_skills_confidence = $issue['scoring_social_skills_confidence'];
        $scoring_social_skills_participation = $issue['scoring_social_skills_participation'];
        $parents_discussion = $issue['parents_discussion'];
        $parents_comment = $issue['parents_comment'];
        $parents_evaluation = $issue['parents_evaluation'];
        $creator_id = $user_id;

        $issue = new Issue();
        $issue->classdate = $classdate;
        $issue->book_id = $book_id;
        $issue->book_from = $book_from;
        $issue->book_to = $book_to;
        $issue->cm_id = $cm_id;
        $issue->class_id = $class_id;
        $issue->teacher_id = $teacher_id;
        $issue->student_id = $student_id;
        $issue->tc_gretest_strength = $tc_gretest_strength;
        $issue->cm_gretest_strength = $cm_gretest_strength;
        $issue->tc_need_improved = $tc_need_improved;
        $issue->cm_need_improved = $cm_need_improved;
        $issue->trail_report = $trail_report;
        // $issue->cm_trail_report = $cm_trail_report;
        $issue->class_term = $class_term;
        $issue->summary = $summary;
        // $issue->cm_summary = $cm_summary;
        $issue->scoring_classroom_duty_attendance = $scoring_classroom_duty_attendance;
        $issue->scoring_classroom_duty_punctuality = $scoring_classroom_duty_punctuality;
        $issue->scoring_classroom_duty_homework = $scoring_classroom_duty_homework;
        $issue->scoring_reading_comprehension = $scoring_reading_comprehension;
        $issue->scoring_reading_fluency = $scoring_reading_fluency;
        $issue->scoring_writing_sentence_structure = $scoring_writing_sentence_structure;
        $issue->scoring_writing_creativity = $scoring_writing_creativity;
        $issue->scoring_ctp_speaking_expression = $scoring_ctp_speaking_expression;
        $issue->scoring_ctp_teamwork = $scoring_ctp_teamwork;
        $issue->scoring_social_skills_behavior = $scoring_social_skills_behavior;
        $issue->scoring_social_skills_confidence = $scoring_social_skills_confidence;
        $issue->scoring_social_skills_participation = $scoring_social_skills_participation;
        $issue->parents_discussion = $parents_discussion;
        $issue->parents_comment = $parents_comment;
        $issue->parents_evaluation = $parents_evaluation;
        $issue->creator_id = $creator_id;
        $issue->created_at = date('Y-m-d H:i:s');
        $issue->save();
        //add turm_student_rank
        // $today = date('Y-m-d');
        // $rank = new TermStudentRank;
        // $rank->student_id =  $issue['student_id'];
        // $rank->rank_id =  $request['rank_score'];
        // $rank->comment = $request['rank_content'];
        // $rank->rating_date = $today;
        // $rank->creator_id = $user_id;
        // $rank->class_id =$request->class['class_id_selected'];
        // $rank->save();

        if ($issue->save()) {
            return response()->json($issue);
        }
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());

        $user_data = $request->users_data;
//      $class = $request->class;
        $issue = $request->issue;
        $issueModel = Issue::find($id);

        if ($user_data) {
            $user_id = (int)$user_data->id;
        }
        $role_query = "SELECT tb.role_id from term_user_branch as tb WHERE tb.user_id = $user_id";
        $role = DB::selectOne(DB::raw($role_query));
        $user_role = $role->role_id;
        $teacher_id = null;
//      $cm_updated_user_id = $user_id;
        if ($user_role == APICode::ROLE_TEACHER) {
            $teacher_id = $user_id;
            $tc_updated_user_id = $teacher_id;
            $cm_updated_user_id = null;
            $tc_updated_at = date('Y-m-d H:i:s');
            $cm_updated_at = null;
            $issueModel->tc_updated_at = $tc_updated_at;
            $issueModel->tc_updated_user_id = $tc_updated_user_id;
        } else {
            $teacher_id = null;
            $cm_updated_user_id = $user_id;
            $tc_updated_user_id = null;
            $tc_updated_at = null;
            $cm_updated_at = date('Y-m-d H:i:s');
            $issueModel->cm_updated_user_id = $cm_updated_user_id;
            $issueModel->cm_updated_at = $cm_updated_at;
        }

//      $book_id = $issue['book_id'];
        // dd($class, $issue, $book_id);

//      $classdate = $class['class_start_date'];
//      $cm_id = $class['cm_id'];
//      $teacher_id = 1;
//      $student_id = $issue['student_id'];
//      $book_from = $issue['book_from'];
//      $book_to = $issue['book_to'];
//      $class_id = $class['class_id'];
        $cm_trail_report = $issue['cm_trail_report'];
        $cm_summary = $issue['cm_summary'];
        $tc_gretest_strength = $issue['tc_gretest_strength'];
        $cm_gretest_strength = $issue['cm_gretest_strength'];
        $tc_need_improved = $issue['tc_need_improved'];
        $cm_need_improved = $issue['cm_need_improved'];
//      $trail_report = $issue['trail_report'];
//      $class_term = $issue['class_term'];
//      $summary = $issue['summary'];
//      $scoring_classroom_duty_attendance = $issue['scoring_classroom_duty_attendance'];
//      $scoring_classroom_duty_punctuality = $issue['scoring_classroom_duty_punctuality'];
//      $scoring_classroom_duty_homework = $issue['scoring_classroom_duty_homework'];
//      $scoring_reading_comprehension = $issue['scoring_reading_comprehension'];
//      $scoring_reading_fluency = $issue['scoring_reading_fluency'];
//      $scoring_writing_sentence_structure = $issue['scoring_writing_sentence_structure'];
//      $scoring_writing_creativity = $issue['scoring_writing_creativity'];
//      $scoring_ctp_speaking_expression = $issue['scoring_ctp_speaking_expression'];
//      $scoring_ctp_teamwork = $issue['scoring_ctp_teamwork'];
//      $scoring_social_skills_behavior = $issue['scoring_social_skills_behavior'];
//      $scoring_social_skills_confidence = $issue['scoring_social_skills_confidence'];
//      $scoring_social_skills_participation = $issue['scoring_social_skills_participation'];
        $parents_discussion = $issue['parents_discussion'];
        $parents_comment = $issue['parents_comment'];
        $parents_evaluation = $issue['parents_evaluation'];
//      $creator_id = $user_id;

        // $issue->classdate = $classdate;
        // $issue->book_id = $book_id;
        // $issue->book_from = $book_from;
        // $issue->book_to = $book_to;
        // $issue->cm_id = $cm_id;
        // $issue->class_id = $class_id;
        // $issue->teacher_id = $teacher_id;
        // $issue->student_id = $student_id;
        // $issue->cm_gretest_strength = $cm_gretest_strength;
        // $issue->cm_need_improved = $cm_need_improved;
        // $issue->trail_report = $trail_report;
        // $issue->class_term = $class_term;
        // $issue->summary = $summary;
        // $issue->scoring_classroom_duty_attendance = $scoring_classroom_duty_attendance;
        // $issue->scoring_classroom_duty_punctuality = $scoring_classroom_duty_punctuality;
        // $issue->scoring_classroom_duty_homework = $scoring_classroom_duty_homework;
        // $issue->scoring_reading_comprehension = $scoring_reading_comprehension;
        // $issue->scoring_reading_fluency = $scoring_reading_fluency;
        // $issue->scoring_writing_sentence_structure = $scoring_writing_sentence_structure;
        // $issue->scoring_writing_creativity = $scoring_writing_creativity;
        // $issue->scoring_ctp_speaking_expression = $scoring_ctp_speaking_expression;
        // $issue->scoring_ctp_teamwork = $scoring_ctp_teamwork;
        // $issue->scoring_social_skills_behavior = $scoring_social_skills_behavior;
        // $issue->scoring_social_skills_confidence = $scoring_social_skills_confidence;
        // $issue->scoring_social_skills_participation = $scoring_social_skills_participation;
        // $issue->creator_id = $creator_id;
        // $issue->tc_updated_at = $tc_updated_at;
        // $issue->cm_updated_at = $cm_updated_at;
        // $issue->tc_updated_user_id = $tc_updated_user_id;
        // $issue->cm_updated_user_id = $cm_updated_user_id;
        $issueModel->cm_trail_report = $cm_trail_report;
        $issueModel->cm_summary = $cm_summary;
        $issueModel->cm_gretest_strength = $cm_gretest_strength;
        $issueModel->cm_need_improved = $cm_need_improved;
        $issueModel->tc_gretest_strength = $tc_gretest_strength;
        $issueModel->tc_need_improved = $tc_need_improved;
        $issueModel->parents_discussion = $parents_discussion;
        $issueModel->parents_comment = $parents_comment;
        $issueModel->parents_evaluation = $parents_evaluation;
        $issueModel->save();
        // add term_student_rank
        $r = $request->rank;
        $rank = new TermStudentRank;
        $resp = (Object)['success' => true];
        if ($r['rank_id'] && (int)$r['rank_id'] > 0) {
            $today = date('Y-m-d');
            $rank->student_id = $issue['student_id'];
            $rank->rank_id = $r['rank_id'];
            $rank->comment = $request['rank_content'];
            $rank->rating_date = $today;
            $rank->creator_id = $user_id;
            $rank->comment = $request->rank['comment'];
            $rank->class_id = $request->class['class_id_selected'];
            $rank->save();
            if ($rank->save()) {
                $resp = $rank;
            }
        }
        // $rank = TermStudentRank::find($request->rank['term_rank_id']);
        // $rank->rank_id = $request->rank['rank_id'];
        // $rank->comment = $request->rank['comment'];
        // $rank->save();
        return response()->json($resp);
    }

    public function show($id)
    {
        $query = "SELECT i.*
                  FROM issues as i
                  WHERE i.id = $id";
        $issue = DB::selectOne(DB::raw($query));

        return response()->json($issue);
    }

    public function getIssueByStudent($student_id)
    {
        // dd($student_id);
        $query = "SELECT i.*, s.name as student_name, s.nick as student_nick
                  FROM issues AS i
                  LEFT JOIN students AS s ON s.id = i.student_id
                  WHERE i.student_id = $student_id ORDER BY i.id DESC";
        $issue = DB::selectOne(DB::raw($query));
        $query_rank = "SELECT id As term_rank_id, student_id,rank_id, created_at,comment
      FROM term_student_rank
      Where student_id = $student_id
      ORDER BY term_student_rank.id DESC";
        $rank = DB::selectOne(DB::raw($query_rank));

        return response()->json(array('issue' => $issue, 'rank' => $rank));
    }

    public function getTermByStudent($student_id)
    {
        $query_rank = "SELECT student_id,rank_id, created_at,comment
              FROM term_student_rank
              Where student_id = $student_id
              ORDER BY term_student_rank.id DESC";
        $rank = DB::selectOne(DB::raw($query_rank));
        return response()->json($rank);

    }

    public function getIssueListByStudent($student_id)
    {
        $query = "SELECT i.*,
                    s.name as student_name,
                    bk.name as book_name,
                    t.ins_name AS teacher_name
                  FROM issues AS i
                  LEFT JOIN students AS s ON s.id = i.student_id
                  LEFT JOIN teachers AS t ON i.teacher_id = t.id
                  LEFT JOIN books AS bk ON bk.id = i.book_id
                  WHERE i.student_id = '$student_id' ORDER BY i.id DESC";
        $issue = DB::select(DB::raw($query));

        return response()->json($issue);
    }

    public function getStudentIssue($id)
    {
        $query = "SELECT s.* FROM students as s WHERE s.id =$id";
        $student = DB::selectOne(DB::raw($query));
        return response()->json($student);
    }

    public function getStudentIssueById($id)
    {
        $query = "SELECT i.*,
                    s.name as student_name,
                    bk.name as book_name
                  FROM issues AS i
                  LEFT JOIN students AS s ON s.id = i.student_id
                  LEFT JOIN books AS bk ON bk.id = i.book_id
                  WHERE i.id = $id";
        $issue = DB::select(DB::raw($query));

        $scores = $this->getScoresList();

        return response()->json([
            "issue" => $issue,
            "scores" => $scores
        ]);
    }

    public function getScoresList()
    {
        $query = "SELECT s.* FROM  scoring_guidelines as s WHERE s.score <= 10";
        $scores = DB::select(DB::raw($query));
        return response()->json($scores);
    }

    public function getBooksList()
    {
        $query = "SELECT id, name, product_id, DATE_FORMAT(start_date, '%Y-%m-%d') AS start_date, DATE_FORMAT(end_date, '%Y-%m-%d') AS end_date FROM  books WHERE `status` > 0 AND start_date <= CURDATE() AND end_date >= CURDATE()";
        $books = DB::select(DB::raw($query));
        return response()->json($books);
    }

    public function getClassInfo(Request $request, $classId)
    {
        $response = new Response();
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        if ($request->users_data) {
            $issue = new Issue();
            $code = empty($classId) ? APICode::WRONG_PARAMS : APICode::SUCCESS;
            $data = $issue->getClassInfo($classId);
        }
        return $response->formatResponse($code, $data);
    }

    public function getStudentsByClass(Request $request, $classId, $date = null)
    {
        $response = new Response();
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        if ($request->users_data) {
            $issue = new Issue();
            $code = empty($classId) ? APICode::WRONG_PARAMS : APICode::SUCCESS;
            $query = "SELECT c.id AS enrolment_id,
                s.id AS student_id,
                s.name AS student_name,
                s.crm_id AS crm_id,
                s.gud_name1,c.enrolment_start_date,c.enrolment_last_date,c.summary_sessions,
                c.id AS contract_id,c.branch_id,c.product_id,c.class_id
              FROM contracts c
              LEFT JOIN students s ON c.student_id = s.id
              WHERE c.class_id = $classId AND s.status >0  AND c.status!=7 AND c.enrolment_last_date >= CURRENT_DATE"; 
            $data =u::query($query);
            foreach($data AS $k=>$row){
                $data[$k]->done_sessions = self::getDoneSessions($row);
            }
        }
        return $response->formatResponse($code, $data);
    }
    public function getDoneSessions($data){
        $tmp_holiDay = u::getPublicHolidays(0, $data->branch_id, $data->product_id);
        $reserved_dates = JobsController::getReservedDates_transfer($data->contract_id);
        if (!empty($reserved_dates)) {
          $holiDay = array_merge($tmp_holiDay, $reserved_dates);
        }else{
          $holiDay = $tmp_holiDay;
        }
        $class_days = u::getClassDays($data->class_id);
        $result = u::calculatorSessions($data->enrolment_start_date, date('Y-m-d'), $holiDay, $class_days);
        return $result ? $result->total : NULL;
    }

    public function loadClassInfo(Request $request, $class_id)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        $session = $request->users_data;
        $user_id = $session->id;
        $user_role = null;
        $role_query = "SELECT tb.role_id from term_user_branch as tb WHERE tb.user_id = $user_id";
        $role = DB::selectOne(DB::raw($role_query));
        $user_role = isset($role) ? $role->role_id : 0;
        if ($session = $request->users_data) {
            // $user_id = null;
            $class_id = (int)$class_id;
            $data = (Object)[];
            $class_info = u::first("SELECT
          c.id AS class_id_selected,
          c.id AS class_id,
          c.cls_name AS class_name,
          c.product_id,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          COALESCE(h.total, 0) AS total_students,
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
          LEFT JOIN (SELECT COUNT(e.id) AS total, c.id FROM `contracts` AS e
            LEFT JOIN classes AS c ON e.class_id = c.id WHERE
            (e.end_date >= CURDATE() AND e.enrolment_end_date <= c.cls_enddate)
            OR
            (e.start_date <= c.cls_enddate AND e.enrolment_end_date >= c.cls_enddate)
            AND c.id = $class_id) AS h ON h.id = c.id
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
            CONCAT(u2.full_name, ' - ', u2.username) AS cm_name
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

            foreach ($students_list as $st) {
                $st_id = $st->student_id;
                $arrDefault = [
                    'A1' => null,
                    'A2' => null,
                    'A3' => null,
                    'B1' => null,
                    'B2' => null,
                    'B3' => null
                ];
                //$qr = "SELECT i.* FROM issues as i where i.student_id = $st->student_id ORDER BY i.id DESC";
                $qr = "
            SELECT b.name as book_name,i.id 
            from books as b 
            JOIN issues as i on i.book_id = b.id
            LEFT JOIN classes as cl on cl.id = i.class_id 
            WHERE i.student_id = $st_id
            AND cl.id = $class_id
          ";
                $issue = DB::select(DB::raw($qr));
                $arrValue = [];
                foreach ($issue as $iss) {
                    $arrValue[$iss->book_name] = $iss->id;
                }
                $newArray = array_merge($arrDefault, $arrValue);
                $st->issue = $newArray;
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
                                $weekdays_text .= $display;
                                $displayed[] = md5($display);
                                if ($i < count($weekdays)) {
                                    $weekdays_text .= ', ';
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
                            $lab = substr($sta, 0, 5) . '~' . substr($end, 0, 5);
                            $sessions->$sid->shift = $lab;
                            $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information . ' ' . $lab : $lab;
                            $nashifts_text .= $lab;
                            $displayed[] = md5($lab);
                            if ($i < count($nashifts)) {
                                $nashifts_text .= ', ';
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
                                $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information . ' ' . "Room: $inf" : "Room: $inf";
                                $namrooms_text .= $inf;
                                $displayed[] = md5($inf);
                                if ($i < count($namrooms)) {
                                    $namrooms_text .= ', ';
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
                            $lab = md5($ava) == md5('avatar.png') ? '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/avatar.png"/> ' . $ten : '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/teachers/' . $ava . '"/> ' . $ten;
                            $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                            if (!in_array(md5($lab), $displayed)) {
                                $sessions->$sid->teacher_avatar = $ava;
                                $sessions->$sid->teacher_name = $ten;
                                $sessions->$sid->teacher_label = $lab;
                                $teachers_text .= $lab;
                                $displayed[] = md5($lab);
                                if ($i < count($teachers)) {
                                    $teachers_text .= ', ';
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
                        $time_place .= $o->information;
                        if ($i < count((array)$sessions)) {
                            $time_place .= '<br/>';
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
            $class_info->class_time = date('d/m/Y', strtotime($class_info->class_start_date)) . ' ~ ' . date('d/m/Y', strtotime($class_info->class_end_date));
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

    public function loadBranches(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = [];
            $code = APICode::SUCCESS;
            $user_id = $session->id;
            $role_id = $session->role_id;
            $branches = $session->branches_ids;
            $where = " id > 0 AND status > 0  ";
            if ($role_id < ROLE_ZONE_CEO) {
                $where .= "AND id IN ($branches)";
            }
            $query = "SELECT id, `name` FROM branches WHERE $where";
            $data = u::query($query);
        }
        return $response->formatResponse($code, $data);
    }

    public function loadSemesters(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = [];
            $code = APICode::SUCCESS;
            $user_id = $session->id;
            $role_id = $session->role_id;
            $branches = $session->branches_ids;
            // $where = "id > 0 AND status > 0 AND start_date <= CURDATE() AND end_date >= CURDATE()";
            $where = "id > 0 AND status > 0";
            $query = "SELECT id, `name` FROM semesters WHERE $where ORDER BY start_date DESC";
            $data = u::query($query);
        }
        return $response->formatResponse($code, $data);
    }

    public function loadClasses(Request $request, $branch_id, $semester_id)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = [];
            $code = APICode::SUCCESS;
            $user_id = $session->id;
            $role_id = $session->role_id;
            $myclass = u::query("SELECT DISTINCT(class_id) FROM term_user_class WHERE user_id = $user_id");
            $classes = [];
            if ($myclass) {
                foreach ($myclass as $iclass) {
                    $classes[] = $iclass->class_id;
                }
            }
            if (count($classes)) {
                $classes = implode(',', $classes);
            }

            $branches = $branch_id ? $branch_id : $session->branches_ids;
            $where = "id > 0 AND status > 0 AND branch_id IN (0,$branches) AND semester_id = $semester_id";
            $query = "SELECT id, id AS item_id, 'program' AS item_type, `name` AS `text`, parent_id, 'fa fa-folder' AS icon FROM programs WHERE $where
                  UNION ALL
                  SELECT CONCAT(999, c.id) AS id, c.id AS item_id, 'class' AS item_type, c.cls_name AS `text`, c.program_id AS parent_id, IF(c.cm_id > 0, 'fa fa-file-text-o', 'fa fa-user-times') AS icon
                  FROM classes AS c INNER JOIN programs AS p ON c.program_id = p.id
                  WHERE (c.teacher_id = $user_id OR c.cm_id = $user_id OR  ( $role_id IN ( 999999999 ,55,56,37,686868 ) ) ) AND c.cls_enddate >= CURRENT_DATE AND c.cls_iscancelled = 'no' AND p.status > 0 AND (c.cm_id IS NOT NULL AND c.cm_id > 0) AND p.branch_id IN ($branches) AND p.semester_id = $semester_id";
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

    public function contracts(Request $request, $pagination, $search)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $pagination = json_decode($pagination);
            $search = json_decode($search);
            $class_id = isset($search->class) ? (int)$search->class : 0;
            $branch_id = isset($search->branch) ? (int)$search->branch : 0;
            $keyword = isset($search->keyword) ? $search->keyword : '';
            $where = '';
            $limit = '';
            if ($pagination->cpage && $pagination->limit) {
                $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
                $limit .= " LIMIT $offset, $pagination->limit";
            }
            if ($keyword != '') {
                $where .= " AND
            ( s.crm_id LIKE '$keyword%'
            OR s.stu_id LIKE '$keyword%'
            OR s.accounting_id LIKE '$keyword%'
            OR s.name LIKE '%$keyword%'
            OR s.nick LIKE '%$keyword%'
            OR s.email LIKE '%$keyword%'
            OR s.phone LIKE '$keyword%'
            OR s.gender LIKE '$keyword')";
            }

            $available_students = u::first("SELECT ((SELECT max_students FROM classes WHERE id = $class_id)
            -
            (SELECT COUNT(e.id) FROM `contracts` AS e
            LEFT JOIN classes AS c ON c.class_id = c.id WHERE
            (e.end_date >= CURDATE() AND c.enrolment_end_date <= c.cls_enddate)
            OR
            (e.start_date <= c.cls_enddate AND .enrolment_end_date >= c.cls_enddate)
            AND c.id = $class_id)) AS total");

            $total_available = (int)$available_students->total;
            $code = APICode::SUCCESS;
            $data = (Object)[];
            $select = "SELECT
                    c.id AS contract_id,
                    c.code AS contract_code,
                    s.id AS student_id,
                    s.stu_id AS student_lms_id,
                    s.accounting_id AS student_accounting_id,
                    c.type AS customer_type,
                    s.name AS student_name,
                    s.nick AS student_nick,
                    CONCAT(s.name, ' - ', COALESCE(s.stu_id, s.crm_id)) AS label,
                    s.school AS student_school,
                    s.school_grade AS student_school_grade,
                    s.phone AS school_phone,
                    c.start_date AS contract_start_date,
                    t.name AS tuition_fee_name,
                    t.price AS tuition_fee_price,
                    t.receivable AS tuition_fee_receivable,
                    CONCAT(u0.full_name, ' - ', u0.username) AS cm_name,
                    CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
                    (c.total_discount + c.must_charge - c.debt_amount) AS charged_total,
                    (ROUND((c.total_discount + c.must_charge - c.debt_amount) / (t.receivable / t.`session`))) AS available_sessions,
                    0 AS checked,
                    '' AS class_date";
            $query = "FROM contracts AS c
                    LEFT JOIN students AS s ON c.student_id = s.id
                    LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
                    LEFT JOIN users AS u0 ON c.cm_id = u0.id
                    LEFT JOIN users AS u1 ON c.ec_id = u1.id
                    LEFT JOIN reserves AS r ON c.student_id = r.student_id
                    LEFT JOIN pendings AS p ON c.student_id = p.student_id
                    LEFT JOIN classes AS cl ON cl.program_id = c.program_id
                  WHERE c.branch_id = $branch_id AND c.enrolment_id IS NULL
                    AND c.status > 0
                    AND cl.id = $class_id
                    AND COALESCE(r.id, (r.start_date > CURDATE() OR r.end_date < CURDATE()), true)
                    AND COALESCE(p.id, (p.start_date > CURDATE() OR p.end_date < CURDATE()), true) $where GROUP BY c.student_id ";
            $classdate = u::query("SELECT cls_id, cjrn_id, cjrn_classdate FROM schedules WHERE class_id = $class_id AND cjrn_classdate > CURDATE() AND `status` > 0 ORDER BY cjrn_classdate ASC");
            $total = u::first("SELECT COUNT(z.id) AS total FROM (SELECT c.id $query) AS z");
            $total = (int)$total->total;
            $list = u::query("$select $query ORDER BY c.status DESC $limit");
            $data->contracts = $list;
            $data->search = $search;
            $data->available = $total_available;
            $data->class_dates = $classdate;
            $data->duration = $pagination->limit * 10;
            $data->pagination = apax_get_pagination($pagination, $total);
        }
        return $response->formatResponse($code, $data);
    }

    public function loadprogram(Request $request, $branch_id, $semester_id)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = [];
            $code = APICode::SUCCESS;
            $user_id = $session->id;
            $role_id = $session->role_id;
            $branches = $branch_id ? $branch_id : $session->branches_ids;
            $where = "id > 0 AND status > 0 AND branch_id IN (0,$branches) AND semester_id = $semester_id";
            $query = "SELECT id, id AS item_id, 'program' AS item_type, `name` AS `text`, parent_id, 'fa fa-folder' AS icon FROM programs WHERE $where
                  UNION ALL
                  SELECT CONCAT(999, c.id) AS id, c.id AS item_id, 'class' AS item_type, c.cls_name AS `text`, c.program_id AS parent_id, 'fa fa-file-text-o' AS icon
                  FROM classes AS c INNER JOIN programs AS p ON c.program_id = p.id
                  WHERE c.cls_iscancelled = 'no' AND p.status > 0 AND (c.cm_id IS NOT NULL AND c.cm_id > 0) AND p.branch_id IN ($branches) AND p.semester_id = $semester_id";
            $class = DB::select(DB::raw($query));
            if (count($class)) {
                foreach ($class as $item) {
                    $item->value = $item->id;
                    $item->opened = true;
                    $item->selected = false;
                    $item->disabled = false;
                    $item->loading = false;
                    $item->children = [];
                }
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function checkIssueExist($student_id)
    {
        $query = "SELECT IF(COUNT(i.id) > 0, 1,0) AS is_existed
                  FROM `issues` AS i
                    LEFT JOIN students AS s ON i.student_id = s.id
                  WHERE s.id = $student_id";
        $issue = DB::selectOne(DB::raw($query));
        return response()->json($issue);
    }

    public function checkAvailableBook($book_id)
    {
        $query = "SELECT if(start_date <= CURDATE() AND end_date >= CURDATE(), 1, 0) as available FROM books WHERE id = $book_id";
        // dd($query);
        $rs = DB::select(DB::raw($query));
        if (!empty($rs)) {
            return $rs[0]->available;
        } else {
            return 0;
        }
    }

    public function sendMail($student_id, $id)
    {
//        dd($student_id);
        $response = new Response();

        //#send_mail
        if (APP_ENV == 'product') {
            $mailModel = new Mail();
            $student = Student::find($student_id);
            $baseUrl = APP_URL;
            $code = $mailModel->sendSingleMail(
                [
                    "address" => $student->email,
                    "name" => ""
                ],
                "[Apax English] Thư thông báo tình hình học tập của $student->name",
                " Kính gửi Quý phụ huynh học sinh $student->name
                    <br><br>Trung tâm Apax English xin chân thành cảm ơn sự tín nhiệm và ủng hộ của Quý phụ huynh đối với chương trình Tiếng Anh của Apax suốt thời gian qua.
                    <br><br>Theo cơ chế mới của trung tâm, để phụ huynh dễ nắm bắt và theo dõi tình hình học tập của con một cách sát sao và chi tiết nhất, trung tâm sẽ gửi phiếu điểm của con với từng điểm mạnh điểm yếu của con cũng như từng kỹ năng, từng vấn đề của con trên lớp với thang điểm 5.
                    <br><br>Apax English xin gửi tới Quý phụ huynh nhận xét của giáo viên về học sinh sau khi kết thúc bộ sách A3 như link bên dưới. 
                    <br><br>" . $baseUrl . "/print/issue/$id/0"
            );
        }


        return $response->formatResponse($code, null);
    }

    public function createFeedback(Request $request)
    {
        $user_data = $request->users_data;
        $issue = new Issue();
        foreach (['student_id', 'class_id' ,'lesson_content', 'general_comment', 'base_skills', 'math_skills', 'creative_skills', 'logic_skills', 'classdate', 'level'] as $field) {
            if (!empty($request->{$field})) {
                $issue->{$field} = $request->{$field};
            }
        }
        $issue->feedback_type = (int)$request->feedback_type;
        $issue->creator_id = $user_data->id;
        $issue->created_at = date('Y-m-d H:i:s');
        $issue->save();

        if ($issue->save()) {
            return response()->json($issue);
        }
    }

    public function updateFeedback(Request $request, $id)
    {
        $feedback = Issue::query()->where('id', '=', $id)->update([
            'lesson_content' => $request->lesson_content,
            'general_comment' => $request->general_comment,
            'base_skills' => $request->base_skills,
            'math_skills' => $request->math_skills,
            'creative_skills' => $request->creative_skills,
            'logic_skills' => $request->logic_skills,
            'classdate' => $request->classdate,
            'level' => $request->level
        ]);
        return response()->json([
            'message' => $feedback === 1 ? 'success!' : 'error!'
        ]);
    }

    public function getFeedback($id)
    {
        $feedback = Issue::query()->where('id', '=', $id)->first();
        return response()->json(['data' => $feedback, 'message' => 'success!', 'code' => 200,]);
    }

    public function getPrintFeedback($classId, $studentId, $date)
    {
        $feedback = Issue::query()->where([
            ['class_id', '=', $classId],
            ['student_id', '=', $studentId],
            ['classdate', '=', $date]
        ])->first();
        $student = Student::query()->where(['id' => $studentId])->first();
        $clazz = Classes::query()->where(['id' => $classId])->first();
        $clazz->teacher = Teacher::query()->where('user_id', '=', $clazz->teacher_id)->first();
        $semester = Semester::query()->where(['id' => $clazz->semester_id])->first();
        return response()->json(['data' => [
            'feedback' => $feedback,
            'student' => $student,
            'class' => $clazz,
            'type' => $semester->name,
        ], 'message' => 'success!', 'code' => 200,]);
    }
    public function addStudentCare(Request $request)
    {
        u::query("INSERT INTO student_care (student_id,class_id,method,note,type_content,created_at,creator_id) VALUES
         ('$request->student_id','$request->class_id','$request->method','$request->note','$request->type_content','".date('Y-m-d H:i:s')."','".$request->users_data->id."')");
        return response()->json("ok");

    }
    public function getStudentsCare(Request $request, $student_id){
        $response = new Response();
        $code = APICode::SUCCESS;
        $arr_method = array(
            '1'=>'Nhắn tin',
            '2'=>'Gọi điện thoại',
            '3'=>'Trao đổi trực tiếp'
        );
        $arr_type_content = array(
            '1'=>'Thái độ trong sinh hoạt hàng ngày',
            '2'=>'Tình hình học tập',
            '3'=>'Bài tập',
            '4'=>'Định hướng',
            '5'=>'Nghỉ học/học bù',
            '6'=>'Ngưng học',
            '7'=>'Hạng mục thông báo chung',
            '8'=>'Chuyển lớp',
            '9'=>'Thành tích học tập',
            '10'=>'Học sinh mới nhập học',
            '11'=>'Test cuối kỳ',
            '12'=>'Quan hệ bạn bè',
            '13'=>'Khác',
        );
        $data = u::query("SELECT c.*,(SELECT full_name FROM users WHERE id=c.creator_id) AS creator_name, 
                (SELECT r.name FROM term_user_branch AS t LEFT JOIN roles AS r ON r.id=t.role_id WHERE t.user_id=c.creator_id AND t.status=1 LIMIT 1 ) AS role_name,
                c.created_at AS created_date
            FROM student_care AS c WHERE student_id=$student_id ORDER BY c.id DESC");
        foreach($data AS $k=>$row){
            $data[$k]->method_show = isset($arr_method[$row->method])?$arr_method[$row->method] :'';
            $data[$k]->type_content_show = isset($arr_type_content[$row->type_content])?$arr_type_content[$row->type_content]:'';
        }
        return $response->formatResponse($code, $data);
    }
    public function updateStudentCare(Request $request)
    {
        u::query("UPDATE student_care  SET 
            method = '$request->method',note='$request->note',type_content='$request->type_content',
            updated_at='".date('Y-m-d H:i:s')."',updator_id ='".$request->users_data->id."' 
            WHERE id = $request->log_id");
        return response()->json("ok");

    }
}


