<?php

namespace App\Templates\Exports;

use App\Providers\UtilityServiceProvider as u;

/**
 * Created by PhpStorm.
 * User: QuyenLD
 * Date: 9/26/2019
 * Time: 10:25 AM
 */
class StudentWithdraw implements ExportTemplateInterface
{

    /**
     * @param null $data
     * @return array
     */
    public function getHeader($data = null)
    {
        return array_merge([
            [
                'name' => "DANH SÁCH HỌC SINH ĐÃ NGỪNG HỌC TẠI CMS",
                'weight' => true
            ]
        ], $data ?: []);
    }

    public function getColumns()
    {
        return [
            [
                'name' => "STT",
                'width' => 20,
                'value' => function ($row, $index) {
                    return $index+1;
                },
                'merge_row' => function ($row) {
                    return count($row->contracts);
                }
            ],
            [
                'name' => "Mã CRM",
                'width' => 20,
                'value' => "crm_id",
                'merge_row' => function ($row) {
                    return count($row->contracts);
                }
            ],
            [
                'name' => "Mã Cyber",
                'width' => 20,
                'value' => "accounting_id",
                'merge_row' => function ($row) {
                    return count($row->contracts);
                }
            ],
            [
                'name' => "Tên học sinh",
                'width' => 20,
                'value' => "student_name",
                'merge_row' => function ($row) {
                    return count($row->contracts);
                }
            ],
            [
                'name' => "Tên phụ huynh",
                'width' => 20,
                'value' => "gud_name1",
                'merge_row' => function ($row) {
                    return count($row->contracts);
                }
            ],
            [
                'name' => "Số điện thoại",
                'width' => 20,
                'value' => "gud_mobile1",
                'merge_row' => function ($row) {
                    return count($row->contracts);
                }
            ],
            [
                'name' => "Email",
                'width' => 20,
                'value' => "email",
                'merge_row' => function ($row) {
                    return count($row->contracts);
                }
            ],
            [
                'name' => "Trung tâm",
                'width' => 30,
                'value' => "contracts.branch_name",
            ],
            [
                'name' => "EC",
                'width' => 20,
                'value' => 'contracts.ec_name',
            ],
            [
                'name' => "Gói Phí",
                'width' => 40,
                'value' => 'contracts.tuition_fee_name'
            ],
            [
                'name' => "Loại Hợp Đồng",
                'width' => 30,
                'value' => 'contracts.type'
            ],
            [
                'name' => "Giá Gói Phí",
                'width' => 20,
                'value' => 'contracts.tuition_fee_price'
            ],
            [
                'name' => "Số Tiền Phải Đóng",
                'width' => 40,
                'value' => 'contracts.must_charge'
            ],
            [
                'name' => "Số Tiền Đã Thu",
                'width' => 20,
                'value' => 'contracts.total_charged'
            ],
            [
                'name' => "Số Buổi Được Học",
                'width' => 20,
                'value' => 'contracts.total_sessions'
            ],
            [
                'name' => "Ngày Bắt Đầu Học",
                'width' => 20,
                'value' => 'contracts.enrolment_start_date'
            ],
            [
                'name' => "Ngày Kết Thúc",
                'width' => 20,
                'value' => 'contracts.enrolment_last_date'
            ],
            [
                'name' => "Trạng Thái",
                'width' => 20,
                'value' => 'contracts.status'
            ]
        ];
    }

    public function getLoop()
    {
        return "contracts";
    }

    public function getStyle()
    {
        return false;
    }

}