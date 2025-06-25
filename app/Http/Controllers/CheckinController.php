<?php

namespace App\Http\Controllers;
use App\Models\CyberAPI;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Checkin;
use App\Providers\UtilityServiceProvider as u;
use App\Models\Response;
use App\Models\APICode;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as x;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Exception;
use App\Models\ProcessExcel;

class CheckinController extends Controller
{
    public function list(Request $request,$pagination,$search,$sort,$act= null)
    {
        $code = APICode::PERMISSION_DENIED;
        if ($session = $request->users_data) {
            $obj_student = new Checkin();
            $list = $obj_student->getStudentInfo($request);
            $pagination = json_decode($pagination);
            $data = [];
            $code = APICode::SUCCESS;
            if ($list && isset($list->data)) {
                $data['code'] = $code;
                $data['message'] = 'Success!';
                $data['data']['list'] = $list->data;
                $data['data']['act'] = $request->act;
                $data['data']['sort'] = json_decode($sort);
                $data['data']['search'] = json_decode($search);
                $data['data']['duration'] = $pagination->limit * 10;
                $data['data']['pagination'] = apax_get_pagination($pagination, $list->total);
            }
        }

        return response()->json($data, $code);
    }

    public function add1(Request $request)
    {
        var_dump($request->all());
        exit;
        $model = new Checkin();
        $creatorId = $request->users_data->id;
        $model->create($request->all(),$creatorId);
        return response()->json(['data' =>['success' =>$request->name]]);
    }

    public function sources(Request $request)
    {
        $sql = "SELECT * FROM `sources` c 
                WHERE c.status = 1";
        $data = u::query($sql);
        return response()->json(['data' =>$data]);
    }
    public function sourcesDetail(Request $request)
    {
        $sql = "SELECT * FROM `source_detail` c 
                WHERE c.status = 1";
        $data = u::query($sql);
        return response()->json(['data' =>$data]);
    }

    public function getTrialClass(Request $request, $branchId = 0)
    {

        $sql = "SELECT CONCAT(p.`name`,' : ',c.`cls_name`) AS name,c.id FROM `classes` c 
                JOIN `products` p ON p.id = c.`product_id`
                WHERE c.is_trial = 1 AND c.brch_id = $branchId AND c.cls_startdate <= NOW() AND c.cls_enddate >= NOW()";
        $data = u::query($sql);
        return response()->json(['data' =>$data]);
    }

    public function getCheckinDetail(Request $request, $id = 0)
    {
        $sql = "SELECT *,
                (select ec_id from `term_student_user` where student_id = $id) ec_id,
                (select cm_id from `term_student_user` where student_id = $id) cm_id,
                (select full_name from users where id = ec_id) as ec_name,
                (select name from branches where id = students.branch_id) as branch_name,
                (select name from `shifts` where id = students.shift_id) as shift_name,
                (select concat(full_name,' - ',hrm_id) from users where id = students.creator_id) as creator_name,
                (SELECT t.role_id FROM `term_user_branch` t WHERE t.`user_id` = students.creator_id ORDER BY id DESC LIMIT 1) as role_id,
                (select s1.crm_id from students s1 where s1.id = students.sibling_id) as sibling_crm_id,
                (select s1.name from students s1 where s1.id = students.sibling_id) as sibling_name,
                (select full_name from users where id = cm_id) as cm_name,
                (select `status` from transfer_checkin where student_id = $id ORDER BY id DESC LIMIT 1 ) as status_transfer,
                (SELECT `name` FROM `provinces` WHERE id = province_id) as province_name,
                (SELECT `name` FROM `districts` WHERE id = district_id) as district_name
                FROM  `students` WHERE id = $id";

        $data = u::first($sql);
        if($data->source == 27 && $data->checked ==0){
            if($request->users_data->role_id != '999999999'&& $request->users_data->role_id != '80' && $request->users_data->role_id != '81'){
                $data->gud_mobile1 = str_replace(substr($data->gud_mobile1,4,3),'***',$data->gud_mobile1);
            }
        }
        $ec = "SELECT t.`user_id` as ec_id,u.`full_name` as ec_name FROM `term_user_branch` t 
                    JOIN `users` u ON u.id = t.`user_id`
                    WHERE t.`branch_id` = (SELECT s.branch_id FROM `students` s WHERE s.id = $id) AND (u.status=1 OR u.id =$data->ec_id) AND t.`role_id` IN (68,69,676767)";
        $dataEc = u::query($ec);
        $cs = "SELECT t.`user_id` as cm_id,u.`full_name` as cm_name FROM `term_user_branch` t 
                    JOIN `users` u ON u.id = t.`user_id`
                    WHERE t.`branch_id` = (SELECT s.branch_id FROM `students` s WHERE s.id = $id) AND (u.status=1 OR u.id =$data->cm_id) AND t.`role_id` IN (55,56)";
        $dataCs = u::query($cs);
        return response()->json(['data' =>['student' =>$data,'ec' =>$dataEc,'cs' =>$dataCs]]);
    }

    public function updateStudent(Request $request, $id = 0){
        $model = new Checkin();
        $creatorId = $request->users_data->id;
        $model->updateStudent($request->all(),$creatorId);
        return response()->json(['data' =>['success' =>$request->name]]);
    }

    public function schoolGrades(Request $request, $id = 0){
        $data = u::query("SELECT id, `name` FROM `school_grades` WHERE STATUS = 1");
        return response()->json(['data' =>$data]);
    }

