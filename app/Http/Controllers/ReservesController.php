<?php

namespace App\Http\Controllers;

use App\Models\CyberAPI;
use App\Models\Mail;
use App\Models\Payment;
use App\Models\Student;
use App\Services\ReserveService;
use App\Services\TemplateExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\APICode;
use App\Models\Response;
use App\Models\Reserve;
use App\Models\Contract;
use App\Models\Sms;
use Illuminate\Routing\Route;
use App\Providers\UtilityServiceProvider as u;
use Mockery\Exception;
use stdClass;

use function GuzzleHttp\json_decode;

class ReservesController extends Controller
{
  private $RESERVE_STUDENT_STATUS = 5;

  public function getList(Request $request)
  {
    $response = new Response();
    $code = APICode::SUCCESS;
    $reserveModel = new Reserve();

    $user_data = $request->users_data;
    $res = null;

    if ($user_data) {
      $res = $reserveModel->getList($request);
    } else {
      $code = APICode::PERMISSION_DENIED;
    }


    return $response->formatResponse($code, $res);
  }

  public function suggest(Request $request, $key, $branch_id)
  {
    $user_data = $request->users_data;
    $response = new Response();
    $code = APICode::SUCCESS;
    $res = null;

    if ($user_data) {
      $branch_ids = u::getBranchIds($user_data);
      if (in_array($branch_id, $branch_ids)) {
        $reserveModel = new Reserve();
        $res = $reserveModel->getSuggestions($key, $branch_id,$user_data->role_id);

        $contract_ids = [];
        $class_ids = [];
        if (!empty($res)) {
          foreach ($res as $r) {
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

            if (isset($class_days[$r->class_id])) {
              $r->class_days = $class_days[$r->class_id];
            } else {
              $r->class_days = u::getDefaultClassDays($r->product_id);
            }
          }
        }
      } else {
        $code = APICode::PERMISSION_DENIED;
      }
    } else {
      $code = APICode::PERMISSION_DENIED;
    }

    return $response->formatResponse($code, $res);
  }

  public function create(Request $data)
  {
    $response = new Response();
    $res = null;

    $reserve = new Reserve();
    $reserve->student_id = $data->student_id;
    $reserve->type = $data->is_reserved==0 ? 1 : $data->reserve_type;
    $reserve->start_date = $data->start_date;
    $reserve->session = $data->{'session'};
    $reserve->end_date = $data->end_date;
    $reserve->new_enrol_end_date = $data->new_end_date;
    $reserve->branch_id = $data->branch_id;
    $reserve->program_id = $data->program_id;
    $reserve->product_id = $data->product_id;
    $reserve->class_id = $data->class_id;
    $reserve->is_reserved = $data->is_reserved;
    $reserve->creator_id = $data->users_data->id;
    $reserve->created_at = date('Y-m-d H:i:s');
    $reserve->meta_data = json_encode($data->meta_data);
    $reserve->creator_id = $data->users_data->id;
    $reserve->note = $data->note;
    $reserve->contract_id = $data->contract_id;
    $reserve->attached_file = '';
    $reserve->special_reserved_sessions = 0;
    $reserve->is_supplement = isset($data->is_supplement) ? (int)$data->is_supplement : 0;

    $hash_key = md5($reserve->student_id . $reserve->type . $reserve->start_date . $reserve->end_date . $reserve->class_id . $reserve->is_reserved);
    if ($reserve->isExisted($hash_key)) {
      if (is_array($data->meta_data)) {
        $meta_data = (object)$data->meta_data;
      } else {
        $meta_data = json_decode($data->meta_data);
      }

      if (isset($meta_data->special_reserved_sessions) && $meta_data->special_reserved_sessions > 0) {
        $reserve->special_reserved_sessions = $meta_data->special_reserved_sessions;
      }

      if ($data->attached_file) {
        $reserve->attached_file = ada()->upload($data->attached_file);
      }

      if ($reserve->save()) {
        // if ($data->reserve_type == 0 && $reserve->is_reserved==1) {
        if ($data->reserve_type == 0) {
          $this->handleFinalApprove($data, $reserve->id, 0);
        } else {
          u::query("UPDATE students SET waiting_status = 4 WHERE id = $data->student_id");
        }
        return $response->formatResponse(APICode::SUCCESS, $res);
      } else {
        return $response->formatResponse(APICode::SERVER_CONNECTION_ERROR, $res);
      }
    } else {
      return $response->formatResponse(APICode::WRONG_PARAMS, $res, 'Bản ghi đã tồn tại. Vui lòng kiểm tra lại trước khi thêm mới');
    }
  }
  
