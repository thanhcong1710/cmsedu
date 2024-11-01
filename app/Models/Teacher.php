<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Teacher extends Model
{
    CONST TEACHER_ROLE = 'CM';

    protected $table = 'teachers';
    public $timestamps = false;

    public static function getTeachersByBranch($branch_id)
    {
        $tableName = 'users';
        $sql = "SELECT u.id, CONCAT(u.full_name, ' - ', u.username) as fullname 
                from $tableName as u 
                INNER JOIN term_user_branch as tub ON tub.user_id = u.id 
                INNER JOIN branches as b ON b.id = tub.branch_id
                INNER JOIN roles as r ON r.id = tub.role_id
                WHERE b.id =:branch_id AND r.id in (55,56)";

        $result = DB::select(DB::raw($sql),['branch_id' => $branch_id]);
        return $result;
    }
}
