<?php

namespace App\Models;

use App\Http\Controllers\RegistersController;
use Illuminate\Http\Request;
use App\Http\Controllers\LMSAPIController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;
use stdClass;

class ClassTransfer extends Model
{
  private $BRANCH_TRANSFER_TYPE = 1;
  private $CLASS_TRANSFER_TYPE = 0;
  private $DELETED = 5;
  private $WAITING_FROM = 0; //chờ bên chuyển duyệt
  private $WAITING_TO = 1; // chờ bên nhận duyệt
  private $WAITING_CASHIER = 2;
  private $APPROVED = 3;
  private $CANCELED_FROM = 4;
  private $CANCELED_TO = 5;
  private $CANCELED_CASHIER = 6;
  private $BRANCH_TRANSFER_CONTRACT_TYPE = 5;
  private $CLASS_TRANSFER_CONTRACT_TYPE = 6;
  private $CLASS_TRANSFER_TRIAL_CONTRACT_TYPE = 0;

  private $OM = 56;
  private $CM = 55;
  private $EC = 68;
  private $EC_LEADER = 69;
  private $GDTT = 686868;
  private $GDV = 7777777;

  protected $table = 'class_transfer';

  public function getList($request)
  {
    $conditions = $this->filter($request);

    if ($conditions) {
      $limit = $this->limit($request);
      $query = "
                SELECT
                    clt.id AS id,
                    clt.`student_id` AS student_id,
                    clt.`from_approved_at` AS from_approved_at,
                    clt.`to_approved_at` AS to_approved_at,
                    clt.`status` AS `status`,
                    clt.`created_at` AS created_at,
                    s.`accounting_id` AS accounting_id,
                    s.`name` AS student_name,
                    s.`crm_id` AS lms_id,
                    c.`cls_name` AS class_name,
                    tc.`cls_name` AS to_class_name,
                    clt.transfer_date
                FROM
                    class_transfer AS clt
                    LEFT JOIN students AS s ON clt.`student_id` = s.`id`
                    LEFT JOIN classes AS c ON clt.`from_class_id` = c.`id`
                    LEFT JOIN classes AS tc ON clt.`to_class_id` = tc.`id`
                $conditions
                ORDER BY clt.created_at DESC
                $limit
            ";

      $result = DB::select(DB::raw($query));

      $total = 0;
      if (!empty($result)) {
        $total = $this->countRecords($conditions);
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
        "items" => $result,
        "pagination" => $page
      ];
    } else {
      $res = [];
    }
    return $res;
  }

  public function validate($data)
  {
    $result_code = APICode::SUCCESS;
    return $result_code;
  }

  public function create($data)
  {
    $res = (Object)[
      'code' => APICode::SERVER_CONNECTION_ERROR,
      'message' => ''
    ];
    $this->student_id = $data->student_id;
    $this->type = $this->CLASS_TRANSFER_TYPE;
    $this->note = $data->note;
    $this->transfer_date = $data->transfer_date;
    $this->status = $this->WAITING_FROM;
    $this->creator_id = $data->users_data->id;
    $this->created_at = date('Y-m-d H:i:s');
    $this->amount_transferred = $data->amount_transferred;
    $this->amount_exchange = $data->amount_exchange;
    $this->session_transferred = $data->session_transferred;
    $this->session_exchange = $data->session_exchange;
    $this->from_branch_id = $data->from_branch_id;
    $this->to_branch_id = $data->to_branch_id;
    $this->from_product_id = $data->from_product_id;
    $this->to_product_id = $data->to_product_id;
    $this->from_program_id = $data->from_program_id;
    $this->to_program_id = $data->to_program_id;
    $this->from_class_id = $data->from_class_id;
    $this->to_class_id = $data->to_class_id;
    $this->contract_id = $data->contract_id;
    $this->semester_id = $data->semester_id;
    $this->meta_data = json_encode($data->meta_data);

    $this->attached_file = '';
    if ($data->attached_file) {
      $this->attached_file = ada()->upload($data->attached_file);
    }

    if ($this->save()) {
      $res = $this->approve($this->id, $data, 1);
    }

    return $res;
  }

  public function getBranchIds($branches)
  {
    $branch_ids = [];
    foreach ($branches as $branch) {
      $branch_ids[] = $branch->branch_id;
    }
    return $branch_ids;
  }

  public function getClassTransfersByUserID($student_id)
  {
    $query = "
                SELECT
                    clt.id AS id,
                    b.`name` AS from_branch,
                    s.accounting_id AS accounting_id,
                    s.crm_id AS lms_id,
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
                    clt.amount_exchange as amount_exchange,
                    clt.session_exchange as session_exchange,
                    clt.amount_transferred as amount_transferred,
                    clt.session_transferred as session_transferred,
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
                    class_transfer AS clt
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
                    clt.`status` < 5
                    AND clt.type = $this->CLASS_TRANSFER_TYPE
                    AND clt.student_id = $student_id
            ORDER BY clt.created_at DESC
        ";
    $data = DB::select(DB::raw($query));
    return $data;
  }

  public function getDetailByTransferID($id)
  {
    $query = "
            SELECT
                    s.`accounting_id` AS accounting_id,
                    s.`name` AS student_name,
                    s.`crm_id` AS lms_id,
                    s.`nick` AS nick,
                    b.`name` AS from_branch,
                    p.`name` AS from_product,
                    p2.`name` AS to_product,
                    pr.`name` AS from_program,
                    pr2.`name` AS to_program,
                    c.`cls_name` AS class_name,
                    tc.`cls_name` AS to_class_name,
                    clt.note as note,
                    clt.meta_data as meta_data,
                    clt.amount_exchange as amount_exchange,
                    clt.session_exchange as session_exchange,
                    clt.amount_transferred as amount_transferred,
                    clt.session_transferred as session_transferred,
                    clt.transfer_date as transfer_date,
                    se.name as semester,
                    clt.attached_file AS attached_file
                FROM
                    class_transfer AS clt
                    LEFT JOIN branches AS b ON clt.`from_branch_id` = b.`id`
                    LEFT JOIN classes AS c ON clt.`from_class_id` = c.`id`
                    LEFT JOIN classes AS tc ON clt.`to_class_id` = tc.`id`
                    LEFT JOIN products AS p ON clt.`from_product_id` = p.`id`
                    LEFT JOIN products AS p2 ON clt.`to_product_id` = p2.`id`
                    LEFT JOIN programs AS pr ON clt.`from_program_id` = pr.`id`
                    LEFT JOIN programs AS pr2 ON clt.`to_program_id` = pr2.`id`
                    LEFT JOIN users AS u ON clt.creator_id = u.id
                    LEFT JOIN users AS u2 ON clt.from_approver_id = u2.id
                    LEFT JOIN users AS u3 ON clt.to_approver_id = u3.id
                    LEFT JOIN students as s on clt.student_id = s.id
                    LEFT JOIN semesters as se on clt.semester_id = se.id
                WHERE clt.id = $id
        ";

    $data = DB::select(DB::raw($query));
    if (!empty($data)) {
      return $data[0];
    } else {
      return $data;
    }
  }

