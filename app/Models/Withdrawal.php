<?php

namespace App\Models;

use App\Http\Controllers\TransferFeeController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;
use App\Http\Controllers\TransferFeeController as t;
use PhpParser\Node\Expr\Cast\Object_;

class Withdrawal extends Model
{
    private $DELETED = 4;
    private $WAITING = 0;
    private $APPROVED = 1;
    private $CANCELED = 2;
    private $COMPLETED = 3;
    protected $table = 'withdrawal_fees';
    public $timestamps = false;

    public function getList($request){

        $conditions = $this->filter($request);
        $limit = $this->limit($request);

        $query = "
            SELECT
                w.*,  
                s.cms_id,
                s.crm_id,
                s.accounting_id,
                s.name AS student_name,
                b.name AS branch_name,
                p.name AS product_name,
                pro.name AS program_name,
                cls.cls_name AS class_name,
                u1.full_name AS approver_name,
                u2.full_name AS refuner_name,
                u3.full_name AS creator_name
            FROM 
                withdrawal_fees AS w
                LEFT JOIN students AS s ON w.student_id = s.id
                LEFT JOIN contracts AS c ON w.contract_id = c.id
                LEFT JOIN branches AS b ON w.branch_id = b.id
                LEFT JOIN products AS p ON c.product_id = p.id
                LEFT JOIN programs AS pro ON c.program_id = pro.id
                LEFT JOIN classes AS cls ON c.class_id = cls.id
                LEFT JOIN users AS u1 ON u1.id = w.approver_id
                LEFT JOIN users AS u2 ON u2.id = w.refuner_id
                LEFT JOIN users AS u3 ON u3.id = w.creator_id
            $conditions->cond
            ORDER BY w.id DESC
            $limit
        ";
            //        echo($query);die;
            $result = DB::select(DB::raw($query));

            $total = 0;
            if(!empty($result)){
                $total = $this->countRecords($conditions);
            }

            $page = apax_get_pagination(json_decode($request->pagination), $total);
            if(!$total){
                $page->spage = 0;
                $page->ppage = 0;
                $page->npage = 0;
                $page->lpage = 0;
                $page->cpage = 0;
                $page->total = 0;
            }

            return [
                "items" => $result,
                "pagination" => $page
            ];
    }
    private function countRecords($conditions){
        $query = "
               SELECT
                    COUNT(w.id) AS count_item
                FROM
                    withdrawal_fees AS w
                    LEFT JOIN students AS s ON w.student_id = s.id
                $conditions->cond
            ";

        $res = DB::select(DB::raw($query));
        if(!empty($res)){
            return $res[0]->count_item;
        }else{
            return 0;
        }
    }
    private function filter($request){
        if(isset($request->filter)){
            try{
                $filter = json_decode($request->filter);
            }catch (\Exception $e){
                return (Object)[
                    "cond" => "",
                    "branch_ids" => "0"
                ];
            }

            $conditions = [];

            if(isset($filter->cms_id)){
                $value = trim($filter->cms_id);
                if(is_numeric($value)){
                    $id = (int)$value;
                    $conditions[] = "(s.cms_id = $id OR s.cms_id LIKE '%$id%' OR s.crm_id = $id OR s.crm_id LIKE '%$id%')";
                }else{
                    $conditions[] = "(s.cms_id LIKE '%$value%' OR crm_id LIKE '%$value%' OR s.accounting_id = '$value')";
                }
            }

            $student_name = isset($filter->student_name)?trim($filter->student_name):'';
            if($student_name){
                $student_name = strtoupper($student_name);
                $conditions[] = "s.name LIKE '%$student_name%'";
            }

            $branch_ids_string = "0";
            if(isset($filter->branch_id) && is_numeric($filter->branch_id)){
                $branch_id = (int)$filter->branch_id;
                if($branch_id !== 0){
                    $conditions[] = "w.branch_id = " . $filter->branch_id;
                }else{
                    $branch_ids = u::getBranchIds($request->users_data);
                    if(!empty($branch_ids)){
                        $branch_ids_string = implode(',',$branch_ids);
                        $conditions[] = "w.branch_id IN ($branch_ids_string)";
                    }
                }
            }else{
                $branch_ids = u::getBranchIds($request->users_data);
                if(!empty($branch_ids)){
                    $branch_ids_string = implode(',',$branch_ids);
                    $conditions[] = "w.branch_id IN ($branch_ids_string)";
                }
            }

            if(isset($filter->status) && is_numeric($filter->status)){
                $status = (int)$filter->status;
                if($status === 0 || $status === 1 || $status === 2 || $status === 3){
                    $conditions[] = "w.status = $status";
                }
            }

            if(!empty($conditions)){
                return (Object)[
                    "cond" => " WHERE " . implode(" AND ",$conditions),
                    "branch_ids" => $branch_ids_string
                ];
            }else{
                return (Object)[
                    "cond" => "",
                    "branch_ids" => "$branch_ids_string"
                ];
            }
        }else{
            return (Object)[
                "cond" => "",
                "branch_ids" => "0"
            ];
        }
    }

