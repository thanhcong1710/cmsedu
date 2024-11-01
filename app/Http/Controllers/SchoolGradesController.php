<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolGrade;

class SchoolGradesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $pageSize = $request->pageSize == null?5: $request->pageSize;
        $schoolgrades = SchoolGrade::paginate($pageSize);
        return response()->json($schoolgrades);
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
        $grades = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];

        $query_information = self::query($request);
        $get_zones_query = $query_information['base_query'];
        $grades =  DB::select(DB::raw($get_zones_query));
        if ($grades){
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
        $response['grades'] = $grades;
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
                $where .= "and $key[$i] like '%$value[$i]%' ";
            }
        }
        if ($where){
            $where = ltrim($where, "and");
            $where = "where ".$where;
        }

        $query = "SELECT *from school_grades $where ORDER BY status DESC, id DESC  $limitation";
        $count_query = "SELECT COUNT(id) as total FROM school_grades $where";
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
        $request->validate([
            'name' => 'required',
            'status' => 'required'
        ]);

        $schoolgrade = new SchoolGrade();
        $schoolgrade->name = $request->name;
        $schoolgrade->description = $request->description;
        $schoolgrade->status = $request->status;

        $schoolgrade->save();
        return response()->json($schoolgrade);
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
        $schoolgrade = SchoolGrade::find($id);
        if ($schoolgrade){
            return response()->json($schoolgrade);
        }
        return response()->json();
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
        $schoolgrade = SchoolGrade::find($id);
        if ($schoolgrade){
            return response()->json($schoolgrade);
        }
        return response()->json();
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
        $request->validate([
            'name' => 'required',
            'status' => 'required'
        ]);

        $schoolgrade = SchoolGrade::find($id);

        $schoolgrade->name = $request->name;
        $schoolgrade->description = $request->description;
        $schoolgrade->status = $request->status;

        $schoolgrade->save();
        return response()->json($schoolgrade);
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
        $schoolgrade = SchoolGrade::find($id);

        if ($schoolgrade->delete()){   
            return response()->json("Delete Success!");
        }
        return response()->json("Delete Error");
    }

    public function search(Request $request){
        $field = explode("," ,$request->field);
        $keyword = explode("," ,$request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("school_grades");

        $p = DB::table('school_grades');
        
        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }
}
