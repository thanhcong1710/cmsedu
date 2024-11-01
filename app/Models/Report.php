<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Providers\UtilityServiceProvider as u;
use App\Models\User;
use App\Models\LmsClass;
use App\Models\Student;
class Report extends Model
{
    public static $fd = '';
    public static $td = '';
    public static $limit = '';
    public static $page = '';
    public static $offset = '';
    public static $authorizeKey = '';
    public static $branchIds = '';
    public static $productIds = '';
    public static $strLimit = '';
    public static $getAll = '';
    public static $keyword = '';
    public static $ecId = '';
    public static $cmId = '';
    public static $omId = '';

    public static function commons($request)
    {
        $fd = $request->fd;
        if (!preg_match('#[0-9]{4}-[0-9]{2}-[0-9]{2}$#isu', $fd, $matches)) {
            $fd = date('Y-m-d', strtotime('-30 days'));
        }
        $td = $request->td;
        if (!preg_match('#[0-9]{4}-[0-9]{2}-[0-9]{2}$#isu', $td, $matches)) {
            $td = date('Y-m-d');
        }
        $fd = $fd . ' 00:00:00';
        $td = $td . ' 23:59:59';
        // dd($request->all());
        $limit  = $request->limit ? (int)$request->limit : 20;
        $page   = $request->page ? (int)$request->page : 1;
        $page   = $page ? $page : 1;
        $offset = ($page - 1) * $limit;
        $keyword      = trim($request->keyword);
        $authorizeKey = $request->headers->get('Authorization');
        $arrBranchAllow = [];
        $arrProducts = array();
        $arrBranchIds = array();

        if ($authorizeKey !== null) {
            $token = Redis::keys("*:$authorizeKey");
            $token = count($token) ? $token[0] : '';
            $session = Redis::get($token);
            $session = $session ? json_decode($session) : $session;
            $roleId = isset($session->role_id) ? $session->role_id : 0;

            $roleDetail = isset($session->roles_detail) ? $session->roles_detail : '';
            if ($roleDetail) {
                foreach ($roleDetail as $k => $role) {
                    $arrBranchAllow[] = $role->branch_id;
                }
            }
            switch ($roleId) {
                case User::ROLE_EC:
                    self::$ecId = $session->id;
                    break;
                case User::ROLE_CM:
                    self::$cmId = $session->id;
                    break;
                case User::ROLE_OM:
                    self::$omId = $session->id;
                    break;
            }
        } else {
            return null;
        }

        if ($request->branches) {
            if (array_key_exists('id', $request->branches)) {
                $arrBranchIds = [$request->branches['id']];
            } else {
                $arrBranchIds = array_column($request->branches, 'id');
            }
        } else {
            $arrBranchIds = $arrBranchAllow;
            $arrBranchIds = $roleId == User::ROLE_ADMINISTRATOR ? array() : $arrBranchIds;
        }

        if ($request->products) {
            if (array_key_exists('id', $request->products)) {
                $arrProducts = [$request->products['id']];
            } else {
                $arrProducts = array_column($request->products, 'id');
            }
        }

        $strLimit = '';
        if ($limit !== null && $offset !== null) {
            $strLimit .= " LIMIT $offset, $limit";
        }
        $strLimit = $request->get_all == true ? '' : $strLimit;

        self::$fd = $fd;
        self::$td = $td;
        self::$strLimit   = $strLimit;
        self::$productIds = implode(',', $arrProducts);;
        self::$keyword    = $keyword;
        self::$branchIds  = implode(',', $arrBranchIds);
    }

