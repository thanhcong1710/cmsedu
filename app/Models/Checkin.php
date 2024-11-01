<?php

namespace App\Models;

use App\Http\Controllers\JobsController;
use Illuminate\Database\Eloquent\Model;
use App\Providers\UtilityServiceProvider as u;

class Checkin extends Model
{
    protected $table = 'student_checkin';

    public function getStudentInfo($filter,$select_limit=true){
        $auth = $filter->users_data;
        $userId = $auth->id;
        $status = ($filter->act && $filter->act == "checked") ? 1 : 0;
        $role_id = $auth->role_id;
        $pagination = json_decode($filter->pagination);
        $sort = json_decode($filter->sort);
        $search = json_decode($filter->search);
        $order = '';
        $limit = '';
        $where = $status ?' s.status > 0' :' s.status = 0 AND s.checked!=2';
        $select = '';
        if ($select_limit && $sort->by && $sort->to ) {
            $order .= " ORDER BY $sort->by $sort->to";
        }else{
            $order .= " ORDER BY s.id DESC"; 
        }
        if ($select_limit && $pagination->cpage && $pagination->limit) {
            $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
            $limit .= " LIMIT $offset, $pagination->limit";
        }
        if(!$select_limit ){
            $limit ="";
        }
        $branches = $search->branch ? (int)$search->branch : $auth->branches_ids;
        $where .= " AND s.branch_id IN ($branches)";
//        if($role_id){
//            if ($role_id == ROLE_EC_LEADER) {
//                $where .= " AND (u1.id = $user_id OR t.ec_id = $user_id OR t.ec_id IN (SELECT u1.id FROM users u1 LEFT JOIN users u2 ON u1.superior_id = u2.hrm_id WHERE u2.id = $user_id))";
//            }
//            if ($role_id == ROLE_EC) {
//                $where .= " AND t.ec_id = $user_id";
//            }
//        }
       
        if($role_id == 80){
            $where .= " AND (s.creator_id  = $userId OR s.creator_id in (SELECT user_id FROM `term_user_branch` WHERE role_id = 80)) ";
        }elseif ($role_id == 81){
            $where .= " AND (s.creator_id  = $userId OR s.creator_id in (SELECT user_id FROM `term_user_branch` WHERE (role_id = 80 OR role_id=81))) ";
        }elseif($role_id==1200){
            $where .= " AND s.source IN(26)";
        }elseif($role_id==1300){
            $where .= " AND s.source IN(32)";
        }
        if ($filter->act && $filter->act == "checked"){
            $where .= " AND s.id IN (SELECT student_id as id FROM `active_checkin` WHERE branch_id IN ($branches)) ";
        }
//        else{
//            $where .= " AND s.branch_id IN ($branches)";
//        }
        // if ($search->gender && $search->gender != '') {
        //     $where .= " AND s.gender = '$search->gender'";
        // }
        if (isset($search->type) && $search->type != '') {
            $where .= " AND s.type = $search->type";
        }
        if (isset($search->ec) && $search->ec != '') {
            $where .= " AND t.ec_id = '$search->ec'";
        }
        if (isset($search->status) && $search->status != -1) {
            if($search->status==5){
                $where .= " AND s.checked=1";
            }else{
                $where .= " AND (SELECT count(t.student_id) FROM `transfer_checkin` t WHERE t.`student_id` = s.id AND t.status=$search->status )>0 ";
            }
        }
        if (isset($search->source) && $search->source != '') {
            $where .= " AND s.source = '$search->source'";
        }
        if (isset($search->startDate) && $search->startDate != '') {
            $where .= " AND DATE_FORMAT(s.created_at,'%Y-%m-%d') >= '$search->startDate'";
        }
        if (isset($search->endDate) && $search->endDate != '') {
            $where .= " AND DATE_FORMAT(s.created_at,'%Y-%m-%d') <= '$search->endDate'";
        }
        if (isset($search->startDateCheckin) && $search->startDateCheckin != '') {
            $where .= " AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') >= '$search->startDateCheckin'";
        }
        if (isset($search->endDateCheckin) && $search->endDateCheckin != '') {
            $where .= " AND DATE_FORMAT(s.checkin_at,'%Y-%m-%d') <= '$search->endDateCheckin'";
        }
        $keyword = isset($search->keyword) ? trim($search->keyword) : '';
        if (isset($keyword) && $keyword != '') {
            $where .= " AND
                      ( s.name LIKE '%$keyword%'
                      OR s.gud_mobile1 LIKE '%$keyword%'
                      OR s.gud_mobile2 LIKE '%$keyword%'
                      OR s.gud_name1 LIKE '%$keyword%'
                      OR s.crm_id LIKE '%$keyword%')";
        }
        $keyword_creator = isset($search->keyword_creator) ? trim($search->keyword_creator) : '';
        if (isset($keyword_creator) && $keyword_creator != '') {
            $where .= " AND s.creator_id =$keyword_creator";
        }
        if (in_array($role_id, [686868,676767,'7777777',55,56,68,69,'999999999'])&& !$filter->act){
            $cond = isset($search->status) && $search->status != -1 ? " AND `status`= $search->status" : "";
            $tmp_where = str_replace("AND s.branch_id IN ($branches)","","$where"); 
            $where = " ($where) OR (SELECT count(student_id) FROM `transfer_checkin` WHERE (to_branch_id IN($branches) OR from_branch_id IN($branches)) AND student_id=s.id $cond AND $tmp_where)>0 ";
        }
        $where = " WHERE $where";
        
        $total = "SELECT COUNT(DISTINCT(s.id)) total";
        $select = "SELECT s.id,s.crm_id,s.name,s.accounting_id,s.gud_mobile1,s.school,s.branch_id,s.gender,s.address,s.date_of_birth,s.status,s.checked,s.checkin_at,s.shift_id,s.created_at, gud_name1 as gud_name,s.type_product,
                    (select ec_id from `term_student_user` where student_id = s.id AND `status`=1 LIMIT 1) ec_id,
                    (select cm_id from `term_student_user` where student_id = s.id AND `status`=1 LIMIT 1) cm_id,
                    (select cm_name from `active_checkin` where student_id = s.id ORDER BY id DESC LIMIT 1) cm_name_active,
                    (select full_name from users where id = ec_id) as ec_name,
                    (select full_name from users where id = cm_id) as cm_name,
                    (select full_name from users where id = s.creator_id) as creator_name,
                    (select name from sources where id = s.source) as source_name,
                    (select name from branches where id = s.branch_id) as branch_name,
                    (SELECT
                    CONCAT(\"Học sinh: <b>\",(SELECT NAME FROM students WHERE id = t.`student_id`),
                    \" </b><br/>sẽ được chuyển từ <br/>\",
                    (SELECT NAME FROM branches WHERE id = t.`from_branch_id`),
                    \" <br/>tới <br/>\",
                    (SELECT NAME FROM branches WHERE id = t.`to_branch_id`))
                     FROM `transfer_checkin` t WHERE t.student_id = s.id ORDER BY t.id DESC LIMIT 1) AS modal,
                     (SELECT t.`from_branch_id` FROM `transfer_checkin` t WHERE t.`student_id` = s.id ORDER BY t.id DESC LIMIT 1) AS from_branch_id,
                    (SELECT t.`to_branch_id` FROM `transfer_checkin` t WHERE t.`student_id` = s.id ORDER BY t.id DESC LIMIT 1) AS to_branch_id,
                    (SELECT b.name FROM `transfer_checkin` t  LEFT JOIN branches AS b ON b.id= t.`to_branch_id` WHERE t.`student_id` = s.id  ORDER BY t.id DESC LIMIT 1) AS to_branch_name,
                    (SELECT t.`status` FROM `transfer_checkin` t WHERE t.`student_id` = s.id  ORDER BY t.id DESC LIMIT 1) AS status_transfer,
                    (SELECT t.`from_approver_id` FROM `transfer_checkin` t WHERE t.`student_id` = s.id  ORDER BY t.id DESC LIMIT 1) AS from_approver_id,
                    (SELECT t.`to_approver_id` FROM `transfer_checkin` t WHERE t.`student_id` = s.id  ORDER BY t.id DESC LIMIT 1) AS to_approver_id";
        $query = " FROM students s";
        $final_query = "$select $query $where $order $limit";
        $data = [];
        $i_total = 0;
        // try{
            $data = u::query($final_query);

            $o_total = u::first("$total FROM students s $where");
            $i_total = $o_total->total;

        // }catch(Exception $ex){ throw $ex;}
        foreach($data AS $k=>$row){
            if($filter->users_data->role_id != '999999999'&& $filter->users_data->id != '140' && $filter->users_data->role_id != '80' && $filter->users_data->role_id != '81'){
                $data[$k]->gud_mobile1 = str_replace(substr($row->gud_mobile1,4,3),'***',$row->gud_mobile1);
            }
        }
        $result = (Object)['data' => $data, 'total' => $i_total];
        return $result;
    }

