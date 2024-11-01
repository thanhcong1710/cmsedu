<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Models\APICode;
use App\Models\Response;
use App\Models\Branch;
// use App\Http\Controllers\EffectAPIController;
use App\Providers\UtilityServiceProvider as u;
use Mockery\Exception;

class ManagerTransfersController extends Controller
{
    public function getEcCmList(Request $request, $branch)
    {
    	$data = [];


    	$q = "SELECT u.id AS ec_id,
                        u.username AS ec_name
    			FROM users AS u
    			LEFT JOIN term_user_branch AS term ON term.user_id = u.id
    			LEFT JOIN branches AS br ON br.id = term.branch_id
    			LEFT JOIN roles AS rl ON rl.id = term.role_id
    			WHERE br.id = $branch AND term.role_id IN (68,69)

    		";
    	$ecs = DB::select(DB::raw($q));

    	$query = "SELECT u.id AS cm_id, u.username AS cm_name
    			FROM users AS u
    			LEFT JOIN term_user_branch AS term ON term.user_id = u.id
    			LEFT JOIN branches AS br ON br.id = term.branch_id
    			LEFT JOIN roles AS rl ON rl.id = term.role_id
    			WHERE br.id = $branch AND term.role_id IN (55, 56)

    		";
    	$cms = DB::select(DB::raw($query));

    	$data['ecs'] = $ecs;
    	$data['cms'] = $cms;

    	return response()->json($data);
    }

    public function getNewEcList(Request $request, $branch, $ec)
    {
    	$branch_id = $branch;
        $ec_id = $ec;


    	$q = "SELECT u.id AS ec_id, u.username AS ec_name
                FROM users AS u
                LEFT JOIN term_user_branch AS term ON term.user_id = u.id
                LEFT JOIN branches AS br ON br.id = term.branch_id
                LEFT JOIN roles AS rl ON rl.id = term.role_id
                WHERE br.id = $branch AND u.status > 0 AND rl.id in (68,69) AND u.id <> $ec_id

            ";
    	$ecs = DB::select(DB::raw($q));


    	return response()->json($ecs);
    }


    public function getNewManagerList(Request $request){
        // $session = $request->users_data;
        // dd($request->all());
        // $branch_id = $request->branch_id;
        // dd($branch_id);

        $q = "SELECT u.id AS ec_id, u.username AS ec_name
                FROM users AS u
                LEFT JOIN term_user_branch AS term ON term.user_id = u.id
                LEFT JOIN branches AS br ON br.id = term.branch_id
                LEFT JOIN roles AS rl ON rl.id = term.role_id
                WHERE br.id = $branch AND u.status > 0 AND rl.id = 68 AND u.id <> $ec_id

            ";
        $ecs = DB::select(DB::raw($q));


        return response()->json($ecs);
    }
    public function getNewEcCmList(Request $request, $branch){
        $data = [];

        $q = "SELECT u.id AS ec_id,
                        u.username AS ec_name
                FROM users AS u
                LEFT JOIN term_user_branch AS term ON term.user_id = u.id
                LEFT JOIN branches AS br ON br.id = term.branch_id
                LEFT JOIN roles AS rl ON rl.id = term.role_id
                WHERE br.id = $branch AND u.status > 0 AND term.role_id IN (68,69)

            ";
        $ecs = DB::select(DB::raw($q));

        $query = "SELECT u.id AS cm_id, u.username AS cm_name
                FROM users AS u
                LEFT JOIN term_user_branch AS term ON term.user_id = u.id
                LEFT JOIN branches AS br ON br.id = term.branch_id
                LEFT JOIN roles AS rl ON rl.id = term.role_id
                WHERE br.id = $branch AND u.status > 0 AND u.status > 0 AND term.role_id IN (55, 56)

            ";
        $cms = DB::select(DB::raw($query));

        $data['ecs'] = $ecs;
        $data['cms'] = $cms;

        return response()->json($data);
    }

    public function getNewCmList(Request $request, $branch, $cm)
    {
        $branch_id = $branch;
        $cm_id = $cm;


        $q = "SELECT u.id AS cm_id, u.username AS cm_name
                FROM users AS u
                LEFT JOIN term_user_branch AS term ON term.user_id = u.id
                LEFT JOIN branches AS br ON br.id = term.branch_id
                LEFT JOIN roles AS rl ON rl.id = term.role_id
                WHERE br.id = $branch AND u.status > 0 AND rl.id in (55,56) AND u.id <> $cm_id

            ";
        $cms = DB::select(DB::raw($q));


        return response()->json($cms);
    }

