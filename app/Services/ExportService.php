<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 7/22/2019
 * Time: 1:58 PM
 */

namespace App\Services;

use function _\size;
use App\Providers\UtilityServiceProvider as u;
use App\Templates\Exports\Student;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportService
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
            $color = !empty($meta['color']) ? $meta['color'] : '078acb';
            $size = !empty($meta['size']) ? $meta['size'] : 14;
            $weight = !empty($meta['weight']) ? $meta['weight'] : false;
            $excel->setStyleCell($sheet, [1, $row], null, $color, $size, $weight, 3, "center", "center", false);
        }
        ++$row;
        $sheet->mergeCellsByColumnAndRow(1, $row, $columnMax, $row);
        return $row;
    }

    /**
     * @param Worksheet $sheet
     * @param ExcelService $excel
     * @param $data
     * @param  Student $template
     * @param null $headerData
     * @throws Exception
     */
    public function write(Worksheet $sheet, ExcelService $excel, $data, $template, $headerData = null)
    {
        $columns = $template->getColumns();
        $header = $template->getHeader($headerData);
        $loop = $template->getLoop();
        $index = $this->writeHeader($header, $sheet, $excel, count($columns)) + 1;
        $index = $this->writeTableHeader($columns, $index, $sheet, $excel);
        $rowIndex = $index;
        $style = true;
        if (method_exists($template,'getStyle'))
            $style = $template->getStyle();
        foreach ($data as $indexItem => $item) {
            if ($loop) {
                foreach ($item->{$loop} as $indexSubItem => $subItem) {
                    $dataRow = clone $item;
                    $dataRow->{$loop} = $subItem;
                    if (!$style)
                        $rowIndex = self::writeRowNoneStyle($sheet, $excel, $columns, $rowIndex, $dataRow, $indexItem, $item, $indexSubItem);
                    else
                        $rowIndex = self::writeRow($sheet, $excel, $columns, $rowIndex, $dataRow, $indexItem, $item, $indexSubItem);
                }
            } else {
                if (!$style)
                    $rowIndex = $this->writeRowNoneStyle($sheet, $excel, $columns, $rowIndex, $item, $indexItem);
                else
                    $rowIndex = $this->writeRow($sheet, $excel, $columns, $rowIndex, $item, $indexItem);
            }
        }
    }

    public function getValue($key, $data, $index)
    {
        if (is_callable($key)) {
            return call_user_func($key, $data, $index);
        }

        return u::get($data, $key);
    }

    public function getRowMerge($key, $data, $index)
    {
        if (is_callable($key)) {
            return call_user_func($key, $data, $index);
        }

        return (int)$key;
    }

    /**
     * @param Worksheet $sheet
     * @param ExcelService $excel
     * @param $columns
     * @param $rowIndex
     * @param $data
     * @param $dataIndex
     * @param null $rootData
     * @param int $indexSubItem
     * @return int
     * @throws Exception
     */

    private function writeRow($sheet, $excel, $columns, $rowIndex, $data, $dataIndex, $rootData = null, $indexSubItem = -1)
    {
        $backgroundColor = $rowIndex % 2 === 0 ? '96dcfe' : 'ccefff';
        foreach ($columns as $index => $column) {
            $columnIndex = $index + 1;
            $horizontal = isset($column['horizontal']) ? $column['horizontal'] : "center";
            $vertical = isset($column['vertical']) ? $column['vertical'] : "center";
            $value = self::getValue($column['value'], $data, $dataIndex);
            if (isset($column["merge_row"])) {
                $rowMerge = self::getRowMerge($column["merge_row"], $rootData, $dataIndex);
                if ($indexSubItem === 0) {
                    $excel->mergeCell($columnIndex, $rowIndex, $columnIndex, $rowIndex + $rowMerge - 1, $sheet);
                    self::writeCell($sheet, $columnIndex, $rowIndex, $value);
                }
            } else {
                self::writeCell($sheet, $columnIndex, $rowIndex, $value);
            }
            /**
            $excel->setStyleCell($sheet, [$columnIndex, $rowIndex], $backgroundColor, '004666', 10, false,
                true, $horizontal, $vertical, true, "FFFFFF");*/
        }
        return $rowIndex + 1;
    }

    private function writeRowNoneStyle($sheet, $excel, $columns, $rowIndex, $data, $dataIndex, $rootData = null, $indexSubItem = -1)
    {
        foreach ($columns as $index => $column) {
            $columnIndex = $index + 1;
            $value = self::getValue($column['value'], $data, $dataIndex);
            if (isset($column["merge_row"])) {
                $rowMerge = self::getRowMerge($column["merge_row"], $rootData, $dataIndex);
                if ($indexSubItem === 0) {
                    $excel->mergeCell($columnIndex, $rowIndex, $columnIndex, $rowIndex + $rowMerge - 1, $sheet);
                    self::writeCell($sheet, $columnIndex, $rowIndex, $value);
                    //$excel->setStyleCell($sheet, [$columnIndex, $rowIndex], "FFFFFF", '000000', 10, false,
                        //true, "top", "top", true, "004666");
                }
            } else {
                self::writeCell($sheet, $columnIndex, $rowIndex, $value);
            }
        }
        return $rowIndex + 1;
    }

    /**
     * @param Worksheet $sheet
     * @param $columnIndex
     * @param $rowIndex
     * @param $value
     */
    public function writeCell($sheet, $columnIndex, $rowIndex, $value)
    {
        $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $value);
    }

    /**
     * @param $columns
     * @param $rowIndex
     * @param Worksheet $sheet
     * @param ExcelService $excel
     * @return mixed
     * @throws Exception
     */
    public function writeTableHeader($columns, $rowIndex, $sheet, $excel)
    {
        $excel->writeHeaderTable($sheet, $excel, $columns, $rowIndex);
        return $rowIndex + 1;
    }

    /**
     * @param $data
     * @param Student $template
     * @param string $filename
     * @param null|array $headerData
     * @throws Exception
     */
    public function export($data, $template, $filename = "cms_export_data.xlsx", $headerData = null, $contentCallable = null)
    {
        $excel = new ExcelService($filename);
        $excel->write(function (Worksheet $sheet, ExcelService $excel) use ($data, $template, $headerData, $contentCallable) {
            if (is_callable($contentCallable))
                $this->write1($sheet, $excel, $data, $template, $headerData, $contentCallable);
            else
                $this->write($sheet, $excel, $data, $template, $headerData);
        });
    }

    /**
     * @param Callable $callable
     * @param string $filename
     * @param int $columnMax
     * @throws Exception
     */
    public function exportCustom($callable, $filename = "cms_export_data.xlsx", $columnMax = 10)
    {
        $excel = new ExcelService($filename);
        $excel->write(function ($sheet, $excel, $spreadsheet) use ($callable, $columnMax) {
            $this->writeHeader(null, $sheet, $excel, $columnMax);
            if (is_callable($callable)) {
                call_user_func($callable, $sheet, $excel, $spreadsheet);
            }
        });
    }

    public function write1(Worksheet $sheet, ExcelService $excel, $data, $template, $headerData = null, $callable = null)
    {
        $columns = $template->getColumns();
        $header = $template->getHeader($headerData);
        $loop = $template->getLoop();
        $index = $this->writeHeader($header, $sheet, $excel, self::getTotalColumns($columns)) + 1;
        $index = $this->writeTableHeader($columns, $index, $sheet, $excel);
        $rowIndex = sizeof($header) > 2 ? 12 : $index +1;
        if (!is_array($data) || empty($data)) {
            return;
        }
        if (is_callable($callable)) {
            return call_user_func($callable, $sheet, $excel, $rowIndex, $columns, $loop);
        }
        foreach ($data as $indexItem => $item) {
            if ($loop) {
                foreach ($item->{$loop} as $indexSubItem => $subItem) {
                    $dataRow = clone $item;
                    $dataRow->{$loop} = $subItem;
                    $rowIndex = self::writeRow($sheet, $excel, $columns, $rowIndex, $dataRow, $indexItem, $item, $indexSubItem);
                }
            } else {
                $rowIndex = $this->writeRow($sheet, $excel, $columns, $rowIndex, $item, $indexItem);
            }
        }
    }



    public function exportNew($data, $template, $filename = "cms_export_data.xlsx", $headerData = null, $columns= null, $contentCallable = null)
    {
        $excel = new ExcelService($filename);
        $excel->write(function (Worksheet $sheet, ExcelService $excel) use ($data, $template, $headerData, $contentCallable, $columns) {
            $this->write1New($sheet, $excel, $data, $template, $headerData, $contentCallable,$columns);
        });
    }

    public function write1New($sheet, $excel, $data, $template, $headerData, $callable, $columns)
    {
        $header = $template->getHeader($headerData);
        $index = $this->writeHeader($header, $sheet, $excel, self::getTotalColumns($columns)) + 1;
        $index = $this->writeTableHeader($columns, $index, $sheet, $excel);
        $rowIndex = sizeof($header) > 2 ? 12 : $index +1;
        if (!is_array($data) || empty($data)) {
            return;
        }
        if (is_callable($callable)) {
            return call_user_func($callable, $sheet, $excel, $rowIndex, $columns);
        }
    }

    public static function getTotalColumns($columns){
        $totalCol = 0;
        foreach ($columns as $index => $column) {
            if (isset($column['children']))
                $totalCol += sizeof($column['children']);
            else
                $totalCol += 1;
        }
        return $totalCol;
    }
}