  public function getClassTransferRequests($request)
  {
    $conditions = [];
    $branch_ids = $this->getBranchIds($request->users_data->roles_detail);

    $condition_string = '';
    if (!empty($branch_ids)) {
      $branch_ids_string = implode(',', $branch_ids);
      $conditions[] = "(clt.from_branch_id IN ($branch_ids_string) AND clt.status=$this->WAITING_FROM) OR (clt.to_branch_id IN ($branch_ids_string) AND clt.status=$this->WAITING_TO)";
    }

    if (!empty($conditions)) {
      $condition_string = ' AND (' . implode(' AND ', $conditions) . ')';
    }
    $query = "
                SELECT
                    clt.id AS id,
                    clt.`student_id` AS student_id,
                    clt.`from_approved_at` AS from_approved_at,
                    clt.`to_approved_at` AS to_approved_at,
                    clt.`status` AS `status`,
                    clt.`created_at` AS created_at,
                    c.`cls_name` AS class_name,
                    tc.`cls_name` AS to_class_name,
                    clt.note as note,
                    clt.from_approve_comment as from_approve_comment,
                    clt.to_approve_comment as to_approve_comment,
                    u.username as creator,
                    u2.username as from_approver,
                    s.`accounting_id` AS accounting_id,
                    s.`name` AS student_name,
                    s.`crm_id` AS lms_id
                FROM
                    class_transfer AS clt
                    LEFT JOIN classes AS c ON clt.`from_class_id` = c.`id`
                    LEFT JOIN classes AS tc ON clt.`to_class_id` = tc.`id`
                    LEFT JOIN users AS u ON clt.creator_id = u.id
                    LEFT JOIN users AS u2 ON clt.from_approver_id = u2.id
                    LEFT JOIN students AS s ON clt.student_id = s.id
                WHERE
                    clt.`status` < 2
                    AND clt.type = $this->CLASS_TRANSFER_TYPE
                    $condition_string
            ORDER BY clt.created_at DESC
        ";
    $data = DB::select(DB::raw($query));
    return $data;
  }

  public function validateApproval($tuition_transfer_id)
  {
    return true;
  }

  public function deny($id, $comment, $approver_id)
  {
    $transfer = $this->find($id);

    $transfer->status = $this->CANCELED_FROM;
    $transfer->from_approve_comment = $comment;
    $transfer->from_approved_at = date('Y-m-d H:i:s');
    $transfer->from_approver_id = $approver_id;

    if ($transfer->save()) {

      $this->sendDenyMail($transfer);

      return APICode::SUCCESS;
    } else {
      return false;
    }
  }

