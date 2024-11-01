<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Response;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->pageSize == null ? 5 : $request->pageSize;
        $roles = Role::paginate($pageSize);
        return response()->json($roles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        return response()->json(Role::all());
    }

    public function getSelectAll(Request $request){
        $ec = !empty($request->ec) ? $request->ec : null;
        $optRoles = [];
        $roles = Role::all();
        foreach ($roles as $role){
            if ($ec){
                if ($role->id < 84){
                    $optRoles[] = ['value' =>"$role->id",'label' =>$role->name];
                }
            }
            else
                $optRoles[] = ['value' =>"$role->id",'label' =>$role->name];
        }
        return response()->json($optRoles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        $res = new Response();
        return $res->formatResponse(APICode::SUCCESS, Role::all());
    }

    public function getRoleDetail($id)
    {
        $model = new Role();
        $data = $model->getRoleDetail($id);
        $res = new Response();
        return $res->formatResponse(APICode::SUCCESS, $data);
    }

    public function updateRole(Request $request, $id)
    {
        $params = $request->all();
        $model = new Role();

        $data = $model->updateRole($id, $params);
        $res = new Response();
        return $res->formatResponse(APICode::SUCCESS, $data);
    }

    public function updateRoleStatus(Request $request, $id)
    {
        $status = (boolean)$request->input("status", false);
        $model = new Role();
        $data = $model->updateRoleStatus($id, $status);
        $res = new Response();
        return $res->formatResponse(APICode::SUCCESS, $data);
    }

    public function getRoleForUser($userId)
    {
        $userModel = new User();
        $user = $userModel->getRoleDetail($userId);
        $roles = Role::all();
        $data = ['user' => $user, 'roles' => $roles];
        $res = new Response();
        return $res->formatResponse(APICode::SUCCESS, $data);
    }


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
        $this->validate($request, [
            'name' => 'required|max:50'
        ]);

        $role = new Role();
        $role->name = trim($request->name);
        $role->description = trim($request->description);
        $role->status = $request->status;
        $role->created_date = date('Y-m-d H:i:s');
        $role->functions = $request->functions;

        $role->save();

        return response()->json($role);
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
        $role = Role::find($id);
        if ($role) {
            return $reponse()->json($role);
        }
        return response()->json();
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
        $role = Role::find($id);
        if ($role) {
            return $reponse()->json($role);
        }
        return response()->json();
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
        $this->validate($request, [
            'name' => 'required|max:50'
        ]);

        $role = Role::find($id);
        $role->name = trim($request->name);
        $role->description = trim($request->description);
        $role->status = $request->status;
        $role->updated_date = date('Y-m-d H:i:s');
        $role->functions = $request->functions;

        $role->save();

        return response()->json($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $role = Role::find($id);

        if ($role->delete()) {
            DB::table('term_user_branch')->where('role_id', $id)->delete();
            return response()->json("delete success!");
        }
        return response()->json("delete error");
    }

    public function search(Request $request)
    {
        $field = explode(",", $request->field);
        $keyword = explode(",", $request->keyword);
        $pageSize = $request->pageSize;
        // dd($field);
        $column = DB::getSchemaBuilder()->getColumnListing("roles");

        $p = DB::table('roles');

        if (in_array($field[0], $column)) $p->where($field[0], 'like', '%' . $keyword[0] . '%');
        for ($i = 1; $i < count($field); $i++) {
            if (in_array($field[$i], $column)) {
                $p->Where($field[$i], 'like', '%' . $keyword[$i] . '%');
            }
        }
        return $p->paginate($pageSize);
    }
}
