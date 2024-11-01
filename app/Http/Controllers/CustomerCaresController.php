<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\CustomerCare;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;
use App\Services\CustomerCareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerCaresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $pageSize = $request->pageSize == null ? 5 : $request->pageSize;
        // return DB::table('customer_care')->get();
        $customer = DB::table('customer_care')->select('customer_care.*', 'students.name as student_name', 'creator.username as creator_name', 'editor.username as editor_name')->Join('students', 'students.id', 'customer_care.crm_id')->leftJoin('users as creator', 'creator.id', 'customer_care.creator_id')->leftJoin('users as editor', 'editor.id', 'customer_care.editor_id')->paginate($pageSize);
        return $customer;
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

    public function list($page, $search, $filter, Request $request)
    {
        $arr_search = explode(',', $search);
        $arr_filter = explode(',', $filter);
        foreach ($arr_search as $k => $row) {
            $request->request->add([$row => $arr_filter[$k]]);
        }
        $cares = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];
        $query_information = self::query($request);
        $get_code_query = $query_information['base_query'];
        // return $get_code_query
        $cares = DB::select(DB::raw($get_code_query));
        if ($cares) {
        }
        $cpage = $query_information['page'];
        $limit = $query_information['limit'];
        $total = $query_information['total'];
        $lpage = $total <= $limit ? 1 : (int)round(ceil($total / $limit));
        $ppage = $cpage > 0 ? $cpage - 1 : 0;
        $npage = $cpage < $lpage ? $cpage + 1 : $lpage;
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
        $response['cares'] = $cares;
        $response['message'] = 'successful';
        return response()->json($response);

    }

    private function query($request, $page = 1, $limit = 20, $filter = NULL)
    {
        $where = ' WHERE cc.status = 1 ';
        if ($request->branch_id != '') {
            $where .= " AND t.branch_id = $request->branch_id";
        }
        if ($request->student_name != '') {
            $where .= " AND (st.name LIKE '%$request->student_name%' OR st.cms_id LIKE '%$request->student_name%' )";
        }
        if ($request->hrm_id != '') {
            $where .= " AND cr.hrm_id = '$request->hrm_id'";
        }
        if ($request->student_id != '') {
            $where .= " AND st.id= $request->student_id";
        }
        $userData = $request->users_data;
        $roleId = $userData->role_id;

        if($roleId === ROLE_EC){
            $where .= " AND cc.creator_id = $userData->id";
        }else if ($roleId === ROLE_EC_LEADER){
            $hrmId = $userData->hrm_id;
            $creatorIds = u::query("select id from users where superior_id = '$hrmId'");
            $creatorIds = array_column($creatorIds, 'id');
            $creatorIds[] = $userData->id;
            $strCreatorIds = implode(",", $creatorIds);
            if(!empty($strCreatorIds)) {
                $where .= " AND cc.creator_id in($strCreatorIds)";
            }
        }

        $selected_page = $request->page ? (int)$request->page : 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $filter = $request->filter ? (int)$request->filter : $filter;
        $query = "SELECT cc.*, q.title AS quality_name, q.score AS quality_score,st.crm_id,st.name AS student_name, st.stu_id as lms_id, st.accounting_id, ctm.name as contact_name, CONCAT(cr.full_name,' - ', cr.username) as creator
            from customer_care as cc 
            JOIN users as cr on cr.id = cc.creator_id 
            JOIN contact_methods as ctm on ctm.id = cc.contact_method_id 
            JOIN students as st on st.crm_id = cc.crm_id 
            LEFT JOIN term_student_user AS t ON t.student_id = st.id
            LEFT JOIN contact_quality AS q ON q.id = cc.contact_quality_id $where
            $limitation";
        $count_query = "SELECT COUNT(cc.id) as total FROM customer_care cc 
            JOIN users as cr on cr.id = cc.creator_id 
            JOIN contact_methods as ctm on ctm.id = cc.contact_method_id 
            JOIN students as st on st.crm_id = cc.crm_id 
            LEFT JOIN term_student_user AS t ON t.student_id = st.id
            LEFT JOIN contact_quality AS q ON q.id = cc.contact_quality_id $where";
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer_care = new CustomerCare();
        $customer_care->crm_id = $request->crm_id;
        $customer_care->contact_method_id = $request->contact_method_id;
        $customer_care->note = $request->note;
        $customer_care->creator_id = $request->creator_id;
        $customer_care->status = 1;
        $customer_care->created_at = $request->created_at;
        $customer_care->stored_date = now();
        $customer_care->contact_quality_id = $request->contact_quality_id;
        $customer_care->save();
        return response()->json($customer_care);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = "SELECT cc.*, q.title AS quality_name, q.score AS quality_score,st.crm_id,st.name AS student_name, st.stu_id as lms_id, st.accounting_id, ctm.name as contact_name, CONCAT(cr.full_name,' - ', cr.username) as creator,
        st.accounting_id,st.crm_id,st.gud_mobile1 AS parent_mobile, st.gud_name1 AS parent_name, st.gud_email1 AS parent_email,t.branch_id
        from customer_care as cc 
        JOIN users as cr on cr.id = cc.creator_id 
        JOIN contact_methods as ctm on ctm.id = cc.contact_method_id 
        JOIN students as st on st.crm_id = cc.crm_id 
        LEFT JOIN term_student_user AS t ON t.student_id = st.id
        LEFT JOIN contact_quality AS q ON q.id = cc.contact_quality_id WHERE cc.id = $id";
        $data = DB::select(DB::raw($query));
        return response()->json($data[0]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $customer_care = CustomerCare::find($id);
        return response()->json($customer_care);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer_care = CustomerCare::find($id);
        $customer_care->contact_method_id = $request->contact_method_id;
        $customer_care->note = $request->note;
        $customer_care->updated_at = $request->created_at;
        $customer_care->contact_quality_id = $request->contact_quality_id;
        $customer_care->save();
        return response()->json($customer_care);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $customer_care = CustomerCare::find($id);
        if ($customer_care->delete()) return response()->json("delete success!");
    }

    public function search(Request $request)
    {
        $field = explode(",", $request->field);
        $keyword = explode(",", $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("customer_care");

        $p = DB::table('customer_care');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%' . $keyword[0] . '%');
        for ($i = 1; $i < count($field); $i++) {
            if (in_array($field[$i], $column)) {
                $p->orWhere($field[$i], 'like', '%' . $keyword[$i] . '%');
            }
        }
        return $p->paginate($pageSize);
    }

    public function suggest(Request $request, $branch_id, $keyword)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($keyword) {
            $keyword = trim($keyword);
            $keys = explode('-', $keyword);
            $key1 = '';
            $key2 = $keyword;
            if (count($keys) == 2) {
                $key1 = trim($keys[0]);
                $key2 = trim($keys[1]);
            }
            $query = '';
            if ($session = json_decode($request->authorized)) {
                $session = $request->users_data;
                $user_id = $session->id;
                $role_id = $session->role_id;
                $branches = $branch_id ? (int)$branch_id : $session->branches_ids;
                $where = "AND s.branch_id IN ($branches)";
                if ($role_id == ROLE_EC_LEADER) {
                    $where .= " AND (u2.id = $user_id OR t.ec_id = $user_id)";
                }
                if ($role_id == ROLE_EC) {
                    $where .= " AND t.ec_id = $user_id";
                }
                $key1 = $key1 ? trim($key1) : '';
                $key2 = $key2 ? trim($key2) : '';
                $where .= $key1 ? " AND ((s.id LIKE '%$key1%' OR s.crm_id LIKE '%$key1%') AND " : " AND ((s.id LIKE '%$keyword%' OR s.crm_id LIKE '%$keyword%') OR ";
                $where .= $key2 ? " s.name LIKE '$key2%' OR s.nick LIKE '$key2%')" : ')';
                $query = "SELECT
            s.id AS student_id,
            CONCAT(s.name, ' - ', s.crm_id) AS label
            FROM students AS s
            LEFT JOIN term_student_user AS t ON t.student_id = s.id
            LEFT JOIN users AS u1 ON t.ec_id = u1.id
            LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id
            WHERE s.id > 0 $where GROUP BY s.id ORDER BY `name` ASC LIMIT 0, 8";
                $data = u::query($query);
                $code = APICode::SUCCESS;
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function reportByDate(Request $request){
        $params = $request->all();
        $data = CustomerCareService::getStudentCareReport($params);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    public function byCrmId(Request $request){
        $response = new Response();
        $query =    "SELECT *,
                    (SELECT u.full_name FROM `users` u WHERE u.id = c.creator_id) AS full_name
                    FROM `student_care` c WHERE c.`student_id` = $request->id";
        return $response->formatResponse(APICode::SUCCESS, u::query($query));
    }
}
