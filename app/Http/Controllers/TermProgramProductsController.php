<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TermProgramProduct;
use Illuminate\Support\Facades\DB;

class TermProgramProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null? 5:$request->pageSize;

        $TermProgramProducts = DB::table('term_program_product')->select('term_program_product.*')->paginate($pageSize);
        foreach ($TermProgramProducts as $term) {
            if ($term->program_id){
                $program = DB::table('programs')->where('programs.id', $term->program_id)->get();
                $term->program = $program;
            }
            else{
                $term->program = null;
            }
            if ($term->product_id){
                $product = DB::table('products')->where('products.id', $term->product_id)->get();
                $term->product = $product;
            }
            else{
                $term->product = null;
            }
        }
        return response()->json($TermProgramProducts);
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
        $TermProgramProduct = DB::table('term_program_product')->where('program_id', $request->program_id)->get();
        // return $TermProgramProduct;
        if ($TermProgramProduct){
            DB::table('term_program_product')->where('id', $TermProgramProduct[0]->id)->update(['product_id'=> $request->product_id, 'program_code_id' => $request->program_code_id, 'status' => $request->status]);
            return 1;
        }
        else{
            $TermProgramProduct = new TermProgramProduct();
            $TermProgramProduct->program_id = $request->program_id;
            $TermProgramProduct->product_id = $request->product_id;
            $TermProgramProduct->program_code_id = $request->program_code_id;
            $TermProgramProduct->status = $request->status;

            $TermProgramProduct->save();
        }
        return response()->json($TermProgramProduct);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $term = TermProgramProduct::find($id);
         if ($term->program_id){
                $program = DB::table('programs')->where('programs.id', $term->program_id)->get();
                $term->program = $program;
            }
            else{
                $term->program = null;
            }
            if ($term->product_id){
                $product = DB::table('products')->where('products.id', $term->product_id)->get();
                $term->product = $product;
            }
            else{
                $term->product = null;
            }
            return response()->json($term);
        // return response()->json("không tìm thấy bản ghi");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $TermProgramProduct = TermProgramProduct::find($id);
        if ($pending){
            
            return response()->json($TermProgramProduct);
        }
        return response()->json("không tìm thấy bản ghi");
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
        $id = (int)$id;
        $TermProgramProduct = (Object)[];
        if ($id) {
            $TermProgramProduct = TermProgramProduct::find($id);        
            $TermProgramProduct->program_id = $request->program_id;
            $TermProgramProduct->product_id = $request->product_id;
            $TermProgramProduct->program_code_id = $request->program_code_id;
            $TermProgramProduct->status = $request->status;
            $TermProgramProduct->save();
        } else {
            $TermProgramProduct = new TermProgramProduct();
            $TermProgramProduct->program_id = $request->program_id;
            $TermProgramProduct->product_id = $request->product_id;
            $TermProgramProduct->program_code_id = $request->program_code_id;
            $TermProgramProduct->status = $request->status;
            $TermProgramProduct->save();
        }
        DB::table('programs')->where('id', $request->program_id)->update([
            'status' => $request->status
        ]);
        return response()->json($TermProgramProduct);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $TermProgramProduct = TermProgramProduct::find($id);
        if ($TermProgramProduct->delete()){
            return response()->json("delete success");
        }
    }

     public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode("," , $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("term_program_product");

        $p = DB::table('term_program_product');
        
        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }
}
