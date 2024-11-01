<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Shift;
use App\Http\Requests\StoreShift;
use App\Providers\UtilityServiceProvider as u;

class ShiftsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $pageSize = $request->pageSize == null ? 5 : $request->pageSize;
      $shifts = Shift::paginate($pageSize);
      return response()->json($shifts);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $shift = new Shift();

      $shift->name = $request->name;
      $shift->start_time = $request->start_time;
      $shift->end_time = $request->end_time;
      $shift->status = $request->status;
      $shift->zone_id = $request->zone_id;
      $shift->save();

        return response()->json($shift);
    }

    public function list($page, $search, $filter, Request $request){
        $arr_search = explode(',', $search);
        $arr_filter = explode(',', $filter);
        foreach ($arr_search AS $k=>$row){
            $request->request->add([$row => $arr_filter[$k]]);
        }
        
        $zones = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];
        $query_information = self::query($request);
        //$shifts = u::query("SELECT * FROM shifts ORDER BY id DESC");
        $get_code_query = $query_information['base_query'];
        $shifts =  DB::select(DB::raw($get_code_query));
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
        $response['shifts'] = $shifts;
        $response['message'] = 'successful';
        return response()->json($response);
        
    }

    private function query(Request $request, $page = 1, $limit = 20){
        $arr_zone = array();
        foreach ($request->users_data->roles_detail AS $role){
            if(!in_array($role->zone_id, $arr_zone)){
                array_push($arr_zone, $role->zone_id);
            }
        }
        $tmp_zone = implode(",",$arr_zone);
        
        $where= " 1 ";
        $name = $request->name;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $status = $request->status;
        
        if($name!=''){
            $where.=" AND shifts.name LIKE '%$name%'";
        }
        if($start_time!=''){
            $where.=" AND shifts.start_time = '$start_time'";
        }
        if($end_time!=''){
            $where.=" AND shifts.end_time = '$end_time'";
        }
        if($status!=''){
            $where.=" AND shifts.status = $status";
        }
        if($arr_zone){
            $where.=" AND zones.id IN (".$tmp_zone.")";
        }
        
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $query = "SELECT shifts.*, zones.name as zone_name
                    from shifts join zones on zones.id = shifts.zone_id
                    WHERE $where ORDER BY shifts.status DESC, shifts.id DESC $limitation";
        $count_query = "SELECT COUNT(shifts.id) as total FROM shifts join zones on zones.id = shifts.zone_id WHERE $where";
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
        $shift = Shift::find($id);
        return response()->json($shift);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shift = Shift::find($id);
        return response()->json($shift);
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
        $shift = Shift::find($id);

        $shift->name = $request->name;
        $shift->start_time = $request->start_time;
        $shift->end_time = $request->end_time;
        $shift->status = $request->status;
        $shift->zone_id = $request->zone_id;
        $shift->save();
        return response()->json($shift);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shift = Shift::find($id);
        $shift->delete();
        return response()->json('Successfully Deleted');
    }

     public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode("," , $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("shifts");

        $p = DB::table('shifts');
        
        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }
    public function getShiftsByBranch(Request $request,$branch_id)
    {
        $branch_info = DB::table('branches')->where('id',$branch_id)->first();
        if($request->status!=NULL){
            $listShifts = Shift::where('zone_id',$branch_info->zone_id)->where('status',$request->status)->get();
        }else{
            $listShifts = Shift::where('zone_id',$branch_info->zone_id)->get();
        }
        return response()->json($listShifts);
    }
}
