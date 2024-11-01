<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsClass extends Model
{
    protected $table = 'classes';
    public $timestamps = false;

    public function program()
    {
    	return $this->belongsTo('App\Models\Program');
    }

    public static function updateCm($classID, $cmID)
    {
        $class = self::find($classID);
        $class->cm_id = $cmID;
        $class->save();
        return true;
    }

    public static function sqlGetClasses($columns = [], $startDate, $endDate, $where = '', $joinTPP = false, $condLess = false) {
      if( empty($columns) ) {
        $column = 'cl2.*';
      }
      else {
        foreach( $columns as $item ) {
          $columnArr[] = 'cl2.' . $item;
        }
        $column = implode(',',$columnArr);
      }
      $joinT = '';
      if( $joinTPP ) {
        $joinT = " LEFT JOIN term_program_product as tpp on tpp.program_id = cl2.program_id ";
      }
      $whereLess = '';
      if($condLess) {
        $whereLess = " AND (SELECT count(DISTINCT e2.student_id) from enrolments as e2 WHERE e2.class_id = cl2.id ) <= 5 ";
      }

      $sql = "
        SELECT 
          $column 
        FROM classes as cl2 
        $joinT 
        WHERE cl2.cls_iscancelled = 'no'
        AND cl2.cls_startdate <= '$startDate' and cl2.cls_enddate >= '$endDate' 
        $where
        $whereLess
        GROUP BY cl2.id 
      ";

      return $sql;
    }
}
