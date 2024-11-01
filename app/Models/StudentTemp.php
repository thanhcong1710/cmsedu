<?php

namespace App\Models;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentTemp extends Model
{
    protected $table = 'student_temp';
    public $timestamps = false;

    public function getStudents($params)
    {
        $where = "";
        if (!empty($params['branch_id'])) {
            $where .= " and s.branch_id IN ({$params['branch_id']}) ";
        }
        else{
            $where .= " and s.branch_id IN ({$params['brd_id']}) ";
        }

        if (!empty($params['u_id'])) {
            if ($params['title'] === "EC")
                $params['ec_id'] = $params['u_id'];
            if (!empty($params['ec_id']) && $params['ec_id'] != 415){
                $where .= " and (s.ec_id = {$params['ec_id']}) ";
            }

        }

        if (isset($params['type']) && (int)$params['type'] >= 0) {
            if ($params['type']  < 2)
                $where .= " and s.type = {$params['type']} ";
            else
                $where .= " and s.std_id >0 ";
        }
        if (isset($params['name'])) {
            $searchKey = trim($params['name']);
            $where .= " and (s.name like '%$searchKey%' or s.gud_name1 like '%$searchKey%' or s.gud_name2 like '%$searchKey%' or 
                     s.gud_mobile1 = '$searchKey' or s.gud_mobile2 = '$searchKey') ";
        }
        if (isset($params['date']) && is_array($params['date']) && count($params['date']) === 2) {
            $date = $params['date'];
            $where .= " and s.created_at >= '{$date[0]}' and s.created_at <= '{$date[1]} 23:59:59' ";
        }

        if (isset($params['note']) && (int)$params['note'] >= 0) {
            $where .= " and s.note LIKE '%{$params['note']}%' ";
        }

        $addWhere = false;
        if (!empty($where)) {
            $addWhere = true;
            $where = "where 1 " . $where;
        }

        if (!empty($params['source'])) {
            if ($params['source'] == "NULL"){
                $where .= " and s.source IS NULL ";
            }
            else{
                $where .= " and s.source LIKE '%{$params['source']}%' ";
            }
        }
        if (!empty($params['import_cs'])) {
            if ($params['import_cs'] == 1){
                $where .= " and (s.import_to_call = 1  OR s.import_to_call = 3) AND s.type=0";
            }elseif ($params['import_cs'] == 2){
                $where .= " and s.import_to_call = 0 AND s.type=0";
            }
        }
        if (!empty($params['import_date'])) {
            $where .= " and s.import_date = '{$params['import_date']}' ";
        }
        if (!$addWhere){
            $where = "where 1 " . $where;
        }

        if (!empty($params['care_date'])){
            $where .= " AND s.id in (SELECT c.`std_temp_id` FROM `customer_care` c WHERE LEFT(c.`created_at`,10) = '{$params['care_date']}')";
        }
        $total = u::first("select count(1) as total from student_temp s $where")->total;

