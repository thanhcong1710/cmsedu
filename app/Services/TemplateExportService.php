<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:46 AM
 */

namespace App\Services;


use App\Providers\UtilityServiceProvider as u;
use App\Templates\TemplateInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateExportService
{
    const HEADERS = [
        ['label' => "UBND thành phố Hà Nội"],
        ['label' => 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY VÀ SÁNG TẠO QUỐC TẾ CMS'],
        ['label' => 'ĐỊA CHỈ: Tầng 4, Tòa 21T2 Dự án Hapulico Complex, Số 01 Nguyễn Huy Tưởng, P.Thanh Xuân Trung, Q.Thanh Xuân, TP Hà Nội, Việt Nam'],
        ['label' => 'MÃ SỐ THUẾ: 0108190194']
    ];

    /**
     * @param $metadata
     * @param Worksheet $sheet
     * @param ExcelService $excel
     * @param $columnMax
     * @return int
     * @throws Exception
     */
    public function writeHeader($metadata, $sheet, $excel, $columnMax)
    {
        $row = 0;
        foreach (self::HEADERS as $index => $header) {
            ++$row;
            $sheet->mergeCellsByColumnAndRow(1, $row, $columnMax, $row);
            $sheet->setCellValueByColumnAndRow(1, $row, $header['label']);
            $excel->setStyleCell($sheet, [1, $row], null, "078acb", 10, false, 3, "left", "center", false);
        }
        ++$row;
        $sheet->mergeCellsByColumnAndRow(1, $row, $columnMax, $row);
        foreach ($metadata as $meta) {
            $row = $row + 1;
            $sheet->mergeCellsByColumnAndRow(1, $row, $columnMax, $row);
            $sheet->setCellValueByColumnAndRow(1, $row, $meta['name']);
            $excel->setStyleCell($sheet, [1, $row], null, "078acb", 14, true, 3, "center", "center", false);
        }
        ++$row;
        $sheet->mergeCellsByColumnAndRow(1, $row, $columnMax, $row);
        return $row;
    }

    /**
     * @param Worksheet $sheet
     * @param ExcelService $excel
     * @param TemplateInterface $template
     * @throws Exception
     */
    public function write($sheet, $excel, $template)
    {
        $columns = $template->getColumns();
        $header = $template->getHeader();
        $index = $this->writeHeader($header, $sheet, $excel, $excel->getLengthColumns($columns, 1)) + 1;
        $index = $excel->writeHeaderTable($sheet, $excel, $columns, $index) + 1;
        $this->writeContent($sheet, $excel, $columns, $index);
    }

    /**
     * @param Worksheet $sheet
     * @param ExcelService $excel
     * @param $columns
     * @param $rowIndex
     * @throws Exception
     */
    private function writeContent($sheet, $excel, &$columns, $rowIndex)
    {
        foreach ($columns as $column) {
            $children = u::get($column, 'children');
            $cIndex = $column['column'] + 1;
            $value = u::get($column, 'value');
            if (isset($value)) {
                $sheet->setCellValueByColumnAndRow($cIndex, $rowIndex, $value);
                $sheet->getStyleByColumnAndRow($cIndex,$rowIndex)->getNumberFormat()->setFormatCode($excel->getFormatCode(u::get($column, 'data_type')));
                $excel->setStyleCell($sheet, [$cIndex, $rowIndex], '96dcfe', null, 10, false,
                    true, "center", "center", true, "FFFFFF");
            }
            if (!empty($children)) {
                self::writeContent($sheet, $excel, $children, $rowIndex);
            }
        }
    }


    /**
     * @param TemplateInterface $template
     * @param string $filename
     * @throws Exception
     */
    public function export($template, $filename = "cms_template.xlsx")
    {
        $excel = new ExcelService($filename);
        $excel->write(function (Worksheet $sheet, ExcelService $excel) use ($template) {
            self::write($sheet, $excel, $template);
        });
    }
}