<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Zone;

class ZonesController extends Controller
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
        return response()->json(Zone::all());
    }

    public function getAllZonesList()
    {
        $zones = Zone::all();
        return response()->json($zones);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list($page, $search, $filter, Request $request){
        $zones = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];

        $query_information = self::query($request);
        $get_zones_query = $query_information['base_query'];
        $zones =  DB::select(DB::raw($get_zones_query));
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
            'cpage' => $cpage,
            'lpage' => $lpage,
            'limit' => $limit,
            'total' => $total
        ];
        $response['zones'] = $zones;
        $response['message'] = 'successful';
        return response()->json($response);
        
    }

    private function query($request, $page = 1, $limit = 20){
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $query = "SELECT *from zones where status >= 0 $limitation";
        $count_query = "SELECT COUNT(id) as total FROM zones";
        $total = DB::select(DB::raw($count_query));
        $total = $total[0]->total;
        return [
            'base_query' => $query,
            'total' => $total,
            'limit' => $limit,
            'page' => $page
        ];
    }

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

        $zone = new Zone();
        $zone->name = $request->name;
        $zone->status = $request->status;
        $zone->save();

        return response()->json($zone);
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
        $zone = Zone::find($id);
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
         $zone = Zone::find($id);
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
        $zone = Zone::find($id);
        $zone->name = $request->name;
        $zone->status = $request->status;
        $zone->save();

        return response()->json($zone);
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
        $zone = Zone::find($id);

        if ($zone->delete()){
            DB::table('tuition_fee')->where('zone_id', $id)->delete();
            DB::table('branches')->where('zone_id', $id)->delete();
        }
    }

    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode("," , $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("zones");

        $p = DB::table('zones');
        
        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }

    public function getBranch($id){
        $branches = DB::table('branches')->where('zone_id', $id)->get();
        return response()->json($branches);
    }
    public function getBranchesByZone($zone_id)
    {
        $query = "SELECT b.* from branches AS b WHERE b.zone_id = '$zone_id'";
        $branches = DB::select(DB::raw($query));

        return response()->json($branches);
    }

    public function checkExist($zone_name)
    {
        $rs = DB::selectOne(DB::raw("SELECT count(*) AS exist from zones where zones.name = '$zone_name' "));
        $exist = $rs->exist;

        return response()->json($exist);
    }

    public function removeZoneIdOfBranch($id){
        $branch = DB::table('branches')
            ->where('id', $id)
            ->update(['zone_id' => 0]);

        return response()->jsono($branch);
    }

    public function getAllBranches($zone_id)
    {
        $query = "SELECT b.* from branches as b WHERE b.zone_id != $zone_id";
        $rs = DB::select(DB::raw($query));
        return response()->json($rs);
    }
}