    public function create($data = [], $creatorId = 0){
        $school = $data["school_level"]." ".$data["school"];
        $this->cms_id = '';
        $this->name = $data['name'];
        $this->gender = $data['gender'];
        $this->type = $data['type'];
        $this->date_of_birth = $data['date_of_birth'];
        $this->gud_mobile1 = $data['gud_mobile1'];
        $this->gud_name1 = $data['gud_name1'];
        $this->gud_email1 = isset($data['gud_email1']) ? $data['gud_email1'] : null;
        $this->address = $data['address'];
        $this->province_id = $data['province_id'];
        $this->district_id = $data['district_id'];
        $this->school = $school;
        $this->created_at = NOW();
        $this->creator_id = $creatorId;
        $this->note = $data['note'];
        $this->branch_id = $data['branch_id'];
        $this->source = $data['source'];
        $this->partner_code = $data['partner_code'];
        $this->ec_id = $data['ec_id'];
        $this->cm_id = $data['cm_id'];
        $this->trial_date = $data['trial_date'];
        $this->class_id = $data['class_id'];

        $this->school_level = $data['school_level'];
        unset($data['district']['created_at']);
        unset($data['district']['updated_at']);
        unset($data['district']['accounting_id']);
        $this->district = json_encode($data['district']);
        $this->province = json_encode($data['province']);
        $this->gud_title = $data['gud_title'];
        $this->gud_firstname1 = $data['gud_firstname1'];
        $this->gud_midname1 = $data['gud_midname1'];
        $this->gud_lastname1 = $data['gud_lastname1'];

        $this->save();
        $lastInsertedId = $this->id;
        $cms_id = 'HS2' . str_pad((string)$lastInsertedId, 6, '0', STR_PAD_LEFT);
        u::query("UPDATE student_checkin SET cms_id = '$cms_id' WHERE id = $lastInsertedId");

    }

