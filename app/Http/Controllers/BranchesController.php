<?php

namespace App\Http\Controllers;

use App\Models\CyberAPI;
use App\Providers\UtilityServiceProvider as u;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\APICode;
use App\Models\Response;

class BranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $cm_id = 3;

    public function paginate(Request $request){
        $pageSize = $request->pageSize == null ? 5: $request->pageSize;
        $branches = Branch::paginate($pageSize);
        return response()->json($branches);
    }

    public function index(Request $request)
    {
        if ($session= json_decode($request->authorized)){
            $role_id = $session->role_id;
            $user_id = $session->id;

            $sql ='';
            if ($role_id == ROLE_SUPER_ADMINISTRATOR || $role_id == ROLE_ADMINISTRATOR || $role_id == ROLE_MANAGERS){
                $sql = "SELECT *from branches";
            }
            else if ($role_id == ROLE_REGION_CEO){
                $region = DB::select("SELECT id, name as region_name from regions where ceo_id = $user_id");
                $region_id = $region[0]->id;
                $sql = "SELECT *from branches where region_id = $region_id";
            }
            else{
                $sql = "SELECT *from branches where id in (SELECT branch_id from term_user_branch where user_id = 1)";
            }
            $data = DB::select(DB::raw($sql));
            return $data;
        }
        else{
            $pageSize = $request->pageSize == null? 5:$request->pageSize;
            $branches = Branch::all();
            return response()->json($branches);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function list($page, $search, $filter, Request $request){
        $branches = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];

        $query_information = self::query($request);
        $get_code_query = $query_information['base_query'];
        // return $get_code_query
        $branches =  DB::select(DB::raw($get_code_query));
        if ($branches){
        }
        $cpage = $query_information['page'];
        $limit = $query_information['limit'];
        $total = $query_information['total'];
        $lpage = $total <= $limit ? 1 : (int)round(ceil($total/$limit));
        $ppage = $cpage > 0 ?  $cpage-1 : 0;
        $npage = $cpage < $lpage ? $cpage +1 : $lpage;
        $response['done'] = true;
        $response['pagination'] = [
            'spage' => 1,
            'ppage' => $ppage,
            'npage' => $npage,
            'lpage' => $lpage,
            'cpage' => $cpage,
            'limit' => $limit,
            'total' => $total
        ];
        $response['branches'] = $branches;
        $response['message'] = 'successful';
        return response()->json($response);

    }

    private function query($request, $page = 1, $limit = 20, $filter = NULL){
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $filter = $request->filter ? (int)$request->filter : $filter;
        $where = '';
        if ($request->search != '_'){
            $key = explode(",", $request->search);
            $value = explode(",", $request->filter);

            for ($i= 0; $i<count($key); $i++){
                $where .= "and br.$key[$i] like '%$value[$i]%' ";
            }
        }
        if ($where){
            $where = ltrim($where, "and");
            $where = "where ".$where;
        }

        $query = "SELECT br.*, z.name as zone_name, r.name as region_name
                    from branches as br 
                    left join zones as z on z.id = br.zone_id
                    LEFT JOIN regions as r on r.id = br.region_id 
                    WHERE br.id >=0 $where ORDER BY br.status DESC, br.id DESC $limitation";
        $count_query = "SELECT COUNT(id) as total FROM branches as br $where";
        $total = DB::select(DB::raw($count_query));
        $total = $total[0]->total;
        return [
            'base_query' => $query,
            'total' => $total,
            'limit' => $limit,
            'page' => $page
        ];
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $brch_id = $request->lms_id;
        $accounting_id = $request->accounting_id;
        $hrm_id = $request->hrm;

        $rs = DB::selectOne(DB::raw("SELECT * FROM branches
                                WHERE `brch_id` = '$brch_id' 
                                OR `accounting_id` = '$accounting_id' 
                                OR `hrm_id` = '$hrm_id' "));

        if(!$rs){
            $branch = new Branch();

            $branch->name = $request->name;
            $branch->brch_id = $request->lms_id;
            $branch->hrm_id = $request->hrm;
            $branch->zone_id = $request->zone;
            $branch->region_id = $request->region_id;
            $branch->opened_date = $request->opened_date;
            $branch->created_at = now();
            $branch->updated_at = now();
            $branch->status = $request->status;
            $branch->branch_code = 'HN'.time().'/TR';
            $branch->hotline = $request->phone;
            $branch->email = $request->email;

            $tmp_text = u::convert_name($request->name);
            $tmp_text = str_replace(" ","",$tmp_text);
            $tmp_text = str_replace("-","",$tmp_text);
            $tmp_text= strtolower($tmp_text);
            $branch->id_lms = $tmp_text ;

            $branch->save();

            $cyberAPI = new CyberAPI();
            $res = $cyberAPI->createBranch($branch, 0);

            if($res){
              $branch->accounting_id = $res;
              $branch->save();
            }
            $lmsAPI = new LMSAPIController();
            $lmsAPI->createBranchLMS($branch->id);
            return response()->json($branch);
        }else {
            return 0;
            // $branch = new Branch();

            // $branch->name = $request->name;
            // $branch->brch_id = $request->lms_id;
            // $branch->accounting_id = $request->accounting_id;
            // $branch->hrm_id = $request->hrm;
            // $branch->zone_id = $request->zone;
            // $branch->region_id = $request->region_id;
            // $branch->opened_date = $request->opened_date;
            // $branch->created_at = now();
            // $branch->updated_at = now();
            // $branch->status = $request->status;
            // $branch->branch_code = 'HN'.time().'/TR';

            // $branch->save();

            // return response()->json($branch);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $q = "SELECT b.*, r.name as region_name 
                    FROM branches AS b 
                    LEFT JOIN regions AS r ON r.id = b.region_id 
                    WHERE b.id = $id";
      $branch = DB::selectOne(DB::raw($q));
      return response()->json($branch);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($requestz->all());
        $branch = Branch::find($id);

        if ($request->zone_id == null){
            $request->zone_id = 1;
        }

        $branch->name = $request->name;
        $branch->brch_id = $request->lms_id;
//        $branch->accounting_id = $request->accounting_id;
        $branch->hrm_id = $request->hrm_id;
        $branch->zone_id = $request->zone_id;
        $branch->region_id = $request->region_id;
        $branch->opened_date = $request->opened_date;
        $branch->created_at = now();
        $branch->updated_at = now();
        $branch->status = $request->status;
        $branch->hotline = $request->phone;
        $branch->email = $request->email;
        $branch->branch_code = '';

        $branch->save();

        $cyberAPI = new CyberAPI();
        $cyberAPI->updateBranch($branch, 0);
        $lmsAPI = new LMSAPIController();
        $lmsAPI->updateBranchLMS($branch->id);
        return response()->json($branch);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id);
        // $value = DB::connection('sqlsrv')->select(DB::raw("SELECT * from EFF_APAX.dbo.fuc_Dashboard01('2018-03-19','2018-03-20')"));
        // $branch = Branch::find($id);
        // $branch->delete();
        // $data = u::query("UPDATE branches SET status = 0 WHERE id = $id");
        // DB::table('branches')
        //     ->where('id', $id)
        //     ->update(['status' => -1]);

        // return response()->json("delete success!");
        $branch = Branch::find($id);
        if ($branch->delete()){
            // DB::table('sessions')->where('room_id', $id)->delete();
            return response()->json("Delete Success!");
        }
        return response()->json("Delete Error");
    }

    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode(",", $request->keyword);
        $pageSize = $request->pageSize;
        // dd($field);
        $column = DB::getSchemaBuilder()->getColumnListing("branches");

        $p = DB::table('branches');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->Where($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }
    public function getEcByBranch($id)
    {
        $query = "SELECT
                    t.user_id AS user_id,
                    u.username AS username,
                    r.name AS role,
                    r.id as role_id
                FROM
                    `term_user_branch` AS t
                    LEFT JOIN users AS u ON t.user_id = u.id
                    LEFT JOIN roles AS r ON t.role_id = r.id
                WHERE
                    t.branch_id = $id
                    AND t.role_id = ".ROLE_EC;
        $rs = DB::select(DB::raw($query));

        return response()->json($rs);
    }

    public function getCmByBranch(Request $request,$id)
    {
        $cond = " 1 ";
        if($request->status!=NULL){
            $cond.=" AND u.status=".$request->status;
        }
        $query = "SELECT
                    t.user_id AS user_id,
                    u.username AS username,
                    u.full_name,
                    r.name AS role,
                    r.id as role_id
                FROM
                    `term_user_branch` AS t
                    INNER JOIN users AS u ON t.user_id = u.id
                    LEFT JOIN roles AS r ON t.role_id = r.id
                WHERE
                    t.branch_id = $id AND u.status=1
                    AND (t.role_id = ".ROLE_CM." OR t.role_id = ".ROLE_OM." OR t.role_id = 57) AND ".$cond;
        $rs = DB::select(DB::raw($query));

        return response()->json($rs);
    }

      public function getStudentRenew(Request $request, $month = null, $year = null){
        $session = $request->users_data;
        $where = '';
        $and = '';
        $role_id = $session->role_id;
        $user_id = $session->id;
        $where = "";
        if (in_array($role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {
            $where.= " AND b.id > 0";
        } else {
            $branches = $session->branches_ids;
            $where.= " AND b.id IN ($branches)";
        }
        $query = "SELECT
                        b.id AS branch_id,
                        b.`name` AS branch_name,
                        COALESCE(z.total, 0) AS resign_total,
                        COALESCE(k.total, 0) AS recharged_total
                    FROM
                        branches AS b 
                    LEFT JOIN 
                        (SELECT
                            branch_id,
                            COUNT( * ) AS total
                        FROM
                            (
                        SELECT
                            c.branch_id,
                            COUNT( c.id ) 
                        FROM
                            contracts AS c
                            LEFT JOIN contracts AS c ON c.id = c.id
                        WHERE
                            c.type > 0 
                            AND MONTH ( c.enrolment_last_date ) = $month 
                            AND YEAR ( c.enrolment_last_date ) = $year 
                            AND c.debt_amount = 0 
                            AND c.status > 2 
                        GROUP BY
                            c.student_id 
                            ) AS x 
                        GROUP BY x.branch_id) AS z 
                    ON z.branch_id = b.id
                    LEFT JOIN
                        (SELECT
                            branch_id,
                            COUNT( * ) AS total
                        FROM
                            (
                        SELECT
                            c.branch_id,
                            COUNT( c.id ) 
                        FROM
                            contracts AS c
                        WHERE
                            c.type > 0 
                            AND MONTH ( c.start_date ) = $month 
                            AND YEAR ( c.start_date ) = $year 
                            AND c.count_recharge > 0
                            AND c.status > 2 
                        GROUP BY
                            c.student_id 
                            ) AS x 
                            GROUP BY x.branch_id) AS k
                    ON k.branch_id = b.id
                WHERE
                    b.status = 1 $where";
        $data = u::query($query);
        return $data;
    }

    public function getStudentRenewDetail(Request $request, $branch_id, $month, $year){
        $sql = "SELECT
        ct.id AS contract_id,
        st.id AS student_id,
        br.id AS branch_id,
        st.name AS student_name,
        st.nick,
        st.accounting_id,
        st.stu_id,
        pd.name AS product_name,
        pr.name AS program_name,
        cl.cls_name AS class_name,
        st.type AS student_type,
        c.enrolment_end_date AS end_date,
        t.name AS tuition_fee_name,
        t.price AS tuition_fee_price,
        'Thành công' AS success,
        CONCAT( ec.full_name, ' - ', ec.username ) AS ec_name,
        CONCAT( cm.full_name, ' - ', cm.username ) AS cm_name 
    FROM
        contracts AS ct
        JOIN students AS st ON st.id = ct.student_id
        JOIN branches AS br ON br.id = ct.branch_id
        LEFT JOIN products AS pd ON pd.id = ct.product_id
        LEFT JOIN programs AS pr ON pr.id = ct.program_id
        LEFT JOIN users AS cm ON cm.id = ct.cm_id
        LEFT JOIN users AS ec ON ec.id = ct.ec_id
        LEFT JOIN tuition_fee AS t ON t.id = ct.tuition_fee_id
        LEFT JOIN contracts AS c ON c.contract_id = ct.id
        LEFT JOIN classes AS cl ON cl.id = c.class_id 
    WHERE
        ct.id IN (
            SELECT
                MAX( c.id ) AS id 
            FROM
                contracts AS c
            WHERE
                c.type > 0 
                AND MONTH ( c.start_date ) = $month
                AND YEAR ( c.start_date ) = $year
                AND c.count_recharge > 0
                AND c.STATUS > 2 
                AND c.branch_id = $branch_id
            GROUP BY
                c.student_id 
            )";
        $data = u::query($sql);
        return $data;
    }

	public function getUserCare(Request $request, $id){
        $session = json_decode($request->authorized);
        $role_id = $session->role_id;
        $user_id = $session->id;
        // return response()->json($session);
        $where = '';
        if ($role_id == ROLE_SUPER_ADMINISTRATOR || $role_id == ROLE_ADMINISTRATOR || $role_id == ROLE_MANAGERS){

        }else if ($role_id == ROLE_REGION_CEO){
            $region_id = DB::select(DB::raw("SELECT id from regions where ceo_id = $session->id"));
            $branch_id = DB::select(DB::raw("SELECT id from branches where region_id in (SELECT id from regions where ceo_id = $session->id)"));
            $br_id = '';
            foreach ($branch_id as $item) {
                # code...
                $br_id .= $item->id.",";
            }
            $br_id = '('.rtrim($br_id, ",").')';
            $where .= "where term.branch_id in $br_id";

        }else{
        }

        if ($where){
            $where .= " AND term.branch_id = $id";
        }
        else {
            $where = "where term.branch_id = $id";
        }

        $sql = "SELECT us.id,us.username from users as us JOIN term_user_branch as term on term.user_id = us.id $where";
        $data = DB::select(DB::raw($sql));
        return $data;
    }

    public function getAllBranches($ids)
    {
        $query = "SELECT b.* from branches as b WHERE b.zone_id in ($ids)";
        $rs = DB::select(DB::raw($query));
        return response()->json($rs);
    }
    public function getBranches(Request $request)
    {
        $users_data = $request->users_data;
        // dd($users_data);
        if ($users_data){
            // dd($users_data);
            $role_id = $users_data->role_id;
            // dd($role_id);
            $user_id = $users_data->id;
            $users_data = $request->users_data;
            $branch_ids = u::getBranchIds($users_data);
            $role_branch = null;
            if(count($branch_ids) == 1){
                $role_branch = 1;
            }else {
                $role_branch = 2;
            };
            $branch_id = $branch_ids[0];
            // $branch_id = 5;
            // dd($branch_id);
            $sql ='';
            if ($role_id == User::ROLE_SUPER_ADMINISTRATOR || $role_id == User::ROLE_ADMINISTRATOR || $role_id == User::ROLE_MANAGERS){
                $sql = "SELECT branches.*, zones.name as zone_name, regions.name as region_name from branches left join zones on zones.id = branches.zone_id left join regions on regions.id = branches.region_id";
            }
            else if ($role_id == User::ROLE_REGION_CEO){
                $region = DB::select("SELECT id from regions where ceo_id = $user_id");
                if( $region ){
                  $region_id = $region[0]->id;
                }else {
                  $region_id = 0;
                }
                //$sql = "SELECT branches.*, zones.name as zone_name, regions.name as region_name from branches left join zones on zones.id = branches.zone_id left join regions on regions.id = branches.region_id where region_id = $region_id";
                $sql = "SELECT branches.*, zones.name as zone_name, regions.name as region_name from branches left join zones on zones.id = branches.zone_id left join regions on regions.id = branches.region_id where branches.id in (SELECT branch_id from term_user_branch where user_id = $user_id)";
            }else if ($role_id == User::ROLE_BRANCH_CEO || $role_id == User::ROLE_CM || $role_id == User::ROLE_OM || $role_id == User::ROLE_EC || $role_id == User::ROLE_EC_LEADER || $role_id == User::ROLE_CS_CASHIER || $role_id == User::ROLE_CS_CASHIER_LEADER ){
                //$sql = "SELECT branches.*, zones.name as zone_name, regions.name as region_name from branches left join zones on zones.id = branches.zone_id left join regions on regions.id = branches.region_id where branches.id = $branch_id";
                $sql = "SELECT branches.*, zones.name as zone_name, regions.name as region_name from branches left join zones on zones.id = branches.zone_id left join regions on regions.id = branches.region_id where branches.id in (SELECT branch_id from term_user_branch where user_id = $user_id)";
            }
            else{
                $sql = "SELECT branches.*, zones.name as zone_name, regions.name as region_name from branches left join zones on zones.id = branches.zone_id left join regions on regions.id = branches.region_id where branches.id in (SELECT branch_id from term_user_branch where user_id = $user_id)";
            }
            $data = DB::select(DB::raw($sql));
            // $data['role_branch'] = $role_branch;
            // dd($data);
            return response()->json($data);
        }
        else{
            // $pageSize = $request->pageSize == null? 5:$request->pageSize;
            $branches = DB::select(DB::raw("SELECT branches.*, zones.name as zone_name, regions.name as region_name from branches left join zones on zones.id = branches.zone_id left join regions on regions.id = branches.region_id"));
            return response()->json($branches);
        }
    }
    public function checkRole(Request $request)
    {
        $users_data = $request->users_data;
        // dd($users_data);
        if ($users_data){
            // dd($users_data);
            $role_id = $users_data->role_id;
            // dd($role_id);
            $user_id = $users_data->id;
            $users_data = $request->users_data;
            $branch_ids = u::getBranchIds($users_data);
            $role_branch = null;
            if(count($branch_ids) == 1){
                $role_branch = 0;
            }else {
                $role_branch = 1;
            };

            $data = $role_branch;
            // dd($data);
            return response()->json($data);
        }
    }
    public function getAllCmByBranch(Request $request, $id) {
        $data = null;
		$code = APICode::PERMISSION_DENIED;
		$response = new Response();
		if ($session = $request->users_data) {
            if($request->status==1){
                $cond = "u.status=1";
            }else{
                $cond = "1";
            }
            $sql = "SELECT u.*
                FROM term_user_branch AS t LEFT JOIN users AS u ON u.id=t.user_id 
                WHERE t.branch_id=$id AND (t.role_id=55 OR t.role_id=56) AND $cond";
            $data = u::query($sql);
            $code = APICode::SUCCESS;
        }
        return $response->formatResponse($code, $data);
    }
    public function getAllEcByBranch(Request $request, $id) {
        $data = null;
		$code = APICode::PERMISSION_DENIED;
		$response = new Response();
		if ($session = $request->users_data) {
            if($request->status==1){
                $cond = "u.status=1";
            }else{
                $cond = "1";
            }
            $sql = "SELECT u.*
                FROM term_user_branch AS t LEFT JOIN users AS u ON u.id=t.user_id 
                WHERE t.branch_id=$id AND (t.role_id=68 OR t.role_id=69) AND $cond";
            $data = u::query($sql);
            $code = APICode::SUCCESS;
        }
        return $response->formatResponse($code, $data);
    }
}
