<?php

namespace App\Models;

use App\Http\Controllers\EffectAPIController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\LMSAPIController;
use App\Http\Controllers\RegistersController;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;
use App\Models\CyberAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function GuzzleHttp\json_decode;

class BranchTransfer extends Model
{
  private $OM = 56;
  private $CM = 55;
  private $EC = 68;
  private $EC_LEADER = 69;
  private $GDTT = 686868;
  private $GDV = 7777777;

  public function __construct()
  {
    $this->transaction_id = (int) microtime(true);
  }

  protected $table = 'branch_transfer';

  public function getList($request, $pagination, $search, $sort)
  {
    $session = $request->users_data;
    $branches = $session->branches_ids;
    $pagination = json_decode($pagination);
    $search = json_decode($search);
    $conditions = " AND clt.status IN (1,2,3,4,5,6,7,8,9,10) AND (clt.from_branch_id IN ($branches) OR (clt.to_branch_id IN ($branches) AND clt.status IN (3,4,5,6)))";
    $search->keyword =  trim($search->keyword);
    if ($search->keyword != "") {
      $conditions .= " AND (s.name LIKE '%$search->keyword%' OR s.cms_id LIKE '%$search->keyword%' OR s.crm_id LIKE '%$search->keyword%' OR s.accounting_id LIKE '%$search->keyword%')";
    }
    if ($search->status != "") {
      $conditions .= " AND (clt.status = $search->status)";
    }
    if ($search->branch) {
      $conditions .= " AND (clt.from_branch_id IN ($search->branch) OR clt.to_branch_id IN ($search->branch)) ";
    }
    if ($conditions) {
      $total = 0;
      $query_total = "SELECT count(clt.id) AS total
      FROM
        branch_transfer clt 
        LEFT JOIN students s ON clt.`student_id` = s.`id`
        LEFT JOIN branches b ON clt.`from_branch_id` = b.`id`
        LEFT JOIN branches br ON clt.to_branch_id = br.`id`
        LEFT JOIN classes c ON clt.`from_class_id` = c.`id`
        LEFT JOIN products p ON clt.`from_product_id` = p.`id`
        LEFT JOIN products p2 ON clt.`to_product_id` = p2.`id`
        LEFT JOIN programs pr ON clt.`from_program_id` = pr.`id`
        LEFT JOIN programs pr2 ON clt.`to_program_id` = pr2.`id`
      WHERE clt.id > 0 AND clt.type>0 AND (clt.version = 3  OR (clt.version <3 AND clt.status NOT IN (1,4))) $conditions";
      $result = u::first($query_total);
      $total = $result->total;
      $limit = '';
      $pagination = json_decode($request->pagination);
      $pagination->cpage = $pagination->cpage ? $pagination->cpage : 1;
      if ($pagination->cpage && $pagination->limit) {
        $offset = ((int) $pagination->cpage - 1) * (int) $pagination->limit;
        if ($offset > $total) {
          $offset = 0;
        }
        $limit .= " LIMIT $offset, $pagination->limit";
      }
      $query = "SELECT
				clt.*, 
				s.`accounting_id` accounting_id,
				s.`name` student_name,
				s.`cms_id` cms_id,
				b.`name` from_branch,
				br.`name` to_branch,
				p.`name` from_product,
				p2.`name` to_product,
				pr.`name` from_program,
				pr2.`name` to_program,
				c.`cls_name` class_name,
				(SELECT CONCAT(`full_name`, ' - ', `username`) FROM users WHERE id = clt.creator_id) creator_name, 
					(SELECT CONCAT(`full_name`, ' - ', `username`) FROM users WHERE id = clt.from_approver_id) from_om_name, 
				(SELECT CONCAT(`full_name`, ' - ', `username`) FROM users WHERE id = clt.to_approver_id) to_om_name, 
        (SELECT CONCAT(`full_name`, ' - ', `username`) FROM users WHERE id = clt.accounting_approver_id) accounting_name, 
				(SELECT `accounting_id` FROM students WHERE id = clt.student_id) student_act, 
					(SELECT `cms_id` FROM students WHERE id = clt.student_id) student_cms, 
					(SELECT `crm_id` FROM students WHERE id = clt.student_id) student_crm, 
					(SELECT `name` FROM students WHERE id = clt.student_id) student_name, 
					(SELECT CONCAT(`name`, ' - ', cms_id) FROM students WHERE id = clt.student_id) student_label 
			FROM
				branch_transfer clt 
				LEFT JOIN students s ON clt.`student_id` = s.`id`
				LEFT JOIN branches b ON clt.`from_branch_id` = b.`id`
				LEFT JOIN branches br ON clt.to_branch_id = br.`id`
				LEFT JOIN classes c ON clt.`from_class_id` = c.`id`
				LEFT JOIN products p ON clt.`from_product_id` = p.`id`
				LEFT JOIN products p2 ON clt.`to_product_id` = p2.`id`
				LEFT JOIN programs pr ON clt.`from_program_id` = pr.`id`
				LEFT JOIN programs pr2 ON clt.`to_program_id` = pr2.`id`
			WHERE clt.id > 0 AND clt.type>0 AND (clt.version = 3  OR (clt.version <3 AND clt.status NOT IN (1,4))) $conditions
			ORDER BY clt.id DESC $limit";
      $result = u::query($query);
      $xlist = [];
      if (!empty($result)) {
        foreach ($result as $item) {
          $item = (object) $item;
          $item->transferred_data = $item->transferred_data ? (object) json_decode($item->transferred_data) : [];
          if (isset($item->transferred_data->contracts)) {
            $item->orgin_contracts = (array) $item->transferred_data->contracts;
            if (count($item->orgin_contracts)) {
              $from_cotract = (object) $item->orgin_contracts[0];
              $item->from_product = $from_cotract->product_name;
              $item->from_tuition_fee = $from_cotract->tuition_fee_name;
            }
          }
          if (isset($item->transferred_data->student)) {
            $item->orgin_student = (array) $item->transferred_data->student;
          }
          $item->exchanged_data = $item->exchanged_data ? (object) json_decode($item->exchanged_data) : [];
          if (isset($item->exchanged_data->contracts)) {
            $item->transfer_contracts = (array) $item->exchanged_data->contracts;
            if (count($item->transfer_contracts)) {
              $to_cotract = (object) $item->transfer_contracts[0];
              $item->to_product = $to_cotract->product_name;
            }
          }
          if (isset($item->exchanged_data->student)) {
            $item->transfer_student = (array) $item->exchanged_data->student;
          }
          $item->information = $item->information ? (object) json_decode($item->information) : [];
          if (isset($item->information->semester_id)) {
            $id = (int) $item->information->semester_id;
            $sem_info = u::first("SELECT * FROM semesters WHERE id=$id");
            if ($sem_info) {
              $item->information->semester_name = $sem_info->name;
            } else {
              $item->information->semester_name = "";
            }
          }
          $xlist[] = $item;
          $item->check_approve = 1;
          $item->check_approve_mess = "";
          if ($item->status == 4 && $item->to_class_id) {
            $to_class_info = u::first("SELECT max_students,`type`,(SELECT count(id) FROM contracts WHERE `status`!=7 AND class_id=$item->to_class_id) AS curr_student FROM classes WHERE id=$item->to_class_id");
            if ($to_class_info && $to_class_info->max_students <= $to_class_info->curr_student) {
              $item->check_approve = 0;
              $item->check_approve_mess = "Lớp đã full học sinh trong lúc chờ duyệt";
            }
            $duplicate_nick = u::first("SELECT s.id
                FROM contracts AS c LEFT JOIN students AS s ON s.id=c.student_id 
                WHERE c.class_id=$item->to_class_id AND c.status!=7 AND s.id!=$item->student_id AND s.nick =(SELECT nick FROM students WHERE id=$item->student_id)");
            if ($duplicate_nick) {
              $item->check_approve = 0;
              $item->check_approve_mess = "Đã add học sinh cùng nick trong lúc chờ duyệt";
            }
            $contract_info = u::first("SELECT * FROM contracts WHERE student_id=$item->student_id AND `status`!=7 ORDER BY count_recharge LIMIT 1");
            if ($contract_info && $to_class_info && ($to_class_info->type != 0 && $contract_info->type > 0)) {
              $item->check_approve = 0;
              $item->check_approve_mess = "Lớp bị đổi trạng thái lớp trải nghiệm trong lúc chờ duyệt không còn phù hợp với học sinh";
            }
          }
        }
      }
      $page = apax_get_pagination(json_decode($request->pagination), $total);
      if (!$total) {
        $page->spage = 0;
        $page->ppage = 0;
        $page->npage = 0;
        $page->lpage = 0;
        $page->cpage = 0;
        $page->total = 0;
      }
      $res = [
        "list" => $xlist,
        "pagination" => $page
      ];
    } else {
      $res = [];
    }
    return $res;
  }
  public function suggestPassenger($key, $branch_id)
  {
    $search_condition = "";
    if ($key && $key !== '_') {
      if (is_numeric($key)) {
        $search_condition .= " AND (s.crm_id LIKE '%$key%' OR s.cms_id LIKE '%$key%' )";
      } else {
        $search_condition .= " AND (s.name LIKE '%$key%' OR s.crm_id LIKE '%$key%' OR s.cms_id LIKE '%$key%' OR s.accounting_id LIKE '%$key%')";
      }
    }
    $query = "SELECT 
				s.`id` student_id,
				s.`crm_id` crm_id,
				s.`cms_id` cms_id,
				s.branch_id,
				s.accounting_id,
				s.`name` student_name,
				s.`nick`,
				s.type student_type
			FROM students s
			WHERE s.status >0  AND s.branch_id = $branch_id $search_condition LIMIT 10";
    $res = u::query($query);
    $result = [];
    if (!empty($res)) {
      foreach ($res as &$r) {
        $r->label = "$r->student_name - $r->cms_id";
        $result[$r->student_id] = $r;
      }
      $result = array_values($result);
    }
    return $result;
  }
  public static function getAllContracts($student_id)
  {
    $student = u::first("SELECT s.id,s.crm_id,s.cms_id,s.status,s.accounting_id,s.type,s.date_of_birth,s.branch_id,s.source,
      s.name,s.email,s.phone,s.gud_name1,s.gud_mobile1,s.gud_email1,s.gud_name2,s.gud_mobile2,s.gud_email2,
      s.address,s.nick,s.school,s.school_grade,s.gender, t.ec_id, t.cm_id, t.ec_leader_id, t.om_id,
			(SELECT COALESCE(CONCAT(full_name, ' - ', username), 'Chưa có EC') FROM users WHERE id = t.ec_id) ec_name,    
			(SELECT COALESCE(CONCAT(full_name, ' - ', username), 'Chưa có CM') FROM users WHERE id = t.cm_id) cm_name, 
			(SELECT COALESCE(CONCAT(full_name, ' - ', username), 'Chưa có EC_Leader') FROM users WHERE id = t.ec_leader_id) ec_leader_name,    
			(SELECT COALESCE(CONCAT(full_name, ' - ', username), 'Chưa có OM') FROM users WHERE id = t.om_id) om_name ,
      0 AS student_trial
      FROM students s LEFT JOIN term_student_user t ON t.student_id = s.id WHERE s.id = $student_id");
    $validate = self::validateBranchTransfer($student->id);
    $data = (object) [
      'has_error' => $validate->has_error,
      'message' => $validate->message,
      'student' => $student,
      'contracts' => []
    ];
    if ($data->has_error == 0) {
      $getContractsQuery = "SELECT 
				c.id contract_id,
				c.branch_id,
				c.code, 
				IF(c.product_id > 0, c.product_id, (SELECT product_id FROM term_program_product WHERE program_id = c.program_id)) product_id,
				(SELECT `name` FROM products WHERE id = c.product_id) product_name,
				(SELECT `code` FROM program_codes WHERE id = c.program_label) program_name,
				c.program_id,
				(SELECT `name` FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_name,
				(SELECT `cls_name` FROM classes WHERE id = c.class_id) class_name,
				c.class_id,
				c.`type` contract_type,
				c.`payload`,
        c.`total_charged`,
        c.`total_sessions`,
        c.`real_sessions`,
        c.`bonus_sessions`,
        c.`summary_sessions`,
				c.`must_charge`,
				IF(c.type > 0 , IF(c.type IN (1,2),IF(c.total_charged > 0, c.total_charged,0), c.total_charged), 0) total_charged,
				IF(c.`class_id` IS NULL, c.`start_date`, c.`enrolment_start_date`) start_date,
				IF(c.`class_id` IS NULL, c.`end_date`, c.`enrolment_last_date`) end_date,
				IF(c.class_id IS NULL, 0,1) has_enrolment,
				c.tuition_fee_id,
				(SELECT receivable FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_receivable,
				(SELECT `session` FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_sessions,
				IF(c.debt_amount > 0, 1, 0) block_edit,
        c.accounting_id,c.debt_amount
			FROM contracts c
				-- LEFT JOIN sessions ss ON c.`class_id` = ss.`class_id`
			WHERE c.`student_id` = $student_id
				AND c.branch_id = $student->branch_id
				AND c.`status` > 0 AND c.`status` < 7
				AND (c.class_id IS NULL OR c.class_id = 0 OR  c.`enrolment_last_date` >= CURDATE())				
				AND ( c.real_sessions > 0 OR c.bonus_sessions > 0 OR (c.type=0 AND c.real_sessions=0))
        AND ((c.count_recharge=0 AND c.debt_amount=0) OR (c.count_recharge!=0))
			-- GROUP BY c.`id`
			ORDER BY c.`count_recharge`, c.`id` ASC";
      $data->contracts = u::query($getContractsQuery);
      if (!empty($data->contracts)) {
        $contract_ids = [];
        $class_ids = [];
        $next_learning_date = '';
        foreach ($data->contracts as $k => $contract) {
          if ($k == 0 && $data->contracts[$k]->contract_type == 0) {
            $data->student->student_trial = 1;
          }
          $data->contracts[$k]->done_sessions = 0;
          if ($contract->class_id != NULL) {
            $class_day = u::getClassDays($contract->class_id);
            $public_holiday = u::getPublicHolidays($contract->class_id, $contract->branch_id, $contract->product_id);
            $reserved_dates = self::getReservedDates_transfer([$contract->contract_id]);
            $merged_holi_day = $reserved_dates && isset($reserved_dates[$contract->contract_id]) ? array_merge($public_holiday, $reserved_dates[$contract->contract_id]) : $public_holiday;
            $done_sessions = u::calSessions($contract->start_date, date('Y-m-d', strtotime("-1 days")), $merged_holi_day, $class_day);
            $data->contracts[$k]->left_sessions = $contract->real_sessions - $done_sessions->total > 0 ? $contract->real_sessions - $done_sessions->total : 0;
            $next_learning_date = u::calEndDate(1, $class_day, $public_holiday, date('Y-m-d'));
            $next_learning_date = $next_learning_date->end_date;
            if ($contract->real_sessions) {
              $data->contracts[$k]->left_amount = ceil($contract->total_charged * $data->contracts[$k]->left_sessions / $contract->real_sessions);
            } else {
              $data->contracts[$k]->left_amount = $contract->total_charged;
            }
            if ($contract->contract_type == 0) {
              $data->contracts[$k]->bonus_sessions = $contract->bonus_sessions - $done_sessions->total > 0 ? $contract->bonus_sessions - $done_sessions->total : 0;
            } else {
              if ($contract->real_sessions < $done_sessions->total) {
                $data->contracts[$k]->bonus_sessions = $contract->real_sessions + $contract->bonus_sessions - $done_sessions->total > 0 ? $contract->real_sessions + $contract->bonus_sessions - $done_sessions->total : 0;
              }
            }
            $data->contracts[$k]->done_sessions = $contract->real_sessions + $contract->bonus_sessions > $done_sessions->total ? $done_sessions->total : $contract->real_sessions + $contract->bonus_sessions;
          } else {
            $data->contracts[$k]->left_amount = $contract->total_charged;
            $data->contracts[$k]->left_sessions = $contract->real_sessions;
          }
          $contract_ids[] = $contract->contract_id;
          $class_ids[] = $contract->class_id;
          if ($contract->contract_type == 0) {
            $data->contracts[$k]->tuition_fee_name = "Học thử";
          }
        }
        $data->student->next_learning_date = $next_learning_date;
        $reserveModel = new Reserve();
        $class_days = u::getMultiClassDays($class_ids);
        $reserved_dates = $reserveModel->getReservedDates($contract_ids);
        foreach ($data->contracts as &$r) {
          if (isset($reserved_dates[$r->contract_id])) {
            $r->reserved_dates = $reserved_dates[$r->contract_id];
          } else {
            $r->reserved_dates = [];
          }
          if (isset($class_days[$r->class_id])) {
            $r->class_days = $class_days[$r->class_id];
          } else {
            $r->class_days = u::getDefaultClassDays($r->product_id);
          }
        }
      }
    }
    return $data;
  }
  public function checkTargetBranch($branch_id = 0, $student_id = 0)
  {
    $data = null;
    $back = false;
    $exdata = null;
    if ($branch_id && $student_id) {
        $student = u::first("SELECT s.id,s.crm_id,s.cms_id,s.status,s.accounting_id,s.type,s.date_of_birth,s.branch_id,s.source,
        s.name,s.email,s.phone,s.gud_name1,s.gud_mobile1,s.gud_email1,s.gud_name2,s.gud_mobile2,s.gud_email2,
        s.address,s.nick,s.school,s.school_grade,s.gender
        FROM students s WHERE s.id = $student_id");
    }
    $managers = $this->getManagerInfo($branch_id);
    $ec_user_id = $managers[$this->EC_LEADER] ? $managers[$this->EC_LEADER] : $managers[$this->EC];
    $cm_user_id = $managers[$this->OM] ? $managers[$this->OM] : $managers[$this->OM];
    $ec = $this->getUserInfo($ec_user_id);
    $cm = $this->getUserInfo($cm_user_id);
    $student->ec_id =isset($ec->id) ? $ec->id : '';
    $student->cm_id = isset($cm->id) ? $cm->id : '';
    $student->ec_leader_id = isset($ec->id) ? $ec->id : '';
    $student->om_id = isset($cm->id) ? $cm->id : '';
    $student->om_name = isset($cm->label) ? $cm->label : '';
    $student->ec_leader_name = isset($ec->label) ? $ec->label : '';
    $student->ec_name = isset($ec->label) ? $ec->label : '';
    $student->cm_name = isset($cm->label) ? $cm->label : '';
    $data = (object) [
      'student' => $student,
      'semesters' => $this->getSemesters($branch_id),
      'ec_receiver' => $ec,
      'cm_receiver' => $cm,
      'ex_data' => $exdata,
      'come_back' => $back
    ];
    return $data;
  }
  protected function getUserInfo($user_id)
  {
    return u::first("SELECT u.*, CONCAT(u.full_name, ' - ', u.username) label FROM users u WHERE id = $user_id");
  }
  public function getSemesters()
  {
    $time = date('Y-m-d');
    return u::query("SELECT id, `name`, product_id, (SELECT name from products WHERE id=semesters.product_id) AS product_name FROM semesters WHERE status = 1 AND end_date > '$time'");
  }
  protected function getManagerInfo($branch_id)
  {
    $query = "SELECT user_id, role_id FROM term_user_branch WHERE branch_id = $branch_id AND status = 1 AND role_id IN ($this->OM, $this->CM, $this->EC, $this->EC_LEADER, $this->GDTT, $this->GDV) GROUP BY role_id";
    $res = u::query($query);
    $info = [
      $this->OM => 0,
      $this->CM => 0,
      $this->EC => 0,
      $this->EC_LEADER => 0,
      $this->GDTT => 0,
      $this->GDV => 0,
    ];
    if ($res) {
      foreach ($res as $r) {
        $info[$r->role_id] = $r->user_id;
      }
      if (isset($info[$this->OM]) && $info[$this->OM]) {
        $info[$this->CM] = $info[$this->OM];
      }
      if (isset($info[$this->EC_LEADER]) && $info[$this->EC_LEADER]) {
        $info[$this->EC] = $info[$this->EC_LEADER];
      }
    }
    return $info;
  }
  public function prepareTransferData($info)
  {
    $student = (object) $info->student;
    $contracts = (array) $info->contracts;
    $information = (object) $info->information;
    $semester_id = (int) $information->semester_id;
    $transfer_date = $information->transfer_date;
    $semester_data = u::first("SELECT p.name product_name, s.product_id FROM semesters s LEFT JOIN products p ON s.product_id = p.id WHERE s.id = $semester_id");
    $product_id = (int) $semester_data->product_id;
    $product_name = $semester_data->product_name;
    $receiver_contracts = [];
    $error = 0;
    $message_error = "";
    $total_transfer_amount = 0;
    $total_transfer_session = 0;
    $total_transfered_amount = 0;
    $total_transfered_session = 0;
    $transfered_start_date = $transfer_date;
    if (count($contracts)) {
      $branch_id = (int) $information->to_branch_id;
      $holiDay = u::getPublicHolidays(0, $branch_id, $product_id);
      $tmp_end_date = date('Y-m-d');
      foreach ($contracts as $i => $contract) {
        $contract = (object) $contract;
        $receiver_contract = (object) [];
        $class_days = $contract->class_days ? $contract->class_days : [2, 5];
        if ($product_id == 4) {
          $class_days = [2, 4, 6];
        }
        $amount = (int) $contract->left_amount;
        $sessions = (int) $contract->left_sessions;
        $total_transfer_amount += $amount;
        $total_transfer_session += $sessions + $contract->bonus_sessions;
        $tuition_fee_id = $contract->tuition_fee_id;

        $original_transfer_data = u::calcTransferTuitionFeeV2($tuition_fee_id, (int) $contract->must_charge, $branch_id, $product_id, (int) $contract->total_sessions);
        $real_transfer_data = u::calcTransferTuitionFeeV2($tuition_fee_id, $amount, $branch_id, $product_id, $sessions);

        $total_transfered_session += $real_transfer_data->sessions + $contract->bonus_sessions;
        $total_transfered_amount += (int) $real_transfer_data->transfer_amount;
        $receiver_contract->contract_id = $contract->contract_id;
        $receiver_contract->total_sessions = $original_transfer_data->sessions;
        $receiver_contract->must_charge = $original_transfer_data->transfer_amount;
        $receiver_contract->real_sessions = $real_transfer_data->sessions;
        $receiver_contract->left_sessions = $real_transfer_data->sessions;
        $receiver_contract->left_amount = $amount;
        $receiver_contract->total_charged = $real_transfer_data->transfer_amount;
        if ($i == 0) {
          $receiver_contract->start_date = $transfered_start_date;
        } else {
          $receiver_contract->start_date = date('Y-m-d', strtotime($tmp_end_date . ' + 1 day'));
        }
        $arr_txt_accounting_id = explode('.',$contract->accounting_id);
        $branch_to_info = u::first("SELECT accounting_id FROM branches WHERE id=$branch_id");
        $receiver_contract->accounting_id = isset($arr_txt_accounting_id[3])? $branch_to_info->accounting_id.'.'.date('y').'.PNH.'.(9000000+(int)$arr_txt_accounting_id[3]):'';
        $receiver_contract->product_id = $product_id;
        $receiver_contract->program_id = 0;
        $receiver_contract->product_name = $product_name;
        $receiver_contract->program_name = 'Chưa chọn chương trình học';
        $receiver_contract->class_name = 'Chưa xếp lớp';
        if ($contract->contract_type == 0) {
          $receiver_contract->tuition_fee_name = 'Học thử';
          $receiver_contract->tuition_fee_id = 0;
          if ($contract->bonus_sessions == 1) {
            $receiver_contract->last_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 1 day'));
            $receiver_contract->end_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 1 day'));
          } elseif ($contract->bonus_sessions == 2) {
            $receiver_contract->last_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 3 day'));
            $receiver_contract->end_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 3 day'));
          } elseif ($contract->bonus_sessions == 3) {
            $receiver_contract->last_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 7 day'));
            $receiver_contract->end_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 7 day'));
          } elseif ($contract->bonus_sessions > 3) {
            $transfered_date = u::calEndDate($contract->bonus_sessions, $class_days, $holiDay, $receiver_contract->start_date);
            $receiver_contract->last_date = $transfered_date->end_date;
            $receiver_contract->end_date = $transfered_date->end_date;
          }
        } else {
          if ($sessions) {
            if ($real_transfer_data->code == 200) {
              $receiver_contract->tuition_fee_name = $real_transfer_data->receive_tuition_fee->name;
              $receiver_contract->tuition_fee_id = $real_transfer_data->receive_tuition_fee->id;
              $transfered_date = u::calEndDate(($real_transfer_data->sessions + (int) $contract->bonus_sessions), $class_days, $holiDay, $receiver_contract->start_date);
              // $transfered_start_date = u::getNextDay($transfered_date->end_date);
              $receiver_contract->last_date = $transfered_date->end_date;
              $receiver_contract->end_date = $transfered_date->end_date;
            } else {
              $error = 1;
              $message_error = $real_transfer_data->message;
              $receiver_contract->tuition_fee_name = '';
              $receiver_contract->tuition_fee_id = 0;
              if ($contract->bonus_sessions == 1) {
                $receiver_contract->last_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 1 day'));
                $receiver_contract->end_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 1 day'));
              } elseif ($contract->bonus_sessions == 2) {
                $receiver_contract->last_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 3 day'));
                $receiver_contract->end_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 3 day'));
              } elseif ($contract->bonus_sessions == 3) {
                $receiver_contract->last_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 7 day'));
                $receiver_contract->end_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 7 day'));
              } elseif ($contract->bonus_sessions > 3) {
                $transfered_date = u::calEndDate($contract->bonus_sessions, $class_days, $holiDay, $receiver_contract->start_date);
                $receiver_contract->last_date = $transfered_date->end_date;
                $receiver_contract->end_date = $transfered_date->end_date;
              }
            }
          } else {
            $tmp_real_transfer_data = u::calcTransferTuitionFeeV2($tuition_fee_id, $amount, $branch_id, $product_id, $contract->bonus_sessions);
            if ($tmp_real_transfer_data->code == 200) {
              $receiver_contract->tuition_fee_name = $tmp_real_transfer_data->receive_tuition_fee->name;
              $receiver_contract->tuition_fee_id = $tmp_real_transfer_data->receive_tuition_fee->id;
              $tmp_transfered_date = u::calEndDate($tmp_real_transfer_data->sessions, $class_days, $holiDay, $receiver_contract->start_date);
              $receiver_contract->last_date = $tmp_transfered_date->end_date;
              $receiver_contract->end_date = $tmp_transfered_date->end_date;
            } else {
              $error = 1;
              $message_error = $tmp_real_transfer_data->message;
              $receiver_contract->tuition_fee_name = '';
              $receiver_contract->tuition_fee_id = 0;
              if ($contract->bonus_sessions == 1) {
                $receiver_contract->last_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 1 day'));
                $receiver_contract->end_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 1 day'));
              } elseif ($contract->bonus_sessions == 2) {
                $receiver_contract->last_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 3 day'));
                $receiver_contract->end_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 3 day'));
              } elseif ($contract->bonus_sessions == 3) {
                $receiver_contract->last_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 7 day'));
                $receiver_contract->end_date = date('Y-m-d', strtotime($receiver_contract->start_date . ' + 7 day'));
              } elseif ($contract->bonus_sessions > 3) {
                $transfered_date = u::calEndDate($contract->bonus_sessions, $class_days, $holiDay, $receiver_contract->start_date);
                $receiver_contract->last_date = $transfered_date->end_date;
                $receiver_contract->end_date = $transfered_date->end_date;
              }
            }
          }
        }
        $tmp_end_date = isset($receiver_contract->end_date) ? $receiver_contract->end_date : '';
        $receiver_contract->bonus_sessions = $contract->bonus_sessions;
        $receiver_contract->summary_sessions = $receiver_contract->real_sessions + $receiver_contract->bonus_sessions;
        $receiver_contract->order = $i + 1;
        $receiver_contracts[] = $receiver_contract;
      }
    }
    $data = (object) [];
    $data->error = $error;
    $data->message_error = $message_error;
    $data->total_transfer_amount = $total_transfer_amount;
    $data->total_transfer_session = $total_transfer_session;
    $data->total_transfered_amount = $total_transfered_amount;
    $data->total_transfered_sessions = $total_transfered_session;
    $data->transfered_contracts = $contracts;
    $data->received_contracts = $receiver_contracts;
    return $data;
  }

  public function storeTransferedData($transsfered_data)
  {
    $student = (object) $transsfered_data->original_student;
    $information = (object) $transsfered_data->information;
    $information->student_id = $student->id;
    $information->note = $transsfered_data->transfer_note;
    $from_branch_id = $information->from_branch_id;
    $to_branch_id = $information->to_branch_id;
    $result = null;
    $student_id = $student->id;
    $created_at = date("Y-m-d H:i:s");
    $user_id = $transsfered_data->user_id;
    $attached_file = '';
    if ($transsfered_data->file) {
      $attached_file = ada()->upload($transsfered_data->file);
    }
    $transfer_note = $transsfered_data->transfer_note;
    $transfer_date = $transsfered_data->transfer_date ? $transsfered_data->transfer_date : date('Y-m-d');
    $to_total_amount = $transsfered_data->to_total_amount;
    $to_total_session = $transsfered_data->to_total_sessions;
    $from_total_amount = $transsfered_data->from_total_amount;
    $from_total_sessions = $transsfered_data->from_total_sessions;
    $transfer_contracts = $transsfered_data->transfer_contracts;
    $from_effect_id = $student->accounting_id;
    $shift_id = isset($transsfered_data->shift_id) ?$transsfered_data->shift_id :'';
    $thoi_gian_hoc = isset($transsfered_data->thoi_gian_hoc) ?$transsfered_data->thoi_gian_hoc :NULL;
    if ($transsfered_data->transfer_date) {
      $type = 1;
    } else {
      $type = 2;
    }
    $receive_contracts = $transsfered_data->receiver_contracts;
    $old_student_data = $transsfered_data->original_student;
    $new_student_data = $transsfered_data->transferred_student;
    $old_data = (object) [
      'student' => $old_student_data,
      'contracts' => $transfer_contracts
    ];
    $new_data = (object) [
      'student' => $new_student_data,
      'contracts' => $receive_contracts
    ];
    if (isset($transfer_contracts[0])) {
      $from_product_id = $transfer_contracts[0]['product_id'];
    } else {
      $from_product_id = 0;
    }
    if (isset($receive_contracts[0])) {
      $to_product_id = $receive_contracts[0]['product_id'];
    } else {
      $to_product_id = 0;
    }
    if ($new_data && $old_data) {
      $transferred_data = u::escapeJsonString(json_encode($old_data, JSON_UNESCAPED_UNICODE));
      $exchanged_data = u::escapeJsonString(json_encode($new_data, JSON_UNESCAPED_UNICODE));
      $information_data = u::escapeJsonString(json_encode($information, JSON_UNESCAPED_UNICODE));
      $comeback = (int) $information->is_back;
      $hash = md5("$student_id.$from_total_amount.$to_total_amount.$from_total_sessions.$to_total_session.$transferred_data.$exchanged_data");
      if (!u::checkExit($hash, 'tuition_transfer')) {
        $code = u::unix();
        u::query("INSERT INTO branch_transfer 
                    (`code`,
                    student_id,
                    `type`,
                    `note`,
                    transfer_date,
                    `status`,
					comeback,
                    from_branch_id,
                    to_branch_id,
                    creator_id,
                    created_at,
					information,					
                    transferred_amount,
                    exchanged_amount,
                    transferred_sessions,
                    exchanged_sessions,
                    transferred_data,
                    exchanged_data,
                    attached_file,
                    `version`,
                    from_effect_id,
                    from_product_id,
                    to_product_id,
                    hash_key,
                    shift_id,
                    thoi_gian_hoc)
                    VALUES
                    ('$code', 
                    '$student_id',
                    $type,
                    '$transfer_note',
                    '$transfer_date',
                    1,
					$comeback,
                    $from_branch_id,
                    $to_branch_id,
                    $user_id,
                    '$created_at',
					'$information_data',
                    $from_total_amount,
                    $to_total_amount,
                    $from_total_sessions,
                    $to_total_session,
                    '$transferred_data',
                    '$exchanged_data',
                    '$attached_file',
                    3,
                    '$from_effect_id',
                    '$from_product_id',
                    '$to_product_id',
                    '$hash',
                    '$shift_id',
                    '$thoi_gian_hoc')
                ");
        $result = $code;
        //insert log_class_transfer
        $class_transfer_id = DB::getPdo()->lastInsertId();
        u::query("UPDATE students SET waiting_status = 3 WHERE id = $student_id");
        u::query("INSERT INTO log_class_transfer (class_transfer_id,`status`,creator_id,created_at,comment) VALUES ($class_transfer_id,1, $user_id,'$created_at','$transfer_note')");
        $this->sendMail( $class_transfer_id);
      }
    }
    return $result;
  }
  private static function validateBranchTransfer($student_id)
  {
    $has_error = 0;
    $message = '';
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
        case 4:
            $has_error = 1;
            $message ="Học sinh đang chờ duyệt bảo lưu";
            break;
        case 5:
            $has_error = 1;
            $message ="Học sinh đang chờ duyệt chuyển lớp";
            break;
        default:
          $has_error = 0;
          $message ="";
      }
    }
    $contracts = u::first("SELECT count(id) AS total FROM contracts WHERE student_id = $student_id AND debt_amount>0 AND status IN (1,2,3,4,5,6) 
    AND (count_recharge !=0 OR (count_recharge =0 AND class_id IS NOT NULL))");
    if ($contracts->total > 0) {
      $has_error = 1;
      $message = "Học sinh có gói phí chưa thu đủ phí";
    }
    $contracts = u::first("SELECT count(id) AS total FROM contracts WHERE student_id = $student_id AND debt_amount>0 AND status IN (1,2,3,4,5,6) AND count_recharge =0 ");
    if ($contracts->total > 0) {
      $has_error = 1;
      $message = "Học sinh có gói phí chưa thu đủ phí";
    }
    $resever_info = u::first("SELECT count(id) AS total FROM reserves WHERE student_id = $student_id AND status=2 AND is_reserved =1 AND end_date>= CURDATE()");
    if ($resever_info->total > 0) {
      $has_error = 1;
      $message = "Học sinh đang được bảo lưu giữ chỗ, chỉ thực hiện chuyển trung tâm sau khi kết thúc bảo lưu";
    }
    $resever_info_tmp = u::first("SELECT count(id) AS total FROM reserves WHERE student_id = $student_id AND status=2 AND is_reserved =0 AND end_date>= CURDATE()");
    if ($resever_info_tmp->total > 0) {
      $has_error = 1;
      $message = "Học sinh đang được bảo lưu không giữ chỗ, chỉ thực hiện chuyển trung tâm sau khi kết thúc bảo lưu";
    }
    return (object) [
      'has_error' => $has_error,
      'message' => $message,
    ];
  }
  static function getReservedDates_transfer($contract_ids = [])
  {
    $res = [];

    if ($contract_ids) {
      $contract_ids_string = implode(',', $contract_ids);
      $query = "SELECT r.contract_id, r.start_date, r.end_date, r.session AS `sessions` FROM `reserves` AS r WHERE r.status = 2 AND r.contract_id IN ($contract_ids_string)";
      $data = u::query($query);

      if (!empty($data)) {
        foreach ($data as $da) {
          if (isset($res[$da->contract_id])) {
            $res[$da->contract_id][] = (object) ['start_date' => $da->start_date, 'end_date' => $da->end_date, 'sessions' => $da->sessions];
          } else {
            $res[$da->contract_id][] = (object) ['start_date' => $da->start_date, 'end_date' => $da->end_date, 'sessions' => $da->sessions];
          }
        }
      }
    }

    return $res;
  }
  public function approveBranchTransfer($info, $request)
  {
    $final = false;
    $status = 0;
    $comment = '';
    $result = '';
    $log_comment = '';

    $approver = "";
    if ($info->approve_by === 'accounting_approver') {
      $status = $info->approve_status ? 6 : 7;
      $comment = ", accounting_approve_comment = '$info->original_approver_note'";
      if ($status == 6) {
        $final = true;
        $approver = ", accounting_approver_id = $info->user_id , accounting_approved_at ='" . date('Y-m-d H:i:s') . "'";
        $result = "Bản ghi chuyển trung tâm mã '$info->code' đã được phê duyệt bởi '$info->user_name' là $info->role_name";
      } else {
        $approver = ", accounting_approver_id = $info->user_id , accounting_approved_at ='" . date('Y-m-d H:i:s') . "'";
        $result = "Bản ghi chuyển trung tâm mã '$info->code' đã bị từ chối bởi '$info->user_name' là $info->role_name";
      }
      $log_comment = $info->original_approver_note;
    } elseif ($info->approve_by === 'from_approver') {
      $status = $info->approve_status ? 4 : 2;
      $comment = ", from_approve_comment = '$info->original_approver_note'";
      if ($status == 4) {
        $approver = ", from_approver_id = $info->user_id , from_approved_at ='" . date('Y-m-d H:i:s') . "'";
        $result = "Bản ghi chuyển trung tâm mã '$info->code' đã được phê duyệt bởi '$info->user_name' là $info->role_name chuyển đi ($info->from_branch)";
      } else {
        $approver = ", from_approver_id = $info->user_id, from_approved_at ='" . date('Y-m-d H:i:s') . "'";
        $result = "Bản ghi chuyển trung tâm mã '$info->code' đã bị từ chối bởi '$info->user_name' là $info->role_name chuyển đi ($info->from_branch)";
      }
      $log_comment = $info->original_approver_note;
    } elseif ($info->approve_by === 'to_approver') {
      $status = $info->approve_status ? 5 : 3;
      $comment = ", to_approve_comment = '$info->destination_approver_note'";
      if ($status == 5) {
        $approver = ", to_approver_id = $info->user_id, to_approved_at ='" . date('Y-m-d H:i:s') . "'";
        $result = "Bản ghi chuyển trung tâm mã '$info->code' đã được phê duyệt bởi '$info->user_name' là $info->role_name chuyển đến ($info->to_branch)";
      } else {
        $approver = ", to_approver_id = $info->user_id, to_approved_at ='" . date('Y-m-d H:i:s') . "'";
        $result = "Bản ghi chuyển trung tâm mã '$info->code' đã bị từ chối bởi '$info->user_name' là $info->role_name chuyển đến ($info->to_branch)";
      }
      $log_comment = $info->destination_approver_note;
    }

    $exchanged_data = (object) $info->exchanged_data;
    $new_student_info = (object) $exchanged_data->student;
    $student_info = u::first("SELECT s.*, sg.name AS school_grade_name FROM students AS s LEFT JOIN school_grades AS sg ON (s.school_grade * 1) = sg.id WHERE s.id = $new_student_info->id");

    if ($final) {
      $lmsAPI = new LMSAPIController();
      $lmsAPI->updateStudentLMS($info->student_id,0,true);

      $new_ec_id = isset($new_student_info->ec_id) ? $new_student_info->ec_id : 0;
      $new_cm_id = isset($new_student_info->cm_id) ? $new_student_info->cm_id : 0;
      $new_om_id = $new_cm_id;
      $new_ec_leader_id = $new_ec_id;
      $newbranch = (int) $info->to_branch_id;

      $branch_info = u::first("SELECT b.* ,
            (SELECT ceo_id FROM regions WHERE id = b.region_id LIMIT 1) AS ceo_region_id,
            (SELECT user_id FROM term_user_branch WHERE branch_id = b.id AND status=1 AND role_id=686868 LIMIT 1) AS ceo_branch_id
            FROM branches AS b WHERE b.id = " . (int) $info->to_branch_id);
      $managers = $this->getManagerInfo((int) $info->to_branch_id);
      $new_ec_leader_id = $managers[$this->EC_LEADER] ? $managers[$this->EC_LEADER] : $new_ec_id;
      $new_om_id = $managers[$this->OM] ? $managers[$this->OM] : $new_cm_id;
      if (count($exchanged_data->contracts)) {
        foreach ($exchanged_data->contracts as $exc_contract) {
          $curr_contract_info = u::first("SELECT * FROM contracts WHERE id =" . $exc_contract['contract_id']);
          if ($curr_contract_info && ($curr_contract_info->type == 8 || $curr_contract_info->type == 85 || $curr_contract_info->type == 86)) {
            $type = 85;
          } elseif ($curr_contract_info && $curr_contract_info->type == 0) {
            $type = 0;
          } else {
            $type = 5;
          }
          if ($curr_contract_info && ($curr_contract_info->type == 0 || $curr_contract_info->type == 8 || $curr_contract_info->type == 85 || $curr_contract_info->type == 86)) {
            $tmp_status = 5;
          } else {
            $tmp_status = 3;
          }
          // self::callCyberContract($exc_contract['contract_id'],$request->users_data->id,'Đóng hợp đồng do chuyển trung tâm');
          u::updateContract((object) [
            "action" => "branch_transfer_contract_" . $exc_contract['contract_id'],
            "id" => $exc_contract['contract_id'],
            'accounting_id' => $exc_contract['accounting_id'],
            "type" => $type,
            "status" => $tmp_status,
            "branch_id" => $branch_info->id,
            "ceo_branch_id" => $branch_info->ceo_branch_id,
            "region_id" => $branch_info->region_id,
            "ceo_region_id" => $branch_info->ceo_region_id,
            "ec_id" => $new_ec_id,
            "ec_leader_id" => $new_ec_leader_id,
            "cm_id" => $new_cm_id,
            "om_id" => $new_om_id,
            "product_id" => $exc_contract['product_id'],
            "program_id" => NULL,
            "tuition_fee_id" => $exc_contract['tuition_fee_id'],
            "total_charged" => $exc_contract['total_charged'],
            "start_date" => $exc_contract['start_date'],
            "end_date" => $exc_contract['end_date'],
            "real_sessions" => $exc_contract['real_sessions'],
            "bonus_sessions" => $exc_contract['bonus_sessions'],
            "summary_sessions" => $exc_contract['summary_sessions'],
            // "total_sessions" => $exc_contract['summary_sessions'],
            "class_id" => NULL,
            "cstd_id" => NULL,
            "start_cjrn_id" => NULL,
            "enrolment_updator_id" => NULL,
            "enrolment_start_date" => NULL,
            "enrolment_end_date" => NULL,
            "enrolment_last_date" => NULL,
            "enrolment_expired_date" => NULL,
            "enrolment_schedule" => NULL,
            "enrolment_updated_at"=>NULL,
            "enrolment_updator_id"=>NULL,
            "enrolment_accounting_id"=>NULL,
            "enrolment_withdraw_date"=>NULL,
            "editor_id" => $request->users_data->id,
            "updated_at"=>date('Y-m-d H:i:s'),
          ]);
          self::callCyberContract($exc_contract['contract_id'],$request->users_data->id,'Chuyển trung tâm');
        }
      }
      $curr_term_student_user_info = u::first("SELECT * FROM term_student_user WHERE student_id=$student_info->id AND `status`=1");
      u::query("UPDATE term_student_user SET 
                ec_id = $new_ec_id,
                cm_id = $new_cm_id,
                ec_leader_id = $new_ec_leader_id,
                om_id = $new_om_id,
                branch_id = $newbranch,
                updated_at = NOW()
                WHERE student_id = $student_info->id");
      u::query("UPDATE students SET branch_id = $newbranch, editor_id = ".$request->users_data->id.", updated_at = '".date('Y-m-d H:i:s')."' WHERE id = $info->student_id");
      //insert log_student_update
      $content_log = "Chuyển trung tâm từ $info->from_branch sang $info->to_branch";
      u::query("INSERT INTO log_student_update (student_id,updated_by,updated_at,content,`status`,cm_id,branch_id,ceo_branch_id) 
        VALUES ($info->student_id,".$request->users_data->id.",'".date('Y-m-d H:i:s')."','$content_log',1,$new_cm_id,$info->to_branch_id,'$branch_info->ceo_branch_id')");
      //insert log_manager_transfer
      DB::table('log_manager_transfer')->insert(
				[
				'student_id' => $info->student_id,
				'from_branch_id' => $info->from_branch_id,
				'to_branch_id' => $info->to_branch_id,
				'from_cm_id' => $curr_term_student_user_info->cm_id,
				'to_cm_id' => $new_cm_id,
				'from_ec_id' => $curr_term_student_user_info->ec_id,
				'to_ec_id' => $new_ec_id,
        'from_ec_leader_id' => $curr_term_student_user_info->ec_leader_id,
        'to_ec_leader_id' => $new_ec_leader_id,
        'from_om_id' => $curr_term_student_user_info->om_id,
        'to_om_id' => $new_om_id,
        'date_transfer' => date('Y-m-d H:i:s'),
        'updated_by' => $request->users_data->id,
        'note' => "Từ chuyển trung tâm",
        'created_at'=> date('Y-m-d H:i:s'),
        'updated_at'=> date('Y-m-d H:i:s')
				]
			);
      /** Withdraw contract enrolment_last_date < NOW **/
      $tmp_contracts = u::query("SELECT * FROM contracts WHERE student_id = $student_info->id AND `status`!=7 AND branch_id = $info->from_branch_id AND (class_id IS NOT NULL OR class_id !=0)");
      foreach($tmp_contracts AS $tmp_contract){
        $updated_at = date('Y-m-d H:i:s');
        $update_contract_data = [
          'id' => $tmp_contract->id,
          'status' => 7,
          'editor_id' => $request->users_data->id,
          'updated_at' => $updated_at,
          'action' => 'withdraw_for_contract_'.$tmp_contract->id.'_'.date('Ymd'),
          'enrolment_withdraw_date' => $updated_at,
          'enrolment_withdraw_reason' => 'Quá hạn số buổi được học'
        ];
        if (count($update_contract_data)) {
          u::updateContract((Object)$update_contract_data);
        }
      }
      $sms_info = u::first("SELECT s.gud_mobile1,s.name AS student_name, 
        (SELECT name FROM branches WHERE id= bt.to_branch_id) AS to_branch_name,
        (SELECT address FROM branches WHERE id= bt.to_branch_id) AS to_branch_address,
        (SELECT hotline FROM branches WHERE id= bt.to_branch_id) AS to_branch_hotline,
        (SELECT start_time FROM shifts WHERE id= bt.shift_id ) AS start_time,
        bt.thoi_gian_hoc
      FROM branch_transfer AS bt LEFT JOIN students AS s ON s.id= bt.student_id WHERE bt.id=$info->id");
      $sms_phone=$sms_info->gud_mobile1;
      /*CMS Edu TB thay đổi trung tâm cho [Tên học sinh], chi tiết như sau:
      Thời gian học: từ ..h.. - ..h..
      Phòng học: [Tên phòng học] – tại Trung tâm CMS [Tên trung tâm]
      Địa chỉ: [Địa chỉ trung tâm]
      Hotline trung tâm :[Số hotline trung tâm]
      Kính mong Quý PH cập nhật thông tin. Trân trọng! */
      $sms_content = "CMS Edu TB thay doi trung tam cho ".u::convert_name($sms_info->student_name).", chi tiet nhu sau: 
      Thoi gian hoc: ".u::convert_name(substr($sms_info->start_time, 0, 5))." ngay ".$sms_info->thoi_gian_hoc.".  
      Phòng học: tai ".u::convert_name($sms_info->to_branch_name).". 
      Dia chi: ".$sms_info->to_branch_address.". 
      Hotline trung tam :".$sms_info->to_branch_hotline."
      Kinh mong Quy PH cap nhat thong tin. Tran trong!";
      $sms =new Sms();
      $sms->sendSms($sms_phone,$sms_content);
    }
    u::query("UPDATE branch_transfer SET `status` = $status $comment $approver WHERE id = $info->id");
    u::query("INSERT INTO log_class_transfer (class_transfer_id,`status`,creator_id,created_at,comment) VALUES ($info->id,$status, $info->creator_id,NOW(),'$log_comment')");
    if ($status == 4 || $status == 1 || $status == 5) {
      u::query("UPDATE students SET waiting_status = 3 WHERE id = $info->student_id");
    } else {
      u::query("UPDATE students SET waiting_status = 0 WHERE id = $info->student_id");
    }
    $this->sendMail( $info->id);
    return $result;
  }
  private function sendMail($transfer_id)
  {
    $transfer_info = u::first("SELECT t.from_branch_id,t.to_branch_id,t.note,t.from_approve_comment,t.to_approve_comment,t.accounting_approve_comment,t.status,
            (SELECT name FROM students WHERE id= t.student_id) AS student_name,
            (SELECT name FROM branches WHERE id = t.from_branch_id) AS from_branch_name,
            (SELECT name FROM branches WHERE id = t.to_branch_id) AS to_branch_name,
            (SELECT email FROM users WHERE id= t.creator_id) AS creator_email
          FROM branch_transfer AS t  WHERE t.id=$transfer_id");
    $arr_mail_from = (object)JobsController::getEmail($transfer_info->from_branch_id);
    $arr_mail_to = (object)JobsController::getEmail($transfer_info->to_branch_id);
    $mail = new Mail();
    if($transfer_info->status==1){
      $to = array('address' => $arr_mail_from->gdtt, 'name' => $arr_mail_from->gdtt);
      $subject = "[CRM] Yêu cầu phê duyệt chuyển trung tâm của bé $transfer_info->student_name";
      $body = "<p>Kính gửi: GĐTT $transfer_info->from_branch_name </p>
                <p>Hệ thống CRM xin thông báo: Anh/chị đã nhận được yêu cầu <strong>chuyển trung tâm</strong> của bé <strong>$transfer_info->student_name</strong> $transfer_info->from_branch_name chuyển trung tâm sang $transfer_info->to_branch_name</p>
                <p>Nội dung: $transfer_info->note</p>
                <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM để phê duyệt yêu cầu.</p>
                <p>Hoặc truy cập link: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết yêu cầu</p>
                <p>Trân trọng cảm ơn!</p>
              ";
      $mail->sendSingleMail($to, $subject, $body,[$arr_mail_from->cskh]);
    }elseif($transfer_info->status==2){
      $to = array('address' => $transfer_info->creator_email, 'name' => $transfer_info->creator_email);
      $subject = "[CRM] GĐTT $transfer_info->from_branch_name đã từ chối yêu cầu chuyển trung tâm của bé $transfer_info->student_name";
      $comment = $transfer_info->from_approve_comment != '' ? $transfer_info->from_approve_comment : "Không có lý do cụ thể.";
      $body = "<p>Kính gửi: Phòng CS</p>
					<p>Hệ thống CRM xin thông báo: Yêu cầu <strong>chuyển trung tâm</strong> của bé <strong>$transfer_info->student_name</strong> $transfer_info->from_branch_name chuyển trung tâm sang $transfer_info->to_branch_name đã bị từ chối phê duyệt!</p>
					<p>Lý do từ chối: $comment</p>
					<p>Anh / Chị vui lòng đăng nhập vào hệ thống CRM tại địa chỉ: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết.</p>
          <p>Trân trọng cảm ơn!</p>";
        $mail->sendSingleMail($to, $subject, $body,[$arr_mail_from->gdtt,$arr_mail_from->cskh]);
    }elseif($transfer_info->status==4){
      $to = array('address' => $arr_mail_to->gdtt, 'name' => $arr_mail_to->gdtt);
      $subject = "[CRM] Yêu cầu phê duyệt chuyển trung tâm của bé $transfer_info->student_name";
      $body = "<p>Kính gửi:  GĐTT $transfer_info->to_branch_name</p>
          <p>Hệ thống CRM xin thông báo: Anh/chị đã nhận được yêu cầu <strong>chuyển trung tâm</strong> của bé <strong>$transfer_info->student_name</strong> $transfer_info->from_branch_name chuyển trung tâm sang $transfer_info->to_branch_name</p>
          <p>Nội dung: $transfer_info->note</p>
          <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM để phê duyệt yêu cầu.</p>
          <p>Hoặc truy cập link: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết yêu cầu</p>
          <p>Trân trọng cảm ơn!</p>";
      $mail->sendSingleMail($to, $subject, $body,[$arr_mail_to->cskh]);
    }elseif($transfer_info->status==3){
      $to = array('address' => $arr_mail_from->gdtt, 'name' => $arr_mail_from->gdtt);
      $subject = "[CRM] GĐTT đã từ chối yêu cầu chuyển trung tâm của bé $transfer_info->student_name";
      $comment = $transfer_info->to_approve_comment != '' ? $transfer_info->to_approve_comment : "Không có lý do cụ thể.";
      $body = "<p>Kính gửi:  GĐTT $transfer_info->from_branch_name</p>
          <p>Hệ thống CRM xin thông báo: Yêu cầu <strong>chuyển trung tâm</strong> của bé <strong>$transfer_info->student_name</strong> $transfer_info->from_branch_name chuyển trung tâm sang $transfer_info->to_branch_name đã bị từ chối phê duyệt!</p>
          <p>Lý do từ chối: $comment</p>
          <p>Anh / Chị vui lòng đăng nhập vào hệ thống CRM tại địa chỉ: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết.</p>
          <p>Trân trọng cảm ơn!</p>";
      $mail->sendSingleMail($to, $subject, $body,[$arr_mail_to->gdtt,$arr_mail_to->cskh,$arr_mail_from->cskh,$transfer_info->creator_email]);
    }elseif($transfer_info->status==5){
      $to = array('address' => 'ketoan@cmsedu.vn', 'name' => 'ketoan@cmsedu.vn');
      $subject = "[CRM] Yêu cầu phê duyệt chuyển trung tâm của bé $transfer_info->student_name";
      $body = "<p>Kính gửi:  Kế toán HO</p>
          <p>Hệ thống CRM xin thông báo: Anh/chị đã nhận được yêu cầu <strong>chuyển trung tâm</strong> của bé <strong>$transfer_info->student_name</strong> $transfer_info->from_branch_name chuyển trung tâm sang $transfer_info->to_branch_name</p>
          <p>Nội dung: $transfer_info->note</p>
          <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM để phê duyệt yêu cầu.</p>
          <p>Hoặc truy cập link: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết yêu cầu</p>
          <p>Trân trọng cảm ơn!</p>";
      $mail->sendSingleMail($to, $subject, $body);
    }elseif($transfer_info->status==7){
      $to = array('address' => $arr_mail_from->gdtt, 'name' => $arr_mail_from->gdtt);
      $subject = "[CRM] Kế toán HO đã từ chối yêu cầu chuyển trung tâm của bé $transfer_info->student_name";
      $comment = $transfer_info->accounting_approve_comment != '' ? $transfer_info->accounting_approve_comment : "Không có lý do cụ thể.";
      $body = "<p>Kính gửi:  GĐTT $transfer_info->from_branch_name</p>
          <p>Hệ thống CRM xin thông báo: Yêu cầu <strong>chuyển trung tâm</strong> của bé <strong>$transfer_info->student_name</strong> $transfer_info->from_branch_name chuyển trung tâm sang $transfer_info->to_branch_name đã bị từ chối phê duyệt!</p>
          <p>Lý do từ chối: $comment</p>
          <p>Anh / Chị vui lòng đăng nhập vào hệ thống CRM tại địa chỉ: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết.</p>
          <p>Trân trọng cảm ơn!</p>";
      $mail->sendSingleMail($to, $subject, $body,['ketoan@cmsedu.vn',$arr_mail_to->gdtt,$arr_mail_to->cskh,$arr_mail_from->cskh,$transfer_info->creator_email]);
    }elseif($transfer_info->status==6){
      $to = array('address' => $arr_mail_from->gdtt, 'name' => $arr_mail_from->gdtt);
      $subject = "[CRM] Kế toán HO đã đồng ý tiếp nhận yêu cầu chuyển trung tâm của bé $transfer_info->student_name";
      $body = "<p>Kính gửi: GĐTT $transfer_info->from_branch_name</p>
					<p>Hệ thống CRM xin thông báo: Yêu cầu <strong>chuyển trung tâm</strong> của bé <strong>$transfer_info->student_name</strong>  $transfer_info->from_branch_name chuyển trung tâm sang $transfer_info->to_branch_name đã được tiếp nhận và phê duyệt!</p>
					<p>Anh / Chị vui lòng đăng nhập vào hệ thống CRM tại địa chỉ: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết.</p>
          <p>Trân trọng cảm ơn!</p>";
      $mail->sendSingleMail($to, $subject, $body,['ketoan@cmsedu.vn',$arr_mail_to->gdtt,$arr_mail_to->cskh,$arr_mail_from->cskh,$transfer_info->creator_email]);
    }
  }
  public static function callCyberContract($contract_id, $user_id,$act){
    $query = "SELECT c.created_at, c.id, (SELECT accounting_id FROM branches WHERE id = c.branch_id) AS branch_accounting_id,
                      s.accounting_id AS student_accounting_id,
                      s.gud_name1 AS parent,
                      c.bill_info,c.note,c.ref_code,
                      t.accounting_id AS tuition_fee_accounting_id,
                      t.receivable AS tuition_fee_receivable,
                      c.total_sessions,
                      c.tuition_fee_price,
                      t.discount AS tuition_fee_discount,
                      c.must_charge AS tien_cl,
                      c.debt_amount,
                      c.total_discount,
                      s.date_of_birth,
                      c.start_date,
                      (SELECT accounting_id FROM users WHERE id = c.ec_id) AS sale_accounting_id,
                      c.sibling_discount,
                      c.discount_value,
                      c.coupon,
                      c.bonus_sessions,
                      c.bonus_amount,
                      c.accounting_id
              FROM contracts c LEFT JOIN students s ON c.student_id = s.id LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
              WHERE c.id = $contract_id AND s.status > 0 ";
    $res = u::first($query);
    $cyberAPI = new CyberAPI();
    $res = $cyberAPI->createContract($res, $user_id,0,$act);
  }
}
