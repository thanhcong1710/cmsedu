<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 7/22/2019
 * Time: 1:58 PM
 */

namespace App\Services;

use App\Models\Student;
use App\Providers\UtilityServiceProvider as u;
use PhpOffice\PhpSpreadsheet\Exception;

class StudentReportService extends ExportService
{

    public function getStudentStudying($params)
    {
        $where = "c.status = 6 ";
        if (!empty($params['branch_ids']) && is_array($params['branch_ids'])) {
            $branchIds = implode(",", $params['branch_ids']);
            $where .= "and c.branch_id in ($branchIds) ";
        }

        if (substr($where, 0, 3) === "and") {
            $where = substr($where, 3, strlen($where));
        }
        if (!empty($where)) {
            $where = " where $where ";
        }

        $total = u::first("select count(1) as total from contracts c $where")->total;
        $limit = "";
        if (!empty($params['limit'])) {
            $l = (int)$params['limit'];
            $p = (int)$params['page'];
            $o = ($p - 1) * $l;
            $limit = " limit  $o, $l ";
        }

        $query = "select 
                     (select name from branches where id = c.branch_id) as branch_name,
                      s.name as student_name,
                      s.crm_id,
                      s.accounting_id as student_accounting_id,
                      (select name from products where id = c.product_id) as product_name,
                      (select name from tuition_fee where id = c.tuition_fee_id) as tuition_fee_name,
                      c.must_charge,
                      c.total_charged,
                      c.debt_amount,
                      c.real_sessions,
                      (select cls_name from classes where id = c.class_id) as class_name,
                      c.enrolment_start_date,
                      c.enrolment_last_date
                   from contracts c inner join students s on c.student_id = s.id
                   $where AND s.status >0 $limit";
        $data = u::query($query);

        return ['data' => $data, 'total' => $total];
    }

    public function exportStudentStudying($params, $template)
    {
        $data = self::getStudentStudying($params);
        $service = new ExportService();
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
        try {
            $service->export($data['data'], $template, "export_student_studying", $headerData);
        } catch (Exception $e) {
        }
    }

    public function getStudentReportByDate($params)
    {
        $where = "c.count_recharge = 0 and p.count = 1 ";
        if (!empty($params['branch_ids']) && is_array($params['branch_ids'])) {
            $branchIds = implode(",", $params['branch_ids']);
            if ($branchIds != 0)
            $where .= " and c.branch_id in ($branchIds) ";
        }

        if (!empty($where)) {
            $where = " where $where ";
        }
        $whereJoinPayment = "c.id = p.contract_id ";
        if (!empty($params['start_date'])) {
            $startDate = $params['start_date'] . " 00:00:00";
            $whereJoinPayment .= "and p.charge_date >= '$startDate' ";
        }
        if (!empty($params['end_date'])) {
            $endDate = $params['end_date'] . " 23:59:59";
            $whereJoinPayment .= "and p.charge_date <= '$endDate' ";
        }
        $total = u::first("select count(1) as total from contracts c inner join payment p on $whereJoinPayment $where")->total;
        $limit = "";
        if (!empty($params['limit'])) {
            $l = (int)$params['limit'];
            $p = (int)$params['page'];
            $o = ($p - 1) * $l;
            $limit = " limit  $o, $l ";
        }

        $query = "select c.student_id, group_concat(c.id) as contract_ids
                   from contracts c inner join payment p on $whereJoinPayment
                   $where
                   group by c.student_id $limit";

        $students = u::query($query);
        if (empty($students)) {
            return null;
        }

        $studentIds = [];
        $contractIds = [];
        foreach ($students as $item) {
            $studentIds[] = $item->student_id;
            $item->contract_ids = explode(",", $item->contract_ids);
            $contractIds = array_merge($contractIds, $item->contract_ids);
        }

        $contractInfos = self::getContractInfoByContractIds($contractIds);
        $studentInfos = self::getStudentInfoByStudentIds($studentIds);
        $data = [];
        foreach ($students as $item) {
            if (!empty($studentInfos[$item->student_id])){
                $student = $studentInfos[$item->student_id];
                $student->contracts = array_map(function ($id) use ($contractInfos) {
                    return $contractInfos[$id];
                }, $item->contract_ids);
                $data[] = $student;
            }

        }
        return ['data' => $data, 'total' => $total];
    }

