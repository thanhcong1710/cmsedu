<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Session;
use App\Providers\UtilityServiceProvider;
use App\Models\TermStudentUser;
use App\Providers\UtilityServiceProvider as u;
use App\Models\Schedule;
use App\Models\Classes;

class SessionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null ? 5 : $request->pageSize;
        $sessions = Session::paginate($pageSize);
        return response()->json($sessions);
    }


    public function list($page, $search, $filter, Request $request){
        $arr_search = explode(',', $search);
        $arr_filter = explode(',', $filter);
        foreach ($arr_search AS $k=>$row){
            $request->request->add([$row => $arr_filter[$k]]);
        }
        $zones = [];
        $response = [
            'done' => false,
            'message' => 'Invalid Token'
        ];
        $query_information = self::query($request);
        $get_code_query = $query_information['base_query'];
        // return $get_code_query
        $sessions =  DB::select(DB::raw($get_code_query));
        if ($sessions){
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
        $response['sessions'] = $sessions;
        $response['message'] = 'successful';
        return response()->json($response);

    }

    private function query($request, $page = 1, $limit = 20, $filter = NULL){
        $arr_branch = array();
        foreach ($request->users_data->roles_detail AS $role){
            if(!in_array($role->branch_id, $arr_branch)){
                array_push($arr_branch, $role->branch_id);
            }
        }
        $tmp_branch= implode(",",$arr_branch);
        $branch_id = $request->branch_id;
        $teacher_id = $request->teacher_id;
        $shift_name = $request->shift_name;
        $class_day = $request->class_day;
        $status = $request->status;
        $class_name = $request->class_name;
        $where=" 1 ";
        if($branch_id!=''){
            $where.=" AND r.branch_id=".$branch_id;
        }
        if($teacher_id!=''){
            $where.=" AND te.id=".$teacher_id;
        }
        if($shift_name!=''){
            $where.=" AND sh.name LIKE'%$shift_name%'";
        }
        if($class_day!=''){
            $where.=" AND r.class_day=".$class_day;
        }
        if($status!=''){
            $where.=" AND r.status=".$status;
        }
        if($class_name){
            $where.=" AND cl.cls_name LIKE '%$class_name%'";
        }
        if($arr_branch){
            $where.=" AND r.branch_id IN (".$tmp_branch.") ";
        }
        $selected_page = $request->page ?  (int) $request->page: 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $filter = $request->filter ? (int)$request->filter : $filter;
        $query = "SELECT r.*, cl.cls_name as class_name, rm.room_name as room_name, sh.name as shift_name, te.ins_name as teacher_name
                          from sessions as r join classes as cl on r.class_id = cl.id
                                          LEFT join shifts as sh on r.shift_id = sh.id
                                          LEFT join teachers as te on r.teacher_id = te.user_id
                                          LEFT join rooms as rm on r.room_id = rm.id
                        WHERE $where ORDER BY r.status DESC, r.id DESC $limitation";
        $count_query = "SELECT COUNT(r.id) as total FROM sessions AS r JOIN classes as cl on r.class_id = cl.id 
                        LEFT JOIN shifts AS sh ON r.shift_id = sh.id 
                        LEFT join teachers as te on r.teacher_id = te.user_id WHERE $where";
        $total = DB::select(DB::raw($count_query));
        
        $total = $total[0]->total;
        return [
            'base_query' => $query,
            'total' => $total,
            'limit' => $limit,
            'page' => $page
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $branch_info = DB::table('branches')->where('id',$request->branch_id)->first();
        $semester_info = DB::table('semesters')->where('id',$request->semester_id)->first();
        $program_info = DB::table('programs')->where('id',$request->program_id)->first();
        $teacher_info = DB::table('teachers')->where('id',$request->teacher_id)->first();
        $holiday = UtilityServiceProvider::getPublicHolidays(0, $request->branch_id, $semester_info->product_id);
        if($request->status==0){
            $cls_iscancelled = 'no';
        }else{
            $cls_iscancelled = 'yes';
        }
        if(!$request->end_date){
            $tmp_end_date = UtilityServiceProvider::calculatorSessionsByNumberOfSessions($request->start_date,$request->num_session,$holiday,$request->weekdays);
            $request->end_date = $tmp_end_date->end_date;
        }
        $class_id = DB::table('classes')->insertGetId(
            [   'cls_name' => $request->class_name,
                'product_id'=>$semester_info->product_id,
                'program_id'=>$request->program_id,
                'cm_id'=>$request->cm_id,
                'cls_startdate'=>$request->start_date,
                'cls_enddate'=>$request->end_date,
                'cls_iscancelled'=>$cls_iscancelled,
                'max_students'=>$request->max_students,
                'created_at' => now(),
                'updated_at' => now(),
                'branch_id'=>$request->branch_id,
                'semester_id'=>$request->semester_id,
                'teacher_id'=>$teacher_info->user_id,
                'brch_id'=>$branch_info->id,
                'sem_id'=>$semester_info->id,
                'is_trial'=>(int)$request->is_trial,
                'level_id'=>$request->level_id
            ]
        );
        foreach ($request->weekdays AS $class_day){
            $session = new Session();
            $session->class_id = $class_id;
            $session->shift_id = $request->shift_id;
            $session->room_id = $request->room_id;
            $session->teacher_id = $teacher_info->user_id;
            $session->class_day = $class_day;
            $session->start_date = $request->start_date;
            $session->end_date = $request->end_date;
            $session->status = 1;
            $session->created_at = now();
            $session->updated_at = now();
            
            $session->branch_id = $request->branch_id ;
            $session->save();
            
        }
        
        $tmp_class_id = $class_id;
        if(strlen($tmp_class_id)< 3){
            $tmp=$tmp_class_id;
            for($i=strlen($tmp_class_id);$i<$tmp_class_id;$i++){
                $tmp= '0'.$tmp;
            }
            $tmp_class_id=$tmp;
        }
        $arr_date = UtilityServiceProvider::calSessions($request->start_date, $request->end_date,$holiday,$request->weekdays);
        foreach ($arr_date->dates AS $cjrn_classdate){
            DB::table('schedules')->insert(
                [
                    'cjrn_id' => date('ymd',strtotime($cjrn_classdate)).substr($tmp_class_id, -3),
                    'cjrn_classdate' => $cjrn_classdate,
                    'status' => 1,
                    'class_id' => $class_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
                );
        }
        if(isset($request->is_import) && $request->is_import === 1){
            return $class_id;
        }

        if ($class_id > 0){
            $lsmAPI = new LMSAPIController();
            $lsmAPI->createClassLMS($class_id);
            return response()->json(['code'=>0, 'data' =>$class_id]);

        }
        else
            return response()->json(['code'=>1, 'data' =>[]]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $sql = "SELECT s.*,c.semester_id,b.name AS branch_name , c.cls_name AS class_name,c.cm_id,c.max_students,c.cls_iscancelled, t.id AS teacher_id
                FROM sessions AS s LEFT JOIN classes AS c ON s.class_id=c.id
                LEFT JOIN branches AS b ON b.id=s.branch_id
                LEFT JOIN teachers AS t ON t.user_id = s.teacher_id
                WHERE s.id=$id ";
        $data = DB::select(DB::raw($sql));
        $class_id = $data[0]->class_id;
        $sql = "SELECT s.class_day FROM sessions AS s
                WHERE s.class_id=$class_id";
        $result = DB::select(DB::raw($sql));
        $weekdays = array();
        foreach ($result AS $row){
            array_push($weekdays, $row->class_day);
        }
        $data[0]->weekdays = $weekdays;
        return response()->json($data[0]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $session = Session::find($id);
        return response()->json($session);
    }

    private function editScheduleOld($id, $classDay, $branchId, $num_session,$start_date,$session_id, $editClass = false,$weekdays=[],$product_id=0){
        if(empty($weekdays)){
            $holiday = u::getPublicHolidayAll($branchId);
            $delSQL = "DELETE FROM `schedules` WHERE class_id = $id";
            u::query($delSQL);
            $startDate = substr($start_date,0,10);
            $toDate = date('Y-m-d', strtotime($startDate) + 1000 * 24 * 60 * 60);
            $days = u::getDaysBetweenTwoDate($startDate, $toDate, $classDay); # 2x Friday
            $cjrnClassDate = [];
            foreach ($days as $day) {
                if (u::ckPublicHolidayCheck($day, $holiday)) {
                    $cjrnClassDate[] = $day;
                }
            }

            for ($i = 0; $i < $num_session; $i++) {
                if (!empty($cjrnClassDate[$i])) {
                    self::updateSchedules($id, $cjrnClassDate[$i]);
                }
            }
        }else{
            $holiday = u::getPublicHolidays(0, $branchId, $product_id);
            $delSQL = "DELETE FROM `schedules` WHERE class_id = $id";
            u::query($delSQL);
            $startDate = substr($start_date,0,10);
            $session = $num_session;
            $days =u::calculatorSessionsByNumberOfSessions($startDate, $session, $holiday, $weekdays);
            $lastSessionDay = $days->end_date;
            $days = $days->dates;
            foreach($days AS $row){
                self::updateSchedules($id, $row);
            }
        }
        
        self::updateSessionEndDate($session_id, $classDay, $startDate, $lastSessionDay);
        if ($editClass)
            self::updateClassesDate($id,$start_date,$lastSessionDay);
    }

    private function updateClassesDate($id,$start_date,$end_date){
        $class = Classes::find($id);
        if ($start_date){
            $class->cls_startdate = $start_date;
        }
        $class->cls_enddate = $end_date;
        $class->updated_at = NOW();
        $class->save();

        $enrolment_end_date = "UPDATE `contracts` set enrolment_end_date = '$end_date' WHERE class_id = $id";
        u::query($enrolment_end_date);
    }

    private function updateSessionEndDate($sessionId, $class_day, $start_date, $end_date){
        $session = Session::find($sessionId);
        if ($start_date) {
            $session->start_date = $start_date;
        }
        if ($class_day) {
            $session->class_day = $class_day;
        }
        if ($end_date) {
            $session->end_date = $end_date;
        }
        $session->updated_at = NOW();
        $session->save();
    }

    private function updateSchedules($classId, $cjrn_classdate){
        $id = u::generateRandomStringOrNumber(9,true);
        $schedule = new Schedule();
        $schedule->cjrn_id = $id;
        $schedule->cls_id = 0;
        $schedule->cjrn_classdate = $cjrn_classdate;
        $schedule->class_id = $classId;
        $schedule->status = 1;
        $schedule->created_at = date('Y-m-d H:i:s');
        $schedule->updated_at = date('Y-m-d H:i:s');
        $schedule->save();
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
        if($request->status == 0){
            $cls_iscancelled = 'no';
        }else{
            $cls_iscancelled = 'yes';
        }

        if ($cls_iscancelled == 'no') {
            $data = [];
            $sql = "SELECT c.`cls_name`,(SELECT COUNT(cjrn_id) FROM schedules WHERE class_id= $id) AS num_session,
            s.`class_day`,(SELECT COUNT(id) FROM `contracts` WHERE class_id= $id) AS actived_student,
            s.`branch_id`, s.`id` as session_id,s.`start_date`,c.product_id,c.cls_startdate
             FROM classes AS c
             LEFT JOIN `sessions` s 
             ON s.class_id = c.id WHERE c.id = $id";
            $old = DB::select(DB::raw($sql));
            if ($old) {
                $data = $old[0];
            };
            if ($data) {
                $request->start_date= $request->start_date ? $request->start_date :$data->start_date;
                $changeClassDay = false;
                if ($data->class_day != $request->weekdays[0]) {
                    $changeClassDay = true;
                    if ($data->actived_student > 0) {
                        return response()->json(['msg' => 'Lớp học đã có học sinh đang học, không thể thay đổi ngày học.']);
                    }
                    else{
                        $classDay = $request->weekdays[0];
                        $branchId = $data->branch_id;
                        $num_session = $request->num_session;
                        $start_date = $request->start_date;
                        $session_id = $data->session_id;
                        if(count($request->weekdays)==1){
                            self::editScheduleOld($id, $classDay, $branchId, $num_session, $start_date,$session_id, true);
                        }else{
                            self::editScheduleOld($id, $classDay, $branchId, $num_session, $start_date,$session_id, true, $request->weekdays,$data->product_id);
                        }
                    }
                }

                if ($data->actived_student > 0){
                    if ($data->num_session > $request->num_session){
                        return response()->json(['msg' => 'Không thể thay đổi số ngày học nhỏ hơn số hiện tại']);
                    }
                }
                else{
                    if (!$changeClassDay) {
                        if (($data->num_session != $request->num_session) || ($data->start_date != $request->start_date)) {
                            $classDay = $request->weekdays[0];
                            $branchId = $data->branch_id;
                            $num_session = $request->num_session;
                            $start_date = $request->start_date;
                            $session_id = $data->session_id;
                            if(count($request->weekdays)==1){
                                self::editScheduleOld($id, $classDay, $branchId, $num_session, $start_date,$session_id, true);
                            }else{
                                self::editScheduleOld($id, $classDay, $branchId, $num_session, $start_date,$session_id, true,$request->weekdays,$data->product_id);
                            }
                            $changeClassDay = true;
                        }
                    }
                }

                if ($data->num_session < $request->num_session) {
                    if (!$changeClassDay) {
                        if(count($request->weekdays)==1){
                            $holiday = u::getPublicHolidayAll($data->branch_id);
                            $session = ($request->num_session - $data->num_session);
                            $cjrn_query = "SELECT MAX(cjrn_classdate) AS last_date FROM `schedules` WHERE class_id = $id";
                            $schedules = DB::select(DB::raw($cjrn_query));
                            if (!$schedules)
                                return response()->json(['msg' => 'Schedules lớp học không tồn tại']);
                            $startDate = $schedules[0]->last_date;
                            $toDate = date('Y-m-d', strtotime($startDate) + 1000 * 24 * 60 * 60);
                            $days = u::getDaysBetweenTwoDate($startDate, $toDate, $data->class_day); # 2x Friday
                            
                            $cjrnClassDate = [];
                            foreach ($days as $day) {
                                if (u::ckPublicHolidayCheck($day, $holiday)) {
                                    $cjrnClassDate[] = $day;
                                }
                            }
                            for ($i = 0; $i < $session; $i++) {
                                if (!empty($cjrnClassDate[$i])) {
                                    self::updateSchedules($id, $cjrnClassDate[$i]);
                                }
                            }
                            $lastSessionDay = !empty($cjrnClassDate[$session]) ? $cjrnClassDate[$session] : null;
                            self::updateSessionEndDate($data->session_id, null, null, $lastSessionDay);
                            self::updateClassesDate($id,null,$lastSessionDay);
                        }else{
                            $holiday = u::getPublicHolidays(0, $data->branch_id, $data->product_id);
                            $cjrn_query = "SELECT MAX(cjrn_classdate) AS last_date FROM `schedules` WHERE class_id = $id";
                            $schedules = DB::select(DB::raw($cjrn_query));
                            if (!$schedules)
                                return response()->json(['msg' => 'Schedules lớp học không tồn tại']);
                            $session = ($request->num_session - $data->num_session);
                            $startDate = $schedules[0]->last_date ? $schedules[0]->last_date : date('Y-m-d',strtotime($data->cls_startdate));
                            $days =u::calculatorSessionsByNumberOfSessions($startDate, $session, $holiday, $request->weekdays);
                            $lastSessionDay = $days->end_date;
                            $days = $days->dates;
                            foreach($days AS $row){
                                self::updateSchedules($id, $row);
                            }
                            self::updateSessionEndDate($data->session_id, null, null, $lastSessionDay);
                            self::updateClassesDate($id,null,$lastSessionDay);
                        }
                        
                    }
                }
            }
        }else{
            $sql = "SELECT (SELECT COUNT(id) FROM `contracts` WHERE class_id= $id AND `status`!=7) AS actived_student
             FROM classes AS c WHERE c.id = $id";
            $data_iscancell = u::first($sql);
            if ($data_iscancell && $data_iscancell->actived_student > 0) {
                return response()->json(['msg' => 'Lớp học có học sinh active trong lớp, không thể chuyển trạng thái ngưng hoạt động.']);
            }
        }

        $teacher_info = DB::table('teachers')->where('id',$request->teacher_id)->first();
        if($request->is_class==1){
            $cmID = trim($request->cm_id);
            $classID = trim($request->class_id);
            $classes = DB::table('classes')->where('id',$classID)->first();
            if($classes && $classes->cm_id!=$request->cm_id){
                TermStudentUser::updateCmByClass($classID, $cmID,$request->teacher_id);
                //Update Term Use Class
                $dataInsert = [
                    'class_id' => $classID,
                    'user_id' => $cmID,
                    'start_date' => now(),
                    'status'  => 1
                ];
                DB::table('term_user_class')->insert($dataInsert);
                //Dissable old
                DB::table('term_user_class')->where('class_id', $classID)
                ->where('user_id',$classes->cm_id)->update([
                    'end_date' => now(),
                    'status' => 0
                ]);
            }elseif($classes){
                TermStudentUser::updateCmByClass($classID, $cmID,$request->teacher_id);
            }
            DB::table('sessions')->where('class_id', $request->class_id)
            ->update(
                [   'shift_id' => $request->shift_id,
                    'room_id'=>$request->room_id,
                    'teacher_id'=>$teacher_info->user_id,
                    'updated_at' => now(),
                ]
                );
            $class_info = u::first("SELECT teacher_id, id_lms FROM classes WHERE id=$request->class_id");
            DB::table('classes')->where('id', $request->class_id)
            ->update(
                [   'cls_name' => $request->class_name,
                    'cm_id'=>$request->cm_id,
                    'cls_iscancelled'=>$cls_iscancelled,
                    'max_students'=>$request->max_students,
                    'updated_at' => now(),
                    'teacher_id'=>$teacher_info->user_id,
                    'is_trial'=>(int)$request->is_trial,
                    'level_id'=>$request->level_id
                ]
                );
            $change_data_class = u::first("SELECT * FROM classes WHERE id=$request->class_id");
            DB::table('log_update_class')->insert([
                'class_id'=>$request->class_id,
                'creator_id'=>$request->users_data->id,
                'created_at'=>now(),
                'meta_data'=>json_encode($classes),
                'change_data'=>json_encode($change_data_class)
            ]);
            $lsmAPI = new LMSAPIController();
            $pre_teacher =$teacher_info->user_id!=$class_info->teacher_id?$class_info->teacher_id:0;
            if($class_info->id_lms){
                $lsmAPI->updateClassLMS($request->class_id,$pre_teacher);
            }else{
                $lsmAPI->createClassLMS($request->class_id);
            }
            return response()->json(['msg' =>'Cập nhật thông tin lớp học thành công']);
            //return response()->json([1]);
        }else{
            $session = Session::find($id);
            $session->shift_id = $request->shift_id;
            $session->room_id = $request->room_id;
            $session->teacher_id = $teacher_info->user_id;
            $session->status = 1;
            $session->updated_at = now();
            
            $session->save();
            return response()->json($session);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $session = Session::find($id);
        $session->delete();
        return response()->json('Successfully Deleted');
    }
    public function search(Request $request){
        $field = explode("," ,$request->field);
        $keyword = explode("," ,$request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("sessions");

        $p = DB::table('sessions');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->orWhere($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->paginate($pageSize);
    }

    public function addSchedule(Request $request){
        $list_class= $request->class_id;
        $arr_list = explode(",",$list_class);
        $session = 48;
        foreach($arr_list AS $id){
            $id = (int)$id;
            $cjrn_classdate = u::first("SELECT cjrn_classdate FROM schedules WHERE class_id=$id ORDER BY cjrn_classdate DESC LIMIT 1");
            $class_info = u::first("SELECT branch_id, product_id FROM classes WHERE id=$id");
            if($class_info && $cjrn_classdate){
                
                $holiday = u::getPublicHolidays(0, $class_info->branch_id, $class_info->product_id);
                $startDate = date('Y-m-d',strtotime($cjrn_classdate->cjrn_classdate)+24*3600);
                $weekdays = u::getClassDays($id);
                $days =u::calculatorSessionsByNumberOfSessions($startDate, $session, $holiday, $weekdays);
                $days = $days->dates;
                foreach($days AS $row){
                    self::updateSchedules($id, $row);
                }
            }
            
        }
        return "ok";
    }

    public function addScheduleTool($list_class){
        $arr_list = explode(",",$list_class);
        $session = 48;
        foreach($arr_list AS $id){
            $id = (int)$id;
            $cjrn_classdate = u::first("SELECT cjrn_classdate FROM schedules WHERE class_id=$id ORDER BY cjrn_classdate DESC LIMIT 1");
            $class_info = u::first("SELECT branch_id, product_id,cls_startdate FROM classes WHERE id=$id");
            if($class_info ){
                $holiday = u::getPublicHolidays(0, $class_info->branch_id, $class_info->product_id);
                if($cjrn_classdate){
                    $startDate = date('Y-m-d',strtotime($cjrn_classdate->cjrn_classdate)+24*3600);
                }else{
                    $startDate = date('Y-m-d',strtotime($class_info->cls_startdate));
                }
                $weekdays = u::getClassDays($id);
                $days =u::calculatorSessionsByNumberOfSessions($startDate, $session, $holiday, $weekdays);
                $days = $days->dates;
                foreach($days AS $row){
                    self::updateSchedules($id, $row);
                }
            }
            echo $id."/";
        }
        return "ok";
    }
}
