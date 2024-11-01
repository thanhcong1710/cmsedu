<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ContractMethod;

class ContractMethodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null ? 5:$request->pageSize;
        $contractMethods = ContractMethod::paginate($pageSize);
        return response()->json($contractMethods);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
            'name' => 'required',
            'status' => 'required',
            'created_at' => 'required'
        ]);

        $contract = new ContractMethod();
        $contract->name = $request->name;
        $contract->status = $request->status;
        $contract->created_at = $request->created_at;
        $contract->updated_at = $request->updated_at;
        $contract->save();

        return response()->json($contract);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contract = ContractMethod::find($id);
        return response()->json($contract);
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
        $contract = ContractMethod::find($id);
        return response()->json($contract);
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
            'name' => 'required',
            'status' => 'required',
            'created_at' => 'required'
        ]);

        $contract = ContractMethod::find($id);
        $contract->name = $request->name;
        $contract->status = $request->status;
        $contract->created_at = $request->created_at;
        $contract->updated_at = $request->updated_at;
        $contract->save();

        return response()->json($contract);
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
        $contract = ContractMethod::find($id);
        if ($contract->delete()) return response()->json("deltete success!");

    }

     public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode("," , $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("contract_methods");

        $p = DB::table('contract_methods');
        
        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }
}
