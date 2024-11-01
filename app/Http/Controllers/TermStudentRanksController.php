<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TermStudentRank;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;

class TermStudentRanksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $pageSize = $request->pageSize == null? 5:$request->pageSize;
        $student_rank = DB::table('term_student_rank')->select('term_student_rank.*')->paginate($pageSize);
        foreach ($student_rank as $term) {
            if ($term->student_id){
                $student = DB::table('students')->where('students.id', $term->student_id)->get();
                $term->student = $student;
            }
            else {
                $term->student= null;
            }
            if ($term->rank_id){
                $rank = DB::table('ranks')->where('ranks.id', $term->rank_id)->get();
                $term->rank = $rank;
            }else{
                $term->rank = null;
            }
            if ($term->creator_id){
                $creator = DB::table('users')->where('users.id', $term->creator_id)->get();
                $term->creator = $creator;
            }else{
                $term->creator = null;
            }
            if ($term->editor_id){
                $editor = DB::table('users')->where('users.id', $term->editor_id)->get();
                $term->editor = $editor;
            }else{
                $term->editor = null;
            }
        }
        return response()->json($student_rank);
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
        $data = (Object)[];
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $request->validate([
                'student_id' => 'required',
                'rank_id' => 'required'
            ]);            
            $code = APICode::SUCCESS;
            $class = u::first("SELECT cl.id FROM classes cl LEFT JOIN contracts c ON c.class_id = cl.id WHERE c.student_id = $request->student_id");
            $class_id = isset($class->id) ? (int)$class->id : 0;
            u::query("INSERT INTO term_student_rank (
                `student_id`,
                `rank_id`,
                `comment`,
                `class_id`,
                `rating_date`,
                `creator_id`,
                `created_at`,
                `updated_at`
                ) VALUES (
                '$request->student_id',
                '$request->rank_id',
                '$request->comment',
                '$class_id',
                NOW(),
                '$session->id',
                NOW(),
                NOW())");
            $data = u::query("SELECT r.id As rank_id,
                CONCAT(u.full_name, ' - ', u.username) creator, 
                r.name rank_name, 
                t.created_at,
                t.comment 
            FROM term_student_rank t 
                LEFT JOIN users u ON t.creator_id = u.id
                LEFT JOIN ranks as r ON r.id = t.rank_id 
            WHERE t.student_id = $request->student_id");
        }
        return $response->formatResponse($code, $data);
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
        $term = TermStudentRank::find($id);
        if ($term->student_id){
                $student = DB::table('students')->where('students.id', $term->student_id)->get();
                $term->student = $student;
            }
            else {
                $term->student= null;
            }
            if ($term->rank_id){
                $rank = DB::table('ranks')->where('ranks.id', $term->rank_id)->get();
                $term->rank = $rank;
            }else{
                $term->rank = null;
            }
            if ($term->creator_id){
                $creator = DB::table('users')->where('users.id', $term->creator_id)->get();
                $term->creator = $creator;
            }else{
                $term->creator = null;
            }
            if ($term->editor_id){
                $editor = DB::table('users')->where('users.id', $term->editor_id)->get();
                $term->editor = $editor;
            }else{
                $term->editor = null;
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
        $student_rank = TermStudentRank::find($id);
        return response()->json($student_rank);
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
            'rank_id' => 'required',
            'creator_id' => 'required'
        ]);
        $student_rank = TermStudentRank::find($id);

        $student_rank->student_id = $request->student_id;
        $student_rank->rank_id = $request->rank_id;
        $student_rank->comment = $request->comment;
        $student_rank->rating_date = date('Y-m-d H:i:s');
        $student_rank->editor_id = $request->editor_id;
        $student_rank->save();
        return response()->json($student_rank);
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
    }

    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode("," , $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("term_student_rank");

        $p = DB::table('term_student_rank');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }

    public function studentRankHistory($id){
        $data = u::query("SELECT r.id As rank_id,
            CONCAT(u.full_name, ' - ', u.username) creator, 
            t.created_at, 
            r.name rank_name, 
            t.comment 
            FROM term_student_rank t 
                LEFT JOIN users u ON t.creator_id = u.id
                LEFT JOIN ranks as r ON r.id = t.rank_id 
            WHERE t.student_id = $id");
        return $data;
    }
}
