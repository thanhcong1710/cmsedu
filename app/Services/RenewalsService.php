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

class RenewalsService extends ExportService
{
    public function exportRenewalsByDate($params, $template, $data)
    {
        $headerData = [];
        if (!empty($params['branch_ids']) && is_array($params['branch_ids'])) {
            $branchIds = implode(",", $params['branch_ids']);
            $branches = u::query("select name from branches where id in ($branchIds)");
            $branchNames = implode(", ", array_values(array_column($branches, 'name')));
            $headerData[] = [
                "name" => "Trung tâm: $branchNames",
                'size' => 10,
                'weight' => false
            ];
        }
        else{
            $headerData[] = [
                "name" => "Tất cả các trung tâm",
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
            //$this->export($data['data'], $template, "export_student_renewals_by_date", $headerData);
            $columns = $template->getColumns();
            $this->exportNew($data, $template, "export_student_renewals_in_month", $headerData, $columns, function ($sheet, $excel, $rowIndex, $columns) use ($data) {
                $rowIndex = 13;
                self::writeDataSheetNow($sheet, $excel, $rowIndex, $data, $columns);
            });
        } catch (Exception $e) {
        }
    }

    private function writeDataSheetNow($sheet, $excel, $rowIndex, $data, $columns){
        foreach ($columns as $c => $col){
            if (!empty($col['value'])){

                if ($col['value'] != "4"){
                    $cols[] = $col['value'];

                }
                else{
                    array_push($cols,$col['children'][0]["children"][0]['value']);
                    array_push($cols,$col['children'][0]["children"][1]['value']);
                    array_push($cols,$col['children'][1]["children"][0]['value']);
                    array_push($cols,$col['children'][1]["children"][1]['value']);

                }
            }
        }
        foreach ($data['data'] as $indexItem => $item) {
            $rowIndex = $this->writeRowNow($sheet, $excel, $cols, $rowIndex, $item, $indexItem);
        }

    }

    private function writeRowNow($sheet, $excel, $cols, $rowIndex, $data, $dataIndex, $rootData = null, $indexSubItem = -1){
        foreach ($cols as $index => $cls) {
            $columnIndex = $index + 1;
            $value = isset($data->{$cls}) ? $data->{$cls} :"";
            if ($cls == "i")
                $value = $dataIndex +1;
            self::writeCell($sheet, $columnIndex, $rowIndex, $value);
        }
        return $rowIndex + 1;
    }

    private static function getRenewalsVip($studentId = 0){
        $sql = "SELECT type FROM contracts WHERE type IN (3,4,8,85) AND student_id = $studentId ORDER BY created_at DESC LIMIT 1";
        $data = u::query($sql);
        if ($data){
            $type = $data[0]->type;
            if ($type == 3 || $type == 4)
                return "Đã nhận chuyển phí";

            if ($type == 8 || $type == 85)
                return "Đã nhận học bổng";
        }
        else
            return '';
    }

    private  static function getRenewalsInfo($studentId = 0, $countRecharge = 0, $branchId = 0, $contractId = 0){

        $sql = "SELECT c.id AS contract_id,c.`ec_id` AS ec_id, c.`accounting_id`, c.`enrolment_start_date`, c.`enrolment_last_date`,c.`status`,
             (SELECT cls_name FROM `classes` WHERE id = c.`class_id`) AS  cls_name, c.`type`,
             (SELECT full_name FROM users WHERE id = ec_id) AS  ec_name,
             (SELECT `accounting_id` FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS  accounting_id,
             (SELECT `session` FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS  `session`,c.`payment_id`
             FROM `contracts` c 
             WHERE c.`student_id` = $studentId AND c.`type` NOT IN (3,4,10,6,8) 
             AND c.`branch_id` = $branchId AND c.`count_recharge` >= $countRecharge AND c.`id` != $contractId ORDER BY c.`count_recharge` DESC";

        $data = u::query($sql);
        $info = $data ? $data[0] : [];
        $accoutingIdArr = ["BLACKHOLE-EARLY BIRD","BRIGHTIG-EARLY BIRD"];
        if ($info && (!in_array($info->accounting_id, $accoutingIdArr) && $info->session != 0)){
            if (!$info->payment_id || $info->status == 7)
                return (object)[
                    're_type' =>'no',
                    're_info' =>'',
                ];
            else
                return (object)[
                    're_type' =>'yes',
                    're_info' =>'',
                ];
        }
        else {
            $reInfo = self::getRenewalsVip($studentId);
            return (object)[
                're_type' =>'no',
                're_info' =>$reInfo,
            ];
        }

    }

    public static function getReportByDate($params, $export = false)
    {
        $wBranch = "";
        $wId = "";
        $branchIds = [];
        $start_date  = "";
        $end_date  = "";
        $orderBy = "";

        if (!empty($params['branch_ids']) && is_array($params['branch_ids'])) {
            $branchIds = implode(",", $params['branch_ids']);
        }
        if (!empty($params['start_date']) && !empty($params['end_date'])) {
            $start_date  = $params['start_date'];
            $end_date  = $params['end_date'];
        }

        if ($branchIds != 0){
            $wBranch = $branchIds ? " AND c.`branch_id` IN ($branchIds)": "";
            $wId = $branchIds ? " AND id IN ($branchIds)": "";
        }
        else
            $orderBy = " ORDER BY c.branch_id ASC";
        if (!$branchIds)
            $orderBy = " ORDER BY c.branch_id ASC";

        $limit = "";
        if (!empty($params['limit'])) {
            $l = (int)$params['limit'];
            $p = (int)$params['page'];
            $o = ($p - 1) * $l;
            if (!$export)
                $limit = " limit  $o, $l ";
        }

        $brand = [];
        $brandName = [];
        $query = "SELECT c.`branch_id`,(SELECT s.accounting_id FROM `students` s WHERE s.id = c.`student_id` ) AS cyber_code,
             (SELECT s.crm_id FROM `students` s WHERE s.id = c.`student_id` ) AS crm_id,
             (SELECT s.name FROM `students` s WHERE s.id = c.`student_id` ) AS sdt_name,
             (SELECT s.date_of_birth FROM `students` s WHERE s.id = c.`student_id` ) AS date_of_birth,
             (SELECT LEFT(s.date_of_birth,4) FROM `students` s WHERE s.id = c.`student_id` ) AS year_of_birth,
             (SELECT s.gud_name1 FROM `students` s WHERE s.id = c.`student_id` ) AS gud_name1,
             (SELECT s.gud_mobile1 FROM `students` s WHERE s.id = c.`student_id` ) AS gud_mobile1,
             (SELECT s.gud_name2 FROM `students` s WHERE s.id = c.`student_id` ) AS gud_name2,
             (SELECT s.gud_mobile2 FROM `students` s WHERE s.id = c.`student_id` ) AS gud_mobile2,
             (SELECT s.address FROM `students` s WHERE s.id = c.`student_id` ) AS address,
             c.`enrolment_start_date`,
             (SELECT cls_name FROM `classes` WHERE id = c.`class_id`) AS cls_name,
             (SELECT `name` FROM `programs` WHERE id = c.`program_id`) AS program_name,
             (SELECT `full_name` FROM `users` WHERE id = c.`ec_id`) AS ec_name, 
             (SELECT `shift_id` FROM `sessions` WHERE class_id = c.`class_id`) AS shift_id,
             (SELECT `class_day` FROM `sessions` WHERE class_id = c.`class_id`) AS class_day,
             (SELECT `name` FROM `shifts` WHERE id = shift_id) AS shift_name,
             c.`tuition_fee_price`,c.`enrolment_last_date`,
             c.`student_id`, c.id AS contract_id, c.`class_id`, c.`status`,c.`enrolment_withdraw_date`,c.`type`,c.`status`,c.`count_recharge`,
             (SELECT `end_date` FROM `reserves` r WHERE r.student_id = c.`student_id` ORDER BY id DESC  LIMIT 1) AS reserve_end_date,
             (SELECT t.`transfer_date` FROM `tuition_transfer` t WHERE t.from_student_id = c.`student_id` AND t.`status` = 2 ORDER BY id DESC  LIMIT 1) AS transfer_date,
             c.`must_charge`,
             (SELECT COUNT(ct.id) FROM contracts ct WHERE ct.student_id = c.student_id AND ct.`enrolment_last_date` >= '$start_date' AND ct.`enrolment_last_date` <= '$end_date') AS total_st1,
             (SELECT COUNT(ct.id) FROM contracts ct WHERE ct.student_id = c.student_id AND ct.count_recharge < c.count_recharge AND ct.`enrolment_last_date` >= '$start_date' AND ct.`enrolment_last_date` <= '$end_date') AS total_st2
             FROM `contracts` c 
             WHERE c.`enrolment_last_date` >= '$start_date' AND c.`enrolment_last_date` <= '$end_date'
             AND c.`status` IN (2,3,4,5,6,7) $wBranch $orderBy ";


        $sql = "SELECT id,name from `branches` WHERE status = 1 $wId";
        $branches = u::query($sql);
        $data  = u::query($query);


        foreach ($branches as $branch){
            $brand[] = $branch->id;
            $brandName[$branch->id] = $branch->name;
        }

        $dataArr = [];
        foreach ($data as $dta){
            $dta->tuition_fee = number_format($dta->must_charge);
            if (!empty($brandName[$dta->branch_id])){
                $dta->branch_name = $brandName[$dta->branch_id];
                $dta->class_day = u::getDayNameVi($dta->class_day);
            }
            else
                $dta->branch_name = "";

            $renew = static::getRenewalsInfo($dta->student_id,$dta->count_recharge,$dta->branch_id,$dta->contract_id);
            if ($renew){
                $dta->re_type = $renew->re_type;
                $dta->re_info = $renew->re_info;
            }
            if ($dta->total_st1 - $dta->total_st2 == 1){
                $dataArr[] = $dta;
            }

            /**if ($dta->reserve_end_date){
                if ($dta->reserve_end_date <= $end_date)
                    $dataArr[] = $dta;
            }
            else{

                if ($dta->enrolment_withdraw_date){
                    if($dta->enrolment_withdraw_date >= $end_date || $dta->enrolment_withdraw_date === '0000-00-00 00:00:00'){
                        $dataArr[] = $dta;
                    }
                }
                else{
                    if ($dta->transfer_date){
                        if ($dta->transfer_date >= $end_date ){
                            $dataArr[] = $dta;
                        }
                    }else{
                        $dataArr[] = $dta;
                    }
                }

            }
            **/
        }

//        $totalSQL = "SELECT count(1) total
//                     FROM `contracts` c
//                     WHERE c.`enrolment_last_date` >= '$start_date' AND c.`enrolment_last_date` <= '$end_date'
//                     AND c.`status` IN (2,3,4,5,6,7) $wBranch";
//        $total = 0;
//        if (!$export){
//            $dataTotal  = u::query($totalSQL);
//            $total = $dataTotal ? $dataTotal[0]->total : $total;
//        }

        $dataArrLms = [];
        if ($limit){
            $strLimit = str_replace(" limit ","",$limit);
            $limitArr = explode(",",$strLimit);
            if ($limitArr){
                $lm1 = trim($limitArr[0]);
                $lm2 = trim($limitArr[1]);
                $lm = $lm1 + $lm2;
                for ($i= $lm1; $i < $lm; $i++){
                    if (!empty($dataArr[$i])){
                        $dataArrLms[] = $dataArr[$i];
                    }
                }
            }
        }
        else
            $dataArrLms = $dataArr;

        return [
            'data' => ($dataArrLms),
            'total' => sizeof($dataArr),
        ];
    }

    public function exportReport28ByDate($params, $template, $data)
    {
        $headerData = [];
        if (!empty($params['scope']) && is_array($params['scope'])) {
            $branchIds = implode(",", $params['scope']);
            $branches = u::query("select name from branches where id in ($branchIds)");
            $branchNames = implode(", ", array_values(array_column($branches, 'name')));
            $headerData[] = [
                "name" => "Trung tâm: $branchNames",
                'size' => 10,
                'weight' => false
            ];
        }
        else{
            $headerData[] = [
                "name" => "Tất cả các trung tâm",
                'size' => 10,
                'weight' => false
            ];
        }

        try {
            $this->export($data, $template, "export_student_until_renew_by_date", $headerData);
        } catch (Exception $e) {
        }
    }
}