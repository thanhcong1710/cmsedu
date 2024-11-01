<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TermUserBranch extends Model
{
    protected $table = 'term_user_branch';
    public $timestamps = false;

    public function updateRoleForUser($userId, $branchId, $roleId, $status, $creatorId)
    {
        $condition = [
            ["user_id", "=", (int)$userId],
            ["branch_id", "=", (int)$branchId],
            ["role_id", "=", (int)$roleId],
        ];
        if (!TermUserBranch::where($condition)->exists() && $status) {
            DB::table($this->table)
                ->insert([
                    "user_id" => (int)$userId,
                    "branch_id" => (int)$branchId,
                    "role_id" => (int)$roleId,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                    "creator_id" => (int) $creatorId,
                    "status" => 0
                ]);
        }
        DB::table($this->table)
            ->where($condition)
            ->update(['status' => $status ? 1 : 0, "updated_at" => date('Y-m-d H:i:s')]);
    }
}
