<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;

class Suggest extends Model
{
    public function getListTuitionFee($key){

        if (!empty($key)){
            $query = "SELECT
                c.id AS id,
                c.branch_id,
                c.product_id,
                c.type,
                c.status,
                (SELECT NAME FROM `products` WHERE id = c.`product_id`) product_name,
                (SELECT NAME FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) name,
                c.`tuition_fee_id`,c.`tuition_fee_price`,c.`created_at`,
                c.`real_sessions`,c.`coupon`,c.`total_charged`,c.`total_sessions`,c.`must_charge`,c.`debt_amount`
                FROM
                contracts AS c 
                WHERE 
                c.`student_id` = $key 
                AND c.`status` NOT IN (6,7) 
                AND c.`debt_amount` = 0 
                AND c.`type` NOT IN (0)
                AND (SELECT COUNT(id) FROM `tuition_transfer` tu WHERE tu.`from_contract_id`  = c.id AND tu.`final_approved_at` IS NULL) = 0
                AND (SELECT COUNT(id) FROM `class_transfer` cl WHERE cl.`contract_id`  = c.id AND cl.`final_approved_at` IS NULL) = 0
                AND (SELECT COUNT(id) FROM `withdrawal_fees` wd WHERE wd.`contract_id`  = c.id) = 0";
            $res = DB::select(DB::raw($query));
            $result = [];
            if(!empty($res)){
                $result = array_values($res);
            }
            return $result;
        }

    }

    public function suggestSenderV1($key, $branch_id){
        $result = [];
        if($key && $key !== '_'){
            $query = "SELECT
                CONCAT(s.`crm_id`,' - ',s.`name`,' (',s.accounting_id,')') AS contract_name,
                c.id AS contract_id,
                c.branch_id,
                c.product_id,
                c.program_id,
                s.`id` AS student_id,
                s.`crm_id` AS crm_id,
                s.accounting_id AS accounting_id,
                s.`name` AS student_name
                FROM
                contracts AS c
                LEFT JOIN students AS s ON c.`student_id` = s.`id`
                WHERE c.id IN ( SELECT * FROM
                (SELECT c.id
                FROM
                contracts AS c
                LEFT JOIN students AS s ON c.`student_id` = s.`id`
                WHERE c.branch_id = $branch_id
                AND c.status > 0 AND c.status < 7
                AND (s.name LIKE '%$key%'  OR s.accounting_id LIKE '%$key%' OR s.crm_id LIKE '%$key%') GROUP BY c.id LIMIT 10)
                temp)
                GROUP BY c.student_id";

            $res = DB::select(DB::raw($query));
            if(!empty($res)){
                $result = array_values($res);
            }
        }
        return $result;
    }

    public function getListClassAvailable($branch_id=0, $program_id=0, $class_id = 0){
        $where = "";
        if ($class_id !=0)
            $where = "c.id != $class_id AND ";
        $result = [];
        $query = "SELECT
                c.`cls_name` AS name,
                c.id AS id,
                s.`class_day` AS class_day,
                c.program_id AS program_id,
                (SELECT cjrn_classdate FROM schedules WHERE c.id = class_id AND STATUS = 1 AND cjrn_classdate > CURRENT_DATE() ORDER BY cjrn_classdate ASC LIMIT 1) AS nearest_day
                FROM
                classes AS c
                LEFT JOIN sessions AS s ON c.id = s.`class_id`
                WHERE
                $where
                c.`program_id` IN ($program_id)
                AND c.cls_iscancelled = 'no'
                AND c.`cls_enddate` > CURDATE()
                AND c.cm_id IS NOT NULL
                AND s.status > 0
                AND c.teacher_id IS NOT NULL
                AND c.cm_id NOT IN (SELECT user_id FROM term_user_branch WHERE role_id IN (55, 56) AND branch_id = $branch_id AND STATUS = 0)
                AND c.teacher_id NOT IN (SELECT user_id FROM term_user_branch WHERE role_id = 36 AND branch_id = $branch_id AND STATUS = 0)
                GROUP BY c.id, s.class_day";

        $res = DB::select(DB::raw($query));
        if(!empty($res)){
            $result = array_values($res);
        }
        return $result;
    }

}