    public function getStudentListByUser(Request $request)
    {
        // dd($request);
        $user_data = $request->users_data;
        $user_role = $user_data->role_id;

        $where_ec = null;
        $where_cm = null;
        $where_lms = null;
        $where_effect = null;

        $branch_id = $request->branch_id;
        $ec_id = $request->ec_id;
        $lms_id = $request->lms_id;
        $accounting_id = $request->accounting_id;
        $cm_id = $request->cm_id;
        // dd($branch_id);
        if($ec_id){
            $where_ec .= "AND ts.ec_id = $ec_id";
        }
        if($lms_id){
            $where_lms .= "AND (s.cms_id = '$lms_id' OR s.crm_id = '$lms_id') ";
        }
        if($accounting_id){
            $where_effect .= "AND s.accounting_id like '%$accounting_id%' ";
        }

        if($cm_id){
            $where_cm .= "AND ts.cm_id = $cm_id";
        }
        $where_branch = null;

        if($user_role < 7777777){
            $where_branch .= "AND ts.branch_id = $branch_id";
        }

        $q = "SELECT s.id as student_id,
                    s.cms_id,
                    s.crm_id,
                    s.accounting_id,
                    s.name as student_name,
                    s.status,
                    s.type as student_type,
                    ec.username as ec_name,
                    cm.username as cm_name
                FROM students as s
                LEFT JOIN term_student_user as ts ON ts.student_id = s.id
                LEFT JOIN users as ec ON ec.id = ts.ec_id
                LEFT JOIN users as cm ON cm.id = ts.cm_id
                WHERE ts.branch_id > 0 $where_branch
                $where_ec $where_cm $where_lms $where_effect
                GROUP BY s.id
            ";

        $students = DB::select(DB::raw($q));

        return response()->json($students);
    }

    public function transferManager(Request $request)
    {
      $userData = $request->users_data;
        // dd($request->all());
      $oldCM = $request->cm_id;
      $oldEC = $request->ec_id;
      $newCM = $request->new_cm_id;
      $newEC = $request->new_ec_id;

      $students = $request->student_ids;

      if( ( $oldCM AND $newCM ) OR ( $oldEC AND $newEC ) ) {
        $success = [];
        $studentlist = [];
        foreach( $students as $stID ) {
          try {
            $dataUpdate = $logMT = [];
            $content = '';
            $student = DB::table('students')->join('branches', 'students.branch_id', '=', 'branches.id')->select('students.crm_id', 'branches.hrm_id')->where('students.id',$stID)->first();
            $stRecord = DB::table('term_student_user')->where('student_id',$stID)->orderBy('id',SORT_DESC)->first();
            // Log Manager Transfer
            $logMT = [
              'from_branch_id' => $stRecord->branch_id,
              'to_branch_id' => $stRecord->branch_id,
              'student_id' => $stID,
              'updated_by' => $userData->id,
              'note' => 'Từ chuyển đổi người quản lý',
              'date_transfer' => date('Y-m-d'),
              'created_at' => date('Y-m-d'),
              'updated_at' => date('Y-m-d'),
            ];

            if($oldCM AND $newCM) {
              $newECRecord  = DB::table('users')->where('id',$newCM)->select(['superior_id'])->first();
              $IDSuper = null;
              if($newECRecord->superior_id) {
                $superID = DB::table('users')->where('hrm_id',$newECRecord->superior_id)->select(['id'])->first();
                $IDSuper = $superID->id;
                $logMT['from_om_id'] = $stRecord->om_id;
                $logMT['to_cm_id'] = $IDSuper;
              }
              $dataUpdate['cm_id'] = $newCM;
              $dataUpdate['om_id'] = $IDSuper;
              $content = 'Thay dổi CM, ';
              $changeCM = $newCM;

              //
              $logMT['from_cm_id'] = $stRecord->cm_id;
              $logMT['to_cm_id'] = $newCM;
            }else {
              $changeCM = $stRecord->cm_id;
            }
            if($oldEC AND $newEC) {
              $newECRecord  = DB::table('users')->where('id',$newEC)->select(['superior_id'])->first();
              $IDSuper = null;
              if($newECRecord->superior_id) {
                $superID = DB::table('users')->where('hrm_id',$newECRecord->superior_id)->select(['id', 'hrm_id'])->first();
                $IDSuper = $superID->id;
                $student_obj = (Object)[];
                $student_obj->ma_crm = $student->crm_id;
                $student_obj->crm_bo_phan = $student->hrm_id;
                $student_obj->crm_salesman = $superID->hrm_id;
                $studentlist[]= $student_obj;
              }
              $dataUpdate['ec_leader_id'] = $IDSuper;
              $dataUpdate['ec_id'] = $newEC;
              $content = $content . "Thay đổi EC";

              //
              $logMT['from_ec_id'] = $stRecord->ec_id;
              $logMT['to_ec_id'] = $newEC;

            }
            //
            $dataUpdate['updated_at'] = now();
            DB::table('term_student_user')->where('student_id',$stID)->update($dataUpdate);
            //
            DB::table('log_manager_transfer')->insert($logMT);
            //Create Logs
            $dataLogUpdate = [
              'student_id' => $stID,
              'updated_by' => $request->users_data->id,
              'updated_at' => now(),
              'content'    => $content,
              'status'     => 1,
              'cm_id'      => $changeCM,
              'branch_id'  => $stRecord->branch_id,
              'ceo_branch_id' => $stRecord->ceo_branch_id
            ];
            DB::table('log_student_update')->insert($dataLogUpdate);
            $success['success'][] = $stID;
          } catch (Exception $e) {
            $success['error'][] = $stID;
          }
        }
        // if (count($studentlist)) {
        //     $token = $request->headers->get('Authorization');
        //     // $effectAPI = new EffectAPIController();
        //     $requestAPI = new Request();
        //     $requestAPI->api_url = 'effect/changeSalesForListStudents';
        //     $requestAPI->api_method = 'POST';
        //     $requestAPI->api_params = json_encode($studentlist);
        //     $requestAPI->api_headers = null;
        //     // $resp = $effectAPI->callAPI($requestAPI, $token);
        // }
        return response()->json($success,200);
      } else {
        return response()->json('Missing Parrams',500);
      }
    }
}