    private static function getScope($scope, $branches = []) {
        $resp = $branches;
        if ($scope) {
            $is_zone = count(array_filter($scope, function($item) {
                return (int)$item > 9000;
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
                    }else{
                        $resp = [0];
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

    public static function making($query, $quary, $page, $limit) {
        $data = null;
        $suma = u::first($quary);
        $list = u::query($query);
        $data = (Object)[];
        $pagination = (Object)[];
        $total = $suma && isset($suma->total) ? (int)$suma->total : 0;
        $pagination->spage = 1;
        $pagination->cpage = $page;
        $pagination->total = $total;
        $pagination->limit = $limit;
        $pagination->lpage = ($total % $limit) == 0 ? (int)($total / $limit) : (int)($total / $limit) + 1;
        $pagination->ppage = $page > 0 ? $page - 1 : 0;
        $pagination->npage = $page < $pagination->lpage ? $page + 1 : $pagination->lpage;
        $data->list = $list;
        $data->paging = $pagination;
        return $data;
    }

    public static function params($request, $session, $export = false) {
        $params = null;
        if ($request && $session) {
            $params = (Object)[];
            $today = date('Y-m-d');
            $from = isset($request->from) ? $request->from : date('Y-m-d', strtotime('-7 days'));
            $to = isset($request->to) ? $request->to : $today;
            $date = isset($request->date) ? $request->date : date('Y-m');
            $products = isset($request->products) ? implode(',',$request->products) : null;
            $cms = isset($request->cms) ? implode(',',$request->cms) : null;
            $ecs = isset($request->ecs) ? implode(',',$request->ecs) : null;
            $keyword = isset($request->keyword) ? trim((string)$request->keyword) : null;
            $type = isset($request->type) ? (int)$request->type : null;
            $scope = self::getScope($request->scope, explode(',',$session->branches_ids));
            $page = isset($request->page) ? (int)$request->page : 1;
            $limit = isset($request->limit) ? (int)$request->limit : 20;
            $limit = $export ? 10 * $limit : $limit;
            $point = $export ? 10 * ($page - 1) : ($page - 1) * $limit;
            $params->k = $keyword;
            $params->r = $products;
            $params->e = $type;
            $params->f = $from;
            $params->t = $to;
            $params->a = $date;
            $params->s = $scope;
            $params->p = $page;
            $params->l = $limit;
            $params->d = $point;
            $params->c = $cms;
            $params->ecs = $ecs;
            $params->role_id = $request->users_data->role_id;
            $params->user_id = $request->users_data->id;
        }
        return $params;
    }

    public static function queryReport01a($p, $total = 0, $unlimit = false) {
        $resp = "";
        if ($total) {
            $resp = "SELECT COUNT(b.id) total
                FROM branches b LEFT JOIN regions r ON b.region_id = r.id
                WHERE b.status = 1 AND b.id IN ($p->s)";
        } else {
            // (SELECT COUNT(u.id) FROM term_user_branch t LEFT JOIN users u ON t.user_id = u.id WHERE t.role_id = 36
            //             AND u.`status` > 0 AND u.start_date <= '$p->f' AND (u.end_date >= '$p->f' OR u.end_date IS NULL)
            //             AND t.branch_id = b.id) AS total_teachers,
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT b.id, b.`name` branch_name, r.`name` region_name,
                    (SELECT COUNT(id) FROM rooms WHERE `status` > 0 AND rooms.branch_id = b.id) total_rooms,
                    (SELECT COUNT(id) FROM classes WHERE classes.brch_id = b.brch_id
                        AND cls_startdate <= '$p->f' AND cls_enddate >= '$p->f'
                        AND cls_iscancelled = 'no') total_classes,
                    (SELECT COUNT(t.id) FROM term_teacher_branch t WHERE t.branch_id = b.id) AS total_teachers,
                    (SELECT COUNT(DISTINCT(c.student_id)) 
                        FROM contracts c 
                        LEFT JOIN pendings p ON p.contract_id = c.id 
                    WHERE c.debt_amount = 0 
                        AND c.type > 0 
                        AND c.branch_id = b.id 
                        AND ((DATE( c.updated_at ) <= '$p->f' AND DATE( c.end_date ) >= '$p->f') OR (DATE( c.created_at ) <= '$p->f'))
                        AND (c.total_charged > 0 OR c.type IN (8, 85, 86)) 
                        AND c.debt_amount = 0 
                        AND c.status != 7 
                    ) AS total_full_fees
                    FROM branches b LEFT JOIN regions r ON b.region_id = r.id
                    WHERE b.status = 1 AND b.id IN ($p->s)";
//            die($resp);
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    
    public static function queryReport04($p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        if($p->s) {
            $where .= " AND b.id in ($p->s) ";
        }
        if ($total) {
            $resp = "SELECT COUNT(b.id) total FROM branches b WHERE b.status = 1 $where";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT b.`name` branch_name,
                (SELECT COUNT(id)
                    FROM classes
                    WHERE classes.brch_id = b.brch_id
                    AND cls_startdate <= ' $p->f'
                    AND cls_enddate >= ' $p->f'
                    AND cls_iscancelled = 'no') total_classes,
                (SELECT COUNT(t.id) FROM term_teacher_branch t WHERE t.branch_id = b.id) AS total_teachers,
                (SELECT COUNT(DISTINCT(c.student_id)) 
                        FROM contracts c 
                        LEFT JOIN enrolments e ON c.enrolment_id = e.id 
                        LEFT JOIN pendings p ON p.contract_id = c.id 
                    WHERE c.debt_amount = 0 
                        AND c.type > 0 
                        AND c.branch_id = b.id 
                        AND e.status > 0 
                        AND ((DATE( c.updated_at ) <= '$p->f' AND DATE( c.end_date ) >= '$p->f') OR (DATE( c.created_at ) <= '$p->f'))
                        AND (c.total_charged > 0 OR c.type IN (8, 85)) 
                        AND c.debt_amount = 0 
                        AND c.status != 7 
                        AND IF (c.enrolment_id, e.`status` > 0, true)
                    ) AS total_full_fee
            FROM branches b WHERE b.status = 1 $where";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }

    public static function queryReport28($p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        if($p->s) {
            $where .= " AND c.branch_id in ($p->s) ";
        }
        if($p->r) {
            $where .= " AND c.product_id in ($p->r) ";
        }
        if($p->c) {
            $where .= " AND c.cm_id in ($p->c) ";
        }
        if ($total) {
            $resp = "SELECT COUNT(c.id) total FROM contracts c
                        LEFT JOIN students s ON c.student_id = s.id
                        LEFT JOIN classes cl ON c.class_id = cl.id
                    WHERE s.status >0 AND c.status NOT IN (0,7,8) AND c.enrolment_last_date <= CURDATE() $where";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        c.student_id student_id,
                        s.name student_name,
                        s.nick student_nick,
                        s.crm_id,
                        c.type customer_type,
                        cl.cls_name class_name,
                        (SELECT CONCAT(full_name,' - ',username) FROM users WHERE id = c.cm_id) cm_name,
                        (SELECT CONCAT(u.full_name,' - ',u.username) FROM users u LEFT JOIN term_student_user t ON t.ec_id = u.id WHERE t.student_id = c.student_id) ec_name,
                        (SELECT name FROM branches WHERE id = s.branch_id) branch_name,
                        (SELECT name FROM products WHERE id = c.product_id) product_name,
                        (SELECT name FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_name,
                        (SELECT `name` FROM programs WHERE id = c.program_id) program_name,
                        c.enrolment_start_date start_date,
                        c.enrolment_last_date last_date,
                        c.enrolment_real_sessions available_sessions,
                        (SELECT s.`accounting_id` FROM students s WHERE s.id = c.student_id) cyber_code
                    FROM contracts c
                        LEFT JOIN students s ON c.student_id = s.id
                        LEFT JOIN classes cl ON c.class_id = cl.id
                    WHERE s.status >0 AND c.status NOT IN (0,7,8) AND c.enrolment_last_date < CURDATE() $where";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }

    public static function queryReport29($p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        if($p->s) {
            $where .= " AND c.branch_id in ($p->s) ";
        }
        if($p->r) {
            $where .= " AND c.product_id in ($p->r) ";
        }
        if ($total) {
            $resp = "SELECT COUNT(e.id) total FROM enrolments e
                        LEFT JOIN students s ON e.student_id = s.id
                        LEFT JOIN contracts c ON e.contract_id = c.id
                        LEFT JOIN classes cl ON e.class_id = cl.id
                    WHERE s.status >0 AND e.status > 0 AND e.real_sessions > 0 AND e.last_date <= CURDATE() AND (SELECT COUNT(x.id) FROM contracts x WHERE x.count_recharge > -1 AND x.student_id = c.student_id) = 0 $where";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        e.student_id student_id,
                        s.name student_name,
                        s.nick student_nick,
                        s.stu_id,
                        c.type customer_type,
                        cl.cls_name class_name,
                        (SELECT u.username FROM users u LEFT JOIN term_student_user t ON t.cm_id = u.id WHERE t.student_id = e.student_id) cm_name,
                        (SELECT u.username FROM users u LEFT JOIN term_student_user t ON t.ec_id = u.id WHERE t.student_id = e.student_id) ec_name,
                        (SELECT name FROM branches WHERE id = s.branch_id) branch_name,
                        (SELECT name FROM products WHERE id = c.product_id) product_name,
                        (SELECT name FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_name,
                        COALESCE(c.program_label, (SELECT name FROM programs WHERE id = c.program_id)) program_name,
                        e.start_date,
                        e.last_date,
                        e.real_sessions available_sessions
                    FROM enrolments e
                        LEFT JOIN students s ON e.student_id = s.id
                        LEFT JOIN contracts c ON e.contract_id = c.id
                        LEFT JOIN classes cl ON e.class_id = cl.id
                    WHERE s.status >0 AND e.status > 0 AND e.real_sessions > 0 AND e.last_date <= CURDATE() AND c.count_recharge = -1 AND c.type = 0 AND (SELECT COUNT(x.id) FROM contracts x WHERE x.count_recharge > -1 AND x.student_id = c.student_id) = 0 $where";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }

    public static function reserves($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $fd = $request->fd ? $request->fd : date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $td = $request->td ? $request->td : date('Y-m-d');
        $branchIds = self::$branchIds;
        $keyword   = self::$keyword;
        $productIds = self::$productIds;
        $fd = $fd . ' 00:00:00';
        $td = $td . ' 23:59:59';
        if ($request->status !== null && $request->status !== '') {
            $where[] = "r.status IN ($request->status)";
        }
        if ($fd && $td) {
            $where[] = "((r.start_date >= '$fd' AND r.start_date <= '$td' ) OR (r.end_date >= '$fd' AND r.end_date <= '$td' ))";
        }
        if ($keyword) {
            $where[] = "(s.stu_id = '$keyword' OR s.effect_id = '$keyword' OR s.name LIKE '%$keyword%')";
        }
        if ($branchIds) {
            $where[] = "r.branch_id IN ($branchIds)";
        }
        if ($productIds) {
            $where[] = "c.product_id IN ($productIds)";
        }
        if ($where) {
            $strWhere = ' WHERE ' . implode(' AND ', $where);
        }

        $query = "
                SELECT
                    r.`id` id,
                    r.`student_id` student_id,
                    r.`type` `type`,
                    r.`start_date` reserve_start_date,
                    r.`end_date` reserve_end_date,
                    r.`session` `session`,
                    r.`created_at` created_at,
                    r.`approved_at` approved_at,
                    r.`comment` `comment`,
                    CASE 
                      WHEN r.is_reserved = 0 THEN 'Không giữ chỗ'
                      WHEN r.is_reserved = 1 THEN 'Giữ chỗ'
                    END AS is_reserved,
                    CASE 
                      WHEN r.status = 0 THEN 'Chờ duyệt'
                      WHEN r.status = 1 THEN 'Đã duyệt'
                      WHEN r.status = 2 THEN 'Từ chối'
                    END AS status,
                    CASE 
                      WHEN r.type = 0 THEN 'Bình thường'
                      WHEN r.type = 1 THEN 'Đặc biệt'
                      WHEN r.type = 2 THEN 'Bình thường + Đặc biệt'
                    END AS type_name,
                    r.`meta_data` meta_data,
                    r.`attached_file` attached_file,
                    s.`name` student_name,
                    s.`stu_id` lms_id,
                    s.`effect_id` effect_id,
                    b.`name` branch_name,
                    p.`name` product_name,
                    pr.`name` program_name,
                    cls.`cls_name` class_name,
                    r.`is_supplement`,
                    t.`name` tuition_fee_name,
                    u1.username creator_name,
                    u2.username approver_name,
                    special_reserved_sessions special_reserved_sessions
                FROM
                    reserves r
                    LEFT JOIN students s ON r.`student_id` = s.`id`
                    LEFT JOIN contracts c ON r.`contract_id` = c.`id`
                    LEFT JOIN tuition_fee t ON t.`id` = c.`tuition_fee_id`
                    LEFT JOIN branches b ON r.`branch_id` = b.`id`
                    LEFT JOIN products p ON r.`product_id` = p.`id`
                    LEFT JOIN programs pr ON r.`program_id` = pr.`id`
                    LEFT JOIN classes cls ON r.`class_id` = cls.`id`
                    LEFT JOIN users u1 ON r.`creator_id` = u1.`id`
                    LEFT JOIN users u2 ON r.`approver_id` = u2.`id`
                $strWhere AND s.status >0 
                GROUP BY r.id
                ORDER BY r.created_at DESC
                $strLimit";

        $queryCount = "
                    SELECT COUNT(DISTINCT(r.id)) AS total
                    FROM reserves r
                    LEFT JOIN students s ON r.`student_id` = s.`id`
                    LEFT JOIN contracts c ON r.`contract_id` = c.`id`
                    LEFT JOIN tuition_fee t ON t.`id` = c.`tuition_fee_id`
                    LEFT JOIN branches b ON r.`branch_id` = b.`id`
                    LEFT JOIN products p ON r.`product_id` = p.`id`
                    LEFT JOIN programs pr ON r.`program_id` = pr.`id`
                    LEFT JOIN classes cls ON r.`class_id` = cls.`id`
                    LEFT JOIN users u1 ON r.`creator_id` = u1.`id`
                    LEFT JOIN users u2 ON r.`approver_id` = u2.`id`
                $strWhere AND s.status >0 ";

        $rs     = DB::select(DB::raw($query));
        $total  = DB::select(DB::raw($queryCount));
        $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;
        if ($rs) {
            foreach ($rs as $k => $v) {
                $metaData = json_decode($v->meta_data);
                $amountLeft     = isset($metaData->amount_left) ? $metaData->amount_left : 0;
                $totalFee       = isset($metaData->total_fee) ? $metaData->total_fee : 0;
                $totalSession   = isset($metaData->total_session) ? $metaData->total_session : 0;
                $numberReserved = isset($v->session) ? $v->session : 0;
                $amountReserved = $totalSession > 0 ? (ceil($totalFee / $totalSession) * $numberReserved) : 0;
                $v->total_session   = $totalSession;
                $v->total_fee       = $totalFee;
                $v->session_left    = isset($metaData->session_left) ? $metaData->session_left : 0;
                $v->amount_left     = $amountLeft;
                $v->start_date      = isset($metaData->start_date) ? $metaData->start_date : '';
                $v->end_date        = isset($metaData->end_date) ? $metaData->end_date : '';
                $v->amount_reserved = $amountReserved;
                $v->number_of_session_reserved = isset($metaData->number_of_session_reserved) ? $metaData->number_of_session_reserved : 0;
            }
        }
        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $totalRecord
        );
        return $return;
    }

    public static function registers($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $fd = self::$fd;
        $td = self::$td;
        $branchIds  = self::$branchIds;
        $productIds = self::$productIds;
        $keyword    = $request->keyword ? trim($request->keyword) : null;
        $where[] = "c.type <> 0";
        if ($productIds) {
            $where[] = "c.product_id IN ( $productIds )";
        }
        if ($fd && $td) {
            $where[] = "e.start_date >= '$fd' AND e.start_date <= '$td'";
        }
        if ($keyword) {
            $where[] = "(s.stu_id = '$keyword' OR c.ec_id IN (SELECT id FROM users WHERE full_name LIKE '%$keyword%') OR c.cm_id IN (SELECT id FROM users WHERE full_name LIKE '%$keyword%'))";
        }
        if ($branchIds) {
            $where[] = "s.branch_id IN ($branchIds)";
        }
        if ($where) {
            $strWhere = ' WHERE ' . implode(' AND ', $where);
        }
//        $strWhere = '';
//        $strLimit = 'LIMIT 0, 20';
        $query = "
                SELECT
                    e.`id` AS id,
                    e.`student_id` AS student_id,
                    e.`start_date` AS enrolment_start_date,
                    e.`end_date` AS enrolment_end_date,
                    e.`last_date` AS enrolment_last_date,
                    s.`name` AS student_name,
                    s.`stu_id` AS lms_id,
                    s.`effect_id` AS effect_id,
                    s.`branch_id` AS branch_id,
                    s.`crm_id` AS crm_id,
                    s.`stu_id` AS lms_id,
                    s.`effect_id` AS effect_id,
                    cl.`cls_name` AS class_name,
                    e.`real_sessions` AS real_sessions,
                    c.`total_charged` AS total_charged,
                    t.`name` AS tuition_fee_name,
                    u1.`full_name` AS cm_name,
                    u2.`full_name` AS ec_name,
                    b.`name` AS branch_name,
                    (SELECT count(1) FROM schedules WHERE cls_id = e.class_id AND cjrn_classdate <= LAST_DAY(e.start_date) && cjrn_classdate >= e.start_date) AS session_first_month,
                    (SELECT name FROM semesters WHERE id = cl.semester_id) AS semester_name
                FROM
                    enrolments AS e
                    LEFT JOIN students AS s ON e.`student_id` = s.`id`
                    LEFT JOIN contracts AS c ON e.`contract_id` = c.`id`
                    LEFT JOIN classes AS cl ON cl.`id` = e.`class_id`
                    LEFT JOIN tuition_fee AS t ON t.`id` = c.`tuition_fee_id`
                    LEFT JOIN users AS u1 ON c.`cm_id` = u1.`id`
                    LEFT JOIN users AS u2 ON c.`ec_id` = u2.`id`
                    LEFT JOIN branches AS b ON c.`branch_id` = b.`id`
                $strWhere
                ORDER BY e.created_at DESC
                $strLimit";

        $queryCount = "
                    SELECT COUNT(1) AS total
                    FROM
                    enrolments AS e
                    LEFT JOIN students AS s ON e.`student_id` = s.`id`
                    LEFT JOIN contracts AS c ON e.`contract_id` = c.`id`
                    LEFT JOIN classes AS cl ON cl.`id` = e.`class_id`
                    LEFT JOIN tuition_fee AS t ON t.`id` = c.`tuition_fee_id`
                    LEFT JOIN users AS u1 ON c.`cm_id` = u1.`id`
                    LEFT JOIN users AS u2 ON c.`ec_id` = u2.`id`
                    LEFT JOIN branches AS b ON c.`branch_id` = b.id
                $strWhere";

        $rs     = DB::select(DB::raw($query));
        $total  = DB::select(DB::raw($queryCount));
        $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;

        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $totalRecord
        );
        return $return;
    }

    public static function tuitionTransfers($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $fd = $request->fd ? $request->fd : date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $td = $request->td ? $request->td : date('Y-m-d');
        $fd = $fd ? $fd . ' 00:00:00' : $fd;
        $td = $td ? $td . ' 23:59:59' : $td;
        $branchIds = self::$branchIds;
        $keyword   = self::$keyword;
        $productIds = self::$productIds;
        $ecId = self::$ecId;
        $cmId = self::$cmId;
        if ($request->status !== '' && $request->status !== null) {
            $where[] = "tt.status IN ($request->status)";
        }

        if ($cmId) {
            $where[] = "(ts1.cm_id IN ( $cmId ) OR ts2.cm_id IN ( $cmId ))";
        }

        if ($ecId) {
            $where[] = "(ts1.ec_id IN ( $ecId ) OR ts2.ec_id IN ( $ecId ))";
        }

        $where[] = "(c1.type <> 0 OR c2.type <> 0)";

        if ($productIds) {
            $where[] = "(c1.product_id IN ( $productIds ) OR c2.product_id IN ( $productIds ))";
        }
        if ($fd && $td) {
            $where[] = "tt.transfer_date >= '$fd' AND tt.transfer_date <= '$td'";
        }
        if ($keyword) {
            $where[] = "(tt.from_student_id = '$keyword' OR tt.to_student_id = '$keyword' OR s1.name LIKE '%" . $keyword . "%' OR s2.name LIKE '%" . $keyword . "%' OR s1.stu_id = '$keyword' OR s2.stu_id = '$keyword' OR s1.effect_id = '$keyword' OR s2.effect_id = '$keyword')";
        }
        if ($branchIds) {
            $where[] = "(s1.branch_id IN ($branchIds) OR s1.branch_id IN ($branchIds))";
        }
        if ($where) {
            $strWhere = ' WHERE ' . implode(' AND ', $where);
        }

        $query = "
                SELECT
                    tt.`id` AS id,
                    s1.`stu_id` AS from_student_id,
                    s2.`stu_id` AS to_student_id,
                    tt.`transfer_date` AS transfer_date,
                    tt.`note` AS note,
                    tt.`amount_transferred` AS amount_transferred,
                    tt.`amount_received` AS amount_received,
                    tt.`session_transferred` AS session_transferred,
                    tt.`session_received` AS session_received,
                    tt.`from_branch_id` AS from_branch_id,
                    tt.`to_branch_id` AS to_branch_id,
                    tt.`created_at` AS created_at,
                    tt.`approved_at` AS approved_at,
                    CASE 
                      WHEN tt.status = 0 THEN 'Chờ duyệt'
                      WHEN tt.status = 1 THEN 'Đã duyệt đi'
                      WHEN tt.status = 2 THEN 'Từ chối'
                    END AS status, 
                    b1.`name` AS from_branch_name,
                    b2.`name` AS to_branch_name,
                    s1.`effect_id` AS from_effect_id,
                    s2.`effect_id` AS to_effect_id,
                    s1.`name` AS from_student_name,
                    s2.`name` AS to_student_name,
                    c1.`total_sessions` AS from_total_sessions,
                    c1.`total_charged` AS from_total_charged,
                    c1.`total_sessions` AS to_total_sessions,
                    c1.`total_charged` AS to_total_charged,
                    cl1.`cls_name` AS from_class_name,
                    cl2.`cls_name` AS to_class_name,
                    p1.name AS from_product_name,
                    p2.name AS to_product_name,
                    u1.username AS creator_name,
                    u2.username AS approver_name
                FROM
                    tuition_transfer AS tt
                    LEFT JOIN students AS s1 ON tt.`from_student_id` = s1.`id`
                    LEFT JOIN students AS s2 ON tt.`to_student_id` = s2.`id`
                    LEFT JOIN branches AS b1 ON tt.`from_branch_id` = b1.id
                    LEFT JOIN branches AS b2 ON tt.`to_branch_id` = b2.id
                    LEFT JOIN contracts AS c1 ON tt.`from_contract_id` = c1.id
                    LEFT JOIN contracts AS c2 ON tt.`to_contract_id` = c2.id
                    LEFT JOIN enrolments AS e1 ON e1.contract_id = c1.id
                    LEFT JOIN enrolments AS e2 ON e2.contract_id = c2.id
                    LEFT JOIN classes AS cl1 ON e1.`class_id` = cl1.id
                    LEFT JOIN classes AS cl2 ON e2.`class_id` = cl2.id
                    LEFT JOIN term_student_user AS ts1 ON ts1.student_id = s1.id
                    LEFT JOIN term_student_user AS ts2 ON ts2.student_id = s2.id
                    LEFT JOIN products AS p1 ON tt.from_product_id = p1.id
                    LEFT JOIN products AS p2 ON tt.to_product_id = p2.id
                    LEFT JOIN users AS u1 ON tt.creator_id = u1.id
                    LEFT JOIN users AS u2 ON tt.approver_id = u2.id
                $strWhere
                ORDER BY tt.transfer_date DESC
                $strLimit";

        $queryCount = "
                    SELECT COUNT(1) AS total
                    FROM tuition_transfer AS tt
                    LEFT JOIN students AS s1 ON tt.from_student_id = s1.`id`
                    LEFT JOIN students AS s2 ON tt.to_student_id = s2.`id`
                    LEFT JOIN branches AS b1 ON tt.`from_branch_id` = b1.id
                    LEFT JOIN branches AS b2 ON tt.`to_branch_id` = b2.id
                    LEFT JOIN contracts AS c1 ON tt.`from_contract_id` = c1.id
                    LEFT JOIN contracts AS c2 ON tt.`to_contract_id` = c2.id
                    LEFT JOIN classes AS cl1 ON tt.from_class_id = cl1.id
                    LEFT JOIN classes AS cl2 ON tt.to_class_id = cl2.id
                    LEFT JOIN term_student_user AS ts1 ON ts1.student_id = s1.id
                    LEFT JOIN term_student_user AS ts2 ON ts2.student_id = s2.id
                    LEFT JOIN products AS p1 ON tt.from_product_id = p1.id
                    LEFT JOIN products AS p2 ON tt.to_product_id = p2.id
                    LEFT JOIN users AS u1 ON tt.creator_id = u1.id
                    LEFT JOIN users AS u2 ON tt.approver_id = u2.id
                $strWhere";

        $rs     = DB::select(DB::raw($query));
        $total  = DB::select(DB::raw($queryCount));
        $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;

        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $totalRecord
        );
        return $return;
    }

    public static function renewals($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $fd = $request->fd;
        if (!$fd) {
            $fd = date('Y-m-d');
        }
        $td = $request->td;
        if (!$td) {
            $td = date('Y-m-d', strtotime('+15 days'));
        }
        $ec_name = $request->ec_name;
        $cm_name = $request->cm_name;
        $branchIds = self::$branchIds;
        $keyword   = $request->student_name ? trim($request->student_name) : null;

        $where[] = "e.`status` = 1 AND e.`last_date` > NOW()";

        if ($fd && $td) {
            $where[] = "e.last_date >= '$fd' AND e.last_date <= '$td'";
        }
        if ($keyword) {
            $where[] = "(s.stu_id = '$keyword' OR s.effect_id LIKE '%$keyword%' OR s.name LIKE '%$keyword%')";
        }
        if ($ec_name) {
            $where[] = "(u1.username LIKE '%$ec_name%' OR u1.full_name LIKE '%$ec_name%')";
        }
        if ($cm_name) {
            $where[] = "(u2.username LIKE '%$cm_name%' OR u2.full_name LIKE '%$cm_name%')";
        }
        if ($branchIds) {
            $where[] = "s.branch_id IN ($branchIds)";
        }
        if ($where) {
            $strWhere = ' WHERE ' . implode(' AND ', $where);
        }

        $query = "
            SELECT
                b.`name` AS `branch_name`,
                s.`stu_id` AS `lms_id`,
                s.`effect_id` AS `effect_id`,
                s.`name` AS `student_name`,
                cl.`cls_name` AS `class_name`,
                t.`name` AS `tuition_fee_name`,
                u1.`username` AS `ec_user_name`,
                u1.`full_name` AS `ec_full_name`,
                u2.`username` AS `cm_user_name`,
                u2.`full_name` AS `cm_full_name`,
                c.`real_sessions` AS `real_sessions`,
                c.`total_charged` AS `total_charged`,
                e.`last_date` AS `last_date`
            FROM
                `enrolments` e
                LEFT JOIN `contracts` c ON e.`contract_id` = c.`id`
                LEFT JOIN `classes` cl ON e.`class_id` = cl.`id`
                LEFT JOIN `tuition_fee` t ON c.`tuition_fee_id` = t.`id`
                LEFT JOIN `students` s ON e.`student_id` = s.`id`
                LEFT JOIN `branches` b ON s.`branch_id` = b.`id`
                LEFT JOIN `term_student_user` tsu ON s.`id` = tsu.`student_id`
                LEFT JOIN `users` u1 ON tsu.`ec_id` = u1.`id`
                LEFT JOIN `users` u2 ON tsu.`cm_id` = u2.`id`
            $strWhere
            ORDER BY e.`last_date`
            $strLimit";

        $queryCount = "
            SELECT
                COUNT(1) AS total
            FROM
                `enrolments` e
                LEFT JOIN `contracts` c ON e.`contract_id` = c.`id`
                LEFT JOIN `classes` cl ON e.`class_id` = cl.`id`
                LEFT JOIN `tuition_fee` t ON c.`tuition_fee_id` = t.`id`
                LEFT JOIN `students` s ON e.`student_id` = s.`id`
                LEFT JOIN `branches` b ON s.`branch_id` = b.`id`
                LEFT JOIN `term_student_user` tsu ON s.`id` = tsu.`student_id`
                LEFT JOIN `users` u1 ON tsu.`ec_id` = u1.`id`
                LEFT JOIN `users` u2 ON tsu.`cm_id` = u2.`id`
             $strWhere";

        $rs     = DB::select(DB::raw($query));
        $total  = DB::select(DB::raw($queryCount));
        $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;

        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $totalRecord
        );
        return $return;
    }

    public static function branchTransfers($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $fd = $request->fd ? $request->fd : date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $td = $request->td ? $request->td : date('Y-m-d');
        $branchIds = self::$branchIds;
        $keyword   = self::$keyword;
        $productIds = self::$productIds;
        $ecId = self::$ecId;
        $cmId = self::$cmId;
        $fd = $fd ? $fd . ' 00:00:00' : $fd;
        $td = $td ? $td . ' 23:59:59' : $td;
        if ($cmId) {
            $where[] = "ts.cm_id IN ( $cmId )";
        }

        if ($ecId) {
            $where[] = "ts.ec_id IN ( $ecId )";
        }

        $where[] = "ct.type IN (1,2)";
        if ($request->status !== "" && $request->status !== null) {
            $where[] = "ct.status IN ( $request->status )";
        } else {
            $where[] = "ct.status IN ( 0,1,2,3,4 )";
        }
        if ($productIds) {
            $where[] = "c.product_id IN ( $productIds )";
        }
        if ($fd && $td) {
            $where[] = "ct.created_at >= '$fd' AND ct.created_at <= '$td'";
        }
        if ($keyword) {
            $where[] = "(s.id = '$keyword' OR s.name LIKE '%" . $keyword . "%' OR s.stu_id = '$keyword' OR s.effect_id = '$keyword')";
        }
        if ($branchIds) {
            $where[] = "(ct.from_branch_id IN ($branchIds) OR ct.to_branch_id IN ($branchIds))";
        }
        if ($where) {
            $strWhere = ' WHERE ' . implode(' AND ', $where);
        }

        $query = "
                SELECT
                    DISTINCT (s.stu_id) AS stu_id,
                    s.name AS student_name,
                    ct.`id` AS id,
                    ct.`student_id`,
                    ct.`transfer_date` AS transfer_date,
                    ct.`from_branch_id` AS from_branch_id,
                    ct.`to_branch_id` AS to_branch_id,
                    ct.`amount_transferred` AS amount_transferred,
                    ct.`session_transferred` AS session_transferred,
                    ct.`session_exchange` AS session_exchange,
                    ct.`amount_exchange` AS amount_exchange,
                    ct.`created_at` AS created_at,
                    ct.`meta_data` AS meta_data,
                    ct.`from_approved_at` AS from_approved_at,
                    ct.`to_approved_at` AS to_approved_at,
                    ct.`transfer_date` AS transfer_date,
                    ct.`from_effect_id` AS from_effect_id,
                    ct.`to_effect_id` AS to_effect_id,                    
                    CASE 
                      WHEN ct.status = 0 THEN 'Chờ duyệt đi'
                      WHEN ct.status = 1 THEN 'Chờ duyệt bên nhận'
                      WHEN ct.status = 2 THEN 'Đã duyệt'
                      WHEN ct.status = 3 THEN 'Từ chối duyệt đi'
                      WHEN ct.status = 4 THEN 'Từ chối duyệt đến'
                      WHEN ct.status = 5 THEN 'Đã xóa'
                    END AS status,
                    b1.`name` AS from_branch_name,
                    b2.`name` AS to_branch_name,           
                    s.used_student_ids AS used_student_ids,
                    u1.username AS creator_name,
                    u2.username AS from_approver_name,
                    u3.username AS to_approver_name,
                    sem.name AS semester_name
                FROM
                    class_transfer AS ct
                    LEFT JOIN contracts AS c ON c.student_id = ct.student_id
                    LEFT JOIN enrolments as e ON c.id = e.contract_id
                    LEFT JOIN students AS s ON ct.`student_id` = s.`id`
                    LEFT JOIN branches AS b1 ON ct.`from_branch_id` = b1.id
                    LEFT JOIN branches AS b2 ON ct.`to_branch_id` = b2.id
                    LEFT JOIN classes AS cl1 ON e.`class_id` = cl1.cls_id
                    LEFT JOIN classes AS cl2 ON ct.`to_class_id` = cl2.cls_id
                    LEFT JOIN semesters AS sem ON sem.id = ct.semester_id
                    LEFT JOIN term_student_user AS ts ON ts.student_id = s.id
                    LEFT JOIN users AS u1 ON ct.creator_id = u1.id
                    LEFT JOIN users AS u2 ON ct.from_approver_id = u2.id
                    LEFT JOIN users AS u3 ON ct.to_approver_id = u3.id
                $strWhere
                ORDER BY ct.transfer_date DESC
                $strLimit";

        $queryCount = "
                    SELECT COUNT(DISTINCT(ct.id)) AS total
                    FROM class_transfer AS ct
                   LEFT JOIN contracts AS c ON c.student_id = ct.student_id
                    LEFT JOIN enrolments as e ON c.id = e.contract_id
                    LEFT JOIN students AS s ON ct.`student_id` = s.`id`
                    LEFT JOIN branches AS b1 ON ct.`from_branch_id` = b1.id
                    LEFT JOIN branches AS b2 ON ct.`to_branch_id` = b2.id
                    LEFT JOIN classes AS cl1 ON e.`class_id` = cl1.cls_id
                    LEFT JOIN classes AS cl2 ON ct.`to_class_id` = cl2.cls_id
                    LEFT JOIN semesters AS sem ON sem.id = ct.semester_id
                    LEFT JOIN term_student_user AS ts ON ts.student_id = s.id
                    LEFT JOIN users AS u1 ON ct.creator_id = u1.id
                    LEFT JOIN users AS u2 ON ct.from_approver_id = u2.id
                    LEFT JOIN users AS u3 ON ct.to_approver_id = u3.id
                $strWhere";

        $rs     = DB::select(DB::raw($query));
        $total  = DB::select(DB::raw($queryCount));
        $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;
//        if ($rs) {
//            foreach ($rs as $k => $v) {
//                $v->from_effect_id = '';
//                $fromEffectID = array();
//                $beforeUsedUserId = $v->used_student_ids ? explode(',', $v->used_student_ids) : null;
//                if ($beforeUsedUserId) {
//                    foreach ($beforeUsedUserId as $key => $value) {
//                        if(strpos($value, 'HV') !== false && $value != $v->to_effect_id) {
//                            $fromEffectID[] = $value;
//                        }
//                    }
//                }
//                if ($fromEffectID) {
//                    $v->from_effect_id = implode(',', $fromEffectID);
//                }
//            }
//        }
        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $totalRecord
        );
        return $return;
    }

    public static function students($request)
    {
        ini_set('max_execution_time', 300);
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $fd = self::$fd;
        $td = self::$td;
        $branchIds = self::$branchIds;
        $keyword   = self::$keyword;
        $productIds = self::$productIds;

        if ($fd && $td) {
            $where[] = "s.created_at >= '$fd' AND s.created_at <= '$td'";
        }
//        if ($productIds) {
//            $where[] = "c.product_id IN ($productIds)";
//        }
        if ($keyword) {
            $where[] = "(s.crm_id = '$keyword' 
                        OR s.accounting_id = '$keyword' 
                        OR s.name LIKE '%$keyword%' 
                        OR u1.full_name LIKE '%$keyword%' 
                        OR u2.full_name LIKE '%$keyword%' 
                        OR u3.full_name LIKE '%$keyword%'
                        OR u1.username LIKE '%$keyword%' 
                        OR u2.username LIKE '%$keyword%'
                        OR u3.username LIKE '%$keyword%'
                        )";
        }
        if ($request->type_student !== null) {
            $where[] = "s.type = $request->type_student";
        }
        if ($request->status_student !== null) {
            $where[] = "s.status = $request->status_student";
        }
        if ($branchIds) {
            $where[] = "s.branch_id IN ($branchIds)";
        }
        if ($where) {
            $strWhere = ' WHERE ' . implode(' AND ', $where);
        }

        $query = "
                SELECT
                    DISTINCT (s.`crm_id`) AS crm_id,
                    s.`accounting_id`,
                    s.`name` AS student_name,
                    s.`branch_id`,
                    s.`type`,
                    s.`gud_name1`,
                    s.`gud_mobile1`,
                    s.`gud_email1`,
                    s.`gud_name2`,
                    s.`gud_mobile2`,
                    s.`gud_email2`,
                    s.`status`,
                    b.`name` AS branch_name,
                    u1.`full_name` AS ec_name,
                    u2.`full_name` AS cm_name,
                    u3.`full_name` AS ec_leader_name,
                    cl.cls_name AS class_name,
                    sem.name AS semester_name,
                    c.enrolment_real_sessions AS real_sessions,
                    c.enrolment_start_date AS  start_date,
                    c.enrolment_end_date AS last_date,
                    (SELECT type FROM contracts WHERE student_id = s.id ORDER BY count_recharge DESC, end_date DESC LIMIT 1) AS contract_type,
                    (SELECT status FROM contracts WHERE student_id = s.id ORDER BY count_recharge DESC, end_date DESC  LIMIT 1) AS contract_status
                FROM
                    students AS s                    
                    LEFT JOIN branches AS b ON s.`branch_id` = b.`id`
                    LEFT JOIN contracts AS c ON c.student_id = s.id
                    LEFT JOIN classes AS cl ON c.class_id = cl.id
                    LEFT JOIN semesters AS sem ON cl.sem_id = sem.id
                    LEFT JOIN term_student_user AS su ON s.`id` = su.`student_id`
                    LEFT JOIN users AS u1 ON su.`ec_id` = u1.`id`
                    LEFT JOIN users AS u2 ON su.`cm_id` = u2.`id`
                    LEFT JOIN users AS u3 ON u1.`superior_id` = u3.`hrm_id`
                $strWhere
                ORDER BY s.id DESC
                $strLimit";

        $queryCount = "
                    SELECT COUNT(1) AS total
                    FROM students AS s
                    LEFT JOIN branches AS b ON s.`branch_id` = b.`id`
                    LEFT JOIN term_student_user AS su ON s.`id` = su.`student_id`
                    LEFT JOIN users AS u1 ON su.`ec_id` = u1.`id`
                    LEFT JOIN users AS u2 ON su.`cm_id` = u2.`id`
                    LEFT JOIN users AS u3 ON u1.`superior_id` = u3.`hrm_id`
                $strWhere";

        $rs     = DB::select(DB::raw($query));
        $total  = DB::select(DB::raw($queryCount));
        $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;
        if ($rs) {
            foreach ($rs as $k => $v) {
                switch ($v->contract_status) {
                    case 1:
                        $v->contract_status_name = 'Đã active nhưng chưa đóng phí';
                        break;
                    case 2:
                        $v->contract_status_name = 'Đã active và đặt cọc nhưng chưa thu đủ phí';
                        break;
                    case 3:
                        $v->contract_status_name = 'Nhận chuyển phí';
                        break;
                    case 4:
                        $v->contract_status_name = 'Nhận chuyển trung tâm';
                        break;
                    case 5:
                        $v->contract_status_name = 'Đã active và đã thu đủ phí';
                        break;
                    case 6:
                        $v->contract_status_name = 'VIP';
                        break;
                    case 7:
                        $v->contract_status_name = 'Đã Withraw';
                        break;
                    case 0:
                        $v->contract_status_name = 'Tiềm năng';
                        break;
                    default:
                        $v->contract_status_name = '';
                        break;
                }
                $v->type_name = $v->type == 1 ? 'VIP' : 'Thường';
                if (null == $v->contract_type) {
                    $v->contract_type_name = '';
                } else {
                    switch ($v->contract_type) {
                        case 1:
                            $v->contract_type_name = 'Chính thức';
                            break;
                        case 2:
                            $v->contract_type_name = 'Tái phí bình thường';
                            break;
                        case 3:
                            $v->contract_type_name = 'Tái phí do nhận chuyển phí';
                            break;
                        case 4:
                            $v->contract_type_name = 'Chỉ nhận chuyển phí';
                            break;
                        case 5:
                            $v->contract_type_name = 'Chuyển trung tâm';
                            break;
                        case 6:
                            $v->contract_type_name = 'Chuyển lớp';
                            break;
                        case 7:
                            $v->contract_type_name = 'Sinh ra do tái phí chưa đủ phí';
                            break;
                        case 8:
                            $v->contract_type_name = 'Được nhận học bổng';
                            break;
                        case 0:
                            $v->contract_type_name = 'Học thử';
                            break;
                        default:
                            $v->contract_type_name = '';
                            break;
                    }
                }
            }
        }
        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $totalRecord
        );
        return $return;
    }

    //Report 09
    public static function studentWithdraw($request) {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $fd = $request->fd;
        if (!$fd) {
            $fd = date('Y-m-d',strtotime('-30 days'));
        }
        $td = $request->td;
        if (!$td) {
            $td = date('Y-m-d');
        }

        $listBranchs = $request->listBranchs;
        $listProducts = $request->listProducts;

        $strBranchIds = '';
        $strProductIds = '';

        if ($listBranchs) {
            $strBranchIds = implode(',', $listBranchs);
        }

        if ($listProducts) {
            $strProductIds = implode(',', $listProducts);
        }

        //Default branchIds
        if (!$listBranchs) {
            $strBranchIds = self::$branchIds;
        }

        $ecId = self::$ecId;
        $cmId = self::$cmId;

        //$where[] = 'e.`status` = 0 and c.enrolment_id > 0';
        $where = array();
        if ($cmId) {
            $where[] = "c.`cm_id` IN ( $cmId )";
        }

        if ($ecId) {
            $where[] = "c.`ec_id` IN ( $ecId )";
        }

        if ($fd && $td) {
            $where[] = "c.enrolment_withdraw_date BETWEEN '$fd' AND '$td'";
        }

        if ($strBranchIds) {
            $where[] = "b.id IN ($strBranchIds)";
        }

        if ($strProductIds) {
            $where[] = "c.`product_id` IN ($strProductIds)";
        }

        if ($where) {
            $strWhere = ' WHERE ' . implode(' AND ', $where);
        }

        $queryTemp = "
            SELECT  s.`stu_id` AS lms_id,
                    s.`effect_id`,
                    s.`name`,
                    c.`enrolment_withdraw_date` AS withdraw_date,
                    c.`enrolment_last_date` AS last_date,
                    c.`enrolment_withdraw_reason` AS withdraw_reason,
                    cl.`cls_name` AS class_name,
                    b.`name` AS branch_name,
                    ec.username AS ec_name,
                    cm.username AS cm_name,
                    p1.name AS product_name,
                    p2.name AS program_name
            FROM `contracts` c
            INNER JOIN
            (       
                SELECT DISTINCT
                (SELECT id
                 FROM contracts D
                 WHERE D.student_id = KQ2.student_id
                   AND D.count_recharge = KQ2.count_recharge
                   AND D.end_date = KQ2.end_date
                 LIMIT 1) AS id
              FROM
                (SELECT *,   
                   (SELECT MAX(end_date)
                    FROM contracts C
                    WHERE C.student_id = KQ1.student_id
                      AND C.count_recharge = KQ1.count_recharge ) AS end_date
                 FROM
                   (SELECT student_id,
                      (SELECT MAX(count_recharge)
                       FROM contracts B
                       WHERE B.student_id = A.student_id) AS count_recharge
                    FROM contracts A
                    WHERE A.`type` > 0) AS KQ1) AS KQ2
            )  AS ctTemp ON c.`id` = ctTemp.id
            INNER JOIN `students` s ON c.`student_id` = s.`id`
            INNER JOIN `branches` b ON c.`branch_id` = b.`id`
            INNER JOIN `classes` cl ON c.`class_id` = cl.`id`
            LEFT JOIN `users` cm ON c.`cm_id` = cm.`id`
            LEFT JOIN `users` ec ON c.`ec_id` = ec.`id`
            LEFT JOIN products AS p1 ON c.product_id = p1.id
            LEFT JOIN programs AS p2 ON c.program_id = p2.id
            $strWhere";

        $queryCount = "SELECT COUNT(1) AS total FROM ($queryTemp) AS temp";

        $query = "$queryTemp ORDER BY b.`id` $strLimit";

        $rs     = DB::select(DB::raw($query));
        $total  = DB::select(DB::raw($queryCount));
        $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;

        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $totalRecord
        );
        return $return;
    }
    /**
    * Get Info Branches
    * @type 0: report 20 ; 1: report 20b;
    */
    public static function branchesInfo($request,$type=1){
        $params = self::params($request,$request->users_data);
        // dd($params);
        $limit = (int)$params->l;
        $page   = (int)$params->p;
        $offset = (int)$params->d;
        $strLimit = '';
        $strLimit = ($limit && ($offset !== null)) ? " LIMIT $offset, $limit" : '';
        $where    = [];
        $strWhere = '';
        $fd = $params->f;
        $td = $params->t;
        // $branchIds = self::$branchIds;
        $branchIds = $params->s;
        $validFromdate = strtotime($fd);
        $validTodate = strtotime($td);
        // dd($branchIds);
        $whereBrc = '';
        if($branchIds){
            $whereBrc = " And b.id in ($branchIds)";
        }

        if( $validFromdate and $validTodate ) {
            $whereCm2m = date('Y-m-d', strtotime(' -2 month',strtotime($fd)));
            $from_dof_date = date('Y-m-d',strtotime($fd));
            $to_dof_date   = date('Y-m-d',strtotime($td));
            $from_dof_date_prev  = date('Y-m-01', strtotime(' -1 month',strtotime($fd)));
            $to_dof_date_prev    = date('Y-m-t', strtotime(' -1 month', strtotime($td)));
        } else {
            $whereCm2m = date('Y-m-d', strtotime(' -2 month'));
            $from_dof_date = date('Y-m-01');
            $to_dof_date = date('Y-m-t');
            $from_dof_date_prev = date('Y-m-01', strtotime(' -1 month'));
            $to_dof_date_prev     = date('Y-m-t', strtotime(' -1 month'));
        }
        $productList = [
            'igarten' => '1', 'april' => '2', 'cdi40' => '3'
        ];
        $arr_field = $type ? ['id','branch_id','cm_id'] : ['id','branch_id'];
        $sqlStudentActive = Student::sqlCountStudentFullfeeActive($from_dof_date, $to_dof_date, '');
        $sqlClasses = LmsClass::sqlGetClasses($arr_field,$from_dof_date, $to_dof_date, '');
        $sqlClassesPResult = $sqlStudentPResult = $sqlClassesPResultLess = $sqlClassesPResultPrev = $sqlStudentPResultPrev = '';

        foreach( $productList as $pKey => $pID ) {
          // Classes
            $where = " AND tpp.product_id = $pID";
            $sqlClassesP = LmsClass::sqlGetClasses($arr_field,$from_dof_date,$to_dof_date, $where, true);
            $sqlClassesPResult .= $type ? " ( select count(*) from ($sqlClassesP) as cl where cl.cm_id = u.id ) as cls_$pKey ," : " ( select count(*) from ($sqlClassesP) as cl where cl.branch_id = b.id ) as cls_$pKey ,";
            // ClassesPrev
            $where = " AND tpp.product_id = $pID";
            $sqlClassesPPrev = LmsClass::sqlGetClasses($arr_field,$from_dof_date_prev,$to_dof_date_prev, $where, true);
            $sqlClassesPResultPrev .= $type ? " ( select count(*) from ($sqlClassesPPrev) as cl where cl.cm_id = u.id ) as cls_prev_$pKey ," : " ( select count(*) from ($sqlClassesPPrev) as cl where cl.branch_id = b.id ) as cls_prev_$pKey ,";

            //Students
            $wherePStudent = " AND ct.product_id = $pID";
            $sqlStudentPActive = Student::sqlCountStudentFullfeeActive($from_dof_date, $to_dof_date, $wherePStudent);
            $sqlStudentPResult .= $type ? "( select count(*) from ( $sqlStudentPActive ) as st2 where st2.cm_id = u.id ) as student_$pKey ," : "( select count(*) from ( $sqlStudentPActive ) as st2 where st2.b_id = b.id ) as student_$pKey ,";

            //Students Prev
            $wherePStudent = " AND ct.product_id = $pID";
            $sqlStudentPActivePrev = Student::sqlCountStudentFullfeeActive($from_dof_date, $to_dof_date, $wherePStudent);
            $sqlStudentPResultPrev .= $type ? "( select count(*) from ( $sqlStudentPActivePrev ) as st2 where st2.cm_id = u.id ) as student_prev_$pKey ," : "( select count(*) from ( $sqlStudentPActivePrev ) as st2 where st2.b_id = b.id ) as student_prev_$pKey ,";

            //Classes Less
            $whereLess = " AND tpp.product_id = $pID";
            $sqlClassesPLess = LmsClass::sqlGetClasses($arr_field,$from_dof_date,$to_dof_date, $whereLess, true, true);
            $sqlClassesPResultLess .= $type ? " ( select count(*) from ($sqlClassesPLess) as cl where cl.cm_id = u.id ) as cls_less5_$pKey ," : " ( select count(*) from ($sqlClassesPLess) as cl where cl.branch_id = b.id ) as cls_less5_$pKey ,";

        }

        $stPending = $type ? Student::sqlCountStudentFullfeePending($to_dof_date,'AND c.cm_id = u.id') : Student::sqlCountStudentFullfeePending($to_dof_date,'AND p.branch_id = b.id');

        $stPendingPrev = $type ? Student::sqlCountStudentFullfeePending($to_dof_date_prev,'AND c.cm_id = u.id') : Student::sqlCountStudentFullfeePending($to_dof_date_prev,'AND p.branch_id = b.id');

        $whereRenew = $type ? " AND c.cm_id = u.id" : " AND s.branch_id = b.id";
        $renew     = Student::sqlCountRenewStudent($from_dof_date, $to_dof_date, $whereRenew);
        $renewSuccess     = Student::sqlCountRenewStudent($from_dof_date, $to_dof_date, $whereRenew, true);
        $renewPrev = Student::sqlCountRenewStudent($from_dof_date_prev, $to_dof_date_prev, $whereRenew);
        $renewPrevSuccess = Student::sqlCountRenewStudent($from_dof_date_prev, $to_dof_date_prev, $whereRenew);

        $sql = !$type ?
        "
        SELECT
          b.id as b_id,
          b.zone_id as b_zone,
          b.name as b_name,
          b.region_id as region_id,
          ( select count(*) from ( $sqlStudentActive ) as st2 where st2.b_id = b.id ) as all_student ,
          ( select count(*) from ( $sqlClasses ) as cl where cl.branch_id = b.id ) as all_cls,
          $sqlClassesPResult
          $sqlStudentPResult
          $sqlClassesPResultLess
          $sqlClassesPResultPrev
          $sqlStudentPResultPrev
          ($renew) as renew,
          ($renewSuccess) as renew_success,
          ($renewPrev) as renew_prev,
          ($renewPrevSuccess) as renew_prev_success,
          ($stPending) as student_pending,
          ($stPendingPrev) as student_pending_prev,
          (
            select count(*) from users as u
            left join term_user_branch as tub on tub.user_id = u.id
            where tub.branch_id = b.id and tub.role_id = 55
          ) as all_cm,
          (
            select count(*) from users as u
            left join term_user_branch as tub on tub.user_id = u.id
            where tub.branch_id = b.id and tub.role_id = 55 and u.start_date >= '$whereCm2m'
          ) as cm2m
          FROM
          branches as b
          WHERE b.status = 1 $whereBrc
          $strLimit
        " :
        "
          SELECT
            b.id as b_id,
            b.zone_id as b_zone,
            b.name as b_name,
            u.full_name as full_name,
            u.id as u_id,
            u.hrm_id as hrm_id,
            b.region_id as region_id,
            ( select count(*) from ( $sqlStudentActive ) as st2 where st2.cm_id = u.id ) as all_student ,
            ( select count(*) from ( $sqlClasses ) as cl where cl.cm_id = u.id ) as all_cls,
            $sqlClassesPResult
            $sqlStudentPResult
            $sqlClassesPResultLess
            $sqlClassesPResultPrev
            $sqlStudentPResultPrev
            ($renew) as renew,
            ($renewSuccess) as renew_success,
            ($renewPrev) as renew_prev,
            ($renewPrevSuccess) as renew_prev_success,
            ($stPending) as student_pending,
            ($stPendingPrev) as student_pending_prev

            FROM users AS u
            LEFT JOIN term_user_branch as tub on tub.user_id = u.id
            LEFT JOIN branches as b on b.id = tub.branch_id
            WHERE tub.role_id in (55,56)
            AND b.status = 1 $whereBrc
            $strLimit
            ";

        $queryCount = !$type ?
            "
            SELECT COUNT(1) AS total
            FROM
            branches as b
            WHERE b.status = 1 $whereBrc
            " :
            "
            SELECT COUNT(1) AS total
            FROM
            users AS u
            LEFT JOIN term_user_branch as tub on tub.user_id = u.id
            LEFT JOIN branches as b on b.id = tub.branch_id
            WHERE tub.role_id in (55,56)
            AND b.status = 1
            $whereBrc
            ";
        $data_map = null;
        $totalRecord = 0;
        // echo $sql;die;
        try{
            $rs     = DB::select(DB::raw($sql));
            $total  = DB::select(DB::raw($queryCount));
            $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;
            if(!empty($rs)){
                $data_map = array_map(function($item){
                    $item->cls_asc_april_prev = ($item->student_prev_april > 0 && $item->cls_prev_april > 0) ? round($item->student_prev_april / $item->cls_prev_april,2) : 0;
                    $item->cls_asc_igarten_prev = ($item->student_prev_igarten > 0 && $item->cls_prev_igarten > 0) ? round($item->student_prev_igarten / $item->cls_prev_igarten,2) : 0;
                    $item->cls_asc_cdi40_prev = ($item->student_prev_cdi40 > 0 && $item->cls_prev_cdi40 > 0 ) ? round($item->student_prev_cdi40 / $item->cls_prev_cdi40,2) : 0;
                    $item->cls_asc_april = ( $item->student_april > 0 && $item->cls_april > 0 ) ? round($item->student_april / $item->cls_april,2) : 0;
                    $item->cls_asc_igarten = ($item->student_igarten > 0 && $item->cls_igarten > 0 ) ? round($item->student_igarten / $item->cls_igarten,2) : 0;
                    $item->cls_asc_cdi40 = ($item->student_cdi40 > 0 && $item->cls_cdi40 > 0) ? round($item->student_cdi40 / $item->cls_cdi40,2) : 0;
                    $item->ratioRenew = ( $item->renew_success > 0 && $item->renew > 0 ) ? round((($item->renew_success / $item->renew)*100),2) : 0;
                    $item->ratioRenewPrev = ( $item->renew_prev_success > 0 && $item->renew_prev > 0 ) ? round((($item->renew_prev_success / $item->renew_prev)*100),2) : 0;
                    $item->ratioPending = ( $item->student_pending > 0 && $item->student_pending_prev > 0 ) ? round((($item->student_pending / $item->student_pending_prev)*100),2) : 0;
                    return $item;
                },$rs);
            }
            // dd($data_map);
        }catch(Exception $exception){ throw $exception;}

        $return = array(
            'data' => $data_map ? $data_map : null,
            'total_record' => $totalRecord
        );
        return $return;
    }
    /**
    * Get data form18
    */
    public static function reportForm18($request){
        self::commons($request);
        $strLimit = self::$strLimit;

        $fd = self::$fd;
        $td = self::$td;
        $branches = self::$branchIds;
        $products = self::$productIds;

        $dateCompare = $request->date_compare ? $request->date_compare . '/01' : date('Y/m/1');
        $dateCompare = date('Y-m-d', strtotime($dateCompare));
        $strDateCompare = date('Y-m-d', strtotime($dateCompare));

        $datestring = "$strDateCompare first day of last month";
        $dt = date_create($datestring);
        $dateComparePrev =  $dt->format('Y-m-d');

        $datePrev = date("Y-m-t", strtotime($dateComparePrev));
        $dateCurr = date("Y-m-t", strtotime($dateCompare));

        $where = array();
        $where1 = array();
        $whereBr = array();

        $strWhereBranch = '';
        $ecId = self::$ecId;
        $cmId = self::$cmId;

        $where[] = "p.`count` = 1 AND c.`count_recharge` = 0";
        $where1[] = "c.`type` IN (4,6,8) AND c.`count_recharge` = 0 AND c.status != 1";

        if ($cmId) {
            $where[] = "c.cm_id IN ( $cmId )";
            $where1[] = "c.cm_id IN ( $cmId )";
        }

        if ($ecId) {
            $where[] = "c.ec_id IN ( $ecId )";
            $where1[] = "c.ec_id IN ( $ecId )";
        }

        if ($branches) {
            $whereBr[] = "br.id IN ($branches)";
        }

        if ($products) {
            $where[] = "c.product_id IN ($products)";
            $where1[] = "c.product_id IN ($products)";
        }

        if ($whereBr) {
            $strWhereBranch = " WHERE " . implode(' AND ', $whereBr);
        }
        $query = "SELECT br.name AS branch_name,
                         br.id AS branch_id
                  FROM branches AS br
                  $strWhereBranch
                  $strLimit";

        $queryCount = "SELECT count(1) AS total FROM `branches` AS br $strWhereBranch";
        try{
            $results = DB::select(DB::raw($query));
            if ($results) {
                foreach ($results as $k => $v) {
                    $totalStudent      = DB::select(DB::raw(self::countStudentByBranchId($v->branch_id, $dateCurr, $where, $where1)));
                    $totalStudentPrev  = DB::select(DB::raw(self::countStudentByBranchId($v->branch_id, $datePrev, $where, $where1)));
                    $v->total_student = isset($totalStudent[0]->total) ? $totalStudent[0]->total : 0;
                    $v->total_student_prev = isset($totalStudentPrev[0]->total) ? $totalStudentPrev[0]->total : 0;
                }
            }
            $total = DB::select(DB::raw($queryCount));
            $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;

        }catch(Exception $exception){
            throw $exception;
        }
        $return = array(
            'data' => $results,
            'total_record' => $totalRecord
        );
        return $return;
    }

    public static function countStudentByBranchId($branchId = 0, $date, $where = [], $where1 = [])
    {
        $strWhere = '';
        $strWhere1 = '';
        if ($where) {
            $strWhere = implode(' AND ', $where);
        }
        if ($where1) {
            $strWhere1 = implode(' AND ', $where1);
        }
        return $query = "SELECT  COUNT(DISTINCT(x.student_id) )  AS total FROM (
                            (
                                SELECT c.`student_id` AS student_id
                                FROM `payment` AS p
                                INNER JOIN `contracts` AS c ON c.`id` = p.`contract_id`
                                INNER JOIN `students` AS s ON c.`student_id` = s.`id`
                                INNER JOIN `branches` b ON c.`branch_id` = b.`id`
                                LEFT JOIN tuition_fee AS tf ON tf.id = c.`tuition_fee_id`
                                LEFT JOIN `enrolments` AS e ON c.`id` = e.`contract_id`
                                WHERE $strWhere
                                    AND p.`charge_date` <= '$date'
                                    AND b.id IN ($branchId)
                            )
                            UNION
                            (
                                SELECT c.`student_id` AS student_id
                                FROM `contracts` AS c
                                INNER JOIN `students` AS s ON c.`student_id` = s.`id`
                                INNER JOIN `branches` b ON c.`branch_id` = b.`id`
                                LEFT JOIN tuition_fee AS tf ON tf.id = c.`tuition_fee_id`
                                LEFT JOIN `enrolments` AS e ON c.`id` = e.`contract_id`
                                WHERE $strWhere1
                                    AND c.`updated_at` <= '$date'
                                    AND b.id IN ($branchId)
                            )
                        ) AS x";
    }
    /**
    * Get data form18
    */
    public static function reportForm19($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $strWhere = '';
        $fd = self::$fd;
        $td = self::$td;
        $branchIds = self::$branchIds;
        $branches = $branchIds;
        $products = self::$productIds;
        $date = $request->date ? $request->date : date('Y-m-d');
        $student_num = (int)$request->student_number ? (int)$request->student_number : 5;
        $keyword = self::$keyword;
        $ecId = self::$ecId;
        $cmId = self::$cmId;
        $strWhereRole = '';

        if ($cmId) {
            $strWhereRole .= " AND c.cm_id IN ( $cmId )";
        }

        if ($ecId) {
            $strWhereRole .= " AND c.ec_id IN ( $ecId )";
        }
        if ($branches) {
            $strWhere .= " AND b.id IN ($branches)";
        }
        if ($products) {
            $strWhere .= " AND pd.id IN ($products)";
        }
        if ($keyword) {
            $strWhere .= " AND cl.cls_name LIKE '%" . $keyword . "%'";
        }

        $sql = "
                SELECT
                    cl.id,
                    cl.cls_name AS class_name,
                    (
                        SELECT COUNT(*)
                        FROM contracts 
                        WHERE
                        class_id = cl.id
                        $strWhereRole
                        AND status IN (1, 2, 3 ,4, 5, 6 )
                    ) AS student_in_class,
                    b.name AS branch_name,
                    p.name AS program_name,
                    pd.name AS product_name
                FROM
                    classes AS cl
                    LEFT JOIN branches AS b ON b.id = cl.branch_id
                    LEFT JOIN programs AS p ON p.id = cl.program_id
                    LEFT JOIN term_program_product AS tpp ON tpp.program_id = p.id
                    LEFT JOIN products AS pd ON pd.id = tpp.product_id
                WHERE
                    cl.cls_iscancelled = 'no'
                    AND b.status = 1
                    $strWhere
                    AND cl.cls_startdate <= '$date' AND cl.cls_enddate >= '$date'
                GROUP BY cl.id
                $strLimit";

        $sql_count = "
                SELECT
                    cl.id,
                    cl.cls_name AS class_name,
                    (
                        SELECT COUNT(*)
                        FROM enrolments AS e
                        LEFT JOIN contracts AS c ON c.id = e.contract_id
                        WHERE
                        e.class_id = cl.id
                        $strWhereRole
                        AND (
                            CASE
                                WHEN e.final_last_date IS NOT NULL
                                THEN e.final_last_date <= '$date'
                            ELSE e.start_date <= '$date'
                            END
                        )
                        AND e.status = 1 AND c.status IN (1, 2, 3 ,4, 5, 6 )
                    ) AS student_in_class,
                    b.name AS branch_name,
                    p.name AS program_name,
                    pd.name AS product_name
                FROM
                    classes AS cl
                    LEFT JOIN branches AS b ON b.id = cl.branch_id
                    LEFT JOIN programs AS p ON p.id = cl.program_id
                    LEFT JOIN term_program_product AS tpp ON tpp.program_id = p.id
                    LEFT JOIN products AS pd ON pd.id = tpp.product_id
                WHERE
                    cl.cls_iscancelled = 'no'
                    AND b.status = 1
                    $strWhere
                    AND cl.cls_startdate <= '$date' AND cl.cls_enddate >= '$date'
                GROUP BY cl.id
                ";

        $results = null;
        $totalRecord = 0;
        try{
            $results = DB::select(DB::raw($sql));
            $total = DB::select(DB::raw($sql_count));
            $totalRecord = count($total) ? (int)count($total) : 0;

        }catch(Exception $exception){
            throw $exception;
        }
        $return = array(
            'data' => $results,
            'total_record' => $totalRecord
        );
        return $return;
    }

    public static function reportForm15($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $fd = $request->fd ? date('Y-m-d', strtotime($request->fd)) : null;
        $td = $request->td ? date('Y-m-d', strtotime($request->td)) : null;
        $keyword    = self::$keyword;
        $branchIds  = implode(',',$request->branch_ids?:[]);
        $productIds = implode(',',$request->product_ids?:[]);
        $ecId = self::$ecId;
        $cmId = self::$cmId;

        if ($cmId) {
            $where[] = "ts.cm_id IN ( $cmId )";
        }

        if ($ecId) {
            $where[] = "ts.ec_id IN ( $ecId )";
        }

        if ($td) {
            $where[] = "c.updated_at <= '$td'";
        }

        $where[] = 'c.type > 0';
        $where[] = 'c.debt_amount > 0';
        $where[] = 'c.payment_id > 0';

        if ($branchIds) {
            $where[] = "s.branch_id IN ( $branchIds )";
        }

        if ($productIds) {
            $where[] = "c.product_id IN ( $productIds )";
        }

        if ($keyword) {
            $where[] = "(s.stu_id = '$keyword' OR s.name LIKE '%$keyword%')";
        }

        if ($where) {
            $strWhere = " WHERE " . implode(' AND ', $where);
        }

        $query = "
                  SELECT c.id AS contract_id,
                        s.crm_id,
                        s.stu_id,
                        s.name AS student_name,
                        br.name AS branch_name,
                        pd.`name` AS product_name,
                        pr.`name` AS programs_name,
                        u1.username AS ec_name,
                        u2.username AS cm_name,
                        c.type AS contract_type,
                        c.total_charged,
                        c.must_charge,
                        c.total_discount,
                        c.debt_amount,
                        c.start_date,
                        c.end_date,
                        c.total_sessions,
                        c.real_sessions,
                        c.`status`,
                        c.payment_id,
                        c.after_discounted_fee,
                        c.discount_value,
                        c.tuition_fee_price,
                        c.done_sessions,
                        c.count_recharge,
                        c.reserved_sessions,
                        pd.NAME AS product_name,
                        pr.NAME AS program_name,
                        tf.NAME AS tuition_fee_name,
                        p.total AS total_amount_charged,
                        p.charge_date AS payment_date,
                        x.dates AS substract,
                        15 - x.dates AS left_dates
                  FROM contracts AS c
                      LEFT JOIN students AS s ON s.id = c.student_id
                      LEFT JOIN branches AS br ON br.id = c.branch_id
                      LEFT JOIN products AS pd ON pd.id = c.product_id
                      LEFT JOIN programs AS pr ON pr.id = c.program_id
                      LEFT JOIN tuition_fee AS tf ON tf.id = c.tuition_fee_id
                      LEFT JOIN payment AS p ON c.payment_id = p.id
                      LEFT JOIN term_student_user AS t1 ON t1.student_id = s.id
                      LEFT JOIN users AS u1 ON t1.ec_id = u1.id
                      LEFT JOIN users AS u2 ON t1.cm_id = u2.id
                      LEFT JOIN
                      (SELECT c.id, TIMESTAMPDIFF(DAY, p.charge_date, CURDATE()) AS dates
                          FROM contracts AS c
                          LEFT JOIN payment AS p ON c.payment_id = p.id
                          WHERE c.type > 0 AND c.debt_amount > 0 AND c.payment_id > 0
                      ) AS x ON x.id = c.id
                  $strWhere AND x.dates >=15
                  ORDER BY c.id DESC
                  $strLimit";
        $queryCount = "
                    SELECT COUNT(1) AS total
                    FROM  contracts AS c
                          LEFT JOIN students AS s ON s.id = c.student_id
                          LEFT JOIN branches AS br ON br.id = c.branch_id
                          LEFT JOIN products AS pd ON pd.id = c.product_id
                          LEFT JOIN programs AS pr ON pr.id = c.program_id
                          LEFT JOIN tuition_fee AS tf ON tf.id = c.tuition_fee_id
                          LEFT JOIN payment AS p ON c.payment_id = p.id
                          LEFT JOIN term_student_user AS t1 ON t1.student_id = s.id
                          LEFT JOIN users AS u1 ON t1.ec_id = u1.id
                          LEFT JOIN users AS u2 ON t1.cm_id = u2.id
                          LEFT JOIN
                          ( SELECT c.id, TIMESTAMPDIFF(DAY, p.charge_date, CURDATE()) AS dates
                            FROM contracts AS c
                            LEFT JOIN payment AS p ON c.payment_id = p.id
                            WHERE c.type > 0 AND c.debt_amount > 0 AND c.payment_id > 0
                          ) AS x ON x.id = c.id
                    $strWhere AND x.dates >=15
                    ORDER BY c.id DESC";

        $rs    = DB::select(DB::raw($query));
        $total = DB::select(DB::raw($queryCount));

        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $total[0]->total ? (int)$total[0]->total : 0
        );
        return $return;
    }

    public static function reportForm14($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $fd = self::$fd;
        $td = self::$td;
        $keyword    = self::$keyword;
        $branchIds  = self::$branchIds;
        $productIds = self::$productIds;
        $status = $request->status;
        $ecId = self::$ecId;
        $cmId = self::$cmId;

        if ($status !== null) {
            $where[] = "p.status = $status";
        }

        if ($fd && $td) {
            $where[] = "((p.start_date >= '$fd' AND p.start_date <= '$td' ) OR (p.end_date >= '$td' AND p.end_date <= '$fd' ))";
        }

        if ($cmId) {
            $where[] = "ts.cm_id IN ( $cmId )";
        }

        if ($ecId) {
            $where[] = "ts.ec_id IN ( $ecId )";
        }

        if ($branchIds) {
            $where[] = "p.branch_id IN ( $branchIds )";
        }
        if ($productIds) {
            $where[] = "c.product_id IN ( $productIds )";
        }
        if ($keyword) {
            $where[] = "(s.stu_id = '$keyword' OR s.name LIKE '%$keyword%' OR s.effect_id = '$keyword')";
        }

        if ($where) {
            $strWhere = " WHERE " . implode(' AND ', $where);
        }

        $query = "
                  SELECT DISTINCT (s.stu_id) AS lms_id,
                        s.name AS student_name,
                        s.effect_id AS effect_id,
                        b.name AS branch_name,
                        prd.name AS product_name,
                        prg.name AS program_name,
                        tf.name AS tuition_fee_name,
                        c.start_date AS start_date,
                        u1.full_name AS creator_name,
                        u2.full_name AS approver_name,
                        p.start_date AS pending_date,
                        p.end_date AS pending_end_date,
                        p.note AS note,
                        p.comment AS comment,
                        p.created_at AS created_at,
                        p.creator_id AS creator_id,
                        p.session AS session,
                        p.status AS status,
                        p.approved_at AS approved_at,
                        re.description as reason_name 
                  FROM pendings AS p
                  LEFT JOIN students AS s ON p.student_id = s.id
                  LEFT JOIN contracts AS c ON p.contract_id = c.id
                  LEFT JOIN branches AS b ON p.branch_id = b.id
                  LEFT JOIN products AS prd ON c.product_id = prd.id
                  LEFT JOIN programs AS prg ON c.program_id = prg.id
                  LEFT JOIN tuition_fee AS tf ON c.tuition_fee_id = tf.id
                  LEFT JOIN term_student_user AS ts ON ts.student_id = s.id
                  LEFT JOIN users AS u1 ON u1.id = p.creator_id
                  LEFT JOIN users AS u2 ON u2.id = p.approver_id
                  LEFT JOIN reasons AS re ON p.reason_id = re.id
                  $strWhere
                  ORDER BY p.created_at DESC
                  $strLimit";

        $queryCount = "
                    SELECT COUNT(1) AS total
                    FROM pendings AS p
                    LEFT JOIN students AS s ON p.student_id = s.id
                    LEFT JOIN contracts AS c ON p.contract_id = c.id
                    LEFT JOIN branches AS b ON p.branch_id = b.id
                    LEFT JOIN products AS prd ON c.product_id = prd.id
                    LEFT JOIN programs AS prg ON c.program_id = prg.id
                    LEFT JOIN tuition_fee AS tf ON c.tuition_fee_id = tf.id
                    LEFT JOIN term_student_user AS ts ON ts.student_id = s.id
                    LEFT JOIN users AS u1 ON u1.id = p.creator_id
                    LEFT JOIN users AS u2 ON u2.id = p.approver_id
                    LEFT JOIN reasons AS re ON p.reason_id = re.id
                    $strWhere
                    ORDER BY p.created_at DESC
                    ";

        $rs    = DB::select(DB::raw($query));
        $total = DB::select(DB::raw($queryCount));
        if ($rs) {
            foreach ($rs as $k => $v) {
                $start = date_create($v->pending_date);
                $end   = date_create($v->pending_end_date);
                $diff  = date_diff($start, $end);
                $v->total_pending_session = $diff->format('%a');
                $status = $v->status;
                switch ($status) {
                    case 1:
                        $v->status_name = 'Đã duyệt';
                        break;
                    case 2:
                        $v->status_name = 'Từ chối';
                        break;
                    case 3:
                        $v->status_name = 'Đã xóa';
                        break;
                    case 0:
                    default:
                        $v->status_name = 'Chờ duyệt';
                        break;
                }
            }
        }
        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => isset($total[0]->total) ? (int)$total[0]->total : 0
        );
        return $return;
    }

    public static function reportForm03($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $strWhere2 = '';
        $fd = $request->date;
        if (!$fd) {
            $fd = date('Y-m-d');
        }
        else
        {
            $fd = date('Y-m-d', strtotime($fd)) . " 23:59:59";
        }
        $listBranchs = $request->listBranchs;
        $listProducts = $request->listProducts;
        $listTypeContract = $request->listTypeContract;
        $listTypeCustomer = $request->listTypeCustomer;
        $listTypeFee = $request->listTypeFee;

        $strBranchIds = '';
        $strProductIds = '';
        $strTypeContract = '';
        $strTypeCustomer = '';
        $strTypeFee = '';

        if ($listBranchs) {
            $strBranchIds = implode(',', $listBranchs);
        }

        if ($listProducts) {
            $strProductIds = implode(',', $listProducts);
        }

        if($listTypeContract) {
            $strTypeContract = implode(',', $listTypeContract);
        }

        if($listTypeCustomer) {
            $strTypeCustomer = implode(',', $listTypeCustomer);
        }

        if($listTypeFee) {
            $strTypeFee = implode(',', $listTypeFee);
        }

        //Default branchIds
        if (!$listBranchs) {
            $strBranchIds = self::$branchIds;
        }

        $where[] = "p.`count` = 1 AND c.`count_recharge` = 0";

        $where2[] = "c.`type` IN (4,6,8) AND c.`count_recharge` = 0 AND c.status != 1";

        if ($fd) {
            $where[] = "YEAR(p.`charge_date`) = YEAR('$fd') AND MONTH(p.`charge_date`) = MONTH('$fd')";
            $where2[] = "YEAR(c.`updated_at`) = YEAR('$fd') AND MONTH(c.`updated_at`) = MONTH('$fd')";
        }

        if ($strBranchIds) {
            $where[] = "b.id IN ($strBranchIds)";
            $where2[] = "b.id IN ($strBranchIds)";
        }

        if ($strProductIds) {
            $where[] = "c.product_id IN ($strProductIds)";
            $where2[] = "c.product_id IN ($strProductIds)";

        }

        if($strTypeCustomer != null) {
            $where[] = "s.type in ($strTypeCustomer)";
            $where2[] = "s.type in ($strTypeCustomer)";
        }

        if($strTypeContract) {
            $where[] = "c.type in ($strTypeContract)";
            $where2[] = "c.type in ($strTypeContract)";
        }

        if($strTypeFee) {
            $where[] = "c.status in ($strTypeFee)";
            $where2[] = "c.status in ($strTypeFee)";
        }

        if ($where) {
            $strWhere = ' AND ' . implode(' AND ', $where);
        }

        if ($where2) {
            $strWhere2 = ' AND ' . implode(' AND ', $where2);
        }

        $queryTemp = "
                    (SELECT  c.`student_id` AS s_id,
                            b.`name` AS branch_name,
                            s.`name` AS student_name,
                            s.`accounting_id`,
                            s.`stu_id` AS lms_id,
                            IF(s.type=1, 'VIP', 'Bình thường') AS customer_type,
                            s.type AS customer_type_id,
                            tf.`name` AS tuition_fee_name,
                            ec.full_name AS ec_name,
                            cm.full_name AS cm_name,
                            p1.name AS product_name,
                            `cl`.`cls_name` AS class_name,
                            p.`must_charge`,
                            p.`amount` AS total_charged ,
                            p.`debt` AS debt,
                            p.`created_at` AS payment_date,
                            c.status,
                            c.type
                    FROM `payment` AS p
                    INNER JOIN `contracts` AS c ON c.`id` = p.`contract_id`
                    INNER JOIN `students` AS s ON c.`student_id` = s.`id`
                    INNER JOIN `branches` b ON c.`branch_id` = b.`id`
                    LEFT JOIN tuition_fee AS tf ON tf.id = c.`tuition_fee_id`
                    LEFT JOIN `enrolments` AS e ON c.`id` = e.`contract_id`
                    LEFT JOIN `classes` AS cl ON e.`class_id` = cl.`id`
                    INNER JOIN `users` cm ON c.`cm_id` = cm.`id`
                    INNER JOIN `users` ec ON c.`ec_id` = ec.`id`
                    INNER JOIN products AS p1 ON c.product_id = p1.id
                    $strWhere)
                    UNION
                    (SELECT c.`student_id` AS s_id,
                        b.`name` AS branch_name,
                        s.`name` AS student_name,
                        s.`accounting_id`,
                        s.`stu_id` AS lms_id,
                        IF(s.type=1, 'VIP', 'Bình thường') AS customer_type,
                        s.type AS customer_type_id,
                        tf.`name` AS tuition_fee_name,
                        ec.full_name AS ec_name,
                        cm.full_name AS cm_name,
                        p1.name AS product_name,
                        `cl`.`cls_name` AS class_name,
                        c.`must_charge`,
                        c.`total_charged`,
                        c.`debt_amount` AS debt,
                        c.`updated_at` AS payment_date,
                        c.status,
                        c.type
                    FROM `contracts` AS c
                    INNER JOIN `students` AS s ON c.`student_id` = s.`id`
                    INNER JOIN `branches` b ON c.`branch_id` = b.`id`
                    LEFT JOIN tuition_fee AS tf ON tf.id = c.`tuition_fee_id`
                    LEFT JOIN `enrolments` AS e ON c.`id` = e.`contract_id`
                    LEFT JOIN `classes` AS cl ON e.`class_id` = cl.`id`
                    INNER JOIN `users` cm ON c.`cm_id` = cm.`id`
                    INNER JOIN `users` ec ON c.`ec_id` = ec.`id`
                    INNER JOIN products AS p1 ON c.product_id = p1.id
                    $strWhere2)
        ";

        $queryCount = "SELECT COUNT(1) AS total FROM ($queryTemp) AS temp";

        $query = "$queryTemp ORDER BY s_id $strLimit";
        //echo $query;exit();
        $rs     = DB::select(DB::raw($query));
        $total  = DB::select(DB::raw($queryCount));
        $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;

        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $totalRecord
        );

        return $return;
    }

  public static function reportForm06($request)
  {
    self::commons($request);


    $from_date = $request->fromDate;
    $to_date = $request->toDate;


    if (!$from_date) {
      $from_date = date('Y-m-01 00:00:00');
    }else{
      $from_date = date('Y-m-d',strtotime($from_date)) . " 00:00:00";
    }

    if (!$to_date) {
      $to_date = date('Y-m-d 23:59:59');
      $pending_date = date('Y-m-d 00:00:00');
    }else{
      $to_date = date('Y-m-d',strtotime($to_date)) . " 23:59:59";
      $pending_date = date('Y-m-d',strtotime($to_date)) . " 00:00:00";
    }

    $branch_ids = $request->branch_ids;
    $product_ids = $request->product_ids;
    $student_type_ids = $request->customer_type_ids;
    if(empty($branch_ids)){
      $branch_ids = u::getBranchIds($request->users_data);
    }

    $where = "";

    if ($branch_ids) {
      $branch_ids_string = implode(',', $branch_ids);
      $where .= " AND t.branch_id IN( $branch_ids_string ) ";
    }
    if ($product_ids) {
      $product_ids_string = implode(',', $product_ids);
      $where .= " AND t.product_id IN ( $product_ids_string ) ";
    }

    if ($student_type_ids) {
      $student_type_ids_string = implode(',', $student_type_ids);
      $where .= " AND t.student_type in ( $student_type_ids_string ) ";
    }

    $q = "SELECT s.`id` AS student_id, c.id AS contract_id, (0 - t.type) AS student_type, t.name AS student_type_name,
                    c.product_id AS product_id, s.created_at as created_at, s.branch_id as branch_id, c.program_id as program_id,
                    c.enrolment_id AS enrolment_id, s.name AS student_name, s.stu_id AS lms_id, s.accounting_id AS accounting_id
                FROM
                    students AS s
                    LEFT JOIN contracts AS c ON c.`student_id` = s.`id`
                    LEFT JOIN enrolments AS e ON c.`enrolment_id` = e.`id`
                    LEFT JOIN pendings AS p ON c.`id` = p.`contract_id`
                    LEFT JOIN (
                        SELECT -1 AS `type`, 'Chính thức' AS `name`
                        UNION SELECT -2 AS `type`, 'Học thử' AS `name`
                        UNION SELECT -3 AS `type`, 'Withdraw' AS `name`
                        UNION SELECT -4 AS `type`, 'Pending' AS `name`
                        UNION SELECT -5 AS `type`, 'Đặt cọc' AS `name`
                        UNION SELECT -6 AS `type`, 'Tiềm năng' AS `name`
                    ) AS t ON s.id <> t.type
                WHERE
                    s.`branch_id` IN ($branch_ids_string)
                    AND (
                        (c.id IS NULL AND t.type = -6)
                        OR
                        (
                            c.id IS NOT NULL AND (c.`branch_id` = s.`branch_id`) AND (
                                (p.id IS NOT NULL AND p.status = 1 AND p.`end_date` >= '$pending_date' AND t.type = -4)
                                OR
                                (
                                    (p.id IS NULL OR p.status = 0 OR p.end_date < '$pending_date')
                                    AND
                                    (
                                        c.`id` IN (
                                            SELECT c.id
                                            FROM
                                            contracts AS c
                                            LEFT JOIN enrolments AS e ON c.id = e.contract_id
                                            LEFT JOIN (
                                                SELECT student_id, MAX(count_recharge) AS count_recharge, MAX(end_date) AS end_date
                                                FROM contracts AS c
                                                WHERE c.`branch_id` IN ($branch_ids_string) AND (c.`type` = 0 OR (c.`type` > 0 AND c.`total_charged` > 0)) GROUP BY c.`student_id`
                                            ) AS t ON c.`student_id` = t.student_id AND c.`count_recharge` = t.count_recharge AND c.end_date = t.end_date
                                            WHERE t.student_id IS NOT NULL
                                        )
                                        AND
                                        (
                                            (e.`status` = 0 AND t.type = -3)
                                            OR
                                            (e.status > 0 AND c.`type` = 0 AND t.type = -2)
                                            OR
                                            (
                                                (e.`id` IS NULL OR (e.`id` IS NOT NULL AND e.`status` > 0)) AND c.`real_sessions` > 0 AND c.type > 0
                                                AND
                                                (
                                                    (c.`debt_amount` > 0 AND t.type = -5)
                                                    OR
                                                    (c.`debt_amount` = 0 AND t.type = -1)
                                                )
                                            )

                                        )
                                    )
                                )
                            )
                        )
                    )
                GROUP BY s.id";
    $query = "
            SELECT
                t.lms_id AS lms_id,
                t.accounting_id AS accounting_id,
                t.student_name AS student_name,
                b.name AS branch_name,
                p1.name AS product_name,
                p2.name AS program_name,
                t.student_type AS student_type,
                cls.cls_name AS class_name,
                u1.username AS ec_name,
                u2.username AS cm_name,
                t.student_type_name AS student_type_name
            FROM
                ($q)  AS t
                LEFT JOIN branches AS b ON t.branch_id = b.id
                LEFT JOIN products AS p1 ON t.product_id = p1.id
                LEFT JOIN programs AS p2 ON t.program_id = p2.id
                LEFT JOIN enrolments AS e ON t.enrolment_id = e.id
                LEFT JOIN classes AS cls ON e.class_id = cls.id
                LEFT JOIN term_student_user AS tsf ON tsf.student_id = t.student_id
                LEFT JOIN users AS u1 ON tsf.ec_id = u1.id
                LEFT JOIN users AS u2 ON tsf.cm_id = u2.id
            WHERE
            (t.created_at BETWEEN '$from_date' AND '$to_date') $where ";


    $queryCount = "SELECT COUNT(1) AS total FROM ($query) AS temp";

    $strLimit = self::$strLimit;
    $total  = DB::select(DB::raw($queryCount));
    $query = "$query $strLimit";
    $rs     = DB::select(DB::raw($query));
    $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;
    $return = array(
      'list' => $rs ? $rs : null,
      'total' => $totalRecord,
      'limit' => $request->limit,
      'page' => $request->page
    );

    return $return;
  }


  public static function createQueryReport09($branch_ids, $products, $from_date, $to_date, $limit = null){
    $where = "";
    if(!empty($branch_ids)) {
      $branch_ids_string = is_array($branch_ids) ? implode(',', $branch_ids) : $branch_ids;
      $where .= " AND c.branch_id IN( $branch_ids_string ) ";
    }
    if (!empty($products)) {
      $product_ids_string = is_array($products) ? implode(',', $products): $products;
      $where .= " AND c.product_id IN ( $product_ids_string ) ";
    }
    $queryDate = "";
    if(!empty($from_date)){
      $queryDate .= "AND c.`enrolment_withdraw_date` >= '$from_date' ";
    }
    if(!empty($to_date)){
      $queryDate.=" AND c.`enrolment_withdraw_date` <= '$to_date' ";
    }

    $where_branch_ids = !empty($branch_ids_string)? "WHERE c.`branch_id` IN ($branch_ids_string)": "";
    $query = "SELECT
                s.`id` AS student_id,
                s.stu_id as lms_id,
                s.accounting_id,
                s.name as `name`,
                b.name AS branch_name,
                p1.name AS product_name,
                p2.name AS program_name,
                cls.cls_name AS class_name,
                ec.username AS ec_name,
                cm.username AS cm_name,
                c.enrolment_withdraw_date AS withdraw_date,
                c.enrolment_withdraw_reason AS withdraw_reason
            FROM
                contracts AS c
                LEFT JOIN enrolments AS e ON e.`contract_id` = c.`id`
                LEFT JOIN students AS s ON c.student_id = s.`id`
                LEFT JOIN branches AS b ON c.branch_id = b.id
                LEFT JOIN products AS p1 ON c.product_id = p1.id
                LEFT JOIN programs AS p2 ON c.program_id = p2.id
                LEFT JOIN classes AS cls ON c.class_id = cls.id
                LEFT JOIN users AS ec ON c.ec_id = ec.id
                LEFT JOIN users AS cm ON c.cm_id = cm.id
            WHERE
                c.status = 7 AND
                s.`branch_id` = c.`branch_id`
                AND c.`id` IN (
                    SELECT c.id
                    FROM
                    contracts AS c
                    LEFT JOIN enrolments AS e ON c.id = e.contract_id
                    LEFT JOIN (
                    SELECT student_id, MAX(count_recharge) AS count_recharge, MAX(end_date) AS end_date
                    FROM contracts AS c
                    $where_branch_ids GROUP BY c.`student_id`
                    ) AS t ON c.`student_id` = t.student_id AND c.`count_recharge` = t.count_recharge AND c.end_date = t.end_date
                    WHERE t.student_id IS NOT NULL
                )
                $where
                $queryDate
            GROUP BY s.`id`
        ";
    if(!empty($limit)){
      $query.=" $limit";
    }
    return $query;
  }

  public static function reportForm09($request)
  {
    self::commons($request);

    $branch_ids = $request->branches;
    $products = $request->products;
    $from_date = !empty($request->from_date)? date('Y-m-d',strtotime($request->from_date)): null;
    $to_date = !empty($request->to_date) ? date('Y-m-d',strtotime($request->to_date)): null;
    $query = self::createQueryReport09($branch_ids, $products, $from_date, $to_date);
    $queryCount = "SELECT COUNT(1) AS total FROM ($query) AS temp";
    $strLimit = self::$strLimit;
    $total  = DB::select(DB::raw($queryCount));
    $query = "$query $strLimit";
    $rs     = DB::select(DB::raw($query));
    $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;
    $return = array(
      'list' => $rs ? $rs : null,
      'total' => $totalRecord,
      'limit' => $request->limit,
      'page' => $request->page
    );

    return $return;
  }

    public static function reportForm13($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';

        $listBranchs = $request->branches;
        $listProducts = $request->products;
        $name = $request->name;

        $strBranchIds = '';
        $strProductIds = '';

        if ($listBranchs) {

            $branch_ids_term = [];
            foreach ($listBranchs as $key => $value)
            {
                array_push($branch_ids_term, $value['id']);
            }
            $listBranchs = $branch_ids_term;
            $strBranchIds = implode(',', $listBranchs);
        }

        if ($listProducts) {
            $strProductIds = implode(',', $listProducts);
        }

        //Default branchIds
        if (!$listBranchs) {
            $strBranchIds = self::$branchIds;
        }

        $where[] = "c.debt_amount > 0";

        if ($strBranchIds) {
            $where[] = "s.`branch_id` IN ($strBranchIds) AND c.branch_id = s.branch_id";
        }

        if ($strProductIds) {
            $where[] = "c.`product_id` IN ($strProductIds)";
        }

        if ($name) {
            $where[] = "s.stu_id = $name OR s.effect_id = $name OR s.`name` LIKE '%$name%'";
        }

        if ($where) {
            $strWhere = ' WHERE ' . implode(' AND ', $where);
        }

        $queryTemp = "
            SELECT
                s.name AS student_name,
                s.stu_id AS lms_id,
                s.accounting_id AS effect_id,
                b.name AS branch_name,
                prd.name AS product_name,
                prg.name AS program_name,
                tf.name AS tuition_fee_name,
                c.payload AS payload,
                c.`must_charge` AS must_charge,
                c.`total_charged` AS total_charged,
                c.debt_amount AS debt_amount,
                IF(p.id, p.created_at, '') AS newest_payment_time,
                IF(u.`id`, u.`username`, '') AS creator,
                c.tuition_fee_price AS tuition_fee_price,
                c.type
            FROM
                contracts AS c
                LEFT JOIN students AS s ON c.student_id = s.id
                LEFT JOIN branches AS b ON c.branch_id = b.id
                LEFT JOIN products AS prd ON c.product_id = prd.id
                LEFT JOIN programs AS prg ON c.program_id = prg.id
                LEFT JOIN tuition_fee AS tf ON c.tuition_fee_id = tf.id
                LEFT JOIN (
                    SELECT id, creator_id, created_at, contract_id FROM payment WHERE id IN (
                        SELECT MAX(id) FROM payment GROUP BY contract_id
                    )
                ) AS p ON c.id = p.contract_id
                LEFT JOIN users AS u ON p.creator_id = u.id
                LEFT JOIN enrolments AS e ON c.`enrolment_id` = e.`id`
            $strWhere
            GROUP BY c.id
        ";

        $queryCount = "SELECT COUNT(1) AS total FROM ($queryTemp) AS temp";

        $query = "$queryTemp ORDER BY c.id $strLimit";

        $rs     = DB::select(DB::raw($query));
        $total  = DB::select(DB::raw($queryCount));
        $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;

        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $totalRecord
        );

        return $return;
    }

    public static function reportForm10($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $fd = $request->fd;
        if (!$fd) {
            $fd = date('Y-m-d');
        }

        $listBranchs = $request->listBranchs;

        $strBranchIds = '';

        if ($listBranchs) {
            $strBranchIds = implode(',', $listBranchs);
        }

        //Default branchIds
        if (!$listBranchs) {
            $strBranchIds = self::$branchIds;
        }

        $where[] = "s.`group` = 'branch'";

        if ($fd) {
            $where[] = "YEAR(s.datetime) = YEAR('$fd') AND MONTH(s.datetime) = MONTH('$fd')";
        }

        if ($strBranchIds) {
            $where[] = "s.`branch_id` IN ($strBranchIds)";
        }

        if ($where) {
            $strWhere = ' WHERE ' . implode(' AND ', $where);
        }

        $query = "
            SELECT s.*,
                   b.`name` AS branch_name
            FROM `sales_report` AS s
                  INNER JOIN `branches` AS b ON s.`branch_id` = b.`id`
            $strWhere
            GROUP BY s.id desc
            $strLimit
        ";
        $queryCount = "SELECT COUNT(1) AS total FROM `sales_report` AS s $strWhere";
        $rs     = DB::select(DB::raw($query));
        $total  = DB::select(DB::raw($queryCount));
        $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;

        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $totalRecord
        );

        return $return;
    }

    public static function queryReport17a($params, $total = 0){
        if ($total) {
            $resp = "SELECT COUNT(t.region_id) AS total FROM (SELECT s.region_id FROM sales_report s WHERE s.`branch_id` IN ($params->s) AND s.`datetime` >= '$params->f 00:00:00' AND s.`datetime` <= '$params->t 23:59:59' AND s.note = 'total_charge_amount' GROUP BY s.`region_id`) t";
        } else {
            $lim = (isset($params->d) && isset($params->l)) ? "LIMIT $params->d, $params->l" : "";

            $resp = "SELECT SUM(t.amount) AS amount, COUNT(t.branch_id) AS total_branch,
                        (SELECT `name` FROM regions WHERE id = t.region_id) AS region_name
                    FROM
                        (
                        SELECT SUM(amount) AS amount, branch_id,
                            region_id
                        FROM
                            sales_report s
                        WHERE
                            s.`role_id` = 686868
                            AND branch_id IN ($params->s)
                            AND MONTH(s.`datetime`) >= MONTH('$params->f 00:00:00') AND YEAR(s.`datetime`) = YEAR('$params->f 00:00:00') 
                            AND MONTH(s.`datetime`) <= MONTH('$params->t 00:00:00') AND YEAR(s.`datetime`) = YEAR('$params->t 00:00:00') 
                            AND branch_id <> 100
                            AND s.note = 'total_charge_amount'
                        GROUP BY branch_id
                        ) t
                    GROUP BY t.region_id $lim";
        }
        return $resp;
    }

    public static function queryReport17b($params, $total = 0){
        if ($total) {
            $resp = "SELECT COUNT(t.branch_id) AS total FROM (SELECT branch_id FROM sales_report s WHERE s.`branch_id` IN ($params->s) AND s.`datetime` >= '$params->f 00:00:00' AND s.`datetime` <= '$params->t 23:59:59' AND s.note = 'total_charge_amount' GROUP BY s.`branch_id`) t";
        } else {
            $lim = (isset($params->d) && isset($params->l)) ? "LIMIT $params->d, $params->l" : "";
            $order = isset($params->order)?($params->order?(($params->order == 1)?"ORDER BY amount DESC":"ORDER BY amount ASC"):""):"";

            $resp = "SELECT SUM(amount) AS amount,
                        (SELECT `name` FROM branches WHERE id = s.branch_id) AS branch_name
                    FROM
                        sales_report s
                    WHERE
                        s.`role_id` = 686868
                        AND branch_id IN ($params->s)
                        AND MONTH(s.`datetime`) >= MONTH('$params->f 00:00:00') AND YEAR(s.`datetime`) = YEAR('$params->f 00:00:00') 
                        AND MONTH(s.`datetime`) <= MONTH('$params->t 00:00:00') AND YEAR(s.`datetime`) = YEAR('$params->t 00:00:00') 
                        AND s.branch_id <> 100
                        AND s.note = 'total_charge_amount'
                    GROUP BY branch_id
                     $order
                     $lim";
        }
        return $resp;
    }

    public static function queryReport17c($params, $total = 0){
        if ($total) {
            $resp = "SELECT COUNT(t.id) AS total FROM (SELECT COUNT(s.user_id) AS id FROM sales_report s WHERE s.`branch_id` IN ($params->s) AND role_id IN (68, 69) AND s.`datetime` >= '$params->f 00:00:00' AND s.`datetime` <= '$params->t 23:59:59' AND s.note = 'new_charge_amount' GROUP BY s.`user_id`) t";
        } else {
            $lim = (isset($params->d) && isset($params->l)) ? "LIMIT $params->d, $params->l" : "";
            $order = isset($params->order)?($params->order?(($params->order == 1)?"ORDER BY amount DESC":"ORDER BY amount ASC"):""):"";

            $resp = "SELECT SUM(amount) AS amount,
                        (SELECT `full_name` FROM users WHERE id = s.user_id) AS full_name,
                        (SELECT `name` FROM branches WHERE id = s.branch_id) AS branch_name,
                        (SELECT `name` FROM roles WHERE id = s.role_id) AS role_name,
                        (SELECT `hrm_id` FROM users WHERE id = s.user_id) AS hrm_id
                    FROM
                        sales_report s
                    WHERE
                        s.`role_id` IN (68, 69)
                        AND branch_id IN ($params->s)
                        AND s.`datetime` >= '$params->f 00:00:00'
                        AND s.`datetime` <= '$params->t 23:59:59'
                        AND s.branch_id <> 100
                        AND s.note = 'new_charge_amount'
                    GROUP BY s.user_id
                    $order
                    $lim";
        }
        return $resp;
    }

    public static function queryReport17d($params, $total = 0){
        if ($total) {
            $resp = "SELECT COUNT(t.id) AS total FROM (SELECT COUNT(s.user_id) AS id FROM sales_report s WHERE s.`branch_id` IN ($params->s) AND role_id IN (68, 69) AND s.`datetime` >= '$params->f 00:00:00' AND s.`datetime` <= '$params->t 23:59:59' AND s.note = 'new_charge_amount' GROUP BY s.`team`) t";
        } else {
            $lim = (isset($params->d) && isset($params->l)) ? "LIMIT $params->d, $params->l" : "";
            $order = isset($params->order)?($params->order?(($params->order == 1)?"ORDER BY amount DESC":"ORDER BY amount ASC"):""):"";

            $resp = "SELECT COUNT(t.user_id) AS total_staff,
                            (SELECT `full_name` FROM users WHERE hrm_id = t.team LIMIT 1) AS full_name,
                            (SELECT `name` FROM branches WHERE id = t.branch_id) AS branch_name,
                            (SELECT `name` FROM roles WHERE id = t.role_id) AS role_name,
                            t.team AS hrm_id,
                            SUM(t.amount) AS amount
                     FROM (
                        SELECT SUM(amount) AS amount,
                            s.user_id,
                            s.team,
                            s.branch_id,
                            s.role_id
                        FROM
                            sales_report s
                        WHERE
                            s.`role_id` IN (68, 69)
                            AND branch_id IN ($params->s)
                            AND s.`datetime` >= '$params->f 00:00:00'
                            AND s.`datetime` <= '$params->t 23:59:59'
                            AND s.branch_id <> 100
                            AND s.note = 'new_charge_amount'
                        GROUP BY s.user_id
                     ) t GROUP BY t.team
                     $order
                     $lim";
        }
        return $resp;
    }


    public static function getMonths($from, $to){
        $from_time = strtotime($from);
        $to_time = strtotime($to);

        $from_year = (int)date('Y',$from_time);
        $to_year = (int)date('Y',$to_time);

        if($to_year > $from_year){
            $to_month = ($to_year - $from_year) * 12 + (int)date('m',$to_time);
        }else{
            $to_month = (int)date('m',$to_time);
        }

        $from_month = (int)date('m',$from_time);

        return ($to_month - $from_month + 1);
    }

    public static function regionRank($amount, $quantity, $months){
        $average = self::average($amount, $quantity, $months);

        $rank = 'Loại 3';
        if($average > 1250000000){
            $rank = 'Loại 1';
        }else if ($average > 800000000){
            $rank = 'Loại 2';
        }
        return $rank;
    }

    public static function branchRank($amount, $quantity, $months){
        $average = self::average($amount, $quantity, $months);

        $rank = 'Loại 3';
        if($average > 1250000000){
            $rank = 'Loại 1';
        }else if ($average > 800000000){
            $rank = 'Loại 2';
        }
        return $rank;
    }

    public static function staffRank($amount, $quantity, $months){
        $average = self::average($amount, $quantity, $months);

        $rank = 'Loại 3';
        if($average > 300000000){
            $rank = 'Loại 1';
        }else if ($average > 120000000){
            $rank = 'Loại 2';
        }
        return $rank;
    }

    public static function average($amount, $quantity, $months = 1)
    {
        return floor($amount / ($quantity * $months));
    }

    public static function form30($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $date = date('Y-m-d');
        $branchIds = self::$branchIds;
        $keyword   = self::$keyword;
        $productIds = self::$productIds;

        if ($request->status !== null && $request->status !== '') {
            $where[] = "r.status IN ($request->status)";
        }
        $where[] = "DATE(r.start_date) <= CURDATE() AND DATE(r.end_date) >= CURDATE()";
        if ($keyword) {
            $where[] = "(s.stu_id = '$keyword' OR s.effect_id = '$keyword' OR s.name LIKE '%$keyword%')";
        }
        if ($branchIds) {
            $where[] = "r.branch_id IN ($branchIds)";
        }
        if ($productIds) {
            $where[] = "c.product_id IN ($productIds)";
        }
        if ($where) {
            $strWhere = ' WHERE ' . implode(' AND ', $where);
        }

        $query = "
                SELECT
                    r.`id` id,
                    r.`student_id` student_id,
                    r.`type` `type`,
                    r.`start_date` reserve_start_date,
                    r.`end_date` reserve_end_date,
                    r.`session` `session`,
                    r.`created_at` created_at,
                    r.`approved_at` approved_at,
                    r.`comment` `comment`,
                    CASE 
                      WHEN r.is_reserved = 0 THEN 'Không giữ chỗ'
                      WHEN r.is_reserved = 1 THEN 'Giữ chỗ'
                    END AS is_reserved,
                    CASE 
                      WHEN r.status = 0 THEN 'Chờ duyệt'
                      WHEN r.status = 1 THEN 'Đã duyệt'
                      WHEN r.status = 2 THEN 'Từ chối'
                    END AS status,
                    CASE 
                      WHEN r.type = 0 THEN 'Bình thường'
                      WHEN r.type = 1 THEN 'Đặc biệt'
                      WHEN r.type = 2 THEN 'Bình thường + Đặc biệt'
                    END AS type_name,
                    r.`meta_data` meta_data,
                    r.`attached_file` attached_file,
                    s.`name` student_name,
                    s.`stu_id` lms_id,
                    s.`effect_id` effect_id,
                    b.`name` branch_name,
                    p.`name` product_name,
                    pr.`name` program_name,
                    cls.`cls_name` class_name,
                    r.`is_supplement`,
                    t.`name` tuition_fee_name,
                    u1.username creator_name,
                    u2.username approver_name,
                    special_reserved_sessions special_reserved_sessions
                FROM
                    reserves r
                    LEFT JOIN students s ON r.`student_id` = s.`id`
                    LEFT JOIN contracts c ON r.`contract_id` = c.`id`
                    LEFT JOIN tuition_fee t ON t.`id` = c.`tuition_fee_id`
                    LEFT JOIN branches b ON r.`branch_id` = b.`id`
                    LEFT JOIN products p ON r.`product_id` = p.`id`
                    LEFT JOIN programs pr ON r.`program_id` = pr.`id`
                    LEFT JOIN classes cls ON r.`class_id` = cls.`id`
                    LEFT JOIN users u1 ON r.`creator_id` = u1.`id`
                    LEFT JOIN users u2 ON r.`approver_id` = u2.`id`
                $strWhere AND s.status >0 
                GROUP BY r.id
                ORDER BY r.created_at DESC
                $strLimit";

        $queryCount = "
                    SELECT COUNT(DISTINCT(r.id)) AS total
                    FROM reserves r
                    LEFT JOIN students s ON r.`student_id` = s.`id`
                    LEFT JOIN contracts c ON r.`contract_id` = c.`id`
                    LEFT JOIN tuition_fee t ON t.`id` = c.`tuition_fee_id`
                    LEFT JOIN branches b ON r.`branch_id` = b.`id`
                    LEFT JOIN products p ON r.`product_id` = p.`id`
                    LEFT JOIN programs pr ON r.`program_id` = pr.`id`
                    LEFT JOIN classes cls ON r.`class_id` = cls.`id`
                    LEFT JOIN users u1 ON r.`creator_id` = u1.`id`
                    LEFT JOIN users u2 ON r.`approver_id` = u2.`id`
                $strWhere AND s.status >0 ";

        $rs     = DB::select(DB::raw($query));
        $total  = DB::select(DB::raw($queryCount));
        $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;
        if ($rs) {
            foreach ($rs as $k => $v) {
                $metaData = json_decode($v->meta_data);
                $amountLeft     = isset($metaData->amount_left) ? $metaData->amount_left : 0;
                $totalFee       = isset($metaData->total_fee) ? $metaData->total_fee : 0;
                $totalSession   = isset($metaData->total_session) ? $metaData->total_session : 0;
                $numberReserved = isset($v->session) ? $v->session : 0;
                $amountReserved = $totalSession > 0 ? (ceil($totalFee / $totalSession) * $numberReserved) : 0;
                $v->total_session   = $totalSession;
                $v->total_fee       = $totalFee;
                $v->session_left    = isset($metaData->session_left) ? $metaData->session_left : 0;
                $v->amount_left     = $amountLeft;
                $v->start_date      = isset($metaData->start_date) ? $metaData->start_date : '';
                $v->end_date        = isset($metaData->end_date) ? $metaData->end_date : '';
                $v->amount_reserved = $amountReserved;
                $v->number_of_session_reserved = isset($metaData->number_of_session_reserved) ? $metaData->number_of_session_reserved : 0;
            }
        }
        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => $totalRecord
        );
        return $return;
    }

