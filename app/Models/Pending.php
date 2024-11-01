<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;

class Pending extends Model
{

    protected $table = 'pendings';
    public function getSuggestions($key, $branch_id){
        //$query = "
        //     SELECT
        //         c.id AS contract_id,
        //         s.`id` AS student_id,
        //         s.`stu_id` AS lms_id,
        //         b.`id` AS branch_id,
        //         pr.`id` AS product_id,
        //         pro.`id` AS program_id,
        //         s.accounting_id AS accounting_id,

        //         s.`name` AS student_name,
        //         s.`nick` AS nick,

        //         pr.`name` AS product_name,
        //         pro.`name` AS program_name,
        //         tf.`name` AS tuition_fee_name,

        //         c.`type` AS contract_type,
        //         c.`payload` AS payload,
        //         c.`must_charge` AS must_charge,
        //         IF(c.passed_trial = 1, c.`real_sessions`, c.`real_sessions` - 3) AS real_sessions,
                
        //         IF(c.type IN (1,2),IF(c.total_charged > 0, c.total_charged,0), c.total_charged) AS total_charged,
        //         IF(e.`id` IS NULL, c.`start_date`, e.`start_date`) AS start_date,
        //         IF(e.`id` IS NULL, c.`end_date`, e.`last_date`) AS end_date
        //     FROM
        //         contracts AS c
        //         LEFT JOIN enrolments AS e ON c.`id` = e.`contract_id`
        //         LEFT JOIN branches AS b ON c.`branch_id` = b.`id`
        //         LEFT JOIN products AS pr ON c.`product_id` = pr.`id`
        //         LEFT JOIN programs AS pro ON c.`program_id` = pro.`id`
        //         LEFT JOIN students AS s ON c.`student_id` = s.`id`
        //         LEFT JOIN pendings AS r ON c.`id` = r.`contract_id`
        //         LEFT JOIN `tuition_fee` AS tf ON c.`tuition_fee_id` = tf.`id`
        //         LEFT JOIN sessions AS se ON e.class_id = se.class_id
        //     WHERE
        //         c.student_id NOT IN (SELECT p.student_id AS student_id FROM pendings AS p WHERE p.status = 0 GROUP BY p.student_id)
        //         AND e.id IS NULL
        //         AND c.branch_id = $branch_id
        //         AND (
        //             (c.`type` = 3 OR c.`type` = 4 OR c.type = 5 OR c.type = 6)
        //             OR ((c.`type` = 1 OR c.`type` = 2) AND ((c.`payload` = 0 AND c.debt_amount = 0) OR (c.`payload`=1 AND c.`total_charged` > 0)))
        //         )

        // ";
        // if($key && $key !== '_'){
        //     if(is_numeric($key)){
        //         $query .= " AND s.stu_id LIKE '%$key%'";
        //     }else{
        //         $query .= " AND s.name LIKE '%$key%'";
        //     }
        // }

        // $query .= " GROUP BY c.id LIMIT 10";
        $search_condition = "";
        if($key && $key !== '_'){
            if(is_numeric($key)){
                $search_condition .= " AND s.stu_id LIKE '%$key%'";
            }else{
                $search_condition .= " AND s.name LIKE '%$key%'";
            }
        }
        $query = "SELECT
            c.id AS contract_id,
            c.branch_id,
            c.product_id,
            c.program_id,
            s.`id` AS student_id,
            s.`stu_id` AS lms_id,
            s.accounting_id AS accounting_id,
            s.`name` AS student_name,
            s.`nick` AS nick,
            cls.`id` AS class_id,
            (SELECT `name` FROM products WHERE id = c.product_id) AS product_name,
            (SELECT `name` FROM programs WHERE id = c.program_id) AS program_name,
            (SELECT `name` FROM tuition_fee WHERE id = c.tuition_fee_id) AS tuition_fee_name,
            (SELECT `cls_name` FROM classes WHERE id = e.class_id) AS class_name,
            c.`type` AS contract_type,
            c.`payload` AS payload,
            c.`must_charge` AS must_charge,
            IF(c.passed_trial = 1, c.`real_sessions`, c.`real_sessions` - 3) AS real_sessions,						
            IF(c.type IN (1,2),IF(c.total_charged > 0, c.total_charged,0), c.total_charged) AS total_charged,
            IF(e.`id` IS NULL, c.`start_date`, e.`start_date`) AS start_date,
            IF(e.`id` IS NULL, c.`end_date`, e.`last_date`) AS end_date
        FROM
            contracts AS c
            LEFT JOIN enrolments AS e ON c.`id` = e.`contract_id`
            LEFT JOIN classes AS cls ON e.`class_id` = cls.`id`
            LEFT JOIN students AS s ON c.`student_id` = s.`id`            
        WHERE c.id IN ( SELECT * FROM 						
            (SELECT c.id
            FROM
                contracts AS c
                LEFT JOIN enrolments AS e ON c.`id` = e.`contract_id`
                LEFT JOIN students AS s ON c.`student_id` = s.`id`
            WHERE c.id > 0
                AND c.student_id NOT IN (SELECT p.student_id AS student_id FROM pendings AS p WHERE p.status = 0 GROUP BY p.student_id)
                AND e.id IS NULL
                AND c.branch_id = $branch_id
                AND (c.`type` IN (3,4,5,6) OR (c.`type` IN (1,2) AND ((c.`payload` = 0 AND c.debt_amount = 0) OR (c.`payload`=1 AND c.`total_charged` > 0))))
                $search_condition GROUP BY c.id LIMIT 10) 
            temp)
        GROUP BY c.id";

        $res = DB::select(DB::raw($query));
        if(!empty($res)){
            foreach ($res as &$r){
                $r->contract_name = $r->lms_id . ' - ' . $r->student_name . ' - ' . $r->product_name . ' - ' . $r->tuition_fee_name;
            }
        }

        return$res;
    }

