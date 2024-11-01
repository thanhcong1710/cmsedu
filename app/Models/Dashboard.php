<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;

class Dashboard
{
    public static function getQueryDashboard01New($fromDate, $toDate, $listAccess)
    {
      $connection = DB::connection('mysql_1');
      $table = "dbo_fuc_dashboard01_new";
      $sql = "
        SELECT 
        (
          SELECT count(*) from (
            SELECT
              st.id AS s_id,
              st.branch_id as branch_id
            FROM
              students AS st
              LEFT JOIN contracts AS ct ON ct.student_id = st.id
              LEFT JOIN payment AS p ON p.contract_id = ct.id
              LEFT JOIN pendings AS pd ON pd.contract_id = ct.id
            WHERE
              st.type = 0
              AND ct.type IN ( 1, 2, 3, 4, 5 )
              AND ct.STATUS IN ( 3, 4, 5, 6 )
              AND pd.id IS NOT NULL
              AND ( p.created_at BETWEEN '$fromDate' AND '$toDate' )
              GROUP by st.id
              UNION
            SELECT
              st.id AS s_id,
              st.branch_id as branch_id
            FROM
              students AS st
              LEFT JOIN contracts AS ct ON ct.student_id = st.id
              LEFT JOIN payment AS p ON p.contract_id = ct.id
              LEFT JOIN pendings AS pd ON pd.contract_id = ct.id
            WHERE
              st.type = 0
              AND ct.type IN ( 1, 2, 3, 4, 5 )
              AND ct.STATUS IN ( 3, 4, 5, 6 )
              AND pd.id IS NULL
              AND ( p.created_at BETWEEN '$fromDate' AND '$toDate' ) 
              GROUP by st.id
              UNION
            SELECT
              st.id AS s_id,
              st.branch_id as branch_id
            FROM
              students AS st
              LEFT JOIN contracts AS ct ON ct.student_id = st.id
            WHERE
              st.type = 1
              AND ( ct.start_date BETWEEN '$fromDate' AND '$toDate' )  
              GROUP by st.id
          ) as x where x.branch_id = b.id 
        ) as hs_moi,
        (
         select count(*) from (
         SELECT
            s.id,
            s.branch_id as branch_id 
          FROM
              contracts AS c
              LEFT JOIN students AS s ON c.student_id = s.id
          WHERE c.debt_amount > 0 AND s.created_at < '$toDate'
          GROUP BY c.id
         ) as y WHERE y.branch_id = b.id 
        ) as hs_no,
        
        (
        
          SELECT count( * ) FROM
          ( SELECT DISTINCT
            st.id AS s_id,
            st.branch_id AS b_id 
            FROM students AS st
            LEFT JOIN contracts AS ct ON ct.student_id = st.id
            LEFT JOIN payment AS p ON p.contract_id = ct.id
            LEFT JOIN pendings AS pd ON pd.contract_id = ct.id 
            WHERE st.type = 0 AND ct.type in (1,2,3,4,5) AND ct.status in (3,4,5,6) and pd.id IS NOT NULL
            AND (p.created_at BETWEEN '$fromDate' AND '$toDate' )
              UNION 
            SELECT DISTINCT
            st.id AS s_id,
            st.branch_id AS b_id 
            FROM students AS st
            LEFT JOIN contracts AS ct ON ct.student_id = st.id
            LEFT JOIN payment AS p ON p.contract_id = ct.id
            LEFT OUTER JOIN pendings AS pd ON pd.contract_id = ct.id 
            WHERE st.type = 0 AND ct.type in (1,2,3,4,5) AND ct.status in (3,4,5,6) and pd.id IS NULL 
            AND (p.created_at BETWEEN '$fromDate' AND '$toDate' )
              UNION 
            SELECT DISTINCT
            st.id AS s_id,
            st.branch_id AS b_id 
            FROM students AS st 
            INNER JOIN contracts as ct on ct.student_id = st.id 
            WHERE st.type = 1 and (ct.start_date BETWEEN '$fromDate' AND '$toDate' ) 
          ) AS br WHERE br.b_id = b.id 
        
        ) as hs_tang_net,
        (
          SELECT count(*) FROM (
            SELECT
             DISTINCT e.student_id,
             s.branch_id as b_id 
            FROM
              enrolments AS e
              LEFT JOIN contracts AS c ON e.contract_id = c.id
              LEFT JOIN students AS s ON c.student_id = s.id
              LEFT JOIN tuition_transfer AS tff ON tff.from_contract_id = c.id 
            WHERE
              e.id IN (
              SELECT
                MAX( e.id ) 
              FROM
                enrolments AS e
                LEFT JOIN contracts AS c ON e.contract_id = c.id
                LEFT JOIN students AS s ON c.student_id = s.id 
              WHERE
                s.branch_id = c.branch_id 
              GROUP BY
                s.id 
              ) 
              AND e.last_date >= '$fromDate' 
              AND e.last_date <= '$toDate' 
              AND tff.id IS NULL 
          ) as z where z.b_id = b.id 
        ) as hs_het_han,
        b.id as branch_id,
        b.hrm_id as hrm_id,
        b.zone_id as zone_id,
        b.region_id as region_id
        FROM branches as b
      ";
      $data = DB::select(DB::raw($sql));
      return $data;
    }

}
