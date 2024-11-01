<?php


namespace App\Http\Controllers;

use App\Models\InfoValidation;
use Illuminate\Http\Request;
use App\Models\Response;
use App\Models\APICode;
use App\Models\ClassTransfer;
use App\Providers\UtilityServiceProvider as u;

class ClassTransfersController
{
  public function getList(Request $request)
  {
    $response = new Response();
    $model = new ClassTransfer();
    $code = APICode::SUCCESS;

    $user_data = $request->users_data;
    $res = null;

    if ($user_data) {
      $res = $model->getList($request);
    } else {
      $code = APICode::PERMISSION_DENIED;
    }

    return $response->formatResponse($code, $res);
  }

  public function getListByUserID($id)
  {
    $transferObj = new ClassTransfer();
    $response = new Response();

    $data = $transferObj->getClassTransfersByUserID($id);
    $code = APICode::SUCCESS;
    return $response->formatResponse($code, $data);
  }

  public function getDetail($id)
  {
    $tuitionTransferObj = new ClassTransfer();
    $response = new Response();

    $data = $tuitionTransferObj->getDetailByTransferID($id);
    $code = APICode::SUCCESS;
    return $response->formatResponse($code, $data);
  }

  public function getRequest(Request $request)
  {
    $tuitionTransferObj = new ClassTransfer();
    $response = new Response();

    $data = $tuitionTransferObj->getClassTransferRequests($request);
    $code = APICode::SUCCESS;
    return $response->formatResponse($code, $data);
  }

  public function suggestStudent(Request $request, $key, $branch_id, $type = 0)
  {
    $response = new Response();
    $transferObj = new ClassTransfer();

    if ($type) {
      $res = $transferObj->suggestStudent($key, $branch_id);
    } else {
      $res = $transferObj->suggestTrial($key, $branch_id);
    }

    return $response->formatResponse(APICode::SUCCESS, $res);
  }

  public function getAllInfo($branch_id)
  {
    $response = new Response();

    $classTransfer = new ClassTransfer();

    $res = $classTransfer->getAllInfo($branch_id);

    return $response->formatResponse(APICode::SUCCESS, $res);
  }

  public function create(Request $request)
  {
    $message = '';
    $data = null;
    $transferObj = new ClassTransfer();
    $response = new Response();

    $validate_code = $transferObj->validate($request);
    if ($validate_code == APICode::SUCCESS) {
      $resp = $transferObj->create($request);
      $code = $resp->code;
      $message = $resp->message;
    } else {
      $code = $validate_code;
    }

    return $response->formatResponse($code, $data, $message);
  }

  public function deny(Request $request, $id)
  {
    $response = new Response();
    $data = null;

    if ($session = $request->users_data) {
      $user_id = $session->id;

      $code = APICode::SUCCESS;
      $transferObj = new ClassTransfer();

      $validate_code = $transferObj->validateApproval($id);
      if ($validate_code == APICode::SUCCESS) {
        if (!$transferObj->deny($id, $request->comment, $user_id)) {
          $code = APICode::SERVER_CONNECTION_ERROR;
        }
      } else {
        $code = $validate_code;
      }
    } else {
      $code = APICode::PERMISSION_DENIED;
    }

    return $response->formatResponse($code, $data);
  }

