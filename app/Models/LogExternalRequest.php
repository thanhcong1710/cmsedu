<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 6/19/2018
 * Time: 10:42 AM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Providers\CurlServiceProvider as curl;
use Illuminate\Support\Facades\DB;

class LogExternalRequest extends Model
{
    public $LMS_API = 0;
    public $EFFECT_API = 1;
    public $LMS_WEB_REQUEST = 2;
    public $SUCCESS_STATUS = 1;
    public $FAILURE_STATUS = 0;
    private $TABLE = 'log_external_request';

    public function logCallingAPI($url, $params, $header, $method, $created_at, $response=null, $status=0, $request_type=0, $priority=0, $uid=0, $act=''){
        DB::table($this->TABLE)->insert(
            [
                'url' => "$url",
                'method' => "$method",
                'header' => "$header",
                'params' => "$params",
                'created_at' => $created_at,
                'type' => $request_type,
                'response' => $response,
                'status' => $status,
                'priority' => $priority,
                'creator_id' => $uid,
                'action' => $act,
                'hash' => md5($url.$method.$params.$response)
            ]
        );
    }

    public function reCallAPI($requestType=0){
        $requests = $this->getFailureRequest($requestType);

        if(!empty($requests)){
            foreach ($requests as $request){
                $url = $request->url;
                $method = $request->method;
                $params = $request->params?json_decode($request->params, true):[];
                $header = $request->header?json_decode($request->header, true):[];
                $proxy = ($requestType==0)?$this->LMS_PROXY:null;

                try{
                    $res = curl::curl($url, $method, $header, $params,$proxy);
                    if($res){
                        $this->confirm($request->id);
                    }else{
                        $this->updateRecallTimes($request->id);
                    }
                }catch (\Exception $exception){
                    $this->updateRecallTimes($request->id);
                }
            }
        }
//        $this->deleteConfirmedRequest();
    }

    private function getFailureRequest($requestType=0){
        $query = "SELECT * FROM log_external_request WHERE `status` = 0 AND `type` = $requestType LIMIT 0,5";
        $res = DB::select(DB::raw($query));
        return $res;
    }

    private function confirm($request_id){
        DB::table($this->TABLE)
            ->where('id', $request_id)
            ->update(['status' => 1]);
    }

    private function updateRecallTimes($request_id){
        DB::table($this->TABLE)
            ->where('id', $request_id)
            ->increment('recall_time', 1);
    }

    private function deleteConfirmedRequest(){
        DB::table($this->TABLE)->where('status', '=', 1)->delete();
    }
}