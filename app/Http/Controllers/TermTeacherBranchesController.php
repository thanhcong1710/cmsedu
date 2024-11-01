<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveTeacher;
use App\Models\Mail;
use App\Models\Session;
use App\Models\Teacher;
use App\Models\TermUserBranch;
use App\Models\TermUserClass;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TermTeacherBranch;
use Illuminate\Support\Facades\DB;

class TermTeacherBranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null? 5:$request->pageSize;
        $termTeacher = DB::table('term_teacher_branch')->paginate($pageSize);
        foreach ($termTeacher as $term) {
            # code...
            if ($term->teacher_id){
                $teacher = DB::table('teachers')->where('teachers.id', $term->teacher_id)->get();
                $term->teacher = $teacher;
            }
            else{
                $term->teacher = null;
            }

            if ($term->branch_id){
                $branch = DB::table('branches')->where('branches.id', $term->branch_id)->get();
                $term->branch = $branch;
            }else{
                $term->branch = null;
            }
        }
        return response()->json($termTeacher);
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
        $termTeacher = new TermTeacherBranch();

        $termTeacher->teacher_id = $request->teacher_id;
        $termTeacher->branch_id = $request->branch_id;
        $termTeacher->is_head_teacher = $request->is_head_teacher;
        $termTeacher->status = $request->status;

        $termTeacher->save();
        return response()->json($termTeacher);
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
        $term = TermTeacherBranch::find($id);
        if ($term->teacher_id){
                $teacher = DB::table('teachers')->where('teachers.id', $term->teacher_id)->get();
                $term->teacher = $teacher;
            }
            else{
                $term->teacher = null;
            }

            if ($term->branch_id){
                $branch = DB::table('branches')->where('branches.id', $term->branch_id)->get();
                $term->branch = $branch;
            }else{
                $term->branch = null;
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
        $termTeacher = TermTeacherBranch::find($id);
        return response()->json($termTeacher);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveTeacher $request, $id)
    {
        $mail = new Mail();
        //
        $termTeacher = TermTeacherBranch::find($id);

        $termTeacher->teacher_id = $request->teacher_id;
        $termTeacher->branch_id = $request->branch_id;
        $termTeacher->is_head_teacher = $request->is_head_teacher;
        $termTeacher->status = $request->status;

        $termTeacher->save();
        // Check
        $checkUser = User::where('email',$request->email)->first();
        if(!$checkUser) {
            $checkHrm = User::where('hrm_id',$request->hrm_id)->first();
            if( $checkHrm ) {
              return response()->error('Trùng mã nhân sự - ' . $request->hrm_id,200);
            }
            $userNameTmp = explode('@',$request->email);
          // Create User
            $user = new User();
            $user->email        = trim($request->email);
            $user->hrm_id       = trim($request->hrm_id);
            $user->full_name    = strtoupper($termTeacher->teacher->ins_name);
            $user->username     = strtoupper($userNameTmp[0]);
            $user->password     = '$2y$10$Um3J8PaKm4mcTHsDEREbiujzvG5qe.E54SICZsOhlx1mlX2o5DBzK';
            $user->status       = 1;
            $user->start_date   = now();
            $user->end_date     = '2099-02-08';
            $user->save();

            // Update Teachers
            $termTeacher->teacher->meta_data = json_encode(['user_id' => $user->id]);
            $termTeacher->teacher->save();
            //Create Term Teach
            $termUserBranch = new TermUserBranch();
            $termUserBranch->user_id = $user->id;
            $termUserBranch->branch_id = $termTeacher->branch_id;
            $termUserBranch->role_id = 36; // ROLE_ID
            $termUserBranch->status = 1;
            $termUserBranch->created_at = now();
            $termUserBranch->updated_at = now();
            $termUserBranch->save();

            $sessions = Session::where('teacher_id',$termTeacher->teacher_id)->groupBy('class_id')->get();
            if($sessions) {
                foreach( $sessions as $session ) {
                    // Create TermUserClass
                    $termUserClass = new TermUserClass();
                    $termUserClass->user_id = $user->id;
                    $termUserClass->class_id = $session->class_id;
                    $termUserClass->start_date = now();
                    $termUserClass->status = 1;
                    $termUserClass->save();
                }
            }
            $content= "
              <!DOCTYPE html>
              <html>
              <head>
              <meta charset='utf-8'>
              <meta name='viewport' content='width=device-width'>
              <style type='text/css'>
                body{ font-family: 'Times New Roman', Times, serif; font-style: italic; }
                p{ font-style: normal; }
              </style>
              </head>
              <body>
              <strong>Dear : Mr/ Ms ".strtoupper($termTeacher->teacher->ins_name)."</strong><br/><br/>
              <strong>CRM Login Details <a href='http://crm.cmsedu.vn/'>http://crm.cmsedu.vn/</a></strong><br/><br/>
              <i>Username:</i> <b>".strtoupper($userNameTmp[0])."</b><br/>
              <i>Password:</i> <b>@12345678</b><br/><br/>
              <i>APAX English Support Mail:</i> <a href='mailto:erp.cmsedu.vn'>erp.cmsedu.vn</a><br/><br/>
              </body>
              </html>
             ";
             $subject = "CRM Login Details";
             if(APP_ENV == 'product'){
                 $mail->sendSingleMail(
                     [
                         'address'=>trim($request->email),
                         'name'=>strtoupper($termTeacher->teacher->ins_name)
                     ],
                     $subject,
                     $content
                 );
             }

          return response()->success(['message'=> 'Cập nhật thành công!']);
        } else {
            if( $request->hrm_id ) {
                if( $checkUser->hrm_id != $request->hrm_id ) {
                  $checkHrm = User::where('hrm_id',$request->hrm_id)->whereNotIn('id',[$checkUser->id])->first();
                  if( $checkHrm ) {
                    return response()->error('Trùng mã nhân sự - ' . $request->hrm_id,200);
                  } else {
                    $checkUser->hrm_id = trim($request->hrm_id);
                    $checkUser->save();
                  }
                }
            }
        }

        return response()->success(['message'=> 'Cập nhật thành công!']);
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
        $termTeacher = TermTeacherBranch::find($id);
        if ($termTeacher->delete()) return response()->json("deletet succeess!");
    }

    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode("," , $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("term_teacher_branch");

        $p = DB::table('term_teacher_branch');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }
}
