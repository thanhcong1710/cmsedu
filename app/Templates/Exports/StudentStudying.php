<?php

namespace App\Templates\Exports;

use App\Providers\UtilityServiceProvider as u;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class StudentStudying implements ExportTemplateInterface
{
    const CONTRACT_STATUS = [
        0 => 'Đã xóa',
        1 => 'Đã active nhưng chưa đóng phí',
        2 => 'Đã active và đặt cọc nhưng chưa thu đủ phí hoặc đang chờ nhận chuyển phí',
        3 => 'Đã active và đã thu đủ phí nhưng chưa được xếp lớp',
        4 => 'Đang bảo lưu không giữ chỗ hoặc pending',
        5 => 'Đang được nhận học bổng hoặc VIP',
        6 => 'Đã được xếp lớp và đang đi học',
        7 => 'Đã bị withdraw',
        8 => 'Đã bỏ cọc',
    ];


    /**
     * @param null $data
     * @return array
     */
    public function getHeader($data = null)
    {
        return array_merge([
            [
                'name' => "DANH SÁCH HỌC SINH THỰC HỌC TẠI TRUNG TÂM",
                'weight' => true
            ]
        ], $data ?: []);
    }

    /**
     * @return array
     * STT	Trung tâm	Tên học sinh 	Mã CMS	Mã Cybers 	Sản phẩm	Gói phí 	Số tiền phải đóng (must_charged)
     * 	Số tiền đã đóng(total_charged)	Công nợ	Số buổi được học	Tên lớp	Ngày bắt đấu (enrolment_start_date)	Ngày kết thúc (enrolment_last_date)
    (select name from branches where id = c.branch_id) as branch_name,
    s.name as student_name,
    s.crm_id,
    s.accounting_id as student_accounting_id,
    (select name from products where id = c.product_id) as product_name,
    (select name from tuition_fee where id = c.tuition_fee_id) as tuition_fee_name,
    c.must_charge,
    c.total_charged,
    c.debt_amount,
    c.real_sessions,
    (select cls_name from classes where id = c.class_id) as class_name,
    c.enrolment_start_date,
    c.enrolment_last_date
     */
    public function getColumns()
    {
        return [
            [
                'name' => "STT",
                'width' => 7,
                'value' => function ($row, $index) {
                    return $index + 1;
                }
            ],
            [
                'name' => "TRUNG TÂM",
                'width' => 20,
                'value' => 'branch_name'
            ],
            [
                'name' => "HỌC SINH",
                'width' => 20,
                'value' => 'student_name',
            ],
            [
                'name' => "MÃ CMS",
                'width' => 20,
                'value' => 'crm_id'
            ],
            [
                'name' => "MÃ CYBER",
                'width' => 20,
                'value' => 'student_accounting_id'
            ],
            [
                'name' => "SẢN PHẨM",
                'width' => 20,
                'value' => 'product_name'
            ],
            [
                'name' => "GÓI PHÍ",
                'width' => 20,
                'value' => 'tuition_fee_name'
            ],
            [
                'name' => "SỐ TIỀN PHẢI ĐÓNG",
                'width' => 20,
                'value' => function ($data) {
                    return u::formatCurrency($data->must_charge);
                }
            ],
            [
                'name' => "SỐ TIỀN ĐÃ ĐÓNG",
                'width' => 20,
                'value' => function ($data) {
                    return u::formatCurrency($data->total_charged);
                }
            ],
            [
                'name' => "CÔNG NỢ",
                'width' => 20,
                'value' => function ($data) {
                    return u::formatCurrency($data->debt_amount);
                }
            ],
            [
                'name' => "SỐ BUỔI ĐƯỢC HỌC",
                'width' => 20,
                'value' => 'real_sessions'
            ],
            [
                'name' => "LỚP HỌC",
                'width' => 20,
                'value' => 'class_name'
            ],
            [
                'name' => "NGÀY BẮT ĐẦU",
                'width' => 20,
                'value' => 'enrolment_start_date'
            ],
            [
                'name' => "NGÀY KẾT THÚC",
                'width' => 20,
                'value' => 'enrolment_last_date'
            ],
        ];
    }

    public function getLoop()
    {
        return null;
    }

}