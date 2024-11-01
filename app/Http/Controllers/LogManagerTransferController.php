<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Session;
use App\Models\LogManagerTransfer;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Models\ProcessExcel;

class LogManagerTransferController extends Controller
{
  public function lists( Request $request )
  {
    $where = '';
    if( $request->branches and $request->branches != null ) {
      $id_br = implode(',',$request->branches);
      $where .= " AND lmt.from_branch_id in ($id_br) and  lmt.to_branch_id in ($id_br)";
    }

    if( $request->from_date != "" and $request->to_date != "" ) {
      $fromDate = date('Y-m-d 00:00',strtotime($request->from_date));
      $toDate = date('Y-m-d 23:59',strtotime($request->to_date));
      $where .= " AND ( lmt.date_transfer BETWEEN '$fromDate' AND '$toDate' )";
    } else {
      $fromDate = date('Y-m-d 00:00');
      $toDate = date('Y-m-d 23:59');
      $where .= " AND ( lmt.date_transfer BETWEEN '$fromDate' AND '$toDate' )";
    }

    if( $request->sources != '' ) {
      $sources = $request->sources;
      $where .= " AND ( lmt.note like '%$sources%' ) ";
    }

    $sql = "
    SELECT
    	s.accounting_id as accounting_id,
    	s.cms_id,
        s.crm_id,
    	s.name as student_name,
    	IF(lmt.from_ec_id != '', fec.full_name, ec.full_name ) as from_ec,
    	IF(lmt.to_ec_id != '', tec.full_name, ec.full_name ) as to_ec,

    	IF(lmt.from_cm_id != '', fcm.full_name, cm.full_name ) as from_cm,
    	IF(lmt.to_cm_id != '', tcm.full_name, cm.full_name ) as to_cm,
    	b.name as branch_name,
    	lmt.date_transfer as date,
    	u.full_name as editor,
    	lmt.note as note

    FROM log_manager_transfer as lmt
    LEFT JOIN students as s on s.id = lmt.student_id
    LEFT JOIN term_student_user as tsu on tsu.student_id = lmt.student_id
    LEFT JOIN users as ec on ec.id = tsu.ec_id
    LEFT JOIN users as cm on cm.id = tsu.cm_id
    LEFT JOIN users as fec on fec.id = lmt.from_ec_id
    LEFT JOIN users as tec on tec.id = lmt.to_ec_id
    LEFT JOIN users as fcm on fcm.id = lmt.from_cm_id
    LEFT JOIN users as tcm on tcm.id = lmt.to_cm_id
    LEFT JOIN branches as b on b.id = lmt.to_branch_id
    LEFT JOIN users as u on u.id = lmt.updated_by
    WHERE s.id is not null

    $where
    ";

    $result = DB::select(DB::raw($sql));
    return response()->json($result);
  }

