<?php

namespace App\Http\Controllers;
use App\Models\CyberAPI;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;
use App\Models\Log_contracts_history;
use App\Models\Sms;

class EnrolmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index()
    {
        //
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
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

    public function loadBranches(Request $request) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = [];
        $code = APICode::SUCCESS;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $branches = $session->branches_ids;
        $where = "id > 0 AND status > 0";
        if ($role_id < 80000000) {
          $where.= " AND id IN ($branches)";
        }
        $query = "SELECT id, `name` FROM branches WHERE $where";
        $data = DB::select(DB::raw($query));
      }
      return $response->formatResponse($code, $data);
    }

    public function loadSemesters(Request $request) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = [];
        $code = APICode::SUCCESS;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $branches = $session->branches_ids;
        $where = "id > 0 AND status > 0 AND end_date > CURDATE()";
        $query = "SELECT id, `name` FROM semesters WHERE $where ORDER BY end_date DESC";
        $data = u::query($query);
      }
      return $response->formatResponse($code, $data);
    }

    public function loadClassesSemesters(Request $request) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = [];
        $code = APICode::SUCCESS;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $branches = $session->branches_ids;
        $where = "id > 0 AND status > 0";
        $query = "SELECT id, `name` FROM semesters WHERE $where ORDER BY start_date DESC";
        $data = u::query($query);
      }
      return $response->formatResponse($code, $data);
    }

    public function semesterUpContracts(Request $request, $search) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = [];
        $code = APICode::SUCCESS;
        $search = json_decode($search);
        $current_id = $search->semester;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $branches = $session->branches_ids;
        $where = "id > 0 AND status > 0";
        $query = "SELECT id, `name` FROM semesters WHERE $where";
        $data = u::query($query);
      }
      return $response->formatResponse($code, $data);
    }

    public function loadClasses(Request $request, $branch_id, $semester_id) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = [];
        $code = APICode::SUCCESS;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $branches = $branch_id ? $branch_id : $session->branches_ids;
        $where = "id > 0 AND status > 0 AND branch_id IN (0,$branches) AND semester_id = $semester_id";
        $query = "SELECT id, 
              id AS item_id, 
              'program' AS item_type, 
              `name` AS `text`, 
              parent_id, 
              'fa fa-folder' AS icon, 
              0 AS status 
        FROM programs 
        WHERE $where
        UNION ALL
        SELECT CONCAT(999, c.id) AS id, 
              c.id AS item_id, 
              'class' AS item_type, 
              c.cls_name AS `text`, 
              c.program_id AS parent_id, 
              IF(c.cm_id > 0, IF(c.status = 1, 'fa fa-close', IF((SELECT COUNT(u.id) FROM users u LEFT JOIN sessions s ON u.id = s.teacher_id WHERE u.status > 0 AND s.class_id = c.id) > 0, 'fa fa-file-text-o', 'fa fa-frown-o')), 'fa fa-user-times') AS icon, 
              c.status 
        FROM classes AS c INNER JOIN programs AS p ON c.program_id = p.id
        WHERE c.cls_iscancelled = 'no' AND p.status > 0 AND p.branch_id IN (0,$branches) AND p.semester_id = $semester_id AND DATE(c.cls_enddate) >= CURDATE()";
        $class = u::query($query);
        if (count($class)) {
          foreach ($class as $item) {
            $item->value = $item->id;
            $item->opened = true;
            $item->selected = false;
            $item->disabled = false;
            $item->loading = false;
            $item->children = [];
          }
          $classes = apax_get_tree_data($class);
          if ($classes) {
            foreach ($classes as $cls) {
              if ($cls) {
                $data[] = $cls;
              }
            }
          }
        }
      }
      return $response->formatResponse($code, $data);
    }

    public function loadAllClasses(Request $request, $branch_id, $semester_id) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = [];
        $code = APICode::SUCCESS;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $branches = $session->branches_ids;
        $where = " status > 0 AND branch_id IN (0,$branch_id) AND semester_id = $semester_id";
        $query = "SELECT id, id AS item_id, 'program' AS item_type, 'no' AS cls_iscancelled, `name` AS `text`, parent_id, 'fa fa-folder' AS icon FROM programs WHERE $where
                  UNION ALL
                  SELECT CONCAT(999, c.id) AS id, c.id AS item_id, 'class' AS item_type, c.cls_iscancelled, c.cls_name AS `text`, c.program_id AS parent_id, IF(c.cm_id > 0, IF (c.cls_iscancelled = 'yes', 'fa fa-window-close-o', 'fa fa-file-text-o'), IF (c.cls_iscancelled = 'yes', 'fa fa-close', 'fa fa-user-times')) AS icon
                  FROM classes AS c INNER JOIN programs AS p ON c.program_id = p.id
                  WHERE p.status > 0 AND p.branch_id IN (0,$branch_id) AND p.semester_id = $semester_id";
        $class = u::query($query);
        if (count($class)) {
          foreach ($class as $item) {
            $item->value = $item->id;
            $item->opened = true;
            $item->selected = false;
            $item->disabled = false;
            $item->loading = false;
            $item->children = [];
          }
          $classes = apax_get_tree_data($class);
          if ($classes) {
            foreach ($classes as $cls) {
              if ($cls) {
                $data[] = $cls;
              }
            }
          }
        }
      }
      return $response->formatResponse($code, $data);
    }

    public function loadSchedule(Request $request, $class_id) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $class_id = (int)$class_id;
        $code = APICode::SUCCESS;
        $data = [];
        $class_info = u::first("SELECT id, product_id, cls_startdate, cls_enddate FROM classes WHERE id = $class_id");
        $classdays = u::getClassDays($class_id);
        $holidays = u::getPublicHolidays($class_id, 0, $class_info->product_id);
        $class_start_date = date('Y-m-d', strtotime($class_info->cls_startdate));
        $class_end_date = date('Y-m-d', strtotime($class_info->cls_enddate));
        $x = new \Moment\Moment(strtotime(date('Y-m-d')));
        $c = new \Moment\Moment(strtotime($class_start_date));
        $z = new \Moment\Moment(strtotime($class_end_date));
        $start_date = $x->from($c)->getDays() <= 0 ? date('Y-m-d') : $class_start_date;
        $s = new \Moment\Moment(strtotime($start_date));
        $end_date = $s->addDays(25)->format('Y-m-d');
        $e = new \Moment\Moment(strtotime($end_date));
        $end_date = $s->from($e)->getDays() <= 0 ? $end_date : $class_end_date;
        $schedule_info = u::calcDoneSessions($start_date, $end_date, $holidays, $classdays);
        if (isset($schedule_info->dates)) {
          foreach($schedule_info->dates as $date) {
            $p = new \Moment\Moment(strtotime($date));
            $info = (object)[];
            $info->date = $date;
            $info->blocks = u::getClassBlockByDate($class_id, $date);
            $data[]= $info;
          }
        }
      }
      return $response->formatResponse($code, $data);
    }

    public function addContracts(Request $request) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = [];
        $code = APICode::SUCCESS;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $branches = $session->branches_ids;
        $post = (Object)$request->input();
        if ($post) {
          $class_id = $post->class;
          $contracts = isset($post->contracts) ? $post->contracts : [];
          if (count($contracts)) {
            $program_info = u::first("SELECT program_id FROM classes WHERE id = $class_id LIMIT 1");
            $program_id = $program_info && isset($program_info->program_id) ? (int)$program_info->program_id : 0;
            $i = 0;
            $insert_log_query = "INSERT INTO log_manager_transfer (student_id, from_branch_id, to_branch_id, from_cm_id, to_cm_id, date_transfer, updated_by, note, created_at, updated_at) VALUES ";
            $insert_log_student_update = "INSERT INTO log_student_update (student_id,updated_by,updated_at,content,`status`,cm_id,branch_id,ceo_branch_id) VALUES ";
            $update_term_query = "INSERT INTO term_student_user (id, cm_id, updated_at) VALUES ";
            $class_information = $post->class_info;
            $class_data = u::first("SELECT cl.*, t.product_id prd_id, s.teacher_id, GROUP_CONCAT(DISTINCT(s.class_day)) enrolment_schedule FROM classes cl LEFT JOIN sessions s ON s.class_id = cl.id LEFT JOIN term_program_product t ON t.program_id = cl.program_id WHERE cl.id = $class_id");
            $teacher_id = $class_data->teacher_id;
            $program_id = $class_data->program_id;
            $class_schedule = $class_data->enrolment_schedule;
            $product_id = $class_data->prd_id;
            $cm_id = $class_information['cm_id'];
            $csLeaderOfBranch = u::first("SELECT user_id FROM term_user_branch WHERE branch_id = $class_data->branch_id AND role_id = 56");
            $om_id = $csLeaderOfBranch ? $csLeaderOfBranch->user_id : null;
            $class_start_date = date('Y-m-d', strtotime($class_information['class_start_date']));
            $class_end_date = date('Y-m-d', strtotime($class_information['class_end_date']));
            $classdays = u::getClassDays($class_id);
            $holidays = u::getPublicHolidays($class_id, 0, $product_id);
            $hash_list = [];
            $updated_students_ids = [];
            $contract_update_data = array();
            $student_list = [];
            foreach ($contracts as $contract) {
              $contract = (Object)$contract;
              $available_sessions = (int)$contract->available_sessions ? (int)$contract->available_sessions : 3;
              $info_class_date = explode('~', $contract->class_date);
              $start_cjrn_id = count($info_class_date) > 1 ? (int)$info_class_date[0] : '';
              $start_date = count($info_class_date) > 1 ? $info_class_date[1] : '';
              $left_infor = u::calSessions($start_date, $class_end_date, $holidays, $classdays);
              $left_sesssions = md5(date('Y-m-d', strtotime($start_date))) === md5(date('Y-m-d', strtotime($class_end_date))) ? 1 : (int)$left_infor->total - 1; //hack code to fix addition 1 session when enrolment
              $left_sesssions = (int)$left_sesssions == 0 ? 1 : $left_sesssions;
              $information = u::calEndDate($available_sessions, $classdays, $holidays, $start_date);
              $previous_final_last_date = u::first("SELECT enrolment_last_date FROM contracts WHERE enrolment_last_date IS NOT NULL AND student_id = ".$contract->student_id." ORDER BY id DESC LIMIT 0, 1");
              $previous_final_last_date = $previous_final_last_date && isset($previous_final_last_date->final_last_date) ? "'$previous_final_last_date->final_last_date'" : "NULL";
              $recharge_counter = (int)$contract->count_recharge;
              $student_id = $contract->student_id;
              $student_list[] = $student_id;
              if ($recharge_counter == 0) {
                $total_real_session = u::first("SELECT COALESCE(SUM(real_sessions), 0) AS total FROM contracts WHERE student_id = $student_id AND count_recharge > 0 AND real_sessions > 0");
                $special_start_date = u::calEndDate(2, $classdays, $holidays, $information->end_date);
                $special_last_date = u::calEndDate((int)$total_real_session->total, $classdays, $holidays, $special_start_date->end_date);
                $previous_final_last_date = isset($special_last_date->end_date) ? $special_last_date->end_date : $previous_final_last_date;
              } elseif ($recharge_counter > 0) {
                $checkHigher = u::query("SELECT id FROM contracts WHERE student_id = $student_id AND count_recharge > $recharge_counter");
                if (count($checkHigher) == 0) {
                  $previous_final_last_date = "NULL";
                }
              }
              // dd($previous_final_last_date);
              // dd($start_date, $information, $available_sessions);
              $hash_key = md5($class_id.$contract->id.$contract->student_id.$class_start_date.$class_data->enrolment_schedule.$class_end_date.$left_sesssions.$available_sessions);
              $i++;
              $updated_students_ids[]= $student_id;
              $hash_list[] = "'$hash_key'";
              // 'end_date' => $information->end_date,
              $contract_update_data[] = (object)array(
                  'id'         => $contract->id,
                  'class_id'   => $class_id,
                  'product_id' => $product_id,
                  'program_id' => $program_id,
                  'updated_at' => date('Y-m-d H:i:s'),
                  'editor_id'  => $user_id,
                  'status'     => 6,
                  'cm_id'      => $cm_id,
                  'om_id'      => $om_id,
                  'teacher_id'     => $teacher_id,
                  'start_cjrn_id'  => $start_cjrn_id,
                  'enrolment_type' => 1,
                  'enrolment_schedule'   => $class_data->enrolment_schedule,
                  'enrolment_updated_at' => date('Y-m-d H:i:s'),
                  'enrolment_updator_id' => $user_id,
                  'enrolment_start_date' => $start_date,
                  'enrolment_end_date'   => $class_end_date,
                  'enrolment_expired_date'  => $previous_final_last_date != 'NULL' ? $previous_final_last_date : $information->end_date,
                  'enrolment_last_date'     => $information->end_date,
                  'enrolment_real_sessions' => $available_sessions,
                  'enrolment_left_sessions' => $left_sesssions,
                  'enrolment_accounting_id' => str_replace('PNH', 'PVH', $contract->accounting_id),
                  'enrolment_withdraw_date' => NULL,
                  'hash_key' => $hash_key,
                  'action'      => "just_enrolment_for_contract_".$contract->id
              );
              // print_r($contract_update_data); die;
              $strCmId = $cm_id?:"NULL";
              $insert_log_query.= "($student_id, $contract->branch_id, $contract->branch_id, $strCmId, $strCmId, NOW(), $user_id, 'Từ quá trình xếp lớp', NOW(), NOW())";
              $insert_log_student_update.= "($student_id,".$request->users_data->id.",'".date('Y-m-d H:i:s')."','Xếp học sinh vào lớp ".$class_information['class_name']."',1,'$cm_id','$contract->branch_id','$contract->ceo_branch_id')";
              $update_term_query.= "($contract->student_id, $strCmId, NOW())";
              if ($i < (int)count($contracts)) {
                $insert_log_query.= ",";
                $insert_log_student_update.= ",";
                $update_term_query.= ",";
              }
            }
              $result_add_contracts = "";
            if ($contracts) {
              $cm_id = isset($post->class_info['cm_id'])? $post->class_info['cm_id']: null;
              $student_list = join(',', $student_list);
              $update_term_student_user = "Update term_student_user SET teacher_id = $teacher_id, cm_id = $cm_id WHERE student_id IN ($student_list)";
              u::query($update_term_student_user);
              $result_add_contracts = u::updateMultiContracts($contract_update_data);
                if(!isset($request->is_import) || $request->is_import !==1) {
                    foreach ($contracts as $c) {
                        $ct = (Object)$c;
                        if($ct->count_recharge>=0 && !($ct->type==10 && $ct->status=4)) {
                            $this->createCyberEnrolment($ct->id, $user_id);
                        }
                    }
                }
            }
            foreach ($contracts as $contract) {
              $contract = (Object)$contract;
              $student_info = u::first("SELECT id_lms FROM students wHERE id=$contract->student_id");
              if($student_info->id_lms){
                $lsmAPI = new LMSAPIController();
                $lsmAPI->updateStudentLMS($contract->student_id);
              }else{
                $lsmAPI = new LMSAPIController();
                $lsmAPI->createStudentLMS($contract->id);
              }
              $sms_info = u::first("SELECT s.gud_mobile1,s.name AS student_name, (SELECT name FROM branches WHERE id= c.branch_id) AS branch_name,
                  (SELECT name FROM products WHERE id= c.product_id) AS product_name,
                  (SELECT address FROM branches WHERE id= c.branch_id) AS address_name,
                  (SELECT hotline FROM branches WHERE id= c.branch_id) AS hotline,
                  (SELECT full_name FROM users WHERE id=c.ec_id) AS ec_name,
                  (SELECT phone FROM users WHERE id=c.ec_id) AS ec_phone, c.enrolment_start_date ,
                  (SELECT st.start_time FROM sessions AS ss LEFT JOIN shifts AS st ON st.id=ss.shift_id WHERE ss.class_id=c.class_id AND ss.status=1 LIMIT 1) AS shift_start,
                  (SELECT st.end_time FROM sessions AS ss LEFT JOIN shifts AS st ON st.id=ss.shift_id WHERE ss.class_id=c.class_id AND ss.status=1 LIMIT 1) AS shift_end,
                  (SELECT ss.class_day FROM sessions AS ss WHERE ss.class_id=c.class_id AND ss.status=1 LIMIT 1) AS class_day
                FROM contracts AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE c.id=$contract->id");
              $sms_hotline = $sms_info->hotline;
              $sms_phone=$sms_info->gud_mobile1;
              /*Cảm ơn Quý PH đã tin tưởng và lựa chọn CMS Edu là nơi phát triển năng lực tư duy, khả năng sáng tạo cho bé [Tên học sinh]. Kính gửi Quý PH thông tin nhập học như sau: 
                Chương trình học: [Ucrea/Bright IG/Black Hole]
                Thời gian bắt đầu học: [Ngày+giờ cụ thể của lớp học]
                Địa điểm học: [Tên trung tâm]
                Địa chỉ: [Địa chỉ trung tâm]
                Vui lòng liên hệ hotline [hotline trung tâm] hoặc TVV [ Tên+số điện thoại TVV] để được hỗ trợ. Trân trọng! */
              $sms_content = " Cam on Quy PH da tin tuong va lua chon CMSedu la noi phat trien nang luc tu duy, kha nang sang tao cho be ".u::convert_name($sms_info->student_name).". Kinh gui Quy PH thong tin nhap hoc nhu sau: 
              Chuong trinh hoc: ".u::convert_name($sms_info->product_name).". 
              Thoi gian bat dau hoc: ".substr($sms_info->shift_start, 0, 5)." - " .substr($sms_info->shift_end, 0, 5)." ".u::getDayNameViKhongDau($sms_info->class_day).".
              Dia diem hoc: ".u::convert_name($sms_info->branch_name).". 
              Dia chi: ".u::convert_name($sms_info->address_name).". 
              Vui long lien he hotline ".$sms_hotline." hoac TVV ".u::convert_name($sms_info->ec_name)." ".$sms_info->ec_phone." de duoc ho tro. Tran trong!";
              $sms =new Sms();
              $sms->sendSms($sms_phone,$sms_content);
            }
            if ($result_add_contracts) {
              $update_term_query.= " ON DUPLICATE KEY UPDATE cm_id = VALUES(cm_id), updated_at = NOW()";
              $updated_students_ids = implode(',', $updated_students_ids);
              $update_pendings_query = "UPDATE pendings SET end_date = SUBDATE(DATE('$start_date'),1) WHERE student_id IN ($updated_students_ids)";
              $update_students_status = "UPDATE students SET status = 6, updated_at = NOW(), editor_id = $user_id WHERE id IN ($updated_students_ids)";
              u::query($insert_log_query);
              u::query($insert_log_student_update);
              u::query($update_term_query);
              u::query($update_pendings_query);
              $data = $contracts;
            } else {
              $code = APICode::SUCCESS;
              $data = "Update false with start_date = $start_date & left_sesssions = $left_sesssions & available_sessions = $available_sessions";
            }
          }
        }
      }
      return $response->formatResponse($code, $data);
    }

    private function createCyberEnrolment($contract_id, $user_id){
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
      $res = $cyberAPI->createEnrolment($res, $user_id);
      if($res){
        // u::query("UPDATE contracts SET enrolment_accounting_id = '$res' WHERE id = $contract_id");
      }
    }

    public function withdraw(Request $request, $id) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $code = APICode::SUCCESS;
        $user_id = $session->id;
        if ($id) {
            $current_contract = u::first("SELECT * FROM contracts WHERE id = $id");
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
                $lsmAPI = new LMSAPIController();
                $lsmAPI->updateStudentLMS($current_contract->student_id);
              }
            }
            $data = true;
        }
      }
      return $response->formatResponse($code, $data);
    }

    public function contracts(Request $request, $pagination, $search) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $pagination = json_decode($pagination);
        $search = json_decode($search);
        $class_id = isset($search->class) ? (int)$search->class : 0;
        $branch_id = isset($search->branch) ? (int)$search->branch : 0;
        $keyword = isset($search->keyword) ? trim($search->keyword) : '';
        $where = 'AND (c.`debt_amount` = 0  OR c.debt_amount IS NULL)';
        // if($session->role_id == 999999999){
        //     $where = ' AND (c.`debt_amount` > 0 OR c.`debt_amount` = 0) ';
        // }
        $limit = '';
        if ($pagination->cpage && $pagination->limit) {
          $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
          $limit.= " LIMIT $offset, $pagination->limit";
        }

          $class_info = u::first("SELECT product_id,is_trial FROM classes WHERE id = $class_id");
          $is_trial = $class_info->is_trial;
          if ($is_trial === 0 && $class_info->product_id !=100 ){
                $where_type = "(type > 0 AND status IN (1,2,3,4,5))";
                $c_type = "c.type IN (8, 85, 3,4)";
          }
          else{
              $where_type = "(type = 0 AND status IN (1,3,5))";
              $c_type = "c.type IN (0)";
          }

        $join = "JOIN ( SELECT student_id, Min( count_recharge ) AS m, id FROM contracts 
                      WHERE branch_id = $branch_id 
                        AND `status` < 6
                        AND ($where_type)
                        AND summary_sessions > 0
                      GROUP BY student_id ) AS b ON (c.student_id = b.student_id AND c.count_recharge = b.m )";
        if ($keyword != '') {
            $where.= " AND
            ( s.crm_id LIKE '$keyword%'
            OR s.cms_id LIKE '$keyword%'
            OR s.name LIKE '%$keyword%'
            OR s.email LIKE '%$keyword%'
            OR s.phone LIKE '$keyword%'
            OR s.accounting_id = '$keyword'
            OR s.gender LIKE '$keyword')";
        }
        $special_condition = '';
        $available_students = u::first("SELECT ((SELECT max_students FROM classes WHERE id = $class_id)
            -
            (SELECT COUNT(DISTINCT s.id) AS total FROM contracts AS c
            LEFT JOIN students AS s ON c.student_id = s.id
          WHERE c.class_id = $class_id AND c.status = 6 AND 
            c.status!=7 AND c.enrolment_start_date <= c.enrolment_end_date)) AS total");
        $total_available = (int)$available_students->total;
        $code = APICode::SUCCESS;
        ///$pending_condition = " AND COALESCE(p.id, (p.start_date > CURDATE() OR p.end_date < CURDATE()), true) ";

        $pending_condition = "";
        $data = (Object)[];
        $select = "SELECT
                    c.id,
                    c.accounting_id,
                    c.product_id,
                    c.id AS contract_id,
                    c.code AS contract_code,
                    s.id AS student_id,
                    s.cms_id AS student_cms_id,
                    c.type AS customer_type,
                    s.name AS student_name,
                    s.nick,
                    c.count_recharge,
                    c.type,
                    CONCAT(s.name, ' - ', COALESCE(s.stu_id, s.crm_id)) AS label,
                    s.school AS student_school,
                    g.name AS student_school_grade,
                    s.phone AS student_phone,
                    s.gud_name1,
                    s.gud_mobile1,
                    (SELECT c0.enrolment_last_date from contracts c0 where c0.student_id = c.student_id and c0.status = 7 and c0.count_recharge < c.count_recharge order by c0.count_recharge desc limit 1 ) as st_last_date,
                    (SELECT file FROM trial_reports WHERE student_id = c.student_id AND session_no = 9 ORDER BY id DESC LIMIT 1) attached_file, 
                    c.start_date AS contract_start_date,
                    t.name AS tuition_fee_name,
                    t.price AS tuition_fee_price,
                    t.receivable AS tuition_fee_receivable,
                     CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
                    c.debt_amount,
                    c.processed, 
                    c.branch_id, 
                    c.ceo_branch_id,
                    c.status, 
                    c.total_charged AS charged_total,
                    c.summary_sessions AS available_sessions,
                    0 AS checked,
                    '' AS class_date";
        /* replace here ON (c.id = b.id AND c.count_recharge = b.m ) */
        $query = "FROM contracts AS c
                    LEFT JOIN students AS s ON c.student_id = s.id
                    LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
                    LEFT JOIN school_grades AS g ON s.school_grade = g.id
                    LEFT JOIN users AS u0 ON c.cm_id = u0.id
                    LEFT JOIN users AS u1 ON c.ec_id = u1.id
                    LEFT JOIN reserves AS r ON c.student_id = r.student_id
                    LEFT JOIN pendings AS p ON c.student_id = p.student_id
                    LEFT JOIN classes AS cl ON cl.program_id = c.program_id                    
                    $join
                    WHERE c.branch_id = $branch_id $special_condition 
                        AND c.`status` < 6
                        AND ((c.type > 0 AND c.status IN (1,2,3,4,5)) OR (c.type = 0 AND c.status IN (1,3,5)))
                        AND (c.product_id = (SELECT se.product_id FROM semesters se RIGHT JOIN classes cls ON cls.sem_id = se.sem_id WHERE cls.id = $class_id)) 
                        AND (cl.id = $class_id OR cl.id IS NULL)
                        AND ((c.summary_sessions > 0 AND c.type IN (1,2,3,4,5,6,7)) OR ($c_type) OR c.type = 10)
	                      AND ((c.total_charged > 0 AND c.type IN (1,2,3,4,5,6,7)) OR ($c_type) OR c.type = 10)
                        AND COALESCE(r.id, (r.start_date > CURDATE() OR r.end_date < CURDATE()), true)
                        AND (s.id NOT IN (SELECT student_id FROM contracts WHERE `status` = 6 AND student_id IS NOT NULL GROUP BY student_id) OR ((SELECT enrolment_last_date FROM contracts WHERE `status` = 1 AND student_id = c.student_id AND id = c.id) <= CURDATE())) 
                        AND s.waiting_status=0
                    $pending_condition
                    $where ";
        // $conditionTimeGetClassDate = $session->role_id == '999999999' ? " ": " AND cjrn_classdate >= CURRENT_DATE ";
        $conditionTimeGetClassDate = "";
        $classdate = u::query("SELECT cls_id, cjrn_id, cjrn_classdate FROM schedules WHERE class_id = $class_id $conditionTimeGetClassDate AND `status` > 0 ORDER BY cjrn_classdate ASC");
        $product_id = $class_info->product_id;
        $holidays = u::getPublicHolidays($class_id, 0, $product_id);
        $classdates = [];
        if ($classdate) {
          foreach ($classdate as $startdate) {
            if (!u::checkInHolydays($startdate->cjrn_classdate, $holidays)) {
              $classdates[]= $startdate;
            }
          }
        }
        $total = u::first("SELECT COUNT(DISTINCT c.student_id) AS total $query ");
        $total = (int)$total->total;
        //echo "$select $query ORDER BY c.status DESC, c.count_recharge ASC $limit";exit();
          if ($keyword != '')
              $list = u::query("$select $query AND c.status != 7 ORDER BY c.`count_recharge` ASC  LIMIT 1");
          else
              $list = u::query("$select $query GROUP BY c.student_id ORDER BY c.status DESC, c.count_recharge ASC $limit");
        $data->contracts = $list;
        $data->search = $search;
        $data->available = $total_available;
        $data->class_dates = $classdates;
        $data->duration = $pagination->limit * 10;
        $data->pagination = apax_get_pagination($pagination, $total);
      }
      return $response->formatResponse($code, $data);
    }

    public function loadprogram(Request $request, $branch_id, $semester_id){
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = [];
        $code = APICode::SUCCESS;
        $data = u::query("SELECT * FROM programs WHERE  branch_id=$branch_id AND semester_id=$semester_id AND `status`=1");
      }
      return $response->formatResponse($code, $data);
    }

    public function loadClassExtendInfo(Request $request, $class_id) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $class_id = (int)$class_id;
        $data = (Object)[];
        $class_info = u::first("SELECT
          c.id AS class_id,
          c.product_id,
          c.cls_name AS class_name,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          IF(((c.cls_enddate < CURDATE()) OR (c.cls_iscancelled = 'yes')), 0, 1) as can_update,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          c.brch_id AS brch_id,
          c.branch_id as branch_id,
          CONCAT(u.full_name, ' - ', u.username) AS cm_name,
          u.status AS cm_status,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', COALESCE(t.avatar, 'avatar.png'), '#', t.ins_name) SEPARATOR '@ ') FROM teachers AS t
            LEFT JOIN `sessions` AS s ON s.teacher_id = t.user_id WHERE s.class_id = $class_id) AS teachers_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', r.room_name) SEPARATOR '@ ') FROM rooms AS r
            LEFT JOIN `sessions` AS s ON s.room_id = r.id WHERE s.class_id = $class_id) AS rooms_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', s.class_day) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN classes AS c ON s.class_id = c.id WHERE s.class_id = $class_id) AS weekdays,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', f.start_time, '-', f.end_time) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN shifts AS f ON s.shift_id = f.id WHERE s.class_id = $class_id) AS shifts_name
        FROM classes AS c
          LEFT JOIN users AS u ON c.cm_id = u.id
        WHERE c.id = $class_id");
        $students_list = u::query("SELECT
            c.id AS contract_id,
            c.student_id,
            s.name AS student_name,
            s.cms_id AS cms_id,
            t.name AS tuition_fee_name,
            t.receivable AS tuition_fee_price,
            t.`session` AS tuition_fee_sessions,
            cl.cls_name AS `class`,
            c.enrolment_start_date AS start_date,
            c.enrolment_end_date AS end_date,
            c.total_sessions AS total_sessions,
            c.must_charge, 
            c.bill_info,
            p.method AS payment_method,
            c.type AS customer_type,
            c.total_charged AS charged_total,
            IF(c.status = 6 AND c.enrolment_expired_date <= CURDATE(), 1, 0) withdraw,
            IF(c.status = 6 AND c.enrolment_expired_date <= CURDATE(), 2, IF(c.status = 0, 3, 1)) ordering,
            c.enrolment_real_sessions AS available_sessions,
            c.enrolment_type enrolment_status, 
            c.status,
            COALESCE(c.enrolment_last_date, c.enrolment_expired_date) last_date,
            CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
            CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
            u3.hrm_id AS ec_leader_id
          FROM contracts AS c
            LEFT JOIN students AS s ON c.student_id = s.id
            LEFT JOIN payment AS p ON c.payment_id = p.id
            LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
            LEFT JOIN classes AS cl ON c.class_id = cl.id
            LEFT JOIN users AS u1 ON c.ec_id = u1.id
            LEFT JOIN users AS u2 ON c.cm_id = u2.id
            LEFT JOIN users AS u3 ON c.ec_leader_id = u3.id
          WHERE c.class_id = $class_id AND c.enrolment_start_date <= c.enrolment_end_date ORDER BY ordering");
        $count_students = u::first("SELECT COUNT(*) AS total FROM (SELECT c.id AS total FROM contracts c
          LEFT JOIN students AS s ON c.student_id = s.id
        WHERE c.class_id = $class_id
          AND c.enrolment_end_date >= CURDATE() AND c.enrolment_expired_date >= CURDATE() AND c.status = 6
          AND c.enrolment_start_date <= c.enrolment_end_date AND c.enrolment_expired_date >= c.enrolment_start_date GROUP BY c.student_id) AS a");
        $total_students = $count_students->total;
        $teachers = [];
        $weekdays = [];
        $namrooms = [];
        $nashifts = [];
        $sessions = (Object)[];
        $weekdays_text = '';
        $namrooms_text = '';
        $nashifts_text = '';
        $teachers_text = '';
        $displayed = [];
        if ($class_info->weekdays) {
          $weekdays = explode('@', $class_info->weekdays);
          if ($weekdays) {
            $i = 0;
            foreach ($weekdays as $weekday) {
              $weekday_info = explode('|', $weekday);
              $i++;
              if ($weekday_info) {
                $sid = "session_$weekday_info[0]";
                $inf = $weekday_info[1];
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                $display = apax_ada_get_weekday($inf);
                if (!in_array(md5($display), $displayed)) {
                  $sessions->$sid->weekday = apax_ada_get_weekday($inf);
                  $sessions->$sid->information = apax_ada_get_weekday($inf);
                  $weekdays_text.= $display;
                  $displayed[] = md5($display);
                  if ($i < count($weekdays)) {
                    $weekdays_text.= ', ';
                  }
                }
              }
            }
          }
        }
        if ($class_info->shifts_name) {
          $nashifts = explode('@', $class_info->shifts_name);
          if ($nashifts) {
            $i = 0;
            foreach ($nashifts as $nashift) {
              $nashift_info = explode('|', $nashift);
              $i++;
              if ($nashift_info) {
                $sid = "session_$nashift_info[0]";
                $inf = $nashift_info[1];
                $inf = explode('-', $inf);
                $sta = $inf[0];
                $end = $inf[1];
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                $lab = substr($sta, 0, 5).'~'.substr($end, 0, 5);
                $sessions->$sid->shift = $lab;
                $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '.$lab : $lab;
                $nashifts_text.= $lab;
                $displayed[] = md5($lab);
                if ($i < count($nashifts)) {
                  $nashifts_text.= ', ';
                }
              }
            }
          }
        }
        if ($class_info->rooms_name) {
          $namrooms = explode('@', $class_info->rooms_name);
          if ($namrooms) {
            $i = 0;
            foreach ($namrooms as $namroom) {
              $namroom_info = explode('|', $namroom);
              $i++;
              if ($namroom_info) {
                $sid = "session_$namroom_info[0]";
                $inf = $namroom_info[1];
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                if (!in_array(md5($inf), $displayed)) {
                  $sessions->$sid->room = "Room: $inf";
                  $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '."Room: $inf" : "Room: $inf";
                  $namrooms_text.= $inf;
                  $displayed[] = md5($inf);
                  if ($i < count($namrooms)) {
                    $namrooms_text.= ', ';
                  }
                }
              }
            }
          }
        }
        if ($class_info->teachers_name) {
          $teachers = explode('@', $class_info->teachers_name);
          if ($teachers) {
            $i = 0;
            foreach ($teachers as $teacher) {
              $teacher_info = explode('|', $teacher);
              $i++;
              if ($teacher_info) {
                $sid = "session_$teacher_info[0]";
                $inf = $teacher_info[1];
                $inf = explode('#', $inf);
                $ava = $inf[0];
                $ten = $inf[1];
                $lab = md5($ava) == md5('avatar.png') ? '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/avatar.png"/> '.$ten : '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/teachers/'.$ava.'"/> '.$ten;
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                if (!in_array(md5($lab), $displayed)) {
                  $sessions->$sid->teacher_avatar = $ava;
                  $sessions->$sid->teacher_name = $ten;
                  $sessions->$sid->teacher_label = $lab;
                  $teachers_text.= $lab;
                  $displayed[] = md5($lab);
                  if ($i < count($teachers)) {
                    $teachers_text.= ', ';
                  }
                }
              }
            }
          }
        }
        $time_place = '';
        if ($sessions) {
          $i = 0;
          foreach ($sessions as $s => $o) {
            if (isset($o->information) && strlen($o->information) > 12) {
              $i++;
              $time_place.= $o->information;
              if ($i < count((array)$sessions)) {
                $time_place.= '<br/>';
              }
            }
          }
        }
        // if (count($students_list)) {
        // $classdays = u::getClassDays($class_id);
        // $class_info = u::first("SELECT product_id FROM classes WHERE id = $class_id");
        // $product_id = $class_info->product_id;
        // $holidays = u::getPublicHolidays($class_id, 0, $product_id);
        //   foreach ($students_list as $student) {
        //     $information = u::getRealSessions($student->available_sessions, $classdays, $holidays);
        //     $student->available_sessions = $information && isset($information->total) ? $information->total : 0;
        //     $student->last_date = $information && isset($information->end_date) ? $information->end_date : '';
        //   }
        // }
        $class_info->total_students = $total_students;
        $class_info->sessions = $sessions;
        $class_info->class_time = "$class_info->class_start_date ~ $class_info->class_end_date";
        $class_info->teachers_name = $teachers_text;
        $class_info->shifts_name = $nashifts_text;
        $class_info->rooms_name = $namrooms_text;
        $class_info->weekdays = $weekdays_text;
        $class_info->time_and_place = $time_place;
        $data->class = $class_info;
        $data->students = $students_list;
        $code = APICode::SUCCESS;
      }
      return $response->formatResponse($code, $data);
    }

    public function getClassExtendInfo(Request $request, $class_id) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $class_id = (int)$class_id;
            $data = (Object)[];
            $class_info = u::first("SELECT
          c.id AS class_id,
          c.product_id,
          c.cls_name AS class_name,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          c.brch_id AS brch_id,
          CONCAT(u.full_name, ' - ', u.username) AS cm_name,
          u.status AS cm_status,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', COALESCE(t.avatar, 'avatar.png'), '#', t.ins_name) SEPARATOR '@ ') FROM teachers AS t
            LEFT JOIN `sessions` AS s ON s.teacher_id = t.user_id WHERE s.class_id = $class_id) AS teachers_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', r.room_name) SEPARATOR '@ ') FROM rooms AS r
            LEFT JOIN `sessions` AS s ON s.room_id = r.id WHERE s.class_id = $class_id) AS rooms_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', s.class_day) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN classes AS c ON s.class_id = c.id WHERE s.class_id = $class_id) AS weekdays,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', f.start_time, '-', f.end_time) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN shifts AS f ON s.shift_id = f.id WHERE s.class_id = $class_id) AS shifts_name
        FROM classes AS c
          LEFT JOIN users AS u ON c.cm_id = u.id
        WHERE c.id = $class_id");
        $students_list = u::query("SELECT
            c.student_id,
            s.name AS student_name,
            s.cms_id AS cms_id
          FROM contracts AS c
            LEFT JOIN students AS s ON c.student_id = s.id
            LEFT JOIN classes AS cl ON c.class_id = cl.id
          WHERE c.class_id = $class_id AND c.enrolment_start_date <= c.enrolment_end_date AND c.status > 0");
            $count_students = u::first("SELECT COUNT(*) AS total FROM (SELECT c.id AS total FROM contracts AS c
          LEFT JOIN students AS s ON c.student_id = s.id
        WHERE c.class_id = $class_id
          AND c.enrolment_end_date >= CURDATE() AND c.enrolment_expired_date >= CURDATE() AND c.status = 6
          AND c.enrolment_start_date <= c.enrolment_end_date AND c.enrolment_expired_date >= c.enrolment_start_date GROUP BY c.student_id) AS a");
            $total_students = $count_students->total;
            $teachers = [];
            $weekdays = [];
            $namrooms = [];
            $nashifts = [];
            $sessions = (Object)[];
            $weekdays_text = '';
            $namrooms_text = '';
            $nashifts_text = '';
            $teachers_text = '';
            $displayed = [];
            if ($class_info->weekdays) {
                $weekdays = explode('@', $class_info->weekdays);
                if ($weekdays) {
                    $i = 0;
                    foreach ($weekdays as $weekday) {
                        $weekday_info = explode('|', $weekday);
                        $i++;
                        if ($weekday_info) {
                            $sid = "session_$weekday_info[0]";
                            $inf = $weekday_info[1];
                            $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                            $display = apax_ada_get_weekday($inf);
                            if (!in_array(md5($display), $displayed)) {
                                $sessions->$sid->weekday = apax_ada_get_weekday($inf);
                                $sessions->$sid->information = apax_ada_get_weekday($inf);
                                $weekdays_text.= $display;
                                $displayed[] = md5($display);
                                if ($i < count($weekdays)) {
                                    $weekdays_text.= ', ';
                                }
                            }
                        }
                    }
                }
            }
            if ($class_info->shifts_name) {
                $nashifts = explode('@', $class_info->shifts_name);
                if ($nashifts) {
                    $i = 0;
                    foreach ($nashifts as $nashift) {
                        $nashift_info = explode('|', $nashift);
                        $i++;
                        if ($nashift_info) {
                            $sid = "session_$nashift_info[0]";
                            $inf = $nashift_info[1];
                            $inf = explode('-', $inf);
                            $sta = $inf[0];
                            $end = $inf[1];
                            $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                            $lab = substr($sta, 0, 5).'~'.substr($end, 0, 5);
                            $sessions->$sid->shift = $lab;
                            $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '.$lab : $lab;
                            $nashifts_text.= $lab;
                            $displayed[] = md5($lab);
                            if ($i < count($nashifts)) {
                                $nashifts_text.= ', ';
                            }
                        }
                    }
                }
            }
            if ($class_info->rooms_name) {
                $namrooms = explode('@', $class_info->rooms_name);
                if ($namrooms) {
                    $i = 0;
                    foreach ($namrooms as $namroom) {
                        $namroom_info = explode('|', $namroom);
                        $i++;
                        if ($namroom_info) {
                            $sid = "session_$namroom_info[0]";
                            $inf = $namroom_info[1];
                            $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                            if (!in_array(md5($inf), $displayed)) {
                                $sessions->$sid->room = "Room: $inf";
                                $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '."Room: $inf" : "Room: $inf";
                                $namrooms_text.= $inf;
                                $displayed[] = md5($inf);
                                if ($i < count($namrooms)) {
                                    $namrooms_text.= ', ';
                                }
                            }
                        }
                    }
                }
            }
            if ($class_info->teachers_name) {
                $teachers = explode('@', $class_info->teachers_name);
                if ($teachers) {
                    $i = 0;
                    foreach ($teachers as $teacher) {
                        $teacher_info = explode('|', $teacher);
                        $i++;
                        if ($teacher_info) {
                            $sid = "session_$teacher_info[0]";
                            $inf = $teacher_info[1];
                            $inf = explode('#', $inf);
                            $ava = $inf[0];
                            $ten = $inf[1];
                            $lab = md5($ava) == md5('avatar.png') ? '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/avatar.png"/> '.$ten : '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/teachers/'.$ava.'"/> '.$ten;
                            $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                            if (!in_array(md5($lab), $displayed)) {
                                $sessions->$sid->teacher_avatar = $ava;
                                $sessions->$sid->teacher_name = $ten;
                                $sessions->$sid->teacher_label = $lab;
                                $teachers_text.= $lab;
                                $displayed[] = md5($lab);
                                if ($i < count($teachers)) {
                                    $teachers_text.= ', ';
                                }
                            }
                        }
                    }
                }
            }
            $time_place = '';
            if ($sessions) {
                $i = 0;
                foreach ($sessions as $s => $o) {
                    if (isset($o->information) && strlen($o->information) > 12) {
                        $i++;
                        $time_place.= $o->information;
                        if ($i < count((array)$sessions)) {
                            $time_place.= '<br/>';
                        }
                    }
                }
            }
            // if (count($students_list)) {
            // $classdays = u::getClassDays($class_id);
            // $class_info = u::first("SELECT product_id FROM classes WHERE id = $class_id");
            // $product_id = $class_info->product_id;
            // $holidays = u::getPublicHolidays($class_id, 0, $product_id);
            //   foreach ($students_list as $student) {
            //     $information = u::getRealSessions($student->available_sessions, $classdays, $holidays);
            //     $student->available_sessions = $information && isset($information->total) ? $information->total : 0;
            //     $student->last_date = $information && isset($information->end_date) ? $information->end_date : '';
            //   }
            // }
            $class_info->total_students = $total_students;
            $class_info->sessions = $sessions;
            $class_info->class_time = "$class_info->class_start_date ~ $class_info->class_end_date";
            $class_info->teachers_name = $teachers_text;
            $class_info->shifts_name = $nashifts_text;
            $class_info->rooms_name = $namrooms_text;
            $class_info->weekdays = $weekdays_text;
            $class_info->time_and_place = $time_place;
            $data->class = $class_info;

            $students = [];
            if(!empty($students_list)){
                foreach ($students_list as $s){
                    $students[$s->student_id] = $s;
                }
            }
            $data->students = $students;
            $code = APICode::SUCCESS;
        }
        return $response->formatResponse($code, $data);
    }

    public function loadClassInfo(Request $request, $class_id) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $class_id = (int)$class_id;
        $data = (Object)[];
        $class_info = u::first("SELECT
          c.id,
          c.is_trial,
          c.id AS class_id,
          c.product_id,
          c.cls_name AS class_name,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          u.status AS cm_status,
          CONCAT(u.full_name, ' - ', u.username) AS cm_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', COALESCE(t.avatar, 'avatar.png'), '#', t.ins_name) SEPARATOR '@ ') FROM teachers AS t
            LEFT JOIN `sessions` AS s ON s.teacher_id = t.user_id WHERE s.class_id = $class_id) AS teachers_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', r.room_name) SEPARATOR '@ ') FROM rooms AS r
            LEFT JOIN `sessions` AS s ON s.room_id = r.id WHERE s.class_id = $class_id) AS rooms_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', s.class_day) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN classes AS c ON s.class_id = c.id WHERE s.class_id = $class_id) AS weekdays,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', f.start_time, '-', f.end_time) SEPARATOR ', ') FROM `sessions` AS s
            LEFT JOIN shifts AS f ON s.shift_id = f.id WHERE s.class_id = $class_id) AS shifts_name,
          (SELECT t.id FROM `sessions` AS s LEFT JOIN `teachers` AS t ON s.teacher_id = t.user_id LEFT JOIN users u ON u.id = t.user_id WHERE u.status > 0 AND s.class_id = $class_id LIMIT 1) AS teacher_id,
          (SELECT s.room_id FROM `sessions` AS s WHERE s.class_id = $class_id LIMIT 1) AS room_id,
          (SELECT s.shift_id FROM `sessions` AS s WHERE s.class_id = $class_id LIMIT 1) AS shift_id,
          (SELECT GROUP_CONCAT(s.class_day SEPARATOR ',') FROM `sessions` AS s WHERE s.class_id = $class_id) AS arr_weekdays,
           (SELECT count(cjrn_id) FROM schedules WHERE class_id=$class_id) AS num_session
        FROM classes AS c
          LEFT JOIN users AS u ON c.cm_id = u.id
        WHERE c.id = $class_id");
        $classdays = u::getClassDays($class_id);
        $holidays = u::getPublicHolidays($class_id, 0, $class_info->product_id);
        $students_list = u::query("SELECT
            c.branch_id,
            c.count_recharge,
            c.id AS contract_id,
            c.student_id,
            c.id AS contract_id,
            s.name AS student_name,
            s.cms_id AS cms_id,
            s.crm_id,    
            t.name AS tuition_fee_name,
            t.receivable AS tuition_fee_price,
            t.`session` AS tuition_fee_sessions,
            cl.cls_name AS class,
            cl.id as class_id,
            c.branch_id,
            c.product_id,
            c.enrolment_start_date,
            c.enrolment_start_date AS start_date,
            c.enrolment_end_date AS end_date,
            c.total_sessions AS total_sessions,
            c.must_charge, 
            c.bill_info,c.program_id,c.product_id,
            COALESCE(p.method, 0) AS payment_method,
            c.type AS customer_type,
            COALESCE(c.debt_amount, c.must_charge - c.total_charged) AS debt_amount,
            c.total_charged AS charged_total,
            IF(c.status = 6 AND c.enrolment_last_date <= CURDATE(), 1, 0) as withdraw,
            c.summary_sessions AS available_sessions,
            c.enrolment_expired_date,
            c.enrolment_last_date last_date,
            c.enrolment_type enrolment_status, 
            c.status,
            CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
            CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
            (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE hrm_id = u1.superior_id LIMIT 1) AS ec_leader_name,
            (SELECT s.class_day FROM `sessions` AS s WHERE s.class_id = $class_id LIMIT 1) AS class_day
          FROM contracts AS c
            LEFT JOIN students AS s ON c.student_id = s.id
            LEFT JOIN payment AS p ON c.payment_id = p.id
            LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
            LEFT JOIN classes AS cl ON c.class_id = cl.id
            LEFT JOIN users AS u1 ON c.ec_id = u1.id
            LEFT JOIN users AS u2 ON c.cm_id = u2.id
          WHERE c.class_id = $class_id  AND c.status = 6
            AND c.enrolment_start_date <= c.enrolment_end_date AND c.enrolment_last_date >= c.enrolment_start_date GROUP BY c.student_id");
        $count_students = u::first("SELECT COUNT(*) AS total FROM (SELECT c.id AS total FROM contracts c
          LEFT JOIN students AS s ON c.student_id = s.id
        WHERE c.class_id = $class_id
          AND c.status = 6
          AND c.enrolment_end_date >= CURDATE()
          AND c.enrolment_last_date >= CURDATE()
          AND c.enrolment_start_date <= c.enrolment_end_date
          AND c.enrolment_last_date >= c.enrolment_start_date
          GROUP BY c.student_id) AS a");
        /*AND c.enrolment_last_date >= CURDATE() AND c.status = 6*/
        $total_students = $count_students->total;
        $teachers = [];
        $weekdays = [];
        $namrooms = [];
        $nashifts = [];
        $sessions = (Object)[];
        $weekdays_text = '';
        $namrooms_text = '';
        $nashifts_text = '';
        $teachers_text = '';
        $displayed = [];
        if ($class_info->weekdays) {
          $weekdays = explode('@', $class_info->weekdays);
          if ($weekdays) {
            $i = 0;
            foreach ($weekdays as $weekday) {
              $weekday_info = explode('|', $weekday);
              $i++;
              if ($weekday_info) {
                $sid = "session_$weekday_info[0]";
                $inf = $weekday_info[1];
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                $display = apax_ada_get_weekday($inf);
                if (!in_array(md5($display), $displayed)) {
                  $sessions->$sid->weekday = apax_ada_get_weekday($inf);
                  $sessions->$sid->information = apax_ada_get_weekday($inf);
                  $weekdays_text.= $display;
                  $displayed[] = md5($display);
                  if ($i < count($weekdays)) {
                    $weekdays_text.= ', ';
                  }
                }
              }
            }
          }
        }
        if ($class_info->shifts_name) {
          $nashifts = explode('@', $class_info->shifts_name);
          if ($nashifts) {
            $i = 0;
            foreach ($nashifts as $nashift) {
              $nashift_info = explode('|', $nashift);
              $i++;
              if ($nashift_info) {
                $sid = "session_$nashift_info[0]";
                $inf = $nashift_info[1];
                $inf = explode('-', $inf);
                $sta = $inf[0];
                $end = $inf[1];
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                $lab = substr($sta, 0, 5).'~'.substr($end, 0, 5);
                $sessions->$sid->shift = $lab;
                $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '.$lab : $lab;
                $nashifts_text.= $lab;
                $displayed[] = md5($lab);
                if ($i < count($nashifts)) {
                  $nashifts_text.= ', ';
                }
              }
            }
          }
        }
        if ($class_info->rooms_name) {
          $namrooms = explode('@', $class_info->rooms_name);
          if ($namrooms) {
            $i = 0;
            foreach ($namrooms as $namroom) {
              $namroom_info = explode('|', $namroom);
              $i++;
              if ($namroom_info && count($namroom_info)) {
                $sid = "session_$namroom_info[0]";
                $inf = $namroom_info[1];
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                if (!in_array(md5($inf), $displayed)) {
                  $sessions->$sid->room = "Room: $inf";
                  $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '."Room: $inf" : "Room: $inf";
                  $namrooms_text.= $inf;
                  $displayed[] = md5($inf);
                  if ($i < count($namrooms)) {
                    $namrooms_text.= ', ';
                  }
                }
              }
            }
          }
        }
        if ($class_info->teachers_name) {
          $teachers = explode('@', $class_info->teachers_name);
          if ($teachers) {
            $i = 0;
            foreach ($teachers as $teacher) {
              $teacher_info = explode('|', $teacher);
              $i++;
              if ($teacher_info) {
                $sid = "session_$teacher_info[0]";
                $inf = $teacher_info[1];
                $inf = explode('#', $inf);
                $ava = $inf[0];
                $ten = $inf[1];
                $lab = md5($ava) == md5('avatar.png') ? '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/avatar.png"/> '.$ten : '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/teachers/'.$ava.'"/> '.$ten;
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                if (!in_array(md5($lab), $displayed)) {
                  $sessions->$sid->teacher_avatar = $ava;
                  $sessions->$sid->teacher_name = $ten;
                  $sessions->$sid->teacher_label = $lab;
                  $teachers_text.= $lab;
                  $displayed[] = md5($lab);
                  if ($i < count($teachers)) {
                    $teachers_text.= ', ';
                  }
                }
              }
            }
          }
        }

        $time_place = '';
        if ($sessions) {
          $i = 0;
          foreach ($sessions as $s => $o) {
            if (isset($o->information) && strlen($o->information) > 12) {
              $i++;
              $time_place.= $o->information;
              if ($i < count((array)$sessions)) {
                $time_place.= '<br/>';
              }
            }
          }
        }

        $students_list_new = [];
        if ($students_list) {
            $branchId = 0;
            if (!empty($students_list[0]))
                $branchId = $students_list[0]->branch_id;

            $holiday =  u::getPublicHolidayAll($branchId);
           foreach ($students_list as $student) {
               $student->is_extend = self::checkExtendClass($class_info->id, $student->cms_id, $class_info->is_trial, $student->branch_id, $student->count_recharge);
              //  $today = date('Y-m-d');
              //  $endDate = $student->last_date;
              //  $startDate = $student->start_date;
              //  $inDate = null;
              // //  if ($endDate >= $today)
              //      $inDate = $today;
              // //  else if ($endDate < $today){
              // //      $inDate = $endDate;
              // //  }
              //  $days =  u::getDaysBetweenTwoDate($startDate, $inDate, $student->class_day); # 2x Friday
              //  $totalSessions = 0;
              //  $inDateList = ['start' =>$startDate,'end' =>$endDate];
              //  //$sessionReserves = u::getReserveSession($inDate,$student->student_id);
              //  $reserveDate = u::getReserveSessionDate($inDateList,$student->student_id);
              //  $dayOff = 0;
              //  if ($reserveDate){
              //      if ($reserveDate[1] >= $inDate){
              //          $dayOff = sizeof(u::getDaysBetweenTwoDate($reserveDate[0], $inDate, $student->class_day));
              //      }
              //      else{
              //          $dayOff =  u::getReserveSession($inDateList,$student->student_id);
              //      }
              //  }

              //  foreach($days as $day){
              //      $totalSessions += u::ckPublicHolidayCheck($day, $holiday);
              //  }

              //  $totalSessions = ($totalSessions - $dayOff);
              $tmp_contract = (object)array(
                'branch_id'=>$student->branch_id,
                'product_id'=>$student->product_id,
                'class_id'=>$student->class_id,
                'contract_id'=>$student->contract_id,
                'enrolment_start_date'=>$student->enrolment_start_date
              );
                $tmp_report = new ReportsController();
               $student->calculate_sessions =  $tmp_report->getDoneSessions($tmp_contract);
               $students_list_new[] = $student;
                 /*$information = u::getRealSessions($student->available_sessions, $classdays, $holidays);
                 $student->available_sessions = $information && isset($information->total) ? $information->total : 0;
                 $student->last_date = $information && isset($information->end_date) ? $information->end_date : '';
                 */
           }
         }
        $class_info->total_students = sizeof($students_list_new);// $total_students;
        $class_info->sessions = $sessions;
        $class_info->class_time = "$class_info->class_start_date ~ $class_info->class_end_date";
        $class_info->teachers_name = $teachers_text;
        $class_info->shifts_name = $nashifts_text;
        $class_info->rooms_name = $namrooms_text;
        $class_info->weekdays = $weekdays_text;
        $class_info->time_and_place = $time_place;
        $class_info->arr_weekdays = explode(',', $class_info->arr_weekdays);
        $data->class = $class_info;
        $data->students = $students_list_new;
        $code = APICode::SUCCESS;
      }
      return $response->formatResponse($code, $data);
    }

    private function checkExtendClass($classId = 0, $cmsId= "",$isTrial= 0, $branchId = 0, $countRecharge = 0){
        $countRecharge = $countRecharge +1;
        $cmsId = "CMS".$cmsId;
        $where = "(c.type > 0 AND c.status IN (1,2,3,4,5)) ";
        if ($isTrial != 0){
            $where = "(c.type = 0 AND c.status IN (1,3,5)) ";
        }
        $sql = "SELECT c.id AS id FROM contracts AS c
                LEFT JOIN students AS s ON c.student_id = s.id
                LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
                LEFT JOIN school_grades AS g ON s.school_grade = g.id
                LEFT JOIN users AS u0 ON c.cm_id = u0.id
                LEFT JOIN users AS u1 ON c.ec_id = u1.id
                LEFT JOIN reserves AS r ON c.student_id = r.student_id
                LEFT JOIN pendings AS p ON c.student_id = p.student_id
                LEFT JOIN classes AS cl ON cl.program_id = c.program_id
                WHERE c.branch_id = $branchId
                AND c.`status` < 6
                AND $where
                AND (c.product_id = (SELECT se.product_id FROM semesters se RIGHT JOIN classes cls ON cls.sem_id = se.sem_id WHERE cls.id = $classId))
                AND (cl.id = $classId OR cl.id IS NULL)
                AND ((c.real_sessions > 0 AND c.type IN (1,2,3,4,5,6,7)) OR (c.type IN (0,8)) OR c.type = 10)
                AND ((c.total_charged > 0 AND c.type IN (1,2,3,4,5,6,7)) OR (c.type IN (0,8)) OR c.type = 10)
                AND COALESCE(r.id, (r.start_date > CURDATE() OR r.end_date < CURDATE()), TRUE)
                AND c.`debt_amount` = 0  AND
                ( s.crm_id LIKE '$cmsId%'
                OR s.cms_id LIKE '$cmsId%'
                OR s.name LIKE '%$cmsId%'
                OR s.email LIKE '%$cmsId%'
                OR s.phone LIKE '$cmsId%'
                OR s.accounting_id = '$cmsId'
                OR s.gender LIKE '$cmsId')  AND c.status != 7 and c.count_recharge = {$countRecharge} LIMIT 1";
        $data = u::query($sql);

        if ($data)
            return $data[0]->id;
        else
            return 0;
    }

    public function loadClassDetail(Request $request, $class_id) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $class_id = (int)$class_id;
        $data = (Object)[];
        $class_info = u::first("SELECT
          c.is_trial,
          c.id,
          IF(c.level_id IS NULL,'',c.level_id) AS level_id,
          c.id AS class_id,
          c.product_id,
          c.program_id,
          c.cls_name AS class_name,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          u.status AS cm_status,
          CONCAT(u.full_name, ' - ', u.username) AS cm_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', COALESCE(t.avatar, 'avatar.png'), '#', t.ins_name) SEPARATOR '@ ') FROM teachers AS t
            LEFT JOIN `sessions` AS s ON s.teacher_id = t.user_id WHERE s.class_id = $class_id) AS teachers_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', r.room_name) SEPARATOR '@ ') FROM rooms AS r
            LEFT JOIN `sessions` AS s ON s.room_id = r.id WHERE s.class_id = $class_id) AS rooms_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', s.class_day) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN classes AS c ON s.class_id = c.id WHERE s.class_id = $class_id) AS weekdays,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', f.start_time, '-', f.end_time) SEPARATOR ', ') FROM `sessions` AS s
            LEFT JOIN shifts AS f ON s.shift_id = f.id WHERE s.class_id = $class_id) AS shifts_name,
          (SELECT t.id FROM `sessions` AS s LEFT JOIN `teachers` AS t ON s.teacher_id = t.user_id LEFT JOIN users u ON u.id = t.user_id WHERE s.class_id = $class_id LIMIT 1) AS teacher_id,
          (SELECT s.room_id FROM `sessions` AS s WHERE s.class_id = $class_id LIMIT 1) AS room_id,
          (SELECT s.shift_id FROM `sessions` AS s WHERE s.class_id = $class_id LIMIT 1) AS shift_id,
          (SELECT GROUP_CONCAT(s.class_day SEPARATOR ',') FROM `sessions` AS s WHERE s.class_id = $class_id) AS arr_weekdays,
           (SELECT count(cjrn_id) FROM schedules WHERE class_id=$class_id) AS num_session,
           (SELECT `cjrn_classdate` FROM schedules WHERE class_id=$class_id AND `cjrn_classdate` >= NOW() ORDER BY cjrn_classdate ASC LIMIT 1) AS next_classdate
        FROM classes AS c
          LEFT JOIN users AS u ON c.cm_id = u.id
        WHERE c.id = $class_id");
        $classdays = u::getClassDays($class_id);
        $holidays = u::getPublicHolidays($class_id, 0, $class_info->product_id);
        $students_list = u::query("SELECT
            c.id AS contract_id,
            c.student_id,
            c.id AS contract_id,
            s.name AS student_name,
            s.cms_id AS cms_id,
            t.name AS tuition_fee_name,
            t.receivable AS tuition_fee_price,
            t.`session` AS tuition_fee_sessions,
            cl.cls_name AS class,
            c.enrolment_start_date AS start_date,
            c.enrolment_end_date AS end_date,
            c.total_sessions AS total_sessions,
            c.bill_info,
            COALESCE(p.method, 0) AS payment_method,
            c.type AS customer_type,
            COALESCE(c.debt_amount, c.must_charge - c.total_charged) AS debt_amount,
            c.total_charged AS charged_total,
            IF(c.status = 6 AND c.enrolment_last_date <= CURDATE(), 1, 0) as withdraw,
            c.real_sessions AS available_sessions,
            c.enrolment_expired_date,
            c.enrolment_last_date last_date,
            c.enrolment_type enrolment_status, 
            c.status,
            CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
            CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
            (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE hrm_id = u1.superior_id LIMIT 1) AS ec_leader_name
          FROM contracts AS c
            LEFT JOIN students AS s ON c.student_id = s.id
            LEFT JOIN payment AS p ON c.payment_id = p.id
            LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
            LEFT JOIN classes AS cl ON c.class_id = cl.id
            LEFT JOIN users AS u1 ON c.ec_id = u1.id
            LEFT JOIN users AS u2 ON c.cm_id = u2.id
          WHERE c.class_id = $class_id 
            AND c.status IN (6, 7, 8)
  
            ORDER BY (CASE WHEN c.status = 6 AND c.enrolment_last_date>now() THEN 1 WHEN c.status = 6 AND c.enrolment_last_date<now() THEN 2 ELSE 3 END) ASC");
        $count_students = u::first("SELECT COUNT(*) AS total FROM (SELECT c.id AS total FROM contracts c
          LEFT JOIN students AS s ON c.student_id = s.id
        WHERE c.class_id = $class_id
          AND c.status IN (6, 7, 8)) AS a");
        $total_students = $count_students->total;
        $teachers = [];
        $weekdays = [];
        $namrooms = [];
        $nashifts = [];
        $sessions = (Object)[];
        $weekdays_text = '';
        $namrooms_text = '';
        $nashifts_text = '';
        $teachers_text = '';
        $displayed = [];
        if ($class_info->weekdays) {
          $weekdays = explode('@', $class_info->weekdays);
          if ($weekdays) {
            $i = 0;
            foreach ($weekdays as $weekday) {
              $weekday_info = explode('|', $weekday);
              $i++;
              if ($weekday_info) {
                $sid = "session_$weekday_info[0]";
                $inf = $weekday_info[1];
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                $display = apax_ada_get_weekday($inf);
                if (!in_array(md5($display), $displayed)) {
                  $sessions->$sid->weekday = apax_ada_get_weekday($inf);
                  $sessions->$sid->information = apax_ada_get_weekday($inf);
                  $weekdays_text.= $display;
                  $displayed[] = md5($display);
                  if ($i < count($weekdays)) {
                    $weekdays_text.= ', ';
                  }
                }
              }
            }
          }
        }
        if ($class_info->shifts_name) {
          $nashifts = explode('@', $class_info->shifts_name);
          if ($nashifts) {
            $i = 0;
            foreach ($nashifts as $nashift) {
              $nashift_info = explode('|', $nashift);
              $i++;
              if ($nashift_info) {
                $sid = "session_$nashift_info[0]";
                $inf = $nashift_info[1];
                $inf = explode('-', $inf);
                $sta = $inf[0];
                $end = $inf[1];
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                $lab = substr($sta, 0, 5).'~'.substr($end, 0, 5);
                $sessions->$sid->shift = $lab;
                $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '.$lab : $lab;
                $nashifts_text.= $lab;
                $displayed[] = md5($lab);
                if ($i < count($nashifts)) {
                  $nashifts_text.= ', ';
                }
              }
            }
          }
        }
        if ($class_info->rooms_name) {
          $namrooms = explode('@', $class_info->rooms_name);
          if ($namrooms) {
            $i = 0;
            foreach ($namrooms as $namroom) {
              $namroom_info = explode('|', $namroom);
              $i++;
              if ($namroom_info && count($namroom_info)) {
                $sid = "session_$namroom_info[0]";
                $inf = $namroom_info[1];
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                if (!in_array(md5($inf), $displayed)) {
                  $sessions->$sid->room = "Room: $inf";
                  $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '."Room: $inf" : "Room: $inf";
                  $namrooms_text.= $inf;
                  $displayed[] = md5($inf);
                  if ($i < count($namrooms)) {
                    $namrooms_text.= ', ';
                  }
                }
              }
            }
          }
        }
        if ($class_info->teachers_name) {
          $teachers = explode('@', $class_info->teachers_name);
          if ($teachers) {
            $i = 0;
            foreach ($teachers as $teacher) {
              $teacher_info = explode('|', $teacher);
              $i++;
              if ($teacher_info) {
                $sid = "session_$teacher_info[0]";
                $inf = $teacher_info[1];
                $inf = explode('#', $inf);
                $ava = $inf[0];
                $ten = $inf[1];
                $lab = md5($ava) == md5('avatar.png') ? '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/avatar.png"/> '.$ten : '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/teachers/'.$ava.'"/> '.$ten;
                $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
                if (!in_array(md5($lab), $displayed)) {
                  $sessions->$sid->teacher_avatar = $ava;
                  $sessions->$sid->teacher_name = $ten;
                  $sessions->$sid->teacher_label = $lab;
                  $teachers_text.= $lab;
                  $displayed[] = md5($lab);
                  if ($i < count($teachers)) {
                    $teachers_text.= ', ';
                  }
                }
              }
            }
          }
        }
        $time_place = '';
        if ($sessions) {
          $i = 0;
          foreach ($sessions as $s => $o) {
            if (isset($o->information) && strlen($o->information) > 12) {
              $i++;
              $time_place.= $o->information;
              if ($i < count((array)$sessions)) {
                $time_place.= '<br/>';
              }
            }
          }
        }
        // if (count($students_list)) {
        //   foreach ($students_list as $student) {
        //     $information = u::getRealSessions($student->available_sessions, $classdays, $holidays);
        //     $student->available_sessions = $information && isset($information->total) ? $information->total : 0;
        //     $student->last_date = $information && isset($information->end_date) ? $information->end_date : '';
        //   }
        // }
        $class_info->total_students = $total_students;
        $class_info->sessions = $sessions;
        $class_info->class_time = "$class_info->class_start_date ~ $class_info->class_end_date";
        $class_info->teachers_name = $teachers_text;
        $class_info->shifts_name = $nashifts_text;
        $class_info->rooms_name = $namrooms_text;
        $class_info->weekdays = $weekdays_text;
        $class_info->time_and_place = $time_place;
        $class_info->arr_weekdays = explode(',', $class_info->arr_weekdays);
        $data->class = $class_info;

        $students_list_new = [];
        if (!empty($request->status) && $request->status == 6){
            foreach ($students_list as $item){
                if ($item->status == 6 && $item->last_date >= date('Y-m-d')){
                    $students_list_new[] = $item;
                }
            }
            $data->students = $students_list_new;
        }else{
            $data->students = $students_list;
        }

        $code = APICode::SUCCESS;
      }
      return $response->formatResponse($code, $data);
    }

    public function studentExtend(Request $request){
        $class_id = $request->class_id;
        $product_id = $request->product_id;
        $program_id = $request->program_id;
        $classdays = [$request->class_day];
        $start_date = date('Y-m-d',strtotime($request->last_date)+7*24*60*60);
        $contract = u::first("SELECT * FROM `contracts` WHERE id = {$request->is_extend}");
        $left_sesssions = 1;
        if ($contract){
            //withdraw current contract
            $current_contract_id = $request->contract_id;
            $curent_last_date = $request->last_date;
            if ($curent_last_date >= date('Y-m-d H:i:s')){
                $withdraw_sql = "UPDATE `contracts` SET  status = 7, enrolment_last_date = NOW(),enrolment_withdraw_date = NOW(),updated_at =NOW() WHERE id = $current_contract_id ";
            }
            else{
                $withdraw_sql = "UPDATE `contracts` SET  status = 7, enrolment_withdraw_date = NOW(),updated_at =NOW() WHERE id = $current_contract_id ";
            }

            $holidays = u::getPublicHolidays($class_id, 0, $product_id);
            $information = u::calEndDate($contract->real_sessions, $classdays, $holidays, $start_date);
            $class_data = u::first("SELECT cl.*, t.product_id prd_id, s.teacher_id, GROUP_CONCAT(DISTINCT(s.class_day)) enrolment_schedule FROM classes cl LEFT JOIN sessions s ON s.class_id = cl.id LEFT JOIN term_program_product t ON t.program_id = cl.program_id WHERE cl.id = $class_id");
            $session = $request->users_data;
            $user_id = $session->id;
            $date = date('Y-m-d H:i:s');
            $hash_key = md5($class_id.$contract->id.$contract->student_id.$class_data->cls_startdate.$class_data->enrolment_schedule.$class_data->cls_enddate.$left_sesssions.$contract->real_sessions);
            $start_cjrn_id = 0;
            $cm_id = $class_data->cm_id;
            $csLeaderOfBranch = u::first("SELECT user_id FROM term_user_branch WHERE branch_id = $class_data->branch_id AND role_id = 56");
            $om_id = $csLeaderOfBranch ? $csLeaderOfBranch->user_id : null;
            $last_date = isset($information->end_date) ? $information->end_date : null;
            $update = "UPDATE `contracts` SET ";
            $set = "
                `class_id`   = $class_id,
                `product_id`   = {$product_id},
                `program_id`   = {$program_id},
                `updated_at`   = '$date',
                `status`   = 6,
                `cm_id`   = $cm_id,
                `om_id`   = $om_id,
                `start_cjrn_id`   = $start_cjrn_id,
                `enrolment_type`   = 1,
                `enrolment_schedule`   = $class_data->enrolment_schedule,
                `enrolment_updated_at`   = '$date',
                `enrolment_updator_id`   = $user_id,
                `enrolment_start_date`   = '$start_date',
                `enrolment_end_date`   = '$class_data->cls_enddate',
                `enrolment_last_date`   = '$last_date',
                `enrolment_left_sessions`   = 1,
                `hash_key`   = '$hash_key' WHERE id = {$contract->id}
            ";
            if ($last_date){
                u::query($withdraw_sql);
                u::query($update.$set);
                $this->createCyberEnrolment($contract->id, $user_id);
            }
            $response = new Response();
            return $response->formatResponse(200, $class_id);
        }

    }
}
