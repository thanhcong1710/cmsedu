<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;
use PhpOffice\PhpSpreadsheet\Exception;
use App\Models\ProcessExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/*
* Created by QuyenLD
*/
class SupportService
{
    CONST TEACHER_ROLE = 'CM';

    public static function getBranchTransferInfo($crmId = "",$from = "", $to="")
    {
        $query = "SELECT '$crmId' as crm_id, cl.`student_id`,cl.`contract_id`, cl.`from_class_id`, cl.`from_branch_id`,
        cl.`to_branch_id`, cl.`status`, cl.`amount_transferred`, cl.`created_at` FROM `class_transfer` cl WHERE cl.`from_branch_id` > 0 
        AND student_id = (SELECT id FROM students WHERE accounting_id = '$crmId')";

        $data = u::query($query);
        return !empty($data[0]) ? $data[0] : [];
    }

    public static function export1($items = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //$sheet->getRowDimension($rowIndex)->setRowHeight(50);
        $sheet->setTitle("Tuần 1");
        $columns = ['crm_id','status','from','to'];
        $rowIndex = 1;
        //var_dump($items);exit;
        //$c = 0;
        foreach ($columns as $key => $value) {
            //$rowIndex = $rowIndex + $key;
            $colName = $key < 26 ? chr($key + 65) : 'A' . chr($key - 26 + 65);
            //$sheet->setCellValue("{$colName}$rowIndex", $value['name']);
//            $detail = $obj->detail;

            foreach ($items as $i => $obj){
                $c = $rowIndex + $i;
                //var_dump($obj);exit;
                if (!empty($obj->crm_id)){
                    $sheet->setCellValue("{$colName}{$c}", $obj->{$value});
                }
                else
                    $sheet->setCellValue("{$colName}{$c}", 0);

            }

            $sheet->getColumnDimension($colName)->setWidth(30);
//            ProcessExcel::styleCells($spreadsheet, "{$colName}$rowIndex", "078acb", "FFFFFF", 10, 0, 3, "center", "center", true);
//            $sheet->getStyle("{$colName}$rowIndex")->getAlignment()->setWrapText(true);
        }

        $filename = "export1.xlsx";
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        try {
            $writer->save("php://output");
            exit();
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
           // $response = new Response();
            //return $response->formatResponse(APICode::CANNOT_CONNECT_API, null, $e->getMessage());
        }
    }

    public static function branchTransferService($items = [])
    {

//        $this->student_id = $data->student_id;
//        $this->type = $this->BRANCH_TRANSFER_TYPE;
//        $this->note = $data->note;
//        $this->transfer_date = $data->transfer_date;
//        $this->status = $this->WAITING_FROM;
//        $this->creator_id = $data->users_data->id;
//        $this->created_at = date('Y-m-d H:i:s');
//        $this->amount_transferred = $data->amount_transferred;
//        $this->amount_exchange = $data->amount_exchange;
//        $this->session_transferred = $data->session_transferred;
//        $this->session_exchange = $data->session_exchange;
//        $this->from_branch_id = $data->from_branch_id;
//        $this->to_branch_id = $data->to_branch_id;
//        $this->from_product_id = $data->from_product_id;
//        $this->to_product_id = $data->to_product_id;
//        $this->from_program_id = $data->from_program_id;
//        $this->to_program_id = $data->to_program_id;
//        $this->from_class_id = $data->from_class_id;
//        $this->contract_id = $data->contract_id;
//        $this->semester_id = $data->semester_id;
//        $this->meta_data = json_encode($data->meta_data);

//        var_dump($items);exit;
    }
}