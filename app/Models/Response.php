<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 3/12/2018
 * Time: 10:36 AM
 */

namespace App\Models;
use App\Models\APICode;
use App\Models\Message;

class Response
{
    public $d;
    public $c;
    public $m;

    public function __construct() {
        $this->d = null;
        $this->c = APICode::PERMISSION_DENIED;
        $this->m = '';
    }

    public function _($d = null) {
        $this->c = APICode::SUCCESS;
        $this->d = $d ? $d : (Object)[];
    }

    public function e() {
        return self::formatResponse($this->c, $this->d, $this->m);
    }

    public function formatResponse($code, $data, $msg = ''){
        $messageObj = new Message();
        return json_encode([
            "code" => $code,
            "message" => $msg ? $msg : $messageObj->getMessage($code),
            "data" => $data
        ]);
    }

    public function formatResponseForce($code, $data, $msg = ''){
        $messageObj = new Message();
        $object =  json_encode([
            "code" => $code,
            "message" => $msg ? $msg : $messageObj->getMessage($code),
            "data" =>$data
        ],JSON_FORCE_OBJECT);
        return $object;
    }
}