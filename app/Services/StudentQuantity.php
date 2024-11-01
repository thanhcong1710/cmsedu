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
use App\Models\ProcessExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;

class StudentQuantity extends ExportService
{
    public function exportDataByDate($params, $template, $data, $list)
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
            $columns = $template->getColumns();
            if (!$list){
                $this->exportNew($data, $template, "export_student_quantity_report_by_date.xlsx", $headerData, $columns, function ($sheet, $excel, $rowIndex, $columns) use ($data) {
                    self::writeDataSheet($sheet, $excel, $rowIndex, $data, $columns);
                });
            }
            else{
                self::exportCustomNew($data, $columns, $filename = "export_student_quantity_report_by_date.xlsx", $headerData);
            }

        } catch (Exception $e) {
        }
    }

    public static function getDataReportByDate($params, $listDays, $week, $month, $list)
    {
        $branchIds = [];
        if (!empty($params['branch_ids']) && is_array($params['branch_ids'])) {
            $branchIds = implode(",", $params['branch_ids']);
        }

        $wBranch = $branchIds ? " and id IN ($branchIds)": "";
        $brand = [];
        $brandName = [];
        $sql = "SELECT id,name from `branches` WHERE status = 1 $wBranch";
        $branches = u::query($sql);
        foreach ($branches as $branch){
            $brand[] = $branch->id;
            $brandName[$branch->id] = $branch->name;
        }
        $dataList = [];
        $brandData = [];

        foreach ($listDays as $w => $day) {
            $startDate = $day['from'].' 00:00:00';
            $endDate = $day['to'].' 23:59:59';

            $startDateLabel = date('d/m/Y', strtotime($day['from']));
            $endDateLabel = date('d/m/Y', strtotime($day['to']));

            if ($w == $week)
                $dataList['w8 ('.$startDateLabel.' - '.$endDateLabel.')'] = self::findByDate($startDate,$endDate, $branchIds, $list);
            else
                $dataList['w'.($w+1).'  ('.$startDateLabel.' - '.$endDateLabel.')'] = self::findByDate($startDate,$endDate, $branchIds, $list);
        }

        if ($list) {//xuat full danh sach
            foreach ($brand as $br) {
                foreach ($dataList as $w => $list) {
                    $dataListFirst = $list['data'];
                    $dataListPending = $list['pending'];
                    foreach ($dataListFirst as $ls) {
                        if ($ls->branch_id == $br) {
                            if (!empty($ls->enrolment_start_date)) {
                                if ($ls->enrolment_withdraw_date && $ls->enrolment_withdraw_date != '0000:00:00 00:00:00') {
                                    if ($ls->enrolment_withdraw_date >= $ls->end_date) {
                                        $brandData[$w]['branch_name'] = $brandName[$br];
                                        $brandData[$w]['week'] = $w;
                                        $brandData[$w]['detail'][] = static::getListDetail1($ls,$w);
                                    }
                                } else {
                                    $brandData[$w]['branch_name'] = $brandName[$br];
                                    $brandData[$w]['week'] = $w;
                                    $brandData[$w]['detail'][] = static::getListDetail1($ls,$w);
                                }
                            } else {
                                if (in_array($ls->student_id, $dataListPending)) {
                                    $brandData[$w]['branch_name'] = $brandName[$br];
                                    $brandData[$w]['week'] = $w;
                                    $brandData[$w]['detail'][] = static::getListDetail1($ls,$w);
                                }

                                if ($ls->std_status == "reserves"){
                                    $brandData[$w]['branch_name'] = $brandName[$br];
                                    $brandData[$w]['week'] = $w;
                                    $brandData[$w]['detail'][] = static::getListDetail1($ls,$w);
                                }
                            }
                        }
                    }
                }
            }

            $all = [];
            foreach ($brandData as $w => $item) {
                $all[$w] =(object)$item;
            }

            return $all;
        }

        if (!$list) {
            foreach ($brand as $br) {
                foreach ($dataList as $w => $list) {
                    $dataListFirst = $list['data'];
                    $dataListPending = $list['pending'];
                    $dataListReserves = $list['reserves'];
                    foreach ($dataListFirst as $ls) {
                        if ($ls->branch_id == $br) {
                            if ($ls->enrolment_withdraw_date && $ls->enrolment_withdraw_date != '0000:00:00 00:00:00') {
                                if ($ls->enrolment_withdraw_date >= $ls->end_date) {
                                    $brandData[$w][$brandName[$br]][] = $ls;
                                }
                            } else {
                                $brandData[$w][$brandName[$br]][] = $ls;
                            }
                        }
                    }

                    foreach ($dataListPending as $ls) {
                        if ($ls->branch_id == $br) {
                            $brandData[$w][$brandName[$br]][] = $ls;
                        }
                    }

                    foreach ($dataListReserves as $ls) {
                        if ($ls->branch_id == $br) {
                            $brandData[$w][$brandName[$br]][] = $ls;
                        }
                    }

                }
                    if (!isset($brandData[$w][$brandName[$br]]))
                        $brandData[$w][$brandName[$br]] = [];
                }
            }

            $all = [];
            foreach ($brandData as $w => $item) {
                $all[$w] = self::percentageData($item);
            }

            return $all;
    }

    public static function getUniqueStudent($dataList = [], $sid = 0){
        $data = [];
        foreach ($dataList as $ls){
            if (empty($ls->enrolment_start_date)){
                $data[$ls->student_id] = $ls;
            }
        }
        return (!empty($data[$sid])) ? $data[$sid] : [];
    }

    public static function getListDetail1($detail, $w){
        $detail->week = $w;
        $detail->type_name = u::getContractTypeName($detail->type);
        if ($detail->enrolment_withdraw_date && $detail->end_date < $detail->enrolment_withdraw_date)
            $detail->status_name = "Đang học";
        else
            $detail->status_name = u::getContractStatusName($detail->status);
        $detail->program_name = preg_replace('/[0-9]+/', '', $detail->tuition_fee_accounting_id);

        if ($detail->std_status === "active"){
            $detail->std_status_name = "Đang học";
        }
        if ($detail->std_status === "pending"){
            $detail->std_status_name = "Chờ xếp lớp";
        }
        if ($detail->std_status === "reserves"){
            $detail->std_status_name = "Bảo lưu";
        }

        return $detail;
    }

    public static function getListDetail($data, $label = false){
        foreach ($data["detail"] as $detail){
            $detail->type_name = u::getContractTypeName($detail->type);
            if ($detail->enrolment_withdraw_date && $detail->end_date < $detail->enrolment_withdraw_date)
                $detail->status_name = "Đang học";
            else
                $detail->status_name = u::getContractStatusName($detail->status);
            $detail->program_name = preg_replace('/[0-9]+/', '', $detail->tuition_fee_accounting_id);
            $data["detail"] = $detail;
        }
        return $data;
    }

    public static function findByDate($startDate,$endDate,$branchIds, $list){
        $where = $branchIds ? " c.branch_id IN ($branchIds) AND": "";
        $whereUnion = $branchIds ? " AND c.branch_id IN ($branchIds)": "";
        $query = "
                SELECT
                (SELECT accounting_id FROM students WHERE id = c.`student_id`) AS cyber_code,
                (SELECT CONCAT(username,'-',full_name) FROM `users` WHERE id = c.`ec_id`) AS ec_name,
                (SELECT cls_name FROM `classes` WHERE id = c.`class_id`) AS class_name,
                (SELECT NAME FROM students WHERE id = c.`student_id`) AS student_name,
                (SELECT crm_id FROM students WHERE id = c.`student_id`) AS crm_id,
                c.`student_id`,c.`count_recharge`,
                (SELECT accounting_id FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_accounting_id,
                c.branch_id,\"reserves\" AS std_status,
                (SELECT NAME FROM `branches` WHERE id = c.`branch_id`) AS branch_name,
                c.`enrolment_start_date`,c.`enrolment_last_date`, c.`status`, c.`type`, c.`id` AS contract_id, c.class_id,
                c.`enrolment_withdraw_date`
                FROM contracts c
                LEFT JOIN `reserves` r ON r.`student_id` = c.`student_id`
                WHERE  (c.`status` =4 AND c.`type` = 10) $whereUnion AND r.`end_date` >=  '$endDate'
                UNION
                SELECT
                (SELECT accounting_id FROM students WHERE id = c.`student_id`) AS cyber_code,
                (SELECT CONCAT(username,'-',full_name) FROM `users` WHERE id = c.`ec_id`) AS ec_name,
                (SELECT cls_name FROM `classes` WHERE id = c.`class_id`) AS class_name,
                (SELECT NAME FROM students WHERE id = c.`student_id`) AS student_name,
                (SELECT crm_id FROM students WHERE id = c.`student_id`) AS crm_id,
                c.`student_id`,c.`count_recharge`,
                (SELECT accounting_id FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_accounting_id,
                c.branch_id,\"pending\" AS std_status,
                (SELECT NAME FROM `branches` WHERE id = c.`branch_id`) AS branch_name,
                c.`enrolment_start_date`,c.`enrolment_last_date`, c.`status`, c.`type`, c.`id` AS contract_id, c.class_id,
                c.`enrolment_withdraw_date`
                FROM contracts c
                WHERE  c.`status` IN(2,3)  $whereUnion
                UNION
                SELECT
                (SELECT accounting_id FROM students WHERE id = c.`student_id`) AS cyber_code,
                (SELECT CONCAT(username,'-',full_name) FROM `users` WHERE id = c.`ec_id`) AS ec_name,
                (SELECT cls_name FROM `classes` WHERE id = c.`class_id`) AS class_name,
                (SELECT NAME FROM students WHERE id = c.`student_id`) AS student_name,
                (SELECT crm_id FROM students WHERE id = c.`student_id`) AS crm_id,
                c.`student_id`,c.`count_recharge`,
                (SELECT accounting_id FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_accounting_id,
                c.branch_id,\"active\" AS std_status,
                (SELECT NAME FROM `branches` WHERE id = c.`branch_id`) AS branch_name,
                c.`enrolment_start_date`,c.`enrolment_last_date`, c.`status`, c.`type`, c.`id` AS contract_id, c.class_id,
                c.`enrolment_withdraw_date` FROM contracts c
                WHERE  $where  c.enrolment_last_date >= '$startDate' AND c.enrolment_start_date <= '$endDate'";

        $data  = u::query($query);
        $pending = [];
        $active = [];
        $reserves = [];
        $dataResp = [];
        foreach ($data as $i => $dt){

            if ($dt->type != 0){

                if ($dt->type == 3 || $dt->type == 4){
                    if ($dt->status  != 2){
                        if ($dt->std_status == "pending"){
                            $pending[$i] = $dt->student_id;
                        }
                        if ($dt->std_status == "active"){
                            $active[$i] = $dt->student_id;
                        }
                        if ($dt->std_status == "reserves"){
                            $reserves[$i] = $dt->student_id;
                        }

                        $dt->start_date = $startDate;
                        $dt->end_date =$endDate ;
                        $dataResp[$i] = $dt;
                    }
                }
                else{
                    if ($dt->std_status == "pending"){
                        $pending[$i] = $dt->student_id;
                    }
                    if ($dt->std_status == "active"){
                        $active[$i] = $dt->student_id;
                    }
                    if ($dt->std_status == "reserves"){
                        $reserves[$i] = $dt->student_id;
                    }

                    $dt->start_date = $startDate;
                    $dt->end_date =$endDate ;
                    $dataResp[$i] = $dt;
                }

            }
        }

        $result = array_diff($pending, $active);
        $dataPending = [];
        $dataPendingList = [];
        $dataActive = [];
        $dataReserves = [];
        $temp = [];
        foreach ($result as $rs => $rss){
            $temp[$rss][] = $rs;
        }

        foreach ($result as $u => $ua){
            $dataPendingList[] = $dataResp[$u];
        }

        if (!$list){
            $uArr = array_unique($result);
            foreach ($uArr as $u => $ua){
                $dataPending[] = self::getMinCountRecharge($dataPendingList,$ua);
            }

            foreach ($active as $ac => $act){
                $dataActive[] = $dataResp[$ac];
            }
            foreach ($reserves as $re => $res){
                $dataReserves[] = $dataResp[$re];
            }
        }

        if (!$list)
            return ['data' => $dataActive, 'pending' => $dataPending,'reserves' =>$dataReserves];
        else
            return ['data' => $dataResp, 'pending' => $result,'reserves' =>$dataReserves];
    }

    public static function getMinCountRecharge($list = [], $std = 0){
        $temp = [];
        foreach ($list as $l =>$li){
            if ($li->student_id === $std){
                $temp[$li->count_recharge] = $l;
            }
        }
        $first = reset($temp);

        return !empty($list[$first]) ? $list[$first] : [];
    }

    public static function sumTuitionActiveNew($tData = [], $t = ''){
        $sem = self::listSem();
        $tData1 = [];
        $data = $tData[$t];
        foreach ($sem as $se){
            $seq = str_replace("g","",$se);
            $data['all'] = 0;
            $sum = array_sum($data);
            if ($sum > 0)
                $tData1['s'.$seq] = number_format(($data[$se]/$sum)*100,2);
            else
                $tData1['s'.$seq] = 0;
        }
        return $tData1;
    }

    public static function listSem(){
        return ['UCREA','BRIGHT','BLACKHOLE'];
    }

    private static function percentageData($dataList){
        $sem = self::listSem();
        $semList = [];
        $semList1 = [];
        foreach ($sem as $se){
            foreach ($dataList as $brId => $data){
                if ($data){
                    $semList[$brId][$se] = static::countTotalTuition($data, $se);
                }
                else{
                    $semList[$brId][$se] = [];
                }
            }
        }

        foreach ($semList as $br => $td){
            $semList1[$br]['UCREA']['total_class'] = self::countTotalClass($td, "UCREA");
            $semList1[$br]['UCREA']['total_student'] = self::countTotalStudent($td, "UCREA");
            $semList1[$br]['UCREA']['total_active'] = self::countStudentActive($td, "UCREA","active");
            $semList1[$br]['UCREA']['total_pending'] = self::countStudentActive($td, "UCREA","pending");
            $semList1[$br]['UCREA']['total_reserves'] = self::countStudentActive($td, "UCREA","reserves");

            $semList1[$br]['BRIGHT']['total_class'] = self::countTotalClass($td, "BRIGHT");
            $semList1[$br]['BRIGHT']['total_student'] = self::countTotalStudent($td, "BRIGHT");
            $semList1[$br]['BRIGHT']['total_active'] = self::countStudentActive($td, "BRIGHT","active");
            $semList1[$br]['BRIGHT']['total_pending'] = self::countStudentActive($td, "BRIGHT","pending");
            $semList1[$br]['BRIGHT']['total_reserves'] = self::countStudentActive($td, "BRIGHT","reserves");

            $semList1[$br]['BLACKHOLE']['total_class'] = self::countTotalClass($td, "BLACKHOLE");
            $semList1[$br]['BLACKHOLE']['total_student'] = self::countTotalStudent($td, "BLACKHOLE");
            $semList1[$br]['BLACKHOLE']['total_active'] = self::countStudentActive($td, "BLACKHOLE","active");
            $semList1[$br]['BLACKHOLE']['total_pending'] = self::countStudentActive($td, "BLACKHOLE","pending");
            $semList1[$br]['BLACKHOLE']['total_reserves'] = self::countStudentActive($td, "BLACKHOLE","reserves");
        }

        foreach ($semList1 as $br => $sem1){
            $semList1[$br]['ALL']['total_class'] = static::countTotalAll($sem1,"total_class");
            $semList1[$br]['ALL']['total_student'] = static::countTotalAll($sem1,"total_student");
            $semList1[$br]['ALL']['total_active'] = static::countTotalAll($sem1,"total_active");
            $semList1[$br]['ALL']['total_pending'] = static::countTotalAll($sem1,"total_pending");
            $semList1[$br]['ALL']['total_reserves'] = static::countTotalAll($sem1,"total_reserves");

        }


        foreach ($semList1 as $br2 => $sem2){
            $semList1[$br2]['ALL']['active_percentage'] = static::countTotalAllPercentage($sem2,"active_percentage");
            $semList1[$br2]['ALL']['pending_percentage'] = static::countTotalAllPercentage($sem2,"pending_percentage");
            $semList1[$br2]['ALL']['reserves_percentage'] = static::countTotalAllPercentage($sem2,"reserves_percentage");
            $semList1[$br2]['ALL']['student_class'] = static::countTotalAllPercentage($sem2,"student_class");
        }

        $sumArr[1] = 'Total';
        $sumArr[2] = static::sumDataAll($semList1,"UCREA","total_class");
        $sumArr[3] = static::sumDataAll($semList1,"UCREA","total_student");
        $sumArr[4] = static::sumDataAll($semList1,"UCREA","total_active");
        $sumArr[5] = static::sumDataAll($semList1,"UCREA","total_reserves");
        $sumArr[6] = static::sumDataAll($semList1,"UCREA","total_pending");

        $sumArr[7] = static::sumDataAll($semList1,"BRIGHT","total_class");
        $sumArr[8] = static::sumDataAll($semList1,"BRIGHT","total_student");
        $sumArr[9] = static::sumDataAll($semList1,"BRIGHT","total_active");
        $sumArr[10] = static::sumDataAll($semList1,"BRIGHT","total_reserves");
        $sumArr[11] = static::sumDataAll($semList1,"BRIGHT","total_pending");

        $sumArr[12] = static::sumDataAll($semList1,"BLACKHOLE","total_class");
        $sumArr[13] = static::sumDataAll($semList1,"BLACKHOLE","total_student");
        $sumArr[14] = static::sumDataAll($semList1,"BLACKHOLE","total_active");
        $sumArr[15] = static::sumDataAll($semList1,"BLACKHOLE","total_reserves");
        $sumArr[16] = static::sumDataAll($semList1,"BLACKHOLE","total_pending");

        $sumArr[17] = static::sumDataAll($semList1,"ALL","total_class");
        $sumArr[18] = static::sumDataAll($semList1,"ALL","total_student");
        $sumArr[19] = static::sumDataAll($semList1,"ALL","total_active");
        $sumArr[20] = static::sumDataAll($semList1,"ALL","total_reserves");
        $sumArr[21] = static::sumDataAll($semList1,"ALL","total_pending");

        $sumArr[22] = $sumArr[13] > 0 ? number_format($sumArr[14]/$sumArr[13],3) : 0 ;
        $sumArr[23] = $sumArr[13] > 0 ? number_format($sumArr[15]/$sumArr[13],3) : 0 ;
        $sumArr[24] = $sumArr[13] > 0 ? number_format($sumArr[16]/$sumArr[13],3) : 0 ;
        $sumArr[25] = $sumArr[17] > 0 ? number_format($sumArr[18]/$sumArr[17],3) : 0 ;

        return ['list'=>$semList1, 'sum'=>$sumArr];
    }

    private static function sumDataAll($array, $key = "UCREA", $sub = "total_class"){
        $count = 0;
        foreach ($array as $br => $item){
            $count += $item[$key][$sub];
        }

        return $count;
    }

    private static function countTotalAll($array, $key = ""){
        $count = 0;
        foreach ($array as $item){
            $count += $item[$key];
        }
        return $count;
    }

    private static function countTotalAllPercentage($array, $key = "active_percentage"){

        if ($key === "active_percentage"){
            if ($array['ALL']['total_student'] == 0)
                return 0;
            return number_format(($array['ALL']['total_active']/$array['ALL']['total_student'])*1,3);
        }


        if ($key === "pending_percentage"){
            if ($array['ALL']['total_student'] == 0)
                return 0;
            return number_format(($array['ALL']['total_pending']/$array['ALL']['total_student'])*1,3);
        }

        if ($key === "reserves_percentage"){
            if ($array['ALL']['total_student'] == 0)
                return 0;
            return number_format(($array['ALL']['total_reserves']/$array['ALL']['total_student'])*1,3);
        }


        if ($key === "student_class"){
            if ($array['ALL']['total_class'] == 0)
                return 0;
            return number_format(($array['ALL']['total_student']/$array['ALL']['total_class'])*1,3);
        }
    }

    private static function countTotalClass($array, $key = "UCREA"){
       $countUC = [];
        foreach($array[$key] as $value){

            if ($value->class_id){
                $countUC[$value->class_id] = 1;
            }
        }

        return sizeof($countUC);
    }

    private static function countStudentActive($array, $key ="UCREA", $std_status = "active"){
        $countUC = 0;
        foreach($array[$key] as $value){
            if ($value->std_status == $std_status){
                $countUC++;
            }
        }
        return $countUC;
    }

    private static function countTotalStudent($array, $key ="UCREA"){
        return sizeof($array[$key]);
    }

    private static function countTotalTuition($array, $key = 0){
        $arrayT = [];
        foreach($array as $value){

            $semName = preg_replace('/[0-9]+/', '', $value->tuition_fee_accounting_id);
            if($semName == $key){
                $arrayT[] = $value;
            }
        }
        return $arrayT;
    }

    public static function sumTuitionActive($data = []){
        $sem = self::listSem();
        $tData = [];
        $tData1 = [];
        array_push($sem,"all");
        foreach ($sem as $se){
            $tData[$se] = self::sumDataTuitionActive($data, $se);
        }
        foreach ($tData as $t =>$td){
            $sum = $tData['all'];
            $seq = str_replace("g","",$t);
            if ($seq != "all" && $sum >0)
                $tData1['s'.$seq] = number_format(($td/$sum)*100,2);
            else
                $tData1['s'.$seq] = 0;
        }
        return ['list'=>$tData, 'percentage'=>$tData1];
    }

    private static function sumDataTuitionActive($array, $key = 0){
        $count = 0;
        foreach($array as $value){

            if(isset($value[$key])){
                $count += $value[$key];
            }
        }
        return $count;
    }

    private function writeDataSheet($sheet, $excel, $rowIndex, $data, $columns)
    {
        $i = 0;
        foreach ($data as $indexItem => $item) {
            $i++;
            $rowIndex = $this->writeRowPercentage($sheet, $excel, $columns, $rowIndex, $item, $indexItem, $i);
        }
    }

    private function processData($data, $br)
    {
        $dataArr = [];
        $dataArr[] = $br;
        foreach ($data as $dt){
            array_push($dataArr, $dt['total_class']);
            array_push($dataArr, $dt['total_student']);
            array_push($dataArr, $dt['total_active']);
            array_push($dataArr, $dt['total_reserves']);
            array_push($dataArr, $dt['total_pending']);

            if (!empty($dt['active_percentage'])){
                array_push($dataArr, $dt['active_percentage']);
            }

            if (!empty($dt['reserves_percentage'])){
                array_push($dataArr, $dt['reserves_percentage']);
            }

            if (!empty($dt['pending_percentage'])){
                array_push($dataArr, $dt['pending_percentage']);
            }

            if (!empty($dt['student_class'])){
                array_push($dataArr, $dt['student_class']);
            }
        }
        return $dataArr;
    }

    private function writeRowPercentage($sheet, $excel, $columns, $rowIndex, $data, $dataIndex, $rootData = null, $indexSubItem = -1)
    {
        $cols = self::getTotalColumns($columns);
        $list = !empty($data["list"]) ? $data["list"]  : [];
        $sum = !empty($data["sum"]) ? $data["sum"]  : [];
        $dataIndex = str_replace("w8","Total",$dataIndex);
        $header = ["$dataIndex","Số Lớp","Tổng Số Học Sinh","Số Hs Đang Học","Số Hs Bảo lưu","Số Hs Chờ Lớp","Số Lớp",
            "Tổng Số Học Sinh","Số Hs Đang Học","Số Hs Bảo lưu","Số Hs Chờ Lớp","Số Lớp","Tổng Số Học Sinh",
            "Số Hs Đang Học","Số Hs Bảo lưu","Số Hs Chờ Lớp","Số Lớp","Tổng Số Học Sinh","Số Hs Đang Học","Số Hs Bảo lưu",
            "Số Hs Chờ Lớp","Tỷ Lệ Active","Tỷ Lệ Bảo lưu","Tỷ Lệ Pending","Số HSTB/ Lớp"];

        $horizontal = "center";
        $vertical = "center";
        if ($rootData > 1){
            for ($index = 0; $index < $cols; $index++) {
                $columnIndex = $index + 1;

                $value = isset($header[$index]) ? $header[$index] :"";
                self::writeCell($sheet, $columnIndex, $rowIndex, $value);
                $excel->setStyleCell($sheet, [$columnIndex, $rowIndex], '078acb', 'ffffff', 10, false,
                true, $horizontal, $vertical, true, "FFFFFF");
            }

            $i = 0;
            $rowIndex1 = 0;
            foreach ($list as $br =>$obj){
                $dataArr = self::processData($obj, $br);
                $i++;
                $rowIndex1 = $rowIndex + $i;
                for ($index = 0; $index < $cols; $index++) {
                    $columnIndex = $index + 1;
                    $value = isset($dataArr[$index]) ? $dataArr[$index] :"";
                    self::writeCell($sheet, $columnIndex, $rowIndex1, $value);
                    $excel->setStyleCell($sheet, [$columnIndex, $rowIndex1], '96dcfe', '004666', 10, false,
                        true, $horizontal, $vertical, true, "FFFFFF");
                }
            }

            $rowIndex2 = $rowIndex1 + 1;
            for ($index = 0; $index < $cols; $index++) {
                $columnIndex = $index + 1;
                $value = isset($sum[$index+1]) ? $sum[$index+1] :"";
                self::writeCell($sheet, $columnIndex, $rowIndex2, $value);
                $excel->setStyleCell($sheet, [$columnIndex, $rowIndex2], '96dcfe', '004666', 10, false,
                    true, $horizontal, $vertical, true, "FFFFFF");
            }
            return $rowIndex2 + 1;
        }
        else{
            self::writeCell($sheet, 1, 11, $dataIndex);
            $i = -1;
            $rowIndex1 = $rowIndex +1;
            foreach ($list as $br =>$obj){
                $dataArr = self::processData($obj, $br);
                $i++;
                $rowIndex1 = $rowIndex + $i;
                for ($index = 0; $index < $cols; $index++) {
                    $columnIndex = $index + 1;
                    $value = isset($dataArr[$index]) ? $dataArr[$index] :"";
                    self::writeCell($sheet, $columnIndex, $rowIndex1, $value);
                    $excel->setStyleCell($sheet, [$columnIndex, $rowIndex1], '96dcfe', '004666', 10, false,
                        true, $horizontal, $vertical, true, "FFFFFF");
                }
            }
            $rowIndex2 = $rowIndex1 + 1;
            for ($index = 0; $index < $cols; $index++) {
                $columnIndex = $index + 1;
                $value = isset($sum[$index+1]) ? $sum[$index+1] :"";
                self::writeCell($sheet, $columnIndex, $rowIndex2, $value);
                $excel->setStyleCell($sheet, [$columnIndex, $rowIndex2], '96dcfe', '004666', 10, false,
                    true, $horizontal, $vertical, true, "FFFFFF");
            }
            return $rowIndex2 + 1;
        }
    }


    public function exportCustomNew($data, $columns, $filename = "export_student_quantity_report_by_date.xlsx", $headerData)
    {
        /*
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        */

        $branchName = !empty($headerData[0]['name']) ? $headerData[0]['name'] : "";
        ini_set('memory_limit','1024M');
        $spreadsheet = new Spreadsheet();
        $i = 0;
        $size = sizeof($data);
        $colEnd = chr(count($columns) + 65);
        $rowIndex = 8;
        foreach ($data as $w => $obj){
            if ($i == 0){
                $j = $i +1;
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getRowDimension($rowIndex)->setRowHeight(50);
                $sheet->setTitle("Tuần $j");
                $reportDate = "Tuần $j : $w";
                $reportDate = str_replace("w$j","",$reportDate);
                ProcessExcel::writeHeadCompanyInfo($spreadsheet, $sheet, $colEnd, $reportName = "BÁO CÁO SỐ LƯỢNG HỌC SINH", $reportDate, null, $branchName);
                foreach ($columns as $key => $value) {
                    $colName = $key < 26 ? chr($key + 65) : 'A' . chr($key - 26 + 65);
                    $sheet->setCellValue("{$colName}$rowIndex", $value['name']);
                    $detail = $obj->detail;
                    foreach ($detail as $t => $ts){
                        $c = $rowIndex + $t +1;
                        $sheet->setCellValue("{$colName}{$c}", static::getDataFillXls($ts,$value["value"]));
                    }

                    $sheet->getColumnDimension($colName)->setWidth(30);
                    ProcessExcel::styleCells($spreadsheet, "{$colName}$rowIndex", "078acb", "FFFFFF", 10, 0, 3, "center", "center", true);
                    $sheet->getStyle("{$colName}$rowIndex")->getAlignment()->setWrapText(true);
                }
            }
            else {
                $objWorkSheet = $spreadsheet->createSheet($i); //Setting index when creating
                $objWorkSheet->getRowDimension($rowIndex)->setRowHeight(50);
                    if ($i < ($size - 1) ){
                        $j = $i+1;
                        $objWorkSheet->setTitle("Tuần $j");
                        $weekName = "Tuần $j";
                    }
                    else{
                        $objWorkSheet->setTitle("Total");
                        $weekName = "Total";
                    }

                $reportDate = "$weekName : $w";
                $reportDate = str_replace("w$j","",$reportDate);
                $reportDate = str_replace("w8","",$reportDate);
                ProcessExcel::writeHeadCompanyInfo($spreadsheet, $objWorkSheet, $colEnd, $reportName = "BÁO CÁO SỐ LƯỢNG HỌC SINH", $reportDate, $i, $branchName);
                foreach ($columns as $key => $value) {
                    $colName = $key < 26 ? chr($key + 65) : 'A' . chr($key - 26 + 65);
                    $objWorkSheet->setCellValue("{$colName}$rowIndex", $value['name']);
                    $detail = $obj->detail;
                    foreach ($detail as $t => $ts){
                        $c = $rowIndex + $t +1;
                        $objWorkSheet->setCellValue("{$colName}{$c}", static::getDataFillXls($ts,$value["value"]));
                    }
                    $objWorkSheet->getColumnDimension($colName)->setWidth($value["width"]);

                    ProcessExcel::styleCells($spreadsheet, "{$colName}$rowIndex", "078acb", "FFFFFF", 10, 0, 3, "center", "center", 'FFFFFF', $i);
                    $objWorkSheet->getStyle("{$colName}$rowIndex")->getAlignment()->setWrapText(true);
                }
            }
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        try {
            $writer->save("php://output");
            exit();
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            $response = new Response();
            return $response->formatResponse(APICode::CANNOT_CONNECT_API, null, $e->getMessage());
        }
    }

    public static function  getDataFillXls($data = [], $key = ""){

        $key = str_replace("detail.","",$key);
        $value =  (!empty($data->{$key}) || $data->{$key} === 0) ? $data->{$key} :"";
        if ($key === "week")
            $value = str_replace("w8","Total",$value);

        return $value;
    }
}