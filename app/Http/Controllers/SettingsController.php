<?php
namespace App\Http\Controllers;

use App\Models\CyberAPI;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * ========================================================== *
 * --------------------- Apax ERP System --------------------
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package Apax ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function holidays(Request $request, $class_id, $product_id) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $data->classdays = u::getClassDays($class_id);
        $data->holidays = u::getPublicHolidays($class_id, 0, $product_id);
      }
      return $response->formatResponse($code, $data);
    }
    public function holidays_v2(Request $request, $zone_id, $product_id) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            //   $data->classdays = u::getClassDays($class_id);
            $branchs = u::first("SELECT id from branches where zone_id = $zone_id");
            $branch_id = $branchs->id;
            $data->holidays = u::getPublicHolidays(0, $branch_id, $product_id);
        }
        return $response->formatResponse($code, $data);
    }
    public function holidates(Request $request, $class_id, $product_id) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            $data->classdays = u::getClassDays($class_id);
            $data->holidates = u::getPublicHolidays($class_id, 0, $product_id);
            $resp = [];
            $where = ($product_id && $product_id !== 9999) ? "AND (h.products LIKE '[$product_id,%' OR h.products LIKE '%,$product_id]' OR h.products LIKE '%,$product_id,%' OR h.products LIKE '[$product_id]') AND h.`status` > 0" : ' AND h.`status` > 0 ';
            if ((int)$class_id) {
                $resp = u::query("SELECT h.* FROM public_holiday AS h
                    LEFT JOIN branches AS b ON h.zone_id = b.zone_id
                    LEFT JOIN classes AS c ON c.branch_id = b.id
                    WHERE c.id = $class_id $where");
            } elseif ((int)$branch_id) {
                $resp = u::query("SELECT h.* FROM public_holiday AS h
                    LEFT JOIN branches AS b ON h.zone_id = b.zone_id
                    WHERE b.id = $branch_id $where");
            }
            if (count($resp)) {
                usort($resp, function ($a, $b) {
                    return strcmp($a->start_date, $b->start_date);
                });
                if($product_id === 9999){
                    $products = u::query("SELECT id FROM products WHERE status = 1");
                    $holidays = [];
                    foreach ($products as $p){
                        $holidays[$p->id] = [];
                    }
                    foreach ($resp as $re){
                        $product_ids = explode(',',str_replace('[','',str_replace(']','',$re->products)));
                        foreach ($holidays as $key => $holiday){
                            if(in_array($key, $product_ids)){
                                $holidays[$key][] = (Object)[
                                    'id' => $re->id,
                                    'name' => $re->name,
                                    'start_date' => $re->start_date,
                                    'end_date' => $re->end_date
                                ];
                            }
                        }
                    }
                    $resp = $holidays;
                }else{
                    foreach ($resp as &$re){
                        $re = (Object)[
                            'id' => $re->id,
                            'name' => $re->name,
                            'start_date' => $re->start_date,
                            'end_date' => $re->end_date
                        ];
                    }
                    unset($re);
                }
                $data->holidays = $resp;
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function test(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            $data->env = ENVIRONMENT;
        }
        return $response->formatResponse($code, $data);
    }

    public function tools(Request $request, $id) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
          $data = (Object)[];
          $code = APICode::SUCCESS;
          $user_id = $session->id;
          $role_id = $session->role_id;
          $branches = $session->branches_ids;
          $id = $id ? (int)$id : (int)explode(',', $branches)[0];
          $where = "status > 0";
          $data->branch_id = $id;
          $data->branches = u::query("SELECT id, `name` FROM branches WHERE $where");
          $data->products = u::query("SELECT id, `name` FROM products WHERE $where");
          $data->tuitions = u::query("SELECT id, `name` FROM tuition_fee WHERE $where");
          $data->holidays = u::query("SELECT p.id, p.name, p.start_date, p.end_date FROM public_holiday p LEFT JOIN branches b ON p.zone_id = b.zone_id WHERE b.id IN ($id) AND p.status > 0");
          $data->classes = u::query("SELECT c.id, c.cls_name, (SELECT GROUP_CONCAT(DISTINCT class_day SEPARATOR ',') AS class_days FROM sessions WHERE class_id = c.id) FROM classes c WHERE c.branch_id IN ($id)");
        }
        return $response->formatResponse($code, $data);
    }

    public function classes(Request $request, $branch_id) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
          $data = (Object)[];
          $code = APICode::SUCCESS;
          $user_id = $session->id;
          $role_id = $session->role_id;
          $branches = $session->branches_ids;
          $id = $branch_id ? (int)$branch_id : (int)explode(',', $branches)[0];
          $where = "status > 0";
        //   $data->classes = u::query("SELECT c.id, c.cls_name, (SELECT GROUP_CONCAT(DISTINCT class_day SEPARATOR ',') AS class_days FROM sessions WHERE class_id = c.id) AS class_days FROM classes c LEFT JOIN semesters s ON c.semester_id = s.id WHERE c.branch_id IN ($id) AND s.end_date >= CURRENT_DATE()");
        $data->classes = u::query("SELECT c.id, c.cls_name, (SELECT GROUP_CONCAT(DISTINCT class_day SEPARATOR ',') AS class_days FROM sessions WHERE class_id = c.id) AS class_days FROM classes c WHERE c.branch_id IN ($id)");
        }
        return $response->formatResponse($code, $data);
    }

    public function branches(Request $request) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = [];
        $code = APICode::SUCCESS;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $branches = $session->branches_ids;
        $where = "id > 0 AND status > 0";
        if ($role_id < 86868686 && !($request->all)) {
          $where.= " AND id IN ($branches)";
        }
        $query = "SELECT id, `name` FROM branches WHERE $where";
        $data = u::query($query);
      }
      return $response->formatResponse($code, $data);
    }

    public function semesters(Request $request) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = [];
        $code = APICode::SUCCESS;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $branches = $session->branches_ids;
        $where = "id > 0 AND status > 0 AND start_date <= CURDATE() AND end_date >= CURDATE()";
        $query = "SELECT id, `name` FROM semesters WHERE $where";
        $data = u::query($query);
      }
      return $response->formatResponse($code, $data);
    }

    public function searchBranches(Request $request, $filter) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = [];
            $code = APICode::SUCCESS;
            $user_id = $session->id;
            $role_id = $session->role_id;
            $branches = $session->branches_ids;
            $where = "b.status > 0";
            if ($role_id < 86868686) {
                $where.= " AND b.id IN ($branches)";
            }
            if ($filter != '_') {
                $filter = json_decode($filter);
                // dd($filter);
                if ((int)$filter->id) {
                    $where.= " AND b.id = ".(int)$filter->id;
                }
                if ($filter->name != '') {
                    $where.= " AND b.name LIKE '%$filter->name%'";
                }
                if ((int)$filter->zone) {
                    $where.= " AND z.id = ".(int)$filter->zone;
                }
                if ((int)$filter->region) {
                    $where.= " AND r.id = ".(int)$filter->region;
                }
            }
            $query = "SELECT b.id, b.name, r.name region, z.name zone FROM branches b LEFT JOIN zones z ON b.zone_id = z.id LEFT JOIN regions r ON b.region_id = r.id WHERE $where ORDER BY id ASC";
            $data = u::query($query);
        }
        return $response->formatResponse($code, $data);
    }

    public function branchesConfig(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = (object)[];
            $code = APICode::SUCCESS;
            $user_id = $session->id;
            $role_id = $session->role_id;
            $branches = $session->branches_ids;
            $where = "b.status > 0";
            if ($role_id < ROLE_ADMINISTRATOR) {
                $where.= " AND b.id IN ($branches)";
            }
            $branches = u::query("SELECT b.id, b.brch_id lms, b.name, r.name region, z.name zone FROM branches b LEFT JOIN zones z ON b.zone_id = z.id LEFT JOIN regions r ON b.region_id = r.id WHERE $where ORDER BY id ASC");
            $regions = u::query("SELECT * FROM regions WHERE `status` > 0");
            $zones = u::query("SELECT * FROM zones WHERE `status` > 0");
            $data->branches = $branches && count($branches) ? $branches : [];
            $data->regions = $regions && count($regions) ? $regions : [];
            $data->zones = $zones && count($zones) ? $zones : [];
        }
        return $response->formatResponse($code, $data);
    }

    public function tuitionsConfig(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = (object)[];
            $code = APICode::SUCCESS;
            $user_id = $session->id;
            $role_id = $session->role_id;
            $branches = $session->branches_ids;
            $where = "b.status > 0";
            if ($role_id < 86868686) {
                $where.= " AND b.id IN ($branches)";
            }
            $branches = u::query("SELECT b.id, b.brch_id lms, b.name, r.name region, z.name zone FROM branches b LEFT JOIN zones z ON b.zone_id = z.id LEFT JOIN regions r ON b.region_id = r.id WHERE $where ORDER BY id ASC");
            $regions = u::query("SELECT r.*, CONCAT(u.full_name, ' - ', u.username) ceo FROM regions r LEFT JOIN users u ON r.ceo_id = u.id WHERE r.status > 0");
            $products = u::query("SELECT * FROM products WHERE status > 0");
            $zones = u::query("SELECT * FROM zones WHERE status > 0");
            // $igarten_tuitions = u::query("SELECT id, name, product_id, session, price, discount, receivable, available_date, expired_date, branch_id FROM tuition_fee WHERE status > 0 AND product_id = 1 AND CURRENT_DATE BETWEEN available_date AND expired_date");
            // $april_tuitions = u::query("SELECT id, name, product_id, session, price, discount, receivable, available_date, expired_date, branch_id FROM tuition_fee WHERE status > 0 AND product_id = 2 AND CURRENT_DATE BETWEEN available_date AND expired_date");
            // $cdi_tuitions = u::query("SELECT id, name, product_id, session, price, discount, receivable, available_date, expired_date, branch_id FROM tuition_fee WHERE status > 0 AND product_id = 3 AND CURRENT_DATE BETWEEN available_date AND expired_date");
            $data->ucrea_tuitions = u::query("SELECT id, name, product_id, session, price, discount, receivable, available_date, expired_date, branch_id FROM tuition_fee WHERE status > 0 AND product_id = 1");
            $data->bright_ig_tuitions = u::query("SELECT id, name, product_id, session, price, discount, receivable, available_date, expired_date, branch_id FROM tuition_fee WHERE status > 0 AND product_id = 2");
            $data->black_hole_tuitions = u::query("SELECT id, name, product_id, session, price, discount, receivable, available_date, expired_date, branch_id FROM tuition_fee WHERE status > 0 AND product_id = 3");
            $data->branches = $branches && count($branches) ? $branches : [];
            $data->products = $products && count($products) ? $products : [];
            $data->regions = $regions && count($regions) ? $regions : [];
            $data->zones = $zones && count($zones) ? $zones : [];
        }
        return $response->formatResponse($code, $data);
    }

    public function loadProductInformation(Request $request, $product_id) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
          $data = (Object)[];
          $code = APICode::SUCCESS;
          $data->product = u::first("SELECT * FROM products WHERE id = $product_id");
          $data->tuitions = u::query("SELECT * FROM tuition_fee WHERE product_id = $product_id AND status > 0");
          // $data->tuitions = u::first("SELECT * FROM tuition_fee WHERE product_id = $product_id AND status > 0 AND CURRENT_DATE BETWEEN available_date AND expired_date");
        }
        return $response->formatResponse($code, $data);
    }

    public function loadInformation(Request $request, $tuition_id) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
          $data = (Object)[];
          $code = APICode::SUCCESS;
          $tuition_info = u::first("SELECT * FROM tuition_fee WHERE id = $tuition_id");
          $branch_where = $tuition_info->branch_id ? "id IN ($tuition_info->branch_id)" : "id = 0";
          $data->tuition = $tuition_info;
          $data->branches = u::query("SELECT * FROM branches WHERE $branch_where");
          $data->black_hole_selected = u::query("SELECT id, name, product_id, session, price, discount, receivable, available_date, expired_date, branch_id FROM tuition_fee WHERE product_id = 3 AND id IN (SELECT exchange_tuition_fee_id FROM tuition_fee_relation WHERE status > 0 AND tuition_fee_id = $tuition_id)");
          $data->bright_ig_selected = u::query("SELECT id, name, product_id, session, price, discount, receivable, available_date, expired_date, branch_id FROM tuition_fee WHERE product_id = 2 AND id IN (SELECT exchange_tuition_fee_id FROM tuition_fee_relation WHERE status > 0 AND tuition_fee_id = $tuition_id)");
          $data->ucrea_selected = u::query("SELECT id, name, product_id, session, price, discount, receivable, available_date, expired_date, branch_id FROM tuition_fee WHERE product_id = 1 AND id IN (SELECT exchange_tuition_fee_id FROM tuition_fee_relation WHERE status > 0 AND tuition_fee_id = $tuition_id)");
        }
        return $response->formatResponse($code, $data);
    }

    public function cms(Request $request, $branch_id) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $branches = explode(',',$branch_id);
            if(!empty($branches)){
                $valid = true;
                foreach ($branches as $branch){
                    if(!is_numeric($branch)){
                        $valid = false;
                        break;
                    }
                }

                if($valid){
                    $code = APICode::SUCCESS;
                    $query = "
                        SELECT
                            u.id AS cm_id,
                            u.username AS cm_name
                        FROM
                            users AS u
                            LEFT JOIN term_user_branch AS t ON u.id = t.user_id
                        WHERE
                            (t.role_id = 55 OR t.role_id = 56)
                            AND t.branch_id = 1
                            AND u.status IN ($branch_id)
                        GROUP BY u.id
                    ";
                    $data = DB::select(DB::raw($query));
                }else{
                    $code = APICode::WRONG_PARAMS;
                }
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function tuitionFeeExchange($tid, $amount, $bid, $pid, $sessions){
        $response = new Response();

        $info = u::calcTransferTuitionFee($tid, $amount, $bid, $pid, $sessions);
        return $response->formatResponse(APICode::SUCCESS, $info);
    }

    public function tuitionFeeExchangeForTuitionTransfer($tid, $amount, $bid, $pid){
        $response = new Response();

        $info = u::exchangeFee($tid, $amount, $bid, $pid);

        $data = (Object)[
          'special' => $info->special,
          'sessions' => $info->sessions,
          'transfer_tuition_fee' => $info->transfer_tuition_fee,
          'receive_tuition_fee' => $info->receive_tuition_fee,
          'transfer_amount' => $info->transfer_amount,
          'single_price' => $info->single_price
        ];

        return $response->formatResponse($info->code, $data, $info->message);
    }

    public function tuitionFeeExchangeForClassTransfer($tid, $amount, $bid, $pid, $sessions){
        $response = new Response();

        $info = u::exchangeFeeForClassTransfer($tid, $amount, $bid, $pid, $sessions);

        $data = (Object)[
          'special' => $info->special,
          'sessions' => $info->sessions,
          'transfer_tuition_fee' => $info->transfer_tuition_fee,
          'receive_tuition_fee' => $info->receive_tuition_fee,
          'transfer_amount' => $info->transfer_amount,
          'single_price' => $info->single_price
        ];

        return $response->formatResponse($info->code, $data, $info->message);
    }

    public function reUpdate($branch_id){
        $query = "SELECT
                      id, start_date, end_date, STATUS, `type`, enrolment_id, creator_id, `relation_contract_id`
                    FROM
                      contracts
                    WHERE
                      end_date > CURDATE()
                      AND STATUS > 0 AND STATUS < 7
                      AND `type` IN (1,2,3,4, 5, 6)
                      AND id NOT IN (SELECT relation_contract_id FROM contracts WHERE relation_contract_id IS NOT NULL)
                      AND branch_id = $branch_id
                      AND processed = 0
                    ORDER BY student_id ASC
                      ";
        $contracts = DB::select(DB::raw($query));

        if(!empty($contracts)){
            foreach ($contracts as $contract){

            }
        }

        return $branch_id;
    }

    public function testAPI(){
      $cyber = new CyberAPI();
      $branch_id = 'C02';
      $name = 'Trung tâm Vinhomes Gardenia';
      $res = $cyber->createBranch($branch_id, $name, 100);

      var_dump($res);die;
    }
    public function tuitionFeeExchangeV2($tid, $amount, $bid, $pid, $sessions) {
        $response = new Response();

        try{
          $info = u::calcTransferTuitionFeeV2($tid, $amount, $bid, $pid, $sessions);
          return $response->formatResponse($info->code, $info, $info->message);
        }catch (\Exception $exception){
          return $response->formatResponse(APICode::PAGE_NOT_FOUND, null, 'Không thể quy đổi gói phí');
        }
    }
}
