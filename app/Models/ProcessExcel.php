<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 3/22/2018
 * Time: 2:38 PM
 */

namespace App\Models;
use PhpOffice\PhpSpreadsheet\Style;

class ProcessExcel
{


    static function styleCells($obj,$cells,$bg_color=null,$color=null,$font_size=null,$font_weight=null,$word_wrap=null,$horizontal=null,$vertical=null,$border=true,$index=-1,$font_family='Times New Roman'){
        $bg_color = $bg_color?$bg_color:'FFFFFF';
        $color = $color?$color:'00000000';
        $font_weight = $font_weight?true:false;
        $font_size = $font_size?$font_size:11;
        $alignment = array();
        switch($horizontal){
            case 'center':
                $alignment['horizontal'] = Style\Alignment::HORIZONTAL_CENTER;
                break;
            case 'left':
                $alignment['horizontal'] = Style\Alignment::HORIZONTAL_LEFT;
                break;
            case 'justify':
                $alignment['horizontal'] = Style\Alignment::HORIZONTAL_JUSTIFY;
                break;
            default:
                $alignment['horizontal'] = Style\Alignment::HORIZONTAL_RIGHT;
        }
        switch($vertical){
            case 'center':
                $alignment['vertical'] = Style\Alignment::VERTICAL_CENTER;
                break;
            case 'justify':
                $alignment['vertical'] = Style\Alignment::VERTICAL_JUSTIFY;
                break;
            case 'bottom':
                $alignment['vertical'] = Style\Alignment::VERTICAL_BOTTOM;
                break;
            default:
                $alignment['vertical'] = Style\Alignment::VERTICAL_TOP;
        }

        $style = array(
            'fill' => array(
                'fillType' => Style\Fill::FILL_GRADIENT_LINEAR,
//                'rotation' => 90,
                'startColor' => [
                    'rgb' => $bg_color,
                ],
                'endColor' => [
                    'rgb' => $bg_color,
                ],
            ),
            'font'  => array(
                'bold'  => $font_weight,
                'color' => array('argb' => $color),
                'size' => $font_size,
                'name' => $font_family,
            ),
            'alignment' => $alignment,
        );

        $length = strlen($border);
        if ($length == 1){
            $style['borders'] = array(
                'outline' => array(
                    'borderStyle' => Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'dadcdd'),
                ));
        }
        else if($length == 0){
            $style['borders'] = array(
                'outline' => array(
                    'borderStyle' => Style\Border::BORDER_THIN
                )
            );
        }
        else{
            $style['borders'] = array(
                'outline' => array(
                    'borderStyle' => Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'FFFFFF'),
                ));
        }

        if($index > -1){
            $obj->setActiveSheetIndex($index)->getStyle($cells)->applyFromArray($style);
            if($word_wrap === true || $word_wrap === false){
                $obj->setActiveSheetIndex($index)->getStyle($cells)->getAlignment()->setWrapText($word_wrap);
            }
        }else{
            $obj->getActiveSheet()->getStyle($cells)->applyFromArray($style);
            if($word_wrap === true || $word_wrap === false){
                $obj->getActiveSheet()->getStyle($cells)->getAlignment()->setWrapText($word_wrap);
            }
        }
    }

    public static function renderReport16InputTitle($spreadsheet, $inputSheet, $inputIndex, $fromDate){
        $inputSheet->freezePane('C6');

        $inputSheet->setCellValue('B1', 'BẢNG THEO DÕI HIỆU SUẤT CỦA APAX ENGLISH');
        $inputSheet->getRowDimension('1')->setRowHeight(19);

        self::styleCells($spreadsheet, "B1", "fffff", "000000", 14, 1, true, "center", "center", false, $inputIndex);

        $inputSheet->getRowDimension('2')->setRowHeight(28.5);
        $inputSheet->getRowDimension('3')->setRowHeight(27);
        $inputSheet->getRowDimension('4')->setRowHeight(22);
        $inputSheet->getRowDimension('5')->setRowHeight(57);

        $inputSheet->setCellValue('B2', 'Ngày: ' . $fromDate);
        self::styleCells($spreadsheet, "B2", "fffff", "000000", 11, 0, true, "center", "center", false, $inputIndex);



        $inputSheet->mergeCells('C2:V2');
        $inputSheet->mergeCells('W2:Y2');
        $inputSheet->mergeCells('G4:J4');

        $inputSheet->mergeCells('A3:A5');
        $inputSheet->mergeCells('B3:B5');
        $inputSheet->mergeCells('C3:N3');
        $inputSheet->mergeCells('O3:Q4');
        $inputSheet->mergeCells('R3:V4');
        $inputSheet->mergeCells('W3:W5');
        $inputSheet->mergeCells('X3:X5');
        $inputSheet->mergeCells('Y3:Y5');
        $inputSheet->mergeCells('Z3:Z5');
        $inputSheet->mergeCells('AA3:AA5');
        $inputSheet->mergeCells('AB3:AB5');
        $inputSheet->mergeCells('AC3:AC5');
        $inputSheet->mergeCells('AD3:AD5');
        $inputSheet->mergeCells('AE3:AE5');
        $inputSheet->mergeCells('AF3:AF5');
        $inputSheet->mergeCells('AG3:AG5');
        $inputSheet->mergeCells('C4:F4');
        $inputSheet->mergeCells('G4:J4');
        $inputSheet->mergeCells('K4:N4');
        self::styleCells($spreadsheet, "A3:A5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "B3:B5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "C3:N3", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "C4:F4", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "C5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "D5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "E5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "F5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "G4:J4", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "G5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "H5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "I5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "J5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "K4:N4", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "K5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "L5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "M5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "N5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "O3:Q4", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "O5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "P5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "Q5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "R3:V4", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "R5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "S5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "T5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "U5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "V5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "W3:W5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "X3:X5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "Y3:Y5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "Z3:Z5", "ffff00", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AA3:AA5", "ffff00", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AB3:AB5", "ffff00", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AC3:AC5", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AD3:AD5", "ffff00", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AE3:AE5", "ff9900", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AF3:AF5", "ff9900", "000000", 11, 1, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AG3:AG5", "ffff00", "000000", 11, 1, true, "center", "center", true, $inputIndex);




        // $inputSheet->mergeCells('A3:M3');
        $inputSheet->setCellValue('C4', 'April');
        $inputSheet->setCellValue('G4', 'Igarten');
        $inputSheet->setCellValue('K4', 'CDI 4.0');
        $inputSheet->setCellValue('O4', 'Classes');
        $inputSheet->setCellValue('R4', 'Teachers');


        $inputSheet->setCellValue('C3', 'Students');


        $inputSheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $inputSheet->setCellValue("A3", "STT");
        $inputSheet->setCellValue("B3", "Trung tâm");
        $inputSheet->setCellValue("C5", "Full fee active");
        $inputSheet->setCellValue("D5", "Full fee pending");
        $inputSheet->setCellValue("E5", "Deposit");
        $inputSheet->setCellValue("F5", "Full fee pending/\nTotal full fee");
        $inputSheet->setCellValue("G5", "Full fee active");
        $inputSheet->setCellValue("H5", "Full fee pending");
        $inputSheet->setCellValue("I5", "Deposit");
        $inputSheet->setCellValue("J5", "Full fee pending/\nTotal full fee");
        $inputSheet->setCellValue("K5", "Full fee active");
        $inputSheet->setCellValue("L5", "Full fee pending");
        $inputSheet->setCellValue("M5", "Deposit");
        $inputSheet->setCellValue("N5", "Full fee pending/\nTotal full fee");
        $inputSheet->setCellValue("O3", "Classes");
        $inputSheet->setCellValue("O5", "April");
        $inputSheet->setCellValue("P5", "Igarten");
        $inputSheet->setCellValue("Q5", "CDI 4.0");
        $inputSheet->setCellValue("R3", "Teachers");
        $inputSheet->setCellValue("R5", "April teachers");
        $inputSheet->setCellValue("S5", "Igarten teachers");
        $inputSheet->setCellValue("T5", "CDI 4.0 teachers");
        $inputSheet->setCellValue("U5", "HT");
        $inputSheet->setCellValue("V5", "FM");
        $inputSheet->setCellValue("W3", "CM <=2 tháng");
        $inputSheet->setCellValue("X3", "Tổng CMs");
        $inputSheet->setCellValue("Y3", "Số phòng học max");
        $inputSheet->setCellValue("Z3", "Diện tích thuê\n(48m2/Phòng)(Hiệu suất xây dựng)");
        $inputSheet->setCellValue("AA3", "Giá tiền thuê Đồng/lm2/tháng");
        $inputSheet->setCellValue("AB3", "Giá thuê 1 tháng\n(Bao gồm cả phí dịch vụ và 10%VAT)");
        $inputSheet->setCellValue("AC3", "Rentention rate\n(75%)");
        $inputSheet->setCellValue("AD3", "Teaching hours by contract\nSố giờ dạy tính theo hợp đồng");
        $inputSheet->setCellValue("AE3", "Actual teaching hours\nSố giờ dạy chưa OT");
        $inputSheet->setCellValue("AF3", "OT");
        $inputSheet->setCellValue("AG3", "Teachers' costs\nTổng tiền phải chả cho giáo viên");


        $inputSheet->getColumnDimension("A")->setWidth(5);
        $inputSheet->getColumnDimension("B")->setWidth(66);
        $inputSheet->getColumnDimension("C")->setWidth(14);
        $inputSheet->getColumnDimension("D")->setWidth(16);
        $inputSheet->getColumnDimension("E")->setWidth(8);
        $inputSheet->getColumnDimension("F")->setWidth(14);
        $inputSheet->getColumnDimension("G")->setWidth(14);
        $inputSheet->getColumnDimension("H")->setWidth(16);
        $inputSheet->getColumnDimension("I")->setWidth(8);
        $inputSheet->getColumnDimension("J")->setWidth(18);
        $inputSheet->getColumnDimension("K")->setWidth(14);
        $inputSheet->getColumnDimension("L")->setWidth(16);
        $inputSheet->getColumnDimension("M")->setWidth(8);
        $inputSheet->getColumnDimension("N")->setWidth(18);
        $inputSheet->getColumnDimension("O")->setWidth(5);
        $inputSheet->getColumnDimension("P")->setWidth(8);
        $inputSheet->getColumnDimension("Q")->setWidth(8);
        $inputSheet->getColumnDimension("R")->setWidth(14);
        $inputSheet->getColumnDimension("S")->setWidth(16);
        $inputSheet->getColumnDimension("T")->setWidth(17);
        $inputSheet->getColumnDimension("U")->setWidth(7);
        $inputSheet->getColumnDimension("V")->setWidth(8);
        $inputSheet->getColumnDimension("W")->setWidth(16);
        $inputSheet->getColumnDimension("X")->setWidth(11);
        $inputSheet->getColumnDimension("Y")->setWidth(20);
        $inputSheet->getColumnDimension("Z")->setWidth(20);
        $inputSheet->getColumnDimension("AA")->setWidth(20);
        $inputSheet->getColumnDimension("AB")->setWidth(20);
        $inputSheet->getColumnDimension("AC")->setWidth(20);
        $inputSheet->getColumnDimension("AD")->setWidth(24);
        $inputSheet->getColumnDimension("AE")->setWidth(15);
        $inputSheet->getColumnDimension("AF")->setWidth(16);
        $inputSheet->getColumnDimension("AG")->setWidth(26);
    }

    public static function renderReport16OutputTitle($spreadsheet, $outputSheet, $outputIndex, $fromDate){
        $outputSheet->freezePane('C6');

        $outputSheet->setCellValue('B1', 'BẢNG THEO DÕI HIỆU SUẤT CỦA APAX ENGLISH');
        $outputSheet->getRowDimension('1')->setRowHeight(19);

        self::styleCells($spreadsheet, "B1", "fffff", "000000", 14, 1, true, "center", "center", false, $outputIndex);

        $outputSheet->getRowDimension('2')->setRowHeight(28.5);
        $outputSheet->getRowDimension('3')->setRowHeight(27);
        $outputSheet->getRowDimension('4')->setRowHeight(22);
        $outputSheet->getRowDimension('5')->setRowHeight(57);

        $outputSheet->setCellValue('B2', 'Ngày: ' . $fromDate);
        self::styleCells($spreadsheet, "B2", "fffff", "000000", 11, 0, true, "center", "center", false, $outputIndex);

        $outputSheet->setCellValue('A3','STT');
        $outputSheet->setCellValue('B3','Trung tâm');
        $outputSheet->setCellValue('C3','Ngày khai trương');
        $outputSheet->setCellValue('D3','1. ACS: Hiệu suất học sinh trong lớp');
        $outputSheet->setCellValue('D5','April (13.8)');
        $outputSheet->setCellValue('E5','Igarten (11)');
        $outputSheet->setCellValue('F5','CDI 4.0 (12)');
        $outputSheet->setCellValue('G3','2. Classes/Teachers (6): ');
        $outputSheet->setCellValue('G5','April 5.8');
        $outputSheet->setCellValue('H5','Igarten 5.8');
        $outputSheet->setCellValue('I5','CDI 4.0:5.0');
        $outputSheet->setCellValue('J3',"3. Classes/CM\n(8) (Hiệu suất sử dụng CM)");
        $outputSheet->setCellValue('K3',"4. Retention rate (75%)");
        $outputSheet->setCellValue('L3',"5. Doanh số thực tế bình quân/ 1,25 tỷ");
        $outputSheet->setCellValue('M3',"10. Lãng phí \n(Từ những gì hiện có)");
        $outputSheet->setCellValue('M5',"Từ Giáo  Viên");
        $outputSheet->setCellValue('N5',"Từ CMs");
        $outputSheet->setCellValue('O3',"Hiệu suất học sinh");
        $outputSheet->setCellValue('O5',"Max học sinh");
        $outputSheet->setCellValue('P5',"Tổng số học sinh");
        $outputSheet->setCellValue('Q5',"Hiệu suất học sinh");

        $outputSheet->getColumnDimension("A")->setWidth(5);
        $outputSheet->getColumnDimension("B")->setWidth(66);
        $outputSheet->getColumnDimension("C")->setWidth(17);
        $outputSheet->getColumnDimension("D")->setWidth(10);
        $outputSheet->getColumnDimension("E")->setWidth(14);
        $outputSheet->getColumnDimension("F")->setWidth(14);
        $outputSheet->getColumnDimension("G")->setWidth(14);
        $outputSheet->getColumnDimension("H")->setWidth(14);
        $outputSheet->getColumnDimension("I")->setWidth(14);
        $outputSheet->getColumnDimension("J")->setWidth(12);
        $outputSheet->getColumnDimension("K")->setWidth(14);
        $outputSheet->getColumnDimension("L")->setWidth(20);
        $outputSheet->getColumnDimension("M")->setWidth(8);
        $outputSheet->getColumnDimension("N")->setWidth(14);
        $outputSheet->getColumnDimension("O")->setWidth(14);
        $outputSheet->getColumnDimension("P")->setWidth(12);
        $outputSheet->getColumnDimension("Q")->setWidth(8);

        $cells = [
            "A3:A5",
            "B3:B5",
            "C3:C5",
            "D3:F4",
            "D5:D5",
            "E5:E5",
            "F5:F5",
            "G3:I4",
            "G5:G5",
            "H5:H5",
            "I5:I5",
            "J3:J5",
            "K3:K5",
            "L3:L5",
        ];

        foreach ($cells as $cell){
            $outputSheet->mergeCells("$cell");
            self::styleCells($spreadsheet, "$cell", "cfe2f3", "000000", 11, 1, true, "center", "center", true, $outputIndex);
        }

        $cells = [
            "M3:N4",
            "M5:N5",
            "O3:Q4",
            "O5:O5",
            "P5:P5",
            "Q5:Q5",
        ];
        foreach ($cells as $cell){
            $outputSheet->mergeCells("$cell");
            self::styleCells($spreadsheet, "$cell", "cfe2f3", "ff0000", 11, 1, true, "center", "center", true, $outputIndex);
        }

    }

    public static function setReport16InputRow($spreadsheet, $inputSheet, $inputIndex, $row, $key){
        $rowIndex = $key + 6;
        $inputSheet->setCellValue("A$rowIndex", $key + 1);
        $inputSheet->setCellValue("B$rowIndex", $row->name);
        $inputSheet->setCellValue("C$rowIndex", $row->april_full_fee_active);
        $inputSheet->setCellValue("D$rowIndex", $row->april_full_fee_pending);
        $inputSheet->setCellValue("E$rowIndex", $row->april_deposit);
        $inputSheet->setCellValue("F$rowIndex", "=D$rowIndex/(C$rowIndex+D$rowIndex)");
        $inputSheet->setCellValue("G$rowIndex", $row->igarten_full_fee_active);
        $inputSheet->setCellValue("H$rowIndex", $row->igarten_full_fee_pending);
        $inputSheet->setCellValue("I$rowIndex", $row->igaten_deposit);
        $inputSheet->setCellValue("J$rowIndex", "=H$rowIndex/(G$rowIndex+H$rowIndex)");
        $inputSheet->setCellValue("K$rowIndex", $row->cdi_full_fee_active);
        $inputSheet->setCellValue("L$rowIndex", $row->cdi_full_fee_pending);
        $inputSheet->setCellValue("M$rowIndex", $row->cdi_deposit);
        $inputSheet->setCellValue("N$rowIndex", "=L$rowIndex/(K$rowIndex+L$rowIndex)");
        $inputSheet->setCellValue("O$rowIndex", $row->april_class);
        $inputSheet->setCellValue("P$rowIndex", $row->igarten_class);
        $inputSheet->setCellValue("Q$rowIndex", $row->cdi_class);
        $inputSheet->setCellValue("R$rowIndex", $row->april_teacher);
        $inputSheet->setCellValue("S$rowIndex", $row->igarten_teacher);
        $inputSheet->setCellValue("T$rowIndex", $row->cdi_teacher);
        $inputSheet->setCellValue("W$rowIndex", $row->total_new_cms);
        $inputSheet->setCellValue("X$rowIndex", $row->total_all_cms);

        self::styleCells($spreadsheet, "A$rowIndex", "ffffff", "000000", 11, 0, true, "center", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "B$rowIndex", "ffffff", "000000", 11, 0, true, "left", "center", true, $inputIndex);


        self::styleCells($spreadsheet, "C$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "D$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "E$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "F$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "G$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "H$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "I$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "J$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "K$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "L$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "M$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "N$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "O$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "P$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "Q$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "R$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "S$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "T$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "U$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "V$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "W$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "X$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "Y$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "Z$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);

        self::styleCells($spreadsheet, "AA$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AB$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AC$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AD$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AE$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AF$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
        self::styleCells($spreadsheet, "AG$rowIndex", "ffffff", "000000", 11, 0, true, "right", "center", true, $inputIndex);
    }

    public static function writeHeadCompanyInfo($spreadsheet, $sheet, $colEnd, $reportName = "", $reportDate = "", $index = null, $branchName = ""){
        $cpnInfo = [
            [
                'id'=>1,
                'column' =>'A',
                'name' =>'UBND thành phố Hà Nội',
                'background' => null,
                'color' => '078acb',
                'font-size' =>10,
                'font-weight'=>null,
                'horizontal' =>'left',
                'vertical' =>'center',
                'row-height' =>0
            ],
            [
                'id'=>2,
                'column' =>'A',
                'name' =>'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY VÀ SÁNG TẠO QUỐC TẾ CMS',
                'background' => null,
                'color' => '078acb',
                'font-size' =>10,
                'font-weight'=>null,
                'horizontal' =>'left',
                'vertical' =>'center',
                'row-height' =>0
            ],
            [
                'id'=>3,
                'column' =>'A',
                'name' =>'ĐỊA CHỈ :Tầng 4, Tòa 21T2 Dự án Hapulico Complex, Số 01 Nguyễn Huy Tưởng, P.Thanh Xuân Trung, Q.Thanh Xuân, TP Hà Nội, Việt Nam',
                'background' => null,
                'color' => '078acb',
                'font-size' =>10,
                'font-weight'=>null,
                'horizontal' =>'left',
                'vertical' =>'center',
                'row-height' =>0
            ],
            [
                'id'=>4,
                'column' =>'A',
                'name' =>'MÃ SỐ THUẾ :0108190194',
                'background' => null,
                'color' => '078acb',
                'font-size' =>10,
                'font-weight'=>null,
                'horizontal' =>'left',
                'vertical' =>'center',
                'row-height' =>0
            ],
            [
                'id'=>5,
                'column' =>'A',
                'name' =>$reportName,
                'background' => null,
                'color' => '078acb',
                'font-size' =>14,
                'font-weight'=>1,
                'horizontal' =>'center',
                'vertical' =>'center',
                'row-height' =>40
            ],
            [
                'id'=>6,
                'column' =>'A',
                'name' =>$branchName,
                'background' => null,
                'color' => '078acb',
                'font-size' =>10,
                'font-weight'=>null,
                'horizontal' =>'center',
                'vertical' =>'center',
                'row-height' =>40
            ],
            [
                'id'=>7,
                'column' =>'A',
                'name' =>$reportDate,
                'background' => null,
                'color' => '078acb',
                'font-size' =>10,
                'font-weight'=>null,
                'horizontal' =>'center',
                'vertical' =>'center',
                'row-height' =>0
            ],
        ];

        foreach ($cpnInfo as $style){
            $sheet->mergeCells("{$style['column']}{$style['id']}:{$colEnd}{$style['id']}");
            $sheet->setCellValue($style['column'].$style['id'], $style['name']);
            if ($style['id'] == 5)
                $sheet->getRowDimension($style['id'])->setRowHeight(40);
            if ($style['id'] == 7)
                $sheet->getRowDimension($style['id'])->setRowHeight(20);

            if (!$index)
                self::styleCells($spreadsheet, "{$style['column']}{$style['id']}", null, $style['color'], $style['font-size'], $style['font-weight'], 3, $style['horizontal'], $style['vertical'], 'FFFFFF', null,'Times New Roman');
            else
                self::styleCells($spreadsheet, "{$style['column']}{$style['id']}", null, $style['color'], $style['font-size'], $style['font-weight'], 3, $style['horizontal'], $style['vertical'], 'FFFFFF', $index,'Times New Roman');
        }
    }
}