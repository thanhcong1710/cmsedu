<?php

namespace App\Models;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;

class CustomerCare extends Model
{
    //
    protected $table = 'customer_care';

    public function getAll($params)
    {
        $where = ' WHERE 1 ';
        if (!empty($params->branch_id)) {
            $where .= " AND t.branch_id = $params->branch_id ";
        }
        if (!empty($params->student_name)) {
            $where .= " AND (st.name LIKE '%$params->student_name%' OR st.cms_id LIKE '%$params->student_name%' )";
        }
        if (!empty($params->hrm_id)) {
            $where .= " AND cr.hrm_id = '$params->hrm_id'";
        }
        if (!empty($params->student_id)) {
            $where .= " AND st.id= $params->student_id";
        }

        $query = "SELECT cc.*, q.title AS quality_name, q.score AS quality_score,st.crm_id,st.name AS student_name, 
            st.stu_id as lms_id, st.accounting_id, ctm.name as contact_name, CONCAT(cr.full_name,' - ', cr.username) as creator 
            from customer_care as cc 
            JOIN users as cr on cr.id = cc.creator_id 
            JOIN contact_methods as ctm on ctm.id = cc.contact_method_id 
            JOIN students as st on st.crm_id = cc.crm_id 
            LEFT JOIN term_student_user AS t ON t.student_id = st.id
            LEFT JOIN contact_quality AS q ON q.id = cc.contact_quality_id $where";
        return u::query($query);
    }

}
