<?php

namespace App\Models;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';
    public $timestamps = false;

    public function getAllBranchWithEcs()
    {
        $query = "SELECT b.id as branch_id, b.accounting_id, b.name as branch_name, 
                         GROUP_CONCAT(u.hrm_id) as user_accounting_id,
                         CONCAT('[',GROUP_CONCAT(CONCAT('{\"', u.accounting_id, '\":\"',u.id, '\"}')),']') as user_ids,
                         tub.role_id FROM branches b 
                    LEFT JOIN term_user_branch tub ON b.id = tub.branch_id AND tub.role_id in (68, 69)
                    LEFT JOIN users u ON u.id = tub.user_id
                    GROUP BY b.accounting_id";
        $branches = u::query($query);
        foreach ($branches as $branch) {
            $branch->user_accounting_ids = explode(',', u::get($branch, 'user_accounting_id'));
            $user_ids = json_decode(u::get($branch, 'user_ids'));
            if (!empty($user_ids)) {
                $accounting_ids = [];
                foreach ($user_ids as $id) {
                    $accounting_ids = array_merge($accounting_ids, (Array)$id);
                }
                $branch->user_ids = $accounting_ids;
            }
        }
        return u::convertArrayToObject($branches, 'accounting_id');
    }

    public function getAllBranchEcs()
    {
        $query = "SELECT b.id as branch_id, b.accounting_id, b.name as branch_name, 
                         GROUP_CONCAT(u.hrm_id) as user_accounting_id,
                         CONCAT('[',GROUP_CONCAT(CONCAT('{\"', u.hrm_id, '\":\"',u.id, '\"}')),']') as user_ids,
                         tub.role_id FROM branches b 
                    LEFT JOIN term_user_branch tub ON b.id = tub.branch_id AND tub.role_id in (68, 69)
                    LEFT JOIN users u ON u.id = tub.user_id
                    GROUP BY b.accounting_id";
        $branches = u::query($query);
        foreach ($branches as $branch) {
            $branch->user_accounting_ids = explode(',', u::get($branch, 'user_accounting_id'));
            $user_ids = json_decode(u::get($branch, 'user_ids'));
            if (!empty($user_ids)) {
                $accounting_ids = [];
                foreach ($user_ids as $id) {
                    $accounting_ids = array_merge($accounting_ids, (Array)$id);
                }
                $branch->user_ids = $accounting_ids;
            }
        }
        return u::convertArrayToObject($branches, 'accounting_id');
    }
}
