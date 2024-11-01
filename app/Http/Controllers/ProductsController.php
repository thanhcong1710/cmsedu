<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\APICode;
use App\Models\Product;
use App\Models\Response;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\Paginator;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $pageSize = $request->pageSize == null?5: $request->pageSize;
        $products = Product::paginate($pageSize);
        return response()->json($products);
    }


    public function program_codes(Request $request, $id){
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $code = APICode::SUCCESS;
            $data = DB::table('program_codes')->where('product_id', $id)->get();
        }
        return $response->formatResponse($code, $data);
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

    public function list($page, Request $request){
        $products = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];

        $query_information = self::query($request);
        $get_code_query = $query_information['base_query'];
        // return $get_code_query
        $products =  DB::select(DB::raw($get_code_query));
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
        $response['products'] = $products;
        $response['message'] = 'successful';
        return response()->json($response);

    }

    private function query($request, $page = 1, $limit = 20, $filter = NULL){
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $filter = $request->filter ? (int)$request->filter : $filter;
        $query = "SELECT *from products ORDER BY id DESC $limitation";
        $count_query = "SELECT COUNT(id) as total FROM products";
        $total = DB::select(DB::raw($count_query));
        $total = $total[0]->total;
        return [
            'base_query' => $query,
            'total' => $total,
            'limit' => $limit,
            'page' => $page
        ];
    }

     public function showAll(){
        $products = Product::all();
        return response()->json($products);
     }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $return = 1;
        $product = new Product();

        $product->name = $request->name;
        $product->status = $request->status;
        $product->max_number_of_days_in_week = $request->max_number_of_days_in_week;
        $product->min_number_of_days_in_week = $request->min_number_of_days_in_week;
        $product->accounting_id = $request->accounting_id;
        $product->description = $request->description;
        if($product->save()){
            $return = $product;
        }

        return response()->json($return);
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
        $product = Product::find($id);
        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $product = Product::find($id);
      return response()->json($product);
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
        $product = Product::find($id);

        $product->name = $request->name;
        $product->status = $request->status;
        $product->max_number_of_days_in_week = $request->max_number_of_days_in_week;
        $product->min_number_of_days_in_week = $request->min_number_of_days_in_week;
        $product->accounting_id = $request->accounting_id;
        $product->description = $request->description;
        $product->save();

        return response()->json($product);

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
        $product = Product::findOrFail($id);


        $product->classes()->delete();
        $product->tuition_fees()->delete();
        $product->delete();
        return response()->json("Delete Error");
    }


    public function search(Request $request){
        $field = explode( "," ,$request->field);
        $keyword = $request->keyword;
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("products");

        $p = DB::table('products');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword.'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword.'%');
            }
        }
        return $p->paginate($pageSize);
    }

    public function getIdNameProduct($id){
        $product = Product::select('id', 'name')->get();
        return response()->json($product);
    }

    public function getProductBySemesterId($semesterId){
      if(empty($semesterId)){
        return null;
      }
      $semester = Semester::select('product_id')->where('id', (int)$semesterId)->first();
      $product = Product::where('id', $semester->product_id)->first();
      return response()->json($product);
    }

	 public function getTuitions($id){
        $tuitions = DB::table('tuition_fee')->where('product_id', $id)->where('tuition_fee.expired_date', '>=', date('Y-m-d'))->groupBy('tuition_fee.name')->get();
        return $tuitions;
    }


    public function searchProducts(Request $request)
    {
      $where = null;
      $name = $request->name;
      $status = $request->status;
      $lms_id = $request->lms_id;
      if($name){
        $where .= "AND name like '%$name%' ";
      }
      if($lms_id){
          $where .= " AND lms_id like '%$lms_id%'";
      }
      if($status===0||$status==1){
          $where .= "AND status = $status";
      }
      $query = "SELECT * from products WHERE id > 0 $where ORDER BY id DESC";

      $data = DB::select(DB::raw($query));

      return response()->json($data);
    }

}
