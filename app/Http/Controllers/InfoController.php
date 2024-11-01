<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 4/3/2018
 * Time: 5:13 PM
 */

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Response;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;
class InfoController
{
    private $PENDING_REASON_TYPE = 0;
    private $WITHDRAW_REASON_TYPE = 1;


    public function getHolidays($branch_id){
        $response = new Response();
        $res = u::getPublicHolidays(0,$branch_id,9999);
        return $response->formatResponse(APICode::SUCCESS, $res);
    }

    public function getClassDays($class_id){
        $response = new Response();
        $query = "SELECT class_day FROM sessions WHERE class_id=$class_id GROUP BY class_day";
        $res = u::query($query);
        $da = [];
        if($res){
            foreach ($res as $r){
                $da[] = $r->class_day;
            }
        }
        return $response->formatResponse(APICode::SUCCESS,$da);
    }

    public function getPendingReasons(){
        $response = new Response();
        $query = "SELECT id, description FROM reasons WHERE `type` = $this->PENDING_REASON_TYPE AND status=1";
        $res = u::query($query);
        $da = [];
        if($res){
            foreach ($res as $r){
                $da[$r->id] = $r;
            }
        }
        return $response->formatResponse(APICode::SUCCESS,$da);
    }
}