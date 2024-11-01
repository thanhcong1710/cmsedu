<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 7/22/2019
 * Time: 1:58 PM
 */

namespace App\Services;

use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService
{

    var $spreadsheet;
    var $filename;

    public function __construct($filename)
    {
        $this->spreadsheet = new Spreadsheet();
        $this->filename = $filename . ".xlsx";
    }

    /**
     * @param $callable
     * @return false|string
     * @throws Exception
     */
    public function write($callable)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        if (is_callable($callable)) {
            call_user_func($callable, $sheet, $this, $this->spreadsheet);
        }
        return $this->response();
    }

    public function addPositionToCellColumns(&$columns, $row = 0, $columnIndex = 0)
    {
        $maxRow = $row;
        $length = count($columns);
        foreach ($columns as $index => &$column) {
            $column['row'] = $row;
            $column['column'] = $columnIndex;
            if (isset($column['children'])) {
                list($mRow, $columnIndex) = self::addPositionToCellColumns($column['children'], $row + 1, $columnIndex);
                if ($mRow > $maxRow) {
                    $maxRow = $mRow;
                }
                if ($index === count($columns) - 1) {
                    $columnIndex -= 1;
                }
            } else if ($index === $length - 1) {
                $columnIndex -= 1;
            }
            ++$columnIndex;
        }
        return array($maxRow, $columnIndex);
    }

    public function getLengthColumns($columns, $columnIndex = 0)
    {
        $length = count($columns);
        foreach ($columns as $index => &$column) {

            if (isset($column['children'])) {
                $columnIndex = self::getLengthColumns($column['children'], $columnIndex);
                if ($index === count($columns) - 1) {
                    $columnIndex -= 1;
                }
            } else if ($index === $length - 1) {
                $columnIndex -= 1;
            }
            ++$columnIndex;
        }
        return $columnIndex;
    }

    public function countChildrenColumn($column, $count = 0)
    {
        if (!isset($column['children'])) {
            return $count;
        }

        foreach ($column['children'] as $child) {
            if (isset($child['children'])) {
                $count += $this->countChildrenColumn($child);
            } else {
                ++$count;
            }
        }

        return $count;
    }

    public function mergeCellForColumns(&$columns, $maxRow, $maxColumn)
    {
        foreach ($columns as $index => &$column) {
            if (!isset($column['children'])) {
                $column['merge_row'] = $maxRow - $column['row'];
            } else {
                $this->mergeCellForColumns($column['children'], $maxRow, $maxColumn);
            }
            $column['merge_column'] = $this->countChildrenColumn($column);
        }
    }

    /**
     * @param Worksheet $sheet
     * @param ExcelService $excel
     * @param $rowIndex
     * @param $columns
     * @return mixed
     * @throws Exception
     */
    public function writeHeaderTable($sheet, $excel, &$columns, $rowIndex)
    {
        list($maxRow, $maxColumn) = $this->addPositionToCellColumns($columns);
        $this->mergeCellForColumns($columns, $maxRow, $maxColumn);
        $this->writeHeaderTable1($sheet, $excel, $columns, $rowIndex, $maxRow === 0 ? 50 : 25);

        return $maxRow + $rowIndex;
    }

    /**
     * @param Worksheet $sheet
     * @param ExcelService $excel
     * @param $columns
     * @param $rowIndex
     * @param $rowHeight
     * @throws Exception
     */
    private function writeHeaderTable1($sheet, $excel, &$columns, $rowIndex, $rowHeight = null)
    {
        foreach ($columns as $key => $column) {
            $children = u::get($column, 'children');
            $rIndex = $column['row'] + $rowIndex;
            $cIndex = $column['column'] + 1;
            if ($key === 0 && $rowHeight) {
                $sheet->getRowDimension($rIndex)->setRowHeight($rowHeight);
            }
            $sheet->setCellValueByColumnAndRow($cIndex, $rIndex, $column['name']);
            $sheet->getColumnDimensionByColumn($cIndex)->setWidth(u::get($column, 'width'));
            $excel->setStyleCell($sheet, [$cIndex, $rIndex], "078acb", "FFFFFF", 10, false,
                true, "center", "center", true, "FFFFFF");
            $mergeRow = u::get($column, 'merge_row', 0);
            $mergeColumn = u::get($column, 'merge_column', 0) - 1;
            if ($mergeRow > 0 || $mergeColumn > 0) {
                $rEndRow = $rIndex + ($mergeRow > 0 ? $mergeRow : 0);
                $cEndColumn = $cIndex + ($mergeColumn > 0 ? $mergeColumn : 0);
                $excel->mergeCell($cIndex, $rIndex, $cEndColumn, $rEndRow, $sheet);
                $excel->setStyleCell($sheet, [$cIndex, $rIndex, $cEndColumn, $rEndRow], "078acb", "FFFFFF", 10, false,
                    true, "center", "center", true, "FFFFFF");
            }
            if (!empty($children)) {
                self::writeHeaderTable1($sheet, $excel, $children, $rowIndex, $rowHeight);
            }
        }
    }


    public function response()
    {
        $writer = new Xlsx($this->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$this->filename");
        header('Cache-Control: max-age=0');
        try {
            $writer->save("php://output");
            exit();
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            $response = new Response();
            return $response->formatResponse(APICode::CANNOT_CONNECT_API, null, $e->getMessage());
        }
    }

    private function getHorizontal($value)
    {
        switch ($value) {
            case 'center':
                return Alignment::HORIZONTAL_CENTER;
            case 'left':
                return Alignment::HORIZONTAL_LEFT;
            case 'justify':
                return Alignment::HORIZONTAL_JUSTIFY;
            default:
                return Alignment::HORIZONTAL_RIGHT;
        }
    }

    private function getVertical($value)
    {
        switch ($value) {
            case 'center':
                return Alignment::VERTICAL_CENTER;
            case 'justify':
                return Alignment::VERTICAL_JUSTIFY;
            case 'bottom':
                return Alignment::VERTICAL_BOTTOM;
            default:
                return Alignment::VERTICAL_TOP;
        }
    }

    /**
     * @param $orientation = 'horizontal' | 'vertical'
     * @param $value
     * @return string
     */
    private function getOrientation($orientation, $value)
    {
        if ($orientation === 'horizontal') {
            return self::getHorizontal($value);
        }

        return $this->getVertical($value);

    }

    /**
     * @param $startColumn
     * @param $startRow
     * @param $endColumn
     * @param $endRow
     * @param Worksheet $sheet
     * @throws Exception
     */
    public function mergeCell($startColumn, $startRow, $endColumn, $endRow, $sheet = null)
    {
        if (empty($sheet)) {
            $sheet = $this->spreadsheet->getActiveSheet();
        }

        $sheet->mergeCellsByColumnAndRow($startColumn, $startRow, $endColumn, $endRow);
    }

    /**
     * @param Worksheet $sheet
     * @param $arr
     * @return \PhpOffice\PhpSpreadsheet\Style\Style
     */
    private function getStyleByColumnAndRow($sheet, $arr)
    {
        if (count($arr) === 2) {
            return $sheet->getStyleByColumnAndRow($arr[0], $arr[1]);
        }
        return $sheet->getStyleByColumnAndRow($arr[0], $arr[1], $arr[2], $arr[3]);
    }

    /**
     * @param Worksheet $sheet
     * @param $cell array | String
     * @param null $bg_color
     * @param null $color
     * @param null $font_size
     * @param bool $font_weight
     * @param null $word_wrap
     * @param null $horizontal
     * @param null $vertical
     * @param bool $border
     * @param string $borderColor
     * @param string $font_family
     * @throws Exception
     */
    public function setStyleCell($sheet, $cell, $bg_color = null, $color = null, $font_size = null, $font_weight = false, $word_wrap = null, $horizontal = null, $vertical = null, $border = true, $borderColor = "FF0000", $font_family = 'Times New Roman')
    {
        $bg_color = $bg_color ? $bg_color : 'FFFFFF';
        $color = $color ? $color : '00000000';
        $font_size = $font_size ? $font_size : 11;
        $alignment = [
            'horizontal' => self::getOrientation('horizontal', $horizontal),
            'vertical' => self::getOrientation('vertical', $vertical)
        ];

        $style = array(
            'fill' => array(
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
//              'rotation' => 90,
                'startColor' => [
                    'rgb' => $bg_color,
                ],
                'endColor' => [
                    'rgb' => $bg_color,
                ],
            ),
            'font' => array(
                'bold' => $font_weight ?: false,
                'color' => array('argb' => $color),
                'size' => $font_size,
                'name' => $font_family,
            ),
            'alignment' => $alignment,
        );

        if ($border !== false) {
            $style['borders'] = array(
                'outline' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('argb' => $borderColor),
                )
            );
        }
        $styleCell = is_array($cell) ? self::getStyleByColumnAndRow($sheet, $cell) : $sheet->getStyle($cell);
        $styleCell->applyFromArray($style);
        if ($word_wrap === true || $word_wrap === false) {
            $styleCell->getAlignment()->setWrapText($word_wrap);
        }
    }

    public function getFormatCode($type)
    {
        switch ($type) {
            case "number":
                return NumberFormat::FORMAT_NUMBER;
            case "string":
                return NumberFormat::FORMAT_TEXT;
            default:
                return NumberFormat::FORMAT_TEXT;
        }
    }
}