<?php

namespace App\Http\Controllers;

use App\Models\InfoValidation;
use App\Models\Schedule;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Withdrawal;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;
use App\Models\Reserve;

class WithdrawalsController extends Controller
{
    public function getList(Request $request){
        $model = new Withdrawal();
        $response = new Response();
        
        $code = APICode::SUCCESS;
        
        $user_data = $request->users_data;
        $res = null;
        
        if($user_data) {
            $res = $model->getList($request);
        }else{
            $code = APICode::PERMISSION_DENIED;
        }
        
        return $response->formatResponse($code, $res);
    }
    public function searchStudent(Request $request, $branch_id, $keyword) {
        $data = null;
        $response = new Response();
        if ($keyword) {
            $keyword = trim($keyword);
            $keys = explode('-', $keyword);
            $key1 = '';
            $key2 = $keyword;
            if (count($keys) == 2) {
                $key1 = trim($keys[0]);
                $key2 = trim($keys[1]);
            }
            $query = '';
            if ($session = json_decode($request->authorized)) {
                $session = $request->users_data;
                $user_id = $session->id;
                $role_id = $session->role_id;
                $branches = $branch_id ? (int)$branch_id : $session->branches_ids;
                $where = "AND s.branch_id IN ($branches)";
                if ($role_id == ROLE_EC_LEADER) {
                    $where.= " AND (u2.id = $user_id OR t.ec_id = $user_id)";
                }
                if ($role_id == ROLE_EC) {
                    $where.= " AND t.ec_id = $user_id";
                }
                $key1 = $key1 ? trim($key1) : '';
                $key2 = $key2 ? trim($key2) : '';
                $where.= $key1 ? " AND ((s.id LIKE '%$key1%' OR s.cms_id LIKE '%$key1%' OR s.crm_id LIKE '%$key1%') AND "
                    : " AND ((s.id LIKE '%$keyword%' OR s.cms_id LIKE '%$keyword%' OR s.crm_id LIKE '%$keyword%') OR ";
                $where.= $key2 ? " s.name LIKE '$key2%' OR s.nick LIKE '$key2%' OR s.accounting_id LIKE '%$keyword%')" : ')';

                $query = "SELECT
                    s.id AS student_id,
                    CONCAT(s.name, ' - ', s.crm_id) AS label
                    FROM students AS s
                    LEFT JOIN term_student_user AS t ON t.student_id = s.id
                    LEFT JOIN users AS u1 ON t.ec_id = u1.id
                    LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id
                    WHERE s.id > 0 
                $where GROUP BY s.id ORDER BY `name` ASC LIMIT 0, 8";
                $data = u::query($query);
                $code = APICode::SUCCESS;
            }
        }
        return $response->formatResponse($code, $data);
    }
    public function getAllContractByStudent(Request $request, $student_id){
        $response = new Response();
        $query = "CALL validate_tuition_transfer($student_id)";
        $validate = DB::select(DB::raw($query));
        
        $data = (Object)[
            'has_error' => $validate[0]->has_error,
            'message' => $validate[0]->message,
            'contracts' => []
        ];
        if($data->has_error == 0){
            $query = "SELECT c.*, 
                    s.name AS student_name,
                    s.date_of_birth,
                    s.crm_id AS cms_id, 
                    b.name AS branch_name,
                    c.type AS contract_type,
                    u1.full_name AS ec_name,
                    u2.full_name AS cm_name,
                    p.name AS product_name,  
                    p.id AS product_id,  
                    t.name AS tuition_fee_name,
                    t.price AS tuition_fee_price,
                    cls.cls_name AS class_name,
                    w.id AS withdraw_id,
                    rc.type AS relation_contract_type,
                    rc.class_id AS relation_contract_class
                FROM contracts AS c
                LEFT JOIN students AS s ON s.id = c.student_id
                LEFT JOIN branches AS b ON b.id = c.branch_id
                LEFT JOIN users u1 ON u1.id = c.ec_id
                LEFT JOIN users u2 ON u2.id = c.cm_id
                LEFT JOIN products AS p ON p.id = c.product_id
                LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
                LEFT JOIN classes AS cls ON cls.id = c.class_id     
                LEFT JOIN withdrawal_fees AS w ON w.contract_id = c.id
                LEFT JOIN contracts AS rc ON rc.id = c.relation_contract_id
                WHERE c.student_id = ".intval($student_id)." AND c.type IN (1,2,3,4,5,6,7,10) AND (c.status = 3 OR (c.status= 6 AND c.debt_amount = 0 AND c.enrolment_last_date >'".date('Y-m-d 00:00:00')."') OR (c.status=4 AND c.must_charge>0))
                ORDER BY c.id";
            $res = u::query($query);
            $result = [];
            if(!empty($res)){
                $class_ids = [];
                $contract_ids = [];
                foreach ($res as &$r){
                    if($r->class_id){
                        $class_ids[] = $r->class_id;
                    }
                    $contract_ids[] = $r->id;
                    $result[$r->id] = $r;
                    
                }
                $class_info = [];
                if(!empty($class_ids)){
                    $class_ids_string = implode(',',$class_ids);
                    $query = "SELECT s.class_id as class_id, s.class_day as class_day FROM sessions as s WHERE class_id IN($class_ids_string) GROUP BY s.class_id, s.class_day";
                    $class_days = DB::select(DB::raw($query));
                    if(!empty($class_days)){
                        foreach ($class_days as $class_day){
                            if(!isset($class_info[$class_day->class_id])){
                                $class_info[$class_day->class_id] = [$class_day->class_day];
                            }else{
                                $class_info[$class_day->class_id][] = $class_day->class_day;
                            }
                        }
                    }
                }
                
                $reserveModel = new Reserve();
                
                $reserved_dates = $reserveModel->getReservedDates($contract_ids);

                foreach ($result as &$r){
                    if(isset($class_info[$r->class_id])){
                        $r->class_days = $class_info[$r->class_id];
                    }else{
                        $r->class_days = [];
                    }
                    
                    if(isset($reserved_dates[$r->id])){
                        $r->reserved_dates = $reserved_dates[$r->id];
                    }else{
                        $r->reserved_dates = [];
                    }
//                    $classdays = u::getClassDays($r->class_id);
//                    $holidays = u::getPublicHolidays($r->class_id, 0, $r->product_id);
//                    $r->nearest_session = Schedule::getNearestDateScheduleByClassId($r->class_id);
//                    $r->done_sessions = u::calSessions($r->enrolment_start_date, date('Y-m-d', time()),$holidays, $classdays)->total;
                }
                
                $result = array_values($result);
            }else{
                $data->has_error = 1;
                $data->message = "Học sinh không đủ điều kiện rút phí";
            }
            $data->contracts = $result;
            
            $arr_withdraw = DB::table('withdrawal_fees')->where('student_id',$student_id)->get();
            foreach ($arr_withdraw AS $withdraw){
                if($withdraw->status == 0){
                    $data->has_error = 1;
                    $data->message = "Học sinh đang chờ duyệt rút phí không thể thêm mới rút phí";
                }elseif($withdraw->status ==1 ){
                    $data->has_error = 1;
                    $data->message = "Học sinh đang chờ hoàn phí không thể thêm mới rút phí";
                }
            }
            $query_contract_error = "SELECT c.* FROM contracts AS c WHERE c.student_id = ".intval($student_id)." AND c.status IN (1,2) ";
            $contrat_error = u::query($query_contract_error);
            foreach ($contrat_error AS $err){
                if($err->status == 1){
                    $data->has_error = 1;
                    $data->message = "Học sinh có gói đã active nhưng chưa đóng phí, không thể rút phí";
                }elseif ($err->status == 2){
                    $data->has_error = 1;
                    $data->message = "Học sinh có gói đã đặt cọc nhưng chưa thu đủ phí hoặc đang chờ nhận chuyển phí, không thể rút phí";
                }
            }
        }
        $code = APICode::SUCCESS;
        return $response->formatResponse($code, $data);
    }
    public function create(Request $request)
    {   
        $withdrawal = new Withdrawal();
        $withdrawal->contract_id = $request->contract_id;
        $withdrawal->student_id = $request->student_id;
        $withdrawal->branch_id = $request->branch_id;
        $withdrawal->creator_id = $request->users_data->id;
        $withdrawal->ec_id = $request->ec_id;
        $withdrawal->refun_amount = $request->refun_amount;
        $withdrawal->real_amount = $request->real_amount;
        $withdrawal->fee_amount = $request->fee_amount;
        $withdrawal->withdraw_date = $request->withdraw_date;
        $withdrawal->left_sessions = $request->left_sessions;
        $withdrawal->done_sessions = $request->done_sessions;
        $withdrawal->comment_created = $request->comment_created;
        $withdrawal->created_at = now();
        $withdrawal->updated_at = now();
        $withdrawal->meta_data = json_encode($request->meta_data) ;
        $withdrawal->status = 0 ;
        if(!empty($request->attached_file)){
          $withdrawal->attached_file = ada()->upload($request->attached_file);
        }
        if($withdrawal->save()){
            $withdrawal->sendRequirementMail($withdrawal);
        }
        $code = APICode::SUCCESS;
        $response = new Response();
        return $response->formatResponse($code, $withdrawal);
    }
    public function getDetail($id) {
        $response = new Response();
        
        $query = "SELECT w.*, 
                s.crm_id AS cms_id,
                s.nick,
                s.accounting_id,
                s.name AS student_name,
                b.name AS branch_name,
                p.name AS product_name,
                pro.name AS program_name,
                cls.cls_name AS class_name
            FROM withdrawal_fees AS w
                LEFT JOIN students AS s ON w.student_id = s.id
                LEFT JOIN contracts AS c ON w.contract_id = c.id
                LEFT JOIN branches AS b ON w.branch_id = b.id
                LEFT JOIN products AS p ON c.product_id = p.id
                LEFT JOIN programs AS pro ON c.program_id = pro.id
                LEFT JOIN classes AS cls ON c.class_id = cls.id
            WHERE w.id = $id ";
        $result = u::query($query);
        $code = APICode::SUCCESS;
        if(!empty($result)){
            $result[0]->withdraw_date = date('Y-m-d',strtotime($result[0]->withdraw_date));
            return $response->formatResponse($code,$result[0]);
        }else{
            return $response->formatResponse($code,$result);
        }
    }
    public function getListByUserID($id){
        $response = new Response();
        $withdraw_info = DB::table('withdrawal_fees')->where('id',$id)->first();
        $query = "
            SELECT
                w.*,
                s.crm_id AS cms_id,
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
                w.student_id = $withdraw_info->student_id
            ORDER BY w.id DESC
        ";
        
        $result = DB::select(DB::raw($query));
        $code = APICode::SUCCESS;
        return $response->formatResponse($code, $result);
    }
    public function getRequest(Request $request){
        $code = APICode::SUCCESS;
        $data = null;
        $WithdrawalObj = new Withdrawal();
        $response = new Response();
        
        if ($session = $request->users_data) {
            $data = $WithdrawalObj->getWithdrawalRequests($request);
        }else{
            $code = APICode::PERMISSION_DENIED;
        }
        
        return $response->formatResponse($code,$data);
    }
    public function getRequestRefun(Request $request){
        $code = APICode::SUCCESS;
        $data = null;
        $WithdrawalObj = new Withdrawal();
        $response = new Response();
        
        if ($session = $request->users_data) {
            $data = $WithdrawalObj->getWithdrawalRequestsRefun($request);
        }else{
            $code = APICode::PERMISSION_DENIED;
        }
        
        return $response->formatResponse($code,$data);
    }
    public function approve(Request $request, $id){
        $code = APICode::SUCCESS;
        $data = null;
        $withdrawObj = new Withdrawal();
        $response = new Response();
        
        if ($session = $request->users_data) {
            $code = $withdrawObj->approve($id, $request);
        }else{
            $code = APICode::PERMISSION_DENIED;
        }
        
        return $response->formatResponse($code,$data);
    }
    