  public function approve($id, $request, $mode = 0)
  {
    $res = (Object)[
      'code' => APICode::SERVER_CONNECTION_ERROR,
      'message' => ''
    ];

    $approver = $request->users_data;
    $transfer = $this->find($id);

    if($transfer->status == $this->WAITING_FROM){
      $transfer->status = $this->APPROVED;
      $transfer->from_approved_at = date('Y-m-d H:i:s');
      $transfer->from_approver_id = $approver->id;

      if ($transfer->save()) {
        $res = $this->processContract($transfer, $request, $mode);
        $lsmAPI = new LMSAPIController();
        $lsmAPI->updateStudentLMS($transfer->student_id,$transfer->from_class_id);
        
        $sms_info = u::first("SELECT s.gud_mobile1,s.name AS student_name,
          (SELECT name FROM products WHERE id= ct.from_product_id) AS from_product_name,
          (SELECT name FROM products WHERE id= ct.to_product_id) AS to_product_name,
          (SELECT name FROM branches WHERE id= ct.to_branch_id) AS to_branch_name,
          (SELECT st.start_time FROM sessions AS s LEFT JOIN shifts AS st ON  st.id=s.shift_id WHERE s.status=1 AND s.class_id=ct.from_class_id LIMIT 1 ) AS from_class_start_time,
          (SELECT st.end_time FROM sessions AS s LEFT JOIN shifts AS st ON  st.id=s.shift_id WHERE s.status=1 AND s.class_id=ct.from_class_id LIMIT 1 ) AS from_class_end_time,
          (SELECT st.start_time FROM sessions AS s LEFT JOIN shifts AS st ON  st.id=s.shift_id WHERE s.status=1 AND s.class_id=ct.to_class_id LIMIT 1 ) AS to_class_start_time,
          (SELECT st.end_time FROM sessions AS s LEFT JOIN shifts AS st ON  st.id=s.shift_id WHERE s.status=1 AND s.class_id=ct.to_class_id LIMIT 1 ) AS to_class_end_time,
          (SELECT st.room_name FROM sessions AS s LEFT JOIN rooms AS st ON  st.id=s.room_id WHERE s.status=1 AND s.class_id=ct.to_class_id LIMIT 1 ) AS room_name
        FROM class_transfer AS ct LEFT JOIN students AS s ON s.id= ct.student_id WHERE ct.id=$id");

        $thoi_luong = (strtotime($sms_info->to_class_end_time) - strtotime($sms_info->to_class_start_time))/60;
        $text_from_class = substr($sms_info->from_class_start_time,0,5)." - ".substr($sms_info->from_class_end_time,0,5);
        $text_to_class = substr($sms_info->to_class_start_time,0,5)." - ".substr($sms_info->to_class_end_time,0,5);
        $sms_phone=$sms_info->gud_mobile1;
        /*CMS Edu TB thay đổi theo lộ trình học tập của [Tên học sinh] từ cấp độ ...... lên cấp độ ...., chi tiết như sau:
        Chương trình: ............
        Thời gian buổi học:........
        Thời lượng buổi học: ...... phút/buổi
        Phòng học: [Tên phòng học] – tại Trung tâm CMS [Tên trung tâm]
        Rất mong Quý PH cập nhật thông tin, tiếp tục đồng hành với [Tên trung tâm]. Trân trọng!
        */
        $sms_content = "CMS Edu TB thay doi theo lo trinh hoc tap cua ".$sms_info->student_name." tu cap do ".$sms_info->from_product_name." len cap do ".$sms_info->to_product_name." , chi tiet nhu sau:
        Chuong trinh: ".$sms_info->to_product_name.". 
        Thoi gian buoi hoc : ".$text_to_class.". 
        Thoi luong buoi hoc: ".$thoi_luong." phut/buoi.  
        Phong hoc: ".$sms_info->room_name." – tai ".$sms_info->to_branch_name.". 
        Rat mong Quy PH cap nhat thong tin, tiep tuc dong hanh voi ".$sms_info->to_branch_name.". Tran trong!";
        $sms =new Sms();
        $sms->sendSms($sms_phone,$sms_content);
      }
    }else{
      $res->code = APICode::PERMISSION_DENIED;
    }


    return $res;
  }

  private function processContract($transfer, $request, $mode = 0)
  {
    $resp = (Object)[
      'code' => APICode::SUCCESS,
      'message' => ''
    ];
    $model = new Contract();
    $contract_id = $transfer->contract_id;
    $contract = $model->find($contract_id);
    $newContract = new stdClass();
    $meta_data = json_decode($transfer->meta_data);

    //_____________________________test service dãn date_________________________

    $class_days = u::getClassDays($transfer->to_class_id);

    if(!empty($class_days)){
      $has_schedules = u::hasSchedules($transfer->to_class_id, $transfer->transfer_date);
      if(!$has_schedules){
        $resp->code = APICode::WRONG_PARAMS;
        $resp->message = "Lớp không có lịch học sau ngày $transfer->transfer_date. Vui lòng thông báo cho OM cập nhật trên CMS";
        $this->cancelTransfer($transfer, $resp);
      }else{
        //_____________________________kết thúc service dãn date_________________________
        $newContract->id = $contract->id;
        if ($contract->type) {
          if(in_array($contract->type, [8, 85, 86])){
            $newContract->type = 86;
          }else{
            $newContract->type = $this->CLASS_TRANSFER_CONTRACT_TYPE;
          }
        } else {
          $newContract->type = $this->CLASS_TRANSFER_TRIAL_CONTRACT_TYPE;
        }

        $newContract->payload = $contract->payload;
        $newContract->student_id = $contract->student_id;
        $newContract->relation_contract_id = NULL;

        $newContract->branch_id = $transfer->to_branch_id;
        $newContract->product_id = $transfer->to_product_id;
        $newContract->program_id = $transfer->to_program_id;

        if ($contract->type) {
          $exchange_info = u::calcTransferTuitionFee($contract->tuition_fee_id, $transfer->amount_transferred, $transfer->to_branch_id, $transfer->to_product_id);
          $newContract->tuition_fee_id = $exchange_info->receive_tuition_fee->id;
        } else {
          $newContract->tuition_fee_id = 0;
        }

        $newContract->payment_id = $contract->payment_id;
        $newContract->accounting_id = $contract->accounting_id;
        $newContract->ceo_branch_id = $contract->ceo_branch_id;
        $newContract->region_id = $contract->region_id;
        $newContract->ceo_region_id = $contract->ceo_region_id;
        $newContract->ec_id = $contract->ec_id;
        $newContract->ec_leader_id = $contract->ec_leader_id;
        $newContract->cm_id = $contract->cm_id;
        $newContract->om_id = $contract->om_id;

        $newContract->code = apax_ada_gen_contract_code();

        $newContract->receivable = $contract->receivable;
        $newContract->payment_id = $contract->payment_id;

        $newContract->total_charged = $transfer->amount_exchange;
        $newContract->must_charge = $contract->must_charge;

        $newContract->start_date = $transfer->transfer_date;
        $newContract->end_date = $meta_data->to_end_date;

        $newContract->total_sessions = $contract->total_sessions;
        $newContract->summary_sessions = $transfer->session_exchange;
        $newContract->real_sessions = $meta_data->real_sessions_received;
        $newContract->bonus_sessions = $meta_data->bonus_sessions_received;

        $newContract->total_discount = $contract->total_discount;
        $newContract->debt_amount = $contract->debt_amount;
        $newContract->passed_trial = 1;
        $newContract->status = $this->setStatus($contract->type);
        $newContract->editor_id = $request->users_data->id;
        $newContract->updated_at = date('Y-m-d H:i:s');

        $newContract->only_give_tuition_fee_transfer = 0;
        $newContract->sibling_discount = $contract->sibling_discount;
        $newContract->after_discounted_fee = $contract->after_discounted_fee;
        $newContract->discount_value = $contract->discount_value;


        $reservable_sessions_transferred = $contract->reservable_sessions - $contract->reserved_sessions;


        $newContract->reservable = ($reservable_sessions_transferred > 0) ? 1 : 0;
        $newContract->reserved_sessions = 0;
        $newContract->reservable_sessions = $reservable_sessions_transferred;
        $newContract->relation_reserved_sessions = $contract->relation_reserved_sessions + $contract->reserved_sessions;

        $newContract->info_available = $contract->info_available;
        $newContract->done_sessions = $contract->done_sessions;
        $newContract->tuition_fee_price = $contract->tuition_fee_price;

        $newContract->note = $contract->note;

        $newContract->count_recharge = $contract->count_recharge;

        $newContract->relation_left_sessions = $contract->relation_left_sessions + $meta_data->sessions_from_start_to_transfer_date;
        $newContract->action = "do_class_transfer_for_contract_$contract->id";
        $newContract->enrolment_schedule = implode(",",$class_days);
        $newContract = $this->createEnrolment($newContract, $transfer);
        $newContract->enrolment_updated_at = $transfer->transfer_date;

        $class = Classes::where('id', $transfer->to_class_id)->select(['cm_id','teacher_id'])->first();
        $res = u::updateContract($newContract);
        TermStudentUser::where('student_id', $transfer->student_id)->update([
          'cm_id' => $class->cm_id,
          'teacher_id' => $class->teacher_id
        ]);
        if ($res) {
          $resp->code = APICode::SUCCESS;
          $resp->mesage = 'Chuyển lớp thành công';
        } else {
          $resp->code = APICode::WRONG_PARAMS;
          $resp->message = 'Có lỗi xảy ra. Vui lòng thử lại';
          $this->cancelTransfer($transfer, $resp);
        }
      }
    }else{
      $resp->code = APICode::WRONG_PARAMS;
      $resp->message = 'Không thể chuyển lớp. Vui lòng cập nhật lịch học trong tuần của lớp';
      $this->cancelTransfer($transfer, $resp);
    }

    return $resp;
  }

  private function setStatus($old_type){
    return 6;
  }

  private function cancelTransfer($transfer, $resp){
    $transfer->status = $this->CANCELED_FROM;
    $transfer->from_approve_comment = $resp->message;
    $transfer->from_approved_at = date('Y-m-d H:i:s');
    $transfer->from_approver_id = 0;
    $transfer->save();
  }

  protected function getManagerInfo($branch_id)
  {
    $query = "SELECT user_id, role_id FROM term_user_branch WHERE branch_id=$branch_id AND status = 1 AND role_id > 1 AND role_id < 88888888 GROUP BY role_id";
    $res = DB::select(DB::raw($query));

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
    }

    return $info;
  }

