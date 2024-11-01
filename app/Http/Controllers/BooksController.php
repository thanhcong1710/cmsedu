<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Book;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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

        $books =  DB::select(DB::raw($get_code_query));
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
        $response['books'] = $books;
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
                $where .= "and b.$key[$i] like '%$value[$i]%' ";
            }
        }
        if ($where){
            $where = ltrim($where, "and");
            $where = "where ".$where;
        }
        $query = "SELECT b.*, pr.name as product_name 
                        from books as b 
                        left join products as pr on pr.id = b.product_id $where ORDER BY b.status DESC, b.id DESC $limitation";
        $count_query = "SELECT COUNT(id) as total FROM books as b $where";
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
        $book = new Book;
        $book->name = $request->name;
        $book->product_id = $request->product_id;
        $book->status = $request->status;
        $book->start_date = $request->start_date;
        $book->end_date = $request->end_date;
        $book->book_id = $request->book_id;
        $book->year_apply = $request->year_apply;
        $book->save();
        return response()->json($book);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::find($id);
        return response()->json($book);
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
        $book = Book::find($id);
        $book->name = $request->name;
        $book->product_id = $request->product_id;
        $book->status = $request->status;
        $book->start_date = $request->start_date;
        $book->end_date = $request->end_date;
        $book->book_id = $request->book_id;
        $book->year_apply = $request->year_apply;
        $book->save();
        return response()->json($book);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        $book->delete();
        return response()->json("OK");
    }

    public function checkExistBook($name)
    {
        $q = "select * from books where name = '$name'";
        $rs = DB::select(DB::raw($q));
        if($rs){
            return 1;
        }else {
            return 0;
        }
    }
}
