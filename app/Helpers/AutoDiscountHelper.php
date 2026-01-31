<?php

namespace App\Helpers;

use Carbon\Carbon;

class AutoDiscountHelper
{
    /**
     * Lấy tất cả các discount rules đang active
     */
    public static function getActiveRules()
    {
        $rules = config('auto_discounts.rules', []);
        $today = Carbon::now()->format('Y-m-d');

        $activeRules = array_filter($rules, function ($rule) use ($today) {
            // Kiểm tra trạng thái active (dựa vào 'status')
            $isActive = !empty($rule['status']);

            if (!$isActive) {
                return false;
            }

            // Kiểm tra ngày hiện tại có trong khoảng thời gian áp dụng không
            $startDate = $rule['start_date'] ?? null;
            $endDate = $rule['end_date'] ?? null;

            if ($startDate && $today < $startDate) {
                return false;
            }

            if ($endDate && $today > $endDate) {
                return false;
            }

            // Kiểm tra ngày cụ thể trong tháng (nếu có cấu hình)
            if (!empty($rule['apply_on_days']) && is_array($rule['apply_on_days'])) {
                $currentDay = (int) Carbon::now()->format('d');
                if (!in_array($currentDay, $rule['apply_on_days'])) {
                    return false;
                }
            }

            return true;
        });

        // Sắp xếp theo priority (cao nhất trước)
        usort($activeRules, function ($a, $b) {
            $priorityA = $a['priority'] ?? 0;
            $priorityB = $b['priority'] ?? 0;
            return $priorityB - $priorityA;
        });

        return $activeRules;
    }

    /**
     * Kiểm tra xem một rule có áp dụng cho contract hiện tại không
     */
    public static function isRuleApplicable($rule, $contractData)
    {
        // Kiểm tra gói phí (số tháng)
        if (!empty($rule['tuition_packages'])) {
            $monthCount = $contractData['count_recharge'] ?? 0;
            if (!in_array($monthCount, $rule['tuition_packages'])) {
                return false;
            }
        }

        // Kiểm tra sản phẩm
        if (!empty($rule['product_ids'])) {
            $productId = $contractData['product_id'] ?? 0;
            if (!in_array($productId, $rule['product_ids'])) {
                return false;
            }
        }

        // Kiểm tra chi nhánh
        if (!empty($rule['branch_ids'])) {
            $branchId = $contractData['branch_id'] ?? 0;
            if (!in_array($branchId, $rule['branch_ids'])) {
                return false;
            }
        }

        // Kiểm tra loại gói (package_types)
        if (!empty($rule['package_types'])) {
            $packageType = $contractData['enrolment_updator_id'] ?? 0;
            if (!in_array((int)$packageType, $rule['package_types'])) {
                return false;
            }
        }

        // Kiểm tra loại gói phí cụ thể (tuition_fee_id)
        if (!empty($rule['tuition_fee_ids'])) {
            $tuitionFeeId = $contractData['tuition_fee_id'] ?? 0;
            if (!in_array($tuitionFeeId, $rule['tuition_fee_ids'])) {
                return false;
            }
        }

        // Kiểm tra loại trừ khi có bonus sessions (exclude_bonus_sessions)
        if (!empty($rule['exclude_bonus_sessions'])) {
            $bonusSessions = $contractData['bonus_sessions'] ?? 0;
            if (in_array($bonusSessions, $rule['exclude_bonus_sessions'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tính toán số tiền giảm trừ
     */
    public static function calculateDiscount($rule, $totalAmount)
    {
        $discountType = $rule['discount_type'] ?? 'fixed';
        $discountValue = $rule['discount_value'] ?? 0;

        if ($discountType === 'percentage') {
            return round(($totalAmount * $discountValue) / 100);
        }

        return $discountValue;
    }

    /**
     * Lấy danh sách discounts áp dụng cho contract
     */
    public static function getApplicableDiscounts($contractData)
    {
        $activeRules = self::getActiveRules();
        $applicableDiscounts = [];

        foreach ($activeRules as $rule) {
            if (self::isRuleApplicable($rule, $contractData)) {
                $totalAmount = $contractData['total_amount'] ?? 0;
                $discountAmount = self::calculateDiscount($rule, $totalAmount);

                $applicableDiscounts[] = [
                    'name' => $rule['name'] ?? '',
                    'description' => $rule['description'] ?? '',
                    'discount_type' => $rule['discount_type'] ?? 'fixed',
                    'discount_value' => $rule['discount_value'] ?? 0,
                    'discount_amount' => $discountAmount,
                    'priority' => $rule['priority'] ?? 0,
                ];
            }
        }

        return $applicableDiscounts;
    }

    /**
     * Lấy discount tốt nhất (theo priority hoặc số tiền giảm nhiều nhất)
     */
    public static function getBestDiscount($contractData)
    {
        $applicableDiscounts = self::getApplicableDiscounts($contractData);

        if (empty($applicableDiscounts)) {
            return null;
        }

        $selectByPriority = config('auto_discounts.select_by_priority', true);

        if ($selectByPriority) {
            // Đã được sắp xếp theo priority, lấy cái đầu tiên
            return $applicableDiscounts[0];
        } else {
            // Lấy discount có số tiền giảm nhiều nhất
            usort($applicableDiscounts, function ($a, $b) {
                return $b['discount_amount'] - $a['discount_amount'];
            });
            return $applicableDiscounts[0];
        }
    }

    /**
     * Lấy tất cả discounts áp dụng (nếu cho phép multiple)
     */
    public static function getAllApplicableDiscounts($contractData)
    {
        $allowMultiple = config('auto_discounts.allow_multiple', false);

        if ($allowMultiple) {
            return self::getApplicableDiscounts($contractData);
        } else {
            $bestDiscount = self::getBestDiscount($contractData);
            return $bestDiscount ? [$bestDiscount] : [];
        }
    }
}