    private function limit($request){
        $limit = '';
        $pagination = json_decode($request->pagination);
        if ($pagination->cpage && $pagination->limit) {
            $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
            $limit.= " LIMIT $offset, $pagination->limit";
        }

        return $limit;
    }
    public function getBranchIds($branches){
        $branch_ids = [];
        foreach ($branches as $branch) {
            $branch_ids[] = $branch->branch_id;
        }
        return $branch_ids;
    }
    public function getWithdrawalRequests($request){
        $conditions = [];
        $branch_ids = $this->getBranchIds($request->users_data->roles_detail);
        $condition_string = '';
        if(!empty($branch_ids)){
            $branch_ids_string = implode(',',$branch_ids);
            $conditions[] = "w.branch_id IN ($branch_ids_string)";
        }

        if(!empty($conditions)){
            $condition_string = ' AND (' . implode(' AND ',$conditions) . ')';
        }

        $query = "
            SELECT
                w.*,
                s.cms_id,
                s.crm_id,
                s.accounting_id,
                s.name AS student_name,
                b.name AS branch_name,
                p.name AS product_name,
                pro.name AS program_name,
                cls.cls_name AS class_name,
                u1.full_name AS approver_name,
                u2.full_name AS refuner_name,
                u3.full_name AS creator_name
            FROM
                withdrawal_fees AS w
                LEFT JOIN students AS s ON w.student_id = s.id
                LEFT JOIN contracts AS c ON w.contract_id = c.id
                LEFT JOIN branches AS b ON w.branch_id = b.id
                LEFT JOIN products AS p ON c.product_id = p.id
                LEFT JOIN programs AS pro ON c.program_id = pro.id
                LEFT JOIN classes AS cls ON c.class_id = cls.id
                LEFT JOIN users AS u1 ON u1.id = w.approver_id
                LEFT JOIN users AS u2 ON u2.id = w.refuner_id
                LEFT JOIN users AS u3 ON u3.id = w.creator_id
            WHERE
                w.status = 0
                $condition_string
            ORDER BY w.id DESC
        ";

        $result = DB::select(DB::raw($query));
        return $result;

    }
    public function getWithdrawalRequestsRefun($request){
        $conditions = [];
        $branch_ids = $this->getBranchIds($request->users_data->roles_detail);
        $condition_string = '';
        if(!empty($branch_ids)){
            $branch_ids_string = implode(',',$branch_ids);
            $conditions[] = "w.branch_id IN ($branch_ids_string)";
        }

        if(!empty($conditions)){
            $condition_string = ' AND (' . implode(' AND ',$conditions) . ')';
        }

        $query = "
            SELECT
                w.*,
                s.cms_id,
                s.crm_id,
                s.name AS student_name,
                b.name AS branch_name,
                p.name AS product_name,
                pro.name AS program_name,
                cls.cls_name AS class_name,
                u1.full_name AS approver_name,
                u2.full_name AS refuner_name,
                u3.full_name AS creator_name
            FROM
                withdrawal_fees AS w
                LEFT JOIN students AS s ON w.student_id = s.id
                LEFT JOIN contracts AS c ON w.contract_id = c.id
                LEFT JOIN branches AS b ON w.branch_id = b.id
                LEFT JOIN products AS p ON c.product_id = p.id
                LEFT JOIN programs AS pro ON c.program_id = pro.id
                LEFT JOIN classes AS cls ON c.class_id = cls.id
                LEFT JOIN users AS u1 ON u1.id = w.approver_id
                LEFT JOIN users AS u2 ON u2.id = w.refuner_id
                LEFT JOIN users AS u3 ON u3.id = w.creator_id
            WHERE
                w.status = 1
                $condition_string
            ORDER BY w.id DESC
        ";

                $result = DB::select(DB::raw($query));
                return $result;

    }
    public function deny($id, $comment, $approver_id){
        $withdraw = $this->find($id);
        $withdraw->status = $this->CANCELED;
        $withdraw->comment = $comment;
        $withdraw->approved_at = now();
        $withdraw->approver_id = $approver_id;


        if($withdraw->save()){
            $this->sendDenyMail($withdraw);
            return APICode::SUCCESS;
        }else{
            return false;
        }
    }
    public function approve($id, $request){
        $withdraw = $this->find($id);
        $withdraw->status = $this->APPROVED;
        $withdraw->approver_id = $request->users_data->id;
        $withdraw->approved_at = now();

        if($withdraw->save()){
            $this->sendConfirmMail($withdraw);
            return APICode::SUCCESS;
        }else{
            return APICode::SERVER_CONNECTION_ERROR;
        }
    }
    public function refun($id, $request){
        $withdraw = $this->find($id);
        $withdraw->status = $this->COMPLETED;
        $withdraw->refuner_id = $request->users_data->id;
        $withdraw->completed_at = now();

        if($withdraw->save()){
            $arr_contract =json_decode($withdraw->meta_data);
            foreach ($arr_contract AS $contract){
                $contract_info = DB::table('contracts')->where('id',$contract->id)->first();
                if($contract_info && $contract_info->class_id!=NULL){
                    $enrolment_last_date = $withdraw->withdraw_date;
                }else{
                    $enrolment_last_date = NULL;
                }

                $total_charged = (int) $withdraw->fee_amount;
                if($contract->status == 6 && (int) $contract->real_sessions>0){
                  $holidays = u::getPublicHolidays($contract->class_id);
                  $classdays = u::getClassDays($contract->class_id);
                  $session = u::calSessions($contract->enrolment_start_date, $withdraw->withdraw_date, $holidays, $classdays);
                  if($session){
                    $total = $session->total?:0;
                    $total_charged += ((int)$contract->total_charged/(int)$contract->real_sessions)*(int)$total;
                  }
                }

                DB::table('contracts')->where('id', $contract->id)
                ->update(
                    [
                        'status' => 8,
                        'enrolment_withdraw_reason' =>  "Đã rút phí",
                        'updated_at' => now(),
                        'real_sessions'=>$contract->done_sessions,
                        'end_date'=>$withdraw->withdraw_date,
                        'enrolment_withdraw_date'=>$withdraw->withdraw_date,
                        'enrolment_last_date'=>$enrolment_last_date,
                        'enrolment_real_sessions'=>$withdraw->done_sessions,
                        'total_charged' => $total_charged
                    ]
                );
            }
            $this->sendApproveMail($withdraw);
            return APICode::SUCCESS;
        }else{
            return APICode::SERVER_CONNECTION_ERROR;
        }
    }
    public function refunDeny($id, $comment, $refuner_id){
        $withdraw = $this->find($id);
        $withdraw->status = $this->DELETED;
        $withdraw->comment = $comment;
        $withdraw->completed_at = now();
        $withdraw->refuner_id = $refuner_id;


        if($withdraw->save()){
            $this->sendDenyMail($withdraw);
            return APICode::SUCCESS;
        }else{
            return false;
        }
    }
    public function sendRequirementMail($transfer){
        $approvableRole = [ROLE_OM, ROLE_BRANCH_CEO];
        $approvableRoleString = implode(',',$approvableRole);
        $approver = u::query("SELECT u.`full_name`, u.id, u.`email`, r.`name` AS role_name
                                    FROM
                                        users AS u
                                        LEFT JOIN term_user_branch AS t ON u.id = t.`user_id`
                                        LEFT JOIN roles AS r ON t.`role_id` = r.`id`
                                    WHERE
                                        t.`role_id` IN ($approvableRoleString)
                                        AND t.`branch_id` = $transfer->branch_id
                                        AND t.status = 1
                                    GROUP BY u.id");

        $from_student = u::first("SELECT s.`name`, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $transfer->student_id");

        if(!empty($approver)){
            if (APP_ENV == 'staging'){
                $approver = [
                    (Object)[
                        "email" => Mail::STAGING_EMAIL,
                        "full_name" => Mail::STAGING_FULL_NAME,
                        "role_name" => "Quản trị viên"
                    ]
                ];
            }elseif (APP_ENV == 'develop'){
                $approver = [
                    (Object)[
                        "email" => Mail::DEVELOP_EMAIL,
                        "full_name" => Mail::DEVELOP_FULL_NAME,
                        "role_name" => "Quản trị viên"
                    ]
                ];
            }

            foreach ($approver as $item) {
                $mail = new Mail();

                $to = [
                    'address' => $item->email,
                    'name' => $item->full_name
                ];
                $subject = "[Hệ thống CRM - CMS] Yêu cầu phê duyệt rút phí của bé $from_student->name";
                $body = "
                    <p>Kính gửi: $item->full_name - $item->role_name</p>
                    
                    <p>Hệ thống CRM - CMS xin thông báo: Anh/chị đã nhận được yêu cầu <strong>rút phí</strong> của bé <strong>$from_student->name</strong> [$from_student->branch_name]</p>
                    
                    <p>Nội dung: $transfer->comment_created</p>
                    
                    <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM - CMS để phê duyệt yêu cầu.</p>
                    
                    <p>Hoặc truy cập link: <a href='http://crm.cmsedu.vn'>crm.cmsedu.vn</a> để xem chi tiết yêu cầu</p>
                    
                    <p>Trân trọng cảm ơn!</p>
                ";
                $mail->sendSingleMail($to, $subject, $body);
            }
        }
    }
    private function sendConfirmMail($transfer){
        $approvableRole = [84];
        $approvableRoleString = implode(',',$approvableRole);
        $approver = u::query("SELECT u.`full_name`, u.id, u.`email`, r.`name` AS role_name
                                    FROM
                                        users AS u
                                        LEFT JOIN term_user_branch AS t ON u.id = t.`user_id`
                                        LEFT JOIN roles AS r ON t.`role_id` = r.`id`
                                    WHERE
                                        t.`role_id` IN ($approvableRoleString)
                                        AND t.`branch_id` = $transfer->branch_id
                                        AND t.status = 1
                                    GROUP BY u.id");

        $from_student = u::first("SELECT s.`name`, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $transfer->student_id");

        if(!empty($approver)){
            if (APP_ENV == 'staging'){
                $approver = [
                    (Object)[
                        "email" => Mail::STAGING_EMAIL,
                        "full_name" => Mail::STAGING_FULL_NAME,
                        "role_name" => "Quản trị viên"
                    ]
                ];
            }elseif (APP_ENV == 'develop'){
                $approver = [
                    (Object)[
                        "email" => Mail::DEVELOP_EMAIL,
                        "full_name" => Mail::DEVELOP_FULL_NAME,
                        "role_name" => "Quản trị viên"
                    ]
                ];
            }

            foreach ($approver as $item) {
                $mail = new Mail();

                $to = [
                    'address' => $item->email,
                    'name' => $item->full_name
                ];

                $subject = "[Hệ thống CRM - CMS] Yêu cầu phê duyệt rút phí của bé $from_student->name";
                $body = "
                    <p>Kính gửi: $item->full_name - $item->role_name</p>
                    
                    <p>Hệ thống CRM - CMS xin thông báo: Anh/chị đã nhận được yêu cầu <strong>rút phí</strong> của bé <strong>$from_student->name</strong> [$from_student->branch_name] </p>
                    
                    <p>Nội dung: $transfer->comment_created</p>
                    
                    <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM - CMS để phê duyệt yêu cầu.</p>
                    
                    <p>Hoặc truy cập link: <a href='http://crm.cmsedu.vn'>crm.cmsedu.vn</a> để xem chi tiết yêu cầu</p>
                    
                    <p>Trân trọng cảm ơn!</p>
                ";
                $mail->sendSingleMail($to, $subject, $body);
            }
        }
    }
    private function sendApproveMail($transfer){
        $creator = u::query("SELECT u.`full_name`, u.id, u.`email`, r.`name` AS role_name
                                    FROM
                                        users AS u
                                        LEFT JOIN term_user_branch AS t ON u.id = t.`user_id`
                                        LEFT JOIN roles AS r ON t.`role_id` = r.`id`
                                    WHERE
                                        u.id = $transfer->creator_id
                                    GROUP BY u.id");

        $from_student = u::first("SELECT s.`name`, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $transfer->student_id");

        if(!empty($creator)){
            if (APP_ENV == 'staging'){
                $creator = [
                    (Object)[
                        "email" => Mail::STAGING_EMAIL,
                        "full_name" => Mail::STAGING_FULL_NAME
                    ]
                ];
            }elseif (APP_ENV == 'develop'){
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

                $subject = "[Hệ thống CRM - CMS] Phê duyệt thành công rút phí của bé $from_student->name";
                $body = "
                    <p>Kính gửi: Phòng CS</p>
                    
                    <p>Hệ thống CRM - CMS xin thông báo: Yêu cầu <strong>rút phí</strong> của bé <strong>$from_student->name</strong> [$from_student->branch_name] đã được phê duyệt thành công</p>
                    
                    <p>Nội dung: $transfer->note</p>
                    
                    <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM - CMS để xem chi tiết.</p>
                    
                    <p>Trân trọng cảm ơn!</p>
                ";
                $mail->sendSingleMail($to, $subject, $body);
            }
        }
    }

    private function sendDenyMail($transfer){
        $creator = u::query("SELECT u.`full_name`, u.id, u.`email`, r.`name` AS role_name
                                    FROM
                                        users AS u
                                        LEFT JOIN term_user_branch AS t ON u.id = t.`user_id`
                                        LEFT JOIN roles AS r ON t.`role_id` = r.`id`
                                    WHERE
                                        u.id = $transfer->creator_id
                                    GROUP BY u.id");

        $from_student = u::first("SELECT s.`name`, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $transfer->student_id");

        if(!empty($creator)){
            if (APP_ENV == 'staging'){
                $creator = [
                    (Object)[
                        "email" => Mail::STAGING_EMAIL,
                        "full_name" => Mail::STAGING_FULL_NAME
                    ]
                ];
            }elseif (APP_ENV == 'develop'){
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

                $subject = "[Hệ thống CRM - CMS] Từ chối rút phí của bé $from_student->name";
                $body = "
                    <p>Kính gửi: Phòng CS</p>
                    
                    <p>Hệ thống CRM - CMS xin thông báo: Yêu cầu <strong>rút phí</strong> của bé <strong>$from_student->name</strong> [$from_student->branch_name] đã bị từ chối phê duyệt </p>
                    
                    <p>Lý do từ chối: $transfer->comment</p>
                    
                    <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM - CMS để xem chi tiết.</p>
                    
                    <p>Trân trọng cảm ơn!</p>
                ";
                $mail->sendSingleMail($to, $subject, $body);
            }
        }
    }
}
