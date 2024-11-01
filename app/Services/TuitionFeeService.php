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

class TuitionFeeService extends ExportService
{
    public function exportPercentageByDate($params, $template, $data)
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
            $title = [];
            foreach($data["label"] as $label){
                $title[]= ['name' => $label["title"], 'width' => 15,'value' => 'vl'];
            }
            $columns = $template->getColumns();
            if (isset($data["month"]) && $data["month"] >0){
                for($i=1; $i<= $data["month"]; $i++){
                    $columns[$i] = [
                        'name' => $data["dates"][$i],
                        'value' => 'vl',
                        'children' => $title
                    ];
                }
            }

            $this->exportNew($data, $template, "export_tuition_percentage_by_date", $headerData, $columns, function ($sheet, $excel, $rowIndex, $columns) use ($data) {
                self::writeDataPercentageSheet($sheet, $excel, $rowIndex, $data, $columns);
            });
        } catch (Exception $e) {
        }
    }

    public function exportByDate($params, $template, $data)
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
            //$this->export($data['temp'], $template, "export_student_history_by_branch", $headerData);
            $this->export($data, $template, "export_tuition_fee_by_date", $headerData, function ($sheet, $excel, $rowIndex, $columns) use ($data) {
               self::writeDataSheet($sheet, $excel, $rowIndex, $data, $columns);
            });
        } catch (Exception $e) {
        }
    }

    private function writeDataSheet($sheet, $excel, $rowIndex, $data, $columns)
    {
        $dataObj = $data['data'];
        foreach ($dataObj as $indexItem => $item) {
            $rowIndex = $this->writeRow($sheet, $excel, $columns, $rowIndex, $item, $indexItem);
        }
        $this->writeRowTotal($sheet, $excel, $columns, $rowIndex,$data['total']);
    }

    private function processDataNext($array,$label, $case =0)
    {
        $value = null;
        if (isset($array[$label]))
            $value =  $array[$label];
        return $value;
    }

    private function processData($data, $b)
    {
        $label = array_keys($data["label"]);
        $tuition = $data["tuition"];
        $sum = $data["sum"];
        $processData = [];
        $sumReport = [];
        array_push($processData,$data["branches"][$b]);
        array_push($sumReport,"Tổng");
        for($i=1; $i<= $data['month'];$i++){
            foreach ($label as $lb){
                $list = self::processDataNext($tuition['w'.$i]['list'][$b], $lb, 0);
                if ($list || $list === 0){
                    array_push($processData,$list);
                }
                $percentage = self::processDataNext($tuition['w'.$i]['percentage'][$b], $lb, 1);
                if ($percentage|| $percentage === 0){
                    array_push($processData,$percentage);
                }

                $sumObj = self::processDataNext($sum['w'.$i]['list'], $lb, 0);

                if ($sumObj|| $sumObj === 0){
                    array_push($sumReport,$sumObj);
                }

                $sumObj1 = self::processDataNext($sum['w'.$i]['percentage'], $lb, 0);

                if ($sumObj1|| $sumObj1 === 0){
                    array_push($sumReport,$sumObj1);
                }
            }
        }

        return ['data' =>$processData, 'sum' =>$sumReport];
    }

    private function writeDataPercentageSheet($sheet, $excel, $rowIndex, $data, $columns)
    {
        $processData = [];
        $processDataSum = [];
        foreach ($data["branches"] as $br => $branch){
            $processDataList = self::processData($data, $br);
            $processData[] = $processDataList['data'];
            $processDataSum[] = $processDataList['sum'];
        }

        foreach ($processData as $indexItem => $item) {
            $rowIndex = $this->writeRowPercentage($sheet, $excel, $columns, $rowIndex, $item, $indexItem);
        }
        $processDataSumExport = $processDataSum ? $processDataSum[0] :[];
        $this->writeRowPercentage($sheet, $excel, $columns, $rowIndex, $processDataSumExport, 0);
        
    }

    private function writeRowTotal($sheet, $excel, $columns, $rowIndex, $data){
        $columnIndexNext = 6;
        foreach($columns as $index => $column){
            if ($index > 1){
                foreach($column['children'] as $c => $child) {
                    $columnIndex = ($c + $columnIndexNext + (8 * ($index - 2) - 2));
                    $total = isset($data[$child['name']]['total']) ? $data[$child['name']]['total'] : "";
                    $revenue = isset($data[$child['name']]['revenue']) ? $data[$child['name']]['revenue'] : "";
                    self::writeCell($sheet, $columnIndex, $rowIndex, $total);
                    self::writeCell($sheet, $columnIndex, $rowIndex +1, $revenue);
                    $excel->setStyleCell($sheet, [$columnIndex, $rowIndex], '078acb', 'FFFFFF', 10, true,
                    true, "center", "center", true, "FFFFFF");
                    $excel->setStyleCell($sheet, [$columnIndex, $rowIndex+1], '078acb', 'FFFFFF', 10, true,
                        true, "center", "center", true, "FFFFFF");
                }
            }else if ($index == 0){
                $columnIndex = 1;
                if (!empty($column['fields'])) {
                    foreach ($column['fields'] as $f => $field) {
                        self::writeCell($sheet, $columnIndex +1, $rowIndex + $f, $field);
                        $excel->setStyleCell($sheet, [$columnIndex +1, $rowIndex + $f], '078acb', 'FFFFFF', 10, true,
                            true, "center", "center", true, "FFFFFF");
                    }
                }
                if (!empty($column['children'])){
                    foreach($column['children'] as $c => $chn){
                        self::writeCell($sheet, $columnIndex + 2, $rowIndex +$c, u::get($data, strtoupper($chn['value'])));
                        $excel->setStyleCell($sheet, [$columnIndex +2, $rowIndex +$c], '078acb', 'FFFFFF', 10, true,
                            true, "center", "center", true, "FFFFFF");
                    }
                }

                $excel->mergeCell($columnIndex, $rowIndex, $columnIndex, $rowIndex + 1, $sheet);
                self::writeCell($sheet, $columnIndex, $rowIndex, "TỔNG");
                $excel->setStyleCell($sheet, [$columnIndex, $rowIndex], '078acb', 'FFFFFF', 10, true,
                    true, "center", "center", true, "FFFFFF");

            }

        }
    }

    public function writeRow($sheet, $excel, $columns, $rowIndex, $data, $dataIndex, $rootData = null, $indexSubItem = -1)
    {
        $backgroundColor = $rowIndex % 2 === 0 ? '96dcfe' : 'ccefff';
        $horizontal = isset($columns['horizontal']) ? $columns['horizontal'] : "center";
        $vertical = isset($columns['vertical']) ? $columns['vertical'] : "center";
        $size = 2;
        foreach ($columns as $index => $column) {
            $columnIndex = $index+1;
            if ($index == 0){
                if (!empty($column['fields'])) {
                    foreach ($column['fields'] as $f => $field) {
                        self::writeCell($sheet, $columnIndex + 1, $rowIndex + $f, $field);
                        $excel->setStyleCell($sheet, [$columnIndex + 1, $rowIndex + $f], $backgroundColor, '004666', 10, false,
                            true, $horizontal, $vertical, true, "FFFFFF");
                    }
                }
                if (!empty($column['children'])){
                    foreach($column['children'] as $c => $chn){
                        self::writeCell($sheet, $columnIndex + 2, $rowIndex +$c, u::get($data, $chn['value']));
                        $excel->setStyleCell($sheet, [$columnIndex + 2, $rowIndex +$c], $backgroundColor, '004666', 10, false,
                            true, $horizontal, $vertical, true, "FFFFFF");
                    }
                }

                $excel->mergeCell($columnIndex, $rowIndex, $columnIndex, $rowIndex + $size - 1, $sheet);
                $value = self::getValue($column['value'], $data, [$dataIndex,$index]);
                self::writeCell($sheet, $columnIndex, $rowIndex, $value);
                $excel->setStyleCell($sheet, [$columnIndex, $rowIndex], $backgroundColor, '004666', 10, false,
                    true, $horizontal, $vertical, true, "FFFFFF");
            }
            else if($index > 1){
                $columnIndexNext = $columnIndex +3;
                if (!empty($data['detail'])){
                        if (isset($column['loop'])){
                            $dataObj = isset($data['detail'][$column['name']]) ? $data['detail'][$column['name']] : [];
                            foreach($column['children'] as $c => $child){
                                $columnIndex =  ($c + $columnIndexNext + (8*($index-2) - $index));
                                self::writeCell($sheet, $columnIndex, $rowIndex, $dataObj[$child['name']]['total']);
                                $excel->setStyleCell($sheet, [$columnIndex, $rowIndex], $backgroundColor, '004666', 10, false,
                                    true, $horizontal, $vertical, true, "FFFFFF");
                                self::writeCell($sheet, $columnIndex, $rowIndex+1, $dataObj[$child['name']]['revenue']);
                                $excel->setStyleCell($sheet, [$columnIndex,  $rowIndex+1], $backgroundColor, '004666', 10, false,
                                    true, $horizontal, $vertical, true, "FFFFFF");

                            }
                        }
                }
            }
        }
        return $rowIndex + $size;
    }

    public function write1(Worksheet $sheet, ExcelService $excel, $data, $template, $headerData = null, $callable = null)
    {
        $columns = $template->getColumns();
        $header = $template->getHeader($headerData);
        $index = $this->writeHeader($header, $sheet, $excel, static::getTotalColumns($columns)) + 1;
        $index = $this->writeTableHeader($columns, $index, $sheet, $excel);
        $rowIndex = sizeof($header) > 2 ? 12 : $index +1;
        $sheet->mergeCells('A10:B10');
        if (!is_array($data) || empty($data)) {
            return;
        }
        if (is_callable($callable)) {
            call_user_func($callable, $sheet, $excel, $rowIndex,$columns);
        }
    }

    public static function getTuitionFeeReportByDate($params)
    {
        $brand = [];
        $where = "";
        $startDate = "";
        $endDate = "";
        if (!empty($params['branch_ids'])) {
            if (is_array($params['branch_ids'])  && $params['branch_ids'][0] != 0) {
                $branchIds = implode(",", $params['branch_ids']);
                $where = " AND c.branch_id in ($branchIds) ";
                $brand = $params['branch_ids'];
            }
        }
        if (!empty($params['start_date'])) {
            $startDate = $params['start_date'] . " 00:00:00";
            //$where .= "and enrolment_last_date >= '$startDate' ";
        }
        if (!empty($params['end_date'])) {
            $endDate = $params['end_date'] . " 23:59:59";
            //$where .= "and enrolment_start_date <= '$endDate' ";
        }

        if (empty($brand)){
            $sql = "SELECT id from `branches` WHERE status = 1";
            $branches = u::query($sql);
            foreach ($branches as $branch){
                $brand[] = $branch->id;
            }
        }

        $query = "SELECT c.`student_id`,c.class_id,c.`total_charged`,c.tuition_fee_id,p.`charge_date`,
                (SELECT accounting_id FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_accounting_id,
                (SELECT NAME FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_name,
                (SELECT cls_name FROM classes WHERE id = c.`class_id`) AS cls_name,c.branch_id,
                (SELECT sem_id FROM classes WHERE id = c.`class_id`) AS sem_id,
                (SELECT shift_id FROM sessions WHERE class_id = c.`class_id`) AS shift_id,
                (SELECT NAME FROM `shifts` WHERE id = shift_id) AS shift_name,
                (SELECT NAME FROM `branches` WHERE id = c.`branch_id`) AS branch_name,
                (SELECT class_day FROM `sessions` WHERE class_id = c.`class_id`) AS class_day,c.`id`,c.status,c.`enrolment_withdraw_date`, c.`type` FROM contracts c
                LEFT JOIN payment p ON c.id = p.contract_id 
                WHERE c.`status` IN(2,3,4,6,5,7) 
                AND ((p.charge_date >= '$startDate' AND p.charge_date <= '$endDate') OR c.`enrolment_last_date` IS NULL)  $where 
                UNION
                SELECT c.`student_id`,c.class_id,c.`total_charged`,c.tuition_fee_id,p.`charge_date`,
                (SELECT accounting_id FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_accounting_id,
                (SELECT NAME FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_name,
                (SELECT cls_name FROM classes WHERE id = c.`class_id`) AS cls_name,c.branch_id,
                (SELECT sem_id FROM classes WHERE id = c.`class_id`) AS sem_id,
                (SELECT shift_id FROM sessions WHERE class_id = c.`class_id`) AS shift_id,
                (SELECT NAME FROM `shifts` WHERE id = shift_id) AS shift_name,
                (SELECT NAME FROM `branches` WHERE id = c.`branch_id`) AS branch_name,
                (SELECT class_day FROM `sessions` WHERE class_id = c.`class_id`) AS class_day,c.`id`,c.status,c.`enrolment_withdraw_date`, c.`type` FROM contracts c
                LEFT JOIN payment p ON c.id = p.contract_id
                WHERE c.`status` IN(2,3,4,6,5,7)  $where  
                AND (c.enrolment_last_date >= '$startDate' AND c.enrolment_start_date <= '$endDate')";

        /*$query =    "SELECT c.`student_id`,c.class_id,c.`total_charged`,c.tuition_fee_id,p.`charge_date`,
                    (SELECT accounting_id FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_accounting_id,
                    (SELECT NAME FROM `branches` WHERE id = c.`branch_id`) AS branch_name,
                    c.`id`,c.status,c.branch_id,c.`enrolment_start_date`,c.`enrolment_last_date` FROM contracts c
                    LEFT JOIN payment p ON c.id = p.contract_id 
                    WHERE c.`created_at` >='2019-08-01 00:00:00' AND c.`status` IN(2,3,4,6,7) 
                    AND ((p.charge_date >= '$startDate' AND p.charge_date <= '$endDate') OR c.`enrolment_last_date` IS NULL) $where
                    UNION
                    SELECT c.`student_id`,c.class_id,c.`total_charged`,c.tuition_fee_id,p.`charge_date`,
                    (SELECT accounting_id FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_accounting_id,
                    (SELECT NAME FROM `branches` WHERE id = c.`branch_id`) AS branch_name,
                    c.`id`,c.status,c.branch_id,c.`enrolment_start_date`,c.`enrolment_last_date`FROM contracts c
                    LEFT JOIN payment p ON c.id = p.contract_id
                    WHERE c.`created_at` >='2019-08-01 00:00:00' AND c.`status` IN(2,3,4,6,7) $where 
                    AND (c.enrolment_last_date >= '$startDate' AND c.enrolment_start_date <= '$endDate')";*/

        //var_dump($query);exit;
        $data = u::query($query);
        $dataTemp = $data;
        $dataTemp1 = [];
        foreach ($dataTemp as $temp){
              if ($temp->status == 7){
                  if ($temp->enrolment_withdraw_date != null){
                      if ($temp->enrolment_withdraw_date >= $startDate && $temp->enrolment_withdraw_date <= $endDate){
                          if ($temp->type != 0){
                              $dataTemp1[] = $temp;
                          }
                      }
                  }
              }
              else{
                  if ($temp->type != 0){
                      $dataTemp1[] = $temp;
                  }
              }
        }

        $studentTempUnique = [];
        $studentTempAll = [];
        $dataTempFinal = [];
        $dataTempAllFinalArr = [];
        foreach ($dataTemp1 as $key => $dataTemp1Obj){
            if ($dataTemp1Obj->charge_date != NULL){
                if (strtotime($dataTemp1Obj->charge_date) <= strtotime($endDate)){
                    $studentTempUnique[$dataTemp1Obj->student_id] = $key;
                    $studentTempAll[$dataTemp1Obj->student_id][$key] = $dataTemp1Obj->id;
                }
            }
            else{
                $studentTempAll[$dataTemp1Obj->student_id][$key] = $dataTemp1Obj->id;
                $studentTempUnique[$dataTemp1Obj->student_id] = $key;
            }
        }
        $q = [];
        $strKey = '';
        foreach ($studentTempAll as $studentId => $tempAll){
            $strKey .= self::reCountObjStudent($tempAll).',';
        }

        $strKey = rtrim($strKey, ",");
        $strKeyArr = explode(",",$strKey);
        //var_dump($strKeyArr);exit;
        foreach ($strKeyArr as $item){
            $dataTempFinal[] = $dataTemp1[$item];
        }

        foreach ($studentTempUnique as $item){
            $dataTempAllFinalArr[] = $dataTemp1[$item];
        }

        $data = $dataTempFinal;
        $branchsTuitionArr = [];
        if (!empty($data)) {
            foreach ($data as $obj) {
                foreach ($brand as $bra) {
                    if ($bra == $obj->branch_id) {
                       //if (strtotime($endDate) >= strtotime($obj->charge_date)){
                            $branchsTuitionArr[$bra]['branch_name']= $obj->branch_name;
                            $branchsTuitionArr[$bra]['data'][$obj->tuition_fee_accounting_id][] = $obj->total_charged;
                       // }
                    }
                }
            }
        }

        $branchsTuitionArrTemp = [];
        /*unique*/
            foreach ($dataTempAllFinalArr as $obj) {
                foreach ($brand as $bra) {
                    if ($bra == $obj->branch_id) {
                        $branchsTuitionArrTemp[$bra]['branch_name']= $obj->branch_name;
                        $branchsTuitionArrTemp[$bra]['data'][$obj->tuition_fee_accounting_id][] = $obj->total_charged;
                    }
                }
            }
        /*end*/

        $ucrea = ['UCREA01','UCREA02','UCREA03','UCREA04','UCREA05','UCREA06','UCREA12','UCREA24'];
        $brightig = ['BRIGHT01','BRIGHT02','BRIGHT03','BRIGHT04','BRIGHT05','BRIGHT06','BRIGHT12','BRIGHT24'];
        $blackhole = ['BLACKHOLE01','BLACKHOLE02','BLACKHOLE03','BLACKHOLE04','BLACKHOLE05','BLACKHOLE06','BLACKHOLE12','BLACKHOLE24'];

        $objData = [];
        $objTotal = [];


        foreach ($branchsTuitionArr as $branchId => $branchsTuition){
            $objData[$branchId]['branch_name'] = $branchsTuition['branch_name'];
            $objData[$branchId]['detail'] = self::setObjClassArr($ucrea,$brightig,$blackhole,$branchsTuition['data']);
            $objData[$branchId]['total'] =  self::reCountObjClassArr($branchsTuitionArrTemp[$branchId]['data']);
            // self::reCountObjClassArr($branchsTuition['data']);//self::setObjClassArr($ucrea,$brightig,$blackhole,$branchsTuition['data'],2);
            $objData[$branchId]['revenue'] = self::reCountObjClassArr($branchsTuition['data'],'revenue');//self::setObjClassArr($ucrea,$brightig,$blackhole,$branchsTuition['data'],3);
        }

        foreach($ucrea as $uc){
            $objTotal[$uc] = self::getTotalAndRevenue($uc,$objData,"UCREA");
        }
        foreach($brightig as $br){
            $objTotal[$br] = self::getTotalAndRevenue($br,$objData,"BRIGHT");
        }
        foreach($blackhole as $bl){
            $objTotal[$bl] = self::getTotalAndRevenue($bl,$objData,"BLACKHOLE");
        }


        $total = self::getTotalAndRevenue(null,$objData,"TOTAL");
        $revenue = self::getTotalAndRevenue(null,$objData,"REVENUE");
        $objTotal["TOTAL"] = $total;
        $objTotal["REVENUE"] = $revenue;
        //var_dump($objData);exit;
        return [
            'data' => ($objData),
            'temp' => ($dataTempFinal),
            'total' => ($objTotal),
        ];
    }

    private static function reCountObjStudent($data){
        $uArr =  array_unique($data);
        $keys = array_keys($uArr);
        return implode(',',$keys);
    }
    private static function reCountObjClassArr($data, $key = "total"){
        $total = 0;
        $revenue = 0;
        foreach ($data as $obj){
            $revenue += array_sum($obj);
            $total += sizeof($obj);
        }

        return $key == "total"  ? $total : number_format($revenue);
    }
    private static function getTotalAndRevenue($uc, $objData, $cName)
    {
        $total = 0;
        $revenue = 0;
        foreach ($objData as $obj) {
            if (!empty($obj['detail'][$cName][$uc])) {
                $total += $obj['detail'][$cName][$uc]['total'];
                $revenue += str_replace(',','',$obj['detail'][$cName][$uc]['revenue']);
            }
            if ($cName == "TOTAL")
                $total += $obj['total'];
            if ($cName == "REVENUE")
                $revenue += str_replace(',','',$obj['revenue']);

        }
        if ($cName == "TOTAL")
            return $total;
        if ($cName == "REVENUE")
            return number_format($revenue);

        return ['total' => $total,'revenue' => number_format($revenue)];
    }

    private static function setObjClassArr($ucrea,$brightig,$blackhole,$objClass, $ref = 1){
        $objClassNew = [];
        $totalALL = 0;
        foreach($objClass as $semId => $data ){
            $semName = preg_replace('/[0-9]+/', '', $semId);
            //if ($semName == "BLACKHOLE"){
                foreach($blackhole as $bh){
                    if(!empty($objClass[$bh]) && sizeof($objClass[$bh]) > 1){
                        $objClassNew['BLACKHOLE'][$bh] = ['total' => sizeof($objClass[$bh]), 'revenue' => number_format(array_sum($objClass[$bh]))];
                    }
                    else{
                        $objClassNew['BLACKHOLE'][$bh] = ['total' => 0, 'revenue' => 0];
                    }
                }
           // }elseif($semName == "BRIGHT"){
                foreach($brightig as $bt){
                    if(!empty($objClass[$bt]) && sizeof($objClass[$bt]) > 1){
                        $objClassNew['BRIGHT'][$bt] = ['total' => sizeof($objClass[$bt]), 'revenue' => number_format(array_sum($objClass[$bt]))];
                    }
                    else{
                        $objClassNew['BRIGHT'][$bt] = ['total' => 0, 'revenue' => 0];
                    }
                }
            //}elseif($semName == "UCREA"){
                foreach($ucrea as $uc){
                    if(!empty($objClass[$uc]) && sizeof($objClass[$uc]) > 1){
                        $objClassNew['UCREA'][$uc] = ['total' => sizeof($objClass[$uc]), 'revenue' => number_format(array_sum($objClass[$uc]))];
                    }
                    else{
                        $objClassNew['UCREA'][$uc] = ['total' => 0, 'revenue' => 0];
                    }
                }
            //}
            $totalALL += sizeof($data);
        }

        if ($ref == 1)
            return $objClassNew;
    }

    private static function getClassNumber($name){
        return str_replace(['BLACKHOLE','UCREA','BRIGHT'],'',$name);
    }

    private static function countTotalTuition($array, $key = 0){
        $count = 0;
        foreach($array as $value){

            if($value->number_of_months == $key){
                $count++;
            }

        }
        return $count;
    }

    private static function percentageData($dataList){
        $sem = self::listSem();
        $semList = [];
        $semList1 = [];
        foreach ($sem as $se){
            $seq = str_replace("g","",$se);
            foreach ($dataList as $brId => $data){
                if ($data){
                    $semList[$brId][$se] = static::countTotalTuition($data, $seq);
                }
                else{
                    $semList[$brId][$se] = 0;
                }
                $semList[$brId]['all'] = sizeof($data);
            }
        }

        foreach ($semList as $t =>$td){
            $semList1[$t] = self::sumTuitionActiveNew($semList, $t);
        }
        return ['list'=>$semList, 'percentage'=>$semList1];
    }

    public static function findByDate($startDate,$endDate,$branchIds){
        $where = $branchIds ? " and c.branch_id IN ($branchIds)": "";
        $query = "SELECT c.student_id,c.branch_id,
                (SELECT number_of_months FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS number_of_months,
                (SELECT accounting_id FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tName
                FROM contracts c INNER JOIN payment p ON c.id = p.contract_id AND p.charge_date >= '$startDate' AND p.charge_date <= '$endDate'
                AND p.count = 1 $where";
        $data  = u::query($query);
        return $data;
        //GROUP_CONCAT(c.id) AS contract_ids //GROUP BY c.student_id
    }

    public static function getTuitionPercentageReportByDate($params, $listDays, $week, $month)
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
                $dataList['w'.($w+1)] = self::findByDate($startDate,$endDate, $branchIds);
            }
            foreach ($brand as $br){
                foreach ( $dataList as $w =>$list){
                    foreach ($list as $ls){
                        if ($ls->branch_id == $br){
                            $brandData[$w][$br][] =  $ls;
                        }
                    }
                    if (!isset($brandData[$w][$br]))
                        $brandData[$w][$br] = [];
                }
            }

            $all = [];
            foreach ($brandData as $w => $item){
               $all[$w] =  self::percentageData($item);
            }

        $label = [
            "g1" =>["id" =>1,"title"=>"Gói 01 tháng"],
            "g3" =>["id" =>2,"title"=>"Gói 03 Tháng"],
            "g6" =>["id" =>3,"title"=>"Gói 06 Tháng"],
            "g12" =>["id" =>4,"title"=>"Gói 12 Tháng"],
            "g24" =>["id" =>5,"title"=>"Gói 24 Tháng"],
            "all" =>["id" =>6,"title"=>"Total"],
            "s1" =>["id" =>7,"title"=>"% gói 01 Tháng"],
            "s3" =>["id" =>8,"title"=>"% gói 03 Tháng"],
            "s6" =>["id" =>9,"title"=>"% gói 06 Tháng"],
            "s12" =>["id" =>10,"title"=>"% gói 12 Tháng"],
            "s24" =>["id" =>11,"title"=>"% gói 24 Tháng"]
        ];

        $percentage = [
            "s1" =>"% gói 01 Tháng",
            "s3" =>"% gói 03 Tháng",
            "s6" =>"% gói 06 Tháng",
            "s12" =>"% gói 12 Tháng",
            "s24" =>"% gói 24 Tháng"
        ];

        $allDataArr = [];
        foreach ($all as $wl =>$al){
            $allDataArr['sum'][$wl] = self::sumTuitionActive($al['list']);
            $allDataArr['tuition'][$wl] = $al;
        }
        $listDate = [];
        foreach ($listDays as $d => $day){
            $m = substr($day['from'], 5,-3);
            $w = "W".($d+1)."/".$m;
            if (($d+1) >$week)
                $w = "TOTAL";
            $listDate[$d+1] = $w." (".$day['from']."-".$day['to'].")";
        }


        $allDataArr['week'] = $week;
        $allDataArr['dates'] = $listDate;
        $allDataArr['month'] = $month;
        $allDataArr['branches'] = $brandName;
        $allDataArr['label'] = $label;
        $allDataArr['percentage'] = $percentage;
        return $allDataArr;
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

    public static function listSem(){
        return ['g1','g3','g6','g12','g24'];
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

    private function writeRowPercentage($sheet, $excel, $columns, $rowIndex, $data, $dataIndex, $rootData = null, $indexSubItem = -1)
    {
        $backgroundColor = $rowIndex % 2 === 0 ? '96dcfe' : 'ccefff';
        $cols = self::getTotalColumns($columns);

        for ($index = 0; $index < $cols; $index++) {
            $columnIndex = $index + 1;
            $horizontal = "center";
            $vertical = "center";
            $value = isset($data[$index]) ? $data[$index] :"";
            self::writeCell($sheet, $columnIndex, $rowIndex, $value);
            $excel->setStyleCell($sheet, [$columnIndex, $rowIndex], $backgroundColor, '004666', 10, false,
                true, $horizontal, $vertical, true, "FFFFFF");
        }
        return $rowIndex + 1;
    }
}