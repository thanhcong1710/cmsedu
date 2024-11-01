<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10/9/2019
 * Time: 3:41 PM
 */

namespace App\Http\Controllers;

use App\Models\Collaborator;
use Illuminate\Http\Request;
use App\Models\APICode;
use App\Models\Response;

class CollaboratorController extends Controller
{

    public function add(Request $request, Collaborator $model)
    {
        $params = $request->all();
        $data = $model->createCollaborator($params);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    public function detail1($id, Collaborator $model)
    {

        $data = $model->getCollaborator($id);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }


    public function edit(Request $request, Collaborator $model)
    {
        $params = $request->all();
        $data = $model->updateCollaborator($params);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    public function list($search,$paging, Collaborator $model)
    {
        return $model->getList(json_decode($search),json_decode($paging));
    }

}