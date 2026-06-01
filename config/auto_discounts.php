<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Auto Discount Rules
     |--------------------------------------------------------------------------
     |
     | Cấu hình các chương trình giảm trừ tự động khi nhập học
     | Mỗi rule có thể áp dụng theo:
     | - Khoảng thời gian (start_date, end_date)
     | - Ngày cụ thể trong tháng (apply_on_days: [1, 15, 30])
     | - Gói phí (tuition_packages: số tháng như 6, 12)
     | - Sản phẩm (product_ids)
     | - Chi nhánh (branch_ids)
     | - Loại giảm: 'fixed' (số tiền cố định) hoặc 'percentage' (%)
     | - Trạng thái: status (1: Active, 0: Inactive)
     | - Loại gói: package_types ([0, 1, 2]) // 0: Gói thường, 1: Combo 1, 2: Combo 2
     | - ID Gói phí: tuition_fee_ids ([1, 2, 3]) // Áp dụng cho các gói phí cụ thể theo ID
     | - Loại trừ khi có bonus sessions: exclude_bonus_sessions ([4]) // Không áp dụng nếu số buổi bonus là 4
     |
     */

    'rules' => [
        // ====================================================================
        // PRIORITY 5: Pre-School - 2b/tuần (tuition_fee_id = 323,324,325)
        // ====================================================================
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 500.000đ cho gói Pre-School 3 tháng',
            'status' => 1,
            'priority' => 50,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'tuition_fee_ids' => [323, 324, 325],
            'tuition_packages' => [3],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 500000,
            'package_types' => [0],
        ],
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 1.000.000đ cho gói Pre-School 6 tháng',
            'status' => 1,
            'priority' => 50,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'tuition_fee_ids' => [323, 324, 325],
            'tuition_packages' => [6],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 1000000,
            'package_types' => [0],
        ],
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 1.000.000đ cho gói Pre-School 9 tháng',
            'status' => 1,
            'priority' => 50,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'tuition_fee_ids' => [323, 324, 325],
            'tuition_packages' => [12],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 1000000,
            'package_types' => [0],
        ],

        // ====================================================================
        // PRIORITY 4: Combo 3 tháng
        // ====================================================================
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 1.000.000đ cho Combo 3 tháng Tích hợp 1',
            'status' => 1,
            'priority' => 41,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'package_types' => [1],
            'tuition_packages' => [3],
            // 'exclude_bonus_sessions' => [4],
            'branch_ids' => [19, 14],
            'discount_type' => 'fixed',
            'discount_value' => 1000000,
        ],
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 500.000đ cho Combo 3 tháng Tích hợp 1',
            'status' => 1,
            'priority' => 40,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'package_types' => [1],
            'tuition_packages' => [3],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 500000,
        ],
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 1.000.000đ cho Combo 3 tháng Tích hợp 2',
            'status' => 1,
            'priority' => 40,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'package_types' => [2],
            'tuition_packages' => [3],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 1000000,
        ],

        // ====================================================================
        // PRIORITY 3: Combo 6 tháng
        // ====================================================================
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 1.000.000đ cho Combo 6 tháng Tích hợp 1',
            'status' => 1,
            'priority' => 30,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'package_types' => [1],
            'tuition_packages' => [6],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 1000000,
        ],
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 1.000.000đ cho Combo 6 tháng Tích hợp 2',
            'status' => 1,
            'priority' => 30,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'package_types' => [2],
            'tuition_packages' => [6],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 1000000,
        ],

        // ====================================================================
        // PRIORITY 2: Brick Moto trở lên - 1b/tuần (product_id = 102..107)
        // ====================================================================
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 500.000đ cho gói Brick Moto 3 tháng',
            'status' => 1,
            'priority' => 20,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'product_ids' => [102, 103, 104, 105, 106, 107],
            'tuition_packages' => [3],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 500000,
            'package_types' => [0],
        ],
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 500.000đ cho gói Brick Moto 6 tháng',
            'status' => 1,
            'priority' => 20,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'product_ids' => [102, 103, 104, 105, 106, 107],
            'tuition_packages' => [6],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 500000,
            'package_types' => [0],
        ],
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 1.000.000đ cho gói Brick Moto 12 tháng',
            'status' => 1,
            'priority' => 20,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'product_ids' => [102, 103, 104, 105, 106, 107],
            'tuition_packages' => [12],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 1000000,
            'package_types' => [0],
        ],

        // ====================================================================
        // PRIORITY 1: Logic Math - 1b/tuần (product_id = 1,2,3)
        // ====================================================================
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 500.000đ cho gói Logic Math 3 tháng',
            'status' => 1,
            'priority' => 10,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'product_ids' => [1, 2, 3],
            'tuition_packages' => [3],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 500000,
            'package_types' => [0],
        ],
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 1.000.000đ cho gói Logic Math 6 tháng',
            'status' => 1,
            'priority' => 10,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'product_ids' => [1, 2, 3],
            'tuition_packages' => [6],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 1000000,
            'package_types' => [0],
        ],
        [
            'name' => 'Lì Xì Bính Ngọ',
            'description' => 'Giảm 1.000.000đ cho gói Logic Math 12 tháng',
            'status' => 1,
            'priority' => 10,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'product_ids' => [1, 2, 3],
            'tuition_packages' => [12],
            // 'exclude_bonus_sessions' => [4],
            'discount_type' => 'fixed',
            'discount_value' => 1000000,
            'package_types' => [0],
        ],

        // ====================================================================
        // PRIORITY 0: Product 4 - Extra Discounts
        // ====================================================================
        [
            'name' => 'Ưu đãi Đăng ký sớm',
            'description' => 'Giảm tự động 15% cho các gói SUMMER đến hết 30/04/2026',
            'status' => 1,
            'priority' => 5,
            'start_date' => '2026-01-01',
            'end_date' => '2026-05-15',
            'product_ids' => [4],
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'payment_types' => [1],
        ],
        [
            'name' => 'Ưu đãi Đăng ký tháng 5',
            'description' => 'Giảm tự động 5% cho các gói SUMMER từ 01/05/2026 đến 31/05/2026',
            'status' => 1,
            'priority' => 5,
            'start_date' => '2026-05-16',
            'end_date' => '2026-06-30',
            'product_ids' => [4],
            'discount_type' => 'percentage',
            'discount_value' => 5,
            'payment_types' => [1],
        ],
        [
            'name' => 'Ưu đãi Đăng ký trả góp',
            'description' => 'Giảm tự động 5% cho các gói SUMMER',
            'status' => 1,
            'priority' => 5,
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'product_ids' => [4],
            'payment_types' => [2],
            'discount_type' => 'percentage',
            'discount_value' => 5,
            'tuition_fee_ids' => [357, 358, 359, 360, 361, 362],
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Settings
     |--------------------------------------------------------------------------
     */

    'allow_multiple' => false,
    'select_by_priority' => true,
];