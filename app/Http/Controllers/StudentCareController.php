<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10/9/2019
 * Time: 3:41 PM
 */

namespace App\Http\Controllers;

use App\Models\StudentCare;
use Illuminate\Http\Request;
use App\Models\APICode;
use App\Models\Response;

class StudentCareController extends Controller
{

    public function getList(Request $request, StudentCare $model)
    {
        //dd($request);
        $params = $request->all();
        $data = $model->getStudents($params);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    public function getCare(Request $request, StudentCare $model)
    {dd($request);
        $params = $request->all();
        $data = $model->getStudents($params);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }


    public function collaborator(Request $request, StudentCare $model)
    {
        //dd($request);
        $params = $request->all();
        $data = $model->getCollaborator($params);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

}