<?php

namespace App\Http\Controllers;

//use App\Models\Branch;
//use App\Models\Config;
use App\Models\Issue;
use App\Models\Report;
use App\Services\RenewalsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use App\Models\Student;
//use App\Models\Contract;
//use App\Models\Payment;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;
use App\Services\StudentReportService;
use App\Services\TuitionFeeService;
use App\Services\SemesterService;
use App\Services\StudentHistoryService;
use App\Services\StudentQuantity;
use App\Services\StudentWithdrawService;

class ReportsController extends Controller
{
    private function getScope($scope, $branches = []) {
        $resp = $branches;
        if ($scope) {
            $is_zone = count(array_filter($scope, function($item) {
                return (int)$item > 9000 ;
            }));
            $is_region = count(array_filter($scope, function($item) {
                return (int)$item > 3000;
            }));
            $branch_list = [];
            if ($is_zone) {
                $zone_list = [];
                $branch_list = [];
                foreach($scope as $itm) {
                    if ((int)$itm > 9000) {
                        $zone_list[] = (int)$itm - 9000;
                    } else {
                        $branch_list[] = (int)$itm;
                    }
                }
                if (count($zone_list)) {
                    $zone_list = implode(',', $zone_list);
                    $list = u::query("SELECT id FROM branches WHERE zone_id IN ($zone_list) AND status > 0");
                    if (count($list)) {
                        $resp = [];
                        foreach ($list as $it) {
                            $resp[] = $it->id;
                        }
                        if (count($branch_list)) {
                            foreach ($branch_list as $id) {
                                if (!in_array($id, $resp)) {
                                    $resp[] = $id;
                                }
                            }
                        }
                    }
                }
            } elseif ($is_region) {
                $region_list = [];
                $branch_list = [];
                foreach($scope as $itm) {
                    if ((int)$itm > 3000) {
                        $region_list[] = (int)$itm - 3000;
                    } else {
                        $branch_list[] = (int)$itm;
                    }
                }
                if (count($region_list)) {
                    $region_list = implode(',', $region_list);
                    $list = u::query("SELECT id FROM branches WHERE region_id IN ($region_list) AND status > 0");
                    if (count($list)) {
                        $resp = [];
                        foreach ($list as $it) {
                            $resp[] = $it->id;
                        }
                        if (count($branch_list)) {
                            foreach ($branch_list as $id) {
                                if (!in_array($id, $resp)) {
                                    $resp[] = $id;
                                }
                            }
                        }
                    }
                }
            } else {
                $resp = [];
                foreach ($scope as $it) {
                    $resp[] = (int)$it;
                }
            }
        }
        return implode(',', $resp);
    }

    private function making($query, $quary, $page, $limit) {
        $data = null;
        $suma = u::first($quary);
        $list = u::query($query);
        $data = (Object)[];
        $pagination = (Object)[];
        $data->list = [];
        $pagination->spage = 0;
        $pagination->cpage = 0;
        $pagination->total = 0;
        $pagination->limit = $limit;
        $pagination->lpage = 0;
        $pagination->ppage = 0;
        $pagination->npage = 0;
        $data->paging = $pagination;
        if (count($list)) {
            $total = $suma && isset($suma->total) ? (int)$suma->total : 0;
            $pagination->spage = 1;
            $pagination->cpage = $page;
            $pagination->total = $total;
            $pagination->limit = $limit;
            $pagination->lpage = ($total % $limit) == 0 ? (int)($total / $limit) : (int)($total / $limit) + 1;
            $pagination->ppage = $page > 0 ? $page - 1 : 0;
            $pagination->npage = $page < $pagination->lpage ? $page + 1 : $pagination->lpage;
            $data->list = $list;
        }
        $data->paging = $pagination;
        return $data;
    }

    public function params($request, $session, $isExport = 0) {
        $params = null;
        if ($request && $session) {
            $params = (Object)[];
            $today = date('Y-m-d');
            $date = isset($request->date) ? $request->date : date('Y-m');
            $from = isset($request->from) ? $request->from : $today;
            $to = isset($request->to) ? $request->to : $today;
            if (!preg_match('#[0-9]{4}-[0-9]{2}-[0-9]{2}$#isu', $from, $matches) || strtotime($from) > time()) {
                $from = date('Y-m-d', strtotime('-7 days'));
            }
            if (!preg_match('#[0-9]{4}-[0-9]{2}-[0-9]{2}$#isu', $to, $matches) || strtotime($to) > time()) {
                $to = date('Y-m-d');
            }
            $products = isset($request->products) ? implode(',',$request->products) : null;
            $cms = isset($request->cms) ? implode(',',$request->cms) : null;
            $keyword = isset($request->keyword) ? trim((string)$request->keyword) : null;
            $type = isset($request->type) ? (int)$request->type : null;
            $scope = $this->getScope($request->scope, explode(',',$session->branches_ids));
            $page = isset($request->page) ? (int)$request->page : 1;
            $limit = isset($request->limit) ? (int)$request->limit : 20;
            $point = ($page - 1) * $limit;
            $point = $point < 0 ? 0 : $point;
            $params->k = $keyword;
            $params->r = $products;
            $params->e = $type;
            $params->a = $date;
            $params->f = $from;
            $params->t = $to;
            $params->s = $scope;
            $params->p = $page;
            $params->c = $cms;

            if(!$isExport || (isset($request->page) && isset($request->limit))){
                $params->l = $limit;
                $params->d = $point;
            }
        }
        return $params;
    }

