<?php

namespace App\Http\Controllers;

use App\Models\InfoValidation;
use App\Models\Schedule;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\APICode;
use App\Models\Mail;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;

class ReportStudentsController extends Controller
{
    public function insertStudentReport(){
        $report_week_info = u::first("SELECT id FROM report_weeks WHERE start_date <= CURRENT_DATE AND end_date >= CURRENT_DATE");
        $report_month = Date('Y-m');
        $list_student = u::query("SELECT r.student_id, r.branch_id, r.class_id ,cl.teacher_id, cl.level_id,r.product_id,r.contract_id
            FROM report_full_fee_active AS r 
                LEFT JOIN report_students AS rs ON rs.student_id=r.student_id AND rs.report_week_id= $report_week_info->id
                LEFT JOIN classes AS cl ON cl.id=r.class_id
            WHERE r.report_month='$report_month' AND rs.id IS NULL");
        self::addItem($list_student,$report_week_info->id);
        return "ok";
    }
    public function addItem($list,$report_week_id) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_students (report_week_id, student_id, branch_id, class_id, teacher_id, `level_id`,product_id, created_at, contract_id) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query.= "('$report_week_id', '$item->student_id', '$item->branch_id', '$item->class_id', '$item->teacher_id', '$item->level_id', '$item->product_id', '$created_at' ,'$item->contract_id'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItem(array_slice($list, 5000),$report_week_id);
            } else {
                foreach($list as $item) {
                    $query.= "('$report_week_id', '$item->student_id', '$item->branch_id', '$item->class_id', '$item->teacher_id', '$item->level_id', '$item->product_id', '$created_at','$item->contract_id'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }
    public function getAllTeacherByBranch(Request $request){
        $response = new Response();
        $cond = "";
        if($request->users_data->role_id==36){
            $cond = " AND t.user_id = ".$request->users_data->id;
        }
        $res = u::query("SELECT t.user_id AS id,t.ins_name AS `name` FROM teachers AS t 
            LEFT JOIN term_teacher_branch AS tt ON t.id=tt.teacher_id 
            WHERE tt.status=1 AND tt.branch_id=$request->branch_id $cond");

        return $response->formatResponse(APICode::SUCCESS, $res);
    }
    public function getAllClassByTeacher(Request $request){
        $response = new Response();
        $cond = $request->product_id ? ( $request->product_id ==1 ? " AND cl.product_id=1" :  " AND cl.product_id!=1") :"";
        $res = u::query("SELECT cl.id,cl.cls_name AS `class_name` FROM classes AS cl WHERE cl.teacher_id=$request->teacher_id AND cl.cls_iscancelled ='no' AND cl.cls_startdate <= CURRENT_DATE AND cl.cls_enddate>= CURRENT_DATE $cond");
        return $response->formatResponse(APICode::SUCCESS, $res);
    }
    public function getAllStudentByClass(Request $request){
        $response = new Response();
        $cond= $request->keyword ? " (s.name LIKE '%$request->keyword%' OR s.crm_id LIKE '%$request->keyword%' OR s.accounting_id LIKE '%$request->keyword%')" : "r.class_id=$request->class_id";
        // if($request->users_data->role_id == 36){
        //     $cond.=" AND r.teacher_id=".$request->users_data->id;
        // }
        $res = u::query("SELECT r.id,r.student_id,(SELECT name FROM  branches WHERE id=r.branch_id) AS branch_name,
                (SELECT cls_name FROM classes WHERE id=r.class_id) AS class_name,
                (SELECT full_name FROM users WHERE id=r.teacher_id) AS teacher_name,
                (SELECT name FROM `level` WHERE id=r.level_id) AS level_name,
                s.crm_id,s.accounting_id,s.name,s.note,
                (SELECT name FROM products WHERE id=r.product_id) AS product_name,
                (SELECT score FROM report_students WHERE student_id=r.student_id AND report_week_id< $request->report_week_id ORDER BY id DESC LIMIT 1) AS last_score,
                r.score,r.report_type,r.comment,r.suggestion,r.is_lock,
                (SELECT score_demo FROM report_student_demo WHERE student_id=s.id) AS score_demo
            FROM report_students AS r
                LEFT JOIN students AS s ON s.id=r.student_id 
            WHERE $cond AND r.report_week_id =$request->report_week_id ");

        return $response->formatResponse(APICode::SUCCESS, $res);
    }
    public function getReportWeeks(Request $request){
        $response = new Response();
        if($request->product_id != 2){
            $res = u::query("SELECT *, CONCAT('Tuần từ ',start_date,' đến ',end_date) AS title FROM report_weeks WHERE start_date <=CURRENT_DATE ORDER BY id DESC LIMIT 2");
        }else{
            $res = u::query("SELECT *, CONCAT('Năm ',`year`,' quý ',`group`) AS title FROM report_weeks WHERE start_date <= '".date('Y-m-d',time()-24*3600*7)."' AND end_date >='".date('Y-m-d',time()-24*3600*7)."' AND fix_group=1 ORDER BY id DESC LIMIT 1");
        }

        return $response->formatResponse(APICode::SUCCESS, $res);
    }
    public function updateData(Request $request){
        $response = new Response();
        if($request->demo){
            $report_student_demo = u::first("SELECT * FROM report_student_demo WHERE student_id=$request->student_id");
            if(!$report_student_demo){
                DB::table('report_student_demo')->insert([
                    'score_demo'=>$request->score_demo,
                    'student_id'=>$request->student_id,
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'updator_id'=>$request->users_data->id,
                ]);
            }else{
                DB::table('report_student_demo')->where('id',$report_student_demo->id)->update([
                    'score_demo'=>$request->score_demo,
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'updator_id'=>$request->users_data->id,
                ]);
            }
            $res= "ok";
        }else{ 
            $product_info = u::first("SELECT product_id FROM report_students WHERE id=$request->id");
            $res = DB::table('report_students')->where('id',$request->id)->update([
                'score'=>$request->score,
                'report_type'=>$this->genTypeStatus($request->score, $product_info->product_id),
                'comment'=>$request->comment,
                'suggestion'=>$request->suggestion,
                'updated_at'=>date('Y-m-d H:i:s'),
                'updator_id'=>$request->users_data->id,
                'is_lock'=>$request->action_is_lock ? 1 : 0
            ]);
        }

        return $response->formatResponse(APICode::SUCCESS, $res);
    }
    public function getReportMonth(Request $request){
        $response = new Response();
        if($request->product_id==2){
            $arr_group = [
                '1'=>'I',
                '2'=>'II',
                '3'=>'III',
                '4'=>'IV'
            ];
            $data = u::query("SELECT DISTINCT `year`,`month`,`group` FROM report_weeks WHERE fix_group=1");
            foreach($data AS $k=> $row){
                $start_date = u::first("SELECT `start_date` FROM report_weeks WHERE year='$row->year' AND `group`='$row->group' ORDER BY `start_date` ASC LIMIT 1");
                $end_date = u::first("SELECT `end_date` FROM report_weeks WHERE year='$row->year' AND `group`='$row->group' ORDER BY `end_date` DESC LIMIT 1");
                $data[$k]->id = $row->year."_".$row->group."_".$row->month;
                $data[$k]->title = "Năm ".$row->year." quý ". $arr_group[$row->group]." (Từ ngày $start_date->start_date đến ngày $end_date->end_date)";
            }
        }else{
            $arr_group = [
                '1'=>'I',
                '2'=>'II',
                '3'=>'III',
                '4'=>'IV'
            ];
            $data = u::query("SELECT DISTINCT `year`,`month`,`group` FROM report_weeks ");
            foreach($data AS $k=> $row){
                $start_date = u::first("SELECT `start_date` FROM report_weeks WHERE year='$row->year' AND `group`='$row->group' AND month='$row->month' ORDER BY `start_date` ASC LIMIT 1");
                $end_date = u::first("SELECT `end_date` FROM report_weeks WHERE year='$row->year' AND `group`='$row->group' AND month='$row->month' ORDER BY `end_date` DESC LIMIT 1");
                $data[$k]->id = $row->year."_".$row->group."_".$row->month;
                $data[$k]->title = "Năm ".$row->year." quý ". $arr_group[$row->group]." tháng ".$row->month ." (Từ ngày $start_date->start_date đến ngày $end_date->end_date)";
            }
        }

        return $response->formatResponse(APICode::SUCCESS, $data);
    }
    public function lockClass(Request $request){
        $response = new Response();
        $data = u::query("UPDATE report_students SET is_lock=1 WHERE class_id=$request->class_id AND report_week_id=$request->report_week_id");
        return $response->formatResponse(APICode::SUCCESS, $data);
    }
    public static function genTypeStatus($score, $product_id){
        if($product_id==1){
            if($score>=90){
                return 5;
            }elseif($score>=71 && $score <=89){
                return 1;
            }elseif($score>=66 && $score <=70){
                return 2;
            }elseif($score>=41 && $score <=65){
                return 6;
            }elseif($score>=25 && $score <=40){
                return 3;
            }else{
                return 4;
            }
        }else{
            if($score>=90){
                return 5;
            }elseif($score>=71 && $score <=89){
                return 1;
            }elseif($score>=56 && $score <=70){
                return 2;
            }elseif($score>=40 && $score <=55){
                return 6;
            }elseif($score>=15 && $score <=39){
                return 3;
            }else{
                return 4;
            }
        }
    } 
    public function getStudentByClass(Request $request){
        $response = new Response();
        $cond= $request->keyword ? " (s.name LIKE '%$request->keyword%' OR s.crm_id LIKE '%$request->keyword%' OR s.accounting_id LIKE '%$request->keyword%')" : "c.class_id=$request->class_id";
        $res = u::query("SELECT c.student_id,(SELECT name FROM  branches WHERE id=c.branch_id) AS branch_name,
                (SELECT cls_name FROM classes WHERE id=c.class_id) AS class_name,
                (SELECT `name` FROM products WHERE id=c.product_id) AS product_name, c.product_id,
                (SELECT u.full_name FROM classes AS cl LEFT JOIN users AS u ON u.id=cl.teacher_id WHERE c.class_id=cl.id) AS teacher_name,
                (SELECT le.name  FROM classes AS cl LEFT JOIN `level`AS le ON le.id=cl.level_id WHERE cl.id=c.class_id) AS level_name,
                s.crm_id,s.accounting_id,s.name,s.note,'' AS list_data_score , '' AS last_score,'' AS xep_loai
            FROM contracts AS c
                LEFT JOIN students AS s ON s.id=c.student_id 
            WHERE $cond AND c.status!=7 ");
        $max_input = 0;
        foreach($res AS $k=>$row){
            $res[$k]->list_data_score = u::query("SELECT * FROM report_student_score WHERE student_id=$row->student_id");
            $last_score_data = u::first("SELECT * FROM report_student_score WHERE student_id=$row->student_id AND (type_score=2 OR score>0) ORDER BY id DESC LIMIT 1");
            if($last_score_data){
                $res[$k]->last_score = $last_score_data->type_score ==1 ?  $last_score_data->score : 'K';
                $res[$k]->xep_loai = $last_score_data->xep_loai;
            }
            $res[$k]->list_data_score[] = (object)[
                'stt'=>count($res[$k]->list_data_score)+1,
                'score'=>'',
                'type_score'=>'1',
                'nhan_xet'=>'',
                'de_xuat'=>'',
            ];
            $max_input = $max_input < count($res[$k]->list_data_score) ? count($res[$k]->list_data_score) : $max_input;
        }

        return $response->formatResponse(APICode::SUCCESS, [
            'max_input'=>$max_input,
            'students'=>$res,
        ]);
    }
    public function updateDataReportScore(Request $request){
        $response = new Response();
        $stt= $request->stt;
        $student_info = (object)$request->student_info;
        $is_lock = $request->is_lock;
        if($is_lock){
            u::query("UPDATE report_student_score  AS r SET r.is_lock=1 
            WHERE r.student_id = ".$student_info->student_id."  AND (r.score IS NOT NULL AND r.score>0 OR r.type_score=2)");
        } else{
            $data_input = (object)$student_info->list_data_score[$stt-1];
            $data_score = u::first("SELECT id FROM report_student_score WHERE student_id=$student_info->student_id AND stt=$stt");
            $xep_loai = $data_input->type_score == '2' ? 'K' : self::genXepLoai($data_input->score,$student_info->product_id);
            if($data_score){
                DB::table('report_student_score')->where('id',$data_score->id)->update([
                    'score'=>$data_input->score,
                    'type_score'=>$data_input->type_score,
                    'nhan_xet'=>$data_input->nhan_xet,
                    'de_xuat'=>$data_input->de_xuat,
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'updator_id'=>$request->users_data->id,
                    'xep_loai'=>$xep_loai,
                ]);
            }else{
                DB::table('report_student_score')->insert([
                    'stt'=>$stt,
                    'score'=>$data_input->score,
                    'type_score'=>$data_input->type_score,
                    'nhan_xet'=>$data_input->nhan_xet,
                    'de_xuat'=>$data_input->de_xuat,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'creator_id'=>$request->users_data->id,
                    'student_id'=>$student_info->student_id,
                    'xep_loai'=>$xep_loai,
                ]);
                return $response->formatResponse(APICode::SUCCESS, "ok");
            }
        }
    }
    public static function genXepLoai($score,$product_id){
        $tmp = "K";
        if($product_id==1){
            if($score <= 24){
                $tmp = 'Yếu';
            } elseif( $score <= 40 && $score >= 25){
                $tmp = 'Trung bình';
            } elseif( $score <= 65 && $score >= 41){
                $tmp = 'Trung bình khá';
            } elseif( $score <= 70 && $score >= 66){
                $tmp = 'Khá';
            } elseif( $score <= 89 && $score >= 71){
                $tmp = 'Giỏi';
            } elseif( $score <= 100 && $score >= 90){
                $tmp = 'Xuất sắc';
            }else{
                $tmp = 'K';
            }
        }else{
            if($score <= 14){
                $tmp = 'Yếu';
            } elseif( $score <= 39 && $score >= 15){
                $tmp = 'Trung bình';
            } elseif( $score <= 55 && $score >= 40){
                $tmp = 'Trung bình khá';
            } elseif( $score <= 70 && $score >= 56){
                $tmp = 'Khá';
            } elseif( $score <= 89 && $score >= 71){
                $tmp = 'Giỏi';
            } elseif( $score <= 100 && $score >= 90){
                $tmp = 'Xuất sắc';
            }else{
                $tmp = 'K';
            }
        }
        return $tmp;
    }
    public function lockClassNew(Request $request){
        $response = new Response();
        $data = u::query("UPDATE report_student_score  AS r 
            LEFT JOIN contracts AS c ON r.student_id= c.student_id SET r.is_lock=1 
            WHERE c.class_id=$request->class_id  AND c.status!=7 AND (r.score IS NOT NULL AND r.score>0 OR r.type_score=2)");
        return $response->formatResponse(APICode::SUCCESS, $data);
    }
}