    public static function reportForm31($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where    = array();
        $strWhere = '';
        $fd = self::$fd;
        $td = self::$td;
        $keyword    = self::$keyword;
        $branchIds  = self::$branchIds;
        $productIds = self::$productIds;
        $status = $request->status;
        $ecId = self::$ecId;
        $cmId = self::$cmId;

        if ($status !== null) {
            $where[] = "p.status = $status";
        }
        $date = date('Y-m-d');
        $where[] = "DATE(p.start_date) <= CURDATE() AND DATE(p.end_date) >= CURDATE()";

        if ($cmId) {
            $where[] = "ts.cm_id IN ( $cmId )";
        }

        if ($ecId) {
            $where[] = "ts.ec_id IN ( $ecId )";
        }

        if ($branchIds) {
            $where[] = "p.branch_id IN ( $branchIds )";
        }
        if ($productIds) {
            $where[] = "c.product_id IN ( $productIds )";
        }
        if ($keyword) {
            $where[] = "(s.stu_id = '$keyword' OR s.name LIKE '%$keyword%' OR s.effect_id = '$keyword')";
        }

        if ($where) {
            $strWhere = " WHERE " . implode(' AND ', $where);
        }

        $query = "
                  SELECT DISTINCT (s.stu_id) AS lms_id,
                        s.name AS student_name,
                        s.effect_id AS effect_id,
                        b.name AS branch_name,
                        prd.name AS product_name,
                        prg.name AS program_name,
                        tf.name AS tuition_fee_name,
                        c.start_date AS start_date,
                        u1.full_name AS creator_name,
                        u2.full_name AS approver_name,
                        p.start_date AS pending_date,
                        p.end_date AS pending_end_date,
                        p.note AS note,
                        p.comment AS comment,
                        p.created_at AS created_at,
                        p.creator_id AS creator_id,
                        p.session AS session,
                        p.status AS status,
                        p.approved_at AS approved_at,
                        re.description as reason_name 
                  FROM pendings AS p
                  LEFT JOIN students AS s ON p.student_id = s.id
                  LEFT JOIN contracts AS c ON p.contract_id = c.id
                  LEFT JOIN branches AS b ON p.branch_id = b.id
                  LEFT JOIN products AS prd ON c.product_id = prd.id
                  LEFT JOIN programs AS prg ON c.program_id = prg.id
                  LEFT JOIN tuition_fee AS tf ON c.tuition_fee_id = tf.id
                  LEFT JOIN term_student_user AS ts ON ts.student_id = s.id
                  LEFT JOIN users AS u1 ON u1.id = p.creator_id
                  LEFT JOIN users AS u2 ON u2.id = p.approver_id
                  LEFT JOIN reasons AS re ON p.reason_id = re.id
                  $strWhere
                  ORDER BY p.created_at DESC
                  $strLimit";

        $queryCount = "
                    SELECT COUNT(1) AS total
                    FROM pendings AS p
                    LEFT JOIN students AS s ON p.student_id = s.id
                    LEFT JOIN contracts AS c ON p.contract_id = c.id
                    LEFT JOIN branches AS b ON p.branch_id = b.id
                    LEFT JOIN products AS prd ON c.product_id = prd.id
                    LEFT JOIN programs AS prg ON c.program_id = prg.id
                    LEFT JOIN tuition_fee AS tf ON c.tuition_fee_id = tf.id
                    LEFT JOIN term_student_user AS ts ON ts.student_id = s.id
                    LEFT JOIN users AS u1 ON u1.id = p.creator_id
                    LEFT JOIN users AS u2 ON u2.id = p.approver_id
                    LEFT JOIN reasons AS re ON p.reason_id = re.id
                    $strWhere
                    ORDER BY p.created_at DESC
                    ";

        $rs    = DB::select(DB::raw($query));
        $total = DB::select(DB::raw($queryCount));
        if ($rs) {
            foreach ($rs as $k => $v) {
                $start = date_create($v->pending_date);
                $end   = date_create($v->pending_end_date);
                $diff  = date_diff($start, $end);
                $v->total_pending_session = $diff->format('%a');
                $status = $v->status;
                switch ($status) {
                    case 1:
                        $v->status_name = 'Đã duyệt';
                        break;
                    case 2:
                        $v->status_name = 'Từ chối';
                        break;
                    case 3:
                        $v->status_name = 'Đã xóa';
                        break;
                    case 0:
                    default:
                        $v->status_name = 'Chờ duyệt';
                        break;
                }
            }
        }
        $return = array(
            'data' => $rs ? $rs : null,
            'total_record' => isset($total[0]->total) ? (int)$total[0]->total : 0
        );
        return $return;
    }

