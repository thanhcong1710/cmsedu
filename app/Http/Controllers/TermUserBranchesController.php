<?php

namespace App\Http\Controllers;

use function _\first;
use App\Models\APICode;
use App\Models\Response;
use App\Models\TermUserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Providers\UtilityServiceProvider as u;

class TermUserBranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null ? 5 : $request->pageSize;
        $termUser = DB::table('term_user_branch')->paginate($pageSize);
        foreach ($termUser as $term) {
            if ($term->user_id) {
                $user = DB::table('users')->where('users.id', $term->user_id)->get();
                $term->user = $user;
            } else {
                $term->user = null;
            }
            if ($term->branch_id) {
                $branch = DB::table('branches')->where('branches.id', $term->branch_id)->get();
                $term->branch_name = $branch[0]->name;
            } else {
                $term->branch_name = null;
            }
            if ($term->role_id) {
                $role = DB::table('roles')->where('roles.id', $term->role_id)->get();
                $term->role_name = $role[0]->name;
            } else {
                $term->role_name = null;
            }
        }

        return response()->json($termUser);

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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'hash_key' => 'max:255'
        ]);
        $termUser = new TermUserBranch();
        $termUser->user_id = $request->user_id;
        $termUser->branch_id = $request->branch_id;
        $termUser->role_id = $request->role_id;
        $termUser->hash_key = $request->hash_key;
        $termUser->created_at = $request->created_at;
        $termUser->updated_at = $request->updated_at == null ? time() : $request->updated_at;
        $termUser->save();

        return response()->json($termUser);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $term = TermUserBranch::find($id);
        if ($term->user_id) {
            $user = DB::table('users')->where('users.id', $term->user_id)->get();
            $term->user = $user;
        } else {
            $term->user = null;
        }
        if ($term->branch_id) {
            $branch = DB::table('branches')->where('branches.id', $term->branch_id)->get();
            $term->branch_name = $branch[0]->name;
        } else {
            $term->branch_name = null;
        }
        if ($term->role_id) {
            $role = DB::table('roles')->where('roles.id', $term->role_id)->get();
            $term->role_name = $role[0]->name;
        } else {
            $term->role_name = null;
        }
        return response()->json($term);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $termUser = TermUserBranch::find($id);
        return response()->json($termUser);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'hash_key' => 'max:255'
        ]);
        $termUser = TermUserBranch::find($id);
        $termUser->user_id = $request->user_id;
        $termUser->branch_id = $request->branch_id;
        $termUser->role_id = $request->role_id;
        $termUser->hash_key = $request->hash_key;
        $termUser->created_at = $request->created_at;
        $termUser->updated_at = $request->updated_at == null ? time() : $request->updated_at;
        $termUser->save();

        return response()->json($termUser);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $termUser = TermUserBranch::find($id);
        if ($termUser->delete()) return response()->json("delete Success");
    }

    public function search(Request $request)
    {
        $field = explode(",", $request->field);
        $keyword = explode(",", $request->keyword);
        $pageSize = $request->pageSize;

        $column = DB::getSchemaBuilder()->getColumnListing("term_user_branch");

        $p = DB::table('term_user_branch');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%' . $keyword[0] . '%');
        for ($i = 1; $i < count($field); $i++) {
            if (in_array($field[$i], $column)) {
                $p->orWhere($field[$i], 'like', '%' . $keyword[$i] . '%');
            }
        }
        return $p->paginate($pageSize);
    }

    public function updateRoleForUser(Request $request, $userId, $branchId)
    {
        $data = [
            'error_code' =>1,
            'msg'=>'Thông báo'
        ];
        $params = $request->all();
        if ($request->status){
            $model = new TermUserBranch();
            $creatorId = $request->users_data->id;
            $model->updateRoleForUser($userId, $request->branch_id, $params['role_id'], (boolean)$params['status'], $creatorId);
            $data['error_code']= 0;
        }
        else{
            $cSQL = "SELECT COUNT(id)  AS total FROM `term_user_branch` WHERE user_id=  $userId";
            $count = u::query($cSQL);
            $total = $count ? $count[0]->total : 0;
            $roleId = $request->role_id;
            $branch_id = $request->branch_id;

            $info = u::first("SELECT created_at FROM `term_user_branch` WHERE user_id = $userId AND branch_id = $branch_id AND role_id= $roleId ");
            $startDate = !empty($info->created_at) ? $info->created_at : null;

            $delete = "DELETE FROM `term_user_branch` WHERE user_id=  $userId AND role_id = $roleId AND branch_id = $branch_id";
            if ($roleId < 84){
                $term_student_user = u::first("SELECT COUNT(id) AS total FROM `term_student_user` WHERE (ec_id = $userId OR cm_id = $userId) AND branch_id = $branch_id");
                if ($term_student_user->total == 0){
                    if ($total >1){
                        u::query($delete);
                        $data['error_code']= 0;
                        if ($startDate){
                            $log = "INSERT INTO `log_user_branch`(`user_id`,`branch_id`,`role_id`,`start_date`,`end_date`)".
                                " VALUES ($userId,$branch_id,$roleId,'$startDate',NOW())";
                            u::query($log);
                        }
                    }
                    else
                        $data['msg']= 'Không thể bỏ gán quyền này!';
                }
                else
                    $data['msg']= 'Lỗi !!! Bạn chưa chuyển người quản lý học sinh. Không thể bỏ gán quyền này!';
            }
            else{
                if ($total >1){
                    u::query($delete);
                    $data['error_code']= 0;
                    if ($startDate){
                        $log = "INSERT INTO `log_user_branch`(`user_id`,`branch_id`,`role_id`,`start_date`,`end_date`)".
                            " VALUES ($userId,$branch_id,$roleId,'$startDate',NOW())";
                        u::query($log);
                    }
                }
                else
                    $data['msg']= 'Không thể bỏ gán quyền này!';
            }
        }

        $res = new Response();
        $session = u::session();
        if(!empty($session)){
            $authorize_key = $request->headers->get('Authorization');
            $keys = Redis::keys("*:$authorize_key");
            $session->status = !empty(u::first("select * from term_user_branch where user_id = $userId and status = 1"))? 1: 0;
            Redis::set($keys[0], json_encode($session));
        }
        return $res->formatResponse(APICode::SUCCESS, $data);
    }
}
