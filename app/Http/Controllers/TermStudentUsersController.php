<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TermStudentUser;
use Illuminate\Support\Facades\DB;

class TermStudentUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null? 5:$request->pageSize;
        
        $TermStudentUser = DB::table('term_student_user')->select('term_student_user.*')->paginate($pageSize);
        foreach ($TermStudentUser as $term) {
            # code...
            if ($term->student_id){
                $student = DB::table('students')->where('students.id', $term->student_id)->get();
                $term->student = $student;
            }
            else {
                $term->student= null;
            }
            if ($term->ec_id){
                $ec = DB::table('users')->where('users.id', $term->ec_id)->get();
                $term->ec = $ec;
            }
            else {
                $term->ec= null;
            }
            if ($term->cm_id){
                $cm = DB::table('users')->where('users.id', $term->cm_id)->get();
                $term->cm = $cm;
            }
            else {
                $term->cm= null;
            }
            if ($term->branch_id){
                $branch = DB::table('branches')->where('branches.id', $term->branch_id)->get();
                $term->branch_name = $branch[0]->name;
            }
            else {
                $term->branch_name= null;
            }
            if ($term->region_id){
                $region = DB::table('regions')->where('regions.id', $term->region_id)->get();
                $term->region_name = $region[0]->name;
            }
            else {
                $term->region_name= null;
            }
            if ($term->zone_id){
                $zone = DB::table('zones')->where('zones.id', $term->zone_id)->get();
                $term->zone_name = $zone[0]->name;
            }
            else {
                $term->zone_name= null;
            }

        }
        return response()->json($TermStudentUser);
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
        //
        $request->validate([
            'student_id' => 'required',
            'ec_id' => 'required',
            'cm_id' => 'required',
            'created_at' => 'required',
            'status' => 'required'
        ]);

        $termStudent = new TermStudentUser();
        $termStudent->student_id = $request->student_id;
        $termStudent->ec_id = $request->ec_id;
        $termStudent->cm_id = $request->cm_id;
        $termStudent->created_at = $request->created_at;
        $termStudent->updated_at = $request->updated_at;
        $termStudent->status = $request->status;
        $termStudent->branch_id = $request->branch_id;
        $termStudent->ceo_branch_id = $request->ceo_branch_id;
        $termStudent->ceo_region_id = $request->ceo_region_id;
        $termStudent->region_id = $request->region_id;
        $termStudent->zone_id = $request->zone_id;

        $termStudent->save();

        return response()->json($termStudent);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $term = TermStudentUser::find($id);
        if ($term->student_id){
                $student = DB::table('students')->where('students.id', $term->student_id)->get();
                $term->student = $student;
            }
            else {
                $term->student= null;
            }
            if ($term->ec_id){
                $ec = DB::table('users')->where('users.id', $term->ec_id)->get();
                $term->ec = $ec;
            }
            else {
                $term->ec= null;
            }
            if ($term->cm_id){
                $cm = DB::table('users')->where('users.id', $term->cm_id)->get();
                $term->cm = $cm;
            }
            else {
                $term->cm= null;
            }
            if ($term->branch_id){
                $branch = DB::table('branches')->where('branches.id', $term->branch_id)->get();
                $term->branch_name = $branch[0]->name;
            }
            else {
                $term->branch_name= null;
            }
            if ($term->region_id){
                $region = DB::table('regions')->where('regions.id', $term->region_id)->get();
                $term->region_name = $region[0]->name;
            }
            else {
                $term->region_name= null;
            }
            if ($term->zone_id){
                $zone = DB::table('zones')->where('zones.id', $term->zone_id)->get();
                $term->zone_name = $zone[0]->name;
            }
            else {
                $term->zone_name= null;
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
        //
        $termStudent = TermStudentUser::find($id);
        return response()->json($termStudent);
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
            'student_id' => 'required',
            'ec_id' => 'required',
            'cm_id' => 'required',
            'created_at' => 'required',
            'status' => 'required'
        ]);

        $termStudent = TermStudentUser::find($id);
        $termStudent->student_id = $request->student_id;
        $termStudent->ec_id = $request->ec_id;
        $termStudent->cm_id = $request->cm_id;
        $termStudent->created_at = $request->created_at;
        $termStudent->updated_at = $request->updated_at;
        $termStudent->status = $request->status;
        $termStudent->branch_id = $request->branch_id;
        $termStudent->ceo_branch_id = $request->ceo_branch_id;
        $termStudent->ceo_region_id = $request->ceo_region_id;
        $termStudent->region_id = $request->region_id;
        $termStudent->zone_id = $request->zone_id;
        
        $termStudent->save();

        return response()->json($termStudent);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $termStudent = TermStudentUser::find($id);
        if ($termStudent->delete()) return response()->json($termStudent);
    }

    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode("," , $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("term_student_user");

        $p = DB::table('term_student_user');
        
        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }
}
