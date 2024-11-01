<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ToolsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class FixData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixdata:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        // $data = u::query("SELECT id,response FROM log_external_request WHERE params NOT LIKE '%custom_fields%' AND url ='https://api2.caresoft.vn/cms/api/v1/contacts' ORDER BY id ");
        // foreach($data AS $log){
        //     // var_dump($row->response);die();
        //     $row=(object)json_decode(trim($log->response,'"'));
        //     if(isset($row->code) && $row->code=='ok'){
        //         $contact = $row->contact;
        //         $id_kh = (int)str_replace('KH.','',$contact->username);
        //         $sql = "SELECT st.id,st.gud_mobile1,st.source,st.note,
        //                 ( SELECT CONCAT( '\"id\": ', custom_field_id, ',\"value\": ', id ) FROM `cs_custom_fields` WHERE label = st.note ) custom_field_1,
        //                 ( SELECT CONCAT( '\"id\": ', custom_field_id, ',\"value\": ', id ) FROM `cs_custom_fields` WHERE label = st.source ) custom_field_2,
        //                 ( SELECT id FROM `cs_custom_fields` WHERE label = st.note ) note_id,
        //                 ( SELECT id FROM `cs_custom_fields` WHERE label = st.`source` ) source_id 
        //             FROM student_temp st
        //             WHERE st.id =$id_kh AND (SELECT count(id) FROM log_external_request WHERE params LIKE CONCAT('%',st.gud_mobile1,'%') AND method = 'PUT')=0";
        //         $data_student = u::first($sql);
        //         if($data_student && $data_student->custom_field_2){
        //             if($data_student->custom_field_1)
        //                 $params = '{"contact": {"phone_no":  "%phone_no%","username":  "%username%","custom_fields": [{field_1},{field_2}]}}';
        //             else
        //                 $params = '{"contact": {"phone_no":  "%phone_no%","username":  "%username%","custom_fields": [{field_2}]}}';
        //             $params = str_replace(["%phone_no%", "%username%","field_1","field_2"], [$data_student->gud_mobile1, "KH." . $data_student->id, $data_student->custom_field_1,$data_student->custom_field_2], $params);
                    
        //             $url = "https://api2.caresoft.vn/cms/api/v1/contacts/$contact->id";
        //             $header = [
        //                 "Content-Type: application/json",
        //                 "Authorization: Bearer 7ooSGVILKlDz8MI"
        //             ];
        //             $ch = curl_init();
        //             curl_setopt($ch, CURLOPT_URL,            $url );
        //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        //             curl_setopt($ch, CURLOPT_POSTFIELDS,     $params );
        //             curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);

        //             $res = null;
                
        //             $res = curl_exec ($ch);
                    
        //             u::query("INSERT INTO log_external_request (`url`,`method`,`params`,header,response,created_at) 
        //                 VALUES ('$url','PUT','".json_encode($params)."','".json_encode($header)."','".json_encode($res)."','".date('Y-m-d H:i:s')."')");
        //             ;   
        //         }
        //         echo $log->id."/"; 
        //     }
        // }
        $list_student = u::query("SELECT  id,name,gud_name1,gud_name2 FROM students ");
        foreach($list_student AS $student){
            $student_name = u::explodeName($student->name);
            $gud_name1 = u::explodeName($student->gud_name1);
            $gud_name2 = u::explodeName($student->gud_name2);
            u::query("UPDATE students SET firstname='$student_name->firstname',midname='$student_name->midname',lastname='$student_name->lastname',
            gud_firstname1='$gud_name1->firstname',gud_midname1='$gud_name1->midname',gud_lastname1='$gud_name1->lastname',
            gud_firstname2='$gud_name2->firstname',gud_midname2='$gud_name2->midname',gud_lastname2='$gud_name2->lastname' WHERE id=$student->id");
            echo "$student->id"."/";
        }
        return "ok";
    }
}