  public function handleFirstApprove(Request $request, $id)
  {
    $response = new Response();
    $res = null;
    $code = APICode::WRONG_PARAMS;
    $message = '';

    $reserve = Reserve::find($id);

    if ($reserve) {
      if ($reserve->type) {
        if ($reserve->status == 0) {
          $reserve->approver_id = $request->users_data->id;
          $reserve->approved_at = date('Y-m-d H:i:s');
          $reserve->status = 1;

          if ($reserve->save()) {
            $code = APICode::SUCCESS;
          } else {
            $code = APICode::WRONG_PARAMS;
            $message = 'Có lỗi xảy ra. Vui lòng thử lại';
          }
        } else {
          $code = APICode::WRONG_PARAMS;
          $message = 'Bản ghi đã được duyệt lần 1';
        }
      } else {
        $code = APICode::PERMISSION_DENIED;
        $message = 'Thao tác không hợp lệ';
      }
    } else {
      $code = APICode::PAGE_NOT_FOUND;
      $message = 'Bảo lưu không tồn tại hoặc đã bị xóa';
    }

    return $response->formatResponse($code, $res, $message);
  }

  public function handleFinalApprove(Request $request, $id, $type = 1, $send_mail = 1)
  {
    $response = new Response();
    $res = null;
    $reserve = Reserve::find($id);
    $contract = Contract::find($reserve->contract_id);

    //_____________________________test service dãn date_________________________
    $holidays = u::getPublicHolidays(0, $contract->branch_id, 9999);
    $class_days = [];

    if ($reserve->class_id) {
      $class_days = u::getClassDays($reserve->class_id);
    }

    $updateInfo = u::updateDate($contract->end_date, $reserve->new_enrol_end_date, $contract->student_id, $holidays, $class_days);

    if (!empty($updateInfo->contracts)) {
      foreach ($updateInfo->contracts as $c) {
        DB::table('contracts')->where('id', $c->id)->update(['start_date' => $c->start, 'end_date' => $c->end]);
      }
    }

    //_____________________________kết thúc test service dãn date_________________________

    if ($type) {
      $approver_id = $request->users_data->id;
    } else {
      $approver_id = 0;
      $reserve->approver_id = $approver_id;
      $reserve->approved_at = date('Y-m-d H:i:s');
    }
    $reserve->status = 2;
    $reserve->final_approver_id = $approver_id;
    $reserve->final_approved_at = date('Y-m-d H:i:s');

    if ($reserve->save()) {
      u::query("UPDATE students SET waiting_status = 0 WHERE id = $reserve->student_id");
      $cyberApi = new CyberAPI();
      $cyberApi->createReserve($reserve->id, $request->users_data->id);
    }
    if ($send_mail && $request->users_data->role_id!='999999999') {
      if ($type) {
        $this->sendApproveMail($reserve->id);
      } else {
        $this->sendNotificationMail($reserve->id);
      }
    }
    $student_info= u::first("SELECT * FROM term_student_user WHERE id=$contract->student_id");
    if ($reserve->is_reserved) {
      try {
        $this->updateEndDate($contract->id, $reserve->new_enrol_end_date, $reserve);
        //Update old Contract
        $update_contract_data = [
          'id' => $contract->id,
          'end_date'=>$reserve->new_enrol_end_date,
          'start_date'=> u::isGreaterThan($contract->start_date, $reserve->new_enrol_end_date) ? $reserve->new_enrol_end_date : $contract->start_date,
          'reserved_sessions' => 0,
          'reservable'=> 0,
          'action' => "do_reserve_for_contract_$contract->id"."_already",
          'updated_at'=> date('Y-m-d H:i:s'),
          'editor_id'=> $request->users_data->id,
        ];
        u::updateContract((Object)$update_contract_data);
        $content_log = "Bảo lưu giữ chỗ";
        u::query("INSERT INTO log_student_update (student_id,updated_by,updated_at,content,`status`,cm_id,branch_id,ceo_branch_id) 
          VALUES ($contract->student_id,".$request->users_data->id.",'".date('Y-m-d H:i:s')."','$content_log',1,'$student_info->cm_id','$student_info->branch_id','$student_info->ceo_branch_id')");
        $code = APICode::SUCCESS;
      } catch (Exception $e) {
        $code = APICode::SERVER_CONNECTION_ERROR;
      }
    } else {
      try {
        $lsmAPI = new LMSAPIController();
        $lsmAPI->updateStudentLMS($reserve->student_id,0,true);
        $this->createNewContract($reserve, $contract, $request->users_data);
        // $this->updateOldContract($contract, $reserve);
        // $meta_data = json_decode($reserve->meta_data);
        // $this->updateEndDate($contract->id, $meta_data->old_enrol_end_date, $reserve);
        $student = Student::find($contract->student_id);
        $student->status = $this->RESERVE_STUDENT_STATUS;
        $student->save();
        $content_log = "Bảo lưu không giữ chỗ";
        u::query("INSERT INTO log_student_update (student_id,updated_by,updated_at,content,`status`,cm_id,branch_id,ceo_branch_id) 
          VALUES ($contract->student_id,".$request->users_data->id.",'".date('Y-m-d H:i:s')."','$content_log',1,'$student_info->cm_id','$student_info->branch_id','$student_info->ceo_branch_id')");
        $code = APICode::SUCCESS;
      } catch (Exception $e) {
        $code = APICode::SERVER_CONNECTION_ERROR;
      }
    }
    $sms_info = u::first("SELECT s.gud_mobile1,s.name AS student_name, 
      (SELECT name FROM branches WHERE id= r.branch_id) AS branch_name, r.start_date, r.end_date,
      (SELECT cls_name FROM classes WHERE id = r.class_id) AS class_name
    FROM reserves AS r LEFT JOIN students AS s ON s.id= r.student_id WHERE r.id=$id");
    $sms_phone=$sms_info->gud_mobile1;
    /*CMS Edu TB thông tin bảo lưu của [Tên học sinh] như sau:
      Tên học sinh - Lớp: [Thông tin lớp học]
      Thời gian bảo lưu: từ ngày ……đến ngày.....
      Kính mong Quý PH cập nhật thông tin về thời gian bảo lưu. Trân trọng!*/
    $sms_content = "CMS Edu TB thong tin bao luu cua ".u::convert_name($sms_info->student_name)." nhu sau: 
    ".u::convert_name($sms_info->student_name)." - Lớp: $sms_info->class_name. 
    Thoi gian bao luu: tu ngay ".$sms_info->start_date." den ngay ".$sms_info->end_date.". 
    Kinh mong Quy PH cap nhat thong tin ve thoi gian bao luu. Tran trong!";
    $sms =new Sms();
    $sms->sendSms($sms_phone,$sms_content);

    return $response->formatResponse($code, $res);
  }

  public function updateOldContract($contract, $reserve)
  {
    $meta_data = json_decode($reserve->meta_data);

    if ($reserve->type == Reserve::NORMAL_TYPE) {
      $contract->reserved_sessions = $contract->reserved_sessions + $reserve->session;
      $contract->reservable_sessions = $contract->reserved_sessions;
      $contract->reservable = 0;
    } else {
      $contract->reserved_sessions = $contract->reservable_sessions;
      $contract->reservable_sessions = 0;
      $contract->reservable = 0;
    }

    $contract->info_available = 3;

    $contract->end_date = $reserve->start_date;

    $contract->total_charged = $meta_data->amount_from_start_to_reserve_date;
    $contract->real_sessions = $meta_data->sessions_from_start_to_reserve_date;
    $contract->debt_amount = 0;

    return $contract->save();
  }

  public function progressMetaSessions($contract, $reserve)
  {
    $r1 = $contract->reserved_sessions + $reserve->session;
    $r2 = $contract->reservable_sessions - $r1;
    $results = [
      'old_contract' => (object)[
        'reserved_sessions' => $r1,
        'reservable' => 0,
        'reservable_sessions' => ($r1 < $contract->reservable_sessions) ? $r1 : $contract->reservable_sessions
      ],
      'new_contract' => (object)[
        'reserved_sessions' => 1,
        'reservable' => ($r2 > 0) ? 1 : 0,
        'reservable_sessions' => ($r2 > 0) ? $r2 : 0
      ]
    ];
    return (object)$results;
  }

  public function createNewContract($reserve, $contract, $userData)
  {
    $reserveMetaData = json_decode($reserve->meta_data);

    $contractModel = new stdClass();
    $contractModel->id = $contract->id;
    $contractModel->accounting_id = $contract->accounting_id;
    $contractModel->code = apax_ada_gen_contract_code();
    $contractModel->type = 10;
    $contractModel->payload = $contract->payload;
    $contractModel->student_id = $contract->student_id;
    // $contractModel->relation_contract_id = $contract->id;

    $contractModel->branch_id = $contract->branch_id;
    $contractModel->ceo_branch_id = $contract->ceo_branch_id;
    $contractModel->region_id = $contract->region_id;
    $contractModel->ceo_region_id = $contract->ceo_region_id;
    $contractModel->ec_id = $contract->ec_id;
    $contractModel->ec_leader_id = $contract->ec_leader_id;
    $contractModel->cm_id = $contract->cm_id;
    $contractModel->om_id = $contract->om_id;

    $meta_data = json_decode($reserve->meta_data);

    $contractModel->product_id = $contract->product_id;
    $contractModel->program_id = 0;
    $contractModel->tuition_fee_id = $contract->tuition_fee_id;
    $contractModel->receivable = $contract->receivable;
    $contractModel->payment_id = $contract->payment_id;
    $contractModel->total_charged = $reserveMetaData->amount_reserved;
    $contractModel->must_charge = $contract->must_charge;
    $contractModel->debt_amount = $contract->debt_amount;
    $contractModel->total_discount = $contract->total_discount;
    $contractModel->description = $contract->description;
    $contractModel->bill_info = $contract->bill_info;

    $contractModel->start_date = $meta_data->new_start_date;
    $contractModel->end_date = $reserve->new_enrol_end_date;

    $contractModel->total_sessions = $contract->total_sessions;
    $contractModel->real_sessions = $reserveMetaData->number_of_real_sessions_reserved;
    $contractModel->summary_sessions = $reserveMetaData->number_of_session_reserved;
    $contractModel->bonus_sessions = $reserveMetaData->number_of_session_reserved - $reserveMetaData->number_of_real_sessions_reserved;

    $contractModel->expected_class = $reserve->expected_class;
    $contractModel->passed_trial = 1;

    $contractModel->status = $this->setStatus($contract->type);

    $contractModel->updated_at = date('Y-m-d H:i:s');
    $contractModel->editor_id = $userData->id;
    $contractModel->only_give_tuition_fee_transfer = 0;
    $contractModel->sibling_discount = $contract->sibling_discount;
    $contractModel->after_discounted_fee = $contract->after_discounted_fee;
    $contractModel->discount_value = $contract->discount_value;
    $contractModel->info_available = $contract->info_available;
    $contractModel->done_sessions = 0;
    $contractModel->tuition_fee_price = $contract->tuition_fee_price;
    $contractModel->count_recharge = $contract->count_recharge;
    $contractModel->relation_left_sessions = $contract->relation_left_sessions + $meta_data->sessions_from_start_to_reserve_date;
    $contractModel->action = "reserve_contract_" . $contract->id;
    $contractModel->reserved_sessions = 0;
    $contractModel->class_id = NULL;
    $contractModel->start_cjrn_id = NULL;
    $contractModel->enrolment_accounting_id = NULL;
    $contractModel->enrolment_start_date = NULL;
    $contractModel->enrolment_end_date = NULL;
    $contractModel->enrolment_expired_date = NULL;
    $contractModel->enrolment_last_date = NULL;
    $contractModel->enrolment_schedule = NULL;
    $contractModel->enrolment_withdraw_date = NULL;

    $contractModel->reservable = 0;
    $contractModel->reservable_sessions = 0;
    $contractModel->relation_reserved_sessions = $contract->relation_reserved_sessions + $contract->reservable_sessions;
    u::updateContract($contractModel);
  }

  private function setStatus($old_type)
  {
    return 4;
  }

  public function updatePaymentContract($contractOldID, $contractNewID)
  {
    return Payment::where('contract_id', $contractOldID)
      ->update(['contract_id' => $contractNewID]);
  }

  public function deny(Request $request, $id)
  {
    $response = new Response();

    $model = new Reserve();
    $reserve = $model->find($id);
    $res = null;
    $code = APICode::SUCCESS;
    if ($reserve->status  == 0) {

      $reserve->approver_id = $request->users_data->id;
      $reserve->approved_at = date('Y-m-d H:i:s');
      $reserve->comment = $request->comment;
    } else {

      $reserve->final_approver_id = $request->users_data->id;
      $reserve->final_approved_at = date('Y-m-d H:i:s');
      $reserve->final_approve_comment = $request->comment;
    }
    $reserve->status = 3;
    u::query("UPDATE students SET waiting_status = 0 WHERE id = $reserve->student_id");
    if (!$reserve->save()) {
      $code = APICode::SERVER_CONNECTION_ERROR;
    } else {
      if ($request->users_data->role_id!='999999999') {
        $this->sendDenyMail($id);
      }
    }

    return $response->formatResponse($code, $res);
  }

  public function updateEndDate($contract_id, $end_date, $reserve)
  {
    $model = new Contract();
    $enrol = $model->where('id', $contract_id)->first();

    if (!$reserve->is_reserved) {
      $meta_data = json_decode($reserve->meta_data);
      $enrol->enrolment_real_sessions = $meta_data->sessions_from_start_to_reserve_date;
      $enrol->enrolment_withdraw_reason = 'Bảo lưu không giữ chỗ';
    }

    $enrol->enrolment_last_date = $end_date;

    if ($enrol->save()) {
      return true;
    } else {
      return false;
    }
  }

  public function createContract($reserve)
  {
    $model = new Contract();
    $contrac_id = $reserve->contract_id;
    $contract = $model->find($contrac_id);
    $newContract = new Contract();

    $newContract->code = apax_ada_gen_contract_code();
    $newContract->type = $contract->type;
    $newContract->payload = $contract->payload;
    $newContract->student_id = $contract->student_id;
    $newContract->relation_contract_id = $contract->id;
    $newContract->branch_id = $contract->branch_id;
    $newContract->ceo_branch_id = $contract->ceo_branch_id;
    $newContract->region_id = $contract->region_id;
    $newContract->ceo_region_id = $contract->ceo_region_id;
    $newContract->ec_id = $contract->ec_id;
    $newContract->ec_leader_id = $contract->ec_leader_id;
    $newContract->cm_id = $contract->cm_id;
    $newContract->om_id = $contract->om_id;
    $newContract->product_id = $contract->product_id;
    $newContract->program_id = 0;
    $newContract->tuition_fee_id = $contract->tuition_fee_id;
    $newContract->payment_id = $contract->payment_id;

    $newContract->receivable = $contract->receivable;
    $newContract->must_charge = $contract->must_charge;

    $newContract->start_date = $reserve->end_date;
    $newContract->end_date = $reserve->new_enrol_end_date;

    $newContract->total_sessions = $contract->total_sessions;
    $newContract->real_sessions = $contract->left_sessions;

    $newContract->total_discount = $contract->total_discount;
    $newContract->debt_amount = $contract->debt_amount;
    $newContract->description = $contract->description;
    $newContract->bill_info = $contract->bill_info;
    $newContract->expected_class = $contract->expected_class;
    $newContract->passed_trial = $contract->passed_trial;
    $newContract->status = $contract->status;
    $newContract->creator_id = $contract->creator_id;
    $newContract->note = $contract->note;
    $newContract->only_give_tuition_fee_transfer = $contract->only_give_tuition_fee_transfer;

    $newContract->count_recharge = $contract->count_recharge;

    return $newContract->save();
  }

  public function getListByUserID(Request $request, $id)
  {
    $response = new Response();
    $res = null;

    if ($request->users_data) {
      $reserveModel = new Reserve();
      $branch_ids = u::getBranchIds($request->users_data);

      $res = $reserveModel->getReservesByUserId($id, $branch_ids);
      $code = APICode::SUCCESS;
    } else {
      $code = APICode::PERMISSION_DENIED;
    }
    return $response->formatResponse($code, $res);
  }

  public function getRequests(Request $request)
  {
    $user_data = $request->users_data;
    $response = new Response();
    if ($user_data) {
      $reserveModel = new Reserve();
      $reserve_regulation = $reserveModel->getReserveRegulation();

      $roles_detail = $user_data->roles_detail;

      $hasPermission = false;
      $conditions = [];

      foreach ($roles_detail as $item) {
        if (isset($reserve_regulation[$item->role_id])) {
          $hasPermission = true;
          $max_session = $reserve_regulation[$item->role_id]->max_days;
          $min_days = $reserve_regulation[$item->role_id]->min_days;
          $conditions[] = "(r.branch_id = $item->branch_id AND r.special_reserved_sessions <= $max_session AND r.special_reserved_sessions >= $min_days AND r.status = 0)";
        }

        if (in_array($item->role_id, [84,85858585, ROLE_SUPER_ADMINISTRATOR])) {
          $hasPermission = true;
          $conditions[] = "(r.status = 0)";
        }
      }

      if ($hasPermission) {
        $searchInfo = $reserveModel->searchForApproving($request);
        $conditions = $searchInfo->conditions;
        $limit = $searchInfo->limit;
        $result = $reserveModel->getRequests($conditions, $limit);
        $total = 0;
        if (!empty($result)) {
            $total = $reserveModel->countRequests($conditions);
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
            "pagination" => $page,
        ];

        return $response->formatResponse(APICode::SUCCESS, $res);
      } else {
        return $response->formatResponse(APICode::PERMISSION_DENIED, null);
      }
    } else {
      return $response->formatResponse(APICode::SESSION_EXPIRED, null);
    }
  }

  public function getPrintData(Request $request, $id)
  {
    $response = new Response();
    $code = APICode::SUCCESS;
    $message = '';
    $res = null;

    $reserve = new Reserve();
    $data = $reserve->getPrintData($id);
    if ($data) {
      $meta_data = json_decode($data->meta_data);
      $res = (object)[
        'gud_name' => $data->gud_name,
        'name' => $data->name,
        'program_name' => $data->program_name,
        'product_name' => $data->product_name,
        'room_name' => $data->room_name,
        'class_time' => $data->shift_name,
        'charged_at' => $data->charged_at ? date('d/m/Y',strtotime($data->charged_at)):'',
        'reserve_start_date' => $data->start_date,
        'reserve_end_date' => $data->end_date,
        'note' => $data->note,
        'branch_name' => trim(str_replace("Trung tâm", "", $data->branch_name)),
        'before_reserve_start_date' => date('d/m/Y',strtotime($meta_data->start_date)),
        'before_reserve_end_date' =>date('d/m/Y',strtotime($meta_data->before_reserve_end_date)),
        'created_at' =>date('d/m/Y',strtotime($data->created_at)),
        'total_fee' => $data->total_fee,
        'done_sessions' => $meta_data->total_session - $meta_data->number_of_session_reserved,
        'number_of_session_reserved' => $meta_data->number_of_session_reserved,
        'amount_reserved' => $meta_data->amount_reserved,
        'payment_accounting_id'=>$data->payment_accounting_id,
        'amount_reserved_text' => u::convert_number_to_words($meta_data->amount_reserved)." đồng",
        'gud_mobile'=>$data->gud_mobile,
        'session'=>$data->session,
        'reserved_sessions'=>$data->reserved_sessions,
      ];
    } else {
      $code = APICode::PAGE_NOT_FOUND;
      $message = 'Bản ghi bảo lưu không tồn tại hoặc đã bị xóa';
    }

    return $response->formatResponse($code, $res, $message);
  }

  private function setClassTime($class_day, $shift_name)
  {
    $class_day_name = u::getDayName($class_day);

    return "$class_day_name $shift_name";
  }
  public function downloadTemplateImport(\App\Templates\Imports\Reserve $template, TemplateExportService $service)
  {
    $service->export($template, 'reserve_import_template');
  }

  public function validateImport(Request $request)
  {
    set_time_limit(-1);
    $_extension = ['xlsx', 'xls', 'csv', 'xlsm', 'Xml', 'Ods'];
    $userData = $request->users_data;
    if (empty($userData) || (int)$userData->role_id !== 999999999) {
      return response()->json(['data' => ['message' => 'Permission denied !']]);
    }
    $this->validate($request, ['file' => 'required']);
    if (!$request->hasFile('file')) {
      return response()->json(['data' => ['message' => 'File is empty !']]);
    }

    $extension = $request->file->extension();
    if (!in_array($extension, $_extension)) {
      return response()->json(['data' => ['message' => 'Format file something wrong !']]);
    }

    $service = new ReserveService();
    return $service->validateImport($request, $request->file->getPathName(), false);
  }

  public function executeImport(Request $request)
  {
    set_time_limit(-1);
    $_extension = ['xlsx', 'xls', 'csv', 'xlsm', 'Xml', 'Ods'];
    $userData = $request->users_data;
    if (empty($userData) || (int)$userData->role_id !== 999999999) {
      return response()->json(['data' => ['message' => 'Permission denied !']]);
    }
    $this->validate($request, ['file' => 'required']);
    if (!$request->hasFile('file')) {
      return response()->json(['data' => ['message' => 'File is empty !']]);
    }

    $extension = $request->file->extension();
    if (!in_array($extension, $_extension)) {
      return response()->json(['data' => ['message' => 'Format file something wrong !']]);
    }

    $service = new ReserveService();
    return $service->validateImport($request, $request->file->getPathName(), true);
  }
  public function getAllData($branch_id)
  {
    $response = new Response();
    $reserve = new Reserve();
    $res = $reserve->getAllInfo($branch_id);

    return $response->formatResponse(APICode::SUCCESS, $res);
  }
  public function getStudentsForMultiReserve(Request $request, $class_id)
  {
    $response = new Response();
    $code = APICode::SUCCESS;

    $reserve = new Reserve();
    $students = $reserve->getStudentsForMultiReserve($class_id);
    $class = $reserve->getClassInfo($class_id);

    $data = (object) [
      'students' => $students,
      'class' => $class,
    ];

    return $response->formatResponse($code, $data);
  }
  public function createMultiReserve(Request $request)
  {
    set_time_limit(300);
    $message = '';
    $data = null;
    $reserve = new Reserve();
    $response = new Response();
    $code = APICode::SUCCESS;

    $data=$reserve->createMultiReserve($request);
    foreach ($data as $reserve) {
      $this->handleFinalApprove($request, $reserve->id, 0,0);
    }

    return $response->formatResponse($code, $data, $message);
  }
  public function sendQlcl(Request $request)
  {
    $response = new Response();
    $data = null;
    $code = APICode::SUCCESS;
    $attached_file = "";
    foreach($request->attached_file AS $file){
      $attached_file .= $attached_file ? '|*|'.ada()->upload($file) : ada()->upload($file);
    }
    $attached_file_curr="";
    if($request->attached_file_curr){
      foreach($request->attached_file_curr AS $file){
        $attached_file_curr.=$attached_file_curr?'|*|'.$file:$file;
      }
    }
    $reserve_info = u::first("SELECT * FROM reserves WHERE id=$request->reserve_id");
    $attached_file = $attached_file_curr ? ($attached_file ? $attached_file_curr .'|*|'.$attached_file : $attached_file_curr ) : $attached_file;
    u::query("UPDATE reserves SET attached_file = '$attached_file' WHERE id=$request->reserve_id");
    if($reserve_info->status!=2 && $request->users_data->role_id!='999999999'){
      $this->sendRequirementMail($request->reserve_id);
    }
    return $response->formatResponse($code, $data);
  }
  private function sendRequirementMail($reserve_id)
  {
    $reserve_info = u::first("SELECT * FROM reserves WHERE id=$reserve_id");
    $student = u::first("SELECT s.`name`,s.crm_id, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $reserve_info->student_id");
    $mail_qlcl = 'qlcl-ksnb@cmsedu.vn';
    $arr_send = JobsController::getEmail($reserve_info->branch_id);
    $mail = new Mail();
    $to = array('address' => $mail_qlcl, 'name' => $mail_qlcl);
    $subject = "[Hệ thống CRM - CMS] Yêu cầu phê duyệt bảo lưu ngoài quy định của bé $student->name";
    $body = "<p>Kính gửi: Phòng Quản Lý Chất Lượng</p>
              <p>Hệ thống CRM - CMS xin thông báo: Anh/chị đã nhận được yêu cầu <strong>bảo lưu ngoài quy định</strong> của bé <strong>$student->name</strong> - [$student->crm_id] - [$student->branch_name]</p>
              <p>Lý do bảo lưu: $reserve_info->note</p>
              <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM - CMS để phê duyệt yêu cầu.</p>
              <p>Hoặc truy cập link: <a href='https://crm.cmsedu.vn'>crm.cmsedu.vn</a> để xem chi tiết yêu cầu và phản hồi trung tâm</p>
              <p>Trân trọng cảm ơn!</p>";
    $arr_file_attack = explode('|*|',$reserve_info->attached_file);
    $tmp_file = array();
    foreach($arr_file_attack AS $k=> $row){
      $tmp_file[] = (object)array('url'=>$row,'name'=>'File đính kèm '.($k+1));
    }
    $mail->sendSingleMail($to, $subject, $body,[$arr_send['cskh'],$arr_send['gdtt'], 'cskh.ho@cmsedu.vn','thao.nguyen4@cmsedu.vn'],$tmp_file);
  }
  private function sendDenyMail($reserve_id)
  {
    $reserve_info = u::first("SELECT * FROM reserves WHERE id=$reserve_id");
    $comment = $reserve_info->comment;
    $student = u::first("SELECT s.`name`,s.crm_id, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $reserve_info->student_id");
    $mail_qlcl = 'qlcl-ksnb@cmsedu.vn';
    $arr_send = JobsController::getEmail($reserve_info->branch_id);
    $mail = new Mail();
    $to = array('address' => $arr_send['cskh'], 'name' => $arr_send['cskh']);
    $subject = "[Hệ thống CRM - CMS] Từ chối bảo lưu ngoài quy định của bé $student->name - $student->crm_id";
    $body = " <p>Kính gửi: Phòng CS</p>
          <p>Hệ thống CRM - CMS xin thông báo: Yêu cầu <strong>bảo lưu ngoài quy định</strong> của bé <strong>$student->name</strong> [$student->branch_name] đã bị từ chối phê duyệt</p>
          <p>Lý do từ chối: $comment</p>
          <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM - CMS để xem chi tiết.</p>
          <p>Trân trọng cảm ơn!</p>";
    $mail->sendSingleMail($to, $subject, $body,[$mail_qlcl,$arr_send['gdtt'], 'cskh.ho@cmsedu.vn','thao.nguyen4@cmsedu.vn']);
  }

  private function sendNotificationMail($reserve_id)
  {
    $reserve_info = u::first("SELECT * FROM reserves WHERE id=$reserve_id");
    $student = u::first("SELECT s.`name`,s.crm_id, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $reserve_info->student_id");
    $arr_send = JobsController::getEmail($reserve_info->branch_id);
    $mail = new Mail();
    $to = array('address' => $arr_send['cskh'], 'name' => $arr_send['cskh']);
    $subject = "[Hệ thống CRM - CMS] Thông báo bảo lưu theo quy định của bé $student->name  - $student->crm_id";
    $body = " <p>Kính gửi: CSKH [$student->branch_name]</p>
            <p>Hệ thống CRM - CMS xin thông báo: <strong>Bảo lưu thường</strong> của bé <strong>$student->name</strong> [$student->branch_name] đã được phê duyệt</p>
            <p>Nội dung: $reserve_info->note</p>
            <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM - CMS để xem chi tiết.</p>
            <p>Trân trọng cảm ơn!</p>";
    $mail->sendSingleMail($to, $subject, $body,[$arr_send['gdtt']]);
  }
  private function sendApproveMail($reserve_id)
  {
    $reserve_info = u::first("SELECT * FROM reserves WHERE id=$reserve_id");
    $student = u::first("SELECT s.`name`,s.crm_id, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $reserve_info->student_id");
    $arr_send = JobsController::getEmail($reserve_info->branch_id);
    $mail_qlcl = 'qlcl-ksnb@cmsedu.vn';
    $mail = new Mail();
    $to = array('address' => $arr_send['cskh'], 'name' => $arr_send['cskh']);
    $subject = "[Hệ thống CRM - CMS] Phê duyệt thành công bảo lưu ngoài quy định của bé $student->name - $student->crm_id";
    $body = " <p>Kính gửi: CSKH [$student->branch_name]</p>
            <p>Hệ thống CRM - CMS xin thông báo: Yêu cầu <strong>bảo lưu ngoài quy định</strong> của bé <strong>$student->name</strong> [$student->branch_name] đã được phê duyệt</p>
            <p>Nội dung: $reserve_info->note</p>
            <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM - CMS để xem chi tiết.</p>
            <p>Trân trọng cảm ơn!</p>";
    $mail->sendSingleMail($to, $subject, $body,[$mail_qlcl,$arr_send['gdtt'],  'cskh.ho@cmsedu.vn','thao.nguyen4@cmsedu.vn']);
  }
  public function getReserveForEdit(Request $request, $id)
  {
      $response = new Response();
      $res = null;

      if ($request->users_data) {

          $query = "SELECT
                  s.crm_id,
                  r.id,
                  r.contract_id,
                  r.student_id,
                  r.type,
                  r.start_date,
                  r.end_date,
                  r.session,
                  r.status,
                  r.created_at,
                  r.approved_at,
                  r.meta_data,
                  r.is_reserved,
                  r.attached_file,
                  r.note,
                  r.new_enrol_end_date as new_end_date,
                  s.`name` AS student_name,
                  s.nick,
                  s.`stu_id` AS lms_id,
                  s.`accounting_id`,
                  r.branch_id,
                  r.product_id,
                  r.program_id,
                  r.class_id,
                  c.`real_sessions`,
                  c.`total_sessions`,
                  c.summary_sessions,
                  c.bonus_sessions,
                  c.end_date AS contract_end_date,
                  (SELECT `name` FROM branches WHERE id = r.branch_id) AS branch_name,
                  (SELECT `name` FROM products WHERE id = r.product_id) AS product_name,
                  (SELECT `name` FROM programs WHERE id = r.program_id) AS program_name,
                  (SELECT `cls_name` FROM classes WHERE id = r.class_id) AS class_name,
                  (SELECT reservable_sessions - reserved_sessions FROM contracts WHERE id = r.contract_id) AS max_session,
                  (SELECT end_date FROM reserves WHERE student_id=r.student_id AND status=2 ORDER BY end_date DESC LIMIT 1 ) AS last_end_date
              FROM
                  reserves AS r
                  LEFT JOIN contracts c ON r.contract_id = c.id
                  LEFT JOIN students AS s ON r.student_id = s.`id`
              WHERE
                  r.id = $id
              ";

          $res = u::first($query);
          $code = APICode::SUCCESS;
      } else {
          $code = APICode::PERMISSION_DENIED;
      }
      return $response->formatResponse($code, $res);
  }
  public function getResultEdit(Request $request, $id){
    $response = new Response();
    $res = null;
    $reserve = (object)$request->reserve;
    $code = APICode::SUCCESS;
    $meta_data= (object)json_decode($reserve->meta_data);
    $reserved_dates = self::getReservedDates_transfer([$reserve->contract_id],$id);
    $holi_day = u::getPublicHolidays(0, $reserve->branch_id, $reserve->product_id);
    $merged_holi_day = $reserved_dates && isset($reserved_dates[$reserve->contract_id]) ? array_merge($holi_day, $reserved_dates[$reserve->contract_id]) : $holi_day;
    $class_day = u::getClassDays($reserve->class_id);
    $reserve_end_date =  u::getRealSessions($reserve->session, $class_day, $merged_holi_day, $reserve->start_date);  
    $reserve->end_date = $reserve_end_date->end_date;
    $merged_holi_day_contract = array_merge( $merged_holi_day , [(object)['start_date' => $reserve->start_date, 'end_date' => $reserve_end_date->end_date]]);
    $contract_end_date =  u::getRealSessions( $reserve->summary_sessions, $class_day, $merged_holi_day_contract, $meta_data->start_date); 
    $reserve->new_end_date = $contract_end_date->end_date;
    return $response->formatResponse($code, $reserve);
  }
  static function getReservedDates_transfer($contract_ids = [] ,$reserve_id)
    {
        $res = [];

        if ($contract_ids) {
            $contract_ids_string = implode(',', $contract_ids);
            $query = "SELECT r.contract_id, r.start_date, r.end_date, r.session AS `sessions` FROM `reserves` AS r WHERE r.status = 2 AND r.contract_id IN ($contract_ids_string) AND r.id!=$reserve_id";
            $data = u::query($query);

            if (!empty($data)) {
                foreach ($data as $da) {
                    if (isset($res[$da->contract_id])) {
                        $res[$da->contract_id][] = (object)['start_date' => $da->start_date, 'end_date' => $da->end_date, 'sessions' => $da->sessions];
                    } else {
                        $res[$da->contract_id][] = (object)['start_date' => $da->start_date, 'end_date' => $da->end_date, 'sessions' => $da->sessions];
                    }
                }
            }
        }

        return $res;
    }
  public function saveEdit(Request $request, $id){
    $response = new Response();
    $reserve = (object)$request->reserve;
    $code = APICode::SUCCESS;
    $curr_reserve_info = u::first("SELECT * FROM reserves WHERE id=$id");
    u::query("UPDATE reserves SET note='$reserve->note',session ='$reserve->session', start_date ='$reserve->start_date',end_date='$reserve->end_date',new_enrol_end_date = '$reserve->new_end_date' WHERE id = $id ");
    if($reserve->is_reserved==1){
      u::updateContract((object) [                        
        'id' => $reserve->contract_id,
        'enrolment_last_date' => $reserve->new_end_date,
      ]);
    }else{
      u::updateContract((object) [                        
        'id' => $reserve->contract_id,
        'start_date' => date('Y-m-d',strtotime($reserve->end_date)+24*3600),
      ]);
    }
    $reserve_info = u::first("SELECT * FROM reserves WHERE id=$id");
    DB::table('reserve_logs')->insertGetId(
      [   
          'reserve_id' => $id,
          'meta_data'=> json_encode($reserve_info),
          'curr_meta_data'=>json_encode($curr_reserve_info),
          'created_at'=> date('Y-m-d H:i:s'),
          'creator_id'=> $request->users_data->id,
      ]
  );
    return $response->formatResponse($code, $reserve);
  }
  public function cancelReserve(Request $request, $id){
    $response = new Response();
    $reserve = (object)$request->reserve;
    $code = APICode::SUCCESS;
    $reserve_info = u::first("SELECT id,student_id FROM reserves WHERE id=$id");
    u::query("UPDATE reserves SET status=4,updated_at='".date('Y-m-d H:i:s')."' WHERE id = $id ");
    u::query("UPDATE students SET waiting_status = 0 WHERE id = $reserve_info->student_id");
    return $response->formatResponse($code, $reserve);
  }
  public function approve(Request $request, $id)
    {
        $message = '';
        $data = null;
        $reserve = new Reserve();
        $response = new Response();

        if ($session = $request->users_data) {
            $validate_code = $reserve->validateApprove($request, $id);
            if ($validate_code == APICode::SUCCESS) {
                $res = $reserve->approve($request, $id, 1);
                $data = $res->data;
                $code = $res->code;
                $message = $res->message;
            } else {
                $code = $validate_code;
            }
        } else {
            $code = APICode::PERMISSION_DENIED;
        }

        return $response->formatResponse($code, $data, $message);
    }
    
}
