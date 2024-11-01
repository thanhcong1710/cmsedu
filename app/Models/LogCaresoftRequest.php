<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LogCaresoftRequest extends Model
{
  public $SUCCESS_STATUS = 1;
  public $FAILURE_STATUS = 0;
  protected $table = 'log_caresoft_request';
  protected $fillable = ['url', 'method', 'header', 'params', 'created_at', 'response', 'status', 'creator_id', 'action'];

  public function logCallingAPI($url, $params, $header, $method, $created_at, $response = null, $status = 0, $uid = 0, $act = '')
  {
    LogCaresoftRequest::create(
      [
        'url' => "$url",
        'method' => "$method",
        'header' => "$header",
        'params' => "$params",
        'created_at' => $created_at,
        'response' => $response,
        'status' => $status,
        'creator_id' => $uid,
        'action' => $act
      ]
    );
  }
}
