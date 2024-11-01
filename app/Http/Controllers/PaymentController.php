<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\ProcessExcel;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PaymentController extends Controller
{

    // Get Payment by contract
    public function getPaymentByContract(Request $request)
    {
        $conId = $request->input('conId');
        $data = null;
        $code = 404;
        try {
            if ($conId) {
                $data = Payment::where('contract_id', $conId)->first();
                $code = 200;
            }

        } catch (Exception $e) {
        }

        $return = ['data' => $data];
        return response()->json($return, $code);
    }

    /**
     * "contract_code (nvarchar): Số chứng từ nhập học
     * creator (nvarchar): Mã nhân viên thu ngân
     * amount(int): Số tiền thu
     * charge_date (datetime (yyyy-mm-dd)): Ngày thu tiền
     * note(text): Ghi chú
     * payment_code(nvarchar): Mã phiếu thu
     * Authorization: token lấy từ API 1"
     * @param Request $request
     * @throws Exception
     */
    public function downloadImportTemplate(Request $request)
    {
        $columns = [
            [
                'name' => "STT",
                'width' => 7,
                'value' => 1
            ],
            [
                'name' => "Số chứng từ nhập học (*)",
                'width' => 20,
                'value' => 'C01.19.PNH.1778'
            ],
            [
                'name' => "Mã gói phí (*)",
                'width' => 20,
                'value' => 'UCREA03'
            ],
            [
                'name' => "Mã phiếu thu(*)",
                'width' => 20,
                'value' => 'C02.19.PNH.1664'
            ],
            [
                'name' => "Mã nhân viên thu ngân(*)",
                'width' => 20,
                'value' => 'KD0002'
            ],
            [
                'name' => "Số tiền thu (*)",
                'width' => 20,
                'value' => 1000000000
            ],
            [
                'name' => "Ngày thu tiền (*)",
                'width' => 20,
                'value' => '2019-03-03'
            ],
            [
                'name' => "Ghi chú",
                'width' => 20,
                'value' => 'Đây là ghi chú'
            ],
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getRowDimension(1)->setRowHeight(50);
        foreach ($columns as $key => $value) {
            $colName = $key < 26 ? chr($key + 65) : 'A' . chr($key - 26 + 65);
            $sheet->setCellValue("{$colName}1", $value['name']);
            $sheet->setCellValue("{$colName}2", $value['value']);
            $sheet->getColumnDimension($colName)->setWidth($value['width']);
            ProcessExcel::styleCells($spreadsheet, "{$colName}1", "172270", "FFFFFF", 10, 0, 3, "center", "center", true);
            $sheet->getStyle("{$colName}1")->getAlignment()->setWrapText(true);
        }
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="file_import_payment_template.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");

        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }
}