    public function report01a(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::queryReport01a($p);
            $quary = Report::queryReport01a($p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }
    
    public function report04(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            if (count($request->branches) > 0) 
            {
                $branches_term = [];
                foreach ($request->branches as $key => $value) 
                {
                    $branches_term[] =  $value['id'];
                }
                $request->scope = $branches_term;
            }
            //dd($request->scope);
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;//dd($p);
            $query = Report::queryReport04($p);
            $quary = Report::queryReport04($p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }

    public function report28(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::queryReport28($p);
            $quary = Report::queryReport28($p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }

    public function report29(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::queryReport29($p);
            $quary = Report::queryReport29($p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }

    public function reportForm02(Request $request)
    {
        $response = new Response();
        $branch_ids = $request->branches;
        $product_ids = $request->products;
        $date = $request->date;
        $start_date = $request->start;
        if (empty($branch_ids)) 
        {
            $branch_ids = u::getBranchIds($request->users_data);
        }
        else
        {
            $branch_ids_term = [];
            foreach ($branch_ids as $key => $value) 
            {
                array_push($branch_ids_term, $value['id']);
            }
            $branch_ids = $branch_ids_term;
        }
        $branch_ids_string = implode(',', $branch_ids);
        $product_condition = '';
        if (!empty($product_ids)) {
            $product_ids_string = implode(',', $product_ids);
            $product_condition = " AND c.`product_id` IN ($product_ids_string)";
        }
        if (!$date) {
            $date = date('Y-m-d 23:59:59');
        }else{
            $date = date('Y-m-d', strtotime($date)) . " 23:59:59";
        }
        if (!$start_date) {
            $start_date = date('Y-m-01 00:00:00');
        }else{
            $start_date = date('Y-m-d', strtotime($start_date)) . " 00:00:00";
        }
//        echo $start_date;
//        echo $date;
        $query = "
            (
                SELECT
                    COUNT(*) AS count_student, t.branch_id AS branch_id, t.student_type
                FROM (
                    SELECT
                        s.id AS student_id,
                        t.type AS student_type,
                        s.`branch_id` AS branch_id
                    FROM
                        students AS s
                        LEFT JOIN contracts AS c ON s.id = c.`student_id`
                        LEFT JOIN enrolments AS e ON c.id = e.`contract_id`
                        LEFT JOIN (
                            SELECT -1 AS `type`
                            UNION SELECT -2 AS `type`
                        ) AS t ON s.id <> t.type
                    WHERE
                        s.`created_at` <= '$date' AND s.created_at >= '$start_date'
                        AND s.`branch_id` IN ($branch_ids_string) AND c.`branch_id` = s.`branch_id`
                        AND c.`id` IN (
                            SELECT c.id
                            FROM
                            contracts AS c
                            LEFT JOIN enrolments AS e ON c.id = e.contract_id
                            LEFT JOIN (
                                SELECT student_id, MAX(count_recharge) AS count_recharge, MAX(end_date) AS end_date
                                FROM contracts AS c
                                WHERE c.`branch_id` IN ($branch_ids_string) GROUP BY c.`student_id`
                            ) AS t ON c.`student_id` = t.student_id AND c.`count_recharge` = t.count_recharge AND c.end_date = t.end_date
                            WHERE t.student_id IS NOT NULL
                        )
                        AND (e.id IS NULL OR (e.id IS NOT NULL AND e.`status` > 0))
                        AND
                        (
                            (c.`debt_amount` = 0 AND c.`type` > 0 AND t.type = -1)
                            OR
                            (c.type = 0 AND t.type = -2)
                        )
                        $product_condition
                    GROUP BY s.id
                ) AS t
                    LEFT JOIN branches AS b ON t.branch_id = b.`id`
                GROUP BY t.branch_id, t.student_type
            )
            UNION

            (
                SELECT
                    COUNT(*) AS count_student,
                    s.`branch_id` AS branch_id,
                    -3 AS student_type
                FROM
                    students AS s
                    LEFT JOIN contracts AS c ON s.`id` = c.`student_id`
                    LEFT JOIN (
                        SELECT MIN(created_at) AS created_at, contract_id FROM payment GROUP BY contract_id
                    ) AS p ON c.`id` = p.`contract_id`
                WHERE
                    s.`branch_id` IN ($branch_ids_string)
                    AND s.`created_at` <= '$date' AND s.created_at >= '$start_date'
                    AND s.`branch_id` = c.`branch_id`
                    AND s.checked=1
                    $product_condition
                GROUP BY s.`branch_id`
            )
            UNION

            (
                SELECT
                    COUNT(*) AS count_student,
                    t1.branch_id AS branch_id,
                    -4 AS student_type
                FROM
                (
                    SELECT
                        s.id AS id,
                        s.`branch_id` AS branch_id
                    FROM
                        students AS s
                        LEFT JOIN contracts AS c ON s.`id` = c.`student_id`
                        LEFT JOIN (
                            SELECT MIN(created_at) AS created_at, contract_id FROM payment GROUP BY contract_id
                        ) AS p ON c.`id` = p.`contract_id`
                    WHERE
                        s.`branch_id` IN ($branch_ids_string)
                        AND s.`created_at` <= '$date' AND s.created_at >= '$start_date'
                        AND s.`branch_id` = c.`branch_id`
                        AND c.`count_recharge` = 0
                        AND (p.created_at <= '$date' AND p.created_at >= '$start_date')
                        $product_condition
                    GROUP BY s.`id`
                ) AS t1
                LEFT JOIN
                (
                    SELECT
                        s.id AS id,
                        s.`branch_id` AS branch_id
                    FROM
                        students AS s
                        LEFT JOIN contracts AS c ON s.id = c.`student_id`
                    WHERE
                        s.`branch_id` IN ($branch_ids_string)
                        AND s.`created_at` <= '$date' AND s.created_at >= '$start_date'
                        AND s.`branch_id` = c.`branch_id`
                        AND c.`count_recharge` = -1
                        $product_condition
                    GROUP BY s.id
                ) AS t2 ON t1.id = t2.id AND t1.branch_id = t2.branch_id
                WHERE t2.id IS NOT NULL
                GROUP BY t1.branch_id
            )
        ";

//        echo $query; die;

        $res = DB::select(DB::raw($query));


//        $branches = Branch::where('id', 'IN', "($branch_ids_string)");
//        var_dump($branches);die;
        $query = "SELECT id, `name` FROM branches WHERE id IN ($branch_ids_string)";
        $branches = DB::select(DB::raw($query));
//        var_dump($branches);die;

        $data = [];
        if(!empty($branches)){
            $rows = [];
            if(!empty($res)){
                foreach ($res as $re){
                    if(!isset($rows[$re->branch_id])){
                        $rows[$re->branch_id] = [];
                    }
                    $rows[$re->branch_id]["type".(0 - $re->student_type)] = $re->count_student;
                }
            }

            foreach ($branches as $branch){
                if(!isset($data[$branch->id])){
                    $data[$branch->id] = [
                        "name" => $branch->name,
                        "student_types" => []
                    ];
                }

                if(isset($rows[$branch->id])){
                    for($i=1; $i < 5; $i++){
                        if(isset($rows[$branch->id]["type$i"])){
                            $data[$branch->id]["student_types"]["type$i"] = $rows[$branch->id]["type$i"];
                        }else{
                            $data[$branch->id]["student_types"]["type$i"] = 0;
                        }
                    }
                }else{
                    $data[$branch->id]['student_types'] = [
                        "type1" => 0,
                        "type2" => 0,
                        "type3" => 0,
                        "type4" => 0,
                    ];
                }

            }
        }

        $result = array_values($data);

//        echo json_encode($rows);die;

//        $data = [];
//        if (!empty($res)) {
//            foreach ($res as $re) {
//                if (isset($data[$re->branch_id])) {
//                    $data[$re->branch_id]->student_types["type" . (0 - $re->student_type)] = (Object)[
//                        "count_student" => $re->count_student
//                    ];
//                } else {
//                    $data[$re->branch_id] = (Object)[
//                        "id" => $re->branch_id,
//                        "name" => $re->branch_name,
//                        "student_types" => [
//                            "type" . (0 - $re->student_type) => (Object)[
//                                "count_student" => $re->count_student
//                            ]
//                        ]
//                    ];
//                }
//            }
//        }

        $code = APICode::SUCCESS;

        return $response->formatResponse($code, $result);
    }

    public function reportForm03(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::reportForm03($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    public function reportForm04(Request $request)
    {

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));
        // dd($request->all());
        $branch_ids = $request->branches;

        $from_date = $request->fromDate;
        $fromDate = strtotime($from_date);
        if (!$fromDate) {
            $from_date =  $today;
        }

        // dd($from_date);
        $where = "";

        if ($branch_ids) {
            $branch_ids_string = implode(',', $branch_ids);
            $where .= " Where  branches.id IN($branch_ids_string)";
        }
        $query = "SELECT `name` branch_name,

        (SELECT COUNT(id)
         FROM classes
         WHERE classes.brch_id = branches.brch_id
           AND cls_startdate <= ' $from_date'
           AND cls_enddate >= ' $from_date'
           AND cls_iscancelled = 'no' ) AS total_classes,

        (SELECT COUNT(u.id)
         FROM term_user_branch t
         LEFT JOIN users u ON t.user_id = u.id
         WHERE t.role_id = 36
           AND u.STATUS > 0
           AND u.start_date <= '$from_date'
           AND (u.end_date >= '$from_date'
                OR u.end_date IS NULL)
           AND t.branch_id = branches.id ) AS total_teachers,

        (SELECT COUNT(DISTINCT (c.student_id))
         FROM contracts c
         LEFT JOIN enrolments e ON c.enrolment_id = e.id
         LEFT JOIN pendings p ON p.contract_id = c.id
         WHERE c.debt_amount = 0
           AND c.type > 0
           AND c.branch_id = branches.id
           AND ( (DATE(c.updated_at) <= '$from_date'
                  AND DATE(c.end_date) >= '$from_date'
                  AND (c.type <> 5
                       OR c.status = 6))
                OR (DATE(c.start_date) <= '$from_date'
                    AND c.type = 5)
                OR (DATE(c.created_at) <= '$from_date'
                    AND c.status = 6))
           AND c.total_charged > 0
           AND c.debt_amount = 0
           AND IF (c.enrolment_id,
                   e.status > 0,
                   TRUE) ) AS total_full_fee
      FROM branches $where;";
        // return $query;

        $branches = DB::select(DB::raw($query));
        // dd($branches);
        return response()->json($branches);

    }

    public function reportForm05(Request $request)
    {
        $branch_ids = $request->branches;
        $products = $request->products;
        $programs = $request->programs;

        $product_ids = [];
        foreach ($products as $product) {
            $product_ids[] = $product['id'];
        }

        $program_ids = [];
        foreach ($programs as $program) {
            $program_ids[] = $program['id'];
        }

        $where = "";

        if ($branch_ids) {
            $branch_ids_string = implode(',', $branch_ids);
            $where .= " AND ct.branch_id in ($branch_ids_string)";
        }

        if ($product_ids) {
            $product_ids_string = implode(',', $product_ids);
            $where .= " AND ct.branch_id in ($product_ids_string)";
        }

        if ($program_ids) {
            $program_ids_string = implode(',', $program_ids);
            $where .= " AND ct.branch_id in ($program_ids_string)";
        }

        dd($where);
    }

    public function reportForm06(Request $request)
    {
        $res = Report::reportForm06($request);
        return response()->json($res);
    }

    public function reportForm07(Request $request)
    {
        // dd($request->all());

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));


        $branch_ids = $request->branch_ids;
        $product_ids = $request->product_ids;
        $program_ids = $request->program_ids;

        $where = "";
        if ($branch_ids) {
            $branch_ids_string = implode(',', $branch_ids);
            $where .= " AND b.id in( $branch_ids_string ) ";
        }
        if ($product_ids) {
            $product_ids_string = implode(',', $product_ids);
            $where .= " AND pr.id in ( $product_ids_string ) ";
        }
        if ($program_ids) {
            $program_ids_string = implode(',', $program_ids);
            $where .= " AND pg.id in ( $program_ids_string ) ";
        }

        $from_date = $request->from_date;
        $to_date = $request->to_tate;

        $fromDate = strtotime($from_date);
        $toDate = strtotime($to_date);

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = date('Y-m-t',strtotime('today'));
        }
        $q = "SELECT s.id AS student_id,
        s.stu_id AS lms_code,
        s.name AS name_student,
        t.rating_date,
        r.name AS rank_name,
        pr.name AS product_name,
        pg.name AS program_name,
        b.name AS branch_name,
        cls.cls_name AS class_name,
        t.comment AS comment,
        u.username AS creator_name,
        u1.username AS ec_name,
        u2.username AS cm_name,
        te.ins_name AS teacher_name
      FROM term_student_rank AS t
      LEFT JOIN classes AS cls ON t.class_id = cls.id
      LEFT JOIN students AS s ON t.student_id = s.id
      LEFT JOIN term_student_user AS t1 ON t1.student_id = s.id
      LEFT JOIN users AS u1 ON t1.ec_id = u1.id
      LEFT JOIN users AS u2 ON t1.cm_id = u2.id
      LEFT JOIN ranks AS r ON t.rank_id = r.id
      LEFT JOIN users AS u ON u.id = t.creator_id
      LEFT JOIN contracts AS c ON c.student_id = s.id
      LEFT JOIN products AS pr ON pr.id = c.product_id
      LEFT JOIN programs AS pg ON pg.id = c.program_id
      LEFT JOIN sessions AS se ON se.class_id = t.class_id
      LEFT JOIN teachers AS te ON se.teacher_id = te.id
      LEFT JOIN branches AS b ON b.id = c.branch_id
      LEFT JOIN term_user_class AS term_class ON term_class.class_id = t.class_id
      LEFT JOIN term_user_branch AS term_branch ON term_branch.user_id = term_class.user_id
      LEFT JOIN users AS user_teacher ON user_teacher.id = term_branch.user_id
";
        // return $q;
        $students = DB::select(DB::raw($q));
        return response()->json($students);
    }

    public function reportForm08(Request $request)
    {
        // dd($request->all());

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));


