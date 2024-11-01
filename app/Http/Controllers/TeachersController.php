<?php

namespace App\Http\Controllers;

use App\Models\Mail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\ProcessExcel;
use PhpOffice\PhpSpreadsheet\Exception;
use App\Models\TermTeacherBranch;
use App\Models\TermUserBranch;
use App\Providers\UtilityServiceProvider as u;

class TeachersController extends Controller
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
        $teachers = Teacher::paginate($pageSize);
        foreach ($teachers as $teacher) {
            # code...
            $p = DB::table('term_teacher_branch')->where('term_teacher_branch.teacher_id',$teacher->id);
            if ($p->count() !=0){
                $term = $p->get();
                $teacher->status = $term[0]->status;
                $branch = DB::table('branches')->Join('term_teacher_branch', 'term_teacher_branch.branch_id','branches.id')->where('term_teacher_branch.teacher_id', $teacher->id)->get();
                $teacher->branch = $branch[0]->name;
            }
            else{
                $teacher->branch = null;
            }
        }
        return response()->json($teachers);
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
        $zones = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];

        $query_information = self::query($request);
        $get_zones_query = $query_information['base_query'];
        $teachers =  DB::select(DB::raw($get_zones_query));

        $i= 0 ;
        foreach( $teachers as $item ) {
            if( $item->meta_data != '' ) {
                $item_meta = json_decode($item->meta_data);
                $userID = $item_meta->user_id;
                $user = DB::table('users')->where('id',$userID)->first();
                if($user) {
                    $teachers[$i]->email = $user->email;
                }
            }
            $i++;
        }
        if ($zones){
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
            'cpage' => $cpage,
            'lpage' => $lpage,
            'limit' => $limit,
            'total' => $total
        ];
        $response['teachers'] = $teachers;
        $response['message'] = 'successful';
        return response()->json($response);

    }

    private function query($request, $page = 1, $limit = 20){
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";

        $where = '';

        if($request->branches != '' ) {
            $branches = $request->branches;
            $where .= " AND br.id in ($branches) ";
        }
        if($request->ins_name != '' ) {
            $ins_name = $request->ins_name;
            $where .= " AND t.ins_name LIKE '$ins_name%' ";
        }
        if($request->ins_id != '' ) {
            $ins_id = $request->ins_id;
            $where .= " AND t.ins_id = $ins_id ";
        }
        if($request->status != '' ) {
            $status = $request->status;
            $where .= " AND term.status = $status ";
        }

        if($request->is_head_teacher != '' ) {
          $is_head_teacher = $request->is_head_teacher;
          $where .= " AND term.is_head_teacher = $is_head_teacher ";
        }


        $query = "SELECT t.*, term.is_head_teacher, term.status, br.name as branch_name,t.meta_data from teachers as t 
                LEFT JOIN term_teacher_branch as term on term.teacher_id = t.id 
                LEFT JOIN branches as br on br.id = term.branch_id
                WHERE 1 = 1 
                $where
                $limitation";
        $count_query = "SELECT COUNT(t.id) as total FROM teachers as t 
                        LEFT JOIN term_teacher_branch as term on term.teacher_id = t.id 
                        LEFT JOIN branches as br on br.id = term.branch_id
                        WHERE 1 = 1 
                        $where";
        $total = DB::select(DB::raw($count_query));
        $total = $total[0]->total;
        return [
            'base_query' => $query,
            'total' => $total,
            'limit' => $limit,
            'page' => $page
        ];
    }

    public function exportExcel(Request $request) 
    {
        $query_information = self::query($request,1,100000000000);
        $teachers =  DB::select(DB::raw($query_information['base_query']));

        foreach( $teachers as $item ) {
            if( $item->meta_data != '' ) {
                $item_meta = json_decode($item->meta_data);
                $userID = $item_meta->user_id;
                $user = DB::table('users')->where('id',$userID)->first();
                if($user) {
                    $item->email = $user->email;
                } else {
                    $item->email = '';
                }
            } else {
                $item->email = '';
            }
        }


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'DANH SÁCH CÁC GIÁO VIÊN');
        $sheet->mergeCells('A1:G1');

        $sheet->setCellValue("A2", "STT");
        $sheet->setCellValue("B2", "Mã INS giáo viên");
        $sheet->setCellValue("C2", "Tên giáo viên");
        $sheet->setCellValue("D2", "Trung tâm làm việc");
        $sheet->setCellValue("E2", "Trạng thái");
        $sheet->setCellValue("F2", "Loại giáo viên");
        $sheet->setCellValue("G2", "Email");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(20);
        $sheet->getColumnDimension("C")->setWidth(40);
        $sheet->getColumnDimension("D")->setWidth(40);
        $sheet->getColumnDimension("E")->setWidth(20);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(44);

        $i = 3;
        foreach ($teachers as $value) {
            
            if( $value->is_head_teacher == 1 ) {
                $tc_type = 'Head Teacher';
            } else if(  $value->is_head_teacher == 0 ) {
                $tc_type = 'Teacher';
            }else {
                $tc_type = '';
            }

            $sheet->setCellValue("A$i", $i - 2);
            $sheet->setCellValue("B$i", $value->ins_id);
            $sheet->setCellValue("C$i", $value->ins_name);
            $sheet->setCellValue("D$i", $value->branch_name);
            $sheet->setCellValue("E$i", ($value->status == 1) ? "Hoạt động" : "Không hoạt động" );
            $sheet->setCellValue("F$i", $tc_type);
            $sheet->setCellValue("G$i", $value->email);
            $i++;
        }

        ProcessExcel::styleCells($spreadsheet, "A1:G1", null, null, 20, 700, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A2:G2", null, null, 11, 700, true, "center", "center", true);

        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Danh sách giáo viên.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;

        dd($teachers);
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
            'ins_name' => 'required|max:50'
        ]);
        
        $user = new User();
        $user->username = explode('@', $request->email)[0];
        $user->full_name = $request->ins_name;
        $user->hrm_id = $request->hrm_id;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt('@12345678');
        $user->created_at = now();
        $user->updated_at = now();
        $user->save();
        
        $teacher = new Teacher();
        $teacher->user_id = $user->id;
        $teacher->ins_name = trim($request->ins_name);
        $teacher->created_at = date('Y:m:d H:i:s');
        $teacher->meta_data = json_encode(array('user_id'=>$user->id));

        $teacher->id_lms = $user->username ;
        $teacher->call_lms = 1;

        $teacher->save();
        
        $term_teacher_branch = new TermTeacherBranch();
        $term_teacher_branch->teacher_id = $teacher->id;
        $term_teacher_branch->branch_id = $request->branch_id;
        $term_teacher_branch->status = $request->status;
        $term_teacher_branch->created_at = date('Y:m:d H:i:s');
        $term_teacher_branch->is_head_teacher = $request->is_head_teacher;
        $term_teacher_branch->save();
        
        $term_user_branch = new TermUserBranch();
        $term_user_branch->user_id = $user->id;
        $term_user_branch->branch_id = $request->branch_id;
        $term_user_branch->role_id = $request->is_head_teacher ==1 ? 37 : 36;
        $term_user_branch->status = $request->status;
        $term_user_branch->created_at = now();
        $term_user_branch->updated_at = now();
        $term_user_branch->save();
        $lsmAPI = new LMSAPIController();
        $lsmAPI->createTeacherLMS($teacher->id);

        $mail = new Mail();
        $to = array('address' => $request->email, 'name' => $request->ins_name);
        $subject = "[CRM] THÔNG TIN TÀI KHOẢN";
        $body = "<p>Kính gửi: $request->ins_name</p>
        <p>CMS gửi tới anh/chị thông tin tài khoản:</p>
        <br>
        <p>1. Tài khoản CRM:</p>
        <p>- Tài khoản đăng nhập: <b>$user->hrm_id</b></p>
        <p>- Mật khẩu: <b>@12345678</b></p>
        <p>- Link đăng nhập: <a href='https://account.cmsedu.vn'>https://account.cmsedu.vn</a></p> 
        <p> Link hướng dẫn: <a href='https://drive.google.com/drive/folders/1XO-3nVOd-4FrYCh_0xkFV7MK8I1IXPDt?usp=sharing'>https://drive.google.com/drive/folders/1XO-3nVOd-4FrYCh_0xkFV7MK8I1IXPDt?usp=sharing</a></p>
        <br>
        <p>2. Tài khoản LMS:</p>
        <p>- Tài khoảng đăng nhập: <b>$user->username</b></p>
        <p>- Mật khẩu: <b>@12345678</b></p>
        <p>- Link đăng nhập: <a href='https://lms-vn.cmsedu.net'>https://lms-vn.cmsedu.net</a></p>
        <br>
        <p>Trân trọng cảm ơn!</p>";
        $mail->sendSingleMail($to, $subject, $body);
        
        return response()->json($teacher);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $teacher = DB::select(DB::raw("SELECT t.*,term.branch_id,term.id as term_id, term.is_head_teacher, term.status, br.name as branch_name from teachers as t 
                LEFT Join term_teacher_branch as term on term.teacher_id = t.id 
                LEFT JOIN branches as br on br.id = term.branch_id
                where t.id = $id
                "));

        foreach($teacher as $item) {
            $metaData = (array)json_decode($item->meta_data);
            if( isset($metaData['user_id']) ) {
                $user = User::find($metaData['user_id']);
                if($user) {
                    $item->email = ($user->email) ? $user->email : null;
                    $item->phone = ($user->phone) ? $user->phone : null;
                    $item->hrm_id = ($user->hrm_id) ? $user->hrm_id : null;
                }else {
                    $item->email = null;
                    $item->hrm_id = null;
                }
            } else {
                $item->email = null;
                $item->hrm_id = null;
            }
        }
        return response()->json($teacher);
    }

 

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response





     */
    public function edit($id)
    {
        $teacher = Teacher::find($id);
        if ($teacher) {
            $term_teacher_branch = TermTeacherBranch::where('teacher_id', $teacher->id)->first();
            $teacher->setAttribute('status', $term_teacher_branch->status);
            $teacher->setAttribute('branch_id', $term_teacher_branch->branch_id);
            $teacher->setAttribute('is_head_teacher', $term_teacher_branch->is_head_teacher);
            
            $metaData = (array)json_decode( $teacher->meta_data);
            if( isset($metaData['user_id']) ) {
                $user = User::find($metaData['user_id']);
                if($user) {
                    $teacher->setAttribute('email',$user->email);
                    $teacher->setAttribute('hrm_id',$user->hrm_id);
                    $teacher->setAttribute('phone',$user->phone);
                }else {
                    $teacher->setAttribute('email',NULL);
                    $teacher->setAttribute('hrm_id',NULL);
                }
            } else {
                $teacher->setAttribute('email',NULL);
                $teacher->setAttribute('hrm_id',NULL);
            }
            return response()->json($teacher);
        }
        return response()->json();
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
        $request->validate([
            'ins_name' => 'required|max:50'
        ]);
        
        $teacher = Teacher::find($id);
        $teacher->ins_name = trim($request->ins_name);
        $teacher->updated_at = date('Y:m:d H:i:s');
        $teacher->save();
        
        $metaData = (array)json_decode($teacher->meta_data);
        if( isset($metaData['user_id']) ) {
            $user = User::find($metaData['user_id']);
            if($user) {
                $user->username = explode('@', $request->email)[0];
                $user->full_name = $request->ins_name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->hrm_id = $request->hrm_id;
                $user->status = $request->status;
                $user->save();
                DB::table('term_user_branch')->where('user_id', $user->id)->update([
                    'branch_id' => $request->branch_id,
                    'status' => $request->status,
                    'role_id' =>$request->is_head_teacher ==1 ? 37 : 36,
                ]);
            }
        }
        
        $term_teacher_branch = TermTeacherBranch::where('teacher_id', $teacher->id)->first();
        $term_teacher_branch->branch_id = $request->branch_id;
        $term_teacher_branch->status = $request->status;
        $term_teacher_branch->updated_at = date('Y:m:d H:i:s');
        $term_teacher_branch->is_head_teacher = $request->is_head_teacher;
        $term_teacher_branch->save();

        $lsmAPI = new LMSAPIController();
        if($teacher->call_lms){
            $lsmAPI->updateTeacherLMS($teacher->id);
        }else{
            $lsmAPI->createTeacherLMS($teacher->id);
        }
        return response()->success('Cập nhật thành công!');
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
        $teacher = Teacher::find($id);
        if (!$teacher) return response()->json();
        else{
            DB::table('term_teacher_branch')->where('teacher_id',$id)->delete();
            DB::table('sessions')->where('teacher_id',$id)->delete();
            return response()->json("delete success!");
        }
    }

    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode("," , $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("teachers");

        $p = DB::table('teachers');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }


        return $p->paginate($pageSize);
    }
}

    public function getTeacherByClass($id){
        $teachers = DB::table('teachers')
                        ->select('teachers.*')
                        ->Join('sessions', 'sessions.teacher_id', 'teachers.id')
                        ->Join('classes', 'classes.id', 'sessions.class_id')
                        ->where('classes.id' , $id)
                        ->groupBy('teachers.id')->get();
        return response()->json($teachers);
    }

    public function getTeachersByBranch($id)
    {
        $listTeachers = Teacher::getTeachersByBranch($id);
        return response()->json($listTeachers);
    }

    public function getTeacherListByBranch(Request $request,$branch_id)
    {
        $q = "SELECT t.*,term.status FROM teachers AS t 
                LEFT JOIN term_teacher_branch as term ON t.id = term.teacher_id
                LEFT JOIN branches as br ON br.id = term.branch_id
                WHERE br.id = $branch_id AND term.status=1
            ";

        $list = DB::select(DB::raw($q));
        if($request->status!=NULL){
            foreach ($list AS $k=> $row){
                $meta_data = json_decode($row->meta_data,true);
                $user_info = DB::table('users')->where('id',$meta_data['user_id'])->first();
                if($user_info && $user_info->status!=$request->status){
                    unset($list[$k]);
                }
            }
        }
        else{
            $listNew = [];
            foreach ($list AS $row){
                if ($row->status  == 1){
                    $listNew[] = $row;
                }
            }
            $list  = $listNew;
        }

        return response()->json($list);
    }

}
