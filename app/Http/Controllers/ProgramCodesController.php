<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProgramCode;

class ProgramCodesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $pageSize = $request->pageSize == null ? 5:$request->pageSize;
        return response()->json(ProgramCode::paginate($pageSize));
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
            'code' => 'required',
            'status'=> 'required'
        ]);
        $code = new ProgramCode();
        $code->code = $request->code;
        $code->description = $request->description;
        $code->status = $request->status;
        $code->product_id = $request->product_id;
        $code->save();

        return response()->json($code);
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
        $programCode =  DB::select(DB::raw($get_code_query));
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
        $response['program_codes'] = $programCode;
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
        if($filter && $filter != '_'){
            $where .= " AND product_id = $filter";
        }
        $where = '';
        if ($request->search != '_'){
            $key = explode(",", $request->search);
            $value = explode(",", $request->filter);

            for ($i= 0; $i<count($key); $i++){
                $where .= "and pc.$key[$i] like '%$value[$i]%' ";
            }
        }
        if ($where){
            $where = ltrim($where, "and");
            $where = "where ".$where;
        }
        $query = "SELECT pc.*, pr.name as product_name from program_codes as pc 
                  left join products as pr on pr.id = pc.product_id 
                  $where ORDER BY pc.id DESC $limitation";
        $count_query = "SELECT COUNT(id) as total FROM program_codes as pc $where";
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $zone = ProgramCode::find($id);
        if ($zone) return response()->json($zone);
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
         $zone = ProgramCode::find($id);
        if ($zone) return response()->json($zone);
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
            'code' => 'required',
            'status'=> 'required'
        ]);
        $code = ProgramCode::find($id);
        $code->code = $request->code;
        $code->description = $request->description;
        $code->status = $request->status;
        $code->product_id = $request->product_id;
        $code->save();

        return response()->json($code);
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
        $zone = ProgramCode::find($id);
        $zone->delete();
        return response()->json("successful");
    }

    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode("," , $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("program_codes");

        $p = DB::table('program_codes');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }
}
