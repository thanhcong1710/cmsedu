<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\CurlServiceProvider as curl;
use App\Models\APICode;
use App\Models\Response;
use App\Models\LogExternalRequest;
use App\Providers\UtilityServiceProvider as u;
class LMSAPIController
{
    public function checkAPI(){
        self::updateClassLMS(14);
    }
    private function callAPI($url, $method, $params, $header, $is_api = true, $is_utf8 = false)
    {
        $resp = null;
        return $resp;
        $response = new Response();
        $data = null;
        $log = new LogExternalRequest();
        try {
            $res = curl::curl($url, $method, $header, $params);
            if ($res) {
                $code = APICode::SUCCESS;    
                $log->logCallingAPI($url, json_encode($params), json_encode($header), $method, date('Y-m-d H:i:s'), $res, $log->SUCCESS_STATUS, $log->LMS_API);
                if ($is_utf8){
                    $data = json_decode(utf8.decode($res));
                }else{
                    $data = json_decode($res);
                }
            } else {
                $code = APICode::FAILURE_CALLING_LMS_API;
                $log->logCallingAPI($url, json_encode($params), json_encode($header), $method,  date('Y-m-d H:i:s'), $res, $log->SUCCESS_STATUS, $log->LMS_API);
            }
        } catch (\Exception $exception) {
            $code = APICode::FAILURE_CALLING_LMS_API;
            $log->logCallingAPI($url, json_encode($params), json_encode($header), $method, date('Y-m-d H:i:s'), $exception->getMessage(), $log->FAILURE_STATUS, $log->LMS_API);
        }

        if ($is_api) {
            $resp = $response->formatResponse($code, $data);
        } else {
            $resp = $data;
        }

        return $resp;
    }
    public function getTokenLMS()
    {
        if (ENVIRONMENT == 'product') {
            $url = "https://lms-vn.cmsedu.net/api/v1/auth/sys/token.do";
        } else {
            $url = "https://lms-vn.cmsedu.net/api/v1/auth/sys/token.do";
        }
        $method = 'POST';
        $params = [
            "sysId" => "crm",
            "accessKey" => "g2hEsGwT5ud9Ts",
        ];
        $header = [];
        $resp = $this->callAPI($url, $method, $params, $header, 0, false);
        return $resp;
    }
    public function createBranchLMS($branch_id)
    {
        $branch_info = u::first("SELECT b.*, 
                (SELECT u.full_name FROM term_user_branch AS t LEFT JOIN users AS u ON u.id=t.user_id WHERE u.status=1 AND t.status=1 AND t.role_id=686868 AND t.branch_id=b.id LIMIT 1) AS center_director_name 
            FROM branches AS b 
            WHERE b.id=$branch_id");
        $token = self::getTokenLMS();
        if($token->status=='SUCCESS'){
            $token=$token->result->accessToken;
        }else{
            $token= "error";
        }
        
        if (ENVIRONMENT == 'product') {
            $url = "https://lms-vn.cmsedu.net/api/v1/user/centerRegAction.do";
        } else {
            $url = "https://lms-vn.cmsedu.net/api/v1/user/centerRegAction.do";
        }
        $method = 'POST';
        $params = [
            "membId" => $branch_info->id_lms,
            "cmsVnMembId" => "cms.vn",
            "cntrPasswd" => "123456aA@",
            "cntrNm" => $branch_info->name,
            "cntrMasterNm" => $branch_info->id_lms,
            "cntrEmail" => $branch_info->email,
            "cntrContact" => $branch_info->hotline,
            "cntrStat" => $branch_info->status==1 ? 'US001':'US002',
            "remark" => "",
        ];
        $header = [
            "Authorization:$token",
        ];
        $resp = $this->callAPI($url, $method, $params, $header, 0, false);
    }
    public function updateBranchLMS($branch_id)
    {
        $branch_info = u::first("SELECT b.*, 
                (SELECT u.full_name FROM term_user_branch AS t LEFT JOIN users AS u ON u.id=t.user_id WHERE u.status=1 AND t.status=1 AND t.role_id=686868 AND t.branch_id=b.id LIMIT 1) AS center_director_name 
            FROM branches AS b 
            WHERE b.id=$branch_id");
        $token = self::getTokenLMS();
        if($token->status=='SUCCESS'){
            $token=$token->result->accessToken;
        }else{
            $token= "error";
        }
        
        if (ENVIRONMENT == 'product') {
            $url = "https://lms-vn.cmsedu.net/api/v1/user/centerModAction.do";
        } else {
            $url = "https://lms-vn.cmsedu.net/api/v1/user/centerModAction.do";
        }
        $method = 'POST';
        $params = [
            "membId" => $branch_info->id_lms,
            "cmsVnMembId" => "cms.vn",
            "cntrNm" => $branch_info->name,
            "cntrMasterNm" => $branch_info->center_director_name ? $branch_info->center_director_name : $branch_info->id_lms,
            "cntrEmail" => $branch_info->email,
            "cntrContact" => $branch_info->hotline,
            "cntrStat" => $branch_info->status==1 ? 'US001':'US002',
            "remark" => "",
        ];

        $header = [
            "Authorization:$token",
        ];
        $resp = $this->callAPI($url, $method, $params, $header, 0, false);
    }
    public function createTeacherLMS($teacher_id)
    {
        $teacher_info = u::first("SELECT t.*, u.status,u.email,u.phone,tb.is_head_teacher,tb.branch_id,
            (SELECT id_lms FROM branches WHERE id=tb.branch_id) AS branch_id_lms
            FROM teachers AS t
                LEFT JOIN users AS u ON t.user_id=u.id 
                LEFT JOIN term_teacher_branch AS tb ON tb.teacher_id=t.id AND tb.status=1
            WHERE t.id=$teacher_id");
        $token = self::getTokenLMS();
        if($token->status=='SUCCESS'){
            $token=$token->result->accessToken;
        }else{
            $token= "error";
        }
        
        if (ENVIRONMENT == 'product') {
            $url = "https://lms-vn.cmsedu.net/api/v1/user/teacherRegAction.do";
        } else {
            $url = "https://lms-vn.cmsedu.net/api/v1/user/teacherRegAction.do";
        }
        $method = 'POST';
        $params = [
            "membId" => $teacher_info->id_lms,
            "tchPasswd" => "@12345678",
            "tchNm" => $teacher_info->ins_name,
            "tchEmail" => $teacher_info->email,
            "tchContact" => $teacher_info->phone,
            "tchStat" => $teacher_info->status ==1 ? 'US001':'US002',
            "head" => $teacher_info->is_head_teacher==1 ?'Y':'N',
            "remark" => "",
            "cntrId" => $teacher_info->branch_id_lms,
        ];
        $header = [
            "Authorization:$token",
        ];
        $resp = $this->callAPI($url, $method, $params, $header, 0, false);
    }
    public function updateTeacherLMS($teacher_id)
    {
        $teacher_info = u::first("SELECT t.*, u.status,u.email,u.phone,tb.is_head_teacher,tb.branch_id,
            (SELECT id_lms FROM branches WHERE id=tb.branch_id) AS branch_id_lms
            FROM teachers AS t
                LEFT JOIN users AS u ON t.user_id=u.id 
                LEFT JOIN term_teacher_branch AS tb ON tb.teacher_id=t.id AND tb.status=1
            WHERE t.id=$teacher_id");
        $token = self::getTokenLMS();
        if($token->status=='SUCCESS'){
            $token=$token->result->accessToken;
        }else{
            $token= "error";
        }
        
        if (ENVIRONMENT == 'product') {
            $url = "https://lms-vn.cmsedu.net/api/v1/user/teacherModAction.do";
        } else {
            $url = "https://lms-vn.cmsedu.net/api/v1/user/teacherModAction.do";
        }
        $method = 'POST';
        $params = [
            "membId" => $teacher_info->id_lms,
            "tchNm" => $teacher_info->ins_name,
            "tchEmail" => $teacher_info->email,
            "tchContact" => $teacher_info->phone,
            "tchStat" => $teacher_info->status ==1 ? 'US001':'US002',
            "head" => $teacher_info->is_head_teacher==1 ?'Y':'N',
            "remark" => "",
            "cntrId" => $teacher_info->branch_id_lms,
        ];
        $header = [
            "Authorization:$token",
        ];
        $resp = $this->callAPI($url, $method, $params, $header, 0, false);
    }
    public function createClassLMS($class_id)
    {
        $class_info = u::first("SELECT t.id_lms AS teacher_id_lms,cl.*,
            (SELECT id_lms FROM branches WHERE id=cl.branch_id) AS branch_id_lms,
            (SELECT name FROM  level WHERE id=cl.level_id) AS level_name 
            FROM classes AS cl
                LEFT JOIN teachers AS t ON t.user_id=cl.teacher_id
            WHERE cl.id=$class_id");
        $token = self::getTokenLMS();
        if($token->status=='SUCCESS'){
            $token=$token->result->accessToken;
        }else{
            $token= "error";
        }
        
        if (ENVIRONMENT == 'product') {
            $url = "https://lms-vn.cmsedu.net/api/v1/cntr/classRegAction.do";
        } else {
            $url = "https://lms-vn.cmsedu.net/api/v1/cntr/classRegAction.do";
        }
        $method = 'POST';
        
        if($class_info->is_trial==1){
            $clsType = 'CT002';
        }elseif($class_info->product_id==2){
            $clsType = 'CT003';
        }elseif($class_info->product_id==3){
            $clsType = 'CT004';
        }else{
            $clsType = 'CT001';
        }
        $params = [
            "clsNm" => $class_info->cls_name,
            "clsTch" => $class_info->teacher_id_lms,
            "clsLevel" => $class_info->level_name,
            "clsStat" => $class_info->cls_iscancelled == 'no'? 'US001':'US002',
            "clsType" => $clsType,
            "cntrId" => $class_info->branch_id_lms,
        ];
        $header = [
            "Authorization:$token",
        ];
        $resp = $this->callAPI($url, $method, $params, $header, 0, false);
        if($resp->status=='SUCCESS' && isset($resp->result->classSeq)){
            u::query("UPDATE classes SET id_lms= '".$resp->result->classSeq."' WHERE id=$class_id");
        }
    }
    public function updateClassLMS($class_id,$pre_teacher_id=0)
    {
        $class_info = u::first("SELECT t.id_lms AS teacher_id_lms,cl.*,
            (SELECT id_lms FROM branches WHERE id=cl.branch_id) AS branch_id_lms,
            (SELECT id_lms FROM teachers WHERE user_id=$pre_teacher_id LIMIT 1) AS pre_teacher_id_lms,
            (SELECT name FROM  level WHERE id=cl.level_id) AS level_name 
            FROM classes AS cl
                LEFT JOIN teachers AS t ON t.user_id=cl.teacher_id
            WHERE cl.id=$class_id");
        $token = self::getTokenLMS();
        if($token->status=='SUCCESS'){
            $token=$token->result->accessToken;
        }else{
            $token= "error";
        }
        
        if (ENVIRONMENT == 'product') {
            $url = "https://lms-vn.cmsedu.net/api/v1/cntr/classModAction.do";
        } else {
            $url = "https://lms-vn.cmsedu.net/api/v1/cntr/classModAction.do";
        }
        $method = 'POST';
        if($class_info->is_trial==1){
            $clsType = 'CT002';
        }elseif($class_info->product_id==2){
            $clsType = 'CT003';
        }elseif($class_info->product_id==3){
            $clsType = 'CT004';
        }else{
            $clsType = 'CT001';
        }
        $params = [
            "clsNm" => $class_info->cls_name,
            "prevClsTch" => $pre_teacher_id ? $class_info->pre_teacher_id_lms :$class_info->teacher_id_lms,
            "clsTch" => $class_info->teacher_id_lms,
            "clsLevel" => $class_info->level_name,
            "clsStat" => $class_info->cls_iscancelled == 'no'? 'US001':'US002',
            "clsType" => $clsType,
            "cntrId" => $class_info->branch_id_lms,
            "classSeq" =>(int)$class_info->id_lms
        ];
        $header = [
            "Authorization:$token",
        ];
        $resp = $this->callAPI($url, $method, $params, $header, 0, false);
        if($pre_teacher_id){
            $list_student = u::query("SELECT student_id FROM contracts WHERE class_id=$class_id AND status!=7");
            foreach($list_student AS $student){
                self::updateStudentLMS($student->student_id);
            }
        }
    }
    public function createStudentLMS($contract_id)
    {
        $student_info = u::first("SELECT b.id_lms AS branch_id_lms,t.id_lms AS teacher_id_lms,cl.id_lms AS class_id_lms,
            DATE_FORMAT(s.created_at ,'%Y-%m-%d') AS student_created_at,s.name AS student_name,s.date_of_birth,s.gender,
            c.enrolment_start_date,c.enrolment_last_date,s.accounting_id,c.student_id,s.crm_id,c.type,cl.is_trial,c.coupon
            FROM contracts AS c
                LEFT JOIN students AS s ON s.id=c.student_id 
                LEFT JOIN classes AS cl ON cl.id=c.class_id
                LEFT JOIN teachers AS t ON t.user_id=cl.teacher_id
                LEFT JOIN branches AS b ON b.id=c.branch_id
            WHERE c.id=$contract_id");
        if($student_info && !in_array($student_info->coupon,['CN00-M027-02-01','C00-M027-02-01','C00-M027-02-02'])){
            $token = self::getTokenLMS();
            if($token->status=='SUCCESS'){
                $token=$token->result->accessToken;
            }else{
                $token= "error";
            }
            
            if (ENVIRONMENT == 'product') {
                $url = "https://lms-vn.cmsedu.net/api/v1/cntr/studentRegAction.do";
            } else {
                $url = "https://lms-vn.cmsedu.net/api/v1/cntr/studentRegAction.do";
            }
            $method = 'POST';
            $params = [
                "cntrId" => $student_info->branch_id_lms,
                "tchId" => $student_info->type==0 && $student_info->is_trial==1 ? $student_info->branch_id_lms:$student_info->teacher_id_lms,
                "classSeq" => (int)$student_info->class_id_lms,
                "intoDt" => $student_info->student_created_at,
                "stuNm" => $student_info->student_name,
                "stuBirthDt" => $student_info->date_of_birth,
                "stuGen" => $student_info->gender,
                "startDt" => $student_info->enrolment_start_date,
                "endDt" => $student_info->enrolment_last_date,
                "validCd" => "VC005",
                "stuStat" => 'SS002' ,
                "remark" => "",
                "stuId" =>$student_info->accounting_id ? $student_info->accounting_id : $student_info->crm_id,
            ];
            $header = [
                "Authorization:$token",
            ];
            $resp = $this->callAPI($url, $method, $params, $header, 0, false);
            if($resp->status=='SUCCESS' && isset($resp->result->stuSeq)){
                u::query("UPDATE students SET id_lms= '".$resp->result->stuSeq."' WHERE id=$student_info->student_id");
            }
        }
    }
    public function updateStudentLMS($student_id,$from_class_id=0,$is_branchTransfer =false)
    {
        $student_info = u::first("SELECT b.id_lms AS branch_id_lms,t.id_lms AS teacher_id_lms,cl.id_lms AS class_id_lms,
            DATE_FORMAT(s.created_at ,'%Y-%m-%d') AS student_created_at,s.name AS student_name,s.date_of_birth,s.gender,
            c.enrolment_start_date,c.enrolment_last_date,s.accounting_id,c.status AS contract_status,s.id_lms,
            (SELECT id_lms FROM classes WHERE id =$from_class_id) AS pre_class_id_lms,c.type,cl.is_trial,c.coupon
            FROM contracts AS c
                LEFT JOIN students AS s ON s.id=c.student_id 
                LEFT JOIN classes AS cl ON cl.id=c.class_id
                LEFT JOIN teachers AS t ON t.user_id=cl.teacher_id
                LEFT JOIN branches AS b ON b.id=c.branch_id
            WHERE c.student_id=$student_id AND c.class_id IS NOT NULL ORDER BY c.count_recharge DESC,c.id DESC  LIMIT 1");
        if($student_info && !in_array($student_info->coupon,['CN00-M027-02-01','C00-M027-02-01'])){
            $token = self::getTokenLMS();
            if($token->status=='SUCCESS'){
                $token=$token->result->accessToken;
            }else{
                $token= "error";
            }
            
            if (ENVIRONMENT == 'product') {
                $url = "https://lms-vn.cmsedu.net/api/v1/cntr/studentModAction.do";
            } else {
                $url = "https://lms-vn.cmsedu.net/api/v1/cntr/studentModAction.do";
            }
            $method = 'POST';
            $params = [
                "membId" => $student_info->teacher_id_lms,
                "classSeq" => (int)$student_info->class_id_lms,
                "intoDt" => $student_info->student_created_at,
                "stuNm" => $student_info->student_name,
                "stuBirthDt" => $student_info->date_of_birth,
                "stuGen" => $student_info->gender,
                "startDt" => $student_info->enrolment_start_date,
                "endDt" => $student_info->enrolment_last_date,
                "validCd" => "VC005",
                "stuStat" =>  $student_info->contract_status ==7 || $is_branchTransfer ?'SS003':'SS002',
                "hMembId" => $student_info->branch_id_lms,
                "hStuSeq" =>(int)$student_info->id_lms,
                "hClassSeq"=>$from_class_id?(int)$student_info->pre_class_id_lms:(int)$student_info->class_id_lms,
                "moveYn"=>$from_class_id?'Y':'N',
                "stuId"=>$student_info->accounting_id
            ];
            $header = [
                "Authorization:$token",
            ];
            $resp = $this->callAPI($url, $method, $params, $header, 0, false);
        }   
    }
    public function updateLms($type){
        if($type==1){
            $list_data = u::query("SELECT * FROM tmp_lms_branch WHERE status=0 LIMIT 200");
            foreach($list_data AS $row){
                $branch_info = u::first("SELECT id FROM branches WHERE id='$row->branch_id'");
                if($branch_info){
                    u::query("UPDATE branches SET id_lms ='$row->id_lms',hotline='$row->hotline',email='$row->email' WHERE id='$row->branch_id'");
                    u::query("UPDATE tmp_lms_branch SET status=1 WHERE id=$row->id");
                    self::updateBranchLMS($row->branch_id);
                }
            }
        }elseif($type==2){
            $list_data = u::query("SELECT * FROM tmp_lms_teacher WHERE status=0 LIMIT 200");
            foreach($list_data AS $row){
                $teacher_info = u::first("SELECT id FROM teachers WHERE user_id='$row->user_id'");
                if($teacher_info){
                    u::query("UPDATE teachers SET id_lms ='$row->id_lms' WHERE user_id='$row->user_id'");
                    u::query("UPDATE tmp_lms_teacher SET status=1 WHERE id=$row->id");
                    self::updateTeacherLMS($teacher_info->id);
                }
            }
        }elseif($type==3){
            $list_data = u::query("SELECT * FROM tmp_lms_class WHERE status=0 LIMIT 200");
            foreach($list_data AS $row){
                $class_info = u::first("SELECT id FROM classes WHERE id='$row->class_id'");
                if($class_info){
                    u::query("UPDATE classes SET id_lms ='$row->id_lms',cls_name = '$row->cls_name',level_id='$row->level_id' WHERE id='$row->class_id'");
                    u::query("UPDATE tmp_lms_class SET status=1 WHERE id=$row->id");
                    self::updateClassLMS($row->class_id);
                }
            }
        }elseif($type==4){
            $list_data = u::query("SELECT * FROM tmp_lms_student WHERE status=0 LIMIT 200");
            foreach($list_data AS $row){
                $student_info = u::first("SELECT id FROM students WHERE accounting_id='$row->accounting_id'");
                if($student_info){
                    u::query("UPDATE students SET id_lms ='$row->id_lms' WHERE accounting_id='$row->accounting_id'");
                    u::query("UPDATE tmp_lms_student SET status=1 WHERE id=$row->id");
                    self::updateStudentLMS($student_info->id);
                }
            }
        }
        return "ok";
    }

    public function updateStudentLMSClass($student_id,$to_class_id=0,$is_branchTransfer =false,$start_date =null, $end_date=null)
    {
        $student_info = u::first("SELECT b.id_lms AS branch_id_lms,t.id_lms AS teacher_id_lms,cl.id_lms AS pre_class_id_lms,
            DATE_FORMAT(s.created_at ,'%Y-%m-%d') AS student_created_at,s.name AS student_name,s.date_of_birth,s.gender,
            c.enrolment_start_date,c.enrolment_last_date,s.accounting_id,c.status AS contract_status,s.id_lms,
            (SELECT id_lms FROM classes WHERE id =$to_class_id) AS class_id_lms,c.type,cl.is_trial,c.coupon
            FROM contracts AS c
                LEFT JOIN students AS s ON s.id=c.student_id 
                LEFT JOIN classes AS cl ON cl.id=c.class_id
                LEFT JOIN teachers AS t ON t.user_id=cl.teacher_id
                LEFT JOIN branches AS b ON b.id=c.branch_id
            WHERE c.student_id=$student_id AND c.class_id IS NOT NULL ORDER BY c.count_recharge DESC,c.id DESC  LIMIT 1");
        if($student_info && !in_array($student_info->coupon,['CN00-M027-02-01','C00-M027-02-01'])){
            $token = self::getTokenLMS();
            if($token->status=='SUCCESS'){
                $token=$token->result->accessToken;
            }else{
                $token= "error";
            }
            
            if (ENVIRONMENT == 'product') {
                $url = "https://lms-vn.cmsedu.net/api/v1/cntr/studentModAction.do";
            } else {
                $url = "https://lms-vn.cmsedu.net/api/v1/cntr/studentModAction.do";
            }
            $method = 'POST';
            $params = [
                "membId" => $student_info->teacher_id_lms,
                "classSeq" => (int)$student_info->class_id_lms,
                "intoDt" => $student_info->student_created_at,
                "stuNm" => $student_info->student_name,
                "stuBirthDt" => $student_info->date_of_birth,
                "stuGen" => $student_info->gender ? $student_info->gender : 'M',
                "startDt" => $start_date? $start_date : $student_info->enrolment_start_date,
                "endDt" => $end_date? $end_date : $student_info->enrolment_last_date,
                "validCd" => "VC005",
                "stuStat" =>  $student_info->contract_status ==7 || $is_branchTransfer ?'SS003':'SS002',
                "hMembId" => $student_info->branch_id_lms,
                "hStuSeq" =>(int)$student_info->id_lms,
                "hClassSeq"=>$to_class_id?(int)$student_info->pre_class_id_lms:(int)$student_info->class_id_lms,
                "moveYn"=>$to_class_id?'Y':'N',
                "stuId"=>$student_info->accounting_id
            ];
            $header = [
                "Authorization:$token",
            ];
            $resp = $this->callAPI($url, $method, $params, $header, 0, false);
        }   
    }
    public function processTmpUpdateClassLms(){
        $list_data = u::query("SELECT t.*,s.id AS student_id FROM tmp_update_class_lms AS t LEFT JOIN students AS s ON  s.accounting_id=t.student_accounting_id WHERE t.status=0 ORDER BY t.id");
        foreach($list_data AS $row){
            $this->updateStudentLMSClass($row->student_id,$row->class_id,false,$row->start_date,$row->end_date);
            u::query("UPDATE tmp_update_class_lms SET status=1 WHERE id=$row->id");
        }
        echo "ok";
        exit();
    }

    public function updateListStudent($page){
        $token = self::getTokenLMS();
        if($token->status=='SUCCESS'){
            $token=$token->result->accessToken;
        }else{
            $token= "error";
        }
        $url = "https://lms-vn.cmsedu.net/api/v1/cntr/getStudentList.do";
        $method = 'POST';
        $params = [
            "start" => 1000*$page+1,
            "length" => 1000,
        ];
        $header = [
            "Authorization:$token",
        ];
        $resp = $this->callAPI($url, $method, $params, $header, 0, false, $is_utf8 = true);
        $list_student = data_get($resp, 'result.data');
        if(!empty($list_student)){
            $this->addItemStudent($list_student);
        }
        return true;
    }

    public static function addItemStudent($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO lms_students (stuId, stuStat, stuStatNm, classSeq, classNm, stuSeq, stuNm, belong, belongNm, membId, `data`, created_at) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query.= "('".data_get($item, "stuId")."', '".data_get($item, "stuStat")."', '".html_entity_decode(data_get($item, "stuStatNm"))."', '".data_get($item, "classSeq")."', '".data_get($item, "classNm")."',
                    '".data_get($item, "stuSeq")."', '".html_entity_decode(data_get($item, "stuNm"))."', '".data_get($item, "belong")."','".html_entity_decode(data_get($item, "belongNm"))."','".data_get($item, "membId")."','".json_encode($item)."', '$created_at'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItemStudent(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $query.= "('".data_get($item, "stuId")."', '".data_get($item, "stuStat")."', '".html_entity_decode(data_get($item, "stuStatNm"))."', '".data_get($item, "classSeq")."', '".data_get($item, "classNm")."',
                    '".data_get($item, "stuSeq")."', '".html_entity_decode(data_get($item, "stuNm"))."', '".data_get($item, "belong")."','".html_entity_decode(data_get($item, "belongNm"))."','".data_get($item, "membId")."','".json_encode($item)."', '$created_at'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function updateListClass($page){
        $token = self::getTokenLMS();
        if($token->status=='SUCCESS'){
            $token=$token->result->accessToken;
        }else{
            $token= "error";
        }
        $url = "https://lms-vn.cmsedu.net/api/v1/cntr/getClassList.do";
        $method = 'POST';
        $params = [
            "start" => 1000*$page+1,
            "length" => 1000,
        ];
        $header = [
            "Authorization:$token",
        ];
        $resp = $this->callAPI($url, $method, $params, $header, 0, false);
        
        $list_class = data_get($resp, 'result.data');
        
        if(!empty($list_class)){
            $this->addItemClass($list_class);
        }
        return true;
    }

    public static function addItemClass($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO lms_classes (classSeq, classNm, classStat, classStatNm, belong, belongNm, membId, `data`, created_at) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query.= "('".data_get($item, "classSeq")."', '".data_get($item, "classNm")."', '".data_get($item, "classStat")."', '".data_get($item, "classStatNm")."', '".data_get($item, "belong")."',
                    '".data_get($item, "belongNm")."', '".data_get($item, "membId")."', '".json_encode($item)."', '$created_at'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItemStudent(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $query.= "('".data_get($item, "classSeq")."', '".data_get($item, "classNm")."', '".data_get($item, "classStat")."', '".data_get($item, "classStatNm")."', '".data_get($item, "belong")."',
                    '".data_get($item, "belongNm")."', '".data_get($item, "membId")."', '".json_encode($item)."', '$created_at'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function updateListTeacher($page){
        $token = self::getTokenLMS();
        if($token->status=='SUCCESS'){
            $token=$token->result->accessToken;
        }else{
            $token= "error";
        }
        $url = "https://lms-vn.cmsedu.net/api/v1/user/getTeacherList.do";
        $method = 'POST';
        $params = [
            "start" => 1000*$page+1,
            "length" => 1000,
            "cmsVnMembId"=> "cms.vn",
        ];
        $header = [
            "Authorization:$token",
        ];
        $resp = $this->callAPI($url, $method, $params, $header, 0, false);
        $list_teacher = data_get($resp, 'result.data');
        
        if(!empty($list_teacher)){
            $this->addItemTeacher($list_teacher);
        }
        return true;
    }

    public static function addItemTeacher($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO lms_teachers (membId, membNm, belong, belongNm, membStat, membStatNm, `data`, created_at) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query.= "('".data_get($item, "membId")."', '".html_entity_decode(data_get($item, "membNm"))."', '".data_get($item, "belong")."', '".html_entity_decode(data_get($item, "belongNm"))."', '".data_get($item, "membStat")."',
                    '".data_get($item, "membStatNm")."', '".json_encode($item)."', '$created_at'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItemTeacher(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $query.= "('".data_get($item, "membId")."', '".html_entity_decode(data_get($item, "membNm"))."', '".data_get($item, "belong")."', '".html_entity_decode(data_get($item, "belongNm"))."', '".data_get($item, "membStat")."',
                    '".data_get($item, "membStatNm")."', '".json_encode($item)."', '$created_at'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function updateInfoStudentLMSNoneCRMId(){
        $students = u::query("SELECT * FROM lms_students WHERE is_process=0 AND stuId=''");
        $token = self::getTokenLMS();
        if($token->status=='SUCCESS'){
            $token=$token->result->accessToken;
        }else{
            $token= "error";
        }
        $url = "https://lms-vn.cmsedu.net/api/v1/cntr/studentModAction.do";
        $method = 'POST';
        foreach($students AS $student){
            $data_info = json_decode(data_get($student, 'data'));

            $params = [
                "membId" => data_get($data_info, 'membId'),
                "classSeq" => (int)data_get($data_info, 'classSeq'),
                "intoDt" => date('Y-m-d', strtotime(data_get($data_info, 'regDate'))),
                "stuNm" => data_get($student, 'stuNm'),
                "stuBirthDt" => data_get($data_info, 'stuBirthDt'),
                "stuGen" => data_get($data_info, 'stuGender'),
                "startDt" => data_get($data_info, 'startDt'),
                "endDt" => data_get($data_info, 'expireDt'),
                "validCd" => data_get($data_info, 'validCd'),
                "stuStat" =>  'SS003',
                "hMembId" => data_get($data_info, 'regId'),
                "hStuSeq" => (int)data_get($data_info, 'stuSeq'),
                "hClassSeq"=> (int)data_get($data_info, 'classSeq'),
                "moveYn"=>'N',
                "stuId"=>data_get($data_info, 'stuId'),
            ];
            $header = [
                "Authorization:$token",
            ];
            $resp = $this->callAPI($url, $method, $params, $header, 0, false);
            u::query("UPDATE lms_students SET is_process=1 WHERE id= ".data_get($student, 'id'));
            echo data_get($student, 'id')."/";
        }
        return "ok";
    }

    public function updateInfoStudentLMSWithDraw(){
        $students = u::query("SELECT l.* 
            FROM
                lms_students AS l
                LEFT JOIN students AS s ON s.id_lms = l.stuSeq 
            WHERE
                ( SELECT count( id ) FROM contracts WHERE student_id = s.id AND class_id IS NOT NULL AND STATUS != 7 )=0
                AND is_process=0  AND l.stuStat!='SS003'");
        $token = self::getTokenLMS();
        if($token->status=='SUCCESS'){
            $token=$token->result->accessToken;
        }else{
            $token= "error";
        }
        $url = "https://lms-vn.cmsedu.net/api/v1/cntr/studentModAction.do";
        $method = 'POST';
        foreach($students AS $student){
            $data_info = json_decode(data_get($student, 'data'));

            $params = [
                "membId" => data_get($data_info, 'membId'),
                "classSeq" => (int)data_get($data_info, 'classSeq'),
                "intoDt" => date('Y-m-d', strtotime(data_get($data_info, 'regDate'))),
                "stuNm" => data_get($student, 'stuNm'),
                "stuBirthDt" => data_get($data_info, 'stuBirthDt'),
                "stuGen" => data_get($data_info, 'stuGender'),
                "startDt" => data_get($data_info, 'startDt'),
                "endDt" => data_get($data_info, 'expireDt'),
                "validCd" => data_get($data_info, 'validCd'),
                "stuStat" =>  'SS003',
                "hMembId" => data_get($data_info, 'regId'),
                "hStuSeq" => (int)data_get($data_info, 'stuSeq'),
                "hClassSeq"=> (int)data_get($data_info, 'classSeq'),
                "moveYn"=>'N',
                "stuId"=>data_get($data_info, 'stuId'),
            ];
            $header = [
                "Authorization:$token",
            ];
            $resp = $this->callAPI($url, $method, $params, $header, 0, false);
            u::query("UPDATE lms_students SET is_process=1 WHERE id= ".data_get($student, 'id'));
            echo data_get($student, 'id')."/";
        }
        return "ok";
    }

    public function updateInfoStudentLMSClass()
    {
        $students = u::query("SELECT s.id ,l.id AS lid
            FROM
                lms_students AS l
                LEFT JOIN students AS s ON s.id_lms = l.stuSeq 
            WHERE is_process=0 AND 
                ( SELECT count( id ) FROM contracts WHERE student_id = s.id AND class_id IS NOT NULL AND STATUS != 7 )>0 ");
        foreach($students AS $student){
            self::updateStudentLMS($student->id);
            echo data_get($student, 'id')."/";
            u::query("UPDATE lms_students SET is_process=1 WHERE id= ".data_get($student, 'lid'));
        }
        return "ok";
    }

    public function updateInfoStudentLMSInClass()
    {
        $students = u::query("SELECT c.student_id
            FROM
                contracts AS c
            WHERE c.status!=7 AND c.class_id=3734 ");
        foreach($students AS $student){
            self::updateStudentLMS($student->student_id);
            echo data_get($student, 'student_id')."/";
        }
        return "ok";
    }
}
