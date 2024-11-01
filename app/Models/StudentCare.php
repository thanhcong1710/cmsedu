<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\UtilityServiceProvider as u;
class StudentCare extends Model
{
    //protected $table = 'students';
    public $timestamps = false;

    public function getStudents($params)
    {
        $where = "";
        if (!empty($params['s_id'])) {
            $where .= " WHERE id = {$params['s_id']} ";
        }
        $total = u::first("SELECT count(1) as total
                FROM `customer_care` c WHERE c.`crm_id` IN (SELECT crm_id FROM students $where)")->total;
        $query = "SELECT *,
                (SELECT q.title FROM `contact_quality` q WHERE q.id = c.`contact_quality_id`) AS title,
                (SELECT q.score FROM `contact_quality` q WHERE q.id = c.`contact_quality_id`) AS score,
                (SELECT u.full_name FROM `users` u WHERE u.id = c.creator_id) AS full_name
                FROM `customer_care` c WHERE c.`crm_id` IN (SELECT crm_id FROM students $where) order by c.id desc";
        $data = u::query($query);

        $sql = "SELECT crm_id,accounting_id,name FROM students $where";
        $student = u::query($sql);

        return ['data' => ['cares' =>$data, 'info' => $student ? $student[0] : null], 'total' => $total];
    }

    public function getCollaborator()
    {
        $query = "SELECT 
                    c.`code` as id , 
                    concat(c.`code`,' - ',c.`school_name`) as name,
                    c.`school_name`,c.`address`,c.`personal_name`,c.`phone_number`,
                    concat(c.`school_name`,'*',c.`address`,'*',c.`personal_name`,'*',c.`phone_number`) as info,
                    c.email
                    FROM `collaborators` c where  c.status = '1'";
        $data = u::query($query);

        return $data;
    }
}