        $offset = isset($params['page']) && isset($params['limit']) ? ((int)$params['page'] - 1) * (int)$params['limit'] : 0;
        $limit = !empty($params['limit']) ? "limit {$offset},{$params['limit']}" : "";
        $query = "select s.*,
                        (select name from  provinces where id = s.province_id) as province_name,
                        (select name from  districts where id = s.district_id) as district_name,
                        (select name from  branches where id = s.branch_id) as branch_name,
                        (select full_name from  users where id = s.ec_id) as ec_name
                  from student_temp s $where ORDER BY s.id desc $limit";
        $data = u::query($query);
        $title = !empty($params['care_date']) ? "DANH SÁCH HỌC VIÊN CHĂM SÓC - ". date('d/m/Y',strtotime($params['care_date'])) : "DANH SÁCH HỌC VIÊN (DỮ LIỆU THÔ)";
        return ['data' => $data, 'total' => $total, 'title' =>$title];
    }

    private function checkMobileParentExists($phones)
    {
        if (empty($phones)) return false;
        $mobilePhones = array_map(function ($phone) {
            if (strlen($phone) <= 9) return $phone;

            return substr($phone, strlen($phone) - 9);
        }, $phones);
        $strPhone = implode(',', $mobilePhones);
        $query = "
            select gud_mobile1, gud_mobile2 from students where substr(gud_mobile1, CHAR_LENGTH(gud_mobile1) - 8) in ($strPhone) or substr(gud_mobile2, CHAR_LENGTH(gud_mobile2) - 8) in ($strPhone)
            union
            select gud_mobile1, gud_mobile2 from student_temp where substr(gud_mobile1, CHAR_LENGTH(gud_mobile1) - 8) in ($strPhone) or substr(gud_mobile2, CHAR_LENGTH(gud_mobile2) - 8) in ($strPhone)
            limit 1
        ";
        $data = DB::select(DB::raw($query));
        return !empty($data);
    }

    public function addNew($phoneNumber = null){
        $data = [
            'branch_id' => 100,
            'name' => "KH $phoneNumber",
            'gender' => "F",
            'date_of_birth' => null,
            'ec_id' => 415,
            'date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
            'gud_name1' => "KH $phoneNumber",
            'gud_date_of_birth1' => null,
            'gud_mobile1' => "$phoneNumber",
            'source' => "auto import",
            'creator_id' => 1,
            'import_to_call' => 1,
            'type' => 0
        ];
        return StudentTemp::insertGetId($data);
    }

    public function saveStudent($item, $userData)
    {
        if (isset($item["action"]) && $item["action"] == 'add'){
            $mobile1  = u::get($item, 'gud_mobile1');
            $count = u::first("SELECT count(s.id) as total from student_temp s WHERE s.gud_mobile1 = '$mobile1' AND s.type = 0");
            if ($count->total == 1){
                return ['error_code'=>1,'message'=>'Có lỗi xảy ra, Số điện thoại phụ huynh đã có trên hệ thống.'];
            }
            /*
            SELECT p.`gud_mobile1` FROM student_temp p WHERE p.`created_at` <= DATE_SUB(NOW(), INTERVAL 14 DAY)
            AND p.`gud_mobile1` = '0963107333' AND (SELECT COUNT(c.id) FROM `customer_care` c WHERE c.`std_temp_id` = p.id) = 0;
            SELECT c.`stored_date` FROM `customer_care` c
            WHERE c.`std_temp_id` IN (SELECT p.id FROM student_temp p WHERE p.`gud_mobile1` = '0963107333') ORDER BY c.id DESC LIMIT 1;
            */
        }

        $gudMobile1 = u::convertMobileNumber(u::get($item, 'gud_mobile1'));
        $gudMobile2 = u::convertMobileNumber(u::get($item, 'gud_mobile2'));

        if (strlen($gudMobile1) > 10)
            $gudMobile1 = substr($gudMobile1,0,10);
        if (strlen($gudMobile2) > 10)
            $gudMobile2 = substr($gudMobile2,0,10);
        if (substr($gudMobile1,0,3) == "043"){
            $gudMobile1 = "0243".substr($gudMobile1,3,strlen($gudMobile1));
        }

        //$phones = array_filter([$gudMobile1, $gudMobile2]);
        $type = u::get($item, 'message.warning') ? 1 : 0;
        ///self::checkMobileParentExists($phones) ? 1 : null;
        $gudDateOfBirth1 = u::get($item, 'gud_date_of_birth1');
        $gudDateOfBirth2 = u::get($item, 'gud_date_of_birth2');
        $data = [
            'branch_id' => u::get($item, 'branch_id'),
            'name' => u::get($item, 'name'),
            'gender' => (int)u::get($item, 'gender') === 1 ? "M" : "F",
            'date_of_birth' => u::get($item, 'birthday'),
            'ec_id' => u::get($item, 'ec_id'),
            'date' => u::get($item, 'date'),
            'gud_name1' => u::get($item, 'gud_name1'),
            'gud_date_of_birth1' => !empty($gudDateOfBirth1) ? $gudDateOfBirth1 : null,
            'gud_mobile1' => $gudMobile1,
            'gud_job1' => u::get($item, 'gud_job1'),
            'gud_email1' => u::get($item, 'gud_email1'),
            'gud_name2' => u::get($item, 'gud_name2'),
            'gud_date_of_birth2' => !empty($gudDateOfBirth2) ? $gudDateOfBirth2 : null,
            'gud_mobile2' => $gudMobile2,
            'gud_job2' => u::get($item, 'gud_job2'),
            'gud_email2' => u::get($item, 'gud_email2'),
            'address' => u::get($item, 'address'),
            'province_id' => u::get($item, 'province_id'),
            'home_phone' => u::get($item, 'home_phone'),
            'district_id' => u::get($item, 'district_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => $userData->id,
            'note' => u::get($item, 'note'),
            'source' => u::get($item, 'source'),
            'type' => $type
        ];
        StudentTemp::insertGetId($data);
        return ['error_code'=>0,'message'=>''];
    }

    public function updateStudent($item, $userData)
    {
        $mobile1  = u::get($item, 'gud_mobile1');
        $id  = u::get($item, 'id');
        $count = u::first("SELECT count(s.id) as total from student_temp s WHERE s.gud_mobile1 = '$mobile1' and s.id != $id AND s.type = 0");
        
        if ($count->total == 1){
            return ['error_code'=>1,'message'=>'Có lỗi xảy ra, Số điện thoại phụ huynh đã có trên hệ thống.'];
        }
        $phones = [u::get($item, 'gud_mobile1')];
        if (u::get($item, 'gud_mobile2')){
            array_push($phones, u::get($item, 'gud_mobile2'));
        }
        $strPhone = implode(',', $phones);
        $query = "
            select gud_mobile1, gud_mobile2 from students where substr(gud_mobile1, CHAR_LENGTH(gud_mobile1) - 8) in ($strPhone)
                                                             or substr(gud_mobile2, CHAR_LENGTH(gud_mobile2) - 8) in ($strPhone)
            union
            select gud_mobile1, gud_mobile2 from student_temp where substr(gud_mobile1, CHAR_LENGTH(gud_mobile1) - 8) in ($strPhone)
                                                                 or substr(gud_mobile2, CHAR_LENGTH(gud_mobile2) - 8) in ($strPhone)
        ";

        $unique = DB::select(DB::raw($query));
        $gudDateOfBirth1 = u::get($item, 'gud_date_of_birth1');
        $gudDateOfBirth2 = u::get($item, 'gud_date_of_birth2');
        $dateOfBirth = u::get($item, 'date_of_birth');
        $student = StudentTemp::find($item['id']);
        $student->branch_id = u::get($item, 'branch_id');
        $student->name = u::get($item, 'name');
        $student->gender = (int)u::get($item, 'gender') === 1 ? "M" : "F";
        $student->date_of_birth = !empty($dateOfBirth) ? $dateOfBirth : null;
        $student->ec_id = u::get($item, 'ec_id');
        $student->date = u::get($item, 'date');
        $student->gud_name1 = u::get($item, 'gud_name1');
        $student->gud_date_of_birth1 = !empty($gudDateOfBirth1) ? $gudDateOfBirth1 : null;
        $student->gud_mobile1 = u::get($item, 'gud_mobile1');
        $student->gud_job1 = u::get($item, 'gud_job1');
        $student->gud_email1 = u::get($item, 'gud_email1');
        $student->gud_name2 = u::get($item, 'gud_name2');
        $student->gud_date_of_birth2 = !empty($gudDateOfBirth2) ? $gudDateOfBirth2 : null;
        $student->gud_mobile2 = u::get($item, 'gud_mobile2');
        $student->gud_job2 = u::get($item, 'gud_job2');
        $student->gud_email2 = u::get($item, 'gud_email2');
        $student->address = u::get($item, 'address');
        $student->province_id = u::get($item, 'province_id');
        $student->home_phone = u::get($item, 'home_phone');
        $student->district_id = u::get($item, 'district_id');
        $student->updated_at = date('Y-m-d');
        $student->editor_id = $userData->id;
        $student->note = u::get($item, 'note');
        $student->source = u::get($item, 'source');
        if (!$unique){
            $student->type = 0;
        }

        $student->save();
        return ['error_code'=>0,'message'=>'success'];
    }

    public static function getStudentById($id){
        $query = "select t.*,
                  (select name from  branches where id = t.branch_id) as branch_name
                  from  student_temp  t where t.id = $id";
        $data = u::query($query);

        $care = "select c.*,
                  (select title from contact_quality where id = c.contact_quality_id) as quality_name,
                  (select score from contact_quality where id = c.contact_quality_id) as score,
                  (select full_name from users where id = c.creator_id) as full_name
                  from customer_care c where c.std_temp_id = $id order by c.id desc";
        $dateCare= u::query($care);
        if ($data)
            return ['student'=>$data[0],'care' =>$dateCare];
        else
            return [];
    }

    public static function destroyCareTemp($stdTempId = 0){
        DB::table('customer_care')->where('std_temp_id',$stdTempId)->delete();
    }

    protected function countTodayImported(){
        $date = date('Y-m-d');
        $sql ="SELECT COUNT(id) AS total FROM student_temp WHERE import_to_call = 1 AND import_date = '$date'";
        $data = u::query($sql);
        $total = 0;
        if ($data){
            $total = $data[0]->total;
        }
        return $total;
    }

    public function createContactCareSoft(){
        $sql = "SELECT st.id,st.gud_mobile1,st.source,st.note,
                ( SELECT CONCAT( '\"id\": ', custom_field_id, ',\"value\": ', id ) FROM `cs_custom_fields` WHERE label = st.note LIMIT 1) custom_field_1,
                ( SELECT CONCAT( '\"id\": ', custom_field_id, ',\"value\": ', id ) FROM `cs_custom_fields` WHERE label = st.source  LIMIT 1) custom_field_2,
                ( SELECT id FROM `cs_custom_fields` WHERE label = st.note  LIMIT 1) note_id,
                ( SELECT id FROM `cs_custom_fields` WHERE label = st.`source`  LIMIT 1) source_id 
            FROM student_temp st
            WHERE st.type != 1 AND st.import_to_call = 0 AND st.push_caresoft IN(1,2) AND st.branch_id IN (12,100)
            GROUP BY st.gud_mobile1 ORDER BY st.push_caresoft DESC ,st.id ASC LIMIT 50";
        $data = u::query($sql);
        if ($data){
            foreach ($data as $item) {
                if($item->custom_field_1)
                    $params = '{"contact": {"phone_no":  "%phone_no%","username":  "%username%","custom_fields": [{field_1},{field_2}]}}';
                else
                    $params = '{"contact": {"phone_no":  "%phone_no%","username":  "%username%","custom_fields": [{field_2}]}}';
                $params = str_replace(["%phone_no%", "%username%","field_1","field_2"], [$item->gud_mobile1, "KH." . $item->id, $item->custom_field_1,$item->custom_field_2], $params);
                self::careSoftApiContact($params, $item->id, $item->gud_mobile1);
            }
        }
    }

    protected function careSoftApiContact($params, $id = 0, $phone = null){
        if (strlen($phone) <10){
            $update = "UPDATE student_temp set type = 1 where gud_mobile1 = '{$phone}' and branch_id IN (12,100)";
            u::query($update);
        }
        else{
            $url = "https://api2.caresoft.vn/cms/api/v1/contacts";
            $header = [
                "Content-Type: application/json",
                "Authorization: Bearer 7ooSGVILKlDz8MI"
            ];
            $date = date('Y-m-d');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,            $url );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt($ch, CURLOPT_POST,           1 );
            curl_setopt($ch, CURLOPT_POSTFIELDS,     $params );
            curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);

            $res = null;
            try {
                $res = curl_exec ($ch);
                if ($res) {
                    $resJson = json_decode($res);
                    if (isset($resJson->code)){
                        if ($resJson->code == "ok"){
                            $update = "UPDATE student_temp set import_to_call = 1, import_date = '$date' where gud_mobile1 = '{$phone}' and branch_id IN (12,100)";
                            u::query($update);
                        }
                        else{
                            $update = "UPDATE student_temp set import_to_call = 3, import_date = '$date' where gud_mobile1 = '{$phone}' and branch_id IN (12,100)";
                            u::query($update);
                        }
                    }
                }
                u::first("INSERT INTO log_external_request (`url`,`method`,`params`,header,response,created_at) 
                    VALUES ('$url','POST','".json_encode($params)."','".json_encode($header)."','".json_encode($res)."','".date('Y-m-d H:i:s')."')");
                // Log::info("PARAMS:".$params);
                // Log::info("CARE_SOFT_API:".$res);
            } catch (\Exception $exception) {
                // Log::info("Exception:".$exception);
                exit;
            }
        }
    }

}