  protected function getRegionId($branch_id)
  {
    $query = "SELECT region_id FROM branches WHERE id=$branch_id";
    $res = DB::select(DB::raw($query));
//        var_dump($res);die;
    $region_id = 0;
    if (!empty($res)) {
      $region_id = $res[0]->region_id;
    }

    return $region_id;
  }

  protected function getTuitionFeeId($tuition_fee_id, $branch_id, $product_id)
  {
    $query = "
            SELECT
                t.exchange_tuition_fee_id AS tuition_fee_id
            FROM
                tuition_fee_relation AS t
                LEFT JOIN branches AS b ON t.`zone_id` = b.`zone_id`
                LEFT JOIN tuition_fee AS te ON t.`exchange_tuition_fee_id` = te.`id`
            WHERE
                t.`tuition_fee_id`=$tuition_fee_id
                AND b.`id`=$branch_id
                AND te.`product_id` = $product_id
        ";

    $tuition_fee_id = 0;

    $res = DB::select(DB::raw($query));
    if (!empty($res)) {
      $tuition_fee_id = $res[0]->tuition_fee_id;
    }

    return $tuition_fee_id;
  }

  private function createEnrolment($contract, $transfer)
  {
    $enrolment = $contract;

    $enrolment->class_id = $transfer->to_class_id;

    $meta_data = json_decode($transfer->meta_data);

    $enrolment->enrolment_start_date = $meta_data->new_enrol_start_date;
    $enrolment->enrolment_end_date = $this->getClassEndDate($transfer->to_class_id);
    $enrolment->enrolment_updated_at = $transfer->from_approved_at;
    $enrolment->enrolment_updator_id = $transfer->from_approver_id;
    $enrolment->enrolment_real_sessions = $transfer->session_exchange;
    $enrolment->enrolment_last_date = $contract->end_date;
    $enrolment->enrolment_type = 1;

    $holidays = u::getPublicHolidays($enrolment->class_id);
    $class_dates = u::getClassDays($enrolment->class_id);

    $enrolment->start_cjrn_id = u::getStartCjrnId($transfer->to_class_id, $contract->start_date);
    $enrolment->enrolment_left_sessions = u::calSessions($enrolment->start_date, $enrolment->end_date, $holidays, $class_dates)->total;
    $enrolment->cstd_id = null;

    return $enrolment;
  }

  private function getClassEndDate($class_id)
  {
    $query = "SELECT cls_enddate FROM classes WHERE id=$class_id";
    $res = DB::select(DB::raw($query));

    if (!empty($res)) {
      return $res[0]->cls_enddate;
    } else {
      return '';
    }
  }

  public function suggestStudent($key, $branch_id)
  {
    $search_condition = "";
    if ($key && $key !== '_') {
      if (is_numeric($key)) {
        $search_condition .= " AND (s.crm_id LIKE '%$key%')";
      } else {
        $search_condition .= " AND (s.name LIKE '%$key%' OR s.crm_id LIKE '%$key%' OR s.accounting_id LIKE '%$key%')";
      }
    }

    $query = "SELECT 
            c.id AS contract_id,
            s.`id` AS student_id,
            s.`crm_id` AS lms_id,
            c.branch_id,
            IF(c.product_id, c.product_id, (SELECT product_id FROM term_program_product WHERE program_id = c.program_id)) AS product_id,
            c.program_id, 
            c.class_id,
            s.accounting_id AS accounting_id,
            se.id AS semester_id,		
            s.`name` AS student_name,
            s.`nick` AS nick,
            IF(s.type > 0, s.type, IF(c.type = 8, 1, 0)) as student_type,	
            (SELECT `name` FROM products WHERE id = c.product_id) AS product_name,
            (SELECT `name` FROM programs WHERE id = c.program_id) AS program_name,
            t.name AS tuition_fee_name,
            t.receivable AS tuition_fee_receivable,
            t.session AS tuition_fee_session,
            (SELECT `cls_name` FROM classes WHERE id = c.class_id) AS class_name,
            c.`type` AS contract_type,
            c.`payload` AS payload,
            c.`must_charge` AS must_charge,
            c.`real_sessions`,
            c.bonus_sessions,
            c.summary_sessions,
            IF(s.type=1, c.must_charge, IF(c.type IN (1,2),IF(c.total_charged > 0, c.total_charged,0), c.total_charged)) AS total_charged,
            c.reserved_sessions AS reserved_sessions,
            c.`enrolment_start_date` AS start_date,
            c.`enrolment_last_date` AS end_date,
            1 AS has_enrolment,
            c.tuition_fee_id,
            s.waiting_status
        FROM
            contracts AS c
            LEFT JOIN students AS s ON c.`student_id` = s.`id`
            LEFT JOIN classes AS cls ON c.`class_id` = cls.`id`
            LEFT JOIN semesters as se ON cls.semester_id = se.id
            LEFT JOIN sessions AS ss ON c.class_id = ss.class_id
            LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
        WHERE c.id IN (SELECT * FROM 
            (SELECT 
                c.id AS contract_id
            FROM
                contracts AS c
                LEFT JOIN students AS s ON c.`student_id` = s.`id`
            WHERE
                c.branch_id = $branch_id 
                AND c.student_id NOT IN (SELECT student_id FROM class_transfer WHERE status < 3 GROUP BY student_id)
                AND c.student_id NOT IN (SELECT from_student_id FROM tuition_transfer WHERE status < 2 GROUP BY from_student_id)
                AND c.student_id NOT IN (SELECT to_student_id FROM tuition_transfer WHERE status < 2 GROUP BY to_student_id)
                AND c.student_id NOT IN (SELECT student_id FROM reserves WHERE status < 2 GROUP BY student_id)
                AND c.id NOT IN (SELECT id FROM contracts WHERE relation_contract_id = c.id)
                AND ( 
                    c.`type` IN (3, 5, 6, 85, 86, 10)
                    OR (c.type IN (4, 8) AND c.summary_sessions > 0) 
                    OR (c.`type` IN (1, 2) AND (c.`debt_amount` = 0 AND c.`summary_sessions` > 0))
                    OR (s.type = 1)
                )
                AND c.debt_amount = 0
                AND c.student_id NOT IN (
                    SELECT t.student_id AS id
                    FROM (
                        SELECT
                        c.student_id, COUNT(c.id) AS count_enroll
                        FROM contracts c
                        WHERE c.status > 0 AND c.status < 7 AND c.class_id IS NOT NULL AND c.`branch_id` = $branch_id GROUP BY c.student_id
                    ) AS t
                    WHERE t.count_enroll > 1
                )
                AND c.`enrolment_last_date` > CURDATE()
                AND c.status > 0 AND c.status < 7
                $search_condition GROUP BY c.id LIMIT 10
            ) temp
        ) GROUP BY c.id";

    $res = DB::select(DB::raw($query));
    if (!empty($res)) {
      $contract_ids = [];
      $class_ids = [];
      foreach ($res as &$r) {
        $r->contract_name = $r->lms_id . ' - ' . $r->student_name . ' - ' . $r->product_name . ' - ' . $r->tuition_fee_name;
        $contract_ids[] = $r->contract_id;
        $class_ids[] = $r->class_id;
      }

      $reserveModel = new Reserve();

      $reserved_dates = $reserveModel->getReservedDates($contract_ids);
      $class_days = u::getMultiClassDays($class_ids);

      foreach ($res as &$r) {
        if (isset($reserved_dates[$r->contract_id])) {
          $r->reserved_dates = $reserved_dates[$r->contract_id];
        } else {
          $r->reserved_dates = [];
        }

        if(isset($class_days[$r->class_id])){
          $r->class_days = $class_days[$r->class_id];
        }else{
          $r->class_days = u::getDefaultClassDays($r->product_id);
        }
      }
    }
    return $res;
  }

