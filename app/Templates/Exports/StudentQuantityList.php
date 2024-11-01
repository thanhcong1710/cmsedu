<?php

namespace App\Templates\Exports;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class StudentQuantityList implements ExportTemplateInterface
{
    /**
     * @param null $data
     * @return array
     */
    public function getHeader($data = null)
    {
        return array_merge([
            [
                'name' => "BÁO CÁO SỐ LƯỢNG HỌC SINH HÀNG THÁNG",
                'weight' => true
            ]
        ], $data ?: []);
    }

    public function getColumns()
    {
        return [
            [
                'name' => "Tuần",
                'width' => 30,
                'value' => "week",
                'merge_row' => function ($row) {
                    return count($row->detail);
                }
            ],
            [
                'name' => "Tên trung tâm",
                'width' => 20,
                'value' => "detail.branch_name",
            ],
            [
                'name' => "Tên học sinh",
                'width' => 30,
                'value' => "detail.student_name",
            ],
            [
                'name' => "Mã CMS",
                'width' => 20,
                'value' => "detail.crm_id",
            ],
            [
                'name' => "Mã Cyber",
                'width' => 20,
                'value' => "detail.cyber_code",
            ],
            [
                'name' => "Tên EC",
                'width' => 40,
                'value' => "detail.ec_name",
            ],
            [
                'name' => "Sản phẩm",
                'width' => 20,
                'value' => "detail.program_name",
            ],
            [
                'name' => "Gói phí",
                'width' => 20,
                'value' => "detail.tuition_fee_accounting_id",
            ],
            [
                'name' => "Ngày bắt đầu",
                'width' => 20,
                'value' => "detail.enrolment_start_date",
            ],
            [
                'name' => "Ngày kết thúc",
                'width' => 20,
                'value' => "detail.enrolment_last_date",
            ],
            [
                'name' => "Loại hợp đồng",
                'width' => 40,
                'value' => "detail.type_name",
            ],
            [
                'name' => "Trạng thái hợp đồng",
                'width' => 80,
                'value' => "detail.status_name",
            ],
            [
                'name' => "Trạng thái",
                'width' => 20,
                'value' => "detail.std_status_name",
            ],
            [
                'name' => "count_recharge",
                'width' => 20,
                'value' => "detail.count_recharge",
            ],

        ];
    }

    public function getLoop()
    {
        return "detail";
    }

    public function getStyle()
    {
        return false;
    }

}