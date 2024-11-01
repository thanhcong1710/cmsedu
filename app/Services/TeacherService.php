<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

/*
* Created by HoiLN
*/
class TeacherService
{
    CONST TEACHER_ROLE = 'CM';

    public function getTeachersByBranch($branch_id)
    {
        $sql = "SELECT u.id, CONCAT(u.full_name, ' - ', u.username) as fullname 
                from users as u 
                INNER JOIN term_user_branch as tub ON tub.user_id = u.id 
                INNER JOIN roles as r ON r.id = tub.role_id
                WHERE tub.branch_id =:branch_id AND r.name =:role_name";

        $result = DB::select(DB::raw($sql),['branch_id' => $branch_id,
                                            'role_name' => self::TEACHER_ROLE]);
        return $result;
    }
}