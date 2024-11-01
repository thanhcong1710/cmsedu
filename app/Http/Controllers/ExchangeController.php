<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;
use App\Providers\UtilityServiceProvider;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    public function list(Request $request)
    {
        $response = new Response();
        $code = APICode::SUCCESS;

        $user_data = $request->users_data;
        $res = null;

        if ($user_data) {
            $res = self::getList($request);
        } else {
            $code = APICode::PERMISSION_DENIED;
        }

        return $response->formatResponse($code, $res);
    }

    public function getList($request)
    {
        $conditions = $this->filter($request);

        if ($conditions) {
            $limit = $this->limit($request);
            $query = "SELECT
                    s.crm_id, 
                    s.accounting_id, 
                    s.name, 
                    lhc.branch_id,
                    lhc.summary_sessions,
                    b.name as branch_name, 
                    p.name AS product_name,
                    t.name AS tuition_fee_name,
                    cls.cls_name AS class_name,
                    lhc.contract_id,
                    lhc.id
                FROM
                    log_contracts_history AS lhc
                    LEFT JOIN students AS s ON lhc.`student_id` = s.`id`
                    LEFT JOIN branches AS b ON lhc.branch_id = b.id 
                    LEFT JOIN products  AS p ON p.id = lhc.product_id
                    LEFT JOIN classes AS cls ON cls.id = lhc.class_id
                    LEFT JOIN tuition_fee AS t ON t.id= lhc.tuition_fee_id  
                $conditions
                GROUP BY lhc.id
                ORDER BY lhc.id DESC
                $limit
            ";
            $result = u::query($query);

            # truy vấn lấy log của sản phẩm và gói phí cũ
            foreach ($result as $k => $value) {

                $log_contract_id = $value->id;
                $contract_id = $value->contract_id;
                $sql = "SELECT
                        lhc.summary_sessions, 
                        p.name AS product_name,
                        t.name AS tuition_fee_name
                    FROM
                        log_contracts_history AS lhc
                        LEFT JOIN students AS s ON lhc.`student_id` = s.`id`
                        LEFT JOIN branches AS b ON lhc.branch_id = b.id 
                        LEFT JOIN products  AS p ON p.id = lhc.product_id
                        LEFT JOIN classes AS cls ON cls.id = lhc.class_id
                        LEFT JOIN tuition_fee AS t ON t.id= lhc.tuition_fee_id  
                    where
                        lhc.contract_id = '$contract_id' AND
                        lhc.id < $log_contract_id
                    GROUP BY lhc.id
                    ORDER BY lhc.id DESC limit 1
                ";
                $result_log = u::first($sql);
                if($result_log){
                    $result[$k]->summary_sessions_from = $result_log->summary_sessions;
                    $result[$k]->product_name_from = $result_log->product_name;
                    $result[$k]->tuition_fee_name_from = $result_log->tuition_fee_name;
                }
            }
        
            $total = 0;
            if (!empty($result)) {
                $total = $this->countRecords($conditions);
            }

            $page = apax_get_pagination(json_decode($request->pagination), $total);
            if (!$total) {
                $page->spage = 0;
                $page->ppage = 0;
                $page->npage = 0;
                $page->lpage = 0;
                $page->cpage = 0;
                $page->total = 0;
            }

            $res = [
                'items' => $result,
                'pagination' => $page,
            ];
        } else {
            $res = [];
        }

        return $res;
    }

    private function filter($request)
    {
        if (isset($request->filter)) {
            try {
                $filter = json_decode($request->filter);
            } catch (\Exception $e) {
                return '';
            }

            $conditions = [];
            $conditions[] = "lhc.action like 'exchange_contract_%'";
            if (isset($filter->lms_effect_id) && $filter->lms_effect_id) {
                $value = trim($filter->lms_effect_id);
                if (is_numeric($value)) {
                    $id = (int) $value;
                    $conditions[] = "(s.cms_id = $id OR s.accounting_id LIKE '%$id%' OR s.crm_id LIKE '%$id%')";
                } else {
                    $conditions[] = "(s.accounting_id LIKE '%$value%' OR s.crm_id LIKE '%$value%')";
                }
            }

            $student_name = isset($filter->student_name) ? trim($filter->student_name) : '';
            if ($student_name) {
                $student_name = strtoupper($student_name);
                $conditions[] = "s.name LIKE '%$student_name%'";
            }

            if (isset($filter->branch_id) && is_numeric($filter->branch_id)) {
                $branch_id = (int) $filter->branch_id;
                if ($branch_id !== 0) {
                    $conditions[] = "(lhc.branch_id = $filter->branch_id OR s.branch_id = $filter->branch_id)";
                } else {
                    $branch_ids = u::getBranchIds($request->users_data);
                    if (!empty($branch_ids)) {
                        $branch_ids_string = implode(',', $branch_ids);
                        $conditions[] = "(lhc.branch_id IN ($branch_ids_string) OR s.branch_id IN ($branch_ids_string))";
                    }
                }
            } else {
                $branch_ids = u::getBranchIds($request->users_data);
                if (!empty($branch_ids)) {
                    $branch_ids_string = implode(',', $branch_ids);
                    $conditions[] = "(lhc.branch_id IN ($branch_ids_string) OR s.branch_id IN ($branch_ids_string))";
                }
            }

            if (!empty($conditions)) {
                return ' WHERE '.implode(' AND ', $conditions);
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    private function limit($request)
    {
        $limit = '';
        $pagination = json_decode($request->pagination);
        $pagination->cpage= $pagination->cpage?$pagination->cpage:1;
        if ($pagination->cpage && $pagination->limit) {
            $offset = ((int) $pagination->cpage - 1) * (int) $pagination->limit;
            $limit .= " LIMIT $offset, $pagination->limit";
        }

        return $limit;
    }

    private function countRecords($conditions)
    {
        $query = "SELECT 
                    COUNT(t.id) AS count_item
                FROM (
                    SELECT
                        lhc.id AS id
                    FROM
                        log_contracts_history AS lhc
                        LEFT JOIN students AS s ON lhc.`student_id` = s.`id`
                    $conditions
                    GROUP BY lhc.id
                ) AS t
            ";
        $res = u::first($query);
        if ($res) {
            return $res->count_item;
        } else {
            return 0;
        }
    }

    public function searchStudent(Request $request, $branch_id, $keyword)
    {
        $data = null;
        $response = new Response();
        if ($keyword) {
            $keyword = trim($keyword);
            $query = '';
            if ($session = json_decode($request->authorized)) {
                $session = $request->users_data;
                $user_id = $session->id;
                $role_id = $session->role_id;
                $branches = $branch_id ? (int) $branch_id : $session->branches_ids;
                $where = "AND s.branch_id IN ($branches)";
                if ($role_id == ROLE_EC_LEADER) {
                    $where .= " AND (u2.id = $user_id OR t.ec_id = $user_id)";
                }
                if ($role_id == ROLE_EC) {
                    $where .= " AND t.ec_id = $user_id";
                }
                $where .= " AND (s.id LIKE '%$keyword%' OR s.stu_id LIKE '%$keyword%' OR s.crm_id LIKE '%$keyword%'  OR s.accounting_id LIKE '%$keyword%')";
                $query = "SELECT
                    s.id AS student_id,
                    CONCAT(s.name, ' - ', s.crm_id) AS label
                    FROM students AS s
                    LEFT JOIN term_student_user AS t ON t.student_id = s.id
                    LEFT JOIN users AS u1 ON t.ec_id = u1.id
                    LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id
                    WHERE s.id > 0 AND 
                    (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7 )>0
                $where GROUP BY s.id ORDER BY `name` ASC LIMIT 0, 8";
                $data = u::query($query);
                $code = APICode::SUCCESS;
            }
        }

        return $response->formatResponse($code, $data);
    }

    public function getAllContractByStudent(Request $request, $student_id)
    {
        $query = 'SELECT s.* FROM students AS s WHERE id='.intval($student_id);
        $student_info = u::first($query);

        $query = "SELECT c.*, b.name AS branch_name, p.name AS product_name,t.name AS tuition_fee_name,
                cls.cls_name AS class_name
                FROM contracts AS c 
                LEFT JOIN branches AS b ON c.branch_id =b.id   
                LEFT JOIN products  AS p ON p.id = c.product_id
                LEFT JOIN classes AS cls ON cls.id = c.class_id
                LEFT JOIN tuition_fee AS t ON t.id= c.tuition_fee_id
                WHERE  c.type>0 AND c.status!=7 AND c.student_id=".intval($student_id)." AND ( c.enrolment_last_date IS NULL OR  c.enrolment_last_date > CURDATE()) AND c.class_id IS NULL
                ORDER BY c.count_recharge ";
        $contracts = u::query($query);
        foreach ($contracts as $k => $contract) {
            if ($contract->status != 6) {
                $contracts[$k]->left_sessions = $contract->real_sessions;
            } else {
                $class_day = UtilityServiceProvider::getClassDays($contract->class_id);
                $public_holiday = UtilityServiceProvider::getPublicHolidays($contract->class_id, $contract->branch_id, $contract->product_id);
                $reserved_dates = self::getReservedDates_transfer([$contract->id]);
                $merged_holi_day = $reserved_dates && isset($reserved_dates[$contract->id]) ? array_merge($public_holiday, $reserved_dates[$contract->id]) : $public_holiday;
                $done_sessions = UtilityServiceProvider::calSessions($contract->enrolment_start_date, date('Y-m-d',strtotime("-1 days")), $merged_holi_day, $class_day);
                $contracts[$k]->left_sessions = $contract->real_sessions - $done_sessions->total > 0 ? $contract->real_sessions - $done_sessions->total : 0;
            }
        }
        $contract_class = u::first("SELECT count(c.id) AS total FROM contracts AS c WHERE c.type>0 AND c.class_id IS NOT NULL AND c.status!=7 AND c.student_id = $student_id ");
        if ($contract_class->total == 0 ||1==1) {
            $access_exchange = true;
        } else {
            $access_exchange = false;
        }
        $response = new Response();
        $validate = self::validateExchange($student_id);
        $data = (object) [
            'has_error' => empty($contracts) ? 1 : $validate->has_error,
            'message' => empty($contracts) ? 'Không còn gói phí có hiệu lực' : $validate->message,
            'access_exchange' => $access_exchange,
            'contracts' => $contracts,
            'student_info' => $student_info,
        ];
        $code = APICode::SUCCESS;

        return $response->formatResponse($code, $data);
    }

    public function getResult(Request $request)
    {
        $start_date = $request->start_date;
        $query = "SELECT c.*, b.name AS branch_name, p.name AS product_name,t.name AS tuition_fee_name,
                cls.cls_name AS class_name
                FROM contracts AS c 
                LEFT JOIN branches AS b ON c.branch_id =b.id   
                LEFT JOIN products  AS p ON p.id = c.product_id
                LEFT JOIN classes AS cls ON cls.id = c.class_id
                LEFT JOIN tuition_fee AS t ON t.id= c.tuition_fee_id
                WHERE  c.type>0 AND c.status!=7 AND c.student_id=".intval($request->student_id)." AND ( c.enrolment_last_date IS NULL OR  c.enrolment_last_date > CURDATE()) AND c.class_id IS NULL
                ORDER BY c.count_recharge ";
        $contracts = u::query($query);
        $total = new \stdClass();
        $total->exchange_left_sessions = 0;
        $total->exchange_bonus = 0;
        $message = '';
        foreach ($contracts as $k => $contract) {
            if ($contract->status != 6) {
                $contracts[$k]->left_sessions = $contract->real_sessions;
            } else {
                $class_day = UtilityServiceProvider::getClassDays($contract->class_id);
                $public_holiday = UtilityServiceProvider::getPublicHolidays($contract->class_id, $contract->branch_id, $contract->product_id);
                $reserved_dates = self::getReservedDates_transfer([$contract->id]);
                $merged_holi_day = $reserved_dates && isset($reserved_dates[$contract->id]) ? array_merge($public_holiday, $reserved_dates[$contract->id]) : $public_holiday;
                $done_sessions = UtilityServiceProvider::calSessions($contract->enrolment_start_date, date('Y-m-d',strtotime($start_date."-1 days")), $merged_holi_day, $class_day);
                $contracts[$k]->left_sessions = $contract->real_sessions - $done_sessions->total > 0 ? $contract->real_sessions - $done_sessions->total : 0;
            }
            if ($contracts[$k]->left_sessions > 0) {
                $result = UtilityServiceProvider::calcTransferTuitionFeeV2($contract->tuition_fee_id, 0, $contract->branch_id, $request->product_transfer, $contracts[$k]->left_sessions);
                
                if ($result->message == '') {
                    $tuition_fee = u::first('SELECT * FROM tuition_fee WHERE id = '.$result->receive_tuition_fee->id);
                    $contracts[$k]->exchange_tuition_fee = $tuition_fee->name;
                    $contracts[$k]->exchange_left_sessions = $result->sessions;
                } else {
                    $message .= $result->message;
                    break;
                }

            } else {
                $tuition_fee_relation_info = u::first("SELECT t.* FROM tuition_fee_relation AS tr LEFT JOIN tuition_fee AS t ON tr.exchange_tuition_fee_id =t.id 
                    WHERE tr.tuition_fee_id=$contract->tuition_fee_id AND t.product_id=$request->product_transfer");
                $contracts[$k]->exchange_tuition_fee = $tuition_fee_relation_info->name;
                $contracts[$k]->exchange_left_sessions = 0;
            }
            $total->exchange_left_sessions += $contracts[$k]->exchange_left_sessions;
            $total->exchange_bonus += $contracts[$k]->bonus_sessions;
        }
        $total->exchange_all = $total->exchange_left_sessions + $total->exchange_bonus;
        $response = new Response();
        $data = (object) [
            'has_error' => false,
            'message' => $message,
            'contracts' => $contracts,
            'total' => $total,
        ];
        $code = APICode::SUCCESS;

        return $response->formatResponse($code, $data);
    }

    public function save(Request $request)
    {
        $start_date = $request->start_date;
        $query = "SELECT c.*, b.name AS branch_name, p.name AS product_name,
                (SELECT name FROM products WHERE id=$request->product_transfer) AS to_product_name,
                cls.cls_name AS class_name
                FROM contracts AS c 
                LEFT JOIN branches AS b ON c.branch_id =b.id   
                LEFT JOIN products  AS p ON p.id = c.product_id
                LEFT JOIN classes AS cls ON cls.id = c.class_id
                WHERE  c.type>0 AND c.status!=7 AND c.student_id=".intval($request->student_id)." AND ( c.enrolment_last_date IS NULL OR  c.enrolment_last_date > CURDATE()) AND c.class_id IS NULL
                ORDER BY c.count_recharge ";
        $contracts = u::query($query);
        foreach ($contracts as $k => $contract) {
            if ($contract->type > 0) {
                if($k==0){
                    $start_date = $contract->start_date;
                }else{
                    $start_date = date('Y-m-d',strtotime($tmp_end_date.' + 1 day'));
                }
                if($contract->real_sessions){
                    $result = UtilityServiceProvider::calcTransferTuitionFeeV2($contract->tuition_fee_id, 0, $contract->branch_id, $request->product_transfer, $contract->real_sessions);
                    if ($result) {
                        // $tuition_fee = u::first('SELECT * FROM tuition_fee WHERE id = '.$result->receive_tuition_fee->id);
                        $result_sessions = $contract->real_sessions ? $result->sessions : 0;
                        $receive_tuition_fee_id = $contract->real_sessions ? $result->receive_tuition_fee->id : $contract->tuition_fee_id;
                        $public_holiday = UtilityServiceProvider::getPublicHolidays($contract->class_id, $contract->branch_id, $contract->product_id);
                        $end_date = UtilityServiceProvider::calEndDate($result_sessions + $contract->bonus_sessions, [2, 5], $public_holiday, $start_date);
                        $tmp_end_date = $end_date->end_date;
                        u::updateContract((object) [                        
                            'id' => $contract->id,
                            'action' => "exchange_contract_$contract->id"."_renew",
                            'product_id' => $request->product_transfer,
                            'real_sessions' => $result_sessions,
                            'total_sessions' => $result_sessions + $contract->bonus_sessions,
                            'summary_sessions' => $result_sessions + $contract->bonus_sessions,
                            'tuition_fee_id' => $receive_tuition_fee_id,
                            'end_date' => $end_date->end_date,
                            'start_date'=>$start_date,
                        ]);
                    }
                }else{
                    $result_sessions = $contract->real_sessions;
                    $tuition_fee_relation_info = u::first("SELECT t.* FROM tuition_fee_relation AS tr LEFT JOIN tuition_fee AS t ON tr.exchange_tuition_fee_id =t.id 
                        WHERE tr.tuition_fee_id=$contract->tuition_fee_id AND t.product_id=$request->product_transfer");
                    $receive_tuition_fee_id = $tuition_fee_relation_info->id;
                    $public_holiday = UtilityServiceProvider::getPublicHolidays($contract->class_id, $contract->branch_id, $contract->product_id);
                    $end_date = UtilityServiceProvider::calEndDate($result_sessions + $contract->bonus_sessions, [2, 5], $public_holiday, $start_date);
                    $tmp_end_date = $end_date->end_date;
                    u::updateContract((object) [                        
                        'id' => $contract->id,
                        'action' => "exchange_contract_$contract->id"."_renew",
                        'product_id' => $request->product_transfer,
                        'real_sessions' => $result_sessions,
                        'total_sessions' => $result_sessions + $contract->bonus_sessions,
                        'summary_sessions' => $result_sessions + $contract->bonus_sessions,
                        'tuition_fee_id' => $receive_tuition_fee_id,
                        'end_date' => $end_date->end_date,
                        'start_date'=>$start_date,
                    ]);
                }
            } else {
                u::updateContract((object) [
                    'id' => $contract->id,
                    'product_id' => $request->product_transfer,
                    'action' => "exchange_contract_$contract->id",
                ]);
            }
        }
        if(isset($contracts[0])){
            DB::table('log_student_update')->insert(
                [
                'student_id' => $request->student_id,
                'updated_by' => $request->users_data->id,
                'cm_id' => $contracts[0]->cm_id,
                'status' => 1,
                'branch_id' => $contracts[0]->branch_id,
                'ceo_branch_id' => 0,
                'content' => "Quy đổi gói phí sang sản phẩm ".$contracts[0]->to_product_name,
                'updated_at' => date('Y-m-d H:i:s'),
                ]
            );
        }
        $response = new Response();
        $data = (object) [
            'has_error' => false,
            'message' => '',
        ];
        $code = APICode::SUCCESS;

        return $response->formatResponse($code, $data);
    }
    private static function validateExchange($student_id){
        $has_error=0;
        $message= '';
        $data = u::first("SELECT * FROM students WHERE id = $student_id");
        if($data){
          switch ($data->waiting_status) {
            case 1:
                $has_error = 1;
                $message ="Học sinh đang chờ duyệt chuyển phí";
                break;
            case 2:
              $has_error = 1;
              $message ="Học sinh đang chờ duyệt nhận phí";
              break;
            case 3:
                $has_error = 1;
                $message ="Học sinh đang chờ duyệt chuyển trung tâm";
                break;
            case 4:
                $has_error = 1;
                $message ="Học sinh đang chờ duyệt bảo lưu";
                break;
            case 5:
                $has_error = 1;
                $message ="Học sinh đang chờ duyệt pending";
                break;
            case 6:
                $has_error = 1;
                $message ="Học sinh đang chờ duyệt rút phí";
                break; 
            case 7:
                $has_error = 1;
                $message ="Học sinh đang chờ duyệt chuyển lớp";
                break; 
            case 8:
                $has_error = 1;
                $message ="Học sinh đang trong quá trình chạy phí";
                break;       
            default:
                    $has_error = 0;
                    $message ="";
          }
            $contracts = u::first("SELECT count(id) AS total FROM contracts WHERE student_id = $student_id AND debt_amount>0 AND status IN (1,2,3,4,5,6)");
            if($contracts->total >0){
                $has_error = 1;
                $message ="Học sinh có gói phí chưa thu đủ phí";
            }
            $resever_info_tmp = u::first("SELECT count(id) AS total FROM reserves WHERE student_id = $student_id AND status=2 AND is_reserved =0 AND end_date>= CURDATE()");
            if($resever_info_tmp->total >0){
                $has_error = 1;
                $message ="Học sinh đang được bảo lưu không giữ chỗ, chỉ thực hiện quy đổi sau khi kết thúc bảo lưu";
            }
        }
        return (Object)[
          'has_error' => $has_error,
          'message' => $message,
        ];
    }
    static function getReservedDates_transfer($contract_ids = [])
    {
        $res = [];

        if ($contract_ids) {
            $contract_ids_string = implode(',', $contract_ids);
            $query = "SELECT r.contract_id, r.start_date, r.end_date, r.session FROM `reserves` AS r WHERE r.status = 1 AND r.contract_id IN ($contract_ids_string)";
            $data = u::query($query);

            if (!empty($data)) {
                foreach ($data as $da) {
                    if (isset($res[$da->contract_id])) {
                        $res[$da->contract_id][] = (Object)['start_date' => $da->start_date, 'end_date' => $da->end_date, 'sessions' => $da->session];
                    } else {
                        $res[$da->contract_id][] = (Object)['start_date' => $da->start_date, 'end_date' => $da->end_date, 'sessions' => $da->session];
                    }
                }
            }
        }

        return $res;
    }
}
