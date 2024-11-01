<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
class AppController extends Controller
{
    public function index(Request $request)
    {
        return view('app');
    }

    public function getNotificationMessage()
    {
        $response = new Response();
        $data = getenv('NOTIFICATION_MESSAGE');
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    public function getStats(Request $request)
    {
        $response = new Response();
        $session = u::session();
        $data = [
            'system_enabled' => getenv('CLIENT_ENABLED'),
            'status' => isset($session->status) ? $session->status : 0,
        ];
        return $response->formatResponse(APICode::SUCCESS, $data);
    }
    public function getPhpInfo(){
        phpinfo();
        die();
    }
}