    public static function reportForm32($request)
    {
        self::commons($request);
        $strLimit = self::$strLimit;
        $where = array();
        $strWhere = '';
        $fd = self::$fd;
        $td = self::$td;
        $branches = self::$branchIds;
        $products = self::$productIds;
        $date = $request->date ? $request->date : date('Y-m-d');
        $student_num = (int)$request->student_number ? (int)$request->student_number : 5;
        $keyword = self::$keyword;
        $ecId = self::$ecId;
        $cmId = self::$cmId;

        $classId = (int)$request->class_id;
        if (!$classId) {
            return array(
                'data' => [],
                'total_record' => 0
            );
        }
        $where[] = "e.start_date <= e.end_date";
        if ($classId) {
            $where[] = "e.class_id = $classId";
        }
        if ($cmId) {
            $where[] = "c.cm_id IN ( $cmId )";
        }

        if ($ecId) {
            $where[] = "c.ec_id IN ( $ecId )";
        }
        if ($keyword) {
            $where[] = "(s.stu_id = '$keyword' 
                        OR s.effect_id = '$keyword' 
                        OR s.name LIKE '%$keyword%' 
                        OR u1.full_name LIKE '%$keyword%' 
                        OR u2.full_name LIKE '%$keyword%' 
                        OR u3.full_name LIKE '%$keyword%'
                        OR u1.username LIKE '%$keyword%' 
                        OR u2.username LIKE '%$keyword%'
                        OR u3.username LIKE '%$keyword%'
                        )";
        }
        if ($where) {
            $strWhere = ' WHERE ' . implode(' AND ', $where);
        }
        $query = "
                SELECT e.id AS enrolment_id,
                       e.student_id,
                       s.name AS student_name,
                       s.nick AS student_nick,
                       s.stu_id AS stu_id,
                       s.effect_id AS effect_id,
                       t.name AS tuition_fee_name,
                       t.receivable AS tuition_fee_price,
                       t.`session` AS tuition_fee_sessions,
                       cl.cls_name AS CLASS,
                       e.start_date AS start_date,
                       e.end_date AS end_date,
                       c.total_sessions AS total_sessions,
                       c.bill_info,
                       p.method AS payment_method,
                       c.must_charge,
                       CASE
                            WHEN c.type = 0 THEN 'Học thử'
                            WHEN c.type = 1 THEN 'Chính thức'
                            WHEN c.type = 2 THEN 'Tái phí bình thường'
                            WHEN c.type = 3 THEN 'Tái phí do nhận chuyển phí'
                            WHEN c.type = 4 THEN 'Chỉ nhận chuyển phí'
                            WHEN c.type = 5 THEN 'Chuyển trung tâm'
                            WHEN c.type = 6 THEN 'Chuyển lớp'
                            WHEN c.type = 7 THEN 'Tái phí chưa đủ phí'
                            WHEN c.type = 8 THEN 'Được nhận học bỏng'
                       END AS contract_name,
                       c.type AS contract_type,
                       c.total_charged AS charged_total,
                       IF(e.status = 1
                          AND e.last_date <= CURDATE(), 1, 0) AS withdraw,
                       IF(e.status = 1
                          AND e.last_date <= CURDATE(), 2, IF(e.status = 0, 3, 1)) ordering,
                       e.real_sessions AS available_sessions,
                       e.status AS enrolment_status,
                       e.last_date,
                       CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
                       CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
                       u3.hrm_id AS ec_leader_id,
                       sem.name AS semester_name
                FROM enrolments AS e
                LEFT JOIN students AS s ON e.student_id = s.id
                LEFT JOIN contracts AS c ON e.contract_id = c.id
                LEFT JOIN payment AS p ON c.payment_id = p.id
                LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
                LEFT JOIN classes AS cl ON e.class_id = cl.id
                LEFT JOIN users AS u1 ON c.ec_id = u1.id
                LEFT JOIN users AS u2 ON c.cm_id = u2.id
                LEFT JOIN users AS u3 ON c.ec_leader_id = u3.id
                LEFT JOIN programs AS pr ON c.program_id = pr.id
                LEFT JOIN semesters AS sem ON sem.id = pr.semester_id
                $strWhere
                ORDER BY ordering
                $strLimit";

