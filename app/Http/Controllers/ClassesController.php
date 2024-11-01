<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Classes;
use App\Models\Contract;
use App\Models\LmsClass;
use App\Models\Response;
use App\Models\TermStudentUser;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null ? 5 : $request->pageSize;
        $classes = LmsClass::paginate($pageSize);
        foreach ($classes as $class) {
            # code...
            // return $class->id;
            // return total student
            $p = DB::table('students')->Join('contracts', 'contracts.student_id', 'students.id')->where('contracts.class_id', $class->id)->count();
            $class->totalStudent = $p;

            //return teacher in class
            $teacher = DB::table('teachers')->Join('sessions', 'sessions.teacher_id', 'teachers.ins_id')->where('sessions.class_id', $class->id)->groupBy('teachers.ins_id')->get();
            // return $teacher;
            $class->teachers = $teacher;

            //return cm of class
            $currentCm = DB::select(DB::raw("SELECT *FROM term_user_class where ((start_date <= CURDATE() and CURDATE()<= end_date) OR (end_date IS NULL and start_date<= CURDATE())) AND class_id = $class->id AND status = 1"));
            if ($currentCm){
                $class->cm_id = $currentCm[0]->user_id;
            }else{
                $class->cm_id = null;
            }
            // return branch of class
            if ($class->brch_id){
                $branch = DB::table('branches')->where('branches.id', $class->brch_id)->get();
                $class->branch_name = $branch[0]->name;
            }
            else{
                $class->branch_name = null;
            }

            //return program of class
            if ($class->program_id){
                $program = DB::table('programs')->where('programs.id', $class->program_id)->get();
                $class->program_name = $program[0]->name;
            }
            else{
                $class->program_name = null;
            }

            //return products of class
            if ($class->product_id){
                $product = DB::table('products')->where('products.id', $class->product_id)->get();
                $class->product_name = $product[0]->name;
            }
            else{
                $class->product_name = null;
            }
        }
        return response()->json($classes);
    }

    public function update(Request $request, $id){
        $classes = LmsClass::find($id);
        $classes->cm_id = $request->cm_id;
        $classes->save();
        return response()->json($classes);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $class = DB::table('classes as c')
        //                 ->leftJoin('users as cm', 'cm.id', '=', 'c.cm_id')
        //                 ->select('c.*',
        //                         'cm.full_name as cm_name',
        //                         'cm.username as cm_username')
        //                 ->where('c.id', $id)
        //                 ->first();

        $query = "SELECT tuc.*,
                        c.*,
                        cm.full_name as cm_name,
                        cm.username as cm_username
                    FROM term_user_class as tuc
                    LEFT JOIN classes as c ON tuc.class_id = c.id
                    LEFT JOIN users as cm  ON cm.id = c.cm_id
                    WHERE ((tuc.start_date <= CURDATE()
                    AND CURDATE()<= tuc.end_date)
                    OR (tuc.end_date IS NULL
                    AND tuc.start_date<= CURDATE()))
                    AND tuc.class_id = $id
                    AND tuc.status = 1";
        $currentCm = DB::select(DB::raw($query));

        // $qr = "select count(contracts) as result from contracts where class_id = $id";

        // $total = DB::select(DB::raw($qr));



        // $qr = "SELECT COUNT(*) from contracts where contracts.class_id = $id";
        //  $total = DB::select(DB::raw($qr));
         // $currentCm[0]->total_student = $total;

        // $total = "SELECT count(DISTINCT c.id) as total from contracts as e where c.class_id = $id ";

        // $current_total = DB::select(DB::raw($currentCm));

        // $currentCm[0]->current_total = $current_total[0];

        // $currentCm = DB::select(DB::raw("SELECT *
  //                                       FROM term_user_class
  //                                       WHERE ((start_date <= CURDATE()
  //                                       AND CURDATE()<= end_date)
  //                                       OR (end_date IS NULL
  //                                       AND start_date<= CURDATE()))
  //                                       AND class_id = $id
  //                                       AND status = 1"));
        // if ($currentCm){
        //     $class->cm_id = $currentCm[0]->user_id;

        // }else{
        //     $class->cm_id = null;
        // }
        // // return $currentCm;
        // $class->current_cm = $currentCm;

        return response()->json($currentCm);

    }

    public function getStudentByClass($class_id, Request $request){
       $p = DB::select(DB::raw("select * FROM students WHERE students.id IN (SELECT st.student_id FROM contracts AS st WHERE st.class_id = $class_id)"));
        // return $p;;
        foreach ($p as $st) {
            # code...
            $contract = DB::table('contracts')->where('contracts.student_id', $st->id)->where('class_id', $class_id)->get();
            $st->contract = $contract;
            if ($contract[0]->tuition_fee_id){
                $tuition = DB::table('tuition_fee')->where('tuition_fee.id', $contract[0]->tuition_fee_id)->get();
            $st->tuition_fee = $tuition[0]->name;
            }
            else{
                $st->tuition_fee = null;
            }
        }
        $paginated = new Paginator($p, count($p), 5, 1, [
             'path'  => $request->url(),
             'query' => $request->query()
           ]);
        return $paginated;
    }
    public function getEnrolmentByClass(Request $request, $class_id)
    {
        // $contract_start_date = $request->contract_start_date;
        // $class_start_date = $request->contract_start_date;
        // $count_student = "SELECT
        //         COUNT(id) AS students
        //         FROM
        //             `contracts` AS c
        //         WHERE
        //             (
        //                 c.enrolment_end_date >= '2018-03-01'
        //                 AND c.enrolment_end_date <= '2018-04-30'
        //             )
        //         OR (
        //             c.enrolment_start_date <= '2018-04-30'
        //             AND c.enrolment_end_date >= '2018-04-30'
        //         )
        //         AND class_id = $class_id";
        // $total_student = "SELECT count(class.id) as students from classes where classes.id = $class_id";

        $query = "SELECT c.id,
                        c.enrolment_start_date AS class_start_date,
                        c.class_id,
                        c.cstd_id,
                        c.status AS student_status,
                        c.product_id,
                        c.type AS contract_type,
                        c.status AS contract_status,
                        c.receivable,
                        c.tuition_fee_id,
                        s.name AS student_name,
                        classes.cls_name AS class_name,
                        classes.cm_id AS class_cm_id,
                        classes.cls_enddate AS class_end_date,
                        cm.full_name AS cm_name,
                        ec.full_name AS ec_name,
                        p.amount AS paid_amount,
                        p.debt,
                        tf.session,
                        tf.name AS tuition_name,
                        tf.price AS tuition_price
                FROM contracts AS c
                    LEFT JOIN students as s
                    ON s.id = c.student_id
                    LEFT JOIN classes
                    ON c.class_id = classes.id
                    LEFT JOIN users AS cm
                    ON cm.id = classes.cm_id
                    LEFT JOIN users AS ec
                    ON ec.id = c.ec_id
                    LEFT JOIN payment AS p
                    ON p.contract_id = c.id
                    LEFT JOIN tuition_fee AS tf
                    ON tf.product_id = classes.product_id
                WHERE c.class_id = $class_id
                GROUP BY c.id";
        $pt = DB::select(DB::raw($query));

        return response()->json($pt);

    }
    public function getClassByProgramBranch(Request $request,$program_id,$branch_id){
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = [];
            $code = APICode::SUCCESS;
            $data = u::query("SELECT *,(SELECT cjrn_classdate FROM schedules WHERE status=1 AND cjrn_classdate > CURRENT_DATE LIMIT 1) AS last_cjrn_class_date ,
            (SELECT count(DISTINCT student_id) FROM contracts WHERE class_id = cls.id AND `status`!=7) curr_student
                FROM classes AS cls WHERE  cls.branch_id=$branch_id AND cls.program_id=$program_id AND cls.cls_iscancelled ='no'");
        }
      return $response->formatResponse($code, $data);

    }
    public function search(Request $request){
        $field = explode("," , $request->field);
        $keyword = explode(",", $request->keyword);
        $pageSize = $request->pageSize;
        // dd($field);
        $column = DB::getSchemaBuilder()->getColumnListing("classes");

        $p = DB::table('classes');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%'.$keyword[0].'%');
        for ($i=1; $i<count($field); $i++){
            if (in_array($field[$i], $column)){
                $p->Where($field[$i], 'like', '%'.$keyword[$i].'%');
            }
        }
        return $p->get();
    }

    public function updateCm(Request $request)
    {
        $code = APICode::SUCCESS;
        $data = null;
        $response = new Response();
        //
        $cmID = trim($request->cm_id);
        $classID = trim($request->class_id);
        //
        $classes = LmsClass::find($classID);
        LmsClass::updateCm($classID, $cmID);
        TermStudentUser::updateCmByClass($classID, $cmID);
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

        return $response->formatResponse($code, $data);
    }
    // Get class info by contractId
    public function getClassByContract(Request $request){

        $contractId = $request->input('conId');
        // dd($contractId);
        $data = null;
        $code = 404;
        try{
            if(!$contractId) return response()->json(['message'=>'Dữ liệu không tồn tại','data'=>null],404);

            $data = Classes::byContract($contractId)->first();
            if($data){
                switch ($data->clse_day) {
                    case 1:
                        $data->day_time = 'Thứ 2: '.$data->clse_shift_start.' - '.$data->clse_shift_end;
                        break;
                    case 2:
                        $data->day_time = 'Thứ 3: '.$data->clse_shift_start.' - '.$data->clse_shift_end;
                        break;
                    case 3:
                        $data->day_time = 'Thứ 4: '.$data->clse_shift_start.' - '.$data->clse_shift_end;
                        break;
                    case 4:
                        $data->day_time = 'Thứ 5: '.$data->clse_shift_start.' - '.$data->clse_shift_end;
                        break;
                    case 5:
                        $data->day_time = 'Thứ 6: '.$data->clse_shift_start.' - '.$data->clse_shift_end;
                        break;
                    case 6:
                        $data->day_time = 'Thứ 7: '.$data->clse_shift_start.' - '.$data->clse_shift_end;
                        break;
                    case 0:
                        $data->day_time = 'Chủ nhật: '.$data->clse_shift_start.' - '.$data->clse_shift_end;
                        break;
                    default:
                        $data->day_time = '';
                        break;
                }
            }
            // dd($data);
            $code = 200;

        }catch(Exception $e){}

        // dd($data);
        return response()->json(['data'=>$data],$code);
    }

    public function getNearestSchoolDayForContract($classId, $contractId)
    {
        $contract = Contract::where('id', '=', $contractId)->first();
        if(!empty($contract) && !empty($classId)) {
            $r = u::first("SELECT cjrn_classdate as nearest_school_day FROM schedules 
                             WHERE class_id = $classId 
                                 AND status = 1 
                                 AND cjrn_classdate >= CURRENT_DATE() 
                                --  AND cjrn_classdate >= '{$contract->enrolment_start_date}'
                             ORDER BY cjrn_classdate ASC LIMIT 1");
            $code = APICode::SUCCESS;
        }else{
            $code = APICode::WRONG_PARAMS;
            $r = null;
        }
        $res = new Response();
        return $res->formatResponse($code, $r);
    }

    public function getNearestSchoolDayForContracts($contractIds){
        if (empty($contractIds)){
            $res = null;
            $code = APICode::WRONG_PARAMS;
        }else {
            $query = "SELECT class_id, enrolment_start_date FROM contracts WHERE id IN ($contractIds)";
            $r = u::query($query);

            $subQuery = "";
            foreach ($r as $key => $contract) {
                if(empty($contract->enrolment_start_date))  continue;

                $subQuery .= " (class_id = $contract->class_id AND cjrn_classdate >= '{$contract->enrolment_start_date}') ";
                if ($key < count($r) - 1) {
                    $subQuery .= " OR ";
                }
            }
            $query = "SELECT cjrn_classdate as nearest_school_day FROM schedules WHERE status = 1";
            if(empty($subQuery)){
                $res = null;
            }else{
                $query.=  " AND ($subQuery) AND cjrn_classdate >= CURRENT_DATE() ORDER BY cjrn_classdate ASC LIMIT 1";
                $res = u::first($query);
            }
            $code = APICode::SUCCESS;
        }
        $response = new Response();
        return $response->formatResponse($code, $res);
    }
    public function getAllDataLevel(Request $request){
        $cond = $request->product_id ? " AND product_id=$request->product_id" : "";
        $r = u::query("SELECT * FROM level WHERE status=1 $cond");
        $code = APICode::SUCCESS;
        $res = new Response();
        return $res->formatResponse($code, $r);
    }
}
