<?php

namespace App\Models;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    const ROLE_SUPER_ADMINISTRATOR = 999999999;
    const ROLE_MANAGERS = 88888888;
    const ROLE_ADMINISTRATOR = 86868686;
    const ROLE_ZONE_CEO = 85858585;
    const ROLE_REGION_CEO = 7777777;
    const ROLE_BRANCH_CEO = 686868;
    const ROLE_CASHIER = 83;

    const ROLE_TEACHER = 36;
    const ROLE_CM = 55;
    const ROLE_OM = 56;
    const ROLE_CS_CASHIER = 57;
    const ROLE_CS_CASHIER_LEADER = 58;
    const ROLE_EC = 68;
    const ROLE_EC_LEADER = 69;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getRoleDetail($userId)
    {
        $res = u::first("select t.created_at, u.id, u.full_name, u.accounting_id, u.email, u.phone,t.branch_id, GROUP_CONCAT(t.role_id) as role_id from users u left join term_user_branch t on u.id = t.user_id and t.status = 1 where u.id = $userId");
        if (isset($res->role_id)) {
            $res->role_ids = array_map("intval", explode(",", $res->role_id));
        }
        return $res;
    }
}
