<?php

namespace App\Templates\Exports;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class StudentRenewals implements ExportTemplateInterface
{
    /**
     * @param null $data
     * @return array
     */
    public function getHeader($data = null)
    {
        return array_merge([
            [
                'name' => "BÁO CÁO DANH SÁCH HỌC SINH CẦN RENEW TRONG THÁNG",
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
                'value' => 'i',
            ],
            [
                'name' => "Tên trung tâm",
                'width' => 20,
                'value' => "branch_name",
            ],
            [
                'name' => "Mã HS",
                'width' => 20,
                'value' => "crm_id",
            ],
            [
                'name' => "Mã Cyber",
                'width' => 20,
                'value' => "cyber_code",
            ],
            [
                'name' => "Tên Học Viên",
                'width' => 20,
                'value' => "sdt_name",
            ],
            [
                'name' => "Ngày Sinh",
                'width' => 20,
                'value' => "date_of_birth",
            ],
            [
                'name' => "Năm Sinh",
                'width' => 20,
                'value' => "year_of_birth",
            ],
            [
                'name' => "Thông Tin Phụ Huynh",
                'width' => 20,
                'value' => "4",
                'children' => [
                    [
                        'name' => "Thông Tin Phụ Huynh 1",
                        'value' => "",
                        'width' => 15,
                        'children' => [
                            ['name' => "Họ Và Tên",'value' => "gud_name1", 'width' => 15],
                            ['name' => "Sđt",'value' => "gud_mobile1", 'width' => 15],
                        ]
                    ],
                    [
                        'name' => "Thông Tin Phụ Huynh 2",
                        'value' => "",
                        'width' => 15,
                        'children' => [
                            ['name' => "Họ Và Tên",'value' => "gud_name2", 'width' => 15],
                            ['name' => "Sđt",'value' => "gud_mobile2", 'width' => 15],
                        ]
                    ],
                ]
            ],
            [
                'name' => "Địa Chỉ",
                'width' => 20,
                'value' => "address",
            ],
            [
                'name' => "Ngày Nhập Học",
                'width' => 20,
                'value' => "enrolment_start_date",
            ],
            [
                'name' => "Lớp Học",
                'width' => 20,
                'value' => "cls_name",
            ],
            [
                'name' => "Chương Trình",
                'width' => 20,
                'value' => "program_name",
            ],
            [
                'name' => "Trị Giá Khóa Học",
                'width' => 20,
                'value' => "tuition_fee",
            ],
            [
                'name' => "Ngày Đến Hạn Đóng Học Phí Mới",
                'width' => 20,
                'value' => "enrolment_last_date",
            ],
            [
                'name' => "Thông Tin Sales",
                'width' => 20,
                'value' => "ec_name",
            ],
            [
                'name' => "Lịch Học Đăng Ký/Ngày",
                'width' => 20,
                'value' => "class_day",
            ],
            [
                'name' => "Ca Học",
                'width' => 20,
                'value' => "shift_name",
            ],
            [
                'name' => "Tình Trạng Renew (Yes/No)",
                'width' => 20,
                'value' => "re_type",
            ],
            [
                'name' => "Nguyên Nhân Chưa Renew",
                'width' => 20,
                'value' => "re_info",
            ],
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