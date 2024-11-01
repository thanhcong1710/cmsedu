<?php
/**
 * Created by PhpStorm.
 * User: Quyenld
 * Date: 9/17/2019
 * Time: 4:58 PM
 */

namespace App\Services;

use App\Providers\UtilityServiceProvider as u;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentHistoryService extends ExportService
{
    const CONTRACT_TYPE = [
        0 => 'Học thử',
        1 => 'Chính thức',
        2 => 'Tái phí bình thường',
        3 => 'Tái phí do nhận chuyển phí',
        4 => 'Chỉ nhận chuyển phí',
        5 => 'Chuyển trung tâm',
        6 => 'Chuyển lớp',
        7 => 'Sinh do tái phí chưa full phí',
        8 => 'Được nhận học bổng hoặc VIP',
        9 => 'Loại hợp đồng đặc biệt',
        10 => 'Sinh ra do bảo lưu không giữ chỗ',
        11 => 'Đã thực hiện qui đổi',
        85 => 'Học bổng chuyển trung tâm',
        86 => 'Học bổng chuyển lớp',
    ];

    const CONTRACT_STATUS = [
        0 => 'Đã xóa',
        1 => 'Đã active nhưng chưa đóng phí',
        2 => 'Đã active và đặt cọc nhưng chưa thu đủ phí hoặc đang chờ nhận chuyển phí',
        3 => 'Đã active và đã thu đủ phí nhưng chưa được xếp lớp',
        4 => 'Đang bảo lưu không giữ chỗ hoặc pending',
        5 => 'Đang được nhận học bổng hoặc VIP',
        6 => 'Đã được xếp lớp và đang đi học',
        7 => 'Đã bị withdraw',
        8 => 'Đã bỏ cọc',
    ];

    public function exportByDate($params, $template, $data)
    {
        $headerData = [];
        if (!empty($params['branch_ids'])) {
            $branchIds = $params['branch_ids'];
            $branches = u::query("select name from branches where id in ($branchIds)");
            $branchNames = implode(", ", array_values(array_column($branches, 'name')));
            $headerData[] = [
                "name" => "Trung tâm: $branchNames",
                'size' => 10,
                'weight' => false
            ];
        }
        if (!empty($params['start_date']) && !empty($params['end_date'])) {
            $headerData[] = [
                "name" => "(Từ ngày {$params['start_date']} đến ngày {$params['end_date']})",
                'size' => 10,
                'weight' => false
            ];
        }
        try {
            $this->export($data['data'], $template, "export_student_history_by_branch", $headerData);
        } catch (Exception $e) {
        }
    }

    public static function getStudentHistoryReportByDate($params)
    {
        $brandUser = [];
        $tsData = [];
        $branchName = "";
        $where = "";
        if (!empty($params['branch_ids'])) {
            $where .= "branch_id in ({$params['branch_ids']}) ";

            $sql = "SELECT name FROM `branches` WHERE id = {$params['branch_ids']}";
            $branchData = u::query($sql);
            if (!empty($branchData[0]))
                $branchName = $branchData[0]->name;
            $tsSQL = "SELECT DISTINCT `student_id` AS ts_student_id FROM `contracts` WHERE branch_id = {$params['branch_ids']}";
            $tsData = u::query($tsSQL);
        }
        else{
            $tsData = u::query("SELECT DISTINCT `student_id` AS ts_student_id FROM `contracts`");
        }
        if (substr($where, 0, 3) === "and") {
            $where = substr($where, 3, strlen($where));
        }
        $limit = "";
        $orderBy = " ORDER BY ts.student_id";
        if (!empty($params['limit'])) {
            $l = (int)$params['limit'];
            $p = (int)$params['page'];
            $o = ($p - 1) * $l;
            $limit = " limit  $o, $l ";
        }

        if (!empty($params['branch_ids']) || $params['branch_ids'] == 0){
            $sql = "SELECT u.id, u.full_name FROM users u";
            $users = u::query($sql);
            foreach ($users as $user){
                $brandUser[$user->id] = $user->full_name;
            }
        }

        if (!$where)
            $where = "1 = 1";
        //$total = u::first("select count(1) as total FROM `contracts` ts WHERE $where")->total;

        $query =    "SELECT ts.accounting_id,
                        (SELECT crm_id FROM students WHERE ts.`student_id` = id) AS cms_code,
                        (SELECT concat(COALESCE(gud_name1,''),'-',COALESCE(gud_name2,'')) FROM students WHERE ts.`student_id` = id) AS gud_name,
                        (SELECT concat(COALESCE(gud_mobile1,''),'-',COALESCE(gud_mobile2,'')) FROM students WHERE ts.`student_id` = id) AS gud_mobile,
                        (SELECT accounting_id FROM students WHERE ts.`student_id` = id) AS cyber_code,
                        (SELECT `name` FROM students WHERE ts.`student_id` = id) AS student_name,
                        (SELECT `name` FROM `branches` WHERE ts.`branch_id` = id) AS branch_name,ts.`ec_id`,ts.`cm_id`,
                        (SELECT `teacher_id` FROM `term_student_user` WHERE ts.`student_id` = student_id) AS teacher_id,
                        (SELECT `ins_name` FROM `teachers` WHERE teacher_id = id) AS teacher_name,
                        ts.`class_id` AS in_class_id,
                        (SELECT `semester_id` FROM `classes` WHERE in_class_id = id) AS semester_id,
                        (SELECT `product_id` FROM `classes` WHERE in_class_id = id) AS p_product_id,
                        (SELECT `name` FROM `products` WHERE p_product_id = id) AS product_name,
                        (SELECT `name` FROM `tuition_fee` WHERE ts.`tuition_fee_id` = id) AS tuition_name,
                        ts.`tuition_fee_price`, ts.must_charge, ts.total_sessions, ts.`total_charged`,
                        ts.real_sessions, ts.`status` as c_type, ts.`type` as s_type,
                        (SELECT `program_id` FROM `classes` WHERE in_class_id = id) AS p_program_id,
                        (SELECT `name` FROM `programs` WHERE p_program_id = id) AS program_name,
                        (SELECT `cls_name` FROM `classes` WHERE in_class_id = id) AS class_name,
                        ts.`enrolment_start_date`, ts.`enrolment_end_date`, ts.`enrolment_last_date`, ts.`student_id`
                        FROM `contracts` ts WHERE $where $orderBy ";
        $data = u::query($query);
        $studentHistoryArr = [];
        $tsDataNew = [];
        if (!empty($data)) {
            foreach ($data as $obj) {
                $obj->cm_id = isset($brandUser[$obj->cm_id]) ? $brandUser[$obj->cm_id]: "";
                $obj->ec_id = isset($brandUser[$obj->ec_id]) ? $brandUser[$obj->ec_id] : "";
                $obj->c_type = isset(self::CONTRACT_STATUS[$obj->c_type]) ? self::CONTRACT_STATUS[$obj->c_type] : "";
                $obj->s_type = isset(self::CONTRACT_TYPE[$obj->s_type]) ? self::CONTRACT_TYPE[$obj->s_type] : "";
                $obj->tuition_fee_price = number_format($obj->tuition_fee_price);
                $obj->must_charge = number_format($obj->must_charge);
                $obj->total_charged = number_format($obj->total_charged);
                $studentHistoryArr[] = $obj;
            }
            foreach ($tsData as $ts) {
                $tsDataSort = self::sortArrayObj($studentHistoryArr, $ts->ts_student_id);
                if ($tsDataSort){
                    $tsDataNew[]  = (object)$tsDataSort;
                }
            }
        }

        $dataArrLms = [];
        if ($limit){
            $strLimit = str_replace(" limit ","",$limit);
            $limitArr = explode(",",$strLimit);
            if ($limitArr){
                $lm1 = trim($limitArr[0]);
                $lm2 = trim($limitArr[1]);
                $lm = $lm1 + $lm2;
                for ($i= $lm1; $i < $lm; $i++){
                    if (!empty($tsDataNew[$i])){
                        $dataArrLms[] = $tsDataNew[$i];
                    }
                }
            }
        }
        else
            $dataArrLms = $tsDataNew;

        return [
            'data' => ($dataArrLms),
            'name' => $branchName,
            'total' => sizeof($tsDataNew),
        ];
    }

    private static function sortArrayObj($data, $key)
    {

        $tsDataNew = [];
        foreach($data as $dt){
            if ($key == $dt->student_id ){

                $gudName = explode("-",$dt->gud_name);
                $gudMobile = explode("-",$dt->gud_mobile);
                $gudName1 = '';
                $gudMobile1 = '';
                $tsDataNew['cms_code'] = $dt->cms_code;
                $tsDataNew['cyber_code'] = $dt->cyber_code;
                $tsDataNew['student_name'] = $dt->student_name;
                if (!empty($gudMobile[0])){
                    $gudName1 = $gudName[0];
                    $gudMobile1 = $gudMobile[0];
                }

                if (!empty($gudMobile[1]) && empty($gudMobile1)){
                    $gudName1 = $gudName[1];
                    $gudMobile1 = $gudMobile[1];
                }

                $tsDataNew['gud_name1'] = $gudName1;
                $tsDataNew['gud_mobile1'] = $gudMobile1;
                $tsDataNew['branch_name'] = $dt->branch_name;
                $tsDataNew['detail'][] = $dt;
            }
        }
        return $tsDataNew;
    }
}