    private function getContractInfoByContractIds($contractIds)
    {
        $strContractIds = implode(",", $contractIds);
        $query = "select c.id,
                 (select name from branches where id = c.branch_id) as branch_name,
                 (select name from products where id = c.product_id) as product_name,
                 (select cls_name from classes where id = c.class_id) as class_name,
                 (select name from tuition_fee where id = c.tuition_fee_id) as tuition_fee_name,
                 (select charge_date from payment where contract_id = c.id order by charge_date DESC limit 1) as charge_date,
                 (select concat(full_name, ' - ', accounting_id) as ec_name from users where id = c.ec_id) as ec_name,
                 (select concat(full_name, ' - ', accounting_id) as cs_name from users where id = c.cm_id) as cs_name,
                  c.must_charge, c.total_charged,c.debt_amount, c.status
        from contracts c where c.id in ($strContractIds)";
        $data = u::query($query);
        return u::convertArrayToObject($data, function ($item) {
            return $item->id;
        });
    }

    private function getStudentInfoByStudentIds($studentIds)
    {
        $strStudentIds = implode(",", $studentIds);
        $query = "select id, accounting_id, crm_id, name, gender, date_of_birth from students where id in ($strStudentIds)";
        $data = u::query($query);
        return u::convertArrayToObject($data, function ($item) {
            return $item->id;
        });
    }

    public function exportStudentByDate($params, $template)
    {
        $data = self::getStudentReportByDate($params);
        $service = new ExportService();
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
        if (!empty($params['start_date']) && !empty($params['end_date'])) {
            $headerData[] = [
                "name" => "(Từ ngày {$params['start_date']} đến ngày {$params['end_date']})",
                'size' => 10,
                'weight' => false
            ];
        }
        try {
            $service->export($data['data'], $template, "export_student_by_date", $headerData);
        } catch (Exception $e) {
        }
    }