  public function suggestTrial($key, $branch_id)
  {
    $search_condition = "";
    if ($key && $key !== '_') {
      if (is_numeric($key)) {
        $search_condition .= " AND (s.crm_id LIKE '%$key%')";
      } else {
        $search_condition .= " AND (s.name LIKE '%$key%' OR s.crm_id LIKE '%$key%')";
      }
    }

    $query = "SELECT 
            c.id AS contract_id,
            s.`id` AS student_id,
            s.`crm_id` AS lms_id,
            c.branch_id,
            IF(c.product_id, c.product_id, (SELECT product_id FROM term_program_product WHERE program_id = c.program_id)) AS product_id,
            c.program_id, 
            c.class_id,
            s.accounting_id AS accounting_id,
            se.id AS semester_id,		
            s.`name` AS student_name,
            s.`nick` AS nick,
            IF(s.type > 0, s.type, IF(c.type = 8, 1, 0)) as student_type,	
            (SELECT `name` FROM products WHERE id = c.product_id) AS product_name,
            (SELECT `name` FROM programs WHERE id = c.program_id) AS program_name,
            (SELECT `name` FROM tuition_fee WHERE id = c.tuition_fee_id) AS tuition_fee_name,
            (SELECT `cls_name` FROM classes WHERE id = c.class_id) AS class_name,
            c.`type` AS contract_type,
            c.`payload` AS payload,
            c.`must_charge` AS must_charge,
            IF(c.passed_trial = 1, c.`real_sessions`, c.`real_sessions` - 3) AS real_sessions,
            IF(s.type=1, c.must_charge, IF(c.type IN (1,2),IF(c.total_charged > 0, c.total_charged,0), c.total_charged)) AS total_charged,
            c.reserved_sessions AS reserved_sessions,
            c.`enrolment_start_date` AS start_date,
            c.`enrolment_last_date` AS end_date,
            1 AS has_enrolment,
            c.tuition_fee_id,
            s.waiting_status
        FROM
            contracts AS c
            LEFT JOIN students AS s ON c.`student_id` = s.`id`
            LEFT JOIN classes AS cls ON c.`class_id` = cls.`id`
            LEFT JOIN semesters as se ON cls.semester_id = se.id
            LEFT JOIN sessions AS ss ON c.class_id = ss.class_id
        WHERE c.id IN (SELECT * FROM 
            (SELECT 
                c.id AS contract_id
            FROM
                contracts AS c
                LEFT JOIN students AS s ON c.`student_id` = s.`id`
            WHERE
                c.branch_id = $branch_id 
                AND c.student_id NOT IN (SELECT student_id FROM class_transfer WHERE status < 2 GROUP BY student_id)
                AND c.student_id NOT IN (SELECT from_student_id FROM tuition_transfer WHERE status = 0 GROUP BY from_student_id)
                AND c.student_id NOT IN (SELECT student_id FROM pendings WHERE status=0 GROUP BY student_id)
                AND c.student_id NOT IN (SELECT student_id FROM reserves WHERE status = 0 GROUP BY student_id)
                AND c.id NOT IN (SELECT id FROM contracts WHERE relation_contract_id = c.id)
                AND c.`type` = 0
                AND c.student_id NOT IN (
                    SELECT t.student_id AS id
                    FROM (
                        SELECT
                        c.student_id, COUNT(c.id) AS count_enroll
                        FROM contracts c
                        WHERE c.status > 0 AND c.status < 7 AND c.class_id IS NOT NULL AND c.`branch_id` = $branch_id GROUP BY c.student_id
                    ) AS t
                    WHERE t.count_enroll > 1
                )
                AND c.`enrolment_last_date` >= CURDATE()
                AND c.status > 0 AND c.status < 7
                $search_condition GROUP BY c.id LIMIT 10
            ) temp
        ) GROUP BY c.id";

    $res = DB::select(DB::raw($query));
    if (!empty($res)) {
      $class_ids = [];
      foreach ($res as &$r) {
        $r->contract_name = $r->lms_id . ' - ' . $r->student_name . ' - ' . $r->product_name . ' - ' . $r->tuition_fee_name;
        $r->reserved_dates = [];
        $class_ids[] = $r->class_id;
      }

      $class_days = u::getMultiClassDays($class_ids);

      foreach ($res as &$r) {
        if(isset($class_days[$r->class_id])){
          $r->class_days = $class_days[$r->class_id];
        }else{
          $r->class_days = u::getDefaultClassDays($r->product_id);
        }
      }
    }
    return $res;
  }

  public function getPrintData($transfer_id){
    $query = "SELECT (SELECT `name` FROM branches WHERE id = ct.from_branch_id) AS branch_name,
                s.gud_name1,
                s.gud_mobile1,
                s.gud_email1,
                s.name,
                s.gender,
                s.date_of_birth,
                (SELECT cls_name FROM classes WHERE id = ct.from_class_id) AS from_class_name,
                (SELECT cls_name FROM classes WHERE id = ct.to_class_id) AS to_class_name,
                ct.meta_data,
                ct.note,
                ct.session_exchange,
                (SELECT `name` FROM products WHERE id=ct.from_product_id) AS product_name
              FROM
                class_transfer ct
                LEFT JOIN contracts c ON ct.contract_id = c.id
                LEFT JOIN students s ON ct.student_id = s.id
              WHERE
                ct.id = $transfer_id
                AND ct.type = 0 AND s.status >0 
              ";
    return u::first($query);
  }

