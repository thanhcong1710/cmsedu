<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Program;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;
class ProgramsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function showChild(){
        $programs = Program::all();
        $data = json_decode($programs);
        // $data->children =[];

    }
    public function programByChild(Request $request, $branch_id = null, $semester_id = null){
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $code = APICode::SUCCESS;
            $branches = $branch_id ? $branch_id : $session->branches_ids;
            $query = "SELECT * FROM (SELECT p.*, t.product_id, IF(s.end_date < CURDATE(), true, false) obsolete FROM programs p LEFT JOIN semesters s ON p.semester_id = s.id LEFT JOIN term_program_product t ON t.program_id = p.id WHERE p.id IN (SELECT parent_id FROM programs WHERE branch_id IN ($branches) AND semester_id = $semester_id)
                UNION ALL
                SELECT p.*, t.product_id, IF(s.end_date < CURDATE(), true, false) obsolete FROM programs p LEFT JOIN semesters s ON p.semester_id = s.id LEFT JOIN term_program_product t ON t.program_id = p.id WHERE p.branch_id IN ($branches) AND p.semester_id = $semester_id 
                ORDER BY level_name ASC, name ASC) x GROUP BY id ORDER BY level_name ASC, name ASC";
            $programs = u::query($query);
            if (count($programs)) {
                $data = [];
                foreach ($programs as $key) {
                    if ($key->parent_id == 0){
                        $key->children = [];
                        $key->icon = 'fa fa-folder';
                        foreach ($programs as $item) {
                            if ($key->id == $item->parent_id){
                                $key->icon = 'fa fa-folder-open';
                                $key->opened = true;
                                $key->children[] = $item;
                            }
                        }
                        $data[] = $key;
                    } else {
                        if ((int)$key->product_id == 0) {
                            $key->icon = 'fa fa-edit';
                        } else {
                            $key->icon = 'fa fa-book';
                        }
                    }
                }
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null?5: $request->pageSize;
        $programs = Program::paginate($pageSize);
        return response()->json($programs);
    }

    public function getTerm(Request $request, $program_id){
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $code = APICode::SUCCESS;
            $data = (object)[];
            $term = u::first("SELECT * FROM term_program_product WHERE program_id = $program_id");
            $data = $term;
            if ($term) {
                $data->program_codes = DB::table('program_codes')->where('product_id', $term->product_id)->get();
            }
        }
        return $response->formatResponse($code, $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $program = new Program();

        $program->program_id = $request->program_id;
        $program->name = $request->name;
        $program->product_id = $request->product_id;
        $program->created_date = date('Y-m-d H:i:s');
        $program->updated_date = date('Y-m-d H:i:s');
        $program->status = $request->status;
        $program->parent_id = $request->parent_id;
        $program->save();

        return response()->json($program);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $program = Program::findOrFail($id);
        return response()->json([$program, $program->classes]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $program = Program::findOrFail($id);
        return response()->json($program);
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
        $program = Program::findOrFail($id);

        $program->program_id = $request->program_id;
        $program->name = $request->name;
        $program->product_id = $request->product_id;
        $program->created_date = date('Y-m-d H:i:s');
        $program->updated_date = date('Y-m-d H:i:s');
        $program->status = $request->status;
        $program->parent_id = $request->parent_id;
        $program->save();

        return response()->json($program);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $program = Program::findOrFail($id);
        $program->delete();
        $program->products()->detach();
        return response()->json('Successfully Deleted');
    }
    public function getContractByProgram(Request $request, $program_id)
    {
        $check_exist = "SELECT IF(COUNT(e.id) > 0, 1,0) AS is_existed
                            FROM `contracts` AS c
                            LEFT JOIN students AS s ON c.student_id = s.id
                        WHERE
                            c.enrolment_start_date BETWEEN '2018-03-01' AND '2018-03-30'
                            AND class_id = 3
                            AND s.`nick` = 'Robin'";

        $query = "SELECT contracts.id
                    AS contract_id
                    FROM contracts";
        $rs_query = DB::select(DB::raw($query));
        // return ($rs);

        $pageSize = $request->pageSize == null?5: $request->pageSize;

        $rs = DB::table('contracts')
                        ->where('contracts.program_id', $program_id)
                        ->where('payment.debt','<', 'contracts.must_charge')
                        ->orWhere('students.type', 1)
                        ->orWhere('contracts.type', 0)
                        ->leftJoin('students', 'contracts.student_id', '=', 'students.id')
                        ->leftJoin('users as ec', 'ec.id', '=', 'contracts.ec_id')
                        ->leftJoin('users as cm', 'cm.id', '=', 'contracts.cm_id')
                        ->leftJoin('payment', 'payment.contract_id', 'contracts.id')
                        ->select('contracts.*', 'payment.amount')
                        ->groupBy('contracts.id')
                        ->orderBy('payment.debt')
                        ->orderBy('students.type')
                        ->paginate($pageSize);
         // return response()->json($rs);

            foreach ($rs as $contract) {
                $student = DB::table('students')->where('students.id',$contract->student_id)->get();
                // $ec = DB::table('users')->where('users.id', '=', $contract->ec_id)->get();
                // $cm = DB::table('users')->where('users.id', '=', $contract->cm_id)->get();
                $payment = DB::table('payment')->where('payment.contract_id', $contract->id)->get();
                if ($contract->tuition_fee_id){
                    $tuiti = DB::table('tuition_fee')->where('tuition_fee.id', $contract->tuition_fee_id)->get();
                    $contract->tuition_name = $tuiti[0]->name;
                    $contract->tuition_price = $tuiti[0]->price;
                    $contract->tuition_session = $tuiti[0]->session;
                }else{
                    $contract->tuition_name = "0";
                }
                if ($contract->ec_id){
                    $ec = DB::table('users')->where('users.id', $contract->ec_id)->get();
                    $contract->ec_username = $ec[0]->username;
                }
                else{
                  $contract->ec_username = "";
                }

                if ($contract->cm_id){
                    $cm = DB::table('users')->where('users.id', $contract->cm_id)->get();
                    $contract->cm_username = $cm[0]->username;
                }
                else{
                    $contract->cm_username = "";
                }
                $contract->student = $student;
            }

        return response()->json($rs);
    }

    public function showAll($branch = null, $product = null){

        $branches = $branch;
        $products = $product;
  
        $where = null;
        if($branches){
            $where .= " AND br.id in ($branches)";
        }

        if($products){
            $where .= " AND term.product_id in ($products)";
        } 


        $q = "SELECT pr.* 
                FROM programs as pr
                LEFT JOIN branches as br ON pr.branch_id = br.id
                LEFT JOIN term_program_product AS term ON term.program_id = pr.id
                LEFT JOIN products as pd ON pd.id = term.product_id WHERE pr.id > 0 $where
            ";

        $programs = DB::select(DB::raw($q));

        return response()->json($programs);
    }
    public function addProgramIelts(Request $request, $branch_id){
        $program_id=DB::table('programs')->insertGetId(
            [
                //'program_id' => '',
                'name'=>$request->program_name,
                'created_at'=>now(),
                'updated_at'=>now(),
                'status'=>$request->status,
                'parent_id'=>$request->program_parent,
                'branch_id'=>$branch_id,
                'semester_id'=>$request->semester_id,
            ]
            );
        DB::table('term_program_product')->insert(
            [
                'program_id' => $program_id,
                'product_id'=>$request->product_id,
                'program_code_id'=>$request->program_code_id,
                'created_at'=>now(),
                'updated_at'=>now(),
                'status'=>$request->status,
            ]
            );
        return response()->json($program_id);
    }
}
