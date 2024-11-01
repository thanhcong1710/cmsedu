<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 3/13/2018
 * Time: 12:13 PM
 */

namespace App\Providers;
use Illuminate\Support\ServiceProvider;

class CurlServiceProvider extends ServiceProvider
{
    public static $HEADER = [
        "cache-control: no-cache",
        "content-type: application/json",
    ];

    public static function get($url, $header = [], $params = [], $proxy=null){
        return self::curl($url, 'GET', $header, $params,$proxy);
    }

    public static function post($url, $header = [], $params = [], $proxy=null){
        return self::curl($url, 'POST', $header, $params,$proxy);
    }

    public static function put($url, $header = [], $params = [], $proxy=null){
        return self::curl($url, 'PUT', $header, $params,$proxy);
    }

    public static function patch($url, $header = [], $params = [], $proxy=null){
        return self::curl($url, 'PATCH', $header, $params,$proxy);
    }

    public static function delete($url, $header = [], $params = [], $proxy=null){
        return self::curl($url, 'DELETE', $header, $params,$proxy);
    }

    public static function curl($url, $method, $header = [], $params = [], $proxy=null){
        $curl = curl_init();
        $http_header = self::$HEADER;
        if(!empty($header)){
            foreach ($header as $h){
                $http_header[] = $h;
            }
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => $http_header
        ];

        if($proxy){
            $options[CURLOPT_PROXY] = $proxy;
            $options[CURLOPT_FOLLOWLOCATION] = 1;
        }
        
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            return false;
        } else {
            return $response;
        }
    }
}