  public function getClassExtendInfo($start, $end, $class_id = 0)
  {
    if ($class_id) {

      $query = "
                SELECT COUNT(*) AS current_students, t.class_id, c.max_students
                FROM (
                    SELECT e.`class_id`, COUNT(e.`class_id`) 
                    FROM `contracts` AS e 
                    WHERE 
                        e.status > 0 AND e.status < 7
                        AND e.class_id IN ($class_id)
                    GROUP BY e.`class_id`, e.`student_id`
                ) AS t LEFT JOIN classes AS c ON t.class_id = c.id
                GROUP BY t.class_id
            ";

      $res = DB::select(DB::raw($query));

      if (!empty($res)) {
        return $res[0];
      } else {
        return (Object)[
          'class_id' => $class_id,
          'current_students' => 0,
          'max_students' => 12
        ];
      }

    } else {
      return false;
    }
  }

  private function filter($request)
  {
    if (isset($request->filter)) {
      try {
        $filter = json_decode($request->filter);
      } catch (\Exception $e) {
        return "";
      }

      $conditions = [];
//      $conditions[] = "clt.`status` < 5
//                            AND clt.type = $this->CLASS_TRANSFER_TYPE
//                            AND clt.`id` IN (SELECT MAX(`id`) FROM `class_transfer` WHERE `type` = 0 GROUP BY `student_id`)
//                            ";
      $conditions[] = "clt.`status` < 5 AND clt.type = $this->CLASS_TRANSFER_TYPE ";
      if (isset($filter->lms_accounting_id) && $filter->lms_accounting_id) {
        $value = trim($filter->lms_accounting_id);
        $conditions[] = "(s.accounting_id LIKE '%$value%' OR s.crm_id LIKE '%$value%')";
      }

      $student_name = isset($filter->student_name) ? trim($filter->student_name) : '';
      if ($student_name) {
        $student_name = strtoupper($student_name);
        $conditions[] = "s.name LIKE '%$student_name%'";
      }

      if (isset($filter->branch_id) && is_numeric($filter->branch_id)) {
        $branch_id = (int)$filter->branch_id;
        if ($branch_id !== 0) {
          $conditions[] = "clt.from_branch_id = " . $filter->branch_id;
        } else {
          $branch_ids = u::getBranchIds($request->users_data);
          if (!empty($branch_ids)) {
            $branch_ids_string = implode(',', $branch_ids);
            $conditions[] = "clt.from_branch_id IN ($branch_ids_string)";
          }
        }
      } else {
        $branch_ids = u::getBranchIds($request->users_data);
        if (!empty($branch_ids)) {
          $branch_ids_string = implode(',', $branch_ids);
          $conditions[] = "clt.from_branch_id IN ($branch_ids_string)";
        }
      }

      $statuses = [
        $this->WAITING_FROM,
        $this->WAITING_TO,
        $this->APPROVED,
        $this->CANCELED_FROM
      ];

      if (isset($filter->status) && is_numeric($filter->status)) {
        $status = (int)$filter->status;
        if (in_array($status, $statuses)) {
          $conditions[] = "clt.status = $status";
        }
      }

      if (!empty($conditions)) {
        return " WHERE " . implode(" AND ", $conditions);
      } else {
        return "";
      }
    } else {
      return "";
    }
  }

  private function limit($request)
  {
    $limit = '';
    $pagination = json_decode($request->pagination);
    if ($pagination->cpage && $pagination->limit) {
      $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
      $limit .= " LIMIT $offset, $pagination->limit";
    }

    return $limit;
  }

  private function countRecords($conditions)
  {
    $query = "
                SELECT 
                    COUNT(t.id) AS count_item
                FROM (
                    SELECT
                        clt.id AS id
                    FROM
                        class_transfer AS clt
                        LEFT JOIN students AS s ON clt.`student_id` = s.`id`
                    $conditions
                    GROUP BY clt.student_id
                ) AS t
            ";
    $res = DB::select(DB::raw($query));
    if (!empty($res)) {
      return $res[0]->count_item;
    } else {
      return 0;
    }
  }

