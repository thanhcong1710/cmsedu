<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * ========================================================== *
 * --------------------- Apax ERP System --------------------
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package Apax ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */

class TestingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

    }

    public function getTime(Request $request) {
        $now = date('Y-m-d H:i:s');
        $sql = u::first("SELECT NOW() crt");
        $sql = $sql->crt;
        echo ("\n\n\nCurrent time on PHP: $now");
        die ("\n\nCurrent time on MySQL: $sql");
    }

    public function doSomeThing(Request $request) {
        $data = null;
        $response = new Response();
        $code = APICode::PERMISSION_DENIED;
        if ($post = (Object)$request->input()) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            $duration = rand(500,3000);
            sleep($duration/1000);
            $data = $post;
            $data->duration = $duration;
        }
        return $response->formatResponse($code, $data);
    }

    public function exeCmd(Request $request) {
        $data = null;
        $response = new Response();
        $code = APICode::PERMISSION_DENIED;
        if ($post = (Object)$request->input()) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            $cmd = $post->cmd;            
            $data->result = exec($cmd);
        }
        return $response->formatResponse($code, $data);
    }

    public function start(Request $request) {
        $data = null;
        $response = new Response();
        $code = APICode::PERMISSION_DENIED;
        if ($post = (Object)$request->input()) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            $total_task = u::first("SELECT COUNT(id) total FROM log_external_request WHERE `status` = 0");
            $total_task = $total_task && isset($total_task->total) ? (int)$total_task->total : 0;
            if ($total_task) {
                $lim = $total_task % 5 > 0 ? (int)$total_task / 5 + 1 : $total_task / 5;
                $result = [];
                for ($i = 0; $i < $lim; $i++) {
                    $result[] = exec("php ../artisan ada:test note --params='log at time:'");
                }
            }
        }
        return $response->formatResponse($code, $data);
    }

}
