<?php

namespace App\Http\Controllers;

use App\Models\Mail;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\ProcessExcel;
use App\Models\Sms;
use Illuminate\Support\Facades\DB;
use App\Models\CyberAPI;

class JobsController extends Controller
{
  public function index(Request $request)
  {
    $report = new ReportsController();
    //update report_full_fee_active
    $report->collectFullFeeActive( $request,'_','_');
    //update report_get_users
    $report->collectReportGetUser( $request,'_');
    $report->collectReportReserve( $request,'_');
    $report->collectReportPending( $request,'_');

    $job = new JobsController();
    //update renew_report
    $job->updateCompletedDate();
    $job->updateAllRenewedDates();
    $job->updateRenewReport();
    return "ok";
  }
  public function update(Request $request)
  {
    $cms_id = isset($request->cms_id) ? $request->cms_id : NULL;
    $branch_id = isset($request->branch_id) ? $request->branch_id : NULL;
    $student_info = u::first("SELECT id FROM students WHERE cms_id = '$cms_id' OR crm_id='$cms_id'");
    $student_id = $student_info ? $student_info->id : NULL;
    $this->updateEnrolmentLastDate($branch_id,$student_id);
    return "ok";
  }
  public function updateToolsRenewReport(Request $request){
    $cms_id = isset($request->cms_id) ? $request->cms_id : NULL;
    $student_info = u::first("SELECT id FROM students WHERE cms_id = '$cms_id' OR crm_id='$cms_id'");
    $student_id = $student_info ? $student_info->id : NULL;
    if($student_id){
      self::updateRenewByStudent($student_id);
    }else{
      self::updateCompletedDate();
      self::updateAllRenewedDates();
      self::updateRenewReport();
    }
    return "ok";
  }
  public static  function updateCompletedDate()
  {
    u::query("UPDATE contracts AS c LEFT JOIN payment AS  p ON p.id=c.payment_id SET c.completed_date=p.charge_date WHERE c.debt_amount=0 AND c.must_charge>0 ");
    u::query("UPDATE contracts AS c LEFT JOIN contracts AS c1 ON c1.id=c.relation_contract_id SET c.completed_date=c1.completed_date WHERE c.relation_contract_id IS NOT NULL AND  c.completed_date IS NULL");
    return true;
  }
  public function updateRenewByStudent($student_id){
    if ($student_id) {
      self::updateCompletedDate();
      self::updateAllRenewedDates($student_id);
      self::updateRenewReport(NULL, $student_id);
    }
    return true;
  }
  public function updateAllRenewedDates($student_id = 0)
  {
    $cond = $student_id ? " c.student_id=$student_id" : " 1 ";
    $query = "SELECT c.id, c.type, c.class_id, c.enrolment_last_date, c.status,c1.id AS next_id, c1.type AS next_type, 
              c1.completed_date AS next_completed_date, c1.status AS next_status, 
              (SELECT id FROM log_contracts_history WHERE contract_id = c.id ORDER BY id DESC LIMIT 1) AS last_log_id,
              IF((SELECT count(id) FROM contracts WHERE relation_contract_id = c.id AND student_id=c.student_id AND id !=relation_contract_id) >0  ,1,0) is_data_curr
            FROM contracts c 
              LEFT JOIN contracts c1 ON c1.student_id=c.student_id 
                AND c1.id = (SELECT id FROM contracts WHERE student_id=c.student_id AND count_recharge>c.count_recharge AND product_id !=4 ORDER BY count_recharge LIMIT 1) 
            WHERE $cond AND c.product_id !=4";
    $contracts = u::query($query);
    if ($contracts) {
      $sql_update_contract = "INSERT INTO contracts (id,renewed_date,success_renewed_date,renew_runtime) VALUES";
      $sql_update_log_contract = "INSERT INTO log_contracts_history (id,renewed_date,success_renewed_date,renew_runtime) VALUES";
      $renew_runtime = date('Y-m-d H:i:s');
      foreach ($contracts as $contract) {
        // var_dump($contract);die();
        $resp = (object)[
          'renewed_date' => '',
          'success_renewed_date' => ''
        ];
        //process renewed_date
        if (
          in_array($contract->type, [0, 7])
          || ($contract->next_id && (in_array($contract->next_type, [3, 4, 8, 85, 86])))
          || !$contract->class_id
          || $contract->status == 2
          || $contract->is_data_curr == 1
        ) {
          $resp->renewed_date = '';
        } else {
          $resp->renewed_date = $contract->enrolment_last_date;
        }
        //process success_renewed_date
        if (!$resp->renewed_date || !$contract->next_id || ($contract->next_type == 7) || ($contract->next_status == 2)) {
          $resp->success_renewed_date = '';
        } else {
          $resp->success_renewed_date = $contract->next_completed_date;
        }
        $resp->renewed_date = strtotime($resp->renewed_date) > 0 ? "'$resp->renewed_date'" : "NULL";
        $resp->success_renewed_date = strtotime($resp->success_renewed_date) > 0 ? "'$resp->success_renewed_date'" : "NULL";
        $sql_update_contract .= $contract->id ? " ($contract->id,$resp->renewed_date,$resp->success_renewed_date,'$renew_runtime')," : '';
        $sql_update_log_contract .= $contract->last_log_id ? " ($contract->last_log_id,$resp->renewed_date,$resp->success_renewed_date,'$renew_runtime')," : '';
      }
      $sql_update_contract = substr($sql_update_contract, 0, -1);
      $sql_update_contract .= " ON DUPLICATE KEY UPDATE renewed_date=VALUES(renewed_date),success_renewed_date=VALUES(success_renewed_date),renew_runtime=VALUES(renew_runtime)";
      $sql_update_log_contract = substr($sql_update_log_contract, 0, -1);
      $sql_update_log_contract .= " ON DUPLICATE KEY UPDATE renewed_date=VALUES(renewed_date),success_renewed_date=VALUES(success_renewed_date),renew_runtime=VALUES(renew_runtime)";
      u::query($sql_update_contract);
      u::query($sql_update_log_contract);
    }
    return true;
  }
  public function updateRenewReport($renewed_month = NULL, $student_id = NULL)
  {
    $cond = $student_id ? " AND s.id = $student_id" : '';
    $renewed_month = $renewed_month ? $renewed_month : date('Y-m', time() - 7 * 3600);
    $query = "SELECT c.student_id, c.id AS contract_id, t.branch_id, c.product_id, c.class_id, t.ec_id, t.cm_id, t.ec_leader_id,
                  t.om_id, t.ceo_branch_id, c.renewed_date, c.success_renewed_date,
                  IF (c.success_renewed_date IS NOT NULL AND DATE_FORMAT(c.success_renewed_date, '%Y-%m') <= '$renewed_month', 1, 2) renewed_status,
                  IF ( DATE_FORMAT(c.renewed_date, '%Y-%m')< '$renewed_month' , '$renewed_month', DATE_FORMAT(c.renewed_date, '%Y-%m') ) renewed_month,
                  (SELECT tuition_fee_id FROM log_contracts_history WHERE contract_id=c1.id LIMIT 1) AS tuition_fee_id,
                  (SELECT must_charge FROM log_contracts_history WHERE contract_id=c1.id LIMIT 1) AS renew_amount 
              FROM contracts c 
                  LEFT JOIN students s ON c.student_id = s.id 
                  LEFT JOIN term_student_user t ON t.student_id = c.student_id
                  LEFT JOIN contracts c1 ON c1.student_id=c.student_id 
                    AND c1.id = (SELECT id FROM contracts WHERE student_id=c.student_id AND count_recharge>c.count_recharge AND product_id !=4 AND completed_date IS NOT NULL ORDER BY count_recharge LIMIT 1) 
              WHERE s.status>0 AND ((DATE_FORMAT(c.renewed_date, '%Y-%m') >= '$renewed_month' OR DATE_FORMAT(c.success_renewed_date, '%Y-%m') >= '$renewed_month'))
              AND (SELECT count(id) FROM renews_report WHERE contract_id = c.id AND fixed=1)=0 $cond";
    $data = u::query($query);
    if ($data) {
      $cond_del = $student_id ? " AND student_id = $student_id" : '';
      u::query("DELETE FROM renews_report WHERE renewed_month >= '$renewed_month' AND fixed!=1 $cond_del");
      self::addItems($data);
    }
    return "ok";
  }
  public function addItems($list)
  {
    if ($list) {
      $created_at = date('Y-m-d H:i:s');
      $query = "INSERT INTO renews_report (student_id, contract_id, branch_id, product_id, class_id, tuition_fee_id, ec_id, cm_id, renewed_cm_id, ec_leader_id, om_id, ceo_id, renew_amount, `status`, renewed_month, last_date, created_at) VALUES ";
      if (count($list) > 5000) {
        for ($i = 0; $i < 5000; $i++) {
          $item = $list[$i];
          $tuition_fee_id = $item->tuition_fee_id;
          $renewed_cm_id = $item->cm_id;
          $renew_amount = $item->renew_amount;
          $query .= "('$item->student_id', '$item->contract_id', '$item->branch_id', '$item->product_id', '$item->class_id', '$tuition_fee_id', '$item->ec_id', '$item->cm_id', '$renewed_cm_id','$item->ec_leader_id','$item->om_id',
                        '$item->ceo_branch_id', '$renew_amount', '$item->renewed_status', '$item->renewed_month', '$item->renewed_date', '$created_at' ),";
        }
        $query = substr($query, 0, -1);
        u::query($query);
        self::addItems(array_slice($list, 5000));
      } else {
        foreach ($list as $item) {
          $tuition_fee_id = $item->tuition_fee_id;
          $renewed_cm_id = $item->cm_id;
          $renew_amount = $item->renew_amount;
          $query .= "('$item->student_id', '$item->contract_id', '$item->branch_id', '$item->product_id', '$item->class_id', '$tuition_fee_id', '$item->ec_id', '$item->cm_id', '$renewed_cm_id','$item->ec_leader_id','$item->om_id',
                '$item->ceo_branch_id', '$renew_amount', '$item->renewed_status', '$item->renewed_month', '$item->renewed_date', '$created_at' ),";
        }
        $query = substr($query, 0, -1);
        u::query($query);
      }
    }
  }
  public function updateEnrolmentLastDate($branch_id = NULL, $student_id = NULL)
  {
    $cond = $branch_id ? " AND id=$branch_id " : "";
    $cond_stu = $student_id ? " AND c.student_id= $student_id" : "";
    $branches = u::query("SELECT id FROM branches WHERE `status`=1 $cond ");
    foreach ($branches as $branch) {
      for ($i = 1; $i < 5; $i++) {
        $branch_id = $branch->id;
        $product_id = $i;
        $list_contract = u::query("SELECT c.id AS contract_id,c.class_id,c.enrolment_start_date ,c.summary_sessions,c.student_id,c.branch_id,c.enrolment_last_date,
                      (SELECT id FROM log_contracts_history WHERE contract_id=c.id ORDER BY id DESC LIMIT 1) AS last_log_id
                    FROM contracts AS c WHERE c.class_id IS NOT NULL AND c.branch_id=$branch_id AND c.product_id=$product_id AND c.status!=7 $cond_stu");
        $tmp_holiDay = u::getPublicHolidays(0, $branch_id, $product_id);
        $sql_insert_log = "INSERT INTO log_update_enrolment_last_date (contract_id,student_id,branch_id,curr_enrolment_last_date,enrolment_last_date,created_at) VALUES ";
        $j = 0;
        $created_at = date('Y-m-d H:i:s');
        $sql_update_contract = "INSERT INTO contracts (id,enrolment_last_date) VALUES";
        $sql_update_log_contract = "INSERT INTO log_contracts_history (id,enrolment_last_date) VALUES";
        foreach ($list_contract as $contract) {
          $reserved_dates = self::getReservedDates_transfer($contract->contract_id, -1);
          if (!empty($reserved_dates)) {
            $holiDay = array_merge($tmp_holiDay, $reserved_dates);
          }else{
            $holiDay = $tmp_holiDay;
          }
          $class_days = u::getClassDays($contract->class_id);
          $data = u::calculatorSessionsByNumberOfSessions($contract->enrolment_start_date, $contract->summary_sessions, $holiDay, $class_days);
          if ($data) {
            $sql_update_contract .= $contract->contract_id ? " ($contract->contract_id,'$data->end_date')," : '';
            $sql_update_log_contract .= $contract->last_log_id ? " ($contract->last_log_id,'$data->end_date')," : '';
            $sql_insert_log .= "($contract->contract_id,$contract->student_id,$contract->branch_id,'$contract->enrolment_last_date','$data->end_date','$created_at'),";
            $j++;
          }
        }
        if ($j > 0) {
          $sql_update_contract = substr($sql_update_contract, 0, -1);
          $sql_update_contract .= " ON DUPLICATE KEY UPDATE enrolment_last_date=VALUES(enrolment_last_date)";
          $sql_update_log_contract = substr($sql_update_log_contract, 0, -1);
          $sql_update_log_contract .= " ON DUPLICATE KEY UPDATE enrolment_last_date=VALUES(enrolment_last_date)";
          u::query($sql_update_contract);
          u::query($sql_update_log_contract);
          $sql_insert_log = substr($sql_insert_log, 0, -1);
          u::query($sql_insert_log);
        }
      }
    }

    return "ok";
  }
  static function getReservedDates_transfer($contract_id)
  {
    $res = [];
    if ($contract_id) {
      $query = "SELECT r.contract_id, r.start_date, r.end_date, r.session FROM `reserves` AS r WHERE r.status = 2 AND r.contract_id =$contract_id ";
      $data = u::query($query);

      if (!empty($data)) {
        foreach ($data as $da) {
          $res[] = (object)['start_date' => $da->start_date, 'end_date' => $da->end_date, 'sessions' => $da->session];
        }
      }
    }

    return $res;
  }
  public static function sendMailSalehub($branch_id, $student_id=0 ,$date_send =NULL){
    $date_send = $date_send ? $date_send : date('Y-m-d');
    $branch_info = u::first("SELECT * FROM branches WHERE id= $branch_id");
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->mergeCells('A1:H1');
    $sheet->mergeCells('A2:H2');
    $sheet->setCellValue('A1', 'THÔNG TIN KHÁCH SALES HUB GỬI TRẢI NGHIỆM');
    $sheet->setCellValue('A2', $branch_info->name);
    $sheet->getRowDimension('1')->setRowHeight(40);
    $sheet->getRowDimension('2')->setRowHeight(36);
    $sheet->getRowDimension('3')->setRowHeight(23);
    $sheet->getRowDimension('4')->setRowHeight(30);
    $sheet->setCellValue('A4', 'STT');
    $sheet->setCellValue('B4', 'Họ tên phụ huynh');
    $sheet->setCellValue('C4', 'Địa chỉ');
    $sheet->setCellValue('D4', 'Họ tên học sinh');
    $sheet->setCellValue('E4', 'Năm sinh');
    $sheet->setCellValue('F4', 'Ngày checkin');
    $sheet->setCellValue('G4', 'Giờ checkin');
    $sheet->setCellValue('H4', 'Nhân viên Sales Hub');
    $sheet->setCellValue('I4', 'Sản phẩm');

    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->getColumnDimension('D')->setWidth(40);
    $sheet->getColumnDimension('E')->setWidth(30);
    $sheet->getColumnDimension('F')->setWidth(30);
    $sheet->getColumnDimension('G')->setWidth(20);
    $sheet->getColumnDimension('H')->setWidth(40);
    $sheet->getColumnDimension('I')->setWidth(30);
    ProcessExcel::styleCells($spreadsheet, "A1:I1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
    ProcessExcel::styleCells($spreadsheet, "A2:I2", NULL, NULL, 14, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "F4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "G4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "H4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "I4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    if($student_id){
      $cond= " AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d')= '$date_send' AND s.id=$student_id ";
    }else{
      if(date('w',strtotime($date_send))==5){
        $max_date_send = date('Y-m-d',time()+24*3600*1);
        $cond = "AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') >= '$date_send' AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') <= '$max_date_send'";
      }elseif(date('w',strtotime($date_send))==6){
        $max_date_send = date('Y-m-d',time()+24*3600*1);
        $cond = "AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') > '$date_send' AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') <= '$max_date_send'";
      }elseif(date('w',strtotime($date_send))==6|| date('w',strtotime($date_send))==0){
        $cond = " AND 1=2";
      }else{
        $cond = "AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') = '$date_send'";
      }
    }
    
    $list_data = u::query("SELECT DISTINCT s.name, s.gud_name1 ,s.address,s.date_of_birth,s.type_product,
      u.full_name AS creator_name, DATE_FORMAT(s.checkin_at,'%Y-%m-%d') AS checkin_date,DATE_FORMAT(s.checkin_at,'%H-%i') AS checkin_time
    FROM students s  
      LEFT JOIN users AS u ON u.id = s.creator_id
      LEFT JOIN term_user_branch AS t ON t.user_id=u.id 
    WHERE  s.status = 0 AND s.branch_id =$branch_id AND t.role_id IN (80,81,7676767)  $cond ORDER BY checkin_time");
    if($student_id){
      u::query("INSERT INTO email_salehub_checkin (student_id,created_at) VALUES ('$student_id','".date('Y-m-d H:i:s')."')");
    }else{
      for ($i = 0; $i < count($list_data); $i++) {
        $x = $i + 5;
        $sheet->setCellValue('A' . $x, $i + 1);
        $sheet->setCellValue('B' . $x, $list_data[$i]->gud_name1);
        $sheet->setCellValue('C' . $x, $list_data[$i]->address);
        $sheet->setCellValue('D' . $x, $list_data[$i]->name);
        $sheet->setCellValue('E' . $x, $list_data[$i]->date_of_birth);
        $sheet->setCellValue('F' . $x, $list_data[$i]->checkin_date);
        $sheet->setCellValue('G' . $x, $list_data[$i]->checkin_time);
        $sheet->setCellValue('H' . $x, $list_data[$i]->creator_name);
        $sheet->setCellValue('I' . $x, $list_data[$i]->type_product==2 ? 'Accelium' : 'CMS');
        $sheet->getRowDimension($x)->setRowHeight(23);
        ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
      } 
      $writer = new Xlsx($spreadsheet);
      if(count($list_data)){
        $file_name = 'filename_sendMailSalehub_'.$branch_info->id.date('YmdHis').'.xlsx';
        $writer->save(FOLDER . DS ."doc/xls/$file_name");
  
        $mail = new Mail();
        $arr_send = self::getEmail($branch_id);
        $cc1 = isset($arr_send['cc1'])? $arr_send['cc1']:'';
        $to = array('address' => $arr_send['gdtt'], 'name' => $arr_send['gdtt']);
        $subject = "[CRM] Sales Hub gửi thông tin khách checkin - ".$branch_info->name;
        $body = " <p>Gửi trung tâm $branch_info->name.</p>
                  <br>
                  <p>Sales Hub gửi thông tin khách checkin ngày ".date('d/m/Y')." chi tiết file đính kèm</p>
                ";
        $file_attack = (object)array('url'=>'static/doc/xls/'.$file_name,'name'=>'File_dinh_kem_salehubs_'.date('d_m_Y').'.xlsx');
        $mail->sendSingleMail($to, $subject, $body,[$arr_send['cskh'],$arr_send['ecl'],$arr_send['ecl_1'],'mai.huynh@cmsedu.vn','anh.nguyen9@cmsedu.vn','nga.tran1@cmsedu.vn','anh.dam@cmsedu.vn',$cc1],[$file_attack]);
        // unlink(FOLDER . DS .'doc/xls/'.$file_name);
      }
    }
    return true;
  }
  public static function getEmail($branch_id){
    $result =array();
    switch ($branch_id) {
      case 1:
        $result =array(
          'cskh'=>'cskh.hapulico@cmsedu.vn',
          'gdtt'=>'',
          'ecl'=>'',
          'ecl_1'=> ''
        );
        break;
      case 2:
        $result =array(
          'cskh'=>'cskh.mydinh@cmsedu.vn',
          'gdtt'=>'linhltd@cmsedu.vn',
          'ecl'=>'',
          'ecl_1'=> ''
        );
        break;
      case 3:
        $result =array(
          'cskh'=>'cskh.trungkinh@cmsedu.vn',
          'gdtt'=>'',
          'ecl'=>'',
          'ecl_1'=> ''
        );
        break;
      case 4:
        $result =array(
          'cskh'=>'cskh.timescity@cmsedu.vn',
          'gdtt'=>'',
          'ecl'=>'',
          'ecl_1'=> ''
        );
        break;
      case 5:
        $result =array(
          'cskh'=>'cskh.phohue@cmsedu.vn',
          'gdtt'=>'linh.nguyen1@cmsedu.vn',
          'ecl'=>'',
          'ecl_1'=> ''
        );
        break;
      case 6:
        $result =array(
        'cskh'=>'cskh.thuykhue@cmsedu.vn',
        'gdtt'=>'',
        'ecl'=>'',
        'ecl_1'=> ''
        );
        break;
      case 7:
        $result =array(
        'cskh'=>'cskh.phamvandong@cmsedu.vn',
        'gdtt'=>'',
        'ecl'=>'',
        'ecl_1'=> ''
        );
        break;
      case 8:
        $result =array(
          'cskh'=>'cskh.kimlien@cmsedu.vn',
          'gdtt'=>'',
          'ecl'=>'',
          'ecl_1'=> ''
        );
        break;
      case 9:
        $result =array(
          'cskh'=>'cskh.nguyenvancu@cmsedu.vn',
          'gdtt'=>'xuangdtt@cmsedu.vn',
          'ecl'=>'thao.tran1@cmsedu.vn',
          'ecl_1'=> ''
        );
        break;
      case 10:
        $result =array(
          'cskh'=>'cskh.halong@cmsedu.vn',
          'gdtt'=>'',
          'ecl'=>'',
          'ecl_1'=> ''
        );
        break;
      case 12:
        $result =array(
          'cskh'=>'cskh.nguyenthithap@cmsedu.vn',
          'gdtt'=>'',
          'ecl'=>'',
          'cc1'=>'',
          'ecl_1'=> '',
        );
        break;
      case 13:
        $result =array(
          'cskh'=>'cskh.hado@cmsedu.vn',
          'gdtt'=>'',
          'ecl'=>'',
          'cc1'=>'',
          'ecl_1'=> ''
        );
        break;
      case 17:
        $result =array(
          'cskh'=>'cskh.thanhhoa@cmsedu.vn',
          'gdtt'=>'mai.hoang@cmsedu.vn',
          'ecl'=>'',
          'cc1'=>'',
          'ecl_1'=> ''
        );
        break;
      case 18:
        $result =array(
        'cskh'=>'',
        'gdtt'=>'',
        'ecl'=>'',
        'cc1'=>'',
        'ecl_1'=> ''
        );
        break;
      default:
        $result =array(
          'cskh'=>'',
          'gdtt'=>'',
          'ecl'=>'',
          'ecl_1'=> ''
        );
    }

    return $result;
  }
  public static function sendMailCreateContractToSalehub(){
    $list_branch = u::query("SELECT id,zone_id,`name` FROM branches WHERE id<=13 AND status=1");
    foreach($list_branch AS $branch){
      $pre_day = date('Y-m-d 08:00:00',time()-24*3600);
      $curr_day = date('Y-m-d 08:00:00');
      $list_students = u::query("SELECT s.name,s.crm_id,s.accounting_id,s.date_of_birth,s.checkin_at,c.created_at,c.must_charge,
          (SELECT name FROM tuition_fee WHERE id =c.tuition_fee_id) AS tuition_fee_name
        FROM contracts AS c LEFT JOIN students AS s ON s.id=c.student_id LEFT JOIN term_user_branch AS t ON s.creator_id =t.user_id AND t.status=1
        WHERE c.created_at>='$pre_day' AND c.created_at <='$curr_day' AND c.count_recharge=0 AND c.branch_id=$branch->id 
          AND (t.role_id IN(80,81,7676767) OR s.source IN(27,31) )");
      if($list_students){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A1', 'THÔNG TIN KHÁCH SALES HUB LÊN GÓI PHÍ TẠI TRUNG TÂM');
        $sheet->setCellValue('A2', $branch->name);
        $sheet->getRowDimension('1')->setRowHeight(40);
        $sheet->getRowDimension('2')->setRowHeight(36);
        $sheet->getRowDimension('3')->setRowHeight(23);
        $sheet->getRowDimension('4')->setRowHeight(30);
        $sheet->setCellValue('A4', 'STT');
        $sheet->setCellValue('B4', 'Họ tên học sinh');
        $sheet->setCellValue('C4', 'Mã CRM');
        $sheet->setCellValue('D4', 'Mã Cyber');
        $sheet->setCellValue('E4', 'Năm sinh');
        $sheet->setCellValue('F4', 'Ngày checkin');
        $sheet->setCellValue('G4', 'Ngày mua gói phí');
        $sheet->setCellValue('H4', 'Tên gói phí');
        $sheet->setCellValue('I4', 'Giá trị gói phí');

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(40);
        $sheet->getColumnDimension('I')->setWidth(40);
        ProcessExcel::styleCells($spreadsheet, "A1:I1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
        ProcessExcel::styleCells($spreadsheet, "A2:I2", NULL, NULL, 14, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "F4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "G4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "H4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "I4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        
        for ($i = 0; $i < count($list_students); $i++) {
          $x = $i + 5;
          $sheet->setCellValue('A' . $x, $i + 1);
          $sheet->setCellValue('B' . $x, $list_students[$i]->name);
          $sheet->setCellValue('C' . $x, $list_students[$i]->crm_id);
          $sheet->setCellValue('D' . $x, $list_students[$i]->accounting_id);
          $sheet->setCellValue('E' . $x, "'".$list_students[$i]->date_of_birth);
          $sheet->setCellValue('F' . $x, "'".$list_students[$i]->checkin_at);
          $sheet->setCellValue('G' . $x, "'".$list_students[$i]->created_at);
          $sheet->setCellValue('H' . $x, $list_students[$i]->tuition_fee_name);
          $sheet->setCellValue('I' . $x, $list_students[$i]->must_charge);
          $sheet->getRowDimension($x)->setRowHeight(23);
          ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
        } 
        $writer = new Xlsx($spreadsheet);
        if(count($list_students)){
          $file_name = 'filename_createContractToSalehub_'.$branch->id.date('YmdHis').'xlsx';
          $writer->save(FOLDER . DS ."doc/xls/$file_name");

          $mail = new Mail();
          $arr_send = self::getEmail($branch->id);
          // if($branch->zone_id==1 || $branch->zone_id==3){
          //   $to =  array('address' => 'nga.tran1@cmsedu.vn', 'name' =>'nga.tran1@cmsedu.vn');
          //   $arr_cc = [ 'mai.huynh@cmsedu.vn',$arr_send['gdtt'],$arr_send['ecl'],$arr_send['ecl_1']];
          // }else{
          //   $to =  array('address' => 'mai.huynh@cmsedu.vn', 'name' =>'mai.huynh@cmsedu.vn');
          //   $arr_cc = ['nga.tran1@cmsedu.vn',$arr_send['gdtt'],$arr_send['ecl'],$arr_send['ecl_1']];
          // }
          $to = array('address' => $arr_send['gdtt'], 'name' =>$arr_send['gdtt']);
          $arr_cc =['nga.tran1@cmsedu.vn','anh.dam@cmsedu.vn',$arr_send['ecl'],$arr_send['ecl_1']];
          $subject = "[CRM] THÔNG TIN KHÁCH SALES HUB LÊN GÓI PHÍ TẠI TRUNG TÂM - ".$branch->name;
          $body = "<p>Thông tin khách sales hub lên gói phí tại trung tâm - $branch->name ngày ".date('d/m/Y')." chi tiết file đính kèm</p>";
          $file_attack = (object)array('url'=>'static/doc/xls/'.$file_name,'name'=>"File_dinh_kem_salehubs_$branch->id".date('d_m_Y').'.xlsx');
          
          // $to =  array('address' => 'thanhcong1710@gmail.com', 'name' =>'thanhcong1710@gmail.com');
          // $arr_cc=['dat.nguyen@cmsedu.vn'];
          $mail->sendSingleMail($to, $subject, $body, $arr_cc,[$file_attack]);
          // unlink(FOLDER . DS .'doc/xls/'.$file_name);
        }
      }
    }
  }
  public static function sendMailCreateContractTransferBranch(){
    $list_branch = u::query("SELECT id,zone_id,`name` FROM branches WHERE id<=13 AND status=1");
    foreach($list_branch AS $branch){
      $pre_day = date('Y-m-d 22:00:00',time()-24*3600);
      $curr_day = date('Y-m-d 22:00:00');
      foreach($list_branch AS $tmp_branch){
        if($tmp_branch->id !=$branch->id){
          $list_students = u::query("SELECT s.name,s.crm_id,s.accounting_id,s.date_of_birth,s.checkin_at,c.created_at,c.must_charge,
              (SELECT name FROM tuition_fee WHERE id =c.tuition_fee_id) AS tuition_fee_name,
              (SELECT to_approve_at FROM transfer_checkin WHERE student_id=s.id AND status=2 AND to_branch_id =c.branch_id AND from_branch_id=$branch->id LIMIT 1) AS to_approve_at,
              (SELECT name FROM branches WHERE id= $tmp_branch->id) As to_branch_name,
              (SELECT name FROM branches WHERE id= $branch->id) As from_branch_name
            FROM contracts AS c LEFT JOIN students AS s ON s.id=c.student_id 
            WHERE c.created_at>='$pre_day' AND c.created_at <='$curr_day' AND c.count_recharge=0 AND c.branch_id=$tmp_branch->id AND 
            (SELECT count(id) FROM transfer_checkin WHERE student_id=s.id AND status=2 AND to_branch_id =c.branch_id AND from_branch_id=$branch->id)>0");
          if($list_students){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:K1');
            $sheet->mergeCells('A2:K2');
            $sheet->setCellValue('A1', 'THÔNG TIN KHÁCH BÁN CHÉO');
            $sheet->setCellValue('A2', $tmp_branch->name);
            $sheet->getRowDimension('1')->setRowHeight(40);
            $sheet->getRowDimension('2')->setRowHeight(36);
            $sheet->getRowDimension('3')->setRowHeight(23);
            $sheet->getRowDimension('4')->setRowHeight(30);
            $sheet->setCellValue('A4', 'STT');
            $sheet->setCellValue('B4', 'Trung tâm gửi');
            $sheet->setCellValue('C4', 'Trung tâm nhận');
            $sheet->setCellValue('D4', 'Họ tên học sinh');
            $sheet->setCellValue('E4', 'Mã CRM');
            $sheet->setCellValue('F4', 'Mã Cyber');
            $sheet->setCellValue('G4', 'Năm sinh');
            $sheet->setCellValue('H4', 'Ngày chuyển khách');
            $sheet->setCellValue('I4', 'Ngày mua gói phí');
            $sheet->setCellValue('J4', 'Tên gói phí');
            $sheet->setCellValue('K4', 'Giá trị gói phí');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(40);
            $sheet->getColumnDimension('D')->setWidth(40);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(40);
            $sheet->getColumnDimension('I')->setWidth(40);
            $sheet->getColumnDimension('J')->setWidth(40);
            $sheet->getColumnDimension('K')->setWidth(40);
            ProcessExcel::styleCells($spreadsheet, "A1:K1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:K2", NULL, NULL, 14, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            
            for ($i = 0; $i < count($list_students); $i++) {
              $x = $i + 5;
              $sheet->setCellValue('A' . $x, $i + 1);
              $sheet->setCellValue('B' . $x, $list_students[$i]->name);
              $sheet->setCellValue('C' . $x, $list_students[$i]->from_branch_name);
              $sheet->setCellValue('D' . $x, $list_students[$i]->to_branch_name);
              $sheet->setCellValue('E' . $x, $list_students[$i]->crm_id);
              $sheet->setCellValue('F' . $x, $list_students[$i]->accounting_id);
              $sheet->setCellValue('G' . $x, "'".$list_students[$i]->date_of_birth);
              $sheet->setCellValue('H' . $x, "'".$list_students[$i]->to_approve_at);
              $sheet->setCellValue('I' . $x, "'".$list_students[$i]->created_at);
              $sheet->setCellValue('J' . $x, $list_students[$i]->tuition_fee_name);
              $sheet->setCellValue('K' . $x, $list_students[$i]->must_charge);
              $sheet->getRowDimension($x)->setRowHeight(23);
              ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "J$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "K$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
            } 
            $writer = new Xlsx($spreadsheet);
            if(count($list_students)){
              $file_name = 'filename_ban_cheo_'.$tmp_branch->id.date('Ymd').'xlsx';
              $writer->save(FOLDER . DS ."doc/xls/$file_name");

              $mail = new Mail();
              $arr_send_from = self::getEmail($branch->id);
              $arr_send_to = self::getEmail($tmp_branch->id);
              $to =  array('address' => $arr_send_from['gdtt'], 'name' =>$arr_send_from['gdtt']);
              $arr_cc = [$arr_send_from['ecl'],$arr_send_from['ecl_1'],$arr_send_to['ecl'],$arr_send_to['ecl_1'],$arr_send_to['gdtt'],'ketoan@cmsedu.vn'];
              
              $subject = "[CRM] THÔNG TIN KHÁCH BÁN CHÉO LÊN GÓI PHÍ TẠI TRUNG TÂM - ".$tmp_branch->name;
              $body = "<p>Thông tin khách bán chéo lên gói phí tại trung tâm - $tmp_branch->name ngày ".date('d/m/Y')." chi tiết file đính kèm</p>";
              $file_attack = (object)array('url'=>'static/doc/xls/'.$file_name,'name'=>"File_dinh_kem_ban_cheo_$tmp_branch->id".date('d_m_Y').'.xlsx');
              
              // $to =  array('address' => 'thanhcong1710@gmail.com', 'name' =>'thanhcong1710@gmail.com');
              // $arr_cc=['dat.nguyen@cmsedu.vn'];
              $mail->sendSingleMail($to, $subject, $body, $arr_cc,[$file_attack]);
              // unlink(FOLDER . DS .'doc/xls/'.$file_name);
            }
          }
        }
      }
      
    }
  }
  public function testSms(Request $request){
    $type = $request->type ? $request->type : 0;
    $sms = new Sms();
    if( $type==1){
      $sms->sendSmsRenew();
    }elseif($type==2){
      $sms->sendSmsEndReserve();
    }else{
      $sms->sendSmsBirthday();
    }
    return "ok";
  }
  public function processQueuesMail(Request $request){
    $mail = new Mail();
    $mail->processMail();
    return "ok";
  }
  public function processQueuesSms(Request $request){
    $sms = new Sms();
    $sms->processSms();
    return "ok";
  }
  public function sendMailRenew($report_month){
    u::query("DELETE FROM email_renews WHERE report_month = '$report_month' ");
    $list = u::query("SELECT c.id AS contract_id,c.student_id,c.branch_id,c.product_id,c.program_id,c.class_id,c.tuition_fee_id,t.ec_id,
          t.cm_id,t.ec_leader_id,t.om_id, '$report_month' AS report_month,c.enrolment_last_date
        FROM
            contracts c
            LEFT JOIN term_student_user AS t ON t.student_id = c.student_id
        WHERE
            c.type > 0 AND c.status!=7 AND DATE_FORMAT(c.enrolment_last_date,'%Y-%m')='$report_month' ");
    if (count($list)) {
        self::addItemsEmailRenew($list);
    }
    self::processSendEmailMailRenew($report_month);
    return "ok";
  }
  public function addItemsEmailRenew($list) {
    if ($list) {
        $created_at = date('Y-m-d H:i:s');
        $query = "INSERT INTO email_renews (student_id,contract_id, branch_id, product_id, program_id, class_id, tuition_fee_id, ec_id, cm_id,ec_leader_id,om_id,status,report_month,created_at,enrolment_last_date) VALUES ";
      
        if (count($list) > 5000) {
            for($i = 0; $i < 5000; $i++) {
                $item = $list[$i];
                $query.= "('$item->student_id', '$item->contract_id', '$item->branch_id', '$item->product_id', '$item->program_id', '$item->class_id', '$item->tuition_fee_id', '$item->ec_id', '$item->cm_id','$item->ec_leader_id','$item->om_id',1,'$item->report_month','$created_at','$item->enrolment_last_date'),";
            }
            $query = substr($query, 0, -1);
            u::query($query);
            self::addItemsEmailRenew(array_slice($list, 5000));
        } else {
            foreach($list as $item) {
              $query.= "('$item->student_id', '$item->contract_id', '$item->branch_id', '$item->product_id', '$item->program_id', '$item->class_id', '$item->tuition_fee_id', '$item->ec_id', '$item->cm_id','$item->ec_leader_id','$item->om_id',1,'$item->report_month','$created_at','$item->enrolment_last_date'),";
            }
            $query = substr($query, 0, -1);
            u::query($query);
        }
    }
  }
  public static function processSendEmailMailRenew($report_month){
    $list_branch = u::query("SELECT id,zone_id,`name` FROM branches WHERE id<=13 AND status=1");
    foreach($list_branch AS $branch){
          $list_students = u::query("SELECT s.name,s.crm_id,s.accounting_id,
              (SELECT name FROM branches WHERE id= r.branch_id) As branch_name,
              (SELECT name FROM products WHERE id= r.product_id) As product_name,
              (SELECT cls_name FROM classes WHERE id= r.class_id) As cls_name,
              (SELECT name FROM tuition_fee WHERE id= r.tuition_fee_id) As tuition_fee_name,
              r.enrolment_last_date
            FROM email_renews AS r LEFT JOIN students AS s ON s.id=r.student_id 
            WHERE r.report_month>='$report_month' AND r.branch_id ='$branch->id' ");
          if($list_students){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:K1');
            $sheet->mergeCells('A2:K2');
            $sheet->setCellValue('A1', 'DANH SÁCH HỌC SINH CÓ NGÀY HẾT PHÍ TRONG THÁNG '.$report_month);
            $sheet->setCellValue('A2', $branch->name);
            $sheet->getRowDimension('1')->setRowHeight(40);
            $sheet->getRowDimension('2')->setRowHeight(36);
            $sheet->getRowDimension('3')->setRowHeight(23);
            $sheet->getRowDimension('4')->setRowHeight(30);
            $sheet->setCellValue('A4', 'STT');
            $sheet->setCellValue('B4', 'MÃ CMS');
            $sheet->setCellValue('C4', 'Mã Kế Toán');
            $sheet->setCellValue('D4', 'Tên Học Sinh');
            $sheet->setCellValue('E4', 'Trung Tâm');
            $sheet->setCellValue('F4', 'Sản Phẩm');
            $sheet->setCellValue('G4', 'Lớp Học');
            $sheet->setCellValue('H4', 'Ngày kết thúc gói phí');
            $sheet->setCellValue('I4', 'Tên gói phí');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(40);
            $sheet->getColumnDimension('D')->setWidth(40);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(40);
            $sheet->getColumnDimension('I')->setWidth(40);
            ProcessExcel::styleCells($spreadsheet, "A1:I1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:I2", NULL, NULL, 14, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            
            for ($i = 0; $i < count($list_students); $i++) {
              $x = $i + 5;
              $sheet->setCellValue('A' . $x, $i + 1);
              $sheet->setCellValue('B' . $x, $list_students[$i]->crm_id);
              $sheet->setCellValue('C' . $x, $list_students[$i]->accounting_id);
              $sheet->setCellValue('D' . $x, $list_students[$i]->name);
              $sheet->setCellValue('E' . $x, $list_students[$i]->branch_name);
              $sheet->setCellValue('F' . $x, $list_students[$i]->product_name);
              $sheet->setCellValue('G' . $x, $list_students[$i]->cls_name);
              $sheet->setCellValue('H' . $x, "'".$list_students[$i]->enrolment_last_date);
              $sheet->setCellValue('I' . $x, $list_students[$i]->tuition_fee_name);
              $sheet->getRowDimension($x)->setRowHeight(23);
              ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
              ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
            } 
            $writer = new Xlsx($spreadsheet);
            if(count($list_students)){
              $file_name = 'ds_hs_het_phi_'.$branch->id.date('Ymd').'xlsx';
              $writer->save(FOLDER . DS ."doc/xls/$file_name");

              $mail = new Mail();
              $arr_send = self::getEmail($branch->id);
              $to =  array('address' => $arr_send['cskh'], 'name' =>$arr_send['cskh']);
              $arr_cc = [$arr_send['gdtt'],'dat.nguyen@cmsedu.vn','qlcl@cmsedu.vn','loan.pham@cmsedu.vn'];
              
              $subject = "[CRM] THÔNG TIN HỌC SINH CÓ NGÀY HẾT PHÍ TRONG THÁNG $report_month TẠI TRUNG TÂM ".$branch->name;
              $body = "<p>Thông tin học sinh hết phí trong tháng $report_month tại trung tâm - $branch->name chi tiết file đính kèm</p>";
              $file_attack = (object)array('url'=>'static/doc/xls/'.$file_name,'name'=>"File_ds_hs_het_phi_$branch->id".date('d_m_Y').'.xlsx');
              
              // $to =  array('address' => 'thanhcong1710@gmail.com', 'name' =>'thanhcong1710@gmail.com');
              // $arr_cc=['dat.nguyen@cmsedu.vn'];
              // $arr_from =array(
              //   'email'=> 'taituc@cmsedu.vn',
              //   'password'=>'cms@1234'
              // );
              $mail->sendSingleMail($to, $subject, $body, $arr_cc,[$file_attack]);
            }
      }
      
    }
  }
  public function sendMailRenewNextMonth($report_month){
    $list_branch = u::query("SELECT id,zone_id,`name` FROM branches WHERE id<=13 AND status=1");
    foreach($list_branch AS $branch){
      $resp = "SELECT r.*, s.name AS student_name, s.crm_id, s.accounting_id,s.gud_mobile1,
            (SELECT `name` FROM branches WHERE id=r.branch_id) AS branch_name,
            (SELECT `name` FROM products WHERE id=r.product_id) AS product_name,
            (SELECT cls_name FROM classes WHERE id=r.class_id) AS class_name,
            IF(r.status=1,'Thành công','Thất bại') AS status_title,
            (SELECT `name` FROM tuition_fee WHERE id= r.tuition_fee_id) AS tuition_fee_name,
            (SELECT `full_name` FROM users WHERE id= r.ec_id) AS ec_name,
            (SELECT `hrm_id` FROM users WHERE id= r.ec_id) AS ec_hrm_id
        FROM renews_report AS r 
            LEFT JOIN students AS s ON s.id=r.student_id 
        WHERE r.id > 0 AND r.`disabled` = 0 AND s.status>0 AND r.renewed_month = '$report_month' AND r.branch_id=$branch->id";
          $students = u::query($resp);
          if($students){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:M1');
            $sheet->mergeCells('A2:M2');
            $sheet->mergeCells('A3:M3');
            $sheet->mergeCells('A4:M4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO DANH SÁCH CHI TIẾT HỌC SINH TỚI HẠN TÁI TỤC');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(30);
            $sheet->getRowDimension('4')->setRowHeight(30);
            $sheet->setCellValue('A3', 'Tính tới tháng: ' . $report_month);

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Mã CMS');
            $sheet->setCellValue('C5', 'Mã Kế Toán');
            $sheet->setCellValue('D5', 'Tên Học Sinh');
            $sheet->setCellValue('E5', 'Trung Tâm');
            $sheet->setCellValue('F5', 'Sản Phẩm');
            $sheet->setCellValue('G5', 'Lớp Học');
            $sheet->setCellValue('H5', 'Hạn Tái Tục');
            $sheet->setCellValue('I5', 'Kết Quả');
            $sheet->setCellValue('J5', 'Gói Tái Phí');
            $sheet->setCellValue('K5', 'Số Tiền Tái Phí');
            $sheet->setCellValue('L5', 'Tên EC');
            $sheet->setCellValue('M5', 'Mã EC');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(40);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(30);
            $sheet->getColumnDimension('K')->setWidth(20);
            $sheet->getColumnDimension('L')->setWidth(30);
            $sheet->getColumnDimension('M')->setWidth(20); 
            ProcessExcel::styleCells($spreadsheet, "A1:I1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:I2", NULL, NULL, 14, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            
            ProcessExcel::styleCells($spreadsheet, "A1:M1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:M2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:M3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:M4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "M5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "N5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');

            for ($i = 0; $i < count($students); $i++) {
                $x = $i + 6;

                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->crm_id);
                $sheet->setCellValue('C' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('D' . $x, $students[$i]->student_name);
                $sheet->setCellValue('E' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('F' . $x, $students[$i]->product_name);
                $sheet->setCellValue('G' . $x, $students[$i]->class_name);
                $sheet->setCellValue('H' . $x, $students[$i]->last_date);
                $sheet->setCellValue('I' . $x, $students[$i]->status_title);
                $sheet->setCellValue('J' . $x, $students[$i]->status==1 ? $students[$i]->tuition_fee_name :'');
                $sheet->setCellValue('K' . $x, $students[$i]->status==1 ? apax_ada_format_number($students[$i]->renew_amount) : '');
                $sheet->setCellValue('L' . $x, $students[$i]->ec_name);
                $sheet->setCellValue('M' . $x, $students[$i]->ec_hrm_id);
                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "J$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "K$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "L$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "M$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
              }
            $writer = new Xlsx($spreadsheet);
            if(count($students)){
              $file_name = 'ds_hs_tai_phi_'.$branch->id.date('Ymd').'xlsx';
              $writer->save(FOLDER . DS ."doc/xls/$file_name");

              $mail = new Mail();
              $arr_send = self::getEmail($branch->id);
              $to =  array('address' => $arr_send['cskh'], 'name' =>$arr_send['cskh']);
              $arr_cc = [$arr_send['gdtt'],'dat.nguyen@cmsedu.vn','qlcl@cmsedu.vn','loan.pham@cmsedu.vn'];
              
              $subject = "[CRM] DANH SÁCH TÁI PHÍ THÁNG $report_month TẠI TRUNG TÂM ".$branch->name;
              $body = "<p>Thông tin học sinh tái phí trong tháng $report_month tại trung tâm - $branch->name chi tiết file đính kèm</p>
              <p>Nếu có bất kỳ vấn đề gì liên quan đến danh sách tái tục vui lòng phản hồi vào luồng mail này.</p>";
              $file_attack = (object)array('url'=>'static/doc/xls/'.$file_name,'name'=>"File_ds_hs_tai_phi_$branch->id".date('d_m_Y').'.xlsx');
              
              // $to =  array('address' => 'thanhcong1710@gmail.com', 'name' =>'thanhcong1710@gmail.com');
              // $arr_cc=['dat.nguyen@cmsedu.vn'];
              $arr_from =array(
                'email'=> 'taituc@cmsedu.vn',
                'password'=>'cms@1234'
              );
              $mail->sendSingleMail($to, $subject, $body, $arr_cc,[$file_attack],$arr_from);
            }
      }
      
    }
    return "ok";
  }
  public static function sendMailSalehubOver($branch_id){
    $date_send = date('Y-m-d');
    $branch_info = u::first("SELECT * FROM branches WHERE id= $branch_id");
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->mergeCells('A1:H1');
    $sheet->mergeCells('A2:H2');
    $sheet->setCellValue('A1', 'THÔNG TIN KHÁCH SALES HUB GỬI TRẢI NGHIỆM');
    $sheet->setCellValue('A2', $branch_info->name);
    $sheet->getRowDimension('1')->setRowHeight(40);
    $sheet->getRowDimension('2')->setRowHeight(36);
    $sheet->getRowDimension('3')->setRowHeight(23);
    $sheet->getRowDimension('4')->setRowHeight(30);
    $sheet->setCellValue('A4', 'STT');
    $sheet->setCellValue('B4', 'Họ tên phụ huynh');
    $sheet->setCellValue('C4', 'Địa chỉ');
    $sheet->setCellValue('D4', 'Họ tên học sinh');
    $sheet->setCellValue('E4', 'Năm sinh');
    $sheet->setCellValue('F4', 'Ngày checkin');
    $sheet->setCellValue('G4', 'Giờ checkin');
    $sheet->setCellValue('H4', 'Nhân viên Sales Hub');

    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->getColumnDimension('D')->setWidth(40);
    $sheet->getColumnDimension('E')->setWidth(30);
    $sheet->getColumnDimension('F')->setWidth(30);
    $sheet->getColumnDimension('G')->setWidth(20);
    $sheet->getColumnDimension('H')->setWidth(40);
    ProcessExcel::styleCells($spreadsheet, "A1:H1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
    ProcessExcel::styleCells($spreadsheet, "A2:H2", NULL, NULL, 14, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "F4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "G4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
    ProcessExcel::styleCells($spreadsheet, "H4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
   
    if(date('w',strtotime($date_send))==5){
      $max_date_send = date('Y-m-d',time()+24*3600*1);
      $cond = "AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') >= '$date_send' AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') <= '$max_date_send'";
    }elseif(date('w',strtotime($date_send))==6){
      $max_date_send = date('Y-m-d',time()+24*3600*1);
      $cond = "AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') > '$date_send' AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') <= '$max_date_send'";
    }elseif(date('w',strtotime($date_send))==6|| date('w',strtotime($date_send))==0){
      $cond = " AND 1=2";
    }else{
      $cond = "AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') = '$date_send'";
    }
    
    $list_data = u::query("SELECT DISTINCT s.name, s.gud_name1 ,s.address,s.date_of_birth,sc.student_id,
      u.full_name AS creator_name, DATE_FORMAT(s.checkin_at,'%Y-%m-%d') AS checkin_date,DATE_FORMAT(s.checkin_at,'%H-%i') AS checkin_time
    FROM students s  
      LEFT JOIN users AS u ON u.id = s.creator_id
      LEFT JOIN term_user_branch AS t ON t.user_id=u.id 
      LEFT JOIN email_salehub_checkin AS sc ON s.id=sc.student_id
    WHERE  sc.id IS NOT NULL AND sc.status=0 AND s.status = 0 AND s.branch_id =$branch_id AND t.role_id IN (80,81,7676767)  $cond ORDER BY checkin_time");
    $list_student = "";
    for ($i = 0; $i < count($list_data); $i++) {
      $x = $i + 5;
      $sheet->setCellValue('A' . $x, $i + 1);
      $sheet->setCellValue('B' . $x, $list_data[$i]->gud_name1);
      $sheet->setCellValue('C' . $x, $list_data[$i]->address);
      $sheet->setCellValue('D' . $x, $list_data[$i]->name);
      $sheet->setCellValue('E' . $x, $list_data[$i]->date_of_birth);
      $sheet->setCellValue('F' . $x, $list_data[$i]->checkin_date);
      $sheet->setCellValue('G' . $x, $list_data[$i]->checkin_time);
      $sheet->setCellValue('H' . $x, $list_data[$i]->creator_name);
      $sheet->getRowDimension($x)->setRowHeight(23);
      ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
      ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
      ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
      ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
      ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
      ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
      ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
      ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
      $list_student.=  $list_student? ",".$list_data[$i]->student_id : $list_data[$i]->student_id;
    } 
    $writer = new Xlsx($spreadsheet);
    if(count($list_data)){
      u::query("UPDATE email_salehub_checkin set `status`=1, send_at='".date('Y-m-d H:i:s')."' WHERE student_id IN($list_student) ");
      $file_name = 'filename_sendMailSalehub_'.$branch_info->id.date('YmdHis').'.xlsx';
      $writer->save(FOLDER . DS ."doc/xls/$file_name");

      $mail = new Mail();
      $arr_send = self::getEmail($branch_id);
      $cc1 = isset($arr_send['cc1'])? $arr_send['cc1']:'';
      $to = array('address' => $arr_send['gdtt'], 'name' => $arr_send['gdtt']);
      $subject = "[CRM] Sales Hub gửi thông tin khách checkin - ".$branch_info->name;
      $body = " <p>Gửi trung tâm $branch_info->name.</p>
                <br>
                <p>Sales Hub gửi thông tin khách checkin ngày ".date('d/m/Y')." chi tiết file đính kèm</p>
              ";
      $file_attack = (object)array('url'=>'static/doc/xls/'.$file_name,'name'=>'File_dinh_kem_salehubs_'.date('d_m_Y').'.xlsx');
      $mail->sendSingleMail($to, $subject, $body,[$arr_send['cskh'],$arr_send['ecl'],$arr_send['ecl_1'],'mai.huynh@cmsedu.vn','anh.nguyen9@cmsedu.vn','nga.tran1@cmsedu.vn','anh.dam@cmsedu.vn',$cc1],[$file_attack]);
      // unlink(FOLDER . DS .'doc/xls/'.$file_name);
    }
    return true;
  }
  public function updateSourceDetail(){
    u::query("DELETE FROM source_detail");
    $connection = DB::connection('mysql_lead');
    $list = $connection->select(DB::raw("SELECT * FROM cms_source_detail"));
    $query = "INSERT INTO source_detail (id,name,status, created_at, creator_id, updated_at, updator_id, branch_id) VALUES ";
    foreach($list AS $item){
      $query.= "('$item->id', '$item->name', '$item->status', '$item->created_at', '$item->creator_id', '$item->updated_at', '$item->updator_id', '$item->branch_id'),";          
    }
    $query = substr($query, 0, -1);
    u::query($query);
    return "ok";
  }
  public static function sendMailAttendances(){
    $list_branch = u::query("SELECT id,zone_id,`name` FROM branches WHERE id<=13 AND status=1");
    foreach($list_branch AS $branch){
      $where = " AND r.branch_id = $branch->id ";
      $date = date('Y-m-d',time()-24*3600);
      $report_month = date('Y-m',time()-24*3600);
      $q = "SELECT b.name AS branch_name, cl.cls_name AS cls_name, s.crm_id, s.name AS student_name,
          '$date' AS `date`
        FROM
          report_full_fee_active AS r 
          LEFT JOIN students AS s ON s.id=r.student_id
          LEFT JOIN branches AS b ON b.id=r.branch_id
          LEFT JOIN classes AS cl ON cl.id=r.class_id
          LEFT JOIN schedules AS sc ON sc.class_id = r.class_id
          LEFT JOIN attendances AS a ON a.student_id = r.student_id AND a.attendance_date='$date'
          WHERE
              sc.cjrn_classdate = '$date' AND r.report_month='$report_month' AND a.id IS NULL  $where 
        ORDER BY r.class_id";

      $list_students = u::query($q);
      
      if($list_students){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A1', 'DANH SÁCH HỌC SINH KHÔNG ĐIỂM DANH');
        $sheet->setCellValue('A2', $branch->name);
        $sheet->getRowDimension('1')->setRowHeight(40);
        $sheet->getRowDimension('2')->setRowHeight(36);
        $sheet->getRowDimension('3')->setRowHeight(23);
        $sheet->getRowDimension('4')->setRowHeight(30);
        $sheet->setCellValue('A4', 'STT');
        $sheet->setCellValue('B4', 'Trung tâm');
        $sheet->setCellValue('C4', 'Lớp');
        $sheet->setCellValue('D4', 'Mã HS');
        $sheet->setCellValue('E4', 'Tên HS');
        $sheet->setCellValue('F4', 'Ngày');
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        ProcessExcel::styleCells($spreadsheet, "A1:F1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
        ProcessExcel::styleCells($spreadsheet, "A2:F2", NULL, NULL, 14, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        ProcessExcel::styleCells($spreadsheet, "F4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        
        for ($i = 0; $i < count($list_students); $i++) {
          $x = $i + 5;
          $sheet->setCellValue('A' . $x, $i + 1);
          $sheet->setCellValue('B' . $x, $list_students[$i]->branch_name);
          $sheet->setCellValue('C' . $x, $list_students[$i]->cls_name);
          $sheet->setCellValue('D' . $x, $list_students[$i]->crm_id);
          $sheet->setCellValue('E' . $x, "'".$list_students[$i]->student_name);
          $sheet->setCellValue('F' . $x, "'".$list_students[$i]->date);
          $sheet->getRowDimension($x)->setRowHeight(23);
          ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
          ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
        } 
        $writer = new Xlsx($spreadsheet);
        if(count($list_students) && date('Y-m-d') >= '2023-07-01'){
          $file_name = 'filename_sendMailAttendances_'.$branch->id.date('YmdHis').'xlsx';
          $writer->save(FOLDER . DS ."doc/xls/$file_name");

          $mail = new Mail();
          $arr_send = self::getEmail($branch->id);
          $to = array('address' => $arr_send['gdtt'], 'name' =>$arr_send['gdtt']);
          $arr_cc =[$arr_send['cskh'],'anh.dam@cmsedu.vn', 'phuong.tran1@cmsedu.vn'];
          $subject = "[CRM] DANH SÁCH HỌC SINH KHÔNG ĐIỂM DANH TẠI TRUNG TÂM - ".$branch->name;
          $body = "<p>Danh sách học sinh không điểm danh tại trung tâm - $branch->name ngày ".date('d/m/Y')." chi tiết file đính kèm</p>";
          $file_attack = (object)array('url'=>'static/doc/xls/'.$file_name,'name'=>"File_dinh_kem_salehubs_$branch->id".date('d_m_Y').'.xlsx');
          
          $mail->sendSingleMail($to, $subject, $body, $arr_cc,[$file_attack]);
        }
      }
    }
  }

  public static function updateContractNewOpen(){
    $list = u::query("SELECT * FROM tmp_update_contract WHERE status=0 LIMIT 1000");
    foreach($list AS $contract){
      $status_proess = 2;
      $accounting_id = trim(data_get($contract,'accounting_id'));
      $crm_id = trim(data_get($contract,'crm_id'));
      if(($accounting_id || $crm_id ) && data_get($contract,'so_buoi_con_lai') >0){
        if($accounting_id){
          $student_info = u::first("SELECT id FROM students WHERE accounting_id = '$accounting_id' AND branch_id=$contract->branch_id");
        } elseif($crm_id){
          $student_info = u::first("SELECT id FROM students WHERE crm_id = '$crm_id' AND branch_id=$contract->branch_id ");
        }
        if(isset($student_info) && $student_info){
          u::query("UPDATE contracts SET `status`=7, `action`= 'update_data_by_reopen' WHERE student_id= $student_info->id AND status!=7");
          
          $contract_in_class= u::first("SELECT * FROM contracts WHERE student_id= $student_info->id AND status!=7 AND class_id IS NOT NULL");
          if($contract_in_class){
            $bonus_sessions = data_get( $contract_in_class, 'bonus_sessions') > 0 ? data_get( $contract_in_class, 'bonus_sessions') : 0;
            $bonus_sessions = $bonus_sessions >  data_get( $contract, 'so_buoi_con_lai') ? 0 : $bonus_sessions;
            u::updateContract((object)array(
              'id'         => $contract_in_class->id,
              'updated_at' => date('Y-m-d H:i:s'),
              'action'      => "update_data_by_reopen",
              'enrolment_start_date' => data_get( $contract, 'ngay_hieu_luc'),
              'summary_sessions' => data_get( $contract, 'so_buoi_con_lai'),
              'bonus_sessions' => $bonus_sessions,
              'real_sessions' => data_get( $contract, 'so_buoi_con_lai') - $bonus_sessions,
              'total_charged' => data_get( $contract, 'so_tien_con_lai'),
            ));
            u::query("UPDATE contracts SET `status`=7, `action`= 'update_data_by_reopen' WHERE student_id= $student_info->id AND status!=7 AND id != $contract_in_class->id");
            $status_proess = 1;
          } else {
            $contract_curr= u::first("SELECT * FROM contracts WHERE student_id= $student_info->id AND status!=7 ORDER BY count_recharge LIMIT 1");
            if($contract_curr){
              $bonus_sessions = data_get( $contract_curr, 'bonus_sessions') > 0 ? data_get( $contract_curr, 'bonus_sessions') : 0;
              $bonus_sessions = $bonus_sessions >  data_get( $contract, 'so_buoi_con_lai') ? 0 : $bonus_sessions;
              u::updateContract((object)array(
                'id'         => $contract_curr->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'action'      => "update_data_by_reopen",
                'summary_sessions' => data_get( $contract, 'so_buoi_con_lai'),
                'bonus_sessions' => $bonus_sessions,
                'real_sessions' => data_get( $contract, 'so_buoi_con_lai') - $bonus_sessions,
                'total_charged' => data_get( $contract, 'so_tien_con_lai'),
              ));
              u::query("UPDATE contracts SET `status`=7, `action`= 'update_data_by_reopen' WHERE student_id= $student_info->id AND status!=7 AND id != $contract_curr->id");
              $status_proess = 1;
            } else {
              $last_contract_withdraw = u::first("SELECT * FROM contracts WHERE student_id= $student_info->id AND status=7 ORDER BY count_recharge DESC LIMIT 1");
              if ($last_contract_withdraw){
                u::updateContract((object)array(
                  'id'         => $last_contract_withdraw->id,
                  'updated_at' => date('Y-m-d H:i:s'),
                  'action'      => "update_data_by_reopen",
                  'summary_sessions' => data_get( $contract, 'so_buoi_con_lai'),
                  'bonus_sessions' => 0,
                  'real_sessions' => data_get( $contract, 'so_buoi_con_lai'),
                  'class_id' => null,
                  'type'=>10,
                  'status'=> 4,
                  'start_date' => data_get( $contract, 'ngay_hieu_luc'),
                  'enrolment_start_date' => null,
                  'enrolment_end_date' => null,
                  'enrolment_last_date' => null,
                  'total_charged' => data_get( $contract, 'so_tien_con_lai'),
                ));
                $status_proess = 1;
              }
            }
          }
        }
      } 
      u::query("UPDATE tmp_update_contract SET status = $status_proess WHERE id= $contract->id");
      echo $contract->id."/";
    }
    return "ok";
  }

  public function updateCyberBranch(){
    $list = u::query("SELECT * FROM branches WHERE accounting_id IS NOT NULL");
    foreach($list AS $row){
      $cyberAPI = new CyberAPI();
      $res = $cyberAPI->updateBranch($row, 0);
    }
    return "ok";
  }

  public function updateCyberSale(){
    $list = u::query("SELECT t.user_id AS id,u.full_name, u.phone, b.accounting_id AS branch_accounting_id, u.hrm_id
      FROM term_user_branch AS t LEFT JOIN users AS u ON u.id=t.user_id 
      LEFT JOIN branches AS b ON b.id=t.branch_id
      WHERE t.role_id IN(68,69) AND u.hrm_id LIKE 'CM%'  ORDER BY t.user_id DESC ");
    foreach($list AS $row){
      $cyberAPI = new CyberAPI();
      $cyber_id = $cyberAPI->createSale($row, 0);
      if($cyber_id){
        u::query("UPDATE users SET accounting_id = '$cyber_id' WHERE id = $row->id");
      }
      echo $row->id."/"; 
    }
    return "ok";
  }

  public function updateCyberStudent(){
    // $list_student =u::query("SELECT s.* FROM students AS s LEFT JOIN branches AS b ON b.id=s.branch_id WHERE b.accounting_id IS NOT NULL AND s.accounting_id IS NOT NULL AND s.id>33497  LIMIT 7000");
    // $list_student = u::query("SELECT s.* FROM tmp_update_cyber AS t LEFT JOIN students AS s ON s.id=t.student_id WHERE s.accounting_id IS NULL");
    $list_student = u::query("SELECT s.* FROM students AS s WHERE s.accounting_id IS NULL AND s.crm_id IN('CMS20101036')");

    $cyberAPI = new CyberAPI();
    foreach($list_student AS $student_info){
      $res = $cyberAPI->createStudent($student_info,1);
      if($res){
        u::query("UPDATE students SET accounting_id = '$res' WHERE id=".$student_info->id);
      }
      echo $student_info->id."/";
    }
    return "ok";
  }

  public function updateCyberContract(){
    $list_contract = u::query("SELECT t.contract_id, t.id FROM tmp_update_cyber AS t WHERE t.contract_id IS NOT NULL AND t.status=0 ");
    $contract = new ContractsController();
    foreach($list_contract AS $row){
      $contract->createCyberContract($row->contract_id,1);
      echo $row->id."/";
      u::query("UPDATE tmp_update_cyber SET status=1 WHERE id= $row->id");
    }
    return "ok";
  }

  public function retryCallCyber(){
    $list_contract = u::query("SELECT id FROM contracts  WHERE accounting_id IN ('C05.24.PNH.44604',
'C03.24.PNH.44578',
'C04.24.PNH.44581',
'C04.24.PNH.44582',
'C07.24.PNH.44586',
'C03.24.PNH.44571',
'C03.24.PNH.44572',
'C03.24.PNH.44573',
'C03.24.PNH.44576',
'C02.24.PNH.44500',
'C02.24.PNH.44510',
'C02.24.PNH.44511',
'C02.24.PNH.44506',
'C02.24.PNH.44507',
'C02.24.PNH.44491',
'C02.24.PNH.44492',
'C02.24.PNH.44490',
'C02.24.PNH.44481',
'C02.24.PNH.44482',
'C02.24.PNH.44483',
'C02.24.PNH.44484',
'C05.24.PNH.44090') ");
    $contract = new ContractsController();
    foreach($list_contract AS $row){
      $contract->createCyberContract($row->id,1);
      echo $row->id."/";
    }
    return "ok";
  }
}
