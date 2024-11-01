<?php

namespace App\Http\Controllers;

use App\Models\CyberAPI;
use Illuminate\Http\Request;
use App\Models\TuitionFee;
use App\Models\APICode;
use App\Models\Response;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;

class TuitionFeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null? 5:$request->pageSize;
        $tuitionFees = TuitionFee::paginate($pageSize);
        $tuitionFees = DB::table('tuition_fee')->paginate($pageSize);
        foreach ($tuitionFees as $term) {

            # code...
            if ($term->product_id){
                $product = DB::table('products')->where('products.id',$term->product_id)->get();
                $term->product = $product;
            }else{
                $term->product = null;
            }
            if ($term->zone_id){
                $zone = DB::table('zones')->where('zones.id',$term->zone_id)->get();
                $term->zone = $zone;
            }else{
                $term->zone = null;
            }
        }
        return response()->json($tuitionFees);
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
        $tuition_fee = new TuitionFee();
        $tuition_fee->name = trim($request->name);
        $tuition_fee->product_id = $request->product_id;
        $tuition_fee->zone_id = $request->zone_id;
        $tuition_fee->session = $request->{"session"};
        $tuition_fee->number_of_months = $request->number_of_months;
        $tuition_fee->price = trim($request->price);
        $tuition_fee->discount = $request->discount;
        $tuition_fee->receivable = $request->receivable;
        $tuition_fee->created_at = $request->created_at;
        $tuition_fee->creator_id = $request->creator_id;
        $tuition_fee->updated_at = $request->updated_at;
        $tuition_fee->editor_id = $request->editor_id;
        $tuition_fee->available_date = $request->available_date;
        $tuition_fee->expired_date = $request->expired_date;
        $tuition_fee->hash_key = $request->hash_key;
        $tuition_fee->changed_fields = $request->changed_fields;
        $tuition_fee->status = (int)$request->status;
        $tuition_fee->branch_id = implode(',' , $request->branch_id);
        $tuition_fee->type = $request->type == null? 0 : $request->type;
        $tuition_fee->accounting_id = $request->accounting_id;
        $tuition_fee->save();
        $tuition_fee_info = u::first("SELECT id FROM tuition_fee WHERE `name` = '".trim($request->name)."' AND `price` = '".trim($request->price)."' AND `branch_id` IN (".implode(',' , $request->branch_id).") AND `available_date` = DATE('".$request->available_date."') AND `expired_date` = DATE('".$request->expired_date."') ORDER BY id DESC LIMIT 0, 1");
        if ($tuition_fee_info) {
            self::upsertTuitionFeesRelation($tuition_fee_info->id, $request->black_hole, $request->bright, $request->ucrea);
        }

