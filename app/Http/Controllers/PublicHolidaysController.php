<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PublicHoliday;
use App\Models\APICode;
use App\Models\Response;
// use App\Http\Requests\publicHolidayRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Providers\UtilityServiceProvider as u;
class PublicHolidaysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $pageSize = $request->pageSize == null? 5: $request->pageSize;
        $publicHolidays = PublicHoliday::paginate($pageSize);
        foreach ($publicHolidays as $holi) {
            # code...
            if ($holi->zone_id){
                $zone = DB::table('zones')->where('zones.id', $holi->zone_id)->get();
                $holi->zone = $zone;
            }else{
                $holi->zone = null;
            }
            if ($holi->branch_id){
                $branch = DB::table('branches')->where('branches.id', $holi->branch_id)->get();
                $holi->branch = $branch;
            }else{
                $holi->branch = "";
            }
            if ($holi->editor_id){
                $editor = DB::table('users')->where('users.id', $holi->editor_id)->get();
                $holi->editor = $editor;
            }else{
                $holi->editor = null;
            }
        }
        return response()->json($publicHolidays);
    }
    
    public function getStart(Request $request) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            $data->zones = u::query("SELECT z.id, z.name FROM zones z LEFT JOIN branches b ON b.zone_id = z.id WHERE b.id IN ($session->branches_ids) AND z.status > 0 GROUP BY z.id");
            $data->branches = u::query("SELECT id, name FROM branches WHERE id IN ($session->branches_ids) AND `status` > 0");
            $data->products = u::query("SELECT id, name FROM products WHERE `status` > 0");
            $data->holidays = u::query("SELECT p.*, z.name AS zone_name FROM public_holiday AS p LEFT JOIN zones AS z ON p.zone_id = z.id WHERE p.status > 0 AND p.zone_id IN (SELECT z.id FROM zones z LEFT JOIN branches b ON b.zone_id = z.id WHERE b.id IN ($session->branches_ids) AND z.status > 0) ORDER BY p.start_date ASC LIMIT 0, 20");
            $total = u::first("SELECT COUNT(id) summary FROM public_holiday WHERE status > 0 AND zone_id IN (SELECT z.id FROM zones z LEFT JOIN branches b ON b.zone_id = z.id WHERE b.id IN ($session->branches_ids) AND z.status > 0)");
            $total = (int)$total->summary;
            
            foreach ($data->holidays AS $h => $holi){
                $holi->products = str_replace(array('[',']'), '', $holi->products);
                $holi->products = explode(',', $holi->products);
                $tmp_product ='';
                foreach ($holi->products AS $k=> $row){
                    $product_info = DB::table('products')->where('id', $row)->first();
                    if($k==0)
                        $tmp_product.=$product_info->name;
                        else
                            $tmp_product.=', '.$product_info->name;
                }
                $data->holidays[$h]->products = $tmp_product;
            }
            $pagination = (Object)[];
            $pagination->total = 0;
            $pagination->lpage = 2;
            $pagination->npage = 2;
            $pagination->ppage = 1;
            $pagination->limit = 20;
            $pagination->cpage = 1;
            $data->pagination = ada()->paging($pagination, $total);
        }
        return $response->formatResponse($code, $data);
    }
    
    public function filter(Request $request, $search) {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = [];
            $code = APICode::SUCCESS;
        }
        return $response->formatResponse($code, $data);
    }
    
    public function list($page, $search, $filter, Request $request){
        $arr_search = explode(',', $search);
        $arr_filter = explode(',', $filter);
        foreach ($arr_search AS $k=>$row){
            $request->request->add([$row => $arr_filter[$k]]);
        }
        $holidays = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];
        
        $query_information = self::query($request);
        $get_code_query = $query_information['base_query'];
        // return $get_code_query
        $holidays =  DB::select(DB::raw($get_code_query));
        if ($holidays){
            foreach ($holidays AS $h => $holi){
                $holi->products = str_replace(array('[',']'), '', $holi->products);
                $holi->products = explode(',', $holi->products);
                $tmp_product ='';
                foreach ($holi->products AS $k=> $row){
                    $product_info = DB::table('products')->where('id', $row)->first();
                    if($k==0)
                        $tmp_product.=$product_info->name;
                        else
                            $tmp_product.=', '.$product_info->name;
                }
                $holidays[$h]->products = $tmp_product;
            }
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
        $response['holidays'] = $holidays;
        $response['message'] = 'successful';
        return response()->json($response);
        
    }
    
    private function query(Request $request, $page = 1, $limit = 20, $filter = NULL){
        // dd(1);
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $filter = $request->filter ? (int)$request->filter : $filter;
        
        $where = ' where 1 ';
        
        if ($request->keyword !=''){
            $where.=" AND ph.name LIKE '%$request->keyword%'";
        }
        if($request->status!= NULL){
            $where.=" AND ph.status = $request->status";
        }
        if($request->zone_id){
            $where.=" AND ph.zone_id= $request->zone_id";
        }
        if($request->product_id){
            $where.=" AND ph.products LIKE '%$request->product_id%'";
        }
        if($request->start_date){
            $where.=" AND ph.start_date ='$request->start_date'";
        }
        if($request->end_date){
            $where.=" AND ph.end_date ='$request->end_date'";
        }
        $query = "SELECT ph.*, z.name as zone_name from public_holiday as ph LEFT join zones as z on ph.zone_id = z.id $where ORDER BY ph.status DESC, ph.id DESC $limitation";
        $count_query = "SELECT COUNT(id) as total FROM public_holiday AS ph $where";
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
        $tmp_products='[';
        $k=0;
        foreach ($request->products AS $product){
            if($k==0)
                $tmp_products.=$product;
                else
                    $tmp_products.=','.$product;
                    $k++;
        }
        $tmp_products.=']';
        $holiday = new PublicHoliday();
        $holiday->name = trim($request->name);
        $holiday->start_date = $request->start_date;
        $holiday->end_date = $request->end_date;
        $holiday->zone_id = $request->zone_id;
        $holiday->branch_id = $request->branch_id;
        $holiday->created_at = date('Y-m-d H:i:s');
        $holiday->status = $request->status;
        $holiday->creator_id = 1;
        $holiday->products = $tmp_products;
        
        // creator id
        // $holiday->updated_at = $request->updated_date;
        // $holiday->editor_id = $request->editor_id;
        
        $holiday->save();
        return response()->json($holiday);
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
        $holi = PublicHoliday::find($id);
        if ($holi->zone_id){
            $zone = DB::table('zones')->where('zones.id', $holi->zone_id)->get();
            $holi->zone = $zone;
        }else{
            $holi->zone = null;
        }
        if ($holi->branch_id){
            $branch = DB::table('branches')->where('branches.id', $holi->branch_id)->get();
            $holi->branch = $branch;
        }else{
            $holi->branch = "";
        }
        if ($holi->editor_id){
            $editor = DB::table('users')->where('users.id', $holi->editor_id)->get();
            $holi->editor = $editor;
        }else{
            $holi->editor = null;
        }
        $holi->products = str_replace(array('[',']'), '', $holi->products);
        $holi->products = explode(',', $holi->products);
        return response()->json($holi);
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
        $holiday = PublicHoliday::find($id);
        $holiday->products = str_replace(array('[',']'), '', $holiday->products);
        $holiday->products = explode(',', $holiday->products);
        if ($holiday){
            return response()->json($holiday);
        }
        return response()->json("không tìm thấy kết quả");
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
        $tmp_products='[';
        $k=0;
        foreach ($request->products AS $product){
            if($k==0)
                $tmp_products.=$product;
                else
                    $tmp_products.=','.$product;
                    $k++;
        }
        $tmp_products.=']';
        //
        $holiday = PublicHoliday::find($id);
        
        $holiday->name = trim($request->name);
        $holiday->start_date = $request->start_date;
        $holiday->end_date = $request->end_date;
        $holiday->zone_id = $request->zone_id;
        $holiday->branch_id = $request->branch_id;
        // $holiday->created_at = $request->created_at;
        // $holiday->creator_id = $request->creator_id;
        $holiday->updated_at = date('Y-m-d H:i:s');
        $holiday->editor_id = Auth::id();
        $holiday->status = $request->status;
        $holiday->products = $tmp_products;
        // dd(Auth::id());
        $holiday->save();
        return response()->json($holiday);
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
        $holiday = PublicHoliday::find($id);
        if ($holiday == null){
            return response()->json("Delete Error");
        }
        $holiday->delete();
        return response()->json("Delete Success!");
    }
    
    public function remove(Request $request, $holyday_id) {
        u::query("UPDATE public_holiday SET `status` = 0 WHERE id = $holyday_id");
        
    }
    
    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode(",", $request->keyword);
        $pageSize = $request->pageSize;
        
        $column = DB::getSchemaBuilder()->getColumnListing("public_holiday");
        
        $p = DB::table('public_holiday');
        
        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->Where($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }
}
