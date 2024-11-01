<?php
namespace App\Http\Middleware;
use Illuminate\Support\Facades\Redis;

use Closure;

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

class Authentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $authorize_key = $request->headers->get('Authorization');
      $response = (object) [];
      $response->message = 'invalid session';
      $response->status = 200;
      $request->authorized = null;
      $check = false;
      $sinformation = (object)[];
      if ($authorize_key !== null) {
        $token = Redis::keys("*:$authorize_key");
        $token = count($token) ? $token[0] : '';
        if ($token) {
          $session = Redis::get($token);
          if ($session) {
            $client_info = (Object)[];
            // $client_info->address = $_SERVER['REMOTE_ADDR'];
            $client_info->agent = $_SERVER['HTTP_USER_AGENT'];
            $client_info->language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $sinformation = json_decode($session);
            $mask = md5(json_encode($client_info).$sinformation->salt);
            if ($sinformation->mac == $mask) {
              $request->authorized = $session;
              $request->users_data = $sinformation;
              $check = true;
            }
          }
        }
      }
      // if ($check && $sinformation) {
      //   $path = explode('/', $request->path());
      //   $controller = $path[1];
      //   $role_id = $sinformation->role_id;
      //   $role_id = "role_$role_id";
      //   $role = $sinformation->roles->$role_id;
      //   $list = $role->rules;
      //   $check = in_array($controller, $list);
      // }      
      if (!$check) {
        $response = [
            'code' => 403,
            'message' => 'Phiên làm việc của bạn đã hết hạn, xin vui lòng thoát ra đăng nhập lại.',
            'data' => null
        ];
        exit(json_encode($response));
      }
      return $next($request);
    }
}