//        $cyberAPI = new CyberAPI();
//        $res = $cyberAPI->createTuitionFee($tuition_fee, $request->creator_id);
//        if($res){
//          $tuition_fee->accounting_id = $res;
//          $tuition_fee->save();
//          $cyberAPI->createTuitionFeeExpire($tuition_fee, $request->creator_id);
//        }

        return response()->json($tuition_fee);
    }

    public function upsertTuitionFeesRelation($tuition_id, $cdis, $aprils, $igartens) {
        $relation_list = [];
        if (count($cdis)) {
            foreach ($cdis as $cdi) {
                $relation_list[] = $cdi['id'];
            }
        }
        if (count($aprils)) {
            foreach ($aprils as $april) {
                $relation_list[] = $april['id'];
            }
        }
        if (count($igartens)) {
            foreach ($igartens as $igarten) {
                $relation_list[] = $igarten['id'];
            }
        }
        $existed = u::query("SELECT exchange_tuition_fee_id FROM tuition_fee_relation WHERE tuition_fee_id = $tuition_id");
        $existed_list = [];
        if (count($existed)) {
            foreach ($existed as $exit) {
                $existed_list[] = $exit->exchange_tuition_fee_id;
            }
        }
        $disabled_list = array_diff($existed_list, $relation_list);
        $add_new_list = array_diff($relation_list, $existed_list);
        if (count($disabled_list)) {
            u::query("UPDATE tuition_fee_relation SET status = 0 WHERE tuition_fee_id = $tuition_id AND exchange_tuition_fee_id IN (".implode(',', $disabled_list).")");
        }
        if (count($add_new_list)) {
            $add_new_query = "INSERT INTO tuition_fee_relation (`tuition_fee_id`, `exchange_tuition_fee_id`, `status`) VALUES";
            $i = 0;
            foreach ($add_new_list as $add_new_item) {
                $i++;
                $add_new_query.= "($tuition_id, $add_new_item, 1)";
                $add_new_query.= $i < (int)count($add_new_list) ? "," : "";
            }
            u::query($add_new_query);
        }
        if (count($relation_list)) {
            u::query("UPDATE tuition_fee_relation SET status = 1 WHERE tuition_fee_id = $tuition_id AND exchange_tuition_fee_id IN (".implode(',', $relation_list).")");
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
        // $student = DB::select(DB::raw("select *from students where "));
        // return $student;
        $term = TuitionFee::find($id);
        if ($term->product_id){
                $product = DB::table('products')->where('products.id',$term->product_id)->get();
                $term->product = $product;
            }else{
                $term->product = null;
            }
            if ($term->zone_id){
                $zone = DB::table('zones')->where('zones.id',$term->zone_id)->get();
                $term->zone = $zone;
            }else{
                $term->zone = null;
            }
        return response()->json($term);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tuitionFee = TuitionFee::find($id);
        return response()->json($tuitionFee);
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
        $tuition_fee = TuitionFee::find($id);
        $tuition_fee->name = trim($request->name);
        $tuition_fee->product_id = $request->product_id;
        $tuition_fee->zone_id = $request->zone_id;
        $tuition_fee->session = $request->{'session'};
        $tuition_fee->number_of_months = $request->number_of_months;
        $tuition_fee->price = trim($request->price);
        $tuition_fee->discount = $request->discount;
        $tuition_fee->receivable = $request->receivable;
        $tuition_fee->created_at = $request->created_at;
        $tuition_fee->creator_id = $request->creator_id;
        $tuition_fee->updated_at = $request->updated_at;
        $tuition_fee->editor_id = $request->editor_id;
        $tuition_fee->available_date = $request->available_date;
        $tuition_fee->expired_date = $request->expired_date;
        $tuition_fee->hash_key = $request->hash_key;
        $tuition_fee->changed_fields = $request->changed_fields;
        $tuition_fee->status = (int)$request->status;
        $tuition_fee->branch_id = implode(',' , $request->branch_id);
        $tuition_fee->type = $request->type == null? 0:$request->type;
        $tuition_fee->accounting_id = $request->accounting_id;
        $tuition_fee->save();
        self::upsertTuitionFeesRelation($id, $request->black_hole, $request->bright, $request->ucrea);

//        $cyberAPI = new CyberAPI();
//        $cyberAPI->updateTuitionFee($tuition_fee, $request->editor_id);
//        $cyberAPI->updateTuitionFeeExpire($tuition_fee, $request->editor_id);

        return response()->json($tuition_fee);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tuition = TuitionFee::find($id);
        if($tuition->delete()) return response()->json("delete Success!");

    }

    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode("," , $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("tuition_fee");

        $p = DB::table('tuition_fee');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }

    public function getBranch($id){
       $t = TuitionFee::find($id);
       $data = explode(",", $t->branch_id);
       $sql = '('.$t->branch_id.')';
       $p = DB::select(DB::raw("SELECT *from branches where id in $sql"));
       return response()->json($p);
    }

    public function list(){
        $sql = "SELECT t.number_of_months AS id,
          CONCAT(t.`number_of_months`,' tháng - giá: ', FORMAT(t.`price`,0),'') AS `name`
        FROM
				  products AS p
				LEFT JOIN tuition_fee AS t ON t.product_id = p.id
				WHERE
				p.`status` > 0 AND t.`status` > 0
				AND (t.available_date <= CURDATE() AND t.expired_date >= CURDATE())
				GROUP BY t.`price`";

        $data = u::query($sql);
        $response = new Response();
        return $response->formatResponse(200, $data, '');
    }

    public function listName(){
        $sql = "SELECT t.id AS id,p.`name` AS p_name,	
          CONCAT(t.`name`,' - giá: ', FORMAT(t.`price`,0),'') AS `name`
        FROM
				  products AS p
				LEFT JOIN tuition_fee AS t ON t.product_id = p.id
				WHERE
				p.`status` > 0 AND t.`status` > 0
				AND (t.available_date <= CURDATE() AND t.expired_date >= CURDATE())";

        $data = u::query($sql);
        $dataNew = [];
        if ($data){
            foreach ($data as $obj){
                if ($obj->p_name == "BRIGHT IG"){
                    $dataNew['bright'][] = $obj;
                }

                if ($obj->p_name == "UCREA"){
                    $dataNew['ucrea'][] = $obj;
                }

                if ($obj->p_name == "BLACK HOLE"){
                    $dataNew['black'][] = $obj;
                }

                if ($obj->p_name == "SUMMER"){
                    $dataNew['summer'][] = $obj;
                }

                if ($obj->p_name == "Tiền Tiểu Học"){
                    $dataNew['tth'][] = $obj;
                }

                if ($obj->p_name == "ACCELIUM"){
                    $dataNew['accelium'][] = $obj;
                }
            }
        }
        $response = new Response();
        return $response->formatResponse(200, ['data' =>$dataNew], '');
    }
}
