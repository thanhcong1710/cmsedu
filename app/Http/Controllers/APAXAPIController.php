<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\CurlServiceProvider as curl;
use App\Models\APICode;
use App\Models\Response;
use App\Models\LogExternalRequest;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\DB;
class APAXAPIController
{
    private function callAPI($url, $method, $params, $header, $is_api = true)
    {
        $resp = null;
        $response = new Response();
        $data = null;
        $log = new LogExternalRequest();
        try {
            $res = curl::curl($url, $method, $header, $params);
            if ($res) {
                $code = APICode::SUCCESS;    
                $log->logCallingAPI($url, json_encode($params), json_encode($header), $method, date('Y-m-d H:i:s'), $res, $log->SUCCESS_STATUS, $log->LMS_API);
                $data = json_decode($res);
            } else {
                $code = APICode::FAILURE_CALLING_LMS_API;
                $log->logCallingAPI($url, json_encode($params), json_encode($header), $method,  date('Y-m-d H:i:s'), $res, $log->SUCCESS_STATUS, $log->LMS_API);
            }
        } catch (\Exception $exception) {
            $code = APICode::FAILURE_CALLING_LMS_API;
            $log->logCallingAPI($url, json_encode($params), json_encode($header), $method, date('Y-m-d H:i:s'), $exception->getMessage(), $log->FAILURE_STATUS, $log->LMS_API);
        }

        if ($is_api) {
            $resp = $response->formatResponse($code, $data);
        } else {
            $resp = $data;
        }

        return $resp;
    }
    public function getToken()
    {
        if (ENVIRONMENT == 'product') {
            $url = "https://ems.apaxleaders.edu.vn/api/third-login";
        } else {
            $url = "https://ems.apaxleaders.edu.vn/api/third-login";
        }
        $method = 'POST';
        $params = [
            "username" => "APICMS",
            "password" => "@123456789",
        ];
        $header = [
            "Accept-Language:en-GB,en-US;q=0.9,en;q=0.8,vi;q=0.7",
            "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36"
        ];
        $resp = $this->callAPI($url, $method, $params, $header, 0, false);
        return $resp;
    }
    public function getAllData()
    {
        $token = self::getToken();
        $token = data_get($token,'access-token');
        
        for($i=1;$i<160;$i++){
            echo $i."/";
            if($i!=100){
                if (ENVIRONMENT == 'product') {
                    $url = "https://ems.apaxleaders.edu.vn/api/apax/list-student";
                } else {
                    $url = "https://ems.apaxleaders.edu.vn/api/apax/list-student";
                }
                $method = 'POST';
                $params = [
                    "branch_id" => $i
                ];
                $header = [
                    "Accept-Language:en-GB,en-US;q=0.9,en;q=0.8,vi;q=0.7",
                    "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36",
                    "Authorization:$token",
                ];
                $resp = $this->callAPI($url, $method, $params, $header, 0, false);
                self::addItems(data_get($resp, 'data'));
            }
        }
        return "ok";
    }

    public function addItems($list) {
        if ($list) {
            $connection = DB::connection('mysql_lead');
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO data_apax_tmp (ten_trung_tam,ma_hoc_sinh, ten_hoc_sinh, so_dien_thoai, ten_phu_huynh, ngay_sinh, dia_chi, truong_hoc, ngay_tao,ma_tinh,ten_tinh,ma_huyen,ten_huyen,created_at) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query.= "('$item->ten_trung_tam', '$item->ma_hoc_sinh', '$item->ten_hoc_sinh', '$item->so_dien_thoai', '$item->ten_phu_huynh', '$item->ngay_sinh', '".addslashes($item->dia_chi)."', '".addslashes($item->truong_hoc)."','$item->ngay_tao','$item->ma_tinh','$item->ten_tinh','$item->ma_huyen','$item->ten_huyen','$created_at'),";
                }
                $query = substr($query, 0, -1);
                $connection->insert(DB::raw($query));
                self::addItems(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $query.= "('$item->ten_trung_tam', '$item->ma_hoc_sinh', '$item->ten_hoc_sinh', '$item->so_dien_thoai', '$item->ten_phu_huynh', '$item->ngay_sinh', '".addslashes($item->dia_chi)."', '".addslashes($item->truong_hoc)."','$item->ngay_tao','$item->ma_tinh','$item->ten_tinh','$item->ma_huyen','$item->ten_huyen','$created_at'),";
                }
                $query = substr($query, 0, -1);
                $connection->insert(DB::raw($query));
            }
        }
    }
}
