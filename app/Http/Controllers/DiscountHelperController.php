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
            'count_recharge' => $request->input('count_recharge'),
            'total_amount' => $request->input('total_amount', 0),
        ];

        $discount = AutoDiscountHelper::getBestDiscount($contractData);

        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => $discount
        ]);
    }
}
