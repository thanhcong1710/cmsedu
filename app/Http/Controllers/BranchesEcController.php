<?php

namespace App\Http\Controllers;

//use App\Models\CyberAPI;
//use App\Providers\UtilityServiceProvider as u;
use App\Models\APICode;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//use App\Models\Branch;

class BranchesEcController extends Controller
{

    public function index(Request $request)
    {
        $session = $request->users_data;
        $params = $request->all();
        $response = new Response();
        if ($session) {
            $branchIds = isset($params['branch_ids']) ? $params['branch_ids'] : null;
            $where = "where tb.role_id in (68,69) AND u.status = 1 AND tb.status = 1 ";
            if (!empty($branchIds)) {
                if (is_array($request['branch_ids'])) {
                    $strBranchIds = implode(",", $branchIds);
                    $where .= " AND tb.`branch_id` in ($strBranchIds) ";
                } else {
                    $where .= " AND tb.`branch_id` = " . $branchIds;
                }
            }
            $sql = "SELECT u.id AS id, u.`full_name` AS name FROM `term_user_branch` tb
                    JOIN `users` u ON u.id = tb.`user_id` $where";
            $data = DB::select(DB::raw($sql));
            return $response->formatResponse(APICode::SUCCESS, ['data' => $data]);
        }
        return $response->formatResponse(APICode::PERMISSION_DENIED, null);
    }

}