  public function approve(Request $request, $id, $mode = 0)
  {
    $message = '';
    $data = null;
    $transferObj = new ClassTransfer();
    $response = new Response();

    if ($session = $request->users_data) {
      $validate_code = $transferObj->validateApproval($id);
      if ($validate_code == APICode::SUCCESS) {
        $res = $transferObj->approve($id, $request, $mode);
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

  public function getPrintData(Request $request, $id){
    $response = new Response();
    $code = APICode::SUCCESS;
    $message = '';
    $res = null;

    $classTransfer = new ClassTransfer();
    $data = $classTransfer->getPrintData($id);
    if($data){
      $res = (Object)[
        'branch_name' => $data->branch_name,
        'gud_name' => $data->gud_name1,
        'gud_mobile' => $data->gud_mobile1,
        'gud_email' => $data->gud_email1,
        'name' => $data->name,
        'gender' => $data->gender === 'M' ? 'Nam' : 'Nữ',
        'date_of_birth' => date('d/m/Y',strtotime($data->date_of_birth)),
        'from_class_name' => $data->from_class_name,
        'to_class_name' => $data->to_class_name,
        'note' => $data->note,
        'product_name'=>$data->product_name
      ];

      $contract = json_decode($data->meta_data);

      $res->session_left = $data->session_exchange;
      $res->done_sessions = $contract->sessions_from_start_to_transfer_date;
    }else{
      $code = APICode::PAGE_NOT_FOUND;
      $message = 'Bản ghi chuyển trung tâm không tồn tại hoặc đã bị xóa';
    }

    return $response->formatResponse($code, $res, $message);
  }

  public function calTotalFee($contracts){
    $total = 0;
    foreach ($contracts as $contract){
      $total += $contract->total_fee;
    }
    return $total;
  }

  public function getClassExtendInfo($start, $end, $class_id)
  {
    $response = new Response();
    $classTransferModel = new ClassTransfer();
    $res = $classTransferModel->getClassExtendInfo($start, $end, $class_id);
    $code = APICode::SUCCESS;
    if (!$res) {
      $code = APICode::PAGE_NOT_FOUND;
      $res = null;
    }

    return $response->formatResponse($code, $res);
  }

  public function check($stu_id, $branch_id)
  {
    $model = new InfoValidation();
    $response = [
      'result' => 'Học sinh có thể chuyển lớp',
      'suggest' => '',
      'student' => []
    ];
    $student = $model->hasStudent($stu_id, $branch_id);
    if ($student) {
      $response['student'] = [
        'id' => $student->id,
        'name' => $student->name,
        'lms_id' => $stu_id,
        'accounting_id' => $student->accounting_id,
        'crm_id' => $student->crm_id
      ];

      $contracts = $model->hasContracts($student->id, $branch_id);

      if ($contracts) {
        $contract_ids = implode(',', $contracts);
        if ($model->hasAvailableContractForClassTransfer($contract_ids, $student->type)) {
          if ($model->hasMoreThanTwoActiveClass($contract_ids, $branch_id)) {
            if ($model->hasActiveEnrolment($contract_ids)) {
              if ($model->hasJoinedBranchTransfer($contract_ids)) {
                if ($model->hasJoinedClassTransfer($contract_ids)) {
                  if ($model->hasJoinedTuitionTransfer($contract_ids)) {
                    if ($model->hasJoinedPending($contract_ids)) {
                      if (!$model->hasJoinedReserve($contract_ids)) {
                        $response['result'] = "Học sinh đăng ký bảo lưu và đang chờ duyệt";
                        $response['suggest'] = "Báo user nhờ OM, GĐTT từ chối bảo lưu";
                      }
                    } else {
                      $response['result'] = "Học sinh đăng ký pending và đang chờ duyệt";
                      $response['suggest'] = "Báo user nhờ OM, GĐTT từ chối pending";
                    }
                  } else {
                    $response['result'] = "Học sinh đang chuyển phí và đang chờ duyệt";
                    $response['suggest'] = "Báo user nhờ OM, GĐTT từ chối chuyển phí";
                  }
                } else {
                  $response['result'] = "Học sinh đang chuyển lớp và đang chờ duyệt";
                  $response['suggest'] = "Báo user nhờ OM, GĐTT từ chối chuyển lớp";
                }
              } else {
                $response['result'] = "Học sinh đang chuyển trung tâm và đang chờ duyệt";
                $response['suggest'] = "Báo user nhờ OM, GĐTT từ chối chuyển trung tâm";
              }
            } else {
              $response['result'] = "Không có enrolment nào gắn với contract HOẶC enrolment gắn với contract đã quá ngày học cuối";
              $response['suggest'] = "Kiểm tra contracts.enrolment_id, enrolments.contract_id, enrolments.last_date > ngày hiện tại, enrolments.status = 1";
            }
          } else {
            $response['result'] = "Số enrolment đang active > 1";
            $response['suggest'] = "Withdraw khỏi lớp, chỉ giữ lại lớp cần thiết";
          }
        } else {
          $response['result'] = "Contract có type = 1, 2 và payload = 0 nhưng chưa debt_amount > 0 HOẶC payload = 1 và total_charged = 0 (hoặc null)";
          $response['suggest'] = "Kiểm tra trong contracts: type > 0, status > 0, real_session > 0, total_charged > 0, payment_id (nếu là contract tạo trên CRM)";
        }
      } else {
        $response['result'] = "Chưa có contracts nào HOẶC tất cả contract đã được chuyển lớp, phí, trung tâm,...";
        $response['suggest'] = "Tạo contract mới (học trải nghiệm/ chính thức/ tái phí)";
      }
    } else {
      $response['result'] = "Không tìm thấy học sinh $stu_id trong trung tâm $branch_id";
    }

    return $response;
  }

  private static function getTotalDayRemaining($days,$allHoliday){
      $totalSessions = 0;
      foreach($days as $day){
          if (!in_array($day,$allHoliday)){
              $totalSessions += 1;
          }
      }
      return $totalSessions;
  }

  private static function getEnrolmentLastDate($days,$allHoliday,$days_remaining){
        $totalDays = [];
        foreach($days as $day){
            if (!in_array($day,$allHoliday)){
                $totalDays[] = $day;
            }
        }
        $last_day = !empty($totalDays[$days_remaining-1]) ? $totalDays[$days_remaining-1] : null;
        return $last_day;
    }

  private function processTransferList($data = [], $auth){
      $request = (object)$data;
      $request->users_data = $auth;
      $message = '';
      $data = null;
      $transferObj = new ClassTransfer();
      $response = new Response();

      $resp = $transferObj->create($request);
      $code = $resp->code;
      $message = $resp->message;

      return $response->formatResponse($code, $data, $message);

  }
  public function allClassTransfers(Request $request){
      if (!empty($request->list_student)){
          $auth = $request->users_data;
          $list_student = $request->list_student;
          $transfer_start_date = $request->transfer_start_date;
          $contractIds = implode($list_student,",");
          $query = "SELECT c.id, c.`student_id`,c.`status`, c.`enrolment_schedule`,c.`real_sessions`,
                    c.`total_charged`,c.`debt_amount`,c.`enrolment_start_date`,c.`enrolment_last_date`,
                    (SELECT class_day FROM `sessions` WHERE class_id = {$request->to_class_id} AND STATUS = 1 LIMIT 1) to_class_day,
                    (SELECT semester_id FROM `classes` WHERE id = {$request->to_class_id}) semester_id
                    FROM contracts c WHERE c.id IN($contractIds)";

          $data = u::query($query);
          $allHoliday = u::getPublicHolidayAll($request->branch_id);
          $dataNew = [];
          foreach ($data as $obj){
              $days =  u::getDaysBetweenTwoDate($transfer_start_date,  $obj->enrolment_last_date, $obj->enrolment_schedule); # 2x Friday
              $to_days =  u::getDaysBetweenTwoDate($transfer_start_date,  date('Y-m-d H:m:s', strtotime("+600 days")), $obj->to_class_day); # 2x Friday
              $obj->days_remaining = self::getTotalDayRemaining($days, $allHoliday);

              $obj->amount_exchange = round(($obj->total_charged/$obj->real_sessions)* $obj->days_remaining,0);
              $obj->amount_transferred = $obj->amount_exchange;
              $obj->last_day = self::getEnrolmentLastDate($to_days, $allHoliday,$obj->days_remaining);
              $old_enrol_end_date = date('Y-m-d', strtotime($request->current_day) - 7*24*60*60);
                $data_transfer = [];
                $data_transfer["student_id"]= $obj->student_id;
                $data_transfer["contract_id"]=$obj->id;
                $data_transfer["note"]=NULL;
                $data_transfer["transfer_date"]="$transfer_start_date";
                $data_transfer["amount_transferred"]=$obj->amount_exchange;
                $data_transfer["amount_exchange"]=$obj->amount_exchange;
                $data_transfer["session_transferred"]=$obj->days_remaining;
                $data_transfer["session_exchange"]=$obj->days_remaining;
                $data_transfer["from_branch_id"]=$request->branch_id;
                $data_transfer["to_branch_id"]=$request->branch_id;
                $data_transfer["from_product_id"]=$request->from_product_id;
                $data_transfer["to_product_id"]=$request->product_id;
                $data_transfer["from_program_id"]=$request->from_program_id;
                $data_transfer["to_program_id"]=$request->program_id;
                $data_transfer["from_class_id"]=$request->from_class_id;
                $data_transfer["to_class_id"]=$request->to_class_id;
                $data_transfer["semester_id"]=$obj->semester_id;
                $data_transfer["attached_file"]=NULL;
                $data_transfer["meta_data"]=
                    [
                        "total_session" =>  $obj->real_sessions,
                        "total_fee"=>  $obj->total_charged,
                        "from_start_date"=>   "$obj->enrolment_start_date",
                        "from_end_date"=>  "$transfer_start_date",
                        "to_start_date"=>  "$transfer_start_date",
                        "to_end_date"=>  "$obj->last_day",
                        "sessions_from_start_to_transfer_date"=>  ($obj->real_sessions - $obj->days_remaining),
                        "amount_from_start_to_transfer_date"=>  ($obj->total_charged - $obj->amount_exchange),
                        "old_enrol_end_date"=>  "$old_enrol_end_date",
                        "new_enrol_start_date"=>  "$request->nearest_day",
                        "shift_checked" =>  -1,
                        "enable_editor" =>  0
                 ];
              self::processTransferList($data_transfer, $auth);
          }
          $response = new Response();
          return $response->formatResponse(200, $request->from_class_id, "Bạn đã chuyển lớp thành công.");
      }
  }
  public function getStudent($class_id)
  {
    $response = new Response();
    $data = u::query("SELECT s.crm_id,s.accounting_id,s.name,c.summary_sessions,c.enrolment_last_date,c.student_id,
        c.enrolment_last_date,c.enrolment_start_date,c.`real_sessions`,c.must_charge,c.start_date,
        c.`total_charged`,c.`debt_amount`,c.tuition_fee_id,c.branch_id,c.product_id,c.total_sessions,c.bonus_sessions,c.id AS contract_id, 
        IF(c.enrolment_last_date >= CURRENT_DATE,(IF(s.waiting_status!=0,'Học sinh đang chờ xử lý  dữ liệu',
          IF((SELECT count(id) FROM reserves WHERe student_id= s.id AND `status`=2 AND `start_date`<=CURRENT_DATE AND end_date >=CURRENT_DATE)>0,'Học sinh đang bảo lưu',''))),'Quá ngày học cuối') AS reason, 
        IF(c.enrolment_last_date < CURRENT_DATE OR s.waiting_status!=0 OR (SELECT count(id) FROM reserves WHERe student_id= s.id AND `status`=2 AND `start_date`<=CURRENT_DATE AND end_date >=CURRENT_DATE)>0
        ,0,1) AS is_valid ,s.waiting_status
      FROM contracts AS c 
        LEFT JOIN students AS s ON s.id=c.student_id 
        WHERE c.status!=7 AND c.class_id=$class_id");
    $code = APICode::SUCCESS;
    foreach($data AS $k=>$row){
      if($row->waiting_status==1){
        $data[$k]->reason ='Học sinh đang chờ duyệt chuyển phí';
      }elseif($row->waiting_status==2){
        $data[$k]->reason ='Học sinh đang chờ duyệt nhận phí';
      }elseif($row->waiting_status==3){
        $data[$k]->reason ='Học sinh đang chờ duyệt truyển chung tâm';
      }elseif($row->waiting_status==4){
        $data[$k]->reason ='Học sinh đang chờ duyệt bảo lưu';
      }elseif($row->waiting_status==5){
        $data[$k]->reason ='Học sinh đang chờ duyệt chuyển lớp';
      }
    }
    return $response->formatResponse($code, $data);
  }
  public function createMulti(Request $request)
  {
    $message = '';
    $data = null;
    $transferObj = new ClassTransfer();
    $response = new Response();

    $validate_code = $transferObj->validate($request);
    if ($validate_code == APICode::SUCCESS) {
      $resp = $transferObj->createMulti($request);
      $code = $resp->code;
      $message = $resp->message;
    } else {
      $code = $validate_code;
    }

    return $response->formatResponse($code, $data, $message);
  }
  public function checkMulti(Request $request)
  {
    $message = '';
    $transferObj = new ClassTransfer();
    $response = new Response();
    $data = $transferObj->checkMulti($request);
    $code = APICode::SUCCESS;
    return $response->formatResponse($code, $data, $message);
  }
}
