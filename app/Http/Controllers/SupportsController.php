<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 12/13/2019
 * Time: 10:37 AM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\UtilityServiceProvider as u;
use App\Models\Response;
use App\Services\SupportService;
use App\Services\LogService;
use Illuminate\Support\Facades\Log;
use App\Models\StudentTemp;
use App\Models\CustomerCare;
use App\Models\LogCaresoftRequest;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class SupportsController extends Controller
{
    public function changeEnrolmentStartDate(Request $request)
    {
        $reserves_session = 0;
        $response = new Response();
        $studentId = $request->student_id;
        $pending = "SELECT \"Chuyển lớp\" AS action_name,
                    (SELECT NAME FROM students WHERE id = c.`student_id`) AS student_name
                    FROM `class_transfer` c WHERE c.`final_approved_at` IS NULL AND c.`student_id` = $studentId AND c.from_branch_id = c.to_branch_id
                    UNION
                    SELECT \"Chuyển Trung Tâm\" AS action_name,
                    (SELECT NAME FROM students WHERE id = c.`student_id`) AS student_name
                    FROM `class_transfer` c WHERE c.`final_approved_at` IS NULL AND c.`student_id` = $studentId AND c.from_branch_id != c.to_branch_id
                    UNION
                    SELECT \"Chuyển Phí\" AS action_name,(SELECT NAME FROM students WHERE id = t.`from_student_id`) AS student_name
                    FROM `tuition_transfer` t WHERE t.`final_approved_at` IS NULL AND t.`from_student_id` = $studentId
                    UNION
                    SELECT \"Bảo lưu\" AS action_name,(SELECT NAME FROM students WHERE id = r.`student_id`) AS student_name
                    FROM `reserves` r WHERE r.`final_approved_at` IS NULL AND r.`student_id` = $studentId
                    UNION
                    SELECT \"Rút phí\" AS action_name,(SELECT NAME FROM students WHERE id = r.`student_id`) AS student_name
                    FROM `withdrawal_fees` r WHERE r.`approved_at` IS NULL AND r.`student_id` = $studentId";
        $pendingData = u::query($pending);
        $msg = 'Học viên ';
        if ($pendingData){
            $msg .= $pendingData[0]->student_name .". Đang CHỜ DUYỆT";
            foreach ($pendingData as $pd){
                $msg .= " ".$pd->action_name.",";
            }
            $msg .= " Không thể thay đổi ngày bắt đầu học.";
            return $response->formatResponse(200, ['code'=>1,'msg'=>$msg]);
        }

        $startDate = $request->start_date;
        $contractId = $request->contract_id;
        $reserves = "SELECT c.id, c.`student_id`,c.`enrolment_start_date`, c.`enrolment_last_date`,
                    r.`final_approved_at`,r.`status`,r.`start_date`,r.`end_date`, r.`session`, r.`is_reserved`,IF(r.`is_reserved` = 0,'ko giu cho','giu cho') AS reserved_name
                    FROM `contracts` c LEFT JOIN `reserves` r
                    ON r.`student_id` = c.`student_id`
        AND r.`start_date` >= c.`enrolment_start_date` AND r.`final_approved_at` != '' AND r.`end_date` <= c.`enrolment_last_date` AND r.is_reserved = 1
                    WHERE c.`id` = $contractId";
        $dataReserves = u::first($reserves);
        if ($dataReserves){
            $reserves_session = $dataReserves->session;
            $reserves_start_date = $dataReserves->start_date;
            if ($reserves_session > 0){
                if ($startDate > $reserves_start_date){
                    return $response->formatResponse(200, ['code'=>1,'msg'=>'Ngày bắt đầu học lớn hơn Ngày Bảo lưu của học sinh đã được duyệt.']);
                }
            }
        }

        if (!empty($request->start_date)) {
            $holiday =  u::getPublicHolidayAll($request->branch_id);
            $nextTime = strtotime($request->start_date) + 60*60*24*750;
            $inDate = date('Y-m-d', $nextTime);
            $days =  u::getDaysBetweenTwoDate($startDate, $inDate, $request->class_day); # 2x Friday
            $daysNew = [];
            foreach($days as $day){
                if (!in_array($day,$holiday)){
                    $daysNew[] = $day;
                }
            }
            $lastDay = null;
            if ($request->debt_amount == 0){
                $key = (int)$request->total_session - 1;
                $key = (int)$reserves_session + $key;
                //$key = $request->real_sessions - 1;
                $lastDay = !empty($daysNew[$key]) ? $daysNew[$key] : null;
            }
            else{
                return $response->formatResponse(200, ['code'=>1,'msg'=>'Học sinh chưa full phí.']);
            }
            /*else{
                $key = $request->real_sessions - 1;
                $lastDay = !empty($daysNew[$key]) ? $daysNew[$key] : null;
            }
            */

            return $response->formatResponse(200, ['code'=>0, 'msg'=>$lastDay]);
        }
        return $response->formatResponse(200, ['code'=>0, 'msg'=>'']);
    }

    public function saveChangeEnrolmentStartDate(Request $request){
        $contractId = !empty($request->contract_id) ? $request->contract_id : 0;
        if ($contractId){
            $sql = "UPDATE `contracts` 
                    SET enrolment_start_date = '{$request->start_date}',
                    enrolment_last_date = '{$request->last_date}',
                    start_date = '{$request->start_date}',
                    end_date = '{$request->last_date}' 
                    WHERE id = $contractId";
            u::query($sql);
        }
        $response = new Response();
        return $response->formatResponse(200, null);
    }

    public function branchTransfer(Request $request){
        SupportService::branchTransferService($request->all());
        //$response = new Response();
       ///return $response->formatResponse(200, null);
    }

    public function studentStudying(Request $request, $id = 0){
        $query =   "SELECT COUNT(c.id) AS total, b.`updated_last_date` FROM contracts c 
                    LEFT JOIN branches b ON c.`branch_id` = b.id
                    WHERE c.`status` = 6 AND c.`branch_id` = $id";
        $data = u::query($query);
        $resp = $data ? $data[0] : null;
        $response = new Response();
        return $response->formatResponse(200, $resp);
    }

    public function branchUpdateEnrolmentLastDate(Request $request){
         $sql = "SELECT c.branch_id,c.`id`, c.`enrolment_start_date`,c.`enrolment_last_date`,c.`real_sessions`,c.`total_sessions`,c.`debt_amount`,c.`enrolment_schedule` AS class_day
                FROM `contracts` c
                WHERE c.`status` = 6 AND c.`branch_id` = {$request->id}";
         $data = u::query($sql);

         if ($data){
             foreach ($data as $obj){
                 self::processLastDateByStudent($obj);
             }
         }

        $response = new Response();
        return $response->formatResponse(200, ['msg' =>'success', 'error' =>0]);
    }

    private function processLastDateByStudent($data = []){
        $lastDay = null;
        $branchId = $data->branch_id;
        ///$debtAmount = $data->debt_amount;
        $realSessions = $data->real_sessions;
        //$totalSessions = $data->total_sessions;
        $classDay = $data->class_day;
        $contractId = $data->id;
        $startDate = $data->enrolment_start_date;
        //$endDate = $data->enrolment_last_date;
        $reserves_session = 0;
        $reserves = "SELECT c.id, c.`student_id`,c.`enrolment_start_date`, c.`enrolment_last_date`,
                    r.`final_approved_at`,r.`status`,r.`start_date`,r.`end_date`, r.`session`, r.`is_reserved`,IF(r.`is_reserved` = 0,'ko giu cho','giu cho') AS reserved_name
                    FROM `contracts` c LEFT JOIN `reserves` r
                    ON r.`student_id` = c.`student_id`
        AND r.`start_date` >= c.`enrolment_start_date` AND r.`final_approved_at` != '' AND r.`end_date` <= c.`enrolment_last_date` AND r.is_reserved = 1
                    WHERE c.`id` = $contractId";
        $dataReserves = u::first($reserves);
        if ($dataReserves){
            $reserves_session = $dataReserves->session;
        }

        $holiday =  u::getPublicHolidayAll($branchId);
        $nextTime = strtotime($startDate) + 60*60*24*1000;
        $inDate = date('Y-m-d', $nextTime);
        $days =  u::getDaysBetweenTwoDate($startDate, $inDate, $classDay); # 2x Friday
        $daysNew = [];
        $passDays = [];
        foreach($days as $day){
            if (!in_array($day,$holiday)){
                $daysNew[] = $day;
            }
            else{
                $passDays[] = $day;
            }
        }

        $key = (int)$realSessions - 1;
        $key = (int)$reserves_session + $key;
        $lastDay = !empty($daysNew[$key]) ? $daysNew[$key] : null;

        if ($lastDay){
            LogService::logContract($contractId);
            $updateContract = "UPDATE contracts c SET c.`enrolment_start_date` = '$startDate', c.`enrolment_last_date` = '$lastDay', c.`start_date` = '$startDate', c.`end_date` = '$lastDay', c.`updated_at` = NOW() WHERE c.id = $contractId";
            $updateBranch = "UPDATE `branches` b SET b.`updated_last_date` = NOW() WHERE b.id = $branchId";
            u::query($updateContract);
            u::query($updateBranch);
        }

    }

    protected function checkAndCreateStudent($phoneNumber = null){
        if (!$phoneNumber)
            exit;
        $phoneNumber1 = ltrim($phoneNumber,0);
        $sql = "SELECT COUNT(id) as c FROM `student_temp` s WHERE s.`gud_mobile1` = '$phoneNumber' OR s.`gud_mobile2` = '$phoneNumber'
                UNION ALL
                SELECT COUNT(id) as c FROM `students` s WHERE s.`gud_mobile1` LIKE '%$phoneNumber1%' OR s.`gud_mobile2` LIKE '%$phoneNumber1%'";

        $data = u::query($sql);

        if ($data[0]->c == 0 && $data[1]->c == 0){// chua ton tai kh
            return 0;
        }
        else if ($data[0]->c  >0 && $data[1]->c >0){ //data tho
            return 2;
        }
        else if ($data[1]->c == 0 && $data[0]->c > 0){ //data student
            return 1;
        }
        else
            return 2;
    }

    protected function geHistoryCareNote($id = 0){
        $sql = "SELECT c.note,c.`tele_saler`,c.`data_note` FROM `customer_care` c WHERE c.std_temp_id = $id ORDER BY id DESC LIMIT 1";
        $data = u::query($sql);
        return $data ? $data[0] : [];
    }

    public function customerInfo(Request $request){
        if (md5("caresoft-connected") != $request->header('authorization'))
            return response()->json(['message' => 'Not authorized'], 401);
        try {
            $host = $request->getSchemeAndHttpHost();
            $logCyberRequest = new LogCaresoftRequest();
            $logCyberRequest->logCallingAPI($host, json_encode($request->all()), json_encode($request->header()), "{$request->method()}", date('Y-m-d H:i:s'), null, 0, 0, "Caresoft gọi api customerInfo của CMS");
        }catch (\Exception $e){
        }
        $info = $this->checkAndCreateStudent($request->phone_number);

        if ($info == 0){
            $stdTemp = new StudentTemp();
            $stdId = $stdTemp->addNew($request->phone_number);
            if ($stdId > 0)
                $info = 1;
        }

        $status = [
                    ["id" =>6, "value"=> "Có con trong độ tuổi và có quan tâm đến CMS"],
                    ["id" =>9, "value"=>  "Có tín hiệu, Không nghe máy, dập máy"],
                    ["id" =>4, "value"=>  "Đến CMS ( làm bài test, tìm hiểu chương trình, tham gia sự kiện )"],
                    ["id" =>5, "value"=>  "Đồng ý lịch hẹn lên trải nghiệm"],
                    ["id" =>2, "value"=>  "Gọi chăm sóc khách hàng"],
                    ["id" =>1, "value"=>  "Nhận góp ý từ khách hàng"],
                    ["id" =>7, "value"=>  "Nói chuyện được với contact"],
                    ["id" =>3, "value"=>  "Nộp tiền"],
                    ["id" =>8, "value"=>  "Số điện thoại không tồn tại, nhầm máy"]
                ];
        $branchId = 0;
        $ecName = '';
        $customerId = '';
        $customerName = '';
        $branchName = '';
        $studentName = '';
        $mobile = '';
        $mobile2 = '';
        $address = '';
        $phoneNumber = !empty($request->phone_number) ? $request->phone_number : 0;

        if ($info == 2){
            $gudPhone = ltrim($phoneNumber,0);
            $sql = "SELECT CONCAT(\"PHHS.\",s.id) AS customer_id,s.`gud_name1` AS gud_name, s.`name`,s.`address`,c.`branch_id`,s.`gud_mobile2`,
                (SELECT NAME FROM branches WHERE id = c.`branch_id`) AS branch_name,
                (SELECT full_name FROM users WHERE id = c.ec_id) AS ec_name
                FROM `students` s
                LEFT JOIN `contracts` c ON c.`student_id` = s.id
                WHERE (s.`gud_mobile1` LIKE '%$gudPhone%' OR s.`gud_mobile2` LIKE '%$gudPhone%') GROUP BY s.`name`";

            $data = u::query($sql);

            $customer = [];
            if ($data){
                foreach ($data as $st){
                    $ecName .= $st->ec_name.',';
                    $customerName = $st->gud_name;
                    $customerId = $st->customer_id;
                    $branchName = $st->branch_name;
                    $mobile = $phoneNumber;
                    $mobile2 = $st->gud_mobile2 ? $st->gud_mobile2 : "";
                    $address = $st->address;
                    $studentName .= $st->name.',';
                    $branchId = $st->branch_id;
                }
                $customer = [
                    'customer_id'=>$customerId,
                    'branch_name'=>$branchName,
                    'customer_name'=>$customerName,
                    'phone_number'=>$mobile,
                    'phone_number_2'=>$mobile2,
                    'address'=>$address ? $address :"Đang cập nhật",
                    'people_name'=>rtrim($studentName,","),
                    'birth_day'=>'',
                    'ec_name'=>rtrim($ecName,","),
                    'note'=> "Nội dung thông tin chăm sóc gần nhất"
                ];
                array_push($customer);
                $customer['people_name_2'] = "";
                $customer['birth_day_2'] = "";
                $customer['people_name_3'] = "";
                $customer['birth_day_3'] = "";
                $customer['reg_branch'] = "";
                $customer['appointment_time'] = "";
            }
        }

        if ($info == 1)
        {
            $sql = "SELECT st.id,CONCAT(\"KH.\",st.id) AS customer_id, COALESCE(st.`gud_name1`,'') AS gud_name, st.`name` AS ec_name,COALESCE(st.`date_of_birth`,'') AS `birth_day`,  COALESCE(st.`name`,'') AS `name`,st.`branch_id`,st.`gud_mobile2`,
                COALESCE(st.`address`,'') AS address,(SELECT NAME FROM branches WHERE id = st.`branch_id`) AS branch_name
                FROM student_temp st WHERE (st.`gud_mobile1` = '$phoneNumber' OR st.`gud_mobile2` = '$phoneNumber') AND st.`branch_id`IN (12,100)";
            $data = u::query($sql);
            if ($data){
                $st = $data[0];
                //foreach ($data as $st){
                    $ecName = $st->ec_name.',';
                    $customerName = $st->gud_name;
                    $customerId = $st->customer_id;
                    $branchName = $st->branch_name;
                    $mobile = $phoneNumber;
                    $address = $st->address;
                    $studentName = $st->name;
                    $branchId = $st->branch_id;
                    $mobile2 = $st->gud_mobile2 ? $st->gud_mobile2 : "";
               // }
                $customer = [
                    'customer_id'=>$customerId,
                    'branch_name'=>$branchName,
                    'customer_name'=>$customerName,
                    'phone_number'=>$mobile,
                    'phone_number_2'=>$mobile2,
                    'address'=>$address,
                    'people_name'=>rtrim($studentName,","),
                    'birth_day'=>$st->birth_day,
                    'ec_name'=>rtrim($ecName,","),
                    'note'=> "Nội dung thông tin chăm sóc gần nhất"
                ];
                $cares = $this->geHistoryCareNote($st->id);
                if ($cares && $cares->data_note){
                    $note = json_decode($cares->data_note);
                    array_push($customer);
                    $customer['people_name_2'] = $note->name_2;
                    $customer['birth_day_2'] = $note->birth_day_2;
                    $customer['people_name_3'] = $note->name_3;
                    $customer['birth_day_3'] = $note->birth_day_3;
                    $customer['reg_branch'] = $note->reg_branch;
                    $customer['appointment_time'] = $note->appointment_time;
                }
                else{
                    array_push($customer);
                    $customer['people_name_2'] = "";
                    $customer['birth_day_2'] = "";
                    $customer['people_name_3'] = "";
                    $customer['birth_day_3'] = "";
                    $customer['reg_branch'] = "";
                    $customer['appointment_time'] = "";
                }
            }
        }


        $ecList = [];
        $cityList = [];
        $branchList = [];
        if ($branchId){
            /**
             * $query = "SELECT te.`user_id`,(SELECT full_name FROM users WHERE id = te.`user_id`) AS full_name
                      FROM `term_user_branch` te
                      WHERE te.`role_id` IN (68,69) AND te.`branch_id` = $branchId";
            $ec = u::query($query);
            if($ec){
                foreach ($ec as $obj){
                    $ecList[] = ['id' =>$obj->user_id,'name' =>$obj->full_name];
                }
            }*/
            $query = "SELECT pr.name, pr.id
                      FROM `provinces` pr ";
            $pr = u::query($query);
            if($pr){
                foreach ($pr as $obj){
                    $cityList[] = ['id' =>$obj->id,'name' =>$obj->name];
                }
            }

            $branches = "SELECT pr.name, pr.id
                      FROM `branches` pr WHERE id < 1000";
            $bch = u::query($branches);
            if($bch){
                foreach ($bch as $obj){
                    $branchList[] = ['id' =>$obj->id,'name' =>$obj->name];
                }
            }
        }
        $customer['branch_list'] = $branchList;
        $customer['city_list'] = $cityList;
        $customer['ec_list'] = $ecList;
        $customer['status_list'] = $status;
        return response()->json(['error_code' =>0,'message' => 'success','data'=>$customer], 200);

    }

    public function appsCallback(Request $request){
        if (md5("caresoft-connected") != $request->header('authorization'))
            return response()->json(['message' => 'Not authorized'], 401);
        $customerId = $request->customer_id;
        $cityId = 1;//$request->city_id;
        $teleSaler = $request->tele_saler;
        $careStatus = $request->care_status;
        $careNote = $request->care_note;

        try {
            $host = $request->getSchemeAndHttpHost();
            $logCyberRequest = new LogCaresoftRequest();
            $logCyberRequest->logCallingAPI($host, json_encode($request->all()), json_encode($request->header()), "{$request->method()}", date('Y-m-d H:i:s'), null, 0, 0, "Caresoft gọi api appsCallback của CMS");
        }catch (\Exception $e){
        }
        if ($customerId&&$cityId && $teleSaler){
            $message = $this->storeCareSoftCallback($request->all());
            // $message = "success";
            return response()->json(['error_code' =>0,'message' => $message,'data'=>$request->all()], 200);
        }
        else
            return response()->json(['error_code' =>1,'message' => 'Thiếu tham số bắt buộc','data'=>$request->all()], 200);
    }

    protected function storeCareSoftCallback($data = []){
        if ($data){
            $id = (int)str_replace('KH.','',$data['customer_id']);
            //$sql = "UPDATE `student_temp` set province_id = {$data['city_id']} where id = $id";
            //u::query($sql);

            $customer_care = new CustomerCare();
            $customer_care->contact_method_id = 1;
            $customer_care->note = isset($data['care_note'])?$data['care_note'] :'';
            $customer_care->creator_id = 415;
            $customer_care->std_temp_id = $id;
            $customer_care->status = 0;
            $customer_care->tele_saler = $data['tele_saler'];

            /*
            if ($request->student_temp_id >0){
                $customer_care->std_temp_id = $request->std_temp_id;
                $customer_care->status = 0;
            }
            else{
                $customer_care->status = 1;
                $customer_care->crm_id = $request->crm_id;
            }
            */

            $customer_care->created_at = date('Y-m-d H:i:s');
            $customer_care->stored_date = date('Y-m-d H:i:s');
            $customer_care->contact_quality_id = isset($data['care_status'])?$data['care_status']:'';
            $customer_care->meta_data = json_encode($data);
            $customer_care->save();
            if(isset($data['appointment_time']) && $data['appointment_time']){
                return self::createCheckin($data);
            }else{
                return "success";
            }
        }

    }
    public static function createCheckin($data){
        $condition = (object)array(
            'mobile' => $data['customer_mobile'],
            'branch_id' => $data['reg_branch'],
        );
        $validate = self::checkStudentExist($condition);
        if($validate->existed==0){
            $unix_check_dublicate = md5($data['people_name'] . $data['customer_name'] . $data['customer_mobile']);
            $student = new Student();
            $used_id = "CMS" . time();
            $student->cms_id = 0;
            $student->crm_id = "CMS" . time();
            $student->name = $data['people_name'];
            $student->phone = $data['customer_mobile'];
            $student->date_of_birth = $data['birth_day'];
            $student->gud_mobile1 = $data['customer_mobile'];
            $student->gud_name1 = $data['customer_name'];
            $student->gender = $data['gender']==1? 'M':'F';
            $student->address = $data['address'];
            $student->district_id = 1;//$request->district_id;
            $student->school = $data['school'];
            $student->school_grade = $data['school_grade'];
            $student->created_at = date('Y-m-d H:i:s');
            $student->hash_key = $unix_check_dublicate;
            $student->used_student_ids = $used_id;
            $student->branch_id = $data['reg_branch'];
            $student->checkin_at = $data['appointment_time'].':00';
            $student->status = 0;
            $student->save();
            $lastInsertedId = $student->id;
            $cms_id = '2' . str_pad((string)$lastInsertedId, 7, '0', STR_PAD_LEFT);
            $crm_id = "CMS$cms_id";
            $cms_id = (int)$cms_id;
            u::query("UPDATE students SET cms_id = '$cms_id', crm_id = '$crm_id' WHERE id = $lastInsertedId");
            u::query("UPDATE `customer_care` c SET c.`crm_id` ='$crm_id' WHERE c.`std_temp_id` IN (SELECT s.id FROM `student_temp` s WHERE s.`gud_mobile1` = '{}')");
            $region_id = 0;
            $zone_id = 0;

            $ecLeader = u::first("SELECT id FROM term_user_branch WHERE  branch_id = ".$data['reg_branch']." AND role_id=69 AND `status`=1");
            $csLeader = u::first("SELECT id FROM term_user_branch WHERE  branch_id = ".$data['reg_branch']." AND role_id=56 AND `status`=1");

            $ecLeaderId = $ecLeader ? $ecLeader->id : 0 ;
            $csLeaderId = $csLeader ? $csLeader->id : 0;


            DB::table('term_student_user')->insert(
                [
                    'student_id' => $lastInsertedId,
                    'ec_id' => $ecLeaderId,
                    'cm_id' => $ecLeaderId,
                    'status' => 0,
                    'ec_leader_id' => $ecLeaderId,
                    'om_id' => $csLeaderId,
                    'branch_id' => $data['reg_branch'],
                    'region_id' => $region_id,
                    'zone_id' => $zone_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
            $content = "Tạo checkin HS: ".$student->name .", CRM_ID: ". $crm_id." thành công.";
            if($data['people_name_2']){
                $unix_check_dublicate = md5($data['people_name_2'] . $data['customer_name'] . $data['customer_mobile']);
                $student = new Student();
                $used_id = "CMS" . time();
                $student->cms_id = 0;
                $student->crm_id = "CMS" . time();
                $student->name = $data['people_name_2'];
                $student->phone = $data['customer_mobile'];
                $student->date_of_birth = $data['birth_day_2'];
                $student->gud_mobile1 = $data['customer_mobile'];
                $student->gud_name1 = $data['customer_name'];
                $student->gender = $data['gender_2'];
                $student->district_id = 1;//$request->district_id;
                $student->school = $data['school_2'];
                $student->school_grade = $data['school_grade_2'];
                $student->created_at = date('Y-m-d H:i:s');
                $student->hash_key = $unix_check_dublicate;
                $student->used_student_ids = $used_id;
                $student->branch_id = $data['reg_branch'];
                $student->checkin_at = $data['appointment_time'].':00';
                $student->status = 0;
                $student->sibling_id = $lastInsertedId;
                $student->save();
                $lastInsertedId_2 = $student->id;
                $cms_id = '2' . str_pad((string)$lastInsertedId_2, 7, '0', STR_PAD_LEFT);
                $crm_id = "CMS$cms_id";
                $cms_id = (int)$cms_id;
                u::query("UPDATE students SET cms_id = '$cms_id', crm_id = '$crm_id' WHERE id = $lastInsertedId_2");
                u::query("UPDATE `customer_care` c SET c.`crm_id` ='$crm_id' WHERE c.`std_temp_id` IN (SELECT s.id FROM `student_temp` s WHERE s.`gud_mobile1` = '{}')");
                $region_id = 0;
                $zone_id = 0;

                $ecLeader = u::first("SELECT id FROM term_user_branch WHERE  branch_id = ".$data['reg_branch']." AND role_id=69 AND `status`=1");
                $csLeader = u::first("SELECT id FROM term_user_branch WHERE  branch_id = ".$data['reg_branch']." AND role_id=56 AND `status`=1");

                $ecLeaderId = $ecLeader ? $ecLeader->id : 0 ;
                $csLeaderId = $csLeader ? $csLeader->id : 0;


                DB::table('term_student_user')->insert(
                    [
                        'student_id' => $lastInsertedId_2,
                        'ec_id' => $ecLeaderId,
                        'cm_id' => $ecLeaderId,
                        'status' => 0,
                        'ec_leader_id' => $ecLeaderId,
                        'om_id' => $csLeaderId,
                        'branch_id' => $data['reg_branch'],
                        'region_id' => $region_id,
                        'zone_id' => $zone_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );
                $content.= " Tạo checkin HS: ".$student->name .", CRM_ID: ". $crm_id." thành công.";
            }
            return $content;
        }else{
            return $validate->msg;
        }

    }
    public static function checkStudentExist($condition){
        $mobile = trim($condition->mobile);
        $branch_id = trim($condition->branch_id);
        $dataStd = u::first("SELECT COUNT(s.id) existed FROM students s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') ");
        $rawData = [];
        if ($branch_id > 0){
            $branch_id = 100;
            $tmp_cond =" AND branch_id!=12";
            $rawData = u::first("SELECT COUNT(s.id) existed FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.type = 0 AND s.branch_id != $branch_id $tmp_cond AND (s.`created_at` >= NOW()-INTERVAL 15 DAY OR s.import_to_call>0 OR (s.branch_id=12 AND s.source ='GĐTT'))");
        }
        $msg = "";
        if ($dataStd->existed >= 1){
            $dataStd->existed = 1;
            $detail = u::first("SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name FROM students s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') ");
            $msg = "Số điện thoại phụ huynh đã có ở: {$detail->branch_name}, hãy nhập số điện thoại khác.";
        }
        if (isset($rawData->existed) && $rawData->existed == 1){
            $detail = u::first("SELECT (SELECT b.name FROM branches b WHERE b.id = s.`branch_id`) AS branch_name FROM `student_temp` s WHERE (s.`gud_mobile1` = '{$mobile}' OR s.`gud_mobile2` = '{$mobile}') AND s.type = 0 AND s.branch_id != $branch_id AND (s.`created_at` >= NOW()-INTERVAL 15 DAY OR s.import_to_call>0 OR (s.branch_id=12 AND s.source ='GĐTT'))");
            $msg = "Số điện thoại phụ huynh thuộc data khách hàng đang chăm sóc của: {$detail->branch_name}, vui lòng nhập số điện thoại khác.";
        }
        $data = !$msg? (object)['existed' =>0,'msg' =>$msg] : (object)['existed' =>1,'msg' =>$msg];
        return $data;
    }

    public function cronPushContact(Request $request){
        $stTemp = new StudentTemp;
        $stTemp->createContactCareSoft($request);
        exit;

    }

    public function logStudentActive(Request $request){
        $sql_1 = "UPDATE `log_student_active` lo 
        SET lo.`status` = (SELECT STATUS FROM contracts WHERE lo.`contract_id` = id),
        lo.`enrolment_withdraw_date` = (SELECT enrolment_withdraw_date FROM contracts WHERE lo.`contract_id` = id AND `status` = 7)
        WHERE lo.`status` = 6";
        u::query($sql_1);
        sleep(1);

        $sql_2 = "INSERT INTO `log_student_active` (
                    crm_id, 
                    accounting_id,
                    cyber_id,
                    enrolment_start_date,
                    enrolment_last_date,
                    `name`,
                    cls_name,
                    date_of_birth,
                    gud_name1,
                    gud_mobile1,
                    branch_name,
                    product_name,
                    tuition_name,
                    ec_name,
                    tuition_fee_price,
                    discount_value,
                    coupon,
                    must_charge,
                    debt_amount,
                    total_charged,
                    `status`,
                    `type`,
                    tuition_fee_id,
                    enrolment_schedule,
                    student_id,branch_id,
                    contract_id
                )
                SELECT st.crm_id, st.`accounting_id`,c.accounting_id AS cyber_id, c.`enrolment_start_date` enrolment_start_date,c.`enrolment_last_date` enrolment_last_date,st.`name`,
                (SELECT cls_name FROM classes WHERE id = c.class_id) AS cls_name,
                st.`date_of_birth`,
                st.`gud_name1`,st.`gud_mobile1`,
                (SELECT NAME FROM branches WHERE c.branch_id = id) AS branch_name,
                (SELECT NAME FROM `products` WHERE c.product_id = id) product_name,
                (SELECT NAME FROM `tuition_fee` WHERE c.`tuition_fee_id` = id) tuition_name,
                (SELECT full_name FROM `users` WHERE c.`ec_id` = id) ec_name,
                c.`tuition_fee_price`, c.`discount_value`, c.`coupon`, c.`must_charge`,c.`debt_amount`,c.`total_charged`,
                c.`status`,c.`type`,c.`tuition_fee_id`,c.`enrolment_schedule`, c.`student_id`,c.branch_id,c.id contract_id
                FROM contracts c 
                LEFT JOIN students st ON st.id = c.`student_id` 
                WHERE c.`status` = 6 AND c.branch_id NOT IN (100,101) 
                AND c.`enrolment_last_date` >= CURDATE() 
                AND c.`enrolment_start_date` >DATE_SUB(CURDATE(),INTERVAL 2 DAY)
                AND c.`student_id` NOT IN (SELECT student_id FROM log_student_active WHERE enrolment_withdraw_date IS NULL)";

        u::query($sql_2);
        exit;
    }
}
