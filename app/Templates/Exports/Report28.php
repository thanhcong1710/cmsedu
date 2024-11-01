<?php

namespace App\Templates\Exports;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class Report28 implements ExportTemplateInterface
{
    /**
     * @param null $data
     * @return array
     */
    public function getHeader($data = null)
    {
        return array_merge([
            [
                'name' => "BÁO CÁO HỌC SINH ĐẾN HẠN CHƯA WITHDRAW",
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
            ],
            [
                'name' => "Mã CMS",
                'width' => 20,
                'value' => "crm_id",
            ],
            [
                'name' => "Mã Cyber",
                'width' => 20,
                'value' => "cyber_code",
            ],
            [
                'name' => "Tên Học Sinh",
                'width' => 20,
                'value' => "student_name",
            ],
            [
                'name' => "Loại Khách Hàng",
                'width' => 20,
                'value' => "contract_type_name",
            ],
            [
                'name' => "Trung Tâm",
                'width' => 20,
                'value' => "branch_name",
            ],
            [
                'name' => "Sản Phẩm",
                'width' => 20,
                'value' => "product_name",
            ],
            [
                'name' => "Chương Trình",
                'width' => 20,
                'value' => "program_name",
            ],
            [
                'name' => "Lớp Học",
                'width' => 20,
                'value' => "class_name",
            ],
            [
                'name' => "Gói Học Phí",
                'width' => 20,
                'value' => "tuition_fee_name",
            ],
            [
                'name' => "Số Buổi",
                'width' => 20,
                'value' => "available_sessions",
            ],
            [
                'name' => "Ngày Bắt Đầu",
                'width' => 20,
                'value' => "start_date",
            ],
            [
                'name' => "Ngày Học Cuối",
                'width' => 20,
                'value' => "last_date",
            ],
            [
                'name' => "EC",
                'width' => 40,
                'value' => "ec_name",
            ],
            [
                'name' => "CS",
                'width' => 40,
                'value' => "cm_name",
            ]
        ];
    }

    public function getLoop()
    {
        return [];
    }

    public function getStyle(){
        return false;
    }

}