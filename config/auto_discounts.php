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
    |
    */

    'rules' => [
        // Ví dụ 1: Giảm 500,000đ cho gói 12 tháng
        [
            'name' => 'Giảm giá gói 12 tháng',
            'description' => 'Giảm 500,000đ khi đăng ký gói 12 tháng',
            'status' => 0, // 1: Active, 0: Inactive
            'priority' => 10, // Số càng lớn càng ưu tiên

            // Thời gian áp dụng
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',

            // Ngày cụ thể trong tháng (để [] hoặc null nếu áp dụng mọi ngày)
            'apply_on_days' => [], // Áp dụng mọi ngày trong khoảng start_date - end_date

            // Điều kiện áp dụng (để null hoặc [] nếu áp dụng cho tất cả)
            'tuition_packages' => [12], // Chỉ áp dụng cho gói 12 tháng
            'product_ids' => [], // Áp dụng cho tất cả sản phẩm
            'branch_ids' => [], // Áp dụng cho tất cả chi nhánh

            // Loại và giá trị giảm
            'discount_type' => 'fixed', // 'fixed' hoặc 'percentage'
            'discount_value' => 500000, // 500,000đ
        ],

        // Ví dụ 2: Giảm 10% cho gói 6 tháng
        [
            'name' => 'Giảm 10% gói 6 tháng',
            'description' => 'Giảm 10% học phí khi đăng ký gói 6 tháng',
            'status' => 0,
            'priority' => 5,

            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',

            'apply_on_days' => [], // Áp dụng mọi ngày

            'tuition_packages' => [6],
            'product_ids' => [],
            'branch_ids' => [],

            'discount_type' => 'percentage',
            'discount_value' => 10, // 10%
        ],

        // Ví dụ 3: Giảm theo chi nhánh cụ thể
        [
            'name' => 'Khuyến mãi chi nhánh Hà Nội',
            'description' => 'Giảm 1,000,000đ cho học sinh đăng ký tại Hà Nội',
            'status' => 0, // Không áp dụng
            'priority' => 8,

            'start_date' => '2026-02-01',
            'end_date' => '2026-02-28',

            'apply_on_days' => [], // Áp dụng mọi ngày

            'tuition_packages' => [6, 12], // Áp dụng cho cả gói 6 và 12 tháng
            'product_ids' => [1, 2], // Chỉ áp dụng cho product_id 1 và 2
            'branch_ids' => [1], // Chỉ áp dụng cho branch_id 1

            'discount_type' => 'fixed',
            'discount_value' => 1000000,
        ],

        // Ví dụ 4: Giảm giá chỉ vào ngày 1 và 30 hàng tháng
        [
            'name' => 'Flash Sale ngày 1 và 30',
            'description' => 'Giảm 300,000đ khi đăng ký vào ngày 1 hoặc 30 hàng tháng',
            'status' => 0,
            'priority' => 15,

            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',

            // CHỈ áp dụng vào ngày 1 và 30 hàng tháng
            'apply_on_days' => [1, 30],

            'tuition_packages' => [], // Áp dụng cho tất cả gói
            'product_ids' => [],
            'branch_ids' => [],

            'discount_type' => 'fixed',
            'discount_value' => 300000,
        ],

        // Thêm các rule khác tại đây...
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */

    // Cho phép áp dụng nhiều discount cùng lúc
    'allow_multiple' => false,

    // Nếu allow_multiple = false, chọn discount theo priority cao nhất
    'select_by_priority' => true,
];
