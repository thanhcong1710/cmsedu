<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\APICode;
use App\Models\Response;

class CouponsController extends Controller
{
    public function getDataVoucher(Request $request)
    {
        $data = null;
        $code = APICode::SUCCESS;
        $response = new Response();
        $coupon_code = $request->coupon_code;
        $cond = $request->branch_id ? " AND (branch_id = $request->branch_id OR branch_id =0 )":"";

        $code_voucher_check = u::first("SELECT c.* FROM coupons AS c 
            WHERE c.code='$coupon_code' AND c.`status`=1 AND c.start_date <= CURRENT_DATE AND c.end_date >= CURRENT_DATE $cond");
        if ($code_voucher_check) {
            $data = (object)array(
                'status' => 1,
                'message' => "ok",
                'coupon_session' => $code_voucher_check->coupon_session,
                'coupon_amount' => $code_voucher_check->coupon_amount,
                'bonus_amount' => $code_voucher_check->bonus_amount
            );
        } else {
            $code_voucher_info = u::first("SELECT c.status,c.start_date,c.end_date, c.branch_id , 
                    (SELECT `name` FROM branches WHERE id=c.branch_id) AS branch_name
                FROM coupons AS c WHERE c.code='$coupon_code'");
            if (empty($code_voucher_info) || $code_voucher_info->status == 0) {
                $mess = "Mã voucher không hợp lệ";
            } elseif ($code_voucher_info->status == 2) {
                $mess = "Mã voucher đã được sử dụng";
            } elseif ($code_voucher_info->start_date > date('Y-m-d')) {
                $mess = "Mã voucher chưa có hiệu lực, ngày bắt đầu có hiệu lực " . $code_voucher_info->start_date;
            } elseif ($code_voucher_info->end_date < date('Y-m-d')) {
                $mess = "Mã voucher đã hết hiệu lực ngày " . $code_voucher_info->end_date;
            }elseif($request->branch_id && $code_voucher_info->branch_id && $request->branch_id !=$code_voucher_info->branch_id){
                $mess = "Mã voucher chỉ áp dụng cho " . $code_voucher_info->branch_name;
            } else {
                $mess = "Mã voucher không hợp lệ";
            }
            $data = (object)array(
                'status' => 0,
                'message' => $mess,
            );
        }
        return $response->formatResponse($code, $data);
    }
}
