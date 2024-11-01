<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Response;
use Illuminate\Http\Request;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;
class SemestersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null ? 5:$request->pageSize;
        $semesters = Semester::paginate($pageSize);
        return response()->json($semesters);
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

    public function getList(Request $request){
        $response = new Response();

        $semesters = Semester::getList();
        return $response->formatResponse(APICode::SUCCESS, $semesters);
    }

    public function current(Request $request){
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $code = APICode::SUCCESS;
            $data = u::query("SELECT * FROM semesters WHERE end_date >= CURDATE() AND status > 0 ORDER BY start_date ASC");
        }
        return $response->formatResponse($code, $data);
    }

    public function obsolete(Request $request){
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $code = APICode::SUCCESS;
            $data = u::query("SELECT * FROM semesters WHERE end_date < CURDATE() AND status > 0 ORDER BY end_date DESC");
        }
        return $response->formatResponse($code, $data);
    }
    
    public function list($page, $search, $filter, Request $request){
        $semesters = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];

        $query_information = self::query($request);
        $get_zones_query = $query_information['base_query'];
        // return $get_zones_query;
        $semesters =  DB::select(DB::raw($get_zones_query));
        if ($semesters){
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
            'cpage' => $cpage,
            'lpage' => $lpage,
            'limit' => $limit,
            'total' => $total
        ];
        $response['semesters'] = $semesters;
        $response['message'] = 'successful';
        return response()->json($response);
    }

    private function query($request, $page = 1, $limit = 20){
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";

        $where = '';        
        if ($request->search != '_'){
            $key = explode(",", $request->search);
            $value = explode(",", $request->filter);
            
            for ($i= 0; $i<count($key); $i++){
                if ($key[$i] == "start_date"){
                    $start_date = $value[$i];
                    $where .="and start_date >= '$start_date' ";
                }
                else if ($key[$i] == "end_date"){
                    $end_date = $value[$i];
                    $where .= "and end_date <= '$end_date'";
                }
                else {
                    $where .= "and $key[$i] like '%$value[$i]%' ";
                }
            }
        }
        if ($where){
            $where = ltrim($where, "and");
            $where = "where ".$where;
        }

        $query = "SELECT * from semesters $where ORDER BY status DESC, id DESC $limitation";
        $count_query = "SELECT COUNT(id) as total FROM semesters $where";
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
        //
        $semester = new Semester();

        $semester->name = $request->name;
        $semester->product_id = $request->product_id;
        $semester->start_date = $request->start_date;
        $semester->end_date = $request->end_date;
        $semester->created_at = now();
        $semester->updated_at = now();
        $semester->status = $request->status;

        $semester->save();
        DB::table('semesters')->where('id',$semester->id)->update(array('sem_id'=>$semester->id));
        return response()->json($semester);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $semester = Semester::find($id);
        return response()->json($semester);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $semester = Semester::find($id);
        return response()->json($semester);
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
        //
        $semester = Semester::find($id);

        $semester->name = $request->name;
        $semester->start_date = $request->start_date;
        $semester->end_date = $request->end_date;
        $semester->product_id = $request->product_id;
        $semester->updated_at = now();
        $semester->status = $request->status;

        $semester->save();
        return response()->json($semester);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $semester = Semester::find($id);
        if ($semester->delete()) return response()->json("delete success");
    }
}
