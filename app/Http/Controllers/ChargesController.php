<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Charge;
use App\Models\Contracts;
use App\Models\CyberAPI;
use App\Models\Response;
use App\Models\Sms;
use App\Models\TuitionFee;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
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
     * Tìm contract để charge
     * @param $contractId
     * @param $tuitionFeeIds
     * @return array|mixed|null
     */
    private function getContractForCharge($contractId, $tuitionFeeIds)
    {
        $ceo_branch_role_id = ROLE_BRANCH_CEO;
        $query = "SELECT c.*,
          b.region_id, b.zone_id,
          (SELECT current_hashkey FROM log_contracts_history WHERE contract_id = c.id AND hash_key = c.hash_key ORDER BY id DESC LIMIT 1) log_previous_hashkey,
          (SELECT version_no FROM log_contracts_history WHERE contract_id = c.id AND hash_key = c.hash_key ORDER BY id DESC LIMIT 1) version_no,
          (SELECT u1.id FROM users u1 LEFT JOIN users u2 ON u2.superior_id = u1.hrm_id INNER JOIN `term_user_branch` t ON t.user_id = u1.id WHERE u2.id = c.ec_id AND t.status = 1) real_ec_leader_id,
          (SELECT superior_id FROM users WHERE id = c.ec_id) real_team_leader_id,
          (SELECT u.id FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WHERE t.branch_id = c.branch_id AND t.role_id = $ceo_branch_role_id AND t.status > 0) real_ceo_branch_id
        FROM
          contracts c
          LEFT JOIN branches b ON c.branch_id = b.id  
        WHERE
        c.id = $contractId";
        if (!empty($tuitionFeeIds)) {
            $str = implode(',', $tuitionFeeIds);
            $query .= " AND c.tuition_fee_id in ($str)";
        }
        return u::first($query);
    }

    private function getContractForChargeNew($contractId, $tuitionFeeIds)
    {
        $query = "SELECT c.*,
          b.region_id, b.zone_id
        FROM
          contracts c
          LEFT JOIN branches b ON c.branch_id = b.id  
        WHERE
        c.id = $contractId";
        if (!empty($tuitionFeeIds)) {
            $str = implode(',', $tuitionFeeIds);
            $query .= " AND c.tuition_fee_id in ($str)";
        }
        return u::first($query);
    }

    /**
     * @param $contractId
     * @param $accountingId
     * @param $method phương thức thanh toán
     * @param $payload
     * @param $mustCharge
     * @param $amount
     * @param $total
     * @param $debt
     * @param $hash
     * @param $count
     * @param $type
     * @param $note
     * @param $chargeDate
     * @param $creatorId
     * @param $isEdit
     */
    private function upsertPayment($contractId, $accountingId, $method, $payload, $mustCharge, $amount, $total, $debt,
                                   $hash, $count, $type, $note, $chargeDate, $creatorId, $isEdit)
    {
        $model = new Charge();
        if($isEdit){
            $model->updatePayment($contractId, $accountingId, $amount, $total, $debt, $note, $chargeDate, $creatorId);
        }else{
           $model->insertPayment($contractId, $accountingId, $method, $payload, $mustCharge, $amount, $total, $debt, $hash,
               $count, $type, $note, $chargeDate,  $creatorId);
        }
        return u::first("SELECT * FROM payment WHERE accounting_id = '$accountingId'");
    }

    /**
     * Lấy ra số buối bảo lưu tối đa cho gói phí của học sinh
     * @param $contract
     * @param $isDebt
     * @return int
     */
    private function getMaxNumberOfReservesSessions($contract, $isDebt)
    {
        if (!$contract || $isDebt) {
            return 0;
        }
        $tuitionFeeId = (int)$contract->tuition_fee_id;
        if ($tuitionFeeId === 0) {
            return 0;
        }
        $tuitionFee = u::first("select number_of_months from tuition_fee where id = $tuitionFeeId");
        if (!$tuitionFee) {
            return 0;
        }
        $numberOfMonths = (int)$tuitionFee->number_of_months ?: 0;
        $reserve = $numberOfMonths >= 6  ? ($numberOfMonths < 12 ? 4 :8) : 0;
        return $reserve > 0 ? $reserve : 0;

    }

    private function getClassdaysByContract($contract)
    {
        if (empty($contract) || !isset($contract->enrolment_schedule)) {
            return [];
        }

        $classdays = $contract->enrolment_schedule;
        return array_map('intval', explode(',', "$classdays"));

    }

    private function getReserveDatesByContractId($contractId)
    {
        if (empty($contractId)) {
            return null;
        }
        $reserveDates = u::query("SELECT start_date, end_date FROM reserves WHERE contract_id = $contractId");
        return $reserveDates ?: [];
    }

    private function calcEnrolmentLastDateByContract($contract, $newRealSessions, $paymentDate)
    {
        $enrolmentStartDate = $contract->enrolment_start_date;
        $enrolmentLastDate = $contract->enrolment_last_date;
        $currentRealSessions = $contract->real_sessions;

        $isStudying = !empty($enrolmentStartDate);
        if (!$isStudying) {
            return null;
        }
        $contractId = $contract->id;
//         $isRecharge = (int)$contract->count_rechage > 0 || (int)$contract->type === 7;
        $classdays = self::getClassdaysByContract($contract);
        $class_id = $contract->class_id;

        $holidays = u::getPublicHolidays($class_id, 0, $contract->product_id);
        $holidays = array_merge($holidays, $this->getReserveDatesByContractId($contractId));
        if ($paymentDate < $enrolmentLastDate) {
            $start_date = $enrolmentStartDate;
            $calcSessions = $newRealSessions;
        } else {
            $calcSessions = $newRealSessions - $currentRealSessions;
            $start_date = $paymentDate;
            $calcSessions = u::checkInHolydays($start_date, $holidays) ? (int)$calcSessions + 1 : (int)$calcSessions;
        }
        $newinfo = u::getRealSessions($calcSessions, $classdays, $holidays, $start_date);
        return $newinfo->end_date;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if (!$session = $request->users_data) {
            return $response->formatResponse($code, $data);
        }
        $post = $request->input();
        $contract_id = (int)$post['contract_id'];
        $tuition_fee_ids = isset($post['tuition_fee_ids']) ? $post['tuition_fee_ids'] ?: [] : [];
        $contract_info = self::getContractForChargeNew($contract_id, $tuition_fee_ids);
        if (!$contract_info) {
            // Không tìm thấy contract để payment
            return $response->formatResponse($code, $data);
        }

        $uid = $session->id;
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $post = $request->input();
        $save = $post['update'];
        $must_charge = (int)$post['must_charge'];
        $amount = (int)$save['charge_amount'];
        $total = (int)$save['total_charged'];
        $count = (int)$post['charge_time'];
        $debt = (int)$save['debt_amount'];
        $payload = (int)$post['payload'];
        $method = (int)$save['method'];
        $date = $save['charge_date'];
        $note = $save['note'];
        $oldAmount = isset($post['old_amount']) ? $post['old_amount'] : 0;
        $accounting_id = isset($save['payment_code']) ? $save['payment_code'] : null;
        $isEdit = $request->is_edit == 1;
        if ($method == 2 && (int)$note) {
            $bank = u::first("SELECT CONCAT(name, '(', alias, ')') label FROM banks WHERE id = $note");
            $note = $bank->label;
        }
        $type = 1;
        $timestamp = time();
        $hash = md5("$must_charge$total$amount$debt$count$method$timestamp");
        $payment_info = self::upsertPayment($contract_id, $accounting_id, $method, $payload, $must_charge, $amount, $total, $debt,
            $hash, $count, $type, $note, $date, $uid, $isEdit);

        $payment_id = $payment_info->id;
        $contract_status = $debt > 0 ? ($contract_info->status == 6 ? 6 : 2) : ($contract_info->status == 6 ? 6 : 3);

//        $ra = (int)$contract_info->receivable;
        $mc = (int)$contract_info->must_charge;
//        $pt = (int)$contract_info->passed_trial;
        $ts = (int)$contract_info->total_sessions;
//        $tp = (int)$contract_info->tuition_fee_price;
//         giá của một buổi học
        $sp = (int)$ts !== 0 ? $mc / $ts : 0;
        $new_real_sessions = (int)$sp !== 0 ? round($total / $sp) : 0;
        $ok_real_sessions = $ts < $new_real_sessions ? (int)$ts : (int)$new_real_sessions;
        $new_type = $contract_info->type == 4 ? 3 : $contract_info->type;
        $isRecharge = $contract_info->count_recharge > 0 || $contract_info->type == 7;
        $new_type = $isRecharge ? ($debt <= 0 ? 2 : 7) : $new_type;
        $real_sessions = $ok_real_sessions - (($contract_info->relation_contract_id) ? (int)$contract_info->relation_left_sessions : 0);
        $reservable_sessions = self::getMaxNumberOfReservesSessions($contract_info, $debt > 0);
        $reservable = $reservable_sessions > $contract_info->reserved_sessions ? 1 : 0;
        $enrolment_last_date = self::calcEnrolmentLastDateByContract($contract_info, $real_sessions, $date);

        //Thu full phí  mới đk cộng học bổng
        $summary_sessions = $debt==0 ? $real_sessions + $contract_info->bonus_sessions : $real_sessions;

        if(($contract_info->product_id==5 || $contract_info->product_id==101) && $debt==0){
          $contract_status=7;
          $contract_info->count_recharge = -100;
        }
        $update_contract_data = (Object)[
            'id' => $contract_id,
            'status' => $contract_status,
            'type' => $new_type,
            'total_charged' => $total,
            'reserved_sessions' => (int)$contract_info->relation_reserved_sessions,
            'reservable_sessions' => $reservable_sessions > 0 ? $reservable_sessions : 'NULL',
            'real_sessions' => $real_sessions,
            'summary_sessions'=> $summary_sessions,
            'debt_amount' => $debt,
            'editor_id' => $uid,
            'payment_id' => $payment_id,
            'updated_at' => date("Y-m-d H:i:s"),
            'enrolment_last_date' => $enrolment_last_date,
            'reservable' => $reservable,
            'action'=>'charge_fee_'.$payment_id,
            'count_recharge'=>$contract_info->count_recharge
        ];

        u::updateContract($update_contract_data);
        //self::updateSaleReport($isEdit ? $amount - $oldAmount ?: 0 : $amount, $contract_info);
        $data->done = true;
        $apax_log_payment = u::first("SELECT count(id) AS total FROM apax_log_payment WHERE contract_id= $contract_id");
        if($apax_log_payment->total==0){ 
          $sms_info = u::first("SELECT s.gud_mobile1,s.name AS student_name, (SELECT name FROM branches WHERE id= c.branch_id) AS branch_name, (SELECT name FROM products WHERE id= c.product_id) AS product_name 
            FROM contracts AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE c.id=$contract_id");
          $sms_phone=$sms_info->gud_mobile1;
          $sms_content="CMSEdu TB Quy phu huynh da nop ".number_format($amount)." dong cho hoc sinh $sms_info->student_name - CT ".u::convert_name($sms_info->product_name).". Hotline CSKH 1800646805.";
          $sms =new Sms();
          $sms->sendSms($sms_phone,$sms_content,2,0,1);
        }
        return $response->formatResponse($code, $data);
    }

    private function updateSaleReport($amount, $contract_info)
    {
        $ec_id = (int)$contract_info->ec_id;
        $team_leader_id = $contract_info->real_team_leader_id != '' ? $contract_info->real_team_leader_id : $ec_id;
        $branch_id = (int)$contract_info->branch_id;
        $ceo_branch_id = (int)$contract_info->real_ceo_branch_id;
        $zone_id = (int)$contract_info->zone_id;
        $region_id = (int)$contract_info->region_id;

        $ec_role_id = ROLE_EC;
        $ceo_branch_role_id = ROLE_BRANCH_CEO;
        date_default_timezone_set("Asia/Bangkok");
        $this_month = date("Y-m");
        $sale_report_hash = md5($ec_id . $branch_id . $this_month);
        $branch_report_hash = md5($ceo_branch_id . $branch_id . $this_month);
        $check_branch_report = u::first("SELECT COUNT(id) existed FROM sales_report WHERE hash = '$branch_report_hash'");
        $check_sale_report = u::first("SELECT COUNT(id) existed FROM sales_report WHERE hash = '$sale_report_hash'");
        if ($check_branch_report && isset($check_branch_report->existed) && $check_branch_report->existed) {
            u::query("UPDATE sales_report SET amount = (amount + $amount), datetime = NOW() WHERE hash = '$branch_report_hash'");
        } else {
            u::query("INSERT INTO sales_report (`branch_id`, `region_id`, `zone_id`, `user_id`, `type`, `group`, 
                          `role_id`, `amount`, `datetime`, `hash`, `meta_data`)
                           VALUES ('$branch_id', '$region_id', '$zone_id', '$ceo_branch_id', 'sale', 'branch',
                                   $ceo_branch_role_id, $amount, NOW(), '$branch_report_hash', '$this_month')");
        }
        if ($check_sale_report && isset($check_sale_report->existed) && $check_sale_report->existed) {
            u::query("UPDATE sales_report SET amount = (amount + $amount), datetime = NOW() WHERE hash = '$sale_report_hash'");
        } else {
            u::query("INSERT INTO sales_report (`branch_id`, `region_id`, `zone_id`, `user_id`, `type`, `team`, 
                          `group`, `role_id`, `amount`, `datetime`, `hash`, `meta_data`)
                           VALUES ('$branch_id', '$region_id', '$zone_id', '$ec_id', 'sale', '$team_leader_id', 'ec',
                                   $ec_role_id, $amount, NOW(), '$sale_report_hash', '$this_month')");
        }
    }

    private function chargeCyber($payment_id, $user_id)
    {
        $query = "SELECT p.created_at,
                      p.id,
                      (SELECT accounting_id FROM branches WHERE id = c.branch_id) AS branch_accounting_id,
                      s.accounting_id AS student_accounting_id,
                      s.gud_name1 AS parent,
                      s.address,
                      c.accounting_id AS contract_accounting_id,
                      c.bill_info,
                      p.method,
                      c.start_date,
                      c.end_date,
                      t.accounting_id AS tuition_fee_accounting_id,
                      t.price AS tuition_fee_price,
                      t.discount AS tuition_fee_discount,
                      p.must_charge,
                      p.total AS total_charged,
                      p.debt AS debt_amount,
                      p.amount
                FROM payment p LEFT JOIN contracts c ON p.contract_id = c.id
                    LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
                    LEFT JOIN students s ON c.student_id = s.id
                WHERE p.id = $payment_id AND s.status > 0  
      ";

        $payment = u::first($query);

        if ($payment) {
            $cyberAPI = new CyberAPI();
            $res = $cyberAPI->charge($payment, $user_id);

            if ($res) {
                u::query("UPDATE payment SET accounting_id = '$res' WHERE id = $payment_id");
            }
        }
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
        $query = "SELECT p.*,
                c.code, c.type AS contract_type, c.receivable, c.must_charge AS contract_must_charge, c.description, c.payload AS contract_payload, c.status,
                c.bill_info, c.start_date AS contract_start_date, c.end_date AS contract_end_date, c.total_sessions, c.real_sessions,
                s.name AS student_name, s.accounting_id AS student_accounting_id, s.phone AS student_phone,
                s.id AS student_id, s.stu_id AS student_lms_id, s.crm_id AS student_crm_id,
                s.stu_id,
                s.accounting_id, 
                CONCAT(s.name, ' - ', s.crm_id) AS label,
                COALESCE(p.count, 1) AS charge_time,
                COALESCE(s.gud_name1, s.gud_name2) AS parent_name,
                COALESCE(s.gud_mobile1, s.gud_mobile2) AS parent_mobile,
                COALESCE(s.gud_email1, s.gud_email2) AS parent_email,
                COALESCE(c.total_discount, 0) AS total_discount,
                COALESCE(p.debt, 0) AS debt_amount,
                COALESCE(p.total, 0) AS total_charged,
                CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
                CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
                CONCAT(u3.full_name, ' - ', u3.username) AS ec_leader_name,
                CONCAT(u4.full_name, ' - ', u4.username) AS creator_name,
                CONCAT(u5.full_name, ' - ', u5.username) AS contract_creator_name,
                COALESCE((c.tuition_fee_price - c.must_charge), 0) AS final_discount,
                tf.name AS tuition_fee_name, tf.price AS tuition_fee_price, p.note,
                b.name AS branch_name, pr.name AS product_name, pg.name AS program_name
              FROM payment AS p
                LEFT JOIN contracts AS c ON p.contract_id = c.id
                LEFT JOIN students AS s ON c.student_id = s.id
                LEFT JOIN users AS u1 ON c.ec_id = u1.id
                LEFT JOIN users AS u2 ON c.cm_id = u2.id
                LEFT JOIN users AS u3 ON c.ec_leader_id = u3.id
                LEFT JOIN users AS u4 ON p.creator_id = u4.id
                LEFT JOIN users AS u5 ON c.creator_id = u5.id
                LEFT JOIN branches AS b ON c.branch_id = b.id
                LEFT JOIN products AS pr ON c.product_id = pr.id
                LEFT JOIN programs AS pg ON c.program_id = pg.id
                LEFT JOIN tuition_fee AS tf ON c.tuition_fee_id = tf.id
              WHERE p.id = $id";
        $data = DB::select(DB::raw($query));
        $data = is_array($data) && count($data) ? $data[0] : $data;
        $contract_id = (int)$data->contract_id;
        $other = "SELECT p.*,
                c.code, c.type, c.receivable, c.must_charge, c.description, c.payload, c.status, c.id AS contract_id,
                c.bill_info, c.start_date, c.end_date, c.total_sessions, c.real_sessions, c.created_at,
                s.name AS student_name, s.accounting_id AS student_accounting_id, s.phone AS student_phone,
                s.id AS student_id, s.stu_id AS student_lms_id, s.crm_id AS student_crm_id,
                CONCAT(s.name, ' - ', s.crm_id) AS label,
                COALESCE(p.count, 0) + 1 AS charge_time,
                COALESCE(s.gud_name1, s.gud_name2) AS parent_name,
                COALESCE(s.gud_mobile1, s.gud_mobile2) AS parent_mobile,
                COALESCE(s.gud_email1, s.gud_email2) AS parent_email,
                COALESCE(s.gud_card1, s.gud_card2) AS parent_card,
                COALESCE(c.total_discount, 0) AS total_discount,
                COALESCE(p.debt, 0) AS debt_amount,
                COALESCE(p.total, 0) AS total_charged,
                CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
                CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
                CONCAT(u3.full_name, ' - ', u3.username) AS ec_leader_name,
                CONCAT(u4.full_name, ' - ', u4.username) AS creator_name,
                CONCAT(u5.full_name, ' - ', u5.username) AS contract_creator_name,
                COALESCE((c.tuition_fee_price - c.must_charge), 0) AS final_discount,
                tf.name AS tuition_fee_name, tf.price AS tuition_fee_price, p.note,
                b.name AS branch_name, pr.name AS product_name, pg.name AS program_name
              FROM payment AS p
                LEFT JOIN contracts AS c ON p.contract_id = c.id
                LEFT JOIN students AS s ON c.student_id = s.id
                LEFT JOIN users AS u1 ON c.ec_id = u1.id
                LEFT JOIN users AS u2 ON c.cm_id = u2.id
                LEFT JOIN users AS u3 ON c.ec_leader_id = u3.id
                LEFT JOIN users AS u4 ON p.creator_id = u4.id
                LEFT JOIN users AS u5 ON c.creator_id = u5.id
                LEFT JOIN branches AS b ON c.branch_id = b.id
                LEFT JOIN products AS pr ON c.product_id = pr.id
                LEFT JOIN programs AS pg ON c.program_id = pg.id
                LEFT JOIN tuition_fee AS tf ON c.tuition_fee_id = tf.id
              WHERE p.contract_id = $contract_id ORDER BY id DESC";
        $history = DB::select(DB::raw($other));
        if ($history) {
          foreach ($history as $record) {
            $record->current = $record->id == $id ? 'current' : '';
          }
        }
        $data->history = $history;
      }
      return $response->formatResponse($code, $data);
    }

    public function quit(Request $request, $id) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = (Object)[];
        $code = APICode::SUCCESS;
        u::query("UPDATE payment set type = 0 WHERE id = $id");
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
     * Search suggest student for payment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function suggest(Request $request, $keyword, $branch) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $branches = $branch ? $branch : $session->branches_ids;
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $special_condition = ENVIRONMENT == 'product' ? " AND s.accounting_id != '' " : '';
        $query = "SELECT c.id AS contract_id, c.code, c.type, c.receivable, c.must_charge, c.description, c.payload, c.status, 
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
              WHERE c.id > 0 $special_condition 
                  AND c.id IN (SELECT MAX(c.id) FROM contracts AS c LEFT JOIN students AS s ON c.student_id = s.id 
									    WHERE c.debt_amount > 0 $special_condition AND c.type IN (1, 5, 6, 7) AND c.`status` IN (1, 2, 4, 6) AND c.only_give_tuition_fee_transfer = 0 
                      AND c.branch_id IN ($branches)
											AND (s.name LIKE '$keyword%' OR s.crm_id LIKE '$keyword%' OR s.stu_id LIKE '$keyword%' OR s.accounting_id LIKE '$keyword%') 
                      GROUP BY student_id)
                  ORDER BY label ASC LIMIT 0, 10";
        $data = u::query($query);
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
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
     * Build base query by parameters from request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function query() {
      return "FROM payment AS p
        LEFT JOIN contracts AS c ON p.contract_id = c.id
        LEFT JOIN products AS pr ON c.product_id = pr.id
        LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
        LEFT JOIN users AS u ON p.creator_id = u.id
        LEFT JOIN students AS s ON c.student_id = s.id
      WHERE p.id > 0";
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
      $where = "AND c.branch_id IN ($branches)";
      if ($search->payload != '') {
        $where.= " AND p.payload = ".(int)$search->payload;
      }
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
      // return "AND c.must_charge > 0 $where
      //   AND c.id IN (SELECT MAX(id) FROM contracts GROUP BY student_id)
      //   AND p.id IN (
      //         SELECT MAX(id) FROM payment WHERE payload = 1 GROUP BY contract_id
      //         UNION
      //         SELECT MAX(id) FROM payment WHERE payload = 0 GROUP BY contract_id)";
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
      $total = "SELECT COUNT(p.id) AS a";
      $select = $this->select();
      $query = $this->query();
      $where = $this->where($request);
      $data = u::query("$select $query $where $order $limit");
      $total = u::first("$total $query $where");
      $result = (Object)['data' => $data, 'total' => $total];
      return $result;
    }

    /**
     * Build select for query by parameters from request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function select() {
      return "SELECT
        p.*, c.code,
        s.name AS student_name,
        s.crm_id AS student_crm_id,
        s.stu_id AS student_lms_id,
        s.accounting_id AS student_accounting_id,
        pr.name AS product_name,
        t.name AS tuition_fee_name,
        CONCAT(u.full_name, ' - ', u.username) AS creator";
    }


}
