<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Providers\CurlServiceProvider as curl;
use App\Models\APICode;
use App\Models\Response;
use App\Models\LogExternalRequest;

class EffectAPIController
{
    // private $BASE_URL = 'http://123.31.12.149:8080/';
    public function __construct(){
        exit();
    }
    // public function getUrl($func){
    //     return $this->BASE_URL . $func;
    // }
    //
    // public function callAPI(Request $request, $token=null){
    //     $resp = null;
    //     $response = new Response();
    //     $data = null;
    //     $code = APICode::SUCCESS;
    //     $log = new LogExternalRequest();
    //     $token = $token?$token:$request->headers->get('Authorization');
    //     $url = $this->BASE_URL . $request->api_url;
    //     $author = isset($request->api_author) ? $request->api_author : (Object)['id' => 0, 'act' => 'Auto process call API'];
    //     $method = $request->api_method;
    //     $params = $request->api_params?json_decode($request->api_params, true):[];
    //     $header = [
    //         "Authorization:" . $token,
    //         "access-token:" . $token
    //     ];
    //     if (ENVIRONMENT == 'product') {
    //         try{
    //             $res = curl::curl($url, $method, $header, $params);
    //             if($res){
    //                 $code = APICode::SUCCESS;
    //                 $data = $res;
    //                 $log->logCallingAPI($url, $request->api_params, $request->api_header, $request->api_method,date('Y-m-d H:i:s'), $res, $log->SUCCESS_STATUS,$log->EFFECT_API, $author->id, $author->act);
    //             }else{
    //                 $code = APICode::FAILURE_CALLING_LMS_API;
    //                 $log->logCallingAPI($url, $request->api_params, $request->api_header, $request->api_method,date('Y-m-d H:i:s'), null, $log->FAILURE_STATUS, $log->EFFECT_API, $author->id, $author->act);
    //             }
    //         }catch (\Exception $exception){
    //             $code = APICode::FAILURE_CALLING_LMS_API;
    //             $log->logCallingAPI($url, $request->api_params, $request->api_header, $request->api_method,date('Y-m-d H:i:s'), $exception->getMessage(), $log->FAILURE_STATUS, $log->EFFECT_API, $author->id, $author->act);
    //         }
    //         $resp = $response->formatResponse($code, $data);
    //     } else {
    //         $randid = rand(10000, 99999);
    //         if (md5($request->api_url) == md5('effect/sales')) {
    //             $status = trim($request->fake->status);
    //             $uemail = trim($request->fake->uemail);
    //             $usname = trim($request->fake->usname);
    //             $userid = trim($request->fake->userid);
    //             $supeid = trim($request->fake->supeid);
    //             $accoun = trim($request->fake->accoun);
    //             $startd = trim($request->fake->startd);
    //             $hrm_id = trim($request->fake->hrm_id);
    //             $creatd = trim($startd.'T'.date('H:i:s'));
    //             $updatd = date('Y-m-d H:i:s');
    //             $effect = isset($request->fake->ex_hrm) && $request->ex_hrm != '' ? $request->ex_hrm : "NV-SALE-TEST-$randid";
    //             $respon = '{"code": 200,"data":{"Salesman":"'.$hrm_id.'","bo_phan":151,"datetime":"2018-09-13T22:40:17.6957797+07:00","dia_chi":"","dien_thoai":"1673349548","disable":0,"f_identity":23416,"gioitinh":"F","leader":239,"ma":"'.$effect.'","ma_crm":"'.$request->ex_hrm.'","ma_user":1,"ten":"'.$usname.'"},"message":"ok"}';
    //             $data = json_decode($respon);
    //         } else {
    //             $effect = "ST-TEST-$randid";
    //             $data = json_decode('{"code":200,"message":"ok","data":{"f_identity":9999999,"ma":"'.$effect.'","ten":"TEST STUDENT","ma_crm":"CRM000000","ngay_sinh":"2019-09-19T00:00:00Z","doitac_lienhe":"Parents name","dia_chi":"Address","phone":"09999999999","bo_phan":90,"salesman":9000,"tkhoan_nh":"","TenNganHang":"","ma_gtgt":"","gioitinh":"M","ma_kh":900000,"datetime":"2019-09-19T14:06:31.600978526+07:00","ma_user":1,"dvcs":1}}');
    //         }
    //         sleep(2);
    //         $log->logCallingAPI($url, $request->api_params, $request->api_header, $request->api_method, date('Y-m-d H:i:s'), json_encode($data), $log->SUCCESS_STATUS,$log->EFFECT_API, $author->id, $author->act);
    //     }
    //     $resp = $response->formatResponse($code, $data);
    //     return $resp;
    // }
    //
    // public function reCallAPI()
    // {
    //     $resp = null;
    //     if (ENVIRONMENT == 'product') {
    //         $response = new Response();
    //         $log = new LogExternalRequest();
    //         $log->reCallAPI($log->EFFECT_API);
    //     }
    //     $resp = $response->formatResponse(APICode::SUCCESS,null);
    //     return $resp;
    // }
}
