<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Response;
use App\Models\APICode;
use App\Models\Sms;
use App\Providers\UtilityServiceProvider as u;

class SmsController extends Controller
{
    public function getStudent(Request $request)
    {
        $response = new Response();
        $data = NULL;
        $code = APICode::SUCCESS;
        if ($request->type == 1) {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-d');
            $where = " AND s.id IS NOT NULL ";
            if($request->branch_id){
                $where .= " AND c.branch_id = $request->branch_id";
            }
            if($request->semester_id){
                $where .= " AND cls.semester_id = $request->semester_id";
            }
            if($request->program_id){
                $where .= " AND c.program_id = $request->program_id";
            }
            if($request->class_id){
                $where .= " AND c.class_id = $request->class_id";
            }
            $resp_total = "SELECT
                            count(DISTINCT s.id) as total
                        FROM
                            contracts c
                            LEFT JOIN students s ON c.student_id = s.id
                            LEFT JOIN classes cls on c.class_id = cls.id
                            LEFT JOIN term_student_user AS t ON t.student_id = c.student_id
                        WHERE
                            c.type > 0
                            AND c.status != 7
                            AND (
                                    (
                                        c.class_id IS NOT NULL
                                        AND c.type != 86 AND c.type !=6
                                        AND c.enrolment_start_date <= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate DESC LIMIT 1 )
                                        AND c.enrolment_last_date >= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate DESC LIMIT 1 )
                                        -- AND c.enrolment_last_date >='$start_date'
                                    )
                                OR  (
                                        c.class_id IS NOT NULL
                                        AND (c.type = 86 OR c.type =6)
                                        AND c.enrolment_start_date <= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate >= '$end_date' AND `status`=1 ORDER BY cjrn_classdate ASC LIMIT 1 )
                                        AND c.enrolment_last_date >= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate ASC LIMIT 1 )
                                        -- AND c.enrolment_last_date >='$start_date'
                                    )
                            )
                            AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND is_reserved=1 AND `start_date` <= '$end_date' AND `end_date`>='$end_date' AND `status`=2) =0
                            AND (SELECT count(id) FROM contracts WHERE student_id = c.student_id AND `type`>0 AND `status`!=7 AND class_id IS NOT NULL AND id!=c.id) = 0
                            AND (c.debt_amount = 0 OR c.foced_is_full_fee_active =1) AND c.foced_is_full_fee_active!=2 AND s.status>0
                            $where
                ";
            $resp_data = "SELECT
                            DISTINCT c.student_id, s.crm_id, s.accounting_id, s.name, s.gud_name1,s.gud_mobile1,s.gud_email1,c.branch_id
                        FROM
                            contracts c
                            LEFT JOIN students s ON c.student_id = s.id
                            LEFT JOIN classes cls on c.class_id = cls.id
                            LEFT JOIN term_student_user AS t ON t.student_id = c.student_id
                        WHERE
                            c.type > 0
                            AND c.status != 7
                            AND (
                                    (
                                        c.class_id IS NOT NULL
                                        AND c.type != 86 AND c.type !=6
                                        AND c.enrolment_start_date <= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate DESC LIMIT 1 )
                                        AND c.enrolment_last_date >= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate DESC LIMIT 1 )
                                        -- AND c.enrolment_last_date >='$start_date'
                                    )
                                OR  (
                                        c.class_id IS NOT NULL
                                        AND (c.type = 86 OR c.type =6)
                                        AND c.enrolment_start_date <= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate >= '$end_date' AND `status`=1 ORDER BY cjrn_classdate ASC LIMIT 1 )
                                        AND c.enrolment_last_date >= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate ASC LIMIT 1 )
                                        -- AND c.enrolment_last_date >='$start_date'
                                    )
                            )
                            AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND is_reserved=1 AND `start_date` <= '$end_date' AND `end_date`>='$end_date' AND `status`=2) =0
                            AND (SELECT count(id) FROM contracts WHERE student_id = c.student_id AND `type`>0 AND `status`!=7 AND class_id IS NOT NULL AND id!=c.id) = 0
                            AND (c.debt_amount = 0 OR c.foced_is_full_fee_active =1) AND c.foced_is_full_fee_active!=2 AND s.status>0
                            $where LIMIT 100
                ";
        }elseif($request->type == 2){
            $where = "";
            if($request->branch_id){
                $where .= " AND s.branch_id = $request->branch_id";
            }
            if($request->startDateCheckin){
                $where .= " AND DATE_FORMAT( s.created_at, '%Y-%m-%d' ) >= '$request->startDateCheckin' ";
            }
            if($request->endDateCheckin){
                $where .= " AND DATE_FORMAT( s.created_at, '%Y-%m-%d' ) <= '$request->endDateCheckin' ";
            }
            $resp_total = "SELECT
                            count(DISTINCT s.id) as total
                        FROM
                            students s 
                        WHERE
                            s.status = 0
                            $where
                ";
            $resp_data = "SELECT
                            DISTINCT s.id AS student_id, s.crm_id, s.accounting_id, s.name, s.gud_name1,s.gud_mobile1,s.gud_email1
                        FROM
                            students s 
                        WHERE
                            s.status = 0
                            $where LIMIT 100
                ";
        }
        $data = (object)array(
            'list'=>u::query($resp_data),
            'total'=>u::first($resp_total)->total
        );
        return $response->formatResponse($code, $data);
    }
    public function getAllSmsTemplate(){
        $response = new Response();
        $data = NULL;
        $code = APICode::SUCCESS;
        $data = u::query("SELECT * FROM sms_template WHERE type=0 AND status=1");
        return $response->formatResponse($code, $data);
    }
    public function send(Request $request){
        $response = new Response();
        $data = NULL;
        $code = APICode::SUCCESS;
        $modal = (object)$request->modal;
        $params_search = (object)$request->params_search;
        $students = (object)$request->students;
        $is_all = $modal->is_all==1 ? 1 :0;
        $content ="";
        if($modal->sms_type==1){
            $content = $modal->sms_content;
        }elseif($modal->sms_type==2){
            $sms_template_info = u::first("SELECT * FROM sms_template WHERE id= $modal->sms_template_id");
            $content = $sms_template_info->content;
        }

        if($modal->is_all ==1){
            $students = self::getStudentSend($params_search);
        }
        $id_sms_campaign = DB::table('sms_campaign')->insertGetId([   
            'content' => $content,
            'count'=>$modal->count,
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>$request->users_data->id,
            'is_all'=>$is_all,
            'params_search'=>json_encode( $params_search),
            'sms_template_id'=>$modal->sms_type==2 ? (int)$modal->sms_template_id :0,
            'list_student'=>json_encode( $students),
            'status'=>1
        ]);
        $arr_check = [];
        $arr_phone_gdtt_branch = self::getPhoneGDTT();
        foreach($students AS $student){
            $student = (object)$student;
            if(isset($student->checked) && $student->checked){
                if($modal->sms_type==2){
                    $student_content = self::getContentSms($content,$student);
                    $sms_type = 2 ;//Tin nhắn chăm sóc khách hàng
                }else{
                    $student_content = $content;
                    $sms_type = 1 ;//Tin nhắn quảng cáo
                }
                $sms_log_id = DB::table('sms_logs')->insertGetId([   
                    'sms_campaign_id' => $id_sms_campaign,
                    'student_id'=>$student->student_id,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'creator_id'=>$request->users_data->id,
                    'email'=>$student->gud_email1,
                    'phone'=>$student->gud_mobile1,
                    'content'=> $student_content,
                    'status'=>1
                ]);
                $sms =new Sms();
                $sms->sendSms($student->gud_mobile1,$student_content,$sms_type,$sms_log_id,1);
                if(!in_array($student->branch_id,$arr_check)){
                    $sms->sendSms($arr_phone_gdtt_branch[$student->branch_id],$student_content,$sms_type,0,1);
                    array_push($arr_check,$student->branch_id);
                }
            }
        }
        return $response->formatResponse($code, $data);
    }
    public static function getStudentSend($params){
        $data = NULL;
        if ($params->type == 1) {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-d');
            $where = " AND s.id IS NOT NULL ";
            if($params->branch_id){
                $where .= " AND c.branch_id = $params->branch_id";
            }
            if($params->semester_id){
                $where .= " AND cls.semester_id = $params->semester_id";
            }
            if($params->program_id){
                $where .= " AND c.program_id = $params->program_id";
            }
            if($params->class_id){
                $where .= " AND c.class_id = $params->class_id";
            }
            $resp_data = "SELECT
                            DISTINCT c.student_id, s.crm_id, s.accounting_id, s.name, s.gud_name1,s.gud_mobile1,s.gud_email1,1 AS checked,
                            (SELECT name FROM branches WHERE id= c.branch_id) AS branch_name,
                            (SELECT hotline FROM branches WHERE id= c.branch_id) AS branch_hotline,c.branch_id
                        FROM
                            contracts c
                            LEFT JOIN students s ON c.student_id = s.id
                            LEFT JOIN classes cls on c.class_id = cls.id
                            LEFT JOIN term_student_user AS t ON t.student_id = c.student_id
                        WHERE
                            c.type > 0
                            AND c.status != 7
                            AND (
                                    (
                                        c.class_id IS NOT NULL
                                        AND c.type != 86 AND c.type !=6
                                        AND c.enrolment_start_date <= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate DESC LIMIT 1 )
                                        AND c.enrolment_last_date >= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate DESC LIMIT 1 )
                                        -- AND c.enrolment_last_date >='$start_date'
                                    )
                                OR  (
                                        c.class_id IS NOT NULL
                                        AND (c.type = 86 OR c.type =6)
                                        AND c.enrolment_start_date <= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate >= '$end_date' AND `status`=1 ORDER BY cjrn_classdate ASC LIMIT 1 )
                                        AND c.enrolment_last_date >= ( SELECT cjrn_classdate FROM schedules WHERE class_id = c.class_id AND cjrn_classdate <= '$end_date' AND `status`=1 ORDER BY cjrn_classdate ASC LIMIT 1 )
                                        -- AND c.enrolment_last_date >='$start_date'
                                    )
                            )
                            AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND is_reserved=1 AND `start_date` <= '$end_date' AND `end_date`>='$end_date' AND `status`=2) =0
                            AND (SELECT count(id) FROM contracts WHERE student_id = c.student_id AND `type`>0 AND `status`!=7 AND class_id IS NOT NULL AND id!=c.id) = 0
                            AND (c.debt_amount = 0 OR c.foced_is_full_fee_active =1) AND c.foced_is_full_fee_active!=2 AND s.status>0
                            $where 
                ";
            $data = u::query($resp_data);
        }elseif ($params->type == 2) {
            $where = "";
            if($params->branch_id){
                $where .= " AND s.branch_id = $params->branch_id";
            }
            if($params->startDateCheckin){
                $where .= " AND DATE_FORMAT( s.created_at, '%Y-%m-%d' ) >= '$params->startDateCheckin' ";
            }
            if($params->endDateCheckin){
                $where .= " AND DATE_FORMAT( s.created_at, '%Y-%m-%d' ) <= '$params->endDateCheckin' ";
            }
            $resp_data = "SELECT
                            DISTINCT s.id AS student_id, s.crm_id, s.accounting_id, s.name, s.gud_name1,s.gud_mobile1,s.gud_email1,1 AS checked,
                            (SELECT name FROM branches WHERE id= c.branch_id) AS branch_name,
                            (SELECT hotline FROM branches WHERE id= c.branch_id) AS branch_hotline
                        FROM
                            students s 
                        WHERE
                            s.status = 0
                            $where 
                ";
            $data = u::query($resp_data);
        }
        return $data;
    }
    public static function getContentSms($text,$info){
        $text = str_replace('{ten_hocsinh}',u::convert_name($info->name),$text);
        $text = str_replace('{trungtam_ten}',u::convert_name($info->branch_name),$text);
        $text = str_replace('{trungtam_hotline}',u::convert_name($info->branch_hotline),$text);
        return $text;
    }
    public function listsTemplate(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($request->users_data) {
            $code = APICode::SUCCESS;
            $query_information = self::getListsTemplate($request);
            $lists = $query_information['lists'];
            $cpage = $query_information['page'];
            $limit = $query_information['limit'];
            $total = $query_information['total'];
            $lpage = $total <= $limit ? 1 : (int) round(ceil($total / $limit));
            $ppage = $cpage > 0 ? $cpage - 1 : 0;
            $npage = $cpage < $lpage ? $cpage + 1 : $lpage;

            $data['lists'] = $lists;
            $data['message'] = 'successful';
            $data['pagination'] = [
                'spage' => 1,
                'ppage' => $ppage,
                'npage' => $npage,
                'lpage' => $lpage,
                'cpage' => $cpage,
                'limit' => $limit,
                'total' => $total,
            ];
        }
        return $response->formatResponse($code, $data);
    }
    private function getListsTemplate($request, $page = 1, $limit = 20, $filter = null)
    {
        $selected_page = $request->page ? (int) $request->page : 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $where = '';
        if ($request->input('status') != -1) {
            $where .= " and p.status = " . $request->input('status');
        }
        if ($request->input('keyword') != '') {
            $where .= " and (p.title like '%" . $request->input('keyword') . "%' )";
        }
        $query = "SELECT
                            p.*
                        FROM
                            sms_template p
                        WHERE
                            1 = 1 $where
                        ORDER BY p.id DESC $limitation ";

        $count_query = "SELECT COUNT(p.id) as total FROM sms_template p WHERE 1 = 1 $where ";

        $total = DB::select(DB::raw($count_query));
        $lists = DB::select(DB::raw($query));

        return [
            'lists' => $lists,
            'total' => $total[0]->total,
            'limit' => $limit,
            'page' => $page,
        ];
    }
    public function addTempalte(Request $request){
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();
        DB::table('sms_template')->insertGetId([   
            'title' => $request->title,
            'content'=>$request->content,
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>$request->users_data->id,
            'type'=>$request->type,
            'status'=>1
        ]);
        return $response->formatResponse($code, $data);
    }
    public function detailTemplate(Request $request,$template_id){
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();
        $data = u::first("SELECT * FROM sms_template WHERE id = $template_id");
        return $response->formatResponse($code, $data);
    }
    public function updateTempalte(Request $request,$template_id){
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();
        DB::table('sms_template')->where('id',$template_id)->update([   
            'title' => $request->title,
            'content'=>$request->content,
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>$request->users_data->id,
            'type'=>$request->type,
            'status'=>1
        ]);
        return $response->formatResponse($code, $data);
    }
    public function listsCampaign(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($request->users_data) {
            $code = APICode::SUCCESS;
            $query_information = self::getListsCampaign($request);
            $lists = $query_information['lists'];
            $cpage = $query_information['page'];
            $limit = $query_information['limit'];
            $total = $query_information['total'];
            $lpage = $total <= $limit ? 1 : (int) round(ceil($total / $limit));
            $ppage = $cpage > 0 ? $cpage - 1 : 0;
            $npage = $cpage < $lpage ? $cpage + 1 : $lpage;

            $data['lists'] = $lists;
            $data['message'] = 'successful';
            $data['pagination'] = [
                'spage' => 1,
                'ppage' => $ppage,
                'npage' => $npage,
                'lpage' => $lpage,
                'cpage' => $cpage,
                'limit' => $limit,
                'total' => $total,
            ];
        }
        return $response->formatResponse($code, $data);
    }
    private function getListsCampaign($request, $page = 1, $limit = 20, $filter = null)
    {
        $selected_page = $request->page ? (int) $request->page : 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $where = '';
        if ($request->input('keyword') != '') {
            $where .= " and (c.content like '%" . $request->input('keyword') . "%' )";
        }
        $query = "SELECT
                            c.*, u.full_name AS fullname,u.hrm_id 
                        FROM
                            sms_campaign c
                            LEFT JOIN users AS u ON c.creator_id =u.id
                        WHERE
                            1 = 1 $where
                        ORDER BY c.id DESC $limitation ";

        $count_query = "SELECT COUNT(c.id) as total FROM sms_campaign c WHERE 1 = 1 $where ";

        $total = DB::select(DB::raw($count_query));
        $lists = DB::select(DB::raw($query));

        return [
            'lists' => $lists,
            'total' => $total[0]->total,
            'limit' => $limit,
            'page' => $page,
        ];
    }
    public function getPhoneGDTT(){
        return array(
            '1'=>'0987805066',
            '2'=>'0904868512',
            '3'=>'0989779819',
            '4'=>'0904018501',
            '5'=>'0989779819',
            '6'=>'0868431333',
            '7'=>'0904868512',
            '8'=>'0977060646',
            '9'=>'0912006163',
            '10'=>'0982997489',
            '12'=>'0982997489',
            '13'=>'0982997489',
        );
    }
}
