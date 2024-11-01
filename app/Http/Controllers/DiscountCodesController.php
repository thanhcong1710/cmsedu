<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Discount;
use App\Models\DiscountCode;
use App\Models\ProcessExcel;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DiscountCodesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json(Discount::paginate());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {

    }

    public function list(Request $request, $search, $paging)
    {
        $model = new DiscountCode();
        return $model->getList(json_decode($search), json_decode($paging));
    }

    public function getById($id)
    {
        $data = u::first("SELECT * FROM discount_codes WHERE id = $id");
        $data->start_date1 = $data->start_date;
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, ['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $code = APICode::PERMISSION_DENIED;
        $user = $request->users_data;
        $message = null;
        $error = 0;
        if (!empty($user)) {
            $params = (Object)$request->all();
            $discountCode = DiscountCode::where('id', '=', $id)->first();
            $discountCode->name = $params->name;
            $discountCode->percent = $params->percent;
            $discountCode->start_date = $params->start_date;
            $discountCode->end_date = $params->end_date;
            $discountCode->creator = $user->id;
            $discountCode->status = $params->status;
            $discountCode->price = $params->price;
            $discountCode->discount = round((float)$params->percent * $params->price / 100.0, 2);
            $discountCode->zone_id = !empty($params->zone_id) ? $params->zone_id: 0;
            $discountCode->fee_ids = !empty($params->fee_ids) ? implode(",",$params->fee_ids): null;
            $discountCode->bonus_sessions = $params->bonus_sessions;
            $discountCode->bonus_amount = $params->bonus_amount;
            try {
                $res = $discountCode->save();
                if ($res) {
                    $code = APICode::SUCCESS;
                }
            } catch (\Exception $exception) {
                $code = APICode::SUCCESS;
                if ($exception->getCode() == 23000) {
                    $error = 23000;
                    $message = "Không thể cập nhật";
                }
            }
        }

        $response = new Response();
        return $response->formatResponse($code, ['code' =>$error], $message);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $code = APICode::PERMISSION_DENIED;
        $user = $request->users_data;
        $message = null;
        $error = 0;
        if (!empty($user)) {
            $params = (Object)$request->all();
            $discountCode = new DiscountCode();
            $discountCode->code = $params->code;
            $discountCode->name = $params->name;
            $discountCode->percent = $params->percent;
            $discountCode->start_date = $params->start_date;
            $discountCode->end_date = $params->end_date;
            $discountCode->creator = $user->id;
            $discountCode->status = $params->status;
            $discountCode->price = $params->price;
            $discountCode->discount = round((float)$params->percent * $params->price / 100.0, 2);
            $discountCode->zone_id = !empty($params->zone_id) ? $params->zone_id: 0;
            $discountCode->fee_ids = !empty($params->fee_ids) ? implode(",",$params->fee_ids): null;
            $discountCode->bonus_sessions = $params->bonus_sessions;
            $discountCode->bonus_amount = $params->bonus_amount;
            try {
                $res = $discountCode->save();
                if ($res) {
                    $code = APICode::SUCCESS;
                }
            } catch (\Exception $exception) {
                $code = APICode::SUCCESS;
                if ($exception->getCode() == 23000) {
                    $error = 23000;
                    $message = "Mã chiết khấu đã tồn tại trong hệ thống";
                }
            }
        }

        $response = new Response();
        return $response->formatResponse($code, ['code' =>$error], $message);

    }

    public function getAvailableDiscountCodes(Request $request)
    {
        $code = APICode::PERMISSION_DENIED;
        $user = $request->users_data;
        $message = null;
        $data = null;
        if (!empty($user)) {
            $discountCode = new DiscountCode();
            $data = $discountCode->getAvailableDiscountCodes($user->role_id);
            $code = APICode::SUCCESS;
        }

        $response = new Response();
        return $response->formatResponse($code, $data, $message);

    }

    public function getAvailableDiscountCodesFee(Request $request, $feeId = 0, $branchId = 0)
    {
        $code = APICode::PERMISSION_DENIED;
        $user = $request->users_data;
        $type = $request->type;
        $sql = "SELECT zone_id from `branches` WHERE id = $branchId";
        $dataZone = u::first($sql);
        $zoneId = !empty($dataZone) ? $dataZone->zone_id : 0;
        $message = null;
        $data = null;

        if (!empty($user)) {
            $discountCode = new DiscountCode();
            $data = $discountCode->getAvailableDiscountCodesNew($user->role_id, $feeId, $zoneId);
            $code = APICode::SUCCESS;
        }

        $dataNew = [];
        if ($data){
            foreach ($data as $item){
                if ($item->user_limit != 0){
                    if ($item->user_group != 0){
                        $group = $item->user_group;
                        $zoneId = $item->zone_id;
                        $total = $this->checkTotalUsingType($type,$zoneId,$group);

                        if ($type == 1 && $total < $item->user_new){
                            $dataNew[] = $item;
                        }

                        if ($type == 2 && $total < $item->user_renew){
                            $dataNew[] = $item;
                        }
                    }
                    else{
                        $total = $this->checkTotalUsing($item->code);
                        if ($total < $item->user_limit){
                            $dataNew[] = $item;
                        }
                    }
                }
                else{
                    $dataNew[] = $item;
                }
            }
        }

        $response = new Response();
        return $response->formatResponse($code, $dataNew, $message);

    }

    protected function checkTotalUsingType($type = 0, $zoneId, $group){
//        $zoneId = (int)$zoneId;
//        AND (zone_id = $zoneId OR zone_id = 0)
        $sql = "SELECT COUNT(coupon) AS total_apply FROM `contracts` 
                WHERE coupon IN 
                (SELECT `code` FROM discount_codes WHERE STATUS = 1 AND (CURRENT_DATE() BETWEEN start_date AND end_date) AND user_group = $group) 
                AND `type`= $type";
        $data = u::query($sql);
        if ($data)
            return $data[0]->total_apply;
        else
            return 0;
    }

    protected function checkTotalUsing($code = null){
        $sql = "SELECT COUNT(coupon) AS total_apply FROM `contracts` WHERE coupon = '$code'";
        $data = u::query($sql);
        if ($data)
            return $data[0]->total_apply;
        else
            return 0;
    }

    /**
     * Mã CKGG    Tên    Tỷ lệ CK    Ngày bắt đầu    Ngày kết thúc    Giá gốc    Tiền CK
     */
    public function downloadImportTemplate()
    {
        $columns = [
            [
                'name' => "STT",
                'width' => 7,
                'value' => 1
            ],
            [
                'name' => "Mã chiết khấu (*)",
                'width' => 20,
                'value' => 'MCK01'
            ],
            [
                'name' => "Tên mã chiết khấu (*)",
                'width' => 20,
                'value' => 'Chiết khấu giảm giá cơ bản'
            ],
            [
                'name' => "Tỷ lệ chiết khấu (*)",
                'width' => 20,
                'value' => 10
            ],
            [
                'name' => "Ngày bắt đầu (yyyy-mm-dd) (*)",
                'width' => 15,
                'value' => '2015-05-25'
            ],
            [
                'name' => "Ngày kết thúc (yyyy-mm-dd) (*)",
                'width' => 15,
                'value' => '2019-05-25'
            ],
            [
                'name' => "Giá gốc (*)",
                'width' => 15,
                'value' => 1000000
            ],
            [
                'name' => "Số tiền chiết khấu",
                'width' => 20,
                'value' => 100000
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
            header('Content-Disposition: attachment;filename="file_import_discount_code_template.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");

        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

}
