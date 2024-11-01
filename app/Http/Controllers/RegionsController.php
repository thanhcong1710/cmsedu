<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Region;
class RegionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $pageSize = $request->pageSize == null ? 5:$request->pageSize;
        $regions = $request->pageSize == null? 5: $request->pageSize ;
        $regions = Region::paginate($pageSize);
        return response()->json($regions);
    }
    
    public function getAllRegionsList(Request $request)
    {
        
        $regions = Region::all();
        return response()->json($regions);
    }
    
    
    public function list($page, $search, $filter, Request $request){
        $regions = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];
        
        $query_information = self::query($request);
        $get_code_query = $query_information['base_query'];
        // return $get_code_query
        $regions =  DB::select(DB::raw($get_code_query));
        if ($regions){
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
        $response['regions'] = $regions;
        $response['message'] = 'successful';
        return response()->json($response);
        
    }
    
    private function query($request, $page = 1, $limit = 20, $filter = NULL){
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $filter = $request->filter ? (int)$request->filter : $filter;
        $query = "SELECT r.*, u.full_name FROM regions as r LEFT JOIN users AS u ON u.id = r.ceo_id  $limitation";
        $count_query = "SELECT COUNT(id) as total FROM regions";
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
            'name' => 'max:50',
            'hrm_id' => 'max:20'
        ]);
        $ceo_info = DB::table('users')->where('email', $request->email)->first();
        if($request->email && !$ceo_info){
            return response()->json(0);
        }else{
            $region = new Region();
            $region->name = $request->name;
            $region->hrm_id = $request->hrm_id;
            if ($ceo_info) $region->ceo_id = $ceo_info->id;
            $region->created_at = date('Y-m-d H:i:s');
            $region->status = $request->status;
            
            $region->save();
            return response()->json($region);
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
        //
        $region = Region::find($id);
        $ceo_info = DB::table('users')->where('id', $region->ceo_id)->first();
        if($ceo_info){
            $region->email = $ceo_info->email;
        }
        if ($region){
            return response()->json($region);
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
        $region = Region::find($id);
        if ($region){
            return reponse()->json($region);
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
        $ceo_info = DB::table('users')->where('email', $request->email)->first();
        if($request->email && !$ceo_info){
            return response()->json(0);
        }else{
            $this->validate($request, [
                'name' => 'max:50',
                'hrm_id' => 'max:20'
            ]);
            
            $region = Region::find($id);
            $region->name = $request->name;
            $region->hrm_id = $request->hrm_id;
            if ($ceo_info) $region->ceo_id = $ceo_info->id;
            $region->updated_at = date('Y-m-d H:i:s');
            $region->status = $request->status;
            
            $region->save();
            
            return response()->json($region);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $region = Region::find($id);
        if ($region->delete()){
            DB::table('branches')->where('region_id', $id)->delete();
            return response()->json("Delete Success!");
        }
        return response()->json("Delete Error");
    }
    
    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode(",", $request->keyword);
        $pageSize = $request->pageSize;
        // dd($field);
        $column = DB::getSchemaBuilder()->getColumnListing("regions");
        
        $p = DB::table('regions');
        
        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->Where($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }
    public function getAll(Request $request, $role_id)
    {
        // dd($role_id);
        
        $session = $request->users_data;
        $branch = $session->roles_detail;
        // $branch_id = $branch->branch_id;
        $branch_id = $branch[0]->branch_id;
        $region_id = $branch[0]->region_id;
        // dd($region_id);
        // $region_id = $branch->region_id;
        if($role_id <= 7777777 && $role_id != 100 ){
            $query = "SELECT r.*
                    from regions as r where r.id = '$region_id'";
        }else {
            $query = "SELECT r.*
                    from regions as r
                ";
        }
        
        
        $regions = DB::select(DB::raw($query));
        
        return response()->json($regions);
    }
    
    public function getAllRegions(Request $request)
    {
        // $role_id = 999999999;
        
        
        
        $session = $request->users_data;
        $role_id = $session->role_id;
        $branch = $session->roles_detail;
        // $branch_id = $branch->branch_id;
        $branch_id = $branch[0]->branch_id;
        $region_id = $branch[0]->region_id;
        // dd($region_id);
        // $region_id = $branch->region_id;
        if($role_id <= 7777777 && $role_id != 100 ){
            $query = "SELECT r.*
                    from regions as r where r.id = '$region_id'";
        }else {
            $query = "SELECT r.*
                    from regions as r
                ";
        }
        
        
        $regions = DB::select(DB::raw($query));
        
        return response()->json($regions);
    }
    
    
}
