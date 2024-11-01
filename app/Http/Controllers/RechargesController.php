<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\DiscountCode;
use App\Models\Response;
use App\Models\Contract;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use App\Providers\UtilityServiceProvider as u;

class RechargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // $holidays = u::getPublicHolydays(55);
       // $classdays = u::getClassDays(55);
      // $holidays = u::getPublicHolidays(55);
      // dd($holidays);
      //     // $holidays = u::getPublicHolydays($class_id);
      //  // dd( $holidays);
      // $sessions = 75;
      // $end = '2018-04-21';
      // $classdays = [4,6];
      // $data = u::calcNewStartDate($end, $classdays, $holidays);
      // dd($data);

      // $enrolment_end_date_query = "SELECT DATE_ADD(DATE('$e_start_date'), INTERVAL $tuition_fee DAY) as start_date";
      // $enrolment_end_date = DB::select(DB::raw($enrolment_end_date_query));
      // $start_date = $enrolment_end_date[0]->start_date;

      // $recharges = Contract::all();
      // return $start_date;

      // $latest_contract = DB::select(DB::raw("SELECT c.id AS latest_contract_id FROM contracts AS c WHERE c.id = (SELECT max(id) FROM contracts)"));
      // dd($latest_contract[0]);
      // $latest_end_date = $latest_contract->end_date;
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

    public function list(Request $request, $pagination, $search, $sort){
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $sort = json_decode($sort);
        $search = json_decode($search);
        $pagination = json_decode($pagination);
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $list = $this->data($request);
        $data->list = $list->data;
        $data->sort = $sort;
        $data->search = $search;
        $data->duration = $pagination->limit * 10;
        $data->pagination = ada()->paging($pagination, $list->total);
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listWaitcharged(Request $request, $pagination, $search, $sort){
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $sort = json_decode($sort);
        $search = json_decode($search);
        $pagination = json_decode($pagination);
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $list = $this->data($request, 1);
        $data->list = $list->data;
        $data->sort = $sort;
        $data->search = $search;
        $data->duration = $pagination->limit * 10;
        $data->pagination = ada()->paging($pagination, $list->total);
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listCharged(Request $request, $pagination, $search, $sort){
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $sort = json_decode($sort);
        $search = json_decode($search);
        $pagination = json_decode($pagination);
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $list = $this->data($request, 2);
        $data->list = $list->data;
        $data->sort = $sort;
        $data->search = $search;
        $data->duration = $pagination->limit * 10;
        $data->pagination = ada()->paging($pagination, $list->total);
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listWaiting(Request $request, $pagination, $search, $sort){
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $sort = json_decode($sort);
        $search = json_decode($search);
        $pagination = json_decode($pagination);
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $list = $this->data($request, 3);
        $data->list = $list->data;
        $data->sort = $sort;
        $data->search = $search;
        $data->duration = $pagination->limit * 10;
        $data->pagination = ada()->paging($pagination, $list->total);
      }
      return $response->formatResponse($code, $data);
    }

    private function query($select = "list") {
      $query = "FROM
          contracts AS c
          LEFT JOIN students AS s ON c.student_id = s.id
          LEFT JOIN branches AS b ON c.branch_id = b.id
          LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
          LEFT JOIN payment AS p ON p.contract_id = c.id
          LEFT JOIN products AS prd ON c.product_id = prd.id
          LEFT JOIN users AS u1 ON u1.id = c.ec_id
          LEFT JOIN users AS u2 ON u2.id = c.cm_id
          LEFT JOIN regions AS r ON r.id = b.region_id";
      return $query;
    }

    public function filter($branch_id, Request $request)
    {
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();
      if ($branch_id == 0) {
        $session = $request->users_data;
        $branches = $session->branches;
        $branch_id = $branches[0]->id;
      }
      if ($branch_id) {
        $data = (object)[];
        $programs_query = "SELECT id, program_id, parent_id, `name` FROM programs WHERE `status` > 0 AND branch_id = $branch_id";
        $tuition_fees_query = "SELECT t.id,
          CONCAT(p.name, ' (', t.name, ')') AS full_name,
          t.name, t.session, t.price, t.receivable, t.type
          FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.`status` > 0 AND (t.branch_id LIKE '$branch_id,%' OR t.branch_id LIKE '%,$branch_id,%' OR t.branch_id LIKE '%,$branch_id' OR t.branch_id = '$branch_id')";
      }
      $programs = DB::select(DB::raw($programs_query));
      $tuition_fees = DB::select(DB::raw($tuition_fees_query));
      $data->programs = array_merge([['id' => '', 'name' => 'Lọc theo chương trình']], $programs);
      $data->tuition_fees = array_merge([['id' => '', 'full_name' => 'Lọc theo gói phí']], $tuition_fees);
      return $response->formatResponse($code, $data);
    }

    private function select($select = "list") {
      $query = "SELECT
          DISTINCT(c.id),
          c.code AS code,
          c.accounting_id, 
          c.payload AS contract_payload,
          c.type AS contract_type,
          c.status AS contract_status,
          c.bill_info AS contract_bill_info,
          c.start_date AS start_date,
          c.receivable AS contract_receivable,
          c.description AS contract_description,
          c.must_charge AS contract_must_charge,
          c.total_charged AS total_charged,
          c.total_sessions AS contract_total_sessions,
          c.summary_sessions,
          c.bonus_sessions,
          c.total_discount AS contract_total_discount,
          c.after_discounted_fee AS after_discounted_fee,
          c.only_give_tuition_fee_transfer,
          c.expected_class,
          c.debt_amount,
          c.bill_info,
          u1.full_name AS contract_ec_name,
          u2.full_name AS contract_cm_name,
          IF(c.type IN (0,1,7,8) AND c.status IN (1,5,7,8) AND (c.enrolment_start_date IS NULL), 1, 0) delete_able,
          (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE hrm_id = u1.superior_id LIMIT 0, 1) AS contract_ec_leader_name, 
          s.id AS student_id,
          s.stu_id As lms_id,
          s.cms_id AS cms_id,
          s.crm_id,  
          CONCAT(s.stu_id, '~', s.accounting_id) AS mix_id,
          r.name AS region_name,
          b.name As branch_name,
          s.name AS student_name,
          s.date_of_birth AS birthday,
          s.nick AS student_nick,
          s.address,
          s.school,
          s.waiting_status,
          (SELECT NAME FROM programs WHERE id = c.program_id) AS program_name,
          COALESCE(s.gud_name1, s.gud_name2) AS parent_name,
          COALESCE(s.gud_mobile1, s.gud_mobile2) AS parent_mobile,
          COALESCE(s.gud_email1, s.gud_email2) AS parent_email,
          s.gender AS student_gender,
          prd.id AS product_id,
          prd.name AS product_name,
          c.real_sessions AS real_sessions,
          t.id AS tuition_fee_id,
          t.name AS tuition_fee_name,
          t.type AS tuition_fee_type,
          t.session AS tuition_fee_session,
          t.price AS tuition_fee_price,
          t.receivable AS tuition_fee_receivable,
          p.amount AS payment_amount";
      return $query;
    }

   private function where($request, $type = 0) {
      $search = json_decode($request->search);
      $session = $request->users_data;
      $user_id = $session->id;
      $role_id = $session->role_id;
      $branches = $search->branch ? (int)$search->branch : $session->branches_ids;
      $where = "WHERE c.count_recharge > 0 AND c.branch_id = s.branch_id AND c.status > 0 ";
      $where.= "AND c.branch_id IN ($branches)";
      if ($role_id == ROLE_REGION_CEO) {
        $where.= " AND c.ceo_region_id = $user_id";
      }
      if ($role_id == ROLE_BRANCH_CEO) {
        $where.= " AND c.ceo_branch_id = $user_id";
      }
      if ($role_id == ROLE_EC_LEADER) {
        $where.= " AND c.ec_leader_id = $user_id";
      }
      if ($role_id == ROLE_EC) {
        $where.= " AND c.ec_id = $user_id";
      }
      if ($role_id == 1200) {
        $where.= " AND s.source IN(26)";
      }
      if ($search->customer_type != '') {
        $where.= " AND c.type = $search->customer_type";
      }
      if ($search->program != '') {
        $where.= " AND c.program_id = $search->program";
      }
      if ($search->tuition_fee != '') {
        $where.= " AND c.tuition_fee_id = $search->tuition_fee";
      }
      if ($search->keyword != '') {
        $keyword = $search->keyword;
        $where.= " AND
          ( c.code LIKE '$keyword%'
          OR s.crm_id LIKE '$keyword%'
          OR s.stu_id LIKE '$keyword%'
          OR s.accounting_id LIKE '$keyword%'
          OR s.name LIKE '%$keyword%'
          OR s.nick LIKE '%$keyword%'
          OR s.email LIKE '%$keyword%'
          OR s.phone LIKE '$keyword%')";
      }
      switch ($type) {
        case 1: {
          $where.= " AND (c.total_charged = 0 OR c.total_charged IS NULL) AND c.type > 0 AND c.debt_amount > 0";
        } break;
        case 2: {
          $where.= " AND c.total_charged > 0 AND c.type > 0 AND c.total_charged <= c.must_charge AND c.debt_amount >= 0";
        } break;
        case 3: {
          $where.= " AND c.`status` < 6
            AND ((c.type > 0 AND c.status IN (1,2,3,4,5)) OR (c.type = 0 AND c.status IN (1,3,5)))
            AND ((c.real_sessions > 0 AND c.type IN (1,2,3,4,5,6,7)) OR (c.type IN (0,8,85)) OR c.type = 10)
            AND ((c.total_charged > 0 AND c.type IN (1,2,3,4,5,6,7)) OR (c.type IN (0,8,85)) OR c.type = 10)
            AND (SELECT count(id) FROM reserves WHERE student_id=c.student_id AND `status`=1 AND `start_date` < CURDATE() AND `end_date` > CURDATE()) =0
            AND (s.id NOT IN (SELECT student_id FROM contracts WHERE `status` = 6 AND student_id IS NOT NULL GROUP BY student_id) OR ((SELECT enrolment_last_date FROM contracts WHERE `status` = 1 AND student_id = c.student_id AND id = c.id) <= CURDATE())) 
            AND s.waiting_status=0
            AND c.`debt_amount` = 0";
        } break;
      }
      return "$where GROUP BY c.id";
    }

    private function data($request, $type = 0) {
      $pagination = json_decode($request->pagination);
      $sort = json_decode($request->sort);
      $order = "";
      $limit = "";
      if ($sort->by && $sort->to) {
        $order.= " ORDER BY $sort->by $sort->to";
      }
      if ($pagination->cpage && $pagination->limit) {
        $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
        $limit.= " LIMIT $offset, $pagination->limit";
      }
      $total = "SELECT COUNT(o.id) AS total FROM (SELECT c.id ";
      $select = $this->select();
      $query = $this->query();
      $where = $this->where($request, $type);
      $data = u::query("$select $query WHERE c.id IN (SELECT * FROM (SELECT c.id FROM contracts c LEFT JOIN students s ON c.student_id = s.id $where ORDER BY c.id DESC $limit) x ) GROUP BY id ORDER BY c.id DESC ");
      if (count($data)) {
        foreach ($data as $item) {
          $check_delete = false;
          if ($item->delete_able && isset($item->creator_id)) {
            $check_delete = (int)$item->creator_id == (int)$session->id;
            if ($session->role_id >= ROLE_BRANCH_CEO) {
              $check_delete = true;
            }
          }
          $item->delete_able = $check_delete;
        }
      }
      $total = u::first("$total $query $where) AS o");
      $total = is_object($total) ? $total->total : 0;
      $result = (Object)['data' => $data, 'total' => $total];
      return $result;
    }

    public function search(Request $request, $branch_id, $keyword) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($keyword) {
        $keys = explode('-', $keyword);
        $key1 = '';
        $key2 = $keyword;
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
            $where.= " AND u2.id = $user_id";
          }
          if ($role_id == ROLE_EC) {
            $where.= " AND t.ec_id = $user_id";
          }
          $key2 = $key2 ? trim($key2) : '';
          $where.= $key1 ? " AND ((s.stu_id LIKE '%$key1%' OR s.cms_id LIKE '%$key1%' OR s.crm_id LIKE '%$key1%') AND " : " AND ((s.stu_id LIKE '%$keyword%' OR s.cms_id LIKE '%$keyword%' OR s.crm_id LIKE '%$keyword%') OR ";
          $where.= $key2 ? " s.name LIKE '$key2%' OR s.nick LIKE '$key2%' OR s.accounting_id LIKE '%$keyword%')" : ')';
          // $where.= " AND c.debt_amount = 0 AND ((s.type = 0 AND c.type IN (1, 2, 3, 5, 6) AND c.status >= 3) OR (s.type = 1 AND c.type > 0) OR (c.type = 4 AND s.type = 0 AND c.real_sessions > 0))";
          $where.= " AND ((s.type = 0 AND ((c.type IN (1, 2, 5, 6, 8, 85, 86) AND c.status >= 3) OR (c.type IN (3, 4) AND c.real_sessions > 0))) OR (s.type = 1 AND c.type > 0) OR (c.must_charge = 0 AND c.total_charged>0) OR c.type = 10)
          AND c.id IN (SELECT max(id) FROM contracts WHERE student_id = s.id AND `status` > 0 AND count_recharge > -1)";
          $query = "SELECT
            s.id AS student_id,
            CONCAT(s.name, ' - ', s.crm_id) AS label
            FROM students AS s
            LEFT JOIN contracts AS c ON c.student_id = s.id
            LEFT JOIN term_student_user AS t ON t.student_id = s.id
            LEFT JOIN users AS u1 ON t.ec_id = u1.id
            LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id
            WHERE s.id > 0 AND c.debt_amount <= 0 $where GROUP BY s.id ORDER BY `name` ASC LIMIT 0, 10";
            $data = u::query($query);
          $code = APICode::SUCCESS;
        }
      }
      return $response->formatResponse($code, $data);
    }

    public function suggest(Request $request, $key, $branch_id) {
      $response = [
        'code' => 200,
        'students' => [],
        'message' => 'empty key'
      ];
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
            $where.= " AND t.ec_leader_id = $user_id";
          }
          if ($role_id == ROLE_EC) {
            $where.= " AND t.ec_id = $user_id";
          }
          $where.= $key1 ? " AND (s.stu_id LIKE '$key1%' AND " : " AND (s.stu_id LIKE '$key%' OR ";
          $where.= $key2 ? " s.name LIKE '$key2%' OR s.nick LIKE '$key2%')" : ')';

          $query = "SELECT
                    DISTINCT(s.id) AS student_id,
                    s.stu_id AS lms_id,
                    s.accounting_id,
                    s.name,
                    s.nick,
                    s.email AS stu_email,
                    s.phone AS home_phone,
                    s.gender,
                    s.type,
                    c.product_id,
                    c.id as contract_id,
                    c.type as contract_type,
                    COALESCE(s.sibling_id, 0) AS sibling,
                    s.school_grade,
                    s.date_of_birth,
                    CONCAT(s.name, ' - ', s.cms_id) AS label,
                    COALESCE(s.gud_name1, s.gud_name2) AS parent_name,
                    COALESCE(s.gud_mobile1, s.gud_mobile2) AS parent_mobile,
                    COALESCE(s.gud_email1, s.gud_email2) AS parent_email,
                    s.gud_name1,
                    s.gud_mobile1,
                    s.gud_name2,
                    s.gud_mobile2,
                    s.address,
                    s.school,
                    t.status AS student_status,
                    s.branch_id AS branch_id,
                    b.name AS branch_name,
                    b.brch_id AS branch_lms_id,
                    r.id AS region_id,
                    r.name AS region_name,
                    u0.id AS cm_id,
                    u0.full_name AS cm_name,
                    u1.id AS ec_id,
                    u1.full_name AS ec_name,
                    u2.id AS ec_leader_id,
                    u2.full_name AS ec_leader_name,
                    u3.id AS ceo_branch_id,
                    u3.full_name AS ceo_branch_name,
                    u4.id AS ceo_region_id,
                    u4.full_name AS ceo_region_name,
                    u5.id AS om_id,
                    u5.full_name AS om_name
                    FROM students AS s
                    LEFT JOIN term_student_user AS t ON t.student_id = s.id
                    LEFT JOIN branches AS b ON t.branch_id = b.id
                    LEFT JOIN regions AS r ON b.region_id = r.id
                    LEFT JOIN users AS u0 ON t.cm_id = u0.id
                    LEFT JOIN users AS u1 ON t.ec_id = u1.id
                    LEFT JOIN users AS u2 ON t.ec_leader_id = u2.id
                    LEFT JOIN users AS u3 ON t.ceo_branch_id = u3.id
                    LEFT JOIN users AS u4 ON t.ceo_region_id = u4.id
                    LEFT JOIN users AS u5 ON t.om_id = u5.id
                    LEFT JOIN contracts as c ON c.student_id = s.id
                    LEFT JOIN payment as p on p.contract_id = c.id
                    WHERE s.id > 0 
                    AND c.id > 0 
                    AND c.type IN (1,2,3,4,5,6) 
                    AND c.status IN (3,4,5,6) 
                    AND c.debt_amount = 0
                    AND c.student_id NOT IN (SELECT st.id 
                                              FROM students as st 
                                              LEFT JOIN contracts as ct ON ct.student_id = st.id 
                                              WHERE ct.type = 7)
                    AND c.id IN (
                      SELECT MAX(c.id)
                      FROM contracts AS c
                      LEFT JOIN students AS s ON c.`student_id` = s.`id`
                      WHERE
                      c.`branch_id` IN ($branch_id)
                      AND c.debt_amount = 0
                      AND c.`type` IN (1,3,4,5,6)
                      AND c.`status` IN (3,4,5,6,7)
                      GROUP BY c.student_id
                    ) $where ORDER BY `name` ASC LIMIT 0, 10";
          $students = u::query($query);
          $product_id = $students && is_array($students) && count($students) ? $students[0]->product_id : 0;
          $holidays = u::getPublicHolidays(0, $branch_id, $product_id);
          if ($students) {
            foreach ($students as $student) {
              $sub_query = "SELECT u.id, u.full_name, u1.id AS boss_id, u1.full_name AS boss_name
                            FROM users AS u
                            LEFT JOIN term_user_branch AS t ON u.id = t.user_id
                            LEFT JOIN users AS u1 ON u.superior_id = u1.hrm_id
                            WHERE t.role_id IN (4, 5) AND t.branch_id = '$student->branch_id' AND u1.id = '$student->ec_leader_id'";
              $student->ec_list = DB::select(DB::raw($sub_query));
              $student->holidays = $holidays;
            }
            $response = [
              'code' => 200,
              'students' => $students,
              'message' => 'Ok'
            ];
          }
        } else {
          $response = [
            'code' => 300,
            'students' => [],
            'message' => 'invalid token'
          ];
        }
      }
      return response()->json($response);
    }

    public function products($branch_id, $student_id, Request $request)
    {
      $code = APICode::PERMISSION_DENIED;
      $data = null;
      $response = new Response();
      $information = (object) [];
      $session = $request->users_data;
      if ($branch_id && $student_id && $session) {
        $code = APICode::SUCCESS;
        $id = (int)$branch_id;
        $user_id = $session->id;
        /*
        $query_old_backup = "SELECT
        p.prod_code AS product_cms_id,
        p.`name` AS product_name,
        p.id AS product_id,
        t.session AS tuition_fee_session,
        t.price AS tuition_fee_price,
        t.discount AS tuition_fee_discount,
        t.receivable AS tuition_fee_receivable,
        t.id AS tuition_fee_id,
        t.type AS tuition_fee_type,
        t.`name` AS tuition_fee_name,
        t.`number_of_months`
        FROM
          products AS p
          LEFT JOIN term_program_product AS h ON h.product_id = p.id
          LEFT JOIN programs AS r ON h.program_id = r.id AND r.branch_id = $id
          LEFT JOIN semesters AS s ON r.semester_id = s.sem_id AND r.branch_id = $id
          LEFT JOIN tuition_fee AS t ON t.product_id = p.id
        WHERE
          p.`status` > 0 AND t.`status` > 0 AND r.`status` > 0 AND h.`status` > 0
          AND (t.available_date <= CURDATE() AND t.expired_date >= CURDATE())
          AND s.end_date >= CURDATE()
          AND r.branch_id = $id AND (t.branch_id LIKE '$id,%' OR t.branch_id LIKE '%,$id,%' OR t.branch_id LIKE '%,$id' OR t.branch_id = '$id' )";
        */
          $query = "SELECT
          p.prod_code AS product_cms_id,
          p.`name` AS product_name,
          p.id AS product_id,
          t.session AS tuition_fee_session,
          t.price AS tuition_fee_price,
          t.discount AS tuition_fee_discount,
          t.receivable AS tuition_fee_receivable,
          t.id AS tuition_fee_id,
          t.type AS tuition_fee_type,
          t.`name` AS tuition_fee_name,
          t.`number_of_months`,
          t.price_min AS tuition_fee_price_min
        FROM
				  products AS p
				LEFT JOIN tuition_fee AS t ON t.product_id = p.id
				WHERE
				p.`status` > 0 AND t.`status` > 0
				AND (t.available_date <= CURDATE() AND t.expired_date >= CURDATE())
				AND (t.branch_id LIKE '$id,%' OR t.branch_id LIKE '%,$id,%' OR t.branch_id LIKE '%,$id' OR t.branch_id = '$id' )
				AND t.`id` NOT IN (SELECT GROUP_CONCAT(tuition_fee_id) FROM contracts WHERE student_id = $student_id GROUP BY tuition_fee_id HAVING COUNT(*) >3)";
        $basedata = u::query($query);
        if ($basedata) {
          $products = [];
          $products_ids = [];
          foreach ($basedata as $item) {
            if (!in_array($item->product_id, $products_ids)) {
              $products_ids[] = $item->product_id;
              $products['product_id'.$item->product_id] = [
                'product_id'=>$item->product_id,
                'product_name'=>$item->product_name,
                'product_cms_id'=>$item->product_cms_id
              ];
            }
          }
          if ($products) {
            foreach ($products as $i => $product) {
              $tuition_fees = [];
              $tuition_fees_ids = [];
              foreach ($basedata as $item) {
                if ($item->product_id == $product['product_id'] && !in_array($item->tuition_fee_id, $tuition_fees_ids)) {
                  $tuition_fees_ids[] = $item->tuition_fee_id;
                  $tuition_fees['tuition_fee_id' . $item->tuition_fee_id] = [
                    'tuition_fee_id' => $item->tuition_fee_id,
                    'tuition_fee_type' => $item->tuition_fee_type,
                    'tuition_fee_name' => $item->tuition_fee_name,
                    'tuition_fee_price' => $item->tuition_fee_price,
                    'tuition_fee_price_min' => $item->tuition_fee_price_min,
                    'tuition_fee_session' => $item->tuition_fee_session,
                    'tuition_fee_discount' => $item->tuition_fee_discount,
                    'tuition_fee_receivable' => $item->tuition_fee_receivable,
                    'number_of_months' => $item->number_of_months
                  ];
                }
              }
              if ($tuition_fees) {
                $products['product_id' . $product['product_id']]['tuition_fees'] = $tuition_fees;
              }
            }
          }
          $role_branch_ceo = ROLE_BRANCH_CEO;
          $role_region_ceo = ROLE_REGION_CEO;
          $student = u::first("SELECT
            s.id AS student_id,
            s.cms_id,
            s.crm_id,
            s.name,
            s.nick,
            s.email AS stu_email,
            s.phone AS home_phone,
            s.gender,
            s.type,
            COALESCE(s.sibling_id, 0) AS sibling,
            s.school_grade,
            s.date_of_birth,
            COALESCE(c.trial_done, 0) AS trail_done,
            CONCAT(s.name, ' - ', s.cms_id) AS label,
            COALESCE(s.gud_name1, s.gud_name2) AS parent_name,
            COALESCE(s.gud_mobile1, s.gud_mobile2) AS parent_mobile,
            COALESCE(s.gud_email1, s.gud_email2) AS parent_email,
            s.gud_name1,
            s.gud_mobile1,
            s.gud_name2,
            s.gud_mobile2,
            s.address,
            s.school,
            t.status AS student_status,
            s.branch_id AS branch_id,
            b.name AS branch_name,
            b.brch_id AS branch_cms_id,
            b.hrm_id AS branch_hrm_id,
            r.id AS region_id,
            r.name AS region_name,
            u0.id AS cm_id,
            u0.full_name AS cm_name,
            u1.id AS ec_id,
            u1.hrm_id AS ec_hrm_id,
            u1.full_name AS ec_name,
            u2.id AS ec_leader_id,
            u2.full_name AS ec_leader_name,
            u3.id AS om_id,
            u3.full_name AS om_name,(SELECT CONCAT(`code`,'*',school_name,'*',COALESCE(address,''),'*',COALESCE(personal_name,'')) FROM collaborators WHERE `code` =  s.ref_code) AS ref_info,
            (SELECT u4.id FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo' AND tu.status = 1 LIMIT 1) AS ceo_branch_id,
            (SELECT u4.full_name FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo' AND tu.status = 1 LIMIT 1) AS ceo_branch_name,
            (SELECT u5.id FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_ceo' AND tu.status = 1 LIMIT 1) AS ceo_region_id,
            (SELECT u5.full_name FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_ceo' AND tu.status = 1 LIMIT 1) AS ceo_region_name
            FROM students AS s
            LEFT JOIN term_student_user AS t ON t.student_id = s.id
            LEFT JOIN branches AS b ON t.branch_id = b.id
            LEFT JOIN regions AS r ON b.region_id = r.id
            LEFT JOIN users AS u0 ON t.cm_id = u0.id
            LEFT JOIN users AS u1 ON t.ec_id = u1.id
            LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id
            LEFT JOIN users AS u3 ON u0.superior_id = u3.hrm_id
            LEFT JOIN term_user_branch AS tu ON t.branch_id = tu.branch_id
            LEFT JOIN (
              SELECT ct.id AS id, ct.type AS `type`, ct.passed_trial AS trial_done
              FROM contracts AS ct
              LEFT JOIN students AS st ON ct.student_id = st.id
              WHERE ct.type = 0) AS c ON c.id = s.id
            WHERE s.id = $student_id LIMIT 0, 1");
          if ($products) {
            // $latest_contract_info = u::first("SELECT id, type, passed_trial, status, only_give_tuition_fee_transfer, start_date, end_date, count_recharge, program_label, enrolment_last_date, enrolment_last_date, COALESCE(enrolment_last_date, enrolment_last_date) latest_date, class_id, enrolment_start_date, enrolment_end_date, enrolment_schedule, DATE(updated_at) updated_at FROM contracts WHERE student_id = $student_id AND ( type = 0 OR (type > 0 AND debt_amount > 0 AND count_recharge = 0)) AND status >= 0 ORDER BY id DESC limit 0, 1");
            $latest_contract_info = u::first("SELECT 
                id, 
                type, 
                passed_trial, 
                `status`, 
                only_give_tuition_fee_transfer, 
                start_date, 
                end_date, 
                count_recharge, 
                program_label, 
                enrolment_last_date, 
                enrolment_last_date, 
                COALESCE(enrolment_last_date, enrolment_last_date) latest_date, 
                class_id, 
                enrolment_start_date, 
                enrolment_end_date, 
                enrolment_schedule, 
                DATE(updated_at) updated_at 
              FROM contracts 
              WHERE student_id = $student_id 
                AND count_recharge >= 0 
                AND status >= 0 
                ORDER BY id DESC limit 1");
            $latest_date = date('Y-m-d');
            if ($latest_contract_info && isset($latest_contract_info->latest_date)) {
              $latest_date = $latest_contract_info->latest_date;
            } elseif (isset($latest_contract_info->end_date) && isset($latest_contract_info->status) && $latest_contract_info->status > 0) {
              $latest_date = $latest_contract_info->end_date;
            }
            if (isset($latest_contract_info->updated_at) && isset($latest_contract_info->status) && in_array((int)$latest_contract_info->status, [0, 7, 8])) {
              $latest_date = $latest_contract_info->updated_at;
            }
            $m = $latest_date && $latest_date != '0000-00-00' ? new \Moment\Moment($latest_date) : date('Y-m-d');
            $latest_date = $latest_date && $latest_date != '0000-00-00' ? $m->format('Y-m-d') : date('Y-m-d');
            $relation_info = (object)[
              'latest_date' => $latest_date,
              'latest_contract' => $latest_contract_info
            ];
            // if ($student->sibling) {
            //   $td = new \Moment\Moment(date('Y-m-d'));
            //   $check_sibling = u::first("SELECT status, enrolment_withdraw_date FROM contracts WHERE student_id = $student->sibling ORDER BY id DESC LIMIT 0, 1");
            //   if ($check_sibling && isset($check_sibling->status)) {
            //     $ca = $td->from(strtotime($check_sibling->enrolment_withdraw_date));
            //     if ((int)$check_sibling->status == 7 && $ca->getDays() <= 0) {
            //       $student->sibling = '';
            //     }
            //   }
            // }
            $data = (object)[];
            $data->student = $student;
            $data->products = $products;
            $data->information = $relation_info ;
          }
        }
      }
      if($data){
        $validate = self::validateContract($student_id);
        $data->has_error = $validate->has_error;
        $data->message = $validate->message;
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();
      $post = $request->input();
      $user_data = $request->users_data;
      if ($post && $user_data) {
        $user_id = (int)$user_data->id;
        $student = isset($request->student) ? (object)$request->student : null;
        $contract = isset($request->contract) ? (object)$request->contract : null;
        $previous_contract = isset($request->contract) && isset($contract->latest_contract) ? (object)$contract->latest_contract : null;
        $shift = isset($contract->shift) ? $contract->shift : '';
        $note = isset($contract->note) ? $contract->note : '';
        $note = str_replace(["'","\r\n",'"'],"",$note);
        $ref_code = isset($contract->ref_code) ? $contract->ref_code : '';
        $createdAt = !empty($contract->back_date) ? $contract->back_date.date(' H:i:s') : date('Y-m-d H:i:s');
        $student_id = (int)$student->student_id;
        $info_available = 2;
        $only_give_tuition_fee_transfer = (int)$contract->receive;
        $expected_class = '';
        $contract_type = $only_give_tuition_fee_transfer ? 4 : 7;
        $ec_id = (int)$student->ec_id;
        $cm_id = (int)$student->cm_id;
        $om_id = (int)$student->om_id;
        $branch_id = (int)$student->branch_id;
        $region_id = (int)$student->region_id;
        $ec_leader_id = (int)$student->ec_leader_id;
        $ceo_branch_id = (int)$student->ceo_branch_id;
        $ceo_region_id = (int)$student->ceo_region_id;
        $product_id = (int)$contract->product;
        $program_label = (int)$contract->program;
        $tuition_fee_price = (int)str_replace('đ','',str_replace(',','',$contract->price));
        $after_discounted_fee = (int)$contract->discounted_amount;
        $price_must_charge = (int)$contract->must_charge_amount;
        $sibling_discount = (int)$contract->sibling;
        $discount_value = (int)$contract->point;
        $tuition_fee_id = (int)$contract->tuition_fee;
        $total_discount = (int)$contract->total_voucher_other;
        $total_sessions = (int)$contract->sessions;
        $real_sessions = 0;
        $payload_type = (int)$contract->payload;
        $passed_trial = 1;
        $receivable = (int)$contract->new_price_amount;
        $description = ada()->quote($contract->detail);
        // $bill_info = ada()->quote($contract->bill_info);
        $bill_info = ada()->quote(str_replace("\n","<br/>",$contract->detail));
        $start_date = $contract->start_date;
        $end_date = $contract->end_date;
        $status = (int)$student->type == 1 ? 5 : 1;
        $hash_key = md5("$student_id.$contract_type.$start_date.$end_date.$product_id.$program_label");
        $check = u::first("SELECT COUNT(id) AS existed FROM contracts WHERE hash_key = '$hash_key'");
        $check = (int)$check->existed;
        $continue = (int)$contract->continue;
        $program_label = "'$contract->program'";
        $contract_code = apax_ada_gen_contract_code($student->name, $student->ec_name, $student->branch_name);
        $coupon = trim(strtoupper(isset($contract->coupon) ? $contract->coupon: ''));
        $bonus_sessions = (int)$request->bonus_sessions;
        $bonus_amount = (int)$request->bonus_amount;
        if ($cm_id == 0) {
          // $note.=', chưa có CM';
        }
        if ($ec_id == 0) {
          // $note.=', chưa có EC';
        }
        if ($start_date == '0000-00-00' || !$start_date || $start_date == '') {
          $start_date = date('Y-m-d');
        }
        if ($end_date == '0000-00-00' || !$end_date || $end_date == '') {
          $m = new \Moment\Moment(strtotime($start_date));
          $classdays = [2];
          if ($total_sessions) {
            $holidays = u::getPublicHolidays(0, $branch_id, $product_id);
            $schedule = u::getRealSessions($total_sessions, $classdays, $holidays, $start_date);
            $end_date = $schedule->end_date;
          }
        }
        if ($only_give_tuition_fee_transfer) {
          $start_date = $contract->start_date;
          $end_date = $start_date;
        }
        $debt_amount = $price_must_charge;
        $reservable_sessions = 0;
        $reservable = 0;
        if ((int)$student->type && $contract->other == $contract->must_charge_amount) {
          $total_discount = $after_discounted_fee;
          $real_sessions = $total_sessions;
          $debt_amount = 0;
          $reservable_sessions = $real_sessions >= 24 ? floor($real_sessions / 4) : 0;
          if($reservable_sessions){
              $reservable = 1;
          }
        }
        $count_recharge = $previous_contract && isset($previous_contract->count_recharge) ? (int)$previous_contract->count_recharge + 1 : 1;

        $summary_sessions = $real_sessions;
        if ($debt_amount == 0) {
          $status = 5;
          $contract_type = 2;
          if (!in_array($status, [3,4,6]) && in_array($contract_type, [2,5,6])) {
            $contract_type = 8;
          }
          $real_sessions = 0;
          $summary_sessions = $total_sessions;
          $bonus_sessions = $total_sessions;
          $reservable_sessions = $real_sessions >= 24 ? floor($real_sessions / 4) : 0;
        }
        if ($status == 5 && $debt_amount > 0) {
            $status = 1;
        }
        if ($only_give_tuition_fee_transfer == 1) {
          $status = 2;
          $contract_type = 3;
          $product_id = (int)$contract->product;
        }
        if ($check == 0) {
          if ($previous_contract && (int)$student->type == 1) {
            u::query("UPDATE contracts SET enrolment_last_date = '$end_date' WHERE id = $previous_contract->id");
          }
          $action ="Recharges_Create";
          $insert_query = "INSERT INTO contracts
          (`code`,
          `type`,
          `payload`,
          `student_id`,
          `branch_id`,
          `ceo_branch_id`,
          `region_id`,
          `ceo_region_id`,
          `ec_id`,
          `ec_leader_id`,
          `cm_id`,
          `om_id`,
          `product_id`,
          `program_label`,
          `tuition_fee_id`,
          `receivable`,
          `must_charge`,
          `total_discount`,
          `debt_amount`,
          `description`,
          `bill_info`,
          `start_date`,
          `end_date`,
          `total_sessions`,
          `real_sessions`,
          `expected_class`,
          `passed_trial`,
          `status`,
          `created_at`,
          `creator_id`,
          `hash_key`,
          `updated_at`,
          `editor_id`,
          `only_give_tuition_fee_transfer`,
          `reservable`,
          `reservable_sessions`,
          `sibling_discount`,
          `discount_value`,
          `after_discounted_fee`,
          `info_available`,
          `tuition_fee_price`,
          `continue_class`,
          `count_recharge`,
          `note`,
          `coupon`,
          `ref_code`,
          `bonus_sessions`,
          `bonus_amount`,
          `summary_sessions`,
          `action`,
          `shift`)
          VALUES
          ('$contract_code',
          $contract_type,
          $payload_type,
          $student_id,
          $branch_id,
          $ceo_branch_id,
          $region_id,
          $ceo_region_id,
          $ec_id,
          $ec_leader_id,
          $cm_id,
          $om_id,
          $product_id,
          $program_label,
          $tuition_fee_id,
          $receivable,
          $price_must_charge,
          $total_discount,
          $debt_amount,
          '$description',
          '$bill_info',
          '$start_date',
          '$end_date',
          $total_sessions,
          $real_sessions,
          '$expected_class',
          $passed_trial,
          $status,
          '$createdAt',
          $user_id,
          '$hash_key',
          NOW(),
          $user_id,
          $only_give_tuition_fee_transfer,
          $reservable,
          $reservable_sessions,
          $sibling_discount,
          $discount_value,
          $after_discounted_fee,
          $info_available,
          $tuition_fee_price,
          $continue,
          $count_recharge,
          '$note',
          '$coupon',
          '$ref_code',
          '$bonus_sessions',
          '$bonus_amount',
          $summary_sessions,
          '$action',
          '$shift')";
          u::query($insert_query);
          $latest_contract = u::first("SELECT id, created_at, updated_at, hash_key FROM contracts WHERE hash_key = '$hash_key' ORDER BY id DESC LIMIT 1");
          $insert_log_contract_history_query = "INSERT INTO log_contracts_history
          (`contract_id`,
          `code`,
          `type`,
          `payload`,
          `student_id`,
          `branch_id`,
          `ceo_branch_id`,
          `region_id`,
          `ceo_region_id`,
          `ec_id`,
          `ec_leader_id`,
          `cm_id`,
          `om_id`,
          `product_id`,
          `program_label`,
          `tuition_fee_id`,
          `receivable`,
          `must_charge`,
          `total_discount`,
          `debt_amount`,
          `description`,
          `bill_info`,
          `start_date`,
          `end_date`,
          `total_sessions`,
          `real_sessions`,
          `expected_class`,
          `passed_trial`,
          `status`,
          `created_at`,
          `creator_id`,
          `hash_key`,
          `updated_at`,
          `editor_id`,
          `only_give_tuition_fee_transfer`,
          `reservable`,
          `sibling_discount`,
          `discount_value`,
          `after_discounted_fee`,
          `info_available`,
          `tuition_fee_price`,
          `continue_class`,
          `count_recharge`,
          `note`,
          `coupon`,
          `bonus_sessions`,
          `bonus_amount`,
          `summary_sessions`,
          `action`,
          `shift`)
          VALUES
          ($latest_contract->id,
          '$contract_code',
          $contract_type,
          $payload_type,
          $student_id,
          $branch_id,
          $ceo_branch_id,
          $region_id,
          $ceo_region_id,
          $ec_id,
          $ec_leader_id,
          $cm_id,
          $om_id,
          $product_id,
          $program_label,
          $tuition_fee_id,
          $receivable,
          $price_must_charge,
          $total_discount,
          $debt_amount,
          '$description',
          '$bill_info',
          '$start_date',
          '$end_date',
          $total_sessions,
          $real_sessions,
          '$expected_class',
          $passed_trial,
          $status,
          NOW(),
          $user_id,
          '$hash_key',
          NOW(),
          $user_id,
          $only_give_tuition_fee_transfer,
          $reservable,
          $sibling_discount,
          $discount_value,
          $after_discounted_fee,
          $info_available,
          $tuition_fee_price,
          $continue,
          $count_recharge,
          '$note',
          '$coupon',
          $bonus_sessions,
          $bonus_amount,
          $summary_sessions,
          '$action',
          '$shift')";
          u::query($insert_log_contract_history_query);
          if($contract->coupon_code){
            $coupon_info = u::first("SELECT * FROM coupons WHERE code='$contract->coupon_code'");
            if($coupon_info){
              if($coupon_info->type==1 && $coupon_info->quota==1){
                u::query("UPDATE coupons SET `status`=2, checked_date='".date('Y-m-d')."' WHERE code='$contract->coupon_code'");
              }
              u::query("INSERT INTO coupon_logs (coupon_id,contract_id,created_at,creator_id) VALUES 
                ('$coupon_info->id','".(int)$latest_contract->id."', '$createdAt',$user_id)");
            }
          }
          $latest_contract = u::first("SELECT id, created_at, updated_at, hash_key FROM contracts WHERE hash_key = '$hash_key' ORDER BY id DESC LIMIT 1");
          $contractController = new ContractsController();
          $contractController->createCyberContract($latest_contract->id, $user_id);
        }
        $data = $student;
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
      $code = APICode::PERMISSION_DENIED;
      $data = null;
      $response = new Response();
      $id = (int)$id;
      $session = $request->users_data;
      $role_id = $session->role_id;
      $branches = $session->branches_ids;
      $role_branch_ceo = ROLE_BRANCH_CEO;
      $role_region_ceo = ROLE_REGION_CEO;
      $relation_info = null;
      if ($id && $branches) {
        $code = APICode::SUCCESS;
        $data = (object) [];
        $student = u::first("SELECT s.id AS student_id,
        s.cms_id,
        s.crm_id,
        s.name,
        s.nick,
        s.gender,
        s.email,
        s.phone,
        s.type,
        s.school_grade,
        s.date_of_birth,
        COALESCE(s.gud_name1, s.gud_name2) AS parent_name,
        COALESCE(s.gud_mobile1, s.gud_mobile2) AS parent_mobile,
        COALESCE(s.gud_email1, s.gud_email2) AS parent_email,
        s.gud_name1 AS gud_name1,
        s.gud_mobile1 AS gud_mobile1,
        s.gud_name2 AS gud_name2,
        s.gud_mobile2 AS gud_mobile2,
        b.name AS branch_name,
        z.name AS zone_name,
        s.address,
        s.school
        FROM contracts AS c
          LEFT JOIN students AS s ON c.student_id = s.id
          LEFT JOIN branches AS b ON c.branch_id = b.id
          LEFT JOIN zones AS z ON b.zone_id = z.id
        WHERE c.student_id IN (SELECT student_id FROM contracts WHERE id = $id) LIMIT 0, 1");
        $contracts = u::query("SELECT
          c.*,
          CONCAT(u1.full_name, ' - ', u1.username) AS contract_ec_name,
          CONCAT(u3.full_name, ' - ', u3.username) AS contract_cm_name,
          IF(c.type IN (1,2,4,7,8) AND c.status IN (1,2,4,5) AND c.enrolment_start_date IS NULL, 1, 0) editable_program,
          IF(c.type <= 4 AND c.status > 0 AND c.status < 7 AND c.enrolment_start_date IS NULL, 1, 0) editable_start_date,
          (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE hrm_id = u1.superior_id LIMIT 0, 1) AS contract_ec_leader_name, 
          (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE hrm_id = u3.superior_id LIMIT 0, 1) AS contract_om_name, 
          (SELECT CONCAT(u.full_name, ' - ', u.username) FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WHERE t.role_id = $role_branch_ceo AND t.branch_id = c.branch_id LIMIT 0, 1) AS contract_ceo_branch_name,
          (SELECT CONCAT(u.full_name, ' - ', u.username) FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WHERE t.role_id = $role_region_ceo AND t.branch_id = c.branch_id LIMIT 0, 1) AS contract_ceo_region_name,
          r.name AS region_name,
          b.name As branch_name,
          prd.id AS product_id,
          prd.name AS product_name,
          c.status enrolment_status, 
          t.id AS tuition_fee_id,
          t.name AS tuition_fee_name,
          t.type AS tuition_fee_type,
          t.session AS tuition_fee_session,
          t.price AS tuition_fee_price,
          t.receivable AS tuition_fee_receivable
        FROM contracts AS c 
          LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
          LEFT JOIN products AS prd ON c.product_id = prd.id
          LEFT JOIN branches AS b ON c.branch_id = b.id
          LEFT JOIN users AS u1 ON u1.id = c.ec_id
          LEFT JOIN users AS u3 ON u3.id = c.cm_id
          LEFT JOIN regions AS r ON r.id = b.region_id
        WHERE c.student_id = $student->student_id AND c.type > 0 AND c.count_recharge > 0 AND c.branch_id IN ($branches) ORDER BY c.id DESC");
        $where_ecs = "t.role_id = ".ROLE_EC;
        $where_cms = "t.role_id = ".ROLE_CM;
        if ($role_id >= ROLE_BRANCH_CEO || $role_id == ROLE_EC_LEADER || $role_id == ROLE_OM) {
          $where_ecs = "(t.role_id = ".ROLE_EC." OR t.role_id = ".ROLE_EC_LEADER.")";
          $where_cms = "(t.role_id = ".ROLE_CM." OR t.role_id = ".ROLE_OM.")";
        }
        $cec = u::first("SELECT
            u1.`full_name` AS ec_name,
            u1.id AS ec_id
          FROM contracts AS c
            LEFT JOIN term_user_branch AS t ON c.branch_id = t.branch_id
            LEFT JOIN users AS u1 ON t.user_id = u1.id AND t.role_id = ".ROLE_EC."
          WHERE c.id = $id AND u1.id > 0 GROUP BY u1.id");
        $ccm = u::first("SELECT
          u1.`full_name` AS cm_name,
          u1.id AS cm_id
        FROM contracts AS c
          LEFT JOIN term_user_branch AS t ON c.branch_id = t.branch_id
          LEFT JOIN users AS u1 ON t.user_id = u1.id AND t.role_id = ".ROLE_CM."
        WHERE c.id = $id AND u1.id > 0 GROUP BY u1.id");
        $cms = [];
        $ecs = [];
        if ($cec) {
          $ecs = u::query("SELECT
              u.`full_name` AS ec_name,
              u.id AS ec_id
            FROM users AS u
              LEFT JOIN term_user_branch AS t ON u.id = t.user_id
            WHERE u.id > 0 AND $where_ecs AND (t.branch_id IN ($branches) OR u.id = ".$cec->ec_id.") GROUP BY u.id");
        }
        if ($ccm) {
          $cms = u::query("SELECT
              u.`full_name` AS cm_name,
              u.id AS cm_id
            FROM users AS u
              LEFT JOIN term_user_branch AS t ON u.id = t.user_id
            WHERE u.id > 0 AND $where_cms AND (t.branch_id IN ($branches) OR u.id = ".$ccm->cm_id.") GROUP BY u.id");
        }
        $programs = [];
        $contracts_list = [];
        if (count($contracts)) {
          $current_contract = $contracts[0];
          foreach($contracts as $contract) {
            $contract->discount_code = DiscountCode::where('code' , '=', $contract->coupon)->first();
            if ($contract->id == $id) {
              $current_contract = $contract;
            } else {
              $contracts_list[] = $contract;
            }
          }
          array_unshift($contracts_list, $current_contract);
          if (!$current_contract->enrolment_id && $current_contract->status < 7 && $current_contract->status > 0) {
            $contract_branch = $current_contract->branch_id;
            $product_id = $current_contract->product_id;
            if ($product_id) {
              // $latest_contract_info = u::first("SELECT id, type, passed_trial, status, only_give_tuition_fee_transfer, start_date, end_date, count_recharge, program_label, enrolment_expired_date, enrolment_last_date, COALESCE(enrolment_last_date, enrolment_last_date) latest_date, class_id, enrolment_start_date, enrolment_end_date, enrolment_schedule, DATE(updated_at) updated_at FROM contracts WHERE student_id = $current_contract->student_id AND ( type = 0 OR (type > 0 AND debt_amount > 0 AND count_recharge = 0)) AND count_recharge > 0 ORDER BY id DESC limit 1");
              $latest_contract_info = u::first("SELECT id, type, passed_trial, status, only_give_tuition_fee_transfer, start_date, end_date, count_recharge, program_label, enrolment_expired_date, enrolment_last_date, COALESCE(enrolment_last_date, enrolment_last_date) latest_date, class_id, enrolment_start_date, enrolment_end_date, enrolment_schedule, DATE(updated_at) updated_at FROM contracts WHERE student_id = $current_contract->student_id AND count_recharge > 0 ORDER BY id DESC limit 1");
              $latest_date = date('Y-m-d');
              if ($latest_contract_info && isset($latest_contract_info->latest_date)) {
                $latest_date = $latest_contract_info->latest_date;
              } elseif (isset($latest_contract_info->end_date) && isset($latest_contract_info->status) && $latest_contract_info->status > 0) {
                $latest_date = $latest_contract_info->end_date;
              }
              if (isset($latest_contract_info->updated_at) && isset($latest_contract_info->status) && in_array((int)$latest_contract_info->status, [0, 7, 8])) {
                $latest_date = $latest_contract_info->updated_at;
              }
              $m = $latest_date && $latest_date != '0000-00-00' ? new \Moment\Moment($latest_date) : date('Y-m-d');
              $latest_date = $latest_date && $latest_date != '0000-00-00' ? $m->format('Y-m-d') : date('Y-m-d');
              $relation_info = (object)[
                'latest_date' => $latest_date,
                'latest_contract' => $latest_contract_info ? $latest_contract_info : null
              ];
              $programs = u::query("SELECT x.name, x.id FROM (SELECT p.name, p.id, (SELECT COUNT(*) subs FROM programs WHERE parent_id = p.id) subs FROM programs p LEFT JOIN term_program_product t ON t.program_id = p.id LEFT JOIN semesters s ON p.semester_id = s.id WHERE p.status > 0 AND s.status > 0 AND t.status > 0 AND (CURRENT_DATE() BETWEEN s.start_date AND s.end_date) AND t.product_id = $product_id AND p.branch_id = $contract_branch ) x WHERE x.subs = 0 ORDER BY id");
            }
          }
        }
        $data->ecs = $ecs;
        $data->cms = $cms;
        $data->ccm = $ccm;
        $data->cec = $cec;
        $data->student = $student;
        $data->programs = $programs;
        $data->contracts = $contracts_list;
        $data->relation_info = $relation_info;
      }
      return $response->formatResponse($code, $data);
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
    public function update(Request $request, $ec_id, $id)
    {
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();
      if ($ec_id != 'undefined' && $id != 'undefined') {
        if ($ec_id && $id) {
          $check_ec_leader = DB::select(DB::raw("SELECT ec_id FROM contracts WHERE id = $id"));
          $check_ec_leader = is_array($check_ec_leader) ? $check_ec_leader[0] : $check_ec_leader;
          $checkecid = $check_ec_leader->ec_id;
          if ($checkecid != $ec_id) {
            $get_ec_leader_query = "SELECT u2.full_name FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $ec_id";
            DB::statement("UPDATE contracts SET ec_id = $ec_id, ec_leader_id = (SELECT u2.id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $ec_id) WHERE id = $id");
            $ec_leader = DB::select(DB::raw($get_ec_leader_query));
            if ($ec_leader) {
              $ec_leader = is_array($ec_leader) ? $ec_leader[0] : $ec_leader;
              $ec_leader_name = $ec_leader->full_name ? $ec_leader->full_name : '';
              $data = (object) [];
              $data->ec_leader_name = $ec_leader_name;
            }
          }
        }
      }
      return $response->formatResponse($code, $data);
    }

    public function updateCm(Request $request, $cm_id, $id)
    {
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();
      if ($cm_id != 'undefined' && $id != 'undefined') {
        if ($cm_id && $id) {
          DB::statement("UPDATE contracts SET cm_id = $cm_id WHERE id = $id");
        }
      }
      return $response->formatResponse($code, $data);
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

    public function remove(Request $request, $contract_id){
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $data = u::query("UPDATE contracts SET status = -2, type = -2 WHERE id = $contract_id");
      }
      return $response->formatResponse($code, $data);
    }

    public function getStudentForRecharge($id)
    {
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();
      if ((int)$id) {
        $query = "SELECT s1.*,
          b.name AS branch_name,
          r.name AS region_name,
          r.id as region_id,
          s2.name AS sibling_name,
          s1.sibling_id,
          u1.id as ec_id,
          CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
          CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
          CONCAT(u3.full_name, ' - ', u3.username) AS ec_leader_name,
          CONCAT(u4.full_name, ' - ', u4.username) AS om_name,
          CONCAT(u5.full_name, ' - ', u5.username) AS ceo_branch_name,
          CONCAT(u6.full_name, ' - ', u6.username) AS ceo_region_name,
          u2.id as cm_id,
          u3.id as ec_leader_id,
          u4.id as om_id,
          u5.id as ceo_branch_id,
          u6.id as ceo_region_id,
          sg.name AS school_grade_name,
          sg.description AS school_grade_description,
          cl.cls_name AS class_name,
          pr.name AS product_name,
          pg.name As program_name,
          pv.name AS province_name,
          d.name AS district_name,
          c.enrolment_start_date AS class_start_date,
          c.enrolment_end_date AS class_end_date,
          c.enrolment_last_date as enrolment_last_date,
          c.enrolment_end_date as contract_end_date,
          c.program_label, 
          c.left_sessions
        FROM students AS s1
          LEFT JOIN students AS s2 ON s1.sibling_id = s2.id
          LEFT JOIN term_student_user AS t1 ON t1.student_id = s1.id
          LEFT JOIN users AS u1 ON t1.ec_id = u1.id
          LEFT JOIN users AS u2 ON t1.cm_id = u2.id
          LEFT JOIN users AS u3 ON t1.ec_leader_id = u3.id
          LEFT JOIN users AS u4 ON t1.om_id = u4.id
          LEFT JOIN users AS u5 ON t1.ceo_branch_id = u5.id
          LEFT JOIN users AS u6 ON t1.ceo_region_id = u6.id
          LEFT JOIN contracts AS c ON c.student_id = s1.id
          LEFT JOIN districts AS d ON d.id = s1.district_id
          LEFT JOIN provinces AS pv ON pv.id = s1.province_id
          LEFT JOIN products AS pr ON c.product_id = pr.id
          LEFT JOIN classes AS cl ON c.class_id = cl.id
          LEFT JOIN school_grades AS sg ON sg.id = s1.school_grade
          LEFT JOIN branches AS b ON t1.branch_id = b.id
          LEFT JOIN regions AS r ON b.region_id = r.id
        WHERE s1.id = $id";
        $student = u::first($query);
        $rs = $student->enrolment_last_date;
        $rs1 = $student->contract_end_date;
        if($rs){
          $student->the_last_date = $rs;
        } else {
          $student->the_last_date = $rs1;
        }

        if ($student) {
          $data = (object)[];
          $get_ranks_query = "SELECT t.comment, t.rating_date,
                                CONCAT(u.full_name, ' - ', u.username) AS rated_user,
                                r.name AS rank_name, r.description AS rank_description
                              FROM term_student_rank AS t
                                LEFT JOIN students AS s ON t.student_id = s.id
                                LEFT JOIN ranks AS r ON t.rank_id = r.id
                                LEFT JOIN users AS u ON t.creator_id = u.id
                              WHERE s.id = $id";
          $ranks = DB::select(DB::raw($get_ranks_query));
          $get_contracts_query = "SELECT c.created_at, c.code, c.type, c.start_date, c.end_date, c.description, c.payload,
                                c.total_sessions, c.left_sessions, c.expected_class, c.bill_info, c.receivable, c.must_charge,
                                CONCAT(u.full_name, ' - ', u.username) AS creator, pr.name AS product_name, pg.name AS program_name,
                                t.name AS tuition_fee_name, t.price AS tuition_fee_price, b.name AS branch_name, c.total_discount
                              FROM contracts AS c
                                LEFT JOIN students AS s ON c.student_id = s.id
                                LEFT JOIN users AS u ON c.creator_id = u.id
                                LEFT JOIN products AS pr ON c.product_id = pr.id
                                LEFT JOIN programs AS pg ON c.program_id = pg.id
                                LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
                                LEFT JOIN branches AS b ON c.branch_id = b.id
                              WHERE s.id = $id and c.type in (1,2,3,4) and c.status in (5,6,7,8)";
          $contracts = DB::select(DB::raw($get_contracts_query));
          $get_enrolments_query = "SELECT c.enrolment_updated_at, c.enrolment_start_date, c.enrolment_end_date, c.description, c.payload,
                                c.total_sessions, c.left_sessions, c.expected_class, c.bill_info, c.receivable, c.must_charge,
                                CONCAT(u.full_name, ' - ', u.username) AS creator, pr.name AS product_name, pg.name AS program_name,
                                t.name AS tuition_fee_name, t.price AS tuition_fee_price, b.name AS branch_name, c.total_discount
                              FROM contracts AS c
                                LEFT JOIN students AS s ON c.student_id = s.id
                                LEFT JOIN users AS u ON c.creator_id = u.id
                                LEFT JOIN products AS pr ON c.product_id = pr.id
                                LEFT JOIN programs AS pg ON c.program_id = pg.id
                                LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
                                LEFT JOIN branches AS b ON c.branch_id = b.id
                              WHERE s.id = $id";
          $enrolments = DB::select(DB::raw($get_enrolments_query));
          $get_tuition_transfer_query = "SELECT t.type, t.created_at, t.transfer_date, t.status, t.note,
                                t.approved_at, t.comment, t.amount_transferred, t.amount_received, t.session_transferred, t.session_received,
                                CONCAT(s1.name, ' - ', s1.crm_id) AS from_student,
                                CONCAT(s2.name, ' - ', s2.crm_id) AS to_student,
                                CONCAT(u1.full_name, ' - ', u1.username) AS created_user,
                                CONCAT(u2.full_name, ' - ', u2.username) AS approved_user,
                                c1.code AS from_contract, c2.code AS to_contract,
                                cl1.cls_name AS from_class, cl2.cls_name AS to_class,
                                b1.name AS from_branch, b2.name AS to_branch,
                                pr1.name AS from_product, pr2.name AS to_product,
                                pg1.name AS from_program, pg2.name AS to_program
                              FROM tuition_transfer AS t
                                LEFT JOIN students AS s1 ON t.from_student_id = s1.id
                                LEFT JOIN students AS s2 ON t.to_student_id = s2.id
                                LEFT JOIN users AS u1 ON t.creator_id = u1.id
                                LEFT JOIN users AS u2 ON t.approver_id = u2.id
                                LEFT JOIN contracts AS c1 ON t.from_contract_id = c1.id
                                LEFT JOIN contracts AS c2 ON t.to_contract_id = c2.id
                                LEFT JOIN branches AS b1 ON t.from_branch_id = c1.id
                                LEFT JOIN branches AS b2 ON t.to_branch_id = c2.id
                                LEFT JOIN classes AS cl1 ON t.from_class_id = cl1.id
                                LEFT JOIN classes AS cl2 ON t.to_class_id = cl2.id
                                LEFT JOIN products AS pr1 ON t.from_product_id = pr1.id
                                LEFT JOIN products AS pr2 ON t.to_product_id = pr2.id
                                LEFT JOIN programs AS pg1 ON t.from_program_id = pg1.id
                                LEFT JOIN programs AS pg2 ON t.to_program_id = pg2.id
                              WHERE s1.id = $id OR s2.id = $id";
          $tuition_transfer = DB::select(DB::raw($get_tuition_transfer_query));
          $get_payments_query = "SELECT p.method, p.payload, p.amount, p.debt, p.created_at, p.count,
                                c.receivable, c.must_charge, c.total_discount, CONCAT(u.full_name, ' - ', u.username) AS created_user
                              FROM payment AS p
                                LEFT JOIN contracts AS c ON p.contract_id = c.id
                                LEFT JOIN users AS u ON p.creator_id = u.id
                                LEFT JOIN students AS s ON c.student_id = s.id
                              WHERE s.id = $id";
          $payments = DB::select(DB::raw($get_payments_query));
          $get_recerves_query = "SELECT r.type, r.session, r.start_date, r.end_date, r.status,
                                r.comment, r.is_reserved, r.attached_file, r.created_at, r.approved_at,
                                CONCAT(u1.full_name, ' - ', u1.username) AS created_user,
                                CONCAT(u2.full_name, ' - ', u2.username) AS approved_user
                              FROM reserves AS r
                                LEFT JOIN users AS u1 ON r.creator_id = u1.id
                                LEFT JOIN users AS u2 ON r.approver_id = u2.id
                                LEFT JOIN students AS s ON r.student_id = s.id
                              WHERE s.id = $id";
          $recerves = DB::select(DB::raw($get_recerves_query));
          $get_pendings_query = "SELECT p.type, p.session, p.start_date, p.end_date, p.status,
                                p.comment, p.note, p.created_at, p.approved_at, r.description, r.type AS reason_type,
                                CONCAT(u1.full_name, ' - ', u1.username) AS created_user,
                                CONCAT(u2.full_name, ' - ', u2.username) AS approved_user
                              FROM pendings AS p
                                LEFT JOIN users AS u1 ON p.creator_id = u1.id
                                LEFT JOIN users AS u2 ON p.approver_id = u2.id
                                LEFT JOIN reasons AS r ON p.reason_id = r.id
                                LEFT JOIN students AS s ON p.student_id = s.id
                              WHERE s.id = $id";
          $pendings = DB::select(DB::raw($get_pendings_query));
          $holiday_query = "SELECT
                              h.id AS holiday_id,
                              h.start_date,
                              h.end_date
                            FROM
                              public_holiday AS h
                              LEFT JOIN branches AS b ON b.zone_id = h.zone_id
                              LEFT JOIN students AS s ON s.branch_id = b.id
                            WHERE
                              s.id = $id";
          $holidays = DB::select(DB::raw($holiday_query));
          // $query_sessions = "";
          // $sessions = DB::select(DB::raw($query_sessions));

          $information = $student;
          if ($information->avatar) {
            $avatar = $information->avatar;

            $link_path_avatar = AVATAR_LINK.$avatar;
            $real_path_avatar = AVATAR.DS.str_replace('/', DS, $link_path_avatar);
            $information->avatar = file_exists($real_path_avatar) ? $link_path_avatar : AVATAR_LINK.'noavatar.png';

            $district_query = "SELECT districts.*, provinces.id as province_id from districts
                                    left join provinces on districts.province_id = provinces.id
                                    where provinces.id = $information->province_id";

            $districts = DB::select(DB::raw($district_query));


          }
          $data->information = $information;
          $data->ranks = $ranks;
          $data->district = $districts;
          $data->holidays = $holidays;
          $data->contracts = $contracts;
          $data->tuition_transfer = $tuition_transfer;
          $data->payments = $payments;
          $data->recerves = $recerves;
          $data->pendings = $pendings;
        }
      }
      return $response->formatResponse($code, $data);
    }

    public function getLatestEnddate($student_id, $day)
    {
      $query = "  SELECT
          max(c.id) AS contract_id,
          c.class_id,
          COALESCE (c.enrolment_last_date, c.end_date) AS start_date,
          DATE_ADD(
            DATE(
              COALESCE (c.enrolment_last_date, c.enrolment_end_date)
            ),
            INTERVAL '$day' DAY
          ) AS last_date,
          s. NAME AS student_name
        FROM
          students AS s
        LEFT JOIN contracts AS c ON c.student_id = s.id
        WHERE
          s.id = '$student_id'";
      $rs = DB::select(DB::raw($query));
      return response()->json($rs);
    }

    public function getRechargesByStudent($id)
    {
      //$query = "SELECT c.* FROM contracts as c where c.id = $id";
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();
      if ((int)$id) {
        $data = (object)[];
        $code = APICode::SUCCESS;

        $contracts = DB::select(DB::raw("SELECT
          c.id AS contract_id,
          c.code AS code,
          c.payload AS contract_payload,
          c.program_label, 
          c.type AS contract_type,
          c.status AS contract_status,
          c.bill_info AS contract_bill_info,
          c.start_date AS contract_start_date,
          c.end_date AS contract_end_date,
          c.receivable AS contract_receivable,
          c.must_charge AS contract_must_charge,
          c.total_discount AS contract_total_discount,
          c.description AS contract_description,
          u1.id AS contract_ec_id,
          u2.id AS contract_ec_leader_id,
          u3.id AS contract_cm_id,
          u1.full_name AS contract_ec_name,
          u2.full_name AS contract_ec_leader_name,
          u3.full_name AS contract_cm_name,
          s.id AS student_id,
          s.stu_id AS student_lms_id,
          s.accounting_id AS student_accounting_id,
          s.name AS student_name,
          s.nick AS student_nick,
          s.gender AS student_gender,
          s.email AS student_email,
          s.phone AS student_phone,
          s.type AS student_type,
          s.school_grade AS student_school_grade,
          s.date_of_birth AS student_birthday,
          COALESCE(s.gud_name1, s.gud_name2) AS student_parent_name,
          COALESCE(s.gud_mobile1, s.gud_mobile2) AS student_parent_mobile,
          COALESCE(s.gud_email1, s.gud_email2) AS student_parent_email,
          s.gud_name1 AS student_mom_name,
          s.gud_mobile1 AS student_mom_mobile,
          s.gud_name2 AS student_dad_mobile,
          s.gud_mobile2 AS student_dad_mobile,
          s.address AS student_address,
          s.school AS student_school,
          r.name AS region_name,
          b.name As branch_name,
          prd.id AS product_id,
          prd.name AS product_name,
          c.expected_class AS expected_class,
          c.left_sessions AS left_sessions,
          t.id AS tuition_fee_id,
          t.name AS tuition_fee_name,
          t.type AS tuition_fee_type,
          t.session AS tuition_fee_session,
          t.price AS tuition_fee_price,
          t.receivable AS tuition_fee_receivable,
          p.amount AS payment_amount
          FROM
            contracts AS c
            LEFT JOIN students AS s ON c.student_id = s.id
            LEFT JOIN branches AS b ON c.branch_id = b.id
            LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
            LEFT JOIN payment AS p ON p.contract_id = c.id
            LEFT JOIN products AS prd ON c.product_id = prd.id
            LEFT JOIN users AS u1 ON u1.id = c.ec_id
            LEFT JOIN users AS u2 ON u2.id = c.ec_leader_id
            LEFT JOIN users AS u3 ON u3.id = c.cm_id
            LEFT JOIN regions AS r ON r.id = b.region_id
          WHERE c.student_id IN (SELECT max(student_id) FROM contracts WHERE id = $id) AND c.id != $id"));
      }
      $data->contracts = $contracts;
      // return response()->json([$code, $contracts]);
      return $response->formatResponse($code, $data);
    }

    public function getEnrolmentHistory($id)
    {
      $query = "SELECT c.*, if(s.type = 0, 'Thường', 'VIP') as student_type,
          CASE c.type
            WHEN 0 THEN 'Withdraw'
            WHEN 1 THEN 'Active'
            WHEN 2 THEN 'Transfering'
              END AS enrolment_type,
          ec.full_name as ec_name,
          ec.username as ec_username,
          cm.full_name as cm_name,
          cm.username as cm_username,
          br.name as branch_name,
          cl.cls_name as class_name,
          pr.name as program_name,
          st.name as semester_name,
          f.name as tuition_fee_name,
          p.name as product_name
        FROM contracts AS c
          LEFT JOIN branches AS br ON br.id = s.branch_id
          LEFT JOIN term_student_user AS t ON t.student_id = s.id
          LEFT JOIN users as ec on ec.id = t.ec_id
          LEFT JOIN users as cm on cm.id = t.cm_id
          LEFT JOIN classes as cl on cl.id = c.class_id
          LEFT JOIN programs as pr on pr.id = cl.program_id
          LEFT JOIN tuition_fee as f on f.id = c.tuition_fee_id
          LEFT JOIN products as p on p.id = c.product_id
          LEFT JOIN semesters as st on st.id = pr.semester_id
        WHERE c.student_id = $id GROUP BY c.id ORDER BY c.id DESC";
      $enrolments = DB::select(DB::raw($query));
      return response()->json($enrolments);
    }
    private static function validateContract($student_id){
      $has_error=0;
      $message= '';
      $data = u::first("SELECT * FROM students WHERE id = $student_id");

      if($data){
          switch ($data->waiting_status) {
          case 1:
            $has_error = 1;
            $message ="Học sinh đang chờ duyệt chuyển phí";
            break;
          case 2:
            $has_error = 1;
            $message ="Học sinh đang chờ duyệt nhận phí";
            break;
          case 3:
            $has_error = 1;
            $message ="Học sinh đang chờ duyệt chuyển trung tâm";
            break;
          // case 4:
          //   $has_error = 1;
          //   $message ="Học sinh đang chờ duyệt bảo lưu";
          //   break;
          case 5:
            $has_error = 1;
            $message ="Học sinh đang chờ duyệt chuyển lớp";
            break;
          default:
            $has_error = 0;
            $message ="";
        }
      }
      return (Object)[
        'has_error' => $has_error,
        'message' => $message,
      ];
    }
}