    public function approver(Request $request, $id = 0){
        $creatorId = $request->users_data->id;
        $status = $request->status == 1 ? 1 :2;
        $update = 0;
        $student = [];
        $role_id = $request->users_data->role_id;
        if ($role_id == 999999999){
            $adStatus = 2;
            if ($status == 2)
                $adStatus = 3;
                
            u::query("
                UPDATE `transfer_checkin` u SET 
                 u.status = $adStatus,
                 u.`from_approver_id` = $creatorId, 
                 u.`from_approve_at` = NOW(),
                 u.`to_approve_at` = NOW(),
                 u.`to_approver_id` = $creatorId
                 WHERE u.`student_id` =$id");

            if ($adStatus == 2){
                $student = $this->processApprove($adStatus,$creatorId,$id);
                $update = 1;
            }
            $status = $adStatus;
        }
        else{
            if ($request->f == 'from'){
                $fromStatus = 1;
                if ($status == 2)
                    $fromStatus = 3;
                u::query("UPDATE `transfer_checkin` u SET u.status = $fromStatus, u.`from_approver_id` = $creatorId, u.`from_approve_at` = NOW() WHERE u.`student_id` =$id");
                $update = 1;
                $status = $fromStatus;
            }
            if ($request->f == 'to'){
                $toStatus = 2;
                if ($status == 2)
                    $toStatus = 4;
                $update = 1;
                $student = $this->processApprove($toStatus,$creatorId,$id);
                $status = $toStatus;
            }
        }

        $to = '';
        if (isset($request->f)){
            $to = $request->f == 'to' ? 'đến' :'đi';
        }
        if ($role_id == 999999999)
            $to = '';
        $msg = $update ? "Bạn đã hoàn tất duyệt chuyển trung tâm $to" : "Từ chối duyệt chuyển trung tâm thành công";
        return response()->json(
            ['data' =>['success' =>$msg,
                'status' =>$status,
                'item' =>$student,
                'from_approver_id'=>$creatorId,
                'to_approver_id'=>$creatorId
                ]
            ]);
    }

    private function processApprove($status,$creatorId,$id){
        $student = [];
        u::query("UPDATE `transfer_checkin` u SET u.status = $status, u.`to_approver_id` = $creatorId, u.`to_approve_at` = NOW() WHERE u.`student_id` =$id");
        if ($status == 2){
            $student= u::first("SELECT 
                        student_id as id,to_ec_id as ec_id, to_cm_id as cm_id, to_branch_id as branch_id,
                        (select full_name from users where id = ec_id) as tmp_ec_name,
                        (select full_name from users where id = cm_id) as tmp_cm_name,
                        (select name from branches where id = branch_id) as tmp_branch_name
                        FROm `transfer_checkin` where student_id = $id AND status=2 ORDER BY id DESC");
            if ($student){
                $model = new Checkin();
                $model->updateStudent((array)$student,$creatorId, true);
            }
        }
        return $student;
    }

    public function shiftList(Request $request, $branch_id = 0){
        $data = u::query("SELECT id, `name` FROM `shifts` WHERE STATUS = 1 AND zone_id  IN (SELECT zone_id FROM branches WHERE id = $branch_id)");
        return response()->json(['data' =>$data]);
    }
    public function add(Request $request){
        $unix_check_dublicate = md5($request->name . $request->gud_name1 . $request->gud_mobile1);
            $sibling_id = null;
            if (isset($request->sibling_id) && $request->sibling_id !='') {
                $sib_id = (int)str_replace('CMS', '', $request->sibling_id);
                $sib_cd = $sib_id - 20000000;
                $sib = u::first("SELECT s.id FROM students s WHERE id = $sib_cd");
                $sibling_id = $sib && isset($sib->id) ? $sib->id : null;
            }
            {
            $uid = $request->users_data->id;
            $student = new Student();
            $used_id = "CMS" . time();
            $student->cms_id = 0;
            $student->crm_id = "CMS" . time();
            $student->name = $request->firstname." ".($request->midname ? $request->midname." ":"").$request->lastname;
            $student->firstname = $request->firstname;
            $student->lastname = $request->lastname;
            $student->midname = $request->midname;
            $student->gender = $request->gender;
            $student->facebook = $request->facebook;
            $student->nick = $request->nick;
            $student->note = $request->note;
            $student->type = $request->type;
            $student->phone = $request->phone;
            $student->sibling_id = $sibling_id;
            $student->email = $request->email;
            $student->date_of_birth = $request->date_of_birth;
            $student->gud_mobile1 = $request->gud_mobile1;
            $student->gud_name1 = $request->gud_firstname1." ".($request->gud_midname1 ? $request->gud_midname1." ":"").$request->gud_lastname1;
            $student->gud_firstname1 = $request->gud_firstname1;
            $student->gud_lastname1 = $request->gud_lastname1;
            $student->gud_midname1 = $request->gud_midname1;
            $student->gud_email1 = $request->gud_email1;
            $student->gud_card1 = $request->gud_card1;
            $student->gud_birth_day1 = $request->gud_birth_day1;
            $student->gud_mobile2 = $request->gud_mobile2;
            $student->gud_name2 = $request->gud_firstname2." ".($request->gud_midname2 ? $request->gud_midname2." ":"").$request->gud_lastname2;
            $student->gud_firstname2 = $request->gud_firstname2;
            $student->gud_lastname2 = $request->gud_lastname2;
            $student->gud_midname2 = $request->gud_midname2;
            $student->gud_email2 = $request->gud_email2;
            $student->gud_card2 = $request->gud_card2;
            $student->gud_birth_day2 = $request->gud_birth_day2;
            $student->gud_gender1 = $request->gud_gender1;
            $student->gud_gender2 = $request->gud_gender2;
            $student->gud_job1 = $request->gud_job1;
            $student->gud_job2 = $request->gud_job2;
            $student->address = $request->address;
            $student->province_id = $request->province_id;
            $student->district_id = 1;//$request->district_id;
            $student->school = $request->school;
            $student->school_level = $request->school_level;
            $student->school_grade = $request->school_grade;
            $student->created_at = date('Y-m-d H:i:s');
            $student->creator_id = $uid;
            $student->updated_at = date('Y-m-d H:i:s');
            $student->editor_id = $uid;
            $student->hash_key = $unix_check_dublicate;
            $student->current_classes = $request->current_classes;
            $student->used_student_ids = $used_id;
            $student->branch_id = $request->branch_id;
            $student->meta_data = $request->meta_data;
            $student->tracking = $request->tracking;
            $student->checkin_at = $request->checkin_at;
            $student->status = 0;
            $student->source = $request->source;
            $student->shift_id = $request->shift_id;
            if ($request->ref_code)
                $student->ref_code = $request->ref_code['id'];
            $student->save();
            $lastInsertedId = $student->id;
            $cms_id = '2' . str_pad((string)$lastInsertedId, 7, '0', STR_PAD_LEFT);
            $crm_id = "CMS$cms_id";
            $cms_id = (int)$cms_id;
            u::query("UPDATE students SET cms_id = '$cms_id', crm_id = '$crm_id' WHERE id = $lastInsertedId");
            u::query("UPDATE `customer_care` c SET c.`crm_id` ='$crm_id' WHERE c.`std_temp_id` IN (SELECT s.id FROM `student_temp` s WHERE s.`gud_mobile1` = '{}')");
            $region_id = 0;
            $zone_id = 0;
            $ec_id = (int)$request->ec_id;
            $cs_id = (int)$request->cm_id;

            $ecLeader = u::first("SELECT u2.id as ec_leader_id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $ec_id");
            $csLeader = u::first("SELECT u2.id as cs_leader_id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $cs_id");

            $ecLeaderId = ($ecLeader && $ecLeader->ec_leader_id > 0) ? $ecLeader->ec_leader_id : $ec_id;
            $csLeaderId = ($csLeader && $csLeader->cs_leader_id > 0) ? $csLeader->cs_leader_id : $cs_id;


            DB::table('term_student_user')->insert(
                [
                    'student_id' => $lastInsertedId,
                    'ec_id' => $ec_id,
                    'cm_id' => $cs_id,
                    'status' => 0,
                    'ec_leader_id' => $ecLeaderId,
                    'om_id' => $csLeaderId,
                    'branch_id' => $request->branch_id,
                    'region_id' => $region_id,
                    'zone_id' => $zone_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
            $content = "Checkin HS: ".$student->name .", CRM_ID: ". $crm_id;
            Checkin::addLogStudentUpdate($lastInsertedId, $content, $cs_id,$uid,$request->branch_id);
            if(in_array($request->users_data->role_id,[80,81,7676767]) && date('Y-m-d',strtotime($request->checkin_at))==date('Y-m-d') && (date('H')>=14 || date('w')==6 ||date('w')==0)){
                JobsController::sendMailSalehub($request->branch_id, $student->id);
            }elseif(date('H')>=14 && (date('w')==5 || date('w')==6) && in_array($request->users_data->role_id,[80,81,7676767]) && date('Y-m-d',strtotime($request->checkin_at))==date('Y-m-d',time()+24*3600)){
                JobsController::sendMailSalehub($request->branch_id, $student->id);
            }
            
                return response()->json(['data' =>['success' =>$request->name]]);
        }
    }

    public function convertStudent(Request $request, $id= 0){
        $uid = $request->users_data->id;
        $termStudent = u::first("SELECT student_id,$uid as cm_id, (select full_name from users where id = $uid) as cm_name,branch_id
            FROM `term_student_user` where student_id = $id");
        if ($termStudent){
            u::query("insert into `active_checkin`(student_id,cm_id,cm_name,branch_id,created_at) 
                values($id,{$termStudent->cm_id},'{$termStudent->cm_name}',{$termStudent->branch_id},now()) ");
            u::query("UPDATE `term_student_user` set status = 1 WHERE student_id = $id");
        }
        $student = Student::find($id);
        $student->status = 1;
        $student->save();
        return response()->json(['data' =>['success' =>"Bạn đã chuyển đổi học sinh: ".$student->name." thành công."]]);
    }
    public static function createContractTrial($student_id,$creator_id){
        $student =u::first("SELECT s.id,s.name,t.om_id,t.ec_id,t.cm_id,t.ec_leader_id,t.branch_id,t.ceo_branch_id,t.ceo_region_id,t.region_id,
                (SELECT name FROM branches WHERE id=t.branch_id) AS branch_name,
                (SELECT full_name FROM users WHERE id=t.ec_id) AS ec_name
            FROM students AS s LEFT JOIN term_student_user AS t ON t.student_id=s.id WHERE s.id=$student_id");
        $contract_code = apax_ada_gen_contract_code($student->name, $student->ec_name, $student->branch_name);
        $type = 0;
        $product_id=100;
        $start_date= date('Y-m-d');
        $bonus_sessions =1;
        $total_sessions = $bonus_sessions;
        $real_sessions = 0;
        $summary_sessions = $bonus_sessions;
        $count_recharge = -1;
        $status = 5;
        $created_at = date('Y-m-d H:i:s');

        $holi_day = u::getPublicHolidays(0, $student->branch_id, 1);
        $tmp_date = u::getRealSessions($bonus_sessions, [2], $holi_day, $start_date);
        $end_date = $tmp_date->end_date;
        $strMd5 = $student_id.$type.$start_date.$end_date.$product_id.'0'.'0'.$student->branch_id;
        $hash_key = md5($strMd5);
        $action = "Contracts_Create";
        $insert_query = "INSERT INTO contracts
        (`code`,
        `type`,
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
        `start_date`,
        `end_date`,
        `total_charged`,
        `must_charge`,
        `debt_amount`,
        `total_sessions`,
        `real_sessions`,
        `status`,
        `created_at`,
        `creator_id`,
        `count_recharge`,
        `bonus_sessions`,
        `summary_sessions`,
        `action`,
        `hash_key`)
        VALUES
        ('$contract_code',
        $type,
        $student_id,
        '$student->branch_id',
        '$student->ceo_branch_id',
        '$student->region_id',
        '$student->ceo_region_id',
        '$student->ec_id',
        '$student->ec_leader_id',
        '$student->cm_id',
        '$student->om_id',
        '$product_id',
        '$start_date',
        '$end_date',
        0,
        0,
        0,
        $total_sessions,
        $real_sessions,
        $status,
        '$created_at',
        $creator_id,
        $count_recharge,
        $bonus_sessions,
        $summary_sessions,
        '$action',
        '$hash_key')";
        $r =  u::query($insert_query);
        
        $latest_contract = u::first("SELECT id, created_at, updated_at, hash_key FROM contracts WHERE hash_key = '$hash_key' ORDER BY id DESC LIMIT 1");
        $previous_hashkey = md5("$latest_contract->id$latest_contract->created_at$latest_contract->updated_at$latest_contract->hash_key");
        $current_hashkey = $previous_hashkey;
        if ($latest_contract && isset($latest_contract->id)) {
            $insert_log_contract_history_query = "INSERT INTO log_contracts_history
            (`contract_id`,
            `code`,
            `type`,
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
            `start_date`,
            `end_date`,
            `total_sessions`,
            `real_sessions`,
            `status`,
            `created_at`,
            `creator_id`,
            `count_recharge`,
            `bonus_sessions`,
            `summary_sessions`,
            `action`,
            `hash_key`,
            `previous_hashkey`,
            `current_hashkey`)
            VALUES
            ('".(int)$latest_contract->id."',
            '$contract_code',
            $type,
            $student_id,
            '$student->branch_id',
            '$student->ceo_branch_id',
            '$student->region_id',
            '$student->ceo_region_id',
            '$student->ec_id',
            '$student->ec_leader_id',
            '$student->cm_id',
            '$student->om_id',
            $product_id,
            '$start_date',
            '$end_date',
            $total_sessions,
            $real_sessions,
            $status,
            '$created_at',
            $creator_id,
            $count_recharge,
            $bonus_sessions,
            $summary_sessions,
            '$action',
            '$hash_key',
            '$previous_hashkey',
            '$current_hashkey')";
            u::query($insert_log_contract_history_query);
        }
    }

    public function transferCheckin(Request $request){
        $creatorId = $request->users_data->id;
        $data['branch_id'] = $request->from;
        $data['new_branch_id'] = $request->to;
        $new_ec_id = $request->ec_id;
        $new_cs_id = $request->cm_id;
        $ecId = $request->from_ec_id;
        $cmId = $request->from_cm_id;
        $branch = u::first("select name from branches where id = $request->from");
        if ($branch)
            $branch_name = $branch->name;
        $checkin = new Checkin();
        $checkin->addTransferCheckin($request->std_id, $data,$creatorId,$new_ec_id,$new_cs_id, $ecId, $cmId);
        return response()->json(['data' =>['success' =>"Chuyển thành công! Học viên đang chờ Giám đốc: {$branch_name} duyệt chuyển đi."]]);
    }

    public function transferCheckinStatus(Request $request, $id = 0, $branch_id = 0){
        $data = u::first("SELECT t.`status`,(select name from branches where id = t.`from_branch_id`) as from_branch_name,
                                (select name from branches where id = t.`to_branch_id`) as to_branch_name
                                FROM `transfer_checkin` t WHERE (t.`from_branch_id` = $branch_id OR t.`to_branch_id` = $branch_id) AND t.`student_id` = $id");
        $success = "";
        $status = 99;
        $validate_info = 1;
        if ($data){
            $status = $data->status;
            if ($data->status == 0)
                $success = "Học viên đang chờ Giám đốc: <b>{$data->from_branch_name}</b> chuyển duyệt chuyển đi.";
            if ($data->status == 1)
                $success = "Học viên đang chờ Giám đốc:<b>{$data->to_branch_name}</b> duyệt chuyển đến.";
            if ($data->status == 2){
                $success = "Học viên đã được chuyển trung tâm từ: <b>{$data->from_branch_name}</b> sang: <b>{$data->to_branch_name}</b> ";
                $status = 99;
            }
            if ($data->status == 3){
                $success = "Trung tâm chuyển: <b>{$data->from_branch_name}</b> từ chối duyệt chuyển đi ";
                $status = 99;
            }
            if ($data->status == 4){
                $success = "Trung tâm nhận: <b>{$data->to_branch_name}</b> từ chối duyệt chuyển đến ";
                $status = 99;
            }
            if($status==99){
                $student_info = u::first("SELECT * FROM students WHERE id=$id");
                if($student_info && $student_info->gud_name1 && $student_info->gud_gender1 && $student_info->gud_birth_day1 
                    && $student_info->gud_email1 && $student_info->date_of_birth && $student_info->gud_job1
                    && $student_info->province_id && $student_info->address && $student_info->district_id){
                        $validate_info=1;
                    }else{
                        $validate_info=0;
                    }
            }
        }else{
            $student_info = u::first("SELECT * FROM students WHERE id=$id");
            if($student_info && $student_info->gud_name1 && $student_info->gud_gender1 && $student_info->gud_birth_day1 
                && $student_info->gud_email1 && $student_info->date_of_birth && $student_info->gud_job1
                && $student_info->province_id && $student_info->address && $student_info->district_id){
                    $validate_info=1;
                }else{
                    $validate_info=0;
                }
        }
        return response()->json(['data' =>['success' =>$success,'status' =>$status,'validate_info'=>$validate_info]]);
    }

    public function historyCare($crmId = 0){
        $status = [
            6 => "Có con trong độ tuổi và có quan tâm đến CMS",
            9 => "Có tín hiệu, Không nghe máy, dập máy",
            4 =>  "Đến CMS ( làm bài test, tìm hiểu chương trình, tham gia sự kiện )",
            5 =>  "Đồng ý lịch hẹn lên trải nghiệm",
            2 =>  "Gọi chăm sóc khách hàng",
            1 =>  "Nhận góp ý từ khách hàng",
            7 =>  "Nói chuyện được với contact",
            3 =>  "Nộp tiền",
             8=>  "Số điện thoại không tồn tại, nhầm máy"
        ];
        $method = [
            1=>'Gọi điện thoại',
            2=>'Gặp trực tiếp'];
        $scores = [
              0=> 0,
              1=> 1,
              2=> 1,
              3=> 5,
              4=> 4,
              5=> 3,
              6=> 2,
              7=> 1,
              8=> 0,
              9=> 0
        ];
        $dataNew = [];
        if (!empty($crmId)){
            $data = u::query("SELECT c.*, (select full_name from users where id = c.creator_id) as fn_creator,c.created_at as care_date, c.note as care_note FROM `customer_care` c WHERE c.`crm_id` = '$crmId' order by c.id desc");
            if ($data){
                foreach ($data as $dt){
                    $dt->fn_name  = $status[$dt->contact_quality_id];
                    $dt->fn_method  = $method[$dt->contact_method_id];
                    $dt->fn_score  = $scores[$dt->contact_quality_id];
                }
            }
            $dataNew = $data;
        }
        return response()->json(['data' =>['data'=>$dataNew, 'status' =>$status]]);
    }
    public function updateChecked(Request $request, $student_id= 0){
        u::query("UPDATE students SET checked=1,update_checked ='".date('Y-m-d H:i:s')."',updator_checked ='".$request->users_data->id."' WHERE id=$student_id");
        $student_info = u::first("SELECT t.branch_id,t.cm_id FROM students AS s LEFT JOIN term_student_user AS t ON t.student_id=s.id WHERE s.id=$student_id");
        DB::table('log_student_update')->insert(
            [
                'student_id' => $student_id,
                'updated_by' => $request->users_data->id,
                'cm_id' => $student_info->cm_id,
                'status' => 1,
                'branch_id' => $student_info->branch_id,
                'content' => "Checkin học sinh",
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
        self::createContractTrial($student_id,$request->users_data->id);
        return response()->json(['data' =>['success' =>"Bạn đã cập nhật checkin cho học sinh thành công."]]);
    }
    public function deleteChecked(Request $request, $student_id= 0){
        u::query("UPDATE students SET checked=2 WHERE id=$student_id");
        return response()->json(['data' =>['success' =>"Bạn đã xóa checkin cho học sinh thành công."]]);
    }
    public function getAllUserByRole(Request $request)
    {
        $cond = $request->roles ? " AND t.role_id IN ($request->roles)" : " AND t.role_id IN (80,81)";
        $sql = "SELECT DISTINCT u.id ,CONCAT(u.full_name,' - ',u.hrm_id) AS label FROM term_user_branch t
                LEFT JOIN users AS u ON u.id=t.user_id
                WHERE u.status = 1 AND t.status=1 $cond";
        $data = u::query($sql);
        return response()->json(['data' =>$data]);
    }
    public function listImport(Request $request) 
    {
        $products = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token ',
        ];
        $query_information = self::queryImport($request);
        $get_code_query = $query_information['base_query'];
        $logs = DB::select(DB::raw($get_code_query));

        $cpage = $query_information['page'];
        $limit = $query_information['limit'];
        $total = $query_information['total'];
        $lpage = $total <= $limit ? 1 : (int) round(ceil($total / $limit));
        $ppage = $cpage > 0 ? $cpage - 1 : 0;
        $npage = $cpage < $lpage ? $cpage + 1 : $lpage;
        $response['done'] = true;
        $response['pagination'] = [
            'spage' => 1,
            'ppage' => $ppage,
            'npage' => $npage,
            'lpage' => $lpage,
            'cpage' => $cpage,
            'limit' => $limit,
            'total' => $total,
        ];
        $response['logs'] = $logs;
        $response['message'] = 'successful';
        return response()->json($response);

    }
    private function queryImport($request, $page = 1, $limit = 20, $filter = null)
    {

        $selected_page = $request->page ? (int) $request->page : 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $where = '';
        if ($request->search != '_') {
            $key = explode(",", $request->search);
            $value = explode(",", $request->filter);

            for ($i = 0; $i < count($key); $i++) {
                $where .= "and o.$key[$i] like '%$value[$i]%' ";
            }
        }
        if ($where) {
            $where = ltrim($where, "and");
        }
        $where = "where 1 " . $where;
        $cond= $request->users_data->role_id !='999999999' ? " AND i.creator_id= ".$request->users_data->id:"";

        $query = "SELECT i.*, (SELECT `full_name` FROM users WHERE id=i.creator_id) as creator_name,
                            (SELECT count(id) FROM checkin_import_log WHERE checkin_import_id=i.id AND status=1) AS count_import_success,
                            (SELECT count(id) FROM checkin_import_log WHERE checkin_import_id=i.id ) AS count_import
                        from checkin_import as i $where $cond ORDER BY i.id DESC $limitation";
        $count_query = "SELECT COUNT(i.id) as total FROM checkin_import as i $where  $cond ";
        $total = DB::select(DB::raw($count_query));
        $total = $total[0]->total;
        return [
            'base_query' => $query,
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
        ];
    }
    public function checkCheckinImport(Request $request){
        $dataRequest = $request->all();
        //dd($dataRequest['branch_id']);
        $userData = $request->users_data;
        $code = APICode::SUCCESS;
        $data_mes = (object) [];
        $response = new Response();

        $attachedFile = $dataRequest['files'];
        if (!$attachedFile) {
            $code = APICode::PERMISSION_DENIED;
            return $response->formatResponse($code, $data_mes);
        }

        // SAVE FILES TO SERVER
        $explod = explode(',', $attachedFile);
        $decod = base64_decode($explod[1]);
        if (str_contains($explod[0], 'spreadsheetml')) {
            $extend = 'xlsx';
        } else {
            $code = APICode::PERMISSION_DENIED;

            return $response->formatResponse($code, $data_mes);
        }
        $fileAttached = md5($request->attached_file.str_random()).'.'.$extend;
        $p = FOLDER.DS.'doc\\other\\'.$fileAttached;
        file_put_contents($p, $decod);

        $reader = new x();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($p);
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        $dataXslx = $sheet->toArray();
        unset($dataXslx[0]);
        $created_at = date('Y-m-d H:i:s');
        $checkin_import_id = DB::table('checkin_import')->insertGetId([
            'created_at' => $created_at,
            'creator_id' => $request->users_data->id,
            'file_name'=>$request->file_name,
            'file_dir'=>'doc\\other\\'.$fileAttached,
        ]);
        $c=0;
        foreach ($dataXslx as $key => $value) 
        {
            if($key > 10 && $value[0]){
                $sql_insert = "INSERT INTO checkin_import_log (checkin_import_id,student_id,stt,ma_trung_tam,ma_ec,ho_ten_hoc_sinh,ngay_sinh_hoc_sinh,ho_ten_phu_huynh_1,ngay_sinh_phu_huynh_1,dien_thoai_phu_huynh_1,
                    email_phu_huynh_1,ho_ten_phu_huynh_2,ngay_sinh_phu_huynh_2,dien_thoai_phu_huynh_2,email_phu_huynh_2,dia_chi,nguon,ngay_check_in,anh_em_file,ma_anh_em_he_thong,`status`,created_at,creator_id,note) VALUES ";
               $c++;
                $tmp_data= (object)array(
                    'stt'=>$value[0],
                    'ma_trung_tam'=>$value[1],
                    'ma_ec'=>$value[2],
                    'ho_ten_hoc_sinh'=>$value[3],
                    'ngay_sinh_hoc_sinh'=>$value[4],
                    'ho_ten_phu_huynh_1'=>$value[5],
                    'ngay_sinh_phu_huynh_1'=>$value[6],
                    'dien_thoai_phu_huynh_1'=>$value[7],
                    'email_phu_huynh_1'=>$value[8],
                    'ho_ten_phu_huynh_2'=>$value[9],
                    'ngay_sinh_phu_huynh_2'=>$value[10],
                    'dien_thoai_phu_huynh_2'=>$value[11],
                    'email_phu_huynh_2'=>$value[12],
                    'dia_chi'=>$value[13],
                    'nguon'=>$value[14],
                    'ngay_check_in'=>$value[15],
                    'anh_em_file'=>$value[16],
                    'ma_anh_em_he_thong'=>$value[17],
                    'creator_id'=>$request->users_data->id
                );
                $create_checkin = self::createCheckin($tmp_data,$checkin_import_id);
                if($create_checkin->status){
                    $student_id=$create_checkin->student_id;
                    $sql_insert.="('$checkin_import_id','$student_id','$tmp_data->stt','$tmp_data->ma_trung_tam','$tmp_data->ma_ec','$tmp_data->ho_ten_hoc_sinh','$tmp_data->ngay_sinh_hoc_sinh','$tmp_data->ho_ten_phu_huynh_1','$tmp_data->ngay_sinh_phu_huynh_1','$tmp_data->dien_thoai_phu_huynh_1',
                        '$tmp_data->email_phu_huynh_1','$tmp_data->ho_ten_phu_huynh_2','$tmp_data->ngay_sinh_phu_huynh_2','$tmp_data->dien_thoai_phu_huynh_2','$tmp_data->email_phu_huynh_2','$tmp_data->dia_chi','$tmp_data->nguon','$tmp_data->ngay_check_in','$tmp_data->anh_em_file','$tmp_data->ma_anh_em_he_thong','$create_checkin->status','".date('Y-m-d H:i:s')."','$tmp_data->creator_id','$create_checkin->message'),";
                }else{
                    $sql_insert.="('$checkin_import_id','0','$tmp_data->stt','$tmp_data->ma_trung_tam','$tmp_data->ma_ec','$tmp_data->ho_ten_hoc_sinh','$tmp_data->ngay_sinh_hoc_sinh','$tmp_data->ho_ten_phu_huynh_1','$tmp_data->ngay_sinh_phu_huynh_1','$tmp_data->dien_thoai_phu_huynh_1',
                        '$tmp_data->email_phu_huynh_1','$tmp_data->ho_ten_phu_huynh_2','$tmp_data->ngay_sinh_phu_huynh_2','$tmp_data->dien_thoai_phu_huynh_2','$tmp_data->email_phu_huynh_2','$tmp_data->dia_chi','$tmp_data->nguon','$tmp_data->ngay_check_in','$tmp_data->anh_em_file','$tmp_data->ma_anh_em_he_thong','$create_checkin->status','".date('Y-m-d H:i:s')."','$tmp_data->creator_id','$create_checkin->message'),";
                }
                $sql_insert = rtrim($sql_insert,",");
                u::query($sql_insert);
            }
        }
        $data_mes->message = 'Import file thành công!';
        $data_mes->error = false;
        
        return $response->formatResponse($code, $data_mes);
       
    }
    private function createCheckin($data,$checkin_import_id){
        $message="";
        $branch_info =u::first("SELECT b.id, (SELECT user_id FROM term_user_branch WHERE branch_id = b.id AND status=1 AND role_id= 686868 ORDER BY id DESC LIMIT 1) AS ceo_id,
                (SELECT user_id FROM term_user_branch WHERE branch_id = b.id AND status=1 AND role_id= 56 ORDER BY id DESC LIMIT 1) AS om_id
            FROM branches AS b WHERE b.accounting_id='$data->ma_trung_tam'");
        if(!$data->ma_trung_tam ||!$branch_info){
            $message ="Mã trung tâm (*) không hợp lệ";
        }
        if(!$data->ho_ten_hoc_sinh){
            $message ="Họ tên học sinh (*) là bắt buộc";
        }
        if(!$data->ngay_sinh_hoc_sinh){
            $message ="Ngày sinh học sinh (*) là bắt buộc";
        }
        if(!$data->ho_ten_phu_huynh_1){
            $message ="Họ tên PH 1 (*) là bắt buộc";
        }
        if(!$data->dien_thoai_phu_huynh_1 || !u::validateMobileNumber($data->dien_thoai_phu_huynh_1)){
            $message ="Điện thoại PH 1 (*) không hợp lệ";
        }
        if(!$data->dia_chi){
            $message ="Địa chỉ (*) là bắt buộc";
        }
        $source_info = u::first("SELECT id FROM sources WHERE id='$data->nguon'");
        if(!$data->ho_ten_hoc_sinh || !$source_info){
            $message ="Mã nguồn (*) không hợp lệ";
        }
        if(!$data->ngay_check_in){
            $data->ngay_check_in = date('Y-m-d');
        }
        $sibling_id = null;
        if($data->anh_em_file=='x'){
            $sib_info = u::first("SELECT l.student_id FROM checkin_import_log AS l WHERE dien_thoai_phu_huynh_1 = '$data->dien_thoai_phu_huynh_1' AND l.checkin_import_id=$checkin_import_id AND l.status=1");
            $sibling_id = $sib_info ? $sib_info->student_id : null;
        }

        if ($data->ma_anh_em_he_thong) {
            $student_info = u::first("SELECT id FROM students WHERE (gud_mobile1 = '$data->dien_thoai_phu_huynh_1' OR gud_mobile2 = '$data->dien_thoai_phu_huynh_1') AND checked!=2  AND crm_id='$data->ma_anh_em_he_thong'");
            $sibling_id = $student_info ? $student_info->id : null;
        }
        if(!$sibling_id){
            $and = "";
            $mobile = trim($data->dien_thoai_phu_huynh_1);
            $mobile2 = trim($data->dien_thoai_phu_huynh_2);

            $dataStd = u::first("SELECT COUNT(s.id) existed FROM students s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.checked!=2 $and");
            if ($dataStd->existed >= 1){
                $dataStd->existed = 1;
                $detail = u::first("SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name FROM students s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.checked!=2 $and");
                $message = "Số điện thoại phụ huynh đã có ở: {$detail->branch_name}, hãy nhập số điện thoại khác.";
            }
            if ($mobile2){
                $dataStd = u::first("SELECT COUNT(s.id) existed FROM students s WHERE (s.`gud_mobile1` = '{$mobile2}' OR s.`gud_mobile2` = '{$mobile2}') AND s.checked!=2 $and");
                
                if ($dataStd->existed >= 1){
                    $dataStd->existed = 1;
                    $detail = u::first("SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name FROM students s WHERE (s.`gud_mobile1` = '{$mobile2}' OR s.`gud_mobile2` = '{$mobile2}') AND s.checked!=2 $and");
                    $message = "Số điện thoại phụ huynh 2 đã có ở: {$detail->branch_name}, hãy nhập số điện thoại khác.";
                }
            }
        }
        
        if($message){
            $data = (object)array(
                'status'=>0,
                'message'=>$message
            );
        }else{
            $student_name = u::explodeName(trim($data->ho_ten_hoc_sinh));
            $gud_name1 = u::explodeName(trim($data->ho_ten_phu_huynh_1));
            $gud_name2 = u::explodeName(trim($data->ho_ten_phu_huynh_2));
            
            $student = new Student();
            $used_id = "CMS" . time();
            $student->cms_id = 0;
            $student->crm_id = "CMS" . time();
            $student->name = trim($data->ho_ten_hoc_sinh);
            $student->firstname = $student_name->firstname;
            $student->lastname = $student_name->lastname;
            $student->midname = $student_name->midname;
            $student->phone = $data->dien_thoai_phu_huynh_1;
            $student->email = $data->email_phu_huynh_1;
            $student->date_of_birth = $data->ngay_sinh_hoc_sinh;
            $student->gud_mobile1 = $data->dien_thoai_phu_huynh_1;
            $student->gud_name1 = trim($data->ho_ten_phu_huynh_1);
            $student->gud_firstname1 = $gud_name1->firstname;
            $student->gud_lastname1 = $gud_name1->lastname;
            $student->gud_midname1 = $gud_name1->midname;
            $student->gud_email1 = $data->email_phu_huynh_1;
            $student->gud_birth_day1 = $data->ngay_sinh_phu_huynh_1;
            $student->gud_mobile2 = $data->dien_thoai_phu_huynh_2;
            $student->gud_name2 = trim($data->ho_ten_phu_huynh_1);
            $student->gud_firstname2 = $gud_name2->firstname;
            $student->gud_lastname2 = $gud_name2->lastname;
            $student->gud_midname2 = $gud_name2->midname;
            $student->gud_email2 = $data->email_phu_huynh_2;
            $student->gud_birth_day2 = $data->ngay_sinh_phu_huynh_2;
            $student->address = $data->dia_chi;
            $student->created_at = date('Y-m-d H:i:s');
            $student->creator_id = $data->creator_id;
            $student->updated_at = date('Y-m-d H:i:s');
            $student->editor_id = $data->creator_id;;
            $student->hash_key =  md5($student->name . $student->gud_name1 . $student->gud_mobile1);
            $student->used_student_ids = $used_id;
            $student->branch_id = $branch_info->id;
            $student->checkin_at = $data->ngay_check_in;
            $student->status = 0;
            $student->source = $data->nguon;
            $student->sibling_id = $sibling_id;
            $student->save();
            $lastInsertedId = $student->id;
            $cms_id = '2' . str_pad((string)$lastInsertedId, 7, '0', STR_PAD_LEFT);
            $crm_id = "CMS$cms_id";
            $cms_id = (int)$cms_id;
            u::query("UPDATE students SET cms_id = '$cms_id', crm_id = '$crm_id' WHERE id = $lastInsertedId");
            $region_id = 0;
            $zone_id = 0;
            if($data->ma_ec){
                $ec_info = u::first("SELECT id FROM users WHERE hrm_id = '$data->ma_ec'");
                $ec_id = $ec_info? $ec_info->id :(int)$branch_info->ceo_id;
            }else{
                $ec_id = (int)$branch_info->ceo_id;
            }
            $cs_id = (int)$branch_info->om_id;
            DB::table('term_student_user')->insert(
                [
                    'student_id' => $lastInsertedId,
                    'ec_id' => $ec_id,
                    'cm_id' => $cs_id,
                    'status' => 0,
                    'ec_leader_id' => 0,
                    'om_id' => $cs_id,
                    'branch_id' => $branch_info->id,
                    'region_id' => $region_id,
                    'zone_id' => $zone_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
            $data = (object)array(
                'status'=>1,
                'message'=>$message,
                'student_id'=>$lastInsertedId
            );
        }
        return $data;
    }
    public function checkCheckinImportExportLog(Request $request,$checkin_import_id)
    {
        if ($checkin_import_id) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'STT');
            $sheet->setCellValue('B1', 'Mã Trung tâm');
            $sheet->setCellValue('C1', 'Mã TVV');
            $sheet->setCellValue('D1', 'Họ tên học sinh');
            $sheet->setCellValue('E1', 'Ngày sinh học sinh');
            $sheet->setCellValue('F1', 'Họ tên PH1');
            $sheet->setCellValue('G1', 'Ngày sinh PH1');
            $sheet->setCellValue('H1', 'Số điện thoại PH1');
            $sheet->setCellValue('I1', 'Email PH1');
            $sheet->setCellValue('J1', 'Họ tên PH2');
            $sheet->setCellValue('K1', 'Ngày sinh PH2');
            $sheet->setCellValue('L1', 'Số điện thoại PH2');
            $sheet->setCellValue('M1', 'Email PH2');
            $sheet->setCellValue('N1', 'Địa chỉ PH2');
            $sheet->setCellValue('O1', 'Mã Nguồn');
            $sheet->setCellValue('P1', 'Ngày checkin');
            $sheet->setCellValue('Q1', 'Anh em trên file');
            $sheet->setCellValue('R1', 'Anh em trên hệ thống');
            $sheet->setCellValue('S1', 'Trạng thái');
            $sheet->setCellValue('T1', 'Ghi chú');
            $sheet->setCellValue('U1', 'Mã học sinh');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(30);
            $sheet->getColumnDimension('H')->setWidth(30);
            $sheet->getColumnDimension('I')->setWidth(30);
            $sheet->getColumnDimension('J')->setWidth(30);
            $sheet->getColumnDimension('K')->setWidth(30);
            $sheet->getColumnDimension('L')->setWidth(30);
            $sheet->getColumnDimension('M')->setWidth(30);
            $sheet->getColumnDimension('N')->setWidth(30);
            $sheet->getColumnDimension('O')->setWidth(30);
            $sheet->getColumnDimension('P')->setWidth(30);
            $sheet->getColumnDimension('Q')->setWidth(30);
            $sheet->getColumnDimension('R')->setWidth(30);
            $sheet->getColumnDimension('S')->setWidth(30);
            $sheet->getColumnDimension('T')->setWidth(30);
            $sheet->getColumnDimension('U')->setWidth(30);
            ProcessExcel::styleCells($spreadsheet, "A1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "B1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "C1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "D1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "E1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "F1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "G1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "H1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "I1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "J1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "K1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "L1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "M1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "N1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "O1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "P1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "Q1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "R1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "S1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "T1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "U1", "223b54", "FFFFFF", 12, 1, true, "center", "center", true);

            $lists = u::query("SELECT l.*,(SELECT crm_id FROM students WHERE id=l.student_id) AS crm_id
                FROM checkin_import_log AS l WHERE l.checkin_import_id=$checkin_import_id");

            foreach ($lists as $k => $row) {
                $sheet->setCellValue('A' . ($k + 2), $row->stt);
                $sheet->setCellValue('B' . ($k + 2), $row->ma_trung_tam);
                $sheet->setCellValue('C' . ($k + 2), $row->ma_ec);
                $sheet->setCellValue('D' . ($k + 2), $row->ho_ten_hoc_sinh);
                $sheet->setCellValue('E' . ($k + 2), "'".$row->ngay_sinh_hoc_sinh);
                $sheet->setCellValue('F' . ($k + 2), $row->ho_ten_phu_huynh_1);
                $sheet->setCellValue('G' . ($k + 2), "'".$row->ngay_sinh_phu_huynh_1);
                $sheet->setCellValue('H' . ($k + 2), "'".$row->dien_thoai_phu_huynh_1);
                $sheet->setCellValue('I' . ($k + 2), "'".$row->email_phu_huynh_1);
                $sheet->setCellValue('J' . ($k + 2), $row->ho_ten_phu_huynh_2);
                $sheet->setCellValue('K' . ($k + 2), $row->ngay_sinh_phu_huynh_2);
                $sheet->setCellValue('L' . ($k + 2), "'".$row->dien_thoai_phu_huynh_2);
                $sheet->setCellValue('M' . ($k + 2), $row->email_phu_huynh_2);
                $sheet->setCellValue('N' . ($k + 2), $row->dia_chi);
                $sheet->setCellValue('O' . ($k + 2), $row->nguon);
                $sheet->setCellValue('P' . ($k + 2), "'".$row->ngay_check_in);
                $sheet->setCellValue('Q' . ($k + 2), $row->anh_em_file);
                $sheet->setCellValue('R' . ($k + 2), $row->ma_anh_em_he_thong);
                $sheet->setCellValue('S' . ($k + 2), $row->status==1 ? "Thành Công":"Lỗi");
                $sheet->setCellValue('T' . ($k + 2), $row->note);
                $sheet->setCellValue('U' . ($k + 2), $row->crm_id);
            }

            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="StudentCheckinImport.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
            exit;
        }
    }
    public function addCheckinByLead(Request $request){
            $unix_check_dublicate = md5($request->student_name . $request->gud_name . $request->gud_mobile_1);
            $creator_info = u::first("SELECT u.id, (SELECT role_id FROM term_user_branch WHERE user_id=u.id AND `status`= 1 LIMIT 1) AS role_id, 
                    (SELECT branch_id FROM term_user_branch WHERE user_id=u.id AND `status`= 1 LIMIT 1) AS branch_id
                FROM users AS u WHERE u.hrm_id='$request->creator_hrm'");
            $uid = $creator_info ? $creator_info->id : 0;
            $student = new Student();
            $student->cms_id = 0;
            $student->crm_id = "CMS" . time();
            $student->name = $request->student_name;
            $tmp_student_name = u::explodeName($request->student_name);
            $student->firstname = $tmp_student_name->firstname;
            $student->lastname = $tmp_student_name->lastname;
            $student->midname = $tmp_student_name->midname;
            $student->gender = $request->student_gender;
            $student->note = $request->student_note;
            $student->date_of_birth = $request->student_date_of_birth;
            $student->gud_mobile1 = $request->gud_mobile_1;
            $student->gud_name1 = $request->gud_name;
            $tmp_parent_name = u::explodeName($request->gud_name);
            $student->gud_firstname1 = $tmp_parent_name->firstname;
            $student->gud_lastname1 = $tmp_parent_name->lastname;
            $student->gud_midname1 = $tmp_parent_name->midname;
            $student->gud_email1 = $request->gud_email;
            $student->gud_birth_day1 = $request->gud_birthday;
            $student->gud_mobile2 = $request->gud_mobile_2;
            $student->gud_gender1 = $request->gud_gender;
            $student->gud_job1 = $request->gud_job;
            $student->address = $request->address;
            $student->province_id = $request->province_id;
            $student->district_id = $request->district_id;
            $student->created_at = date('Y-m-d H:i:s');
            $student->creator_id = $uid;
            $student->updated_at = date('Y-m-d H:i:s');
            $student->editor_id = $uid;
            $student->hash_key = $unix_check_dublicate;
            $student->branch_id = $request->branch_id;
            $student->checkin_by_branch_id = $creator_info ? $creator_info->branch_id : 0;
            $student->checkin_by = $creator_info ? $creator_info->id : 0;
            $student->checkin_branch_id = $request->branch_id;
            $student->checkin_at = $request->checkin_at;
            $student->type_product = $request->type_product;
            $student->sibling_id = $request->sibling_id ? $request->sibling_id : 0;
            $student->status = 0;
            $student->source = $request->source;
            $student->source_detail = $request->source_detail;
            $student->save();
            $lastInsertedId = $student->id;
            $cms_id = '2' . str_pad((string)$lastInsertedId, 7, '0', STR_PAD_LEFT);
            $crm_id = "CMS$cms_id";
            $cms_id = (int)$cms_id;
            u::query("UPDATE students SET cms_id = '$cms_id', crm_id = '$crm_id' WHERE id = $lastInsertedId");
            $region_id = 0;
            $zone_id = 0;
            $ec_info = u::first("SELECT id,(SELECT role_id FROM term_user_branch WHERE user_id=u.id AND `status`=1) AS role_id FROM users AS u WHERE u.hrm_id='$request->ec_hrm'");
            $cs_info = u::first("SELECT user_id FROM term_user_branch WHERE branch_id=$request->branch_id AND role_id=56 AND status=1");
            $ec_id = $ec_info ? (int) $ec_info->id : 0 ;
            $cs_id = $cs_info ? (int) $cs_info->user_id : 0;

            $ecLeader = u::first("SELECT u2.id as ec_leader_id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $ec_id");
            $csLeader = u::first("SELECT u2.id as cs_leader_id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $cs_id");

            $ecLeaderId = ($ecLeader && $ecLeader->ec_leader_id > 0) ? $ecLeader->ec_leader_id : $ec_id;
            $csLeaderId = ($csLeader && $csLeader->cs_leader_id > 0) ? $csLeader->cs_leader_id : $cs_id;


            DB::table('term_student_user')->insert(
                [
                    'student_id' => $lastInsertedId,
                    'ec_id' => $ec_id,
                    'cm_id' => $cs_id,
                    'status' => 0,
                    'ec_leader_id' => $ecLeaderId,
                    'om_id' => $csLeaderId,
                    'branch_id' => $request->branch_id,
                    'region_id' => $region_id,
                    'zone_id' => $zone_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
            $content = "Checkin HS: ".$student->name .", CRM_ID: ". $crm_id;
            Checkin::addLogStudentUpdate($lastInsertedId, $content, $cs_id,$uid,$request->branch_id);

            if($creator_info && in_array($creator_info->role_id,[80,81,7676767]) && date('Y-m-d',strtotime($request->checkin_at))==date('Y-m-d') && (date('H')>=14 || date('w')==6 ||date('w')==0)){
                JobsController::sendMailSalehub($request->branch_id, $lastInsertedId);
            }elseif(date('H')>=14 && (date('w')==5 || date('w')==6) && $creator_info && in_array($creator_info->role_id,[80,81,7676767]) && date('Y-m-d',strtotime($request->checkin_at))==date('Y-m-d',time()+24*3600)){
                JobsController::sendMailSalehub($request->branch_id, $lastInsertedId);
            }
            return response()->json(['data' =>['student_id' =>$lastInsertedId]]);
    }
    public function updateCheckinByLead(Request $request){
        $student = Student::find($request->crm_student_id);
        $creator_info = u::first("SELECT u.id, (SELECT role_id FROM term_user_branch WHERE user_id=u.id AND `status`=1) AS role_id,
                (SELECT branch_id FROM term_user_branch WHERE user_id=u.id AND `status`= 1 LIMIT 1) AS branch_id 
            FROM users AS u WHERE u.hrm_id='$request->updator_hrm'");
        if(!$creator_info){
            $creator_info = u::first("SELECT u.id, (SELECT role_id FROM term_user_branch WHERE user_id=u.id AND `status`=1) AS role_id, 
                    (SELECT branch_id FROM term_user_branch WHERE user_id=u.id AND `status`= 1 LIMIT 1) AS branch_id
                FROM users AS u WHERE u.hrm_id='$student->creator_id'");
        }
        
        if($student->branch_id == $request->branch_id){
            $student->checkin_by_branch_id = $creator_info ? $creator_info->branch_id : 0;
            $student->checkin_by = $creator_info ? $creator_info->id : 0;
            $student->checkin_branch_id = $request->branch_id;
            $student->checkin_at = $request->checkin_at;
            $student->type_product = $request->type_product;
            $student->save();
        }else{
            $student->branch_id = $request->branch_id;
            $student->checkin_by_branch_id = $creator_info ? $creator_info->branch_id : 0;
            $student->checkin_by = $creator_info ? $creator_info->id : 0;
            $student->checkin_branch_id = $request->branch_id;
            $student->checkin_at = $request->checkin_at;
            $student->type_product = $request->type_product;
            $student->save();
            $ecLeader = u::first("SELECT user_id as ec_leader_id FROM term_user_branch WHERE role_id = 69 AND status=1 AND branch_id = $request->branch_id");
            $csLeader = u::first("SELECT user_id as ec_leader_id FROM term_user_branch WHERE role_id = 56 AND status=1 AND branch_id = $request->branch_id");
            $ecLeaderId = ($ecLeader && $ecLeader->ec_leader_id > 0) ? $ecLeader->ec_leader_id : 0;
            $csLeaderId = ($csLeader && $csLeader->cs_leader_id > 0) ? $csLeader->cs_leader_id : 0;
            DB::table('term_student_user')->insert(
                [
                    'student_id' => $student->id,
                    'ec_id' => $ecLeaderId,
                    'cm_id' => $csLeaderId,
                    'ec_leader_id' => $ecLeaderId,
                    'om_id' => $csLeaderId,
                    'branch_id' => $request->branch_id,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
        }
        
        if($creator_info && in_array($creator_info->role_id,[80,81,7676767]) && date('Y-m-d',strtotime($request->checkin_at))==date('Y-m-d') && (date('H')>=14 || date('w')==6 ||date('w')==0)){
            JobsController::sendMailSalehub($request->branch_id, $student->id);
        }elseif(date('H')>=14 && (date('w')==5 || date('w')==6) && $creator_info && in_array($creator_info->role_id,[80,81,7676767]) && date('Y-m-d',strtotime($request->checkin_at))==date('Y-m-d',time()+24*3600)){
            JobsController::sendMailSalehub($request->branch_id, $student->id);
        }
        return response()->json(['data' =>['student_id' =>$student->id]]);
    }
    public function updateStudentInfoByLead(Request $request)
    {
        $creator_info = u::first("SELECT u.id, (SELECT role_id FROM term_user_branch WHERE user_id=u.id AND `status`=1) AS role_id FROM users AS u WHERE u.hrm_id='$request->updator_hrm'");
        $uid = $creator_info ? $creator_info->id : 0;
        $old_student = u::first("SELECT s.* FROM students s WHERE s.id = $request->crm_id AND s.status >0");
        $student = Student::find($request->crm_id);
        $student->name = $request->student_name;
        $tmp_student_name = u::explodeName($request->student_name);
        $student->firstname = $tmp_student_name->firstname;
        $student->lastname = $tmp_student_name->lastname;
        $student->midname = $tmp_student_name->midname;
        $student->gender = $request->gender;
        $student->note = $request->note;
        $student->save();
        $info_change = "";
        if ($student->name != $old_student->name) {
            $info_change .= "Sửa tên: '$old_student->name' - thành: '$student->name'.<br/>\n";
        }
        if ($student->gender != $old_student->gender) {
            $from_gender = strtolower($old_student->gender) == 'f' ? 'Nữ' : 'Nam';
            $to_gender = strtolower($student->gender) == 'f' ? 'Nữ' : 'Nam';
            $info_change .= "Sửa giới tính: '$from_gender' - thành: '$to_gender'.<br/>\n";
        }
        if ($student->school != $old_student->school) {
            $info_change .= "Sửa ghi chú: '$old_student->note' - thành: '$student->note'.<br/>\n";
        } 
        if ($info_change) {
            DB::table('log_student_update')->insert(
                [
                    'student_id' => $student->id,
                    'updated_by' => $uid,
                    'status' => 1,
                    'branch_id' => $student->branch_id,
                    'content' => $info_change,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
        }
        return response()->json(['data' =>['student_id' =>$student->id]]);
    }
    public function updateParentInfoByLead(Request $request)
    {
        $list_student = explode(',',$request->list_student_crm);
        foreach($list_student AS $student_id){
            $creator_info = u::first("SELECT u.id, (SELECT role_id FROM term_user_branch WHERE user_id=u.id AND `status`=1) AS role_id FROM users AS u WHERE u.hrm_id='$request->updator_hrm'");
            $uid = $creator_info ? $creator_info->id : 0;
            $old_student = u::first("SELECT s.* FROM students s WHERE s.id = $student_id AND s.status >0");
            $student = Student::find($student_id);
            $student->gud_name1 = $request->gud_name;
            $tmp_gud_name = u::explodeName($request->gud_name);
            $student->gud_firstname1 = $tmp_gud_name->firstname;
            $student->gud_lastname1 = $tmp_gud_name->lastname;
            $student->gud_midname1 = $tmp_gud_name->midname;
            $student->gud_gender1 = $request->gud_gender;
            $student->gud_birth_day1 = $request->gud_birthday;
            $student->gud_job1 = $request->gud_job;
            $student->source = $request->source;
            $student->source_detail = $request->source_detail;
            $student->address = $request->address;
            $student->province_id = $request->province_id;
            $student->district_id = $request->district_id;
            $student->gud_mobile1 = $request->gud_mobile_1;
            $student->gud_mobile2 = $request->gud_mobile_2;
            $student->save();
            $info_change = "";
            if ($student->gud_name1 != $student->gud_name1) {
                $info_change .= "Sửa tên phụ huynh: '$old_student->gud_name1' - thành: '$student->gud_name1'.<br/>\n";
            }
            if ($student->gud_mobile1 != $student->gud_mobile1) {
                $info_change .= "Sửa số điện thoại phụ huynh: '$old_student->gud_mobile1' - thành: '$student->gud_mobile1'.<br/>\n";
            }
            if ($student->gud_mobile2 != $student->gud_mobile2) {
                $info_change .= "Sửa số điện thoại 2 phụ huynh: '$old_student->gud_mobile2' - thành: '$student->gud_mobile2'.<br/>\n";
            }
            if ($student->source != $old_student->source) {
                $info_change .= "Sửa nguồn từ: '$old_student->source' - thành: '$student->source'.<br/>\n";
            }
            if ($student->gud_birth_day1 != $old_student->gud_birth_day1) {
                $info_change .= "Sửa ngày sinh phụ huynh: '$old_student->gud_birth_day1' - thành: '$student->gud_birth_day1'.<br/>\n";
            }
            if ($student->address != $old_student->address) {
                $info_change .= "Sửa ngày địa chỉ: '$old_student->address' - thành: '$student->address'.<br/>\n";
            }
            
            if ($info_change) {
                DB::table('log_student_update')->insert(
                    [
                        'student_id' => $student->id,
                        'updated_by' => $uid,
                        'status' => 1,
                        'branch_id' => $student->branch_id,
                        'content' => $info_change,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );
            }
        }
        
        return response()->json(['data' =>['student_id' =>$request->list_student_crm]]);
    }
}
