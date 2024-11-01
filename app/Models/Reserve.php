<?php

namespace App\Models;

use App\Http\Controllers\ReservesController;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Reserve extends Model
{
    protected $table = 'reserves';

    const NORMAL_TYPE = 0;
    const SPECIAL_TYPE = 1;
    const BOTH_TYPE = 2;
    const ALL_TYPE = -1;

    public function getSuggestions($key, $branch_id,$role_id=0)
    {
        $search_condition = "";
        if ($key && $key !== '_') {
            if (is_numeric($key)) {
                $search_condition .= " AND (s.stu_id LIKE '%$key%' OR s.crm_id LIKE '%$key%')";
            } else {
                $search_condition .= " AND (s.name LIKE '%$key%' OR s.crm_id LIKE '%$key%' OR s.accounting_id LIKE '%$key%')";
            }
        }
        // $search_condition .=$role_id == '999999999'?"": " AND c.reservable=1 ";
        $query = "SELECT
            c.id AS contract_id,
            c.branch_id,
            IF(c.product_id > 0, c.product_id, (SELECT product_id FROM term_program_product WHERE program_id = c.program_id)) AS product_id,
            c.program_id,
            s.`id` AS student_id,
            s.`crm_id` AS lms_id,
            s.accounting_id AS accounting_id,
            s.`name` AS student_name,
            s.`nick` AS nick,
            cls.`id` AS class_id,
            (SELECT `name` FROM products WHERE id = c.product_id) AS product_name,
            (SELECT `name` FROM programs WHERE id = c.program_id) AS program_name,
            (SELECT `name` FROM tuition_fee WHERE id = c.tuition_fee_id) AS tuition_fee_name,
            (SELECT `cls_name` FROM classes WHERE id = c.class_id) AS class_name,
            c.`type` AS contract_type,
            c.`payload` AS payload,
            c.`real_sessions` AS real_sessions,
            c.bonus_sessions,
            c.summary_sessions,
            c.`total_sessions` AS total_sessions,
            IF(c.type IN (1,2),IF(c.total_charged > 0, c.total_charged,0), c.total_charged) AS total_charged,
            c.`enrolment_start_date` AS start_date,
            -- c.`enrolment_last_date` AS end_date,
            c.reservable AS reservable,
            c.reserved_sessions AS reserved_sessions,
            c.reservable_sessions AS reservable_sessions,
            c.class_id,
            -- IF($role_id !='999999999' AND  (SELECT count(id) FROM reserves WHERE `status`<3 AND contract_id=c.id)>0 AND 1=2,41 , s.waiting_status) AS waiting_status,
            s.waiting_status,
            (SELECT cjrn_classdate FROM schedules WHERE class_id=c.class_id AND  cjrn_classdate > CURRENT_DATE AND `status`=1 LIMIT 1) AS end_date_schedule
        FROM
            contracts AS c
            LEFT JOIN classes AS cls ON c.`class_id` = cls.`id`
            LEFT JOIN students AS s ON c.`student_id` = s.`id`
            LEFT JOIN sessions AS se ON c.class_id = se.class_id
            LEFT JOIN sessions AS se2 ON c.class_id = se2.class_id
        WHERE c.id IN ( SELECT * FROM 						
            (SELECT c.id
            FROM
                contracts AS c
                LEFT JOIN students AS s ON c.`student_id` = s.`id`
            WHERE c.branch_id = $branch_id
                AND c.status > 0 AND c.status < 7
                AND c.class_id IS NOT NULL
                -- AND c.enrolment_last_date >= CURDATE()
                AND (c.`type` IN (3, 4, 5, 6, 8, 85, 86, 10) OR (c.`type` IN (1,2) AND (((c.`payload` = 0 OR c.payload IS NULL) AND c.debt_amount = 0) OR (c.`payload`=1 AND c.`total_charged` > 0))))
                $search_condition GROUP BY c.id LIMIT 10) 
            temp)
        GROUP BY c.id";
        $res = DB::select(DB::raw($query));

        $student_ids = [];

        if (!empty($res)) {
            foreach ($res as &$r) {
                $r->contract_name = $r->lms_id . ' - ' . $r->student_name . ' - ' . $r->product_name . ' - ' . $r->tuition_fee_name;
                $student_ids[] = $r->student_id;
                $public_holiday = u::getPublicHolidays($r->class_id, $r->branch_id, $r->product_id);
                $class_day = u::getClassDays($r->class_id);
                $done_sessions = u::calSessions($r->start_date, date('Y-m-d'), $public_holiday, $class_day);
                $r->done_sessions = $done_sessions->total;
            }
        }

        return $res;
    }

    public function getContractForReserveByStudentId($studentId)
    {
        $query = "SELECT
            c.id AS contract_id,
            c.branch_id,
            IF(c.product_id > 0, c.product_id, (SELECT product_id FROM term_program_product WHERE program_id = c.program_id)) AS product_id,
            c.program_id,
            s.`id` AS student_id,
            s.`crm_id` AS lms_id,
            s.accounting_id AS accounting_id,
            s.`name` AS student_name,
            s.`nick` AS nick,
            cls.`id` AS class_id,
            (SELECT `name` FROM products WHERE id = c.product_id) AS product_name,
            (SELECT `name` FROM programs WHERE id = c.program_id) AS program_name,
            (SELECT `name` FROM tuition_fee WHERE id = c.tuition_fee_id) AS tuition_fee_name,
            (SELECT `cls_name` FROM classes WHERE id = c.class_id) AS class_name,
            c.`type` AS contract_type,
            c.`payload` AS payload,
            c.`real_sessions` AS real_sessions,
            c.`total_sessions` AS total_sessions,
            IF(c.type IN (1,2),IF(c.total_charged > 0, c.total_charged,0), c.total_charged) AS total_charged,
            c.`enrolment_start_date` AS start_date,
            c.`enrolment_last_date` AS end_date,
            c.reservable AS reservable,
            c.reserved_sessions AS reserved_sessions,
            c.reservable_sessions AS reservable_sessions,
            c.enrolment_schedule,
            c.class_id
        FROM
            contracts AS c
            LEFT JOIN classes AS cls ON c.`class_id` = cls.`id`
            LEFT JOIN students AS s ON c.`student_id` = s.`id`
        WHERE c.id IN ( SELECT * FROM 						
            (SELECT c.id
            FROM
                contracts AS c
                LEFT JOIN students AS s ON c.`student_id` = s.`id`
            WHERE
                s.id = $studentId
                AND c.status > 0 AND c.status < 7
                AND c.student_id NOT IN (SELECT student_id FROM class_transfer WHERE status < 3 GROUP BY student_id)
                AND c.student_id NOT IN (SELECT from_student_id FROM tuition_transfer WHERE status < 2 GROUP BY from_student_id)
                AND c.student_id NOT IN (SELECT to_student_id FROM tuition_transfer WHERE status < 2 GROUP BY to_student_id)
                AND c.student_id NOT IN (SELECT student_id FROM reserves WHERE status < 2 GROUP BY student_id)
                AND c.id NOT IN (SELECT id FROM contracts WHERE relation_contract_id = c.id)
                AND c.class_id IS NOT NULL
                -- AND c.enrolment_last_date >= CURDATE()
                AND (c.`type` IN (3, 4, 5, 6, 8, 85, 86) OR (c.`type` IN (1,2) AND (((c.`payload` = 0 OR c.payload IS NULL) AND c.debt_amount = 0) OR (c.`payload`=1 AND c.`total_charged` > 0))))
            ) 
            temp)
        GROUP BY c.id";
        $res = u::first($query);
        if ($res) {
            $res->holidays = u::getPublicHolidays(0, $res->branch_id);
            $res->class_days = explode(",", trim("$res->enrolment_schedule"));
        }
        return $res;
    }

    public function getNumberOfReservesByStudentId($studentId)
    {
        $query = "select * from contracts c where  c.status > 0 AND c.status < 7
                AND c.student_id NOT IN (SELECT student_id FROM class_transfer WHERE status < 3 GROUP BY student_id)
                AND c.student_id NOT IN (SELECT from_student_id FROM tuition_transfer WHERE status < 2 GROUP BY from_student_id)
                AND c.student_id NOT IN (SELECT to_student_id FROM tuition_transfer WHERE status < 2 GROUP BY to_student_id)
                AND c.student_id NOT IN (SELECT student_id FROM reserves WHERE status < 2 GROUP BY student_id)
                AND c.id NOT IN (SELECT id FROM contracts WHERE relation_contract_id = c.id)
                AND c.class_id IS NOT NULL
                AND (c.`type` IN (3, 4, 5, 6, 8, 85, 86) OR (c.`type` IN (1,2) AND (((c.`payload` = 0 OR c.payload IS NULL) AND c.debt_amount = 0) OR (c.`payload`=1 AND c.`total_charged` > 0))))
                AND c.student_id = $studentId";
        return DB::select(DB::raw($query));
    }

    public function getList($request)
    {
        $conditions = $this->filter($request);

        if ($conditions) {
            $limit = $this->limit($request);
            $query = "
                SELECT
                    r.id AS id,
                    r.student_id AS student_id,
                    r.type AS `type`,
                    r.start_date AS start_date,
                    r.end_date AS end_date,
                    r.session AS `session`,
                    r.status AS `status`,
                    r.created_at AS created_at,
                    r.approved_at AS approved_at,
                    r.comment AS `comment`,
                    r.meta_data AS meta_data,
                    r.is_reserved AS is_reserved,
                    r.attached_file AS attached_file,
                    s.`name` AS student_name,
                    s.`crm_id` AS crm_id,
                    s.`accounting_id` AS accounting_id,
                    b.`name` AS branch_name,
                    p.`name` AS product_name,
                    pr.`name` AS program_name,
                    cls.`cls_name` AS class_name,
                    IF(r.type > 0 AND r.status = 0, (SELECT `name` FROM roles WHERE id = (SELECT MIN(role_id) FROM pending_regulation WHERE status = 1 AND `type` = 1 AND min_days <= r.special_reserved_sessions AND max_days >= r.special_reserved_sessions)), null)
                    AS has_permission_role
                FROM
                    reserves AS r
                    LEFT JOIN students AS s ON r.student_id = s.`id`
                    LEFT JOIN contracts AS c ON r.`contract_id` = c.`id`
                    LEFT JOIN branches AS b ON r.`branch_id` = b.`id`
                    LEFT JOIN products AS p ON r.`product_id` = p.`id`
                    LEFT JOIN programs AS pr ON r.`program_id` = pr.`id`
                    LEFT JOIN classes AS cls ON r.`class_id` = cls.`id`
                
                $conditions
                ORDER BY r.created_at DESC
                $limit
            ";

            $result = DB::select(DB::raw($query));

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
                "items" => $result,
                "pagination" => $page
            ];
        } else {
            $res = [];
        }


        return $res;
    }

    public function getReservesByUserId($id, $branch_ids)
    {
        $branch_ids_string = implode(',', $branch_ids);
        $query = "
            SELECT
                r.id,
                r.student_id,
                r.type,
                r.start_date,
                r.end_date,
                r.session,
                r.status,
                r.created_at,
                r.approved_at,
                r.final_approved_at,
                r.comment,
                r.meta_data,
                r.is_reserved,
                r.attached_file,
                r.note,
                r.new_enrol_end_date as new_end_date,
                s.`name` AS student_name,
                s.nick,
                s.`crm_id`,
                s.`accounting_id`,
                (SELECT `name` FROM branches WHERE id = r.branch_id) AS branch_name,
                (SELECT `name` FROM products WHERE id = r.product_id) AS product_name,
                (SELECT `name` FROM programs WHERE id = r.program_id) AS program_name,
                (SELECT `cls_name` FROM classes WHERE id = r.class_id) AS class_name,
                (SELECT username FROM users WHERE id = r.creator_id) as creator,
                (SELECT username FROM users WHERE id = r.approver_id) as approver,
                (SELECT username FROM users WHERE id = r.final_approver_id) as final_approver,
                IF(r.status!=2 OR r.end_date >= CURRENT_DATE,1,0) as can_edit_reserve,
                (SELECT count(id) FROM reserves WHERE r.student_id=student_id AND status IN (0,2) AND id>r.id) AS next_reserve
            FROM
                reserves AS r
                LEFT JOIN students AS s ON r.student_id = s.`id`
            WHERE
                r.status < 4 AND r.student_id = $id
                AND r.branch_id IN ($branch_ids_string)
            GROUP BY r.id
            ORDER BY r.created_at DESC
        ";

        $res = DB::select(DB::raw($query));
        return $res;
    }

    public function getReserveRegulation()
    {
        $query = "
            SELECT 
                p.role_id AS role_id, p.min_days AS min_days, p.max_days AS max_days
            FROM 
              `pending_regulation` AS p
            WHERE 
                 p.start_date <= CURDATE() 
                 AND CURDATE() <= p.`expired_date` 
                 AND p.`type` = 1 
                 AND p.`status` = 1
        ";
        $res = DB::select(DB::raw($query));

        if (!empty($res)) {
            $regulation = [];
            foreach ($res as $re) {
                $regulation[$re->role_id] = $re;
            }

            return $regulation;
        } else {
            return $res;
        }
    }

    public function getRequests($conditions, $limit)
    {
        $conditions_string = implode(' AND ', $conditions);
        $conditions_string = $conditions_string? $conditions_string : "1";
        $query = "
                    SELECT
                        r.id AS id,
                        r.student_id AS student_id,
                        r.type AS `type`,
                        r.start_date AS start_date,
                        r.end_date as end_date,
                        r.session AS `session`,
                        r.status AS `status`,
                        r.created_at AS created_at,
                        r.approved_at AS approved_at,
                        r.comment AS `comment`,
                        r.meta_data AS meta_data,
                        r.is_reserved AS is_reserved,
                        r.attached_file AS attached_file,
                        r.note as note,
                        r.new_enrol_end_date as new_end_date,
                        s.`name` AS student_name,
                        s.nick as nick,
                        s.`crm_id` AS crm_id,
                        s.`accounting_id` AS accounting_id,
                        b.`name` AS branch_name,
                        p.`name` AS product_name,
                        pr.`name` AS program_name,
                        cls.`cls_name` AS class_name,
                        u.username as creator,
                        u2.username as approver
                    FROM
                        reserves AS r
                        LEFT JOIN students AS s ON r.student_id = s.`id`
                        LEFT JOIN contracts AS c ON r.`contract_id` = c.`id`
                        LEFT JOIN branches AS b ON r.`branch_id` = b.`id`
                        LEFT JOIN products AS p ON r.`product_id` = p.`id`
                        LEFT JOIN programs AS pr ON r.`program_id` = pr.`id`
                        LEFT JOIN classes AS cls ON r.`class_id` = cls.`id`
                        LEFT JOIN users as u ON r.creator_id = u.id
                        LEFT JOIN users as u2 on r.approver_id = u2.id
                    WHERE
                      r.status = 0 AND $conditions_string
                    GROUP BY r.id
                    ORDER BY r.created_at DESC $limit";
        $res = DB::select(DB::raw($query));
        return $res;
    }
    public function countRequests($conditions){
        $conditions_string = implode(' AND ', $conditions);
        $conditions_string = $conditions_string? $conditions_string : "1";
        $query = "SELECT COUNT(t.id) AS total
                  FROM (
                    SELECT
                        r.id AS id
                    FROM
                        reserves AS r
                        LEFT JOIN students AS s ON r.student_id = s.`id`
                        LEFT JOIN contracts AS c ON r.`contract_id` = c.`id`
                        LEFT JOIN branches AS b ON r.branch_id = b.id
                    WHERE
                        r.status = 0 AND $conditions_string
                    GROUP BY r.id
                  ) t";
    
        $res = u::first($query);
        return $res->total;
    }
    public function searchForApproving($request){
        $resp = (Object)[
          'conditions' => [],
          'limit' => ''
        ];
    
        if (isset($request->filter)) {
          try {
            $filter = json_decode($request->filter);
          } catch (\Exception $e) {
            return $resp;
          }
    
          $conditions = [];
          if(isset($filter->lms_effect_id) && $filter->lms_effect_id){
            $value = trim($filter->lms_effect_id);
            if(is_numeric($value)){
              $id = (int)$value;
              $conditions[] = "(s.stu_id = $id)";
            }else{
              $conditions[] = "(s.accounting_id LIKE '%$value%' OR s.crm_id LIKE '%$value%' OR s.name LIKE '%$value%')";
            }
          }
    
          if (isset($filter->branch_id) && is_numeric($filter->branch_id)) {
            $branch_id = (int)$filter->branch_id;
            if ($branch_id !== 0) {
              $conditions[] = "r.branch_id = " . $filter->branch_id;
            }
          }
    
          if (isset($filter->is_reserved) && is_numeric($filter->is_reserved)) {
            $is_reserved = (int)$filter->is_reserved;
            if ($is_reserved === 0 || $is_reserved === 1) {
              $conditions[] = "r.is_reserved = $is_reserved";
            }
          }
    
          if (isset($filter->is_extended) && is_numeric($filter->is_extended)) {
            $is_extended = (int)$filter->is_extended;
            if ($is_extended === 0 || $is_extended === 1) {
              $conditions[] = "r.type = $is_extended";
            }
          }
    
          $resp->conditions = $conditions;
          $resp->limit = $this->limit($request);
        }
    
        return $resp;
    }
    public function getReservedDates($contract_ids = [])
    {
        $res = [];

        if ($contract_ids) {
            $contract_ids_string = implode(',', $contract_ids);
            $query = "
                SELECT r.contract_id, r.start_date, r.end_date FROM `reserves` AS r WHERE r.status = 2 AND r.contract_id IN ($contract_ids_string)   
            ";
            $data = DB::select(DB::raw($query));

            if (!empty($data)) {
                foreach ($data as $da) {
                    if (isset($res[$da->contract_id])) {
                        $res[$da->contract_id][] = (object)['start_date' => $da->start_date, 'end_date' => $da->end_date];
                    } else {
                        $res[$da->contract_id] = [(object)['start_date' => $da->start_date, 'end_date' => $da->end_date]];
                    }
                }
            }
        }

        return $res;
    }

    public function getPrintData($id)
    {
        $query = "SELECT s.`gud_name1` AS gud_name,
                    s.gud_mobile1 AS gud_mobile,
                    s.`name`,
                    (SELECT `name` FROM programs WHERE id = r.`program_id`) AS program_name,
                    ss.class_day,
                    (SELECT `name` FROM shifts WHERE id = ss.`shift_id` AND STATUS > 0) AS shift_name,
                    (SELECT `room_name` FROM rooms WHERE id = ss.`room_id` AND STATUS > 0) AS room_name,
                    r.`start_date`,
                    r.`end_date`,
                    r.created_at,
                    r.`note`,
                    (SELECT `name` FROM branches WHERE id = r.`branch_id`) AS branch_name,
                    r.`meta_data`,
                    (SELECT `name` FROM products WHERE id = r.`product_id`) AS product_name,
                    (SELECT charge_date FROM payment WHERE contract_id = r.`contract_id` ORDER BY charge_date DESC LIMIT 1) AS charged_at,
                    (SELECT accounting_id FROM payment WHERE contract_id = r.`contract_id` ORDER BY charge_date DESC LIMIT 1) AS payment_accounting_id,
                    (SELECT reserved_sessions FROM contracts WHERE id = r.`contract_id` ) reserved_sessions,
                    r.session,
                    (SELECT SUM(amount) FROM payment WHERE contract_id = r.`contract_id`) AS total_fee
                  FROM
                    reserves r
                    LEFT JOIN students s ON r.`student_id` = s.`id`
                    LEFT JOIN classes cls ON r.`class_id` = cls.`id`
                    LEFT JOIN sessions ss ON cls.`id` = ss.`class_id` AND ss.`status` > 0
                  WHERE
                    r.id = $id AND s.status >0 
              ";
        return u::first($query);
    }

    private function countRecords($conditions)
    {
        $query = "
                SELECT 
                    COUNT(t.id) AS count_item
                FROM (
                    SELECT
                        r.id AS id
                    FROM
                        reserves AS r
                        LEFT JOIN students AS s ON r.student_id = s.`id`
                        LEFT JOIN contracts AS c ON r.`contract_id` = c.`id`
                        LEFT JOIN branches AS b ON r.`branch_id` = b.`id`
                        LEFT JOIN products AS p ON r.`product_id` = p.`id`
                        LEFT JOIN programs AS pr ON r.`program_id` = pr.`id`
                        LEFT JOIN classes AS cls ON r.`class_id` = cls.`id`
                    
                    $conditions
                    GROUP BY r.student_id
                ) AS t
            ";
        $res = DB::select(DB::raw($query));
        if (!empty($res)) {
            return $res[0]->count_item;
        } else {
            return 0;
        }
    }

    private function filter($request)
    {
        if (isset($request->filter)) {
            try {
                $filter = json_decode($request->filter);
            } catch (\Exception $e) {
                return "";
            }

            $conditions = [];
            // $conditions[] = "r.id IN (SELECT MAX(id) FROM reserves AS r WHERE r.status < 4 GROUP BY student_id)";

            // if (isset($filter->lms_accounting_id) && $filter->lms_accounting_id) {
            //     $value = trim($filter->lms_accounting_id);
            //     $conditions[] = "(s.crm_id LIKE '%$value%' OR s.accounting_id LIKE '%$value%')";
            // }

            $student_name = isset($filter->student_name) ? trim($filter->student_name) : '';
            if ($student_name) {
                $student_name = strtoupper($student_name);
                $conditions[] = "(s.name LIKE '%$student_name%' OR s.crm_id LIKE '%$student_name%' OR s.accounting_id LIKE '%$student_name%')";
            }

            if (isset($filter->reserve_type) && (($filter->reserve_type === self::NORMAL_TYPE) || ($filter->reserve_type === self::SPECIAL_TYPE))) {
                $conditions[] = "r.type = " . $filter->reserve_type;
            }

            if (isset($filter->branch_id) && is_numeric($filter->branch_id)) {
                $branch_id = (int)$filter->branch_id;
                if ($branch_id !== 0) {
                    $conditions[] = "r.branch_id = " . $filter->branch_id;
                } else {
                    $branch_ids = u::getBranchIds($request->users_data);
                    if (!empty($branch_ids)) {
                        $branch_ids_string = implode(',', $branch_ids);
                        $conditions[] = "r.branch_id IN ($branch_ids_string)";
                    }
                }
            } else {
                $branch_ids = u::getBranchIds($request->users_data);
                if (!empty($branch_ids)) {
                    $branch_ids_string = implode(',', $branch_ids);
                    $conditions[] = "r.branch_id IN ($branch_ids_string)";
                }
            }

            if (isset($filter->semester_id) && is_numeric($filter->semester_id)) {
                $semester_id = (int)$filter->semester_id;
                if ($semester_id !== 0) {
                    $conditions[] = "cls.semester_id = $semester_id";
                }
            }

            if (isset($filter->product_id) && is_numeric($filter->product_id)) {
                $product_id = (int)$filter->product_id;
                if ($product_id !== 0) {
                    $conditions[] = "r.product_id = $product_id";
                }
            }

            if (isset($filter->program_id) && is_numeric($filter->program_id)) {
                $program_id = (int)$filter->program_id;
                if ($program_id !== 0) {
                    $conditions[] = "r.program_id = $program_id";
                }
            }

            if (isset($filter->tuition_fee_id) && is_numeric($filter->tuition_fee_id)) {
                $tuition_fee_id = (int)$filter->tuition_fee_id;
                if ($tuition_fee_id !== 0) {
                    $conditions[] = "c.tuition_fee_id = $tuition_fee_id";
                }
            }

            if (isset($filter->class_id) && is_numeric($filter->class_id)) {
                $class_id = (int)$filter->class_id;
                if ($class_id !== 0) {
                    $conditions[] = "r.class_id = $class_id";
                }
            }

            if (isset($filter->min_sessions) && is_numeric($filter->min_sessions)) {
                $sessions = (int)$filter->min_sessions;
                if ($sessions) {
                    $conditions[] = "r.session >= $sessions";
                }
            }

            if (isset($filter->max_sessions) && is_numeric($filter->max_sessions)) {
                $sessions = (int)$filter->max_sessions;
                if ($sessions) {
                    $conditions[] = "r.session <= $sessions";
                }
            }

            if (isset($filter->creator_id) && is_numeric($filter->creator_id)) {
                $creator_id = (int)$filter->creator_id;
                if ($creator_id) {
                    $conditions[] = "r.creator_id = $creator_id";
                }
            }

            if (isset($filter->min_created_date) && $filter->min_created_date && u::isValidDate($filter->min_created_date)) {
                $time = $filter->min_created_date . " 00:00:00";
                $conditions[] = "r.created_at >= '$time'";
            }

            if (isset($filter->max_created_date) && $filter->max_created_date && u::isValidDate($filter->max_created_date)) {
                $time = $filter->max_created_date . " 23:59:59";
                $conditions[] = "r.created_at <= '$time'";
            }

            if (isset($filter->start_date) && $filter->start_date && u::isValidDate($filter->start_date)) {
                $time = $filter->start_date;
                $conditions[] = "r.start_date >= '$time'";
            }

            if (isset($filter->end_date) && $filter->end_date && u::isValidDate($filter->end_date)) {
                $time = $filter->end_date;
                $conditions[] = "r.end_date <= '$time'";
            }

            if (isset($filter->is_reserved) && is_numeric($filter->is_reserved)) {
                $is_reserved = (int)$filter->is_reserved;
                if ($is_reserved === 0 || $is_reserved === 1) {
                    $conditions[] = "r.is_reserved = $is_reserved";
                }
            }

            if (isset($filter->min_approved_date) && $filter->min_approved_date && u::isValidDate($filter->min_approved_date)) {
                $time = $filter->min_approved_date . " 00:00:00";
                $conditions[] = "r.approved_at >= '$time'";
            }

            if (isset($filter->max_approved_date) && $filter->max_approved_date && u::isValidDate($filter->max_approved_date)) {
                $time = $filter->max_approved_date . " 23:59:59";
                $conditions[] = "r.approved_at <= '$time'";
            }

            if (isset($filter->approver_id) && is_numeric($filter->approver_id)) {
                $approver_id = (int)$filter->approver_id;
                if ($approver_id) {
                    $conditions[] = "r.approver_id = $approver_id";
                }
            }

            if (isset($filter->status) && is_numeric($filter->status)) {
                $status = (int)$filter->status;
                if ($status === 0 || $status === 1 || $status === 2 || $status === 3) {
                    $conditions[] = "r.status = $status";
                }
            }

            if (!empty($conditions)) {
                return " WHERE " . implode(" AND ", $conditions);
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

    private function limit($request)
    {
        $limit = '';
        $pagination = json_decode($request->pagination);
        if ($pagination->cpage && $pagination->limit) {
            $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
            $limit .= " LIMIT $offset, $pagination->limit";
        }

        return $limit;
    }

    public function isExisted($hash_key)
    {
        $query = "SELECT COUNT(id) AS count_id FROM reserves WHERE hash_key = '$hash_key'";
        $item = u::first($query);
        return $item ? true : false;
    }

    /**
     * Lấy danh sách học sinh đã được bảo lưu
     * @param $studentIds
     * @param $date
     * @param int $is_reserved
     * @return array|null
     */
    public function getReservedByStudentId($studentIds, $date, $is_reserved = 1,$classId = null)
    {
        if (empty($studentIds) || empty($date)) {
            return [];
        }
        $studentIdString = implode(", ", $studentIds);
        $query = "SELECT *
              FROM reserves
              WHERE student_id IN ($studentIdString) AND start_date <= '$date' AND end_date >= '$date' AND status = 2";
        if ($classId)
            $query = "SELECT *
              FROM reserves
              WHERE student_id IN ($studentIdString) AND class_id = $classId AND status = 2";
        if (isset($is_reserved)) {
            $query .= " AND is_reserved = $is_reserved";
        }
        return u::query($query);
    }
    public function getAllInfo($branch_id)
    {
        $res = (object)[
            'semesters' => [],
            'programs' => [],
            'classes' => [],
            'holidays' => []
        ];
        $semesters = $this->getSemesters();
        $res->holidays = u::getPublicHolidays(0, $branch_id, 9999);

        if (!empty($semesters)) {
            $res->semesters = $semesters;
            $semester_ids = [];
            foreach ($semesters as $semester) {
                $semester_ids[] = $semester->id;
            }

            $programs = $this->getPrograms($semester_ids, $branch_id);

            if (!empty($programs)) {
                $res->programs = $programs;
                $program_ids = [];
                foreach ($programs as $sem) {
                    foreach ($sem as $program) {
                        $program_ids[] = $program->id;
                    }
                }

                $res->classes = $this->getClasses($program_ids);
            }
        }

        return $res;
    }
    private function getSemesters()
    {
        $query = "SELECT id, `name`, product_id FROM semesters WHERE status=1 AND end_date > CURDATE()";
        $res = u::query($query);
        return $res ? $res : [];
    }

    private function getPrograms($semester_ids, $branch_id)
    {
        if (!empty($semester_ids)) {
            $res = [];
            $semester_ids_string = implode(",", $semester_ids);
            $query = "SELECT
                    pr.`name` AS `name`,
                    pr.`id` AS id,
                    pr.semester_id as semester_id
                FROM
                    term_program_product AS t
                    LEFT JOIN programs AS pr ON t.`program_id` = pr.`id`
                WHERE
                    t.`status` = 1
                    AND pr.semester_id IN ($semester_ids_string)
                    AND pr.branch_id = $branch_id";
            $data = u::query($query);

            foreach ($data as $datum) {
                if (isset($res["semester$datum->semester_id"])) {
                    $res["semester$datum->semester_id"][] = (object)[
                        'id' => $datum->id,
                        'name' => $datum->name
                    ];
                } else {
                    $res["semester$datum->semester_id"] = [
                        (object)[
                            'id' => $datum->id,
                            'name' => $datum->name
                        ]
                    ];
                }
            }
        } else {
            $res = null;
        }

        return $res ? $res : [];
    }

    private function getClasses($program_ids)
    {
        if (!empty($program_ids)) {
            $program_ids_string = implode(",", $program_ids);
            $query = "SELECT
                    c.`cls_name` AS class_name,
                    c.id AS id,
                    s.`class_day` AS class_day,
                    c.program_id as program_id
                FROM
                    classes AS c
                    LEFT JOIN sessions AS s ON c.id = s.`class_id`
                WHERE 
                    c.`program_id` IN ($program_ids_string)
                    AND c.cls_iscancelled = 'no'
                    AND c.`cls_enddate` > CURDATE()
                    AND c.cm_id IS NOT NULL
                    AND s.status > 0
                GROUP BY c.id, s.class_day";
            $data = u::query($query);
            $res = $this->processClasses($data);
        } else {
            $res = null;
        }
        return $res ? $res : [];
    }
    private function processClasses($classes)
    {
        if (!empty($classes)) {
            $res = [];
            foreach ($classes as $class) {
                if (isset($res["program$class->program_id"])) {
                    if (isset($res["program$class->program_id"]["class$class->id"])) {
                        $res["program$class->program_id"]["class$class->id"]->class_days[] = $class->class_day;
                    } else {
                        $res["program$class->program_id"]["class$class->id"] = (object)[
                            'id' => $class->id,
                            'class_name' => $class->class_name,
                            'class_days' => [$class->class_day]
                        ];
                    }
                } else {
                    $res["program$class->program_id"] = [
                        "class$class->id" => (object)[
                            'id' => $class->id,
                            'class_name' => $class->class_name,
                            'class_days' => [$class->class_day]
                        ]
                    ];
                }
            }
        } else {
            $res = null;
        }
        return $res;
    }
    public function getStudentsForMultiReserve($class_id)
    {
        $query = "SELECT
                c.id AS contract_id,
                c.branch_id,
                IF(c.product_id > 0, c.product_id, (SELECT product_id FROM term_program_product WHERE program_id = c.program_id)) AS product_id,
                c.program_id,
                s.`id` AS student_id,
                s.`stu_id` AS lms_id,
                s.accounting_id,
                s.`name` AS student_name,
                s.`nick`,
                c.`type` AS contract_type,
                c.`payload`,
                c.`real_sessions`,
                c.`total_sessions`,
                c.summary_sessions,
                c.bonus_sessions,
                c.total_charged,
                c.`enrolment_start_date` AS start_date,
                c.`enrolment_last_date` AS end_date,
                c.class_id,
                c.enrolment_schedule,
                c.tuition_fee_id,
                c.debt_amount,
                FALSE AS checked,
                (SELECT shift_id FROM sessions WHERE class_id = c.class_id AND status = 1 LIMIT 1) AS shift_id,
                s.waiting_status,
                c.action
            FROM
                contracts AS c
                LEFT JOIN students AS s ON c.`student_id` = s.`id`
            WHERE
                c.status !=7
                AND c.class_id = $class_id";

        $res = u::query($query);

        if (!empty($res)) {
            $contract_ids = [];

            foreach ($res as $r) {
                $contract_ids[] = $r->contract_id;
            }

            $reserveModel = new Reserve();

            $reserved_dates = $reserveModel->getReservedDates($contract_ids);

            foreach ($res as &$r) {
                $r->reserved_dates = isset($reserved_dates[$r->contract_id]) ? $reserved_dates[$r->contract_id] : [];
            }
        }

        return $res;
    }
    public function getClassInfo($class_id)
    {
        $query = "SELECT
                    (SELECT shift_id FROM sessions WHERE class_id = cls.id AND status = 1 LIMIT 1) AS shift_id,
                    (SELECT MAX(cjrn_classdate) FROM schedules WHERE class_id = cls.id) AS class_end_date
                 FROM classes cls WHERE cls.id = $class_id";
        return u::first($query);
    }
    public function createMultiReserve($request)
    {
        $creator_id = $request->users_data->id;
        $created_at = date('Y-m-d H:i:s');

        $student_ids = $this->createReserves($request->reserves, $creator_id, $created_at);
        if (!empty($student_ids)) {
            $student_ids_string = implode(",", $student_ids);
            $reserve_ids = u::query("SELECT id FROM reserves WHERE student_id IN ($student_ids_string) AND status = 0");
        } else {
            $reserve_ids = [];
        }

        return $reserve_ids;
    }
    private function createReserves($reserves, $creator_id, $created_at)
    {
        $student_ids = [];
        if (!empty($reserves)) {
            $parts = [];
            $meta_data = '';
            foreach ($reserves as $r) {
                $reserve = (object)$r;
                $parts[] = [
                    "student_id" => $reserve->student_id,
                    "type" => $reserve->reserve_type,
                    "start_date" => $reserve->start_date,
                    "session" => $reserve->sessions,
                    "end_date" => $reserve->end_date,
                    "new_enrol_end_date" => $reserve->new_end_date,
                    "branch_id" => $reserve->branch_id,
                    "program_id" => $reserve->program_id,
                    "product_id" => $reserve->product_id,
                    "class_id" => $reserve->class_id,
                    "is_reserved" => $reserve->is_reserved,
                    "creator_id" => $creator_id,
                    "created_at" => $created_at,
                    "meta_data" => json_encode($reserve->meta_data),
                    "note" => $reserve->note,
                    "contract_id" => $reserve->contract_id,
                    "attached_file" => '',
                    "special_reserved_sessions" => 0,
                    "is_supplement" => isset($reserve->is_supplement) ? (int)$reserve->is_supplement : 0,
                ];
                $student_ids[] = $reserve->student_id;
            }
            Reserve::insert($parts);
        }
        return $student_ids;
    }
}