    public function deny(Request $request, $id){
        $code = APICode::SUCCESS;
        $response = new Response();
        $data = null;
        $withdrawObj = new Withdrawal();
        
        if ($session = $request->users_data) {
            $user_id = $session->id;
            if(!$withdrawObj->deny($id, $request->comment, $user_id)){
                $code = APICode::SERVER_CONNECTION_ERROR;
            }
        }else{
            $code = APICode::PERMISSION_DENIED;
        }
        
        return $response->formatResponse($code,$data);
    }
    public function refun(Request $request, $id){
        $code = APICode::SUCCESS;
        $data = null;
        $withdrawObj = new Withdrawal();
        $response = new Response();
        
        if ($session = $request->users_data) {
            $code = $withdrawObj->refun($id, $request);
        }else{
            $code = APICode::PERMISSION_DENIED;
        }
        
        return $response->formatResponse($code,$data);
    }
    public function refunDeny(Request $request, $id){
        $code = APICode::SUCCESS;
        $response = new Response();
        $data = null;
        $withdrawObj = new Withdrawal();
        
        if ($session = $request->users_data) {
            $user_id = $session->id;
            if(!$withdrawObj->refunDeny($id, $request->comment, $user_id)){
                $code = APICode::SERVER_CONNECTION_ERROR;
            }
        }else{
            $code = APICode::PERMISSION_DENIED;
        }
        
        return $response->formatResponse($code,$data);
    }
}
