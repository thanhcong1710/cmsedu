<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;

class ScopeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($branch_name, Request $request)
    {
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();
      if ($session = $request->users_data) {
        $user_id = $session->id;
        $roles_ids = [];
        if (count($session->roles_detail)) {
          foreach ($session->roles_detail as $role_item) {
            if ($role_item->role_status) {
              $roles_ids[] = $role_item->role_id;
            }
          }
          $data = 1;
          if (count($roles_ids)) {
            rsort($roles_ids);
            $main_role_id = $roles_ids[0];
            if ($main_role_id > ROLE_BRANCH_CEO) {
              if (strlen($branch_name) > 3) {
                $where = '';
                if ($main_role_id == ROLE_REGION_CEO) {
                  $region = DB::select(DB::raw("SELECT * FROM regions WHERE ceo_id = $user_id"));
                  $where = " AND bregion_id = $region->id";
                }
                $query = "SELECT b.*, CONCAT(b.name, ' (', r.name, ')') AS branch_name, r.name AS region_name
                FROM branches AS b LEFT JOIN regions AS r ON b.region_id = r.id
                WHERE (b.name LIKE '%$branch_name%' OR b.brch_id LIKE '%$branch_name%' OR b.accounting_id LIKE '%$branch_name%')
                AND b.`status` > 0 $where";
                $data = DB::select(DB::raw($query));
              } else $data = 2;
            }
          }
        }
      }
      return $response->formatResponse($code, $data);
    }

    public function test(Request $request, $params, $ses, $date) {
      $code = APICode::SUCCESS;
      $data = null;
      // dd(ada()::uniless('Kiểm tra thử thư viện Ada'));
      $response = new Response();
      $sessions = (int)$ses;
      $date = $date ? $date : '2018-04-19';
      $c = '2018-02-15';
      $classdays = (int)$params > 3 ? u::getClassDays($params) : json_decode($params);
      $class_id = (int)$params > 3 ? $params : 56;
      $class_info = u::first("SELECT product_id FROM classes WHERE id = $class_id");
      $product_id = $class_info->product_id;
      $holidays = u::getPublicHolidays($class_id, 0, $product_id);
      // dd($holidays);
      // $data = u::getRealSessions($sessions, $classdays, $holidays, $date);
      $data = u::calcNewStartDate($date, $classdays, $holidays);
      // $data->classdays = $classdays;
      return $response->formatResponse($code, $data);
    }

    public function transferTest(Request $request, $params) {
      $code = APICode::SUCCESS;
      $data = null;
      $params = json_decode($params);
      $tftfid = $params->tftfid;
      $tfamnt = $params->tfamnt;
      $brchid = $params->brchid;
      $prodid = $params->prodid;
      $tfsess = $params->tfsess;
      $response = new Response();
      $data = u::calcTransferTuitionFee($tftfid, $tfamnt, $brchid, $prodid, $tfsess);
      return $response->formatResponse($code, $data);
    }

    public function getBranch(Request $request, $branch_name, $excepted_branch_id, $limited=1){
        $code = APICode::SUCCESS;
        $data = null;
        $response = new Response();
        if ($session = $request->users_data) {
            $user_id = $session->id;
            $roles_ids = [];
            if (count($session->roles_detail)) {
                foreach ($session->roles_detail as $role_item) {
                    if ($role_item->role_status) {
                        $roles_ids[] = $role_item->role_id;
                    }
                }
                $data = 1;
                if (count($roles_ids)) {
                    rsort($roles_ids);
                    $main_role_id = $roles_ids[0];
                    if ($main_role_id > 0) {
                        if (strlen($branch_name) > 3) {
                            $where = '';
                            if($limited){
                                $branch_ids = u::getBranchIds($request->users_data);
                                $branch_ids_string = implode(',',$branch_ids);
                                $where .= " AND b.id IN ($branch_ids_string)";
                            }
                            $query = "SELECT b.*, IF(r.id IS NOT NULL, CONCAT(b.name, ' (', r.name, ')'), b.name) AS branch_name, IF(r.id IS NOT NULL, r.name, 'Chưa có vùng') AS region_name
                              FROM branches AS b LEFT JOIN regions AS r ON b.region_id = r.id
                              WHERE b.id <> $excepted_branch_id AND (b.name LIKE '%$branch_name%' OR b.brch_id LIKE '%$branch_name%' OR b.accounting_id LIKE '%$branch_name%')
                              AND b.`status` > 0 $where";
                            $data = DB::select(DB::raw($query));
//                            echo $query;
                        } else $data = 2;
                    }
                }
            }
        }
        return $response->formatResponse($code, $data);
    }
}
