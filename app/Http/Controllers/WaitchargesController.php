<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Contracts;
use App\Models\Payment;
use App\Models\APICode;
use App\Models\Response;

class WaitchargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = (Object)[];
        $code = APICode::SUCCESS;
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
      date_default_timezone_set("Asia/Bangkok");
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $uid = $session->id;
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $post = $request->input();
        
        $data->done = true;
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request, $id) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $data = u::first("SELECT c.id AS contract_id, c.code, c.type, c.receivable, c.must_charge, c.description, c.payload, c.status, 
        c.bill_info, c.start_date, c.end_date, c.total_sessions, c.real_sessions, c.created_at,
        s.name AS student_name, s.accounting_id AS student_accounting_id, s.phone AS student_phone, COALESCE(s.email, s.gud_email1) AS student_email,
        s.id AS student_id, s.stu_id AS student_lms_id, s.crm_id AS student_crm_id,
        s.stu_id,
        s.accounting_id, 
        CONCAT(s.name, ' - ', COALESCE(s.crm_id, '')) AS label,
        COALESCE(p.count, 0) + 1 AS charge_time,
        COALESCE(s.gud_name1, s.gud_name2) AS parent_name,
        COALESCE(s.gud_mobile1, s.gud_mobile2) AS parent_mobile,
        COALESCE(s.gud_email1, s.gud_email2) AS parent_email,
        COALESCE(c.total_discount, 0) AS total_discount,
        COALESCE(c.debt_amount, 0) AS debt_amount,
        COALESCE(c.total_charged, 0) AS total_charged,
        CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
        CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
        CONCAT(u3.full_name, ' - ', u3.username) AS ec_leader_name,
        CONCAT(u4.full_name, ' - ', u4.username) AS creator_name,
        COALESCE((c.tuition_fee_price - c.must_charge), 0) AS final_discount,
        tf.name AS tuition_fee_name, tf.price AS tuition_fee_price, p.note,
        b.name AS branch_name, pr.name AS product_name, pg.name AS program_name
      FROM contracts AS c
        LEFT JOIN students AS s ON c.student_id = s.id
        LEFT JOIN users AS u1 ON c.ec_id = u1.id
        LEFT JOIN users AS u2 ON c.cm_id = u2.id
        LEFT JOIN users AS u3 ON c.ec_leader_id = u3.id
        LEFT JOIN users AS u4 ON c.creator_id = u4.id
        LEFT JOIN branches AS b ON c.branch_id = b.id
        LEFT JOIN products AS pr ON c.product_id = pr.id
        LEFT JOIN programs AS pg ON c.program_id = pg.id
        LEFT JOIN tuition_fee AS tf ON c.tuition_fee_id = tf.id
        LEFT JOIN (SELECT payment.*
          FROM payment
          WHERE id IN (SELECT MAX(id) FROM payment WHERE contract_id > 0 GROUP BY contract_id)
          AND debt > 0) AS p ON p.contract_id = c.id
      WHERE c.id = $id");
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, $pagination, $search, $sort) {
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
        $data->pagination = apax_get_pagination($pagination, $list->total->a);
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Build select for query by parameters from request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function select() {
      return "SELECT
        c.*,
        s.name AS student_name,
        s.crm_id AS student_crm_id,
        s.stu_id AS student_lms_id,
        s.accounting_id AS student_accounting_id,
        pr.name AS product_name,
        t.name AS tuition_fee_name,
        u.username AS ec_name";
    }

    /**
     * Build base query by parameters from request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function query() {
      return "FROM contracts AS c 
        LEFT JOIN products AS pr ON c.product_id = pr.id
        LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
        LEFT JOIN users AS u ON c.ec_id = u.id
        LEFT JOIN students AS s ON c.student_id = s.id
      WHERE c.id > 0";
    }

    /**
     * Build select for query by parameters from request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function where($request) {
      $search = json_decode($request->search);
      $session = $request->users_data;
      $role_id = $session->role_id;
      $branches = $search->branch ? (int)$search->branch : $session->branches_ids;
      $where = "AND c.branch_id IN ($branches) AND c.debt_amount > 0 AND c.status IN (1,2,4,6)";
      if ($search->product != '') {
        $where.= " AND c.product_id = ".(int)$search->product;
      }
      if ($search->tuition_fee != '') {
        $where.= " AND c.tuition_fee_id = ".(int)$search->tuition_fee;
      }
      if ($search->keyword != '') {
        $where.= " AND (s.crm_id LIKE '$search->keyword%' OR s.stu_id LIKE '$search->keyword%' OR s.accounting_id LIKE '$search->keyword%' OR s.name LIKE '%$search->keyword%')";
      }
      return $where;
    }

    /**
     * Build base query by parameters from request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function data($request) {
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
      $total = "SELECT COUNT(c.id) AS a";
      $select = $this->select();
      $query = $this->query();
      $where = $this->where($request);
      $data = u::query("$select $query $where $order $limit");
      $total = u::first("$total $query $where");
      $result = (Object)['data' => $data, 'total' => $total];
      return $result;
    }

    
}
