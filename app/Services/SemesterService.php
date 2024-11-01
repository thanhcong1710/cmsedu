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

class SemesterService extends ExportService
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

            $this->exportNew($data, $template, "export_semester_percentage_by_date", $headerData, $columns, function ($sheet, $excel, $rowIndex, $columns) use ($data) {
                self::writeDataPercentageSheet($sheet, $excel, $rowIndex, $data, $columns);
            });
        } catch (Exception $e) {
        }
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
            "UCREA" => ["id" =>1,"title"=>"UCREA"],
            "BRIGHT" => ["id" =>2,"title"=>"BRIGHT IG"],
            "BLACKHOLE" => ["id" =>3,"title"=>"BLACK HOLE"],
            "all" => ["id" =>4, "title"=>"TOTAL"],
            "sUCREA" => ["id" =>5,"title"=>"% UCREA"],
            "sBRIGHT" => ["id" =>6,"title"=>"% BRIGHT IG"],
            "sBLACKHOLE" => ["id" =>7,"title"=>"% BLACK HOLE"],
        ];
        $percentage = [];
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

    private static function countTotalTuition($array, $key = 0){
        $count = 0;
        foreach($array as $value){
            $semName = preg_replace('/[0-9]+/', '', $value->tName);
            if($semName == $key){
                $count++;
            }
        }
        return $count;
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

    private function processDataNext($array,$label, $case =0)
    {
        $value = null;
        if (isset($array[$label]))
            $value =  $array[$label];
        return $value;
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