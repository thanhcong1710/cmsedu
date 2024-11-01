<?php

namespace App\Http\Controllers;

use App\Models\BranchTransfer;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use App\Models\CyberAPI;
use App\Models\Contract;
use App\Models\LogCyberRequest;
use App\Models\Sms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Response;
use App\Models\APICode;
use App\Providers\CurlServiceProvider as curl;

class ToolsController extends Controller
{
  public function copyLogContract($log_id)
  {
    $log_contract_info = u::first("SELECT * FROM log_contracts_history WHERE id=$log_id");
    if ($log_contract_info) {
      $contract_info = u::first("SELECT * FROM contracts WHERE id=$log_contract_info->contract_id");
      $contract = Contract::find($log_contract_info->contract_id);
      foreach ($contract_info as $key => $value) {
        if($key != 'id'){
          $contract->$key = $log_contract_info->$key;
        }
      }
      $contract->save();
    }

    return 'ok';
  }
  public function convertContract($contract_id)
  {
    $contract_info = u::first("SELECT id,total_charged,debt_amount FROM contracts WHERE id=$contract_id");
    if ($contract_info && !$contract_info->total_charged && !$contract_info->debt_amount) {
      u::query("UPDATE contracts SET
          summary_sessions = real_sessions,
          bonus_sessions=real_sessions,
          real_sessions=0
        WHERE id= $contract_id");
      u::query("UPDATE log_contracts_history SET
          summary_sessions = real_sessions,
          bonus_sessions=real_sessions,
          real_sessions=0
        WHERE contract_id= $contract_id");
    }else{
      u::query("UPDATE contracts SET
          summary_sessions = real_sessions
        WHERE id= $contract_id");
      u::query("UPDATE log_contracts_history SET
          summary_sessions = real_sessions
        WHERE contract_id= $contract_id");
    }
    return true;
  }
  public function recallApiCyberCreateContract(Request $request, $contract_id)
  {
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
                      c.bonus_amount
              FROM contracts c LEFT JOIN students s ON c.student_id = s.id LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
              WHERE c.id = $contract_id AND (c.accounting_id IS NULL OR c.accounting_id=0)";
    $res = u::first($query);
    if ($res) {
      $cyberAPI = new CyberAPI();
      $res = $cyberAPI->createContract($res, $request->users_data->id);
      if ($res) {
        u::query("UPDATE contracts SET accounting_id = '$res' WHERE id = $contract_id");
        u::query("UPDATE log_contracts_history SET accounting_id = '$res' WHERE contract_id = $contract_id");
      }
    }
    return "ok";
  }
  public function convertDataTuitionTransfer(){
    $data= u::query("SELECT t.* FROM tuition_transfer AS t WHERE (SELECT count(id) FROM tuition_transfer_v2 WHERE convert_id=t.id)=0 ");
    $query = "INSERT INTO tuition_transfer_v2 (from_student_id, to_student_id, note, transfer_date, `status`, creator_id, created_at, transferred_amount, received_amount, transferred_sessions, received_sessions ,
      from_branch_id, to_branch_id,from_product_id,to_product_id,from_class_id, to_class_id, accounting_approver_id, accounting_approved_at, accounting_comment, ceo_approver_id, ceo_approved_at, ceo_comment, editor_id, updated_at,
      meta_data,attached_file, convert_id) VALUES ";
    foreach($data AS $item){
      if($item->status==0){
        $status = 1;
      }elseif($item->status==1){
        $status = 4;
      }elseif($item->status==2){
        $status = 6;
      }elseif($item->status==3 && $item->final_approver_id){
        $status = 3;
      }elseif($item->status==3 && !$item->final_approver_id){
        $status = 2;
      }else{
        $status = 0;
      }
      $query.= "('$item->from_student_id', '$item->to_student_id', '$item->note', '$item->transfer_date', '$status','$item->creator_id', '$item->created_at', '$item->amount_transferred', '$item->amount_received', '$item->session_transferred', '$item->session_received', 
      '$item->from_branch_id','$item->to_branch_id','$item->from_product_id','$item->to_product_id','".(int)$item->from_class_id."','".(int)$item->to_class_id."','$item->final_approver_id','$item->final_approved_at','$item->final_approve_comment','$item->approver_id','$item->approved_at','$item->comment',
      '".(int)$item->editor_id."','$item->updated_at','$item->meta_data','$item->attached_file','$item->id'),";
    }
    $query = substr($query, 0, -1);
    u::query($query);
    return "ok";
  }
  public function convertDataBranchTransfer(){
    $data = u::query("SELECT c.* FROM class_transfer AS c WHERE c.type>0 AND (SELECT count(id) FROM branch_transfer WHERE convert_id=c.id)=0");
    $query = "INSERT INTO branch_transfer (student_id, from_branch_id, to_branch_id, transferred_amount, transferred_sessions, creator_id, created_at, `status`, note, transfer_date, `type` ,
      exchanged_sessions, exchanged_amount,from_approver_id,from_approved_at,from_approve_comment,accounting_approver_id, accounting_approved_at, accounting_approve_comment, to_approver_id, to_approved_at, to_approve_comment, 
      meta_data, semester_id, from_product_id, to_product_id, updated_at, attached_file,convert_id) VALUES ";
    foreach($data AS $item){
      if($item->status==0){
        $status = 1;
      }elseif($item->status==1){
        $status = 4;
      }elseif($item->status==2){
        $status = 5;
      }elseif($item->status==3){
        $status = 6;
      }elseif($item->status==4){
        $status = 2;
      }elseif($item->status==5){
        $status = 3;
      }elseif($item->status==6){
        $status = 7;
      }else{
        $status = 0;
      }
      $query.= "('$item->student_id', '$item->from_branch_id', '$item->to_branch_id', '$item->amount_exchange', '$item->session_exchange', '$item->creator_id', '$item->created_at', '$status', '$item->note', '$item->transfer_date', '$item->type', 
      '$item->session_exchange','$item->amount_exchange','$item->from_approver_id','$item->from_approved_at','$item->from_approve_comment','$item->final_approver_id','$item->final_approved_at','$item->final_approve_comment','$item->to_approver_id','$item->to_approved_at','$item->to_approve_comment',
      '$item->meta_data','$item->semester_id','$item->from_product_id','$item->to_product_id','$item->updated_at','$item->attached_file','$item->id'),";
    }
    $query = substr($query, 0, -1);
    u::query($query);
    return "ok";
  }
  public function recallApiCyberEnrolmentSummer(Request $request){
    $enrolment_start_date = isset($request->enrolment_start_date) ? $request->enrolment_start_date:"";
    if($enrolment_start_date){
      $sql = isset($request->contract_id) ? " AND c.id= $request->contract_id " : "";
      $query = "SELECT c.created_at, c.id, (SELECT accounting_id FROM branches WHERE id = c.branch_id) AS branch_accounting_id,
                          s.accounting_id AS student_accounting_id,
                          s.gud_name1 AS parent,
                          c.bill_info,
                          t.accounting_id AS tuition_fee_accounting_id,
                          t.receivable AS tuition_fee_receivable,
                          c.total_sessions,
                          c.tuition_fee_price,
                          t.discount AS tuition_fee_discount,
                          c.must_charge,
                          c.total_discount,
                          s.date_of_birth,
                          '$enrolment_start_date' AS enrolment_start_date,
                          c.accounting_id,
                          c.`total_charged`,
                          c.real_sessions,
                          c.summary_sessions
                  FROM contracts c LEFT JOIN students s ON c.student_id = s.id LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
                  WHERE c.product_id=4 AND c.status!=7 $sql";
        $res = u::query($query);
        foreach($res AS $row){
          $cyberAPI = new CyberAPI();
          $res = $cyberAPI->createEnrolment($row, $request->users_data->id);
          u::updateContract((object)array(
            'id'         => $row->id,
            'updated_at' => date('Y-m-d H:i:s'),
            'editor_id'  => $request->users_data->id,
            'status'     => 7,
            'action'      => "recall_cyber_enrolment_summer"
          ));
        }
      return "ok";
    }else{
      return "false";
    }
  }
  public function insertReportFullFeeActive(Request $request, $cms_id, $report_month, $contract_id){
    if($cms_id!='' && $report_month!='' && $contract_id!=''){
      $student_info = u::first("SELECT s.*,t.branch_id,t.cm_id FROM students AS s LEFT JOIN term_student_user AS t ON t.student_id = s.id WHERE s.crm_id LIKE '%$cms_id%'");
      $contract_info = u::first("SELECT (SELECT `enrolment_start_date` FROM `log_contracts_history` WHERE student_id = c.student_id AND enrolment_start_date IS NOT NULL AND `type` <> 0 ORDER BY id LIMIT 1) AS date_start, c.* FROM contracts AS c WHERE c.id=$contract_id");
      if($student_info && $contract_info){
        $report_full_fee_active = u::first("SELECT * FROM report_full_fee_active WHERE student_id=$student_info->id AND report_month='$report_month'");
        if(!$report_full_fee_active){
          $created_at = date('Y-m-d H:i:s');
          $last_date  = $contract_info->enrolment_last_date ? $contract_info->enrolment_last_date : $contract_info->end_date; 
          u::query("INSERT INTO report_full_fee_active (student_id,contract_id,class_id,product_id,cm_id,report_month,branch_id,created_at,creator_id,last_date,date_start)
          VALUES (
            '$student_info->id',
            '$contract_info->id',
            '$contract_info->class_id',
            '$contract_info->product_id',
            '$student_info->cm_id',
            '$report_month',
            '$student_info->branch_id',
            '$created_at',
            '99999',
            '$last_date',
            '$contract_info->date_start')");
        }
      }
    }
    return 'ok';
  }
  public function recallApiCyberBranchTransfer(Request $request){
    $contract_id = $request->contract_id;
    if($contract_id){
      $branch_transfer = new BranchTransfer();
      $branch_transfer->callCyberContract($contract_id,$request->users_data->id,'Chuyển trung tâm');
    }
    return "ok";
  }
  public function withdrawAll(Request $request){
    $user_id = $request->users_data->id;
    self::processWithdrawAll($user_id);
    return "ok";
  }
  public static function processWithdrawAll($user_id){
    $date= date('Y-m-d',time()-3600*24*7);
    $date_trial = date('Y-m-d',time()-3600*24);
    $contracts = u::query("SELECT c.* FROM contracts AS c LEFT JOIN classes AS cls ON cls.id=c.class_id 
      WHERE c.status!=7 AND (c.enrolment_last_date < '$date' OR (c.enrolment_last_date < '$date_trial' AND cls.semester_id=5))");
    foreach($contracts AS $contract){
      $current_contract=$contract;
      $id = $contract->id;
      if ($current_contract && isset($current_contract->type) && $current_contract->type === 0) {
        $update_contract_data = [
          'id' => $id,
          'status' => 7,
          'enrolment_updated_at'=>date('Y-m-d H:i:s'),
          'enrolment_end_date' => date('Y-m-d'),
          'editor_id' => $user_id,
          'updated_at' => date('Y-m-d H:i:s'),
          'action' => 'withdraw_for_contract_'.$id.'_'.date('Ymd'),
          'enrolment_withdraw_date' =>date('Y-m-d'),
          'enrolment_withdraw_reason' => 'Rời khỏi lớp học thử',
          'enrolment_expired_date'=>date('Y-m-d')
        ];
        $info_change = 'Withdraw học sinh ra khỏi lớp học trải nghiệm';
      } else {
        $enrolment_last_date = $current_contract->enrolment_last_date;
        $set_last_date = $enrolment_last_date >= date('Y-m-d') ? date('Y-m-d') : $enrolment_last_date;
        $info_change = 'Withdraw học sinh do quá hạn số buổi học';
        $update_contract_data = [
          'id' => $id,
          'status' => 7,
          'updated_at' => date('Y-m-d H:i:s'),
          'enrolment_withdraw_date' =>date('Y-m-d'),
          'enrolment_updated_at'=>date('Y-m-d H:i:s'),
          'enrolment_end_date' => date('Y-m-d'),
          'enrolment_last_date'=>$set_last_date,
          'editor_id' => $user_id,
          'action' => 'withdraw_for_contract_'.$id.'_'.date('Ymd'),
          'enrolment_withdraw_reason' => 'Quá hạn số buổi được học',
          'enrolment_expired_date'=>date('Y-m-d')
        ];
      }
      if ($current_contract) {
        $current_time = date('Y-m-d H:i:s');
        DB::table('log_student_update')->insert(
          [
            'student_id' => $current_contract->student_id,
            'updated_by' => $user_id,
            'cm_id' => $current_contract->cm_id,
            'status' => 1,
            'branch_id' => $current_contract->branch_id,
            'ceo_branch_id' => 0,
            'content' => $info_change,
            'updated_at' => $current_time
          ]
        );
        if (count($update_contract_data)) {
          u::updateContract((Object)$update_contract_data);
        }
      }
      $lsmAPI = new LMSAPIController();
      $lsmAPI->updateStudentLMS($current_contract->student_id);
    }
  }
  public function recallApiCyberCreateEnrolment(Request $request, $contract_id)
  {
    $query = "SELECT c.created_at, c.id, (SELECT accounting_id FROM branches WHERE id = c.branch_id) AS branch_accounting_id,
                        s.accounting_id AS student_accounting_id,
                        s.gud_name1 AS parent,
                        c.bill_info,
                        t.accounting_id AS tuition_fee_accounting_id,
                        t.receivable AS tuition_fee_receivable,
                        c.total_sessions,
                        c.tuition_fee_price,
                        t.discount AS tuition_fee_discount,
                        c.must_charge,
                        c.total_discount,
                        s.date_of_birth,
                        c.enrolment_start_date,
                        c.accounting_id,
                        c.`total_charged`,
                        c.real_sessions,
                        c.summary_sessions
                FROM contracts c LEFT JOIN students s ON c.student_id = s.id LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
                WHERE c.id = $contract_id";
    $res = u::first($query);
    $cyberAPI = new CyberAPI();
    $res = $cyberAPI->createEnrolment($res, $request->users_data->id);
    
    return "ok";
  }
  public function sendSms(Request $request)
  {
    $arr_phone = explode(",",$request->phone);
    $sms = new Sms();
    foreach($arr_phone AS $phone){
      $sms->sendSms($phone,$request->content);
    }
    
    return "ok";
  }
  public function callCyberHocvien(Request $request){
    $data = u::query("SELECT * FROM tmp_cyber_hocvien WHERE status=0 LIMIT 100");
    foreach ($data AS $row){
      
      $request->api_method = 'POST';
      $request->api_url = getenv('CYBER_SERVER_ADDRESS') . 'HocVien';
      $request->api_params = [
        "Ma_kh" => $row->Ma_kh,
        "Ten_kh" => $row->Ten_kh,
        "Dia_chi" => $row->Dia_chi,
        "Ngay_sinh" => $row->Ngay_sinh,
        "Dien_thoai" => $row->Dien_thoai,
        "GT" => $row->GT,
        "suc_khoe" => $row->suc_khoe,
        "Kha_nang" => $row->Kha_nang,
        "ten_me" => $row->ten_me,
        "DT_me" => $row->DT_me,
        "email_me" => $row->email_me,
        "ten_bo" => $row->ten_bo,
        "DT_bo" =>$row->DT_bo,
        "email_bo" => $row->email_bo,
        "Ma_Tp" => $row->Ma_Tp,
        "Ma_quan" =>$row->Ma_quan,
        "Ma_xa" => $row->Ma_xa,
        "Ghi_chu" => $row->Ghi_chu,
        "User_Name" =>$row->User_Name,
      ];

      $request->api_author = $request->users_data->id;
      $request->act = "Thêm mới học sinh";
      $res = $this->callAPI($request);
      u::query("UPDATE tmp_cyber_hocvien SET status=1, response =".json_encode($res)." WHERE id = $row->id");
      
    }
  }
  public function callCyberGoiphi(Request $request){
    $data = u::query("SELECT * FROM tmp_cyber_goiphi WHERE status=0 LIMIT 100");
    foreach ($data AS $row){
      
      $request->api_method = 'POST';
      $request->api_url = getenv('CYBER_SERVER_ADDRESS') . 'ThongTinDangKyHoc';
      $request->api_params = [
        "TOKENKEY"=>$row->TOKENKEY,
        "ngay_ct" => $row->ngay_ct,
        "Ma_post" => $row->Ma_post,
        "So_ct" => $row->So_ct,
        "Ma_TTLN" => $row->Ma_TTLN,
        "Ma_KH" => $row->Ma_KH,
        "ong_ba" => $row->ong_ba,
        "Ma_Hs" => $row->Ma_Hs,
        "ma_Td2" => $row->ma_Td2,
        "Nguoi_don" => $row->Nguoi_don,
        "Ngay_sinh" => $row->Ngay_sinh,
        "dien_giai" =>$row->dien_giai,
        "Is_Doi_Goi" => $row->Is_Doi_Goi,
        "Ma_vv_i" => $row->Ma_vv_i,
        "Gt1" => $row->Gt1,
        "Gt2" => $row->Gt2,
        "Tien_CK" => $row->Tien_CK,
        "Phai_nop" => $row->Phai_nop,
        "MA_TD1_I" => $row->MA_TD1_I,
        "TL_CK" => $row->TL_CK,
        "TIEN_NT" => $row->TIEN_NT,
        "TIEN_CK2" =>$row->TIEN_CK2,
        "TIEN_CL" => $row->TIEN_CL,
        "MA_CD_I" => $row->MA_CD_I,
        "MA_DVCS" =>$row->MA_DVCS,
        "User_name" =>$row->User_name,
        "So_buoiKM" => $row->So_buoiKM,
        "So_thangKM" => $row->So_thangKM,
        "Tien_KM" => $row->Tien_KM,
      ];

      $request->api_author = $request->users_data->id;
      $request->act = "Thêm mới hợp đồng";
      $res = $this->callAPI($request);
      u::query("UPDATE tmp_cyber_goiphi SET status=1, response =".json_encode($res)." WHERE id = $row->id");
      
    }
  }
  public function callCyberXeplop(Request $request){
    $data = u::query("SELECT * FROM tmp_cyber_xeplop WHERE status=0 LIMIT 100");
    foreach ($data AS $row){
      if(!$row->So_ct && $row->Ma_KH){
        $query = "SELECT c.created_at, c.id, (SELECT accounting_id FROM branches WHERE id = c.branch_id) AS branch_accounting_id,
                        s.accounting_id AS student_accounting_id,
                        s.gud_name1 AS parent,
                        c.bill_info,
                        t.accounting_id AS tuition_fee_accounting_id,
                        t.receivable AS tuition_fee_receivable,
                        c.total_sessions,
                        c.tuition_fee_price,
                        t.discount AS tuition_fee_discount,
                        c.must_charge,
                        c.total_discount,
                        s.date_of_birth,
                        c.enrolment_start_date,
                        c.accounting_id,
                        c.`total_charged`,
                        c.real_sessions,
                        c.summary_sessions
                FROM contracts c LEFT JOIN students s ON c.student_id = s.id LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
                WHERE s.accounting_id = '$row->Ma_KH' AND c.status!=7 AND c.class_id IS NOT NULL ";
        $res = u::first($query);
        if($res){
          $cyberAPI = new CyberAPI();
          $res = $cyberAPI->createEnrolment($res, $request->users_data->id);
          u::query("UPDATE tmp_cyber_xeplop SET status=1, response =".json_encode($res)." WHERE id = $row->id");
        }
      }else{
        $request->api_method = 'POST';
        $request->api_url = getenv('CYBER_SERVER_ADDRESS') . 'PhieuHoc';
        $request->api_params = [
          "ngay_ct" => $row->ngay_ct,
          "So_ctPNH" => $row->So_ctPNH,
          "So_ct" => $row->So_ct,
          "Ma_TTLN" => $row->Ma_TTLN,
          "Ma_KH" => $row->Ma_KH,
          "ong_ba" => $row->ong_ba,
          "dien_giai" => $row->dien_giai,
          "So_seri" => $row->So_seri,
          "So_HD" => $row->So_HD,
          "Ma_vv_i" => $row->Ma_vv_i,
          "Tg_hoc" => $row->Tg_hoc,
          "SL_Buoi" => $row->SL_Buoi,
          "Gt1" => $row->Gt1,
          "CK_NT" => $row->CK_NT,
          "TIEN_CL" => $row->TIEN_CL,
          "GT_PB" => $row->GT_PB,
          "Ma_thue" => $row->Ma_thue,
          "Thue_suat" => $row->Thue_suat,
          "thue_nt" => $row->thue_nt,
          "MA_Phi_I" => $row->MA_Phi_I,
          "User_name" => $row->User_name,
        ];

        $request->api_author = $request->users_data->id;
        $request->act = "Thêm mới đăng ký lớp";
        $res = $this->callAPI($request);
        u::query("UPDATE tmp_cyber_xeplop SET status=1, response =".json_encode($res)." WHERE id = $row->id");
      }
    }

    $data = u::query("SELECT * FROM tmp_cyber_xeplop WHERE status=2 LIMIT 100");
    foreach ($data AS $row){
      $query = "SELECT c.created_at, c.id, (SELECT accounting_id FROM branches WHERE id = c.branch_id) AS branch_accounting_id,
                      s.accounting_id AS student_accounting_id,
                      s.gud_name1 AS parent,
                      c.bill_info,
                      t.accounting_id AS tuition_fee_accounting_id,
                      t.receivable AS tuition_fee_receivable,
                      c.total_sessions,
                      c.tuition_fee_price,
                      t.discount AS tuition_fee_discount,
                      c.must_charge,
                      c.total_discount,
                      s.date_of_birth,
                      c.enrolment_start_date,
                      c.accounting_id,
                      c.`total_charged`,
                      c.real_sessions,
                      c.summary_sessions,
                      c.coupon,
                  c.debt_amount,
                  c.start_date,
                  c.sibling_discount,
                  c.discount_value,
                  c.bonus_sessions,
                  c.bonus_amount,
                      (SELECT accounting_id FROM users WHERE id = c.ec_id) AS sale_accounting_id,
                      c.bill_info,c.note,c.ref_code,'$row->Ma_KH' AS Ma_KH,
                      c.must_charge AS tien_cl
              FROM contracts c LEFT JOIN students s ON c.student_id = s.id LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
              WHERE s.accounting_id = '$row->Ma_KH' AND c.status!=7 AND c.class_id IS NULL ";
      $list = u::query($query);
      foreach($list AS $rows){
        $cyberAPI = new CyberAPI();
        $response = $cyberAPI->createContract($rows, $request->users_data->id);  
        u::query("UPDATE tmp_cyber_xeplop SET status=1, response =".json_encode($response)." WHERE id = $row->id");
      } 

      $query = "SELECT c.created_at, c.id, (SELECT accounting_id FROM branches WHERE id = c.branch_id) AS branch_accounting_id,
                      s.accounting_id AS student_accounting_id,
                      s.gud_name1 AS parent,
                      c.bill_info,
                      t.accounting_id AS tuition_fee_accounting_id,
                      t.receivable AS tuition_fee_receivable,
                      c.total_sessions,
                      c.tuition_fee_price,
                      t.discount AS tuition_fee_discount,
                      c.must_charge,
                      c.total_discount,
                      s.date_of_birth,
                      c.enrolment_start_date,
                      c.accounting_id,
                      c.`total_charged`,
                      c.real_sessions,
                      c.summary_sessions,
                      (SELECT accounting_id FROM users WHERE id = c.ec_id) AS sale_accounting_id,
                      c.bill_info,c.note,c.ref_code,
                      c.must_charge AS tien_cl,c.coupon,
                      c.debt_amount,
                  c.start_date,
                  c.sibling_discount,
                  c.discount_value,
                  c.bonus_sessions,
                  c.bonus_amount,'$row->Ma_KH' AS Ma_KH
              FROM contracts c LEFT JOIN students s ON c.student_id = s.id LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
              WHERE s.accounting_id = '$row->Ma_KH' AND c.status!=7 AND c.class_id IS NOT NULL ";
      $res = u::first($query);
      if($res){
        $cyberAPI = new CyberAPI();
        $response = $cyberAPI->createContract($res, $request->users_data->id);
        $response = $cyberAPI->createEnrolment($res, $request->users_data->id);
        u::query("UPDATE tmp_cyber_xeplop SET status=1, response =".json_encode($response)." WHERE id = $row->id");
      }
    }

    return "ok";
  }
  private function callAPI(Request $request)
  {
    $log = new LogCyberRequest();
    $url = $request->api_url;
    $author = $request->api_author;
    $method = $request->api_method;
    $params = $request->api_params ? $request->api_params : [];
    $act = $request->act;
    if (isset($params) && isset($params['Ma_TTLN']) && $params['Ma_TTLN'] === 'TEST'){
        return null;
    }
    $header = [
      "Content-Type: application/json",
      "Authorization: ".getenv("CYBER_SERVER_AUTHORIZATION")
    ];
    $res = null;
    // Log::info('CYBER_API:'.$url);
    // Log::info('CYBER_PARAMS:'.json_encode($params));
    try {
      $res = curl::curl($url, $method, $header, $params);
      if ($res) {
        $log->logCallingAPI($url, json_encode($request->api_params), json_encode($header), $method, date('Y-m-d H:i:s'), $res, $log->SUCCESS_STATUS, $author, $act);
      } else {
        $log->logCallingAPI($url, json_encode($request->api_params), json_encode($header), $method, date('Y-m-d H:i:s'), null, $log->FAILURE_STATUS, $author, $act);
      }
    } catch (\Exception $exception) {
      $log->logCallingAPI($url, json_encode($request->api_params), json_encode($header), $method, date('Y-m-d H:i:s'), null, $log->FAILURE_STATUS, $author, $act);
    }

    return $res;
  }

  public function getDoneSessions(){
    u::query("DELETE FROM tmp_contracts ");
    $contracts = u::query("SELECT id AS contract_id, summary_sessions, bonus_sessions, class_id, enrolment_start_date 
      FROM contracts WHERE `status`!=7");
    self::addItemsTmpContract($contracts);
    u::query("UPDATE tmp_contracts SET status=1 where class_id=0");
    $list = u::query("SELECT t.*,c.enrolment_start_date FROM tmp_contracts AS t LEFT JOIN contracts AS c ON c.id=t.contract_id WHERE t.status=0 AND t.class_id!=0");
    foreach($list AS $row){
      $done_sessions = 0;
      if($row->class_id){
        $reserved_dates = JobsController::getReservedDates_transfer($row->contract_id);
        $tmp_holiDay = u::getPublicHolidays($row->class_id);
        if (!empty($reserved_dates)) {
          $holiDay = array_merge($tmp_holiDay, $reserved_dates);
        }else{
          $holiDay = $tmp_holiDay;
        }
        $class_days = u::getClassDays($row->class_id);
        $data = u::calSessions($row->enrolment_start_date,date('Y-m-d'),$holiDay,$class_days);
        if($data){
          $done_sessions = $data->total;
        }
      }
      u::query("UPDATE tmp_contracts SET 
          done_sessions='$done_sessions',
          updated_at='".date('Y-m-d H:i:s')."',
          status=1
          WHERE id= $row->id
      ");
    }
    return "ok";
  }
  public static function addItemsTmpContract($list) {
    if ($list) {
        $created_at = date('Y-m-d H:i:s');
        $query = "INSERT INTO tmp_contracts (contract_id,summary_sessions, bonus_sessions, class_id, updated_at) VALUES ";
        if (count($list) > 5000) {
            for($i = 0; $i < 5000; $i++) {
                $item = $list[$i];
                $query.= "('$item->contract_id', '$item->summary_sessions', '$item->bonus_sessions', '$item->class_id','$created_at'),";
            }
            $query = substr($query, 0, -1);
            u::query($query);
            self::addItemsTmpContract(array_slice($list, 5000));
        } else {
            foreach($list as $item) {
              $query.= "('$item->contract_id', '$item->summary_sessions', '$item->bonus_sessions', '$item->class_id','$created_at'),";
            }
            $query = substr($query, 0, -1);
            u::query($query);
        }
    }
}

  public function actionTool13(Request $request){
    $cms_id = $request->cms_id;
    $student_info =u::first("SELECT * FROM students WHERE crm_id LIKE '%$cms_id'");
    if($student_info){
      $cyberAPI = new CyberAPI();
      $res = $cyberAPI->createStudent($student_info,3);
      if($res){
        u::query("UPDATE students SET accounting_id = '$res' WHERE id=".$student_info->id);
      }
    }
    return "ok";
  }
  public function actionTool14(Request $request){
    $cms_id = $request->cms_id;
    $student_info =u::first("SELECT c.id AS contract_id FROM contracts AS c LEFT JOIN students AS s ON s.id= c.student_id WHERE s.crm_id LIKE '%$cms_id'
      AND c.class_id IS NOT NULL AND c.status!=7 ORDER BY c.count_recharge DESC ,c.id DESC  LIMIT 1");
    if($student_info){
      $lms_api = new LMSAPIController();
      $lms_api->createStudentLMS($student_info->contract_id);
    }
    return "ok";
  }
  public function actionTool15(Request $request){
    $cms_id = $request->cms_id;
    $student_info =u::first("SELECT id FROM students WHERE crm_id LIKE '%$cms_id'");
    if($student_info){
      $lms_api = new LMSAPIController();
      $lms_api->updateStudentLMS($student_info->id);
    }
    return "ok";
  }
  public function processReserveTransferOnline(){
    u::query("UPDATE tmp_reserve_online AS t LEFT JOIN students AS s oN s.accounting_id=t.accounting_id SET t.student_id=s.id WHERE t.student_id IS NULL");
    $list_student = u::query("SELECT t.note,t.id,t.month,t.is_reserve,t.sessions, c.id AS contract_id, c.student_id,c.class_id,c.product_id,c.program_id,c.branch_id,c.enrolment_last_date,c.enrolment_start_date FROM tmp_reserve_online AS t LEFT JOIN contracts AS c ON c.student_id=t.student_id 
      WHERE t.status=0 AND c.status!=7 AND c.class_id IS NOT NULL");
    $sql_insert = "INSERT INTO reserves (student_id, `type`, `start_date`, `session`, `end_date`, `status`, is_reserved, contract_id, branch_id, product_id, program_id, class_id, is_transfer_online,created_at,note) VALUES ";
    $j =0;
    $sql_update = "INSERT INTO tmp_reserve_online (id,status,updated_at) VALUES";
    foreach($list_student AS $student){
      $sql_update .= " ($student->id,1,'".date('Y-m-d H:i:s')."'),";
      $holiDay = u::getPublicHolidays(0, $student->branch_id, $student->product_id);
      $class_days = u::getClassDays($student->class_id);
      $tmp_start_month = date('Y-m-01',strtotime($student->month));
      $tmp_end_month = date('Y-m-t',strtotime($student->month));
      $start_date = $tmp_start_month > $student->enrolment_start_date ? $tmp_start_month : $student->enrolment_start_date;
      $end_date = $tmp_end_month;
      $done_sessions = u::calculatorSessions($start_date,$end_date,$holiDay,$class_days);
      $i=0;
      if(!empty($done_sessions->dates)){
        foreach($done_sessions->dates AS $date){
          if($student->is_reserve == 1 || $i< $student->sessions){
            $i++;
            $sql_insert.="('$student->student_id','0','$date',1,'$date',2,1,'$student->contract_id','$student->branch_id','$student->product_id','$student->program_id','$student->class_id',1,'".date('Y-m-d H:i:s')."','$student->note'),";
            $j++;
          }
        }
      }
      
    }
    if($j>0){
      $sql_insert = substr($sql_insert, 0, -1);
      u::query($sql_insert);
    }
    if(count($list_student)){
      $sql_update = substr($sql_update, 0, -1);
      $sql_update .= " ON DUPLICATE KEY UPDATE status=VALUES(status),updated_at= VALUES(updated_at)";
      u::query($sql_update);
    }
    return "ok";
  }
  public function processTransferAllBranch(){
    u::query("UPDATE tmp_transfer_all_branch AS t LEFT JOIN students AS s ON s.accounting_id=t.accounting_id SET t.student_id=s.id WHERE t.student_id IS NULL");
    $list = u::query("SELECT * FROM tmp_transfer_all_branch WHERE status =0");
    $branch_transfer = new BranchTransfer();
    foreach($list AS $row){
      $new_branch_info = u::first("SELECT b.id AS branch_id, b.accounting_id,
          (SELECT user_id FROM term_user_branch WHERE role_id=56 AND status=1 AND branch_id=b.id LIMIT 1) AS om_id,
          (SELECT user_id FROM term_user_branch WHERE role_id=69 AND status=1 AND branch_id=b.id LIMIT 1) AS ecl_id,
          (SELECT user_id FROM term_user_branch WHERE role_id=686868 AND status=1 AND branch_id=b.id LIMIT 1) AS ceo_branch_id
        FROM branches AS b WHERE b.id=$row->to_branch_id");
      $lmsAPI = new LMSAPIController();
      $lmsAPI->updateStudentLMS($row->student_id,0,true);
      //update contracts
      $list_contracts = u::query("SELECT * FROM contracts WHERE student_id=$row->student_id AND status!=7");
      foreach ($list_contracts as $i => $contract) {
        if ($contract && ($contract->type == 8 || $contract->type == 85 || $contract->type == 86)) {
          $type = 85;
        } elseif ($contract && $contract->type == 0) {
          $type = 0;
        } else {
          $type = 5;
        }
        if ($contract && ($contract->type == 0 || $contract->type == 8 || $contract->type == 85 || $contract->type == 86)) {
          $tmp_status = 5;
        } else {
          $tmp_status = 3;
        }
        if($contract->class_id){
          $transfer_date = $row->date_transfer;
          $class_day = u::getClassDays($contract->class_id);
          $public_holiday = u::getPublicHolidays($contract->class_id, $contract->branch_id, $contract->product_id);
          $reserved_dates = $branch_transfer->getReservedDates_transfer([$contract->id]);
          $merged_holi_day = $reserved_dates && isset($reserved_dates[$contract->id]) ? array_merge($public_holiday, $reserved_dates[$contract->id]) : $public_holiday;
          $done_sessions = u::calSessions($contract->start_date, date('Y-m-d', strtotime($transfer_date)-24*3600), $merged_holi_day, $class_day);
        
          $data_left_sessions = $contract->real_sessions - $done_sessions->total > 0 ? $contract->real_sessions - $done_sessions->total : 0;
            
          if ($contract->real_sessions) {
            $data_left_amount = ceil($contract->total_charged * $data_left_sessions / $contract->real_sessions);
          } else {
            $data_left_amount = $contract->total_charged;
          }
          $data_bonus_sessions = $contract->bonus_sessions;
          if ($contract->type == 0) {
            $data_bonus_sessions = $contract->bonus_sessions - $done_sessions->total > 0 ? $contract->bonus_sessions - $done_sessions->total : 0;
          } else {
            if ($contract->real_sessions < $done_sessions->total) {
              $data_bonus_sessions = $contract->real_sessions + $contract->bonus_sessions - $done_sessions->total > 0 ? $contract->real_sessions + $contract->bonus_sessions - $done_sessions->total : 0;
            }
          }
          $arr_txt_accounting_id = explode('.',$contract->accounting_id);
          u::updateContract((object) [
            "action" => "branch_transfer_contract_" . $contract->id,
            "id" => $contract->id,
            'accounting_id' => isset($arr_txt_accounting_id[3])? $new_branch_info->accounting_id.'.'.date('y').'.PNH.'.(9000000+(int)$arr_txt_accounting_id[3]):'',
            "type" => $type,
            "status" => $tmp_status,
            "branch_id" => $row->to_branch_id,
            "ceo_branch_id" => $new_branch_info->ceo_branch_id,
            "ec_id" => $new_branch_info->ecl_id,
            "ec_leader_id" => $new_branch_info->ecl_id,
            "cm_id" => $new_branch_info->om_id,
            "om_id" => $new_branch_info->om_id,
            "total_charged" => $data_left_amount,
            "start_date" => $transfer_date,
            "real_sessions" => $data_left_sessions,
            "bonus_sessions" => $data_bonus_sessions,
            "summary_sessions" => $data_left_sessions + $data_bonus_sessions,
            "program_id" => NULL,
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
            "updated_at"=>date('Y-m-d H:i:s'),
          ]);
        }else{
          u::updateContract((object) [
            "action" => "branch_transfer_contract_" . $contract->id,
            "id" => $contract->id,
            'accounting_id' => isset($arr_txt_accounting_id[3])? $new_branch_info->accounting_id.'.'.date('y').'.PNH.'.(9000000+(int)$arr_txt_accounting_id[3]):'',
            "type" => $type,
            "status" => $tmp_status,
            "branch_id" => $row->to_branch_id,
            "ceo_branch_id" => $new_branch_info->ceo_branch_id,
            "ec_id" => $new_branch_info->ecl_id,
            "ec_leader_id" => $new_branch_info->ecl_id,
            "cm_id" => $new_branch_info->om_id,
            "om_id" => $new_branch_info->om_id,
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
            "updated_at"=>date('Y-m-d H:i:s'),
          ]);
        }
        $branch_transfer->callCyberContract($contract->id,1,'Chuyển trung tâm');
      }

      //update studet info
      u::query("UPDATE students SET branch_id = $new_branch_info->branch_id WHERE id=$row->student_id");
      u::query("UPDATE term_student_user SET 
        om_id = $new_branch_info->om_id, 
        cm_id = $new_branch_info->om_id,
        ec_id=$new_branch_info->ecl_id, 
        ec_leader_id=$new_branch_info->ecl_id,
        teacher_id = NULL,
        branch_id=$new_branch_info->branch_id WHERE student_id=$row->student_id");

      u::query("UPDATE tmp_transfer_all_branch SET status=1, updated_at='".date('Y-m-d H:i:s')."'WHERE id=$row->id");
    }
    return "ok";
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
  public function processUpdateCoupon(Request $request){
    $response = new Response();
    $data = null;
    $code = APICode::SUCCESS;
    $contract_info = u::first("SELECT * FROM contracts WHERE id = $request->contract_id");
    $coupon_info = u::first("SELECT * FROM coupons WHERE code = '$request->coupon'");
    if(!$contract_info){
      $data = array('status'=>'error','message'=>'Không tồn tại mã giao dịch');
    }elseif(!$coupon_info){
      $data = array('status'=>'error','message'=>'Không tồn tại mã coupon');
    }elseif($coupon_info->status == 2){
      $data = array('status'=>'error','message'=>'Coupon đã được xử dụng');
    }else{
      // $curr_coupon_info = u::first("SELECT * FROM coupons WHERE code = '$contract_info->coupon'");
      // if($curr_coupon_info){
      //   $discount_amount = $coupon_info->coupon_amount - $curr_coupon_info->coupon_amount;
      //   $coupon_session = $coupon_info->coupon_session - $curr_coupon_info->coupon_session;
      //   $coupon_bonus_amount = $coupon_info->bonus_amount - $curr_coupon_info->bonus_amount;
      // }else{
        $discount_amount = $coupon_info->coupon_amount;
        $coupon_session = $coupon_info->coupon_session;
        $coupon_bonus_amount = $coupon_info->bonus_amount;
      // }
      $must_charge = $contract_info->must_charge -  $discount_amount;
      $discount_value = $contract_info->total_discount + $discount_amount;
      $coupon = $coupon_info->code;
      $debt_amount = $contract_info->must_charge - $contract_info->total_charged;
      $bonus_sessions =  (int)$contract_info->bonus_sessions + $coupon_session;
      $bonus_amount = (int)$contract_info->bonus_amount + $coupon_bonus_amount;
      $summary_sessions = (int)$contract_info->summary_sessions  + $coupon_session; 
      if($must_charge!= $contract_info->must_charge){
        u::query("UPDATE payment SET must_charge = $must_charge WHERE contract_id = $contract_info->id");
        u::query("UPDATE payment SET debt = must_charge-total WHERE contract_id=$contract_info->id");
      }
      u::updateContract((object)[
          "id" => $contract_info->id,
          "must_charge" => $must_charge,
          'discount_value' => $discount_value ? $discount_value : 0,
          "bonus_sessions" => $bonus_sessions ? $bonus_sessions : 0,
          "debt_amount" => $debt_amount ? $debt_amount : 0,
          "bonus_amount" => $bonus_amount ? $bonus_amount : 0,
          "summary_sessions"=>$summary_sessions ? $summary_sessions :0,
      ]);
      u::query("UPDATE coupons SET status=2,checked_date =".date('Y-m-d')." WHERE id=$coupon_info->id");
      u::query("INSERT INTO coupon_logs (coupon_id,contract_id,created_at) VALUES ('$coupon_info->id','$contract_info->id','".date('Y-m-d H:i:s')."')");
      // if($curr_coupon_info){
      //   u::query("UPDATE coupons SET status=1,checked_date = NULL WHERE id=$curr_coupon_info->id");
      //   u::query("DELETE FROM coupon_logs  WHERE coupon_id=$curr_coupon_info->id");
      // }
      $data = array('status'=>'success','message'=>'Cập nhật thành công');
    }
    return $response->formatResponse($code, $data);
  }
  public function genReportWeek(){
    // $start_date='2022-02-28';
    // $max_end_date = '2023-03-12';
    // $end_date = date('Y-m-d',strtotime($start_date)+6*24*3600);
    // $i=1;
    // $j=1;
    // while($end_date <= $max_end_date){
    //   $year = "2022";
    //   $month =ceil($i/4);
    //   $group = ceil($j/12);
    //   u::query("INSERT INTO report_weeks (`start_date`,`end_date`,`year`,`month`,`group`) VALUES ('$start_date','$end_date','$year','$month','$group')");
    //   $i++;
    //   if($i==16) $i=1;
    //   $j++;
    //   if($j==48) $j=1;
    //   $start_date = date('Y-m-d',strtotime($start_date)+7*24*3600);
    //   $end_date = date('Y-m-d',strtotime($start_date)+6*24*3600);
    // }
    $list = u::query("SELECT * FROM report_weeks ");
    $i=1;
    $j=1;
    foreach($list AS $k=>$row){
      $month =ceil($i/4);
      $group = ceil($j/12);
      u::query("UPDATE report_weeks SET month='$month',`group`='$group' WHERE id= $row->id");
      $i++;
      if($i>16) $i=1;
      $j++;
      if($j>48) $j=1; 
    }
    echo "ok";
  }
}
