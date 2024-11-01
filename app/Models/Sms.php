<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 3/13/2018
 * Time: 9:28 AM
 */

namespace App\Models;
use App\Models\APICode;
use App\Providers\CurlServiceProvider as curl;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\DB;

class Sms
{
    // dvkh@cmsedu.vn
    private $api_key = 'BF6EE459A0011379C729815971B451';
    private $secret_key = 'CFBFEBE8DBCDAADD52D4901329BF35';

    //marketing@cmsedu.vn
    // private $api_key = '42F400A8FCCF8A7A8800BECF88F681';
    // private $secret_key = '95CB6E1A65C60B2ECC0DD2884FD710';

    public function sendSms($phone,$content,$sms_type=2,$sms_log_id =0,$is_send=0,$creator_id=0){
        // if (APP_ENV === "staging")
        // return 200;
        $api_key = '';
        $secret_key = '';
        if($creator_id){
            $role_info = u::first("SELECT * FROM term_user_branches WHERE user_id=$creator_id AND status=1");
            $config_info = u::first("SELECT * FROM sms_config WHERE role_id = $role_info->role_id AND (branch_id=0 OR branch_id=$role_info->branch_id)");
            $api_key = $config_info ? $config_info->sms_api_key : '';
            $secret_key = $config_info ? $config_info->sms_secret_key : '';
        }else{
            $api_key = $this->api_key;
            $secret_key = $this->secret_key;
        }
        $url = 'http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_post_json/';
        $method = 'POST';
        $header = [];
        $params = [
            'ApiKey'=>$api_key,
            'SecretKey'=>$secret_key,
            'IsUnicode'=> 0,
            'SmsType'=>2,
            'Brandname'=>'CMSEDU',
            'Phone'=>$phone,
            'Content'=>u::convert_name($content),
        ];
        $res = NULL;
        
        DB::table('log_sms')->insert([
            'phone'=>$phone,
            'url' => $url,
            'method' => $method,
            'header' => json_encode($header),
            'params' => json_encode($params),
            'created_at' => date('Y-m-d H:i:s'),
            'response' => $res,
            'sms_log_id'=>$sms_log_id,
            'is_process'=> $is_send ? 1 :0,
            'creator_id'=>$creator_id,
        ]);

        return true;
    }
    public function  processSms(){
        $list_sms = u::query("SELECT * FROM log_sms WHERE `status`=0 AND `is_lock`=0 LIMIT 100");
        u::query("UPDATE log_sms SET `is_lock`=1 , add_queue_at ='".date('Y-m-d H:i:s')."' WHERE `status`=0 AND `is_lock`=0 LIMIT 100");
        foreach($list_sms AS $sms){
            $url=$sms->url;
            $method=$sms->method; 
            $header = json_decode($sms->header,true);
            $params = json_decode($sms->params,true);
            $phone = $sms->phone;

            $res = NULL;
            if(($sms->is_process==1 && APP_ENV === "product" ) || in_array($phone,array('0389941902','0966948868','0975657600'))){
                $res = curl::curl($url, $method, $header, $params);
            }
            u::query("UPDATE log_sms SET 
                    `is_lock`=0,
                    `status`=1,
                    send_at = '".date('Y-m-d H:i:s')."',
                    response = '$res'
                WHERE id= $sms->id");

        }
        return true;
    }
    public function sendSmsBirthday(){
        $date = date('Y-m-d');
        $data = u::query("SELECT s.gud_mobile1 FROM students AS s WHERE s.gud_birth_day1 ='$date' AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND status!=7)>0");
        foreach($data AS $row){
            $sms_phone=$row->gud_mobile1;
            /*CMS Edu chúc mừng sinh nhật Quý Phụ huynh. Chúc Anh/Chị sức khỏe, hạnh phúc và thành công. Trân trọng cảm ơn!*/
            $sms_content = "CMS Edu chuc mung sinh nhat Quy Phu huynh. Chuc Anh/Chi suc khoe, hanh phuc va thanh cong. Hotline CSKH 1800646805";
            $sms = new Sms();
            $sms->sendSms($sms_phone,$sms_content,2,0,1);
        }
        return true;
    }
    public function sendSmsRenew(){
        $date = date('Y-m-d');
        $renewed_date = date('Y-m-d',time()+24*3600*30);
        $data = u::query("SELECT DISTINCT s.name,s.gud_mobile1,(SELECT name FROM branches WHERE id=c.branch_id) AS branch_name 
            FROM contracts AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE c.renewed_date='$renewed_date' AND c.success_renewed_date IS NULL");
        foreach($data AS $row){
            $sms_phone=$row->gud_mobile1;
            /*CMS [Tên trung tâm] cảm ơn Quý PH tin tưởng thời gian vừa qua. Thông tin kỳ tái phí tiếp theo của [Tên học sinh] dự kiến vào [ngày tái phí]. Trân trọng!*/
            $sms_content = u::convert_name($row->branch_name)." cam on Quy PH tin tuong thoi gian vua qua. Thong bao ky tai phi tiep theo cua ".u::convert_name($row->name)." du kien vao ".$renewed_date.". Tran trong!";
            $sms = new Sms();
            $sms->sendSms($sms_phone,$sms_content);
        }
        return true;
    }
    public function sendSmsEndReserve(){
        $start_date = date('Y-m-d');
        $reserve_end_date = date('Y-m-d',time()+24*3600*3);
        $data = u::query("SELECT s.gud_mobile1,s.name,(SELECT name FROM branches WHERE id=r.branch_id) AS branch_name FROM reserves AS r LEFT JOIN students AS s ON s.id = r.student_id 
            WHERE r.start_date>= '$start_date' AND r.end_date ='$reserve_end_date' AND r.status=2");
        foreach($data AS $row){
            $sms_phone=$row->gud_mobile1;
            /*CMS Edu TB thông tin của [Tên học sinh] sẽ hết hạn bảo lưu vào ../../.... Quý PH vui lòng cập nhật thông tin và liên hệ với TVV CMS hoặc số hotline của [Tên trung tâm] để được hỗ trợ. Trân trọng!*/
            $sms_content = "CMS Edu TB thong tin bao luu cua ".u::convert_name($row->name)." se het han bao luu vao ngay ".$reserve_end_date.". Quy PH vui long cap nhat thong tin va lien he voi TVV CMS hoac so hotline cua ".u::convert_name($row->branch_name)." de duoc ho tro nhanh nhat. Tran trong!";
            $sms = new Sms();
            $sms->sendSms($sms_phone,$sms_content);
        }
        return true;
    }
}