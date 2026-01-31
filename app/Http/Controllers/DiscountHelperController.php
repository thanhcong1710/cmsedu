<?php

namespace App\Http\Controllers;

use App\Helpers\AutoDiscountHelper;
use Illuminate\Http\Request;

class DiscountHelperController extends Controller
{
    /**
     * Lấy danh sách discounts áp dụng cho contract
     */
    public function getApplicableDiscounts(Request $request)
    {
        $contractData = [
            'branch_id' => $request->input('branch_id'),
            'product_id' => $request->input('product_id'),
            'count_recharge' => $request->input('count_recharge'), // số tháng
            'total_amount' => $request->input('total_amount', 0), // tổng tiền
            'enrolment_updator_id' => $request->input('enrolment_updator_id'),
            'tuition_fee_id' => $request->input('tuition_fee_id'),
            'bonus_sessions' => $request->input('bonus_sessions'),
        ];

        $discounts = AutoDiscountHelper::getAllApplicableDiscounts($contractData);

        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => $discounts
        ]);
    }

    /**
     * Lấy discount tốt nhất
     */
    public function getBestDiscount(Request $request)
    {
        $contractData = [
            'branch_id' => $request->input('branch_id'),
            'product_id' => $request->input('product_id'),
            'count_recharge' => $request->input('count_recharge'), // số tháng
            'total_amount' => $request->input('total_amount', 0), // tổng tiền
            'enrolment_updator_id' => $request->input('enrolment_updator_id'),
            'tuition_fee_id' => $request->input('tuition_fee_id'),
            'bonus_sessions' => $request->input('bonus_sessions'),
        ];

        $discount = AutoDiscountHelper::getBestDiscount($contractData);

        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => $discount
        ]);
    }
}