    public function updateStudent($data = [], $creatorId = 0, $transfer = false){
        $id = (int)$data['id'];
        $student = Student::find($id);
        $pre_checkin = $student->checkin_at;
        $pre_branch_id = $student->branch_id;
        $student->updated_at = NOW();
        $student->editor_id =$creatorId;

        if (isset($data['date_of_birth']) && $data['date_of_birth'] != "Invalid date"){
            $student->date_of_birth = $data['date_of_birth'];
        }
        $ec_id = (int)$data['ec_id'];
        $cm_id = (int)$data['cm_id'];
        $branch_id = (int)$data['branch_id'];
        if (!$transfer){
            $sibling_id = null;
            if (isset($data['sibling_id']) && $data['sibling_id'] !='') {
                $sib_id = (int)str_replace('CMS', '', $data['sibling_id']);
                $sib_cd = $sib_id - 20000000;
                $sib = u::first("SELECT s.id FROM students s WHERE id = $sib_cd");
                $sibling_id = $sib && isset($sib->id) ? $sib->id : null;
            }

            $student->sibling_id = $sibling_id;
            $student->name = $data['name'];
            $student->gender = $data['gender'];
            $student->type = $data['type'];
            $student->gud_mobile1 = $data['gud_mobile1'];
            $student->gud_mobile2 = $data['gud_mobile2'];
            $student->gud_name1 = $data['gud_name1'];
            $student->gud_email1 = isset($data['gud_email1']) ? $data['gud_email1'] : null;
            $student->gud_birth_day1 = isset($data['gud_birth_day1']) ? $data['gud_birth_day1'] : null;
            $student->gud_birth_day2 = isset($data['gud_birth_day2']) ? $data['gud_birth_day2'] : null;
            $student->gud_gender1 = isset($data['gud_gender1']) ? $data['gud_gender1'] : null;
            $student->gud_gender2 = isset($data['gud_gender2']) ? $data['gud_gender2'] : null;
            $student->gud_job1 = isset($data['gud_job1']) ? $data['gud_job1'] : null;
            $student->gud_job2 = isset($data['gud_job2']) ? $data['gud_job2'] : null;
            $student->gud_firstname2 = isset($data['gud_firstname2']) ? $data['gud_firstname2'] : null;
            $student->gud_lastname2 = isset($data['gud_lastname2']) ? $data['gud_lastname2'] : null;
            $student->gud_midname2 = isset($data['gud_midname2']) ? $data['gud_midname2'] : null;
            $student->gud_firstname1 = isset($data['gud_firstname1']) ? $data['gud_firstname1'] : null;
            $student->gud_lastname1 = isset($data['gud_lastname1']) ? $data['gud_lastname1'] : null;
            $student->gud_midname1 = isset($data['gud_midname1']) ? $data['gud_midname1'] : null;
            $student->firstname = isset($data['firstname']) ? $data['firstname'] : null;
            $student->lastname = isset($data['lastname']) ? $data['lastname'] : null;
            $student->midname = isset($data['midname']) ? $data['midname'] : null;
            $student->address = $data['address'];
            $student->province_id = $data['province_id'];
            $student->district_id = $data['district_id'];
            $student->school = $data["school"];;
            $student->school_level = $data["school_level"];;
            $student->school_grade = $data["school_grade"];;
            $student->note = $data['note'];
            $student->source = $data['source'];
            $student->shift_id = $data['shift_id'];
            $student->checkin_at = $data['checkin_at'];
            u::query("UPDATE `customer_care` c SET c.`crm_id` ='{$student->crm_id}' WHERE c.`std_temp_id` IN (SELECT s.id FROM `student_temp` s WHERE s.`gud_mobile1` = '{$data['gud_mobile1']}')");
        }
        $student->branch_id = $branch_id;
        $student->save();
        $tmp_role = u::first("SELECT role_id FROM term_user_branch WHERE user_id=$student->creator_id AND `status`=1");
        if($tmp_role && in_array($tmp_role->role_id,[80,81,7676767]) && date('Y-m-d',strtotime($student->checkin_at))==date('Y-m-d') && ($pre_checkin != $student->checkin_at || $pre_branch_id !=$student->branch_id ) && (date('H')>=14 || date('w')==6 ||date('w')==0)){
            JobsController::sendMailSalehub($student->branch_id, $student->id);
        }elseif(date('H')>=14 && (date('w')==5 || date('w')==6) && in_array($tmp_role->role_id,[80,81,7676767]) && date('Y-m-d',strtotime($student->checkin_at))==date('Y-m-d',time()+24*3600) && ($pre_checkin != $student->checkin_at || $pre_branch_id !=$student->branch_id )){
            JobsController::sendMailSalehub($student->branch_id, $student->id);
        }
        /*
        if (isset($data['new_branch_id']) && $data['new_branch_id'] >0){
            $new_ec_id =  $data['new_ec_id'];
            $new_cs_id =  $data['new_cs_id'];
            $this->addTransferCheckin($id, $data,$creatorId,$new_ec_id,$new_cs_id);
        }
        */
        $ecLeader = u::first("SELECT u2.id as ec_leader_id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $ec_id");
        $csLeader = u::first("SELECT u2.id as cs_leader_id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $cm_id");

        $ecLeaderId = ($ecLeader && $ecLeader->ec_leader_id >0) ? $ecLeader->ec_leader_id : $ec_id;
        $csLeaderId = ($csLeader && $csLeader->cs_leader_id >0) ? $csLeader->cs_leader_id : $cm_id;

        u::query("UPDATE term_student_user SET branch_id = $branch_id, cm_id = '$cm_id', ec_id = '$ec_id',ec_leader_id = '$ecLeaderId', om_id = '$csLeaderId' WHERE student_id = '$id'");
        $content = "Cập nhật HS checkin: ".$student->name .", CRM_ID: ". $student->crm_id;
        Checkin::addLogStudentUpdate($id, $content, $cm_id,$creatorId,$branch_id);
    }

    public function addTransferCheckin($id, $data = [],$creatorId,$new_ec_id,$new_cs_id, $ecId, $cmId){
        $sql = "insert into `transfer_checkin` (`from_ec_id`,`from_cm_id`,`to_ec_id`,`to_cm_id`,`student_id`, `from_branch_id`, `to_branch_id`, `creator_id`, `created_at`)";
        $sql .="values('$ecId','$cmId','$new_ec_id','$new_cs_id',$id,{$data['branch_id']},{$data['new_branch_id']},$creatorId,NOW())";
        u::query($sql);
    }

    public static function addLogStudentUpdate($lastInsertedId, $content, $cm_id,$uid, $branch_id){
        $add = new LogStudentUpdate();
        $add->student_id = $lastInsertedId;
        $add->updated_by = $uid;
        $add->updated_at = NOW();
        $add->content = $content;
        $add->cm_id = $cm_id;
        $add->status = 1;
        $add->branch_id = $branch_id;
        $add->ceo_branch_id = 0;
        $add->save();
    }
}