    public function getList($request){
        $conditions = $this->filter($request);

        if($conditions){
            $limit = $this->limit($request);
            $query = "
                SELECT
                    r.id AS id,
                    r.student_id AS student_id,
                    
                    r.start_date AS start_date,
                    r.end_date AS end_date,
                    r.session AS `session`,
                    r.status AS `status`,
                    r.created_at AS created_at,
                    r.approved_at AS approved_at,
                    r.comment AS `comment`,
                    r.meta_data AS meta_data,
                    r1.description as description,
                    s.`name` AS student_name,
                    s.`stu_id` AS lms_id,
                    s.`accounting_id` AS accounting_id,
                    b.`name` AS branch_name
                    
                    
                FROM
                    pendings AS r
                    LEFT JOIN students AS s ON r.student_id = s.`id`
                    LEFT JOIN contracts AS c ON r.`contract_id` = c.`id`
                    LEFT JOIN branches AS b ON r.`branch_id` = b.`id`
                    LEFT JOIN reasons AS r1 ON r.reason_id = r1.id
                $conditions    
                GROUP BY r.student_id
                ORDER BY r.created_at DESC
                $limit
            ";

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

            $res = [
                "items" => $result,
                "pagination" => $page
            ];
        }else{
            $res = [];
        }


        return $res;
    }

    public function getRequests($conditions){
        $conditions_string = implode(' OR ',$conditions);
        $query = "
                    SELECT
                        r.id AS id,
                        r.student_id AS student_id,
                        
                        r.start_date AS start_date,
                        r.end_date AS end_date,
                        r.session AS `session`,
                        r.status AS `status`,
                        r.created_at AS created_at,
                        r.approved_at AS approved_at,
                        r.comment AS `comment`,
                        r.meta_data AS meta_data,
                        r.attached_file AS attached_file,
                        r1.description as description,
                        s.`name` AS student_name,
                        s.`stu_id` AS lms_id,
                        s.`accounting_id` AS accounting_id,
                        b.`name` AS branch_name,
                        u.username as creator,
                        u2.username as approver,
                        
                        b.`name` AS branch_name,
                        p.`name` AS product_name,
                        pr.`name` AS program_name
                       
                    FROM
                        pendings AS r
                        LEFT JOIN students AS s ON r.student_id = s.`id`
                        LEFT JOIN contracts AS c ON r.`contract_id` = c.`id`
                        LEFT JOIN branches AS b ON r.`branch_id` = b.`id`
                        LEFT JOIN reasons AS r1 ON r.reason_id = r1.id
                        left join users as u on r.creator_id = u.id
                        left join users as u2 on r.approver_id = u2.id
                        
                        LEFT JOIN products AS p ON r.`product_id` = p.`id`
                        LEFT JOIN programs AS pr ON r.`program_id` = pr.`id`
                        
                    WHERE
                        r.status = 0 AND ($conditions_string)
                    GROUP BY r.id
                    ORDER BY r.created_at DESC
                ";
        $res = DB::select(DB::raw($query));
        return $res;
    }