    public function exportStudentByDate1($params, $template, $list = false)
    {
        if (!$list)
            $data = static::getStudentActiveReportByDate($params);
        else
            $data = static::getStudentActiveListReportByDate($params);

        $service = new ExportService();
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
            if($list)
                $this->export($data['data'], $template, "export_student_active_list_by_date", $headerData);
            else {
                $service->export($data, $template, "export_student_active_by_date", $headerData, function ($sheet, $excel, $rowIndex, $columns) use ($data) {
                    self::writeExcelStudentCustom($sheet, $excel, $rowIndex, $data, $columns);
                });
            }
        } catch (Exception $e) {
        }
    }

    public static function getStudentActiveReportByDate($params, $search = false)
    {
        $brand = [];
        $where = "";
        if (!empty($params['branch_ids'])) {
            if (is_array($params['branch_ids'])) {
                $branchIds = implode(",", $params['branch_ids']);
                $where .= "branch_id in ($branchIds) ";
                $brand = $params['branch_ids'];
            }
        }
        if (!empty($params['start_date'])) {
            $startDate = $params['start_date'] . " 00:00:00";
            $where .= "and enrolment_last_date >= '$startDate' ";
        }
        if (!empty($params['end_date'])) {
            $endDate = $params['end_date'] . " 23:59:59";
            $where .= "and enrolment_start_date <= '$endDate' ";
        }
        if (substr($where, 0, 3) === "and") {
            $where = substr($where, 3, strlen($where));
        }
        if (!empty($where)) {
            $where = " AND $where ";
        }
        $limit = "";
        if (!empty($params['limit'])) {
            $l = (int)$params['limit'];
            $p = (int)$params['page'];
            $o = ($p - 1) * $l;
            $limit = " limit  $o, $l ";
        }
        $semIdArr = [1, 2, 3];
        $semNameArr = [1 => "Ucrea", 2 => "Bright IG", 3 => "Black Hole"];

        if (empty($brand)){
            $sql = "SELECT id from `branches` WHERE status = 1";
            $branches = u::query($sql);
            foreach ($branches as $branch){
                $brand[] = $branch->id;
            }
        }

        $query = "SELECT c.`student_id`,c.class_id,c.status,
                (SELECT cls_name FROM classes WHERE id = c.`class_id`) AS cls_name,c.branch_id,
                (SELECT sem_id FROM classes WHERE id = c.`class_id`) AS sem_id,
                (SELECT shift_id FROM sessions WHERE class_id = c.`class_id`) AS shift_id,
                (SELECT NAME FROM `shifts` WHERE id = shift_id) AS shift_name,
                (SELECT NAME FROM `branches` WHERE id = c.`branch_id`) AS branch_name,
                (SELECT class_day FROM `sessions` WHERE class_id = c.`class_id`) AS class_day FROM contracts c
                WHERE 1=1 $where";

        $data = u::query($query);
        $brandArray = [];
        $brandClassIdArray = [];
        $allClassInfoArray = [];
        if (!empty($data)) {
            foreach ($data as $obj) {
                if ($obj->status != 7){
                    foreach ($brand as $bra) {
                        if ($bra == $obj->branch_id) {
                            $brandArray[$bra][] = $obj;
                            $brandClassIdArray[$bra][] = $obj->class_id;
                        }
                    }
                    $allClassInfoArray[$obj->class_id]['cls_name'] = $obj->cls_name;
                    $allClassInfoArray[$obj->class_id]['class_day'] = u::getDayNameVi($obj->class_day);
                    $allClassInfoArray[$obj->class_id]['shift_name'] = $obj->shift_name;
                    $allClassInfoArray[$obj->class_id]['total'] = 0;
                }
            }
        }

        $branchsName = [];
        $brandArray1 = [];
        foreach ($brandArray as $key => $brArr) {
            foreach ($semIdArr as $semId) {
                foreach ($brArr as $obj) {
                    if ($obj->sem_id == $semId)
                        $brandArray1[$key][$semId][] = ($obj->class_id);
                    $branchsName[$key] = $obj->branch_name;
                }
            }
        }
        $brandArray2 = [];
        foreach ($brandArray1 as $key => $br1Arr) {
            foreach ($br1Arr as $id => $obj) {
                $brandArray2[$key][$id] = array_count_values($obj);
            }
        }

        $data = [];

        $totalStudentClassA = 0;
        $totalStudentClassB = 0;
        $totalStudentClassC = 0;
        $totalClassA = 0;
        $totalClassB = 0;
        $totalClassC = 0;

        foreach ($brandArray2 as $id => $br2) {

            $class1Obj = [];
            $class2Obj = [];
            $class3Obj = [];
            $totalStudentClassA += !empty($br2[1]) ? array_sum($br2[1]) : 0;
            $totalStudentClassB += !empty($br2[2]) ? array_sum($br2[2]) : 0;
            $totalStudentClassC += !empty($br2[3]) ? array_sum($br2[3]) : 0;

            $totalClassA += !empty($br2[1]) ? sizeof($br2[1]) : 0;
            $totalClassB += !empty($br2[2]) ? sizeof($br2[2]) : 0;
            $totalClassC += !empty($br2[3]) ? sizeof($br2[3]) : 0;


            $class1 = isset($br2[1]) ? $br2[1] : [];
            $class2 = isset($br2[2]) ? $br2[2] : [];
            $class3 = isset($br2[3]) ? $br2[3] : [];
            $aTotal = [];
            $bTotal = [];
            $cTotal = [];
            $aTotalStudent = 0;
            $bTotalStudent = 0;
            $cTotalStudent = 0;

            foreach ($class1 as $classId => $classTotal) {
                $class1Obj[] = self::setObjClassArr(['id'=>$classId,'total' =>$classTotal],$allClassInfoArray);
                $aTotalStudent += $classTotal;
            }

            $aTotal['class'] = sizeof($class1);
            $aTotal['student'] = $aTotalStudent;

            foreach ($class2 as $classId => $classTotal) {
                $class2Obj[] = self::setObjClassArr(['id'=>$classId,'total' =>$classTotal],$allClassInfoArray);
                $bTotalStudent += $classTotal;
            }
            $bTotal['class'] = sizeof($class2);
            $bTotal['student'] = $bTotalStudent;

            foreach ($class3 as $classId => $classTotal) {
                $class3Obj[] = self::setObjClassArr(['id'=>$classId,'total' =>$classTotal],$allClassInfoArray);
                $cTotalStudent += $classTotal;
            }

            $cTotal['class'] = sizeof($class3);
            $cTotal['student'] = $cTotalStudent;
            $data[] = (object)[
                'id' => $id,
                'branch_name' => $branchsName[$id],
                'detail' => ['A'=>$class1Obj,'B'=>$class2Obj,'C'=>$class3Obj],
                'total' =>[
                    'A'=>['class'=>sizeof($class1),'student'=>array_sum($class1)],
                    'B'=>['class'=>sizeof($class2),'student'=>array_sum($class2)],
                    'C'=>['class'=>sizeof($class3),'student'=>array_sum($class3)]],
                'size'=> max([sizeof($class1Obj),sizeof($class2Obj),sizeof($class3Obj)])
            ];
        }
        return [
            'data' => ($data),
            'total' => (object)[
                'class'=>[
                    'A'=>$totalClassA,
                    'B'=>$totalClassB,
                    'C'=>$totalClassC,
                ],
                'student'=>[
                    'A'=>$totalStudentClassA,
                    'B'=>$totalStudentClassB,
                    'C'=>$totalStudentClassC,
                ],
                'sum'=>[
                    'student'=>($totalStudentClassA + $totalStudentClassB + $totalStudentClassC),
                    'class'=>($totalClassA + $totalClassB + $totalClassC),
                ],
            ]
        ];
    }

    private static function setObjClassArr($obj, $classArr){
        $getClass = [];
        if (isset($classArr[$obj['id']])){
            $getClass = $classArr[$obj['id']];
            $getClass['total'] = $obj['total'];
        }
        return (object)$getClass;
    }

    public function writeExcelStudentCustom($sheet, $excel, $rowIndex, $data, $columns, $loop = false)
    {
        $dataObj = $data['data'];
        $startRow = $rowIndex;
        foreach ($dataObj as $indexItem => $item) {
            $rowIndex = $this->writeRowStd($sheet, $excel, $columns, $rowIndex, $item, $indexItem);
        }

        $cols = self::getTotalColumns($columns);
        self::setColsBgColor($sheet, $excel, $cols, $startRow, $rowIndex);
        $this->writeRowTotal($sheet, $excel, $columns, $rowIndex,$data['total']);
    }

    private function writeRowTotal($sheet, $excel, $columns, $rowIndex, $data){
        $totalCol  = self::getTotalColumns($columns);
        for ($columnIndex=1; $columnIndex <= $totalCol; $columnIndex++){
            $value = self::getValueByIndex($columnIndex,$data);
            self::writeCell($sheet, $columnIndex, $rowIndex, $value);
            $excel->setStyleCell($sheet, [$columnIndex, $rowIndex], '078acb', 'FFFFFF', 12, true,
                true, "center", "center", true, "FFFFFF");
        }
    }



    private static function getValueByIndex($index,$data){
        if ($index == 1)
            return "Tổng";
        elseif ($index == 3)
            return $data->class["A"];
        elseif ($index == 4)
            return $data->student["A"];
        elseif ($index == 7)
            return $data->class["B"];
        elseif ($index == 8)
            return $data->student["B"];
        elseif ($index == 11)
            return $data->class["C"];
        elseif ($index == 12)
            return $data->student["C"];
        elseif ($index == 15)
            return $data->sum["student"];
        elseif ($index == 16)
            return $data->sum["class"];
        elseif ($index == 17)
            return $data->sum["class"];
    }

    public function writeRowStd($sheet, $excel, $columns, $rowIndex, $data, $dataIndex, $rootData = null, $indexSubItem = -1)
    {
        $size = self::getValue("size", $data, null);
        foreach ($columns as $index => $column) {
            $columnIndex = $index+1;
            $value = self::getValue($column['value'], $data, [$dataIndex,$index]);
            if (isset($column['loop'])){
                $value = $value[$column['sub']];
            }
            if (is_array($value)){
                if (isset($column['children'])){
                    $children = $column['children'];
                    self::writexx($index,$children, $value, $rowIndex, $sheet);
                }
            }
            else{
                $total = self::getValue("total", $data, null);
                if ($columnIndex > 2){
                    $columnIndex = $columnIndex+9;
                    $value = self::getClassStudentInfo($columnIndex, $total);
                }
                $excel->mergeCell($columnIndex, $rowIndex, $columnIndex, $rowIndex + $size - 1, $sheet);
                self::writeCell($sheet, $columnIndex, $rowIndex, $value);
            }
        }
        return $rowIndex + $size;
    }

    private function writexx($index, $children, $value, $rowIndex, $sheet){
        foreach ($children as $n => $columnChild){
            foreach ($value as $idx => $obj){
                $rowIndex1 = $rowIndex+$idx;
                self::writeCell($sheet, $columnChild["col"], $rowIndex1, $obj->{$columnChild["value"]});
            }
        };
    }

    private static function setColsBgColor($sheet, $excel, $cols, $startRow, $rowIndex){
        for ($rw = $startRow; $rw < $rowIndex; $rw++){
            for($col=1; $col <= $cols; $col++){
                $excel->setStyleCell($sheet, [$col, $rw], "96dcfe", '004666', 10, false,
                    true, "center", "center", true, "FFFFFF");
            }
        }
    }

    private static function getClassStudentInfo($index, $total){
        $totalClass = 0;
        $totalStudent = 0;
        foreach ($total as $obj){
            $totalStudent += $obj["student"];
            $totalClass += $obj["class"];
        }
        if ($index == 15)
            return $totalStudent;
        else
            return $totalClass;
    }

    public static function getStudentActiveListReportByDate($params)
    {
        $brand = [];
        $where = "";
        if (!empty($params['branch_ids'])) {
            if (is_array($params['branch_ids'])) {
                $branchIds = implode(",", $params['branch_ids']);
                $where .= "branch_id in ($branchIds) ";
                $brand = $params['branch_ids'];
            }
        }
        if (!empty($params['start_date'])) {
            $startDate = $params['start_date'] . " 00:00:00";
            $where .= "and enrolment_last_date >= '$startDate' ";
        }
        if (!empty($params['end_date'])) {
            $endDate = $params['end_date'] . " 23:59:59";
            $where .= "and enrolment_start_date <= '$endDate' ";
        }
        if (substr($where, 0, 3) === "and") {
            $where = substr($where, 3, strlen($where));
        }
        if (!empty($where)) {
            $where = " AND $where ";
        }
        if (empty($brand)){
            $sql = "SELECT id from `branches` WHERE status = 1";
            $branches = u::query($sql);
            foreach ($branches as $branch){
                $brand[] = $branch->id;
            }
        }

        $query =    "SELECT 
                    (SELECT NAME FROM `students` WHERE id = c.`student_id`) AS student_name,
                    c.`student_id`,c.class_id,c.status,
                    (SELECT cls_name FROM classes WHERE id = c.`class_id`) AS cls_name,c.branch_id,
                    (SELECT sem_id FROM classes WHERE id = c.`class_id`) AS sem_id,
                    (SELECT shift_id FROM sessions WHERE class_id = c.`class_id`) AS shift_id,
                    (SELECT NAME FROM `shifts` WHERE id = shift_id) AS shift_name,
                    (SELECT NAME FROM `branches` WHERE id = c.`branch_id`) AS branch_name,
                    (SELECT class_day FROM `sessions` WHERE class_id = c.`class_id`) AS class_day,
                    (SELECT crm_id FROM `students` WHERE id = c.`student_id`) AS crm_id,
                    (SELECT `name` FROM `semesters` WHERE product_id = c.`product_id`) AS seme_name,
                    (SELECT `accounting_id` FROM `students` WHERE id = c.`student_id`) AS accounting_id, c.enrolment_last_date, c.enrolment_start_date
                    FROM contracts c
                    WHERE  1=1  $where ORDER BY c.`branch_id`, c.`product_id`";

        $data = u::query($query);
        $dataNew = [];
        if (!empty($data)) {
            foreach ($data as $obj) {
                if ($obj->status == 7){
                    $obj->status1 = 'Đã withdraw';
                }
                else if ($obj->enrolment_last_date < date('Y-m-d') && $obj->status == 6){
                    $obj->status1 = 'Hết hạn nhưng chưa withdraw';
                }
                else
                    $obj->status1 = '';

                $obj->day_name = u::getDayNameVi($obj->class_day);
                $dataNew[] = $obj;
            }
        }
        return [
            'data' => ($dataNew),
        ];
    }

    public function exportTrialList($params, $template, $users_data)
    {
        $export = $params['export'];
        $data = self::getExportStudentTrialList((object)$params,$users_data);

        $dataNew = [];
        foreach ($data as $item){
            $item->source = u::getStudentSourceName($item->source);
            $dataNew[] = $item;
        }

        if ($export == 0){
            echo json_encode([
                "code" => 200,
                "message" =>"",
                "data" => $dataNew
            ]);
            exit;
        }


        $service = new ExportService();
        $headerData = static::setHeaderExportList($params);
        try {
            $service->export($data, $template, "export_student_trial_list", $headerData);
        } catch (Exception $e) {
        }
    }

    public static function setHeaderExportList($params){
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
        return $headerData;
    }
    public function exportStudentList($params, $template, $users_data)
    {
        $data = self::getExportStudentList((object)$params,$users_data);

        $service = new ExportService();
        $headerData = [];
        try {
            $service->export($data, $template, "export_student_list", $headerData);
        } catch (Exception $e) {
        }
    }

    public function getExportStudentTrialList($search, $auth){
        $start_date = $search->start_date;
        $end_date = $search->end_date;
        $where = "";
        if (!empty($search->branch_ids) && $search->branch_ids !="null"){
            $branch_ids = implode(",",$search->branch_ids);
            $where = " AND c.`branch_id` IN ({$branch_ids})";
        }

        $select ="SELECT s.`name`,s.`date_of_birth`,s.`gud_name1`,
                s.`gud_mobile1`,s.`email`,s.`address`,
                (SELECT `name` FROM `products` WHERE c.`product_id` = id)  cls_name,
                c.`enrolment_start_date`,s.`source`,
                u1.`full_name` cs_name, u1.`hrm_id`,c.`note`,\"-\" AS `status`,
                (SELECT `name` FROM `branches` WHERE c.`branch_id` = id)  branch_name
                FROM contracts c
                LEFT JOIN students s ON c.student_id = s.id
                LEFT JOIN users u1 ON u1.id = c.ec_id
                WHERE s.status >0 AND c.count_recharge = '-1' AND s.branch_id = c.branch_id
                AND c.status > 0 AND c.type = 0 AND c.`enrolment_start_date` != \"\"
                AND c.`enrolment_start_date` BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' {$where}
                GROUP BY c.id ORDER BY c.`branch_id`";
        return u::query($select);
    }

    public function getExportStudentList($search, $auth){
        $role_id = $auth->role_id;
        $user_id = $auth->id;
        $branches = $search->branch ? (int)$search->branch : $auth->branches_ids;
        $where = " AND s.branch_id IN ($branches)";
        if($role_id){
          if ($role_id == ROLE_EC_LEADER) {
            $where .= " AND ( t.ec_id = $user_id OR t.ec_id IN (SELECT u1.id FROM users u1 LEFT JOIN users u2 ON u1.superior_id = u2.hrm_id WHERE u2.id = $user_id))";
          }
          if ($role_id == ROLE_EC) {
            $where .= " AND t.ec_id = $user_id";
          }
        }
        if ($search->gender && $search->gender != '') {
          $where .= " AND s.gender = '$search->gender'";
        }
        if (isset($search->type) && $search->type != '') {
          $where .= " AND s.type = $search->type";
        }
        if (isset($search->ec) && $search->ec != '') {
          $where .= " AND t.ec_id = '$search->ec'";
        }
        $keyword = isset($search->keyword) ? trim($search->keyword) : '';
        if (isset($keyword) && $keyword != '') {
            if ($search->field == '0' || $search->field == '') {
              $where .= " AND
                  ( s.name LIKE '%$keyword%'
                  OR s.crm_id LIKE '%$keyword%'
                  OR s.accounting_id LIKE '%$keyword%'
                  OR s.gud_name1 LIKE '%$keyword%' OR s.gud_name2 LIKE '%$keyword%'
                  OR s.phone LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.gud_mobile2 LIKE '%$keyword%'
                  OR s.address LIKE '%$keyword%'
                  OR s.phone LIKE '$keyword%'
                  OR s.school LIKE '%$keyword%')
              ";
            }
            if ($search->field == '1') {
                $where .= " AND (s.name LIKE '%$keyword%') ";
            }
            if ($search->field == '2') {
                $where .= " AND (s.crm_id LIKE '%$keyword%') ";
            }
            if ($search->field == '3') {
                $where .= " AND (s.accounting_id LIKE '%$keyword%') ";
            }
            if ($search->field == '4') {
                $where .= " AND (s.phone LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.gud_mobile2 LIKE '%$keyword%') ";
            }
            if ($search->field == '5') {
                $where .= " AND (s.gud_name1 LIKE '%$keyword%' OR s.gud_name2 LIKE '%$keyword%') ";
            }
            if ($search->field == '6') {
                $where .= " AND (s.address LIKE '%$keyword%') ";
            }
            if ($search->field == '7') {
                $where .= " AND (s.school LIKE '%$keyword%' ) ";
            }
        }
        if ($role_id == 80 || $role_id == 81)
            $where .= " AND s.creator_id = $user_id";
    
        if(isset($search->status) && $search->status==4){
            $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND type>0)>0 AND 
                (
                (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7)=0
                OR (
                        (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7 AND debt_amount>0 AND total_charged=0)>0
                        AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`=7)>0
                        AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7 AND (debt_amount=0 OR total_charged>0))=0
                    )
                )
            )";
            }elseif(isset($search->status) && $search->status==3){
            $where.=" AND ( (SELECT count(id) FROM reserves 
                WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)>0 
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7)>0)";
            }elseif(isset($search->status) && $search->status=='2.4'){
            $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND  class_id IS NOT NULL AND `type`= 10)>0 
                AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 )";
            }elseif(isset($search->status) && $search->status=='2.3'){
            $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND  class_id IS NOT NULL AND `type` IN(5,85))>0 
                AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 )";
            }elseif(isset($search->status) && $search->status=='2.2'){
            $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND  class_id IS NOT NULL AND `type` IN(6,86))>0 
                AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 )";
            }elseif(isset($search->status) && $search->status=='2.1'){
            $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND  enrolment_start_date <= CURRENT_DATE AND class_id IS NOT NULL AND `type` NOT IN(5,6,86,85,10))>0 
                AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 )";
            }elseif(isset($search->status) && $search->status=='2.5'){
            $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND enrolment_start_date > CURRENT_DATE AND  class_id IS NOT NULL AND `type` NOT IN(5,6,86,85,10))>0 
                AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 )";
            }elseif(isset($search->status) && $search->status=='1.4'){
            $where.=" AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  start_date <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NOT NULL)=0
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NULL AND `type` IN (10))>0";
            }elseif(isset($search->status) && $search->status=='1.3'){
            $where.=" AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  start_date <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NOT NULL)=0
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NULL AND `type` IN (5,85))>0";
            }elseif(isset($search->status) && $search->status=='1.2'){
            $where.=" AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  start_date <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NOT NULL)=0
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NULL AND debt_amount=0 )>0";
            }elseif(isset($search->status) && $search->status=='1.1'){
            $where.=" AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  start_date <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NOT NULL)=0
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NULL AND `type` NOT IN (5,85) AND debt_amount>0 AND total_charged>0)>0";
            }elseif(isset($search->status) && $search->status=='5'){
            $where.=" AND (SELECT count(id) FROM contracts WHERE student_id=s.id)=0";
            }elseif(isset($search->status) && $search->status=='6'){
            $where.=" AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND `type`>0 AND `debt_amount`>0 AND (total_charged=0 OR total_charged IS NULL))>0
            AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND `type`>0 AND (total_charged>0 OR debt_amount=0))=0";
        }
    
        $select = "SELECT
                  DISTINCT(s.id), s.accounting_id, s.cms_id,s.crm_id, s.source, s.status,
                  (SELECT so.name FROM sources so WHERE so.`id`  = s.source) AS source_name,
                  s.date_of_birth,
                  s.type student_type,
                  s.name,
                  s.email student_email,
                  s.phone student_phone,
                  s.gender student_gender,
                  s.address student_address,
                  s.date_of_birth student_birthday,
                  COALESCE(s.phone, s.gud_name1) phone,
                  COALESCE(s.gud_name1, s.gud_name2) parent_name,
                  COALESCE(s.avatar, 'noavatar.png') student_avatar,
                  COALESCE(s.gud_email1, s.gud_email2) parent_email,
                  COALESCE(s.gud_mobile1, s.gud_mobile2) parent_mobile,
                  (SELECT name FROM branches WHERE id=s.branch_id) AS branch_name,
                  s.school student_school, s.attached_file student_profile, s.school_grade student_school_grade,
                  (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE id=t.ec_id) ec_name,
                  (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE id=t.cm_id) cm_name,
                  (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE id=s.creator_id) creator_name,
                  (SELECT COUNT(id) FROM `customer_care` WHERE crm_id = s.crm_id) AS total_care,
                  (SELECT cl.cls_name FROM classes cl LEFT JOIN contracts c ON c.class_id = cl.id WHERE c.student_id = s.id ORDER BY c.id DESC LIMIT 0, 1) cls_name,
                  (SELECT e.status FROM enrolments e WHERE e.student_id = s.id ORDER BY e.id DESC LIMIT 0, 1) class_status,
                  (SELECT p.charge_date FROM payment AS p LEFT JOIN contracts AS ct ON ct.id=p.contract_id WHERE ct.student_id=s.id ORDER BY p.charge_date DESC LIMIT 1) AS last_charge_date,
                  (SELECT `name` FROM students WHERE id=s.sibling_id ) AS sibling_name,
                  (SELECT `title` FROM jobs WHERE id=s.gud_job1 ) AS gud_job1,
                  (SELECT `title` FROM jobs WHERE id=s.gud_job2 ) AS gud_job2,
                  s.school,s.school_level,s.gud_birth_day2,s.gud_birth_day1,s.gud_email1,s.gud_email2,s.gud_mobile2,s.gud_mobile1,s.gud_name1,s.gud_name2,
                  (SELECT name FROM districts WHERE id= s.district_id) AS district_name,
                  (SELECT name FROM provinces WHERE id= s.province_id) AS province_name";
        $query = " FROM students s
            LEFT JOIN term_student_user t ON t.student_id = s.id
          WHERE s.id > 0";
        $final_query = "$select $query AND s.status >0 $where ";
        $data = u::query($final_query);
        if ($data) {
            if( $role_id !='999999999'){
                $data_map = array_map(function($arr){
                $arr->student_avatar = file_exists(AVATAR.DS.str_replace('/', DS, AVATAR_LINK.$arr->student_avatar)) ? AVATAR_LINK.$arr->student_avatar : AVATAR_LINK.'noavatar.png';
                $student_status = Student::getStatusStudent($arr->id);
                $arr->status_id = $student_status->status;
                $arr->status_name = $student_status->status_name;
                $arr->parent_mobile = str_replace(substr($arr->parent_mobile,4,3),'***',$arr->parent_mobile);
                }, $data);
            }else{
                $data_map = array_map(function($arr){
                $arr->student_avatar = file_exists(AVATAR.DS.str_replace('/', DS, AVATAR_LINK.$arr->student_avatar)) ? AVATAR_LINK.$arr->student_avatar : AVATAR_LINK.'noavatar.png';
                $student_status = Student::getStatusStudent($arr->id);
                $arr->status_id = $student_status->status;
                $arr->status_name = $student_status->status_name;
                }, $data); 
            }
        }
        return $data;
    }

    public function exportPendingActiveList($params, $template, $users_data)
    {
        $data = self::getPendingActiveList((object)$params,$users_data);

        $dataNew = [];
        foreach ($data as $item){
            $item->source = u::getStudentSourceName($item->source);
            $dataNew[] = $item;
        }

        $service = new ExportService();
        $headerData = static::setHeaderExportList($params);
        try {
            $service->export($data, $template, "export_student_active_list", $headerData);
        } catch (Exception $e) {
        }
    }

    public function exportStudentActiveList($params, $template, $users_data)
    {
        $data = self::getStudentActiveList((object)$params,$users_data);

        $dataNew = [];
        foreach ($data as $item){
            $item->source = u::getStudentSourceName($item->source);
            $dataNew[] = $item;
        }

        $service = new ExportService();
        $headerData = static::setHeaderExportList($params);
        try {
            $service->export($data, $template, "export_student_pending_list", $headerData);
        } catch (Exception $e) {
        }
    }

    public function getPendingActiveList($search, $auth){
        $start_date = $search->start_date;
        $end_date = $search->end_date;
        $where = "";
        if (!empty($search->branch_ids) && $search->branch_ids !="null"){
            $branch_ids = implode(",",$search->branch_ids);
            $where = " AND c.`branch_id` IN ({$branch_ids})";
        }

        $select ="SELECT s.`name`,s.`date_of_birth`,s.`gud_name1`,
                s.`gud_mobile1`,s.`email`,s.`address`,
                (SELECT `name` FROM `products` WHERE c.`product_id` = id)  cls_name,
                c.`enrolment_start_date`,s.`source`,
                u1.`full_name` cs_name, u1.`hrm_id`,c.`note`,\"-\" AS `status`,
                (SELECT `name` FROM `branches` WHERE c.`branch_id` = id)  branch_name
                FROM contracts c
                LEFT JOIN students s ON c.student_id = s.id
                LEFT JOIN users u1 ON u1.id = c.ec_id
                WHERE s.status >0 AND c.count_recharge = '-1' AND s.branch_id = c.branch_id
                AND c.status > 0 AND c.type = 0 AND c.`enrolment_start_date` != \"\"
                AND c.`enrolment_start_date` BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' {$where}
                GROUP BY c.id ORDER BY c.`branch_id`";
        return u::query($select);
    }

    public function getStudentActiveList($search, $auth){
        $start_date = $search->start_date;
        $end_date = $search->end_date;
        $where = "";
        if (!empty($search->branch_ids) && $search->branch_ids !="null"){
            $branch_ids = implode(",",$search->branch_ids);
            $where = " AND c.`branch_id` IN ({$branch_ids})";
        }

        $select ="SELECT s.`name`,s.`date_of_birth`,s.`gud_name1`,
                s.`gud_mobile1`,s.`email`,s.`address`,
                (SELECT `name` FROM `products` WHERE c.`product_id` = id)  cls_name,
                c.`enrolment_start_date`,s.`source`,
                u1.`full_name` cs_name, u1.`hrm_id`,c.`note`,\"-\" AS `status`,
                (SELECT `name` FROM `branches` WHERE c.`branch_id` = id)  branch_name
                FROM contracts c
                LEFT JOIN students s ON c.student_id = s.id
                LEFT JOIN users u1 ON u1.id = c.ec_id
                WHERE s.status >0 AND c.count_recharge = '-1' AND s.branch_id = c.branch_id
                AND c.status > 0 AND c.type = 0 AND c.`enrolment_start_date` != \"\"
                AND c.`enrolment_start_date` BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' {$where}
                GROUP BY c.id ORDER BY c.`branch_id`";
        return u::query($select);
    }
}
