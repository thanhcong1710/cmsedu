<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Dashboard;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Providers\UtilityServiceProvider as u;
use App\Models\Response;

class DashboardController extends Controller
{
    //
    public function getDataAll(Request $request)
    {
      $date = $request->date;
      if( $date AND $date != '' ) {
        $fromDate = date('Y-m-01',strtotime($date));
        $toDate   = date('Y-m-t',strtotime($date));
      } else {
        $fromDate = date('Y-m-01');
        $toDate = date('Y-m-d');
      }

      $session = $request->users_data;
      $branches_access = $session->branches_ids;

      $listAccess = DB::table('branches')->whereIn('id',explode(',',$branches_access))
                    ->select(['hrm_id'])->pluck('hrm_id')->toArray();
      $listAccess = implode(',',$listAccess);

      if (in_array($session->role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {
        $whereX = "";
      } else {
        $whereX = " AND ma_crm in ( $listAccess ) ";
      }

        $connection = DB::connection('mysql_1');
        $sql = "
        SELECT 
        
        sum(ps_no1) as ps_no1,
        sum(ps_no1kh) as ps_no1kh,
        vung_mien,
        ma_crm,
        ma_eff,
        ten,
        sapxep
        
        FROM dbo_fuc_dashboard01_new 
        WHERE (date BETWEEN '$fromDate' AND '$toDate' ) AND in_dam = 0 
        $whereX
        GROUP BY ma_crm ORDER BY ps_no1 DESC ";

        $sql_zone = "SELECT * FROM dbo_fuc_dashboard01_new WHERE invisible > 0 and sapxep > 0 GROUP BY ten ORDER BY invisible ";
        $result_zone = u::msquery($sql_zone);
        $result = $connection->select(DB::raw($sql));

        $nowD = date('Y-m-d 00:00:00');
        $ps_ngay = $connection->select(DB::raw("
          SELECT * FROM dbo_fuc_dashboard01_new 
          WHERE in_dam = 0  
					and date = '$nowD' 
					GROUP BY ma_crm ORDER BY ps_no1 DESC 
        "));

        $sqlLocal = Dashboard::getQueryDashboard01New($fromDate, $toDate,$listAccess);
        $i = 0;
        foreach ($result as $item) {
            foreach ($ps_ngay as $ngay) {
              if( $item->ma_crm == $ngay->ma_crm ) {
                $item->ps_ngay = $ngay->ps_ngay;
              }
            }
            if( !isset($item->ps_ngay) )
              $item->ps_ngay = 0;

            foreach( $sqlLocal as $local ) {
              if( $item->ma_crm == $local->hrm_id ) {
                $result[$i]->hs_moi = $local->hs_moi;
                $result[$i]->hs_het_han = $local->hs_het_han;
                $result[$i]->hs_tang_net = $local->hs_tang_net;
                $result[$i]->hs_no = $local->hs_no;
              }
            }
            if ($item->ps_no1kh != 0) {
                $item->percent = round($item->ps_no1 / $item->ps_no1kh * 100,2);
            } else {
                $item->percent = 0;
            }
            $i++;
        }

        $defautZonet = (object)[
          'hs_moi' => 0,
          'hs_het_han' => 0,
          'hs_tang_net' => 0,
          'hs_no' => 0,
          'ps_no1' => 0,
          'ps_no1kh' => 0,
          'ps_ngay' => 0,
          'ten' => '',
          'percent' => 0,
        ];
        $data_zone = [];
        $data_total = (object)$defautZonet;
        $data_total->ten = 'TẤT CẢ';

        $x = 0;

        foreach ($result as $xitem) {
          if( !isset($xitem->hs_moi) )
            $xitem->hs_moi = 0;
          if( !isset($xitem->hs_het_han) )
            $xitem->hs_het_han = 0;
          if( !isset($xitem->hs_tang_net) )
            $xitem->hs_tang_net = 0;
          if( !isset($xitem->hs_no) )
            $xitem->hs_no = 0;





            $data_total->hs_moi = $data_total->hs_moi + $xitem->hs_moi;
            $data_total->hs_het_han = $data_total->hs_het_han + $xitem->hs_het_han;
            $data_total->hs_tang_net = $data_total->hs_tang_net + $xitem->hs_tang_net;
            $data_total->hs_no = $data_total->hs_no + $xitem->hs_no;
            $data_total->ps_no1 = $data_total->ps_no1 + $xitem->ps_no1;
            $data_total->ps_no1kh = $data_total->ps_no1kh + $xitem->ps_no1kh;
            $data_total->ps_ngay = $data_total->ps_ngay + $xitem->ps_ngay;

            if ($data_total->ps_no1kh != 0) {
              $data_total->percent = round($data_total->ps_no1 / $data_total->ps_no1kh * 100, 2);
            } else $data_total->percent = 0;

          $x++;
        }



        foreach ($result_zone as $zitem) {
            $defautZone = (object)[
              'hs_moi' => 0,
              'hs_het_han' => 0,
              'hs_tang_net' => 0,
              'hs_no' => 0,
              'ps_no1' => 0,
              'ps_no1kh' => 0,
              'ps_ngay' => 0,
              'ten' => '',
            ];

            $data_zone[$zitem->ten] = $defautZone;
            $data_zone[$zitem->ten]->ten = $zitem->ten;
              foreach( $result as $val ) {

                if( $zitem->vung_mien == $val->vung_mien AND $val->vung_mien != '' ) {
                  $data_zone[$zitem->ten]->hs_moi = $data_zone[$zitem->ten]->hs_moi + $val->hs_moi;
                  $data_zone[$zitem->ten]->hs_het_han = $data_zone[$zitem->ten]->hs_het_han + $val->hs_het_han;
                  $data_zone[$zitem->ten]->hs_tang_net = $data_zone[$zitem->ten]->hs_tang_net + $val->hs_tang_net;
                  $data_zone[$zitem->ten]->hs_no = $data_zone[$zitem->ten]->hs_no + $val->hs_no;
                  $data_zone[$zitem->ten]->ps_no1 = $data_zone[$zitem->ten]->ps_no1 + $val->ps_no1;
                  $data_zone[$zitem->ten]->ps_no1kh = $data_zone[$zitem->ten]->ps_no1kh + $val->ps_no1kh;
                  $data_zone[$zitem->ten]->ps_ngay = $data_zone[$zitem->ten]->ps_ngay + $val->ps_ngay;
                }
              }
        }

        foreach ($data_zone as $ditem) {
          if ($ditem->ps_no1kh != 0) {
            $ditem->percent = round($ditem->ps_no1 / $ditem->ps_no1kh * 100,2);
          } else $ditem->percent = 0;
        }


        return response()->json([
            'code' => 200,
            'data' => [
              'total' => $data_total,
              'zones' => $data_zone,
              'result' => $result,

            ],
            'user_data' => $session
        ]);
    }

    public function getByInvisible($invisible)
    {
        $data = u::msquery("SELECT *from dbo_fuc_dashboard01 where invisible = $invisible");
        foreach ($data as $item) {
            if ($item->invisible == 0) {
                if ($item->ps_no1 != 0) {
                    $so_no1 = explode(',', $item->ps_no1);
                    $number_no1 = '';
                    foreach ($so_no1 as $s) {
                        $number_no1 .= $s;
                    }
                    $number_no1 = (float)$number_no1;
                    $so_no1kh = explode(',', $item->ps_no1kh);
                    $number_no1kh = '';
                    foreach ($so_no1kh as $s) {
                        $number_no1kh .= $s;
                    }
                    $number_no1kh = (float)$number_no1kh;
                    $item->percent = floor($number_no1 / $number_no1kh * 100);
                }
            }
        }
        return $data;
    }

    public function getTopBranch(Request $request, $rank = null, $rows = 5)
    {
        $session = $request->users_data;
        $role_id = $session->role_id;
        $branches_access = $session->branches_ids;

        $listAccess = DB::table('branches')->whereIn('id',explode(',',$branches_access))
          ->select(['hrm_id'])->pluck('hrm_id')->toArray();
        $listAccess = implode(',',$listAccess);

        if (in_array($session->role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {
          $whereX = "";
        } else {
          $whereX = " AND ma_crm in ( $listAccess ) ";
        }

        $fromDate = date('Y-m-01');
        $toDate = date('Y-m-d');

        $desc = $rank == 1 ? 'desc' : '';
        $query = "
        SELECT 
          ten,
          sum(ps_no1) as ps_no1,
          sum(ps_no1kh) as ps_no1kh
        FROM dbo_fuc_dashboard01_new 
        WHERE in_dam = 0 $whereX AND (date BETWEEN '$fromDate' AND '$toDate' ) 
        GROUP BY ma_crm HAVING ps_no1kh > 0 ORDER BY ps_no1 $desc  LIMIT $rows
        ";

        $connection = DB::connection('mysql_1');
        $data = $connection->select(DB::raw($query));
        return $data;
    }

    public function studentsNewMonth(Request $request)
    {
        $session = json_decode($request->authorized);
        $sql = '';
        if ($session->role_id == ROLE_ADMINISTRATOR) {

        }
    }

    public function studetnRenew(Request $request, $month = null, $year = null)
    {

    }

    public function topLeader(Request $request, $rank = null, $rows = 5)
    {
        $session = $request->users_data;
        $user_id = $session->id;
        $role_id = $session->role_id;
        $branches_access = $session->branches_ids;

        $sql = '';
        $desc = $rank == 1 ? 'desc' : '';
        if (in_array($role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {

            $sql2 = "
              SELECT * from tbldoanhso_team as tbt 
              WHERE in_dam = 1 and ma_nv != '' and ten_nv != '' 
              AND ten_nv != 'KHÔNG CÓ LEADER' AND ten_nv != 'Dữ liệu thiếu Leader' 
              ORDER BY doanhso $desc  LIMIT $rows
            ";
        } else{
          $listAccess = DB::table('branches')->whereIn('id',explode(',',$branches_access))
            ->select(['accounting_id'])->pluck('accounting_id')->toArray();
          $listAccess = implode(',',$listAccess);

          $sql2 = "
              SELECT * from tbldoanhso_team as tbt 
              WHERE in_dam = 1 and ma_nv != '' and ten_nv != '' 
              and ten_nv != 'KHÔNG CÓ LEADER' AND ten_nv != 'Dữ liệu thiếu Leader' 
              AND ma_tt in ( $listAccess ) 
              ORDER BY doanhso $desc  LIMIT $rows
            ";

        }
        $connection = DB::connection('mysql_1');
        $data = $connection->select(DB::raw($sql2));
        foreach ($data as $item) {
          if( isset($item->ma_tt) ) {
            $xItem = Branch::where('accounting_id',$item->ma_tt)->first();
            if( $xItem ) {
              $item->ten_tt = $xItem->name;
            }
          }
        }
        return $data;
    }

    public function topSale(Request $request, $rank = null, $rows = 5)
    {
      $session = $request->users_data;
      $user_id = $session->id;
      $role_id = $session->role_id;
      $branches_access = $session->branches_ids;

      $sql = '';
      $desc = $rank == 1 ? 'desc' : '';
      if (in_array($role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {

        $sql2 = "
              SELECT * from tbldoanhso_team as tbt 
              WHERE in_dam = 0 and ma_nv != '' and ten_nv != '' and ten_nv != 'Dữ liệu thiếu Sale'
              ORDER BY doanhso $desc  LIMIT $rows
            ";
      } else{
        $listAccess = DB::table('branches')->whereIn('id',explode(',',$branches_access))
          ->select(['accounting_id'])->pluck('accounting_id')->toArray();
        $listAccess = implode(',',$listAccess);

        $sql2 = "
              SELECT * from tbldoanhso_team as tbt 
              WHERE in_dam = 0 and ma_nv != '' and ten_nv != '' and ten_nv != 'Dữ liệu thiếu Sale'
              AND ma_tt in ( $listAccess ) 
              ORDER BY doanhso $desc  LIMIT $rows
            ";

      }
      $connection = DB::connection('mysql_1');
      $data = $connection->select(DB::raw($sql2));
      foreach ($data as $item) {
        if( isset($item->ma_tt) ) {
          $xItem = Branch::where('accounting_id',$item->ma_tt)->first();
          if( $xItem ) {
            $item->ten_tt = $xItem->name;
          }
        }
      }
      return $data;
    }

    public function getStudentsStatusMonthly(Request $request, $branch_id)
    {
        $branches = u::getBranchIds($request->users_data);

        if (!in_array($branch_id, $branches)) {
            $branches = [0];
        } else {
            $branches = [$branch_id];
        }
        $id_branch = implode(',', $branches);

        $query = "SELECT c.id AS contract_id,
            c.type as contract_type,
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
            pd.name as product_name,
            pr.name as program_name,
            tf.name as tuition_fee_name,
            p.total as total_amount_charged,
            p.created_at as payment_date,
            s.`name`,
            s.nick,
            s.accounting_id,
            s.crm_id,
            s.stu_id,
            15 - x.dates AS left_dates
            FROM contracts AS c
            LEFT JOIN students AS s ON s.id = c.student_id
            LEFT JOIN branches AS br ON br.id = c.branch_id
            LEFT JOIN products AS pd ON pd.id = c.product_id
            LEFT JOIN programs AS pr ON pr.id = c.program_id
            LEFT JOIN tuition_fee AS tf ON tf.id = c.tuition_fee_id
            LEFT JOIN payment AS p ON c.payment_id = p.id
            LEFT JOIN (SELECT c.id, TIMESTAMPDIFF(DAY,p.created_at,CURDATE()) AS dates FROM contracts AS c LEFT JOIN payment AS p ON c.payment_id = p.id WHERE c.type > 0 AND c.payload = 0 AND c.debt_amount > 0 AND c.payment_id > 0 AND c.branch_id IN ($id_branch) ) AS x ON x.id = c.id
            WHERE c.type > 0 AND c.debt_amount > 0 AND c.payment_id > 0 AND c.branch_id IN ($id_branch) AND x.dates IS NOT NULL
            GROUP BY s.id
            ORDER BY x.dates DESC";

        $students = u::query($query);

        return response()->json($students);
    }

    public function getStudentRenew(Request $request, $month, $year)
    {
        $from_date = date('Y-m-01', strtotime("$year-$month-01"));
        $to_date = date('Y-m-t', strtotime("$year-$month-01"));

        $where = '';

        $branches = u::getBranchIds($request->users_data);
        $id_branch = implode(',', $branches);

        $query = "
            SELECT 
                    ( 
                        SELECT count(DISTINCT e.student_id) 
                        FROM 
                            enrolments AS e 
                            LEFT JOIN contracts AS c ON e.contract_id = c.id 
                            LEFT JOIN students AS s ON c.student_id = s.id 
                            LEFT JOIN tuition_transfer AS tff ON tff.from_contract_id = c.id 
                        WHERE 
                            e.id IN ( 
                                SELECT MAX( e.id ) 
                                FROM enrolments AS e 
                                    LEFT JOIN contracts AS c ON e.contract_id = c.id 
                                    LEFT JOIN students AS s ON c.student_id = s.id 
                                WHERE 
                                    s.branch_id = c.branch_id 
                                GROUP BY s.id 
                            ) 
                            AND e.last_date >= '$from_date' 
                            AND e.last_date <= '$to_date' 
                            AND tff.id IS NULL 
                            AND s.branch_id = b.id
                            AND c.type > 0
                            AND c.status > 0 
                    ) as resign_total, 
                    ( 
                        SELECT count(DISTINCT e.student_id) 
                        FROM 
                            enrolments AS e 
                            LEFT JOIN contracts AS c ON e.contract_id = c.id 
                            LEFT JOIN students AS s ON c.student_id = s.id 
                            LEFT JOIN tuition_transfer AS tff ON tff.from_contract_id = c.id 
                        WHERE 
                            e.id IN ( 
                                SELECT MAX( e.id ) 
                                FROM 
                                    enrolments AS e 
                                    LEFT JOIN contracts AS c ON e.contract_id = c.id 
                                    LEFT JOIN students AS s ON c.student_id = s.id 
                                    WHERE s.branch_id = c.branch_id GROUP BY s.id 
                                )
                            AND c.type > 0
                            AND c.status > 0 
                            AND e.last_date >= '$from_date' 
                            AND e.last_date <= '$to_date' 
                            AND tff.id IS NULL 
                            AND s.branch_id = b.id 
                            AND e.final_last_date > '$to_date' 
                    ) as recharged_total, 
                    b.name as branch_name,
                    b.id AS branch_id 
            FROM branches as b 
            WHERE b.status = 1 AND b.id in ($id_branch)        
         ";
        $data = DB::select(DB::raw($query));
        return $data;
    }

    public function getStudentRenewDetail(Request $request, $branch_id, $month, $year)
    {
        $from_date = date('Y-m-01', strtotime("$year-$month-01"));
        $to_date = date('Y-m-t', strtotime("$year-$month-01"));

        $where = '';

        $branches = u::getBranchIds($request->users_data);

        if (!in_array($branch_id, $branches)) {
            $branches = [0];
        } else {
            $branches = [$branch_id];
        }

        $id_branch = implode(',', $branches);
        $where .= " AND br.id in ($id_branch) ";

        $q = "SELECT
            c.id AS contract_id,
            s.id as student_id,
            br.id AS branch_id,
            s.name AS student_name,
            s.nick AS nick,
            s.accounting_id,
            s.stu_id,
            pd.NAME AS product_name,
            pr.NAME AS program_name,
            cl.cls_name AS class_name,
            s.type AS student_type,
            e.last_date AS end_date,
            tf.name AS tuition_fee_name,
            tf.price AS tuition_fee_price,
            c.count_recharge AS recharge_time,
            s.crm_id,
            br.name AS branch_name,
            CONCAT( u1.full_name, ' - ', u1.username ) AS ec_name,
            CONCAT( u2.full_name, ' - ', u2.username ) AS cm_name,
            IF ( e.final_last_date > '$to_date', c.must_charge , '' ) as must_charge,
            IF ( e.final_last_date > '$to_date', 'Thành công', IF ( '$to_date' <= CURDATE( ), 'Thất bại', '' ) ) as success,
            IF(DATE_ADD('$to_date', INTERVAL +20 DAY) > e.last_date, IF ( e.final_last_date > '$to_date', 0, 1), 0) AS soon
          FROM
            enrolments AS e 
            LEFT JOIN contracts AS c ON e.contract_id = c.id 
            LEFT JOIN students AS s ON c.student_id = s.id 
            LEFT JOIN tuition_transfer AS tff ON tff.from_contract_id = c.id 
            LEFT JOIN programs AS pr ON pr.id = c.program_id 
            LEFT JOIN classes AS cl ON cl.program_id = pr.id 
            LEFT JOIN products AS pd ON pd.id = c.product_id 
            LEFT JOIN payment AS p ON p.contract_id = c.id 
            LEFT JOIN branches AS br ON br.id = s.branch_id 
            LEFT JOIN tuition_fee AS tf ON tf.id = c.tuition_fee_id 
            LEFT JOIN users AS u1 ON u1.id = c.ec_id 
            LEFT JOIN users AS u2 ON u2.id = c.cm_id 
            WHERE
                e.id IN (
                    SELECT MAX( e.id ) 
                    FROM enrolments AS e 
                        LEFT JOIN contracts AS c ON e.contract_id = c.id 
                        LEFT JOIN students AS s ON c.student_id = s.id 
                    WHERE 
                        s.branch_id = c.branch_id 
                        AND s.branch_id IN( $branch_id ) GROUP BY s.id 
                ) 
                AND e.last_date >= '$from_date' 
                AND e.last_date <= '$to_date' 
                AND tff.id IS NULL 
                AND c.type > 0
                AND c.status > 0
          GROUP BY s.id";

        $recharges = u::query($q);

        return $recharges;
    }

    public function getStudentsStatusMonthlyOverview(Request $request)
    {
        $branches = u::getBranchIds($request->users_data);
        if (empty($branches)) {
            $branches = [0];
        }
        $id_branch = implode(',', $branches);

        $query = "
           
                SELECT IF(t.branch_id IS NOT NULL, COUNT(*), 0)AS total_students, b.id AS branch_id, b.name AS branch_name
                FROM
                branches AS b LEFT JOIN
                (
                    SELECT 
                        s.id AS student_id,
                        br.id AS branch_id,
                        br.name AS branch_name
                    FROM contracts AS c
                        LEFT JOIN students AS s ON s.id = c.student_id
                        LEFT JOIN branches AS br ON br.id = c.branch_id
                        LEFT JOIN products AS pd ON pd.id = c.product_id
                        LEFT JOIN programs AS pr ON pr.id = c.program_id
                        LEFT JOIN tuition_fee AS tf ON tf.id = c.tuition_fee_id
                        LEFT JOIN payment AS p ON c.payment_id = p.id
                        LEFT JOIN (SELECT c.id, TIMESTAMPDIFF(DAY,p.created_at,CURDATE()) AS dates FROM contracts AS c LEFT JOIN payment AS p ON c.payment_id = p.id WHERE c.type > 0 AND c.payload = 0 AND c.debt_amount > 0 AND c.payment_id > 0 AND c.branch_id IN ($id_branch) ) AS x ON x.id = c.id
                    WHERE c.type > 0 AND c.debt_amount > 0 AND c.payment_id > 0 AND c.branch_id IN ($id_branch) AND x.dates IS NOT NULL
                    GROUP BY s.id
                ) AS t ON t.branch_id = b.id
                WHERE b.id IN($id_branch)
                GROUP BY b.id
               
        ";
        $data = DB::select(DB::raw($query));

        return $data;
    }

    public function getStudentAttendances(Request $request)
    {
        $where = '';
        $branches = u::getBranchIds($request->users_data);
        $id_branch = implode(',', $branches);
        $where .= " AND (r.branch_id in ($id_branch) OR '".$request->users_data->role_id."' = '999999999')";
        $date = date('Y-m-d',time()-24*3600);
        $report_month = date('Y-m',time()-24*3600);
        $q = "SELECT b.name AS branch_name, cl.cls_name AS cls_name, s.crm_id, s.name AS student_name,
            '$date' AS `date`
          FROM
            report_full_fee_active AS r 
            LEFT JOIN students AS s ON s.id=r.student_id
            LEFT JOIN branches AS b ON b.id=r.branch_id
            LEFT JOIN classes AS cl ON cl.id=r.class_id
            LEFT JOIN schedules AS sc ON sc.class_id = r.class_id
            LEFT JOIN attendances AS a ON a.student_id = r.student_id AND a.attendance_date='$date'
            WHERE
               sc.cjrn_classdate = '$date' AND r.report_month='$report_month' AND a.id IS NULL  $where
               AND (SELECT count(id) FROM public_holiday WHERe `start_date` <= '$date' AND `end_date` >='$date' AND b.zone_id=zone_id)=0
          ORDER BY r.class_id";

        $recharges = u::query($q);

        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $recharges);
    }
}