    public function getPendingRegulation(){
        $query = "
            SELECT 
                p.role_id AS role_id, p.min_days AS min_days, p.max_days AS max_days
            FROM 
              `pending_regulation` AS p
            WHERE 
                 p.start_date <= CURDATE() 
                 AND CURDATE() <= p.`expired_date` 
                 AND p.`type` = 0 
                 AND p.`status` = 1
        ";
        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            $regulation = [];
            foreach ($res as $re){
                $regulation[$re->role_id] = $re;
            }

            return $regulation;
        }else{
            return $res;
        }
    }

    public function getPendingsByUserId($id){
        $query = "
            SELECT
                r.id AS id,
                r.student_id AS student_id,
                
                r.start_date AS start_date,
                r.end_date AS end_date,
                r.session AS `session`,
                r.status AS `status`,
                r.created_at AS created_at,
                r.approved_at AS approved_at,
                r.comment AS `comment`,
                r.meta_data AS meta_data,
                r.attached_file AS attached_file,
                r1.description as description,
                s.`name` AS student_name,
                s.`stu_id` AS lms_id,
                s.`accounting_id` AS accounting_id,
                b.`name` AS branch_name,
                u.username as creator,
                u2.username as approver,
                
                b.`name` AS branch_name,
                p.`name` AS product_name,
                pr.`name` AS program_name
               
            FROM
                pendings AS r
                LEFT JOIN students AS s ON r.student_id = s.`id`
                LEFT JOIN contracts AS c ON r.`contract_id` = c.`id`
                LEFT JOIN branches AS b ON r.`branch_id` = b.`id`
                LEFT JOIN reasons AS r1 ON r.reason_id = r1.id
                left join users as u on r.creator_id = u.id
                left join users as u2 on r.approver_id = u2.id
                
                LEFT JOIN products AS p ON r.`product_id` = p.`id`
                LEFT JOIN programs AS pr ON r.`program_id` = pr.`id`
                
            WHERE
                r.student_id = $id
            ORDER BY r.created_at DESC
        ";

        $res = DB::select(DB::raw($query));
        return $res;
    }

    private function filter($request){
        if(isset($request->filter)){
            try{
                $filter = json_decode($request->filter);
            }catch (\Exception $e){
                return "";
            }

            $conditions = [];
            $conditions[] = "r.id IN (SELECT MAX(id) FROM pendings AS r WHERE r.status < 3 GROUP BY student_id)";

            if(isset($filter->lms_accounting_id) && $filter->lms_accounting_id){
                $value = trim($filter->lms_accounting_id);
                if(is_numeric($value)){
                    $id = (int)$value;
                    $conditions[] = "(s.stu_id = $id OR s.accounting_id LIKE '%$id%')";
                }else{
                    $conditions[] = "s.accounting_id LIKE '%$value%'";
                }
            }

            $student_name = isset($filter->student_name)?trim($filter->student_name):'';
            if($student_name){
                $student_name = strtoupper($student_name);
                $conditions[] = "s.name LIKE '%$student_name%'";
            }

            if(isset($filter->branch_id) && is_numeric($filter->branch_id)){
                $branch_id = (int)$filter->branch_id;
                if($branch_id !== 0){
                    $conditions[] = "r.branch_id = " . $filter->branch_id;
                }else{
                    $branch_ids = u::getBranchIds($request->users_data);
                    if(!empty($branch_ids)){
                        $branch_ids_string = implode(',',$branch_ids);
                        $conditions[] = "r.branch_id IN ($branch_ids_string)";
                    }
                }
            }else{
                $branch_ids = u::getBranchIds($request->users_data);
                if(!empty($branch_ids)){
                    $branch_ids_string = implode(',',$branch_ids);
                    $conditions[] = "r.branch_id IN ($branch_ids_string)";
                }
            }

            if(isset($filter->status) && is_numeric($filter->status)){
                $status = (int)$filter->status;
                if($status === 0 || $status === 1 || $status === 2){
                    $conditions[] = "r.status = $status";
                }
            }

            if(!empty($conditions)){
                return " WHERE " . implode(" AND ",$conditions);
            }else{
                return "";
            }
        }else{
            return "";
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

    private function countRecords($conditions){
        $query = "
                SELECT 
                    COUNT(t.id) AS count_item
                FROM (
                    SELECT r.id AS id
                    FROM
                        pendings AS r
                        LEFT JOIN students AS s ON r.student_id = s.`id`
                        LEFT JOIN contracts AS c ON r.`contract_id` = c.`id`
                        LEFT JOIN branches AS b ON r.`branch_id` = b.`id`
                        LEFT JOIN reasons AS r1 ON r.reason_id = r1.id
                    $conditions    
                    GROUP BY r.student_id
                ) AS t
            ";
        $res = DB::select(DB::raw($query));
        if(!empty($res)){
            return $res[0]->count_item;
        }else{
            return 0;
        }
    }
}
