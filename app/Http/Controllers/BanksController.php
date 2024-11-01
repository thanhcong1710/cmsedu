<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Zone;
use App\Models\Bank;
use \Mockey\Exception;
use App\Providers\UtilityServiceProvider as u;

class BanksController extends Controller
{
    public function index( Request $request ) 
    {
        $result = Bank::all();
        return response()->success($result);
    }

    public function getAll(Request $request) {
        $data = null;
        if ($session = $request->users_data) {
            $data = u::query("SELECT id, name, alias, logo FROM banks");
        }
        return response()->success($data);
    }

    public function store( Request $request )
    {
        $userData = $request->users_data;
        $hasKey = md5($request->name . $request->alias . $request->note);
        $dataInsert = [
            'name' => $request->name,
            'alias' => $request->alias,
            'note' => $request->note,
            'hash_key' => $hasKey,
            'created_at' => now(),
            'updated_at' => now(),
            'creator_id' => $userData->id,
        ];

        if( $request->hasFile('logo') ) {
            $folderUpload = FOLDER.DS.'\\img\\others';
            $file = $request->file('logo');
            $fileName = md5(time()).'.'.$file->getClientOriginalExtension();
            $file->move($folderUpload, $fileName);
            $dataInsert['logo'] = DS . 'img\\others\\' . $fileName;
        }
        try {
            DB::table('banks')->insert($dataInsert);
            return response()->success('OK');
        } catch(Exception $e) {
            return response()->error('Có lỗi xảy ra',500);
        }
        
    }

    public function update( Request $request, $id ) 
    {
        $userData = $request->users_data;
        $hasKey = md5($request->name . $request->alias . $request->note);
        $dataInsert = [
            'name' => $request->name,
            'alias' => $request->alias,
            'note' => $request->note,
            'hash_key' => $hasKey,
            'updated_at' => now(),
            'updator_id' => $userData->id,
        ];

        if( $request->hasFile('logo') ) {
            $folderUpload = FOLDER.DS.'\\img\\others';
            $file = $request->file('logo');
            $fileName = md5(time()).'.'.$file->getClientOriginalExtension();
            $file->move($folderUpload, $fileName);
            $dataInsert['logo'] = DS . 'img\\others\\' . $fileName;
        }
        // dd($dataInsert);

        try {
            DB::table('banks')->where('id',$id)->update($dataInsert);
            return response()->success('OK');
        } catch(Exception $e) {
            return response()->error('Có lỗi xảy ra',500);
        }
    }

    public function show( $id )
    {
        $data = Bank::find($id);
        return response()->success($data);
    }

    public function destroy( $id ) 
    {
        $data = Bank::find($id);
        try {
            $data->delete();
            return response()->success('OK');
        } catch(Exception $e) {
            return response()->error('Có lỗi xảy ra',500);
        }
    }
}
