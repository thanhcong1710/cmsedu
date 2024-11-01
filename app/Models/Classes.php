<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Classes extends Model
{
	protected $table = 'classes';
	public $timestamps = false;

	public function scopeByContract($query, $conId){
		if($conId){

                /**
                * Laravel >=5.6
                 */
			// $query->select('classes.id','classes.cls_name','classes.class_day_time','classes.class_day_time','users.full_name','classes.product_id','cl_session.clse_room_name','cl_session.clse_day','cl_session.clse_room_name','cl_session.clse_shift_name','cl_session.clse_shift_start','cl_session.clse_shift_end','programs.name as prog_name')
			// 	->leftJoin('programs','classes.program_id','=','programs.id')
                //   ->leftJoin('users','classes.teacher_id','=','users.id')
                // ->joinSub(DB::table('sessions')
                // 			->select('sessions.class_id as clse_id','sessions.shift_id as clse_shift_id','sessions.room_id as clse_room_id','sessions.class_day as clse_day','rooms.room_name as clse_room_name','shifts.name as clse_shift_name','shifts.start_time as clse_shift_start','shifts.end_time as clse_shift_end')
                // 			->leftJoin('rooms','sessions.room_id','=','rooms.id')
                // 			->leftJoin('shifts','sessions.shift_id','=','shifts.id')
                // 			->groupBy('sessions.class_id'),'cl_session',function($join){
                // 				$join->on('classes.id','=','cl_session.clse_id');
                // 			})
                $query->select('classes.id','classes.cls_name','classes.class_day_time','classes.class_day_time','users.full_name','classes.product_id','rooms.room_name as clse_room_name','sessions.class_day as clse_day','rooms.room_name as clse_room_name','shifts.name as clse_shift_name','shifts.start_time as clse_shift_start','shifts.end_time as clse_shift_end','programs.name as prog_name')
                                ->leftJoin('programs','classes.program_id','=','programs.id')
                ->leftJoin('users','classes.teacher_id','=','users.id')
                ->leftJoin('sessions','classes.id','=','sessions.class_id')
                ->leftJoin('rooms','sessions.room_id','=','rooms.id')
                ->leftJoin('shifts','sessions.shift_id','=','shifts.id')
                ->where(function($query) use ($conId){
                	$query->whereRaw('classes.id = (select class_id FROM contracts WHERE id = '.$conId.')');
                        });
		}

		return $query;
	}
}
