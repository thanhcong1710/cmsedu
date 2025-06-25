<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Attendances;
use App\Models\CyberAPI;
use App\Models\Response;
use App\Models\Student;
use App\Providers\UtilityServiceProvider as u;
use App\Services\StudentReportService;
use App\Services\TemplateExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Exception;
use function _\size;

// use App\Http\Controllers\EffectAPIController;

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
class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $contracts = Contract::paginate(10);
        // return response()->json($contracts);
        $contracts = DB::table('contracts')->get();

        return response()->json($contracts);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, $pagination, $search, $sort)
    {
        $code = APICode::PERMISSION_DENIED;
        if ($session = $request->users_data) {
            $obj_student = new Student();
            $list = $obj_student->getStudentInfo($request);
            $pagination = json_decode($pagination);
            $data = [];
            $code = APICode::SUCCESS;
            if ($list && isset($list->data)) {
                $data['code'] = $code;
                $data['message'] = 'Success!';
                $data['data']['list'] = $list->data;
                $data['data']['sort'] = json_decode($sort);
                $data['data']['search'] = json_decode($search);
                $data['data']['duration'] = $pagination->limit * 10;
                $data['data']['pagination'] = apax_get_pagination($pagination, $list->total);
            }
        }

        return response()->json($data, $code);
    }

    /**
     * Build select for query by parameters from request.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    private function select($select = "list")
    {
        $query = "";
        $role_branch_ceo = ROLE_BRANCH_CEO;
        $role_region_ceo = ROLE_REGION_CEO;
        if ($select == "list") {
            $request = request();
            $search = json_decode($request->search);
            $product_id = isset($search->product) ? $search->product : null;
            $program_id = isset($search->program) ? $search->program : null;
            $query = "SELECT
                      DISTINCT(s.id),
                      s.accounting_id, 
                      s.crm_id AS crm_id,
                      s.stu_id AS lms_stu_id,
                      s.name AS student_name,
                      s.nick AS student_nick,
                      s.email AS student_email,
                      s.phone AS student_phone,
                      s.gender AS student_gender,
                      s.type AS student_type,
                      s.source,
                      s.status,
                      s.date_of_birth AS student_birthday,
                      COALESCE(s.gud_name1, s.gud_name2) AS parent_name,
                      COALESCE(s.gud_mobile1, s.gud_mobile2) AS parent_mobile,
                      COALESCE(s.gud_email1, s.gud_email2) AS parent_email,
                      s.address AS student_address,
                      COALESCE(s.avatar, 'noavatar.png') AS student_avatar,
                      s.school AS student_school,
                      s.school_grade AS student_school_grade,
                      s.attached_file AS student_profile,
                      b.name AS branch_name,
                      CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
                      CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
                      CONCAT(u3.full_name, ' - ', u3.username) AS creator_name,
                      (SELECT COUNT(id) FROM pendings WHERE student_id = s.id AND CURDATE() >= start_date AND CURDATE() <= end_date ORDER BY id DESC LIMIT 0, 1) is_pending,
                      (SELECT `status` FROM contracts WHERE student_id = s.id AND CURDATE() >= start_date AND CURDATE() <= end_date ORDER BY id DESC LIMIT 0, 1) contract_status,
                      (SELECT `status` FROM enrolments WHERE student_id = s.id ORDER BY id DESC LIMIT 0, 1) enrolment_status,
                      (SELECT cl.cls_name FROM classes cl LEFT JOIN enrolments e ON e.class_id = cl.id WHERE e.student_id = s.id ORDER BY e.id DESC LIMIT 0, 1) class_name,
                      (SELECT e.status FROM enrolments e WHERE e.student_id = s.id ORDER BY e.id DESC LIMIT 0, 1) class_status,
                      s2.name AS sibling_name";
            if ($program_id) {
                $query .= " , pr.name AS program_name,
                            pr.id AS program_id";
            }
            if ($product_id) {
                $query .= " , p.name AS product_name,
                            p.id AS program_id";
            }
        } elseif ($select == "suggest") {
            $query = "SELECT
          s.id AS student_id,
          s.stu_id AS lms_id,
          s.accounting_id, 
          s.gender,
          s.type,
          s.school_grade,
          s.name,
          s.nick,
          s.email AS stu_email,
          s.phone AS home_phone,
          s.date_of_birth,
          s.gud_name1,
          s.gud_mobile1,
          s.gud_name2,
          s.gud_mobile2,
          s.address,
          s.school,
          s.branch_id AS branch_id,
          COALESCE(c.trial_done, 0) AS trail_done,
          CONCAT(s.name, ' - ', COALESCE(s.stu_id, s.crm_id)) AS label,
          COALESCE(s.gud_name1, s.gud_name2) AS parent_name,
          COALESCE(s.gud_mobile1, s.gud_mobile2) AS parent_mobile,
          COALESCE(s.gud_email1, s.gud_email2) AS parent_email,
          t.status AS student_status,
          b.name AS branch_name,
          b.brch_id AS branch_lms_id,
          r.id AS region_id,
          r.name AS region_name,
          u0.id AS cm_id,
          u1.id AS ec_id,
          u2.id AS ec_leader_id,
          u3.id AS om_id,
          u4.id AS ceo_branch_id,
          u5.id AS ceo_region_id,
          CONCAT(u0.full_name, ' - ', u0.username) AS cm_name,
          CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
          CONCAT(u2.full_name, ' - ', u2.username) AS ec_leader_name,
          CONCAT(u3.full_name, ' - ', u3.username) AS om_name,
          (SELECT u4.id FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo') AS ceo_branch_id,
          (SELECT u4.full_name FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo') AS ceo_branch_name,
          (SELECT u5.id FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_ceo') AS ceo_region_id,
          (SELECT u5.full_name FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_ceo') AS ceo_region_name";
        }
        return $query;
    }

    /**
     * Build base query by parameters from request.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    private function query($select = "list")
    {
        $query = "";
        if ($select == "list") {
            $query = "FROM students s
          LEFT JOIN students s2 ON s.sibling_id = s2.id
          LEFT JOIN branches b ON s.branch_id = b.id
          LEFT JOIN term_student_user t ON t.student_id = s.id
          LEFT JOIN users u1 ON t.ec_id = u1.id
          LEFT JOIN users u2 ON t.cm_id = u2.id
          LEFT JOIN users u3 ON s.creator_id = u3.id
          LEFT JOIN users u4 ON u1.superior_id = u4.hrm_id
          LEFT JOIN contracts c ON c.student_id = s.id
        WHERE s.id > 0 AND s.status >0";
        } elseif ($select == "suggest") {
            $role_branch_ceo = ROLE_BRANCH_CEO;
            $role_region_ceo = ROLE_REGION_CEO;
            $query = "FROM students s
        LEFT JOIN term_student_user t ON t.student_id = s.id
        LEFT JOIN branches b ON t.branch_id = b.id
        LEFT JOIN regions r ON t.region_id = r.id
        LEFT JOIN users u0 ON t.cm_id = u0.id
        LEFT JOIN users u1 ON t.ec_id = u1.id
        LEFT JOIN users u2 ON u1.superior_id = u2.hrm_id
        LEFT JOIN users u3 ON u0.superior_id = u3.hrm_id
        LEFT JOIN term_user_branch tu ON t.branch_id = tu.branch_id
        LEFT JOIN (
            SELECT ct.id id, ct.type `type`, ct.passed_trial trial_done
            FROM contracts ct
            LEFT JOIN students st ON ct.student_id = st.id
            WHERE ct.type = 0) c ON c.id = s.id
        WHERE s.id > 0 AND s.status >0";
        }
        return $query;
    }

    /**
     * Build select for query by parameters from request.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    private function where($request)
    {
        $search = json_decode($request->search);
        $session = $request->users_data;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $branches = $search->branch ? (int)$search->branch : $session->branches_ids;
        $where = "AND s.branch_id IN ($branches)";
        if ($role_id && $role_id == ROLE_EC_LEADER) {
            $where .= " AND (u1.id = $user_id OR t.ec_id = $user_id OR t.ec_id IN (SELECT u1.id FROM users u1 LEFT JOIN users u2 ON u1.superior_id = u2.hrm_id WHERE u2.id = $user_id))";
        }
        if ($role_id && $role_id == ROLE_EC) {
            $where .= " AND t.ec_id = $user_id";
        }
        if ($search->gender != '') {
            $where .= " AND s.gender = '$search->gender'";
        }
        if ($search->customer_type != '') {
            $where .= " AND s.type = $search->customer_type";
        }
        if (isset($search->ec) && $search->ec != '') {
            $where .= " AND t.ec_id = '$search->ec'";
        }
        // if ($role_id && !in_array($role_id, [ROLE_EC, ROLE_EC_LEADER]) && isset($search->cm) && $search->cm != '') {
        //   $where.= " AND t.cm_id = '$search->cm'";
        // }
        if (isset($search->keyword) && isset($search->field)) {
            $keyword = $search->keyword;
            $field = $search->field;
            if ($field == 'all') {
                $where .= " AND
            ( s.crm_id LIKE '%$keyword%'
            OR s.id LIKE '$keyword%'
            OR s.name LIKE '%$keyword%'
            OR s.nick LIKE '%$keyword%'
            OR s.email LIKE '%$keyword%'
            OR s.phone LIKE '$keyword%'
            OR s.gender LIKE '$keyword'
            OR s.gud_mobile1 LIKE '$keyword%'
            OR s.gud_mobile2 LIKE '$keyword%'
            OR s.gud_name1 LIKE '$keyword%'
            OR s.gud_name2 LIKE '$keyword%'
            OR s.gud_email1 LIKE '$keyword%'
            OR s.gud_email2 LIKE '$keyword%'
            OR s.gud_card1 LIKE '$keyword%'
            OR s.gud_card2 LIKE '$keyword%')";
            } else if ($field != '' && $keyword) {
                $where .= " AND (s.$field LIKE '%$keyword%')";
            }
        }
        return $where;
    }

    /**
     * Build base query by parameters from request.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    private function data($request)
    {
        $pagination = json_decode($request->pagination);
        $sort = json_decode($request->sort);
        $order = "";
        $limit = "";
        if ($sort->by && $sort->to) {
            $order .= " ORDER BY $sort->by $sort->to";
        }
        if ($pagination->cpage && $pagination->limit) {
            $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
            $limit .= " LIMIT $offset, $pagination->limit";
        }
        $total = "SELECT COUNT(DISTINCT(s.id)) total";
        $where = $this->where($request);
        return $where;
        $select = $this->select();
        $query = $this->query();
        $final_query = "$select $query AND s.id IN (SELECT * FROM (SELECT s.id FROM students s LEFT JOIN term_student_user t ON t.student_id = s.id LEFT JOIN users u1 ON t.ec_id = u1.id LEFT JOIN users u2 ON t.cm_id = u2.id WHERE s.id > 0 AND s.status >0 $where $order $limit) temp_data )";
        $data = u::query($final_query);
        if ($data) {
            foreach ($data as $item) {
                $avatar = $item->student_avatar;
                $link_path_avatar = AVATAR_LINK . $avatar;
                $real_path_avatar = AVATAR . DS . str_replace('/', DS, $link_path_avatar);
                $item->student_avatar = file_exists($real_path_avatar) ? $link_path_avatar : AVATAR_LINK . 'noavatar.png';
            }
        }
        $total = u::first("$total FROM students s LEFT JOIN term_student_user t ON t.student_id = s.id LEFT JOIN users u1 ON t.ec_id = u1.id LEFT JOIN users u2 ON t.cm_id = u2.id WHERE s.id > 0 AND s.status >0 $where");
        $total = $total->total;
        $result = (Object)['data' => $data, 'total' => $total];
        return $result;
    }

    public function updateTrialComment(Request $request)
    {
        // dd($request->all());
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $infor = (object)$request->all();
            $stuid = $infor->stuid;
            $date1 = $infor->date1;
            $date2 = $infor->date2;
            $date3 = $infor->date3;
            $note1 = $infor->note1;
            $note2 = $infor->note2;
            $note3 = $infor->note3;
            $comt1 = $infor->comt1;
            $comt2 = $infor->comt2;
            $comt3 = $infor->comt3;
            $file1 = ada()->upload($infor->file1);
            $file2 = ada()->upload($infor->file2);
            $file3 = ada()->upload($infor->file3);
            $sess1 = 1;
            $sess2 = 2;
            $sess3 = 3;
            $check_data = u::query("SELECT * FROM trial_reports WHERE student_id = $stuid");
            $check1 = false;
            $check2 = false;
            $check3 = false;
            if (count($check_data)) {
                foreach ($check_data as $check_dat) {
                    if ((int)$check_dat->session_no == 1) {
                        $check1 = true;
                    } else if ((int)$check_dat->session_no == 2) {
                        $check2 = true;
                    } else if ((int)$check_dat->session_no == 3) {
                        $check3 = true;
                    }
                }
            }
            $query1 = "";
            $query2 = "";
            $query3 = "";
            if ($check1) {
                $query1 = "UPDATE trial_reports SET learning_date = '$date1', comment = '$comt1', note = '$note1', file = '$file1' WHERE student_id = $stuid AND session_no = 1";
            } else {
                $query1 = "INSERT INTO trial_reports (student_id, session_no, learning_date, comment, note, file) VALUES ($stuid, 1, '$date1', '$comt1', '$note1', '$file1')";
            }
            if ($check2) {
                $query2 = "UPDATE trial_reports SET learning_date = '$date2', comment = '$comt2', note = '$note2', file = '$file2' WHERE student_id = $stuid AND session_no = 2";
            } else {
                $query2 = "INSERT INTO trial_reports (student_id, session_no, learning_date, comment, note, file) VALUES ($stuid, 2, '$date2', '$comt2', '$note2', '$file2')";
            }
            if ($check3) {
                $query3 = "UPDATE trial_reports SET learning_date = '$date3', comment = '$comt3', note = '$note3', file = '$file3' WHERE student_id = $stuid AND session_no = 3";
            } else {
                $query3 = "INSERT INTO trial_reports (student_id, session_no, learning_date, comment, note, file) VALUES ($stuid, 3, '$date3', '$comt3', '$note3', '$file3')";
            }
            u::query($query1);
            u::query($query2);
            u::query($query3);
            $data = (Object)['success' => true];
            $code = APICode::SUCCESS;
        }
        return $response->formatResponse($code, $data);
    }

    public function loadECCM(Request $request, $branch_id)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = (object)[];
            $code = APICode::SUCCESS;
            $uid = $session->id;
            $role_id = $session->role_id;
            $branches_ids = $branch_id ? (int)$branch_id : $session->branches_ids;
            $ecs = [];
            $cms = [];
            if (in_array($role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS, ROLE_ZONE_CEO, ROLE_REGION_CEO, ROLE_BRANCH_CEO, ROLE_OM, ROLE_CM])) {
                $ecs[] = (object)['id' => '', 'name' => 'Lọc theo nhân viên EC'];
                $cms[] = (object)['id' => '', 'name' => 'Lọc theo nhân viên CM'];
                $ecs = array_merge($ecs, u::query("SELECT u.id, CONCAT(u.full_name, ' - ', u.username) AS name FROM users AS u LEFT JOIN term_user_branch AS t ON t.user_id = u.id WHERE u.status > 0 AND t.branch_id IN ($branches_ids) AND (t.role_id IN (" . ROLE_EC_LEADER . ", " . ROLE_EC . "))"));
                $cms = array_merge($cms, u::query("SELECT u.id, CONCAT(u.full_name, ' - ', u.username) AS name FROM users AS u LEFT JOIN term_user_branch AS t ON t.user_id = u.id WHERE u.status > 0 AND t.branch_id IN ($branches_ids) AND (t.role_id IN (" . ROLE_OM . ", " . ROLE_CM . "))"));
            } elseif ($role_id === ROLE_EC_LEADER) {
                $ecs[] = (object)['id' => '', 'name' => 'Lọc theo nhân viên EC'];
                $ecs = array_merge($ecs, u::query("SELECT u.id, CONCAT(u.full_name, ' - ', u.username) AS name FROM users AS u WHERE u.id = $uid UNION SELECT e.id, CONCAT(e.full_name, ' - ', e.username) AS name FROM users AS u LEFT JOIN users AS e ON e.superior_id = u.hrm_id LEFT JOIN term_user_branch AS t ON t.user_id = u.id WHERE u.status > 0 AND t.branch_id IN ($branches_ids) AND (t.role_id IN (" . ROLE_EC_LEADER . ", " . ROLE_EC . ")) AND u.id = $uid"));
            } elseif ($role_id === ROLE_OM) {
                $cms[] = (object)['id' => '', 'name' => 'Lọc theo nhân viên CS'];
                $cms = array_merge($cms, u::query("SELECT u.id, CONCAT(u.full_name, ' - ', u.username) AS name FROM users AS u WHERE u.id = $uid UNION SELECT e.id, CONCAT(e.full_name, ' - ', e.username) AS name FROM users AS u LEFT JOIN users AS e ON e.superior_id = u.hrm_id LEFT JOIN term_user_branch AS t ON t.user_id = u.id WHERE u.status > 0 AND t.branch_id IN ($branches_ids) AND (t.role_id IN (" . ROLE_OM . ", " . ROLE_CM . ")) AND u.id = $uid"));
            } elseif ($role_id === ROLE_EC) {
                $ecs = u::query("SELECT id, CONCAT(full_name, ' - ', username) AS name FROM users WHERE id = $uid");
            } elseif ($role_id === ROLE_CM) {
                $cms = u::query("SELECT id, CONCAT(full_name, ' - ', username) AS name FROM users WHERE id = $uid");
            }
            $data->ecs = $ecs;
            $data->cms = $cms;
        }
        return $response->formatResponse($code, $data);
    }

    public function suggest(Request $request, $key, $branch_id)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        $role_branch_ceo = ROLE_BRANCH_CEO;
        $role_region_ceo = ROLE_REGION_CEO;
        if ($key) {
            $keys = explode('-', $key);
            $key1 = '';
            $key2 = $key;
            if (count($keys) == 2) {
                $key1 = trim($keys[0]);
                $key2 = trim($keys[1]);
            }
            $query = '';
            if ($session = json_decode($request->authorized)) {
                $session = $request->users_data;
                $user_id = $session->id;
                $role_id = $session->role_id;
                $branches = $branch_id ? (int)$branch_id : $session->branches_ids;
                $where = "AND s.branch_id IN ($branches)";
                if ($role_id == ROLE_EC_LEADER) {
                    $where .= " AND t.ec_leader_id = $user_id";
                }
                if ($role_id == ROLE_EC) {
                    $where .= " AND t.ec_id = $user_id";
                }
                $key2 = $key2 ? trim($key2) : '';
                $where .= $key1 ? " AND (s.stu_id LIKE '$key1%' AND " : " AND (s.stu_id LIKE '$key%' OR ";
                $where .= $key2 ? " s.name LIKE '$key2%' OR s.nick LIKE '$key2%')" : ')';
                $where .= $branch_id ? " AND s.branch_id = $branch_id" : '';
                $query = "SELECT
            s.id student_id,
            s.stu_id lms_id,
            s.name,
            s.nick,
            s.email stu_email,
            s.phone home_phone,
            s.gender,
            s.type,
            COALESCE(s.sibling_id, 0) sibling,
            s.school_grade,
            s.date_of_birth,
            COALESCE(c.trial_done, 0) trail_done,
            CONCAT(s.name, ' - ', COALESCE(s.stu_id, crm_id)) label,
            COALESCE(s.gud_name1, s.gud_name2) parent_name,
            COALESCE(s.gud_mobile1, s.gud_mobile2) parent_mobile,
            COALESCE(s.gud_email1, s.gud_email2) parent_email,
            s.gud_name1,
            s.gud_mobile1,
            s.gud_name2,
            s.gud_mobile2,
            s.address,
            s.school,
            t.status student_status,
            s.branch_id branch_id,
            b.name branch_name,
            b.brch_id branch_lms_id,
            r.id region_id,
            r.name region_name,
            u0.id cm_id,
            u0.full_name cm_name,
            u1.id ec_id,
            u1.full_name ec_name,
            u2.id ec_leader_id,
            u2.full_name ec_leader_name,
            u3.id om_id,
            u3.full_name om_name,
            (SELECT u4.id FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo') ceo_branch_id,
            (SELECT u4.full_name FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo') ceo_branch_name,
            (SELECT u5.id FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo') ceo_region_id,
            (SELECT u5.full_name FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_ceo') ceo_region_name
            FROM students s
            LEFT JOIN term_student_user t ON t.student_id = s.id
            LEFT JOIN branches b ON t.branch_id = b.id
            LEFT JOIN regions r ON t.region_id = r.id
            LEFT JOIN users u0 ON t.cm_id = u0.id
            LEFT JOIN users u1 ON t.ec_id = u1.id
            LEFT JOIN users u2 ON u1.superior_id = u2.hrm_id
            LEFT JOIN users u3 ON u0.superior_id = u3.hrm_id
            LEFT JOIN term_user_branch tu ON t.branch_id = tu.branch_id
            LEFT JOIN (
              SELECT ct.id id, ct.type `type`, ct.passed_trial trial_done
              FROM contracts ct
              LEFT JOIN students st ON ct.student_id = st.id
              WHERE ct.type = 0) c ON c.id = s.id
            WHERE s.id > 0 AND s.status >0 $where ORDER BY `name` ASC LIMIT 0, 10";
                $data = u::query($query);
                $code = APICode::SUCCESS;
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function filter($branch_id, Request $request)
    {
        $code = APICode::SUCCESS;
        $data = null;
        $response = new Response();
        if ($branch_id == 0) {
            $session = $request->users_data;
            $branches = $session->branches;
            $branch_info = $branches[0];
            $branch_id = $branch_info->branch_id;
        }
        if ($branch_id) {
            $data = (object)[];
            $students_query = "SELECT id, `name`
                            FROM students
                            WHERE branch_id IN ($branch_id)";
            $tuition_fees_query = "SELECT t.id,
                            CONCAT(p.name, ' (', t.name, ')') full_name,
                            t.name, t.session, t.price, t.receivable, t.type
                            FROM tuition_fee t
                              LEFT JOIN products p ON t.product_id = p.id
                            WHERE t.`status` > 0 AND t.branch_id IN ($branch_id)";
        }
        $students = DB::select(DB::raw($students_query));
        $tuition_fees = DB::select(DB::raw($tuition_fees_query));
        $data->students = array_merge([['id' => -1, 'name' => 'Lọc theo chương trình']], $students);
        $data->tuition_fees = array_merge([['id' => -1, 'full_name' => 'Lọc theo gói phí']], $tuition_fees);
        return $response->formatResponse($code, $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        $session = $request->users_data;
        $roles = $session->roles_detail;
        $branch_list = [];
        if ($roles) {
            foreach ($roles as $role) {
                $role_ids[] = $role->role_id;
                $branch_list[] = $role->branch_id;
            }
            $brchids = explode(',', $session->branches_ids);
            $branch_id = (int)$brchids[0];
            $branch_id = isset($request->branch_id) ? (int)$request->branch_id : (int)$branch_id;
            $role_id = $session->role_id;
            $role_param = "role_$role_id";
            $role_data = $session->roles->$role_param;
            $region_id = $role_data->region_id;
            $uid = $session->id;
            $post = $request->input();
            $sibling_id = '';
            $avatar = ada()->upload($request->avatar, 'avatars/students');
            $file_attached = ada()->upload($request->attached_file);
            if ($request->sibling_id != '') {
                $sib_id = (int)str_replace('CMS', '', $request->sibling_id);
                $sib_cd = $sib_id - 20000000;
                $sib = u::first("SELECT s.id FROM students s WHERE id = $sib_cd AND s.status >0");
                $sibling_id = $sib && isset($sib->id) ? $sib->id : 0;
            }
            $unix_check_dublicate = md5($request->name . $request->gud_name1 . $request->gud_mobile1);
            $check_existed = u::first("SELECT * FROM students WHERE (md5(CONCAT(name, gud_name1, gud_mobile1)) = '$unix_check_dublicate') OR (md5(CONCAT(name, gud_name2, gud_mobile2)) = '$unix_check_dublicate') OR (md5(CONCAT(name, gud_name1, gud_mobile2)) = '$unix_check_dublicate') OR (md5(CONCAT(name, gud_name2, gud_mobile1)) = '$unix_check_dublicate')");
            $is_existed = 0;
            if ($check_existed && isset($check_existed->id) && $check_existed->id > 0) {
                $is_existed = 1;
            }
            if ($is_existed) {
                if ($check_existed->tracking === 0) {
                    u::query("UPDATE students SET tracking = 1, branch_id = $branch_id WHERE id = $check_existed->id");
                }
            } else {
                $student = new Student();
                $used_id = "CMS" . time();
                $student->cms_id = 0;
                $student->crm_id = "CMS" . time();
                $student->name = $request->name;
                $student->gender = $request->gender;
                $student->facebook = $request->facebook;
                $student->nick = $request->nick;
                $student->note = $request->note;
                $student->type = $request->type;
                $student->phone = $request->phone;
                $student->email = $request->email;
                $student->date_of_birth = $request->date_of_birth;
                $student->gud_mobile1 = $request->gud_mobile1;
                $student->gud_name1 = $request->gud_name1;
                $student->gud_email1 = $request->gud_email1;
                $student->gud_card1 = $request->gud_card1;
                $student->gud_birth_day1 = $request->gud_birth_day1;
                $student->gud_mobile2 = $request->gud_mobile2;
                $student->gud_name2 = $request->gud_name2;
                $student->gud_email2 = $request->gud_email2;
                $student->gud_card2 = $request->gud_card2;
                $student->gud_birth_day2 = $request->gud_birth_day2;
                $student->address = $request->address;
                $student->province_id = $request->province_id;
                $student->district_id = $request->district_id;
                $student->school = $request->school;
                $student->school_grade = $request->school_grade;
                $student->created_at = date('Y-m-d H:i:s');
                $student->creator_id = $uid;
                $student->updated_at = date('Y-m-d H:i:s');
                $student->editor_id = $uid;
                $student->hash_key = $unix_check_dublicate;
                $student->current_classes = $request->current_classes;
                $student->used_student_ids = $used_id;
                $student->avatar = $avatar;
                $student->branch_id = $branch_id;
                $student->meta_data = $request->meta_data;
                $student->tracking = $request->tracking;
                $student->status = 1;
                $student->source = $request->source;
                $student->sibling_id = $sibling_id;
                $student->attached_file = $file_attached;
                if ($request->ref_code)
                    $student->ref_code = $request->ref_code['id'];
                $code = APICode::SUCCESS;
                $data = (object)['success' => $request->name];

                $student->save();
                $lastInsertedId = $student->id;
                $cms_id = '2' . str_pad((string)$lastInsertedId, 7, '0', STR_PAD_LEFT);
                $crm_id = "CMS$cms_id";
                $cms_id = (int)$cms_id;
                u::query("UPDATE students SET cms_id = '$cms_id', crm_id = '$crm_id' WHERE id = $lastInsertedId");
                if ($request->temp_id > 0){
                    u::query("UPDATE student_temp SET std_id = '$lastInsertedId' WHERE id = $request->temp_id");
                    u::query("UPDATE customer_care SET status = 1,crm_id = '$crm_id' WHERE std_temp_id = $request->temp_id");
                }
                $status = 1;
                $ceo_branch_id = 0;
                $ceo_region_id = 0;
                $region_id = 0;
                $zone_id = 0;
                $ec_leader_id = 0;
                $ec_id = (int)$request->ec_id;
                $cs_id = (int)$request->cm_id;
                $om_id = 0;
                if ($role_id == ROLE_EC) {
                    $ec_id = $uid;
                }
                if ($role_id == ROLE_CM) {
                    $cm_id = $uid;
                }

                $ecLeader = u::first("SELECT u2.id as ec_leader_id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $ec_id");
                $csLeader = u::first("SELECT u2.id as cs_leader_id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $cs_id");

                $ecLeaderId = ($ecLeader && $ecLeader->ec_leader_id >0) ? $ecLeader->ec_leader_id : $ec_id;
                $csLeaderId = ($csLeader && $csLeader->cs_leader_id >0) ? $csLeader->cs_leader_id : $cs_id;


                DB::table('term_student_user')->insert(
                    [
                        'student_id' => $lastInsertedId,
                        'ec_id' => $ec_id,
                        'cm_id' => $cs_id,
                        'status' => $status,
                        'ec_leader_id' => $ecLeaderId,
                        'om_id' => $csLeaderId,
                        'branch_id' => $branch_id,
                        'region_id' => $region_id,
                        'zone_id' => $zone_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );
            }
        }
        return $response->formatResponse($code, $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request, $student_id)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($student_id && !is_numeric($student_id)) {
            $code = APICode::SUCCESS;
        }
        if ($session = $request->users_data && $student_id && is_numeric($student_id)) {
            $query = "SELECT s1.*,
          b.name branch_name,
          b.brch_id,
          b.hrm_id branch_hrm_id,
          (SELECT id FROM contracts WHERE `status` = 6 AND student_id = s1.id LIMIT 0, 1) enrolment_id,
          (SELECT class_id FROM contracts WHERE `status` = 6 AND student_id = s1.id LIMIT 0, 1) c_class_id,
          (SELECT branch_id FROM contracts WHERE `status` = 6 AND student_id = s1.id LIMIT 0, 1) c_branch_id,
          r.name region_name,
          s2.name sibling_name,
          s2.crm_id sibling_id,
          t1.ec_id ec_id,
          t1.cm_id cm_id,
          t1.teacher_id teacher_id,
          CONCAT(u6.full_name, '- ', u6.username) as teacher_name,
          u1.hrm_id ec_hrm_id,
          CONCAT(s1.note, '<br/><br/><i><b>Ghi chú trên LMS:</b>', s1.lms_note, '</i>') student_note,
          CONCAT(u1.full_name, ' - ', u1.username) ec_name,
          CONCAT(u2.full_name, ' - ', u2.username) cm_name,
          CONCAT(u3.full_name, ' - ', u3.username) ec_leader_name,
          CONCAT(u4.full_name, ' - ', u4.username) om_name,
          CONCAT(u5.full_name, ' - ', u5.username) creator_name,
          sg.name school_grade_name,
          sg.description school_grade_description,
          (SELECT COALESCE(cl.cls_name, '') class_name FROM classes cl LEFT JOIN contracts c ON c.class_id = cl.id WHERE c.status = 6 AND c.student_id = s1.id ORDER BY c.id DESC LIMIT 1) class_name,
          COALESCE(pr.name, 'Chưa có sản phẩm') product_name,
          COALESCE(pg.name, 'Chưa có chương trình') program_name,
          pv.name province_name,
          d.name district_name,
          (SELECT COUNT(id) FROM `active_checkin` WHERE student_id = s1.id) as checkin_converted,
          (SELECT class_day FROM sessions WHERE c_branch_id = branch_id AND c_class_id = class_id LIMIT 0, 1) class_day,
          (SELECT summary_sessions FROM contracts WHERE student_id = s1.id AND type > 0 AND status !=7 LIMIT 0, 1) contract_real_sessions,
          (SELECT enrolment_start_date FROM contracts WHERE student_id = s1.id AND `status` = 6 LIMIT 0, 1) class_start_date,
          (SELECT enrolment_last_date FROM contracts WHERE student_id = s1.id  AND `status` = 6 LIMIT 0, 1) class_end_date,
          (SELECT c.product_id FROM contracts c WHERE c.student_id = s1.id LIMIT 0, 1) product_id,
          (SELECT learning_date FROM trial_reports WHERE student_id = $student_id AND session_no = 1) date1,
          (SELECT comment FROM trial_reports WHERE student_id = $student_id AND session_no = 1) comm1,
          (SELECT note FROM trial_reports WHERE student_id = $student_id AND session_no = 1) note1,
          (SELECT file FROM trial_reports WHERE student_id = $student_id AND session_no = 1) file1,
          (SELECT learning_date FROM trial_reports WHERE student_id = $student_id AND session_no = 2) date2,
          (SELECT comment FROM trial_reports WHERE student_id = $student_id AND session_no = 2) comm2,
          (SELECT note FROM trial_reports WHERE student_id = $student_id AND session_no = 2) note2,
          (SELECT file FROM trial_reports WHERE student_id = $student_id AND session_no = 2) file2,
          (SELECT learning_date FROM trial_reports WHERE student_id = $student_id AND session_no = 3) date3,
          (SELECT comment FROM trial_reports WHERE student_id = $student_id AND session_no = 3) comm3,
          (SELECT note FROM trial_reports WHERE student_id = $student_id AND session_no = 3) note3,
          (SELECT file FROM trial_reports WHERE student_id = $student_id AND session_no = 3) file3,
          (SELECT title FROM jobs WHERE id=s1.gud_job1) AS gud_job1_name,
          (SELECT title FROM jobs WHERE id=s1.gud_job2) AS gud_job2_name,
          c.enrolment_real_sessions,
          s1.source,
          (SELECT type FROM contracts WHERE student_id = s1.id AND type > 0 LIMIT 0, 1) contract_type,
          pr.prod_code,
          rl.name creator_title,
          s1.meta_data,
          c.reserved_sessions reserved_sessions,
          c.reservable_sessions reservable_sessions,
          (SELECT COUNT(id) FROM contracts WHERE student_id= $student_id AND status > 0 AND status < 7 AND `type` > 0 AND real_sessions > 0) is_official,
          IF((SELECT COUNT(id) FROM contracts WHERE student_id= $student_id AND `status` != 7 AND class_id IS NOT NULL)>0,1,0) is_active_class
        FROM students s1
          LEFT JOIN students s2 ON s1.sibling_id = s2.id
          LEFT JOIN term_student_user t1 ON t1.student_id = s1.id AND s1.branch_id = t1.branch_id
          LEFT JOIN users u1 ON t1.ec_id = u1.id
          LEFT JOIN users u2 ON t1.cm_id = u2.id
          LEFT JOIN users u3 ON t1.ec_leader_id = u3.id
          LEFT JOIN users u4 ON t1.om_id = u4.id
          LEFT JOIN users u5 ON s1.creator_id = u5.id
          LEFT JOIN users u6 ON t1.teacher_id = u6.id
          LEFT JOIN term_user_branch tb ON s1.creator_id = tb.user_id
          LEFT JOIN roles rl ON rl.id = tb.role_id
          LEFT JOIN contracts c ON c.student_id = s1.id
          LEFT JOIN districts d ON d.id = s1.district_id
          LEFT JOIN provinces pv ON pv.id = s1.province_id
          LEFT JOIN products pr ON c.product_id = pr.id
          LEFT JOIN programs pg ON c.program_id = pg.id
          LEFT JOIN school_grades sg ON sg.id = s1.school_grade
          LEFT JOIN branches b ON t1.branch_id = b.id
          LEFT JOIN regions r ON t1.region_id = r.id
        WHERE s1.id = $student_id  AND s1.status >0 GROUP BY s1.id";
            $student = u::first($query);
            if ((int)$student->enrolment_id == 0) {
                $contract_data = u::first("SELECT c.*, pr.prod_code, pr.name product_name, pg.name program_name FROM contracts c LEFT JOIN products pr ON c.product_id = pr.id LEFT JOIN programs pg ON c.program_id = pg.id WHERE c.student_id = $student->id AND c.`status` > 0 AND c.`type` > 0 ORDER BY id DESC LIMIT 0, 1");
                if ($contract_data && is_object($contract_data)) {
                    $student->class_name = 'Chưa xếp lớp';
                    $student->product_id = $contract_data->product_id;
                    $student->prod_code = $contract_data->prod_code;
                    $student->product_name = $contract_data->product_name;
                    $student->program_name = $contract_data->program_name;
                    $student->class_start_date = $contract_data->start_date;
                    $student->class_end_date = $contract_data->end_date;
                    $student->real_sessions = $contract_data->real_sessions;
                }
            }
            if ($student) {
                $branch_id = isset($student->branch_id) ? (int)$student->branch_id : 0;
                $code = APICode::SUCCESS;
                $data = (object)[];
                $charge = [];
                $update = [];
                $class_transfer = [];
                $semester_transfer = [];
                if (!$student->is_official) {
                    $get_trial_info = "SELECT id, start_date, end_date FROM contracts WHERE `type` = 0 AND status > 0 AND status < 7 AND student_id = $student_id AND branch_id = $branch_id ORDER BY id DESC LIMIT 1";
                    $trial = u::query($get_trial_info);
                    if ($trial && !empty($trial)) {
                        $student->trial_stu = 1;
                        $student->start_trial = $trial[0]->start_date;
                        $student->end_trial = $trial[0]->end_date;
                    } else {
                        $student->trial_stu = 0;
                        $student->start_trial = '';
                        $student->end_trial = '';
                    }
                } else {
                    $student->trial_stu = 0;
                    $student->start_trial = '';
                    $student->end_trial = '';
                }
                $get_ranks_query = "SELECT t.comment,
              t.created_at,
              t.rating_date,
              CONCAT(u.full_name, ' - ', u.username) rated_user,
              r.name rank_name,
              r.description rank_description
            FROM term_student_rank t
              LEFT JOIN students s ON t.student_id = s.id
              LEFT JOIN ranks r ON t.rank_id = r.id
              LEFT JOIN users u ON t.creator_id = u.id
            WHERE s.id = $student_id ORDER BY t.id DESC";
                $ranks = u::query($get_ranks_query);
                $get_contracts_query = "SELECT c.id, c.status, c.created_at, c.created_at, c.code, c.type, c.start_date, c.end_date, c.description, c.payload,
              (SELECT cl.cls_name FROM classes cl WHERE cl.id = c.class_id LIMIT 1) class_name,
              c.total_sessions, c.real_sessions,c.bonus_sessions,c.summary_sessions, c.expected_class, c.bill_info, c.receivable, c.must_charge,
              CONCAT(u.full_name, ' - ', u.username) creator, pr.name product_name, pg.name program_name,
              t.name tuition_fee_name, t.price tuition_fee_price, b.name branch_name, c.total_discount, c.reserved_sessions, c.reservable_sessions
            FROM contracts c
              LEFT JOIN students s ON c.student_id = s.id
              LEFT JOIN users u ON c.creator_id = u.id
              LEFT JOIN products pr ON c.product_id = pr.id
              LEFT JOIN programs pg ON c.program_id = pg.id
              LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
              LEFT JOIN branches b ON c.branch_id = b.id
            WHERE s.id = $student_id ORDER BY c.id DESC";
                $contracts = u::query($get_contracts_query);
                $get_enrolments_query = "SELECT c.id enrolment_id,
              c.enrolment_updated_at created_at,
              c.enrolment_start_date start_date,
              c.enrolment_end_date end_date,
              c.enrolment_last_date last_date,
              COALESCE(c.enrolment_last_date, c.enrolment_expired_date) latest_date,
              c.status enrolment_status,
              c.enrolment_last_date,
              c.id contract_id,
              c.description,
              c.type contract_type,
              c.payload,
              c.total_sessions,
              c.real_sessions,
              c.bonus_sessions,
              c.summary_sessions,
              c.expected_class,
              c.bill_info,
              c.receivable,
              c.must_charge,
              cl.cls_name class_name,
              cl.id class_id,
              sm.name semester,
              CONCAT(u.full_name, ' - ', u.username) creator,
              pr.name product_name,
              pg.name program_name,
              t.name tuition_fee_name,
              t.price tuition_fee_price,
              b.name branch_name,
              c.total_discount,
              (SELECT s.class_day FROM sessions s WHERE s.class_id = cl.id LIMIT 1) class_day,
               c.product_id,c.branch_id,c.class_id,c.enrolment_start_date
            FROM contracts c
              LEFT JOIN students s ON c.student_id = s.id
              LEFT JOIN classes cl ON c.class_id = cl.id
              LEFT JOIN semesters sm ON cl.semester_id = sm.id
              LEFT JOIN users u ON c.creator_id = u.id
              LEFT JOIN products pr ON c.product_id = pr.id
              LEFT JOIN programs pg ON c.program_id = pg.id
              LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
              LEFT JOIN branches b ON c.branch_id = b.id
            WHERE s.id = $student_id AND c.enrolment_start_date IS NOT NULL ORDER BY c.id DESC";
                $enrolments = u::query($get_enrolments_query);
                $get_tuition_transfer_query = "SELECT t.transferred_amount, t.received_amount, t.transfer_date, t.status, 
                        (SELECT `accounting_id` FROM students WHERE id = t.to_student_id) to_student_act,
                        (SELECT `accounting_id` FROM students WHERE id = t.from_student_id) from_student_act,
                        (SELECT `stu_id` FROM students WHERE id = t.to_student_id) to_student_lms,
                        (SELECT `stu_id` FROM students WHERE id = t.from_student_id) from_student_lms,
                        (SELECT `crm_id` FROM students WHERE id = t.to_student_id) to_student_crm,
                        (SELECT `crm_id` FROM students WHERE id = t.from_student_id) from_student_crm,
                        (SELECT `name` FROM students WHERE id = t.to_student_id) to_student_name,
                        (SELECT `name` FROM students WHERE id = t.from_student_id) from_student_name,
                        (SELECT CONCAT(`username`) FROM users WHERE id = t.creator_id) creator_name
                    FROM
                        `tuition_transfer_v2` t
                    WHERE (t.`to_student_id` = $student_id OR t.from_student_id = $student_id) AND t.status >0
                    ORDER BY t.`created_at` DESC";
                $tuition_transfer = u::query($get_tuition_transfer_query);
                $get_class_transfer_query = "SELECT
              clt.id id,
              b.`name` from_branch,
              s.stu_id lms_id,
              s.name student_name,
              s.nick nick,
              p.`name` from_product,
              pr.`name` from_program,
              c.`cls_name` class_name,
              c2.`cls_name` to_class_name,
              br.`name` to_branch,
              se.name semester,
              p2.`name` to_product,
              pr2.`name` to_program,
              clt.transfer_date transfer_date,
              clt.note note,
              clt.meta_data meta_data,
              clt.amount_exchange amount_exchange,
              clt.session_exchange session_exchange,
              clt.amount_transferred amount_transferred,
              clt.session_transferred session_transferred,
              clt.transfer_date transfer_date,
              clt.`from_approved_at` from_approved_at,
              clt.`to_approved_at` to_approved_at,
              clt.`status` `status`,
              clt.`created_at` created_at,
              clt.from_approve_comment from_approve_comment,
              clt.to_approve_comment to_approve_comment,
              clt.attached_file attached_file,
              u.username creator,
              u2.username from_approver,
              u3.username to_approver
            FROM
              class_transfer clt
              LEFT JOIN branches b ON clt.`from_branch_id` = b.`id`
              LEFT JOIN branches br ON clt.to_branch_id = br.`id`
              LEFT JOIN classes c ON clt.`from_class_id` = c.`id`
              LEFT JOIN classes c2 ON clt.`to_class_id` = c2.`id`
              LEFT JOIN products p ON clt.`from_product_id` = p.`id`
              LEFT JOIN products p2 ON clt.`to_product_id` = p2.`id`
              LEFT JOIN programs pr ON clt.`from_program_id` = pr.`id`
              LEFT JOIN programs pr2 ON clt.`to_program_id` = pr2.`id`
              LEFT JOIN users u ON clt.creator_id = u.id
              LEFT JOIN users u2 ON clt.from_approver_id = u2.id
              LEFT JOIN users u3 ON clt.to_approver_id = u3.id
              LEFT JOIN students s ON clt.student_id = s.id
              LEFT JOIN semesters se ON clt.semester_id = se.id
            WHERE
              clt.`status` < 7
              AND clt.type = 0
              AND clt.student_id = $student_id
            ORDER BY clt.created_at DESC";
                $class_transfer = u::query($get_class_transfer_query);
                $get_payments_query = "SELECT p.*,
              c.total_discount,
              c.code contract_code,
              c.id contract_id,
              (select accounting_id from contracts where id = c.id limit 1) as contract_accounting_id,
              IF (c.count_recharge > 0, true, false) is_recharged,
              CONCAT(u.full_name, ' - ', u.username) created_user
            FROM payment p
              LEFT JOIN contracts c ON p.contract_id = c.id
              LEFT JOIN users u ON p.creator_id = u.id
              LEFT JOIN students s ON c.student_id = s.id
            WHERE s.id = $student_id ORDER BY p.id DESC";
                $payments = u::query($get_payments_query);
                $get_recerves_query = "SELECT
              r.id id,
              r.student_id student_id,
              r.type `type`,
              r.start_date start_date,
              r.end_date end_date,
              r.session `session`,
              r.status `status`,
              r.created_at created_at,
              r.approved_at approved_at,
              r.comment `comment`,
              r.meta_data meta_data,
              r.is_reserved is_reserved,
              r.attached_file attached_file,
              r.note note,
              r.new_enrol_end_date new_end_date,
              s.`name` student_name,
              s.nick nick,
              s.`stu_id` lms_id,
              b.`name` branch_name,
              p.`name` product_name,
              pr.`name` program_name,
              cls.`cls_name` class_name,
              u.username creator,
              u2.username approver
            FROM
              reserves r
              LEFT JOIN students s ON r.student_id = s.`id`
              LEFT JOIN contracts c ON r.`contract_id` = c.`id`
              LEFT JOIN branches b ON r.`branch_id` = b.`id`
              LEFT JOIN products p ON r.`product_id` = p.`id`
              LEFT JOIN programs pr ON r.`program_id` = pr.`id`
              LEFT JOIN classes cls ON r.`class_id` = cls.`id`
              LEFT JOIN users u ON r.creator_id = u.id
              LEFT JOIN users u2 on r.approver_id = u2.id
            WHERE
              r.status < 3 AND r.student_id = $student_id
              AND r.branch_id = $branch_id
            GROUP BY r.id
            ORDER BY r.created_at DESC";
                $recerves = u::query($get_recerves_query);
                $get_pendings_query = "SELECT
              r.id id,
              r.student_id student_id,
              r.start_date start_date,
              r.end_date end_date,
              r.session `session`,
              r.status `status`,
              r.created_at created_at,
              r.approved_at approved_at,
              r.comment `comment`,
              r.meta_data meta_data,
              r.attached_file attached_file,
              r1.description description,
              s.`name` student_name,
              s.`stu_id` lms_id,
              b.`name` branch_name,
              u.username creator,
              u2.username approver,
              b.`name` branch_name,
              p.`name` product_name,
              pr.`name` program_name
            FROM
              pendings r
              LEFT JOIN students s ON r.student_id = s.`id`
              LEFT JOIN contracts c ON r.`contract_id` = c.`id`
              LEFT JOIN branches b ON r.`branch_id` = b.`id`
              LEFT JOIN reasons r1 ON r.reason_id = r1.id
              LEFT JOIN users u ON r.creator_id = u.id
              LEFT JOIN users u2 ON r.approver_id = u2.id
              LEFT JOIN products p ON r.`product_id` = p.`id`
              LEFT JOIN programs pr ON r.`program_id` = pr.`id`
            WHERE
              r.student_id = $student_id
            ORDER BY r.created_at DESC";
                $pendings = u::query($get_pendings_query);
                $get_withdrawal_fees_query = "SELECT
                w.*,
                s.cms_id,
                s.crm_id,
                s.name AS student_name,
                b.name AS branch_name,
                p.name AS product_name,
                pro.name AS program_name,
                cls.cls_name AS class_name,
                u1.full_name AS approver_name,
                u2.full_name AS refuner_name,
                u3.full_name AS creator_name
            FROM
                withdrawal_fees AS w
                LEFT JOIN students AS s ON w.student_id = s.id
                LEFT JOIN contracts AS c ON w.contract_id = c.id
                LEFT JOIN branches AS b ON w.branch_id = b.id
                LEFT JOIN products AS p ON c.product_id = p.id
                LEFT JOIN programs AS pro ON c.program_id = pro.id
                LEFT JOIN classes AS cls ON c.class_id = cls.id
                LEFT JOIN users AS u1 ON u1.id = w.approver_id
                LEFT JOIN users AS u2 ON u2.id = w.refuner_id
                LEFT JOIN users AS u3 ON u3.id = w.creator_id
            WHERE 
                w.student_id = $student_id
            ORDER BY w.id";
                $withdrawal_fees = u::query($get_withdrawal_fees_query);

                $get_branch_transfer_query = "SELECT
                  clt.id AS id,
                  b.`name` AS from_branch,
                  s.name AS student_name,
                  s.nick AS nick,
                  p.`name` AS from_product,
                  pr.`name` AS from_program,
                  c.`cls_name` AS class_name,
                  c2.`cls_name` AS to_class_name,
                  br.`name` AS to_branch,
                  se.name AS semester,
                  p2.`name` AS to_product,
                  pr2.`name` AS to_program,
                  clt.transfer_date AS transfer_date,
                  clt.note as note,
                  clt.meta_data AS meta_data,
                  clt.transferred_amount as amount_exchange,
                  clt.transferred_sessions as session_exchange,
                  clt.transferred_amount as amount_transferred,
                  clt.transferred_sessions as session_transferred,
                  clt.transfer_date as transfer_date,
                  clt.`from_approved_at` AS from_approved_at,
                  clt.`to_approved_at` AS to_approved_at,
                  clt.`status` AS `status`,
                  clt.`created_at` AS created_at,
                  clt.from_approve_comment as from_approve_comment,
                  clt.to_approve_comment as to_approve_comment,
                  clt.attached_file AS attached_file,
                  u.username as creator,
                  u2.username as from_approver,
                  u3.username as to_approver              
                FROM
                    branch_transfer AS clt
                  LEFT JOIN branches AS b ON clt.`from_branch_id` = b.`id`
                  LEFT JOIN branches AS br ON clt.to_branch_id = br.`id`
                  LEFT JOIN classes AS c ON clt.`from_class_id` = c.`id`
                  LEFT JOIN classes AS c2 ON clt.`to_class_id` = c2.`id`
                  LEFT JOIN products AS p ON clt.`from_product_id` = p.`id`
                  LEFT JOIN products AS p2 ON clt.`to_product_id` = p2.`id`
                  LEFT JOIN programs AS pr ON clt.`from_program_id` = pr.`id`
                  LEFT JOIN programs AS pr2 ON clt.`to_program_id` = pr2.`id`
                  LEFT JOIN users AS u ON clt.creator_id = u.id
                  LEFT JOIN users AS u2 ON clt.from_approver_id = u2.id
                  LEFT JOIN users AS u3 ON clt.to_approver_id = u3.id
                  LEFT JOIN students AS s ON clt.student_id = s.id
                  LEFT JOIN semesters AS se ON clt.semester_id = se.id
                WHERE
                  clt.`status` < 7
                  AND clt.type = 1
                  AND clt.student_id = $student_id
                ORDER BY clt.created_at DESC";
                $branch_transfer = u::query($get_branch_transfer_query);

                $update = u::query("SELECT l.*,
              CONCAT(u1.full_name, ' - ', u1.username) updator_name,
              CONCAT(u2.full_name, ' - ', u2.username) cm_name,
              r.name updator_title
            FROM log_student_update l
              LEFT JOIN users u1 ON l.updated_by = u1.id
              LEFT JOIN users u2 ON l.cm_id = u2.id
              LEFT JOIN term_user_branch t ON l.updated_by = t.user_id
              LEFT JOIN roles r ON t.role_id = r.id
            WHERE student_id = $student_id GROUP BY l.id ORDER BY id DESC");
                $student_ranks = u::query("SELECT * FROM ranks WHERE status > 0");
                if ($student->avatar) {
                    $link_path_avatar = $student->avatar;
                    $real_path_avatar = DIRECTORY . str_replace('/', DS, $link_path_avatar);
                    $student->avatar = file_exists($real_path_avatar) ? $link_path_avatar : AVATAR_LINK . 'noavatar.png';
                } else $student->avatar = AVATAR_LINK . 'noavatar.png';
                $province_id = (int)$student->province_id;
                $district_query = "SELECT districts.*, provinces.id province_id from districts
            left join provinces on districts.province_id = provinces.id
            where provinces.id = $province_id";
                $districts = u::query($district_query);
                $districts = $districts ? $districts : [];
                $student->district = $districts;
                $student->trial_reports = (object)[];
                $student->trial_reports = (Object)[
                    'date1' => $student->date1,
                    'comm1' => $student->comm1,
                    'note1' => $student->note1,
                    'file1' => $student->file1,
                    'date2' => $student->date2,
                    'comm2' => $student->comm2,
                    'note2' => $student->note2,
                    'file2' => $student->file2,
                    'date3' => $student->date3,
                    'comm3' => $student->comm3,
                    'note3' => $student->note3,
                    'file3' => $student->file3
                ];
                $attendance = array();
                if ($enrolments) {
                    $today = date('Y-m-d');
                    $attendanceModel = new Attendances();
                    $allHoliday = u::getPublicHolidayAll($student->branch_id);
                    foreach ($enrolments as $k => $enrolment) {
                        // $endDate = $enrolment->last_date;
                        // $startDate = $enrolment->start_date;
                        // if ($endDate >= $today)
                        //     $inDate = $today;
                        // else if ($endDate < $today){
                        //     $inDate = $endDate;
                        // }

                        // $days =  u::getDaysBetweenTwoDate($startDate, $inDate, $enrolment->class_day); # 2x Friday
                        // $totalSessions = 0;
                        // $inDateList = ['start' =>$startDate, 'end' =>$endDate];
                        // ///$sessionReserves = u::getReserveSession($inDateList,$student_id);
                        // $reserveDate = u::getReserveSessionDate($inDateList,$student_id);
                        // $dayOff = 0;
                        // if ($reserveDate){
                        //     if ($reserveDate[1] >= $inDate){
                        //         $dayOff = sizeof(u::getDaysBetweenTwoDate($reserveDate[0], $inDate, $enrolment->class_day));
                        //     }
                        //     else{
                        //         $dayOff =  u::getReserveSession($inDateList,$student_id);
                        //     }
                        // }

                        // foreach($days as $day){
                        //     if (!in_array($day,$allHoliday)){
                        //         $totalSessions += 1;
                        //     }
                        // }

                        // $totalSessions = ($totalSessions - $dayOff);
                        $tmp_contract = (object)array(
                            'branch_id'=>$enrolment->branch_id,
                            'product_id'=>$enrolment->product_id,
                            'class_id'=>$enrolment->class_id,
                            'contract_id'=>$enrolment->contract_id,
                            'enrolment_start_date'=>$enrolment->enrolment_start_date
                          );
                        $tmp_report = new ReportsController();
                        $enrolments[$k]->enrolment_real_sessions = $tmp_report->getDoneSessions($tmp_contract);
                        $attendance[$enrolment->class_id] = $attendanceModel->getClassAttendanceDetailByStudent($enrolment->class_id, $student->id);
                    }
                }
                $student->attendance = $attendance;
                $sources = u::query("SELECT id, `name` FROM `sources` WHERE STATUS = 1");
                $student->sources = is_array($sources) ? $sources : [];
                $attendances= u::query("SELECT a.attendance_date,(SELECT cls_name FROM classes WHERE id=a.class_id) AS cls_name,
                        a.note,a.status,(SELECT CONCAT(full_name,hrm_id) FROM users WHERE id=a.creator_id) AS creator_name     
                    FROM attendances AS a WHERE a.student_id=$student->id ORDER BY a.attendance_date DESC");
                $data = (object)[
                    'student' => $student,
                    'tabs' => [
                        'ranks' => is_array($ranks) ? $ranks : [],
                        'update' => is_array($update) ? $update : [],
                        'payment' => is_array($payments) ? $payments : [],
                        'recerve' => is_array($recerves) ? $recerves : [],
                        'pending' => is_array($pendings) ? $pendings : [],
                        'contract' => is_array($contracts) ? $contracts : [],
                        'enrolment' => is_array($enrolments) ? $enrolments : [],
                        'student_ranks' => is_array($student_ranks) ? $student_ranks : [],
                        'class_transfer' => is_array($class_transfer) ? $class_transfer : [],
                        'branch_transfer' => is_array($branch_transfer) ? $branch_transfer : [],
                        'tuition_transfer' => is_array($tuition_transfer) ? $tuition_transfer : [],
                        'semester_transfer' => is_array($semester_transfer) ? $semester_transfer : [],
                        'withdrawal_fees' => is_array($withdrawal_fees) ? $withdrawal_fees : [],
                        'sources' => is_array($sources) ? $sources : [],
                        'attendances' => is_array($attendances) ? $attendances : [],
                    ]
                ];
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function studentByBranch($id)
    {
        $p = Student::where('branch_id', $id);
        return response()->json($p->get());
    }

    public function studentDetail($id)
    {
        $student = DB::table('students')
            ->join('provinces', 'provinces.id', 'students.province_id')
            ->join('districts', 'districts.id', 'students.district_id')
            ->join('branches', 'branches.id', 'students.branch_id')
            ->select('students.*',
                'provinces.name AS province_name',
                'districts.name AS district_name',
                'branches.name AS branch_name')
            ->where('students.id', $id)
            ->get();
        return response()->json($student[0]);
    }

    public function sibling($id, Request $request)
    {
        $student_id = $request->student_id;
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();
        if ($id) {
            $data = u::query("SELECT CONCAT(`name`, ' - Mã CMS: ', cms_id) name, id , gud_mobile1 FROM students WHERE (id = '$id' OR cms_id = '$id' OR crm_id = 'CMS$id' OR crm_id = '$id')");// AND `status` > 0
            $data = is_array($data) && count($data) ? $data[0] : $data;
            $data = $data ? ($data->id==$student_id ? ['id' => '', 'name' => 'Mã CMS anh/chị em học cùng không hợp lệ.']:$data) : ['id' => '', 'name' => 'Không tìm thấy anh chị em học cùng với mã CMS vừa nhập.'];
        }
        return $response->formatResponse($code, $data);
    }

    public function checkStudentExist(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($request) {
            $code = APICode::SUCCESS;
            $condition = $request;
            if (isset($condition->type) && $condition->type == "checkin"){
                if (isset($condition->sibling) && $condition->sibling == true){
                    $data = (object)['existed' =>0,'msg' =>''];
                    return $response->formatResponse($code, $data);
                }
                $and = "";
                $mobile = trim($condition->mobile);
                $mobile2 = trim($condition->mobile2);
                $name = trim($condition->name);
                $branch_id = trim($condition->branch_id);
                if (isset($condition->id))
                    $and = " AND s.id != {$condition->id}";
                $dataStd = u::first("SELECT COUNT(s.id) existed FROM students s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.checked!=2 $and");
                $rawData = [];
                if ($branch_id > 0){
                    $tmp_cond ="";
                    if (isset($condition->hub) && $condition->hub == true){
                        $branch_id = 100;
                        $tmp_cond =" AND branch_id!=12";
                    }
                    $rawData = u::first("SELECT COUNT(s.id) existed FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.type = 0 AND s.branch_id != $branch_id $tmp_cond AND (s.`created_at` >= NOW()-INTERVAL 15 DAY OR s.import_to_call>0 OR (s.branch_id=12 AND s.source ='GĐTT'))");
                }
                $msg = "";
                if ($dataStd->existed >= 1){
                    $dataStd->existed = 1;
                    $detail = u::first("SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name, (SELECT so.name FROM sources so WHERE so.id = s.source) AS source_name FROM students s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.checked!=2 $and");
                    $msg = "Số điện thoại phụ huynh đã có ở: {$detail->branch_name} nguồn {$detail->source_name}, hãy nhập số điện thoại khác.";
                }
                if (isset($rawData->existed) && $rawData->existed == 1){
                    $detail = u::first("SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name, s.source FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.type = 0 AND s.branch_id != $branch_id AND (s.`created_at` >= NOW()-INTERVAL 15 DAY OR s.import_to_call>0 OR (s.branch_id=12 AND s.source ='GĐTT'))");
                    $msg = "Số điện thoại phụ huynh thuộc data khách hàng đang chăm sóc của: {$detail->branch_name} nguồn {$detail->source}, vui lòng nhập số điện thoại khác.";
                }
                // $connection = DB::connection('mysql_lead');
                // $lead_info = $connection->select(DB::raw("SELECT p.id,u.name,u.hrm_id,u.branch_name FROM cms_parents AS p LEFT JOIN users AS u ON u.id=p.owner_id WHERE p.mobile_1='{$mobile}' OR  p.mobile_2='{$mobile}'"));
                // if(isset($lead_info[0])){
                //     $lead_info = $lead_info[0];
                //     $dataStd->existed = 1;
                //     $msg = "Số điện thoại phụ huynh thuộc data khách hàng đang chăm sóc của: {$lead_info->branch_name} - $lead_info->name - $lead_info->hrm_id, vui lòng nhập số điện thoại khác.";
                // }
                if ($mobile2){
                    if (isset($condition->id))
                    $and = " AND s.id != {$condition->id}";
                    $dataStd = u::first("SELECT COUNT(s.id) existed FROM students s WHERE (s.`gud_mobile1` = '{$mobile2}' OR s.`gud_mobile2` = '{$mobile2}') AND s.checked!=2 $and");
                    $rawData = [];
                    if ($branch_id > 0){
                        $tmp_cond ="";
                        if (isset($condition->hub) && $condition->hub == true){
                            $branch_id = 100;
                            $tmp_cond =" AND branch_id!=12";
                        }
                        $rawData = u::first("SELECT COUNT(s.id) existed FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$mobile2}' OR s.`gud_mobile2` = '{$mobile2}') AND s.type = 0 AND s.branch_id != $branch_id $tmp_cond AND (s.`created_at` >= NOW()-INTERVAL 15 DAY OR s.import_to_call>0 OR (s.branch_id=12 AND s.source ='GĐTT'))");
                    }
                    $msg = "";
                    if ($dataStd->existed >= 1){
                        $dataStd->existed = 1;
                        $detail = u::first("SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name, 
                                (SELECT so.name FROM sources so WHERE so.id = s.source) AS source_name
                            FROM students s WHERE (s.`gud_mobile1` = '{$mobile2}' OR s.`gud_mobile2` = '{$mobile2}') AND s.checked!=2 $and");
                        $msg = "Số điện thoại phụ huynh 2 đã có ở: {$detail->branch_name} nguồn {$detail->source_name}, hãy nhập số điện thoại khác.";
                    }
                    if (isset($rawData->existed) && $rawData->existed == 1){
                        $detail = u::first("SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name, s.source FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$mobile2}' OR s.`gud_mobile2` = '{$mobile2}') AND s.type = 0 AND s.branch_id != $branch_id AND (s.`created_at` >= NOW()-INTERVAL 15 DAY OR s.import_to_call>0 OR (s.branch_id=12 AND s.source ='GĐTT'))");
                        $msg = "Số điện thoại phụ huynh 2 thuộc data khách hàng đang chăm sóc của: {$detail->branch_name} nguồn {$detail->source}, vui lòng nhập số điện thoại khác.";
                    }
                    // $connection = DB::connection('mysql_lead');
                    // $lead_info = $connection->select(DB::raw("SELECT p.id,u.name,u.hrm_id,u.branch_name FROM cms_parents AS p LEFT JOIN users AS u ON u.id=p.owner_id WHERE p.mobile_1='{$mobile2}' OR  p.mobile_2='{$mobile2}'"));
                    // if(isset($lead_info[0])){
                    //     $lead_info = $lead_info[0];
                    //     $dataStd->existed = 1;
                    //     $msg = "Số điện thoại phụ huynh thuộc data khách hàng đang chăm sóc của: {$lead_info->branch_name} - $lead_info->name - $lead_info->hrm_id, vui lòng nhập số điện thoại khác.";
                    // }
                }
                $data = !$msg? (object)['existed' =>0,'msg' =>$msg] : (object)['existed' =>1,'msg' =>$msg];
            }
            else{
                $sibling = $condition->sibling;
                $mobile = trim($condition->mobile);
                $mobile2 = isset($condition->mobile2) ? trim($condition->mobile2) : null;
                $branch_id = isset($condition->branch_id) ? trim($condition->branch_id): 0;
                $msg = "";
                if (!$sibling){
                    $dataStd = u::first("SELECT COUNT(s.id) existed FROM students s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.checked!=2");
                    $dataSQL = "SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name,(SELECT so.name FROM sources so WHERE so.id = s.source) AS source_name FROM students s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.checked!=2";
                    $rawData = [];
                    if ($branch_id > 0){
                        $rawData = u::first("SELECT COUNT(s.id) existed FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.checked!=2 AND s.type = 0 AND s.branch_id != $branch_id");
                        $rawSQL = "SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name, s.source FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.checked!=2 AND s.type = 0 AND s.branch_id != $branch_id";
                    }
                    if ($dataStd->existed >= 1){
                        $dataStd->existed = 1;
                        $detail = u::first($dataSQL);
                        $msg = "Số điện thoại phụ huynh 1 đã có ở: {$detail->branch_name} nguồn {$detail->source_name}, hãy nhập số điện thoại khác.";
                    }
                    if (isset($rawData->existed) && $rawData->existed == 1){
                        $detail = u::first($rawSQL);
                        $msg = "Số điện thoại phụ huynh 1 thuộc data khách hàng đang chăm sóc của: {$detail->branch_name} nguồn {$detail->source}, vui lòng nhập số điện thoại khác.";
                    }
                    // $connection = DB::connection('mysql_lead');
                    // $lead_info = $connection->select(DB::raw("SELECT p.id,u.name,u.hrm_id,u.branch_name FROM cms_parents AS p LEFT JOIN users AS u ON u.id=p.owner_id WHERE p.mobile_1='{$mobile}' OR  p.mobile_2='{$mobile}'"));
                    // if(isset($lead_info[0])){
                    //     $lead_info = $lead_info[0];
                    //     $dataStd->existed = 1;
                    //     $msg = "Số điện thoại phụ huynh thuộc data khách hàng đang chăm sóc của: {$lead_info->branch_name} - $lead_info->name - $lead_info->hrm_id, vui lòng nhập số điện thoại khác.";
                    // }
                }

                if ($mobile2){
                    $cond = $sibling ? " AND s.id!= $sibling":"";
                    $dataStd = u::first("SELECT COUNT(s.id) existed FROM students s WHERE (s.`gud_mobile1` = '{$mobile2}' OR s.`gud_mobile2` = '{$mobile2}') $cond");
                    $dataSQL = "SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name,(SELECT so.name FROM sources so WHERE so.id = s.source) AS source_name FROM students s WHERE (s.`gud_mobile1` = '{$mobile2}' OR s.`gud_mobile2` = '{$mobile2}')";
                    $rawData = [];
                    if ($branch_id > 0){
                        $rawData = u::first("SELECT COUNT(s.id) existed FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$mobile2}' OR s.`gud_mobile2` = '{$mobile2}') AND s.type = 0 AND s.branch_id != $branch_id");
                        $rawSQL = "SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name, s.source FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$mobile2}' OR s.`gud_mobile2` = '{$mobile2}') AND s.type = 0 AND s.branch_id != $branch_id";
                        $detail = u::first($rawSQL);
                    }
                    if ($dataStd->existed >= 1){
                        $dataStd->existed = 1;
                        $detail = u::first($dataSQL);
                        $msg = "Số điện thoại phụ huynh 2 đã có ở: {$detail->branch_name} nguồn {$detail->source_name}, hãy nhập số điện thoại khác.";
                    }
                    if (isset($rawData->existed) && $rawData->existed == 1){
                        $msg = "Số điện thoại phụ huynh 2 thuộc data khách hàng đang chăm sóc của của: {$detail->branch_name} nguồn {$detail->source}, vui lòng nhập số điện thoại khác.";
                    }
                    // $connection = DB::connection('mysql_lead');
                    // $lead_info = $connection->select(DB::raw("SELECT p.id,u.name,u.hrm_id,u.branch_name FROM cms_parents AS p LEFT JOIN users AS u ON u.id=p.owner_id WHERE p.mobile_1='{$mobile2}' OR  p.mobile_2='{$mobile2}'"));
                    // if(isset($lead_info[0])){
                    //     $lead_info = $lead_info[0];
                    //     $dataStd->existed = 1;
                    //     $msg = "Số điện thoại phụ huynh thuộc data khách hàng đang chăm sóc của: {$lead_info->branch_name} - $lead_info->name - $lead_info->hrm_id, vui lòng nhập số điện thoại khác.";
                    // }
                }
                $data = !$msg? (object)['existed' =>0,'msg' =>$msg] : (object)['existed' =>1,'msg' =>$msg];
                /*
                 * $text = trim($condition->name) . trim($condition->parent) . trim($condition->mobile);
                $hash = md5($text);
                $data = u::first("SELECT COUNT(id) existed FROM students
                    WHERE md5(CONCAT(name, gud_name1, gud_mobile1)) = '$hash'
                      OR md5(CONCAT(name, gud_name1, gud_mobile2)) = '$hash'
                      OR md5(CONCAT(name, gud_name2, gud_mobile1)) = '$hash'
                      OR md5(CONCAT(name, gud_name2, gud_mobile2)) = '$hash'");
                */
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function dayCount($from, $to, $day = 5) {
        $days = [];
        $endDate = strtotime($to);
        for($i = strtotime(u::getDayName($day), strtotime($from)); $i <= $endDate; $i = strtotime('+1 week', $i)){
            $days[] = date('Y-m-d', $i);
        }
        return $days;
    }

    public function ckPublicHoliday($day) {
        $sql = "SELECT count(id) as total FROM `public_holiday` WHERE start_date <= '{$day}' AND end_date >='{$day}'";
        $total = u::query($sql)[0]->total;
        if ($total == 0)
            return 1;
        else
            return 0;
    }

    public function checkStudentExistEdit($hash, $id)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($hash) {
            $code = APICode::SUCCESS;
            $data = u::first("SELECT COUNT(id) existed FROM students WHERE md5(CONCAT(name, gud_name1, gud_mobile1)) = '$hash' AND id != '$id' AND tracking > 0");
        }
        return $response->formatResponse($code, $data);
    }

    public function getUsersByBranch($id)
    {
        $response = [
            'ecs' => [],
            'cms' => []
        ];
        $ecs_ids = u::query("SELECT CONCAT(u.full_name, ' - ', u.username) ec_name, u.id ec_id FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WhERE u.status > 0 AND (t.role_id = 68 OR t.role_id = 69 OR t.role_id = 676767) AND t.branch_id = $id");
        $cms_ids = u::query("SELECT CONCAT(u.full_name, ' - ', u.username) cm_name, u.id cm_id FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WhERE u.status > 0 AND (t.role_id = 55 OR t.role_id = 56) AND t.branch_id = $id");
        $ecs_ids = array_merge([['ec_name' => 'Vui lòng chọn EC', 'ec_id' => '']], $ecs_ids);
        $cms_ids = array_merge([['cm_name' => 'Vui lòng chọn CS', 'cm_id' => '']], $cms_ids);
        $response['ecs'] = $ecs_ids;
        $response['cms'] = $cms_ids;
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $response
        ]);
    }

    public function getECCMAndBranches(Request $request)
    {
        $response = [
            'title_id' => 0,
            'branches' => [],
            'school_grades' => [],
            'ecs' => [],
            'ec_id' => 0,
            'branch_id' => 0
        ];
        $session = $request->users_data;
        if ($session) {
            $uid = $session->id;
            $roles = $session->roles_detail;
            $role_ids = [];
            $branch_list = [];
            if ($roles) {
                foreach ($roles as $role) {
                    $role_ids[] = $role->role_id;
                    $branch_list[] = $role->branch_id;
                }
                $response['branch_id'] = $branch_list[0];
                rsort($role_ids);
                $role_id = $role_ids[0];
                $role_param = "role_$role_id";
                $role_data = $session->roles->$role_param;
                $region_id = $role_data->region_id;
                $title_id = 1;
                $my_branches = count($branch_list) > 1 ? implode(',', $branch_list) : $branch_list[0];
                $branches_ids = u::query("SELECT `name`, id branch_id FROM branches WhERE `status` > 0 AND id IN($my_branches)");
                $response['branches'] = $branches_ids;
                    
                if ($role_id >= ROLE_REGION_CEO) {
                    $branches_ids = u::query("SELECT `name`, id branch_id FROM branches WhERE `status` > 0 AND region_id = $region_id");
                    $response['branches'] = $branches_ids;
                } elseif ($role_id == ROLE_BRANCH_CEO || $role_id == 57) {
                    $ecs_ids = u::query("SELECT CONCAT(u.full_name, ' - ', u.username, ' : ', u.hrm_id) ec_name, u.id ec_id FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WhERE u.status > 0 AND (t.role_id = " . ROLE_EC . " OR t.role_id = " . ROLE_EC_LEADER . ") AND t.branch_id IN ($my_branches)");
                    $ecs_ids = array_merge([['ec_name' => 'Chọn EC', 'ec_id' => '']], $ecs_ids);
                    $ecs_ids = apax_remove_dublicates($ecs_ids);
                    $response['ecs'] = $ecs_ids;
                    $title_id = 4;
                } elseif ($role_id == ROLE_EC_LEADER) {
                    $ecs_ids = u::query("SELECT CONCAT(u1.full_name, ' - ', u1.username, ' : ', u1.hrm_id) ec_name, u1.id ec_id FROM users u1 LEFT JOIN users u2 ON u1.superior_id = u2.hrm_id LEFT JOIN term_user_branch t ON t.user_id = u1.id WhERE u1.status > 0 AND (t.role_id = " . ROLE_EC . " OR t.role_id = " . ROLE_EC_LEADER . ") AND t.branch_id IN ($my_branches) AND u2.id = $uid OR u1.id = $uid");
                    $ecs_ids = array_merge([['ec_name' => 'Chọn EC', 'ec_id' => '']], $ecs_ids);
                    $ecs_ids = apax_remove_dublicates($ecs_ids);
                    $response['ecs'] = $ecs_ids;
                    $response['ec_id'] = $uid;
                    $title_id = 4;
                } elseif ($role_id == ROLE_OM || $role_id == 58) {
                    $ecs_ids = u::query("SELECT CONCAT(u.full_name, ' - ', u.username, ' : ', u.hrm_id) ec_name, u.id ec_id FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WhERE u.status > 0 AND (t.role_id = " . ROLE_EC . " OR t.role_id = " . ROLE_EC_LEADER . ") AND t.branch_id IN ($my_branches)");
                    $ecs_ids = array_merge([['ec_name' => 'Chọn EC', 'ec_id' => '']], $ecs_ids);
                    $ecs_ids = apax_remove_dublicates($ecs_ids);
                    $response['ecs'] = $ecs_ids;
                    $response['om_id'] = $uid;
                    $title_id = 4;
                } elseif ($role_id == ROLE_EC) {
                    $ecs_ids = u::query("SELECT CONCAT(u.full_name, ' - ', u.username, ' : ', u.hrm_id) ec_name, u.id ec_id FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WhERE u.status > 0 AND (t.role_id = " . ROLE_EC . " OR t.role_id = " . ROLE_EC_LEADER . ") AND t.user_id = $uid");
                    $ecs_ids = apax_remove_dublicates($ecs_ids);
                    $response['ecs'] = $ecs_ids;
                    $response['ec_id'] = $uid;
                    $title_id = 2;
                    $ec_id = $uid;
                } elseif ($role_id == ROLE_CM) {
                    $ecs_ids = u::query("SELECT CONCAT(u.full_name, ' - ', u.username, ' : ', u.hrm_id) ec_name, u.id ec_id FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WhERE u.status > 0 AND (t.role_id = " . ROLE_EC . " OR t.role_id = " . ROLE_EC_LEADER . ") AND t.branch_id IN ($my_branches)");
                    $ecs_ids = array_merge([['ec_name' => 'Chọn EC', 'ec_id' => '']], $ecs_ids);
                    $ecs_ids = apax_remove_dublicates($ecs_ids);
                    $response['ecs'] = $ecs_ids;
                    $response['cm_id'] = $uid;
                    $title_id = 3;
                }

                $cs_ids = u::query("SELECT CONCAT(u.full_name, ' - ', u.username, ' : ', u.hrm_id) cm_name, u.id cm_id FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WhERE u.status > 0 AND (t.role_id = 55 OR t.role_id = 56) AND t.branch_id IN ($my_branches)");
                $cs_ids = array_merge([['cm_name' => 'Vui lòng chọn CS', 'cm_id' => '']], $cs_ids);
                $cs_ids = apax_remove_dublicates($cs_ids);
                $response['cms'] = $cs_ids;


                $response['title_id'] = $title_id;
                $school_grades = u::query("SELECT id class_id, `name` class_name FROM school_grades WHERE `status` > 0");
                $school_grades = array_merge([['class_id' => '', 'class_name' => 'Chọn School grade']], $school_grades);
                $response['school_grades'] = $school_grades;
            }
        }
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $response
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student = Student::find($id);
        return response()->json($student);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        $session = $request->users_data;
        $roles = $session->roles_detail;
        $branch_list = [];
        if ($roles) {
            foreach ($roles as $role) {
                $role_ids[] = $role->role_id;
                $branch_list[] = $role->branch_id;
            }
            $role_id = $session->role_id;
            $role_param = "role_$role_id";
            $role_data = $session->roles->$role_param;
            $region_id = $role_data->region_id;
            $cm_id = (int)$request->cm_id;
            $ec_id = (int)$request->ec_id;
            $uid = $session->id;
            $post = $request->input();
            $avatar = ada()->upload($request->avatar, 'avatars/students');
            $file_attached = ada()->upload($request->attached_file);
            $student = Student::find($id);
            $used_id = $request->crm_id;
            $student->stu_id = $request->stu_id;
            $student->name = $request->firstname." ".($request->midname ? $request->midname." ":"").$request->lastname;
            $student->firstname = $request->firstname;
            $student->lastname = $request->lastname;
            $student->midname = $request->midname;
            $student->nick = $request->nick;
            $student->gender = $request->gender;
            $student->note = $request->note;
            $student->type = $request->type;
            $student->facebook = $request->facebook;
            $student->phone = $request->phone;
            $student->email = $request->email;
            if ($request->date_of_birth != '' && strtotime($request->date_of_birth)) {
                $student->date_of_birth = $request->date_of_birth;
            }
            $student->gud_mobile1 = $request->gud_mobile1;
            $student->gud_name1 = $request->gud_firstname1." ".($request->gud_midname1 ? $request->gud_midname1." ":"").$request->gud_lastname1;
            $student->gud_firstname1 = $request->gud_firstname1;
            $student->gud_lastname1 = $request->gud_lastname1;
            $student->gud_midname1 = $request->gud_midname1;
            $student->gud_email1 = $request->gud_email1;
            $student->gud_card1 = $request->gud_card1;
            $student->gud_birth_day1 = $request->gud_birth_day1;
            $student->gud_mobile2 = $request->gud_mobile2;
            $student->gud_name2 = $request->gud_firstname2." ".($request->gud_midname2 ? $request->gud_midname2." ":"").$request->gud_lastname2;
            $student->gud_firstname2 = $request->gud_firstname2;
            $student->gud_lastname2 = $request->gud_lastname2;
            $student->gud_midname2 = $request->gud_midname2;
            $student->gud_email2 = $request->gud_email2;
            $student->gud_card2 = $request->gud_card2;
            $student->gud_birth_day2 = $request->gud_birth_day2;
            $student->gud_gender1 = $request->gud_gender1;
            $student->gud_gender2 = $request->gud_gender2;
            $student->gud_job1 = $request->gud_job1;
            $student->gud_job2 = $request->gud_job2;
            $student->address = $request->address;
            $student->province_id = $request->province_id;
            $student->district_id = $request->district_id;
            $student->school = $request->school;
            $student->school_level = $request->school_level;
            $student->school_grade = $request->school_grade;
            // $student->creator_id = $uid;
            $student->editor_id = $uid;
            $student->current_classes = $request->current_classes;
            $student->used_student_ids = $used_id;
            $student->avatar = $avatar;
            $student->branch_id = $request->branch_id;
            $student->meta_data = $request->meta_data;
            $student->source = $request->source;
            $student->tracking = $request->tracking;
            if ($request->sibling_id) {
                $sib_id = (int)str_replace('CMS', '', $request->sibling_id);
                $sib_cd = $sib_id - 20000000;
                $sib = u::first("SELECT s.id FROM students s WHERE id = $sib_cd AND s.status >0");
                $student->sibling_id = $sib && isset($sib->id) ? $sib->id : 0;
            } else {
                $student->sibling_id = '';
            }
            $student->attached_file = $file_attached;
            $unix_check_dublicate = md5($request->name . $request->gud_name1 . $request->gud_mobile1);
            $is_existed = u::first("SELECT COUNT(*) existed FROM students WHERE (md5(CONCAT(name, gud_name1, gud_mobile1)) = '$unix_check_dublicate') OR (md5(CONCAT(name, gud_name2, gud_mobile2)) = '$unix_check_dublicate') OR (md5(CONCAT(name, gud_name1, gud_mobile2)) = '$unix_check_dublicate') OR (md5(CONCAT(name, gud_name2, gud_mobile1)) = '$unix_check_dublicate')");
            $is_existed = (int)$is_existed->existed;
            $old_student = u::first("SELECT s.*, b.hrm_id branch_hrm FROM students s LEFT JOIN branches b ON s.branch_id = b.id WHERE s.id = $id AND s.status >0");
            $user_query = "SELECT st.*,
                        ec.full_name ec_name,
                        ec.id ec_id,
                        cm.full_name cm_name,
                        cm.id cm_id,
                        ts.ec_leader_id,
                        ts.om_id
                      FROM students st
                      LEFT JOIN term_student_user ts on ts.student_id = st.id
                      LEFT JOIN users ec on ec.id = ts.ec_id
                      LEFT JOIN users cm on cm.id = ts.cm_id
                      WHERE st.id = $id AND st.status >0";
            $ec_info = u::first($user_query);
            $data = $ec_info;
            $old_ec_name = $data->ec_name;
            $old_cm_name = $data->cm_name;
            $old_student->ec_id = $ec_info->ec_id;
            $old_student->cm_id = $ec_info->cm_id;
            $cm_id = $request->cm_id;
            $code = APICode::SUCCESS;
            $data = (object)['success' => $request->name];
            $student->save();
            $new_student = $student;
            $new_student->ec_id = $request->ec_id;
            $new_student->cm_id = $request->cm_id;
            $info_change = "";
            if ($new_student->name != $old_student->name) {
                $info_change .= "Sửa tên: '$old_student->name' - thành: '$new_student->name'.<br/>\n";
            }
            if ($new_student->nick != $old_student->nick) {
                $info_change .= "Sửa nick: '$old_student->nick' - thành: '$new_student->nick'.<br/>\n";
            }
            if ($new_student->gender != $old_student->gender) {
                $from_gender = strtolower($old_student->gender) == 'f' ? 'Nữ' : 'Nam';
                $to_gender = strtolower($new_student->gender) == 'f' ? 'Nữ' : 'Nam';
                $info_change .= "Sửa giới tính: '$from_gender' - thành: '$to_gender'.<br/>\n";
            }
            if ($new_student->phone != $old_student->phone) {
                $info_change .= "Sửa số điện thoại: '$old_student->phone' - thành: '$new_student->phone'.<br/>\n";
            }
            if ($new_student->address != $old_student->address) {
                $info_change .= "Sửa địa chỉ: '$old_student->address' - thành: '$new_student->address'.<br/>\n";
            }
            if ($new_student->school != $old_student->school) {
                $info_change .= "Sửa trường học: '$old_student->school' - thành: '$new_student->school'.<br/>\n";
            }
            if ($new_student->email != $old_student->email) {
                $info_change .= "Sửa email: '$old_student->email' - thành: '$new_student->email'.<br/>\n";
            }
            if ($new_student->source != $old_student->source) {
                $info_change .= "Sửa nguồn từ: '$old_student->source' - thành: '$new_student->source'.<br/>\n";
            }
            if ($new_student->date_of_birth != $old_student->date_of_birth) {
                $info_change .= "Sửa ngày sinh: '$old_student->date_of_birth' - thành: '$new_student->date_of_birth'.<br/>\n";
            }
            if ($new_student->gud_name1 != $old_student->gud_name1) {
                $info_change .= "Sửa tên phụ huynh: '$old_student->gud_name1' - thành: '$new_student->gud_name1'.<br/>\n";
            }
            if ($new_student->gud_mobile1 != $old_student->gud_mobile1) {
                $info_change .= "Sửa số điện thoại phụ huynh: '$old_student->gud_mobile1' - thành: '$new_student->gud_mobile1'.<br/>\n";
            }
            if ($new_student->ec_id != $old_student->ec_id) {
                $info_change .= "Sửa EC từ: '$old_student->ec_id' - sang: '$new_student->ec_id'.<br/>\n";
            }
            if ($new_student->cm_id != $old_student->cm_id) {
                $info_change .= "Sửa CM từ: '$old_student->cm_id' - sang: '$new_student->cm_id'.<br/>\n";
            }
            $student_id = $student->id;
            $ec_id = (int)$request->ec_id;
            $status = 1;
            $ec_id = (int)$request->ec_id;
            $branch_id = (int)$request->branch_id;
            $lastInsertedId = $student->id;
            $branch_id = (int)$request->branch_id;
            $ceo_branch_id = 0;
            $ceo_region_id = 0;
            $region_id = 0;
            $zone_id = 0;
            $ec_leader_id = 0;
            $ec_id = (int)$request->ec_id;
            $cm_id = (int)$request->cm_id;
            $cs_id = (int)$request->cm_id;
            $om_id = 0;
            $content = $request->{'content'};
            if ($role_id == ROLE_EC) {
                $ec_id = $uid;
            }
            if ($role_id == ROLE_CM) {
                $cm_id = $uid;
            }
            $current_time = date('Y-m-d H:i:s');

            $ecLeader = u::first("SELECT u2.id as ec_leader_id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $ec_id");
            $csLeader = u::first("SELECT u2.id as cs_leader_id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $cs_id");

            $ecLeaderId = ($ecLeader && $ecLeader->ec_leader_id >0) ? $ecLeader->ec_leader_id : $ec_id;
            $csLeaderId = ($csLeader && $csLeader->cs_leader_id >0) ? $csLeader->cs_leader_id : $cs_id;

            u::query("UPDATE term_student_user SET cm_id = '$cs_id', ec_id = '$ec_id',ec_leader_id = '$ecLeaderId', om_id = '$csLeaderId' WHERE student_id = '$student_id'");
            if ($info_change) {
                DB::table('log_student_update')->insert(
                    [
                        'student_id' => $lastInsertedId,
                        'updated_by' => $uid,
                        'cm_id' => $cs_id,
                        'status' => $status,
                        'branch_id' => $branch_id,
                        'ceo_branch_id' => $ceo_branch_id,
                        'content' => $info_change,
                        'updated_at' => $current_time
                    ]
                );
            }
            if($ec_info && ($ec_info->ec_id !=$ec_id || $ec_info->cm_id !=$cs_id || $ec_info->ec_leader_id !=  $ecLeaderId || $ec_info->om_id!=$csLeaderId)){
                DB::table('log_manager_transfer')->insert(
                    [
                    'student_id' => $lastInsertedId,
                    'from_branch_id' => $branch_id,
                    'to_branch_id' => $branch_id,
                    'from_cm_id' => $ec_info->cm_id,
                    'to_cm_id' => $cs_id,
                    'from_ec_id' => $ec_info->ec_id,
                    'to_ec_id' => $ec_id,
                    'from_ec_leader_id' => $ec_info->ec_leader_id,
                    'to_ec_leader_id' => $ecLeaderId,
                    'from_om_id' => $ec_info->om_id,
                    'to_om_id' => $csLeaderId,
                    'date_transfer' => date('Y-m-d H:i:s'),
                    'updated_by' => $request->users_data->id,
                    'note' => "Từ cập nhật thông tin học sinh",
                    'created_at'=> date('Y-m-d H:i:s'),
                    'updated_at'=> date('Y-m-d H:i:s')
                    ]
                );
            }
            if ($student->accounting_id) {
                $cyberAPI = new CyberAPI();
                $cyberAPI->updateStudent($student, $uid);
            }
            if($student->id_lms){
                $LmsAPI = new LMSAPIController();
                $LmsAPI->updateStudentLMS($student->id);
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function getStudentUpdate($id)
    {
        $query = "SELECT ls.*,
                ec.full_name ec_name,
                ec.username ec_username,
                cm.full_name cm_name,
                cm.username cm_username
                from log_student_update ls
                left join users ec on ec.id = ls.updated_by
                left join users cm on cm.id = ls.cm_id
                where ls.student_id = $id";

        $data = DB::select(DB::raw($query));

        return response()->json($data);
    }

    public function getEnrolmentHistory($id)
    {
        $query = "SELECT c.*, if(s.type = 0, 'Thường', 'VIP') student_type,
                  CASE c.type
                    WHEN 0 THEN 'Học trải nghiệm'
                    WHEN 1 THEN 'Chính thức'
                    WHEN 2 THEN 'Tái phí bình thường'
                    WHEN 3 THEN 'Tái phí do nhận chuyển phí'
                    WHEN 4 THEN 'Chỉ nhận chuyển phí'
                    WHEN 5 THEN 'Chuyển trung tâm'
                    WHEN 6 THEN 'Chuyển lớp'
                    WHEN 7 THEN 'Tái phí chưa full phí'
                      END contract_type,
                  CASE c.status
                    WHEN 1 THEN 'Chưa đóng phí'
                    WHEN 2 THEN 'Còn nợ phí'
                    WHEN 3 THEN 'Chưa xếp lớp'
                    WHEN 4 THEN 'Đang bảo lưu'
                    WHEN 5 THEN 'Học bổng/VIP'
                    WHEN 6 THEN 'Đang đi học'
                    WHEN 7 THEN 'Đã bị Wthdraw'
                    WHEN 8 THEN 'Đã bỏ cọc'
                      END enrolment_status,
                  c.status,
                  IF(c.status = 6 AND enrolment_last_date < CURDATE(), 1, 0) withdraw_now, 
                  ec.full_name ec_name,
                  ec.username ec_username,
                  cm.full_name cm_name,
                  cm.username cm_username,
                  br.name branch_name,
                  cl.cls_name class_name,
                  pr.name program_name,
                  st.name semester_name,
                  f.name tuition_fee_name,
                  p.name product_name
                FROM contracts c
                  LEFT JOIN students s ON s.id = c.student_id
                  LEFT JOIN branches br ON br.id = c.branch_id
                  LEFT JOIN users ec on ec.id = c.ec_id
                  LEFT JOIN users cm on cm.id = c.cm_id
                  LEFT JOIN classes cl on cl.id = c.class_id
                  LEFT JOIN programs pr on pr.id = cl.program_id
                  LEFT JOIN tuition_fee f on f.id = c.tuition_fee_id
                  LEFT JOIN products p on p.id = c.product_id
                  LEFT JOIN semesters st on st.id = pr.semester_id
                WHERE c.student_id = $id ORDER BY c.id DESC";
        $enrolments = DB::select(DB::raw($query));
        return response()->json($enrolments);
    }

    public function getContractsByStudent($student_id)
    {
        $query = "SELECT c.id contract_id,
                c.created_at,
                c.start_date,
                br.name branch_name,
                s.name student_name,
                if(s.type = 0, 'Thường', 'VIP') student_type,
                pg.name program_name,
                ec.full_name ec_name,
                f.name tuition_fee_name,
                f.session tuition_session,
                f.price tuition_price,
                pm.must_charge contract_must_charge
                FROM contracts c
                LEFT JOIN students s ON s.id = c.student_id
                LEFT JOIN term_student_user t ON t.student_id = s.id
                LEFT JOIN users ec ON ec.id = t.ec_id
                LEFT JOIN users cm ON cm.id = t.cm_id
                LEFT JOIN programs pg ON pg.id = c.program_id
                LEFT JOIN products pd ON pd.id = c.product_id
                LEFT JOIN branches br ON br.id = s.branch_id
                LEFT JOIN tuition_fee f on f.id = c.tuition_fee_id
                LEFT JOIN payment pm on pm.contract_id = c.id
                WHERE c.student_id = $student_id";

        $contracts = DB::select(DB::raw($query));
        return response()->json($contracts);
    }

    public function getReservesByStudent($student_id)
    {
        $query = "SELECT r.id reserve_id,
                  r.created_at reserve_created_at,
                  r.end_date reserve_end_at,
                  r.session reserve_session,
                  r.note reserve_reason,
                  r.start_date reserve_start_date,
                  r.note reserve_note,
                  if(r.is_reserved = 0, 'Giữ Chỗ', 'Không giữ chỗ') reserve_is_reserved,
                  if(r.type = 0, 'Bình thường', 'Đặc biệt') reserve_type,
                  s.name student_name,
                  s.crm_id,
                  s.stu_id,
                  br.name branch_name,
                  pg.name program_name
                FROM reserves r
                  LEFT JOIN students s ON s.id = r.student_id
                  LEFT JOIN contracts c ON c.id = r.contract_id
                  LEFT JOIN branches br ON br.id = s.branch_id
                  LEFT JOIN programs pg ON pg.id = c.program_id
                WHERE r.student_id = $student_id";

        $reserves = DB::select(DB::raw($query));
        return response()->json($reserves);
    }

    public function getPendingHistoryList($student_id)
    {
        $query = "SELECT pd.*, if(s.type = 0, 'Thường', 'VIP') student_type,
                  c.type contract_type,
                  ec.full_name ec_name,
                  ec.username ec_username,
                  cm.full_name cm_name,
                  cm.username cm_username,
                  br.name branch_name,
                  cl.cls_name class_name,
                  pr.name program_name,
                  st.name semester_name,
                  f.name tuition_fee_name,
                  p.name product_name
                FROM pendings pd
                  LEFT JOIN students s ON s.id = pd.student_id
                  LEFT JOIN contracts c ON c.id = pd.contract_id
                  LEFT JOIN branches br ON br.id = s.branch_id
                  LEFT JOIN term_student_user t ON t.student_id = s.id
                  LEFT JOIN users ec on ec.id = t.ec_id
                  LEFT JOIN users cm on cm.id = t.cm_id
                  LEFT JOIN classes cl on cl.id = e.class_id
                  LEFT JOIN programs pr on pr.id = cl.program_id
                  LEFT JOIN tuition_fee f on f.id = c.tuition_fee_id
                  LEFT JOIN products p on p.id = c.product_id
                  LEFT JOIN semesters st on st.id = pr.semester_id
                WHERE pd.student_id = $student_id GROUP BY pd.id ORDER BY pd.id DESC";
        $pendings = DB::select(DB::raw($query));
        return response()->json($pendings);
    }

    public function getClassTransferHistoryList($student_id)
    {
        $query = "SELECT clt.*, if(s.type = 0, 'Thường', 'VIP') student_type,
                  c.type contract_type,
                  ec.full_name ec_name,
                  ec.username ec_username,
                  cm.full_name cm_name,
                  cm.username cm_username,
                  br1.name from_branch_name,
                  br2.name to_branch_name,
                  creator.username creator_name,
                  cl1.cls_name from_class_name,
                  cl2.cls_name to_class_name
                FROM class_transfer clt
                  LEFT JOIN students s ON s.id = clt.student_id
                  LEFT JOIN contracts c ON c.id = clt.contract_id
                  LEFT JOIN branches br1 ON br1.id = clt.from_branch_id
                  LEFT JOIN branches br2 ON br2.id = clt.to_branch_id
                  LEFT JOIN classes cl1 ON cl1.id = clt.from_class_id
                  LEFT JOIN classes cl2 ON cl2.id = clt.to_class_id
                  LEFT JOIN term_student_user t ON t.student_id = s.id
                  LEFT JOIN users creator on creator.id = clt.creator_id
                  LEFT JOIN users ec on ec.id = t.ec_id
                  LEFT JOIN users cm on cm.id = t.cm_id
                WHERE clt.student_id = $student_id GROUP BY clt.id ORDER BY clt.id DESC";
        $class_transfers = DB::select(DB::raw($query));
        return response()->json($class_transfers);
    }

    public function getChargeListByStudent(Request $request, $student_id)
    {
        $session = $request->users_data;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $query = "SELECT p.id payment_id,
                      c.id contract_id,
                      c.type contract_type,
                      s.id student_id,
                      s.name student_name,
                      u.id creator_id,
                      u.username creator_name,
                      CASE p.type
                        WHEN 0 THEN 'Bỏ cọc'
                        WHEN 1 THEN 'Lần đầu'
                        WHEN 2 THEN 'Tái phí'
                          END payment_type,
                      p.contract_id,
                      if(p.method = 0, 'Tiền mặt', 'Chuyển khoản') payment_method,
                      if(p.payload = 0, 'Một lần', 'Nhiều lần') payment_type,
                      p.must_charge,
                      p.amount,
                      p.total,
                      p.debt,
                      p.created_at,
                      p.creator_id,
                      p.hash_key,
                      p.count,
                      p.type,
                      p.note

                FROM payment p
                LEFT JOIN contracts c ON c.id = p.contract_id
                LEFT JOIN users u ON u.id = p.creator_id
                LEFT JOIN students s ON s.id = c.student_id
                WHERE c.student_id = '$student_id' ";
        $contracts = DB::select(DB::raw($query));

        return response()->json($contracts);
    }

    public function getStudentName(Request $request, $lms_id = null)
    {
        $response = new Response();
        $code = APICode::SUCCESS;
        $res = null;

        if ($lms_id) {
            $query = "SELECT `name` student_name FROM students WHERE stu_id = $lms_id";
            $result = DB::select(DB::raw($query));
            if (!empty($result)) {
                $code = APICode::SUCCESS;
                $res = $result[0];
            } else {
                $code = APICode::PAGE_NOT_FOUND;
            }
        } else {
            $code = APICode::PAGE_NOT_FOUND;
        }

        return $response->formatResponse($code, $res);
    }

    public function checkEditExistInformation(Request $request)
    {
        $student_id = $request->student_id;
        $crm_id = $request->crm_id;
        $stu_id = $request->stu_id;
        $q = null;
        $rs = null;
        $q1 = "SELECT COUNT(s.id) existed FROM students s WHERE s.id != '$student_id' AND s.crm_id = '$crm_id' AND s.status >0";
        $rs1 = u::first($q1);
        if ($crm_id) {
            $q = $q1;
            $rs = $rs1;
            if ($rs->existed && $crm_id) {
                return -1;
            }
        } else {
            return 0;
        }
    }

    public function countStudents(Request $request, $branch_id)
    {
        $response = new Response();
        $res = null;
        if ($branch_id && is_numeric($branch_id)) {
            $res = Student::where('branch_id', $branch_id)->select('id')->get();
        }

        return $response->formatResponse(APICode::SUCCESS, $res);
    }

    public function checkPhoneExist($phone)
    {
        $message = '';
        $exist = Student::where('gud_mobile1', '=', $phone)->first();
        $status = $exist ? 1 : 0;
        if($exist){
            $branch_info =  u::first("SELECT b.name FROM  term_student_user AS t LEFT JOIN branches AS b ON b.id=t.branch_id WHERE t.student_id=".$exist->id);
            $message = $branch_info ?"Số điện thoại đã tồn tại ở ".$branch_info->name:'Số điện thoại đã tồn tại trên hệ thống';
        }
        $data = (object) array(
            'status'=>$status,
            'message'=> $message
        );
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    public function checkPhoneExistEdit(Request $request, $phone, $student_id,$branch_id = 0)
    {
        //
//        $exist = Student::where('gud_mobile1', '=', $phone)->where('id', '!=', $student_id)->first();
//        $exist = $exist ? 1 : 0;
//        $response = new Response();
//        return $response->formatResponse(APICode::SUCCESS, $exist);
        //$response = new Response();
        $tmp_cond="";
        $student_info = u::first("SELECT source FROM students WHERE id=$student_id");
        if($student_info && in_array($student_info->source,array(31,27)) ){
            $tmp_cond = " AND s.branch_id!=12";
        }
        $sibling = $request->sibling;
        $gud = $request->gud;
        $dataStd= [];
        if ($sibling && $sibling != 'null'){
            if ($gud == 2){
                $cond = $sibling ? " AND s.id!= $sibling":"";
                $dataStd = u::first("SELECT COUNT(s.id) existed FROM students s WHERE (s.`gud_mobile1` = '{$phone}' OR s.`gud_mobile2` = '{$phone}') AND s.checked!=2 AND s.id != $student_id $cond");
                $dataSQL = "SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name,(SELECT so.name FROM sources so WHERE so.id = s.source) AS source_name FROM students s WHERE (s.`gud_mobile1` = '{$phone}' OR s.`gud_mobile2` = '{$phone}') AND s.checked!=2 AND s.id != $student_id $cond";
                $rawData = [];
                if ($branch_id > 0){
                    $rawData = u::first("SELECT COUNT(s.id) existed FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$phone}' OR s.`gud_mobile2` = '{$phone}') AND s.type = 0 AND s.branch_id NOT IN (100) AND s.branch_id != $branch_id $tmp_cond");
                    $rawSQL = "SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name ,s.source FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$phone}' OR s.`gud_mobile2` = '{$phone}') AND s.type = 0 AND s.branch_id != $branch_id $tmp_cond";
                }
            }
        }
        else{
            $dataStd = u::first("SELECT COUNT(s.id) existed FROM students s WHERE (s.`gud_mobile1` = '{$phone}' OR s.`gud_mobile2` = '{$phone}') AND s.checked!=2 AND s.id != $student_id");
            $dataSQL = "SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name,(SELECT so.name FROM sources so WHERE so.id = s.source) AS source_name FROM students s WHERE (s.`gud_mobile1` = '{$phone}' OR s.`gud_mobile2` = '{$phone}') AND s.checked!=2 AND s.id != $student_id";
            $rawData = [];
            if ($branch_id > 0){
                $rawSQL = "SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name,s.source FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$phone}' OR s.`gud_mobile2` = '{$phone}') AND s.type = 0 AND s.branch_id != $branch_id $tmp_cond";
                $rawData = u::first("SELECT COUNT(s.id) existed FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$phone}' OR s.`gud_mobile2` = '{$phone}') AND s.type = 0 AND s.branch_id NOT IN (100) AND s.branch_id != $branch_id $tmp_cond");
            }
        }

        $msg = "";
        if (isset($dataStd->existed) && $dataStd->existed >= 1){
            $dataStd->existed = 1;
            $detail = u::first($dataSQL);
            $msg = " đã tồn tại ở: {$detail->branch_name} nguồn {$detail->source_name}, vui lòng nhập số điện thoại khác.";
        }
        if (isset($rawData->existed) && $rawData->existed == 1){
            $detail = u::first($rawSQL);
            $msg = " thuộc data khách hàng đang chăm sóc của: {$detail->branch_name} nguồn {$detail->source}, vui lòng nhập số điện thoại khác.";
        }
        $data = !$msg? (object)['existed' =>0,'msg' =>$msg] : (object)['existed' =>1,'msg' =>$msg];

        return response()->json(['data' =>$data]);
//        return $response->formatResponse(APICode::SUCCESS, $data->existed);
    }

    public function downloadStudentTemplate(\App\Templates\Imports\Student $template, TemplateExportService $service)
    {
        try {
            $service->export($template, "student_import_template");
        } catch (Exception $e) {
        }
    }

    public function reportByDate(Request $request)
    {
        $params = $request->all();
        $service = new StudentReportService();
        $data = $service->getStudentReportByDate($params);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }
    public function exportByDate(Request $request, StudentReportService $service, \App\Templates\Exports\Student $template)
    {
        $params = $request->all();
        $service->exportStudentByDate($params, $template);
    }


    public function exportStudying(Request $request, StudentReportService $service, \App\Templates\Exports\StudentStudying $template){
        $params = $request->all();
        $service->exportStudentStudying($params, $template);
    }

    public function getDetail($id = 0){
        $sq1 = "SELECT 
                c.id AS contract_id,c.`total_sessions`,
                (SELECT class_day FROM `sessions` WHERE c.class_id = class_id) AS class_day,
                (SELECT accounting_id FROM products WHERE id = c.`product_id`) AS product_name,
                c.`tuition_fee_id`,c.enrolment_accounting_id,
                (SELECT `crm_id` FROM `students` WHERE id = c.student_id) AS cms_id,
                (SELECT accounting_id FROM `students` WHERE id = c.student_id) AS cyber_code,
                (SELECT `name` FROM `students` WHERE id = c.student_id) AS std_name,
                (SELECT `name` FROM branches WHERE id = c.branch_id) AS branch_name,
                (SELECT cls_name FROM `classes` WHERE id = c.class_id) AS cls_name,
                (SELECT accounting_id FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_accounting_id,
                (SELECT NAME FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_name,
                c.branch_id,accounting_id,c.code,
                c.student_id,c.created_at,c.status,c.type,c.program_id, 
                c.class_id, c.payment_id,c.count_recharge,c.`enrolment_start_date`,c.`enrolment_last_date`,c.`enrolment_end_date`, c.`start_date`, c.`end_date`,c.`enrolment_withdraw_date`,c.`total_sessions`
                ,c.`tuition_fee_id`,c.`product_id`,c.`debt_amount`,c.`accounting_id`, c.`must_charge`,c.`total_charged`,c.`tuition_fee_price`,c.`real_sessions`  FROM contracts c
                WHERE c.student_id = $id  AND c.status = 6";

        $data = u::first($sq1);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    public function pendingApproval(Request $request, $id){
        $studentId = (int)$id;

        $sql = "SELECT \"Chuyển lớp\" AS action_name,
                (SELECT NAME FROM students WHERE id = c.`student_id`) AS student_name,
                \"Chờ duyệt\" AS status_name,
                (SELECT `name` FROM branches WHERE id = c.from_branch_id) AS branch_name,
                CONCAT((SELECT cls_name FROM classes WHERE id = c.`from_class_id`),' ---> ',
                (SELECT cls_name FROM classes WHERE id = c.`to_class_id`)) AS content, c.`created_at`,
                (SELECT full_name FROM users WHERE id = c.`creator_id`) AS creator_name
                FROM `class_transfer` c WHERE c.`final_approved_at` IS NULL AND c.`student_id` = $studentId AND c.from_branch_id = c.to_branch_id
                UNION
                SELECT \"Chuyển TT\" AS action_name,
                (SELECT NAME FROM students WHERE id = c.`student_id`) AS student_name,
                \"Chờ duyệt\" AS status_name,
                (SELECT `name` FROM branches WHERE id = c.from_branch_id) AS branch_name,
                CONCAT((SELECT NAME FROM branches WHERE id = c.`from_branch_id`),' ---> ',
                (SELECT NAME FROM branches WHERE id = c.`to_branch_id`)) AS content, c.`created_at`,
                (SELECT full_name FROM users WHERE id = c.`creator_id`) AS creator_name
                FROM `class_transfer` c WHERE c.`final_approved_at` IS NULL AND c.`student_id` = $studentId AND c.from_branch_id != c.to_branch_id
                UNION
                SELECT \"Chuyển Phí\" AS action_name,(SELECT NAME FROM students WHERE id = t.`from_student_id`) AS student_name,\"Chờ duyệt\" AS status_name,
                (SELECT NAME FROM branches WHERE id = (SELECT branch_id FROM students WHERE id = t.`from_student_id`)) AS branch_name,
                CONCAT((SELECT NAME FROM students WHERE id = t.`from_student_id`),':(',(SELECT NAME FROM branches WHERE id = (SELECT branch_id FROM students WHERE id = t.`from_student_id`)),')',' ---> ',
                (SELECT NAME FROM students WHERE id = t.`to_student_id`),':(',(SELECT NAME FROM branches WHERE id = (SELECT branch_id FROM students WHERE id = t.`to_student_id`)),')'
                ,', Ngày chuyển: ', t.transfer_date,'- Total:', t.transferred_amount) AS content, t.`created_at`,(SELECT full_name FROM users WHERE id = t.`creator_id`) AS creator_name
                FROM `tuition_transfer_v2` t WHERE t.status IN(1,4) AND t.`from_student_id` = $studentId
                UNION
                SELECT \"Nhận Phí\" AS action_name,(SELECT NAME FROM students WHERE id = t.`to_student_id`) AS student_name,\"Chờ duyệt\" AS status_name,
                (SELECT NAME FROM branches WHERE id = (SELECT branch_id FROM students WHERE id = t.`to_student_id`)) AS branch_name,
                CONCAT((SELECT NAME FROM students WHERE id = t.`from_student_id`),':(',(SELECT NAME FROM branches WHERE id = (SELECT branch_id FROM students WHERE id = t.`from_student_id`)),')',' ---> ',
                (SELECT NAME FROM students WHERE id = t.`to_student_id`),':(',(SELECT NAME FROM branches WHERE id = (SELECT branch_id FROM students WHERE id = t.`to_student_id`)),')'
                ,', Ngày chuyển: ', t.transfer_date,'- Tổng tiền: ', t.transferred_amount) AS content, t.`created_at`,(SELECT full_name FROM users WHERE id = t.`creator_id`) AS creator_name
                FROM `tuition_transfer_v2` t WHERE t.status IN(1,4) AND t.`to_student_id` = $studentId
                UNION
                SELECT \"Bảo lưu\" AS action_name,(SELECT NAME FROM students WHERE id = r.`student_id`) AS student_name,\"Chờ duyệt\" AS status_name,
                (SELECT NAME FROM branches WHERE id = (SELECT branch_id FROM students WHERE id = r.`student_id`)) AS branch_name,
                CONCAT(r.`start_date`,' --->', r.`end_date`,', số buổi:',r.session,', ',IF(r.is_reserved= 1, 'Bảo lưu: giữ chỗ', 'Bảo lưu: Không giữ chỗ')) AS content, r.`created_at`,(SELECT full_name FROM users WHERE id = r.`creator_id`) AS creator_name
                FROM `reserves` r WHERE r.`final_approved_at` IS NULL AND r.`student_id` = $studentId
                UNION
                SELECT \"Rút phí\" AS action_name,(SELECT NAME FROM students WHERE id = r.`student_id`) AS student_name,\"Chờ duyệt\" AS status_name,
                (SELECT NAME FROM branches WHERE id = (SELECT branch_id FROM students WHERE id = r.`student_id`)) AS branch_name,
                CONCAT('Số tiền được rút: ', r.`refun_amount`) AS content, r.`created_at`,(SELECT full_name FROM users WHERE id = r.`creator_id`) AS creator_name
                FROM `withdrawal_fees` r WHERE r.`approved_at` IS NULL AND r.`student_id` = $studentId";

        $data = u::query($sql);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }
    public function getSchools($province_id, $district_id, $school_level)
    {
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();
        if ($province_id && $district_id && $school_level) {
            $data = u::query("SELECT `name`, `level`, address, id FROM schools WHERE `level` = '$school_level' AND district_id = $district_id AND province_id = $province_id");
            if (count($data)) {
                foreach($data as $i => $o) {
                    if (mb_strtolower(substr($o->name, 0, strlen($o->level))) != mb_strtolower($o->level)) {
                        $name = $o->name;
                        $o->name = "$o->level $name";
                    }
                }
            }
        }
        return $response->formatResponse($code, $data);
    }
    public function getAllJobs()
    {
        $code = APICode::SUCCESS;
        $response = new Response();
        $data = u::query("SELECT id,title AS name FROM jobs WHERE `status` = 1");
        return $response->formatResponse($code, $data);
    }
    
    public function uploadFile($student_id){
        $code = APICode::SUCCESS;
        $response = new Response();
        $student_info= u::first("SELECT file_kt FROM students WHERE id=$student_id");
        $files_arr = array();
        $file_kt = $student_info->file_kt;
        if(isset($_FILES['files']['name'])){
            $countfiles = count($_FILES['files']['name']);
            $upload_location = FOLDER . DS . 'img/others/' ;
            for($index = 0;$index < $countfiles;$index++){
                if(isset($_FILES['files']['name'][$index]) && $_FILES['files']['name'][$index] != ''){
                    $filename = $_FILES['files']['name'][$index];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    $valid_ext = array("png","jpeg","jpg","pdf","txt","doc","docx","xls","xlsx");
                    if(in_array($ext, $valid_ext)){
                        $newfilename = date('Y_m_d_').rand(0,999)."_".$filename;
                        $path = $upload_location.$newfilename;
                        if(move_uploaded_file($_FILES['files']['tmp_name'][$index],$path)){
                            $files_arr[] = $newfilename;
                            $file_kt.= $file_kt ? "|*|"."static/img/others/".$newfilename : "static/img/others/".$newfilename;
                        }
                    }
                }
            }
            u::query("UPDATE students SET file_kt='$file_kt' WHERE id=$student_id");
        }
        return $response->formatResponse($code, "ok");
    }
    public function loadFile($student_id){
        $code = APICode::SUCCESS;
        $response = new Response();
        $student_info= u::first("SELECT file_kt FROM students WHERE id=$student_id");
        $files_arr = array();
        $file_kt = $student_info->file_kt;
        $file_kt = explode('|*|',$file_kt);
        foreach($file_kt AS $row){
            $files_arr[] =(object)[
                'link'=>"../".$row,
                'name'=>str_replace('static/img/others/','',$row),
                'file'=>$row
            ];
        }
        return $response->formatResponse($code, $files_arr);
    }
    public function removeFile(Request $request,$student_id){
        $code = APICode::SUCCESS;
        $response = new Response();
        $student_info= u::first("SELECT file_kt FROM students WHERE id=$student_id");
        $file_kt = $student_info->file_kt;
        $file_kt = explode('|*|',$file_kt);
        $tmp="";
        foreach($file_kt AS $row){
            if($row !=  $request->file_name){
                $tmp.= $tmp ? "|*|".$row : $row;
            }
        }
        u::query("UPDATE students SET file_kt='$tmp' WHERE id=$student_id");
        return $response->formatResponse($code, "ok");
    }
}
