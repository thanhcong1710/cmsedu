<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Models\Rank;
class RanksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $pageSize = $request->pageSize == null? 5: $request->pageSize ;
        $ranks = Rank::paginate($pageSize);
        return response()->json($ranks);
    }

    public function getByType(Request $request, $type) 
    {
        $ranks = Rank::where('type',$type)->get();
        return response()->success($ranks);
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
        $ranks = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];

        $query_information = self::query($request);
        $get_code_query = $query_information['base_query'];
        // return $get_code_query
        $ranks =  DB::select(DB::raw($get_code_query));
        if ($ranks){
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
        $response['ranks'] = $ranks;
        $response['message'] = 'successful';
        return response()->json($response);
        
    }

    private function query($request, $page = 1, $limit = 20, $filter = NULL){
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $filter = $request->filter ? (int)$request->filter : $filter; 
        $query = "SELECT * from ranks ORDER BY status DESC, id DESC $limitation";
        $count_query = "SELECT COUNT(id) as total FROM ranks";
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
        $this->validate($request, [
            'name' => 'required|max:255',
            'status' => 'required'
        ]);

        $rank = new Rank();
        $rank->name = trim($request->name);
        $rank->description = trim($request->description);
        $rank->type = $request->type;
        $rank->status = $request->status;

        $rank->save();
        return response()->json($rank);
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
        $rank = Rank::find($id);
        if ($rank){
            return response()->json($rank);
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
        $rank = Rank::find($id);
        if ($rank){
            return $reponse()->json($rank);
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
        $this->validate($request, [
            'name' => 'required|max:255',
            'status' => 'required'
        ]);

        $rank = Rank::find($id);
        $rank->name = $request->name;
        $rank->description = $request->description;
        $rank->status = $request->status;
        $rank->type = $request->type;

        $rank->save();
        return response()->json($rank);
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
        $rank = Rank::find($id);
        if ($rank->delete()){
        	DB::table('term_student_rank')->where('rank_id', $id)->delete();
        	return response()->json("Delete Success!");
        }
        return response()->json("Delete Error");
    }


    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode(",", $request->keyword);
        $pageSize = $request->pageSize;
        // dd($field);
        $column = DB::getSchemaBuilder()->getColumnListing("ranks");

        $p = DB::table('ranks');
        
        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->Where($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }

    public function checkCreateGrade($name)
    {
        $q = "select s.* from school_grades as s where s.name = '$name'";
        $rs = DB::selectOne(DB::raw($q));
        if($rs){
            return 0;
        }else {
            return 1;
        }
    }
}
