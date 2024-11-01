<?php

namespace App\Models;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';
    public $timestamps = false;

    public static function getSchedules($class_id, $sort = true){
        if (!$sort){
            return self::selectRaw("cjrn_id, cjrn_classdate")->whereRaw("class_id = $class_id")->get();
        }
        else{
            return self::selectRaw("cjrn_id, cjrn_classdate")->whereRaw("class_id = $class_id")->orderByRaw('cjrn_classdate ASC')->get();
        }

    }

  public static function getNearestDateScheduleByClassId($classId)
  {
    if(empty($classId)){
      return null;
    }
    $res = u::first("SELECT cjrn_classdate FROM schedules WHERE class_id = $classId AND cjrn_classdate > NOW() and status = 1 ORDER BY cjrn_classdate ASC");
    return $res ? $res->cjrn_classdate : null;
  }
}
