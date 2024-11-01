<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\CustomerCare;
use App\Models\LmsClass;
use App\Models\ProcessExcel;
use App\Models\Report;
use App\Models\Report as r;
use App\Models\Student;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Checkin;

class ExelController extends Controller
{
    public function show($branch = null, $product = null, $type = null)
    {

    }

    public function test($branch = null, $product = null, $program = null, $rank = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO ĐÁNH GIÁ HỌC SINH');
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

    public function extractReport($branch = null, $product = null, $program = null, $fromDate = null, $toDate = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO TỔNG SỐ HỌC SINH MỚI HÀNG THÁNG');
        $sheet->mergeCells('A1:O1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);


        $sheet->setCellValue('A2', 'TỪ NGÀY : ' . $fromDate . ' - ' . 'ĐẾN NGÀY : ' . $toDate);
        $sheet->mergeCells('A2:O2');
        // $sheet->mergeCells('A3:O3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "MÃ LMS");
        $sheet->setCellValue("C5", "MÃ EFFECT");
        $sheet->setCellValue("D5", "HỌ TÊN HỌC SINH");
        $sheet->setCellValue("E5", "SẢN PHẨM");
        $sheet->setCellValue("F5", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("G5", "LỚP");
        $sheet->setCellValue("H5", "LOẠI KHÁCH HÀNG");
        $sheet->setCellValue("I5", "GÓI PHÍ");
        $sheet->setCellValue("J5", "NGÀY ĐÓNG PHÍ");
        $sheet->setCellValue("K5", "SỐ TIỀN PHẢI ĐÓNG");
        $sheet->setCellValue("L5", "ĐÃ ĐÓNG");
        $sheet->setCellValue("M5", "CÔNG NỢ");
        $sheet->setCellValue("N5", "EC");
        $sheet->setCellValue("O5", "CM");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(15);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(15);
        $sheet->getColumnDimension("F")->setWidth(25);
        $sheet->getColumnDimension("G")->setWidth(24);
        $sheet->getColumnDimension("H")->setWidth(25);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(24);
        $sheet->getColumnDimension("K")->setWidth(24);
        $sheet->getColumnDimension("L")->setWidth(24);
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(35);
        $sheet->getColumnDimension("O")->setWidth(35);


        $branch_id = '';
        if ($branch != '_') {
            $branch_id .= '(' . $branch . ')';
        }
        $list_branch = DB::select(DB::raw("SELECT id,name from branches where id in $branch_id"));
        // return $list_branch;
        $product_id = '';
        if ($product != '_') {
            $product_id .= '(' . $product . ')';
        }

        $program_id = '';
        if ($program != '_') {
            $program_id .= '(' . $program . ')';
        }

        $from_date = '';
        if ($fromDate != '_') {
            $from_date .= '(' . $fromDate . ')';
        }


        $to_date = '';
        if ($toDate != '_') {
            $to_date .= '(' . $toDate . ')';
        }


        $where = '';

        if ($branch_id) {
            $where .= "and ct.branch_id in $branch_id ";
        }
        if ($program_id) {
            $where .= "and ct.program_id in $program_id ";
        }
        if ($product_id) {
            $where .= "and ct.product_id in $product_id ";
        }
        // $where .= " and count(ct.*) as payment_time <= 1";

        // if ($from_date){
        //     $where .= "and pm.created_at > '$from_date'";
        // }
        // if ($to_date){
        //     $where .= "and pm.created_at < '$to_date'";
        // }
        // $where .= "and count(pm.contract_id) = 1";

        $c = '';
        if ($where) {
            $where = substr($where, 3);
            $c .= 'where ' . $where;
        }
        $students = DB::select(DB::raw("SELECT
                                                st.name as student_name,
                                                st.stu_id as stu_id,
                                                st.accounting_id as accounting_id,
                                                IF(st.type =1, 'VIP', 'Thường') AS student_type,
                                                cl.cls_name AS class_name,
                                                cl.id as class_id,
                                                ct.cm_id AS cm_id,
                                                ct.must_charge,
                                                CONCAT(us.full_name, ' - ', us.username) AS ec_name,
                                                CONCAT(cm.full_name, ' - ', us.username) AS cm_name,
                                                ct.ec_id AS ec_id,
                                                pr.name AS product_name,
                                                pg.name AS program_name,
                                                pm.amount AS amount,
                                                pm.debt AS debt,
                                                br.id as branch_id,
                                                date_format(pm.created_at, '%Y-%m-%d') as payment_date,
                                                tf.name as tuition_price
                                            FROM students AS st
                                            LEFT JOIN contracts AS ct ON ct.student_id = st.id
                                            LEFT JOIN branches AS br ON br.id = ct.branch_id
                                            LEFT JOIN enrolments AS el ON (el.contract_id = ct.id AND el.student_id = st.id)
                                            LEFT JOIN classes AS cl ON cl.brch_id = br.brch_id
                                            LEFT JOIN products AS pr ON ct.product_id = pr.id
                                            LEFT JOIN programs AS pg ON ct.program_id = pg.id
                                            LEFT JOIN payment AS pm ON ct.id = pm.contract_id
                                            LEFT JOIN tuition_fee AS tf ON tf.product_id = ct.id
                                            LEFT JOIN users AS cm ON cm.id = ct.cm_id
                                            LEFT JOIN users AS us ON us.id = ct.ec_id $c GROUP BY st.id"));
        // return $students;
        $p = 5;
        for ($j = 0; $j < count($list_branch); $j++) {
            $y = $p + 1;
            $p++;
            $st = 'A' . $y;
            $en = 'O' . $y;
            $sheet->setCellValue('A' . $y, $list_branch[$j]->name);
            ProcessExcel::styleCells($spreadsheet, "A$y:O$y", "FFFFFF", "black", 12, 1, 3, "left", "center", true);
            $sheet->mergeCells("$st:$en");
            for ($i = 0; $i < count($students); $i++) {

                if ($students[$i]->branch_id == $list_branch[$j]->id) {
                    $x = $p + 1;
                    $p++;
                    $sheet->setCellValue('A' . $x, $i + 1);
                    $sheet->setCellValue('B' . $x, $students[$i]->stu_id);
                    $sheet->setCellValue('C' . $x, $students[$i]->accounting_id);
                    $sheet->setCellValue('D' . $x, $students[$i]->student_name);
                    $sheet->setCellValue('E' . $x, $students[$i]->product_name);
                    $sheet->setCellValue('F' . $x, $students[$i]->program_name);
                    $sheet->setCellValue('G' . $x, $students[$i]->class_name);
                    $sheet->setCellValue('H' . $x, $students[$i]->student_type);
                    $sheet->setCellValue('I' . $x, $students[$i]->tuition_price);
                    $sheet->setCellValue('J' . $x, $students[$i]->payment_date);
                    $sheet->setCellValue('K' . $x, $students[$i]->must_charge);
                    $sheet->setCellValue('L' . $x, $students[$i]->amount);
                    $sheet->setCellValue('M' . $x, ($students[$i]->must_charge - $students[$i]->amount));
                    $sheet->setCellValue('N' . $x, $students[$i]->ec_name);
                    $sheet->setCellValue('O' . $x, $students[$i]->cm_name);

                    $st = "A" . $x;
                    $ed = "O" . $x;
                    ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);

                    ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "C$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "D$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "N$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "O$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                }
            }
        }

        // return $students;
        // return $where;

        ProcessExcel::styleCells($spreadsheet, "A5:O5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:O1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:O2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:O3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:O4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Học Sinh Mới Hàng Tháng.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function report_acs($branch = null, $from_date = null, $to_date = null)
    {
        $fromDate = strtotime($from_date);
        $toDate = strtotime($to_date);

        $today = date('Y-m-d');

        if (!$fromDate) {
            $from_date = $today;
        }

        $where = "";
        if ($branch != '_')
            $where .= " AND b.id in ($branch)";
        $q = "
            SELECT
              b.id,
              b.NAME branch_name,
              r.NAME region_name,
              ( SELECT COUNT( id ) FROM rooms WHERE `status` > 0 AND rooms.branch_id = b.id ) total_rooms,
              (
                SELECT COUNT( id )
                FROM classes
                WHERE
                  classes.brch_id = b.brch_id
                  AND cls_startdate <= '$from_date' AND cls_enddate >= '$from_date'
                  AND cls_iscancelled = 'no'

              ) total_classes,
              (
                SELECT
                  COUNT( u.id )
                FROM
                  term_user_branch t
                  LEFT JOIN users u ON t.user_id = u.id
                WHERE
                  t.role_id = 36
                  AND u.STATUS > 0
                  AND u.start_date <= '$from_date' AND ( u.end_date >= '$from_date'
                    OR u.end_date IS NULL
                  )
                  AND t.branch_id = b.id
                ) AS total_teachers,
              (
              SELECT
                COUNT( DISTINCT ( c.student_id ) )
              FROM
                contracts c
                LEFT JOIN enrolments e ON c.enrolment_id = e.id
                LEFT JOIN pendings p ON p.contract_id = c.id
              WHERE
                c.debt_amount = 0
                AND c.type > 0
                AND c.branch_id = b.id
                AND (
                  (
                    DATE( c.updated_at ) <= '$from_date' AND DATE( c.end_date ) >= '$from_date'
                    AND ( c.type <> 5 OR c.STATUS = 6 )
                  )
                  OR ( DATE( c.start_date ) <= '$from_date' AND c.type = 5 )
                  OR ( DATE( c.created_at ) <= '$from_date' AND c.STATUS = 6 )
                )
                AND c.total_charged > 0
                AND c.debt_amount = 0
              AND
              IF
                ( c.enrolment_id, e.STATUS > 0, TRUE )
              ) AS total_full_fees
              FROM
              branches b
              LEFT JOIN regions r ON b.region_id = r.id
              WHERE b.status = 1
              $where
          ";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Average Classes Size');
        $sheet->mergeCells('A1:H1');
        $sheet->getRowDimension('1')->setRowHeight(30);
        ProcessExcel::styleCells($spreadsheet, "A1:H1", "FFFFFF", "fff", 16, 1, true, "center", "center", true);

        $sheet->setCellValue('A2', "Ngày: $from_date");
        $sheet->mergeCells('A2:H2');
        ProcessExcel::styleCells($spreadsheet, "A2:H2", "FFFFFF", "black", 11, '', true, "center", "center", true);


        $sheet->setCellValue("A3", "BRANCH");
        $sheet->setCellValue("B3", "BRANCH");
        $sheet->setCellValue("C3", "CLASS ROOM");
        $sheet->setCellValue("D3", "MAX CLASS NUMBER");
        $sheet->setCellValue("E3", "TOTAL ACTUALY CLASS");
        $sheet->setCellValue("F3", "TOTAL FULL FEE STUDENT");
        $sheet->setCellValue("G3", "ASC FULL FEE");
        $sheet->setCellValue("H3", "ROOM PERFOMANCE");

        $sheet->mergeCells('A3:A4');
        $sheet->mergeCells('B3:B4');
        $sheet->mergeCells('C3:C4');
        $sheet->mergeCells('D3:D4');
        $sheet->mergeCells('E3:E4');
        $sheet->mergeCells('F3:F4');
        $sheet->mergeCells('G3:G4');
        $sheet->mergeCells('H3:H4');


        ProcessExcel::styleCells($spreadsheet, "A3:H3", "FFFFFF", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A4:H4", "FFFFFF", "black", 11, 1, true, "center", "center", true);

        $sheet->getStyle('A3:G3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('add8e6');

        $sheet->getColumnDimension("A")->setWidth(40);
        $sheet->getColumnDimension("B")->setWidth(15);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(25);
        $sheet->getColumnDimension("E")->setWidth(25);
        $sheet->getColumnDimension("F")->setWidth(15);
        $sheet->getColumnDimension("G")->setWidth(20);
        $sheet->getColumnDimension("H")->setWidth(20);


        $branches = DB::select(DB::raw($q));
//        echo $q;die;
        for ($j = 0; $j < count($branches); $j++) {

            $y = $j + 5;

            $maxClassNumber = 0;
            $OPmaxClassNumber = (($branches[$j]->total_rooms * 2 * 4) + ($branches[$j]->total_rooms * 6 * 2));
            if ($OPmaxClassNumber > 0)
                $maxClassNumber = $OPmaxClassNumber / 2;

            $ascFullfee = 0;
            if ($branches[$j]->total_full_fees > 0)
                $ascFullfee = ($branches[$j]->total_full_fees / $branches[$j]->total_classes);

            $progressC = 0;
            if ($branches[$j]->total_classes > 0)
                $progressC = ($branches[$j]->total_classes / $maxClassNumber);

            $sheet->setCellValue('A' . $y, $branches[$j]->branch_name);
            $sheet->setCellValue('B' . $y, $branches[$j]->region_name);
            $sheet->setCellValue('C' . $y, $branches[$j]->total_rooms);
            $sheet->setCellValue('D' . $y, $maxClassNumber);
            $sheet->setCellValue('E' . $y, $branches[$j]->total_classes);
            $sheet->setCellValue('F' . $y, $branches[$j]->total_full_fees);
            $sheet->setCellValue('G' . $y, $ascFullfee);
            $sheet->setCellValue('H' . $y, $progressC . '%');
//            ProcessExcel::styleCells($spreadsheet, "$st:$en", "51BCBB", "black", 11, '', true, "center", "center", true);

            $st = 'A' . $y;
            ProcessExcel::styleCells($spreadsheet, "$st", "FFFFFF", "black", 11, '', true, "left", "center", true);

            $st = 'D' . $y;
            ProcessExcel::styleCells($spreadsheet, "$st", "FFFFFF", "0000ff", 11, 1, true, "center", "center", true);

            // $sheet->getRowDimension("$y")->setRowHeight(20);
        }

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Average Classes Size.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC01A($branch = null, $fromDate = null, $toDate = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Average Classes Size 123');
        $sheet->mergeCells('A1:H1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $sheet->setCellValue('A2', 'Từ ngày' . $fromDate . ' - ' . 'Đến ngày: ' . $toDate);
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "BRANCH");
        $sheet->setCellValue("C5", "CLASS ROOM");
        $sheet->setCellValue("D5", "MAX CLASS NUMBER");
        $sheet->setCellValue("E5", "TOTAL ACTUALY CLASS");
        $sheet->setCellValue("F5", "TOTAL FULL FEE STUDENT");
        $sheet->setCellValue("G5", "ACS FULL FEE");
        $sheet->setCellValue("H5", "ROOM PERFOMANCE");


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
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(24);

//      dd($branch, $fromDate, $toDate);

        $branch_id = '';
        if ($branch != '_') {
            $branch_id .= '(' . $branch . ')';
        }


        $from_date = '';
        if ($fromDate != '_') {
            $from_date .= '(' . $fromDate . ')';
        }


        $to_date = '';
        if ($toDate != '_') {
            $to_date .= '(' . $toDate . ')';
        }


        $where = '';

        if ($branch_id) {
            $where .= "and ct.branch_id in $branch_id ";
        }

        if ($from_date) {
            $where .= "and ct.branch_id in $branch_id ";
        }

        $c = '';
        if ($where) {
            $where = substr($where, 3);
            $c .= 'where ' . $where;
        }
        $students = DB::select(DB::raw("SELECT
                                st.name,
                                st.stu_id,
                                st.accounting_id,
                                cl.cls_name as class_name,
                                rank.name as rank,
                                tsr.comment as comment,
                                tsr.rating_date as ratingdate,
                                ct.cm_id as cm_id,
                                us.username as creator,
                                ct.ec_id as ec_id,
                                pr.name as product_name,
                                pg.name as program_name
                                from students as st
                                LEFT JOIN contracts as ct on ct.student_id = st.id
                                LEFT JOIN enrolments as el on (el.contract_id = ct.id and el.student_id = st.id)
                                LEFT JOIN classes as cl on cl.id = el.class_id
                                LEFT JOIN products as pr on ct.product_id = pr.id
                                LEFT JOIN programs as pg on ct.program_id = pg.id
                                LEFT JOIN term_student_rank as tsr on tsr.student_id = st.id
                                left join users as us on us.id = tsr.creator_id $c"));
//        dd($students);
        for ($i = 0; $i < count($students); $i++) {
            $x = $i + 6;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('D' . $x, $students[$i]->name);
            $sheet->setCellValue('B' . $x, $students[$i]->stu_id);
            $sheet->setCellValue('C' . $x, $students[$i]->accounting_id);
            $sheet->setCellValue('E' . $x, $students[$i]->product_name);
            $sheet->setCellValue('F' . $x, $students[$i]->program_name);
            $sheet->setCellValue('H' . $x, $students[$i]->rank);
            $sheet->setCellValue('G' . $x, $students[$i]->class_name);


            $st = "A" . $x;
            $ed = "H" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
        }

        ProcessExcel::styleCells($spreadsheet, "A5:N5", "FFFFFF", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:N1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:N2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:N3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:N4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);


        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Average Classes Size 123.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function report_renew_detail($branch = null, $pd = null, $pr = null, $from_date = null, $to_date = null)
    {
        $fromDate = strtotime($from_date);
        $toDate = strtotime($to_date);

        $endMonth = date('Y-m-t');
        $default_start_date = (date('Y-m-01'));

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = $endMonth;
        }


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO HỌC SINH TÁI PHÍ - CHI TIẾT');
        $sheet->mergeCells('A1:M1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        // $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(20);

        $sheet->setCellValue('F2', "TỪ NGÀY - ĐẾN NGÀY: $from_date - $to_date");
        $sheet->mergeCells('F2:H2');
        $sheet->getRowDimension('2')->setRowHeight(15);

        $sheet->setCellValue("A4", "STT");
        $sheet->setCellValue("B4", "MÃ LMS");
        $sheet->setCellValue("C4", "MÃ EFFECT");
        $sheet->setCellValue("D4", "TÊN HỌC SINH");
        $sheet->setCellValue("E4", "SẢN PHẨM");
        $sheet->setCellValue("F4", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("G4", "LỚP");
        $sheet->setCellValue("H4", "LOẠI KHÁCH HÀNG");
        $sheet->setCellValue("I4", "NGÀY ĐẾN HẠN TÁI TỤC");
        $sheet->setCellValue("J4", "KẾT QUẢ");
        $sheet->setCellValue("K4", "GÓI TÁI PHÍ");
        $sheet->setCellValue("L4", "SỐ TIỀN TÁI PHÍ");
        $sheet->setCellValue("M4", "EC");
        $sheet->setCellValue("N4", "CM");
        $sheet->getRowDimension('4')->setRowHeight(40);

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);

        $where = null;

        if ($pd && $pd != '_') {
            $where .= " AND c.product_id IN ($pd)";
        }

        if ($pr && $pr != '_') {
            $where .= " AND c.program_id IN ($pr)";
        }

        if ($branch && $branch != '_') {
            $where .= "AND s.branch_id IN ($branch)";
        }

        $q = "
        SELECT
          s.id AS student_id,
          s.NAME AS student_name,
          IF ( e.final_last_date > '$to_date', 'Thành công', IF ( '$to_date' <= CURDATE( ), 'Thất bại', '' ) ) AS customer_result,
          IF ( e.final_last_date > '$to_date', tf.name , '' ) as tuition_fee_name,
          IF ( e.final_last_date > '$to_date', c.must_charge , '' ) as must_charge,
          e.id AS eid ,
          c.count_recharge AS recharge_time,
          s.crm_id,
          s.accounting_id,
          s.stu_id,
          IF( s.type = 1, 'VIP', 'Thường' ) AS customer_type,
          br.NAME AS branch_name,
          pd.NAME AS product_name,
          pr.NAME AS program_name,
          tf.price AS list_price,
          u1.username AS ec_name,
          u2.username AS cm_name,
          cl.cls_name AS class_name,
          p.total AS total_charged,
          p.debt,
          p.created_at AS charged_date,
          e.last_date AS e_lastdate

        FROM
          enrolments AS e
          LEFT JOIN contracts AS c ON e.contract_id = c.id
          LEFT JOIN students AS s ON c.student_id = s.id
          LEFT JOIN tuition_transfer AS tff ON tff.from_contract_id = c.id
          LEFT JOIN programs AS pr ON pr.id = c.program_id
          LEFT JOIN classes AS cl ON cl.program_id = pr.id
          LEFT JOIN products AS pd ON pd.id = c.product_id
          LEFT JOIN payment AS p ON p.contract_id = c.id
          LEFT JOIN branches AS br ON br.id = s.branch_id
          LEFT JOIN tuition_fee AS tf ON tf.id = c.tuition_fee_id
          LEFT JOIN users AS u1 ON u1.id = c.ec_id
          LEFT JOIN users AS u2 ON u2.id = c.cm_id
        WHERE
          e.id IN (
          SELECT
            MAX( e.id )
          FROM
            enrolments AS e
            LEFT JOIN contracts AS c ON e.contract_id = c.id
            LEFT JOIN students AS s ON c.student_id = s.id
          WHERE
            s.branch_id = c.branch_id
            $where
          GROUP BY
            s.id
          )
          AND e.last_date >= '$from_date'
          AND e.last_date <= '$to_date'
          AND tff.id IS NULL
        GROUP BY s.id
      ";

        // return($query);


        $result = DB::select(DB::raw($q));
        $p = 5;
        $stt = 5;

        for ($j = 0; $j < count($result); $j++) {
            if ($result) {
                $p++;
                $y = $p;
                $stt++;
                $sheet->setCellValue('A' . $y, $stt);
                $sheet->setCellValue('B' . $y, $result[$j]->stu_id);
                $sheet->setCellValue('C' . $y, $result[$j]->accounting_id);
                $sheet->setCellValue('D' . $y, $result[$j]->student_name);
                $sheet->setCellValue('E' . $y, $result[$j]->product_name);
                $sheet->setCellValue('F' . $y, $result[$j]->program_name);
                $sheet->setCellValue('G' . $y, $result[$j]->class_name);
                $sheet->setCellValue('H' . $y, $result[$j]->customer_type);
                $sheet->setCellValue('I' . $y, $result[$j]->e_lastdate);
                $sheet->setCellValue('J' . $y, $result[$j]->customer_result);
                $sheet->setCellValue('K' . $y, $result[$j]->tuition_fee_name);
                $sheet->setCellValue('L' . $y, $result[$j]->must_charge);
                $sheet->setCellValue('M' . $y, $result[$j]->ec_name);
                $sheet->setCellValue('N' . $y, $result[$j]->cm_name);
                $st = 'A' . $y;
                $en = 'N' . $y;
                ProcessExcel::styleCells($spreadsheet, "$st:$en", "black", "black", 11, '', true, "left", "center", true);
            }
        }


        ProcessExcel::styleCells($spreadsheet, "A1:N1", "black", "fff", 16, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A2:N2", "black", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A3:N3", "3FC2EE", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A4:N4", "3FC2EE", "black", 11, 1, true, "center", "center", true);


        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Danh sách Học Sinh Tái Phí - Chi Tiết.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function report_renew_general($branch = null, $zones = null, $regions = null, $from_date = null, $to_date = null)
    {
        $fd = strtotime($from_date);
        $td = strtotime($to_date);

        if (!$fd)
            $from_date = date('Y-m-01');
        if (!$td)
            $to_date = date('Y-m-t');

        // dd($zones, $regions, $from_date, $to_date);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO TỔNG HỢP HỌC SINH TÁI PHÍ - TỔNG HỢP');
        $sheet->mergeCells('A1:D1');
        $sheet->getRowDimension('1')->setRowHeight(30);
        // $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(50);

        $sheet->setCellValue('A2', "TỪ NGÀY - ĐẾN NGÀY: $from_date - $to_date");
        $sheet->mergeCells('A2:D2');

        $sheet->setCellValue("A3", "TRUNG TÂM");
        $sheet->setCellValue("B3", "SỐ HỌC SINH ĐẾN HẠN TÁI PHÍ");
        $sheet->setCellValue("C3", "SỐ HỌC SINH ĐÓNG PHÍ TÁI TỤC");
        $sheet->setCellValue("D3", "TỶ LỆ TÁI TỤC");
        $sheet->mergeCells('A3:A4');
        $sheet->mergeCells('B3:B4');
        $sheet->mergeCells('C3:C4');
        $sheet->mergeCells('D3:D4');
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        ProcessExcel::styleCells($spreadsheet, "A1:D1", "black", "fff", 16, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A2:D2", "black", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A3:D3", "3FC2EE", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A4:D4", "3FC2EE", "black", 11, 1, true, "center", "center", true);

        $where = '';
        if ($branch != '_')
            $where .= " AND b.id in ($branch) ";

        $whereRenew = " AND s.branch_id = b.id";
        $renewSql = Student::sqlCountRenewStudent($from_date, $to_date, $whereRenew);
        $renewSqlSuccess = Student::sqlCountRenewStudent($from_date, $to_date, $whereRenew, true);
        $query = "SELECT
                  ( $renewSql ) as total_item,
                  ( $renewSqlSuccess ) as success_item,
                  b.name as branch_name

                FROM branches as b WHERE b.status = 1 $where
             ";
        $branches = u::query($query);
        // return $data;

        $x = 5;

        for ($i = 0; $i < count($branches); $i++) {
            $oper = '';
            $x++;
            if ($branches[$i]->success_item > 0) {
                $oper = ($branches[$i]->success_item / $branches[$i]->total_item) * 100;
            } else {
                $oper = 0;
            }

            $sheet->setCellValue('A' . $x, $branches[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $branches[$i]->total_item);
            $sheet->setCellValue('C' . $x, $branches[$i]->success_item);
            $sheet->setCellValue('D' . $x, $oper . '%');


            $st = "A" . $x;
            $ed = "D" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
        }

        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Danh sách tổng hợp học sinh tái phí.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC02(Request $request, $params)
    {
        $da = json_decode($params);
        $branch_ids = $da->branches;
        $product_ids = $da->products;
        $start_date = $da->start ? $da->start : date('Y-m-01');
        $date = $da->date ? $da->date : date('Y-m-d');

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN ANH NGỮ APAX');
        $sheet->mergeCells('A1:I1');
        ProcessExcel::styleCells($spreadsheet, "A1:I1", "ffffff", "000", 9, 1, true, "left", "center", false);

        $sheet->setCellValue('A2', 'Tầng 7, Tòa nhà 14 Láng Hạ, Ba Đình, Hà Nội');
        $sheet->mergeCells('A2:I2');
        ProcessExcel::styleCells($spreadsheet, "A2:I2", "ffffff", "000", 9, 1, true, "left", "center", false);

        $sheet->setCellValue('A3', 'BÁO CÁO HIỆN TRẠNG TRUNG TÂM');
        $sheet->mergeCells('A3:I3');
        ProcessExcel::styleCells($spreadsheet, "A3:Q3", "ffffff", "000", 16, 1, true, "center", "center", false);

        $sheet->setCellValue('A4', "Từ ngày : $start_date - Đến ngày: $date");
        $sheet->mergeCells('A4:I4');
        ProcessExcel::styleCells($spreadsheet, "A4:Q4", "ffffff", "000", 9, 0, true, "center", "center", false);

        $sheet->getRowDimension('1')->setRowHeight(18);
        $sheet->getRowDimension('2')->setRowHeight(18);
        $sheet->getRowDimension('3')->setRowHeight(25);
        $sheet->getRowDimension('4')->setRowHeight(18);
        $sheet->getRowDimension('5')->setRowHeight(30);

        // $sheet->setCellValue('A3', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "TRUNG TÂM");
        $sheet->setCellValue("C5", "CỌC BỎ");
        $sheet->setCellValue("D5", "TỔNG SỐ HỌC SINH FULL PHÍ");
        $sheet->setCellValue("E5", "TỔNG SỐ HỌC SINH TRIAL");
        $sheet->setCellValue("F5", "TỔNG TRIAL CHUYỂN SANG THANH TOÁN");
        $sheet->setCellValue("G5", "TỔNG SỐ HỌC SINH CHECKIN");
        $sheet->setCellValue("H5", "TỔNG FULL PHÍ/TỔNG TRIAL");
        $sheet->setCellValue("I5", "TỔNG FULL PHÍ/TỔNG TRIAL CHUYỂN SANG THANH TOÁN");
        $sheet->setCellValue("J5", "TỔNG FULL PHÍ/TỔNG CHECKIN");
        ProcessExcel::styleCells($spreadsheet, "A5:J5", "add8e6", "000", 9, 1, true, "center", "center", true);


        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(15);
        $sheet->getColumnDimension("D")->setWidth(25);
        $sheet->getColumnDimension("E")->setWidth(25);
        $sheet->getColumnDimension("F")->setWidth(25);
        $sheet->getColumnDimension("G")->setWidth(25);
        $sheet->getColumnDimension("H")->setWidth(25);
        $sheet->getColumnDimension("I")->setWidth(40);
        $sheet->getColumnDimension("J")->setWidth(25);

        if (empty($branch_ids)) {
            $token = $da->tk;
            $response = (object)[];
            $response->message = 'invalid session';
            $response->status = 200;
            $request->authorized = null;
            $check = false;
            $sinformation = (object)[];
            if ($token !== null) {
                $session = Redis::get($token);
                if ($session) {
                    $sinformation = json_decode($session);
                    $request->authorized = $session;
                    $request->users_data = $sinformation;
                    $check = true;
                }
            }
            if (!$check) {
                $response = [
                    'code' => 403,
                    'message' => 'Permission denied...',
                    'data' => null
                ];
                exit(json_encode($response));
            }
            $branch_ids = u::getBranchIds($request->users_data);
        } else {
            $branch_ids_term = [];
            foreach ($branch_ids as $key => $value) {
                array_push($branch_ids_term, $value->id);
            }
            $branch_ids = $branch_ids_term;
        }
        $branch_ids_string = implode(',', $branch_ids);


        $product_condition = '';
        if (!empty($product_ids)) {
            $product_ids_string = implode(',', $product_ids);
            $product_condition = " AND c.`product_id` IN ($product_ids_string)";
        }

        if (!$date) {
            $date = date('Y-m-d 23:59:59');
        } else {
            $date = date('Y-m-d', strtotime($date)) . " 23:59:59";
        }
        if (!$start_date) {
            $start_date = date('Y-m-01 00:00:00');
        } else {
            $start_date = date('Y-m-d', strtotime($start_date)) . " 00:00:00";
        }

        $query = "
            (
                SELECT
                    COUNT(*) AS count_student, t.branch_id AS branch_id, t.student_type
                FROM (
                    SELECT
                        s.id AS student_id,
                        t.type AS student_type,
                        s.`branch_id` AS branch_id
                    FROM
                        students AS s
                        LEFT JOIN contracts AS c ON s.id = c.`student_id`
                        LEFT JOIN enrolments AS e ON c.id = e.`contract_id`
                        LEFT JOIN (
                            SELECT -1 AS `type`
                            UNION SELECT -2 AS `type`
                        ) AS t ON s.id <> t.type
                    WHERE
                        s.`created_at` <= '$date' AND s.created_at >= '$start_date'
                        AND s.`branch_id` IN ($branch_ids_string) AND c.`branch_id` = s.`branch_id`
                        AND c.`id` IN (
                            SELECT c.id
                            FROM
                            contracts AS c
                            LEFT JOIN enrolments AS e ON c.id = e.contract_id
                            LEFT JOIN (
                                SELECT student_id, MAX(count_recharge) AS count_recharge, MAX(end_date) AS end_date
                                FROM contracts AS c
                                WHERE c.`branch_id` IN ($branch_ids_string) GROUP BY c.`student_id`
                            ) AS t ON c.`student_id` = t.student_id AND c.`count_recharge` = t.count_recharge AND c.end_date = t.end_date
                            WHERE t.student_id IS NOT NULL
                        )
                        AND (e.id IS NULL OR (e.id IS NOT NULL AND e.`status` > 0))
                        AND
                        (
                            (c.`debt_amount` = 0 AND c.`type` > 0 AND t.type = -1)
                            OR
                            (c.type = 0 AND t.type = -2)
                        )
                        $product_condition
                    GROUP BY s.id
                ) AS t
                    LEFT JOIN branches AS b ON t.branch_id = b.`id`
                GROUP BY t.branch_id, t.student_type
            )
            UNION

            (
                SELECT
                    COUNT(*) AS count_student,
                    s.`branch_id` AS branch_id,
                    -3 AS student_type
                FROM
                    students AS s
                    LEFT JOIN contracts AS c ON s.`id` = c.`student_id`
                    LEFT JOIN (
                        SELECT MIN(created_at) AS created_at, contract_id FROM payment GROUP BY contract_id
                    ) AS p ON c.`id` = p.`contract_id`
                WHERE
                    s.`branch_id` IN ($branch_ids_string)
                    AND s.`created_at` <= '$date' AND s.created_at >= '$start_date'
                    AND s.`branch_id` = c.`branch_id`
                    AND s.checked=1
                    $product_condition
                GROUP BY s.`branch_id`
            )
            UNION

            (
                SELECT
                    COUNT(*) AS count_student,
                    t1.branch_id AS branch_id,
                    -4 AS student_type
                FROM
                (
                    SELECT
                        s.id AS id,
                        s.`branch_id` AS branch_id
                    FROM
                        students AS s
                        LEFT JOIN contracts AS c ON s.`id` = c.`student_id`
                        LEFT JOIN (
                            SELECT MIN(created_at) AS created_at, contract_id FROM payment GROUP BY contract_id
                        ) AS p ON c.`id` = p.`contract_id`
                    WHERE
                        s.`branch_id` IN ($branch_ids_string)
                        AND s.`created_at` <= '$date' AND s.created_at >= '$start_date'
                        AND s.`branch_id` = c.`branch_id`
                        AND c.`count_recharge` = 0
                        AND (p.created_at <= '$date' AND p.created_at >= '$start_date')
                        $product_condition
                    GROUP BY s.`id`
                ) AS t1
                LEFT JOIN
                (
                    SELECT
                        s.id AS id,
                        s.`branch_id` AS branch_id
                    FROM
                        students AS s
                        LEFT JOIN contracts AS c ON s.id = c.`student_id`
                    WHERE
                        s.`branch_id` IN ($branch_ids_string)
                        AND s.`created_at` <= '$date' AND s.created_at >= '$start_date'
                        AND s.`branch_id` = c.`branch_id`
                        AND c.`count_recharge` = -1
                        $product_condition
                    GROUP BY s.id
                ) AS t2 ON t1.id = t2.id AND t1.branch_id = t2.branch_id
                WHERE t2.id IS NOT NULL
                GROUP BY t1.branch_id
            )
        ";

        $res = DB::select(DB::raw($query));


//        $branches = Branch::where('id', 'IN', "($branch_ids_string)");
//        var_dump($branches);die;
        $query = "SELECT id, `name` FROM branches WHERE id IN ($branch_ids_string)";
        $branches = DB::select(DB::raw($query));
//        var_dump($branches);die;

        $data = [];
        if (!empty($branches)) {
            $rows = [];
            if (!empty($res)) {
                foreach ($res as $re) {
                    if (!isset($rows[$re->branch_id])) {
                        $rows[$re->branch_id] = [];
                    }
                    $rows[$re->branch_id]["type" . (0 - $re->student_type)] = $re->count_student;
                }
            }

            foreach ($branches as $b) {
                if (!isset($data[$b->id])) {
                    $data[$b->id] = [
                        "name" => $b->name,
                        "student_types" => []
                    ];
                }

                if (isset($rows[$b->id])) {
                    for ($i = 1; $i < 5; $i++) {
                        if (isset($rows[$b->id]["type$i"])) {
                            $data[$b->id]["student_types"]["type$i"] = $rows[$b->id]["type$i"];
                        } else {
                            $data[$b->id]["student_types"]["type$i"] = 0;
                        }
                    }
                } else {
                    $data[$b->id]['student_types'] = [
                        "type1" => 0,
                        "type2" => 0,
                        "type3" => 0,
                        "type4" => 0,
                    ];
                }

            }
        }

        $x = 0;
        for ($i = 0; $i < count($branches); $i++) {
            $x = $i + 6;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $branches[$i]->name);
            $sheet->setCellValue('C' . $x, '');
            $sheet->setCellValue('D' . $x, $data[$branches[$i]->id]["student_types"]["type1"]);
            $sheet->setCellValue('E' . $x, $data[$branches[$i]->id]["student_types"]["type2"]);
            $sheet->setCellValue('F' . $x, $data[$branches[$i]->id]["student_types"]["type4"]);
            $sheet->setCellValue('G' . $x, $data[$branches[$i]->id]["student_types"]["type3"]);
            $sheet->setCellValue('H' . $x, ada()->ratio($data[$branches[$i]->id]["student_types"]["type1"], $data[$branches[$i]->id]["student_types"]["type2"], 2));
            $sheet->setCellValue('I' . $x, ada()->ratio($data[$branches[$i]->id]["student_types"]["type1"], $data[$branches[$i]->id]["student_types"]["type4"], 2));
            $sheet->setCellValue('J' . $x, ada()->ratio($data[$branches[$i]->id]["student_types"]["type1"], $data[$branches[$i]->id]["student_types"]["type3"], 2));
        }

        ProcessExcel::styleCells($spreadsheet, "A6:A$x", "ffffff", "000", 9, 0, true, "right", "center", false);
        ProcessExcel::styleCells($spreadsheet, "B6:B$x", "ffffff", "000", 9, 0, true, "left", "center", false);
        ProcessExcel::styleCells($spreadsheet, "C6:J$x", "ffffff", "000", 9, 0, true, "right", "center", false);
        // return [$c, $branches];
        // return $branches;
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO HIỆN TRẠNG TRUNG TÂM.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC03($branch = null, $product = null, $program = null, $fromDate = null, $toDate = null)
    {
        $endMonth = date('Y-m-t');
        $default_start_date = (date('Y-m-01'));

        $from_date = strtotime($fromDate);
        $to_date = strtotime($toDate);

        if (!$from_date)
            $fromDate = $default_start_date;

        if (!$to_date)
            $toDate = $endMonth;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO TỔNG SỐ HỌC SINH MỚI HÀNG THÁNG');
        $sheet->mergeCells('A1:O1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);


        $sheet->setCellValue('A2', 'TỪ NGÀY : ' . $fromDate . ' - ' . 'ĐẾN NGÀY : ' . $toDate);
        $sheet->mergeCells('A2:O2');
        // $sheet->mergeCells('A3:O3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "MÃ LMS");
        $sheet->setCellValue("C5", "MÃ EFFECT");
        $sheet->setCellValue("D5", "HỌ TÊN HỌC SINH");
        $sheet->setCellValue("E5", "SẢN PHẨM");
        $sheet->setCellValue("F5", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("G5", "LỚP");
        $sheet->setCellValue("H5", "LOẠI KHÁCH HÀNG");
        $sheet->setCellValue("I5", "GÓI PHÍ");
        $sheet->setCellValue("J5", "NGÀY ĐÓNG PHÍ");
        $sheet->setCellValue("K5", "SỐ TIỀN ĐÃ ĐÓNG");
        $sheet->setCellValue("L5", "EC");
        $sheet->setCellValue("M5", "CM");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(15);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(15);
        $sheet->getColumnDimension("F")->setWidth(25);
        $sheet->getColumnDimension("G")->setWidth(24);
        $sheet->getColumnDimension("H")->setWidth(25);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(24);
        $sheet->getColumnDimension("K")->setWidth(24);
        $sheet->getColumnDimension("L")->setWidth(24);
        $sheet->getColumnDimension("M")->setWidth(24);

        $where = $where_branch = "";
        if ($branch != '_') {
            $where .= " AND st.branch_id in ($branch)";
            $where_branch = " WHERE br.id in ($branch)";
        }
        if ($product != '_') {
            $where .= " AND ct.product_id in ($product)";
        }
        if ($program != '_') {
            $where .= " AND ct.program_id in ($program)";
        }

        $q = "

              SELECT
                st.id AS s_id,
                b.name AS branch_name,
                st.name AS student_name,
                st.accounting_id,
                st.stu_id AS lms_id,
                if(st.type=1, 'VIP', 'Bình thường') AS customer_type,
                tf.name AS tuition_fee_name,
                ec.full_name AS ec_name,
                cm.full_name AS cm_name,
                pr.name as program_name,
                prd.name as product_name,
                p.created_at as payment_date,
                p.total as total_charged,
                cl.cls_name as class_name,
                st.branch_id as branch_id
              FROM
                students AS st
                LEFT JOIN contracts AS ct ON ct.student_id = st.id
                LEFT JOIN payment AS p ON p.contract_id = ct.id
                LEFT JOIN pendings AS pd ON pd.contract_id = ct.id
                LEFT JOIN branches as b on b.id = st.branch_id
                LEFT JOIN products AS prd ON prd.id = ct.product_id
                LEFT JOIN programs AS pr ON pr.id = ct.program_id
                LEFT JOIN tuition_fee as tf on tf.id = ct.tuition_fee_id
                LEFT JOIN users AS ec ON ec.id = ct.ec_id
                LEFT JOIN users AS cm ON cm.id = ct.cm_id
                LEFT JOIN classes as cl ON cl.program_id = pr.id
              WHERE
                st.type = 0
                AND ct.type IN ( 1, 2, 3, 4, 5 )
                AND ct.STATUS IN ( 3, 4, 5, 6 )
                AND pd.id IS NOT NULL
                AND ( p.created_at BETWEEN '$fromDate' AND '$toDate' ) $where
                GROUP by st.id
                UNION

              SELECT
                st.id AS s_id,
                b.name AS branch_name,
                st.name AS student_name,
                st.accounting_id,
                st.stu_id AS lms_id,
                if(st.type=1, 'VIP', 'Bình thường') AS customer_type,
                tf.name AS tuition_fee_name,
                ec.full_name AS ec_name,
                cm.full_name AS cm_name,
                pr.name as program_name,
                prd.name as product_name,
                p.created_at as payment_date,
                p.total as total_charged,
                cl.cls_name as class_name,
                st.branch_id as branch_id
              FROM
                students AS st
                LEFT JOIN contracts AS ct ON ct.student_id = st.id
                LEFT JOIN payment AS p ON p.contract_id = ct.id
                LEFT JOIN pendings AS pd ON pd.contract_id = ct.id
                LEFT JOIN branches as b on b.id = st.branch_id
                LEFT JOIN products AS prd ON prd.id = ct.product_id
                LEFT JOIN programs AS pr ON pr.id = ct.program_id
                LEFT JOIN tuition_fee as tf on tf.id = ct.tuition_fee_id
                LEFT JOIN users AS ec ON ec.id = ct.ec_id
                LEFT JOIN users AS cm ON cm.id = ct.cm_id
                LEFT JOIN classes as cl ON cl.program_id = pr.id
              WHERE
                st.type = 0
                AND ct.type IN ( 1, 2, 3, 4, 5 )
                AND ct.STATUS IN ( 3, 4, 5, 6 )
                AND pd.id IS NULL
                AND ( p.created_at BETWEEN '$fromDate' AND '$toDate' ) $where
                GROUP by st.id
                UNION
              SELECT
                st.id AS s_id,
                b.name AS branch_name,
                st.name AS student_name,
                st.accounting_id,
                st.stu_id AS lms_id,
                if(st.type=1, 'VIP', 'Bình thường') AS customer_type,
                tf.name AS tuition_fee_name,
                ec.full_name AS ec_name,
                cm.full_name AS cm_name,
                pr.name as program_name,
                prd.name as product_name,
                p.created_at as payment_date,
                p.total as total_charged,
                cl.cls_name as class_name,
                st.branch_id as branch_id
              FROM
                students AS st
                LEFT JOIN contracts AS ct ON ct.student_id = st.id
                LEFT JOIN payment AS p ON p.contract_id = ct.id
                LEFT JOIN pendings AS pd ON pd.contract_id = ct.id
                LEFT JOIN branches as b on b.id = st.branch_id
                LEFT JOIN products AS prd ON prd.id = ct.product_id
                LEFT JOIN programs AS pr ON pr.id = ct.program_id
                LEFT JOIN tuition_fee as tf on tf.id = ct.tuition_fee_id
                LEFT JOIN users AS ec ON ec.id = ct.ec_id
                LEFT JOIN users AS cm ON cm.id = ct.cm_id
                LEFT JOIN classes as cl ON cl.program_id = pr.id
              WHERE
                st.type = 1
                AND ( ct.start_date BETWEEN '$fromDate' AND '$toDate' )  $where
                GROUP by st.id

            ";
        $students = DB::select(DB::raw($q));
//        echo $q;die;
//        dd($students);
        // return $students;
        $list_branch = DB::select(DB::raw("SELECT br.* from branches as br $where_branch"));
        $p = 5;
        for ($j = 0; $j < count($list_branch); $j++) {
            $y = $p + 1;
            $p++;
            $st = 'A' . $y;
            $en = 'O' . $y;
            $sheet->setCellValue('A' . $y, $list_branch[$j]->name);
            ProcessExcel::styleCells($spreadsheet, "A$y:O$y", "FFFFFF", "black", 12, 1, 3, "left", "center", true);
            $sheet->mergeCells("$st:$en");
            for ($i = 0; $i < count($students); $i++) {

                if ($students[$i]->branch_id == $list_branch[$j]->id) {
                    $x = $p + 1;
                    $p++;
                    $sheet->setCellValue('A' . $x, $i + 1);
                    $sheet->setCellValue('B' . $x, $students[$i]->lms_id);
                    $sheet->setCellValue('C' . $x, $students[$i]->accounting_id);
                    $sheet->setCellValue('D' . $x, $students[$i]->student_name);
                    $sheet->setCellValue('E' . $x, $students[$i]->product_name);
                    $sheet->setCellValue('F' . $x, $students[$i]->program_name);
                    $sheet->setCellValue('G' . $x, $students[$i]->class_name);
                    $sheet->setCellValue('H' . $x, $students[$i]->customer_type);
                    $sheet->setCellValue('I' . $x, $students[$i]->tuition_fee_name);
                    $sheet->setCellValue('J' . $x, $students[$i]->payment_date);
                    $sheet->setCellValue('K' . $x, $students[$i]->total_charged);
                    $sheet->setCellValue('L' . $x, $students[$i]->ec_name);
                    $sheet->setCellValue('M' . $x, ($students[$i]->cm_name));


                    $st = "A" . $x;
                    $ed = "O" . $x;
                    ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);

                    ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "C$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "D$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "N$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "O$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                }
            }
        }

        // return $students;
        // return $where;

        ProcessExcel::styleCells($spreadsheet, "A5:O5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:O1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:O2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:O3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:O4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Học Sinh Mới Hàng Tháng.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC04($branch = null, $fromDate = null, $toDate = null)
    {

        $branch = $branch ? $branch : null;
        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));

        $from_date = strtotime($fromDate);
        if (!$from_date) {
            $fromDate = $today;
        }

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO HIỆU SUẤT GIÁO VIÊN');
        $sheet->mergeCells('A1:G1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $sheet->setCellValue('A2', 'NGÀY: ' . $fromDate);
        $sheet->mergeCells('A2:G2');

        // $sheet->setCellValue('A3', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "TRUNG TÂM");
        $sheet->setCellValue("C5", "TỔNG SỐ GIÁO VIÊN");
        $sheet->setCellValue("D5", "TỔNG SỐ LỚP");
        $sheet->setCellValue("E5", "TỔNG SỐ HỌC SINH FULL PHÍ");
        $sheet->setCellValue("F5", "SỐ LỚP/GIÁO VIÊN");
        $sheet->setCellValue("G5", "HỌC SINH FULL/GIÁO VIÊN");


        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(25);
        $sheet->getColumnDimension("D")->setWidth(25);
        $sheet->getColumnDimension("E")->setWidth(25);
        $sheet->getColumnDimension("F")->setWidth(25);
        $sheet->getColumnDimension("G")->setWidth(25);


        // return [$branch, $fromDate, $toDate];

        $branch_id = '';
        if ($branch != '_') {
            $branch_id .= $branch;
            $branch_id = rtrim($branch_id, ",");

        }
        $where = '';

        if ($branch_id) {
            $where .= "Where branches.id in ($branch_id)";
        }
        // return $c;
        $query = "SELECT `name` branch_name,

        (SELECT COUNT(id)
         FROM classes
         WHERE classes.brch_id = branches.brch_id
           AND cls_startdate <= ' $fromDate'
           AND cls_enddate >= ' $fromDate'
           AND cls_iscancelled = 'no' ) AS total_classes,

        (SELECT COUNT(u.id)
         FROM term_user_branch t
         LEFT JOIN users u ON t.user_id = u.id
         WHERE t.role_id = 36
           AND u.STATUS > 0
           AND u.start_date <= '$fromDate'
           AND (u.end_date >= '$fromDate'
                OR u.end_date IS NULL)
           AND t.branch_id = branches.id ) AS total_teachers,

        (SELECT COUNT(DISTINCT (c.student_id))
         FROM contracts c
         LEFT JOIN enrolments e ON c.enrolment_id = e.id
         LEFT JOIN pendings p ON p.contract_id = c.id
         WHERE c.debt_amount = 0
           AND c.type > 0
           AND c.branch_id = branches.id
           AND ( (DATE(c.updated_at) <= '$fromDate'
                  AND DATE(c.end_date) >= '$fromDate'
                  AND (c.type <> 5
                       OR c.status = 6))
                OR (DATE(c.start_date) <= '$fromDate'
                    AND c.type = 5)
                OR (DATE(c.created_at) <= '$fromDate'
                    AND c.status = 6))
           AND c.total_charged > 0
           AND c.debt_amount = 0
           AND IF (c.enrolment_id,
                   e.status > 0,
                   TRUE) ) AS total_full_fee
      FROM branches $where";
        //echo $query;exit();
        $branches = DB::select(DB::raw($query));

        for ($i = 0; $i < count($branches); $i++) {
            $x = $i + 6;
            $b = 0;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $branches[$i]->branch_name);
            $sheet->setCellValue('C' . $x, $branches[$i]->total_teachers);
            $sheet->setCellValue('D' . $x, $branches[$i]->total_classes);
            $sheet->setCellValue('E' . $x, $branches[$i]->total_full_fee);

            if ($branches[$i]->total_classes > 0 && $branches[$i]->total_teachers > 0) {
                $sheet->setCellValue('F' . $x, round($branches[$i]->total_classes / $branches[$i]->total_teachers, 2));
            } else {
                $sheet->setCellValue('F' . $x, $b);
            }
            if ($branches[$i]->total_full_fee > 0 && $branches[$i]->total_teachers > 0) {
                $sheet->setCellValue('G' . $x, round($branches[$i]->total_full_fee / $branches[$i]->total_teachers, 2));
            } else {
                $sheet->setCellValue('G' . $x, $b);
            }


            $st = "A" . $x;
            $ed = "G" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
        }

        ProcessExcel::styleCells($spreadsheet, "A5:G5", "ffffff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:G1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:G2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:G3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:G4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        // return [$c, $branches];
        // return $branches;
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO HIỆU SUẤT GIÁO VIÊN.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC05($branch = null, $product = null, $program = null)
    {

        $branch = $branch ? $branch : null;
        $product = $product ? $product : null;
        $program = $program ? $program : null;
        $fromDate = '2018-04-01';
        $toDate = date("Y-m-d");


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO HIỆU SUẤT PHÒNG HỌC');
        $sheet->mergeCells('A1:O1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);


        $sheet->setCellValue('A2', 'TỪ NGÀY : ' . $fromDate . ' - ' . 'ĐẾN NGÀY : ' . $toDate);
        $sheet->mergeCells('A2:O2');
        // $sheet->mergeCells('A3:O3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "MÃ LMS");
        $sheet->setCellValue("C5", "MÃ EFFECT");
        $sheet->setCellValue("D5", "HỌ TÊN HỌC SINH");
        $sheet->setCellValue("E5", "SẢN PHẨM");
        $sheet->setCellValue("F5", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("G5", "LỚP");
        $sheet->setCellValue("H5", "LOẠI KHÁCH HÀNG");
        $sheet->setCellValue("I5", "GÓI PHÍ");
        $sheet->setCellValue("J5", "NGÀY ĐÓNG PHÍ");
        $sheet->setCellValue("K5", "SỐ TIỀN PHẢI ĐÓNG");
        $sheet->setCellValue("L5", "ĐÃ ĐÓNG");
        $sheet->setCellValue("M5", "CÔNG NỢ");
        $sheet->setCellValue("N5", "EC");
        $sheet->setCellValue("O5", "CM");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(15);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(15);
        $sheet->getColumnDimension("F")->setWidth(25);
        $sheet->getColumnDimension("G")->setWidth(24);
        $sheet->getColumnDimension("H")->setWidth(25);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(24);
        $sheet->getColumnDimension("K")->setWidth(24);
        $sheet->getColumnDimension("L")->setWidth(24);
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(35);
        $sheet->getColumnDimension("O")->setWidth(35);


        $branch_id = '';
        if ($branch != '_') {
            $branch_id .= '(' . $branch . ')';
        }
        $list_branch = DB::select(DB::raw("SELECT id,name from branches where id in $branch_id"));
        // return $list_branch;
        $product_id = '';
        if ($product != '_') {
            $product_id .= '(' . $product . ')';
        }

        $program_id = '';
        if ($program != '_') {
            $program_id .= '(' . $program . ')';
        }


        $where = '';

        if ($branch_id) {
            $where .= "and ct.branch_id in $branch_id ";
        }
        if ($program_id) {
            $where .= "and ct.program_id in $program_id ";
        }
        if ($product_id) {
            $where .= "and ct.product_id in $product_id ";
        }
        // $where .= " and count(ct.*) as payment_time <= 1";

        // if ($from_date){
        //     $where .= "and pm.created_at > '$from_date'";
        // }
        // if ($to_date){
        //     $where .= "and pm.created_at < '$to_date'";
        // }
        // $where .= "and count(pm.contract_id) = 1";

        $c = '';
        if ($where) {
            $where = substr($where, 3);
            $c .= 'where ' . $where;
        }
        $students = DB::select(DB::raw("SELECT
                                                st.name as student_name,
                                                st.stu_id as stu_id,
                                                st.accounting_id as accounting_id,
                                                IF(st.type =1, 'VIP', 'Thường') AS student_type,
                                                cl.cls_name AS class_name,
                                                cl.id as class_id,
                                                ct.cm_id AS cm_id,
                                                ct.must_charge,
                                                CONCAT(us.full_name, ' - ', us.username) AS ec_name,
                                                CONCAT(cm.full_name, ' - ', us.username) AS cm_name,
                                                ct.ec_id AS ec_id,
                                                pr.name AS product_name,
                                                pg.name AS program_name,
                                                pm.amount AS amount,
                                                pm.debt AS debt,
                                                br.id as branch_id,
                                                date_format(pm.created_at, '%Y-%m-%d') as payment_date,
                                                tf.name as tuition_price
                                            FROM students AS st
                                            LEFT JOIN contracts AS ct ON ct.student_id = st.id
                                            LEFT JOIN branches AS br ON br.id = ct.branch_id
                                            LEFT JOIN enrolments AS el ON (el.contract_id = ct.id AND el.student_id = st.id)
                                            LEFT JOIN classes AS cl ON cl.brch_id = br.brch_id
                                            LEFT JOIN products AS pr ON ct.product_id = pr.id
                                            LEFT JOIN programs AS pg ON ct.program_id = pg.id
                                            LEFT JOIN payment AS pm ON ct.id = pm.contract_id
                                            LEFT JOIN tuition_fee AS tf ON tf.product_id = ct.id
                                            LEFT JOIN users AS cm ON cm.id = ct.cm_id
                                            LEFT JOIN users AS us ON us.id = ct.ec_id $c GROUP BY st.id"));
        // return $students;
        $p = 5;
        for ($j = 0; $j < count($list_branch); $j++) {
            $y = $p + 1;
            $p++;
            $st = 'A' . $y;
            $en = 'O' . $y;
            $sheet->setCellValue('A' . $y, $list_branch[$j]->name);
            ProcessExcel::styleCells($spreadsheet, "A$y:O$y", "FFFFFF", "black", 12, 1, 3, "left", "center", true);
            $sheet->mergeCells("$st:$en");
            for ($i = 0; $i < count($students); $i++) {

                if ($students[$i]->branch_id == $list_branch[$j]->id) {
                    $x = $p + 1;
                    $p++;
                    $sheet->setCellValue('A' . $x, $i + 1);
                    $sheet->setCellValue('B' . $x, $students[$i]->stu_id);
                    $sheet->setCellValue('C' . $x, $students[$i]->accounting_id);
                    $sheet->setCellValue('D' . $x, $students[$i]->student_name);
                    $sheet->setCellValue('E' . $x, $students[$i]->product_name);
                    $sheet->setCellValue('F' . $x, $students[$i]->program_name);
                    $sheet->setCellValue('G' . $x, $students[$i]->class_name);
                    $sheet->setCellValue('H' . $x, $students[$i]->student_type);
                    $sheet->setCellValue('I' . $x, $students[$i]->tuition_price);
                    $sheet->setCellValue('J' . $x, $students[$i]->payment_date);
                    $sheet->setCellValue('K' . $x, $students[$i]->must_charge);
                    $sheet->setCellValue('L' . $x, $students[$i]->amount);
                    $sheet->setCellValue('M' . $x, ($students[$i]->must_charge - $students[$i]->amount));
                    $sheet->setCellValue('N' . $x, $students[$i]->ec_name);
                    $sheet->setCellValue('O' . $x, $students[$i]->cm_name);

                    $st = "A" . $x;
                    $ed = "O" . $x;
                    ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);

                    ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "C$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "D$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "N$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "O$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                }
            }
        }

        // return $students;
        // return $where;

        ProcessExcel::styleCells($spreadsheet, "A5:O5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:O1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:O2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:O3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:O4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO HIỆU SUẤT PHÒNG HỌC.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function report_total_student(Request $request, $data)
    {
        $da = json_decode($data);
        $from_date = $da->from_date;
        $to_date = $da->to_date;


        if (!$from_date) {
            $from_date = date('Y-m-01 00:00:00');
        } else {
            $from_date = date('Y-m-d', strtotime($from_date)) . " 00:00:00";
        }

        if (!$to_date) {
            $to_date = date('Y-m-d 23:59:59');
            $pending_date = date('Y-m-d 00:00:00');
        } else {
            $to_date = date('Y-m-d', strtotime($to_date)) . " 23:59:59";
            $pending_date = date('Y-m-d', strtotime($to_date)) . " 00:00:00";
        }

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN ANH NGỮ APAX');
        $sheet->mergeCells('A1:I1');
        ProcessExcel::styleCells($spreadsheet, "A1:I1", "ffffff", "000", 9, 1, true, "left", "center", false);

        $sheet->setCellValue('A2', 'Tầng 7, Tòa nhà 14 Láng Hạ, Ba Đình, Hà Nội');
        $sheet->mergeCells('A2:I2');
        ProcessExcel::styleCells($spreadsheet, "A2:I2", "ffffff", "000", 9, 1, true, "left", "center", false);

        $sheet->setCellValue('A3', 'BÁO CÁO HIỆN TRẠNG TRUNG TÂM');
        $sheet->mergeCells('A3:I3');
        ProcessExcel::styleCells($spreadsheet, "A3:Q3", "ffffff", "000", 16, 1, true, "center", "center", false);

        $sheet->setCellValue('A4', "Ngày: " . date('Y-m-d', strtotime($to_date)));
        $sheet->mergeCells('A4:I4');
        ProcessExcel::styleCells($spreadsheet, "A4:Q4", "ffffff", "000", 9, 0, true, "center", "center", false);

        $sheet->getRowDimension('1')->setRowHeight(18);
        $sheet->getRowDimension('2')->setRowHeight(18);
        $sheet->getRowDimension('3')->setRowHeight(25);
        $sheet->getRowDimension('4')->setRowHeight(18);
        $sheet->getRowDimension('5')->setRowHeight(18);
        $sheet->getRowDimension('6')->setRowHeight(30);


        $sheet->setCellValue("A6", "STT");
        $sheet->setCellValue("B6", "MÃ EFFECT");
        $sheet->setCellValue("C6", "TÊN HỌC SINH");
        $sheet->setCellValue("D6", "TRUNG TÂM");
        $sheet->setCellValue("E6", "SẢN PHẨM");
        $sheet->setCellValue("F6", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("G6", "LOẠI KHÁCH HÀNG");
        $sheet->setCellValue("H6", "LỚP");
        $sheet->setCellValue("I6", "SỐ BUỔI CÒN LẠI");
        $sheet->setCellValue("J6", "NGÀY HỌC CUỐI");
        $sheet->setCellValue("K6", "EC");
        $sheet->setCellValue("L6", "CM");
        ProcessExcel::styleCells($spreadsheet, "A6:M6", "add8e6", "000", 9, 1, true, "center", "center", true);


        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(15);
        $sheet->getColumnDimension("C")->setWidth(25);
        $sheet->getColumnDimension("D")->setWidth(25);
        $sheet->getColumnDimension("E")->setWidth(25);
        $sheet->getColumnDimension("F")->setWidth(25);
        $sheet->getColumnDimension("G")->setWidth(25);
        $sheet->getColumnDimension("H")->setWidth(25);
        $sheet->getColumnDimension("I")->setWidth(25);
        $sheet->getColumnDimension("J")->setWidth(25);
        $sheet->getColumnDimension("K")->setWidth(25);
        $sheet->getColumnDimension("L")->setWidth(25);


        $branch_ids = $da->branch_ids;
        $student_type_ids = $da->customer_type_ids;
        $product_ids = $da->product_ids;

        if (empty($branch_ids)) {
            $token = $da->tk;
            $response = (object)[];
            $response->message = 'invalid session';
            $response->status = 200;
            $request->authorized = null;
            $check = false;
            $sinformation = (object)[];
            if ($token !== null) {
                $session = Redis::get($token);
                if ($session) {
                    $sinformation = json_decode($session);
                    $request->authorized = $session;
                    $request->users_data = $sinformation;
                    $check = true;
                }
            }
            if (!$check) {
                $response = [
                    'code' => 403,
                    'message' => 'Permission denied...',
                    'data' => null
                ];
                exit(json_encode($response));
            }
            $branch_ids = u::getBranchIds($request->users_data);
        }

        $where = "";

        if ($branch_ids) {
            $branch_ids_string = implode(',', $branch_ids);
            $where .= " AND t.branch_id IN( $branch_ids_string ) ";
        }
        if ($product_ids) {
            $product_ids_string = implode(',', $product_ids);
            $where .= " AND t.product_id IN ( $product_ids_string ) ";
        }

        if ($student_type_ids) {
            $student_type_ids_string = implode(',', $student_type_ids);
            $where .= " AND t.student_type in ( $student_type_ids_string ) ";
        }

        $q = "
            SELECT
                    s.`id` AS student_id,
                    c.id AS contract_id,
                    (0 - t.type) AS student_type,
                    t.name AS student_type_name,
                    c.product_id AS product_id,
                    s.created_at as created_at,
                    s.branch_id as branch_id,
                    c.program_id as program_id,
                    c.enrolment_id AS enrolment_id,
                    s.name AS student_name,
                    s.stu_id AS lms_id,
                    s.accounting_id AS accounting_id
                FROM
                    students AS s
                    LEFT JOIN contracts AS c ON c.`student_id` = s.`id`
                    LEFT JOIN enrolments AS e ON c.`enrolment_id` = e.`id`
                    LEFT JOIN pendings AS p ON c.`id` = p.`contract_id`
                    LEFT JOIN (
                        SELECT -1 AS `type`, 'Chính thức' AS `name`
                        UNION SELECT -2 AS `type`, 'học trải nghiệm' AS `name`
                        UNION SELECT -3 AS `type`, 'Withdraw' AS `name`
                        UNION SELECT -4 AS `type`, 'Pending' AS `name`
                        UNION SELECT -5 AS `type`, 'Đặt cọc' AS `name`
                        UNION SELECT -6 AS `type`, 'Tiềm năng' AS `name`
                    ) AS t ON s.id <> t.type
                WHERE
                    s.`branch_id` IN ($branch_ids_string)
                    AND (
                        (c.id IS NULL AND t.type = -6)
                        OR
                        (
                            c.id IS NOT NULL AND (c.`branch_id` = s.`branch_id`) AND (
                                (p.id IS NOT NULL AND p.status = 1 AND p.`end_date` >= '$pending_date' AND t.type = -4)
                                OR
                                (
                                    (p.id IS NULL OR p.status = 0 OR p.end_date < '$pending_date')
                                    AND
                                    (
                                        c.`id` IN (
                                            SELECT c.id
                                            FROM
                                            contracts AS c
                                            LEFT JOIN enrolments AS e ON c.id = e.contract_id
                                            LEFT JOIN (
                                                SELECT student_id, MAX(count_recharge) AS count_recharge, MAX(end_date) AS end_date
                                                FROM contracts AS c
                                                WHERE c.`branch_id` IN ($branch_ids_string) AND (c.`type` = 0 OR (c.`type` > 0 AND c.`total_charged` > 0)) GROUP BY c.`student_id`
                                            ) AS t ON c.`student_id` = t.student_id AND c.`count_recharge` = t.count_recharge AND c.end_date = t.end_date
                                            WHERE t.student_id IS NOT NULL
                                        )
                                        AND
                                        (
                                            (e.`status` = 0 AND t.type = -3)
                                            OR
                                            (e.status > 0 AND c.`type` = 0 AND t.type = -2)
                                            OR
                                            (
                                                (e.`id` IS NULL OR (e.`id` IS NOT NULL AND e.`status` > 0)) AND c.`real_sessions` > 0 AND c.type > 0
                                                AND
                                                (
                                                    (c.`debt_amount` > 0 AND t.type = -5)
                                                    OR
                                                    (c.`debt_amount` = 0 AND t.type = -1)
                                                )
                                            )

                                        )
                                    )
                                )
                            )
                        )
                    )
                GROUP BY s.id
        ";

        $query = "
            SELECT
                t.lms_id AS lms_id,
                t.accounting_id AS accounting_id,
                t.student_name AS student_name,
                b.name AS branch_name,
                p1.name AS product_name,
                p2.name AS program_name,
                t.student_type AS student_type,
                cls.cls_name AS class_name,
                u1.username AS ec_name,
                u2.username AS cm_name,
                t.student_type_name AS student_type_name
            FROM
                ($q)  AS t
                LEFT JOIN branches AS b ON t.branch_id = b.id
                LEFT JOIN products AS p1 ON t.product_id = p1.id
                LEFT JOIN programs AS p2 ON t.program_id = p2.id
                LEFT JOIN enrolments AS e ON t.enrolment_id = e.id
                LEFT JOIN classes AS cls ON e.class_id = cls.id
                LEFT JOIN term_student_user AS tsf ON tsf.student_id = t.student_id
                LEFT JOIN users AS u1 ON tsf.ec_id = u1.id
                LEFT JOIN users AS u2 ON tsf.cm_id = u2.id
            WHERE
            (t.created_at BETWEEN '$from_date' AND '$to_date') $where ";

        $res = DB::select(DB::raw($query));

        $student_types = [
            1 => [
                "total" => 0,
                "name" => "Chính thức",
            ],
            2 => [
                "total" => 0,
                "name" => "học trải nghiệm",
            ],
            3 => [
                "total" => 0,
                "name" => "Withdraw",
            ],
            4 => [
                "total" => 0,
                "name" => "Pending",
            ],
            5 => [
                "total" => 0,
                "name" => "Đặt cọc",
            ],
            6 => [
                "total" => 0,
                "name" => "Tiềm năng",
            ],
        ];

        $x = 0;
        foreach ($res as $i => $re) {
            $x = $i + 7;
            $sheet->setCellValue("A$x", $i + 1);
            $sheet->setCellValue("B$x", $re->accounting_id);
            $sheet->setCellValue("C$x", $re->student_name);
            $sheet->setCellValue("D$x", $re->branch_name);
            $sheet->setCellValue("E$x", $re->product_name);
            $sheet->setCellValue("F$x", $re->program_name);
            $sheet->setCellValue("G$x", $student_types[$re->student_type]['name']);

            $student_types[$re->student_type]['total'] += 1;

            $sheet->setCellValue("H$x", $re->class_name);
            $sheet->setCellValue("I$x", '');
            $sheet->setCellValue("J$x", '');
            $sheet->setCellValue("K$x", $re->ec_name);
            $sheet->setCellValue("L$x", $re->cm_name);
        }

        ProcessExcel::styleCells($spreadsheet, "A7:A$x", "ffffff", "000", 9, 0, true, "right", "center", false);
//        ProcessExcel::styleCells($spreadsheet, "B7:I$x", "ffffff", "000", 9, 0, true, "left", "center", false);
        ProcessExcel::styleCells($spreadsheet, "J7:J$x", "ffffff", "000", 9, 0, true, "right", "center", false);
        ProcessExcel::styleCells($spreadsheet, "K7:M$x", "ffffff", "000", 9, 0, true, "left", "center", false);

        $sheet->setCellValue('E5', "Chính thức: " . $student_types[1]['total']);
        $sheet->setCellValue('F5', "học trải nghiệm: " . $student_types[2]['total']);
        $sheet->setCellValue('G5', "Withdraw: " . $student_types[3]['total']);
        $sheet->setCellValue('H5', "Pending: " . $student_types[4]['total']);
        $sheet->setCellValue('I5', "Đặt cọc: " . $student_types[5]['total']);
        $sheet->setCellValue('J5', "Tiềm năng: " . $student_types[6]['total']);
        ProcessExcel::styleCells($spreadsheet, "E5:J5", "ffffff", "000", 9, 1, true, "left", "center", false);

        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO TỔNG SỐ HỌC SINH.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC07($branch = null, $products = null, $programs = null, $fromDate = null, $toDate = null)
    {
        // dd($toDate);

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));

        $from_date = strtotime($fromDate);
        $to_Date = strtotime($toDate);

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));

        if (!$from_date) {
            $fromDate = $default_start_date;
        }
        if (!$to_Date) {
            $toDate = $today;
        }

        $where = $where_product = "";
        if ($branch != '_')
            $where .= " AND b.id in( $branch ) ";

        if ($products != '_') {
            $where .= " AND pr.id in ( $products ) ";
        }

        if ($programs != '_')
            $where .= " AND pg.id in ( $programs ) ";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO PHÂN LOẠI HỌC SINH');
        $sheet->mergeCells('A1:O1');
        $sheet->getRowDimension('1')->setRowHeight(60);
        $sheet->getRowDimension('2')->setRowHeight(20);
        $sheet->getRowDimension('3')->setRowHeight(20);
        $sheet->getRowDimension('4')->setRowHeight(40);

        $sheet->setCellValue('A2', "TỪ NGÀY $fromDate  ĐẾN NGÀY:$toDate");
        $sheet->mergeCells('A2:O2');
        $sheet->mergeCells('A3:O3');
        $sheet->setCellValue("A4", "STT");
        $sheet->setCellValue("B4", "MÃ LMS");
        $sheet->setCellValue("C4", "MÃ EFFECT");
        $sheet->setCellValue("D4", "HỌ TÊN HỌC SINH");
        $sheet->setCellValue("E4", "TRUNG TÂM");
        $sheet->setCellValue("F4", "SẢN PHÂM");
        $sheet->setCellValue("G4", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("H4", "LƠP");
        $sheet->setCellValue("I4", "LỌAI ĐÁNH GIÁ");
        $sheet->setCellValue("J4", "GIÁO VIÊN");
        $sheet->setCellValue("K4", "NGƯƠÌ ĐÁNH GIÁ");
        $sheet->setCellValue("L4", "NGÀY ĐÁNH GIÁ");
        $sheet->setCellValue("M4", "NÔI DUNG");
        $sheet->setCellValue("N4", "EC");
        $sheet->setCellValue("O4", "EM");


        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:C5');
        $sheet->mergeCells('D4:D5');
        $sheet->mergeCells('E4:E5');
        $sheet->mergeCells('F4:F5');
        $sheet->mergeCells('G4:G5');
        $sheet->mergeCells('H4:H5');
        $sheet->mergeCells('I4:I5');
        $sheet->mergeCells('J4:J5');
        $sheet->mergeCells('K4:K5');
        $sheet->mergeCells('L4:L5');
        $sheet->mergeCells('M4:M5');
        $sheet->mergeCells('N4:N5');
        $sheet->mergeCells('O4:O5');

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(20);

        ProcessExcel::styleCells($spreadsheet, "A1:O1", "black", "black", 16, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A2:O2", "black", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A3:O3", "ffffff", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A4:O4", "ffffff", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A5:O5", "ffffff", "black", 11, 1, true, "center", "center", true);

        $sql = "SELECT s.id AS student_id,
        s.stu_id AS lms_code,
        s.accounting_id AS effect_code,
        s.name AS name_student,
        t.rating_date,
        r.name AS rank_name,
        pr.name AS product_name,
        pg.name AS program_name,
        b.name AS branch_name,
        cls.cls_name AS class_name,
        t.comment AS comment,
        u.username AS creator_name,
        u1.username AS ec_name,
        u2.username AS cm_name,
        te.ins_name AS teacher_name
      FROM term_student_rank AS t
      LEFT JOIN classes AS cls ON t.class_id = cls.id
      LEFT JOIN students AS s ON t.student_id = s.id
      LEFT JOIN term_student_user AS t1 ON t1.student_id = s.id
      LEFT JOIN users AS u1 ON t1.ec_id = u1.id
      LEFT JOIN users AS u2 ON t1.cm_id = u2.id
      LEFT JOIN ranks AS r ON t.rank_id = r.id
      LEFT JOIN users AS u ON u.id = t.creator_id
      LEFT JOIN contracts AS c ON c.student_id = s.id
      LEFT JOIN products AS pr ON pr.id = c.product_id
      LEFT JOIN programs AS pg ON pg.id = c.program_id
      LEFT JOIN sessions AS se ON se.class_id = t.class_id
      LEFT JOIN teachers AS te ON se.teacher_id = te.id
      LEFT JOIN branches AS b ON b.id = c.branch_id
      LEFT JOIN term_user_class AS term_class ON term_class.class_id = t.class_id
      LEFT JOIN term_user_branch AS term_branch ON term_branch.user_id = term_class.user_id
      LEFT JOIN users AS user_teacher ON user_teacher.id = term_branch.user_id
      WHERE s.id > 0
        AND (DATE(t.rating_date) >= '$fromDate' AND DATE(t.rating_date) <= '$toDate')
        $where
      GROUP BY s.id";
        // echo $sql;die;
        $result = DB::select(DB::raw($sql));
        $p = 5;
        $p++;
        $stt = 1;
        for ($j = 0; $j < count($result); $j++) {
            if ($result) {
                $y = $p + 1;
                $st = 'A' . $y;
                $en = 'O' . $y;
                $sheet->setCellValue('A' . $y, $stt);
                $sheet->setCellValue('B' . $y, $result[$j]->lms_code);
                $sheet->setCellValue('C' . $y, $result[$j]->effect_code);
                $sheet->setCellValue('D' . $y, $result[$j]->name_student);
                $sheet->setCellValue('E' . $y, $result[$j]->branch_name);
                $sheet->setCellValue('F' . $y, $result[$j]->product_name);
                $sheet->setCellValue('G' . $y, $result[$j]->program_name);
                $sheet->setCellValue('H' . $y, $result[$j]->class_name);
                $sheet->setCellValue('I' . $y, $result[$j]->rank_name);
                $sheet->setCellValue('J' . $y, $result[$j]->teacher_name);
                $sheet->setCellValue('K' . $y, $result[$j]->creator_name);
                $sheet->setCellValue('L' . $y, $result[$j]->rating_date);
                $sheet->setCellValue('M' . $y, $result[$j]->comment);
                $sheet->setCellValue('N' . $y, $result[$j]->ec_name);
                $sheet->setCellValue('O' . $y, $result[$j]->cm_name);
                $p++;
                $stt++;
                ProcessExcel::styleCells($spreadsheet, "$st:$en", "ffffff", "black", 11, '', true, "center", "center", true);
            }
        }


        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO PHÂN LOẠI HỌC SINH.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC08($branch = null, $product = null, $program = null, $fromDate = null, $toDate = null)
    {

        $branches = $branch;
        $products = $product;
        $programs = $program;

        $fromDate = strtotime($fromDate);
        $toDate = strtotime($toDate);

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = $today;
        }


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO PHÂN LOẠI CM');
        $sheet->mergeCells('A1:O1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);


        $sheet->setCellValue('A2', 'TỪ NGÀY : ' . $from_date . ' - ' . 'ĐẾN NGÀY : ' . $to_date);
        $sheet->mergeCells('A2:O2');
        // $sheet->mergeCells('A3:O3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "MÃ LMS");
        $sheet->setCellValue("C5", "MÃ EFFECT");
        $sheet->setCellValue("D5", "HỌ TÊN HỌC SINH");
        $sheet->setCellValue("E5", "SẢN PHẨM");
        $sheet->setCellValue("F5", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("G5", "LỚP");
        $sheet->setCellValue("H5", "LOẠI KHÁCH HÀNG");
        $sheet->setCellValue("I5", "GÓI PHÍ");
        $sheet->setCellValue("J5", "NGÀY ĐÓNG PHÍ");
        $sheet->setCellValue("K5", "SỐ TIỀN PHẢI ĐÓNG");
        $sheet->setCellValue("L5", "ĐÃ ĐÓNG");
        $sheet->setCellValue("M5", "CÔNG NỢ");
        $sheet->setCellValue("N5", "EC");
        $sheet->setCellValue("O5", "CM");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(15);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(15);
        $sheet->getColumnDimension("F")->setWidth(25);
        $sheet->getColumnDimension("G")->setWidth(24);
        $sheet->getColumnDimension("H")->setWidth(25);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(24);
        $sheet->getColumnDimension("K")->setWidth(24);
        $sheet->getColumnDimension("L")->setWidth(24);
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(35);
        $sheet->getColumnDimension("O")->setWidth(35);


        $where = "";

        if ($branches && $branches != '_') {
            $where .= "AND ct.branch_id in $branches ";
        }
        if ($programs && $programs != '_') {
            $where .= "AND ct.program_id in $programs ";
        }
        if ($products && $products != '_') {
            $where .= "AND ct.product_id in $products ";
        }
        $where .= " AND (ct.created_at BETWEEN $from_date AND $to_date)";
        // dd($where);
        // if ($from_date){
        //     $where .= "and pm.created_at > '$from_date'";
        // }
        // if ($to_date){
        //     $where .= "and pm.created_at < '$to_date'";
        // }
        // $where .= "and count(pm.contract_id) = 1";

        // $c = '';
        // if ($where){
        //     $where = substr($where, 3);
        //     $c .= 'where '.$where;
        // }
        $where_branch = null;
        if ($branches && $branches != '_') {
            $where_branch .= "WHERE br.id IN $branches";
        }

        $students = DB::select(DB::raw("SELECT
                                                st.name as student_name,
                                                st.stu_id as stu_id,
                                                st.accounting_id as accounting_id,
                                                IF(st.type =1, 'VIP', 'Thường') AS student_type,
                                                cl.cls_name AS class_name,
                                                cl.id as class_id,
                                                ct.cm_id AS cm_id,
                                                ct.must_charge,
                                                CONCAT(us.full_name, ' - ', us.username) AS ec_name,
                                                CONCAT(cm.full_name, ' - ', us.username) AS cm_name,
                                                ct.ec_id AS ec_id,
                                                pr.name AS product_name,
                                                pg.name AS program_name,
                                                pm.amount AS amount,
                                                pm.debt AS debt,
                                                br.id as branch_id,
                                                date_format(pm.created_at, '%Y-%m-%d') as payment_date,
                                                tf.name as tuition_price
                                            FROM students AS st
                                            LEFT JOIN contracts AS ct ON ct.student_id = st.id
                                            LEFT JOIN branches AS br ON br.id = ct.branch_id
                                            LEFT JOIN enrolments AS el ON (el.contract_id = ct.id AND el.student_id = st.id)
                                            LEFT JOIN classes AS cl ON cl.brch_id = br.brch_id
                                            LEFT JOIN products AS pr ON ct.product_id = pr.id
                                            LEFT JOIN programs AS pg ON ct.program_id = pg.id
                                            LEFT JOIN payment AS pm ON ct.id = pm.contract_id
                                            LEFT JOIN tuition_fee AS tf ON tf.product_id = ct.id
                                            LEFT JOIN users AS cm ON cm.id = ct.cm_id
                                            LEFT JOIN users AS us ON us.id = ct.ec_id WHERE st.id > 0 $where GROUP BY st.id"));
        // return $students;
        $list_branch = DB::select(DB::raw("SELECT br.* from branches as br $where_branch"));
        $p = 5;
        for ($j = 0; $j < count($list_branch); $j++) {
            $y = $p + 1;
            $p++;
            $st = 'A' . $y;
            $en = 'O' . $y;
            $sheet->setCellValue('A' . $y, $list_branch[$j]->name);
            ProcessExcel::styleCells($spreadsheet, "A$y:O$y", "FFFFFF", "black", 12, 1, 3, "left", "center", true);
            $sheet->mergeCells("$st:$en");
            for ($i = 0; $i < count($students); $i++) {

                if ($students[$i]->branch_id == $list_branch[$j]->id) {
                    $x = $p + 1;
                    $p++;
                    $sheet->setCellValue('A' . $x, $i + 1);
                    $sheet->setCellValue('B' . $x, $students[$i]->stu_id);
                    $sheet->setCellValue('C' . $x, $students[$i]->accounting_id);
                    $sheet->setCellValue('D' . $x, $students[$i]->student_name);
                    $sheet->setCellValue('E' . $x, $students[$i]->product_name);
                    $sheet->setCellValue('F' . $x, $students[$i]->program_name);
                    $sheet->setCellValue('G' . $x, $students[$i]->class_name);
                    $sheet->setCellValue('H' . $x, $students[$i]->student_type);
                    $sheet->setCellValue('I' . $x, $students[$i]->tuition_price);
                    $sheet->setCellValue('J' . $x, $students[$i]->payment_date);
                    $sheet->setCellValue('K' . $x, $students[$i]->must_charge);
                    $sheet->setCellValue('L' . $x, $students[$i]->amount);
                    $sheet->setCellValue('M' . $x, ($students[$i]->must_charge - $students[$i]->amount));
                    $sheet->setCellValue('N' . $x, $students[$i]->ec_name);
                    $sheet->setCellValue('O' . $x, $students[$i]->cm_name);

                    $st = "A" . $x;
                    $ed = "O" . $x;
                    ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);

                    ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "C$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "D$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "N$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "O$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                }
            }
        }

        // return $students;
        // return $where;

        ProcessExcel::styleCells($spreadsheet, "A5:O5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:O1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:O2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:O3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:O4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO PHÂN LOẠI CM.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC07_Demo_func($branch = null, $products = null, $programs = null, $fromDate = null, $toDate = null)
    {

        $branch = $branch;
        $products = $product;
        $programs = $program;


        $fromDate = strtotime($fromDate);
        $toDate = strtotime($toDate);

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = $today;
        }


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO PHÂN LOẠI HỌC SINH');
        $sheet->mergeCells('A1:O1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);


        $sheet->setCellValue('A2', 'TỪ NGÀY : ' . $fromDate . ' - ' . 'ĐẾN NGÀY : ' . $toDate);
        $sheet->mergeCells('A2:O2');
        // $sheet->mergeCells('A3:O3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "MÃ LMS");
        $sheet->setCellValue("C5", "MÃ EFFECT");
        $sheet->setCellValue("D5", "HỌ TÊN HỌC SINH");
        $sheet->setCellValue("E5", "SẢN PHẨM");
        $sheet->setCellValue("F5", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("G5", "LỚP");
        $sheet->setCellValue("H5", "LOẠI KHÁCH HÀNG");
        $sheet->setCellValue("I5", "GÓI PHÍ");
        $sheet->setCellValue("J5", "NGÀY ĐÓNG PHÍ");
        $sheet->setCellValue("K5", "SỐ TIỀN PHẢI ĐÓNG");
        $sheet->setCellValue("L5", "ĐÃ ĐÓNG");
        $sheet->setCellValue("M5", "CÔNG NỢ");
        $sheet->setCellValue("N5", "EC");
        $sheet->setCellValue("O5", "CM");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(15);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(15);
        $sheet->getColumnDimension("F")->setWidth(25);
        $sheet->getColumnDimension("G")->setWidth(24);
        $sheet->getColumnDimension("H")->setWidth(25);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(24);
        $sheet->getColumnDimension("K")->setWidth(24);
        $sheet->getColumnDimension("L")->setWidth(24);
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(35);
        $sheet->getColumnDimension("O")->setWidth(35);


        $branch_id = '';
        if ($branch != '_') {
            $branch_id .= '(' . $branch . ')';
        }
        $list_branch = DB::select(DB::raw("SELECT id,name from branches where id in $branch"));
        // return $list_branch;
        $product_id = '';
        if ($product != '_') {
            $product_id .= '(' . $product . ')';
        }

        $program_id = '';
        if ($program != '_') {
            $program_id .= '(' . $program . ')';
        }

        $from_date = '';
        if ($fromDate != '_') {
            $from_date .= '(' . $fromDate . ')';
        }


        $to_date = '';
        if ($toDate != '_') {
            $to_date .= '(' . $toDate . ')';
        }


        $where = '';

        if ($branch_id) {
            $where .= "and ct.branch_id in $branch_id ";
        }
        if ($program_id) {
            $where .= "and ct.program_id in $program_id ";
        }
        if ($product_id) {
            $where .= "and ct.product_id in $product_id ";
        }
        // $where .= " and count(ct.*) as payment_time <= 1";

        // if ($from_date){
        //     $where .= "and pm.created_at > '$from_date'";
        // }
        // if ($to_date){
        //     $where .= "and pm.created_at < '$to_date'";
        // }
        // $where .= "and count(pm.contract_id) = 1";
        dd($where);

        $students = DB::select(DB::raw("SELECT
                                                st.name as student_name,
                                                st.stu_id as stu_id,
                                                st.accounting_id as accounting_id,
                                                IF(st.type =1, 'VIP', 'Thường') AS student_type,
                                                cl.cls_name AS class_name,
                                                cl.id as class_id,
                                                ct.cm_id AS cm_id,
                                                ct.must_charge,
                                                CONCAT(us.full_name, ' - ', us.username) AS ec_name,
                                                CONCAT(cm.full_name, ' - ', us.username) AS cm_name,
                                                ct.ec_id AS ec_id,
                                                pr.name AS product_name,
                                                pg.name AS program_name,
                                                pm.amount AS amount,
                                                pm.debt AS debt,
                                                br.id as branch_id,
                                                date_format(pm.created_at, '%Y-%m-%d') as payment_date,
                                                tf.name as tuition_price
                                            FROM students AS st
                                            LEFT JOIN contracts AS ct ON ct.student_id = st.id
                                            LEFT JOIN branches AS br ON br.id = ct.branch_id
                                            LEFT JOIN enrolments AS el ON (el.contract_id = ct.id AND el.student_id = st.id)
                                            LEFT JOIN classes AS cl ON cl.brch_id = br.brch_id
                                            LEFT JOIN products AS pr ON ct.product_id = pr.id
                                            LEFT JOIN programs AS pg ON ct.program_id = pg.id
                                            LEFT JOIN payment AS pm ON ct.id = pm.contract_id
                                            LEFT JOIN tuition_fee AS tf ON tf.product_id = ct.id
                                            LEFT JOIN users AS cm ON cm.id = ct.cm_id
                                            LEFT JOIN users AS us ON us.id = ct.ec_id WHERE s.id > 0 $where GROUP BY st.id"));

        $p = 5;
        for ($j = 0; $j < count($list_branch); $j++) {
            $y = $p + 1;
            $p++;
            $st = 'A' . $y;
            $en = 'O' . $y;
            $sheet->setCellValue('A' . $y, $list_branch[$j]->name);
            ProcessExcel::styleCells($spreadsheet, "A$y:O$y", "FFFFFF", "black", 12, 1, 3, "left", "center", true);
            $sheet->mergeCells("$st:$en");
            for ($i = 0; $i < count($students); $i++) {

                if ($students[$i]->branch_id == $list_branch[$j]->id) {
                    $x = $p + 1;
                    $p++;
                    $sheet->setCellValue('A' . $x, $i + 1);
                    $sheet->setCellValue('B' . $x, $students[$i]->stu_id);
                    $sheet->setCellValue('C' . $x, $students[$i]->accounting_id);
                    $sheet->setCellValue('D' . $x, $students[$i]->student_name);
                    $sheet->setCellValue('E' . $x, $students[$i]->product_name);
                    $sheet->setCellValue('F' . $x, $students[$i]->program_name);
                    $sheet->setCellValue('G' . $x, $students[$i]->class_name);
                    $sheet->setCellValue('H' . $x, $students[$i]->student_type);
                    $sheet->setCellValue('I' . $x, $students[$i]->tuition_price);
                    $sheet->setCellValue('J' . $x, $students[$i]->payment_date);
                    $sheet->setCellValue('K' . $x, $students[$i]->must_charge);
                    $sheet->setCellValue('L' . $x, $students[$i]->amount);
                    $sheet->setCellValue('M' . $x, ($students[$i]->must_charge - $students[$i]->amount));
                    $sheet->setCellValue('N' . $x, $students[$i]->ec_name);
                    $sheet->setCellValue('O' . $x, $students[$i]->cm_name);

                    $st = "A" . $x;
                    $ed = "O" . $x;
                    ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);

                    ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "C$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "D$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "N$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "O$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                }
            }
        }

        // return $students;
        // return $where;

        ProcessExcel::styleCells($spreadsheet, "A5:O5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:O1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:O2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:O3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:O4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO PHÂN LOẠI HỌC SINH.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC08_demo_func($branch = null, $product = null, $program = null, $fromDate = null, $toDate = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO PHÂN LOẠI CM');
        $sheet->mergeCells('A1:O1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);


        $sheet->setCellValue('A2', 'TỪ NGÀY : ' . $fromDate . ' - ' . 'ĐẾN NGÀY : ' . $toDate);
        $sheet->mergeCells('A2:O2');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "MÃ LMS");
        $sheet->setCellValue("C5", "MÃ EFFECT");
        $sheet->setCellValue("D5", "HỌ TÊN HỌC SINH");
        $sheet->setCellValue("E5", "SẢN PHẨM");
        $sheet->setCellValue("F5", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("G5", "LỚP");
        $sheet->setCellValue("H5", "LOẠI KHÁCH HÀNG");
        $sheet->setCellValue("I5", "GÓI PHÍ");
        $sheet->setCellValue("J5", "NGÀY ĐÓNG PHÍ");
        $sheet->setCellValue("K5", "SỐ TIỀN PHẢI ĐÓNG");
        $sheet->setCellValue("L5", "ĐÃ ĐÓNG");
        $sheet->setCellValue("M5", "CÔNG NỢ");
        $sheet->setCellValue("N5", "EC");
        $sheet->setCellValue("O5", "CM");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(15);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(15);
        $sheet->getColumnDimension("F")->setWidth(25);
        $sheet->getColumnDimension("G")->setWidth(24);
        $sheet->getColumnDimension("H")->setWidth(25);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(24);
        $sheet->getColumnDimension("K")->setWidth(24);
        $sheet->getColumnDimension("L")->setWidth(24);
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(35);
        $sheet->getColumnDimension("O")->setWidth(35);


        $branch_id = '';
        if ($branch != '_') {
            $branch_id .= '(' . $branch . ')';
        }
        $list_branch = DB::select(DB::raw("SELECT id,name from branches where id in $branch_id"));
        // return $list_branch;
        $product_id = '';
        if ($product != '_') {
            $product_id .= '(' . $product . ')';
        }

        $program_id = '';
        if ($program != '_') {
            $program_id .= '(' . $program . ')';
        }

        $from_date = '';
        if ($fromDate != '_') {
            $from_date .= '(' . $fromDate . ')';
        }


        $to_date = '';
        if ($toDate != '_') {
            $to_date .= '(' . $toDate . ')';
        }


        $where = '';

        if ($branch_id) {
            $where .= "and ct.branch_id in $branch_id ";
        }
        if ($program_id) {
            $where .= "and ct.program_id in $program_id ";
        }
        if ($product_id) {
            $where .= "and ct.product_id in $product_id ";
        }
        // $where .= " and count(ct.*) as payment_time <= 1";

        // if ($from_date){
        //     $where .= "and pm.created_at > '$from_date'";
        // }
        // if ($to_date){
        //     $where .= "and pm.created_at < '$to_date'";
        // }
        // $where .= "and count(pm.contract_id) = 1";

        $c = '';
        if ($where) {
            $where = substr($where, 3);
            $c .= 'where ' . $where;
        }
        $students = DB::select(DB::raw("SELECT
                                                st.name as student_name,
                                                st.stu_id as stu_id,
                                                st.accounting_id as accounting_id,
                                                IF(st.type =1, 'VIP', 'Thường') AS student_type,
                                                cl.cls_name AS class_name,
                                                cl.id as class_id,
                                                ct.cm_id AS cm_id,
                                                ct.must_charge,
                                                CONCAT(us.full_name, ' - ', us.username) AS ec_name,
                                                CONCAT(cm.full_name, ' - ', us.username) AS cm_name,
                                                ct.ec_id AS ec_id,
                                                pr.name AS product_name,
                                                pg.name AS program_name,
                                                pm.amount AS amount,
                                                pm.debt AS debt,
                                                br.id as branch_id,
                                                date_format(pm.created_at, '%Y-%m-%d') as payment_date,
                                                tf.name as tuition_price
                                            FROM students AS st
                                            LEFT JOIN contracts AS ct ON ct.student_id = st.id
                                            LEFT JOIN branches AS br ON br.id = ct.branch_id
                                            LEFT JOIN enrolments AS el ON (el.contract_id = ct.id AND el.student_id = st.id)
                                            LEFT JOIN classes AS cl ON cl.brch_id = br.brch_id
                                            LEFT JOIN products AS pr ON ct.product_id = pr.id
                                            LEFT JOIN programs AS pg ON ct.program_id = pg.id
                                            LEFT JOIN payment AS pm ON ct.id = pm.contract_id
                                            LEFT JOIN tuition_fee AS tf ON tf.product_id = ct.id
                                            LEFT JOIN users AS cm ON cm.id = ct.cm_id
                                            LEFT JOIN users AS us ON us.id = ct.ec_id $c GROUP BY st.id"));
        // return $students;
        $p = 5;
        for ($j = 0; $j < count($list_branch); $j++) {
            $y = $p + 1;
            $p++;
            $st = 'A' . $y;
            $en = 'O' . $y;
            $sheet->setCellValue('A' . $y, $list_branch[$j]->name);
            ProcessExcel::styleCells($spreadsheet, "A$y:O$y", "FFFFFF", "black", 12, 1, 3, "left", "center", true);
            $sheet->mergeCells("$st:$en");
            for ($i = 0; $i < count($students); $i++) {

                if ($students[$i]->branch_id == $list_branch[$j]->id) {
                    $x = $p + 1;
                    $p++;
                    $sheet->setCellValue('A' . $x, $i + 1);
                    $sheet->setCellValue('B' . $x, $students[$i]->stu_id);
                    $sheet->setCellValue('C' . $x, $students[$i]->accounting_id);
                    $sheet->setCellValue('D' . $x, $students[$i]->student_name);
                    $sheet->setCellValue('E' . $x, $students[$i]->product_name);
                    $sheet->setCellValue('F' . $x, $students[$i]->program_name);
                    $sheet->setCellValue('G' . $x, $students[$i]->class_name);
                    $sheet->setCellValue('H' . $x, $students[$i]->student_type);
                    $sheet->setCellValue('I' . $x, $students[$i]->tuition_price);
                    $sheet->setCellValue('J' . $x, $students[$i]->payment_date);
                    $sheet->setCellValue('K' . $x, $students[$i]->must_charge);
                    $sheet->setCellValue('L' . $x, $students[$i]->amount);
                    $sheet->setCellValue('M' . $x, ($students[$i]->must_charge - $students[$i]->amount));
                    $sheet->setCellValue('N' . $x, $students[$i]->ec_name);
                    $sheet->setCellValue('O' . $x, $students[$i]->cm_name);

                    $st = "A" . $x;
                    $ed = "O" . $x;
                    ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);

                    ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "C$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "D$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "N$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                    ProcessExcel::styleCells($spreadsheet, "O$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
                }
            }
        }

        // return $students;
        // return $where;

        ProcessExcel::styleCells($spreadsheet, "A5:O5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:O1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:O2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:O3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:O4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO PHÂN LOẠI CM.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function report_withdraw($branch = null, $products = "", $programs = null, $fromDate = null, $toDate = null)
    {
        if ($branch == '_') {
            $branch = null;
        }
        if ($products == '_') {
            $products = null;
        }
        if ($programs == '_') {
            $programs = null;
        }
        if ($fromDate == '_') {
            $fromDate = null;
        }
        if ($toDate == '_') {
            $toDate = null;
        }

        $from_date = !empty($fromDate) ? date('Y-m-d', strtotime($fromDate)) : null;
        $to_date = !empty($toDate) ? date('Y-m-d', strtotime($toDate)) : null;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->freezePane('C6');

        $sheet->setCellValue('A1', 'BÁO CÁO HỌC SINH WITHDRAW');
        $sheet->mergeCells('A1:J1');
        $sheet->getRowDimension('1')->setRowHeight(60);
        $sheet->getRowDimension('2')->setRowHeight(20);
        $sheet->getRowDimension('3')->setRowHeight(20);
        $sheet->getRowDimension('4')->setRowHeight(40);

        $sheet->setCellValue('A2', "TỪ NGÀY - ĐẾN NGÀY: $fromDate - $toDate");
        $sheet->mergeCells('A2:J2');

        $where_product = !empty($products) ? " WHERE id in( $products )" : "";
        $res_products = DB::select(DB::raw("SELECT name from products $where_product"));
        $name = '';
        foreach ($res_products as $product) {
            $name .= $product->name . ',';
        }
        $name = rtrim($name, ',');
        // return $name;
        $sheet->setCellValue('A3', "Sản phẩm: $name");
        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue("A4", "STT");
        $sheet->setCellValue("B4", "MÃ LMS");
        $sheet->setCellValue("C4", "MÃ EFFECT");
        $sheet->setCellValue("D4", "HỌ TÊN HỌC SINH");
        $sheet->setCellValue("E4", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("F4", "LỚP");
        $sheet->setCellValue("G4", "NGÀY WITHDRAW");
        $sheet->setCellValue("H4", "LOẠI WITHDRAW");
        $sheet->setCellValue("I4", "EC");
        $sheet->setCellValue("J4", "CM");
        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:C5');
        $sheet->mergeCells('D4:D5');
        $sheet->mergeCells('E4:E5');
        $sheet->mergeCells('F4:F5');
        $sheet->mergeCells('G4:G5');
        $sheet->mergeCells('H4:H5');
        $sheet->mergeCells('I4:I5');
        $sheet->mergeCells('J4:J5');
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        ProcessExcel::styleCells($spreadsheet, "A1:J1", "black", "fff", 16, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A2:J2", "black", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A3:J3", "3FC2EE", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A4:J4", "3FC2EE", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A5:J5", "3FC2EE", "black", 11, 1, true, "center", "center", true);

        $sql = Report::createQueryReport09($branch, $products, $from_date, $to_date, null);
        $result = DB::select(DB::raw($sql));

        // $st = 'A'.$x;
        // $en = 'J'.$x;
        $p = 5;
        $p++;
        // $sheet->mergeCells("$st:$en");
        // ProcessExcel::styleCells($spreadsheet,"$st:$en","3FC2EE","black",9,1,true,"left","center",true);
        $stt = 1;
        // dd($result);
        for ($j = 0; $j < count($result); $j++) {
            if ($result) {
                $y = $p + 1;
                $st = 'A' . $y;
                $en = 'J' . $y;
                // $sheet->mergeCells("$st:$en");
                $sheet->setCellValue('A' . $y, $stt);
                $sheet->setCellValue('B' . $y, $result[$j]->lms_id);
                $sheet->setCellValue('C' . $y, u::get($result[$j], 'accounting_id'));
                $sheet->setCellValue('D' . $y, $result[$j]->name);
                $sheet->setCellValue('E' . $y, $result[$j]->program_name);
                $sheet->setCellValue('F' . $y, $result[$j]->class_name);
                $sheet->setCellValue('G' . $y, $result[$j]->withdraw_date);
                $sheet->setCellValue('H' . $y, $result[$j]->withdraw_reason);

                // $sheet->setCellValue('H'.$y, $type);
                $sheet->setCellValue('I' . $y, $result[$j]->ec_name);
                $sheet->setCellValue('J' . $y, $result[$j]->cm_name);
                $p++;
                $stt++;
                ProcessExcel::styleCells($spreadsheet, "$st:$en", "3FC2EE", "black", 11, '', true, "center", "center", true);
            }
        }


        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Danh sách WITHDRAW.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function report_bc10($branch = null, $product = "", $program = null, $fromDate = null, $toDate = null, $type = null)
    {
        $conn = DB::connection('mysql_1');

        $branch = $branch ? $branch : null;
        $product = $product ? $product : null;
        $program = $program ? $program : null;
        $fromDate = $fromDate ? $fromDate : null;
        $toDate = $toDate ? $toDate : null;
        $type = null;

        if ($type == 'infor') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A2', 'DASHBOARD REPORT');
            $sheet->mergeCells('A2:I2');

            $sheet->getRowDimension('2')->setRowHeight(50);
            ProcessExcel::styleCells($spreadsheet, "A2:I2", "4472c4ff", "fff", 16, 1, true, "left", "center", true);

            $sheet->setCellValue('A3', 'DỮ LIỆU TOÀN BỘ CÁC TRUNG TÂM');

            ProcessExcel::styleCells($spreadsheet, "A3:I3", "4472c4ff", "fff", 16, 1, true, "center", "center", true);

            $sheet->mergeCells('A3:I3');

            $sheet->setCellValue('A4', 'TRUNG TÂM');
            $sheet->setCellValue('B4', 'DOANH SỐ');
            $sheet->setCellValue('C4', 'DOANH SỐ TRONG NGÀY');
            $sheet->setCellValue('D4', 'DOANH SỐ KẾ HOẠCH');
            $sheet->setCellValue('E4', '% KẾ HOẠCH');
            $sheet->setCellValue('F4', 'SỐ HỌC SINH MỚI');
            $sheet->setCellValue('G4', 'SỐ HỌC SINH HIỆN TẠI');
            $sheet->setCellValue('H4', 'SỐ HỌC SINH SẮP HẾT HẠN');
            $sheet->setCellValue('I4', 'SỐ HỌC SINH CÒN NỢ');

            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(25);
            $sheet->getColumnDimension('D')->setWidth(25);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(25);
            $sheet->getColumnDimension('H')->setWidth(25);
            $sheet->getColumnDimension('I')->setWidth(25);
            ProcessExcel::styleCells($spreadsheet, "A4:I4", "add8e6", "black", 11, 1, true, "center", "center", true);
            $sheet->getStyle('A4:I4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('add8e6');

            $sql = '';
            $sql_tong = '';
            $sql_vung = '';
            $vung = "";
            if (in_array($role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {
                $sql = "SELECT *from dbo_fuc_dashboard01_new where invisible = 0 order by ps_no1 desc";
                $sql_tong = "SELECT *from dbo_fuc_dashboard01_new where invisible = 3";
                $sql_vung = "SELECT *from dbo_fuc_dashboard01_new where invisible = 4";
            } else if ($role_id == ROLE_REGION_CEO) {
                $region = DB::select("SELECT * from regions where ceo_id = $id");
                $region_id = $region[0]->id;
                $branch_id = DB::select(DB::raw("SELECT hrm_id from branches where zone_id = $region_id"));
                // return $branch_id;
                $hrm_id = '';
                foreach ($branch_id as $item) {
                    # code...
                    $item->hrm_id = str_replace("\n", "", $item->hrm_id);
                    $item->hrm_id = str_replace("\r", "", $item->hrm_id);
                    $hrm_id .= $item->hrm_id . ',';
                }
                $hrm_id = rtrim($hrm_id, ",");
                $hrm = '(' . $hrm_id . ')';

                $sql = "SELECT *from dbo_fuc_dashboard01_new where invisible = 0 and ma_crm in $hrm order by ps_no1 desc";
                $sum = $conn->select("SELECT SUM(ps_no1) as ps_no1, SUM(ps_no1kh) as ps_no1kh,SUM(hs_moi) as hs_moi, SUM(hs_hientai) as hs_hientai, SUM(hs_no) as hs_no,SUM(hs_hethan) as hs_hethan, SUM(ps_ngay) as ps_ngay  from dbo_fuc_dashboard01_new where invisible = 0 AND ma_crm in $hrm");
                $vung = array(
                    "ten" => $region[0]->name,
                    "ps_no1" => apax_ada_format_number($sum[0]->ps_no1),
                    "ps_no1kh" => apax_ada_format_number($sum[0]->ps_no1kh),
                    "hs_moi" => apax_ada_format_number($sum[0]->hs_moi),
                    "hs_hientai" => apax_ada_format_number($sum[0]->hs_hientai),
                    "hs_no" => apax_ada_format_number($sum[0]->hs_no),
                    "hs_hethan" => apax_ada_format_number($sum[0]->hs_hethan),
                    "ps_ngay" => apax_ada_format_number($sum[0]->ps_ngay),
                    "invisible" => 4,
                    "percent" => floor($sum[0]->ps_no1 / $sum[0]->ps_no1kh * 100)
                );
            } else {
                $branch_id = DB::select("SELECT accounting_id from branches where id in (SELECT branch_id from term_user_branch where user_id = $id)");
                $accounting_id = $branch_id[0]->accounting_id;

                $sql = "SELECT *from dbo_fuc_dashboard01_new where ma_eff = '$accounting_id'";
            }

            if ($sql && $sql_tong && $sql_vung) {
                $data = $conn->select($sql);
                $data_tong = $conn->select($sql_tong);
                $data_vung = $conn->select($sql_vung);

                $data_tong[0]->ps_no1 = apax_ada_format_number($data_tong[0]->ps_no1);
                $data_tong[0]->ps_no1kh = apax_ada_format_number($data_tong[0]->ps_no1kh);
                $data_tong[0]->hs_moi = apax_ada_format_number($data_tong[0]->hs_moi);
                $data_tong[0]->hs_no = apax_ada_format_number($data_tong[0]->hs_no);
                $data_tong[0]->hs_hientai = apax_ada_format_number($data_tong[0]->hs_hientai);
                $data_tong[0]->hs_hethan = apax_ada_format_number($data_tong[0]->hs_hethan);
                $data_tong[0]->hs_no = apax_ada_format_number($data_tong[0]->hs_no);

                $sheet->setCellValue('A5', $data_tong[0]->ten);
                $sheet->setCellValue('B5', $data_tong[0]->ps_no1);
                $sheet->setCellValue('C5', $data_tong[0]->ps_ngay);
                $sheet->setCellValue('D5', $data_tong[0]->ps_no1kh);
                $sheet->setCellValue('E5', $data_tong[0]->pt . '%');
                $sheet->setCellValue('F5', $data_tong[0]->hs_moi);
                $sheet->setCellValue('G5', $data_tong[0]->hs_hientai);
                $sheet->setCellValue('H5', $data_tong[0]->hs_hethan);
                $sheet->setCellValue('I5', $data_tong[0]->hs_no);

                ProcessExcel::styleCells($spreadsheet, "B5:I5", "add8e6", "black", 11, 1, true, "center", "center", true);
                ProcessExcel::styleCells($spreadsheet, "A5", "add8e6", "black", 11, 1, true, "left", "center", true);
                $sheet->getStyle('A5:I5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff');
                $p = 5;
                for ($i = 0; $i < count($data_vung); $i++) {
                    $x = $p + 1;
                    $p++;

                    $data_vung[$i]->ps_no1 = apax_ada_format_number($data_vung[$i]->ps_no1);
                    $data_vung[$i]->ps_no1kh = apax_ada_format_number($data_vung[$i]->ps_no1kh);
                    $data_vung[$i]->hs_moi = apax_ada_format_number($data_vung[$i]->hs_moi);
                    $data_vung[$i]->hs_no = apax_ada_format_number($data_vung[$i]->hs_no);
                    $data_vung[$i]->hs_hientai = apax_ada_format_number($data_vung[$i]->hs_hientai);
                    $data_vung[$i]->hs_hethan = apax_ada_format_number($data_vung[$i]->hs_hethan);
                    $data_vung[$i]->hs_no = apax_ada_format_number($data_vung[$i]->hs_no);

                    $sheet->setCellValue("A$x", $data_vung[$i]->ten);
                    $sheet->setCellValue("B$x", $data_vung[$i]->ps_no1);
                    $sheet->setCellValue("C$x", $data_vung[$i]->ps_ngay);
                    $sheet->setCellValue("D$x", $data_vung[$i]->ps_no1kh);
                    $sheet->setCellValue("E$x", $data_vung[$i]->pt . '%');
                    $sheet->setCellValue("F$x", $data_vung[$i]->hs_moi);
                    $sheet->setCellValue("G$x", $data_vung[$i]->hs_hientai);
                    $sheet->setCellValue("H$x", $data_vung[$i]->hs_hethan);
                    $sheet->setCellValue("I$x", $data_vung[$i]->hs_no);
                    ProcessExcel::styleCells($spreadsheet, "B$x:I$x", "add8e6", "black", 11, 1, true, "center", "center", true);
                    ProcessExcel::styleCells($spreadsheet, "A$x", "add8e6", "black", 11, 1, true, "left", "center", true);
                    $sheet->getStyle("A$x:I$x")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ff0000');

                    for ($j = 0; $j < count($data); $j++) {
                        if (trim($data[$j]->vung_mien) == trim($data_vung[$i]->ten)) {
                            $x = $p + 1;
                            $p++;

                            $data[$j]->ps_no1 = apax_ada_format_number($data[$j]->ps_no1);
                            $data[$j]->ps_no1kh = apax_ada_format_number($data[$j]->ps_no1kh);
                            $data[$j]->hs_moi = apax_ada_format_number($data[$j]->hs_moi);
                            $data[$j]->hs_no = apax_ada_format_number($data[$j]->hs_no);
                            $data[$j]->hs_hientai = apax_ada_format_number($data[$j]->hs_hientai);
                            $data[$j]->hs_hethan = apax_ada_format_number($data[$j]->hs_hethan);
                            $data[$j]->hs_no = apax_ada_format_number($data[$j]->hs_no);

                            $sheet->setCellValue("A$x", $data[$j]->ten);
                            $sheet->setCellValue("B$x", $data[$j]->ps_no1);
                            $sheet->setCellValue("C$x", $data[$j]->ps_ngay);
                            $sheet->setCellValue("D$x", $data[$j]->ps_no1kh);
                            $sheet->setCellValue("E$x", $data[$j]->pt . '%');
                            $sheet->setCellValue("F$x", $data[$j]->hs_moi);
                            $sheet->setCellValue("G$x", $data[$j]->hs_hientai);
                            $sheet->setCellValue("H$x", $data[$j]->hs_hethan);
                            $sheet->setCellValue("I$x", $data[$j]->hs_no);

                            ProcessExcel::styleCells($spreadsheet, "B$x:I$x", "add8e6", "black", 11, '', true, "center", "center", true);
                            ProcessExcel::styleCells($spreadsheet, "A$x", "add8e6", "black", 11, '', true, "left", "center", true);
                        }
                    }

                }
            } else if ($sql && $sql_tong == '' && $sql_vung == '' && $vung) {
                // $vung = json_encode($vung);
                // return $vung;
                $sheet->setCellValue("A5", $vung["ten"]);
                $sheet->setCellValue("B5", $vung["ps_no1"]);
                $sheet->setCellValue("C5", $vung["ps_ngay"]);
                $sheet->setCellValue("D5", $vung["ps_no1kh"]);
                $sheet->setCellValue("E5", $vung["percent"]);
                $sheet->setCellValue("F5", $vung["hs_moi"]);
                $sheet->setCellValue("G5", $vung["hs_hientai"]);
                $sheet->setCellValue("H5", $vung["hs_hethan"]);
                $sheet->setCellValue("I5", $vung["hs_no"]);

                ProcessExcel::styleCells($spreadsheet, "B5:I5", "add8e6", "black", 11, 1, true, "center", "center", true);
                ProcessExcel::styleCells($spreadsheet, "A5", "add8e6", "black", 11, 1, true, "left", "center", true);
                $sheet->getStyle('A5:I5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff');
                $data = $conn->select($sql);
                $p = 5;
                for ($i = 0; $i < count($data); $i++) {
                    $x = $p + 1;
                    $p++;
                    $data[$i]->ps_no1 = apax_ada_format_number($data[$i]->ps_no1);
                    $data[$i]->ps_no1kh = apax_ada_format_number($data[$i]->ps_no1kh);
                    $data[$i]->hs_moi = apax_ada_format_number($data[$i]->hs_moi);
                    $data[$i]->hs_no = apax_ada_format_number($data[$i]->hs_no);
                    $data[$i]->hs_hientai = apax_ada_format_number($data[$i]->hs_hientai);
                    $data[$i]->hs_hethan = apax_ada_format_number($data[$i]->hs_hethan);
                    $data[$i]->hs_no = apax_ada_format_number($data[$i]->hs_no);

                    $sheet->setCellValue("A$x", $data[$i]->ten);
                    $sheet->setCellValue("B$x", $data[$i]->ps_no1);
                    $sheet->setCellValue("C$x", $data[$i]->ps_ngay);
                    $sheet->setCellValue("D$x", $data[$i]->ps_no1kh);
                    $sheet->setCellValue("E$x", $data[$i]->pt . '%');
                    $sheet->setCellValue("F$x", $data[$i]->hs_moi);
                    $sheet->setCellValue("G$x", $data[$i]->hs_hientai);
                    $sheet->setCellValue("H$x", $data[$i]->hs_hethan);
                    $sheet->setCellValue("I$x", $data[$i]->hs_no);

                    ProcessExcel::styleCells($spreadsheet, "B$x:I$x", "add8e6", "black", 11, '', true, "center", "center", true);
                    ProcessExcel::styleCells($spreadsheet, "A$x", "add8e6", "black", 11, '', true, "left", "center", true);
                }
            } else {
                $data = $conn->select($sql);
                $sheet->setCellValue('A5', $data[0]->ten);
                $sheet->setCellValue('B5', $data[0]->ps_no1);
                $sheet->setCellValue('C5', $data[0]->ps_ngay);
                $sheet->setCellValue('D5', $data[0]->ps_no1kh);
                $sheet->setCellValue('E5', $data[0]->pt);
                $sheet->setCellValue('F5', $data[0]->hs_moi);
                $sheet->setCellValue('G5', $data[0]->hs_hientai);
                $sheet->setCellValue('H5', $data[0]->hs_hethan);
                $sheet->setCellValue('I5', $data[0]->hs_no);

                ProcessExcel::styleCells($spreadsheet, "B5:I5", "add8e6", "black", 11, '', true, "center", "center", true);
                ProcessExcel::styleCells($spreadsheet, "A5", "add8e6", "black", 11, '', true, "left", "center", true);

            }


            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Dữ liệu tổng hợp các trung tâm.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
            exit;
        }
        if ($type == 'best_branch') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A2', 'TOP 5 BEST TRUNG TÂM');
            $sheet->mergeCells('A2:C2');

            $sheet->getRowDimension('2')->setRowHeight(40);
            ProcessExcel::styleCells($spreadsheet, "A2:C2", "4472c4ff", "fff", 16, 1, true, "center", "center", true);


            $sheet->setCellValue('A3', 'STT');
            $sheet->setCellValue('B3', 'TÊN TRUNG TÂM');
            $sheet->setCellValue('C3', 'DOANH SỐ');

            $sheet->getColumnDimension('A')->setWidth(50);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(45);

            ProcessExcel::styleCells($spreadsheet, "A3:C3", "add8e6", "black", 11, 1, true, "center", "center", true);
            $sheet->getStyle('A3:C3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('add8e6');

            // VALUE HERE

            $sql = '';

            if (in_array($role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {
                $sql = "SELECT *from dbo_fuc_dashboard01_new where invisible = 0 order by ps_no1 desc limit 5";
            } else if ($role_id == ROLE_REGION_CEO) {
                $region = DB::select("SELECT * from regions where ceo_id = $id");
                $region_id = $region[0]->id;
                $branch_id = DB::select(DB::raw("SELECT hrm_id from branches where zone_id = $region_id"));
                // return $branch_id;
                $hrm_id = '';
                foreach ($branch_id as $item) {
                    # code...
                    $item->hrm_id = str_replace("\n", "", $item->hrm_id);
                    $item->hrm_id = str_replace("\r", "", $item->hrm_id);
                    $item->hrm_id = str_replace("\t", "", $item->hrm_id);
                    $hrm_id .= $item->hrm_id . ',';
                }
                $hrm_id = rtrim($hrm_id, ",");
                $hrm = '(' . $hrm_id . ')';

                $sql = "SELECT *from dbo_fuc_dashboard01_new where invisible = 0 AND ma_crm in $hrm order by ps_no1 desc limit 5";
            } else {
                $branch = DB::select("SELECT hrm_id from branches where id in (SELECT branch_id from term_user_branch where user_id = $id)");
                $hrm_id = $branch[0]->hrm_id;

                $sql = "SELECT *from dbo_fuc_dashboard01_new where invisible = 0 AND ma_crm = $hrm_id";
            }

            $data = $conn->select(DB::raw($sql));
            $p = 3;

            for ($i = 0; $i < count($data); $i++) {
                $x = $p + 1;
                $p++;
                $sheet->setCellValue("A$x", $data[$i]->ma_eff);
                $sheet->setCellValue("B$x", $data[$i]->ten);
                $sheet->setCellValue("C$x", $data[$i]->ps_no1);

                ProcessExcel::styleCells($spreadsheet, "A$x:B$x", "add8e6", "black", 11, '', true, "left", "center", true);
                ProcessExcel::styleCells($spreadsheet, "C$x", "add8e6", "black", 11, '', true, "center", "center", true);
            }
            ProcessExcel::styleCells($spreadsheet, "A4:C4", "add8e6", "0000ff", 11, '', true, "left", "center", true);
            ProcessExcel::styleCells($spreadsheet, "C4", "add8e6", "0000ff", 11, '', true, "center", "center", true);
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="TOP 5 BEST TRUNG TÂM.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
            exit;
        }
        if ($type == 'bad_branch') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A2', 'TOP 5 BAD TRUNG TÂM');
            $sheet->mergeCells('A2:C2');

            $sheet->getRowDimension('2')->setRowHeight(40);
            ProcessExcel::styleCells($spreadsheet, "A2:C2", "4472c4ff", "fff", 16, 1, true, "center", "center", true);


            $sheet->setCellValue('A3', 'STT');
            $sheet->setCellValue('B3', 'TÊN TRUNG TÂM');
            $sheet->setCellValue('C3', 'DOANH SỐ');

            $sheet->getColumnDimension('A')->setWidth(50);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(45);

            ProcessExcel::styleCells($spreadsheet, "A3:C3", "add8e6", "black", 11, 1, true, "center", "center", true);
            $sheet->getStyle('A3:C3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('add8e6');

            // VALUE HERE

            $sql = '';

            if (in_array($role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {
                $sql = "SELECT *from dbo_fuc_dashboard01_new where invisible = 0 order by ps_no1 limit 5";
            } else if ($role_id == ROLE_REGION_CEO) {
                $region = DB::select("SELECT * from regions where ceo_id = $id");
                $region_id = $region[0]->id;
                $branch_id = DB::select(DB::raw("SELECT hrm_id from branches where zone_id = $region_id"));
                // return $branch_id;
                $hrm_id = '';
                foreach ($branch_id as $item) {
                    # code...
                    $item->hrm_id = str_replace("\n", "", $item->hrm_id);
                    $item->hrm_id = str_replace("\r", "", $item->hrm_id);
                    $item->hrm_id = str_replace("\t", "", $item->hrm_id);
                    $hrm_id .= $item->hrm_id . ',';
                }
                $hrm_id = rtrim($hrm_id, ",");
                $hrm = '(' . $hrm_id . ')';

                $sql = "SELECT *from dbo_fuc_dashboard01_new where invisible = 0 AND ma_crm in $hrm order by ps_no1 limit 5";
            } else {
                $branch = DB::select("SELECT hrm_id from branches where id in (SELECT branch_id from term_user_branch where user_id = $id)");

                $hrm_id = $branch[0]->hrm_id;

                $sql = "SELECT *from dbo_fuc_dashboard01_new where invisible = 0 AND ma_crm = $hrm_id";
            }

            $data = $conn->select(DB::raw($sql));
            $p = 3;

            for ($i = 0; $i < count($data); $i++) {
                $x = $p + 1;
                $p++;
                $sheet->setCellValue("A$x", $data[$i]->ma_eff);
                $sheet->setCellValue("B$x", $data[$i]->ten);
                $sheet->setCellValue("C$x", $data[$i]->ps_no1);

                ProcessExcel::styleCells($spreadsheet, "A$x:B$x", "add8e6", "black", 11, '', true, "left", "center", true);
                ProcessExcel::styleCells($spreadsheet, "C$x", "add8e6", "black", 11, '', true, "center", "center", true);
            }
            ProcessExcel::styleCells($spreadsheet, "A4:C4", "add8e6", "ff0000", 11, '', true, "left", "center", true);
            ProcessExcel::styleCells($spreadsheet, "C4", "add8e6", "ff0000", 11, '', true, "center", "center", true);

            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="TOP 5 BEST TRUNG TÂM.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
            exit;
        }
        if ($type == 'best_leader') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A2', 'TOP 5 BEST LEADER');
            $sheet->mergeCells('A2:D2');

            $sheet->getRowDimension('2')->setRowHeight(40);
            ProcessExcel::styleCells($spreadsheet, "A2:D2", "4472c4ff", "fff", 16, 1, true, "center", "center", true);


            $sheet->setCellValue('A3', 'Mã leader');
            $sheet->setCellValue('B3', 'Tên leader');
            $sheet->setCellValue('C3', 'Doanh số');
            $sheet->setCellValue('D3', 'Trung tâm');

            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(45);
            $sheet->getColumnDimension('D')->setWidth(45);

            ProcessExcel::styleCells($spreadsheet, "A3:D3", "add8e6", "black", 11, 1, true, "center", "center", true);
            $sheet->getStyle('A3:D3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('add8e6');

            // VALUE HERE
            $sql = '';
            if (in_array($role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {
                $sql = "SELECT *from tbldoanhso_team where ma_nv != '' order by doanhso desc limit 5";
            } else if ($role_id == ROLE_REGION_CEO) {
                $region = DB::select("SELECT * from regions where ceo_id = $user_id");
                $region_id = $region[0]->id;
                $branch_id = DB::select(DB::raw("SELECT accounting_id from branches where zone_id = $region_id"));
                // return $branch_id;
                $accounting_id = '';
                foreach ($branch_id as $item) {
                    # code...
                    $item->accounting_id = str_replace("\n", "", $item->accounting_id);
                    $item->accounting_id = str_replace("\r", "", $item->accounting_id);
                    $item->accounting_id = str_replace("\t", "", $item->accounting_id);
                    $accounting_id .= '\'' . $item->accounting_id . '\',';
                }
                $accounting_id = rtrim($accounting_id, ",");
                // return $accounting_id;
                $effect = '(' . $accounting_id . ')';


                $sql = "SELECT *from tbldoanhso_team  where ma_tt in $effect order by doanhso desc limit 5";
            } else {
                $branch = DB::select(DB::raw("SELECT accounting_id from branches where id in (SELECT branch_id from term_user_branch where user_id = $user_id)"));
                $branch_id = $branch[0]->accounting_id;

                $sql = "SELECT *from tbldoanhso_team where ma_tt = '$branch_id'";
            }

            $data = $conn->select($sql);
            $p = 3;
            for ($i = 0; $i < count($data); $i++) {
                $x = $p + 1;
                $p++;
                $ma_tt = trim($data[$i]->ma_tt);
                $ma_tt = str_replace("\n", "", $ma_tt);
                $ma_tt = str_replace("\r", "", $ma_tt);
                $ma_tt = str_replace("\t", "", $ma_tt);

                $branch = DB::select("SELECT name from branches where accounting_id = '$ma_tt'");
                if ($branch) $name = $branch[0]->name;
                else $name = trim($data[$i]->ma_tt);
                $sheet->setCellValue("A$x", trim($data[$i]->ma_nv));
                $sheet->setCellValue("B$x", trim($data[$i]->ten_nv));
                $sheet->setCellValue("C$x", trim($data[$i]->doanhso));
                $sheet->setCellValue("D$x", trim($name));
                ProcessExcel::styleCells($spreadsheet, "A$x:D$x", "add8e6", "black", 11, '', true, "left", "center", true);
                ProcessExcel::styleCells($spreadsheet, "C$x", "add8e6", "black", 11, '', true, "center", "center", true);
            }
            ProcessExcel::styleCells($spreadsheet, "A4:B4", "add8e6", "0000ff", 11, '', true, "left", "center", true);
            ProcessExcel::styleCells($spreadsheet, "C4", "add8e6", "0000ff", 11, '', true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "D4", "add8e6", "0000ff", 11, '', true, "left", "center", true);

            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="TOP 5 BEST LEADER.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
            exit;
        }
        if ($type == 'bad_leader') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A2', 'TOP 5 BAD LEADER');
            $sheet->mergeCells('A2:D2');

            $sheet->getRowDimension('2')->setRowHeight(40);
            ProcessExcel::styleCells($spreadsheet, "A2:D2", "4472c4ff", "ff0000", 16, 1, true, "center", "center", true);


            $sheet->setCellValue('A3', 'Mã leader');
            $sheet->setCellValue('B3', 'Tên leader');
            $sheet->setCellValue('C3', 'Doanh số');
            $sheet->setCellValue('D3', 'Trung tâm');

            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(45);
            $sheet->getColumnDimension('D')->setWidth(45);

            ProcessExcel::styleCells($spreadsheet, "A3:D3", "add8e6", "fff", 11, 1, true, "center", "center", true);
            $sheet->getStyle('A3:D3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('add8e6');

            // VALUE HERE
            $sql = '';
            if (in_array($role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {
                $sql = "SELECT *from tbldoanhso_team where ma_nv != '' order by doanhso limit 5";
            } else if ($role_id == ROLE_REGION_CEO) {
                $region = DB::select("SELECT * from regions where ceo_id = $user_id");
                $region_id = $region[0]->id;
                $branch_id = DB::select(DB::raw("SELECT accounting_id from branches where zone_id = $region_id"));
                // return $branch_id;
                $accounting_id = '';
                foreach ($branch_id as $item) {
                    # code...
                    $item->accounting_id = str_replace("\n", "", $item->accounting_id);
                    $item->accounting_id = str_replace("\r", "", $item->accounting_id);
                    $item->accounting_id = str_replace("\t", "", $item->accounting_id);
                    $accounting_id .= '\'' . $item->accounting_id . '\',';
                }
                $accounting_id = rtrim($accounting_id, ",");
                // return $accounting_id;
                $effect = '(' . $accounting_id . ')';


                $sql = "SELECT *from tbldoanhso_team  where ma_tt in $effect order by doanhso limit 5";
            } else {
                $branch = DB::select(DB::raw("SELECT accounting_id from branches where id in (SELECT branch_id from term_user_branch where user_id = $user_id)"));
                $branch_id = $branch[0]->accounting_id;

                $sql = "SELECT *from tbldoanhso_team where ma_tt = '$branch_id'";
            }

            $data = $conn->select($sql);
            $p = 3;
            for ($i = 0; $i < count($data); $i++) {
                $x = $p + 1;
                $p++;
                $ma_tt = trim($data[$i]->ma_tt);
                $ma_tt = str_replace("\n", "", $ma_tt);
                $ma_tt = str_replace("\r", "", $ma_tt);
                $ma_tt = str_replace("\t", "", $ma_tt);

                $branch = DB::select("SELECT name from branches where accounting_id = '$ma_tt'");
                if ($branch) $name = $branch[0]->name;
                else $name = trim($data[$i]->ma_tt);
                $sheet->setCellValue("A$x", trim($data[$i]->ma_nv));
                $sheet->setCellValue("B$x", trim($data[$i]->ten_nv));
                $sheet->setCellValue("C$x", trim($data[$i]->doanhso));
                $sheet->setCellValue("D$x", trim($name));
                ProcessExcel::styleCells($spreadsheet, "A$x:D$x", "add8e6", "black", 11, '', true, "left", "center", true);
                ProcessExcel::styleCells($spreadsheet, "C$x", "add8e6", "black", 11, '', true, "center", "center", true);
            }
            ProcessExcel::styleCells($spreadsheet, "A4:B4", "add8e6", "ff0000", 11, '', true, "left", "center", true);
            ProcessExcel::styleCells($spreadsheet, "C4", "add8e6", "ff0000", 11, '', true, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "D4", "add8e6", "ff0000", 11, '', true, "left", "center", true);

            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="TOP 5 BAD LEADER.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
            exit;
        }
        if ($type == 'best_sales') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A2', 'TOP 10 BEST SALES');
            $sheet->mergeCells('A2:D2');

            $sheet->getRowDimension('2')->setRowHeight(40);
            ProcessExcel::styleCells($spreadsheet, "A2:D2", "4472c4ff", "fff", 16, 1, true, "center", "center", true);


            $sheet->setCellValue('A3', 'Mã Sale');
            $sheet->setCellValue('B3', 'Tên Sale');
            $sheet->setCellValue('C3', 'Doanh số');
            $sheet->setCellValue('D3', 'Trung tâm');

            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(45);
            $sheet->getColumnDimension('D')->setWidth(45);

            ProcessExcel::styleCells($spreadsheet, "A3:D3", "add8e6", "fff", 11, 1, true, "center", "center", true);
            $sheet->getStyle('A3:D3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('add8e6');

            // VALUE HERE
            $sql = '';
            if (in_array($role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {
                $sql = "SELECT *from tbldoanhso_sp order by doanhso desc limit 10";
            } else if ($role_id == ROLE_REGION_CEO) {
                $region = DB::select("SELECT * from regions where ceo_id = $user_id");
                $region_id = $region[0]->id;
                $branch_id = DB::select(DB::raw("SELECT accounting_id from branches where zone_id = $region_id"));
                // return $branch_id;
                $accounting_id = '';
                foreach ($branch_id as $item) {
                    # code...
                    $item->accounting_id = str_replace("\n", "", $item->accounting_id);
                    $item->accounting_id = str_replace("\r", "", $item->accounting_id);
                    $item->accounting_id = str_replace("\t", "", $item->accounting_id);
                    $accounting_id .= '\'' . $item->accounting_id . '\',';
                }
                $accounting_id = rtrim($accounting_id, ",");
                // return $accounting_id;
                $effect = '(' . $accounting_id . ')';

                $sql = "SELECT *from tbldoanhso_sp where ma_tt in $effect order by doanhso desc limit 10";
            } else {
                $branch = DB::select(DB::raw("SELECT accounting_id from branches where id in (SELECT branch_id from term_user_branch where user_id = $user_id)"));
                $branch_id = $branch[0]->accounting_id;

                $sql = "SELECT *from tbldoanhso_sp where ma_tt = '$branch_id' order by doanhso desc limit 10";
            }

            $data = $conn->select($sql);

            $p = 3;
            for ($i = 0; $i < count($data); $i++) {
                $x = $p + 1;
                $p++;
                $sheet->setCellValue("A$x", trim($data[$i]->ma_sp));
                $sheet->setCellValue("B$x", trim($data[$i]->sanpham));
                $sheet->setCellValue("C$x", trim($data[$i]->doanhso));

                $ma_tt = trim($data[$i]->ma_tt);
                $ma_tt = str_replace("\n", "", $ma_tt);
                $ma_tt = str_replace("\r", "", $ma_tt);
                $ma_tt = str_replace("\t", "", $ma_tt);

                $branch = DB::select("SELECT name from branches where accounting_id = '$ma_tt'");
                if ($branch) $name = $branch[0]->name;
                else $name = trim($data[$i]->ma_tt);
                $sheet->setCellValue("D$x", trim($name));
                ProcessExcel::styleCells($spreadsheet, "A$x:D$x", "add8e6", "black", 11, '', true, "left", "center", true);
                ProcessExcel::styleCells($spreadsheet, "C$x", "add8e6", "black", 11, '', true, "center", "center", true);
            }
            ProcessExcel::styleCells($spreadsheet, "A4:D4", "add8e6", "0000ff", 11, '', true, "left", "center", true);
            ProcessExcel::styleCells($spreadsheet, "C4", "add8e6", "0000ff", 11, '', true, "center", "center", true);

            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="TOP 10 BEST SALES.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
            exit;
        }
        if ($type == 'bad_sales') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A2', 'TOP 10 BAD SALES');
            $sheet->mergeCells('A2:D2');

            $sheet->getRowDimension('2')->setRowHeight(40);
            ProcessExcel::styleCells($spreadsheet, "A2:D2", "4472c4ff", "ff0000", 16, 1, true, "center", "center", true);


            $sheet->setCellValue('A3', 'Mã Sale');
            $sheet->setCellValue('B3', 'Tên Sale');
            $sheet->setCellValue('C3', 'Doanh số');
            $sheet->setCellValue('D3', 'Trung tâm');

            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(45);
            $sheet->getColumnDimension('D')->setWidth(45);

            ProcessExcel::styleCells($spreadsheet, "A3:D3", "add8e6", "fff", 11, 1, true, "center", "center", true);
            $sheet->getStyle('A3:D3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('add8e6');

            // VALUE HERE

            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="TOP 10 BAD SALES.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
            exit;
        }
    }

    public function reportBC10($branch = null, $product = null, $program = null, $customerType = null, $fromDate = null, $toDate = null)
    {

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));
        // dd($new_date);


        $branches = $branch;
        $programs = $program;
        $customerTypes = $customerType;
        $from_date = $fromDate;
        $to_date = $toDate;

        $fromDate = strtotime($from_date);
        $toDate = strtotime($to_date);

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = $today;
        }

        // dd($from_date, $to_date);


        $where = "";

        if ($branches && $branches != '_') {

            $where .= " AND s.id in ($branches)";
        }

        /*if ($programs && $programs != '_') {

            $where .= " AND pr.id in ($programs)";
        }

        if ($customerTypes && $customerTypes != '_') {

            $where .= " AND ct.type in ($customerTypes)";
        }


        if ($fromDate) {
            $where .= " AND pd.created_at >= '$from_date' ";
        }


        if ($toDate) {
            $where .= " AND pd.created_at <= '$to_date' ";
        }*/


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN ANH NGỮ APAX');
        $sheet->setCellValue('A2', 'Tầng 7, Tòa nhà 14 Láng Hạ, Ba Đình, Hà Nội');
        $sheet->setCellValue('A3', 'BÁO CÁO DOANH SỐ');
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:P3');
        $sheet->getRowDimension('1')->setRowHeight(20);
        $sheet->getRowDimension('2')->setRowHeight(20);
        $sheet->getRowDimension('3')->setRowHeight(50);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $sheet->setCellValue('A4', 'NGÀY : ' . date('d-m-Y H:i:s'));
        $sheet->mergeCells('A4:P4');
        // $sheet->mergeCells('A3:M3');


        // $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A7", "STT");
        $sheet->setCellValue("B7", "Ngày thực hiện");
        $sheet->setCellValue("C7", "Người thực hiện");
        $sheet->setCellValue("D7", "Ngày điều chuyển");
        $sheet->setCellValue("E7", "Mã LMS");
        $sheet->setCellValue("F7", "Học sinh");
        $sheet->setCellValue("G7", "Mức học phí còn lại");
        $sheet->setCellValue("H7", "Số buổi còn lại");
        $sheet->setCellValue("I7", "Trình độ");
        $sheet->setCellValue("J7", "Trung tâm chuyển đi");
        $sheet->setCellValue("K7", "Lớp chuyển đi");
        $sheet->setCellValue("L7", "Trung tâm chuyển đến");
        $sheet->setCellValue("M7", "Lớp chuyển đến");
        $sheet->setCellValue("N7", "Mã EFFECT TT chuyển đi");
        $sheet->setCellValue("O7", "Mã EFFECT TT chuyển đến");
        $sheet->setCellValue("P7", "Trạng thái");
        $sheet->setCellValue("Q7", "Lý do từ chối nếu có");
        $sheet->setCellValue("R7", "EC (Trung tâm chuyển đến)");
        $sheet->setCellValue("S7", "CM (Trung tâm chuyển đến)");
        $sheet->setCellValue("T7", "EC (Trung tâm chuyển đi)");
        $sheet->setCellValue("U7", "CM (Trung tâm chuyển đi)");
        $sheet->setCellValue("V7", "Ngày duyệt đến");
        $sheet->setCellValue("W7", "Ngày duyệt đi");
        $sheet->setCellValue("X7", "Người duyệt đi");
        $sheet->setCellValue("Y7", "Ghi chú duyệt đến");
        $sheet->setCellValue("Z7", "Ghi chú duyệt đi");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(15);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(20);
        $sheet->getColumnDimension("H")->setWidth(20);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(20);
        $sheet->getColumnDimension("K")->setWidth(20);
        $sheet->getColumnDimension("L")->setWidth(20);
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(24);
        $sheet->getColumnDimension("O")->setWidth(24);
        $sheet->getColumnDimension("P")->setWidth(24);
        $sheet->getColumnDimension("Q")->setWidth(24);
        $sheet->getColumnDimension("R")->setWidth(24);
        $sheet->getColumnDimension("S")->setWidth(24);
        $sheet->getColumnDimension("T")->setWidth(24);
        $sheet->getColumnDimension("U")->setWidth(24);
        $sheet->getColumnDimension("V")->setWidth(24);
        $sheet->getColumnDimension("W")->setWidth(24);
        $sheet->getColumnDimension("X")->setWidth(24);
        $sheet->getColumnDimension("Y")->setWidth(24);
        $sheet->getColumnDimension("Z")->setWidth(24);

        $pendings = DB::select(DB::raw("SELECT s.*,
                   b.`name` AS branch_name
            FROM `sales_report` AS s
                  INNER JOIN `branches` AS b ON s.`branch_id` = b.`id`
            $where
            GROUP BY s.id desc
            "));
        // dd($pendings);
        for ($i = 0; $i < count($pendings); $i++) {
            $x = $i + 6;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $pendings[$i]->branch_name);
            /*$sheet->setCellValue('C' . $x, $pendings[$i]->stu_id);
            $sheet->setCellValue('D' . $x, $pendings[$i]->accounting_id);
            $sheet->setCellValue('E' . $x, $pendings[$i]->branch_name);
            $sheet->setCellValue('F' . $x, $pendings[$i]->program_name);
            $sheet->setCellValue('G' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('H' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('I' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('J' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('K' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('L' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('M' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('N' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('O' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('P' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('Q' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('R' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('S' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('T' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('U' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('V' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('W' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('X' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('Y' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('Z' . $x, $pendings[$i]->student_name);*/


            $st = "A" . $x;
            $ed = "Z" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
        }

        ProcessExcel::styleCells($spreadsheet, "A7:Z7", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:Z1", "FFFFFF", "black", 11, 1, 3, "left", "left", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:Z2", "FFFFFF", "black", 11, 1, 3, "left", "left", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:Z3", "FFFFFF", "black", 15, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:Z4", "FFFFFF", "black", 10, 1, 3, "center", "center", true, 0);

        // return $students;
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo doanh số.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC11($branch = null, $product = null, $program = null, $customerType = null, $fromDate = null, $toDate = null)
    {

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));
        // dd($new_date);


        $branches = $branch;
        $products = $product;
        $programs = $program;
        $customerTypes = $customerType;
        $from_date = $fromDate;
        $to_date = $toDate;

        $fromDate = strtotime($from_date);
        $toDate = strtotime($to_date);

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = $today;
        }

        $where = "";

        if ($branches && $branches != '_') {

            $where .= " AND c.branch_id in ($branches)";
        }

        if ($products && $products != '_') {

            $where .= "  AND c.product_id in ($products)";
        }


        if ($programs && $programs != '_') {

            $where .= "  AND c.program_id in ($programs)";
        }

        if ($customerTypes && $customerTypes != '_') {

            $where .= " AND c.type in ($customerTypes)";
        }

        $where .= " AND (c.created_at BETWEEN '$from_date' AND '$to_date')";
        /*if ($fromDate) {
            $where .= " AND c.created_at >= '$from_date' ";
        }


        if ($toDate) {
            $where .= " AND c.created_at <= '$to_date' ";
        }*/

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN ANH NGỮ APAX');
        $sheet->setCellValue('A2', 'Tầng 7, Tòa nhà 14 Láng Hạ, Ba Đình, Hà Nội');
        $sheet->setCellValue('A3', 'BÁO CÁO CHUYỂN LỚP - CHUYỂN TRUNG TÂM');
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:P3');
        $sheet->getRowDimension('1')->setRowHeight(20);
        $sheet->getRowDimension('2')->setRowHeight(20);
        $sheet->getRowDimension('3')->setRowHeight(50);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $sheet->setCellValue('A4', 'NGÀY : ' . date('d-m-Y H:i:s'));
        $sheet->mergeCells('A4:P4');
        // $sheet->mergeCells('A3:M3');


        // $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A7", "STT");
        $sheet->setCellValue("B7", "Ngày thực hiện");
        $sheet->setCellValue("C7", "Người thực hiện");
        $sheet->setCellValue("D7", "Ngày điều chuyển");
        $sheet->setCellValue("E7", "Mã LMS");
        $sheet->setCellValue("F7", "Học sinh");
        $sheet->setCellValue("G7", "Mức học phí còn lại");
        $sheet->setCellValue("H7", "Số buổi còn lại");
        $sheet->setCellValue("I7", "Trình độ");
        $sheet->setCellValue("J7", "Trung tâm chuyển đi");
        $sheet->setCellValue("K7", "Lớp chuyển đi");
        $sheet->setCellValue("L7", "Trung tâm chuyển đến");
        $sheet->setCellValue("M7", "Lớp chuyển đến");
        $sheet->setCellValue("N7", "Mã EFFECT TT chuyển đi");
        $sheet->setCellValue("O7", "Mã EFFECT TT chuyển đến");
        $sheet->setCellValue("P7", "Trạng thái");
        $sheet->setCellValue("Q7", "Lý do từ chối nếu có");
        $sheet->setCellValue("R7", "EC (Trung tâm chuyển đến)");
        $sheet->setCellValue("S7", "CM (Trung tâm chuyển đến)");
        $sheet->setCellValue("T7", "EC (Trung tâm chuyển đi)");
        $sheet->setCellValue("U7", "CM (Trung tâm chuyển đi)");
        $sheet->setCellValue("V7", "Ngày duyệt đến");
        $sheet->setCellValue("W7", "Ngày duyệt đi");
        $sheet->setCellValue("X7", "Người duyệt đi");
        $sheet->setCellValue("Y7", "Ghi chú duyệt đến");
        $sheet->setCellValue("Z7", "Ghi chú duyệt đi");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(15);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(20);
        $sheet->getColumnDimension("H")->setWidth(20);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(20);
        $sheet->getColumnDimension("K")->setWidth(20);
        $sheet->getColumnDimension("L")->setWidth(20);
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(24);
        $sheet->getColumnDimension("O")->setWidth(24);
        $sheet->getColumnDimension("P")->setWidth(24);
        $sheet->getColumnDimension("Q")->setWidth(24);
        $sheet->getColumnDimension("R")->setWidth(24);
        $sheet->getColumnDimension("S")->setWidth(24);
        $sheet->getColumnDimension("T")->setWidth(24);
        $sheet->getColumnDimension("U")->setWidth(24);
        $sheet->getColumnDimension("V")->setWidth(24);
        $sheet->getColumnDimension("W")->setWidth(24);
        $sheet->getColumnDimension("X")->setWidth(24);
        $sheet->getColumnDimension("Y")->setWidth(24);
        $sheet->getColumnDimension("Z")->setWidth(24);

        $sql = "SELECT
                s.id student_id,
                s.name student_name,
                s.type student_type,
                s.accounting_id as effect_id,
                s.stu_id lms_id,
                if(s.type=1, 'VIP', 'Bình thường') customer_type,
                (SELECT `name` FROM tuition_fee WHERE id = c.tuition_fee_id) tuition_fee_name,
                (SELECT `name` FROM users WHERE id = c.ec_id) ec_name,
                (SELECT `name` FROM users WHERE id = c.cm_id) cm_name,
                (SELECT `name` FROM products WHERE id = c.product_id) product_name,
            (SELECT `name` FROM programs WHERE id = c.program_id) program_name,
                (SELECT `name` FROM branches WHERE id = c.branch_id) branch_name,
                c.real_sessions remain_sessions,
                c.must_charge,
                c.debt_amount,
                c.total_charged
            FROM contracts c
                LEFT JOIN students s ON c.student_id = s.id
            WHERE  c.count_recharge >= 0 AND s.status > 0 $where GROUP BY s.id";
        //echo $sql; exit();
        $pendings = DB::select(DB::raw($sql));
        for ($i = 0; $i < count($pendings); $i++) {
            $x = $i + 6;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $pendings[$i]->student_name);
            /*$sheet->setCellValue('C' . $x, $pendings[$i]->stu_id);
            $sheet->setCellValue('D' . $x, $pendings[$i]->accounting_id);
            $sheet->setCellValue('E' . $x, $pendings[$i]->branch_name);
            $sheet->setCellValue('F' . $x, $pendings[$i]->program_name);
            $sheet->setCellValue('G' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('H' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('I' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('J' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('K' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('L' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('M' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('N' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('O' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('P' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('Q' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('R' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('S' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('T' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('U' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('V' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('W' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('X' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('Y' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('Z' . $x, $pendings[$i]->student_name);*/


            $st = "A" . $x;
            $ed = "Z" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
        }

        ProcessExcel::styleCells($spreadsheet, "A7:Z7", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:Z1", "FFFFFF", "black", 11, 1, 3, "left", "left", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:Z2", "FFFFFF", "black", 11, 1, 3, "left", "left", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:Z3", "FFFFFF", "black", 15, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:Z4", "FFFFFF", "black", 10, 1, 3, "center", "center", true, 0);

        // return $students;
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO CHUYỂN LỚP - CHUYỂN TRUNG TÂM.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC12($branch = null, $product = null, $program = null, $customerType = null, $fromDate = null, $toDate = null)
    {

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));
        // dd($new_date);


        $branches = $branch;
        $programs = $program;
        $customerTypes = $customerType;
        $from_date = $fromDate;
        $to_date = $toDate;

        $fromDate = strtotime($from_date);
        $toDate = strtotime($to_date);

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = $today;
        }

        $where = "";

        if ($branches && $branches != '_') {

            $where .= " AND br.id in ($branches)";
        }

        if ($programs && $programs != '_') {

            $where .= " AND pr.id in ($programs)";
        }

        if ($customerTypes && $customerTypes != '_') {

            $where .= " AND ct.type in ($customerTypes)";
        }

        if ($fromDate) {
            $where .= " AND pd.created_at >= '$from_date' ";
        }

        if ($toDate) {
            $where .= " AND pd.created_at <= '$to_date' ";
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO CHUYỂN PHÍ');
        $sheet->mergeCells('A1:N1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $sheet->setCellValue('A2', 'NGÀY : ' . date('d-m-Y'));
        $sheet->mergeCells('A2:N2');
        // $sheet->mergeCells('A3:M3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "TÊN HỌC SINH");
        $sheet->setCellValue("C5", "MÃ LMS");
        $sheet->setCellValue("D5", "MÃ EFFECT");
        $sheet->setCellValue("E5", "TRUNG TÂM HỌC SINH");
        $sheet->setCellValue("F5", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("G5", "LỚP");
        $sheet->setCellValue("H5", "GÓI HỌC PHÍ");
        $sheet->setCellValue("I5", "GIÁ NIÊM YẾT");
        $sheet->setCellValue("J5", "SỐ TIỀN THỰC THU");
        $sheet->setCellValue("K5", "SỐ TIỀN ĐÃ ĐÓNG");
        $sheet->setCellValue("L5", "CÔNG NỢ");
        $sheet->setCellValue("M5", "NGÀY ĐĂNG KÝ HỌC");
        $sheet->setCellValue("N5", "SỐ NGÀY CHỜ XẾP LỚP");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(15);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(20);
        $sheet->getColumnDimension("H")->setWidth(20);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(20);
        $sheet->getColumnDimension("K")->setWidth(20);
        $sheet->getColumnDimension("L")->setWidth(20);
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(24);


        $branch_id = '';
        if ($branch != '_') {
            $branch_id .= '(' . $branch . ')';
        }


        $where = '';

        if ($branch_id) {
            $where .= "and ct.branch_id in $branch_id ";
        }

        /*$c = '';
        if ($where) {
            $where = substr($where, 3);
            $c .= 'AND ' . $where;
        }*/
        // return $c;

        $pendings = DB::select(DB::raw("SELECT pd.id as pending_id,
                                                pd.created_at as pending_date,
                                                st.name as student_name,
                                                st.accounting_id,
                                                st.stu_id,
                                                br.name as branch_name,
                                                pr.name as program_name,
                                                tui.name as tuition_name,
                                                tui.price as tuition_price,
                                                pm.must_charge,
                                                pm.total,
                                                pm.debt,
                                                pm.created_at
                                            FROM pendings as pd
                                                LEFT JOIN students as st on st.id = pd.student_id
                                                LEFT JOIN contracts as ct on ct.id = pd.contract_id
                                                LEFT JOIN branches as br on br.id = ct.branch_id
                                                LEFT JOIN tuition_fee as tui on tui.id = ct.tuition_fee_id
                                                LEFT JOIN programs as pr on pr.id = ct.program_id
                                                LEFT JOIN payment as pm on pm.contract_id = ct.id
                                            WHERE br.id > 0 $where"));

        for ($i = 0; $i < count($pendings); $i++) {
            $x = $i + 6;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('C' . $x, $pendings[$i]->stu_id);
            $sheet->setCellValue('D' . $x, $pendings[$i]->accounting_id);
            $sheet->setCellValue('E' . $x, $pendings[$i]->branch_name);
            $sheet->setCellValue('F' . $x, $pendings[$i]->program_name);
            $sheet->setCellValue('G' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('H' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('I' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('J' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('K' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('L' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('M' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('N' . $x, $pendings[$i]->student_name);


            $st = "A" . $x;
            $ed = "N" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
        }

        ProcessExcel::styleCells($spreadsheet, "A5:N5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:N1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:N2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:N3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:N4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        // return $students;
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO CHUYỂN PHÍ.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC13(Request $request, $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN ANH NGỮ APAX');
        $sheet->mergeCells('A1:I1');
        ProcessExcel::styleCells($spreadsheet, "A1:I1", "ffffff", "000", 9, 1, true, "left", "center", false);

        $sheet->setCellValue('A2', 'Tầng 7, Tòa nhà 14 Láng Hạ, Ba Đình, Hà Nội');
        $sheet->mergeCells('A2:I2');
        ProcessExcel::styleCells($spreadsheet, "A2:I2", "ffffff", "000", 9, 1, true, "left", "center", false);

        $sheet->setCellValue('A3', 'BÁO CÁO CÔNG NỢ');
        $sheet->mergeCells('A3:I3');
        ProcessExcel::styleCells($spreadsheet, "A3:Q3", "ffffff", "000", 16, 1, true, "center", "center", false);

        $sheet->setCellValue('A4', 'Ngày : ' . date('d-m-Y'));
        $sheet->mergeCells('A4:I4');
        ProcessExcel::styleCells($spreadsheet, "A4:Q4", "ffffff", "000", 9, 0, true, "center", "center", false);

        $sheet->getRowDimension('1')->setRowHeight(18);
        $sheet->getRowDimension('2')->setRowHeight(18);
        $sheet->getRowDimension('3')->setRowHeight(25);
        $sheet->getRowDimension('4')->setRowHeight(18);
        $sheet->getRowDimension('5')->setRowHeight(30);


        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "MÃ LMS");
        $sheet->setCellValue("C5", "MÃ EFFECT");
        $sheet->setCellValue("D5", "TÊN HỌC SINH");
        $sheet->setCellValue("E5", "TRUNG TÂM");
        $sheet->setCellValue("F5", "SẢN PHẨM");
        $sheet->setCellValue("G5", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("H5", "LỚP");
        $sheet->setCellValue("I5", "GÓI HỌC PHÍ");
        $sheet->setCellValue("J5", "LOẠI THU PHÍ");
        $sheet->setCellValue("K5", "GIÁ NIÊM YẾT");
        $sheet->setCellValue("L5", "TỔNG SỐ TIỀN GIẢM TRỪ");
        $sheet->setCellValue("M5", "SỐ TIỀN THỰC THU");
        $sheet->setCellValue("N5", "SỐ TIỀN ĐÃ ĐÓNG");
        $sheet->setCellValue("O5", "CÔNG NỢ");
        $sheet->setCellValue("P5", "NGÀY ĐÓNG TIỀN \n(gần nhất)");
        $sheet->setCellValue("Q5", "NGƯỜI THU TIỀN \n(gần nhất)");

        $sheet->getColumnDimension("A")->setWidth(5);
        $sheet->getColumnDimension("B")->setWidth(20);
        $sheet->getColumnDimension("C")->setWidth(23);
        $sheet->getColumnDimension("D")->setWidth(27);
        $sheet->getColumnDimension("E")->setWidth(29);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(20);
        $sheet->getColumnDimension("H")->setWidth(35);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(20);
        $sheet->getColumnDimension("K")->setWidth(20);
        $sheet->getColumnDimension("L")->setWidth(20);
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(24);
        $sheet->getColumnDimension("O")->setWidth(24);
        $sheet->getColumnDimension("P")->setWidth(24);
        $sheet->getColumnDimension("Q")->setWidth(24);

        ProcessExcel::styleCells($spreadsheet, "A5:Q5", "add8e6", "000", 9, 1, true, "center", "center", true);

        $da = json_decode($data);
        $branch_ids = $da->branches;
        $product_ids = $da->products;

        $conditions = [];

        $conditions[] = "c.debt_amount > 0";
        if (empty($branch_ids)) {
            $token = $da->tk;
            $response = (object)[];
            $response->message = 'invalid session';
            $response->status = 200;
            $request->authorized = null;
            $check = false;
            $sinformation = (object)[];
            if ($token !== null) {
                $session = Redis::get($token);
                if ($session) {
                    $sinformation = json_decode($session);
                    $request->authorized = $session;
                    $request->users_data = $sinformation;
                    $check = true;
                }
            }
            if (!$check) {
                $response = [
                    'code' => 403,
                    'message' => 'Permission denied...',
                    'data' => null
                ];
                exit(json_encode($response));
            }

            $branch_ids = u::getBranchIds($request->users_data);
        } else {

            $branch_ids_term = [];
            foreach ($branch_ids as $key => $value) {
                array_push($branch_ids_term, $value->id);
            }
            $branch_ids = $branch_ids_term;
        }
        $branch_ids_string = implode(',', $branch_ids);
        $conditions[] = "c.`branch_id` IN ($branch_ids_string)";

        if (!empty($product_ids)) {
            $product_ids_string = implode(',', $product_ids);
            $conditions[] = "c.`product_id` IN ($product_ids_string)";
        }

        $conditions_string = "";
        if (!empty($conditions)) {
            $conditions_string = " WHERE " . implode(' AND ', $conditions);
        }

        $query = "
            SELECT
                s.name AS student_name,
                s.stu_id AS lms_id,
                s.accounting_id AS accounting_id,
                b.name AS branch_name,
                prd.name AS product_name,
                prg.name AS program_name,
                tf.name AS tuition_fee_name,
                c.payload AS payload,
                c.`must_charge` AS must_charge,
                c.`total_charged` AS total_charged,
                c.debt_amount AS debt_amount,
                IF(p.id, p.created_at, '') AS newest_payment_time,
                IF(u.`id`, u.`username`, '') AS creator,
                IF(cls.`id`, cls.`cls_name`, '') AS class_name,
                c.tuition_fee_price AS tuition_fee_price
            FROM
                contracts AS c
                LEFT JOIN students AS s ON c.student_id = s.id
                LEFT JOIN branches AS b ON c.branch_id = b.id
                LEFT JOIN products AS prd ON c.product_id = prd.id
                LEFT JOIN programs AS prg ON c.program_id = prg.id
                LEFT JOIN tuition_fee AS tf ON c.tuition_fee_id = tf.id
                LEFT JOIN (
                    SELECT id, creator_id, created_at, contract_id FROM payment WHERE id IN (
                        SELECT MAX(id) FROM payment GROUP BY contract_id
                    )
                ) AS p ON c.id = p.contract_id
                LEFT JOIN users AS u ON p.creator_id = u.id
                LEFT JOIN enrolments AS e ON c.`enrolment_id` = e.`id`
                LEFT JOIN classes AS cls ON e.`class_id` = cls.`id`
            $conditions_string
            GROUP BY c.id
        ";

        $pendings = DB::select(DB::raw($query));

        for ($i = 0; $i < count($pendings); $i++) {
            $x = $i + 6;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $pendings[$i]->lms_id);
            $sheet->setCellValue('C' . $x, $pendings[$i]->accounting_id);
            $sheet->setCellValue('D' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('E' . $x, $pendings[$i]->branch_name);
            $sheet->setCellValue('F' . $x, $pendings[$i]->product_name);
            $sheet->setCellValue('G' . $x, $pendings[$i]->program_name);
            $sheet->setCellValue('H' . $x, $pendings[$i]->class_name);
            $sheet->setCellValue('I' . $x, $pendings[$i]->tuition_fee_name);
            $sheet->setCellValue('J' . $x, $pendings[$i]->payload ? "Nhiều lần" : "Một lần");
            $sheet->setCellValue('K' . $x, apax_ada_format_number($pendings[$i]->tuition_fee_price));
            $sheet->setCellValue('L' . $x, apax_ada_format_number($pendings[$i]->tuition_fee_price - $pendings[$i]->must_charge));
            $sheet->setCellValue('M' . $x, apax_ada_format_number($pendings[$i]->must_charge));
            $sheet->setCellValue('N' . $x, apax_ada_format_number($pendings[$i]->total_charged));
            $sheet->setCellValue('O' . $x, apax_ada_format_number($pendings[$i]->debt_amount));
            $sheet->setCellValue('P' . $x, $pendings[$i]->newest_payment_time);
            $sheet->setCellValue('Q' . $x, $pendings[$i]->creator);


            ProcessExcel::styleCells($spreadsheet, "A6:A$x", "ffffff", "000", 9, 0, true, "right", "center", false);
            ProcessExcel::styleCells($spreadsheet, "B6:B$x", "ffffff", "000", 9, 0, true, "left", "center", false);
            ProcessExcel::styleCells($spreadsheet, "C6:C$x", "ffffff", "000", 9, 0, true, "right", "center", false);
            ProcessExcel::styleCells($spreadsheet, "D6:J$x", "ffffff", "000", 9, 0, true, "left", "center", false);
            ProcessExcel::styleCells($spreadsheet, "K6:O$x", "ffffff", "000", 9, 0, true, "right", "center", false);
            ProcessExcel::styleCells($spreadsheet, "P6:Q$x", "ffffff", "000", 9, 0, true, "left", "center", false);

        }

        // return $students;
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO CÔNG NỢ.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC14(Request $request, $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN ANH NGỮ APAX');
        $sheet->mergeCells('A1:I1');
        ProcessExcel::styleCells($spreadsheet, "A1:I1", "ffffff", "000", 9, 1, true, "left", "center", false);

        $sheet->setCellValue('A2', 'Tầng 7, Tòa nhà 14 Láng Hạ, Ba Đình, Hà Nội');
        $sheet->mergeCells('A2:I2');
        ProcessExcel::styleCells($spreadsheet, "A2:I2", "ffffff", "000", 9, 1, true, "left", "center", false);

        $sheet->setCellValue('A3', 'BÁO CÁO PENDING');
        $sheet->mergeCells('A3:I3');
        ProcessExcel::styleCells($spreadsheet, "A3:P3", "ffffff", "000", 16, 1, true, "center", "center", false);

        $sheet->setCellValue('A4', 'Ngày: ' . date('d-m-Y'));
        $sheet->mergeCells('A4:I4');
        ProcessExcel::styleCells($spreadsheet, "A4:P4", "ffffff", "000", 9, 0, true, "center", "center", false);

        $sheet->getRowDimension('1')->setRowHeight(18);
        $sheet->getRowDimension('2')->setRowHeight(18);
        $sheet->getRowDimension('3')->setRowHeight(25);
        $sheet->getRowDimension('4')->setRowHeight(18);
        $sheet->getRowDimension('5')->setRowHeight(30);


        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "MÃ LMS");
        $sheet->setCellValue("C5", "MÃ EFFECT");
        $sheet->setCellValue("D5", "TÊN HỌC SINH");
        $sheet->setCellValue("E5", "TRUNG TÂM");
        $sheet->setCellValue("F5", "SẢN PHẨM");
        $sheet->setCellValue("G5", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("H5", "GÓI HỌC PHÍ");
        $sheet->setCellValue("I5", "GIÁ NIÊM YẾT");
        $sheet->setCellValue("J5", "SỐ TIỀN THỰC THU");
        $sheet->setCellValue("K5", "SỐ TIỀN ĐÃ ĐÓNG");
        $sheet->setCellValue("L5", "CÔNG NỢ");
        $sheet->setCellValue("M5", "NGÀY NHẬP HỌC");
        $sheet->setCellValue("N5", "NGÀY BẮT ĐẦU PENDING");
        $sheet->setCellValue("O5", "NGÀY KẾT THÚC PENDING");
        $sheet->setCellValue("P5", "SỐ NGÀY CHỜ XẾP LỚP");


        $sheet->getColumnDimension("A")->setWidth(5);
        $sheet->getColumnDimension("B")->setWidth(20);
        $sheet->getColumnDimension("C")->setWidth(23);
        $sheet->getColumnDimension("D")->setWidth(27);
        $sheet->getColumnDimension("E")->setWidth(29);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(20);
        $sheet->getColumnDimension("H")->setWidth(35);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(20);
        $sheet->getColumnDimension("K")->setWidth(20);
        $sheet->getColumnDimension("L")->setWidth(20);
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(24);
        $sheet->getColumnDimension("O")->setWidth(24);
        $sheet->getColumnDimension("P")->setWidth(24);


        ProcessExcel::styleCells($spreadsheet, "A5:P5", "add8e6", "000", 9, 1, true, "center", "center", true);

        $da = json_decode($data);
        $branch_ids = $da->branches;
        $product_ids = $da->products;
        $date = $da->date;

        $conditions = [];

        if (!$date) {
            $date = date('Y-m-d');
        }
        $conditions[] = "p.end_date >= '$date'";

        if (empty($branch_ids)) {
            $token = $da->tk;
            $response = (object)[];
            $response->message = 'invalid session';
            $response->status = 200;
            $request->authorized = null;
            $check = false;
            $sinformation = (object)[];
            if ($token !== null) {
                $session = Redis::get($token);
                if ($session) {
                    $sinformation = json_decode($session);
                    $request->authorized = $session;
                    $request->users_data = $sinformation;
                    $check = true;
                }
            }
            if (!$check) {
                $response = [
                    'code' => 403,
                    'message' => 'Permission denied...',
                    'data' => null
                ];
                exit(json_encode($response));
            }

            $branch_ids = u::getBranchIds($request->users_data);
        }
        $branch_ids_string = implode(',', $branch_ids);
        $conditions[] = "p.`branch_id` IN ($branch_ids_string)";

        if (!empty($product_ids)) {
            $product_ids_string = implode(',', $product_ids);
            $conditions[] = "p.`product_id` IN ($product_ids_string)";
        }

        $conditions_string = "";
        if (!empty($conditions)) {
            $conditions_string = " WHERE " . implode(' AND ', $conditions);
        }

        $query = "
            SELECT
                s.name AS student_name,
                s.stu_id AS lms_id,
                s.accounting_id AS accounting_id,
                b.name AS branch_name,
                prd.name AS product_name,
                prg.name AS program_name,
                tf.name AS tuition_fee_name,
                c.tuition_fee_price AS tuition_fee_price,
                c.must_charge AS must_charge,
                c.total_charged AS total_charged,
                c.debt_amount AS debt_amount,
                c.start_date AS start_date,
                p.start_date AS pending_date,
                p.end_date as pending_end_date,
                p.session AS sessions
            FROM
                pendings AS p
                LEFT JOIN students AS s ON p.student_id = s.id
                LEFT JOIN contracts AS c ON p.contract_id = c.id
                LEFT JOIN branches AS b ON p.branch_id = b.id
                LEFT JOIN products AS prd ON p.product_id = prd.id
                LEFT JOIN programs AS prg ON p.program_id = prg.id
                LEFT JOIN tuition_fee AS tf ON c.tuition_fee_id = tf.id
            $conditions_string
            GROUP BY p.contract_id
        ";

//        echo $query;
        $pendings = DB::select(DB::raw($query));

        for ($i = 0; $i < count($pendings); $i++) {
            $x = $i + 6;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $pendings[$i]->lms_id);
            $sheet->setCellValue('C' . $x, $pendings[$i]->accounting_id);
            $sheet->setCellValue('D' . $x, $pendings[$i]->student_name);
            $sheet->setCellValue('E' . $x, $pendings[$i]->branch_name);
            $sheet->setCellValue('F' . $x, $pendings[$i]->product_name);
            $sheet->setCellValue('G' . $x, $pendings[$i]->program_name);
            $sheet->setCellValue('H' . $x, $pendings[$i]->tuition_fee_name);
            $sheet->setCellValue('I' . $x, apax_ada_format_number($pendings[$i]->tuition_fee_price));
            $sheet->setCellValue('J' . $x, apax_ada_format_number($pendings[$i]->must_charge));
            $sheet->setCellValue('K' . $x, apax_ada_format_number($pendings[$i]->total_charged));
            $sheet->setCellValue('L' . $x, apax_ada_format_number($pendings[$i]->debt_amount));
            $sheet->setCellValue('M' . $x, $pendings[$i]->start_date);
            $sheet->setCellValue('N' . $x, $pendings[$i]->pending_date);
            $sheet->setCellValue('O' . $x, $pendings[$i]->pending_end_date);
            $sheet->setCellValue('P' . $x, $pendings[$i]->sessions);


//            $st = "A" . $x;
//            $ed = "Q" . $x;
            ProcessExcel::styleCells($spreadsheet, "A6:A$x", "ffffff", "000", 9, 0, true, "right", "center", false);
            ProcessExcel::styleCells($spreadsheet, "B6:B$x", "ffffff", "000", 9, 0, true, "left", "center", false);
            ProcessExcel::styleCells($spreadsheet, "C6:C$x", "ffffff", "000", 9, 0, true, "right", "center", false);
            ProcessExcel::styleCells($spreadsheet, "D6:H$x", "ffffff", "000", 9, 0, true, "left", "center", false);
            ProcessExcel::styleCells($spreadsheet, "I6:L$x", "ffffff", "000", 9, 0, true, "right", "center", false);
            ProcessExcel::styleCells($spreadsheet, "M6:O$x", "ffffff", "000", 9, 0, true, "right", "center", false);
            ProcessExcel::styleCells($spreadsheet, "P6:Q$x", "ffffff", "000", 9, 0, true, "right", "center", false);

        }
        // return $students;
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO PENDING.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC15($branches = null, $product = null, $program = null)
    {

        $branch = $branches;
        $products = $product;
        $programs = $program;

        $where = null;

        if ($products && $products != '_') {
            $where .= " AND c.product_id IN ($products)";
        }

        if ($programs && $programs != '_') {
            $where .= " AND c.program_id IN ($programs)";
        }

        $where_branch = null;

        if ($branch && $branch != '_') {
            $where .= "AND c.branch_id IN ($branch)";
        }


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->freezePane('C5');
        $sheet->setTitle("Iutput");

        $sheet->setCellValue('A1', 'BÁO CÁO QUÁ HẠN CỌC');
        $sheet->mergeCells('A1:P1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $sheet->setCellValue('A2', 'NGÀY : ' . date('d-m-Y'));
        $sheet->mergeCells('A2:P2');
        // $sheet->mergeCells('A3:M3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "TÊN HỌC SINH");
        $sheet->setCellValue("C5", "MÃ LMS");
        $sheet->setCellValue("D5", "MÃ EFFECT");
        $sheet->setCellValue("E5", "TRUNG TÂM HỌC SINH");
        $sheet->setCellValue("F5", "CHƯƠNG TRÌNH");
        $sheet->setCellValue("G5", "SẢN PHẨM");
        $sheet->setCellValue("H5", "GÓI HỌC PHÍ");
        $sheet->setCellValue("I5", "GIÁ NIÊM YẾT");
        $sheet->setCellValue("J5", "TIỀN PHẢI ĐÓNG");
        $sheet->setCellValue("K5", "TIỀN ĐÃ ĐÓNG");
        $sheet->setCellValue("L5", "CÔNG NỢ");
        $sheet->setCellValue("M5", "NGÀY THU TIỀN");
        $sheet->setCellValue("N5", "CHỜ HOÀN PHÍ");
        $sheet->setCellValue("O5", "EC");
        $sheet->setCellValue("P5", "CM");


        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(15);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(20);
        $sheet->getColumnDimension("H")->setWidth(20);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(20);
        $sheet->getColumnDimension("K")->setWidth(20);
        $sheet->getColumnDimension("L")->setWidth(20);
        $sheet->getColumnDimension("M")->setWidth(24);
        $sheet->getColumnDimension("N")->setWidth(24);
        $sheet->getColumnDimension("O")->setWidth(24);
        $sheet->getColumnDimension("P")->setWidth(24);

        $q = "SELECT c.id AS contract_id,
        s.accounting_id,
        s.crm_id,
        s.stu_id,
        s.name AS student_name,
        br.name AS branch_name,
        pd.`name` AS product_name,
        pr.`name` AS programs_name,
        u1.username AS ec_name,
        u2.username AS cm_name,
        c.type AS contract_type,
        c.total_charged,
        c.must_charge,
        c.total_discount,
        c.debt_amount,
        c.start_date,
        c.end_date,
        c.total_sessions,
        c.real_sessions,
        c.`status`,
        c.payment_id,
        c.after_discounted_fee,
        c.discount_value,
        c.tuition_fee_price,
        c.done_sessions,
        c.count_recharge,
        c.reserved_sessions,
        pd.NAME AS product_name,
        pr.NAME AS program_name,
        tf.NAME AS tuition_fee_name,
        p.total AS total_amount_charged,
        p.created_at AS payment_date,
        x.dates AS substract,
        15 - x.dates AS left_dates
        FROM contracts AS c
        LEFT JOIN students AS s ON s.id = c.student_id
        LEFT JOIN branches AS br ON br.id = c.branch_id
        LEFT JOIN products AS pd ON pd.id = c.product_id
        LEFT JOIN programs AS pr ON pr.id = c.program_id
        LEFT JOIN tuition_fee AS tf ON tf.id = c.tuition_fee_id
        LEFT JOIN payment AS p ON c.payment_id = p.id
        LEFT JOIN term_student_user AS t1 ON t1.student_id = s.id
        LEFT JOIN users AS u1 ON t1.ec_id = u1.id
        LEFT JOIN users AS u2 ON t1.cm_id = u2.id
        LEFT JOIN
        ( SELECT c.id,TIMESTAMPDIFF(DAY, p.created_at, CURDATE()) AS dates
          FROM contracts AS c
          LEFT JOIN payment AS p ON c.payment_id = p.id
        WHERE c.type > 0
            AND c.payload = 0
            AND c.debt_amount > 0
            AND c.payment_id > 0
            $where
             ) AS x ON x.id = c.id
        WHERE c.type > 0
            AND c.debt_amount > 0
            AND c.payment_id > 0
            $where
            AND x.dates IS NOT NULL
        GROUP BY s.id
        ORDER BY x.dates DESC";
        // echo $q;die;

        $students = u::query($q);

        for ($i = 0; $i < count($students); $i++) {
            $x = $i + 6;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $students[$i]->student_name);
            $sheet->setCellValue('C' . $x, $students[$i]->stu_id);
            $sheet->setCellValue('D' . $x, $students[$i]->accounting_id);
            $sheet->setCellValue('E' . $x, $students[$i]->branch_name);
            $sheet->setCellValue('F' . $x, $students[$i]->program_name);
            $sheet->setCellValue('G' . $x, $students[$i]->product_name);
            $sheet->setCellValue('H' . $x, $students[$i]->tuition_fee_name);
            $sheet->setCellValue('I' . $x, number_format($students[$i]->tuition_fee_price));
            $sheet->setCellValue('J' . $x, number_format($students[$i]->must_charge));
            $sheet->setCellValue('K' . $x, number_format($students[$i]->total_charged));
            $sheet->setCellValue('L' . $x, number_format($students[$i]->debt_amount));
            $sheet->setCellValue('M' . $x, $students[$i]->payment_date);
            $sheet->setCellValue('N' . $x, $students[$i]->left_dates);
            $sheet->setCellValue('O' . $x, $students[$i]->ec_name);
            $sheet->setCellValue('P' . $x, $students[$i]->cm_name);
            // $sheet->setCellValue("A5", "STT");
            // $sheet->setCellValue("B5", "TÊN HỌC SINH");
            // $sheet->setCellValue("C5", "MÃ LMS");
            // $sheet->setCellValue("D5", "MÃ EFFECT");
            // $sheet->setCellValue("E5", "TRUNG TÂM HỌC SINH");
            // $sheet->setCellValue("F5", "CHƯƠNG TRÌNH");
            // $sheet->setCellValue("G5", "SẢN PHẨM");
            // $sheet->setCellValue("H5", "GÓI HỌC PHÍ");
            // $sheet->setCellValue("I5", "GIÁ NIÊM YẾT");
            // $sheet->setCellValue("J5", "TIỀN PHẢI ĐÓNG");
            // $sheet->setCellValue("K5", "TIỀN ĐÃ ĐÓNG");
            // $sheet->setCellValue("L5", "CÔNG NỢ");
            // $sheet->setCellValue("M5", "NGÀY THU TIỀN");
            // $sheet->setCellValue("N5", "CHỜ HOÀN PHÍ");
            // $sheet->setCellValue("O5", "EC");
            // $sheet->setCellValue("P5", "CM");


            $st = "A" . $x;
            $ed = "P" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
        }

        ProcessExcel::styleCells($spreadsheet, "A5:P5", "ffffff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:P1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:P2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:P3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:P4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        // return $students;
        $writer = new Xlsx($spreadsheet);
        $newSheet = $spreadsheet->getActiveSheet()->copy();
        $newSheet->setTitle("Output");
        $spreadsheet->addSheet($newSheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO QUÁ HẠN CỌC.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC16(Request $request, $data)
    {
        $da = json_decode($data);
        $branch_ids = [];
        $date = $da->date ? $da->date : date('Y-m-d');

        if (empty($branch_ids)) {
            $token = $da->tk;
            $response = (object)[];
            $response->message = 'invalid session';
            $response->status = 200;
            $request->authorized = null;
            $check = false;
            $sinformation = (object)[];
            if ($token !== null) {
                $session = Redis::get($token);
                if ($session) {
                    $sinformation = json_decode($session);
                    $request->authorized = $session;
                    $request->users_data = $sinformation;
                    $check = true;
                }
            }
            if (!$check) {
                $response = [
                    'code' => 403,
                    'message' => 'Permission denied...',
                    'data' => null
                ];
                exit(json_encode($response));
            }

            $branch_ids = u::getBranchIds($request->users_data);
        }

        $branch_ids_string = implode(',', $branch_ids);

        $spreadsheet = new Spreadsheet();
        $outputIndex = 0;
        $inputIndex = 1;

        $outputSheet = $spreadsheet->setActiveSheetIndex($outputIndex);
        $outputSheet->setTitle("Output");

        $inputSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Input');
        $spreadsheet->addSheet($inputSheet, $inputIndex);

        ProcessExcel::renderReport16OutputTitle($spreadsheet, $outputSheet, $outputIndex, $date);
        ProcessExcel::renderReport16InputTitle($spreadsheet, $inputSheet, $inputIndex, $date);

        $query = "
            SELECT b.id, b.name, b.opened_date,
             (SELECT total_full_fee('all', 'april', '$date', b.id)) april_full_fee_total,
             (SELECT total_full_fee('active', 'april', '$date', b.id)) april_full_fee_active,
             ((SELECT total_full_fee('all', 'april', '$date', b.id)) - (SELECT total_full_fee('active', 'april', '$date', b.id))) april_full_fee_pending,
             (100 - (SELECT total_full_fee('active', 'april', '$date', b.id))*100/(SELECT total_full_fee('all', 'april', '$date', b.id))) april_percentage_full_fee_pending,
             (SELECT total_deposit_student('april','$date',b.id)) april_deposit,
            (SELECT total_class('april','$date',b.id)) april_class,
            (SELECT total_teachers('april','$date',b.id)) april_teacher,
             (SELECT total_full_fee('all', 'igarten', '$date', b.id)) igarten_full_fee_total,
             (SELECT total_full_fee('active', 'igarten', '$date', b.id)) igarten_full_fee_active,
             ((SELECT total_full_fee('all', 'igarten', '$date', b.id)) - (SELECT total_full_fee('active', 'igarten', '$date', b.id))) igarten_full_fee_pending,
             (100 - (SELECT total_full_fee('active', 'igarten', '$date', b.id))*100/(SELECT total_full_fee('all', 'igarten', '$date', b.id))) igarten_percentage_full_fee_pending,
             (SELECT total_deposit_student('igarten','$date',b.id)) igaten_deposit,
            (SELECT total_class('igarten','$date',b.id)) igarten_class,
            (SELECT total_teachers('igarten','$date',b.id)) igarten_teacher,
             (SELECT total_full_fee('all', 'cdi', '$date', b.id)) cdi_full_fee_total,
             (SELECT total_full_fee('active', 'cdi', '$date', b.id)) cdi_full_fee_active,
             ((SELECT total_full_fee('all', 'cdi', '$date', b.id)) - (SELECT total_full_fee('active', 'cdi', '$date', b.id))) cdi_full_fee_pending,
             (100 - (SELECT total_full_fee('active', 'cdi', '$date', b.id))*100/(SELECT total_full_fee('all', 'cdi', '$date', b.id))) cdi_percentage_full_fee_pending,
             (SELECT total_deposit_student('cdi','$date',b.id)) cdi_deposit,
            (SELECT total_class('cdi','$date',b.id)) cdi_class,
            (SELECT total_teachers('cdi','$date',b.id)) cdi_teacher,
            --  () head_teachers,
             (SELECT
               COUNT(DISTINCT(u.id))
              FROM
               users u
               LEFT JOIN term_user_branch t ON t.user_id = u.id
              WHERE
               t.branch_id = b.id
               AND t.role_id IN (55,56)
               AND u.status > 0
               AND DATE(u.start_date) >= DATE_ADD(DATE('$date'), INTERVAL -2 MONTH)) total_new_cms,
             (SELECT
               COUNT(DISTINCT(u.id))
              FROM
               users u
               LEFT JOIN term_user_branch t ON t.user_id = u.id
              WHERE
               t.branch_id = b.id
               AND t.role_id IN (55,56)
               AND u.status > 0
               AND DATE(u.start_date) <= DATE('$date')) total_all_cms
            FROM branches b
            WHERE b.id IN ($branch_ids_string)
            ORDER BY id ASC
        ";

        $rows = DB::select(DB::raw($query));

        foreach ($rows as $key => $row) {
            ProcessExcel::setReport16InputRow($spreadsheet, $inputSheet, $inputIndex, $row, $key);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BẢNG THEO DÕI HIỆU SUẤT CỦA APAX ENGLISH.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC17a($region = null, $fromDate = null, $toDate = null)
    {
        // dd($branch);
        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));

        $from_date = strtotime($fromDate);
        $to_date = strtotime($toDate);

        if (!$from_date) {
            $fromDate = $default_start_date;
        }
        if (!$to_date) {
            $toDate = $today;
        }

        $where = "";
        if ($region != '_') {
            $where .= " AND r.id in ($region)";
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO PHÂN LOẠI VÙNG');
        $sheet->mergeCells('A1:D1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $sheet->setCellValue('A2', 'NGÀY : ' . date('d-m-Y'));
        $sheet->mergeCells('A2:D2');
        // $sheet->mergeCells('A3:M3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "VÙNG");
        $sheet->setCellValue("C5", "LOẠI");
        $sheet->setCellValue("D5", "BÌNH QUÂN DOANH SỐ VÙNG");


        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(15);
        $sheet->getColumnDimension("D")->setWidth(20);


        $q = "
          SELECT
              SUM( p.amount ) AS total_amount,
              r.name as name,
              r.id as region_id
            FROM
              branches AS b
              LEFT JOIN regions as r ON r.id = b.region_id
              LEFT JOIN contracts AS c ON c.branch_id = b.id
              LEFT JOIN payment AS p ON p.contract_id = c.id
            WHERE
              b.STATUS = 1
              AND (
              CASE

                  WHEN p.created_at IS NOT NULL THEN
                  ( p.created_at BETWEEN '$fromDate' AND '$toDate' ) ELSE ( b.id IS NOT NULL )
                END
                )
                $where
            GROUP BY r.id
        ";
        $branches = DB::select(DB::raw($q));
        $regions = [];
        $defaultRegion = [
            'name' => '',
            'total_amount' => 0,
            'type' => '',
            'c_branch' => 0,
        ];
        foreach ($branches as $branch) {
            if (!isset($regions[$branch->region_id]))
                $regions[$branch->region_id] = $defaultRegion;

            $regions[$branch->region_id]['name'] = $branch->name;
            $regions[$branch->region_id]['total_amount'] =
                ($regions[$branch->region_id]['total_amount'] + $branch->total_amount);
            $regions[$branch->region_id]['c_branch'] = $regions[$branch->region_id]['c_branch'] + 1;
        }

        $configData = ['hnregion1', 'hnregion2', 'hnregion3'];
        $defineType = [
            'hnregion1' => 'Loại 1',
            'hnregion2' => 'Loại 2',
            'hnregion3' => 'Loại 3'
        ];
        $config = Config::whereIn('key', $configData)->select(['key', 'value'])->get()->toArray();

        $i = 0;
        foreach ($regions as $region) {

            if ($region['total_amount'] != null)
                $average = ($region['total_amount'] / $region['c_branch']);
            else
                $average = 0;

            $typeRgKey = '';
            $typeRg = 'Undefined';
            foreach ($config as $cf) {
                $valueCf = explode(',', $cf['value']);
                if ($average >= $valueCf[0] and $average <= $valueCf[1]) {
                    $typeRgKey = $cf['key'];
                    break;
                }
            }

            if (isset($defineType[$typeRgKey])) {
                $typeRg = $defineType[$typeRgKey];
            }

            $x = $i + 6;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $region['name']);
            $sheet->setCellValue('C' . $x, $typeRg);
            $sheet->setCellValue('D' . $x, number_format($average) . 'đ');


            $st = "A" . $x;
            $ed = "D" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
            $i++;
        }

        ProcessExcel::styleCells($spreadsheet, "A5:D5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:D1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:D2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:D3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:D4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        // return $students;
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO PHÂN LOẠI VÙNG.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC17b($branch = null, $fromDate = null, $toDate = null, $best = null, $worst = null)
    {

        $endMonth = date('Y-m-t');
        $default_start_date = (date('Y-m-01'));

        $from_date = strtotime($fromDate);
        $to_date = strtotime($toDate);

        if (!$from_date)
            $fromDate = $default_start_date;

        if (!$to_date)
            $toDate = $endMonth;

        $where = $where_branch = null;
        if ($branch != '_') {
            $where .= " AND b.id in ($branch)";
        }


        $q = "
          SELECT
            b.name,
            SUM(p.amount) as total_amount
          FROM branches as b
          LEFT JOIN contracts as c on c.branch_id = b.id
          LEFT JOIN payment as p on p.contract_id = c.id
          WHERE b.status = 1
          AND (
          CASE WHEN p.created_at IS NOT NULL
            THEN (p.created_at BETWEEN '$fromDate' AND '$toDate' )
            ELSE ( b.id IS NOT NULL )
          END )

          $where
          GROUP BY b.id
        ";

        $res = DB::select(DB::raw($q));

        $configData = ['hnbranch1', 'hnbranch2', 'hnbranch3'];
        $defineType = [
            'hnbranch1' => 'Loại 1',
            'hnbranch2' => 'Loại 2',
            'hnbranch3' => 'Loại 3'
        ];

        $config = Config::whereIn('key', $configData)->select(['key', 'value'])->get()->toArray();

        $max = 0;
        if ($best) {
            $max = $best;
        } elseif ($worst) {
            $max = $worst;
        }

        $count = 1;
        $branches = [];

        if ($max) {
            foreach ($res as $branche) {
                if ($max && ($count <= $max)) {
                    $totalAmount = 0;
                    if ($branche->total_amount != null)
                        $totalAmount = $branche->total_amount;

                    $typeRgKey = '';
                    $typeRg = 'Undefined';
                    foreach ($config as $cf) {
                        $valueCf = explode(',', $cf['value']);
                        if ($totalAmount >= $valueCf[0] and $totalAmount <= $valueCf[1]) {
                            $typeRgKey = $cf['key'];
                            break;
                        }
                    }

                    if (isset($defineType[$typeRgKey])) {
                        $typeRg = $defineType[$typeRgKey];
                    }

                    $branche->type = $typeRg;
                    $branche->total_amount = $totalAmount;
                    $branches[] = $branche;
                    $count++;
                } else {
                    break;
                }
            }
        } else {
            foreach ($res as $branche) {
                $totalAmount = 0;
                if ($branche->total_amount != null)
                    $totalAmount = $branche->total_amount;

                $typeRgKey = '';
                $typeRg = 'Undefined';
                foreach ($config as $cf) {
                    $valueCf = explode(',', $cf['value']);
                    if ($totalAmount >= $valueCf[0] and $totalAmount <= $valueCf[1]) {
                        $typeRgKey = $cf['key'];
                        break;
                    }
                }

                if (isset($defineType[$typeRgKey])) {
                    $typeRg = $defineType[$typeRgKey];
                }

                $branche->type = $typeRg;
                $branche->total_amount = $totalAmount;
                $branches[] = $branche;
                $count++;
            }
        }


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO PHÂN LOẠI TRUNG TÂM');
        $sheet->mergeCells('A1:D1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $sheet->setCellValue('A2', 'NGÀY : ' . date('d-m-Y'));
        $sheet->mergeCells('A2:D2');
        // $sheet->mergeCells('A3:M3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "TRUNG TÂM");
        $sheet->setCellValue("C5", "LOẠI");
        $sheet->setCellValue("D5", "DOANH SỐ");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(15);
        $sheet->getColumnDimension("D")->setWidth(20);


        // return $c;
        // dd($branches);
        for ($i = 0; $i < count($branches); $i++) {
            $x = $i + 6;

            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $branches[$i]->name);
            $sheet->setCellValue('C' . $x, $branches[$i]->type);
            $sheet->setCellValue('D' . $x, number_format($branches[$i]->total_amount, 0, '.', ',') . 'đ');


            $st = "A" . $x;
            $ed = "D" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
        }

        ProcessExcel::styleCells($spreadsheet, "A5:D5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:D1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:D2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:D3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:D4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        // return $students;
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO PHÂN LOẠI TRUNG TÂM.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC17c($branch = null, $fromDate = null, $toDate = null, $bestLeader = null, $worstLeader = null, $bestStaff = null, $worstStaff = null)
    {

        $endMonth = date('Y-m-t');
        $default_start_date = (date('Y-m-01'));

        $from_date = $fromDate;
        $to_date = $toDate;


        $fromDate = strtotime($from_date);
        $toDate = strtotime($to_date);

        if (!$fromDate) {
            $from_date = $default_start_date;
        }
        if (!$toDate) {
            $to_date = $endMonth;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO PHÂN LOẠI NHÂN VIÊN');
        $sheet->mergeCells('A1:F1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $sheet->setCellValue('A2', 'NGÀY : ' . date('d-m-Y'));
        $sheet->mergeCells('A2:F2');
        // $sheet->mergeCells('A3:M3');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "MÃ NHÂN VIÊN");
        $sheet->setCellValue("C5", "TÊN NHÂN VIÊN");
        $sheet->setCellValue("D5", "VỊ TRÍ");
        $sheet->setCellValue("E5", "LOẠI");
        $sheet->setCellValue("F5", "DOANH SỐ");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(15);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(20);


        $where = '';
        if ($branch != '_') {
            $where .= " AND tub.branch_id in ($branch) ";
        }
        $orderBy = " ORDER BY total_amount DESC";
        if ($bestStaff != '_') {
            $orderBy = " ORDER BY total_amount DESC LIMIT $bestStaff";
        }
        if ($worstStaff != '_') {
            $orderBy = " ORDER BY total_amount ASC LIMIT $worstStaff";
        }

        // return $c;
        $q = "
          SELECT
              u.full_name as name,
              sum(p.amount) as total_amount,
              b.name as branch_name,
              u.hrm_id as hrm_id
            FROM users as u

            LEFT JOIN term_user_branch as tub on tub.user_id = u.id
            LEFT JOIN branches as b on b.id = tub.branch_id
            LEFT JOIN roles as r on r.id = tub.role_id
            LEFT JOIN contracts as c on c.ec_id = u.id
            LEFT JOIN payment as p on p.contract_id = c.id

            WHERE r.id in (68,69)
            $where
            AND (
              CASE
                WHEN p.created_at IS NOT NULL THEN
                  ( p.created_at BETWEEN '$from_date' AND '$to_date' ) ELSE ( u.id IS NOT NULL )
                END
            )

            GROUP BY u.id
            $orderBy
        ";

        $ecDatas = DB::select(DB::raw($q));

        $configData = ['hnkeystaff1', 'hnkeystaff2', 'hnkeystaff3'];
        $defineType = [
            'hnkeystaff1' => 'Loại 1',
            'hnkeystaff2' => 'Loại 2',
            'hnkeystaff3' => 'Loại 3'
        ];

        $config = Config::whereIn('key', $configData)->select(['key', 'value'])->get()->toArray();
        foreach ($ecDatas as $ec) {
            $totalAmount = 0;
            if ($ec->total_amount != null)
                $totalAmount = $ec->total_amount;

            $typeRgKey = '';
            $typeRg = 'Undefined';
            foreach ($config as $cf) {
                $valueCf = explode(',', $cf['value']);
                if ($totalAmount >= $valueCf[0] and $totalAmount <= $valueCf[1]) {
                    $typeRgKey = $cf['key'];
                    break;
                }
            }

            if (isset($defineType[$typeRgKey])) {
                $typeRg = $defineType[$typeRgKey];
            }

            $ec->type = $typeRg;
            $ec->total_amount = $totalAmount;
        }

        $i = 0;
        foreach ($ecDatas as $ecData) {
            $x = $i + 6;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $ecData->hrm_id);
            $sheet->setCellValue('C' . $x, $ecData->name);
            $sheet->setCellValue('D' . $x, 'EC');
            $sheet->setCellValue('E' . $x, $ecData->type);
            $sheet->setCellValue('F' . $x, $ecData->total_amount);


            $st = "A" . $x;
            $ed = "F" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
            $i++;
        }

        ProcessExcel::styleCells($spreadsheet, "A5:F5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:F1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:F2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:F3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:F4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        // return $students;
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO PHÂN LOẠI NHÂN VIÊN.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC19($branch = null, $num = null, $dateTime = null)
    {
        $student_number = $num;
        $validDate = strtotime($dateTime);
        if (!$validDate) {
            $dateTime = date('Y-m-d');
        }
        $spreadsheet = new Spreadsheet();
        $columns = [
            ['name' => 'stt', 'label' => "STT", 'width' => 8],
            ['name' => 'branch_name', 'label' => "TRUNG TÂM", 'width' => 25],
            ['name' => 'class_name', 'label' => "LỚP", 'width' => 30],
            ['name' => 'product_name', 'label' => "SẢN PHẨM", 'width' => 25],
            ['name' => 'program_name', 'label' => "CHƯƠNG TRÌNH", 'width' => 25],
            ['name' => 'student_in_class', 'label' => "SỐ HỌC SINH TRONG LỚP", 'width' => 25],
        ];

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Báo cáo số học sinh trong lớp');
        $sheet->mergeCells('A1:F1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $sheet->setCellValue('A2', 'NGÀY: ' . $dateTime);
        $sheet->mergeCells('A2:E2');

        // $sheet->setCellValue('A3', 'Kiểm tra tiếng Việt có dấu !');
        foreach ($columns as $index => $column) {
            $colName = chr($index + 65);
            $sheet->setCellValue("${colName}5", $column['label']);
            $sheet->getColumnDimension($colName)->setWidth($column['width']);
        }

        $where = null;

        if ($branch && $branch != '_') {
            $where .= "AND b.id in ($branch)";
        }
        $q = "
            SELECT
                cl.cls_name AS class_name,
                (
                        SELECT COUNT(*)
                        FROM contracts 
                        WHERE
                        class_id = cl.id
                        AND status IN (1, 2, 3 ,4, 5, 6 )
                    ) AS student_in_class,
                b.name AS branch_name,
                p.name AS program_name,
                pd.name AS product_name
            FROM
                classes AS cl
                LEFT JOIN branches AS b ON b.id = cl.branch_id
                LEFT JOIN programs AS p ON p.id = cl.program_id
                LEFT JOIN term_program_product AS tpp ON tpp.program_id = p.id
                LEFT JOIN products AS pd ON pd.id = tpp.product_id
            WHERE
                cl.cls_iscancelled = 'no'
                AND b.status = 1
                $where
                AND cl.cls_startdate <= '$dateTime' AND cl.cls_enddate >= '$dateTime'
            GROUP BY cl.id";
        $classes = DB::select(DB::raw($q));
        for ($i = 0; $i < count($classes); $i++) {
            $x = $i + 6;
            foreach ($columns as $index => $column) {
                $colName = chr($index + 65);
                $sheet->setCellValue("{$colName}$x", $column['name'] === 'stt' ? $i + 1 : $classes[$i]->{$column['name']});
                ProcessExcel::styleCells($spreadsheet, "{$colName}$x", "FFFFFF", "black", 11, 0, 3, "center", "center", true);
            }
        }

        ProcessExcel::styleCells($spreadsheet, "A5:F5", "4472c4ff", "ffffff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:F1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:F2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:F3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:F4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo số học sinh trong lớp.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC18($branch = null, $product = null, $program = null, $fromDate = null, $toDate = null)
    {

        $from_date = strtotime($fromDate);
        $to_date = strtotime($toDate);

        $endMonth = date('Y-m-t');
        $default_start_date = (date('Y-m-01'));

        if (!$from_date or !$to_date) {
            $fromDate = $default_start_date;
            $toDate = $endMonth;
            $dateMonthFromPrev = date('Y-m-01', strtotime('first day of last month'));
            $dateMonthToPrev = date('Y-m-t', strtotime('first day of last month'));
        } else {
            $dateMonthFromPrev = date('Y-m-01', strtotime('first day of last month', strtotime($fromDate)));
            $dateMonthToPrev = date('Y-m-t', strtotime('first day of last month', strtotime($toDate)));
        }

        $where = $wherePP = null;

        if ($product != '_') {
            $wherePP .= " AND ct.program_id in ($product)";
        }

        if ($program != '_')
            $wherePP .= " AND c.program_id IN ($program)";

        if ($branch != '_')
            $where .= " AND b.id IN ($branch)";

        //$where .= " AND (c.created_at BETWEEN '$from_date' AND '$to_date')";

        $whereQr = 'br.b_id = b.id';
        $inMonth = Student::getSqlStudentFullfee($fromDate, $toDate, '', $whereQr, $wherePP);
        $inMonthPrev = Student::getSqlStudentFullfee($dateMonthFromPrev, $dateMonthToPrev, '', $whereQr, $wherePP);

        $q = "SELECT
                  b.name as branch_name,
                  ( $inMonth ) AS total_student,
                  ( $inMonthPrev ) AS total_student_prev
                FROM
                  branches AS b WHERE b.status = 1 $where";
//              echo $q;die;
        $branches = DB::select(DB::raw($q));

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Báo cáo số học sinh Tăng Net');
        $sheet->mergeCells('A1:J1');
        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(30);

        $sheet->setCellValue('A2', 'TỪ NGÀY: ' . $fromDate . ' - ' . 'ĐẾN NGÀY : ' . $toDate);
        $sheet->mergeCells('A2:J2');

        // $sheet->setCellValue('A3', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "TRUNG TÂM");
        $sheet->setCellValue("C5", "SỐ HỌC SINH THÁNG TRƯỚC");
        $sheet->setCellValue("D5", "TỔNG SỐ HỌC SINH HIỆN TẠI");
        $sheet->setCellValue("E5", "TỔNG SỐ HỌC SINH TĂNG NET");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(15);
        $sheet->getColumnDimension("D")->setWidth(25);
        $sheet->getColumnDimension("E")->setWidth(25);


        for ($i = 0; $i < count($branches); $i++) {
            $x = $i + 6;

            $net = ($branches[$i]->total_student - $branches[$i]->total_student_prev);

            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $branches[$i]->branch_name);
            $sheet->setCellValue('C' . $x, $branches[$i]->total_student_prev);
            $sheet->setCellValue('D' . $x, $branches[$i]->total_student);
            $sheet->setCellValue('E' . $x, $net);


            $st = "A" . $x;
            $ed = "E" . $x;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
        }

        ProcessExcel::styleCells($spreadsheet, "A5:E5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:E1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:E2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:E3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:E4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        // return [$c, $branches];
        // return $branches;
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo số học sinh Tăng Net.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function reportBC20($branches = null, $fromDate = null, $toDate = null)
    {

        $from_date = $fromDate;
        $to_date = $toDate;

        $validFromdate = strtotime($fromDate);
        $validTodate = strtotime($toDate);

        $whereBrc = '';
        if ($branches != '_') {
            $whereBrc = " And b.id in ($branches)";
        }

        if ($validFromdate and $validTodate) {
            $whereCm2m = date('Y-m-d', strtotime(' -2 month', strtotime($from_date)));
            $from_dof_date = date('Y-m-d', strtotime($from_date));
            $to_dof_date = date('Y-m-d', strtotime($to_date));
            $from_dof_date_prev = date('Y-m-01', strtotime(' -1 month', strtotime($from_date)));
            $to_dof_date_prev = date('Y-m-t', strtotime(' -1 month', strtotime($to_date)));
        } else {
            $whereCm2m = date('Y-m-d', strtotime(' -2 month'));
            $from_dof_date = date('Y-m-01');
            $to_dof_date = date('Y-m-t');
            $from_dof_date_prev = date('Y-m-01', strtotime(' -1 month'));
            $to_dof_date_prev = date('Y-m-t', strtotime(' -1 month'));
        }

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->freezePane('C6');
        // $sheet->setTitle("Iutput");
        // $sheet->getStyle('A5:AG5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('add8e6');
        $spreadsheet->getActiveSheet()->getStyle('C3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('D3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('Z4')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AA4')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AB4')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AC3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AD3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AF3')->getAlignment()->setWrapText(true);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO SỐ LIỆU HỆ THỐNG - THÁNG ' . date('Y-m', strtotime($to_dof_date)));
        $sheet->mergeCells('A1:M1');

        $sheet->mergeCells('E3:M3');
        $sheet->mergeCells('N3:S3');
        $sheet->mergeCells('T3:Y3');
        $sheet->mergeCells('Z3:AB3');

        $sheet->mergeCells('A3:A5');
        $sheet->mergeCells('B3:B5');
        $sheet->mergeCells('C3:C5');
        $sheet->mergeCells('D3:D5');
        $sheet->mergeCells('AC3:AC5');
        $sheet->mergeCells('AD3:AD5');
        $sheet->mergeCells('AE3:AE5');
        $sheet->mergeCells('AF3:AF5');
        $sheet->mergeCells('AG3:AG5');

        $sheet->mergeCells('Z4:Z5');
        $sheet->mergeCells('AA4:AA5');
        $sheet->mergeCells('AB4:AB5');


        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(50);

        $sheet->setCellValue('A2', 'TỪ NGÀY : ' . $from_dof_date . ' - ĐẾN NGÀY ' . $to_dof_date);
        $sheet->mergeCells('A2:M2');
        $sheet->mergeCells('E4:G4');
        $sheet->mergeCells('H4:J4');
        $sheet->mergeCells('K4:M4');
        $sheet->mergeCells('N4:P4');
        $sheet->mergeCells('Q4:S4');
        $sheet->mergeCells('T4:V4');
        $sheet->mergeCells('W4:Y4');
        $sheet->mergeCells('W4:Y4');


        // $sheet->mergeCells('A3:M3');
        $sheet->setCellValue('E4', 'April');
        $sheet->setCellValue('H4', 'Igarten');
        $sheet->setCellValue('K4', 'CDI 4.0');
        $sheet->setCellValue('N4', 'Tháng ' . date("Y-m", strtotime($to_dof_date_prev)));
        $sheet->setCellValue('Q4', 'Tháng ' . date("Y-m", strtotime($to_dof_date)));
        $sheet->setCellValue('T4', 'Tháng ' . date("Y-m", strtotime($to_dof_date_prev)));
        $sheet->setCellValue('W4', 'Tháng ' . date("Y-m", strtotime($to_dof_date)));


        $sheet->setCellValue('E3', 'Quản lý số lượng HS');
        $sheet->setCellValue('N3', 'ACS');
        $sheet->setCellValue('T3', 'Renew');
        $sheet->setCellValue('Z3', 'Số học sinh Pending');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A3", "Khu vực");
        $sheet->setCellValue("B3", "Trung tâm");
        $sheet->setCellValue("C3", "Tổng số lớp");
        $sheet->setCellValue("D3", "Tổng số học sinh");
        $sheet->setCellValue("E5", "Số lớp");
        $sheet->setCellValue("F5", "Số học sinh");
        $sheet->setCellValue("G5", "Số lớp sĩ số <= 5");
        $sheet->setCellValue("H5", "Số lớp");
        $sheet->setCellValue("I5", "Số học sinh");
        $sheet->setCellValue("J5", "Số lớp sĩ số <= 5");
        $sheet->setCellValue("K5", "Số lớp");
        $sheet->setCellValue("L5", "Số học sinh");
        $sheet->setCellValue("M5", "Số lớp sĩ số <= 5");
        $sheet->setCellValue("N5", "April");
        $sheet->setCellValue("O5", "iGarten");
        $sheet->setCellValue("P5", "CDI 4.0");
        $sheet->setCellValue("Q5", "April");
        $sheet->setCellValue("R5", "iGarten");
        $sheet->setCellValue("S5", "CDI 4.0");
        $sheet->setCellValue("T5", "H/s đến hạn tái phí");
        $sheet->setCellValue("U5", "Tái phí thành công");
        $sheet->setCellValue("V5", "Tỷ lệ");
        $sheet->setCellValue("W5", "H/s đến hạn tái phí");
        $sheet->setCellValue("X5", "Tái phí thành công");
        $sheet->setCellValue("Y5", "Tỷ lệ");
        $sheet->setCellValue("Z4", date('Y-m', strtotime($to_dof_date_prev)));
        $sheet->setCellValue("AA4", date('Y-m', strtotime($to_dof_date)));
        $sheet->setCellValue("AB4", "Tỷ lệ tăng/ giảm");
        $sheet->setCellValue("AC3", "Tổng số CM");
        $sheet->setCellValue("AD3", "Số CM dưới 2 tháng");
        $sheet->setCellValue("AE3", "Số CM cần bổ sung");
        $sheet->setCellValue("AF3", "OMs cần bổ sung");
        $sheet->setCellValue("AG3", "Ghi chú");


        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(35);
        $sheet->getColumnDimension("C")->setWidth(10);
        $sheet->getColumnDimension("D")->setWidth(10);
        $sheet->getColumnDimension("E")->setWidth(10);
        $sheet->getColumnDimension("F")->setWidth(10);
        $sheet->getColumnDimension("G")->setWidth(10);
        $sheet->getColumnDimension("H")->setWidth(10);
        $sheet->getColumnDimension("I")->setWidth(10);
        $sheet->getColumnDimension("J")->setWidth(10);
        $sheet->getColumnDimension("K")->setWidth(10);
        $sheet->getColumnDimension("L")->setWidth(10);
        $sheet->getColumnDimension("M")->setWidth(10);
        $sheet->getColumnDimension("N")->setWidth(10);
        $sheet->getColumnDimension("O")->setWidth(10);
        $sheet->getColumnDimension("P")->setWidth(10);
        $sheet->getColumnDimension("Q")->setWidth(10);
        $sheet->getColumnDimension("R")->setWidth(10);
        $sheet->getColumnDimension("S")->setWidth(10);
        $sheet->getColumnDimension("T")->setWidth(10);
        $sheet->getColumnDimension("U")->setWidth(10);
        $sheet->getColumnDimension("V")->setWidth(10);
        $sheet->getColumnDimension("W")->setWidth(10);
        $sheet->getColumnDimension("X")->setWidth(10);
        $sheet->getColumnDimension("Y")->setWidth(10);
        $sheet->getColumnDimension("Z")->setWidth(10);
        $sheet->getColumnDimension("AA")->setWidth(10);
        $sheet->getColumnDimension("AB")->setWidth(10);
        $sheet->getColumnDimension("AC")->setWidth(10);
        $sheet->getColumnDimension("AD")->setWidth(10);
        $sheet->getColumnDimension("AE")->setWidth(10);
        $sheet->getColumnDimension("AF")->setWidth(10);
        $sheet->getColumnDimension("AG")->setWidth(25);


        $productList = [
            'igarten' => '1', 'april' => '2', 'cdi40' => '3'
        ];

        $sqlStudentActive = Student::sqlCountStudentFullfeeActive($from_dof_date, $to_dof_date, '');
        $sqlClasses = LmsClass::sqlGetClasses(['id', 'branch_id'], $from_dof_date, $to_dof_date, '');
        $sqlClassesPResult = $sqlStudentPResult = $sqlClassesPResultLess = $sqlClassesPResultPrev = $sqlStudentPResultPrev = '';
        foreach ($productList as $pKey => $pID) {
            // Classes
            $where = " AND tpp.product_id = $pID";
            $sqlClassesP = LmsClass::sqlGetClasses(['id', 'branch_id'], $from_dof_date, $to_dof_date, $where, true);
            $sqlClassesPResult .= " ( select count(*) from ($sqlClassesP) as cl where cl.branch_id = b.id ) as cls_$pKey ,";
            // ClassesPrev
            $where = " AND tpp.product_id = $pID";
            $sqlClassesPPrev = LmsClass::sqlGetClasses(['id', 'branch_id'], $from_dof_date_prev, $to_dof_date_prev, $where, true);
            $sqlClassesPResultPrev .= " ( select count(*) from ($sqlClassesPPrev) as cl where cl.branch_id = b.id ) as cls_prev_$pKey ,";

            //Students
            $wherePStudent = " AND ct.product_id = $pID";
            $sqlStudentPActive = Student::sqlCountStudentFullfeeActive($from_dof_date, $to_dof_date, $wherePStudent);
            $sqlStudentPResult .= "( select count(*) from ( $sqlStudentPActive ) as st2 where st2.b_id = b.id ) as student_$pKey ,";
            //Students Prev
            $wherePStudent = " AND ct.product_id = $pID";
            $sqlStudentPActivePrev = Student::sqlCountStudentFullfeeActive($from_dof_date, $to_dof_date, $wherePStudent);
            $sqlStudentPResultPrev .= "( select count(*) from ( $sqlStudentPActivePrev ) as st2 where st2.b_id = b.id ) as student_prev_$pKey ,";

            //Classes Less
            $whereLess = " AND tpp.product_id = $pID";
            $sqlClassesPLess = LmsClass::sqlGetClasses(['id', 'branch_id'], $from_dof_date, $to_dof_date, $whereLess, true, true);
            $sqlClassesPResultLess .= " ( select count(*) from ($sqlClassesPLess) as cl where cl.branch_id = b.id ) as cls_less5_$pKey ,";
        }

        $stPending = Student::sqlCountStudentFullfeePending($to_dof_date, 'AND p.branch_id = b.id');
        $stPendingPrev = Student::sqlCountStudentFullfeePending($to_dof_date_prev, 'AND p.branch_id = b.id');

        $whereRenew = " AND s.branch_id = b.id";
        $renew = Student::sqlCountRenewStudent($from_dof_date, $to_dof_date, $whereRenew);
        $renewSuccess = Student::sqlCountRenewStudent($from_dof_date, $to_dof_date, $whereRenew, true);
        $renewPrev = Student::sqlCountRenewStudent($from_dof_date_prev, $to_dof_date_prev, $whereRenew);
        $renewPrevSuccess = Student::sqlCountRenewStudent($from_dof_date_prev, $to_dof_date_prev, $whereRenew);

        $sql = "
        SELECT
          b.id as b_id,
          b.zone_id as b_zone,
          b.name as b_name,
          b.region_id as region_id,
          ( select count(*) from ( $sqlStudentActive ) as st2 where st2.b_id = b.id ) as all_student ,
          ( select count(*) from ( $sqlClasses ) as cl where cl.branch_id = b.id ) as all_cls,
          $sqlClassesPResult
          $sqlStudentPResult
          $sqlClassesPResultLess
          $sqlClassesPResultPrev
          $sqlStudentPResultPrev
          ($renew) as renew,
          ($renewSuccess) as renew_success,
          ($renewPrev) as renew_prev,
          ($renewPrevSuccess) as renew_prev_success,
          ($stPending) as student_pending,
          ($stPendingPrev) as student_pending_prev,
          (
            select count(*) from users as u
            left join term_user_branch as tub on tub.user_id = u.id
            where tub.branch_id = b.id and tub.role_id = 55
          ) as all_cm,
          (
            select count(*) from users as u
            left join term_user_branch as tub on tub.user_id = u.id
            where tub.branch_id = b.id and tub.role_id = 55 and u.start_date >= '$whereCm2m'
          ) as cm2m

          FROM
          branches as b
          WHERE b.status = 1 $whereBrc
        ";
//        echo $sql;die;
        $branches = DB::select(DB::raw($sql));

        $x = 6;
        $j = 0;
        foreach ($branches as $branch) {
            $j++;
            $cls_asc_april = $cls_asc_igarten = $cls_asc_cdi40 = 0;
            //
            if ($branch->student_april > 0 and $branch->cls_april > 0)
                $cls_asc_april = $branch->student_april / $branch->cls_april;
            if ($branch->student_igarten > 0 and $branch->cls_igarten > 0)
                $cls_asc_igarten = $branch->student_igarten / $branch->cls_igarten;
            if ($branch->student_cdi40 > 0 and $branch->cls_cdi40 > 0)
                $cls_asc_cdi40 = $branch->student_cdi40 / $branch->cls_cdi40;

            $cls_asc_april_prev = $cls_asc_igarten_prev = $cls_asc_cdi40_prev = 0;
            if ($branch->student_prev_april > 0 and $branch->cls_prev_april > 0)
                $cls_asc_april_prev = $branch->student_prev_april / $branch->cls_prev_april;
            if ($branch->student_prev_igarten > 0 and $branch->cls_prev_igarten > 0)
                $cls_asc_igarten_prev = $branch->student_prev_igarten / $branch->cls_prev_igarten;
            if ($branch->student_prev_cdi40 > 0 and $branch->cls_prev_cdi40 > 0)
                $cls_asc_cdi40_prev = $branch->student_prev_cdi40 / $branch->cls_prev_cdi40;

            $ratioRenew = $ratioRenewPrev = 0;
            if ($branch->renew_success > 0 and $branch->renew > 0)
                $ratioRenew = ($branch->renew_success / $branch->renew) * 100;
            if ($branch->renew_prev_success > 0 and $branch->renew_prev > 0)
                $ratioRenewPrev = ($branch->renew_prev_success / $branch->renew_prev) * 100;

            $ratioPending = 0;
            if ($branch->student_pending > 0 and $branch->student_pending_prev > 0)
                $ratioPending = ($branch->student_pending / $branch->student_pending_prev) * 100;


            $sheet->setCellValue('A' . $x, '-');

            $sheet->setCellValue('B' . $x, $branch->b_name);
            $sheet->setCellValue('C' . $x, $branch->all_cls);
            $sheet->setCellValue('D' . $x, $branch->all_student);

            $sheet->setCellValue('E' . $x, $branch->cls_april);
            $sheet->setCellValue('F' . $x, $branch->student_april);
            $sheet->setCellValue('G' . $x, $branch->cls_less5_april);

            $sheet->setCellValue('H' . $x, $branch->cls_igarten);
            $sheet->setCellValue('I' . $x, $branch->student_igarten);
            $sheet->setCellValue('J' . $x, $branch->cls_less5_igarten);

            $sheet->setCellValue('K' . $x, $branch->cls_cdi40);
            $sheet->setCellValue('L' . $x, $branch->student_cdi40);
            $sheet->setCellValue('M' . $x, $branch->cls_less5_cdi40);

            $sheet->setCellValue('N' . $x, $cls_asc_april_prev);
            $sheet->setCellValue('O' . $x, $cls_asc_igarten_prev);
            $sheet->setCellValue('P' . $x, $cls_asc_cdi40_prev);

            $sheet->setCellValue('Q' . $x, $cls_asc_april);
            $sheet->setCellValue('R' . $x, $cls_asc_igarten);
            $sheet->setCellValue('S' . $x, $cls_asc_cdi40);

            $sheet->setCellValue('T' . $x, $branch->renew);
            $sheet->setCellValue('U' . $x, $branch->renew_success);
            $sheet->setCellValue('V' . $x, $ratioRenew);

            $sheet->setCellValue('W' . $x, $branch->renew_prev);
            $sheet->setCellValue('X' . $x, $branch->renew_prev_success);
            $sheet->setCellValue('Y' . $x, $ratioRenewPrev);
            $sheet->setCellValue('Z' . $x, $branch->student_pending);
            $sheet->setCellValue('AA' . $x, $branch->student_pending_prev);
            $sheet->setCellValue('AB' . $x, $ratioPending);
            $sheet->setCellValue('AC' . $x, $branch->all_cm);
            $sheet->setCellValue('AD' . $x, $branch->cm2m);

            $sheet->setCellValue('AE' . $x, 0.0);
            $sheet->setCellValue('AF' . $x, 0.0);
            $sheet->setCellValue('AG' . $x, 0.0);
            // $day_delay = '';
            // if ($branches[$i]->day_delay < 0){
            //     $day_delay = '('.ltrim($branches[$i]->day_delay, '-').')';
            // }
            // else $day_delay = $students[$i]->day_delay;
            // $sheet->setCellValue('M'.$x, $day_delay);
            $n = $x;

            $st = "A" . $n;
            $ed = "AG" . $n;
            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
            ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
            $x++;
        }

//      echo $x;die;
        $total_hn = count($branches) + 7;
        $total_hcm = count($branches) + 8;
        $total_other = count($branches) + 9;
        $total_all = count($branches) + 6;
        ProcessExcel::styleCells($spreadsheet, "B$total_hn", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
        ProcessExcel::styleCells($spreadsheet, "B$total_hcm", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
        ProcessExcel::styleCells($spreadsheet, "B$total_other", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
        ProcessExcel::styleCells($spreadsheet, "B$total_all", "FFFFFF", "black", 9, 0, 3, "left", "center", false);

        ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 1, true, "center", "center", true);

        $sheet->setCellValue('A' . $total_all, '');
        $sheet->setCellValue('B' . $total_all, 'Tổng hợp hệ thống');
        $sheet->setCellValue('C' . $total_all, "=SUM(C6:C$x)");
        $sheet->setCellValue('D' . $total_all, "=SUM(D6:D$x)");
        $sheet->setCellValue('E' . $total_all, "=SUM(E6:E$x)");
        $sheet->setCellValue('F' . $total_all, "=SUM(F6:F$x)");
        $sheet->setCellValue('G' . $total_all, "=SUM(G6:G$x)");
        $sheet->setCellValue('H' . $total_all, "=SUM(H6:H$x)");
        $sheet->setCellValue('I' . $total_all, "=SUM(I6:I$x)");
        $sheet->setCellValue('J' . $total_all, "=SUM(J6:J$x)");
        $sheet->setCellValue('K' . $total_all, "=SUM(K6:K$x)");
        $sheet->setCellValue('L' . $total_all, "=SUM(L6:L$x)");
        $sheet->setCellValue('M' . $total_all, "=SUM(M6:M$x)");
        $sheet->setCellValue('N' . $total_all, "=SUM(N6:N$x) / $j");
        $sheet->setCellValue('O' . $total_all, "=SUM(O6:O$x) / $j");
        $sheet->setCellValue('P' . $total_all, "=SUM(P6:P$x) / $j");
        $sheet->setCellValue('Q' . $total_all, "=SUM(Q6:Q$x) / $j");
        $sheet->setCellValue('R' . $total_all, "=SUM(R6:R$x) / $j");
        $sheet->setCellValue('S' . $total_all, "=SUM(S6:S$x) / $j");
        $sheet->setCellValue('T' . $total_all, "=SUM(T6:T$x)");
        $sheet->setCellValue('U' . $total_all, "=SUM(U6:U$x)");
        $sheet->setCellValue('V' . $total_all, "=SUM(V6:V$x) / $j");
        $sheet->setCellValue('W' . $total_all, "=SUM(W6:W$x)");
        $sheet->setCellValue('X' . $total_all, "=SUM(X6:X$x)");
        $sheet->setCellValue('Y' . $total_all, "=SUM(Y6:Y$x) / $j");
        $sheet->setCellValue('Z' . $total_all, "=SUM(Z6:Z$x)");
        $sheet->setCellValue('AA' . $total_all, "=SUM(AA6:AA$x)");
        $sheet->setCellValue('AB' . $total_all, "=SUM(AB6:AB$x) / $j");
        $sheet->setCellValue('AC' . $total_all, "=SUM(AC6:AC$x)");
        $sheet->setCellValue('AD' . $total_all, "=SUM(AD6:AD$x)");
        $sheet->setCellValue('AE' . $total_all, 0.0);
        $sheet->setCellValue('AF' . $total_all, 0.0);
        $sheet->setCellValue('AG' . $total_all, 0.0);


        ProcessExcel::styleCells($spreadsheet, "A5:AG5", "FFFFFF", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:AG1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:AG2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:AG3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:AG4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        $writer = new Xlsx($spreadsheet);


        ProcessExcel::styleCells($spreadsheet, "A5:N5", "FFFFFF", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:N1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:N2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:N3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:N4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);


        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO SỐ LIỆU BỘ PHẬN VẬN HÀNH TRUNG TÂM.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;

    }

    public function reportBC20b($branches = null, $fromDate = null, $toDate = null)
    {
        $from_date = $fromDate;
        $to_date = $toDate;

        $validFromdate = strtotime($fromDate);
        $validTodate = strtotime($toDate);

        $whereBrc = '';
        if ($branches != '_') {
            $whereBrc = " And b.id in ($branches)";
        }

        if ($validFromdate and $validTodate) {
            $whereCm2m = date('Y-m-d', strtotime(' -2 month', strtotime($from_date)));
            $from_dof_date = date('Y-m-d', strtotime($from_date));
            $to_dof_date = date('Y-m-d', strtotime($to_date));
            $from_dof_date_prev = date('Y-m-01', strtotime(' -1 month', strtotime($from_date)));
            $to_dof_date_prev = date('Y-m-t', strtotime(' -1 month', strtotime($to_date)));
        } else {
            $whereCm2m = date('Y-m-d', strtotime(' -2 month'));
            $from_dof_date = date('Y-m-01');
            $to_dof_date = date('Y-m-t');
            $from_dof_date_prev = date('Y-m-01', strtotime(' -1 month'));
            $to_dof_date_prev = date('Y-m-t', strtotime(' -1 month'));
        }
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->freezePane('C6');
        // $sheet->setTitle("Iutput");
        // $sheet->getStyle('A5:AG5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('add8e6');
        $spreadsheet->getActiveSheet()->getStyle('C3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('D3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('Z4')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AA4')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AB4')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AC3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AD3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AF3')->getAlignment()->setWrapText(true);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO SỐ LIỆU BỘ PHẬN VẬN HÀNH TRUNG TÂM - THÁNG ' . date('m/Y', strtotime($to_dof_date)));
        $sheet->mergeCells('A1:M1');

        $sheet->mergeCells('E3:M3');
        $sheet->mergeCells('N3:S3');
        $sheet->mergeCells('T3:Y3');
        $sheet->mergeCells('Z3:AB3');

        $sheet->mergeCells('A3:A5');
        $sheet->mergeCells('B3:B5');
        $sheet->mergeCells('C3:C5');
        $sheet->mergeCells('D3:D5');
        $sheet->mergeCells('AC3:AC5');
        $sheet->mergeCells('AD3:AD5');
        $sheet->mergeCells('AE3:AE5');
        $sheet->mergeCells('AF3:AF5');
        $sheet->mergeCells('AG3:AG5');

        $sheet->mergeCells('Z4:Z5');
        $sheet->mergeCells('AA4:AA5');
        $sheet->mergeCells('AB4:AB5');


        $sheet->getRowDimension('1')->setRowHeight(50);
        $sheet->getRowDimension('2')->setRowHeight(30);
        $sheet->getRowDimension('3')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(50);

        $sheet->setCellValue('A2', 'TỪ NGÀY : ' . $from_dof_date . ' - ĐẾN NGÀY ' . $to_dof_date);
        $sheet->mergeCells('A2:M2');
        $sheet->mergeCells('E4:G4');
        $sheet->mergeCells('H4:J4');
        $sheet->mergeCells('K4:M4');
        $sheet->mergeCells('N4:P4');
        $sheet->mergeCells('Q4:S4');
        $sheet->mergeCells('T4:V4');
        $sheet->mergeCells('W4:Y4');
        $sheet->mergeCells('W4:Y4');


        // $sheet->mergeCells('A3:M3');
        $sheet->setCellValue('E4', 'April');
        $sheet->setCellValue('H4', 'Igarten');
        $sheet->setCellValue('K4', 'CDI 4.0');
        $sheet->setCellValue('N4', 'Tháng ' . date("m/Y", strtotime($to_dof_date_prev)));
        $sheet->setCellValue('Q4', 'Tháng ' . date("m/Y", strtotime($to_dof_date)));
        $sheet->setCellValue('T4', 'Tháng ' . date("m/Y", strtotime($to_dof_date_prev)));
        $sheet->setCellValue('W4', 'Tháng ' . date("m/Y", strtotime($to_dof_date)));


        $sheet->setCellValue('E3', 'Quản lý số lượng HS');
        $sheet->setCellValue('N3', 'ACS');
        $sheet->setCellValue('T3', 'Renew');
        $sheet->setCellValue('Z3', 'Số học sinh Pending');


        $sheet->setCellValue('A5', 'Kiểm tra tiếng Việt có dấu !');

        $sheet->setCellValue("A3", "Trung tâm");
        $sheet->setCellValue("B3", "Họ tên nhân viên");
        $sheet->setCellValue("C3", "Mã nhân viên");
        $sheet->setCellValue("D3", "Hạng nhân viên");
        $sheet->setCellValue("E5", "Số lớp");
        $sheet->setCellValue("F5", "Số học sinh");
        $sheet->setCellValue("G5", "Số lớp ACS <=5");
        $sheet->setCellValue("H5", "Số lớp");
        $sheet->setCellValue("I5", "Số học sinh");
        $sheet->setCellValue("J5", "Số lớp ACS <=5");
        $sheet->setCellValue("K5", "Số lớp");
        $sheet->setCellValue("L5", "Số học sinh");
        $sheet->setCellValue("M5", "Số lớp ACS <=5");
        $sheet->setCellValue("N5", "April(15)");
        $sheet->setCellValue("O5", "iGarten(15.5)");
        $sheet->setCellValue("P5", "CDI 4.0(12)");
        $sheet->setCellValue("Q5", "April(15)");
        $sheet->setCellValue("R5", "iGarten(15.5)");
        $sheet->setCellValue("S5", "CDI 4.0(12)");
        $sheet->setCellValue("T5", "H/s đến hạn tái phí");
        $sheet->setCellValue("U5", "Tái phí thành công");
        $sheet->setCellValue("V5", "Tỷ lệ");
        $sheet->setCellValue("W5", "H/s đến hạn tái phí");
        $sheet->setCellValue("X5", "Tái phí thành công");
        $sheet->setCellValue("Y5", "Tỷ lệ ");
        $sheet->setCellValue("Z4", "Tháng " . date("m/Y", strtotime($to_dof_date_prev)));
        $sheet->setCellValue("AA4", "Tháng " . date("m/Y", strtotime($to_dof_date)));
        $sheet->setCellValue("AB4", "Tỷ lệ tăng/ giảm");
        $sheet->setCellValue("AC3", "Tổng số lớp");
        $sheet->setCellValue("AD3", "Tổng số học sinh");


        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(35);
        $sheet->getColumnDimension("C")->setWidth(10);
        $sheet->getColumnDimension("D")->setWidth(10);
        $sheet->getColumnDimension("E")->setWidth(10);
        $sheet->getColumnDimension("F")->setWidth(10);
        $sheet->getColumnDimension("G")->setWidth(10);
        $sheet->getColumnDimension("H")->setWidth(10);
        $sheet->getColumnDimension("I")->setWidth(10);
        $sheet->getColumnDimension("J")->setWidth(10);
        $sheet->getColumnDimension("K")->setWidth(10);
        $sheet->getColumnDimension("L")->setWidth(10);
        $sheet->getColumnDimension("M")->setWidth(10);
        $sheet->getColumnDimension("N")->setWidth(10);
        $sheet->getColumnDimension("O")->setWidth(10);
        $sheet->getColumnDimension("P")->setWidth(10);
        $sheet->getColumnDimension("Q")->setWidth(10);
        $sheet->getColumnDimension("R")->setWidth(10);
        $sheet->getColumnDimension("S")->setWidth(10);
        $sheet->getColumnDimension("T")->setWidth(10);
        $sheet->getColumnDimension("U")->setWidth(10);
        $sheet->getColumnDimension("V")->setWidth(10);
        $sheet->getColumnDimension("W")->setWidth(10);
        $sheet->getColumnDimension("X")->setWidth(10);
        $sheet->getColumnDimension("Y")->setWidth(10);
        $sheet->getColumnDimension("Z")->setWidth(10);
        $sheet->getColumnDimension("AA")->setWidth(10);
        $sheet->getColumnDimension("AB")->setWidth(10);
        $sheet->getColumnDimension("AC")->setWidth(10);
        $sheet->getColumnDimension("AD")->setWidth(10);
        $sheet->getColumnDimension("AE")->setWidth(10);
        $sheet->getColumnDimension("AF")->setWidth(10);
        $sheet->getColumnDimension("AG")->setWidth(25);


        $productList = [
            'igarten' => '1', 'april' => '2', 'cdi40' => '3'
        ];

        $sqlStudentActive = Student::sqlCountStudentFullfeeActive($from_dof_date, $to_dof_date, '');
        $sqlClasses = LmsClass::sqlGetClasses(['id', 'branch_id', 'cm_id'], $from_dof_date, $to_dof_date, '');
        $sqlClassesPResult = $sqlStudentPResult = $sqlClassesPResultLess = $sqlClassesPResultPrev = $sqlStudentPResultPrev = '';
        foreach ($productList as $pKey => $pID) {
            // Classes
            $where = " AND tpp.product_id = $pID";
            $sqlClassesP = LmsClass::sqlGetClasses(['id', 'branch_id', 'cm_id'], $from_dof_date, $to_dof_date, $where, true);
            $sqlClassesPResult .= " ( select count(*) from ($sqlClassesP) as cl where cl.cm_id = u.id ) as cls_$pKey ,";
            // ClassesPrev
            $where = " AND tpp.product_id = $pID";
            $sqlClassesPPrev = LmsClass::sqlGetClasses(['id', 'branch_id', 'cm_id'], $from_dof_date_prev, $to_dof_date_prev, $where, true);
            $sqlClassesPResultPrev .= " ( select count(*) from ($sqlClassesPPrev) as cl where cl.cm_id = u.id ) as cls_prev_$pKey ,";

            //Students
            $wherePStudent = " AND ct.product_id = $pID";
            $sqlStudentPActive = Student::sqlCountStudentFullfeeActive($from_dof_date, $to_dof_date, $wherePStudent);
            $sqlStudentPResult .= "( select count(*) from ( $sqlStudentPActive ) as st2 where st2.cm_id = u.id ) as student_$pKey ,";
            //Students Prev
            $wherePStudent = " AND ct.product_id = $pID";
            $sqlStudentPActivePrev = Student::sqlCountStudentFullfeeActive($from_dof_date, $to_dof_date, $wherePStudent);
            $sqlStudentPResultPrev .= "( select count(*) from ( $sqlStudentPActivePrev ) as st2 where st2.cm_id = u.id ) as student_prev_$pKey ,";

            //Classes Less
            $whereLess = " AND tpp.product_id = $pID";
            $sqlClassesPLess = LmsClass::sqlGetClasses(['id', 'branch_id', 'cm_id'], $from_dof_date, $to_dof_date, $whereLess, true, true);
            $sqlClassesPResultLess .= " ( select count(*) from ($sqlClassesPLess) as cl where cl.cm_id = u.id ) as cls_less5_$pKey ,";
        }

        $stPending = Student::sqlCountStudentFullfeePending($to_dof_date, 'AND c.cm_id = u.id');
        $stPendingPrev = Student::sqlCountStudentFullfeePending($to_dof_date_prev, 'AND c.cm_id = u.id');

        $whereRenew = " AND c.cm_id = u.id";
        $renew = Student::sqlCountRenewStudent($from_dof_date, $to_dof_date, $whereRenew);
        $renewSuccess = Student::sqlCountRenewStudent($from_dof_date, $to_dof_date, $whereRenew, true);
        $renewPrev = Student::sqlCountRenewStudent($from_dof_date_prev, $to_dof_date_prev, $whereRenew);
        $renewPrevSuccess = Student::sqlCountRenewStudent($from_dof_date_prev, $to_dof_date_prev, $whereRenew);

        $sql = "
          SELECT
            b.id as b_id,
            b.zone_id as b_zone,
            b.name as b_name,
            u.full_name as full_name,
            u.id as u_id,
            u.hrm_id as hrm_id,
            b.region_id as region_id,
            ( select count(*) from ( $sqlStudentActive ) as st2 where st2.cm_id = u.id ) as all_student ,
            ( select count(*) from ( $sqlClasses ) as cl where cl.cm_id = u.id ) as all_cls,
            $sqlClassesPResult
            $sqlStudentPResult
            $sqlClassesPResultLess
            $sqlClassesPResultPrev
            $sqlStudentPResultPrev
            ($renew) as renew,
            ($renewSuccess) as renew_success,
            ($renewPrev) as renew_prev,
            ($renewPrevSuccess) as renew_prev_success,
            ($stPending) as student_pending,
            ($stPendingPrev) as student_pending_prev

            from users as u
            LEFT JOIN term_user_branch as tub on tub.user_id = u.id
            LEFT JOIN branches as b on b.id = tub.branch_id
            WHERE tub.role_id in (55,56)
            AND b.status = 1 $whereBrc
          ";
//        echo $sql;die;
        $branches = DB::select(DB::raw($sql));

        $x = 6;
        $j = 0;
        foreach ($branches as $branch) {
            $j++;

            $cls_asc_april = $cls_asc_igarten = $cls_asc_cdi40 = 0;
            //
            if ($branch->student_april > 0 and $branch->cls_april > 0)
                $cls_asc_april = $branch->student_april / $branch->cls_april;
            if ($branch->student_igarten > 0 and $branch->cls_igarten > 0)
                $cls_asc_igarten = $branch->student_igarten / $branch->cls_igarten;
            if ($branch->student_cdi40 > 0 and $branch->cls_cdi40 > 0)
                $cls_asc_cdi40 = $branch->student_cdi40 / $branch->cls_cdi40;

            $cls_asc_april_prev = $cls_asc_igarten_prev = $cls_asc_cdi40_prev = 0;
            if ($branch->student_prev_april > 0 and $branch->cls_prev_april > 0)
                $cls_asc_april_prev = $branch->student_prev_april / $branch->cls_prev_april;
            if ($branch->student_prev_igarten > 0 and $branch->cls_prev_igarten > 0)
                $cls_asc_igarten_prev = $branch->student_prev_igarten / $branch->cls_prev_igarten;
            if ($branch->student_prev_cdi40 > 0 and $branch->cls_prev_cdi40 > 0)
                $cls_asc_cdi40_prev = $branch->student_prev_cdi40 / $branch->cls_prev_cdi40;

            $ratioRenew = $ratioRenewPrev = 0;
            if ($branch->renew_success > 0 and $branch->renew > 0)
                $ratioRenew = ($branch->renew_success / $branch->renew) * 100;
            if ($branch->renew_prev_success > 0 and $branch->renew_prev > 0)
                $ratioRenewPrev = ($branch->renew_prev_success / $branch->renew_prev) * 100;

            $ratioPending = 0;
            if ($branch->student_pending > 0 and $branch->student_pending_prev > 0)
                $ratioPending = ($branch->student_pending / $branch->student_pending_prev) * 100;

            $sheet->setCellValue('A' . $x, $branch->b_name);
            $sheet->setCellValue('B' . $x, $branch->full_name);
            $sheet->setCellValue('C' . $x, $branch->hrm_id);
            $sheet->setCellValue('D' . $x, '-');

            $sheet->setCellValue('E' . $x, $branch->cls_april);
            $sheet->setCellValue('F' . $x, $branch->student_april);
            $sheet->setCellValue('G' . $x, $branch->cls_less5_april);

            $sheet->setCellValue('H' . $x, $branch->cls_igarten);
            $sheet->setCellValue('I' . $x, $branch->student_igarten);
            $sheet->setCellValue('J' . $x, $branch->cls_less5_igarten);

            $sheet->setCellValue('K' . $x, $branch->cls_cdi40);
            $sheet->setCellValue('L' . $x, $branch->student_cdi40);
            $sheet->setCellValue('M' . $x, $branch->cls_less5_cdi40);

            $sheet->setCellValue('N' . $x, $cls_asc_april_prev);
            $sheet->setCellValue('O' . $x, $cls_asc_igarten_prev);
            $sheet->setCellValue('P' . $x, $cls_asc_cdi40_prev);

            $sheet->setCellValue('Q' . $x, $cls_asc_april);
            $sheet->setCellValue('R' . $x, $cls_asc_igarten);
            $sheet->setCellValue('S' . $x, $cls_asc_cdi40);

            $sheet->setCellValue('T' . $x, $branch->renew);
            $sheet->setCellValue('U' . $x, $branch->renew_success);
            $sheet->setCellValue('V' . $x, $ratioRenew);

            $sheet->setCellValue('W' . $x, $branch->renew_prev);
            $sheet->setCellValue('X' . $x, $branch->renew_prev_success);
            $sheet->setCellValue('Y' . $x, $ratioRenewPrev);
            $sheet->setCellValue('Z' . $x, $branch->student_pending);
            $sheet->setCellValue('AA' . $x, $branch->student_pending_prev);
            $sheet->setCellValue('AB' . $x, $ratioPending);

            $sheet->setCellValue('AC' . $x, $branch->all_cls);
            $sheet->setCellValue('AD' . $x, $branch->all_student);

            // $day_delay = '';
            // if ($branches[$i]->day_delay < 0){
            //     $day_delay = '('.ltrim($branches[$i]->day_delay, '-').')';
            // }
            // else $day_delay = $students[$i]->day_delay;
            // $sheet->setCellValue('M'.$x, $day_delay);
            $n = $x;

            $st = "A" . $n;
            $ed = "AG" . $n;
//            ProcessExcel::styleCells($spreadsheet, "$st:$ed", "FFFFFF", "black", 9, 0, 3, "center", "center", true);
//            ProcessExcel::styleCells($spreadsheet, "B$x", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
            $x++;
        }

        $total_all = count($branches) + 6;

        $stx = "A" . $total_all;
        $endx = "AG" . $total_all;
        ProcessExcel::styleCells($spreadsheet, "B$total_all", "FFFFFF", "black", 9, 0, 3, "left", "center", false);
        ProcessExcel::styleCells($spreadsheet, "$stx:$endx", "black", "black", 9, 1, true, "center", "center", true);
        // dd($c);


        $sheet->setCellValue('A' . $total_all, '');
        $sheet->setCellValue('B' . $total_all, 'Tổng hệ thống');
        $sheet->setCellValue('C' . $total_all, '-');
        $sheet->setCellValue('D' . $total_all, '-');
        $sheet->setCellValue('E' . $total_all, "=SUM(E6:E$x)");
        $sheet->setCellValue('F' . $total_all, "=SUM(F6:F$x)");
        $sheet->setCellValue('G' . $total_all, "=SUM(G6:G$x)");
        $sheet->setCellValue('H' . $total_all, "=SUM(H6:H$x)");
        $sheet->setCellValue('I' . $total_all, "=SUM(I6:I$x)");
        $sheet->setCellValue('J' . $total_all, "=SUM(J6:J$x)");
        $sheet->setCellValue('K' . $total_all, "=SUM(K6:K$x)");
        $sheet->setCellValue('L' . $total_all, "=SUM(L6:L$x)");
        $sheet->setCellValue('M' . $total_all, "=SUM(M6:M$x)");
        $sheet->setCellValue('N' . $total_all, "=SUM(N6:N$x) / $j");
        $sheet->setCellValue('O' . $total_all, "=SUM(O6:O$x) / $j");
        $sheet->setCellValue('P' . $total_all, "=SUM(P6:P$x) / $j");
        $sheet->setCellValue('Q' . $total_all, "=SUM(Q6:Q$x) / $j");
        $sheet->setCellValue('R' . $total_all, "=SUM(R6:R$x) / $j");
        $sheet->setCellValue('S' . $total_all, "=SUM(S6:S$x) / $j");
        $sheet->setCellValue('T' . $total_all, "=SUM(T6:T$x)");
        $sheet->setCellValue('U' . $total_all, "=SUM(U6:U$x)");
        $sheet->setCellValue('V' . $total_all, "=SUM(V6:V$x) / $j");
        $sheet->setCellValue('W' . $total_all, "=SUM(W6:W$x)");
        $sheet->setCellValue('X' . $total_all, "=SUM(X6:X$x)");
        $sheet->setCellValue('Y' . $total_all, "=SUM(Y6:Y$x) / $j");
        $sheet->setCellValue('Z' . $total_all, "=SUM(Z6:Z$x)");
        $sheet->setCellValue('AA' . $total_all, "=SUM(AA6:AA$x)");
        $sheet->setCellValue('AB' . $total_all, "=SUM(AB6:AB$x) / $j");
        $sheet->setCellValue('AC' . $total_all, "=SUM(AC6:AC$x)");
        $sheet->setCellValue('AD' . $total_all, "=SUM(AD6:AD$x)");


        ProcessExcel::styleCells($spreadsheet, "A5:AG5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:AG1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:AG2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:AG3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:AG4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);

        $writer = new Xlsx($spreadsheet);


        ProcessExcel::styleCells($spreadsheet, "A5:N5", "4472c4ff", "fff", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A1:N1", "FFFFFF", "black", 16, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A2:N2", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A3:N3", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);
        ProcessExcel::styleCells($spreadsheet, "A4:N4", "FFFFFF", "black", 11, 1, 3, "center", "center", true, 0);


        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO SỐ LIỆU BỘ PHẬN VẬN HÀNH TRUNG TÂM.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }


    //report 17d

    public function reportBC17d($branch = null, $fromDate = null, $toDate = null)
    {
        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));

        $from_date = strtotime($fromDate);
        $to_date = strtotime($toDate);

        $today = date('Y-m-d');
        $default_start_date = (date('Y-m-01'));

        if (!$from_date) {
            $fromDate = $default_start_date;
        }
        if (!$to_date) {
            $toDate = date('Y-m-t', strtotime('today'));
        }


        $where = $where_product = "";
        if ($branch != '_')
            $where .= " AND t.branch_id in( $branch ) ";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'BÁO CÁO DOANH SỐ THEO TEAM');
        $sheet->mergeCells('A1:D1');
        $sheet->getRowDimension('1')->setRowHeight(60);
        $sheet->getRowDimension('2')->setRowHeight(20);
        $sheet->getRowDimension('3')->setRowHeight(20);
        $sheet->getRowDimension('4')->setRowHeight(40);

        $sheet->setCellValue('A2', "TỪ NGÀY $fromDate - ĐẾN NGÀY: $toDate");
        $sheet->mergeCells('A2:D2');
        $sheet->setCellValue("A4", "STT");
        $sheet->setCellValue("B4", "TEAM");
        $sheet->setCellValue("C4", "DOANH SỐ");
        $sheet->setCellValue("D4", "LOẠI");


        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:C5');
        $sheet->mergeCells('D4:D5');

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(20);

        ProcessExcel::styleCells($spreadsheet, "A1:D1", "black", "fff", 16, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A2:D2", "black", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A3:D3", "ffffff", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A4:D4", "ffffff", "black", 11, 1, true, "center", "center", true);
        ProcessExcel::styleCells($spreadsheet, "A5:D5", "ffffff", "black", 11, 1, true, "center", "center", true);

        $sql = "SELECT
      u.full_name,
      u.hrm_id,
      t.user_id,
      t.role_id,
      t.branch_id,
      x.id payment_id,
      SUM(x.amount) summary
      FROM
      term_user_branch t
      LEFT JOIN users AS u ON u.id = t.user_id
      LEFT JOIN (
      SELECT DISTINCT
              ( p.id ),
              c.ec_leader_id,
              c.branch_id,
              p.contract_id,
              p.created_at,
              p.amount
      FROM
              payment p
              LEFT JOIN contracts c ON p.contract_id = c.id
      WHERE
              p.created_at >= ' $fromDate'
              AND p.created_at <= '$toDate'
      GROUP BY
              p.id
      ) x ON t.user_id = x.ec_leader_id
      AND t.branch_id = x.branch_id
      WHERE t.role_id = 69 $where
      ORDER BY
      contract_id DESC";
        // echo $sql;die;
        $result = DB::select(DB::raw($sql));
        $p = 5;
        $p++;
        $stt = 1;
        for ($j = 0; $j < count($result); $j++) {
            if ($result) {
                $y = $p + 1;
                $st = 'A' . $y;
                $en = 'D' . $y;
                $b = '';
                $sheet->setCellValue('A' . $y, $stt);
                $sheet->setCellValue('B' . $y, $result[$j]->full_name);
                $sheet->setCellValue('C' . $y, number_format($result[$j]->summary));
                $sheet->setCellValue('D' . $y, $b);

                $p++;
                $stt++;
                ProcessExcel::styleCells($spreadsheet, "$st:$en", "3FC2EE", "black", 11, '', true, "center", "center", true);
            }
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO DOANH SỐ THEO TEAM.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function exportStudents($branch_id)
    {
        if ($branch_id && is_numeric($branch_id)) {
            $query = "
                SELECT 
                    s.`name` AS student_name,
                    s.`nick`,
                    s.`date_of_birth`,
                    s.crm_id,
                    s.`stu_id`,
                    s.`accounting_id`,
                    s2.`stu_id` AS stu_sibling_id,
                    IF(s.`type` > 0, 'VIP', 'Thường') AS student_type,
                    s.`gud_name1`,
                    u1.`username` AS ec_name,
                    u2.`username` AS cm_name,
                    b.name AS branch_name,
                    cls.cls_name AS class_name,
                    IF(c.id IS NULL, 'Tiềm năng', IF(c.type = 0,'học trải nghiệm','Chính thức')) AS contract_type
                FROM
                    students AS s
                    LEFT JOIN contracts AS c ON s.id = c.`student_id`
                    LEFT JOIN term_student_user AS t ON s.id = t.`student_id`
                    LEFT JOIN enrolments AS e ON s.`id` = e.`student_id`
                    LEFT JOIN classes AS cls ON e.`class_id` = cls.`id`
                    LEFT JOIN users AS u1 ON t.`ec_id` = u1.`id`
                    LEFT JOIN users AS u2 ON t.`cm_id` = u2.`id`
                    LEFT JOIN branches AS b ON s.`branch_id`=b.id
                    LEFT JOIN students AS s2 ON s.`sibling_id` = s2.id
                WHERE
                    s.`branch_id` IN ($branch_id)
                    AND c.id IN (SELECT MIN(id) FROM contracts WHERE branch_id IN ($branch_id) AND end_date >= CURDATE() GROUP BY student_id)
                GROUP BY s.`id`
            ";
            $data = DB::select(DB::raw($query));

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN ANH NGỮ APAX');
            $sheet->mergeCells('A1:I1');
            ProcessExcel::styleCells($spreadsheet, "A1:I1", "ffffff", "000", 9, 1, true, "left", "center", false);

            $sheet->setCellValue('A2', 'Tầng 7, Tòa nhà 14 Láng Hạ, Ba Đình, Hà Nội');
            $sheet->mergeCells('A2:I2');
            ProcessExcel::styleCells($spreadsheet, "A2:I2", "ffffff", "000", 9, 1, true, "left", "center", false);

            $sheet->setCellValue('A3', 'DANH SÁCH HỌC SINH');
            $sheet->mergeCells('A3:I3');
            ProcessExcel::styleCells($spreadsheet, "A3:Q3", "ffffff", "000", 16, 1, true, "center", "center", false);

            $sheet->setCellValue('A4', 'Ngày : ' . date('d-m-Y'));
            $sheet->mergeCells('A4:I4');
            ProcessExcel::styleCells($spreadsheet, "A4:Q4", "ffffff", "000", 9, 0, true, "center", "center", false);

            $sheet->getRowDimension('1')->setRowHeight(18);
            $sheet->getRowDimension('2')->setRowHeight(18);
            $sheet->getRowDimension('3')->setRowHeight(25);
            $sheet->getRowDimension('4')->setRowHeight(18);
            $sheet->getRowDimension('5')->setRowHeight(30);


            $sheet->setCellValue("A5", "STT");
            $sheet->setCellValue("B5", "TÊN HỌC SINH");
            $sheet->setCellValue("C5", "NICKNAME");
            $sheet->setCellValue("D5", "NGÀY SINH");
            $sheet->setCellValue("E5", "MÃ CRM");
            $sheet->setCellValue("F5", "MÃ LMS");
            $sheet->setCellValue("G5", "MÃ EFFECT");
            $sheet->setCellValue("H5", "LMS anh em");
            $sheet->setCellValue("I5", "LOẠI HS");
            $sheet->setCellValue("J5", "PHỤ HUYNH");
            $sheet->setCellValue("K5", "EC");
            $sheet->setCellValue("L5", "CM");
            $sheet->setCellValue("M5", "TRUNG TÂM");
            $sheet->setCellValue("N5", "TRẠNG THÁI");


            $sheet->getColumnDimension("A")->setWidth(5);
            $sheet->getColumnDimension("B")->setWidth(20);
            $sheet->getColumnDimension("C")->setWidth(23);
            $sheet->getColumnDimension("D")->setWidth(27);
            $sheet->getColumnDimension("E")->setWidth(29);
            $sheet->getColumnDimension("F")->setWidth(20);
            $sheet->getColumnDimension("G")->setWidth(20);
            $sheet->getColumnDimension("H")->setWidth(35);
            $sheet->getColumnDimension("I")->setWidth(20);
            $sheet->getColumnDimension("J")->setWidth(20);
            $sheet->getColumnDimension("K")->setWidth(20);
            $sheet->getColumnDimension("L")->setWidth(20);
            $sheet->getColumnDimension("M")->setWidth(24);
            $sheet->getColumnDimension("N")->setWidth(24);

            ProcessExcel::styleCells($spreadsheet, "A5:N5", "add8e6", "000", 9, 1, true, "center", "center", true);

            for ($i = 0; $i < count($data); $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $data[$i]->student_name);
                $sheet->setCellValue('C' . $x, $data[$i]->nick);
                $sheet->setCellValue('D' . $x, $data[$i]->date_of_birth);
                $sheet->setCellValue('E' . $x, $data[$i]->crm_id);
                $sheet->setCellValue('F' . $x, $data[$i]->stu_id);
                $sheet->setCellValue('G' . $x, $data[$i]->accounting_id);
                $sheet->setCellValue('H' . $x, $data[$i]->stu_sibling_id);
                $sheet->setCellValue('I' . $x, $data[$i]->student_type);
                $sheet->setCellValue('J' . $x, $data[$i]->gud_name1);
                $sheet->setCellValue('K' . $x, $data[$i]->ec_name);
                $sheet->setCellValue('L' . $x, $data[$i]->cm_name);
                $sheet->setCellValue('M' . $x, $data[$i]->branch_name);
                $sheet->setCellValue('N' . $x, $data[$i]->contract_type);


                ProcessExcel::styleCells($spreadsheet, "A6:A$x", "ffffff", "000", 9, 0, true, "right", "center", false);
                ProcessExcel::styleCells($spreadsheet, "B6:N$x", "ffffff", "000", 9, 0, true, "left", "center", false);
            }

            // return $students;
            $writer = new Xlsx($spreadsheet);

            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="DANH SÁCH HỌC SINH.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
            exit;

        } else {

        }
    }

    public function exportHoliday()
    {
        $products = u::query("SELECT id, `name` FROM products WHERE status = 1");
        $holidays = [];

        $resp = u::query("SELECT h.start_date, h.end_date, h.products, z.name AS zone_name, h.name FROM public_holiday AS h
                          LEFT JOIN zones AS z ON h.zone_id = z.id
                          WHERE h.status=1 ORDER BY h.start_date ASC");

        foreach ($products as $p) {
            $holidays[$p->id] = [
                "id" => $p->id,
                "name" => $p->name,
                "list" => []
            ];
        }

        foreach ($resp as $re) {
            $product_ids = explode(',', str_replace('[', '', str_replace(']', '', $re->products)));
            foreach ($holidays as $key => $holiday) {
                if (in_array($key, $product_ids)) {
                    $holidays[$key]['list'][] = (Object)[
                        'name' => $re->name,
                        'zone' => $re->zone_name,
                        'start_date' => $re->start_date,
                        'end_date' => $re->end_date
                    ];
                }
            }
        }

        $spreadsheet = new Spreadsheet();

        $sheetIndex = 0;

//        dd($holidays);

        foreach ($holidays as $holiday) {
            if ($sheetIndex > 0) {
                $spreadsheet->createSheet();
            }

            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle($holiday['name']);

            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN ANH NGỮ APAX');
            $sheet->mergeCells('A1:E1');
            ProcessExcel::styleCells($spreadsheet, "A1:E1", "ffffff", "000", 9, 1, true, "left", "center", false);

            $sheet->setCellValue('A2', 'Tầng 7, Tòa nhà 14 Láng Hạ, Ba Đình, Hà Nội');
            $sheet->mergeCells('A2:E2');
            ProcessExcel::styleCells($spreadsheet, "A2:E2", "ffffff", "000", 9, 1, true, "left", "center", false);

            $sheet->setCellValue('A3', 'SẢN PHẨM ' . $holiday['name']);
            $sheet->mergeCells('A3:E3');
            ProcessExcel::styleCells($spreadsheet, "A3:E3", "ffffff", "000", 16, 1, true, "center", "center", false);

            $sheet->setCellValue('A4', 'Ngày : ' . date('d-m-Y'));
            $sheet->mergeCells('A4:E4');
            ProcessExcel::styleCells($spreadsheet, "A4:E4", "ffffff", "000", 9, 0, true, "center", "center", false);

            $sheet->getRowDimension('1')->setRowHeight(18);
            $sheet->getRowDimension('2')->setRowHeight(18);
            $sheet->getRowDimension('3')->setRowHeight(25);
            $sheet->getRowDimension('4')->setRowHeight(18);
            $sheet->getRowDimension('5')->setRowHeight(30);


            $sheet->setCellValue("A5", "STT");
            $sheet->setCellValue("B5", "TIÊU ĐỀ");
            $sheet->setCellValue("C5", "TỪ NGÀY");
            $sheet->setCellValue("D5", "ĐẾN NGÀY");
            $sheet->setCellValue("E5", "KHU VỰC ÁP DỤNG");


            $sheet->getColumnDimension("A")->setWidth(5);
            $sheet->getColumnDimension("B")->setWidth(20);
            $sheet->getColumnDimension("C")->setWidth(23);
            $sheet->getColumnDimension("D")->setWidth(27);
            $sheet->getColumnDimension("E")->setWidth(29);

            ProcessExcel::styleCells($spreadsheet, "A5:E5", "add8e6", "000", 9, 1, true, "center", "center", true);


            if (!empty($holiday['list'])) {
                $data = $holiday['list'];
                $x = 0;
                for ($i = 0; $i < count($data); $i++) {
                    $x = $i + 6;
                    $sheet->setCellValue('A' . $x, $i + 1);
                    $sheet->setCellValue('B' . $x, $data[$i]->name);
                    $sheet->setCellValue('C' . $x, $data[$i]->start_date);
                    $sheet->setCellValue('D' . $x, $data[$i]->end_date);
                    $sheet->setCellValue('E' . $x, $data[$i]->zone);
                }
                ProcessExcel::styleCells($spreadsheet, "A6:A$x", "ffffff", "000", 9, 0, true, "right", "center", false);
                ProcessExcel::styleCells($spreadsheet, "B6:E$x", "ffffff", "000", 9, 0, true, "left", "center", false);
            }

            $sheetIndex++;
        }

        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="PUBLIC HOLIDAYS.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function exportStudent(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN ANH NGỮ APAX');
        $sheet->mergeCells('A1:L1');
        ProcessExcel::styleCells($spreadsheet, "A1:L1", "ffffff", "000", 11, 1, true, "left", "center", false);

        $sheet->setCellValue('A2', 'Tầng 7, Tòa nhà 14 Láng Hạ, Ba Đình, Hà Nội');
        $sheet->mergeCells('A2:L2');
        ProcessExcel::styleCells($spreadsheet, "A2:L2", "ffffff", "000", 11, 1, true, "left", "center", false);

        $sheet->setCellValue('A3', 'BÁO CÁO THÔNG TIN HỌC VIÊN');
        $sheet->mergeCells('A3:L3');
        ProcessExcel::styleCells($spreadsheet, "A3:L3", "ffffff", "000", 20, 1, true, "center", "center", false);

//        $sheet->setCellValue('A4', 'Từ ngày ' . date('d/m/Y', strtotime($startDate)) . ' đến ngày ' . date('d/m/Y',strtotime($endDate)));
        $sheet->setCellValue('A4', 'Ngày xuất báo cáo ' . date('d/m/Y'));
        $sheet->mergeCells('A4:L4');
        ProcessExcel::styleCells($spreadsheet, "A4:L4", "ffffff", "000", 11, 0, true, "center", "center", false);

        $sheet->getRowDimension('1')->setRowHeight(25);
        $sheet->getRowDimension('2')->setRowHeight(25);
        $sheet->getRowDimension('3')->setRowHeight(45);
        $sheet->getRowDimension('4')->setRowHeight(30);
        $sheet->getRowDimension('5')->setRowHeight(30);


        $sheet->setCellValue("A5", "STT");
        $sheet->setCellValue("B5", "Trung tâm");
        $sheet->setCellValue("C5", "Mã CMS");
        $sheet->setCellValue("D5", "Mã Effect");
        $sheet->setCellValue("E5", "Tên học sinh");
        $sheet->setCellValue("F5", "Loại khách hàng");
        $sheet->setCellValue("G5", "Tên phụ huynh");
        $sheet->setCellValue("H5", "SĐT phụ huynh");
        $sheet->setCellValue("I5", "CM");
        $sheet->setCellValue("J5", "EC");
        $sheet->setCellValue("K5", "Loại hợp đồng");
        $sheet->setCellValue("L5", "Trạng thái");

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(12);
        $sheet->getColumnDimension("D")->setWidth(18);
        $sheet->getColumnDimension("E")->setWidth(36);
        $sheet->getColumnDimension("F")->setWidth(16);
        $sheet->getColumnDimension("G")->setWidth(30);
        $sheet->getColumnDimension("H")->setWidth(20);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(30);
        $sheet->getColumnDimension("K")->setWidth(30);
        $sheet->getColumnDimension("L")->setWidth(15);
        ProcessExcel::styleCells($spreadsheet, "A5:L5", "add8e6", "000", 11, 1, true, "center", "center", true);

        $request->get_all = true;
        $res = r::students($request);

        $data = $res['data'];
        if (!empty($data)) {
            $len = count($data);
            $max = 0;
            for ($i = 0; $i < $len; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $data[$i]->branch_name);
                $sheet->setCellValue('C' . $x, $data[$i]->crm_id);
                $sheet->setCellValue('D' . $x, $data[$i]->accounting_id);
                $sheet->setCellValue('E' . $x, $data[$i]->student_name);
                $sheet->setCellValue('F' . $x, $data[$i]->type_name);
                $sheet->setCellValue('G' . $x, $data[$i]->gud_name1);
                $sheet->setCellValue('H' . $x, $data[$i]->gud_mobile1);
                $sheet->setCellValue('I' . $x, $data[$i]->cm_name);
                $sheet->setCellValue('J' . $x, $data[$i]->ec_name);
                $sheet->setCellValue('K' . $x, $data[$i]->contract_type_name);
                $sheet->setCellValue('L' . $x, $data[$i]->contract_status_name);
                $max = $x;
            }
            ProcessExcel::styleCells($spreadsheet, "A6:L$max", null, "black", 11, 0, true, "center", "center", true);
        }

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="BÁO CÁO DANH SÁCH HỌC SINH.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }
    
    public function exportStudentCares($params)
    {
        $columns = [
            ['name' => 'stt', 'label' => 'STT', 'width' => 5],
            ['name' => 'crm_id', 'label' => 'Mã CMS', 'width' => 15],
            ['name' => 'accounting_id', 'label' => 'Mã Cyber', 'width' => 15],
            ['name' => 'student_name', 'label' => 'Tên học sinh', 'width' => 30],
            ['name' => 'contact_name', 'label' => 'Phương thức', 'width' => 20],
            ['name' => 'quality_name', 'label' => 'Chất lượng chăm sóc', 'width' => 40],
            ['name' => 'quality_score', 'label' => 'Điểm', 'width' => 10],
            ['name' => 'creator', 'label' => 'Người thực hiện', 'width' => 40],
            ['name' => 'created_at', 'label' => 'Thời gian thực hiện', 'width' => 30],
        ];

        $customerCare = new CustomerCare();
        $data = $customerCare->getAll(json_decode($params));
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO CHẤT LƯỢNG CHĂM SÓC KHÁCH HÀNG');
        $colEnd = chr(count($columns) + 65);
        $sheet->mergeCells("A1:{$colEnd}1");
        $sheet->getRowDimension(1)->setRowHeight(40);
        ProcessExcel::styleCells($spreadsheet, "A1", null, '223b54', 12, 1, 3, "center", "center", false, 0, 'Cambria');

        foreach ($columns as $index => $column) {
            $colName = chr($index + 65);
            $sheet->setCellValue("${colName}5", $column['label']);
            $sheet->getColumnDimension($colName)->setWidth($column['width']);
            $sheet->getRowDimension(5)->setRowHeight(40);
            ProcessExcel::styleCells($spreadsheet, "${colName}5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
        }

        foreach ($data as $pos => $item) {
            $position = $pos + 6;
            foreach ($columns as $index => $column) {
                $colName = chr($index + 65);
                $sheet->setCellValue("{$colName}$position", $column['name'] === 'stt' ? $pos + 1 : $item->{$column['name']});
                ProcessExcel::styleCells($spreadsheet, "{$colName}$position", "FFFFFF", "black", 11, 0, 3, "center", "center", true);
            }
        }
        self::save($spreadsheet, "Báo cáo chất lượng chăm sóc khách hàng");

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
    public function export01a1(Request $request) {
        if ($session = $request->users_data) {
            $birthday_mode = $request->birthday_mode;
            $birthday_mode = $birthday_mode ? true: false;
            $p = r::params($request, $session);
            $query = r::queryReport01a1($birthday_mode, $p, 0, 1);
            $students = u::query($query);

            $branchs = u::query("SELECT name from branches WHERE id in ($p->s)");
            $branch_name = '';
            foreach ($branchs as $branch) {
                $branch_name .= $branch->name . ', ';
            }
            $report_controller = new ReportsController();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:O1');
            $sheet->mergeCells('A2:O2');
            $sheet->mergeCells('A3:O3');
            $sheet->mergeCells('A4:O4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO CHI TIẾT HS FULL FEE ACTIVE');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(33);
            $sheet->getRowDimension('5')->setRowHeight(30);
            $sheet->setCellValue('A3', trim($branch_name, ', '));
            $sheet->setCellValue('A4', 'Tính tới tháng: ' . substr($p->a, 0, 7));

            $sheet->setCellValue('A5', 'STT/Ordinal numbers');
            $sheet->setCellValue('B5', 'Trung tâm/Branch');
            $sheet->setCellValue('C5', 'Mã CRM');
            $sheet->setCellValue('D5', 'Mã kế toán/LMS code');
            $sheet->setCellValue('E5', 'Họ tên học sinh/Student name');
            $sheet->setCellValue('F5', 'Phụ huynh/Parent name');
            $sheet->setCellValue('G5', 'Ngày sinh/Date of birthday');
            $sheet->setCellValue('H5', 'Lớp/Class');
            $sheet->setCellValue('I5', 'Sản phẩm/Product');
            $sheet->setCellValue('J5', 'Email phụ huynh');
            // $sheet->setCellValue('K5', 'Họ tên CM');
            $sheet->setCellValue('L5', 'Họ tên giáo viên/Teacher');
            $sheet->setCellValue('M5', 'Tên gói phí/Fee package');
            $sheet->setCellValue('N5', 'Số buổi gói phí/Lesson');
            $sheet->setCellValue('O5', 'Trạng thái/Status');
            $sheet->setCellValue('P5', 'Ngày bắt đầu học tại CMS');
            $sheet->setCellValue('Q5', 'Số buổi còn lại /Number of lessons left');
            $sheet->setCellValue('R5', 'Ngày kết thúc dự kiến/Expected end date');
            $sheet->setCellValue('S5', 'Ngày bắt đầu/Start date');
            if(in_array($request->users_data->role_id,['999999999'])){
                $sheet->setCellValue('T5', 'Số điện thoại');
            }
            $sheet->setCellValue('U5', 'Số tiền phải đóng');
            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(0);
            $sheet->getColumnDimension('D')->setWidth(40);
            $sheet->getColumnDimension('E')->setWidth(40);
            $sheet->getColumnDimension('F')->setWidth(40);
            $sheet->getColumnDimension('G')->setWidth(40);
            $sheet->getColumnDimension('H')->setWidth(40);
            $sheet->getColumnDimension('I')->setWidth(40);
            $sheet->getColumnDimension('J')->setWidth(40);
            $sheet->getColumnDimension('K')->setWidth(0);
            $sheet->getColumnDimension('L')->setWidth(40);
            $sheet->getColumnDimension('M')->setWidth(40);
            $sheet->getColumnDimension('N')->setWidth(40);
            $sheet->getColumnDimension('O')->setWidth(40);
            $sheet->getColumnDimension('P')->setWidth(0);
            $sheet->getColumnDimension('Q')->setWidth(40);
            $sheet->getColumnDimension('R')->setWidth(40);
            $sheet->getColumnDimension('S')->setWidth(30);
            $sheet->getColumnDimension('T')->setWidth(30);
            $sheet->getColumnDimension('U')->setWidth(30);
            ProcessExcel::styleCells($spreadsheet, "A1:N1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:N2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:N3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:N4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "M5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "N5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "O5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "P5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "Q5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "R5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "S5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            if(in_array($request->users_data->role_id,['999999999'])){
                ProcessExcel::styleCells($spreadsheet, "T5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            }
            ProcessExcel::styleCells($spreadsheet, "U5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('C' . $x, $students[$i]->crm_id);
                $sheet->setCellValue('D' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('E' . $x, $students[$i]->student_name);
                $sheet->setCellValue('F' . $x, $students[$i]->gud_name1);
                $sheet->setCellValue('G' . $x, $students[$i]->date_of_birth);
                $sheet->setCellValue('H' . $x, $students[$i]->cls_name);
                $sheet->setCellValue('I' . $x, $students[$i]->product_name);
                $sheet->setCellValue('J' . $x, $students[$i]->gud_email1);
                $sheet->setCellValue('K' . $x, $students[$i]->cm_name);
                $sheet->setCellValue('L' . $x, $students[$i]->teacher_name);
                $sheet->setCellValue('M' . $x, $students[$i]->tuition_fee_name);
                $sheet->setCellValue('N' . $x, $students[$i]->tuition_fee_session);
                $sheet->setCellValue('O' . $x, $students[$i]->count_recharge);
                $sheet->setCellValue('P' . $x, $students[$i]->enrolment_start_date_official_contract);
                $done_session = isset($students[$i]->done_session) ? $students[$i]->done_session : $report_controller->getDoneSessions($students[$i]);
                $sheet->setCellValue('Q' . $x, $students[$i]->summary_sessions-$done_session);
                $sheet->setCellValue('R' . $x, $students[$i]->enrolment_last_date);
                $sheet->setCellValue('S' . $x, $students[$i]->enrolment_start_date);
                if($request->users_data->role_id=='999999999'){
                    $sheet->setCellValue('T' . $x, "'".$students[$i]->gud_mobile1);
                }
                $sheet->setCellValue('U' . $x, $students[$i]->must_charge);
                $sheet->getRowDimension($x)->setRowHeight(23);

            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Báo cáo học sinh full fee active.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function export01b1(Request $request) {
        if ($session = $request->users_data) {
            $p = r::params($request, $session);
            $query = r::queryReport01b1($p, 0, 1);
            $students = u::query($query);
            // lấy ra tổng số thành công
            $success_total = r::queryReport01b1_success($p);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $sheet->mergeCells('A1:M1');
            $sheet->mergeCells('A2:M2');
            $sheet->mergeCells('A3:M3');
            $sheet->mergeCells('A4:M4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO DANH SÁCH CHI TIẾT HỌC SINH TỚI HẠN TÁI TỤC');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(30);
            $sheet->getRowDimension('4')->setRowHeight(30);
            $sheet->setCellValue('A3', 'Tính tới tháng: ' . substr($p->a, 0, 7));
            $sheet->setCellValue('A4', 'THÀNH CÔNG/TỔNG SỐ: '.$success_total.'/'.count($students));

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Mã CMS');
            $sheet->setCellValue('C5', 'Mã Kế Toán');
            $sheet->setCellValue('D5', 'Tên Học Sinh');
            $sheet->setCellValue('E5', 'Trung Tâm');
            $sheet->setCellValue('F5', 'Sản Phẩm');
            $sheet->setCellValue('G5', 'Lớp Học');
            $sheet->setCellValue('H5', 'Hạn Tái Tục');
            $sheet->setCellValue('I5', 'Kết Quả');
            $sheet->setCellValue('J5', 'Gói Tái Phí');
            $sheet->setCellValue('K5', 'Số Tiền Tái Phí');
            $sheet->setCellValue('L5', 'Tên EC');
            $sheet->setCellValue('M5', 'Mã EC');
            if(in_array($request->users_data->role_id,['999999999'])){
                $sheet->setCellValue('N5', 'Số điện thoại');
            }
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(40);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(30);
            $sheet->getColumnDimension('K')->setWidth(20);
            $sheet->getColumnDimension('L')->setWidth(30);
            $sheet->getColumnDimension('M')->setWidth(20); 
            if(in_array($request->users_data->role_id,['999999999'])){
                $sheet->getColumnDimension('N')->setWidth(20); 
            }

            ProcessExcel::styleCells($spreadsheet, "A1:M1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:M2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:M3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:M4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "M5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "N5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');

            for ($i = 0; $i < count($students); $i++) {
                $x = $i + 6;

                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->crm_id);
                $sheet->setCellValue('C' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('D' . $x, $students[$i]->student_name);
                $sheet->setCellValue('E' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('F' . $x, $students[$i]->product_name);
                $sheet->setCellValue('G' . $x, $students[$i]->class_name);
                $sheet->setCellValue('H' . $x, $students[$i]->last_date);
                $sheet->setCellValue('I' . $x, $students[$i]->status_title);
                $sheet->setCellValue('J' . $x, $students[$i]->status==1 ? $students[$i]->tuition_fee_name :'');
                $sheet->setCellValue('K' . $x, $students[$i]->status==1 ? apax_ada_format_number($students[$i]->renew_amount) : '');
                $sheet->setCellValue('L' . $x, $students[$i]->ec_name);
                $sheet->setCellValue('M' . $x, $students[$i]->ec_hrm_id);
                if(in_array($request->users_data->role_id,['999999999'])){
                    $sheet->setCellValue('N' . $x, $students[$i]->gud_mobile1);
                }
                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "J$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "K$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "L$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "M$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                if(in_array($request->users_data->role_id,['999999999'])){
                    ProcessExcel::styleCells($spreadsheet, "N$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                }
            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Danh Sách Học Sinh Tới Hạn Tái Phí.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function export01b2(Request $request) {
        if ($session = $request->users_data) {
            $p = r::params($request, $session);
            $query = r::queryReport01b2($p, 0, 1);
            $branches = u::query($query);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:E1');
            $sheet->mergeCells('A2:E2');
            $sheet->mergeCells('A3:E3');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO TỔNG HỢP HỌC SINH TỚI HẠN TÁI TỤC');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(30);
            $sheet->setCellValue('A3', 'Tính tới tháng: ' . substr($p->a, 0, 7));
            $sheet->setCellValue('A4', 'STT');
            $sheet->setCellValue('B4', 'Trung Tâm');
            $sheet->setCellValue('C4', 'Số Học Sinh Đến Hạn Tái Tục');
            $sheet->setCellValue('D4', 'Số Học Sinh Đóng Phí Tái Tục');
            $sheet->setCellValue('E4', 'Tỷ lệ tái tục');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(36);
            $sheet->getColumnDimension('C')->setWidth(39);
            $sheet->getColumnDimension('D')->setWidth(39);
            $sheet->getColumnDimension('E')->setWidth(23);
            ProcessExcel::styleCells($spreadsheet, "A1:E1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:E2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:E3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            for ($i = 0; $i < count($branches); $i++) {
                $x = $i + 5;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $branches[$i]->branch_name);
                $sheet->setCellValue('C' . $x, $branches[$i]->total_item);
                $sheet->setCellValue('D' . $x, $branches[$i]->success_item);
                $sheet->setCellValue('E' . $x, $branches[$i]->total_item?round(($branches[$i]->success_item)*100/ $branches[$i]->total_item,2):75);

                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Báo Cáo Tổng Hợp Học Sinh Tới Hạn Tái Phí.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function export01b3(Request $request) {
        if ($session = $request->users_data) {
            $p = Report::params($request, $session);
            $data = Report::dataReport01b3($request, $session,true);
            $cms = $data->list;
            $total = $data->sumar;
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:H1');
            $sheet->mergeCells('A2:H2');
            $sheet->mergeCells('A3:H3');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO DANH SÁCH CHI TIẾT HỌC SINH TỚI HẠN TÁI TỤC THEO EC');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(30);
            $sheet->setCellValue('A3', 'Tính tới tháng: ' . substr($p->a, 0, 7));
            $sheet->setCellValue('A4', 'STT');
            $sheet->setCellValue('B4', 'Trung tâm');
            $sheet->setCellValue('C4', 'Mã Nhân Viên');
            $sheet->setCellValue('D4', 'Họ Và Tên');
            $sheet->setCellValue('E4', 'Chức Danh');
            $sheet->setCellValue('F4', 'Học sinh đến hạn tái phí');
            $sheet->setCellValue('G4', 'Học sinh đã tái phí thành công');
            $sheet->setCellValue('H4', 'Tỷ Lệ HS Tái Tục (%)');
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(18);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(30);
            $sheet->getColumnDimension('H')->setWidth(30);
            ProcessExcel::styleCells($spreadsheet, "A1:H1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:H2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:H3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            for ($i = 0; $i < count($cms); $i++) {
                $x = $i + 5;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, isset($cms[$i]->branch_name) ? $cms[$i]->branch_name : '');
                $sheet->setCellValue('C' . $x, isset($cms[$i]->hrm_id) ? $cms[$i]->hrm_id : '');
                $sheet->setCellValue('D' . $x, isset($cms[$i]->ec_name) ? $cms[$i]->ec_name  : '');
                $sheet->setCellValue('E' . $x, isset($cms[$i]->role_name) ? $cms[$i]->role_name : '');
                $sheet->setCellValue('F' . $x, isset($cms[$i]->total_renew) ? $cms[$i]->total_renew : '');
                $sheet->setCellValue('G' . $x, isset($cms[$i]->success_renew) ? $cms[$i]->success_renew : '');
                $sheet->setCellValue('H' . $x, $cms[$i]->total_renew?round(($cms[$i]->success_renew)*100/ $cms[$i]->total_renew,2)."%":"70%");
                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
            }
            $sheet->setCellValue('A' . ($x+1), "Tổng");
            $sheet->setCellValue('F' . ($x+1), $total->total_item);
            $sheet->setCellValue('G' . ($x+1), $total->success_item);
            $sheet->setCellValue('H' . ($x+1), $total->rate_item."%");
            $sheet->mergeCells('A'.($x+1).':E'.($x+1));
            ProcessExcel::styleCells($spreadsheet, "A".($x+1), 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B".($x+1), 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C".($x+1), 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D".($x+1), 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E".($x+1), 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F".($x+1), 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G".($x+1), 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H".($x+1), 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Danh Sách Học Sinh Tới Hạn Tái Phí Theo EC.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function export02a(Request $request) {
        if ($session = $request->users_data) {
            $p = r::params($request, $session);
            $query = r::queryReport02a($p, 0, 1);
            $branches = u::query($query);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:L1');
            $sheet->mergeCells('A2:L2');
            $sheet->mergeCells('A3:L3');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO CHI TIẾT HỌC SINH PENDING');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(30);
            $sheet->setCellValue('A3', 'Tính tới tháng: ' . substr($p->a, 0, 7));
            $sheet->setCellValue('A4', 'STT');
            $sheet->setCellValue('B4', 'Trung Tâm');
            $sheet->setCellValue('C4', 'Mã CMS');
            $sheet->setCellValue('D4', 'Mã kế toán');
            $sheet->setCellValue('E4', 'Học sinh');
            $sheet->setCellValue('F4', 'Sản phẩm');
            $sheet->setCellValue('G4', 'Mã nhân viên');
            $sheet->setCellValue('H4', 'Tên nhân viên');
            $sheet->setCellValue('I4', 'Tên gói phí');
            $sheet->setCellValue('J4', 'Số buổi');
            $sheet->setCellValue('K4', 'Ngày dự kiến bắt đầu');
            $sheet->setCellValue('L4', 'Ngày dự kiến kết thúc');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(30);
            $sheet->getColumnDimension('I')->setWidth(40);
            $sheet->getColumnDimension('J')->setWidth(20);
            $sheet->getColumnDimension('K')->setWidth(30);
            $sheet->getColumnDimension('L')->setWidth(30);
            ProcessExcel::styleCells($spreadsheet, "A1:L1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:L2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:L3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            for ($i = 0; $i < count($branches); $i++) {
                $x = $i + 5;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $branches[$i]->branch_name);
                $sheet->setCellValue('C' . $x, $branches[$i]->crm_id);
                $sheet->setCellValue('D' . $x, $branches[$i]->accounting_id);
                $sheet->setCellValue('E' . $x, $branches[$i]->student_name);
                $sheet->setCellValue('F' . $x, $branches[$i]->product_name);
                $sheet->setCellValue('G' . $x, $branches[$i]->hrm_id);
                $sheet->setCellValue('H' . $x, $branches[$i]->cm_name);
                $sheet->setCellValue('I' . $x, $branches[$i]->tuition_fee_name);
                $sheet->setCellValue('J' . $x, $branches[$i]->summary_sessions);
                $sheet->setCellValue('K' . $x, $branches[$i]->start_date);
                $sheet->setCellValue('L' . $x, $branches[$i]->end_date);
                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "J$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "K$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "L$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');

            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="BÁO CÁO CHI TIẾT HỌC SINH PENDING.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function export02b(Request $request) {
        if ($session = $request->users_data) {
            $p = r::params($request, $session);
            $query = r::queryReport02b($p, 0, 1);
            $branches = u::query($query);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:M1');
            $sheet->mergeCells('A2:M2');
            $sheet->mergeCells('A3:M3');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO CHI TIẾT HỌC SINH BẢO LƯU');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(30);
            $sheet->setCellValue('A3', 'Tính tới tháng: ' . substr($p->a, 0, 7));
            $sheet->setCellValue('A4', 'STT');
            $sheet->setCellValue('B4', 'Trung Tâm');
            $sheet->setCellValue('C4', 'Mã CMS');
            $sheet->setCellValue('D4', 'Mã kế toán');
            $sheet->setCellValue('E4', 'Học sinh');
            $sheet->setCellValue('F4', 'Sản phẩm');
            $sheet->setCellValue('G4', 'Mã nhân viên');
            $sheet->setCellValue('H4', 'Tên nhân viên');
            $sheet->setCellValue('I4', 'Tên gói phí');
            $sheet->setCellValue('J4', 'Số buổi');
            $sheet->setCellValue('K4', 'Ngày dự kiến bắt đầu');
            $sheet->setCellValue('L4', 'Ngày dự kiến kết thúc');
            $sheet->setCellValue('M4', 'Loại bảo lưu');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(30);
            $sheet->getColumnDimension('I')->setWidth(40);
            $sheet->getColumnDimension('J')->setWidth(20);
            $sheet->getColumnDimension('K')->setWidth(30);
            $sheet->getColumnDimension('L')->setWidth(30);
            $sheet->getColumnDimension('M')->setWidth(30);
            ProcessExcel::styleCells($spreadsheet, "A1:M1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:M2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:M3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "M4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            for ($i = 0; $i < count($branches); $i++) {
                $x = $i + 5;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $branches[$i]->branch_name);
                $sheet->setCellValue('C' . $x, $branches[$i]->crm_id);
                $sheet->setCellValue('D' . $x, $branches[$i]->accounting_id);
                $sheet->setCellValue('E' . $x, $branches[$i]->student_name);
                $sheet->setCellValue('F' . $x, $branches[$i]->product_name);
                $sheet->setCellValue('G' . $x, $branches[$i]->hrm_id);
                $sheet->setCellValue('H' . $x, $branches[$i]->cm_name);
                $sheet->setCellValue('I' . $x, $branches[$i]->tuition_fee_name);
                $sheet->setCellValue('J' . $x, $branches[$i]->summary_sessions);
                $sheet->setCellValue('K' . $x, $branches[$i]->start_date);
                $sheet->setCellValue('L' . $x, $branches[$i]->end_date);
                $sheet->setCellValue('M' . $x, $branches[$i]->is_reserved ? "Không giữ chỗ":"Giữ chỗ");
                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "J$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "K$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "L$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "M$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="BÁO CÁO CHI TIẾT HỌC SINH BẢO LƯU.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function checkinListExport(Request $request) {
        set_time_limit(300);
        if ($session = $request->users_data) {
            $obj_student = new Checkin();
            $list = $obj_student->getStudentInfo($request,$limit=false);
            $list = $list->data;
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:T1');
            $sheet->mergeCells('A2:T2');
            $sheet->mergeCells('A3:T3');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'DANH SÁCH HỌC SINH CHECKIN');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(30);
            $sheet->setCellValue('A4', 'STT');
            $sheet->setCellValue('B4', 'Tên HS');
            $sheet->setCellValue('C4', 'Mã CMS');
            $sheet->setCellValue('D4', 'Trải nghiệm');
            $sheet->setCellValue('E4', 'Giới tính');
            $sheet->setCellValue('F4', 'Trường');
            $sheet->setCellValue('G4', 'Nguồn');
            $sheet->setCellValue('H4', 'Ngày sinh');
            $sheet->setCellValue('I4', 'Họ Tên Bố Mẹ');
            if($request->users_data->role_id=='999999999'||$request->users_data->id=='140'){
                $sheet->setCellValue('J4', 'Điện Thoại Phụ Huynh');
            }
            $sheet->setCellValue('K4', 'Địa Chỉ');
            $sheet->setCellValue('L4', 'EC Tư Vấn');
            $sheet->setCellValue('M4', 'CS');
            $sheet->setCellValue('N4', 'Trung Tâm');
            $sheet->setCellValue('O4', 'Trung Tâm Chuyển Đến');
            $sheet->setCellValue('P4', 'Thời Điểm');
            $sheet->setCellValue('Q4', 'Ngày/Giờ Checkin');
            $sheet->setCellValue('R4', 'Người Tạo');
            $sheet->setCellValue('S4', 'Đã checkin');
            $sheet->setCellValue('T4', 'Trạng Thái');

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(30);
            $sheet->getColumnDimension('I')->setWidth(40);
            if($request->users_data->role_id=='999999999'||$request->users_data->id=='140'){
                $sheet->getColumnDimension('J')->setWidth(30);
            }else{
                $sheet->getColumnDimension('J')->setWidth(0);
            }
            $sheet->getColumnDimension('K')->setWidth(30);
            $sheet->getColumnDimension('L')->setWidth(30);
            $sheet->getColumnDimension('M')->setWidth(30);
            $sheet->getColumnDimension('N')->setWidth(30);
            $sheet->getColumnDimension('O')->setWidth(30);
            $sheet->getColumnDimension('P')->setWidth(30);
            $sheet->getColumnDimension('Q')->setWidth(30);
            $sheet->getColumnDimension('R')->setWidth(30);
            $sheet->getColumnDimension('S')->setWidth(30);
            $sheet->getColumnDimension('T')->setWidth(30);
            ProcessExcel::styleCells($spreadsheet, "A1:T1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:T2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:T3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "M4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "N4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "O4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "P4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "Q4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "R4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "S4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "T4", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            $arr_status_name = array(
                0=>'CHỜ DUYỆT ĐI',
                1=>'CHỜ DUYỆT ĐẾN',
                2=>'ĐÃ CHUYỂN TT',
                3=>'TT CHUYỂN TỪ CHỐI',
                4=>'TT NHẬN TỪ CHỐI',
            );
            for ($i = 0; $i < count($list); $i++) {
                $x = $i + 5;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $list[$i]->name);
                $sheet->setCellValue('C' . $x, $list[$i]->crm_id);
                $sheet->setCellValue('D' . $x, $list[$i]->shift_id  > 0 ? 'Đã học thử':'Chưa học thử');
                $sheet->setCellValue('E' . $x, $list[$i]->gender=='F'?'Nữ':'Nam');
                $sheet->setCellValue('F' . $x, $list[$i]->school);
                $sheet->setCellValue('G' . $x, $list[$i]->source_name);
                $sheet->setCellValue('H' . $x, $list[$i]->date_of_birth);
                $sheet->setCellValue('I' . $x, $list[$i]->gud_name);
                if($request->users_data->role_id=='999999999'||$request->users_data->id=='140'){
                    $sheet->setCellValue('J' . $x, $list[$i]->gud_mobile1);
                }
                $sheet->setCellValue('K' . $x, $list[$i]->address);
                $sheet->setCellValue('L' . $x, $list[$i]->ec_name);
                $sheet->setCellValue('M' . $x, $list[$i]->cm_name);
                $sheet->setCellValue('N' . $x, $list[$i]->branch_name);
                $sheet->setCellValue('O' . $x, $list[$i]->to_branch_name);
                $sheet->setCellValue('P' . $x, $list[$i]->created_at);
                $sheet->setCellValue('Q' . $x, $list[$i]->checkin_at);
                $sheet->setCellValue('R' . $x, $list[$i]->creator_name);
                $sheet->setCellValue('S' . $x, $list[$i]->checked==1?'Đã checkin':'Chưa checkin');
                $sheet->setCellValue('T' . $x, isset($arr_status_name[$list[$i]->status_transfer])?$arr_status_name[$list[$i]->status_transfer]:'');
                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "J$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "K$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "L$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "M$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "N$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "O$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "P$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "Q$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "R$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "S$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "T$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="DANH SACH HOC SINH CHECKIN.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function report_r01(Request $request) {
        if ($session = $request->users_data) {
            $p = r::params($request, $session);
            $query = r::report_r01( $p, 0, 1);
            $students = u::query($query);

            $branchs = u::query("SELECT name from branches WHERE id in ($p->s)");
            $branch_name = '';
            foreach ($branchs as $branch) {
                $branch_name .= $branch->name . ', ';
            }
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:M1');
            $sheet->mergeCells('A2:M2');
            $sheet->mergeCells('A3:M3');
            $sheet->mergeCells('A4:M4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'THÔNG TIN KHÁCH SALES HUB LÊN GÓI PHÍ TẠI TRUNG TÂM');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(33);
            $sheet->getRowDimension('5')->setRowHeight(30);
            $sheet->setCellValue('A3', trim($branch_name, ', '));
            $sheet->setCellValue('A4', 'Tính tới tháng: ' . substr($p->a, 0, 7));

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Trung tâm');
            $sheet->setCellValue('C5', 'Họ tên học sinh');
            $sheet->setCellValue('D5', 'Mã CRM');
            $sheet->setCellValue('E5', 'Mã Cyber');
            $sheet->setCellValue('F5', 'Ngày sinh');
            $sheet->setCellValue('G5', 'Ngày checkin');
            $sheet->setCellValue('H5', 'Ngày Mua gói phí');
            $sheet->setCellValue('I5', 'Tên Gói phí');
            $sheet->setCellValue('J5', 'Giá trị gói phí');
            $sheet->setCellValue('K5', 'Số tiền đã đóng');
            $sheet->setCellValue('L5', 'Người tạo checkin');
            $sheet->setCellValue('M5', 'Nguồn chi tiết');
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(40);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);
            $sheet->getColumnDimension('J')->setWidth(20);
            $sheet->getColumnDimension('K')->setWidth(20);
            $sheet->getColumnDimension('L')->setWidth(20);
            $sheet->getColumnDimension('M')->setWidth(30);
            
            ProcessExcel::styleCells($spreadsheet, "A1:M1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:M2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:M3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:M4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "M5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            $must_charge =0;
            $total_charged =0;
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('C' . $x, $students[$i]->student_name);
                $sheet->setCellValue('D' . $x, $students[$i]->crm_id);
                $sheet->setCellValue('E' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('F' . $x, $students[$i]->date_of_birth);
                $sheet->setCellValue('G' . $x, $students[$i]->date_checkin);
                $sheet->setCellValue('H' . $x, $students[$i]->date_create_contract);
                $sheet->setCellValue('I' . $x, $students[$i]->tuition_fee_name);
                $sheet->setCellValue('J' . $x, $students[$i]->must_charge);
                $sheet->setCellValue('K' . $x, $students[$i]->total_charged);
                $sheet->setCellValue('L' . $x, $students[$i]->creator_checkin_name);
                $sheet->setCellValue('M' . $x, $students[$i]->source_detail);
                $sheet->getRowDimension($x)->setRowHeight(23);
                $must_charge += (int)$students[$i]->must_charge;
                $total_charged += (int)$students[$i]->total_charged;
            }
            
            $sheet->mergeCells('A'.($x+1).':I'.($x+1));
            $sheet->setCellValue('A' . ($x+1),"Tổng");
            $sheet->setCellValue('J' . ($x+1), $must_charge);
            $sheet->setCellValue('K' . ($x+1), $total_charged);
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="THÔNG TIN KHÁCH SALES HUB LÊN GÓI PHÍ TẠI TRUNG TÂM.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function report_r02(Request $request) {
        if ($session = $request->users_data) {
            $p = r::params($request, $session);
            $query = r::report_r02( $p, 0, 1);
            $students = u::query($query);
            $summary = (object)array(
                'must_charge'=>0,
                'total_charged'=>0,
            );
            foreach($students AS $row){
                $summary->must_charge += (int)$row->must_charge;
                $summary->total_charged += (int)$row->total_charged;
            }
            $branchs = u::query("SELECT name from branches WHERE id in ($p->s)");
            $branch_name = '';
            foreach ($branchs as $branch) {
                $branch_name .= $branch->name . ', ';
            }
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:J1');
            $sheet->mergeCells('A2:J2');
            $sheet->mergeCells('A3:J3');
            $sheet->mergeCells('A4:J4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'THÔNG TIN KHÁCH MARKETING LÊN GÓI PHÍ TẠI TRUNG TÂM');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(33);
            $sheet->getRowDimension('5')->setRowHeight(30);
            $sheet->setCellValue('A3', trim($branch_name, ', '));
            $sheet->setCellValue('A4', 'Tính tới tháng: ' . substr($p->a, 0, 7));

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Trung tâm');
            $sheet->setCellValue('C5', 'Họ tên học sinh');
            $sheet->setCellValue('D5', 'Mã Cyber');
            $sheet->setCellValue('E5', 'Ngày sinh');
            $sheet->setCellValue('F5', 'Ngày Mua gói phí');
            $sheet->setCellValue('G5', 'Tên Gói phí');
            $sheet->setCellValue('H5', 'Giá trị gói phí');
            $sheet->setCellValue('I5', 'Số tiền đã đóng');
            if(in_array($request->users_data->role_id,[1200,'999999999'])){
                $sheet->setCellValue('J5', 'SĐT phụ huynh');
            }
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(40);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);
            $sheet->getColumnDimension('J')->setWidth(20);
            
            ProcessExcel::styleCells($spreadsheet, "A1:J1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:J2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:J3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:J4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
       
          
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('C' . $x, $students[$i]->student_name);
                $sheet->setCellValue('D' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('E' . $x, $students[$i]->date_of_birth);
                $sheet->setCellValue('F' . $x, $students[$i]->date_create_contract);
                $sheet->setCellValue('G' . $x, $students[$i]->tuition_fee_name);
                $sheet->setCellValue('H' . $x, $students[$i]->must_charge);
                $sheet->setCellValue('I' . $x, $students[$i]->total_charged);
                if(in_array($request->users_data->role_id,[1200,'999999999'])){
                    $sheet->setCellValue('J' . $x, $students[$i]->gud_mobile1);
                }
                $sheet->getRowDimension($x)->setRowHeight(23);

            }
            $sheet->mergeCells('A'.($x+1).':G'.($x+1));
            $sheet->setCellValue('A' . ($x+1),"Tổng");
            $sheet->setCellValue('H' . ($x+1), $summary->must_charge);
            $sheet->setCellValue('I' . ($x+1), $summary->total_charged);
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="THÔNG TIN KHÁCH MARKETING LÊN GÓI PHÍ TẠI TRUNG TÂM.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function report_r04(Request $request) {
        if ($session = $request->users_data) {
            $p = r::params($request, $session);
            $query = r::report_r04( $p, 0, 1);
            $students = u::query($query);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:J1');
            $sheet->mergeCells('A2:J2');
            $sheet->mergeCells('A3:J3');
            $sheet->mergeCells('A4:J4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO HỌC SINH MỚI');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(33);
            $sheet->getRowDimension('5')->setRowHeight(30);

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Mã cyber');
            $sheet->setCellValue('C5', 'Tên học sinh');
            $sheet->setCellValue('D5', 'Trung tâm');
            $sheet->setCellValue('E5', 'Sản phẩm');
            $sheet->setCellValue('F5', 'Loại khách hàng');
            $sheet->setCellValue('G5', 'Gói phí');
            $sheet->setCellValue('H5', 'Ngày đóng phí gần nhất');
            $sheet->setCellValue('I5', 'Số tiền phải đóng');
            $sheet->setCellValue('J5', 'Đã đóng');
            $sheet->setCellValue('K5', 'Tư vấn viên');
            $sheet->setCellValue('L5', 'Tình trạng học sinh');
            if(in_array($request->users_data->role_id,['999999999'])){
                $sheet->setCellValue('M5', 'SĐT phụ huynh');
            }
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);
            $sheet->getColumnDimension('J')->setWidth(20);
            $sheet->getColumnDimension('K')->setWidth(20);
            $sheet->getColumnDimension('L')->setWidth(20);
            $sheet->getColumnDimension('M')->setWidth(20);
            
            ProcessExcel::styleCells($spreadsheet, "A1:M1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:M2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:M3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:M4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "M5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            
          
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('C' . $x, $students[$i]->name);
                $sheet->setCellValue('D' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('E' . $x, $students[$i]->product_name);
                $sheet->setCellValue('F' . $x, $students[$i]->contract_type);
                $sheet->setCellValue('G' . $x, $students[$i]->tuition_fee_name);
                $sheet->setCellValue('H' . $x, $students[$i]->charge_date);
                $sheet->setCellValue('I' . $x, $students[$i]->must_charge);
                $sheet->setCellValue('J' . $x, $students[$i]->total_charged);
                $sheet->setCellValue('K' . $x, $students[$i]->ec_name);
                $sheet->setCellValue('L' . $x, $students[$i]->status_student);
                if(in_array($request->users_data->role_id,['999999999'])){
                    $sheet->setCellValue('M' . $x, $students[$i]->gud_mobile1);
                }
                $sheet->getRowDimension($x)->setRowHeight(23);

            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="BÁO CÁO HỌC SINH MỚI.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function report_r05(Request $request) {
        if ($session = $request->users_data) {
            $p = r::params($request, $session);
            $query = r::report_r05( $p, 0, 1);
            $students = u::query($query);
            $summary = (object)array(
                'must_charge'=>0,
                'amount'=>0,
            );
            foreach($students AS $row){
                $summary->must_charge += (int)$row->must_charge;
                $summary->amount += (int)$row->amount;
                $summary->ban_cheo += $row->count_recharge ==0 && $row->pre_branch ? (int)$row->amount/2 : 0;
                $summary->thuc_thu += $row->count_recharge ==0 && $row->pre_branch ? (int)$row->amount/2 : (int)$row->amount;
            }
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:J1');
            $sheet->mergeCells('A2:J2');
            $sheet->mergeCells('A3:J3');
            $sheet->mergeCells('A4:J4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO DOANH THU');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(33);
            $sheet->getRowDimension('5')->setRowHeight(30);

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Ngày hạch toán');
            $sheet->setCellValue('C5', 'Ngày thu');
            $sheet->setCellValue('D5', 'Mã khách hàng');
            $sheet->setCellValue('E5', 'Phân loại học viên (Cũ/mới)');
            $sheet->setCellValue('F5', 'Tên khách hàng');
            $sheet->setCellValue('G5', 'Người nộp tiền');
            $sheet->setCellValue('H5', 'Tên gói phí');
            $sheet->setCellValue('I5', 'Trung tâm');
            $sheet->setCellValue('J5', 'Thời gian học');
            $sheet->setCellValue('K5', 'Mã nhân viên');
            $sheet->setCellValue('L5', 'Tên tư vấn viên');
            $sheet->setCellValue('M5', 'Trung tâm NVKD');
            $sheet->setCellValue('N5', 'Diễn giải');
            $sheet->setCellValue('O5', 'GT khóa học');
            $sheet->setCellValue('P5', 'Tiền giảm khác');
            $sheet->setCellValue('Q5', 'Mã CC/GG');
            $sheet->setCellValue('R5', 'Số tiền giảm trừ theo mã CC/CK');
            $sheet->setCellValue('S5', 'Tiền sau CK');
            $sheet->setCellValue('T5', 'Đã nộp');
            $sheet->setCellValue('U5', 'Số tiền còn lại phải thu');
            $sheet->setCellValue('V5', 'Số chứng từ PNH');
            $sheet->setCellValue('W5', 'Doanh số bán chéo');
            $sheet->setCellValue('X5', 'Trung tâm  bán chéo');
            $sheet->setCellValue('Y5', 'TVV Trung tâm bán chéo');
            $sheet->setCellValue('Z5', 'Doanh số thực thu của  trung tâm');
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(30);
            $sheet->getColumnDimension('H')->setWidth(30);
            $sheet->getColumnDimension('I')->setWidth(30);
            $sheet->getColumnDimension('J')->setWidth(30);
            $sheet->getColumnDimension('K')->setWidth(30);
            $sheet->getColumnDimension('L')->setWidth(30);
            $sheet->getColumnDimension('M')->setWidth(30);
            $sheet->getColumnDimension('N')->setWidth(30);
            $sheet->getColumnDimension('O')->setWidth(30);
            $sheet->getColumnDimension('P')->setWidth(30);
            $sheet->getColumnDimension('Q')->setWidth(30);
            $sheet->getColumnDimension('R')->setWidth(30);
            $sheet->getColumnDimension('S')->setWidth(30);
            $sheet->getColumnDimension('T')->setWidth(30);
            $sheet->getColumnDimension('U')->setWidth(30);
            $sheet->getColumnDimension('V')->setWidth(30);
            $sheet->getColumnDimension('W')->setWidth(30);
            $sheet->getColumnDimension('X')->setWidth(30);
            $sheet->getColumnDimension('Y')->setWidth(30);
            $sheet->getColumnDimension('Z')->setWidth(30);
            
            ProcessExcel::styleCells($spreadsheet, "A1:Z1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:Z2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:Z3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:Z4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "M5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "N5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "O5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "P5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "Q5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "R5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "S5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "T5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "U5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "V5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "W5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "X5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "Y5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "Z5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
          
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->charge_date);
                $sheet->setCellValue('C' . $x, $students[$i]->charge_date);
                $sheet->setCellValue('D' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('E' . $x, $students[$i]->count_recharge ==0 ? 'Mới' : 'Tái tục');
                $sheet->setCellValue('F' . $x, $students[$i]->name);
                $sheet->setCellValue('G' . $x, $students[$i]->gud_name1);
                $sheet->setCellValue('H' . $x, $students[$i]->tuition_fee_name);
                $sheet->setCellValue('I' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('J' . $x, $students[$i]->start_date);
                $sheet->setCellValue('K' . $x, $students[$i]->ec_hrm);
                $sheet->setCellValue('L' . $x, $students[$i]->ec_name);
                $sheet->setCellValue('M' . $x, $students[$i]->ec_branch);
                $sheet->setCellValue('N' . $x, $students[$i]->note);
                $sheet->setCellValue('O' . $x, $students[$i]->tuition_price);
                $sheet->setCellValue('P' . $x, $students[$i]->total_discount);
                $sheet->setCellValue('Q' . $x, $students[$i]->coupon);
                $sheet->setCellValue('R' . $x, $students[$i]->discount_coupon);
                $sheet->setCellValue('S' . $x, $students[$i]->must_charge);
                $sheet->setCellValue('T' . $x, $students[$i]->amount);
                $sheet->setCellValue('U' . $x, $students[$i]->debt);
                $sheet->setCellValue('V' . $x, $students[$i]->contract_accounting);
                $sheet->setCellValue('W' . $x, $students[$i]->count_recharge ==0 && $students[$i]->pre_branch ? $students[$i]->amount/2 :'');
                $sheet->setCellValue('X' . $x, $students[$i]->count_recharge ==0 && $students[$i]->pre_branch ? $students[$i]->pre_branch :'');
                $sheet->setCellValue('Y' . $x, $students[$i]->count_recharge ==0 && $students[$i]->pre_branch ? $students[$i]->pre_ec_name :'');
                $sheet->setCellValue('Z' . $x, $students[$i]->count_recharge ==0 && $students[$i]->pre_branch ? $students[$i]->amount/2 :$students[$i]->amount);
                $sheet->getRowDimension($x)->setRowHeight(23);

            }
            $sheet->mergeCells('A'.($x+1).':R'.($x+1));
            $sheet->mergeCells('U'.($x+1).':Z'.($x+1));
            $sheet->setCellValue('A' . ($x+1),"Tổng");
            $sheet->setCellValue('S' . ($x+1), $summary->must_charge);
            $sheet->setCellValue('T' . ($x+1), $summary->amount);
            $sheet->setCellValue('W' . ($x+1), $summary->ban_cheo);
            $sheet->setCellValue('Z' . ($x+1), $summary->thuc_thu);

            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="BÁO CÁO DOANH SỐ.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function report_r06(Request $request) {
        if ($session = $request->users_data) {
            $p = r::params($request, $session);
            $query = r::report_r06( $p,$request, 0, 1);
            $students = u::query($query);
            $summary = (object)array(
                'must_charge'=>0,
                'total_charged'=>0,
            );
            foreach($students AS $row){
                $summary->must_charge += (int)$row->must_charge;
                $summary->total_charged += (int)$row->total_charged;
            }
            $branchs = u::query("SELECT name from branches WHERE id in ($p->s)");
            $branch_name = '';
            foreach ($branchs as $branch) {
                $branch_name .= $branch->name . ', ';
            }
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:J1');
            $sheet->mergeCells('A2:J2');
            $sheet->mergeCells('A3:J3');
            $sheet->mergeCells('A4:J4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO DOANH THU NGUỒN');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(33);
            $sheet->getRowDimension('5')->setRowHeight(30);
            $sheet->setCellValue('A3', trim($branch_name, ', '));
            $sheet->setCellValue('A4', 'Tính tới tháng: ' . substr($p->a, 0, 7));

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Trung tâm');
            $sheet->setCellValue('C5', 'Họ tên học sinh');
            $sheet->setCellValue('D5', 'Mã Cyber');
            $sheet->setCellValue('E5', 'Ngày sinh');
            $sheet->setCellValue('F5', 'Ngày Mua gói phí');
            $sheet->setCellValue('G5', 'Tên Gói phí');
            $sheet->setCellValue('H5', 'Nguồn');
            $sheet->setCellValue('I5', 'Nguồn chi tiết');
            $sheet->setCellValue('J5', 'Loại hợp đồng');
            $sheet->setCellValue('K5', 'Giá trị gói phí');
            $sheet->setCellValue('L5', 'Số tiền đã đóng');
            if(in_array($request->users_data->role_id,[1200,'999999999'])){
                $sheet->setCellValue('M5', 'SĐT phụ huynh');
            }
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(40);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(30);
            $sheet->getColumnDimension('J')->setWidth(20);
            $sheet->getColumnDimension('K')->setWidth(20);
            $sheet->getColumnDimension('L')->setWidth(20);
            $sheet->getColumnDimension('M')->setWidth(20);
            
            ProcessExcel::styleCells($spreadsheet, "A1:M1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:M2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:M3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:M4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "M5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
          
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('C' . $x, $students[$i]->student_name);
                $sheet->setCellValue('D' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('E' . $x, $students[$i]->date_of_birth);
                $sheet->setCellValue('F' . $x, $students[$i]->date_create_contract);
                $sheet->setCellValue('G' . $x, $students[$i]->tuition_fee_name);
                $sheet->setCellValue('H' . $x, $students[$i]->source_name);
                $sheet->setCellValue('I' . $x, $students[$i]->source_detail);
                $sheet->setCellValue('J' . $x, $students[$i]->contract_type);
                $sheet->setCellValue('K' . $x, $students[$i]->must_charge);
                $sheet->setCellValue('L' . $x, $students[$i]->total_charged);
                if(in_array($request->users_data->role_id,[1200,'999999999'])){
                    $sheet->setCellValue('M' . $x, $students[$i]->gud_mobile1);
                }
                $sheet->getRowDimension($x)->setRowHeight(23);

            }
            $sheet->mergeCells('A'.($x+1).':J'.($x+1));
            $sheet->setCellValue('A' . ($x+1),"Tổng");
            $sheet->setCellValue('K' . ($x+1), $summary->must_charge);
            $sheet->setCellValue('L' . ($x+1), $summary->total_charged);
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="THÔNG TIN KHÁCH MARKETING LÊN GÓI PHÍ TẠI TRUNG TÂM.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function report_r07(Request $request) {
        if ($session = $request->users_data) {
            if(!$request->report_month){
                $report_week_info = u::first("SELECT * FROM report_weeks WHERE start_date <= CURRENT_DATE AND end_date >= CURRENT_DATE");
                $request->report_month = $report_week_info->year."_".$report_week_info->group."_".$report_week_info->month;
            }
            $p = r::params($request, $session);
            $query = r::report_r07( $p,$request, 0, 1);
            $students = u::query($query);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:W1');
            $sheet->mergeCells('A2:W2');
            $sheet->mergeCells('A3:W3');
            $sheet->mergeCells('A4:W4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO TÌNH HÌNH HỌC SINH');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(20);
            $sheet->getRowDimension('4')->setRowHeight(20);
            $sheet->getRowDimension('5')->setRowHeight(30);

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Trung tâm');
            $sheet->setCellValue('C5', 'Tên Lớp');
            $sheet->setCellValue('D5', 'Giáo viên');
            $sheet->setCellValue('E5', 'Level');
            $sheet->setCellValue('F5', 'Mã LMS');
            $sheet->setCellValue('G5', 'Mã HS');
            $sheet->setCellValue('H5', 'Họ và tên');
            $sheet->setCellValue('I5', 'Giới tính');
            $sheet->setCellValue('J5', 'Thời gian đăng ký');
            $sheet->setCellValue('K5', 'Trạng thái');
            $sheet->setCellValue('L5', 'Ngày đăng ký');
            $sheet->setCellValue('M5', 'Ghi chú');
            $sheet->setCellValue('N5', 'Chương trình');
            $sheet->setCellValue('O5', 'Điểm đầu vào');
            $sheet->setCellValue('P5', 'Điểm lần 1');
            $sheet->setCellValue('Q5', 'Điểm lần 2');
            $sheet->setCellValue('R5', 'Điểm lần 3');
            $sheet->setCellValue('S5', 'Điểm lần 4');
            $sheet->setCellValue('T5', 'Điểm gần nhất');
            $sheet->setCellValue('U5', 'Xếp loại');
            $sheet->setCellValue('V5', 'Nhận xét của GV');
            $sheet->setCellValue('W5', 'Đề xuất của GV');
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);
            $sheet->getColumnDimension('J')->setWidth(20);
            $sheet->getColumnDimension('K')->setWidth(20);
            $sheet->getColumnDimension('L')->setWidth(20);
            $sheet->getColumnDimension('M')->setWidth(20);
            $sheet->getColumnDimension('N')->setWidth(20);
            $sheet->getColumnDimension('O')->setWidth(20);
            $sheet->getColumnDimension('P')->setWidth(20);
            $sheet->getColumnDimension('Q')->setWidth(20);
            $sheet->getColumnDimension('R')->setWidth(20);
            $sheet->getColumnDimension('S')->setWidth(20);
            $sheet->getColumnDimension('T')->setWidth(20);
            $sheet->getColumnDimension('U')->setWidth(20);
            $sheet->getColumnDimension('V')->setWidth(30);
            $sheet->getColumnDimension('W')->setWidth(30);
            
            ProcessExcel::styleCells($spreadsheet, "A1:W1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:W2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:W3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:W4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "M5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "N5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "O5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "P5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "Q5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "R5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "S5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "T5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "U5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "V5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "W5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
          
            $arr_status = [
                ''=>'',
                '1'=>"Giỏi",
                '2'=>"Khá",
                '3'=>"Trung bình",
                '4'=>"Yếu"];
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('C' . $x, $students[$i]->class_name);
                $sheet->setCellValue('D' . $x, $students[$i]->teacher_name);
                $sheet->setCellValue('E' . $x, $students[$i]->level_name);
                $sheet->setCellValue('F' . $x, $students[$i]->crm_id);
                $sheet->setCellValue('G' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('H' . $x, $students[$i]->student_name);
                $sheet->setCellValue('I' . $x, $students[$i]->gender);
                $sheet->setCellValue('J' . $x, $students[$i]->thoigian_dangky);
                $sheet->setCellValue('K' . $x, $students[$i]->student_status);
                $sheet->setCellValue('L' . $x, $students[$i]->ngay_dangky);
                $sheet->setCellValue('M' . $x, $students[$i]->note);
                $sheet->setCellValue('N' . $x, $students[$i]->product_name);
                $sheet->setCellValue('O' . $x, $students[$i]->score_demo);
                $sheet->setCellValue('P' . $x, $students[$i]->score_week_1);
                $sheet->setCellValue('Q' . $x, $students[$i]->score_week_2);
                $sheet->setCellValue('R' . $x, $students[$i]->score_week_3);
                $sheet->setCellValue('S' . $x, $students[$i]->score_week_4);
                $sheet->setCellValue('T' . $x, $students[$i]->score_week_last);
                $sheet->setCellValue('U' . $x, $arr_status[$students[$i]->report_type]);
                $sheet->setCellValue('V' . $x, $students[$i]->comment);
                $sheet->setCellValue('W' . $x, $students[$i]->suggestion);
                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "J$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "K$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "L$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "M$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "N$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "O$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "P$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "Q$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "R$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "S$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "T$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "U$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "V$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "W$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="BÁO CÁO TÌNH HÌNH HỌC SINH.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function report_r08(Request $request) {
        if ($session = $request->users_data) {
            if(!$request->report_month){
                $report_week_info = u::first("SELECT * FROM report_weeks WHERE start_date <= CURRENT_DATE AND end_date >= CURRENT_DATE");
                $request->report_month = $report_week_info->year."_".$report_week_info->group."_".$report_week_info->month;
            }
            $p = r::params($request, $session);
            $query = r::report_r08( $p,$request, 0, 1);
            $students = u::query($query);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:W1');
            $sheet->mergeCells('A2:W2');
            $sheet->mergeCells('A3:W3');
            $sheet->mergeCells('A4:W4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO TÌNH HÌNH HỌC SINH');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(20);
            $sheet->getRowDimension('4')->setRowHeight(20);
            $sheet->getRowDimension('5')->setRowHeight(30);

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Trung tâm');
            $sheet->setCellValue('C5', 'Tên Lớp');
            $sheet->setCellValue('D5', 'Giáo viên');
            $sheet->setCellValue('E5', 'Level');
            $sheet->setCellValue('F5', 'Mã LMS');
            $sheet->setCellValue('G5', 'Mã HS');
            $sheet->setCellValue('H5', 'Họ và tên');
            $sheet->setCellValue('I5', 'Giới tính');
            $sheet->setCellValue('J5', 'Thời gian đăng ký');
            $sheet->setCellValue('K5', 'Trạng thái');
            $sheet->setCellValue('L5', 'Ngày đăng ký');
            $sheet->setCellValue('M5', 'Ghi chú');
            $sheet->setCellValue('N5', 'Chương trình');
            $sheet->setCellValue('O5', 'Điểm đầu vào');
            $sheet->setCellValue('P5', 'Điểm của quý');
            $sheet->setCellValue('Q5', 'Xếp loại');
            $sheet->setCellValue('R5', 'Nhận xét của GV');
            $sheet->setCellValue('S5', 'Đề xuất của GV');
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);
            $sheet->getColumnDimension('J')->setWidth(20);
            $sheet->getColumnDimension('K')->setWidth(20);
            $sheet->getColumnDimension('L')->setWidth(20);
            $sheet->getColumnDimension('M')->setWidth(20);
            $sheet->getColumnDimension('N')->setWidth(20);
            $sheet->getColumnDimension('O')->setWidth(20);
            $sheet->getColumnDimension('P')->setWidth(20);
            $sheet->getColumnDimension('Q')->setWidth(20);
            $sheet->getColumnDimension('R')->setWidth(30);
            $sheet->getColumnDimension('S')->setWidth(30);
            
            ProcessExcel::styleCells($spreadsheet, "A1:S1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:S2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:S3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:S4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "J5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "K5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "L5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "M5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "N5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "O5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "P5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "Q5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "R5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "S5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
          
            $arr_status = [
                ''=>'',
                '1'=>"Giỏi",
                '2'=>"Khá",
                '3'=>"Trung bình",
                '4'=>"Yếu",
                '5'=>"Xuất sắc",
                '6'=>"Trung bình khá"
            ];
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('C' . $x, $students[$i]->class_name);
                $sheet->setCellValue('D' . $x, $students[$i]->teacher_name);
                $sheet->setCellValue('E' . $x, $students[$i]->level_name);
                $sheet->setCellValue('F' . $x, $students[$i]->crm_id);
                $sheet->setCellValue('G' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('H' . $x, $students[$i]->student_name);
                $sheet->setCellValue('I' . $x, $students[$i]->gender);
                $sheet->setCellValue('J' . $x, $students[$i]->thoigian_dangky);
                $sheet->setCellValue('K' . $x, $students[$i]->student_status);
                $sheet->setCellValue('L' . $x, $students[$i]->ngay_dangky);
                $sheet->setCellValue('M' . $x, $students[$i]->note);
                $sheet->setCellValue('N' . $x, $students[$i]->product_name);
                $sheet->setCellValue('O' . $x, $students[$i]->score_demo);
                $sheet->setCellValue('P' . $x, $students[$i]->score_week_1);
                $sheet->setCellValue('Q' . $x, $arr_status[$students[$i]->report_type]);
                $sheet->setCellValue('R' . $x, $students[$i]->comment);
                $sheet->setCellValue('S' . $x, $students[$i]->suggestion);
                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "J$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "K$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "L$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "M$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "N$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "O$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "P$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "Q$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "R$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "S$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="BÁO CÁO TÌNH HÌNH HỌC SINH IG - BH.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
    public function report_r09(Request $request) {
        if ($session = $request->users_data) {
           
            $p = r::params($request, $session);
            $query = r::report_r09( $p,$request, 0, 1);
            $students = u::query($query);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:I1');
            $sheet->mergeCells('A2:I2');
            $sheet->mergeCells('A3:I3');
            $sheet->mergeCells('A4:I4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO CHECKIN');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(20);
            $sheet->getRowDimension('4')->setRowHeight(20);
            $sheet->getRowDimension('5')->setRowHeight(30);

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Mã CRM');
            $sheet->setCellValue('C5', 'Tên học sinh');
            $sheet->setCellValue('D5', 'Trung tâm');
            $sheet->setCellValue('E5', 'TVV');
            $sheet->setCellValue('F5', 'Thời gian checkin');
            $sheet->setCellValue('G5', 'Nguồn');
            $sheet->setCellValue('H5', 'Nguồn chi tiết');
            $sheet->setCellValue('I5', 'Người tích checkin');
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(30);
            
            ProcessExcel::styleCells($spreadsheet, "A1:I1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:I2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:I3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:I4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->crm_id);
                $sheet->setCellValue('C' . $x, $students[$i]->name);
                $sheet->setCellValue('D' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('E' . $x, $students[$i]->ec_name);
                $sheet->setCellValue('F' . $x, $students[$i]->checkin_at);
                $sheet->setCellValue('G' . $x, $students[$i]->source_name);
                $sheet->setCellValue('H' . $x, $students[$i]->source_detail_name);
                $sheet->setCellValue('I' . $x, $students[$i]->updator_checked);
                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "I$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="BÁO CÁO CHECKIN.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }

    public function report_r10(Request $request) {
        if ($session = $request->users_data) {
            $p = r::params($request, $session);
            $query = r::report_r10( $p, 0, 1);
            $students = u::query($query);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:H1');
            $sheet->mergeCells('A2:h2');
            $sheet->mergeCells('A3:H3');
            $sheet->mergeCells('A4:H4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO HỌC SINH ĐANG HỌC THỬ');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(33);
            $sheet->getRowDimension('5')->setRowHeight(30);

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Mã cyber');
            $sheet->setCellValue('C5', 'Mã CRM');
            $sheet->setCellValue('D5', 'Tên học sinh');
            $sheet->setCellValue('E5', 'Trung tâm');
            $sheet->setCellValue('F5', 'Sản phẩm');
            $sheet->setCellValue('G5', 'Lớp');
            $sheet->setCellValue('H5', 'Ngày bắt đầu');
            $sheet->setCellValue('I5', 'Ngày kết thúc');
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);
            
            ProcessExcel::styleCells($spreadsheet, "A1:H1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:H2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:H3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:H4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('C' . $x, $students[$i]->crm_id);
                $sheet->setCellValue('D' . $x, $students[$i]->name);
                $sheet->setCellValue('E' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('F' . $x, $students[$i]->product_name);
                $sheet->setCellValue('G' . $x, $students[$i]->class_name);
                $sheet->setCellValue('H' . $x, $students[$i]->enrolment_start_date);
                $sheet->setCellValue('I' . $x, $students[$i]->enrolment_last_date);
                
            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="BÁO CÁO HỌC SINH ĐANG HỌC THỬ.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }

    public function report_r11(Request $request) {
        if ($session = $request->users_data) {
            $p = r::params($request, $session);
            $query = r::report_r11( $p,$request, 0, 1);
            $students = u::query($query);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:H1');
            $sheet->mergeCells('A2:H2');
            $sheet->mergeCells('A3:H3');
            $sheet->mergeCells('A4:H4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO HỌC SINH ĐÃ HỌC THỬ');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(33);
            $sheet->getRowDimension('4')->setRowHeight(33);
            $sheet->getRowDimension('5')->setRowHeight(30);

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Mã cyber');
            $sheet->setCellValue('C5', 'Mã CRM');
            $sheet->setCellValue('D5', 'Tên học sinh');
            $sheet->setCellValue('E5', 'Trung tâm');
            $sheet->setCellValue('F5', 'Sản phẩm');
            $sheet->setCellValue('G5', 'Lớp');
            $sheet->setCellValue('H5', 'Ngày bắt đầu');
            $sheet->setCellValue('I5', 'Ngày kết thúc');
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);
            
            ProcessExcel::styleCells($spreadsheet, "A1:H1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:H2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:H3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:H4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "I5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->accounting_id);
                $sheet->setCellValue('C' . $x, $students[$i]->crm_id);
                $sheet->setCellValue('D' . $x, $students[$i]->name);
                $sheet->setCellValue('E' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('F' . $x, $students[$i]->product_name);
                $sheet->setCellValue('G' . $x, $students[$i]->class_name);
                $sheet->setCellValue('H' . $x, $students[$i]->enrolment_start_date);
                $sheet->setCellValue('I' . $x, $students[$i]->enrolment_last_date);
                
            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="BÁO CÁO HỌC SINH ĐÃ HỌC THỬ.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }

    public function report_r12(Request $request) {
        if ($session = $request->users_data) {
           
            $p = r::params($request, $session);
            $query = r::report_r12( $p,$request, 0, 1);
            $students = u::query($query);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:H1');
            $sheet->mergeCells('A2:H2');
            $sheet->mergeCells('A3:H3');
            $sheet->mergeCells('A4:H4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO CONFIRM HỌC SINH');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(20);
            $sheet->getRowDimension('4')->setRowHeight(20);
            $sheet->getRowDimension('5')->setRowHeight(30);

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Mã CRM');
            $sheet->setCellValue('C5', 'Tên học sinh');
            $sheet->setCellValue('D5', 'Trung tâm tạo');
            $sheet->setCellValue('E5', 'TVV tạo');
            $sheet->setCellValue('F5', 'Thời gian tạo');
            $sheet->setCellValue('G5', 'Thời gian checkin');
            $sheet->setCellValue('H5', 'Trung tâm checkin');
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(30);
            $sheet->getColumnDimension('H')->setWidth(30);
            
            ProcessExcel::styleCells($spreadsheet, "A1:H1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:H2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:H3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:H4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "H5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->crm_id);
                $sheet->setCellValue('C' . $x, $students[$i]->name);
                $sheet->setCellValue('D' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('E' . $x, $students[$i]->checkin_by);
                $sheet->setCellValue('F' . $x, $students[$i]->created_at);
                $sheet->setCellValue('G' . $x, $students[$i]->checkin_at);
                $sheet->setCellValue('H' . $x, $students[$i]->checkin_by_branch_name);
                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "H$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="BÁO CÁO CONFIRM HỌC SINH.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }

    public function report_r13(Request $request) {
        if ($session = $request->users_data) {
           
            $p = r::params($request, $session);
            $query = r::report_r13( $p,$request, 0, 1);
            $students = u::query($query);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:G1');
            $sheet->mergeCells('A2:G2');
            $sheet->mergeCells('A3:G3');
            $sheet->mergeCells('A4:G4');
            $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY & SÁNG TẠO QUỐC TẾ CMS');
            $sheet->setCellValue('A2', 'BÁO CÁO CONFIRM');
            $sheet->getRowDimension('1')->setRowHeight(36);
            $sheet->getRowDimension('2')->setRowHeight(50);
            $sheet->getRowDimension('3')->setRowHeight(20);
            $sheet->getRowDimension('4')->setRowHeight(20);
            $sheet->getRowDimension('5')->setRowHeight(30);

            $sheet->setCellValue('A5', 'STT');
            $sheet->setCellValue('B5', 'Trung tâm tạo');
            $sheet->setCellValue('C5', 'TVV tạo');
            $sheet->setCellValue('D5', 'CONFIRM');
            $sheet->setCellValue('E5', 'ShowUP');
            $sheet->setCellValue('F5', 'DEMO');
            $sheet->setCellValue('G5', 'DEAL');
            
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(30);
            
            ProcessExcel::styleCells($spreadsheet, "A1:G1", NULL, NULL, 16, 1, 3, "center", "center", true, 0, 'Calibri');
            ProcessExcel::styleCells($spreadsheet, "A2:G2", NULL, NULL, 20, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "A3:G3", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');
            ProcessExcel::styleCells($spreadsheet, "A4:G4", NULL, NULL, 12, 1, 3, "center", "center", true, 0, 'Arial');

            ProcessExcel::styleCells($spreadsheet, "A5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "B5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "C5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "D5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "E5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "F5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            ProcessExcel::styleCells($spreadsheet, "G5", '223b54', 'FFFFFF', 12, 1, 3, "center", "center", true, 0, 'Cambria');
            
            for ($i = 0; $i < count($students) ; $i++) {
                $x = $i + 6;
                $sheet->setCellValue('A' . $x, $i + 1);
                $sheet->setCellValue('B' . $x, $students[$i]->branch_name);
                $sheet->setCellValue('C' . $x, $students[$i]->full_name." - ".$students[$i]->hrm_id);
                $sheet->setCellValue('D' . $x, $students[$i]->comfirm);
                $sheet->setCellValue('E' . $x, $students[$i]->show_up);
                $sheet->setCellValue('F' . $x, $students[$i]->demo);
                $sheet->setCellValue('G' . $x, $students[$i]->deal);
                $sheet->getRowDimension($x)->setRowHeight(23);
                ProcessExcel::styleCells($spreadsheet, "A$x", 'FFFFFF', '111111', 11, 0, 3, "right", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "B$x", 'FFFFFF', '111111', 11, 0, 3, "left", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "C$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "D$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "E$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "F$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
                ProcessExcel::styleCells($spreadsheet, "G$x", 'FFFFFF', '111111', 11, 0, 3, "center", "center", true, 0, 'Cambria');
            }
            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="BÁO CÁO CONFIRM.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            die('Request not found...');
        }
        exit;
    }
}