  private function sendDenyMail($transfer)
  {
    $creator = u::query("SELECT u.`full_name`, u.id, u.`email`, r.`name` AS role_name
                                    FROM 
                                        users AS u
                                        LEFT JOIN term_user_branch AS t ON u.id = t.`user_id`
                                        LEFT JOIN roles AS r ON t.`role_id` = r.`id`
                                    WHERE
                                        u.id = $transfer->creator_id
                                    GROUP BY u.id");

    $student = u::first("SELECT s.`name`, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $transfer->student_id");

    if (!empty($creator)) {
      if (APP_ENV == 'staging') {
        $creator = [
          (Object)[
            "email" => Mail::STAGING_EMAIL,
            "full_name" => Mail::STAGING_FULL_NAME
          ]
        ];
      } elseif (APP_ENV == 'develop') {
        $creator = [
          (Object)[
            "email" => Mail::DEVELOP_EMAIL,
            "full_name" => Mail::DEVELOP_FULL_NAME
          ]
        ];
      }
      foreach ($creator as $item) {
        $mail = new Mail();

        $to = [
          'address' => $item->email,
          'name' => $item->full_name
        ];

        $subject = "[CRM] Từ chối chuyển lớp của bé $student->name";
        $body = "
                    <p>Kính gửi: Phòng CSO</p>
                    
                    <p>Hệ thống CRM xin thông báo: Yêu cầu <strong>chuyển lớp</strong> của bé <strong>$student->name</strong> [$student->branch_name] đã bị từ chối phê duyệt</p>
                    
                    <p>Lý do từ chối: $transfer->from_approve_comment</p>
                    
                    <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM để xem chi tiết.</p>
                    
                    <p>Trân trọng cảm ơn!</p>
                ";
        $mail->sendSingleMail($to, $subject, $body);
      }
    }
  }

  public function getAllInfo($branch_id)
  {
    $res = [
      'semesters' => [],
      'classes' => []
    ];
    $semesters = $this->getSemesters();
    if (!empty($semesters)) {
      $products = $this->getProducts();
      if (!empty($products)) {
        $product_ids = [];
        foreach ($products as $product) {
          $product_ids[] = $product->id;
        }

        $product_ids_string = implode(',', $product_ids);

        $semester_ids = [];
        foreach ($semesters as $semester) {
          $semester_ids[] = $semester->id;
        }

        $semester_ids_string = implode(',', $semester_ids);

        $query = "SELECT 
                        pr.`name` AS `name`,
                        pr.`id` AS id,
                        pr.semester_id as semester_id,
                        t.`product_id` as product_id
                    FROM
                        term_program_product AS t
                        LEFT JOIN products AS p ON t.`product_id` = p.`id`
                        LEFT JOIN programs AS pr ON t.`program_id` = pr.`id`
                    WHERE
                        p.`id` IN ($product_ids_string)
                        AND t.`status` = 1
                        AND pr.semester_id IN ($semester_ids_string)
                        AND pr.branch_id = $branch_id
                        ";
        $programs = DB::select(DB::raw($query));

        if (!empty($programs)) {
          $program_ids = [];
          foreach ($semesters as $semester) {
            $product_array = [];
            foreach ($products as $product) {
              $program_array = [];
              foreach ($programs as $key => $program) {
                if ($program->semester_id == $semester->id && $program->product_id == $product->id) {
                  $program_array['program' . $program->id] = [
                    'id' => $program->id,
                    'name' => $program->name
                  ];
                  $program_ids[] = $program->id;
                  unset($programs[$key]);
                }
              }
              $product_array['product' . $product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'programs' => $program_array
              ];
            }

            $res['semesters']['semester' . $semester->id] = [
              'id' => $semester->id,
              'name' => $semester->name,
              'product_id' => $semester->product_id,
              'products' => $product_array
            ];
          }
          if (!empty($program_ids)) {
            $program_ids_string = implode(',', $program_ids);
            $query = "
                                SELECT 
                                    c.`cls_name` AS class_name,
                                    c.id AS id,
                                    s.`class_day` AS class_day,
                                    c.program_id as program_id,
                                    (SELECT cjrn_classdate FROM schedules WHERE c.id = class_id AND status = 1 AND cjrn_classdate > CURRENT_DATE() ORDER BY cjrn_classdate ASC LIMIT 1) as nearest_day
                                FROM
                                    classes AS c
                                    LEFT JOIN sessions AS s ON c.id = s.`class_id`
                                WHERE 
                                    c.`program_id` IN ($program_ids_string)
                                    AND c.cls_iscancelled = 'no'
                                    AND c.`cls_enddate` > CURDATE()
                                    AND c.cm_id IS NOT NULL
                                    AND s.status > 0
                                    AND c.teacher_id IS NOT NULL
                                    AND c.cm_id NOT IN (SELECT user_id FROM term_user_branch WHERE role_id IN (55, 56) AND branch_id = $branch_id AND status = 0)
                                    AND c.teacher_id NOT IN (SELECT user_id FROM term_user_branch WHERE role_id = 36 AND branch_id = $branch_id AND status = 0)
                                GROUP BY c.id, s.class_day
                            ";

            $classes = DB::select(DB::raw($query));

            $class_data = [];
            if (!empty($classes)) {


              $class_day = [];
              foreach ($classes as $class) {
                if (isset($class_data[$class->program_id])) {
                  $class_data[$class->program_id][$class->id] = $class;
                } else {
                  $class_data[$class->program_id] = [$class->id => $class];
                }

                if($class->class_day !== null){
                  if (isset($class_day[$class->id])) {
                    $class_day[$class->id][] = $class->class_day;
                  } else {
                    $class_day[$class->id] = [$class->class_day];
                  }
                }
              }

              foreach ($class_data as &$p) {
                foreach ($p as &$c) {
                  if (isset($class_day[$c->id])) {
                    $c->class_days = $class_day[$c->id];
                  } else {
                    $c->class_days = [];
                  }
                }
              }
              unset($p);
              unset($c);
              $res['classes'] = $class_data;
            }

          }

        }

      }
    }

    return $res;
  }

  public function getSemesters()
  {
    $time = date('Y-m-d');
    $query = "SELECT id, `name`, product_id FROM semesters WHERE status=1 AND end_date > '$time'";
    $res = DB::select(DB::raw($query));
    return $res;
  }

  public function getProducts()
  {
    $query = "SELECT id, `name` FROM products WHERE status=1";
    $res = DB::select(DB::raw($query));
    return $res;
  }

  public function getPrograms($branch_id, $semester_id, $product_id)
  {
    $query = "SELECT 
                        pr.`name` AS `name`,
                        pr.`id` AS id
                    FROM
                        term_program_product AS t
                        LEFT JOIN products AS p ON t.`product_id` = p.`id`
                        LEFT JOIN programs AS pr ON t.`program_id` = pr.`id`
                    WHERE
                        p.`id` = $product_id
                        AND t.`status` = 1
                        AND pr.semester_id = $semester_id
                        AND pr.branch_id = $branch_id
                        AND pr.`id` NOT IN (
                            SELECT parent_id FROM programs AS p GROUP BY parent_id
                        )";
    $res = DB::select(DB::raw($query));
    return $res;
  }
  public function createMulti($data)
  {
    $res = (Object)[
      'code' => APICode::SUCCESS,
      'message' => ''
    ];
    $from_info = (object)$data->from;
    $to_info = (object)$data->to;
    $students =$data->students;
    $public_holiday = u::getPublicHolidays($from_info->class_id, 0,0);
    $from_class_days = u::getClassDays($from_info->class_id);
    $to_class_days = u::getClassDays($to_info->class_id);
    foreach($students AS $st){
      $st = (object)$st;
      if(isset($st->checked) && $st->checked){
        $tmp_class_transfer = new ClassTransfer();
        $tmp_class_transfer->student_id = $st->student_id;
        $tmp_class_transfer->type = $tmp_class_transfer->CLASS_TRANSFER_TYPE;
        $tmp_class_transfer->note = $from_info->note;
        $tmp_class_transfer->transfer_date = $from_info->transfer_date;
        $tmp_class_transfer->status = $tmp_class_transfer->WAITING_FROM;
        $tmp_class_transfer->creator_id = $data->users_data->id;
        $tmp_class_transfer->created_at = date('Y-m-d H:i:s');
        $reserved_dates = self::getReservedDates_transfer([$st->contract_id]);
        $merged_holi_day = $reserved_dates && isset($reserved_dates[$st->contract_id]) ? array_merge($public_holiday, $reserved_dates[$st->contract_id]) : $public_holiday;
        $done_sessions = u::calculatorSessions($st->enrolment_start_date, date('Y-m-d', strtotime("-1 days")), $merged_holi_day, $from_class_days);
        $done_sessions_total = isset($done_sessions->total) ? $done_sessions->total :0;
        $tmp_class_transfer->amount_transferred = $done_sessions_total < $st->real_sessions && $st->real_sessions ? ceil(($st->real_sessions-$done_sessions_total) * ($st->total_charged/$st->real_sessions)):0;
        $total_sessions = $done_sessions_total > $st->real_sessions ? 0 : $st->real_sessions - $done_sessions_total;
        $transfer_data = u::calcTransferTuitionFeeV2($st->tuition_fee_id, $tmp_class_transfer->amount_transferred, $st->branch_id, $st->product_id, (int) $total_sessions);
        $tmp_class_transfer->amount_exchange = isset($transfer_data->transfer_amount) ? $transfer_data->transfer_amount:0;
        $tmp_class_transfer->session_transferred = $st->summary_sessions - $done_sessions_total;
        $tmp_class_transfer->session_exchange = isset($transfer_data->sessions) ? ($st->summary_sessions - $st->real_sessions + $transfer_data->sessions) : ($st->summary_sessions - $done_sessions_total);
        $tmp_class_transfer->from_branch_id = $from_info->branch_id;
        $tmp_class_transfer->to_branch_id = $from_info->branch_id;
        $from_semester_info = u::first("SELECT product_id FROM semesters WHERE id = $from_info->semester_id");
        $to_semester_info = u::first("SELECT product_id FROM semesters WHERE id = $to_info->semester_id");
        $tmp_class_transfer->from_product_id = $from_semester_info->product_id;
        $tmp_class_transfer->to_product_id = $to_semester_info->product_id;
        $tmp_class_transfer->from_program_id = $from_info->program_id;
        $tmp_class_transfer->to_program_id = $to_info->program_id;
        $tmp_class_transfer->from_class_id = $from_info->class_id;
        $tmp_class_transfer->to_class_id = $to_info->class_id;
        $tmp_class_transfer->contract_id = $st->contract_id;
        $tmp_class_transfer->semester_id = $from_info->semester_id;
        $end_date = u::calculatorSessionsByNumberOfSessions($tmp_class_transfer->transfer_date, $tmp_class_transfer->session_exchange, $public_holiday,$to_class_days);
        $new_enrol_start_date = u::calculatorSessionsByNumberOfSessions($tmp_class_transfer->transfer_date, 1, $public_holiday,$to_class_days);
        $old_enrol_end_date = $done_sessions->total ? u::calculatorSessionsByNumberOfSessions($st->enrolment_start_date, $done_sessions->total, $merged_holi_day,$from_class_days)->end_date : $st->enrolment_start_date;
        $meta_data =(object)array(
            "total_session"=> $st->total_sessions,
            "total_fee"=> $st->must_charge,
            "from_start_date"=> $st->start_date,
            "from_end_date"=> $tmp_class_transfer->transfer_date,
            "to_start_date"=> $tmp_class_transfer->transfer_date,
            "to_end_date"=> $end_date->end_date,
            "sessions_from_start_to_transfer_date"=> $done_sessions->total,
            "real_sessions_from_start_to_transfer_date"=> $total_sessions,
            "bonus_sessions_from_start_to_transfer_date"=> $done_sessions->total > $st->real_sessions ? $done_sessions->total - $st->real_sessions:0,
            "real_sessions_transferred"=> $done_sessions->total > $st->real_sessions ? 0 : $st->real_sessions - $done_sessions->total,
            "bonus_sessions_transferred"=> $done_sessions->total > $st->real_sessions ? $st->bonus_sessions - ($done_sessions->total - $st->real_sessions) :  $st->bonus_sessions,
            "real_sessions_received"=> isset($transfer_data->sessions) ? $transfer_data->sessions: 0 ,
            "bonus_sessions_received"=> $done_sessions->total > $st->real_sessions ? $st->bonus_sessions - ($done_sessions->total - $st->real_sessions) :  $st->bonus_sessions,
            "amount_from_start_to_transfer_date"=> $tmp_class_transfer->amount_transferred,
            "old_enrol_end_date"=> $old_enrol_end_date,
            "new_enrol_start_date"=> $new_enrol_start_date->end_date,
            "shift_checked"=> -1,
            "enable_editor"=> 0
        );
        $tmp_class_transfer->meta_data = json_encode($meta_data);

        $tmp_class_transfer->attached_file = '';
        if ($tmp_class_transfer->save()) {
          $tmp_class_transfer->approve($tmp_class_transfer->id, $data, 1);
          $lsmAPI = new LMSAPIController();
          $lsmAPI->updateStudentLMS($tmp_class_transfer->student_id,$tmp_class_transfer->from_class_id);
        }
      }
    }
    return $res;
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
  public function checkMulti($data)
  {
    $res = (Object)[
      'code' => APICode::SUCCESS,
      'message' => ''
    ];
    $from_info = (object)$data->from;
    $to_info = (object)$data->to;
    $students =$data->students;
    $public_holiday = u::getPublicHolidays($from_info->class_id, 0,0);
    $from_class_days = u::getClassDays($from_info->class_id);
    $to_public_holiday = u::getPublicHolidays($to_info->class_id, 0,0);
    $to_class_days = u::getClassDays($to_info->class_id);
    foreach($students AS $k=> $st){
      $st = (object)$st;
      if($st->is_valid){
        $reserved_dates = self::getReservedDates_transfer([$st->contract_id]);
        $merged_holi_day = $reserved_dates && isset($reserved_dates[$st->contract_id]) ? array_merge($public_holiday, $reserved_dates[$st->contract_id]) : $public_holiday;
        $done_sessions = u::calculatorSessions($st->enrolment_start_date, date('Y-m-d', strtotime("-1 days")), $merged_holi_day, $from_class_days);
        $done_sessions_total = isset($done_sessions->total) ? $done_sessions->total :0;
        $amount_transferred = $done_sessions_total < $st->real_sessions && $st->real_sessions ? ceil(($st->real_sessions-$done_sessions_total) * ($st->total_charged/$st->real_sessions)):0;
        $total_sessions = $done_sessions_total > $st->real_sessions ? 0 : $st->real_sessions - $done_sessions_total;
        $transfer_data = u::calcTransferTuitionFeeV2($st->tuition_fee_id, $amount_transferred, $st->branch_id, $st->product_id, (int) $total_sessions);
        $st->left_sessions = $st->summary_sessions - $done_sessions_total >0 ? $st->summary_sessions - $done_sessions_total : 0 ;
        $session_exchange = isset($transfer_data->sessions) ? ($st->summary_sessions - $st->real_sessions + $transfer_data->sessions) : ($st->summary_sessions - $done_sessions_total);
        if($total_sessions>0 && $transfer_data->code != APICode::SUCCESS){
          $st->is_valid = 0;
          $st->reason = $transfer_data->message;
        }
        if(!$session_exchange){
          $st->is_valid = 0;
          $st->reason = 'Số buổi còn lại không hợp lệ';
        } 
        if($st->is_valid ==1){
          $end_date = u::calculatorSessionsByNumberOfSessions($from_info->transfer_date, $session_exchange, $to_public_holiday,$to_class_days);
          $st->enrolment_last_date = $end_date->end_date;
        }
      }
      $students[$k]=$st;
    }

    return $students;
  }
}