        $queryCount = "
                SELECT COUNT(1) AS total
                FROM enrolments AS e
                LEFT JOIN students AS s ON e.student_id = s.id
                LEFT JOIN contracts AS c ON e.contract_id = c.id
                LEFT JOIN payment AS p ON c.payment_id = p.id
                LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
                LEFT JOIN classes AS cl ON e.class_id = cl.id
                LEFT JOIN users AS u1 ON c.ec_id = u1.id
                LEFT JOIN users AS u2 ON c.cm_id = u2.id
                LEFT JOIN users AS u3 ON c.ec_leader_id = u3.id
                LEFT JOIN programs AS pr ON c.program_id = pr.id
                LEFT JOIN semesters AS sem ON sem.id = pr.semester_id
                $strWhere";

        $results = null;
        $totalRecord = 0;
        try{
            $results = DB::select(DB::raw($query));
            $total = DB::select(DB::raw($queryCount));
            $totalRecord = isset($total[0]->total) ? (int)$total[0]->total : 0;

        }catch(Exception $exception){
            throw $exception;
        }
        $return = array(
            'data' => $results,
            'total_record' => $totalRecord
        );
        return $return;
    }
    public static function queryReport33($p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        if($p->s) {
            $where .= " AND c.branch_id in ($p->s) ";
        }
        if($p->r) {
            $where .= " AND c.product_id in ($p->r) ";
        }
        if ($total) {
            $resp = "SELECT COUNT(e.id) total FROM enrolments e
                        LEFT JOIN students s ON e.student_id = s.id
                        LEFT JOIN contracts c ON e.contract_id = c.id
                        LEFT JOIN classes cl ON e.class_id = cl.id
                    WHERE s.status >0 AND e.student_id IN (SELECT student_id FROM (SELECT COUNT(id) total, student_id FROM contracts WHERE TYPE = 0 AND count_recharge = -1 AND (contracts.status=5 OR contracts.status =1) GROUP BY student_id) t WHERE t.total >= 2) $where";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        e.student_id student_id,
                        s.name student_name,
                        s.nick student_nick,
                        s.stu_id,
                        c.type customer_type,
                        cl.cls_name class_name,
                        (SELECT u.username FROM users u LEFT JOIN term_student_user t ON t.cm_id = u.id WHERE t.student_id = e.student_id) cm_name,
                        (SELECT u.username FROM users u LEFT JOIN term_student_user t ON t.ec_id = u.id WHERE t.student_id = e.student_id) ec_name,
                        (SELECT name FROM branches WHERE id = s.branch_id) branch_name,
                        (SELECT name FROM products WHERE id = c.product_id) product_name,
                        (SELECT name FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_name,
                        COALESCE(c.program_label, (SELECT name FROM programs WHERE id = c.program_id)) program_name,
                        e.start_date,
                        e.last_date,
                        e.real_sessions available_sessions
                    FROM enrolments e
                        LEFT JOIN students s ON e.student_id = s.id
                        LEFT JOIN contracts c ON e.contract_id = c.id
                        LEFT JOIN classes cl ON e.class_id = cl.id
                    WHERE s.status >0 AND e.student_id IN (SELECT student_id FROM (SELECT COUNT(id) total, student_id FROM contracts WHERE TYPE = 0 AND count_recharge = -1 AND (contracts.status=5 OR contracts.status =1) GROUP BY student_id) t WHERE t.total >= 2) $where";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function queryReport34($p, $date, $total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        if($p->s) {
            $where .= " AND b.id in ($p->s) ";
        }
        if ($total) {
            $resp = "SELECT COUNT(b.id) total FROM branches AS b
                    WHERE b.status=1 $where";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        b.name AS branch_name,
                        (SELECT count(id) FROM students WHERE branch_id = b.id AND status =1  AND created_at > '".$date." 00:00:00' AND created_at < '".$date." 23:59:59') AS count_student
                    FROM branches AS b
                    WHERE b.status=1 $where";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function reportForm34($request){
        $scope = self::getScope($request->scope, explode(',',$request->users_data->branches_ids));
        $where = '';
        if($scope) {
            $where .= " AND b.id in ($scope) ";
        }
        $date = $request->date;
        $query = "SELECT
            b.name AS branch_name,
            (SELECT count(id) FROM students WHERE branch_id = b.id AND status =1  AND created_at > '".$date." 00:00:00' AND created_at < '".$date." 23:59:59') AS count_student
        FROM branches AS b
        WHERE b.status=1 $where";
        $data = DB::select(DB::raw($query));
        return $data;
    }
    public static function queryReport34b2($p, $date, $total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        if($p->s!=='') {
            $where .= " AND b.id in ($p->s) ";
        }
        if ($total) {
            $resp = "SELECT COUNT(b.id) total FROM students AS s 
                    LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status =1
                    LEFT JOIN branches AS b ON b.id=t.branch_id
                    WHERE s.status=1 $where AND s.created_at > '".$date." 00:00:00' AND s.created_at < '".$date." 23:59:59'";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT s.*, b.name AS branch_name
                    FROM students AS s 
                    LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status =1
                    LEFT JOIN branches AS b ON b.id=t.branch_id
                    WHERE s.status=1 $where AND s.created_at > '".$date." 00:00:00' AND s.created_at < '".$date." 23:59:59'";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function reportForm34b2($request){
        $scope = self::getScope($request->scope, explode(',',$request->users_data->branches_ids));
        $where = '';
        if($scope!='') {
            $where .= " AND b.id in ($scope) ";
        }
        $date = $request->date;
        $query = "SELECT s.*, b.name AS branch_name
                FROM students AS s 
                LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status =1
                LEFT JOIN branches AS b ON b.id=t.branch_id
                WHERE s.status=1 $where AND s.created_at > '".$date." 00:00:00' AND s.created_at < '".$date." 23:59:59'";
        $data = DB::select(DB::raw($query));
        return $data;
    }
    public static function queryReport01a1($birthday_mode = false, $p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        $start_date = $p && isset($p->a) ? date('Y-m-01',strtotime($p->a.'-01')) : date('Y-m-01');
        if(date('Y-m',strtotime($p->a.'-01')) == date('Y-m')){
            $end_date = date('Y-m-d');
        }else{
            $end_date = date('Y-m-t',strtotime($p->a.'-01'));
        }
        $resp = "";
        $month = date('n', strtotime($start_date));
        $extra = $birthday_mode ? " AND MONTH(s.date_of_birth) = '$month' " : '';
        if($p->a < date('Y-m')){
            $where = ' AND s.id IS NOT NULL ';
            if ($p->k != '') {
                $where .= " AND (s.name like '%$p->k%' or s.stu_id like '%$p->k%' or s.crm_id like '%$p->k%' or s.accounting_id like '%$p->k%') ";
            }
            if ($p->c != '') {
                $where .= " AND r.cm_id in ($p->c) ";
            }
            if ($p->s != '') {
                $where .= " AND r.branch_id in ($p->s) ";
            }
            if ($p->r != '') {
                $where .= " AND r.product_id in ($p->r) ";
            }
            if ($total) {
                $resp = "SELECT
                            count(DISTINCT student_id) as total
                        FROM
                            report_full_fee_active AS r
                            LEFT JOIN students AS s ON s.id=r.student_id
                        WHERE
                            r.report_month = '$p->a'
                            $where $extra
                ";
            } else {
                $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
                $resp = "SELECT
                            s.crm_id, s.name as student_name, u.hrm_id, u.full_name as cm_name, p.name as product_name, s.accounting_id,s.gud_name1,s.date_of_birth,cls.cls_name,
                            (SELECT t.`name` FROM contracts AS c LEFT JOIN tuition_fee AS t ON t.id=c.tuition_fee_id  WHERE c.id=r.contract_id LIMIT 1) AS tuition_fee_name,
                            (SELECT t.`session` FROM contracts AS c LEFT JOIN tuition_fee AS t ON t.id=c.tuition_fee_id  WHERE c.id=r.contract_id LIMIT 1) AS tuition_fee_session,
                            IF((SELECT c.count_recharge FROM contracts AS c WHERE c.id=r.contract_id LIMIT 1) =0,'NEW','RENEW') AS count_recharge,
                            (SELECT enrolment_start_date FROM log_contracts_history WHERE student_id = r.student_id AND count_recharge >= 0 AND enrolment_start_date IS NOT NULL ORDER BY id ASC LIMIT 1) as enrolment_start_date_official_contract,
                            (SELECT name FROM branches WHERE id=r.branch_id LIMIT 1) AS branch_name,
                            (SELECT ins_name FROM teachers WHERE user_id=cls.teacher_id) AS teacher_name,
                            r.last_date AS  enrolment_last_date,
                            r.done_session, r.summary_sessions,
                            s.gud_mobile1,s.gud_email1,
                            r.date_start AS enrolment_start_date,
                            (SELECT  must_charge FROM contracts WHERE id=r.contract_id) AS must_charge
                        FROM
                            report_full_fee_active r
                            LEFT JOIN students s ON r.student_id = s.id
                            LEFT JOIN classes cls on r.class_id = cls.id
                            LEFT JOIN products p on r.product_id = p.id
                            LEFT JOIN users u ON u.id=r.cm_id
                        WHERE
                            r.report_month = '$p->a' 
                            $where $extra GROUP BY s.id
                        ORDER BY r.cm_id ,SUBSTRING_INDEX(s.name, ' ', -1) ";
                if (!$unlimit) {
                    $resp.= " $lim";
                }
            }
        }else{
            $where = ' AND s.id IS NOT NULL ';
            if ($p->k != '') {
                $where .= " AND (s.name like '%$p->k%' or s.stu_id like '%$p->k%' or s.crm_id like '%$p->k%' or s.accounting_id like '%$p->k%') ";
            }
            if ($p->c != '') {
                $where .= " AND t.cm_id in ($p->c) ";
            }
            if ($p->s != '') {
                $where .= " AND c.branch_id in ($p->s) ";
            }
            if ($p->r != '') {
                $where .= " AND c.product_id in ($p->r) ";
            }
            if ($total) {
                $resp = "SELECT
                            count(DISTINCT s.id) as total
                        FROM
                            contracts c
                            LEFT JOIN students s ON c.student_id = s.id
                            LEFT JOIN classes cls on c.class_id = cls.id
                            LEFT JOIN term_student_user AS t ON t.student_id = c.student_id
                        WHERE
                            c.type > 0
                            AND c.status != 7
                            $extra
                            AND (
                                    (
                                        c.class_id IS NOT NULL
                                        AND c.type != 86 AND c.type !=6
                                        AND c.enrolment_start_date <= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate DESC LIMIT 1 )
                                        AND c.enrolment_last_date >= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate DESC LIMIT 1 )
                                        -- AND c.enrolment_last_date >='$start_date'
                                    )
                                OR  (
                                        c.class_id IS NOT NULL
                                        AND (c.type = 86 OR c.type =6)
                                        AND c.enrolment_start_date <= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate >= '$end_date' AND `status`=1 ORDER BY cjrn_classdate ASC LIMIT 1 )
                                        AND c.enrolment_last_date >= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate ASC LIMIT 1 )
                                        -- AND c.enrolment_last_date >='$start_date'
                                    )
                            )
                            AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND is_reserved=1 AND `start_date` <= '$end_date' AND `end_date`>='$end_date' AND `status`=2) =0
                            AND (SELECT count(id) FROM contracts WHERE student_id = c.student_id AND `type`>0 AND `status`!=7 AND class_id IS NOT NULL AND id!=c.id) = 0
                            AND (c.debt_amount = 0 OR c.foced_is_full_fee_active =1) AND c.foced_is_full_fee_active!=2 AND s.status>0
                            $where
                ";
            } else {
                $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
                $resp = "SELECT
                            s.crm_id, s.name as student_name, u.hrm_id, u.full_name as cm_name, p.name as product_name, s.nick, s.accounting_id,s.gud_name1,s.date_of_birth,
                            IF((cls.cls_name IS NOT NULL),cls.cls_name,(SELECT cla.cls_name FROM contracts AS co LEFT JOIN classes AS cla ON cla.id=co.class_id
                                WHERE co.student_id=s.id AND co.`status` !=7 AND co.`class_id` IS NOT NULL LIMIT 1)) AS cls_name,
                            (SELECT `name` FROM tuition_fee WHERE id=c.tuition_fee_id LIMIT 1) AS tuition_fee_name,
                            (SELECT `session` FROM tuition_fee WHERE id=c.tuition_fee_id LIMIT 1) AS tuition_fee_session,
                            IF(c.count_recharge =0,'NEW','RENEW') AS  count_recharge,
                            (SELECT enrolment_start_date FROM log_contracts_history WHERE student_id = c.student_id AND count_recharge >= 0 AND enrolment_start_date IS NOT NULL ORDER BY id ASC LIMIT 1) as enrolment_start_date_official_contract,
                            (SELECT name FROM branches WHERE id=c.branch_id LIMIT 1) AS branch_name,
                            (SELECT ins_name FROM teachers WHERE user_id=cls.teacher_id) AS teacher_name, c.enrolment_start_date,c.branch_id,c.product_id,c.id AS contract_id,c.class_id, c.enrolment_last_date, c.summary_sessions,
                             s.gud_mobile1,s.gud_email1, c.enrolment_start_date,
                            c.must_charge
                        FROM
                            contracts c
                            LEFT JOIN students s ON c.student_id = s.id
                            LEFT JOIN classes cls on c.class_id = cls.id
                            LEFT JOIN term_student_user AS t ON t.student_id = c.student_id
                            LEFT JOIN users u on t.cm_id = u.id
                            LEFT JOIN products p on c.product_id = p.id
                        WHERE
                            c.type > 0
                            AND c.status < 7
                            $extra
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
                            AND (c.debt_amount = 0 OR c.foced_is_full_fee_active =1) AND c.foced_is_full_fee_active!=2 AND s.status>0
                            $where GROUP BY s.id
                        ORDER BY t.cm_id ,SUBSTRING_INDEX(s.name, ' ', -1) ";
                if (!$unlimit) {
                    $resp.= " $lim";
                }
            }
        }
        return $resp;
    }
    public static function queryReport01b1($p, $total = 0, $unlimit = false) {
        $dt = isset($p->a) ? $p->a : date('Y-m');
        $where[]  = "";
        if ($p->s) {
            $where[] = "r.branch_id IN ($p->s)";
        }
        if ($dt) {
            $where[] = "r.renewed_month = '$dt'";
        }
        if ($p->r) {
            $where[] = "r.product_id IN ($p->r)";
        }
        if ($p->k) {
            $where[] = "(s.crm_id LIKE '%$p->k%' OR s.accounting_id LIKE '%$p->k%' OR s.name LIKE '%$p->k%')";
        }
        if($p->ecs) {
            $where[]= "r.ec_id IN ($p->ecs)";
        }
        if($p->e > 0) {
            $where[]= "r.`status` = $p->e";
        }
        if ($where) {
            $strWhere = implode(' AND ', $where);
        }
        $qtotal = "SELECT COUNT(r.id) total FROM renews_report AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE r.id > 0 AND r.`disabled` = 0 $strWhere AND s.status>0";
        if ($total) {
            $resp = $qtotal;
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT r.*, s.name AS student_name, s.crm_id, s.accounting_id,s.gud_mobile1,
                        (SELECT `name` FROM branches WHERE id=r.branch_id) AS branch_name,
                        (SELECT `name` FROM products WHERE id=r.product_id) AS product_name,
                        (SELECT cls_name FROM classes WHERE id=r.class_id) AS class_name,
                        IF(r.status=1,'Thành công','Thất bại') AS status_title,
                        (SELECT `name` FROM tuition_fee WHERE id= r.tuition_fee_id) AS tuition_fee_name,
                        (SELECT `full_name` FROM users WHERE id= r.ec_id) AS ec_name,
                        (SELECT `hrm_id` FROM users WHERE id= r.ec_id) AS ec_hrm_id
                    FROM renews_report AS r 
                        LEFT JOIN students AS s ON s.id=r.student_id 
                    WHERE r.id > 0 AND r.`disabled` = 0 $strWhere AND s.status>0 ";
            if (!$unlimit ) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function queryReport01b1_success($p, $total = 0) {
        $dt = isset($p->a) ? $p->a : date('Y-m');
        $where[]  = "";
        if ($p->s) {
            $where[] = "r.branch_id IN ($p->s)";
        }
        if ($dt) {
            $where[] = "r.renewed_month = '$dt'";
        }
        if ($p->r) {
            $where[] = "r.product_id IN ($p->r)";
        }
        if ($p->k) {
            $where[] = "(s.crm_id LIKE '%$p->k%' OR s.name LIKE '%$p->k%' OR s.accounting_id LIKE '%$p->k%')";
        }
        if($p->ecs) {
            $where[]= "r.ec_id IN ($p->ecs)";
        }
        if($p->e > 0) {
            if ($p->e != 1) return 0;
            $where[]= "r.`status` = $p->e";
        } else {
            $where[]= "r.`status` = 1";
        }
        if ($where) {
            $strWhere = implode(' AND ', $where);
        }

        $qtotal = "SELECT COUNT(r.id) total FROM renews_report AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE r.id > 0 AND r.`disabled` = 0 $strWhere AND s.status>0";
        $tt = u::first($qtotal);
        $total = $tt->total;
        return $total;
    }
    public static function queryReport01b2($p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        if($p->s) {
            $where .= " AND b.id IN ($p->s) ";
        }
        if ($total) {
            $resp = "SELECT COUNT(b.id) total FROM branches b WHERE b.status = 1 $where";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $renewSql = "SELECT COUNT(r.id) FROM renews_report AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE s.status>0 AND  r.`disabled` = 0 AND r.renewed_month = '$p->a' AND r.branch_id =b.id";
            $resp = "SELECT
                    ($renewSql AND r.status>0) total_item,
                    ($renewSql AND r.status=1) success_item,
                    b.name branch_name
                FROM branches b WHERE b.status = 1 $where";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function summaryReport01b3($p, $total = 0, $unlimit = false) {
        $data = null;
        $where = '';
        if($p->s) {
            $where .= " AND r.branch_id IN ($p->s) ";
        }
        $dt = isset($p->a) ? $p->a : date('Y-m');
        if ($dt) {
            $where .= " AND r.renewed_month = '$dt' ";
        }
        if($p->ecs) {
            $where .= " AND r.ec_id in ($p->ecs) ";
        }
        $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
        $query = "SELECT SUM(expired) total_expired, SUM(success) total_success FROM ( SELECT
            (SELECT COUNT(r1.id) FROM renews_report AS r1 LEFT JOIN students AS s ON s.id=r1.student_id WHERE r1.ec_id = r.ec_id AND r1.`disabled` = 0 AND r1.`status` > 0 AND r1.renewed_month = '$dt' AND r1.branch_id IN ($p->s) AND s.status>0) expired,
			(SELECT COUNT(r1.id) FROM renews_report AS r1 LEFT JOIN students AS s ON s.id=r1.student_id WHERE r1.ec_id = r.ec_id AND r1.`disabled` = 0 AND r1.`status` = 1 AND r1.renewed_month = '$dt' AND r1.branch_id IN ($p->s) AND s.status>0) success
            FROM renews_report r
            WHERE r.id > 0 AND r.`disabled` = 0 $where
            GROUP BY r.ec_id";
        if (!$unlimit) {
            $query.= " $lim";
        }
        $query.= ") x";
        $summary = u::first($query);
        $total_item = (int)$summary->total_expired;
        $success_item = (int)$summary->total_success;
        if ($total_item) {
            $data = (Object)[];
            $data->total_item = $total_item;
            $data->success_item = $success_item;
            $data->rate_item = round(($success_item / $total_item) * 100, 2);
        }else{
            if($p->s){
                $zone_info= u::first("SELECT zone_id FROM branches WHERE id IN ($p->s)");
                $tmp_zone_id = $zone_info ? $zone_info->zone_id :1;
            }else{
                $tmp_zone_id =1;
            }
            $data = (Object)[];
            $data->total_item = 0;
            $data->success_item = 0;
            $data->rate_item = $tmp_zone_id==2 ? 75:70;
        }
        return $data;
    }
    public static function queryReport01b3($p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        $dt = isset($p->a) ? $p->a : date('Y-m');
        if($p->s) {
            $where .= " AND t.branch_id IN ($p->s) ";
        }
        if($p->ecs) {
            $where .= " AND t.user_id in ($p->ecs) ";
        }
        if($p->a < date('Y-m')){
            $where .= " AND  t.role_id IN(68,69)  AND (SELECT count(id) FROM report_get_users WHERE user_id = t.user_id AND report_month = '$dt' AND `status`=1)>0 ";
        }else{
            $where .= " AND t.role_id IN (68,69) AND ( t.status =1 OR
            (t.status=0 AND (SELECT COUNT(id) FROM renews_report WHERE ec_id = t.user_id AND `status` > 0 AND `disabled` = 0 AND renewed_month = '$dt' AND branch_id IN ($p->s))>0))";
        }
        if ($total) {
            $resp = "SELECT COUNT(1) total
                    FROM term_user_branch AS t
                    WHERE t.role_id IN (68,69) AND (t.status =1 OR
                        (t.status=0 AND (SELECT COUNT(id) FROM renews_report WHERE ec_id = t.user_id AND `status` > 0 AND `disabled` = 0 AND renewed_month = '$dt' AND branch_id IN ($p->s))>0)
                        OR t.user_id IN (SELECT user_id FROM report_get_users WHERE user_id = t.user_id AND report_month = '$dt' AND `status`=1 AND role_id IN (68,69) AND branch_id = t.branch_id))  $where";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $sql_renew = "SELECT COUNT(r.id) FROM renews_report AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE s.status>0 AND r.ec_id = t.user_id AND r.`disabled` = 0 AND r.renewed_month = '$dt' AND r.branch_id IN ($p->s)";
            $resp = "SELECT b.name AS branch_name, u.full_name AS ec_name, u.id AS ec_id, b.id AS branch_id,
                u.hrm_id,
                (SELECT ro.`name` FROM roles ro WHERE t.role_id = ro.id LIMIT 1 ) role_name,
                ($sql_renew AND r.status >0) total_renew,
                ($sql_renew AND r.status=1) success_renew,
                IF(($sql_renew AND r.status >0) = 0,70, ROUND(($sql_renew AND r.status =1)*100 / ($sql_renew AND r.status >0))) rate
                FROM term_user_branch AS t
                    LEFT JOIN users AS u ON u.id = t.user_id
                    LEFT JOIN branches AS b ON b.id=t.branch_id
                WHERE u.id > 0  $where";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function dataReport01b3($request, $session ,$unlimit = false){
        $p = Report::params($request, $session);
        $query = Report::queryReport01b3($p,0 ,$unlimit);
        $quary = Report::queryReport01b3($p, 1,$unlimit);
        $data = Report::making($query, $quary, $p->p, $p->l);
        $data->sumar = Report::summaryReport01b3($p ,0 ,$unlimit);
        return $data;
    }
    public static function queryReport02a($p, $total = 0, $unlimit = false) {
        $dt = isset($p->a) ? $p->a : date('Y-m');
        if($dt<date('Y-m')){
            $where[]  = "";
            if ($p->s) {
                $where[] = "r.branch_id IN ($p->s)";
            }
            if ($p->r) {
                $where[] = "r.product_id IN ($p->r)";
            }
            if ($p->k) {
                $where[] = "(s.crm_id LIKE '%$p->k%' OR s.name LIKE '%$p->k%')";
            }
            if($p->c) {
                $where[]= "r.cm_id IN ($p->c)";
            }
            if ($where) {
                $strWhere = implode(' AND ', $where);
            }
            $qtotal = "SELECT COUNT(DISTINCT r.id) total 
                        FROM report_pending AS r 
                            LEFT JOIN students AS s ON s.id=r.student_id 
                        WHERE r.report_month = '$dt' $strWhere";
            if ($total) {
                $resp = $qtotal;
            } else {
                $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
                $resp = "SELECT s.crm_id, s.accounting_id, s.name AS student_name,
                            (SELECT name FROM branches WHERE id=r.branch_id) AS branch_name,
                            (SELECT name FROM products WHERE id=r.product_id) AS product_name,
                            (SELECT hrm_id FROM users WHERE id=r.cm_id) AS hrm_id,
                            (SELECT full_name FROM users WHERE id=r.cm_id) AS cm_name,
                            (SELECT name FROM tuition_fee WHERE id=r.tuition_fee_id) AS tuition_fee_name,
                            r.sessions AS summary_sessions,
                            r.start_date, r.end_date
                        FROM report_pending AS r 
                            LEFT JOIN students AS s ON s.id=r.student_id 
                        WHERE r.report_month = '$dt' $strWhere";
                if (!$unlimit ) {
                    $resp.= " $lim";
                }
            }
        }else{
            $where[]  = "";
            if ($p->s) {
                $where[] = "c.branch_id IN ($p->s)";
            }
            
            if ($p->r) {
                $where[] = "c.product_id IN ($p->r)";
            }
            if ($p->k) {
                $where[] = "(s.crm_id LIKE '%$p->k%' OR s.name LIKE '%$p->k%')";
            }
            if($p->c) {
                $where[]= "t.cm_id IN ($p->c)";
            }
            if ($where) {
                $strWhere = implode(' AND ', $where);
            }
            $qtotal = "SELECT COUNT(DISTINCT s.id) total 
                        FROM contracts AS c 
                            LEFT JOIN students AS s ON s.id=c.student_id 
                            LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status=1
                        WHERE s.status>0 AND c.status!=7 AND c.class_id IS NULL AND c.type>0 AND c.summary_sessions>0 $strWhere
                            AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1)
                            AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= CURRENT_DATE AND `start_date`<=CURRENT_DATE AND `status`=2) =0 ";
            if ($total) {
                $resp = $qtotal;
            } else {
                $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
                $resp = "SELECT s.crm_id, s.accounting_id, s.name AS student_name,
                            (SELECT name FROM branches WHERE id=c.branch_id) AS branch_name,
                            (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                            (SELECT hrm_id FROM users WHERE id=t.cm_id) AS hrm_id,
                            (SELECT full_name FROM users WHERE id=t.cm_id) AS cm_name,
                            (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                            c.summary_sessions,
                            c.start_date, c.end_date
                        FROM contracts AS c 
                            LEFT JOIN students AS s ON s.id=c.student_id 
                            LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status=1
                        WHERE s.status>0 AND c.status!=7 AND c.class_id IS NULL AND c.type>0 AND c.summary_sessions>0 $strWhere
                            AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1)
                            AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= CURRENT_DATE AND `start_date`<=CURRENT_DATE AND `status`=2) =0";
                if (!$unlimit ) {
                    $resp.= " $lim";
                }
            }
        }
        
        return $resp;
    }
    public static function queryReport02b($p, $total = 0, $unlimit = false) {
        $dt = isset($p->a) ? $p->a : date('Y-m');
        if($dt<date('Y-m')){
            $where[]  = "";
            if ($p->s) {
                $where[] = "r.branch_id IN ($p->s)";
            }
            if ($p->e !=-1) {
                $where[]=" r.is_reserved = $p->e";
            }
            if ($p->r) {
                $where[] = "r.product_id IN ($p->r)";
            }
            if ($p->k) {
                $where[] = "(s.crm_id LIKE '%$p->k%' OR s.name LIKE '%$p->k%')";
            }
            if($p->c) {
                $where[]= "r.cm_id IN ($p->c)";
            }
            if ($where) {
                $strWhere = implode(' AND ', $where);
            }
            $qtotal = "SELECT COUNT(DISTINCT s.id) total 
                        FROM report_reserve AS r 
                            LEFT JOIN students AS s ON s.id=r.student_id 
                        WHERE r.report_month ='$dt' $strWhere";
            if ($total) {
                $resp = $qtotal;
            } else {
                $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
                $resp = "SELECT s.crm_id, s.accounting_id, s.name AS student_name,
                            (SELECT name FROM branches WHERE id=r.branch_id) AS branch_name,
                            (SELECT name FROM products WHERE id=r.product_id) AS product_name,
                            (SELECT hrm_id FROM users WHERE id=r.cm_id) AS hrm_id,
                            (SELECT full_name FROM users WHERE id=r.cm_id) AS cm_name,
                            (SELECT name FROM tuition_fee WHERE id=r.tuition_fee_id) AS tuition_fee_name,
                            r.sessions AS summary_sessions,
                            r.start_date, r.end_date,
                            (SELECT is_reserved FROM reserves WHERE contract_id=r.contract_id AND student_id=s.id AND end_date>= CURRENT_DATE AND `start_date`<=CURRENT_DATE AND `status`=2 LIMIT 1) AS is_reserved
                        FROM report_reserve AS r 
                            LEFT JOIN students AS s ON s.id=r.student_id 
                        WHERE r.report_month ='$dt' $strWhere";
                if (!$unlimit ) {
                    $resp.= " $lim";
                }
            }
        }else{
            $where[]  = "";
            if ($p->s) {
                $where[] = "c.branch_id IN ($p->s)";
            }
            $cond="";
            if ($p->e !=-1) {
                $cond.=" AND is_reserved = $p->e";
            }
            if ($p->r) {
                $where[] = "c.product_id IN ($p->r)";
            }
            if ($p->k) {
                $where[] = "(s.crm_id LIKE '%$p->k%' OR s.name LIKE '%$p->k%')";
            }
            if($p->c) {
                $where[]= "t.cm_id IN ($p->c)";
            }
            if ($where) {
                $strWhere = implode(' AND ', $where);
            }
            $qtotal = "SELECT COUNT(DISTINCT s.id) total 
                        FROM contracts AS c 
                            LEFT JOIN students AS s ON s.id=c.student_id 
                            LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status=1
                        WHERE s.status>0 AND c.status!=7 AND c.type>0 AND c.summary_sessions>0 $strWhere
                            AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1)
                            AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= CURRENT_DATE AND `start_date`<=CURRENT_DATE AND `status`=2 $cond) >0 ";
            if ($total) {
                $resp = $qtotal;
            } else {
                $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
                $resp = "SELECT s.crm_id, s.accounting_id, s.name AS student_name,
                            (SELECT name FROM branches WHERE id=c.branch_id) AS branch_name,
                            (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                            (SELECT hrm_id FROM users WHERE id=t.cm_id) AS hrm_id,
                            (SELECT full_name FROM users WHERE id=t.cm_id) AS cm_name,
                            (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                            c.summary_sessions,
                            c.start_date, c.end_date,
                            (SELECT is_reserved FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= CURRENT_DATE AND `start_date`<=CURRENT_DATE AND `status`=2 LIMIT 1) AS is_reserved
                        FROM contracts AS c 
                            LEFT JOIN students AS s ON s.id=c.student_id 
                            LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status=1
                        WHERE s.status>0 AND c.status!=7 AND c.type>0 AND c.summary_sessions>0 $strWhere
                            AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1)
                            AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= CURRENT_DATE AND `start_date`<=CURRENT_DATE AND `status`=2 $cond) >0";
                if (!$unlimit ) {
                    $resp.= " $lim";
                }
            }
        }
        return $resp;
    }
    public static function report_r01($p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        $resp = "";
        $where = ' AND s.id IS NOT NULL ';
        if ($p->k != '') {
            $where .= " AND (s.name like '%$p->k%' or s.stu_id like '%$p->k%' or s.crm_id like '%$p->k%' or s.accounting_id like '%$p->k%') ";
        }
        if ($p->s != '') {
            $where .= " AND sh.branch_id in ($p->s) ";
        }
        if ($total) {
            $resp = "SELECT
                        count(DISTINCT student_id) as total
                    FROM
                        salehub_create_contract AS sh
                        LEFT JOIN students AS s ON s.id=sh.student_id
                    WHERE
                        DATE_FORMAT(sh.created_at,'%Y-%m') = '$p->a'
                        $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        s.crm_id, s.name as student_name, s.accounting_id,s.date_of_birth,
                        DATE_FORMAT(sh.created_at,'%Y-%m-%d') AS date_create_contract,
                        (SELECT DATE_FORMAT(updated_at,'%Y-%m-%d') FROM log_student_update WHERE content='Checkin học sinh' AND student_id=s.id) AS date_checkin,
                        (SELECT full_name FROM users WHERE id=s.creator_id) AS creator_checkin_name,
                        (SELECT name FROM  tuition_fee WHERE id=sh.tuition_fee_id) AS tuition_fee_name,
                        (SELECT price FROM  tuition_fee WHERE id=sh.tuition_fee_id) AS price,
                        (SELECT name FROM branches WHERE id=sh.branch_id) AS branch_name,
                        (SELECT must_charge FROM contracts WHERE id=sh.contract_id) AS must_charge,
                        (SELECT total_charged FROM contracts WHERE id=sh.contract_id) AS total_charged,
                        s.gud_name1, s.gud_mobile1,
                        (SELECT name FROM source_detail WHERE id = s.source_detail) AS source_detail
                    FROM
                        salehub_create_contract AS sh
                        LEFT JOIN students AS s ON s.id=sh.student_id
                    WHERE
                        DATE_FORMAT(sh.created_at,'%Y-%m') = '$p->a'
                        $where ORDER BY sh.id DESC";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function report_r02($p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        $resp = "";
        $where = ' s.id IS NOT NULL AND s.source=26 AND sh.type>0 AND sh.count_recharge=0';
        if ($p->k != '') {
            $where .= " AND (s.name like '%$p->k%' or s.stu_id like '%$p->k%' or s.crm_id like '%$p->k%' or s.accounting_id like '%$p->k%') ";
        }
        if ($p->s != '') {
            $where .= " AND sh.branch_id in ($p->s) ";
        }
        if($p->f !=''){
            $where .= " AND DATE_FORMAT(sh.created_at,'%Y-%m-%d') >= '$p->f' ";
        }
        if($p->t !=''){
            $where .= " AND DATE_FORMAT(sh.created_at,'%Y-%m-%d') <= '$p->t' ";
        }
        if ($total) {
            $resp = "SELECT
                        count(DISTINCT student_id) as total
                    FROM
                        contracts AS sh
                        LEFT JOIN students AS s ON s.id=sh.student_id
                    WHERE
                        $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        s.crm_id, s.name as student_name, s.accounting_id,s.date_of_birth,
                        DATE_FORMAT(sh.created_at,'%Y-%m-%d') AS date_create_contract,
                        (SELECT name FROM  tuition_fee WHERE id=sh.tuition_fee_id) AS tuition_fee_name,
                        (SELECT price FROM  tuition_fee WHERE id=sh.tuition_fee_id) AS price,
                        (SELECT name FROM branches WHERE id=sh.branch_id) AS branch_name,
                        sh.must_charge,sh.total_charged,s.gud_mobile1
                    FROM
                        contracts AS sh
                        LEFT JOIN students AS s ON s.id=sh.student_id
                    WHERE
                        $where ORDER BY sh.id DESC";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function report_r04($p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = ' s.id IS NOT NULL ';
        if ($p->k != '') {
            $where .= " AND (s.name like '%$p->k%' or s.stu_id like '%$p->k%' or s.crm_id like '%$p->k%' or s.accounting_id like '%$p->k%') ";
        }
        if ($p->s != '') {
            $where .= " AND c.branch_id in ($p->s) ";
        }
        if($p->f !=''){
            $where .= " AND DATE_FORMAT(c.created_at,'%Y-%m-%d') >= '$p->f' ";
        }
        if($p->t !=''){
            $where .= " AND DATE_FORMAT(c.created_at,'%Y-%m-%d') <= '$p->t' ";
        }
        if ($total) {
            $resp = "SELECT
                        count(DISTINCT student_id) as total
                    FROM
                        contracts AS c
                        LEFT JOIN students AS s ON s.id=c.student_id
                    WHERE
                     c.count_recharge =0  AND (c.total_charged>0 OR debt_amount=0) AND $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        s.accounting_id,s.name,
                        (SELECT name FROM  branches WHERE id=c.branch_id) AS branch_name,
                        (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                        (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                        IF(c.total_charged=0 , 'Học bổng','Thường') AS contract_type, c.must_charge, c.total_charged,s.gud_mobile1,
                        (SELECT full_name FROM users WHERE id=c.ec_id) AS ec_name,
                        IF(c.class_id is NOT NULL ,'Đang học', IF(debt_amount>0,'Cọc','Chưa học')) AS status_student,
                        (SELECT charge_date FROM payment WHERE contract_id=c.id ORDER BY charge_date DESC LIMIT 1) AS charge_date
                    FROM
                        contracts AS c
                        LEFT JOIN students AS s ON s.id=c.student_id
                    WHERE
                        c.count_recharge =0  AND (c.total_charged>0 OR debt_amount=0) AND $where ORDER BY c.id DESC";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function report_r05($p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = ' s.id IS NOT NULL AND s.source ';
        if ($p->k != '') {
            $where .= " AND (s.name like '%$p->k%' or s.stu_id like '%$p->k%' or s.crm_id like '%$p->k%' or s.accounting_id like '%$p->k%') ";
        }
        if ($p->s != '') {
            $where .= " AND c.branch_id in ($p->s) ";
        }
        if($p->f !=''){
            $where .= " AND p.charge_date >= '$p->f' ";
        }
        if($p->t !=''){
            $where .= " AND p.charge_date <= '$p->t' ";
        }
        if($p->role_id==68){
            $where .=" AND c.ec_id=$p->user_id";
        }
        if($p->role_id==69){
            $list_ec = u::query("SELECT u.id FROM users AS u LEFT JOIN users AS u1 ON u1.hrm_id=u.superior_id WHERE u1.id=$p->user_id");
            $list_ec_id = $p->user_id;
            foreach($list_ec AS $row){
                $list_ec_id.=",".$row->id;
            }
            $where .=" AND c.ec_id IN ( $list_ec_id)";
        }
        if ($total) {
            $resp = "SELECT
                        count(DISTINCT student_id) as total
                    FROM
                        payment AS p 
                        LEFT JOIN contracts AS c ON c.id=p.contract_id
                        LEFT JOIN students AS s ON s.id=c.student_id
                    WHERE
                        $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT DATE_FORMAT(p.created_at,'%Y-%m-%d') AS created_date,
                        p.charge_date,s.accounting_id,c.count_recharge, s.name,s.gud_name1,
                        (SELECT name  FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                        (SELECT name FROM branches WHERE id=c.branch_id) AS branch_name,
                        c.start_date, (SELECT hrm_id FROM users WHERE id=c.ec_id) AS ec_hrm,
                        (SELECT  full_name FROM  users WHERE id=c.ec_id) AS ec_name,
                        (SELECT name FROM term_user_branch AS t LEFT JOIN branches AS b ON b.id=t.branch_id WHERE t.user_id=c.ec_id AND t.status=1 LIMIT 1) AS ec_branch,
                        p.note, (SELECT price FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_price,
                        c.discount_value,c.total_discount, c.coupon, p.must_charge,p.amount,p.debt,c.accounting_id AS contract_accounting,
                        (SELECT discount FROM discount_codes WHERE code=c.coupon) AS discount_coupon,
                        c.count_recharge,
                        (SELECT b.name FROM transfer_checkin AS t LEFT JOIN branches AS b ON b.id=t.from_branch_id WHERE t.status=2 AND t.student_id=s.id ORDER BY t.id DESC LIMIT 1) AS pre_branch,
                        (SELECT u.full_name FROM transfer_checkin AS t LEFT JOIN users AS u ON u.id=t.from_ec_id WHERE t.status=2 AND t.student_id=s.id ORDER BY t.id DESC LIMIT 1) AS pre_ec_name
                    FROM
                        payment AS p 
                        LEFT JOIN contracts AS c ON c.id=p.contract_id
                        LEFT JOIN students AS s ON s.id=c.student_id
                    WHERE
                       $where ORDER BY p.id DESC";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function report_r06($p, $request,$total = 0, $unlimit = false) {
        $resp = "";
        $where = '';
        $resp = "";
        $where = ' s.id IS NOT NULL AND sh.type>0 ';
        if ($request->source_id != '') {
            $where .= " AND s.source = $request->source_id";
        }
        if($request->users_data->role_id=='81'){
            $where .= " AND (s.source = 32 OR s.source = 27)";
        }
        if($request->users_data->role_id=='1300'){
            $where .= " AND s.source = 32";
        }
        if ($request->contract_type == 1) {
            $where .= " AND sh.count_recharge=0";
        }elseif($request->contract_type == 2){
            $where .= " AND sh.count_recharge>0";
        }
        if ($p->k != '') {
            $where .= " AND (s.name like '%$p->k%' or s.stu_id like '%$p->k%' or s.crm_id like '%$p->k%' or s.accounting_id like '%$p->k%') ";
        }
        if ($p->s != '') {
            $where .= " AND sh.branch_id in ($p->s) ";
        }
        if($p->f !=''){
            $where .= " AND sh.created_at >= '$p->f' ";
        }
        if($p->t !=''){
            $where .= " AND sh.created_at <= '$p->t' ";
        }
        if ($total) {
            $resp = "SELECT
                        count(sh.id) as total
                    FROM
                        contracts AS sh
                        LEFT JOIN students AS s ON s.id=sh.student_id
                    WHERE
                        $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        s.crm_id, s.name as student_name, s.accounting_id,s.date_of_birth,s.gud_name1,s.gud_mobile1,
                        (SELECT CONCAT(full_name,' - ',hrm_id) FROM users WHERE id =s.creator_id) AS creator_name,
                        DATE_FORMAT(sh.created_at,'%Y-%m-%d') AS date_create_contract,
                        (SELECT name FROM  tuition_fee WHERE id=sh.tuition_fee_id) AS tuition_fee_name,
                        (SELECT price FROM  tuition_fee WHERE id=sh.tuition_fee_id) AS price,
                        (SELECT name FROM branches WHERE id=sh.branch_id) AS branch_name,
                        sh.must_charge,sh.total_charged,s.gud_mobile1,
                        (SELECT name FROM sources WHERE id =s.source) AS source_name,
                        IF(sh.count_recharge=0,'Mới','Tái Phí') AS contract_type,
                        (SELECT name FROM source_detail WHERE id =s.source_detail) AS source_detail
                    FROM
                        contracts AS sh
                        LEFT JOIN students AS s ON s.id=sh.student_id
                    WHERE
                        $where ORDER BY sh.id DESC";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function report_r07($p, $request,$total = 0, $unlimit = false) {
        $arr_month = explode("_",$request->report_month);
        $list_report_week = u::query("SELECT * FROM report_weeks WHERE `year`=".$arr_month[0]." AND `group`=".$arr_month[1]." AND `month`=".$arr_month[2]);
        $list = "";
        foreach($list_report_week AS $week){
            $list.= $list ? ",".$week->id : $week->id;
        }
        $last_report_student_week = u::first("SELECT report_week_id FROM report_students WHERE report_week_id IN($list) ORDER BY id DESC LIMIT 1");
        $last_report_week_id  = isset($last_report_student_week->report_week_id) ? $last_report_student_week->report_week_id : 0;

        $resp = "";
        $where = " r.report_week_id = $last_report_week_id AND r.is_lock=1 AND r.product_id=1";
        if ($p->k != '') {
            $where .= " AND (s.name like '%$p->k%' or s.stu_id like '%$p->k%' or s.crm_id like '%$p->k%' or s.accounting_id like '%$p->k%') ";
        } 
        if ($request->branch_id != -1 && $request->branch_id!=0) {
            $where .= " AND r.branch_id = $request->branch_id ";
        }
        if ($request->teacher_id != -1 && $request->teacher_id!=0) {
            $where .= " AND r.teacher_id = $request->teacher_id ";
        }
        if ($request->class_id != -1 && $request->class_id!=0) {
            $where .= " AND r.class_id = $request->class_id ";
        }
        if($request->users_data->role_id == 36){
            $where.=" AND r.teacher_id=".$request->users_data->id;
        }
        if ($total) {
            $resp = "SELECT
                        count(DISTINCT student_id) as total
                    FROM
                        report_students AS r
                        LEFT JOIN students AS s ON s.id=r.student_id
                    WHERE
                        $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        r.student_id, b.name AS branch_name,cl.cls_name AS class_name,
                        t.full_name AS teacher_name, 
                        (SELECT name FROM `level` WHERE id=r.level_id) AS level_name,
                        s.crm_id,s.accounting_id,s.name AS student_name,
                        IF(s.gender='M','Nam','Nữ') AS gender,
                        CONCAT(c.start_date,'~',c.end_date) AS thoigian_dangky,
                        'Đã đăng ký' AS student_status, c.created_at AS ngay_dangky,
                        c.note,(SELECT name FROM products WHERE id=r.product_id) AS product_name,
                        (SELECT score_demo FROM report_student_demo WHERE student_id =r.student_id) AS score_demo,
                        r.report_type, r.comment, r.suggestion, 
                        (SELECT score FROM report_students WHERE student_id=r.student_id AND report_week_id=".$list_report_week[0]->id." AND is_lock=1) AS score_week_1,
                        (SELECT score FROM report_students WHERE student_id=r.student_id AND report_week_id=".$list_report_week[1]->id." AND is_lock=1) AS score_week_2,
                        (SELECT score FROM report_students WHERE student_id=r.student_id AND report_week_id=".$list_report_week[2]->id." AND is_lock=1) AS score_week_3,
                        (SELECT score FROM report_students WHERE student_id=r.student_id AND report_week_id=".$list_report_week[3]->id." AND is_lock=1) AS score_week_4,
                        (SELECT score FROM report_students WHERE student_id=r.student_id ORDER BY id DESC LIMIT 1 ) AS score_week_last
                    FROM
                        report_students AS r
                        LEFT JOIN students AS s ON s.id=r.student_id
                        LEFT JOIN branches AS b ON b.id=r.branch_id
                        LEFT JOIN classes AS cl ON cl.id=r.class_id
                        LEFT JOIN users AS t ON t.id=r.teacher_id
                        LEFT JOIN contracts AS c ON c.id=r.contract_id
                    WHERE
                        $where ORDER BY r.id DESC";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function report_r08($p, $request,$total = 0, $unlimit = false) {
        $arr_month = explode("_",$request->report_month);
        $list_report_week = u::query("SELECT * FROM report_weeks WHERE `year`=".$arr_month[0]." AND `group`=".$arr_month[1]." AND fix_group=1");
        $list = "";
        foreach($list_report_week AS $week){
            $list.= $list ? ",".$week->id : $week->id;
        }
        $last_report_student_week = u::first("SELECT report_week_id FROM report_students WHERE report_week_id IN($list) ORDER BY id DESC LIMIT 1");
        $last_report_week_id  = isset($last_report_student_week->report_week_id) ? $last_report_student_week->report_week_id : 0;

        $resp = "";
        $where = " r.report_week_id = $last_report_week_id AND r.is_lock=1 AND r.product_id!=1";
        if ($p->k != '') {
            $where .= " AND (s.name like '%$p->k%' or s.stu_id like '%$p->k%' or s.crm_id like '%$p->k%' or s.accounting_id like '%$p->k%') ";
        } 
        if ($request->branch_id != -1 && $request->branch_id!=0) {
            $where .= " AND r.branch_id = $request->branch_id ";
        }
        if ($request->teacher_id != -1 && $request->teacher_id!=0) {
            $where .= " AND r.teacher_id = $request->teacher_id ";
        }
        if ($request->class_id != -1 && $request->class_id!=0) {
            $where .= " AND r.class_id = $request->class_id ";
        }
        if($request->users_data->role_id == 36){
            $where.=" AND r.teacher_id=".$request->users_data->id;
        }
        if ($total) {
            $resp = "SELECT
                        count(DISTINCT student_id) as total
                    FROM
                        report_students AS r
                        LEFT JOIN students AS s ON s.id=r.student_id
                    WHERE
                        $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        r.student_id, b.name AS branch_name,cl.cls_name AS class_name,
                        t.full_name AS teacher_name, 
                        (SELECT name FROM `level` WHERE id=r.level_id) AS level_name,
                        s.crm_id,s.accounting_id,s.name AS student_name,
                        IF(s.gender='M','Nam','Nữ') AS gender,
                        CONCAT(c.start_date,'~',c.end_date) AS thoigian_dangky,
                        'Đã đăng ký' AS student_status, c.created_at AS ngay_dangky,
                        c.note,(SELECT name FROM products WHERE id=r.product_id) AS product_name,
                        (SELECT score_demo FROM report_student_demo WHERE student_id =r.student_id) AS score_demo,
                        r.report_type, r.comment, r.suggestion, 
                        (SELECT score FROM report_students WHERE student_id=r.student_id AND report_week_id=".$list_report_week[0]->id." AND is_lock=1) AS score_week_1,
                        (SELECT score FROM report_students WHERE student_id=r.student_id ORDER BY id DESC LIMIT 1 ) AS score_week_last
                    FROM
                        report_students AS r
                        LEFT JOIN students AS s ON s.id=r.student_id
                        LEFT JOIN branches AS b ON b.id=r.branch_id
                        LEFT JOIN classes AS cl ON cl.id=r.class_id
                        LEFT JOIN users AS t ON t.id=r.teacher_id
                        LEFT JOIN contracts AS c ON c.id=r.contract_id
                    WHERE
                        $where ORDER BY r.id DESC";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function report_r09($p, $request,$total = 0, $unlimit = false) {
        $resp = "";
        $where = " s.checked = 1 ";
        if ($p->s != '') {
            $where .= " AND s.branch_id in ($p->s) ";
        }
        if($request->from_date !=''){
            $where .= " AND s.update_checked >= '$request->from_date 00:00:00' ";
        }
        if($request->to_date !=''){
            $where .= " AND s.update_checked <= '$request->to_date 23:59:59' ";
        }
        if($request->users_data->role_id == 81){
            $where.=" AND s.source IN (27,32)";
        }
        if ($total) {
            $resp = "SELECT
                        count(DISTINCT id) as total
                    FROM
                        students AS s
                    WHERE
                        $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        s.id, s.crm_id, (SELECT name FROM branches WHERE id=s.branch_id) AS branch_name,s.name,
                        s.update_checked AS checkin_at, (SELECT name FROM sources WHERE id=s.source) AS source_name,
                        (SELECT name FROM source_detail WHERE id=s.source_detail) AS source_detail_name,
                        (SELECT CONCAT(full_name,' - ',hrm_id) FROM users AS u LEFT JOIN  term_student_user AS t ON t.ec_id=u.id WHERE t.student_id=s.id) AS ec_name,
                        (SELECT CONCAT(full_name,' - ',hrm_id) FROM users AS u WHERE s.updator_checked=u.id) AS updator_checked
                    FROM
                        students AS s 
                    WHERE
                        $where ORDER BY s.id DESC";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function report_r10($p, $total = 0, $unlimit = false) {
        $resp = "";
        $where = ' s.id IS NOT NULL ';
        if ($p->k != '') {
            $where .= " AND (s.name like '%$p->k%' or s.stu_id like '%$p->k%' or s.crm_id like '%$p->k%' or s.accounting_id like '%$p->k%') ";
        }
        if ($p->s != '') {
            $where .= " AND c.branch_id in ($p->s) ";
        }
        if($p->f !=''){
            $where .= " AND DATE_FORMAT(c.enrolment_start_date,'%Y-%m-%d') >= '$p->f' ";
        }
        if($p->t !=''){
            $where .= " AND DATE_FORMAT(c.enrolment_start_date,'%Y-%m-%d') <= '$p->t' ";
        }
        if ($total) {
            $resp = "SELECT
                        count(DISTINCT student_id) as total
                    FROM
                        contracts AS c
                        LEFT JOIN students AS s ON s.id=c.student_id
                    WHERE
                     c.type =0  AND c.class_id IS NOT NULL AND c.status!=7 AND $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        s.accounting_id,s.name,s.crm_id,
                        (SELECT name FROM  branches WHERE id=c.branch_id) AS branch_name,
                        (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                        (SELECT cls_name FROM classes WHERE id=c.class_id) AS class_name,
                        c.enrolment_start_date, c.enrolment_last_date
                    FROM
                        contracts AS c
                        LEFT JOIN students AS s ON s.id=c.student_id
                    WHERE
                    c.type =0  AND c.class_id IS NOT NULL AND c.status!=7 AND $where ORDER BY c.enrolment_start_date DESC";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function report_r11($p,$request, $total = 0, $unlimit = false) {
        $resp = "";
        $where = ' s.id IS NOT NULL ';
        if ($p->k != '') {
            $where .= " AND (s.name like '%$p->k%' or s.stu_id like '%$p->k%' or s.crm_id like '%$p->k%' or s.accounting_id like '%$p->k%') ";
        }
        if ($p->s != '') {
            $where .= " AND c.branch_id in ($p->s) ";
        }
        if($request->from !=''){
            $where .= " AND DATE_FORMAT(c.enrolment_start_date,'%Y-%m-%d') >= '$request->from' ";
        }
        if($request->to !=''){
            $where .= " AND DATE_FORMAT(c.enrolment_start_date,'%Y-%m-%d') <= '$request->to' ";
        }
        if ($total) {
            $resp = "SELECT
                        count(DISTINCT student_id) as total
                    FROM
                        contracts AS c
                        LEFT JOIN students AS s ON s.id=c.student_id
                    WHERE
                     c.type =0  AND c.class_id IS NOT NULL AND c.status=7 AND $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        s.accounting_id,s.name,s.crm_id,
                        (SELECT name FROM  branches WHERE id=c.branch_id) AS branch_name,
                        (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                        (SELECT cls_name FROM classes WHERE id=c.class_id) AS class_name,
                        c.enrolment_start_date, c.enrolment_last_date
                    FROM
                        contracts AS c
                        LEFT JOIN students AS s ON s.id=c.student_id
                    WHERE
                    c.type =0  AND c.class_id IS NOT NULL AND c.status=7 AND $where ORDER BY c.enrolment_start_date DESC";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
    public static function report_r12($p, $request,$total = 0, $unlimit = false) {
        $resp = "";
        $where = " 1 ";
        if($request->users_data->id == 323){
            if (!empty($request->scope)) {
                $where .= " AND s.checkin_branch_id in ($p->s) ";
            }
        }else{
            if ($p->s != '') {
                $where .= " AND s.checkin_branch_id in ($p->s) ";
            }
        }
        if($request->from_date !=''){
            $where .= " AND s.checkin_at >= '$request->from_date 00:00:00' ";
        }
        if($request->to_date !=''){
            $where .= " AND s.checkin_at <= '$request->to_date 23:59:59' ";
        }
        if($request->from_date_int !=''){
            $where .= " AND s.created_at >= '$request->from_date_int 00:00:00' ";
        }
        if($request->to_date_int !=''){
            $where .= " AND s.created_at <= '$request->to_date_int 23:59:59' ";
        }
        if($request->keyword !=''){
            $where .= " AND (s.name LIKE '%$request->keyword%' OR s.crm_id LIKE '%$request->keyword%' OR s.gud_mobile_1 LIKE '%$request->keyword%' )";
        }
        if ($total) {
            $resp = "SELECT
                        count(DISTINCT id) as total
                    FROM
                        students AS s
                    WHERE
                        $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        s.id, s.crm_id, (SELECT name FROM branches WHERE id=s.checkin_branch_id) AS branch_name,s.name,
                        s.checkin_at AS checkin_at,s.created_at,
                        (SELECT CONCAT(full_name,' - ',hrm_id) FROM users AS u WHERE s.checkin_by=u.id) AS checkin_by,
                        (SELECT name FROM branches WHERE id=s.checkin_by_branch_id) AS checkin_by_branch_name
                    FROM
                        students AS s 
                    WHERE
                        $where ORDER BY s.checkin_at DESC";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }

    public static function report_r13($p, $request,$total = 0, $unlimit = false) {
        $resp = "";
        $where = " ";
        $where1 = " ";
        $where2 = " ";
        $where3 = " ";
        $where4 = " ";
        $where5 = " ";
        if ($p->s != '') {
            $where .= " AND t.branch_id in ($p->s) ";
        }
        if(!empty($request->ecs)){
            $where .= " AND u.id IN (".implode(",", $request->ecs).") ";
        }
        if(!empty($request->sources)){
            $where1 .= " AND s.source IN (".implode(",", $request->sources).") ";
        }
        if(!empty($request->sources_detail)){
            $where1 .= " AND s.source_detail IN (".implode(",", $request->sources_detail).") ";
        }
        if(!empty($request->branch_checkin)){
            $where1 .= " AND s.checkin_branch_id IN (".implode(",", $request->branch_checkin).") ";
        }
        if($request->to_date !=''){
            $where2 .= " AND s.checkin_at <= '$request->to_date 23:59:59' ";
            $where3 .= " AND s.update_checked <= '$request->to_date 23:59:59' ";
            $where4 .= " AND enrolment_start_date <= '$request->to_date 23:59:59' ";
            $where5 .= " AND created_at <= '$request->to_date 23:59:59' ";
        }
        if($request->from_date !=''){
            $where2 .= " AND s.checkin_at >= '$request->from_date 00:00:00' ";
            $where3 .= " AND s.update_checked >= '$request->from_date 00:00:00' ";
            $where4 .= " AND enrolment_end_date >= '$request->from_date 00:00:00' ";
            $where5 .= " AND created_at >= '$request->from_date 00:00:00' ";
        }
        if ($total) {
            $resp = "SELECT
                        count(DISTINCT u.id) as total
                    FROM
                        users AS u
                        LEFT JOIN term_user_branch AS t ON t.user_id=u.id AND t.status=1
                    WHERE u.status=1 AND t.role_id IN(68,69)
                        $where 
            ";
        } else {
            $lim = (isset($p->d) && isset($p->l)) ? "LIMIT $p->d, $p->l" : "";
            $resp = "SELECT
                        b.name AS branch_name, u.full_name, u.hrm_id,
                        (SELECT count(s.id) FROM students AS s WHERE s.checkin_by= u.id $where1 $where2) AS comfirm,
                        (SELECT count(s.id) FROM students As s WHERE s.checkin_by= u.id AND s.checked=1 $where1 $where3) AS show_up,
                        (SELECT count(DISTINCT s.id) FROM students AS s WHERE s.checkin_by= u.id AND (SELECT count(id) FROM contracts WHERE type=0 AND class_id IS NOT NULL AND student_id=s.id $where4)>0 $where1 ) AS demo,
                        (SELECT count(DISTINCT s.id) FROM students AS s WHERE s.checkin_by= u.id AND (SELECT count(id) FROM contracts WHERE type>0 AND student_id=s.id $where5)>0  $where1) AS deal
                    FROM users AS u
                        LEFT JOIN term_user_branch AS t ON t.user_id=u.id AND t.status=1
                        LEFT JOIN branches AS b ON t.branch_id= b.id
                    WHERE u.status=1 AND t.role_id IN (68,69)
                        $where  ORDER BY t.branch_id";
            if (!$unlimit) {
                $resp.= " $lim";
            }
        }
        return $resp;
    }
}
