<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;
use App\Models\ProcessExcel;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\Redis;

class ConfigExportController extends Controller
{
    public function exportProducts()
    {
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'DANH SÁCH SẢN PHẨM');
        $sheet->mergeCells('A1:N1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $rank_id = '(' . $rank . ')';
        $ranks = DB::select(DB::raw("SELECT *from ranks where id in $rank_id"));
        $name = '';
        foreach ($ranks as $r) {
            $name .= $r->name . ',';
        }
        $name = rtrim($name, ',');
        // return $name;
        $sheet->setCellValue('A2', 'TỪ NGÀY - ĐẾN NGÀY');
        $sheet->mergeCells('A2:N2');
        $sheet->setCellValue('A3', "Loại: $name");
        $sheet->mergeCells('A3:N3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "MÃ LMS");
        $sheet->setCellValue("C5", "MÃ EFFECT");
        $sheet->setCellValue("D5", "HỌ TÊN HỌC SINH");
        $sheet->setCellValue("E5", "SẢN PHẨM");
        $sheet->setCellValue("F5", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("G5", "LỚP");
        $sheet->setCellValue("H5", "LOẠI ĐÁNH GIÁ");
        $sheet->setCellValue("I5", "GIÁO VIÊN");
        $sheet->setCellValue("J5", "NGƯỜI ĐÁNH GIÁ");
        $sheet->setCellValue("K5", "NGÀY ĐÁNH GIÁ");
        $sheet->setCellValue("L5", "NỘI DUNG");
        $sheet->setCellValue("M5", "EC");
        $sheet->setCellValue("N5", "CM");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(15);
        $sheet->getColumnDimension("F")->setWidth(25);
        $sheet->getColumnDimension("G")->setWidth(24);
        $sheet->getColumnDimension("H")->setWidth(55);
        $sheet->getColumnDimension("I")->setWidth(25);
        $sheet->getColumnDimension("J")->setWidth(35);
        $sheet->getColumnDimension("K")->setWidth(24);
        $sheet->getColumnDimension("L")->setWidth(30);
        $sheet->getColumnDimension("M")->setWidth(35);
        $sheet->getColumnDimension("N")->setWidth(35);


        $branch_id = '';
        if ($branch != '_') {
            $branch_id .= '(' . $branch . ')';
        }

        $product_id = '';
        if ($product != '_') {
            $product_id .= '(' . $product . ')';
        }

        $program_id = '';
        if ($program != '_') {
            $program_id .= '(' . $program . ')';
        }

        $rank_id = '';
        if ($rank != '_') {
            $rank_id .= '(' . $rank . ')';
        }
        $where = '';

        if ($branch_id) {
            $where .= "and ct.branch_id in $branch_id ";
        }
        // if ($program_id){
        //     $where .= "and ct.program_id in $program_id ";
        // }
        if ($product_id) {
            $where .= "and ct.product_id in $product_id ";
        }
        if ($rank_id) {
            $where .= "and r.id in $rank_id ";
        }
        $c = '';
        if ($where) {
            $where = substr($where, 3);
        }
        // return $where;
        $sql = "SELECT st.id,st.stu_id,st.accounting_id,st.name, pr.name as program_name, pd.name as product_name, r.name as rank_name, cl.cls_name as class_name
                ,tea.ins_name as teacher_name, term.rating_date as ratingdate, CONCAT(creator.full_name,' - ',creator.username) as creator_name, term.comment,
                CONCAT(ec.full_name,' - ',ec.username) as ec_name, CONCAT(cm.full_name,' - ',cm.username) as cm_name
                from term_student_rank as term
                JOIN students as st on st.id = term.student_id
                LEFT JOIN contracts as ct ON ct.student_id = st.id
                LEFT JOIN programs as pr on pr.id = ct.program_id
                LEFT JOIN ranks as r on r.id = term.rank_id
                LEFT JOIN products as pd on pd.id = ct.product_id
                LEFT JOIN enrolments as el on el.contract_id = ct.id
                LEFT JOIN classes as cl on cl.id = el.class_id
                LEFT JOIN sessions as sem on sem.class_id = cl.id
                LEFT JOIN users as cm on ct.cm_id = cm.id
                LEFT JOIN users as ec on ec.id = ct.ec_id
                LEFT JOIN teachers as tea on tea.id = sem.teacher_id
                LEFT JOIN users as creator on creator.id = term.creator_id
                where $where AND term.id in (SELECT MAX(id) from term_student_rank GROUP BY student_id) GROUP BY st.id";
        // return $sql;
        $students = DB::select(DB::raw($sql));

        // return $students;
        for ($i = 0; $i < count($students); $i++) {
            $x = $i + 6;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('D' . $x, $students[$i]->name);
            $sheet->setCellValue('B' . $x, $students[$i]->stu_id);
            $sheet->setCellValue('C' . $x, $students[$i]->accounting_id);
            $sheet->setCellValue('E' . $x, $students[$i]->product_name);
            $sheet->setCellValue('F' . $x, $students[$i]->program_name);
            $sheet->setCellValue('H' . $x, $students[$i]->rank_name);
            $sheet->setCellValue('G' . $x, $students[$i]->class_name);
            $sheet->setCellValue('I' . $x, $students[$i]->teacher_name);
            $sheet->setCellValue('J' . $x, $students[$i]->creator_name);
            $sheet->setCellValue('K' . $x, $students[$i]->ratingdate);
            $sheet->setCellValue('L' . $x, $students[$i]->comment);
            $sheet->setCellValue('M' . $x, $students[$i]->ec_name);
            $sheet->setCellValue('N' . $x, $students[$i]->cm_name);

            $st = "A" . $x;
            $ed = "N" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "D$x", "FFFFFF", "black", 9, '', 3, "left", "center", true);
            ProcessExcel::styleCells($spreadsheet, "L$x:N$x", "FFFFFF", "black", 9, '', 3, "left", "center", true);
        }

        ProcessExcel::styleCells($spreadsheet, "A5:N5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:N1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:N2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:N3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:N4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);


        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Danh sách đánh giá học sinh.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }
}
