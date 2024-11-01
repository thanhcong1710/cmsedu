<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 6/25/2020
 * Time: 10:29 AM
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggest;
use App\Models\Response;
use App\Models\APICode;

class SuggestController  extends Controller
{
    public function suggestSender(Request $request, $key, $branch_id)
    {
        $response = new Response();
        $suggest = new Suggest();
        $res = $suggest->suggestSenderV1($key, $branch_id);
        return $response->formatResponse(APICode::SUCCESS, $res);
    }

    public function getAllTuition($key)
    {
        $response = new Response();
        $suggest = new Suggest();
        $res = $suggest->getListTuitionFee($key);
        return $response->formatResponse(APICode::SUCCESS, $res);
    }

    public function getClassAvailable(Request $request,$branch_id, $program_id)
    {
        $class_id = $request->not_in;
        $response = new Response();
        $suggest = new Suggest();
        $res = $suggest->getListClassAvailable($branch_id, $program_id,$class_id);
        return $response->formatResponse(APICode::SUCCESS, $res);
    }
}