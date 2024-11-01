<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use Illuminate\Support\Facades\DB;

class ConfigsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($session = json_decode($request->authorized)){
            $id = $session->id;
        }
        return $id;
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
        $zones = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];

        $query_information = self::query($request);
        $get_code_query = $query_information['base_query'];
        // return $get_code_query
        $configs =  DB::select(DB::raw($get_code_query));
        if ($configs){
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
        $response['configs'] = $configs;
        $response['message'] = 'successful';
        return response()->json($response);
        
    }

    private function query($request, $page = 1, $limit = 20, $filter = NULL){
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $filter = $request->filter ? (int)$request->filter : $filter; 
        $query = "SELECT *from config $limitation";
        $count_query = "SELECT COUNT(id) as total FROM config";
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
        $request->validate([
            'key' => 'required',
            'value' => 'required'
        ]);

        $config = new Config();

        $config->key =  $request->key;
        $config->value = $request->value;
        $config->status = $request->status;
        $config->name=  $request->name;
        $config->group = $request->group;
        $config->description = $request->description;

        $config->save();
        return response()->json($config);
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
        $config = Config::find($id);
        return response()->json($config);
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
         $config = Config::find($id);
        return response()->json($config);
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
            'key' => 'required',
            'value' => 'required'
        ]);

        $config = Config::find($id);

        $config->key  = $request->key;
        $config->value = $request->value;
        $config->status = $request->status;
        $config->name = $request->name;
        $config->group = $request->group;
        $config->description = $request->description;

        $config->save();
        return response()->json($config);
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
         $config = Config::find($id);
         $config->delete();
        return response()->json("delete success!");
    }
}
