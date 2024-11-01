<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 3/27/2018
 * Time: 1:52 PM
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Providers\CurlServiceProvider as curl;
use App\Models\APICode;
use App\Models\Response;

class golangAPIController
{
    public function callAPI(Request $request){
        $response = new Response();
        $data = null;

        $url = $request->api_url;
        $method = $request->api_method;
        $params = $request->api_params?$request->api_params:[];
        $header = [
            'Authorization: '.$request->users_data->key
        ];

        try{
            $res = curl::curl($url, $method, $header, $params);
            if($res){
                $code = APICode::SUCCESS;
                $data = $res;
            }else{
                $code = APICode::FAILURE_CALLING_GOLANG_API;
            }
        }catch (\Exception $exception){
            $code = APICode::FAILURE_CALLING_GOLANG_API;
        }

        return $response->formatResponse($code, $data);
    }
}