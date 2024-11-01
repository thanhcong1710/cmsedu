<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PendingRegulation;

class PendingRegulationsController extends Controller
{
    //
    public function index(){
    	return response()->json(PendingRegulation::paginate(5));
    }

    public function show($id){
    	$penRole = PendingRegulation::find($id);
    	return response()->json($penRole);
    }

    public function edit($id){
    	$penRole = PendingRegulation::find($id);
    	return response()->json($penRole);
    }
    public function store(Request $request){
    	$request->validate([
    		'role_id' => 'required',
            'min_days' => 'required',
    		'max_days' => 'required',
    		'start_date' => 'required',
    		'expired_date' => 'required',
            'status' => 'required'
    	]);
        // dd($request->all());

    	$rule = new PendingRegulation();
    	$rule->role_id = $request->role_id;
        $rule->min_days = $request->min_days;
    	$rule->max_days = $request->max_days;
    	$rule->start_date = $request->start_date;
        $rule->expired_date = $request->expired_date;
        $rule->status = $request->status;
    	$rule->type = $request->type;

    	$rule->save();
    	return response()->json($rule);
    }
    public function update(Request $request, $id){
       $request->validate([
            'role_id' => 'required',
            'min_days' => 'required',
            'max_days' => 'required',
            'start_date' => 'required',
            'expired_date' => 'required',
            'status' => 'required'
        ]);

        $rule =  PendingRegulation::find($id);
        $rule->role_id = $request->role_id;
        $rule->min_days = $request->min_days;
        $rule->max_days = $request->max_days;
        $rule->start_date = $request->start_date;
        $rule->expired_date = $request->expired_date;
        $rule->type = $request->type;
        $rule->status = $request->status;

        $rule->save();
        return response()->json($rule);
    }

    public function destroy($id){
    	$penRole = PendingRegulation::find($id);
    	$penRole->delete();
    	return response()->json("delete Success");
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
        $rules =  DB::select(DB::raw($get_code_query));
        if ($rules){
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
        $response['pending'] = $rules;
        $response['message'] = 'successful';
        return response()->json($response);
        
    }

    private function query($request, $page = 1, $limit = 20, $filter = NULL){
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";

        $where = '';        

        if ($request->search != '_'){
            $key = explode(",", $request->search);
            $value = explode(",", $request->filter);

            for ($i= 0; $i<count($key); $i++){
                if ($key[$i] == "days"){
                    $v = $value[$i];
                    $where .= "and pr.min_days <= $v and pr.max_days >= $v";
                }
                else if ($key[$i] == "start_date"){
                    $v = $value[$i];
                    $where .="and pr.start_date >= '$v'";
                }
                else if ($key[$i] == "expired_date"){
                    $v = $value[$i];
                    $where .="and pr.expired_date <= '$v'";
                }
                else{
                    $where .= "and pr.$key[$i] like '%$value[$i]%' ";   
                } 
            }
        }
        if ($where){
            $where = ltrim($where, "and");
            $where = "where ".$where;
        }

        $filter = $request->filter ? (int)$request->filter : $filter; 
        $query = "SELECT pr.*, r.name as role_name 
                        FROM pending_regulation as pr 
                        join roles as r on pr.role_id = r.id 
                        $where ORDER BY pr.status DESC, pr.id DESC $limitation";
        $count_query = "SELECT COUNT(id) as total FROM pending_regulation as pr $where";
        $total = DB::select(DB::raw($count_query));
        $total = $total[0]->total;
        return [
            'base_query' => $query,
            'total' => $total,
            'limit' => $limit,
            'page' => $page
        ];
    }
}
