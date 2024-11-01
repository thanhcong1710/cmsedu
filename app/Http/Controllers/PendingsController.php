<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use App\Models\Pending;
use App\Models\APICode;
use App\Models\Response;
use App\Models\Mail;
use App\Providers\UtilityServiceProvider as u;



class PendingsController extends Controller
{
    public function suggest(Request $request, $key, $branch_id){
        $user_data = $request->users_data;
        $response = new Response();
        $code = APICode::SUCCESS;
        $res = null;

        if($user_data){
            $branch_ids = u::getBranchIds($user_data);
            if(in_array($branch_id,$branch_ids)){
                $pendingModel = new Pending();
                $res = $pendingModel->getSuggestions($key,$branch_id);
            }else{
                $code = APICode::PERMISSION_DENIED;
            }
        }else{
            $code = APICode::PERMISSION_DENIED;
        }

        return $response->formatResponse($code,$res);
    }

    public function create(Request $data){
        $response = new Response();
        $res = null;

        $reserve = new Pending();
        $reserve->student_id = $data->student_id;

        $reserve->start_date = $data->start_date;
        $reserve->session = $data->session;
        $reserve->end_date = $data->end_date;

        $reserve->branch_id = $data->branch_id;
        $reserve->program_id = $data->program_id;
        $reserve->product_id = $data->product_id;

        $reserve->new_end_date = $data->new_end_date;
        $reserve->creator_id = $data->users_data->id;
        $reserve->created_at = date('Y-m-d H:i:s');
        $reserve->meta_data = json_encode($data->meta_data);
        $reserve->creator_id = $data->users_data->id;
        $reserve->reason_id = $data->reason_id;
        $reserve->contract_id = $data->contract_id;
        $reserve->attached_file = '';

        if ($data->attached_file) {
            $reserve->attached_file = ada()->upload($data->attached_file);
        }

        if($reserve->save()){
            $this->sendRequirementMail($reserve);
            return $response->formatResponse(APICode::SUCCESS,$res);
        }else{
            return $response->formatResponse(APICode::SERVER_CONNECTION_ERROR,$res);
        }
    }

    public function getList(Request $request){
        $response = new Response();
        $pendingModel = new Pending();
        $code = APICode::SUCCESS;

        $user_data = $request->users_data;
        $res = null;

        if($user_data) {
            $res = $pendingModel->getList($request);
        }else{
            $code = APICode::PERMISSION_DENIED;
        }

        return $response->formatResponse($code, $res);
    }

    public function getListByUserID($id){
//        var_dump($user_id);die;
        $response = new Response();
        $pendingModel = new Pending();

        $res = $pendingModel->getPendingsByUserId($id);
        return $response->formatResponse(APICode::SUCCESS, $res);
    }

    public function getRequests(Request $request){
        $user_data = $request->users_data;
        $response = new Response();
        if($user_data){
            $pendingModel = new Pending();
            $pending_regulation = $pendingModel->getPendingRegulation();

            $roles_detail = $user_data->roles_detail;

            $hasPermission = false;
            $conditions = [];

            foreach ($roles_detail as $item){
                if(isset($pending_regulation[$item->role_id])){
                    $hasPermission = true;
                    $max_session = $pending_regulation[$item->role_id]->max_days;
                    $min_days = $pending_regulation[$item->role_id]->min_days;
                    $conditions[] = "(r.branch_id = $item->branch_id AND r.session <= $max_session AND r.session >= $min_days)";
                }
            }

            if($hasPermission){
                $res = $pendingModel->getRequests($conditions);

                return $response->formatResponse(APICode::SUCCESS, $res);
            }else{
                return $response->formatResponse(APICode::PERMISSION_DENIED,null);
            }
        }else{
            return $response->formatResponse(APICode::SESSION_EXPIRED,null);
        }

    }

    public function deny(Request $request, $id){
        $response = new Response();

        $model = new Pending();
        $reserve = $model->find($id);
        $res = null;
        $code = APICode::SUCCESS;
        $reserve->status = 2;
        $reserve->approver_id = $request->users_data->id;
        $reserve->approved_at = date('Y-m-d H:i:s');
        $reserve->comment = $request->comment;


        if(!$reserve->save()){
            $this->sendDenyMail($reserve);
            $code = APICode::SERVER_CONNECTION_ERROR;
        }

        return $response->formatResponse($code, $res);
    }

    public function approve(Request $request, $id){
        $response = new Response();

        $model = new Pending();
        $reserve = $model->find($id);
        $res = null;
        $code = APICode::SUCCESS;
        $reserve->status = 1;
        $reserve->approver_id = $request->users_data->id;
        $reserve->approved_at = date('Y-m-d H:i:s');
        $reserve->comment = $request->comment;

        $contractModel = new Contract();
        $contract = $contractModel->find($reserve->contract_id);

        $meta_data = json_decode($reserve->meta_data);
//        $contract->start_date = $meta_data->start_date;
        $contract->end_date = $meta_data->end_date;


//        var_dump($contract->start_date);
//        var_dump($contract->end_date);die;

        if(!$reserve->save()){
            $this->sendApproveMail($reserve);
            $code = APICode::SERVER_CONNECTION_ERROR;
        }else{
            $contract->save();
        }

        return $response->formatResponse($code, $res);
    }

