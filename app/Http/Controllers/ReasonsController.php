<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reason;
use Illuminate\Support\Facades\DB;

class ReasonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null? 20:$request->pageSize;
        $reasons = Reason::paginate($pageSize);
        return response()->json($reasons);
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
            'description' => 'max:255'
        ]);

        $reason = new Reason();
        $reason->description = $request->description;
        $reason->type = $request->type;
        $reason->status = $request->status;
        $reason->save();
        return response()->json($reason);
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
        $reason = Reason::find($id);
        return response()->json($reason);
    }

    public function list($page, $search, $filter, Request $request){
        $zones = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];

        $query_information = self::query($request);
        $get_code_query = $query_information['base_query'];
        // return $get_code_query
        $reasons =  DB::select(DB::raw($get_code_query));
        if ($zones){
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
        $response['reasons'] = $reasons;
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
                $where .= "and $key[$i] like '%$value[$i]%' ";
            }
        }
        if ($where){
            $where = ltrim($where, "and");
            $where = "where ".$where;
        }

        $query = "SELECT *from reasons $where ORDER BY status DESC, id DESC $limitation";
        $count_query = "SELECT COUNT(id) as total FROM reasons $where";
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $reason = Reason::find($id);
        return response()->json($reason);
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
            'description' => 'max:255'
        ]);

        $reason = Reason::find($id);
        $reason->description = $request->description;
        $reason->type = $request->type;
        $reason->status = $request->status;
        $reason->save();

        return response()->json($reason);
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
        $reason = Reason::find($id);
        if ($reason->delete()) return response()->json("success!");
    }
}
