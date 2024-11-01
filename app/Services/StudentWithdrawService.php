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

class StudentWithdrawService extends ExportService
{

    public function exportDataByDate($params, $template, $data)
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
            //$columns = $template->getColumns();
            $this->export($data['data'], $template, $filename = "export_student_withdraw_by_date.xlsx", $headerData);
//            $this->exportNew($data, $template, "export_student_withdraw_by_date", $headerData, $columns, function ($sheet, $excel, $rowIndex, $columns) use ($data) {
//                //self::writeDataSheet($sheet, $excel, $rowIndex, $data, $columns);
//            });
        } catch (Exception $e) {
        }
    }

    public static function getReportByDate($params, $export = false)
    {

        $wBranch = "";
        $wBranch1 = "";
        $branchIds = [];

        if (!empty($params['branch_ids']) && is_array($params['branch_ids'])) {
            $branchIds = implode(",", $params['branch_ids']);
        }

        if ($branchIds != 0){
            //$wBranch = $branchIds ? " AND c.`branch_id` IN ($branchIds)": "";
            $wBranch1 = $branchIds ? " AND s.`branch_id` IN ($branchIds)": "";
        }

        if (!$wBranch1)
            $wBranch1 = " AND 1=1";
        $limit = "";
        if (!empty($params['limit'])) {
            $l = (int)$params['limit'];
            $p = (int)$params['page'];
            $o = ($p - 1) * $l;
            if (!$export)
                $limit = " limit  $o, $l ";
        }


        $query = "SELECT *,GROUP_CONCAT(c.id) AS c_list, s.name AS student_name,s.accounting_id as accounting_id,
                s.gud_email1 as email
                FROM students s 
                INNER JOIN contracts c ON c.`student_id` = s.`id`
                WHERE (SELECT COUNT(id) FROM contracts WHERE contracts.`student_id` = s.`id` AND STATUS NOT IN (7,8))=0 AND s.status >0 $wBranch1 GROUP BY c.`student_id` $limit";


//        $query = "SELECT
//                    c.`student_id`,
//                    GROUP_CONCAT(c.id) AS c_list,
//                    GROUP_CONCAT(c.status) AS c_status,
//                    (SELECT gud_name1 FROM students WHERE id = c.`student_id`) AS gud_name1,
//                    (SELECT gud_mobile1 FROM students WHERE id = c.`student_id`) AS gud_mobile1,
//                    (SELECT gud_name2 FROM students WHERE id = c.`student_id`) AS gud_name2,
//                    (SELECT gud_mobile2 FROM students WHERE id = c.`student_id`) AS gud_mobile2,
//                    (SELECT crm_id FROM students WHERE id = c.`student_id`) AS crm_id,
//                    (SELECT email FROM students WHERE id = c.`student_id`) AS email,
//                    (SELECT `accounting_id` FROM students WHERE id = c.`student_id`) AS accounting_id,
//                    (SELECT `name` FROM students WHERE id = c.`student_id`) AS student_name,
//                     c.`student_id`
//                     FROM contracts c
//                    WHERE  $wBranch1
//                    GROUP BY c.`student_id` ORDER BY c.student_id $limit";
        $sqlTotal = "SELECT c.id FROM students s
                INNER JOIN contracts c ON c.`student_id` = s.`id`
                WHERE (SELECT COUNT(id) FROM contracts WHERE contracts.`student_id` = s.`id` AND STATUS NOT IN (7,8))=0 AND s.status >0 $wBranch1 GROUP BY c.`student_id`";

        $data  = u::query($query);
        $dataArr = [];
        foreach ($data as $dta){
//            if ($wBranch1 === "1=1")
//                $filter = self::filterStatus($dta->c_status);
//            else
//                $filter = self::filterStatusNew($dta->student_id);
//            if ($filter){
                $dta->contracts = self::getContractDetail($dta->c_list);
                if (empty($dta->gud_mobile1) && !empty($dta->gud_mobile2))
                    $dta->gud_mobile1 = $dta->gud_mobile2;

                if (empty($dta->gud_name1) && !empty($dta->gud_name2))
                    $dta->gud_name1 = $dta->gud_name2;
                $dta->c_size = sizeof($dta->contracts);
                $dataArr[] = $dta;
//            }
        }

        $total = 0;
        if (!$export){
            $dataTotal  = u::query($sqlTotal);
            $total = sizeof($dataTotal);
        }

        return [
            'data' => ($dataArr),
            'total' => $total,
        ];
    }

    public static function filterStatusNew($studentId = 0){
        $sql = "SELECT COUNT(id) AS c FROM contracts WHERE `status` NOT IN (7,8) AND student_id = $studentId";
        $data = u::query($sql);
        $c = 0;
        if ($data)
            $c = $data[0]->c;

        return $c > 0 ? false: true;
    }

    public static function filterStatus($status = ''){
        $statusArr = explode(",",$status);
        if (in_array(7,$statusArr) && !in_array(8,$statusArr)){

            if (in_array(1,$statusArr))
                return false;
            if (in_array(2,$statusArr))
                return false;
            if (in_array(3,$statusArr))
                return false;
            if (in_array(4,$statusArr))
                return false;
            if (in_array(5,$statusArr))
                return false;
            if (in_array(6,$statusArr))
                return false;
            else
                return true;

        }
        else if (in_array(8,$statusArr)){
            if (in_array(1,$statusArr))
                return false;
            if (in_array(2,$statusArr))
                return false;
            if (in_array(3,$statusArr))
                return false;
            if (in_array(4,$statusArr))
                return false;
            if (in_array(5,$statusArr))
                return false;
            if (in_array(6,$statusArr))
                return false;
            else
                return true;
        }
        else{
            return false;
        }


    }

    public static function getContractDetail($contractList){
       // var_dump($contractList);exit;
        $sql = "SELECT 
                (SELECT NAME FROM branches WHERE c.`branch_id` = id) AS branch_name,
                (SELECT full_name FROM `users` WHERE c.`ec_id` = id) AS ec_name,
                (SELECT NAME FROM `tuition_fee` WHERE id = c.`tuition_fee_id`) AS tuition_fee_name,
                c.`type`,c.`tuition_fee_price`,c.`must_charge`,c.`total_charged`,c.`total_sessions`,c.`enrolment_start_date`,c.`enrolment_last_date`,c.`status`,
                c.start_date, c.end_date,c.student_id
                FROM `contracts` c WHERE c.id IN ($contractList)";
        $data = u::query($sql);
        $dataNew = [];
        foreach ($data as $item){
            $item1 = self::getInfo($item);
            $dataNew[] = $item1;
        }

        return $dataNew;
    }

    public static function getInfo($item){
        $item->type = u::getContractTypeName($item->type);
        $item->status = u::getContractStatusName($item->status);
        $item->must_charge = number_format($item->must_charge);
        $item->tuition_fee_price = number_format($item->tuition_fee_price);
        $item->total_charged = number_format($item->total_charged);

        if (empty($item->enrolment_start_date)){
            if ($item->start_date && $item->end_date){
                $item->enrolment_start_date = $item->start_date;
                $item->enrolment_last_date = $item->end_date;
            }

        }
        return $item;
    }
}
