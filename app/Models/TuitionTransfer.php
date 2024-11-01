<?php

namespace App\Models;

use App\Http\Controllers\ContactsController;
use App\Http\Controllers\ContractsController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\LMSAPIController;
use App\Http\Controllers\TransferFeeController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;
use App\Http\Controllers\TransferFeeController as t;
use PhpParser\Node\Expr\Cast\Object_;

class TuitionTransfer extends Model
{
    private $DELETED = 4;
    private $WAITING = 0;
    private $FIRST_APPROVED = 1;
    private $APPROVED = 2;
    private $CANCELED = 3;
    private $TUITION_TRANSFER_TYPE = 0;
    protected $table = 'tuition_transfer_v2';
    public $timestamps = false;

    public function getList($request)
    {
        $search = isJson($request->search) ? (object) json_decode($request->search, true) : (object) [];
        $status = $search->tuition_fee;
        $session = $request->users_data;
        $branches = $session->branches_ids;
        $status_condition = '1,2,3,4,5,6';
        if($session->role_id == 999999999 && $search->keyword){
            $conditions = "t.status IN ($status_condition) ";
        }else {
            if ($search->branch > 0) {
                $branches = $search->branch;
            }
            $conditions = "t.status IN ($status_condition) AND (from_branch_id IN ($branches) OR to_branch_id IN ($branches))";
        }
       
        
        if ($status != '') {
            $where_status = "and t.status = $status";
        } else {
            $where_status = '';
        }
        if ($search->keyword != '') {
            $keyword = $search->keyword;
            $string_search = " AND
              (( t.code LIKE '$keyword%'
              OR s.crm_id LIKE '$keyword%'
              OR s.stu_id LIKE '$keyword%'
              OR s.accounting_id LIKE '$keyword%'
              OR s.name LIKE '%$keyword%'
              OR s.nick LIKE '%$keyword%'
              OR s.email LIKE '%$keyword%'
              OR s.phone LIKE '$keyword%') 

              or 

              (t.code LIKE '$keyword%'
              OR s2.crm_id LIKE '$keyword%'
              OR s2.stu_id LIKE '$keyword%'
              OR s2.accounting_id LIKE '$keyword%'
              OR s2.name LIKE '%$keyword%'
              OR s2.nick LIKE '%$keyword%'
              OR s2.email LIKE '%$keyword%'
              OR s2.phone LIKE '$keyword%'))";
        } else {
            $string_search = '';
        }
        $query = "SELECT
            t.id, 
            t.code,
            t.note,
            t.`type`,
            t.`status`,
            to_student_id,
            from_student_id,
            t.transfer_date,
            t.received_amount,
            t.received_sessions,
            t.transferred_amount,
            t.transferred_sessions,
            t.from_branch_id,
            t.to_branch_id,
            t.creator_id, 
            t.created_at, 
            t.ceo_comment,
            t.attached_file,
            t.ceo_approver_id, 
            t.ceo_approved_at, 
            t.transferred_data,
            t.received_data, 
            t.accounting_comment, 
            t.accounting_approver_id, 
            t.accounting_approved_at, 
            t.version,
            t.meta_data,
            t.sibling,
            s.nick,
            (SELECT `name` FROM branches WHERE id = t.from_branch_id) from_branch_name,
            (SELECT `name` FROM branches WHERE id = t.to_branch_id) to_branch_name,
            (SELECT CONCAT(`username`) FROM users WHERE id = t.creator_id) creator_name,
            (SELECT CONCAT(`full_name`, ' - ', `username`) FROM users WHERE id = t.ceo_approver_id) ceo_name,
            (SELECT CONCAT(`full_name`, ' - ', `username`) FROM users WHERE id = t.accounting_approver_id) accounting_name,
            (SELECT `accounting_id` FROM students WHERE id = t.to_student_id) to_student_act,
            (SELECT `accounting_id` FROM students WHERE id = t.from_student_id) from_student_act,
            (SELECT `stu_id` FROM students WHERE id = t.to_student_id) to_student_lms,
            (SELECT `stu_id` FROM students WHERE id = t.from_student_id) from_student_lms,
            (SELECT `crm_id` FROM students WHERE id = t.to_student_id) to_student_crm,
            (SELECT `crm_id` FROM students WHERE id = t.from_student_id) from_student_crm,
            (SELECT `name` FROM students WHERE id = t.to_student_id) to_student_name,
            (SELECT `name` FROM students WHERE id = t.from_student_id) from_student_name,
            (SELECT CONCAT(`name`, ' - ', stu_id) FROM students WHERE id = t.to_student_id) to_student_label,
            (SELECT CONCAT(`name`, ' - ', stu_id) FROM students WHERE id = t.from_student_id) from_student_label,
            t.reason_id ,
            t.amount_truythu
            FROM tuition_transfer_v2 t LEFT JOIN students s ON s.id = t.from_student_id  LEFT JOIN students s2 ON s2.id = t.to_student_id 
            WHERE $conditions $string_search $where_status
            ORDER BY id DESC  
        ";
        $query_tong = "SELECT
            count(*) as tong
            FROM tuition_transfer_v2 t LEFT JOIN students s ON s.id = t.from_student_id  LEFT JOIN students s2 ON s2.id = t.to_student_id 
            WHERE $conditions $string_search $where_status
        ";
        //echo $query_tong; exit();
        $total = u::first($query_tong)->tong;
        $limit = $this->limit($request, $total);
        if (!empty($limit)) {
            $query .= " $limit";
        }
        $result = u::query($query);
        $xlist = [];
        if (!empty($result)) {
            //$total = $this->countRecords($conditions);
            foreach ($result as $item) {
                $item = (object) $item;
                if ($item->reason_id) {
                    $reason_info = u::first("SELECT * FROM reasons WHERE id=$item->reason_id");
                    $item->note = $reason_info->description;
                }
                $item->transferred_data = $item->transferred_data ? (array) json_decode($item->transferred_data, true) : [];
                $item->received_data = $item->received_data ? (array) json_decode($item->received_data, true) : [];
                if (!empty($item->transferred_data)) {
                    foreach ($item->transferred_data as $k => $row) {
                        if (!isset($row['bonus_sessions'])) {
                            $item->transferred_data[$k]['bonus_sessions'] = 0;
                        }
                        if (!isset($row['done_bonus_sessions'])) {
                            $item->transferred_data[$k]['done_bonus_sessions'] = 0;
                        }
                    }
                }
                /*
                + Lấy lịch sử phê duyệt
                */
                $ceo = (object) ['message' => '', 'date' => ''];
                $kt = (object) ['message' => '', 'date' => ''];
                $sql_log_transferred = "SELECT * FROM log_tuition_transfer WHERE  tuition_transfer_id = $item->id";
                $log_transferreds = u::query($sql_log_transferred);
                foreach ($log_transferreds as $k => $log_transferred) {
                    $item_log_transferred = (object) $log_transferred;
                    switch ($log_transferred->status) {
                        case 3:
                            $kt->message = $item_log_transferred->comment ? $item_log_transferred->comment : '';
                            $kt->date = $item_log_transferred->created_at;
                            break;
                        case 2:
                            $ceo->message = $item_log_transferred->comment ? $item_log_transferred->comment : '';
                            $ceo->date = $item_log_transferred->created_at;
                            break;
                        case 5:
                            $kt->message = $item_log_transferred->comment ? $item_log_transferred->comment : '';
                            $kt->date = $item_log_transferred->created_at;
                            break;
                        case 4:
                            $ceo->message = $item_log_transferred->comment ? $item_log_transferred->comment : '';
                            $ceo->date = $item_log_transferred->created_at;
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                if ($item->version == 2 && $item->meta_data) {
                    $meta_data = json_decode($item->meta_data);
                    $tmp_transferred_sessions = 0;
                    foreach ($meta_data->contracts as $row) {
                        $tmp_transferred_sessions += $row->total_session - $row->sessions_from_start_to_transfer_date;
                    }
                    $item->transferred_sessions = $tmp_transferred_sessions;
                }
                $item->log_ceo = $ceo;
                $item->log_kt = $kt;
                $xlist[] = $item;
            }
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

        return (object) [
            "list" => $xlist,
            "pagination" => $page
        ];
    }
    private function limit($request, $totalRecord = 0)
    {
        $limit = '';
        $pagination = json_decode($request->pagination);
        $pagination->cpage = $pagination->cpage ? $pagination->cpage : 1;
        if ($pagination->cpage && $pagination->limit) {
            $offset = ((int) $pagination->cpage - 1) * (int) $pagination->limit;
            if ($offset > $totalRecord) {
                $offset = 0;
            }
            $limit .= " LIMIT $offset, $pagination->limit";
        }

        return $limit;
    }
    public function suggestSender($key, $branch_id)
    {
        $search_condition = "";
        if ($key && $key !== '_') {
            if (is_numeric($key)) {
                $search_condition .= " AND (s.id LIKE '$key%' OR s.stu_id LIKE '$key%')";
            } else {
                $search_condition .= " AND (s.name LIKE '$key%' OR s.phone LIKE '$key%' OR s.crm_id LIKE '$key%')";
            }
        }
        $query = "SELECT 
            s.`id` student_id,
            s.crm_id,
            s.`stu_id` lms_id,
            COALESCE(s.`stu_id`, s.crm_id) client_id,
            s.branch_id,
            s.accounting_id,
            s.`name` student_name,
            s.`nick`,
            s.type student_type,
            s.sibling_id
        FROM
            students s LEFT JOIN contracts on contracts.student_id = s.id
        WHERE s.status > 0 AND s.branch_id = $branch_id $search_condition  GROUP BY s.id LIMIT 10";


        $res = u::query($query);

        $result = [];
        if (!empty($res)) {
            foreach ($res as &$r) {
                $type = (int)$r->student_type === 1 ? ' (VIP)' : '';
                $r->label = $r->student_name . ' - ' . $r->client_id . $type;
                $result[$r->student_id] = $r;
            }
            $result = array_values($result);
        }
        return $result;
    }
    public static function getAllSenderContracts($student_id)
    {
        $student = Student::find($student_id);
        $data = (object)[
            'has_error' => 0,
            'message' => '',
            'contracts' => []
        ];
        if ($student_id == 'undefined') {
            $data = (object)[
                'has_error' => 1,
                'message' => 'Mã không hợp lệ',
                'contracts' => []
            ];
        } else {
            $waiting_status = [
                0 => 'không có bản ghi chờ duyệt nào',
                1 => 'Chờ duyệt chuyển phí',
                2 => 'Chờ duyệt nhận phí',
                3 => 'Chờ duyệt chuyển trung tâm',
                4 => 'Chờ duyệt bảo lưu',
                5 => 'Chờ duyệt chuyển lớp',
            ];

            if ($student->waiting_status != 0) {
                $data = (object)[
                    'has_error' => 1,
                    'message' => $waiting_status[$student->waiting_status],
                    'contracts' => []
                ];
            }
            // $resever_info = u::first("SELECT count(id) AS total FROM reserves WHERE student_id = $student_id AND status=2 AND is_reserved =1 AND end_date>= CURDATE()");
            // if ($resever_info->total > 0) {
            //     $data = (object)[
            //         'has_error' => 1,
            //         'message' => "Học sinh đang được bảo lưu giữ chỗ, chỉ thực hiện chuyển phí sau khi kết thúc bảo lưu",
            //         'contracts' => []
            //     ];
            // }
            // $resever_info_tmp = u::first("SELECT count(id) AS total FROM reserves WHERE student_id = $student_id AND status=2 AND is_reserved =0 AND end_date>= CURDATE()");
            // if ($resever_info_tmp->total > 0) {
            //     $data = (object)[
            //         'has_error' => 1,
            //         'message' => "Học sinh đang được bảo lưu không giữ chỗ, chỉ thực hiện chuyển phí sau khi kết thúc bảo lưu",
            //         'contracts' => []
            //     ];
            // }
            $resever_info_over = u::first("SELECT SUM(`session`) AS total FROM reserves WHERE student_id = $student_id AND status=2 AND is_reserved =0 AND end_date>= CURDATE()");
            if ($resever_info_over->total > 5) {
                $data = (object)[
                    'has_error' => 1,
                    'message' => "Học sinh đã thực hiện bảo lưu quá 5 buổi không được phép chuyển phí",
                    'contracts' => []
                ];
            }
        }
        if ($data->has_error == 0) {
            $getContractsQuery = "SELECT 
                c.id contract_id,
                c.code, 
                c.accounting_id,
                c.branch_id,
                c.count_recharge,
                c.total_sessions, 
                c.reservable_sessions,
                c.reserved_sessions,
                (c.reservable_sessions - c.reserved_sessions) transfer_reservable_sessions, 
                IF(c.product_id > 0, c.product_id, (SELECT product_id FROM term_program_product WHERE program_id = c.program_id)) product_id,
                (SELECT `name` FROM products WHERE id = c.product_id) product_name,
                IF(c.program_id > 0, (SELECT `name` FROM programs WHERE id = c.program_id), (SELECT `name` FROM programs WHERE id = c.program_label)) program_name,
                c.program_id,
                c.program_label,
                c.enrolment_last_date,
                c.enrolment_start_date,
                c.status,
                COALESCE(c.enrolment_last_date, c.end_date) last_date, 
                (SELECT `name` FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_name,
                (SELECT `cls_name` FROM classes WHERE id = c.class_id) class_name,
                c.class_id,
                c.`type` contract_type,
                c.`payload` payload,
                c.`must_charge` must_charge,
                c.`real_sessions`,
                c.total_charged,
                IF(c.debt_amount > 0, 0 , bonus_sessions) AS bonus_sessions,
                IF(c.`class_id` IS NULL, c.`start_date`, c.`enrolment_start_date`) start_date,
                IF(c.`class_id` IS NULL, c.`end_date`, c.`enrolment_last_date`) end_date,
                IF(c.class_id IS NULL, 0,1) has_enrolment,
                c.tuition_fee_id,
                (SELECT receivable FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_receivable,
                (SELECT `session` FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_sessions,
                IF(c.debt_amount > 0, 1, 0) block_edit,
                (SELECT class_id FROM contracts WHERE id = c.relation_contract_id AND student_id = $student_id) pre_contract_enrolment
            FROM contracts c WHERE
                c.`student_id` = $student_id
                AND c.branch_id = $student->branch_id
                AND c.`status` > 0 AND c.`status` < 7
                AND (c.type NOT IN (0) OR (c.type IN (8,85,86) AND c.class_id IS NOT NULL))
                -- AND c.must_charge>0
                AND (c.class_id IS NULL OR c.class_id=0 OR c.`enrolment_last_date` >= CURDATE()) 
                AND c.summary_sessions>0
            GROUP BY c.`id` ORDER BY c.`count_recharge`, c.`id` ASC
            ";
            $data->contracts = u::query($getContractsQuery);
            $data->student = $student;
            if (!empty($data->contracts)) {
                $contract_ids = [];
                $class_ids = [];
                $renewed = 0;
                $renew = 0;
                $reserved_sessions = 0;
                foreach ($data->contracts as $contract) {
                    if ($contract->count_recharge) {
                        $renewed += 1;
                    }
                    $contract_ids[] = $contract->contract_id;
                    $class_ids[] = $contract->class_id;
                }
                $renew = (int)$renewed > 0 ? 1 : $renew;
                $renew = ($renewed > 0 && ((int)$renewed == (int)count($data->contracts))) ? 2 : $renew;
                $reserveModel = new Reserve();
                $class_days = u::getMultiClassDays($class_ids);
                $reserved_dates = self::getReservedDates_transfer($contract_ids);
                foreach ($reserved_dates as $reserved_dates_key => $reserved_dates_value) {
                    foreach ($reserved_dates_value as $k => $v) {
                        $reserved_sessions += $v->sessions;
                    }
                }
                $data->renew_information = (object)[
                    'status' => (int)$renew,
                    'new' => (int)(count($data->contracts) - $renewed),
                    'renewed' => (int)$renewed,
                    'branch_id' => (int)$student->branch_id,
                    'check_new_payment' => $student->check_new_payment,
                    'sibling_id' => $student->sibling_id,
                    'from_student_id' => $student->id,
                ];
                $total_available_sessions = 0;
                $total_available_amount = 0;
                $data->reserved_all = (int)$reserved_sessions;
                if (count($data->contracts)) {
                    $dctr = $data->contracts[0];
                    $holi_day = u::getPublicHolidays(0, $dctr->branch_id, $dctr->product_id);
                    $i = 0;
                    foreach ($data->contracts as $contract) {
                        if (isset($reserved_dates[$contract->contract_id])) {
                            $contract->reserved_dates = $reserved_dates[$contract->contract_id];
                        } else {
                            $contract->reserved_dates = [];
                        }

                        $reserved_dates = $contract->reserved_dates;
                        $holi_day = array_merge($holi_day, $reserved_dates);
                        if (isset($class_days[$contract->class_id])) {
                            $contract->class_days = $class_days[$contract->class_id];
                        } else {
                            $contract->class_days = u::getDefaultClassDays($contract->product_id);
                        }
                        if ($contract->class_id) {
                            $today = date('Y-m-d', strtotime("-1 days"));
                            $done_sessions_info = u::calSessions($contract->start_date, $today, $holi_day, $contract->class_days);
                            $done_sessions = (int)$done_sessions_info->total;
                            $sessions = $contract->real_sessions > $done_sessions ? $contract->real_sessions - $done_sessions : 0;
                            $amount = $contract->real_sessions ? ceil(($contract->total_charged / $contract->real_sessions) * $sessions) : 0;
                            $contract->done_bonus_sessions = $done_sessions > $contract->real_sessions ? ($done_sessions - $contract->real_sessions > $contract->bonus_sessions ? $contract->bonus_sessions : $done_sessions - $contract->real_sessions) : 0;
                            $contract->done_sessions = $contract->real_sessions > $done_sessions ? $done_sessions : $contract->real_sessions;
                            $contract->done_amount = $contract->total_charged - $amount;
                            $contract->left_amount = $amount;
                            $contract->left_sessions = $sessions;
                            $contract->total_charged = $contract->total_charged;
                            $contract->origin_total_charged = $contract->total_charged;
                            $contract->origin_real_sessions = $contract->real_sessions;
                            $total_available_sessions += (int)$contract->real_sessions + (int) $contract->bonus_sessions;
                            $total_available_amount += $amount;
                        } else {
                            $contract->done_bonus_sessions = 0;
                            $contract->done_sessions = 0;
                            $contract->done_amount = 0;
                            $contract->left_amount = $contract->total_charged;
                            $contract->left_sessions = $contract->real_sessions;
                            $contract->total_charged = $contract->total_charged;
                            $contract->origin_total_charged = $contract->total_charged;
                            $contract->origin_real_sessions = $contract->real_sessions;
                            $total_available_sessions += (int)$contract->real_sessions + (int) $contract->bonus_sessions;
                            $total_available_amount += $contract->left_amount;
                        }
                        $contract->order = $i + 1;
                        $i++;
                    }
                }
                $data->total_available_sessions = $total_available_sessions;
                $data->total_available_amount = $total_available_amount;
                if ($data->total_available_sessions == 0) {
                    $data->has_error = 1;
                    $data->message = 'Học sinh không còn buổi học để chuyển phí';
                }
            }
        }
        return $data;
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
                        $res[$da->contract_id][] = (object)['start_date' => $da->start_date, 'end_date' => $da->end_date, 'sessions' => $da->sessions];
                    } else {
                        $res[$da->contract_id][] = (object)['start_date' => $da->start_date, 'end_date' => $da->end_date, 'sessions' => $da->sessions];
                    }
                }
            }
        }

        return $res;
    }
    public function suggestReceiver($key, $branch_id, $excepted_student_id)
    {
        $search_condition = "";
        if ($key && $key !== '_') {
            if (is_numeric($key)) {
                $search_condition .= " AND (s.id LIKE '$key%' OR s.stu_id LIKE '$key%')";
            } else {
                $search_condition .= " AND (s.name LIKE '$key%' OR s.phone LIKE '$key%' OR s.crm_id LIKE '$key%')";
            }
        }
        $query = "SELECT 
            s.`id` student_id,
            s.crm_id,
            s.`stu_id` lms_id,
            COALESCE(s.`stu_id`, s.crm_id) client_id,
            s.branch_id,
            s.accounting_id,
            s.`name` student_name,
            s.`nick`,
            s.type student_type,
            s.sibling_id
        FROM
            students s
        WHERE s.status > 0 AND s.id != $excepted_student_id AND s.branch_id = $branch_id $search_condition 
            LIMIT 10";
        $res = u::query($query);
        $result = [];
        if (!empty($res)) {
            foreach ($res as &$r) {
                $type = (int)$r->student_type === 1 ? ' (VIP)' : '';
                $r->label = $r->student_name . ' - ' . $r->client_id . $type;
                $result[$r->student_id] = $r;
            }
            $result = array_values($result);
        }
        return $result;
    }
    public function getReceiversLatestContract($request, $student_id, $from_student_id)
    {
        $student_info = Student::find($student_id);
        $waiting_status = [
            0 => 'không có bản ghi chờ duyệt nào',
            1 => 'Chờ duyệt chuyển phí',
            2 => 'Chờ duyệt nhận phí',
            3 => 'Chờ duyệt chuyển trung tâm',
            4 => 'Chờ duyệt bảo lưu',
            5 => 'Chờ duyệt chuyển lớp',
        ];

        $data = (object)[];
        $data->alert = '';
        $data->warning = '';
        if ($student_id == 'undefined') {
            $student = false;
            $data->alert = 'Dữ liệu không hợp lệ';
            $data->warning = 'Chọn lại học sinh khác';
        }
        if ($student_info->waiting_status != 0) {
            $student = false;
            $data->alert = $waiting_status[$student_info->waiting_status];
            $data->warning = $waiting_status[$student_info->waiting_status] . " chọn lại học sinh khác";
        } else {
            $student = u::first("SELECT s.id,s.name,s.crm_id,s.stu_id,s.status,s.accounting_id,s.type,s.date_of_birth,s.branch_id,
                s.email,s.phone,s.gud_name1,s.gud_mobile1,s.gud_email1,s.gud_name2,s.gud_mobile2,s.gud_email2,
                s.address,s.nick,s.school,s.school_grade,s.gender, t.ec_id, t.cm_id, t.ec_leader_id, t.om_id, 
                IF((SELECT count(p.id) FROM contracts AS c LEFT JOIN payment AS p ON p.contract_id = c.id where p.id IS NOT NULL AND c.student_id=s.id)>0,0,1) AS is_new,
                s.sibling_id
            FROM students s LEFT JOIN term_student_user t ON t.student_id = s.id 
            WHERE s.id = $student_id AND s.status >0");
            $from_student_info = u::first("SELECT sibling_id FROM students WHERE id= $from_student_id");
            // if (!$student->is_new && $from_student_info->sibling_id != $student_id && $student->sibling_id != $from_student_id) {
            //     $student = false;
            //     $data->alert = "Học sinh nhận không phải là học sinh new, không phải là anh chị em học cùng nên không được chuyển phí";
            //     $data->warning = "Học sinh nhận không phải là học sinh new, không phải là anh chị em học cùng nên không được chuyển phí";
            // } else {
                $check_coc = u::first("SELECT count(id) AS total FROM contracts WHERE student_id =$student_id AND `status`!=7 AND debt_amount>0 AND `type`>0");
                if($check_coc->total>0){
                    $data->sibling = 0;
                    $cond=" AND debt_amount>0";
                }else{
                    $data->sibling = $from_student_info->sibling_id == $student_id || $student->sibling_id == $from_student_id ? 1 : 0;
                    $cond=" ";
                }
                $data->contract = u::first("SELECT  c.id AS contract_id, c.code, 
                        c.branch_id,
                        c.count_recharge,
                        c.total_sessions, 
                        c.reservable_sessions,
                        c.reserved_sessions,
                        c.accounting_id,
                        IF(c.product_id > 0, c.product_id, (SELECT product_id FROM term_program_product WHERE program_id = c.program_id)) product_id,
                        (SELECT `name` FROM products WHERE id = c.product_id) product_name,
                        IF(c.program_id > 0, (SELECT `name` FROM programs WHERE id = c.program_id), (SELECT `name` FROM programs WHERE id = c.program_label)) program_name,
                        c.program_id,
                        c.program_label,
                        c.enrolment_last_date,
                        c.enrolment_start_date,
                        c.status,
                        COALESCE(c.enrolment_last_date, c.end_date) last_date, 
                        (SELECT `name` FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_name,
                        (SELECT `cls_name` FROM classes WHERE id = c.class_id) class_name,
                        c.class_id,
                        c.`type` contract_type,
                        c.`payload` payload,
                        c.`must_charge` must_charge,
                        c.`real_sessions`,
                        c.total_charged,
                        c.bonus_sessions,
                        c.debt_amount,
                        IF(c.`class_id` IS NULL, c.`start_date`, c.`enrolment_start_date`) start_date,
                        IF(c.`class_id` IS NULL, c.`end_date`, c.`enrolment_last_date`) end_date,
                        IF(c.class_id IS NULL, 0,1) has_enrolment,
                        c.tuition_fee_id,
                        (SELECT receivable FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_receivable,
                        (SELECT `session` FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_sessions
                    FROM contracts AS c WHERE c.student_id = $student_id $cond ORDER BY c.count_recharge DESC LIMIT 1");
                $data->product = $data->contract ? $data->contract->product_id : 0;
                if(!($from_student_info->sibling_id == $student_id || $student->sibling_id == $from_student_id) && (!$data->contract || $data->contract->contract_type==0 || $data->contract->debt_amount==0)){
                    $student = false;
                    $data->alert = "Học sinh chưa có gói nhận chuyển phí. Trung tâm vào nhập học tạo gói phí";
                    $data->warning = "Học sinh chưa có gói nhận chuyển phí. Trung tâm vào nhập học tạo gói phí";
                }
            // }
        }
        $data->student = $student;
        return $data;
    }
    public static function prepareTransferData($data)
    {
        $alert = '';
        $session = $data->user;
        $sender = (object)$data->sender;
        $receiver = (object)$data->receiver;
        $transfer_to_contract = (object)$data->receiver_contract;
        $transfer_contracts = (array)$data->transfer_contracts;
        $receiver_contracts = [];
        $transfer_date = $data->transfer_date;
        $total_transfered_amount = 0;
        $amount_truythu = $data->amount_truythu;
        $sibling = $data->sibling;
        $sibling_product = $data->sibling_product;
        $total_transfer_session = 0;
        $total_transfered_session = 0;
        if (count($transfer_contracts)) {
            $holi_day = u::getPublicHolidays(0, $sender->branch_id, $transfer_contracts[0]['product_id']);
            $contract_ids = [];
            foreach ($transfer_contracts as $i => $contract) {
                $contract_ids[] = $contract['contract_id'];
            }
            $reserved_dates = self::getReservedDates_transfer($contract_ids);
            $to_contract_info = u::first("SELECT * FROM contracts WHERE student_id =  $receiver->id AND status <7 ORDER BY count_recharge DESC LIMIT 1");
            if ($to_contract_info) {
                if ($to_contract_info->class_id) {
                    $tmp_transfer_date = date('Y-m-d', strtotime($to_contract_info->enrolment_last_date . " +1 days"));
                } else {
                    $tmp_transfer_date = date('Y-m-d', strtotime($to_contract_info->end_date . " +1 days"));
                }
            } else {
                $tmp_transfer_date = $transfer_date;
            }
            foreach ($transfer_contracts as $i => $contract) {
                $contract = (object)$contract;
                $class_days = $contract->class_days ? $contract->class_days : [2];
                $merged_holi_day = $reserved_dates && isset($reserved_dates[$contract->contract_id]) ? array_merge($holi_day, $reserved_dates[$contract->contract_id]) : $holi_day;
                $transfer_contracts[$i]['left_amount'] = 0;
                $transfer_contracts[$i]['left_sessions'] = 0;
                $transfer_contracts[$i]['done_sessions'] = 0;
                $transferable_sessions = $contract->real_sessions;
                if (!$sibling) {
                    if ($i == 0 && $contract->class_id) {
                        $done_sessions_info = u::calSessions($contract->start_date, date('Y-m-d', strtotime($transfer_date . " -1 days")), $merged_holi_day, $class_days);
                        $tmp_done_sessions_info = u::calSessions($contract->start_date, date('Y-m-d', strtotime("-1 days")), $merged_holi_day, $class_days);
                        $done_sessions = (int)$done_sessions_info->total;
                        $tmp_done_sessions = (int)$tmp_done_sessions_info->total;
                        $transferable_sessions = $contract->real_sessions - $done_sessions > 0 ? $contract->real_sessions - $done_sessions : 0;
                        $left_sessions = $contract->real_sessions > $done_sessions ? ($done_sessions - $tmp_done_sessions) : ($contract->real_sessions > $tmp_done_sessions ? $contract->real_sessions - $tmp_done_sessions : 0);
                        $left_soon_amount = $contract->origin_real_sessions ? ceil($left_sessions * ($contract->origin_total_charged / $contract->origin_real_sessions)) : 0;
                        $done_bonus_sessions = $done_sessions > $contract->real_sessions ? ($done_sessions - $contract->real_sessions > $contract->bonus_sessions ? $contract->bonus_sessions : $done_sessions - $contract->real_sessions) : 0;
                        $transfer_contracts[$i]['left_amount'] = $left_soon_amount;
                        $transfer_contracts[$i]['left_sessions'] = $left_sessions;
                        $transfer_contracts[$i]['done_sessions'] = $tmp_done_sessions > $contract->real_sessions ? $contract->real_sessions : $tmp_done_sessions;
                        $transfer_contracts[$i]['done_bonus_sessions'] = $done_bonus_sessions;
                    }
                    $transferred_amount = ceil(($transferable_sessions * $contract->origin_total_charged) / $contract->origin_real_sessions);
                    $total_transfered_amount += $transferred_amount;
                    $transfer_contracts[$i]['from_sessions'] = $transferable_sessions;
                    $transfer_contracts[$i]['from_amount'] =  $transferred_amount;
                } else {
                    $receiver_contract = (object)[];
                    $receiver_contract->transfered_contract_id = $contract->contract_id;
                    $receiver_contract->reservable_sessions = $contract->reservable_sessions;
                    $receiver_contract->reserved_sessions = $contract->reserved_sessions;
                    $receiver_contract->product_id = isset($transfer_to_contract->product_id) ? $transfer_to_contract->product_id: $sibling_product;
                    $receiver_contract->program_id = isset($transfer_to_contract->program_id) ? $transfer_to_contract->program_id : 0;
                    $sibling_product_info = u::first("SELECT `name` FROM products WHERE id=$sibling_product");
                    $receiver_contract->product_name = isset($transfer_to_contract->product_name)? $transfer_to_contract->product_name :($sibling_product_info?$sibling_product_info->name:'');

                    if ($i == 0 && $contract->class_id) {
                        $done_sessions_info = u::calSessions($contract->start_date, date('Y-m-d', strtotime($transfer_date . " -1 days")), $merged_holi_day, $class_days);
                        $tmp_done_sessions_info = u::calSessions($contract->start_date, date('Y-m-d', strtotime("-1 days")), $merged_holi_day, $class_days);
                        $done_sessions = (int)$done_sessions_info->total;
                        $tmp_done_sessions = (int)$tmp_done_sessions_info->total;
                        $transferable_sessions = $contract->real_sessions - $done_sessions > 0 ? $contract->real_sessions - $done_sessions : 0;
                        $left_sessions = $contract->real_sessions > $done_sessions ? ($done_sessions - $tmp_done_sessions) : ($contract->real_sessions > $tmp_done_sessions ? $contract->real_sessions - $tmp_done_sessions : 0);
                        $left_soon_amount = $contract->origin_real_sessions ? ceil($left_sessions * ($contract->origin_total_charged / $contract->origin_real_sessions)) : 0;
                        $done_bonus_sessions = $done_sessions > $contract->real_sessions ? ($done_sessions - $contract->real_sessions > $contract->bonus_sessions ? $contract->bonus_sessions : $done_sessions - $contract->real_sessions) : 0;
                        $transfer_contracts[$i]['left_amount'] = $left_soon_amount;
                        $transfer_contracts[$i]['left_sessions'] = $left_sessions;
                        $transfer_contracts[$i]['done_sessions'] = $tmp_done_sessions > $contract->real_sessions ? $contract->real_sessions : $tmp_done_sessions;
                        $transfer_contracts[$i]['done_bonus_sessions'] = $done_bonus_sessions;
                        $receiver_contract->real_sessions = $transferable_sessions;
                        $receiver_contract->total_charged = $contract->origin_real_sessions ? ceil($transferable_sessions * ($contract->origin_total_charged / $contract->origin_real_sessions)) : 0;
                        $receiver_contract->bonus_sessions = $contract->bonus_sessions - $done_bonus_sessions;
                    } else {
                        $receiver_contract->real_sessions = $contract->real_sessions;
                        $receiver_contract->total_charged = $contract->total_charged;
                        $receiver_contract->bonus_sessions = $contract->bonus_sessions;
                    }
                    $transfer_contracts[$i]['from_sessions'] = $receiver_contract->real_sessions + $receiver_contract->bonus_sessions;
                    $receiver_contract->to_sessions = $receiver_contract->real_sessions + $receiver_contract->bonus_sessions;
                    $transfer_contracts[$i]['from_amount'] = $receiver_contract->total_charged;
                    $receiver_contract->to_amount = $receiver_contract->total_charged;
                    $receiver_contract->left_sessions = $receiver_contract->to_sessions;
                    $receiver_contract->left_amount = $receiver_contract->to_amount;
                    $receiver_contract->transfered_reservable_sessions = $contract->transfer_reservable_sessions;
                    $total_transfered_session += $receiver_contract->to_sessions;
                    $total_transfer_session += $transfer_contracts[$i]['from_sessions'];

                    $receiver_tuition_fee_info = u::first("SELECT t.* FROM tuition_fee AS t LEFT JOIN tuition_fee_relation AS r ON r.exchange_tuition_fee_id=t.id  
                        WHERE t.product_id = $sibling_product AND (t.branch_id LIKE '%,$receiver->branch_id' OR t.branch_id 
                            LIKE '%,$receiver->branch_id,%' OR t.branch_id LIKE '$receiver->branch_id,%' OR t.branch_id = '$receiver->branch_id') AND r.tuition_fee_id=$contract->tuition_fee_id");
                    if($receiver_tuition_fee_info ){
                        $receiver_contract->tuition_fee_id = $receiver_tuition_fee_info ? $receiver_tuition_fee_info->id : 0;
                        $receiver_contract->tuition_fee_name = $receiver_tuition_fee_info ? $receiver_tuition_fee_info->name : 0;
                        $tname = $receiver_contract->tuition_fee_name;
                        $receiver_contract->code = isset($transfer_to_contract->code) ? $transfer_to_contract->code : "TT$contract->code $tname";
                        $receiver_contract->expected_start_date = isset($transfer_to_contract->date) ? $transfer_to_contract->date : NULL;

                        if ($i == 0) {
                            $receiver_contract->start_date = $tmp_transfer_date;
                            $transfered_date = u::getRealSessions($receiver_contract->to_sessions, [2], $holi_day, $receiver_contract->start_date);
                            $receiver_contract->end_date = $transfered_date->end_date;
                            $transfer_date = $receiver_contract->end_date;
                        } else {
                            $receiver_contract->start_date = strtotime(date("Y-m-d", strtotime($transfer_date)) . " +1 day");
                            $receiver_contract->start_date = strftime("%Y-%m-%d", $receiver_contract->start_date);
                            $transfered_date = u::getRealSessions($receiver_contract->to_sessions, [2], $holi_day, $receiver_contract->start_date);
                            $receiver_contract->end_date = $transfered_date->end_date;
                            $transfer_date = $receiver_contract->end_date;
                        }

                        $contract_hieuluc = self::getavailable_contracts($receiver->id);
                        if (!isset($transfer_to_contract->date) && $i == 0 && $contract_hieuluc ) {
                            if (!$contract_hieuluc->class_id) {
                                $start_date = strtotime(date("Y-m-d", strtotime($contract_hieuluc->end_date)) . " +1 day");
                                $start_date = strftime("%Y-%m-%d", $start_date);
                                $transfered_date = u::getRealSessions($receiver_contract->to_sessions, [2], $holi_day, $start_date);
                                $receiver_contract->start_date = $start_date;
                                $receiver_contract->end_date = $transfered_date->end_date;
                                if ($transfer_date > $contract_hieuluc->end_date) {
                                    $receiver_contract->start_date = $transfer_date;
                                    $transfered_date = u::getRealSessions($receiver_contract->to_sessions, [2], $holi_day, $transfer_date);
                                    $receiver_contract->end_date = $transfered_date->end_date;
                                }
                            } elseif ($contract_hieuluc->class_id && $contract_hieuluc->status == 6) {
                                $start_date = strtotime(date("Y-m-d", strtotime($contract_hieuluc->enrolment_last_date)) . " +1 day");
                                $start_date = strftime("%Y-%m-%d", $start_date);
                                $receiver_contract->start_date = $start_date;
                                $transfered_date = u::getRealSessions($receiver_contract->to_sessions, $contract_hieuluc->class_days, $holi_day, $start_date);
                                $receiver_contract->end_date = $transfered_date->end_date;
                                if ($transfer_date > $contract_hieuluc->enrolment_last_date) {
                                    $receiver_contract->start_date = $transfer_date;
                                    $transfered_date = u::getRealSessions($receiver_contract->to_sessions, $contract_hieuluc->class_days, $holi_day, $transfer_date);
                                    $receiver_contract->end_date = $transfered_date->end_date;
                                }
                            }
                            $transfer_date = $receiver_contract->end_date;
                        }
                        if (!isset($transfer_to_contract->date) && $i > 0) {
                            $transfered_date = u::getRealSessions($receiver_contract->to_sessions, [2], $holi_day, $transfer_date);
                            $start_date = strtotime(date("Y-m-d", strtotime($transfer_date)) . " +1 day");
                            $start_date = strftime("%Y-%m-%d", $start_date);
                            $receiver_contract->start_date = $start_date;
                            $receiver_contract->end_date = $transfered_date->end_date;
                            $transfer_date = $receiver_contract->end_date;
                        }
                        $transferred_amount = $contract->origin_real_sessions ? ceil($transferable_sessions * ($contract->origin_total_charged / $contract->origin_real_sessions)):0;
                        $total_transfered_amount += $transferred_amount;
                        $receiver_contract->order = $i + 1;
                        $receiver_contracts[] = $receiver_contract;
                    }else{
                        $data->alert = "Chưa có gói phí quy đổi tương ứng!";
                        return $data;
                    }
                }
            }
        }
        if($total_transfered_amount==0 & $total_transfer_session==0){
            $data->alert = "Số tiền được chuyển phải lớn hơn 0!";
        }elseif (!$sibling && $total_transfered_amount - $amount_truythu > $transfer_to_contract->debt_amount) {
            $data->alert = "Công nợ của học sinh nhận phải lớn hơn hoặc bằng số tiền được chuyển!";
        } else {
            $data = (object)[];
            $data->alert = $alert;
            $data->total_transfer_amount = $total_transfered_amount;
            $data->total_transfered_amount = $sibling ? $total_transfered_amount : $total_transfered_amount - $amount_truythu;
            $data->expected_start_date = isset($transfer_to_contract->date) ? $transfer_to_contract->date : NULL;
            $data->total_transfer_session = $total_transfer_session;
            $data->total_transfered_sessions = $total_transfered_session;
            $data->transfered_contracts = $transfer_contracts;
            $data->received_contracts = $receiver_contracts;
        }
        return $data;
    }
    public static function storeTransferedData($transsfered_data)
    {
        $waiting_status = [
            0 => 'không có bản ghi chờ duyệt nào',
            1 => 'Chờ duyệt chuyển phí',
            2 => 'Chờ duyệt nhận phí',
            3 => 'Chờ duyệt chuyển trung tâm',
            4 => 'Chờ duyệt bảo lưu',
            5 => 'Chờ duyệt chuyển lớp'
        ];
        if ($transsfered_data->file) {
            $attached_file = ada()->upload($transsfered_data->file);
        } else {
            $attached_file = '';
        }
        $sender = (object)$transsfered_data->sender;
        $receiver = (object)$transsfered_data->receiver;
        $info_student_data_chuyen = [];
        $info_student_data_chuyen['id'] = $sender->id;
        $senders_waiting_status = self::waiting_status_Update_Student($info_student_data_chuyen);
        if ($senders_waiting_status->waiting_status != 0) {
            return $waiting_status[$senders_waiting_status->waiting_status];
        }
        $info_student_data_nhan = [];
        $info_student_data_nhan['id'] = $receiver->id;
        $receivers_waiting_status = self::waiting_status_Update_Student($info_student_data_nhan);
        if ($receivers_waiting_status->waiting_status != 0) {
            return $waiting_status[$receivers_waiting_status->waiting_status];
        }
        #-----------------------------------------------
        $from_branch_id = $sender->branch_id;
        $to_branch_id = $receiver->branch_id;
        $result = null;
        #=========================================
        $sender_id = $sender->id; # mã học sinh chuyển
        $receiv_id = $receiver->id; # mã học sinh nhận
        $created_at = date("Y-m-d H:i:s");
        $user_id = $transsfered_data->user_id;
        $transfer_note = $transsfered_data->transfer_note;
        $transfer_date = $transsfered_data->transfer_date;
        $to_total_amount = $transsfered_data->to_total_amount;
        $to_total_session = $transsfered_data->sibling ? $transsfered_data->to_total_sessions : 0 ; //vì CMS  chỉ tính chuyển phí ko chuyển buổi
        $from_total_amount = $transsfered_data->from_total_amount;
        $from_total_sessions = $transsfered_data->sibling ? $transsfered_data->from_total_sessions : 0 ; //vì CMS  chỉ tính chuyển phí ko chuyển buổi
        $receiver_contracts = $transsfered_data->receiver_contracts;
        $transfer_contracts = $transsfered_data->transfer_contracts;
        $amount_truythu = $transsfered_data->amount_truythu;
        $reason_id = $transsfered_data->reason_id;
        $sibling = $transsfered_data->sibling;
        $to_product_id = $transsfered_data->sibling_product;
        $from_product_id = 0;
        # bắt đầu tính toán để lưu
        foreach ($transfer_contracts as $k => $transfer_contracts_value) {
            if ($k == 0) {
                $from_product_id = $transfer_contracts_value['product_id'];
                $from_program_id = $transfer_contracts_value['program_id'];
                $from_class_id = $transfer_contracts_value['class_id'];
            }
        }

        if (count($transfer_contracts) && count($receiver_contracts)) {
            $transferred_contracts = u::escapeJsonString(json_encode($transfer_contracts, JSON_UNESCAPED_UNICODE));
            $received_contracts = u::escapeJsonString(json_encode($receiver_contracts, JSON_UNESCAPED_UNICODE));
            $hash = md5("$sender_id.$receiv_id.$from_total_amount.$to_total_amount.$from_total_sessions.$to_total_session.$transferred_contracts.$received_contracts");

            $code = u::unix();
            $sql = "INSERT INTO tuition_transfer_v2 
                (`code`,
                from_student_id,
                to_student_id,
                `type`,
                `note`,
                transfer_date,
                `status`,
                from_branch_id,
                to_branch_id,
                creator_id,
                created_at,
                transferred_amount,
                received_amount,
                transferred_sessions,
                received_sessions,
                transferred_data,
                received_data,
                attached_file,
                version,
                from_program_id,
                from_class_id,
                from_product_id,
                to_product_id,
                hash_key,
                amount_truythu,
                reason_id,
                sibling)
                VALUES
                ('$code', 
                '$sender_id',
                '$receiv_id',
                0,
                '$transfer_note',
                '$transfer_date',
                1,
                $from_branch_id,
                $to_branch_id,
                $user_id,
                '$created_at',
                $from_total_amount,
                $to_total_amount,
                $from_total_sessions,
                $to_total_session,
                '$transferred_contracts',
                '$received_contracts',
                '$attached_file',
                3,
                '$from_program_id',
                '$from_class_id',
                '$from_product_id',
                '$to_product_id',
                '$hash',
                '$amount_truythu',
                '$reason_id',
                '$sibling')
            ";
            $insert = u::query($sql);
            u::query("UPDATE students SET waiting_status = 1 WHERE id =$sender->id");
            u::query("UPDATE students SET waiting_status = 2 WHERE id =$receiver->id");
            $result = $code;
        }
        $info_insert = u::first("SELECT id FROM tuition_transfer_v2 WHERE from_student_id = $sender->id AND to_student_id=$receiver->id AND `status`=1 ORDER BY id LIMIT 1");
        self::sendMail( $info_insert->id);
        return "Bản ghi chuyển phí mã <b>" . $result . "</b> đã được khởi tạo thành công và chờ được phê duyệt!";
    }

    public static function approveTuitionTransfer($data, $request)
    {
        /*********************
            $data->approve_status = true <-> phê duyệt
            1. Tạo: status = 1
            2. Giám đốc đã từ chối = 2
            3. Kế toán HO đã từ chối = 3
            4. Giám đốc đã phê duyệt = 4
            5. Kế toán HO đã phê duyệt = 5
            6. Đã được phê duyệt bởi cả GDTT và Thu ngân = 6
         **********************/
        $tuition_transfer_detail = self::find($data->id);
        if (isset($tuition_transfer_detail->status)) {
            $data->status = $tuition_transfer_detail->status;
        }
        $user_id = $data->user_id;
        $status_update = '';
        $comment_update = '';
        $session = $data->session;
        $result = '';

        if (($data->approve_by === 'ceo' || $session->role_id == 999999999) && $data->status == 1) {

            $status = $data->approve_status ? 4 : 2;
            $status_update = "`status` = $status, ";
            $comment_update = "`ceo_comment` = '$data->ceo_branch_note', ";
            $ceo_approver_id = "`ceo_approver_id` = $session->id, ";
            $ceo_approved_at = "`ceo_approved_at` = NOW(), ";
            u::query("UPDATE tuition_transfer_v2 SET `updated_at` = NOW(), $ceo_approver_id $ceo_approved_at $status_update $comment_update `editor_id` = $session->id WHERE id = $data->id");
            u::query("INSERT INTO log_tuition_transfer (`tuition_transfer_id`,`status`,`creator_id`,`created_at`,`comment`) VALUES ($data->id, $status, $user_id, NOW(), '$data->note')");
            if($status==2){
                u::query("UPDATE students SET waiting_status = 0 WHERE id = $tuition_transfer_detail->from_student_id");
                u::query("UPDATE students SET waiting_status = 0 WHERE id = $tuition_transfer_detail->to_student_id");
                $result = "Bản ghi chuyển phí mã '$data->code' giám đốc trung tâm từ chối phê duyệt!";
            } else {
                $result = "Bản ghi chuyển phí mã '$data->code' giám đốc trung tâm phê duyệt!";
            }
        } elseif (($data->approve_by === 'accounting'  || $session->role_id == 999999999) && $data->status == 4) {
            u::query("UPDATE students SET waiting_status = 0 WHERE id = $tuition_transfer_detail->from_student_id");
            u::query("UPDATE students SET waiting_status = 0 WHERE id = $tuition_transfer_detail->to_student_id");

            $data_tuition_transfer = self::find($data->id);
            $status = $data->approve_status ? 6 : 3;
            $status_update = "`status` = $status, ";
            $comment_update = "`accounting_comment` = '$data->accounting_note', ";
            if ($status == 3) {
                $result = "Bản ghi chuyển phí mã '$data->code' kế toán HO từ chối phê duyệt!";

            } else {
                $result = "Bản ghi chuyển phí mã '$data->code' đã được hoàn thành!";

            }
            u::query("UPDATE tuition_transfer_v2 SET `updated_at` = NOW(), accounting_approver_id = $user_id, accounting_approved_at = NOW(), $status_update $comment_update `editor_id` = $session->id WHERE id = $data->id");
            u::query("INSERT INTO log_tuition_transfer (`tuition_transfer_id`,`status`,`creator_id`,`created_at`,`comment`) VALUES ($data->id, $status, $user_id, NOW(), '$data->note')");

            if ($status == 6) {
                $contract_vips = u::query("SELECT * FROM contracts WHERE student_id = $data->from_student_id AND debt_amount = 0 AND status !=7 AND type IN (8,85,86) ");
                if ($contract_vips) {
                    foreach ($contract_vips as $contract_vip) {
                        u::updateContract((object)[
                            "id" => $contract_vip->id,
                            "status" => 7,
                            'action' => "tuition_transfer_update_sender_vip_approved_for_contract_$contract_vip->id",
                            "bonus_sessions" => 0,
                            "summary_sessions" => 0,
                            "end_date" => $contract_vip->start_date,
                        ]);
                    }
                }
                $contract_trail = u::first("SELECT * FROM contracts WHERE student_id = $data->from_student_id AND type=0 AND status!=7");
                if ($contract_trail) {
                    if ($contract_trail->class_id == NULL) {
                        u::updateContract((object)[
                            "id" => $contract_trail->id,
                            "status" => 7,
                            'action' => "tuition_transfer_update_sender_trail_approved_for_contract_$contract_trail->id",
                            "bonus_sessions" => 0,
                            "summary_sessions" => 0,
                            "end_date" => $contract_trail->start_date,
                        ]);
                    } elseif ($contract_trail->class_id != NULL && $contract_trail->enrolment_last_date > date('Y-m-d')) {
                        $trail_holiday = u::getPublicHolidays($contract_trail->class_id, $contract_trail->branch_id, $contract_trail->product_id);
                        $trail_class_day = u::getClassDays($contract_trail->class_id);
                        $trail_done_sessions = u::calSessions($contract_trail->start_date, date('Y-m-d'), $trail_holiday, $trail_class_day);
                        if ($trail_done_sessions->end_date) {
                            $trail_enrolment_last_date = $trail_done_sessions->end_date;
                        } else {
                            $trail_enrolment_last_date = $contract_trail->enrolment_start_date;
                        }
                        u::updateContract((object)[
                            "id" => $contract_trail->id,
                            'action' => "tuition_transfer_update_sender_trail_approved_for_contract_$contract_trail->id",
                            "bonus_sessions" => $trail_done_sessions->total,
                            "summary_sessions" => $trail_done_sessions->total,
                            "enrolment_last_date" => $trail_enrolment_last_date,
                            "status" => 7,
                        ]);
                    }
                }
                $contracts = [];
                $tmp_contract_id = 0;
                foreach (json_decode($data_tuition_transfer->transferred_data) as $ctrl) {
                    $ctrl = (object)$ctrl;
                    if ($tmp_contract_id == 0 || $tmp_contract_id > $ctrl->contract_id) {
                        $tmp_contract_id = $ctrl->contract_id;
                    }
                    $contract_info = Contract::find($ctrl->contract_id);
                    $end_date = $contract_info->enrolment_status > 0 ? $data_tuition_transfer->transfer_date : $contract_info->start_date;
                    $data_update_contract = (object)[
                        'id' => $ctrl->contract_id,
                        'end_date' => $end_date,
                        'editor_id' => $user_id,
                        'enrolment_piority' => 0,
                        'action' => "approve_tuition_transfer_for_contract_$ctrl->contract_id" . "_at_" . date('Ymd'),
                        'bonus_sessions ' => 0,
                        'updated_at'=>date('Y-m-d H:i:s'),
                        'reservable'=>0,
                        'enrolment_update_at'=>date('Y-m-d H:i:s'),
                        'enrolment_updatetor_id'=>$user_id,
                        'enrolment_withdraw_date'=>NULL,
                        'enrolment_left_session'=>0,
                    ];
                    // Not enroled
                    if (!$contract_info->class_id) {
                        $data_update_contract->status = 7;
                        $data_update_contract->enrolment_real_sessions = 0;
                        $data_update_contract->real_sessions = 0;
                        $data_update_contract->summary_sessions = 0;
                        $data_update_contract->total_charged = 0;
                        $data_update_contract->end_date = $contract_info->start_date;
                        $data_update_contract->bonus_sessions = 0;
                        $data_update_contract->reservable_sessions = 0;
                    }
                    // Already enroled
                    if ($contract_info->class_id ) {
                        $reserved_dates = self::getReservedDates_transfer([$ctrl->contract_id]);
                        $holi_day = u::getPublicHolidays($ctrl->class_id, $data_tuition_transfer->from_branch_id, $ctrl->product_id);
                        $tmp_holi_day = $holi_day;
                        $holi_day = $reserved_dates && isset($reserved_dates[$ctrl->contract_id]) ? array_merge($holi_day, $reserved_dates[$ctrl->contract_id]) : $holi_day;

                        $end_date_info = null;
                        $done_sessions = isset($ctrl->done_sessions) && $ctrl->done_sessions ? $ctrl->done_sessions : 0;
                        if (!isset($ctrl->done_bonus_sessions)) {
                            $ctrl->done_bonus_sessions = 0;
                        }
                        $done_sessions = $done_sessions + $ctrl->left_sessions + $ctrl->done_bonus_sessions;
                        $tuition_fee = TuitionFee::find($ctrl->tuition_fee_id);
                        $left_soon_amount = $ctrl->origin_real_sessions ? ceil(($ctrl->done_sessions + $ctrl->left_sessions) * ($ctrl->origin_total_charged / $ctrl->origin_real_sessions)) : 0;
                        $end_date_info = u::calSessions($contract_info->enrolment_start_date, $tuition_transfer_detail->transfer_date, $tmp_holi_day, $ctrl->class_days);

                        $enrolment_last_date = $end_date_info && isset($end_date_info->end_date) && $end_date_info->end_date  ? $end_date_info->end_date : $ctrl->enrolment_start_date;
                        $data_update_contract->reservable_sessions = (int)$ctrl->reserved_sessions;
                        $data_update_contract->real_sessions = $ctrl->done_sessions + $ctrl->left_sessions;
                        $data_update_contract->summary_sessions = $done_sessions;
                        $data_update_contract->bonus_sessions = $ctrl->done_bonus_sessions;
                        $data_update_contract->total_charged = $left_soon_amount;
                        $data_update_contract->enrolment_real_sessions = $ctrl->done_sessions;
                        $data_update_contract->enrolment_last_date = $enrolment_last_date;
                    } else {
                        $data_update_contract->status = 7;
                        $data_update_contract->bonus_sessions = 0;
                    }
                    $data_update_contract->processed = 0;
                    $contracts[] = $data_update_contract;
                }
                u::updateMultiContracts($contracts);

                #===========================Tạo contrat mới cho HS nhận phí===========================

                $data_tuition_transfers = json_decode($data_tuition_transfer->received_data);
                if($data_tuition_transfer->sibling==1){
                    $to_studen_id =  $data_tuition_transfer->to_student_id;
                    $to_student_info = self::getStudentsInformation($to_studen_id);
                    $ec_id = (int)$to_student_info->ec_id;
                    $cm_id = (int)$to_student_info->cm_id;
                    $om_id = (int)$to_student_info->om_id;
                    $branch_id = (int)$to_student_info->branch_id;
                    $region_id = (int)$to_student_info->region_id;
                    $ec_leader_id = (int)$to_student_info->ec_leader_id;
                    $ceo_branch_id = (int)$to_student_info->ceo_branch_id;
                    $ceo_region_id = (int)$to_student_info->ceo_region_id;
                    if (count($data_tuition_transfers) != 0) {
                        $count_recharge = u::first("SELECT MAX(count_recharge) count_recharge, id FROM contracts  WHERE student_id = $to_studen_id");
                        if ($count_recharge->count_recharge) {
                            u::query("UPDATE contracts SET processed = 20 WHERE student_id = " . $to_studen_id . " AND count_recharge = $count_recharge->count_recharge");
                        }
                        foreach ($data_tuition_transfers as $k => $data_received_data_insert) {
                            $data_received_data = (object)$data_received_data_insert;
                            $contracts_all = self::getContractsStudentAll($to_studen_id);
                            $start_date = $data_tuition_transfer->transfer_date;
                            $not_available = false;
                            $available_contracts = [];
                            $holi_day = u::getPublicHolidays(0, $data_tuition_transfer->to_branch_id, $data_received_data->product_id);
                            if ($contracts_all) {
                                $n = 0;
                                foreach ($contracts_all as $key => $ct) {
                                    if (in_array($ct->status, [0, 7, 8])) {
                                        $n = $n + 1;
                                    } else {
                                        $available_contracts[] = $ct;
                                    }
                                }
                                if ($n == count($contracts_all)) {
                                    $not_available = true;
                                    $data_start_date = isset($data_received_data->expected_start_date) ? $data_received_data->expected_start_date : $data_tuition_transfer->transfer_date;
                                    $real_end_date_info = u::calEndDate($data_received_data->left_sessions, [2, 5], $holi_day, $data_start_date);
                                } else {
                                    if (count($available_contracts) > 0) {
                                        $last_available_contract = $available_contracts[0];
                                        if (!$last_available_contract->class_id) {
                                            $start_date = strtotime(date("Y-m-d", strtotime($last_available_contract->end_date)) . " +1 day");
                                            $start_date = strftime("%Y-%m-%d", $start_date);
                                            $real_end_date_info  = u::getRealSessions($data_received_data->left_sessions, [2, 5], $holi_day, $start_date);
                                            if ($data_tuition_transfer->transfer_date > $last_available_contract->end_date) {
                                                $start_date = $data_tuition_transfer->transfer_date;
                                                $real_end_date_info  = u::getRealSessions($data_received_data->left_sessions, [2, 5], $holi_day, $start_date);
                                            }
                                        } elseif ($last_available_contract->class_id && $last_available_contract->status == 6) {
                                            $enrolment_last_date = strtotime(date("Y-m-d", strtotime($last_available_contract->enrolment_last_date)) . " +1 day");
                                            $enrolment_last_date = strftime("%Y-%m-%d", $enrolment_last_date);
                                            $start_date = $enrolment_last_date;
                                            $contract_hieuluc_get_class = self::getavailable_contracts($to_studen_id);
                                            $real_end_date_info  = u::getRealSessions($data_received_data->left_sessions, $contract_hieuluc_get_class->class_days, $holi_day, $start_date);
                                            if ($data_tuition_transfer->transfer_date > $last_available_contract->enrolment_last_date) {
                                                $contract_hieuluc_get_class = self::getavailable_contracts($to_studen_id);
                                                $start_date = $data_tuition_transfer->transfer_date; # coognj t
                                                $real_end_date_info  = u::getRealSessions($data_received_data->left_sessions, $contract_hieuluc_get_class->class_days, $holi_day, $start_date);
                                            }
                                        }
                                        $contract_type = $last_available_contract->type == 0 ? 4 : 3;
                                    }
                                }
                                $count_recharge = $contracts_all[0]->count_recharge + 1;
                            } else {
                                $not_available = true;
                                $data_start_date = isset($data_received_data->expected_start_date) ? $data_received_data->expected_start_date : $data_tuition_transfer->transfer_date;
                                $real_end_date_info  = u::getRealSessions($data_received_data->left_sessions, [2, 5], $holi_day, $data_start_date);
                                $count_recharge = 0;
                            }
                            if ($not_available) {
                                $contract_type = 4;
                                $start_date = $data_received_data->expected_start_date;
                            }
                            $end_date = $real_end_date_info->end_date;
                            $product_id = $data_received_data->product_id;
                            $expected_class = '';
                            $student_id = $to_studen_id;
                            $hash_key = md5("$student_id.$contract_type.$start_date.$end_date.$product_id.$expected_class");
                            if ($count_recharge > 0) {
                                $contract_type = 3;
                            }
                            $data_received_data_insert = (object)[
                                'type' => $contract_type,
                                'payload' => 0,
                                'student_id' => $to_studen_id,
                                'branch_id' => $branch_id,
                                'ceo_branch_id' => $ceo_branch_id,
                                'region_id' => $region_id,
                                'ceo_region_id' => $ceo_region_id,
                                'ec_id' => $ec_id,
                                'ec_leader_id' => $ec_leader_id,
                                'cm_id' => $cm_id,
                                'om_id' => $om_id,
                                // 'enrolment_real_sessions' => $data_received_data->left_sessions,
                                'relation_contract_id' => $data_received_data->transfered_contract_id,
                                'product_id' => $data_received_data->product_id,
                                'tuition_fee_id' => $data_received_data->tuition_fee_id,
                                'program_label' => NULL,
                                'nick' => $to_student_info->nick,
                                'receivable' => $data_received_data->left_amount,
                                'must_charge' => $data_received_data->left_amount,
                                'total_charged' => $data_received_data->left_amount,
                                'total_discount' => 0,
                                'debt_amount' => 0,
                                'description' => '',
                                'bill_info' => '',
                                'start_date' => $start_date ? $start_date :$data_tuition_transfer->transfer_date,
                                'end_date' => $end_date,
                                'total_sessions' => $data_received_data->real_sessions,
                                'real_sessions' => $data_received_data->real_sessions,
                                'bonus_sessions' => $data_received_data->bonus_sessions,
                                'summary_sessions' => $data_received_data->left_sessions,
                                'expected_class' => '',
                                'status' => 3,
                                'actived' => 1,
                                'action' => 'tuition_transfer_so_create_new_contract_' . date('Ymd'),
                                'created_at' => date('Y-m-d H:i:s'),
                                'creator_id' => $user_id,
                                'hash_key' => $hash_key,
                                'editor_id' => $user_id,
                                'only_give_tuition_fee_transfer' => 1,
                                'reservable' => 1,
                                // 'reservable_sessions' => $data_received_data->transfered_reservable_sessions,
                                'sibling_discount' => 0,
                                'discount_value' => 0,
                                'after_discounted_fee' => 0,
                                'info_available' => 2,
                                'tuition_fee_price' => 0,
                                'count_recharge' => $count_recharge,
                                'note' => ''
                            ];
                            $new_contract = u::createContract($data_received_data_insert);
                            $data_tuition_transfers[$k]->new_contract_id = $new_contract->id;
                            $contract_controller = new ContractsController();
                            $contract_controller->createCyberContract($new_contract->id,$user_id);
                            $contract_controller->createCyberStudent($to_studen_id, $user_id);
                        }
                        $data_tuition_transfers = json_encode($data_tuition_transfers);
                        u::query("UPDATE tuition_transfer_v2 SET received_data = '$data_tuition_transfers' WHERE id=$data->id");
                    }
                }else{
                    $received_amount = $data_tuition_transfer->received_amount;
                    foreach($data_tuition_transfers AS $contract){
                        if($received_amount){
                            $contract = (object)$contract;
                            $contract_info = u::first("SELECT * FROM contracts WHERE id=$contract->contract_id");
                            $debt = $contract_info->debt_amount > $received_amount ? $contract_info->debt_amount - $received_amount : 0 ;
                            $total = $contract_info->debt_amount > $received_amount ? $contract_info->total_charged + $received_amount : $contract_info->must_charge;
                            $contract_status = $debt > 0 ? ($contract_info->status == 6 ? 6 : 2) : ($contract_info->status == 6 ? 6 : 3);
                            $reservable_sessions = self::getMaxNumberOfReservesSessions($contract, $debt > 0);
                            $reservable = $reservable_sessions > $contract_info->reserved_sessions ? 1 : 0;
                            $mc = (int)$contract_info->must_charge;
                            $ts = (int)$contract_info->total_sessions;
                            $sp = (int)$ts !== 0 ? $mc / $ts : 0;
                            $real_sessions = (int)$sp !== 0 ? round($total / $sp) : 0;
                            $summary_sessions = $debt == 0 ? $real_sessions + $contract_info->bonus_sessions : $real_sessions;
                            $update_contract_data = (Object)[
                                'id' => $contract->contract_id,
                                'status' => $contract_status,
                                'total_charged' => $total,
                                'reservable_sessions' => $reservable_sessions > 0 ? $reservable_sessions : 'NULL',
                                'real_sessions' => $real_sessions,
                                'summary_sessions'=> $summary_sessions,
                                'debt_amount' => $debt,
                                'editor_id' => $user_id,
                                'updated_at' => date("Y-m-d H:i:s"),
                                'reservable' => $reservable,
                                'action' => "approve_tuition_transfer_for_contract_$ctrl->contract_id" . "_at_" . date('Ymd'),
                            ];
                            u::updateContract($update_contract_data);
                            $received_amount = $contract->debt_amount > $received_amount ? 0 : $received_amount - $contract->debt_amount ;
                        }
                    }
                }

                $lsmAPI = new LMSAPIController();
                $lsmAPI->updateStudentLMS($data->from_student_id);
                $lsmAPI->updateStudentLMS($data->to_student_id);
            }
        }
        self::sendMail($data->id);
        return $result;
    }
    static function waiting_status_Update_Student($data = [], $update = false)
    {
        if (is_array($data) && count($data) > 0 && $update && $data['id'] != '' && $data['id'] != 0) {
            $student = Student::find($data['id']);

            foreach ($data as $key => $value) {
                if ($key == 'id') {
                    $student->id = $value;
                } else {
                    $student->$key = $value;
                }
            }
            //dd($data);
            $student->save();

            return $student;
        }
        if (is_array($data) && count($data) > 0 && $data['id'] != '' && $data['id'] != 0 && $update == false) {
            return Student::find($data['id']);
        }
    }
    public static function getStudentsInformation($id = 0)
    {
        $id = (int)$id;
        $role_branch_ceo = 686868;
        $role_region_ceo = 7777777;
        $role_region_tn = 84;
        $role_om = 56;
        return u::first(
            "SELECT
            s.id student_id,
            s.crm_id crm_id,
            s.stu_id lms_id,
            s.accounting_id,
            s.stu_id,
            s.name,
            s.nick,
            s.email stu_email,
            s.phone home_phone,
            s.gender,
            s.branch_id, 
            s.type,
            s.phone,
            s.waiting_status, 
            COALESCE(s.sibling_id, 0) sibling,
            g.name school_grade,
            s.date_of_birth,
            CONCAT(s.name, ' - ', COALESCE(s.stu_id, crm_id)) label,
            COALESCE(s.gud_name1, s.gud_name2) parent_name,
            COALESCE(s.gud_mobile1, s.gud_mobile2) parent_mobile,
            COALESCE(s.gud_email1, s.gud_email2) parent_email,
            s.gud_name1,
            s.gud_mobile1,
            s.gud_name2,
            s.gud_mobile2,
            s.address,
            s.school,
            t.status student_status,
            s.branch_id branch_id,
            b.name branch_name,
            b.brch_id branch_lms_id,
            b.hrm_id branch_hrm_id,
            r.id region_id,
            r.name region_name,
            u0.id cm_id,
            u0.email cm_email,
            u0.full_name cm_name,
            u1.id ec_id,
            u1.hrm_id ec_hrm_id,
            u1.full_name ec_name,
            u2.id ec_leader_id,
            u2.full_name ec_leader_name,            
            (SELECT u3.id FROM users u3 LEFT JOIN term_user_branch tu ON u3.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_om' AND tu.status = 1 LIMIT 1) om_id,
            (SELECT u3.full_name FROM users u3 LEFT JOIN term_user_branch tu ON u3.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_om' AND tu.status = 1 LIMIT 1) om_name,
            (SELECT u3.email FROM users u3 LEFT JOIN term_user_branch tu ON u3.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_om' AND tu.status = 1 LIMIT 1) om_mail,
            (SELECT u5.email FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_tn' AND tu.status = 1 LIMIT 1) tn_branch_mail,
            (SELECT u4.id FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo' AND tu.status = 1 LIMIT 1) ceo_branch_id,
            (SELECT u4.email FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo' AND tu.status = 1 LIMIT 1) ceo_branch_mail,
            (SELECT u4.full_name FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo' AND tu.status = 1 LIMIT 1) ceo_branch_name,
            (SELECT u5.id FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_ceo' AND tu.status = 1 LIMIT 1) ceo_region_id,
            (SELECT u5.full_name FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_ceo' AND tu.status = 1 LIMIT 1) ceo_region_name,
            (SELECT SUM(bonus_sessions) FROM contracts WHERE student_id=s.id AND `type`=0) AS trial_sessions,
            b.zone_id,
            s.source
        FROM students s
            LEFT JOIN term_student_user t ON t.student_id = s.id
            LEFT JOIN branches b ON t.branch_id = b.id
            LEFT JOIN regions r ON b.region_id = r.id
            LEFT JOIN school_grades g ON s.school_grade = g.id
            LEFT JOIN users u0 ON t.cm_id = u0.id
            LEFT JOIN users u1 ON t.ec_id = u1.id
            LEFT JOIN users u2 ON u1.superior_id = u2.hrm_id
            LEFT JOIN users u3 ON u0.superior_id = u3.hrm_id
            LEFT JOIN (
            SELECT ct.id id, ct.type `type`
                FROM contracts ct
                    LEFT JOIN students st ON ct.student_id = st.id
                WHERE ct.type = 0 AND st.status >0) c ON c.id = s.id
        WHERE s.id = $id AND s.status >0 LIMIT 0, 1
        "
        );
    }
    public static function getavailable_contracts($studen_id)
    {
        $data =  u::first(
            "SELECT 
            c.id,    
            c.type,           
            c.count_recharge,
            c.enrolment_last_date,
            c.status,
            c.start_date,
            c.class_id,
            c.product_id,
            c.end_date   
        FROM contracts c
            LEFT JOIN students s ON c.student_id = s.id
            LEFT JOIN branches b ON c.branch_id = b.id
            LEFT JOIN zones z ON b.zone_id = z.id
        WHERE s.status > 0 AND c.student_id = $studen_id and c.status NOT IN (0,7,8)  ORDER BY  c.count_recharge DESC
        "
        );

        if ($data) {
            $data->class_days = u::getClassDays($data->class_id);
            return $data;
        }
    }
    public static function sendMail($transfer_id)
    {
        $transfer_info = u::first("SELECT t.from_branch_id,t.to_branch_id,t.note,t.ceo_comment,t.accounting_comment,t.status,
                (SELECT name FROM students WHERE id= t.from_student_id) AS from_student_name,
                (SELECT name FROM students WHERE id= t.to_student_id) AS to_student_name,
                (SELECT name FROM branches WHERE id = t.from_branch_id) AS from_branch_name,
                (SELECT name FROM branches WHERE id = t.to_branch_id) AS to_branch_name,
                (SELECT email FROM users WHERE id= t.creator_id) AS creator_email
            FROM tuition_transfer_v2 AS t  WHERE t.id=$transfer_id");
        $arr_mail_from = (object)JobsController::getEmail($transfer_info->from_branch_id);
        // $arr_mail_to = (object)JobsController::getEmail($transfer_info->to_branch_id);
        $mail = new Mail();
        if($transfer_info->status==1){
            $to = array('address' => $arr_mail_from->gdtt, 'name' => $arr_mail_from->gdtt);
            $subject = "[CRM] Yêu cầu phê duyệt chuyển phí của bé $transfer_info->from_student_name";
            $body = "<p>Kính gửi: GĐTT $transfer_info->from_branch_name </p>
                        <p>Hệ thống CRM xin thông báo: Anh/chị đã nhận được yêu cầu <strong>chuyển phí</strong> của bé <strong>$transfer_info->from_student_name</strong> $transfer_info->from_branch_name chuyển phí sang bé <strong>$transfer_info->to_student_name</strong> $transfer_info->to_branch_name</p>
                        <p>Nội dung: $transfer_info->note</p>
                        <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM để phê duyệt yêu cầu.</p>
                        <p>Hoặc truy cập link: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết yêu cầu</p>
                        <p>Trân trọng cảm ơn!</p>
                    ";
            $mail->sendSingleMail($to, $subject, $body,[$arr_mail_from->cskh]);
        }elseif($transfer_info->status==2){
            $to = array('address' => $transfer_info->creator_email, 'name' => $transfer_info->creator_email);
            $subject = "[CRM] GĐTT $transfer_info->from_branch_name đã từ chối yêu cầu chuyển phí của bé $transfer_info->from_student_name";
            $comment = $transfer_info->ceo_comment != '' ? $transfer_info->ceo_comment : "Không có lý do cụ thể.";
            $body = "<p>Kính gửi: Phòng CS</p>
                            <p>Hệ thống CRM xin thông báo: Yêu cầu <strong>chuyển phí</strong> của bé <strong>$transfer_info->from_student_name</strong> $transfer_info->from_branch_name chuyển phí sang bé <strong>$transfer_info->to_student_name</strong> $transfer_info->to_branch_name đã bị từ chối phê duyệt!</p>
                            <p>Lý do từ chối: $comment</p>
                            <p>Anh / Chị vui lòng đăng nhập vào hệ thống CRM tại địa chỉ: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết.</p>
                <p>Trân trọng cảm ơn!</p>";
            $mail->sendSingleMail($to, $subject, $body,[$arr_mail_from->gdtt,$arr_mail_from->cskh]);
        }elseif($transfer_info->status==4){
            $to = array('address' => 'ketoan@cmsedu.vn', 'name' => 'ketoan@cmsedu.vn');
            $subject = "[CRM] Yêu cầu phê duyệt chuyển phí của bé $transfer_info->from_student_name";
            $body = "<p>Kính gửi: Kế toán HO </p>
                <p>Hệ thống CRM xin thông báo: Anh/chị đã nhận được yêu cầu <strong>chuyển phí</strong> của bé <strong>$transfer_info->from_student_name</strong> $transfer_info->from_branch_name chuyển phí sang bé <strong>$transfer_info->to_student_name</strong> $transfer_info->to_branch_name</p>
                <p>Nội dung: $transfer_info->note</p>
                <p>Anh/ Chị vui lòng đăng nhập vào hệ thống CRM để phê duyệt yêu cầu.</p>
                <p>Hoặc truy cập link: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết yêu cầu</p>
                <p>Trân trọng cảm ơn!</p>";
            $mail->sendSingleMail($to, $subject, $body);
        }elseif($transfer_info->status==3){
            $to = array('address' => $arr_mail_from->gdtt, 'name' => $arr_mail_from->gdtt);
            $subject = "[CRM] Kế toán HO đã từ chối yêu cầu chuyển phí của bé $transfer_info->from_student_name";
            $comment = $transfer_info->accounting_comment != '' ? $transfer_info->accounting_comment : "Không có lý do cụ thể.";
            $body = "<p>Kính gửi:  GĐTT $transfer_info->from_branch_name</p>
                <p>Hệ thống CRM xin thông báo: Yêu cầu <strong>chuyển phí</strong> của bé <strong>$transfer_info->from_student_name</strong> $transfer_info->from_branch_name chuyển phí sang bé <strong>$transfer_info->to_student_name</strong> $transfer_info->to_branch_name đã bị từ chối phê duyệt!</p>
                <p>Lý do từ chối: $comment</p>
                <p>Anh / Chị vui lòng đăng nhập vào hệ thống CRM tại địa chỉ: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết.</p>
                <p>Trân trọng cảm ơn!</p>";
            $mail->sendSingleMail($to, $subject, $body,['ketoan@cmsedu.vn',$arr_mail_from->cskh,$transfer_info->creator_email]);
        }elseif($transfer_info->status==6){
            $to = array('address' => $arr_mail_from->gdtt, 'name' => $arr_mail_from->gdtt);
            $subject = "[CRM] Kế toán HO đã đồng ý tiếp nhận yêu cầu chuyển phí của bé $transfer_info->from_student_name";
            $body = "<p>Kính gửi: GĐTT $transfer_info->from_branch_name</p>
                    <p>Hệ thống CRM xin thông báo: Yêu cầu <strong>chuyển phí</strong> của bé <strong>$transfer_info->from_student_name</strong> $transfer_info->from_branch_name chuyển phí sang bé <strong>$transfer_info->to_student_name</strong> $transfer_info->to_branch_name đã được tiếp nhận và phê duyệt!</p>
                    <p>Anh / Chị vui lòng đăng nhập vào hệ thống CRM tại địa chỉ: <a href='https://crm.cmsedu.vn/'>crm.cmsedu.vn</a> để xem chi tiết.</p>
                <p>Trân trọng cảm ơn!</p>";
            $mail->sendSingleMail($to, $subject, $body,['ketoan@cmsedu.vn',$arr_mail_from->cskh,$transfer_info->creator_email]);
        }
    }
    public static function getMaxNumberOfReservesSessions($contract, $isDebt)
    {
        if (!$contract || $isDebt) {
            return 0;
        }
        $tuitionFeeId = (int)$contract->tuition_fee_id;
        if ($tuitionFeeId === 0) {
            return 0;
        }
        $tuitionFee = u::first("select number_of_months from tuition_fee where id = $tuitionFeeId");
        if (!$tuitionFee) {
            return 0;
        }
        $numberOfMonths = (int)$tuitionFee->number_of_months ?: 0;
        $reserve = $numberOfMonths >= 6  ? ($numberOfMonths < 12 ? 4 :8) : 0;
        return $reserve > 0 ? $reserve : 0;

    }
    public static function getContractsStudentAll($studen_id, $all = true)
    {
        if ($all) {
            return u::query(
                "SELECT 
                c.id, 
                c.type,              
                c.count_recharge,
                c.enrolment_last_date,
                c.status,
                c.start_date,
                c.class_id,
                c.product_id,
                c.end_date           
            FROM contracts c
                LEFT JOIN students s ON c.student_id = s.id
                LEFT JOIN branches b ON c.branch_id = b.id
                LEFT JOIN zones z ON b.zone_id = z.id
            WHERE s.status > 0 AND c.student_id = $studen_id  ORDER BY  c.count_recharge DESC
            "
            );
        } else {
            return u::first(
                "SELECT 
                c.id,    
                c.type,           
                c.count_recharge,
                c.enrolment_last_date,
                c.status,
                c.start_date,
                c.class_id,
                c.product_id,
                c.end_date    
            FROM contracts c
                LEFT JOIN students s ON c.student_id = s.id
                LEFT JOIN branches b ON c.branch_id = b.id
                LEFT JOIN zones z ON b.zone_id = z.id
            WHERE s.status > 0 AND c.student_id = $studen_id  ORDER BY  c.count_recharge DESC
            "
            );
        }
    }
}
