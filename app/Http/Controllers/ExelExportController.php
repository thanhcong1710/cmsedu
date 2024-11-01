<?php

namespace App\Http\Controllers;

use App\Models\ProcessExcel;
use Illuminate\Http\Request;
use App\Services\CustomerCareService;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExelExportController extends Controller
{
    public function studentCare(Request $request)
    {

        $columns = [
            ['name' => 'stt', 'label' => 'STT', 'width' => 5],
            ['name' => 'creator', 'label' => 'Tên Tư Vấn Viên', 'width' => 30],
            ['name' => 'hrm_id', 'label' => 'Mã NV', 'width' => 15],
            ['name' => 'accounting_id', 'label' => 'Mã Kế Toán', 'width' => 15],
            ['name' => 'student_name', 'label' => 'Học Sinh', 'width' => 25],
            ['name' => 'crm_id', 'label' => 'Mã CMS', 'width' => 15],
            ['name' => 'br_name', 'label' => 'Trung Tâm', 'width' => 30],
            ['name' => 'created_at', 'label' => 'Ngày Giờ', 'width' => 20],
            ['name' => 'quality_name', 'label' => 'Kết Quả Tương Tác', 'width' => 35],
            ['name' => 'quality_score', 'label' => 'Điểm Tương Tác', 'width' => 20],
        ];

        $params = $request->all();
        $data = CustomerCareService::getStudentCareReport($params);
        $reportDate = "TỪ NGÀY @from ĐẾN NGÀY @to";
        if (!empty($params['start_date'])){
            $reportDate = str_replace(['@from','@to'],[date('d/m/Y',strtotime($params['start_date'])),date('d/m/Y',strtotime($params['end_date']))],$reportDate);
        }
        else
            $reportDate = "TOÀN BỘ THỜI GIAN";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $reportName = "Báo cáo chất lượng chăm sóc khách hàng";

        $fmStyle = [
            [
                'id'=>1,
                'column' =>'A1',
                'name' =>'UBND thành phố Hà Nội',
                'background' => null,
                'color' => '000080',
                'font-size' =>9,
                'font-weight'=>1,
                'horizontal' =>'left',
                'vertical' =>'center',
                'row-height' =>0
            ],
            [
                'id'=>2,
                'column' =>'A2',
                'name' =>'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY VÀ SÁNG TẠO QUỐC TẾ CMS',
                'background' => null,
                'color' => '000080',
                'font-size' =>9,
                'font-weight'=>1,
                'horizontal' =>'left',
                'vertical' =>'center',
                'row-height' =>0
            ],
            [
                'id'=>3,
                'column' =>'A3',
                'name' =>'ĐỊA CHỈ :Tầng 4, Tòa 21T2 Dự án Hapulico Complex, Số 01 Nguyễn Huy Tưởng, P.Thanh Xuân Trung, Q.Thanh Xuân, TP Hà Nội, Việt Nam',
                'background' => null,
                'color' => '000080',
                'font-size' =>9,
                'font-weight'=>null,
                'horizontal' =>'left',
                'vertical' =>'center',
                'row-height' =>0
            ],
            [
                'id'=>4,
                'column' =>'A4',
                'name' =>'MÃ SỐ THUẾ :0108190194',
                'background' => null,
                'color' => '000080',
                'font-size' =>9,
                'font-weight'=>null,
                'horizontal' =>'left',
                'vertical' =>'center',
                'row-height' =>0
            ],
            [
                'id'=>5,
                'column' =>'A5',
                'name' =>$reportName,
                'background' => null,
                'color' => '000080',
                'font-size' =>16,
                'font-weight'=>1,
                'horizontal' =>'center',
                'vertical' =>'center',
                'row-height' =>40
            ],
            [
                'id'=>6,
                'column' =>'A6',
                'name' =>$reportDate,
                'background' => null,
                'color' => '000080',
                'font-size' =>9,
                'font-weight'=>1,
                'horizontal' =>'center',
                'vertical' =>'center',
                'row-height' =>0
            ],
        ];

        self::setHeadStyle($spreadsheet, $sheet, $columns, $fmStyle);
        foreach ($columns as $index => $column) {
            $colName = chr($index + 65);
            $sheet->setCellValue("${colName}8", $column['label']);
            $sheet->getColumnDimension($colName)->setWidth($column['width']);
            $sheet->getRowDimension(8)->setRowHeight(40);
            ProcessExcel::styleCells($spreadsheet, "${colName}8", 'c0ffc0', '000080', 8, null, 3, "center", "center", true, 0, 'Tahoma');
        }
        if ($data)
            $data = $data['data'];
        foreach ($data as $pos => $item) {
            $position = $pos + 9;
            foreach ($columns as $index => $column) {
                $colName = chr($index + 65);
                $sheet->setCellValue("{$colName}$position", $column['name'] === 'stt' ? $pos + 1 : $item->{$column['name']});
                ProcessExcel::styleCells($spreadsheet, "{$colName}$position", "FFFFFF", "black", 11, 0, 3, "center", "center", true);
            }
        }
        self::save($spreadsheet, "Báo cáo chất lượng chăm sóc khách hàng");
    }

    private function setHeadStyle($spreadsheet,$sheet, $columns, $styles = []){
        foreach ($styles as $style){
            $sheet->setCellValue($style['column'], $style['name']);
            $colEnd = chr(count($columns) + 65);
            $sheet->mergeCells("{$style['column']}:{$colEnd}{$style['id']}");
            if ($style['row-height'] > 0)
                $sheet->getRowDimension($style['id'])->setRowHeight($style['row-height']);
            ProcessExcel::styleCells($spreadsheet, "{$style['column']}", null, $style['color'], $style['font-size'], $style['font-weight'], 0, $style['horizontal'], $style['vertical'], false, null, 'Tahoma');
        }
    }

    private function save($spreadsheet, $fileName)
    {
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
        } finally {
            exit;
        }
    }
}