        $branch_ids = $request->branches;

        $products = $request->products;
        $programs = $request->programs;

        $product_ids = [];
        $program_ids = [];
        foreach ($products as $product) {
            $product_ids[] = $product['id'];
        }
        foreach ($programs as $program) {
            $program_ids[] = $program['id'];
        }

        $where = "";
        if ($branch_ids) {
            $branch_ids_string = implode(',', $branch_ids);
            $where .= " AND br.id in( $branch_ids_string ) ";
        }
        if ($product_ids) {
            $product_ids_string = implode(',', $product_ids);
            $where .= " AND pd.id in ( $product_ids_string ) ";
        }
        if ($program_ids) {
            $program_ids_string = implode(',', $program_ids);
            $where .= " AND pr.id in ( $program_ids_string ) ";
        }

        $from_date = $request->fromDate;
        $to_date = $request->toDate;

        $fromDate = strtotime($from_date);
        $toDate = strtotime($to_date);

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = $today;
        }
        $where .= " AND (ct.created_at BETWEEN '$from_date' AND '$to_date') ";
        Report::commons($request);
        $strLimit = Report::$strLimit;    
        $q = "SELECT s.id,
                    s.accounting_id as effect_id,
                    s.stu_id AS lms_id,
                    s.name,
                    br.name AS branch_name,
                    pr.name AS program_name,
                    pd.name AS product_name,
                    cl.cls_name AS class_name,
                    ec.username AS ec_name,
                    cm.username AS cm_name,
                    ts.rating_date as rating_date,
                    rating_creator.username as rating_creator_name,
                    rk.name as rank_name
                FROM students AS s
                    LEFT JOIN contracts AS ct ON ct.student_id = s.id
                    LEFT JOIN programs AS pr ON ct.program_id = pr.id
                    LEFT JOIN products AS pd ON ct.product_id = pd.id
                    LEFT JOIN branches AS br ON br.id = ct.branch_id
                    LEFT JOIN classes AS cl ON cl.program_id = pr.id
                    LEFT JOIN users AS ec ON ec.id = ct.ec_id
                    LEFT JOIN users AS cm ON cm.id = ct.cm_id
                    LEFT JOIN term_student_rank AS ts ON ts.student_id = s.id
                    LEFT JOIN ranks AS rk ON rk.id = ts.rank_id
                    LEFT JOIN users AS rating_creator ON rating_creator.id = ts.creator_id
                WHERE s.id > 0 $where GROUP BY s.id $strLimit
            ";  
        $queryCount = "SELECT count(1) AS total 
                FROM students AS s
                    LEFT JOIN contracts AS ct ON ct.student_id = s.id
                    LEFT JOIN programs AS pr ON ct.program_id = pr.id
                    LEFT JOIN products AS pd ON ct.product_id = pd.id
                    LEFT JOIN branches AS br ON br.id = ct.branch_id
                    LEFT JOIN classes AS cl ON cl.program_id = pr.id
                    LEFT JOIN users AS ec ON ec.id = ct.ec_id
                    LEFT JOIN users AS cm ON cm.id = ct.cm_id
                    LEFT JOIN term_student_rank AS ts ON ts.student_id = s.id
                    LEFT JOIN ranks AS rk ON rk.id = ts.rank_id
                    LEFT JOIN users AS rating_creator ON rating_creator.id = ts.creator_id
                WHERE s.id > 0 $where
                    GROUP BY s.id";
        $total  = count(DB::select(DB::raw($queryCount)));
        $totalRecord = isset($total) ? (int)$total : 0;
        $students = DB::select(DB::raw($q));
        $return = array(
            'data' => $students ? $students : null,
            'total_record' => $totalRecord
        );
        return $return;

    }

    public function reportForm09(Request $request)
    {
        $res = Report::reportForm09($request);
        return response()->json($res);
    }

    public function reportForm09_need_to_look(Request $request)
    {
        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));
        // dd($request->all());
        $branch_ids = $request->branches;
        $products = $request->products;
        $programs = $request->programs;
        $from_date = $request->fromDate;
        $to_date = $request->toDate;

        $fromDate = strtotime($from_date);
        $toDate = strtotime($to_date);

        $product_ids = [];
        $program_ids = [];
        foreach ($products as $product) {
            $product_ids[] = $product['id'];
        }
        foreach ($programs as $program) {
            $program_ids[] = $program['id'];
        }

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = $today;
        }


        $where = "";

        if ($branch_ids) {
            $branch_ids_string = implode(',', $branch_ids);
            $where .= " AND c.branch_id in ($branch_ids_string)";
        }
        if ($product_ids) {
            $product_ids_string = implode(',', $product_ids);
            $where .= " AND c.product_id in ($product_ids_string)";
        }
        if ($program_ids) {
            $program_ids_string = implode(',', $program_ids);
            $where .= " AND c.program_id in ($program_ids_string)";
        }
        $where .= " AND (c.created_at BETWEEN '$from_date' AND '$to_date')";


        $q = "SELECT
            s.id student_id,
            s.name student_name,
            s.type student_type,
            s.effect_id,
            s.stu_id lms_id,
            if(s.type=1, 'VIP', 'Bình thường') customer_type,
            (SELECT `name` FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_name,
            (SELECT `name` FROM users WHERE id = c.ec_id) ec_name,
            (SELECT `name` FROM users WHERE id = c.cm_id) cm_name,
            (SELECT `name` FROM products WHERE id = c.product_id) product_name,
        (SELECT `name` FROM programs WHERE id = c.program_id) program_name,
            (SELECT `name` FROM branches WHERE id = c.branch_id) branch_name,
            c.real_sessions remain_sessions,
            c.must_charge,
            c.debt_amount,
            c.total_charged
        FROM contracts c
            LEFT JOIN students s ON c.student_id = s.id
        WHERE  c.count_recharge >= 0  $where GROUP BY s.id";

        return $q;
        $students = DB::select(DB::raw($q));

        return response()->json($students);
    }

    public function reportForm10(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::reportForm10($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    public function reportForm11(Request $request)
    {

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));
        // dd($request->all());
        $branch_ids = $request->branches;
        $products = $request->products;
        $programs = $request->programs;
        $customers = $request->customerType;
        // dd($customer_type);
        $from_date = $request->fromDate;
        $to_date = $request->toDate;
        $product_ids = [];
        $program_ids = [];
        $customer_ids = [];
        // dd($from_date);

        $fromDate = strtotime($from_date);
        $toDate = strtotime($to_date);

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = $today;
        }

        Report::commons($request);
        $strLimit = Report::$strLimit;    

        $where = "";

        if ($branch_ids) {
            $branch_ids_string = implode(',', $branch_ids);
            $where .= " AND c.branch_id in ($branch_ids_string)";
        }
        if ($products) {
            $product_ids_string = implode(',', $products);
            $where .= " AND c.product_id in ($product_ids_string)";
        }
        if ($programs) {
            $program_ids_string = implode(',', $programs);
            $where .= " AND c.program_id in ($program_ids_string)";
        }
        if ($customers) {
            $customer_ids_string = implode(',', $customers);
            $where .= " AND c.type in ($customer_ids_string)";
        }
        $where .= " AND (c.created_at BETWEEN '$from_date' AND '$to_date')";

        $q = "SELECT
                s.id student_id,
                s.name student_name,
                s.type student_type,
                s.accounting_id as effect_id,
                s.stu_id lms_id,
                if(s.type=1, 'VIP', 'Bình thường') customer_type,
                (SELECT `name` FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_name,
                (SELECT `name` FROM users WHERE id = c.ec_id) ec_name,
                (SELECT `name` FROM users WHERE id = c.cm_id) cm_name,
                (SELECT `name` FROM products WHERE id = c.product_id) product_name,
            (SELECT `name` FROM programs WHERE id = c.program_id) program_name,
                (SELECT `name` FROM branches WHERE id = c.branch_id) branch_name,
                c.real_sessions remain_sessions,
                c.must_charge,
                c.debt_amount,
                c.total_charged
            FROM contracts c
                LEFT JOIN students s ON c.student_id = s.id
            WHERE  c.count_recharge >= 0  $where GROUP BY s.id $strLimit";
            //echo $q; exit();
            $queryCount = "select count(1) as total
            FROM contracts c
                LEFT JOIN students s ON c.student_id = s.id
            WHERE  c.count_recharge >= 0  $where GROUP BY s.id";//echo $queryCount; exit();
        $total  = count(DB::select(DB::raw($queryCount)));
        $totalRecord = isset($total) ? (int)$total : 0;
        $students = DB::select(DB::raw($q));
        $return = array(
            'data' => $students ? $students : null,
            'total_record' => $totalRecord
        );
        return $return;
    }

    public function reportForm12(Request $request)
    {

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));
        // dd($request->all());
        $branch_ids = $request->branches;
        $products = $request->products;
        $programs = $request->programs;
        $customers = $request->customerType;
        // dd($customer_type);
        $from_date = $request->fromDate;
        $to_date = $request->toDate;
        $product_ids = [];
        $program_ids = [];
        $customer_ids = [];
        // dd($from_date);

        $fromDate = strtotime($from_date);
        $toDate = strtotime($to_date);

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = $today;
        }
        Report::commons($request);
        $strLimit = Report::$strLimit; 

        $where = "";

        if ($branch_ids) {
            $branch_ids_string = implode(',', $branch_ids);
            $where .= " AND c.branch_id in ($branch_ids_string)";
        }
        if ($products) {
            $product_ids_string = implode(',', $products);
            $where .= " AND c.product_id in ($product_ids_string)";
        }
        if ($programs) {
            $program_ids_string = implode(',', $programs);
            $where .= " AND c.program_id in ($program_ids_string)";
        }
        if ($customers) {
            $customer_ids_string = implode(',', $customers);
            $where .= " AND c.type in ($customer_ids_string)";
        }
        $where .= " AND (c.created_at BETWEEN '$from_date' AND '$to_date')";

        $q = "SELECT
                s.id student_id,
                s.name student_name,
                s.type student_type,
                s.accounting_id as effect_id,
                s.stu_id lms_id,
                if(s.type=1, 'VIP', 'Bình thường') customer_type,
                (SELECT `name` FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_name,
                (SELECT `name` FROM users WHERE id = c.ec_id) ec_name,
                (SELECT `name` FROM users WHERE id = c.cm_id) cm_name,
                (SELECT `name` FROM products WHERE id = c.product_id) product_name,
            (SELECT `name` FROM programs WHERE id = c.program_id) program_name,
                (SELECT `name` FROM branches WHERE id = c.branch_id) branch_name,
                c.real_sessions remain_sessions,
                c.must_charge,
                c.debt_amount,
                c.total_charged
            FROM contracts c
                LEFT JOIN students s ON c.student_id = s.id
            WHERE  c.count_recharge >= 0  $where GROUP BY s.id $strLimit";
        //return $q;
         $queryCount = "select count(1) as total
            FROM contracts c
                LEFT JOIN students s ON c.student_id = s.id
            WHERE  c.count_recharge >= 0  $where GROUP BY s.id";//echo $queryCount; exit();
        $total  = count(DB::select(DB::raw($queryCount)));
        $totalRecord = isset($total) ? (int)$total : 0;
        $students = DB::select(DB::raw($q));
        $return = array(
            'data' => $students ? $students : null,
            'total_record' => $totalRecord
        );
        return $return;       
    }

    public function reportForm13(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::reportForm13($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    public function reportForm14(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = [
            'code'   => $code,
            'data'   => array()
        ];
        $data = Report::reportForm14($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        } else {
            $responseData['code'] = APICode::PERMISSION_DENIED;
        }
        return response()->json($responseData);
    }

    public function reportForm15(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = [
            'code'   => $code,
            'data'   => array()
        ];
        $data = Report::reportForm15($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']= $data['data'];
            $responseData['total'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    public function reportForm17a(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = $this->params($request, $session);
            if(isset($request->from) && $request->from){
                $date = '';

                try{
                    $date = strtotime($request->from);
                }catch (\Exception $exception){
                    $date = strtotime(date('Y-m-d'));
                }

                $p->f = date('Y-m-01', $date);
                $p->t = date('Y-m-t', $date);
            }
            $code = APICode::SUCCESS;
            $query = Report::queryReport17a($p);
            $quary = Report::queryReport17a($p, 1);
            $data = $this->making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }

    public function reportForm17b(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = $this->params($request, $session);

            if(isset($request->sort)){
                $p->order = $request->sort;
            }

            if(isset($request->from) && $request->from){
                $date = '';

                try{
                    $date = strtotime($request->from);
                }catch (\Exception $exception){
                    $date = strtotime(date('Y-m-d'));
                }

                $p->f = date('Y-m-01', $date);
                $p->t = date('Y-m-t', $date);
            }

            $code = APICode::SUCCESS;
            $query = Report::queryReport17b($p);
            $quary = Report::queryReport17b($p, 1);
            $data = $this->making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }

    public function reportForm17c(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = $this->params($request, $session);

            if(isset($request->sort)){
                $p->order = $request->sort;
            }

            if(isset($request->from) && $request->from){
                $date = '';

                try{
                    $date = strtotime($request->from);
                }catch (\Exception $exception){
                    $date = strtotime(date('Y-m-d'));
                }

                $p->f = date('Y-m-01', $date);
                $p->t = date('Y-m-t', $date);
            }

            $code = APICode::SUCCESS;
            $query = Report::queryReport17c($p);
            $quary = Report::queryReport17c($p, 1);
            $data = $this->making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }

    public function reportForm17d(Request $request){
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = $this->params($request, $session);

            if(isset($request->sort)){
                $p->order = $request->sort;
            }

            if(isset($request->from) && $request->from){
                $date = '';

                try{
                    $date = strtotime($request->from);
                }catch (\Exception $exception){
                    $date = strtotime(date('Y-m-d'));
                }

                $p->f = date('Y-m-01', $date);
                $p->t = date('Y-m-t', $date);
            }

            $code = APICode::SUCCESS;
            $query = Report::queryReport17d($p);
            $quary = Report::queryReport17d($p, 1);
            $data = $this->making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }
    /**
    * Report Form18
    */
    public function reportForm18(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::reportForm18($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }

        return response()->json($responseData);
    }

    public function reportForm19(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = [
            'code'   => $code,
            'data'   => array()
        ];

        $data = Report::reportForm19($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }

        return response()->json($responseData);
    }

    public function reserves(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::reserves($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    public function registers(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::registers($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    public function tuitionTransfers(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::tuitionTransfers($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    public function renewals(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::renewals($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    public function branchTransfers(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::branchTransfers($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    public function students(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::students($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    //Report 09
    public function studentWithdraw(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::studentWithdraw($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }
    /**
    * Get data report form 20
    */
    public function getBranchesInfo(Request $request,$type){
        // dd($request->all());
        $data = Report::branchesInfo($request,$type);
        return response()->json($this->responseData($data));
    }
    /**
    * form response data
    */
    private function responseData(array $data){
        $responseData = [
            'code'   => APICode::SUCCESS,
            'data'   => [
                'code' => APICode::SUCCESS,
                'list' => isset($data['data']) ? $data['data'] : [],
                'total_record' => isset($data['total_record']) ? $data['total_record'] : 0
            ]
        ];
        return $responseData;
    }

    public function form30(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::form30($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    public function form31(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::reportForm31($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }

    public function form32(Request $request)
    {
        $code = APICode::SUCCESS;
        $responseData = array(
            'code'   => $code,
            'data'   => array()
        );
        $data = Report::reportForm32($request);
        if ($data) {
            $responseData['code'] = $code;
            $responseData['data']['code'] = $code;
            $responseData['data']['list'] = $data['data'];
            $responseData['data']['total_record'] = $data['total_record'];
        }
        return response()->json($responseData);
    }
    public function report33(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::queryReport33($p);
            $quary = Report::queryReport33($p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }
    public function report34(Request $request) {
        $date = $request->date;
        if(!$date){
            $date = date('Y-m-d');
        }
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::queryReport34($p,$date);
            $quary = Report::queryReport34($p,$date, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }
    public function report34b2(Request $request) {
        $date = $request->date;
        if(!$date){
            $date = date('Y-m-d');
        }
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::queryReport34b2($p,$date);
            $quary = Report::queryReport34b2($p,$date, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }

    public function studentActive(Request $request){

        $data = StudentReportService::getStudentActiveReportByDate($request->all(), true);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    public function exportStudentActiveByDate(Request $request, StudentReportService $service)
    {
        $template = new  \App\Templates\Exports\StudentActive;
        $params = $request->all();
        $list = false;
        if (!empty($params["list"])){
            $template = new  \App\Templates\Exports\StudentActiveList;
            $list = true;
        }

        $service->exportStudentByDate1($params, $template, $list);
    }

    public function tuitionFee(Request $request){

        $data = TuitionFeeService::getTuitionFeeReportByDate($request->all());
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    public function exportTuitionFeeByDate(Request $request, TuitionFeeService $service, \App\Templates\Exports\TuitionFee $template)
    {
        $params = $request->all();
        $data = $service->getTuitionFeeReportByDate($params);
        $service->exportByDate($params, $template, $data);
    }

    public function studentHistory(Request $request){

        $data = StudentHistoryService::getStudentHistoryReportByDate($request->all());
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    public function studentHistoryByDate(Request $request, StudentHistoryService $service, \App\Templates\Exports\StudentHistory $template)
    {
        $params = $request->all();
        $data = $service->getStudentHistoryReportByDate($params);
        $service->exportByDate($params, $template, $data);
    }

    public function percentage(Request $request, $export = false){
        $listDays = [];
        if (!empty($request->start_date)){
            $listDays = u::getDaysWeekInOnceMonth($request->start_date,$request->end_date);
        }
        $week = sizeof($listDays);
        $month = $week +1;
        array_push($listDays,["from"=>$request->start_date,"to"=>$request->end_date]);
        $data = TuitionFeeService::getTuitionPercentageReportByDate($request->all(), $listDays, $week ,$month);
        if ($export)
            return $data;
        $response = new Response();
        return $response->formatResponseForce(APICode::SUCCESS, $data);
    }

    public function exportPercentage(Request $request, TuitionFeeService $service, \App\Templates\Exports\TuitionPercentage $template)
    {
        $params = $request->all();
        $data = self::percentage($request, true);
        $service->exportPercentageByDate($params, $template, $data);
    }

    public function semester(Request $request, $export = false){
        $listDays = [];
        if (!empty($request->start_date)){
            $listDays = u::getDaysWeekInOnceMonth($request->start_date,$request->end_date);
        }
        $week = sizeof($listDays);
        $month = $week +1;
        array_push($listDays,["from"=>$request->start_date,"to"=>$request->end_date]);
        $data = SemesterService::getTuitionPercentageReportByDate($request->all(), $listDays, $week ,$month);
        if ($export)
            return $data;
        $response = new Response();
        return $response->formatResponseForce(APICode::SUCCESS, $data);
    }

    public function exportSemester(Request $request, SemesterService $service, \App\Templates\Exports\SemesterPercentage $template)
    {
        $params = $request->all();
        $data = self::semester($request, true);
        $service->exportPercentageByDate($params, $template, $data);
    }

    public function studentRenewals(Request $request, $export = false){
        $data = RenewalsService::getReportByDate($request->all(), $export);
        if ($export)
            return $data;
        $response = new Response();
        return $response->formatResponseForce(APICode::SUCCESS, $data);
    }

    public function exportRenewals(Request $request, RenewalsService $service, \App\Templates\Exports\StudentRenewals $template)
    {
        $params = $request->all();
        $data = self::studentRenewals($request, true);
        $service->exportRenewalsByDate($params, $template, $data);
    }

    public function export28(Request $request, RenewalsService $service, \App\Templates\Exports\Report28 $template) {
        $data = null;
        $params = $request->all();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $query = Report::queryReport28($p, 0, true);
            $query .= " ORDER BY c.branch_id ASC";
            $data = u::query($query);
        }

        $dataNew = [];
        foreach ($data as $item){
            $item->contract_type_name = u::getContractTypeName($item->customer_type);
            $dataNew[] = $item;
        }

        $service->exportReport28ByDate($params, $template, $dataNew);
    }

    public function studentQuantityReport(Request $request, $export = false, $listExport = false){
        $today = date('Y-m-d');
        $listDays = [];
        $list = (!empty($request->list)) ? true : false;

        if ($listExport)
            $list  = $listExport;

        if (!empty($request->start_date)){
            if ($today >=  $request->start_date && $today <= $request->end_date){
                $listDays = u::getDaysWeekInOnceMonth($request->start_date, $today);
            }
            else
                $listDays = u::getDaysWeekInOnceMonth($request->start_date, $request->end_date);
        }
        $week = sizeof($listDays);
        $month = $week +1;
        array_push($listDays,["from"=>$request->start_date,"to"=>$request->end_date]);
        $data = StudentQuantity::getDataReportByDate($request->all(), $listDays, $week ,$month, $list);

        if ($export)
            return $data;
        $response = new Response();
        return $response->formatResponseForce(APICode::SUCCESS, $data);
    }

    public function studentQuantityExport(Request $request, StudentQuantity $service, \App\Templates\Exports\StudentQuantity $template)
    {
        $list = (!empty($request->list)) ? true : false;
        $params = $request->all();
        $data = self::studentQuantityReport($request, true, $list);

        if ($list)
            $template = new \App\Templates\Exports\StudentQuantityList;

        $service->exportDataByDate($params, $template, $data, $list);
    }

    public function studentWithdrawNew(Request $request, $export = false){
        $data = StudentWithdrawService::getReportByDate($request->all(), $export);
        if ($export)
            return $data;
        $response = new Response();
        return $response->formatResponseForce(APICode::SUCCESS, $data);
    }

    public function studentWithdrawExport(Request $request, StudentWithdrawService $service, \App\Templates\Exports\StudentWithdraw $template){
        $data = self::studentWithdrawNew($request, true);
        $service->exportDataByDate($request->all(), $template, $data);
    }

    public function studentListExport(Request $request, StudentReportService $service, \App\Templates\Exports\StudentList $template)
    {
        $params = $request->all();
        $service->exportStudentList($params, $template, $request->users_data);
    }

    public function studentTrialExport(Request $request, StudentReportService $service, \App\Templates\Exports\StudentTrialList $template)
    {
        $params = $request->all();
        $service->exportTrialList($params, $template, $request->users_data);
    }

    public function studentActiveReport(Request $request, StudentReportService $service, \App\Templates\Exports\StudentTrialList $template)
    {
        $params = $request->all();
        $service->exportStudentActiveList($params, $template, $request->users_data);
    }
    public function studentPendingReport(Request $request, StudentReportService $service, \App\Templates\Exports\StudentTrialList $template)
    {
        $params = $request->all();
        $service->exportStudentPendingList($params, $template, $request->users_data);
    }
    public function report01a1(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $birthday_mode = $request->birthday_mode;
            $p = Report::params($request, $session);
            $cms = isset($request->cms) ? implode(',', $request->cms) : null;
            $p->c = $cms;
            $code = APICode::SUCCESS;
            $query = Report::queryReport01a1($birthday_mode, $p);
            $quary = Report::queryReport01a1($birthday_mode, $p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
            $list =$data->list;
            foreach($list AS $k=>$row){
                $list[$k]->done_session = isset($row->done_session) ? $row->done_session : self::getDoneSessions($row);
            }
            $data->list =$list;
        }

        return $response->formatResponse($code, $data);
    }
    public function getDoneSessions($data){
        $tmp_holiDay = u::getPublicHolidays(0, $data->branch_id, $data->product_id);
        $reserved_dates = JobsController::getReservedDates_transfer($data->contract_id);
        if (!empty($reserved_dates)) {
          $holiDay = array_merge($tmp_holiDay, $reserved_dates);
        }else{
          $holiDay = $tmp_holiDay;
        }
        $class_days = u::getClassDays($data->class_id);
        $result = u::calculatorSessions($data->enrolment_start_date, date('Y-m-d'), $holiDay, $class_days);
        return $result ? $result->total : NULL;
    }
    public function collectFullFeeActive(Request $request, $month, $branch)
    {
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();

        if($month && $month!="_"){
            $report_month = (int)$month > 9 ? date("Y").'-'.(int)$month : date("Y").'-0'.(int)$month;
        }else{
            $report_month = date('Y-m',time()-7*3600);
        }
        $start_date = date('Y-m-01',strtotime($report_month.'-01'));
        if(date('Y-m',strtotime($report_month.'-01')) == date('Y-m')){
            $end_date = date('Y-m-d');
        }else{
            $end_date = date('Y-m-t',strtotime($report_month.'-01'));
        }
        $where = "";
        $where_del = "";
        if (count(explode(',', $branch)) > 0 && $branch != '_') {
            $where = " AND c.branch_id IN ($branch)";
            $where_del = " AND branch_id IN ($branch)";
        }
        u::query("DELETE FROM report_full_fee_active WHERE report_month = '$report_month' $where_del ");
        $list = u::query("SELECT DISTINCT s.id student_id,
            c.id contract_id,
            s.branch_id,
            IF((c.class_id !=0 AND c.class_id IS NOT NULL),c.class_id,(SELECT class_id FROM contracts WHERE student_id=s.id AND `status` !=7 AND `class_id` IS NOT NULL LIMIT 1)) AS class_id,
            t.cm_id,
            c.product_id,
            '$report_month' report_month,
            IF(c.enrolment_last_date IS NOT NULL,c.enrolment_last_date,c.end_date) AS last_date,
            (SELECT `enrolment_start_date` FROM `log_contracts_history` WHERE student_id = c.student_id AND enrolment_start_date IS NOT NULL AND `type` <> 0 ORDER BY id LIMIT 1) AS date_start,
            c.summary_sessions,c.enrolment_start_date
        FROM
            contracts c
            LEFT JOIN students s ON c.student_id = s.id
            LEFT JOIN classes cls ON c.class_id = cls.id
            LEFT JOIN term_student_user AS t ON t.student_id = c.student_id
            LEFT JOIN users u ON t.cm_id = u.id
        WHERE
            c.type > 0
            AND c.`status` < 7
            AND (
                    (
                        c.class_id IS NOT NULL
                        AND c.type != 86 AND c.type !=6
                        AND c.enrolment_start_date <= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate DESC LIMIT 1 )
                        AND c.enrolment_last_date >= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate DESC LIMIT 1 )
                        -- AND c.enrolment_last_date >='$start_date'
                        -- AND (c.must_charge > 0 OR c.created_at < '2020-08-10 00:00:00')
                    )
                OR  (
                        c.class_id IS NOT NULL
                        AND (c.type = 86 OR c.type =6)
                        AND c.enrolment_start_date <= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate >= '$end_date' AND `status`=1 ORDER BY cjrn_classdate ASC LIMIT 1 )
                        AND c.enrolment_last_date >= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate ASC LIMIT 1 )
                        -- AND c.enrolment_last_date >='$start_date'
                        -- AND (c.must_charge > 0 OR c.created_at < '2020-08-10 00:00:00')
                    )
            )
            AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND is_reserved=1 AND `start_date` <= '$end_date' AND `end_date`>='$end_date' AND `status`=2) =0
            AND (SELECT count(id) FROM contracts WHERE student_id = c.student_id AND `type`>0 AND `status`!=7 AND class_id IS NOT NULL AND id!=c.id) = 0
            AND ( c.debt_amount = 0 OR c.foced_is_full_fee_active = 1 )
            AND c.foced_is_full_fee_active != 2
            AND s.id IS NOT NULL
            AND s.status > 0
        $where GROUP BY s.id");
        if (count($list)) {
            self::addItems($list);
        }
        return $response->formatResponse($code, $data);
    }
    public function addItems($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_full_fee_active (student_id,contract_id, class_id, product_id, cm_id, report_month, branch_id, created_at, creator_id,last_date,date_start,done_session,summary_sessions) VALUES ";
            $query_all = "INSERT INTO report_full_fee_active_all (student_id,contract_id, class_id, product_id, cm_id, report_month, branch_id, created_at, creator_id,last_date,date_start,done_session,summary_sessions) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $done_session = self::getDoneSessions($item);
                    $query.= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '$item->cm_id', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$item->last_date','$item->date_start','$done_session','$item->summary_sessions'),";
                    $query_all.= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '$item->cm_id', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$item->last_date','$item->date_start','$done_session','$item->summary_sessions'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                $query_all = substr($query_all, 0, -1);
                u::query($query_all);
                self::addItems(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $done_session = self::getDoneSessions($item);
                    $query.= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '$item->cm_id', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$item->last_date','$item->date_start','$done_session','$item->summary_sessions'),";
                    $query_all.= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '$item->cm_id', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$item->last_date','$item->date_start','$done_session','$item->summary_sessions'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                $query_all = substr($query_all, 0, -1);
                u::query($query_all);
            }
        }
    }

    public function report01b1(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::queryReport01b1($p);
            $quary = Report::queryReport01b1($p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
            // lấy ra tổng số thành công
            $success_total = Report::queryReport01b1_success($p);
            $data->success_total = $success_total;
        }
        return $response->formatResponse($code, $data);
    }

    public function report01b2(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::queryReport01b2($p);
            $quary = Report::queryReport01b2($p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }
    public function report01b3(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::queryReport01b3($p);
            $quary = Report::queryReport01b3($p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
            $data->sumar = Report::summaryReport01b3($p);
        }
        return $response->formatResponse($code, $data);
    }

    public function report02a(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::queryReport02a($p);
            $quary = Report::queryReport02a($p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }
    public function report02b(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::queryReport02b($p);
            $quary = Report::queryReport02b($p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }
        return $response->formatResponse($code, $data);
    }
    public function addItemUser($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_get_users (user_id, role_id, branch_id, report_month, created_at, `status`) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query.= "('$item->user_id', '$item->role_id', '$item->branch_id', '$item->report_month', '$created_at', 1),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItemUser(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $query.= "('$item->user_id', '$item->role_id', '$item->branch_id', '$item->report_month', '$created_at', 1),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function collectReportGetUser(Request $request, $month)
    {
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();

        if($month && $month!="_"){
            $report_month = (int)$month > 9 ? date("Y").'-'.(int)$month : date("Y").'-0'.(int)$month;
        }else{
            $report_month = date('Y-m',time()-7*3600);
        }
        $list = u::query("SELECT
            t.*,
            '$report_month' report_month
        FROM
            term_user_branch AS t
            LEFT JOIN  users AS u ON t.user_id= u.id
        WHERE
            u.status=1 AND t.role_id IN (55,56,68,69) AND t.status=1");
        u::query("DELETE FROM report_get_users WHERE report_month = '$report_month' AND `type`=0 ");
        if (count($list)) {
            self::addItemUser($list);
        }
        return $response->formatResponse($code, $data);
    }
    public function addItemPending($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_pending (student_id, branch_id, product_id, cm_id, ec_id, contract_id,report_month,`sessions`,created_at ,tuition_fee_id,`start_date`,end_date) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query.= "('$item->student_id', '$item->branch_id', '$item->product_id', '$item->cm_id', '$item->ec_id', '$item->contract_id', '$item->report_month', '$item->sessions', '$created_at' , '$item->tuition_fee_id', '$item->start_date', '$item->end_date'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItemUser(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $query.= "('$item->student_id', '$item->branch_id', '$item->product_id', '$item->cm_id', '$item->ec_id', '$item->contract_id', '$item->report_month', '$item->sessions', '$created_at', '$item->tuition_fee_id', '$item->start_date', '$item->end_date'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function collectReportPending(Request $request, $month)
    {
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();

        if($month && $month!="_"){
            $report_month = (int)$month > 9 ? date("Y").'-'.(int)$month : date("Y").'-0'.(int)$month;
        }else{
            $report_month = date('Y-m',time()-7*3600);
        }
        $date = date('Y-m-d',time()-7*3600);
        $list = u::query("SELECT DISTINCT s.id AS student_id,c.branch_id,c.product_id,t.cm_id,t.ec_id,
                c.tuition_fee_id,c.summary_sessions AS `sessions`,c.start_date, c.end_date,c.id AS contract_id,c.tuition_fee_id,
                '$report_month' report_month
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id 
                LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status=1
            WHERE s.status>0 AND c.status!=7 AND c.class_id IS NULL AND c.type>0 AND c.summary_sessions>0
                AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1)
                AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= '$date' AND `start_date`<='$date' AND `status`=2) =0");
        u::query("DELETE FROM report_pending WHERE report_month = '$report_month'");
        if (count($list)) {
            self::addItemPending($list);
        }
        return $response->formatResponse($code, $data);
    }
    public function addItemReserve($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_reserve (student_id, branch_id, product_id, cm_id, ec_id, contract_id,report_month,`sessions`,created_at, is_reserved,tuition_fee_id) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query.= "('$item->student_id', '$item->branch_id', '$item->product_id', '$item->cm_id', '$item->ec_id', '$item->contract_id', '$item->report_month', '$item->sessions', '$created_at','$item->is_reserved','$item->tuition_fee_id'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItemUser(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $query.= "('$item->student_id', '$item->branch_id', '$item->product_id', '$item->cm_id', '$item->ec_id', '$item->contract_id', '$item->report_month', '$item->sessions', '$created_at','$item->is_reserved','$item->tuition_fee_id'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function collectReportReserve(Request $request, $month)
    {
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();

        if($month && $month!="_"){
            $report_month = (int)$month > 9 ? date("Y").'-'.(int)$month : date("Y").'-0'.(int)$month;
        }else{
            $report_month = date('Y-m',time()-7*3600);
        }
        $date = date('Y-m-d',time()-7*3600);
        $list = u::query("SELECT DISTINCT s.id AS student_id,c.branch_id,c.product_id,t.cm_id,t.ec_id,
                c.tuition_fee_id,c.summary_sessions AS `sessions`,c.start_date, c.end_date,c.id AS contract_id,c.tuition_fee_id,
                (SELECT is_reserved FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= '$date' AND `start_date`<='$date' AND `status`=2 LIMIT 1) AS is_reserved,
                '$report_month' report_month
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id 
                LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status=1
            WHERE s.status>0 AND c.status!=7 AND c.class_id IS NULL AND c.type>0 AND c.summary_sessions>0
                AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1)
                AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= '$date' AND `start_date`<='$date' AND `status`=2) >0");
        u::query("DELETE FROM report_reserve WHERE report_month = '$report_month'");
        if (count($list)) {
            self::addItemReserve($list);
        }
        return $response->formatResponse($code, $data);
    }
    public function report_r01(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $cms = isset($request->cms) ? implode(',', $request->cms) : null;
            $p->c = $cms;
            $code = APICode::SUCCESS;
            $query = Report::report_r01( $p);
            $quary = Report::report_r01( $p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
            $summary = (object)array(
                'must_charge'=>0,
                'total_charged'=>0,
            );
            foreach($data->list AS $row){
                $summary->must_charge += (int)$row->must_charge;
                $summary->total_charged += (int)$row->total_charged;
            }
            $data->summary = $summary;
        }

        return $response->formatResponse($code, $data);
    }
    public function report_r02(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $cms = isset($request->cms) ? implode(',', $request->cms) : null;
            $p->c = $cms;
            $code = APICode::SUCCESS;
            $query = Report::report_r02( $p);
            $quary = Report::report_r02( $p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
            $summary = (object)array(
                'must_charge'=>0,
                'total_charged'=>0,
            );
            foreach($data->list AS $row){
                $summary->must_charge += (int)$row->must_charge;
                $summary->total_charged += (int)$row->total_charged;
            }
            $data->summary = $summary;
        }

        return $response->formatResponse($code, $data);
    }
    public function report_r04(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::report_r04( $p);
            $quary = Report::report_r04( $p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }

        return $response->formatResponse($code, $data);
    }
    public function report_r05(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::report_r05( $p);
            $quary = Report::report_r05( $p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
            $summary = (object)array(
                'must_charge'=>0,
                'amount'=>0,
            );
            foreach($data->list AS $row){
                $summary->must_charge += (int)$row->must_charge;
                $summary->amount += (int)$row->amount;
            }
            $data->summary = $summary;
        }

        return $response->formatResponse($code, $data);
    }
    public function report_r06(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::report_r06( $p,$request);
            $quary = Report::report_r06( $p,$request, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
            $summary = (object)array(
                'must_charge'=>0,
                'total_charged'=>0,
                'ban_cheo'=>0,
                'thuc_thu'=>0
            );
            foreach($data->list AS $row){
                $summary->must_charge += (int)$row->must_charge;
                $summary->total_charged += (int)$row->total_charged;
                $summary->ban_cheo += $row->count_recharge ==0 && $row->pre_branch ? (int)$row->amount/2 : 0;
                $summary->thuc_thu += $row->count_recharge ==0 && $row->pre_branch ? (int)$row->amount/2 : (int)$row->amount;
            }
            $data->summary = $summary;
        }

        return $response->formatResponse($code, $data);
    }
    public function report_r07(Request $request)
    {
        if(!$request->report_month){
            $report_week_info = u::first("SELECT * FROM report_weeks WHERE start_date <= CURRENT_DATE AND end_date >= CURRENT_DATE");
            $request->report_month = $report_week_info->year."_".$report_week_info->group."_".$report_week_info->month;
        }
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::report_r07( $p,$request);
            $quary = Report::report_r07( $p,$request, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
            $arr_group = [
                '1'=>'I',
                '2'=>'II',
                '3'=>'III',
                '4'=>'IV'
            ];
            $arr_month = \explode('_',$request->report_month);
            $start_date = u::first("SELECT `start_date` FROM report_weeks WHERE year='".$arr_month[0]."' AND `group`='".$arr_month[1]."' AND month='".$arr_month[2]."' ORDER BY `start_date` ASC LIMIT 1");
            $end_date = u::first("SELECT `end_date` FROM report_weeks WHERE year='".$arr_month[0]."' AND `group`='".$arr_month[1]."' AND month='".$arr_month[2]."' ORDER BY `end_date` DESC LIMIT 1");
            $data->title_report =  "Năm ".$arr_month[0]." quý ". $arr_group[$arr_month[1]]." tháng ".$arr_month[2]." (Từ ngày $start_date->start_date đến ngày $end_date->end_date)";
        }

        return $response->formatResponse($code, $data);
    }
    public function report_r08(Request $request)
    {
        if(!$request->report_month){
            $report_week_info = u::first("SELECT * FROM report_weeks WHERE end_date <= CURRENT_DATE AND fix_group=1 LIMIT 1");
            $request->report_month = $report_week_info->year."_".$report_week_info->group;
        }
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::report_r08( $p,$request);
            $quary = Report::report_r08( $p,$request, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
            $arr_group = [
                '1'=>'I',
                '2'=>'II',
                '3'=>'III',
                '4'=>'IV'
            ];
            $arr_month = \explode('_',$request->report_month);
            $start_date = u::first("SELECT `start_date` FROM report_weeks WHERE year='".$arr_month[0]."' AND `group`='".$arr_month[1]."' ORDER BY `start_date` ASC LIMIT 1");
            $end_date = u::first("SELECT `end_date` FROM report_weeks WHERE year='".$arr_month[0]."' AND `group`='".$arr_month[1]."' ORDER BY `end_date` DESC LIMIT 1");
            $data->title_report =  "Năm ".$arr_month[0]." quý ". $arr_group[$arr_month[1]]." (Từ ngày $start_date->start_date đến ngày $end_date->end_date)";
        }

        return $response->formatResponse($code, $data);
    }
    public function report_r09(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::report_r09( $p,$request);
            $quary = Report::report_r09( $p,$request, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }

        return $response->formatResponse($code, $data);
    }
    public function report_r10(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::report_r10( $p);
            $quary = Report::report_r10( $p, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }

        return $response->formatResponse($code, $data);
    }
    public function report_r11(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::report_r11( $p,$request);
            $quary = Report::report_r11( $p,$request, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }

        return $response->formatResponse($code, $data);
    }

    public function report_r12(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::report_r12( $p,$request);
            $quary = Report::report_r12( $p,$request, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }

        return $response->formatResponse($code, $data);
    }

    public function report_r13(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $code = APICode::SUCCESS;
            $query = Report::report_r13( $p,$request);
            $quary = Report::report_r13( $p,$request, 1);
            $data = Report::making($query, $quary, $p->p, $p->l);
        }

        return $response->formatResponse($code, $data);
    }
}
