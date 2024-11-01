<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TermUserClass;
use App\Models\LmsClass;

class TermUserClassesController extends Controller
{
	public function index(){
		return 1;
	}

    public function store(Request $request){
    	$request->validate([
    		'start_date' => 'required|min:date("Y-m-d")'
    	]);

        $curentCM = DB::select(DB::raw("SELECT *FROM term_user_class where ((start_date <= CURDATE() and CURDATE()<= end_date) OR (end_date IS NULL and start_date<= CURDATE())) AND class_id = $request->class_id AND status = 1"));

        $day = strtotime(date("Y-m-d", strtotime($request->start_date)) . " -1 days");
		$day = strftime("%Y-%m-%d",$day);

        if (!$curentCM){
        	$terms = DB::table('term_user_class')->where('start_date', '>', date('Y-m-d'))->where('class_id', $request->class_id)->get();
        	// return count($terms);
        	if (count($terms) == 0){
        		// return 1;
        		DB::table('classes')->where('id', $request->class_id)->update(['cm_id' => $request->user_id]);
        		DB::table('term_user_class')->insert(['user_id'=> $request->user_id, 'class_id'=> $request->class_id, 'start_date' => $request->start_date, 'status' => 1]);	
        	}else{
        		DB::table('term_user_class')->where('start_date', '>', $request->start_date)->update(['status' => 0]);
                DB::table('term_user_class')->where('start_date', $request->start_date)->update(['status' => 0]);
                $p = DB::table('term_user_class')->where('start_date', '<', $request->start_date)->where('status', 1)->orderBy('start_date', 'desc')->limit(1)->get();

                DB::table('term_user_class')->where('id', $p[0]->id)->update(['end_date' => $day]);
                DB::table('term_user_class')->insert(['user_id'=> $request->user_id, 'class_id'=> $request->class_id, 'start_date' => $request->start_date, 'status' => 1]);
        	}
        }
        else if (!$curentCM[0]->end_date){
     
        	DB::table('term_user_class')->where('id', $curentCM[0]->id)->update(['end_date' => $day]);

        	DB::table('term_user_class')->insert(['user_id'=> $request->user_id, 'class_id'=> $request->class_id, 'start_date' => $request->start_date, 'status' => 1]);
        }
        else if ($curentCM[0]->end_date && $curentCM[0]->end_date >= $request->start_date){
        	DB::table('term_user_class')->where('start_date', '>', date('Y-m-d'))->update(['status'=>0]);
			
        	DB::table('term_user_class')->where('id', $curentCM[0]->id)->update(['end_date' => $day]);
        	DB::table('term_user_class')->insert(['user_id'=> $request->user_id, 'class_id'=> $request->class_id, 'start_date' => $request->start_date, 'status' => 1]);
        }
        else if ($curentCM[0]->end_date && $curentCM[0]->end_date < $request->start_date){
        	DB::table('term_user_class')->where('start_date', '>', $request->start_date)->update(['status' => 0]);
        	DB::table('term_user_class')->where('start_date', $request->start_date)->update(['status' => 0]);
        	$p = DB::table('term_user_class')->where('start_date', '<', $request->start_date)->where('status', 1)->orderBy('start_date', 'desc')->limit(1)->get();

        	DB::table('term_user_class')->where('id', $p[0]->id)->update(['end_date' => $day]);
        	DB::table('term_user_class')->insert(['user_id'=> $request->user_id, 'class_id'=> $request->class_id, 'start_date' => $request->start_date, 'status' => 1]);
        }

    }
}
