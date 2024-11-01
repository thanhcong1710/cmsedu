<?php

namespace App\Templates\Exports;

use App\Providers\UtilityServiceProvider as u;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class Student implements ExportTemplateInterface
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
        8 => 'Đã bỏ cọc'
    ];


    /**
     * @param null $data
     * @return array
     */
    public function getHeader($data = null)
    {
        return array_merge([
            [
                'name' => "XUẤT DANH SÁCH HỌC SINH MỚI",
                'weight' => true
            ]
        ], $data ?: []);
    }

    public function getColumns()
    {
        return [
            [
                'name' => "STT",
                'width' => 7,
                'value' => function ($row, $index) {
                    return $index + 1;
                },
                'merge_row' => function ($row) {
                    return count($row->contracts);
                }
            ],
            [
                'name' => "MÃ CRM",
                'width' => 20,
                'value' => 'crm_id',
                'merge_row' => function ($row) {
                    return count($row->contracts);
                }
            ],
            [
                'name' => "MÃ CYBER",
                'width' => 20,
                'value' => 'accounting_id',
                'merge_row' => function ($row) {
                    return count($row->contracts);
                }
            ],
            [
                'name' => "HỌC SINH",
                'width' => 20,
                'value' => 'name',
                'merge_row' => function ($row) {
                    return count($row->contracts);
                }
            ],
            [
                'name' => "TRUNG TÂM",
                'width' => 20,
                'value' => 'contracts.branch_name'
            ],
            [
                'name' => "SẢN PHẨM",
                'width' => 20,
                'value' => 'contracts.product_name'
            ],
            [
                'name' => "LỚP HỌC",
                'width' => 20,
                'value' => 'contracts.class_name'
            ],
            [
                'name' => "GÓI PHÍ",
                'width' => 20,
                'value' => 'contracts.tuition_fee_name'
            ],
            [
                'name' => "NGÀY ĐÓNG PHÍ",
                'width' => 20,
                'value' => 'contracts.charge_date'
            ],
            [
                'name' => "SỐ TIỀN PHẢI ĐÓNG",
                'width' => 20,
                'horizontal' => 'right',
                'value' => function ($data) {
                    return u::formatCurrency($data->contracts->must_charge);
                }
            ],
            [
                'name' => "SỐ TIỀN ĐÃ ĐÓNG",
                'width' => 20,
                'horizontal' => 'right',
                'value' => function ($data) {
                    return u::formatCurrency($data->contracts->total_charged);
                }
            ],
            [
                'name' => "CÔNG NỢ",
                'width' => 20,
                'horizontal' => 'right',
                'value' => function ($data) {
                    return u::formatCurrency($data->contracts->debt_amount);
                }
            ],
            [
                'name' => "EC",
                'width' => 20,
                'value' => 'contracts.ec_name'
            ],
            [
                'name' => "CS",
                'width' => 20,
                'value' => 'contracts.cs_name'
            ],
            [
                'name' => "TRẠNG THÁI",
                'width' => 20,
                'value' => function ($data) {
                    return self::CONTRACT_STATUS[$data->contracts->status];
                }
            ],
        ];
    }

    public function getLoop()
    {
        return "contracts";
    }

}