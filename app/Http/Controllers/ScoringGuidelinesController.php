<?php

namespace App\Http\Controllers;
/*
 * Created by HoiLN
 */

use App\Models\APICode;
use App\Models\Response;
use App\Models\ScoringGuidelines;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoringGuidelinesController extends Controller
{
    public function lists()
    {
        $code = APICode::SUCCESS;
        $response = new Response();

        $results = ScoringGuidelines::all();
        return $response->formatResponse($code, $results);
    }

    public function list(Request $request){
        $products = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];
        $query_information = self::query($request);
        $get_code_query = $query_information['base_query'];
        // return $get_code_query

        $scoring =  DB::select(DB::raw($get_code_query));
        if ($products){
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
        $response['scoring'] = $scoring;
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
                $where .= "and $key[$i] like '%$value[$i]%' ";
            }
        }
        if ($where){
            $where = ltrim($where, "and");
            $where = "where ".$where;
        }
        $query = "SELECT *from scoring_guidelines $where $limitation";
        $count_query = "SELECT COUNT(id) as total FROM books";
        $total = DB::select(DB::raw($count_query));
        $total = $total[0]->total;
        return [
            'base_query' => $query,
            'total' => $total,
            'limit' => $limit,
            'page' => $page
        ];
    }

    public function detail(Request $request, $id)
    {
        $code = APICode::SUCCESS;
        $response = new Response();

        $result = ScoringGuidelines::find($id);
        return $response->formatResponse($code, $result);
    }

    public function create(Request $request)
    {
        $code = APICode::SUCCESS;
        $response = new Response();

        $dataInsert = [
            'score' => $request->score,
            'guideline' => $request->guideline,
            'explanation' => $request->explanation,
            'status'=>$request->status,
        ];
        $dataCreated = ScoringGuidelines::create($dataInsert);
        return $response->formatResponse($code, $dataCreated);
    }

    public function update(Request $request)
    {
        $code = APICode::SUCCESS;
        $response = new Response();
        // dd($request->all());
        $id = $request->id;
        $score = $request->score;
        $guideline = $request->score;
        $explanation = $request->explanation;
        $status = $request->status;

        // dd($status);


        $updateID = trim($id);
        $dataUpdate = [
            'score' => $score,
            'guideline' => $guideline,
            'explanation' => $explanation,
            'status' => $status
        ];
        // dd($dataUpdate);
        $item = ScoringGuidelines::find($updateID);
        if( $item ) {
            $item->update($dataUpdate);
            return $response->formatResponse($code, $item);
        }
        $code = APICode::PAGE_NOT_FOUND;
        return $response->formatResponse($code, $item);
    }

    public function remove(Request $request, $id)
    {
        $code = APICode::SUCCESS;
        $data = null;
        $response = new Response();

        $item = ScoringGuidelines::find($id);
        if( $item ) {
            $item->delete();
            return $response->formatResponse($code, $data);
        }
        $code = APICode::PAGE_NOT_FOUND;
        return $response->formatResponse($code, $data);
    }


}