  public function exportExcel( $branch , $from_date, $to_date, $sources )
  {
    $where = '';
    if( $branch and $branch != '_' ) {
      $where .= " AND lmt.from_branch_id in ($branch) and  lmt.to_branch_id in ($branch)";
    }

    if( $from_date != "_" and $to_date != "_" ) {
      $fromDate = date('Y-m-d 00:00',strtotime($from_date));
      $toDate   = date('Y-m-d 23:59',strtotime($to_date));
    } else {
      $fromDate = date('Y-m-d 00:00');
      $toDate   = date('Y-m-d 23:59');
    }
    $where .= " AND ( lmt.date_transfer BETWEEN '$fromDate' AND '$toDate' )";

    if( $sources != '_' ) {
      $where .= " AND ( lmt.note like '%$sources%' ) ";
    }

    $sql = "
    SELECT
    	s.accounting_id as accounting_id,
    	s.cms_id,
        s.crm_id,
    	s.name as student_name,
    	IF(lmt.from_ec_id != '', fec.full_name, ec.full_name ) as from_ec,
    	IF(lmt.to_ec_id != '', tec.full_name, ec.full_name ) as to_ec,

    	IF(lmt.from_cm_id != '', fcm.full_name, cm.full_name ) as from_cm,
    	IF(lmt.to_cm_id != '', tcm.full_name, cm.full_name ) as to_cm,
    	b.name as branch_name,
    	lmt.date_transfer as date,
    	u.full_name as editor,
    	lmt.note as note

    FROM log_manager_transfer as lmt
    LEFT JOIN students as s on s.id = lmt.student_id
    LEFT JOIN term_student_user as tsu on tsu.student_id = lmt.student_id
    LEFT JOIN users as ec on ec.id = tsu.ec_id
    LEFT JOIN users as cm on cm.id = tsu.cm_id
    LEFT JOIN users as fec on fec.id = lmt.from_ec_id
    LEFT JOIN users as tec on tec.id = lmt.to_ec_id
    LEFT JOIN users as fcm on fcm.id = lmt.from_cm_id
    LEFT JOIN users as tcm on tcm.id = lmt.to_cm_id
    LEFT JOIN branches as b on b.id = lmt.to_branch_id
    LEFT JOIN users as u on u.id = lmt.updated_by
    WHERE s.id is not null

    $where
    ";
    $result = DB::select(DB::raw($sql));
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'DANH SÁCH HỌC SINH CHUYỂN NGƯỜI QUẢN LÝ TỪ NGÀY ' . $fromDate . ' ĐẾN NGÀY ' . $toDate );
    $sheet->mergeCells('A1:P1');
    ProcessExcel::styleCells($spreadsheet, "A1:P1", "FFFFFF", "black", 16, 0, 3, "center", "center", true);

    $sheet->getRowDimension('1')->setRowHeight(30);

    $sheet->setCellValue('A2', 'STT');
    $sheet->setCellValue('B2', 'Trung tâm');
    $sheet->setCellValue('C2', 'CRM');
    $sheet->setCellValue('D2', 'Cyber');
    $sheet->setCellValue('E2', 'Tên học sinh');
    $sheet->setCellValue('F2', 'From EC');
    $sheet->setCellValue('G2', 'To EC');
    $sheet->setCellValue('H2', 'From CM');
    $sheet->setCellValue('I2', 'To CM');
    $sheet->setCellValue('J2', 'Ngày');
    $sheet->setCellValue('K2', 'Người T.Hiện');
    $sheet->setCellValue('L2', 'Nguồn');

    $sheet->getColumnDimension("A")->setWidth(8);
    $sheet->getColumnDimension("B")->setWidth(20);
    $sheet->getColumnDimension("C")->setWidth(20);
    $sheet->getColumnDimension("D")->setWidth(20);
    $sheet->getColumnDimension("E")->setWidth(20);
    $sheet->getColumnDimension("F")->setWidth(20);
    $sheet->getColumnDimension("G")->setWidth(20);
    $sheet->getColumnDimension("H")->setWidth(20);
    $sheet->getColumnDimension("I")->setWidth(20);
    $sheet->getColumnDimension("J")->setWidth(20);
    $sheet->getColumnDimension("K")->setWidth(20);
    $sheet->getColumnDimension("L")->setWidth(20);


    $x = 3;
    foreach ($result as $row) {

      $sheet->setCellValue("A$x", $x - 2);
      $sheet->setCellValue("B$x", $row->branch_name);
      $sheet->setCellValue("C$x", $row->crm_id);
      $sheet->setCellValue("D$x", $row->accounting_id);
      $sheet->setCellValue("E$x", $row->student_name);
      $sheet->setCellValue("F$x", $row->from_ec);
      $sheet->setCellValue("G$x", $row->to_ec);
      $sheet->setCellValue("H$x", $row->from_cm);
      $sheet->setCellValue("I$x", $row->to_cm);
      $sheet->setCellValue("J$x", date('Y-m-d',strtotime($row->date)));
      $sheet->setCellValue("K$x", $row->editor);
      $sheet->setCellValue("L$x", $row->note);

      $x++;
    }


    $writer = new Xlsx($spreadsheet);
    try {
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="Danh sach hoc sinh chuyen nguoi quan ly.xlsx"');
      header('Cache-Control: max-age=0');
      $writer->save("php://output");
    } catch (Exception $exception) {
      throw $exception;
    }
    exit;
  }
}
