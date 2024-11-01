<?php

namespace App\Templates\Exports;

use App\Providers\UtilityServiceProvider as u;

/**
 * Created by PhpStorm.
 * User: QuyenLD
 * Date: 9/26/2019
 * Time: 10:25 AM
 */
class StudentHistory implements ExportTemplateInterface
{

    /**
     * @param null $data
     * @return array
     */
    public function getHeader($data = null)
    {
        return array_merge([
            [
                'name' => "XUẤT DANH SÁCH LỊCH SỬ HỌC VIÊN THEO TRUNG TÂM",
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
                    return count($row->detail);
                }
            ],
            [
                'name' => "Mã CMS",
                'width' => 20,
                'value' => "cms_code",
                'merge_row' => function ($row) {
                    return count($row->detail);
                }
            ],
            [
                'name' => "Mã Cyber",
                'width' => 20,
                'value' => "cyber_code",
                'merge_row' => function ($row) {
                    return count($row->detail);
                }
            ],
            [
                'name' => "Tên học sinh",
                'width' => 20,
                'value' => "student_name",
                'merge_row' => function ($row) {
                    return count($row->detail);
                }
            ],
            [
                'name' => "Tên phụ huynh",
                'width' => 20,
                'value' => "gud_name1",
                'merge_row' => function ($row) {
                    return count($row->detail);
                }
            ],
            [
                'name' => "Số điện thoại",
                'width' => 20,
                'value' => "gud_mobile1",
                'merge_row' => function ($row) {
                    return count($row->detail);
                }
            ],
            [
                'name' => "Trung tâm",
                'width' => 30,
                'value' => "branch_name",
                'merge_row' => function ($row) {
                    return count($row->detail);
                }
            ],
            [
                'name' => "EC",
                'width' => 20,
                'value' => 'detail.ec_id',
            ],
            [
                'name' => "CS",
                'width' => 20,
                'value' => 'detail.cm_id'
            ],
            [
                'name' => "Giáo viên",
                'width' => 20,
                'value' => 'detail.teacher_name'
            ],
            [
                'name' => "Sản phẩm",
                'width' => 20,
                'value' => 'detail.product_name'
            ],
            [
                'name' => "Gói phí",
                'width' => 40,
                'value' => 'detail.tuition_name'
            ],
            [
                'name' => "Giá gói phí",
                'width' => 20,
                'value' => 'detail.tuition_fee_price'
            ],
            [
                'name' => "Số tiền phải đóng",
                'width' => 20,
                'value' => 'detail.must_charge'
            ],
            [
                'name' => "Số buổi theo gói phí",
                'width' => 20,
                'value' => 'detail.total_sessions'
            ],
            [
                'name' => "Số tiền đã đóng",
                'width' => 20,
                'value' => 'detail.total_charged'
            ],
            [
                'name' => "Số buổi thực tế được học",
                'width' => 20,
                'value' => 'detail.real_sessions'
            ],
            [
                'name' => "Trạng thái",
                'width' => 50,
                'value' => 'detail.c_type'
            ],
            [
                'name' => "Loại hợp đồng",
                'width' => 30,
                'value' => 'detail.s_type'
            ],
            [
                'name' => "Chương trình",
                'width' => 20,
                'value' => 'detail.program_name'
            ],
            [
                'name' => "Lớp học",
                'width' => 20,
                'value' => 'detail.class_name'
            ],
            [
                'name' => "Ngày bắt đầu học",
                'width' => 20,
                'value' => 'detail.enrolment_start_date'
            ],
            [
                'name' => "Ngày kết thúc",
                'width' => 20,
                'value' => 'detail.enrolment_last_date'
            ]
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