    private function sendRequirementMail($reserve){
        $approver = u::query("SELECT u.`full_name`, u.id, u.`email`, r.`name` AS role_name
                                    FROM 
                                        users AS u
                                        LEFT JOIN term_user_branch AS t ON u.id = t.`user_id`
                                        LEFT JOIN roles AS r ON t.`role_id` = r.`id`
                                        LEFT JOIN pending_regulation AS p ON t.`role_id` = p.`role_id`
                                    WHERE
                                        p.`min_days` <= $reserve->session
                                        AND p.`max_days` >= $reserve->session
                                        AND t.`branch_id` = $reserve->branch_id
                                        AND p.id IS NOT NULL
                                        AND p.`type` = 0
                                        AND t.status = 1
                                    GROUP BY u.id");

        $student = u::first("SELECT s.`name`, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $reserve->student_id");

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

                $subject = "[CRM] Yêu cầu phê duyệt pending của bé $student->name";
                $body = "
                    <p>Kính gửi: $item->full_name - $item->role_name</p>
                    
                    <p>Hệ thống CRM xin thông báo: Anh/chị đã nhận được yêu cầu <strong>pending</strong> của bé <strong>$student->name</strong> [$student->branch_name]</p>
                    
                    <p>Nội dung: $reserve->note</p>
                    
                    <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM để phê duyệt yêu cầu.</p>
                    
                    <p>Hoặc truy cập link: <a href='http://crm.cmsedu.vn'>crm.cmsedu.vn</a> để xem chi tiết yêu cầu</p>
                    
                    <p>Trân trọng cảm ơn!</p>
                ";
                $mail->sendSingleMail($to, $subject, $body);
            }
        }
    }

    private function sendApproveMail($reserve){
        $creator = u::query("SELECT u.`full_name`, u.id, u.`email`, r.`name` AS role_name
                                    FROM 
                                        users AS u
                                        LEFT JOIN term_user_branch AS t ON u.id = t.`user_id`
                                        LEFT JOIN roles AS r ON t.`role_id` = r.`id`
                                    WHERE
                                        u.id = $reserve->creator_id
                                    GROUP BY u.id");

        $student = u::first("SELECT s.`name`, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $reserve->student_id");

        if(!empty($creator)){
            if (APP_ENV == 'staging'){
                $creator = [
                    (Object)[
                        "email" => Mail::STAGING_EMAIL,
                        "full_name" => Mail::STAGING_FULL_NAME,
                        "role_name" => "Quản trị viên"
                    ]
                ];
            }elseif (APP_ENV == 'develop'){
                $creator = [
                    (Object)[
                        "email" => Mail::DEVELOP_EMAIL,
                        "full_name" => Mail::DEVELOP_FULL_NAME,
                        "role_name" => "Quản trị viên"
                    ]
                ];
            }
            foreach ($creator as $item) {
                $mail = new Mail();

                $to = [
                    'address' => $item->email,
                    'name' => $item->full_name
                ];

                $subject = "[CRM] Phê duyệt thành công pending của bé $student->name";
                $body = "
                    <p>Kính gửi: Phòng CSO</p>
                    
                    <p>Hệ thống CRM xin thông báo: Yêu cầu <strong>pending</strong> của bé <strong>$student->name</strong> [$student->branch_name] đã được phê duyệt</p>
                    
                    <p>Nội dung: $reserve->note</p>
                    
                    <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM để xem chi tiết.</p>
                    
                    <p>Trân trọng cảm ơn!</p>
                ";
                $mail->sendSingleMail($to, $subject, $body);
            }
        }
    }

    private function sendDenyMail($reserve){
        $creator = u::query("SELECT u.`full_name`, u.id, u.`email`, r.`name` AS role_name
                                    FROM 
                                        users AS u
                                        LEFT JOIN term_user_branch AS t ON u.id = t.`user_id`
                                        LEFT JOIN roles AS r ON t.`role_id` = r.`id`
                                    WHERE
                                        u.id = $reserve->creator_id
                                    GROUP BY u.id");

        $student = u::first("SELECT s.`name`, b.name as branch_name FROM students AS s LEFT JOIN branches AS b ON s.branch_id = b.id WHERE s.id = $reserve->student_id");

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

                $subject = "[CRM] Từ chối pending của bé $student->name";
                $body = "
                    <p>Kính gửi: Phòng CSO</p>
                    
                    <p>Hệ thống CRM xin thông báo: Yêu cầu <strong>pending</strong> của bé <strong>$student->name</strong> [$student->branch_name] đã bị từ chối phê duyệt</p>
                    
                    <p>Lý do từ chối: Không đồng ý phê duyệt vì: $reserve->comment</p>
                    
                    <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM để xem chi tiết.</p>
                    
                    <p>Trân trọng cảm ơn!</p>
                ";
                $mail->sendSingleMail($to, $subject, $body);
            }
        }
    }
}
