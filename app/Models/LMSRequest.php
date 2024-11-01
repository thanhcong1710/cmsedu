<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 6/18/2018
 * Time: 4:48 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\CurlServiceProvider as curl;

class LMSRequest extends Model
{
    private $STF_ID = 2809;
    private $LMS_URL_SETUP = "";

    public function activeStudent($lms_id)
    {
        $log = new LogExternalRequest();
        $result = null;
        $url = $this->LMS_URL_SETUP . "CounStudentStatusHistoryEdit";
        $method = 'POST';
        $params = [
            "counn" => [
                "coun_memo" => "",
                "coun_status" => "Active",
                "coun_status_detail" => "",
                "coun_std_id" => "$lms_id"
            ],
            "staff" => [
                "stf_id" => $this->STF_ID
            ]
        ];

        try{
            $res = curl::curl($url, $method,[],$params);
            if($res){
                $result = $res;
                $log->logCallingAPI($url, json_encode($params), json_encode([]), $method,date('Y-m-d H:i:s'), $res, $log->SUCCESS_STATUS, $log->LMS_WEB_REQUEST, 1);
            }else{
                $log->logCallingAPI($url, json_encode($params), json_encode([]), $method,date('Y-m-d H:i:s'), null, $log->FAILURE_STATUS, $log->LMS_WEB_REQUEST, 1);
            }
        }catch (\Exception $exception){
            $log->logCallingAPI($url, json_encode($params), json_encode([]), $method,date('Y-m-d H:i:s'), $exception->getMessage(), $log->FAILURE_STATUS, $log->LMS_WEB_REQUEST, 1);
        }

        return $result;
    }

    public function withdrawStudent($lms_id)
    {
        $log = new LogExternalRequest();
        $result = null;
        $url = $this->LMS_URL_SETUP . "CounStudentStatusHistoryEdit";
        $method = 'POST';
        $params = [
            "counn" => [
                "coun_memo" => "",
                "coun_status" => "Withdrawal",
                "coun_status_detail" => "",
                "coun_std_id" => "$lms_id"
            ],
            "staff" => [
                "stf_id" => $this->STF_ID
            ]
        ];

        try{
            $res = curl::curl($url, $method,[],$params);
            if($res){
                $result = $res;
                $log->logCallingAPI($url, json_encode($params), json_encode([]), $method,date('Y-m-d H:i:s'), $res, $log->SUCCESS_STATUS, $log->LMS_WEB_REQUEST, 1);
            }else{
                $log->logCallingAPI($url, json_encode($params), json_encode([]), $method,date('Y-m-d H:i:s'), null, $log->FAILURE_STATUS, $log->LMS_WEB_REQUEST, 1);
            }
        }catch (\Exception $exception){
            $log->logCallingAPI($url, json_encode($params), json_encode([]), $method,date('Y-m-d H:i:s'), $exception->getMessage(), $log->FAILURE_STATUS, $log->LMS_WEB_REQUEST, 1);
        }

        return $result;
    }

    public function scheduleChangeStatusStudent($lms_id,$status=1){
        $log = new LogExternalRequest();
        $result = null;
        $url = $this->LMS_URL_SETUP . "CounStudentStatusHistoryEdit";
        $method = 'POST';
        $params = [
            "counn" => [
                "coun_memo" => "",
                "coun_status" => $status?"Active":"Withdrawal",
                "coun_status_detail" => "",
                "coun_std_id" => "$lms_id"
            ],
            "staff" => [
                "stf_id" => $this->STF_ID
            ]
        ];
        $log->logCallingAPI($url, json_encode($params), json_encode([]), $method,date('Y-m-d H:i:s'), null, $log->FAILURE_STATUS, $log->LMS_WEB_REQUEST, 1);
    }
}
