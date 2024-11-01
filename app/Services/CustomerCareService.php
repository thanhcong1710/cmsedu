<?php

namespace App\Services;

use App\Providers\UtilityServiceProvider as u;

class CustomerCareService {

    public static function getStudentCareReport($params){
        $where = "";
        if (!empty($params['ecc'])) {
            if (is_array($params['ecc'])){
                $eccIds = implode(",", $params['ecc']);
                $where  .= "us.`id` in ($eccIds) AND ";
            }
            else{
                if ($params['ecc'] != "null")
                    $where  .= "us.`id` in ({$params['ecc']}) AND ";
            }

        }
        if (!empty($params['branch_ids'])) {
            if (is_array($params['branch_ids'])){
                $branchIds = implode(",", $params['branch_ids']);
                $where .= "ts.`branch_id` in ($branchIds) ";
            }
            else{
                if ($params['branch_ids'] != "null")
                    $where .= "ts.`branch_id` in ({$params['branch_ids']}) ";
            }

        }
        if (!empty($params['start_date'])) {
            $startDate = $params['start_date'] . " 00:00:00";
            $where .= "AND cc.`created_at` >= '$startDate' ";
        }
        if (!empty($params['end_date'])) {
            $endDate = $params['end_date'] . " 23:59:59";
            $where .= "AND cc.`created_at` <= '$endDate' ";
        }
        if (substr($where, 0, 3) === "and") {
            $where = substr($where, 3, strlen($where));
        }

        if(!empty($where)){
            $where = " AND $where ";
        }

        $limit = "";
        if(!empty($params['limit'])){
            $l = (int) $params['limit'];
            $p = (int) $params['page'] ;
            $o = ($p -1) * $l;
            $limit = "limit  $o, $l ";
        }
        $order = "ORDER BY cc.crm_id";
        $sql = "SELECT cc.*,us.hrm_id, q.title AS quality_name, q.score AS quality_score,st.crm_id,st.name AS student_name, st.stu_id AS lms_id, st.accounting_id, cm.name AS contact_name, CONCAT(us.full_name,' - ', us.username) AS creator, us.`superior_id`, br.name AS br_name
                FROM customer_care AS cc
                JOIN users AS us ON us.id = cc.creator_id
                JOIN contact_methods AS cm ON cm.id = cc.contact_method_id
                JOIN students AS st ON st.crm_id = cc.crm_id
                LEFT JOIN term_student_user AS ts ON ts.student_id = st.id
                LEFT JOIN branches AS br ON br.id = ts.`branch_id`
                INNER JOIN contact_quality AS q ON q.id = cc.contact_quality_id $where $order $limit";

        $sqlCount = "SELECT count(cc.id) as total
                FROM customer_care AS cc
                JOIN users AS us ON us.id = cc.creator_id
                JOIN contact_methods AS cm ON cm.id = cc.contact_method_id
                JOIN students AS st ON st.crm_id = cc.crm_id
                LEFT JOIN term_student_user AS ts ON ts.student_id = st.id
                LEFT JOIN branches AS br ON br.id = ts.`branch_id`
                INNER JOIN contact_quality AS q ON q.id = cc.contact_quality_id $where $order";

        $total = u::first($sqlCount)->total;
        $students = u::query($sql);
        if(empty($students)){
            return null;
        }

        return ['data' => $students, 'total' => $total];

    }
}