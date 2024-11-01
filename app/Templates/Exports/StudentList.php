<?php

namespace App\Templates\Exports;

use App\Providers\UtilityServiceProvider as u;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class StudentList implements ExportTemplateInterface
{
    const CONTRACT_STATUS = [
        6 => 'Đã được xếp lớp và đang đi học',
        7 => 'Đã bị withdraw',
    ];


    /**
     * @param null $data
     * @return array
     */
    public function getHeader($data = null)
    {
        return array_merge([
            [
                'name' => "XUẤT DANH SÁCH HỌC SINH",
                'weight' => true
            ]
        ], $data ?: []);
    }

    public function getColumns()
    {
        //Tên Học Sinh	Mã CMS	Mã Cyber	Nguồn Từ	Trạng Thái	Học Lớp	Giới Tính	Ngày Sinh	Phụ Huynh	Điện Thoại	EC	Trung Tâm
        return [
            [
                'name' => "STT",
                'width' => 7,
                'value' => function ($row, $index) {
                    return $index + 1;
                },
            ],
            [
                'name' => "Tên Học Sinh",
                'width' => 20,
                'value' => 'name',
            ],
            [
                'name' => "Mã CMS",
                'width' => 20,
                'value' => 'crm_id',
            ],
            [
                'name' => "Mã Cyber",
                'width' => 20,
                'value' => 'accounting_id',
            ],
            // [
            //     'name' => "Trạng Thái",
            //     'width' => 20,
            //     'value' => 'status_name'
            // ],
            [
                'name' => "Học Lớp",
                'width' => 20,
                'value' => 'cls_name'
            ],
            [
                'name' => "Ngày Sinh",
                'width' => 20,
                'value' => 'date_of_birth'
            ],
            [
                'name' => "Điạ Chỉ",
                'width' => 20,
                'value' => 'student_address'
            ],
            [
                'name' => "Trung Tâm",
                'width' => 20,
                'value' =>'branch_name'
            ],
            [
                'name' => "Ngày đóng phí gần nhất",
                'width' => 20,
                'value' =>'last_charge_date'
            ],
            [
                'name' => "Họ tên mẹ",
                'width' => 20,
                'value' =>'gud_name1'
            ],
            [
                'name' => "SĐT mẹ",
                'width' => 20,
                'value' =>'gud_mobile1'
            ],
            [
                'name' => "Email mẹ",
                'width' => 20,
                'value' =>'gud_email1'
            ],
            [
                'name' => "Ngày sinh mẹ",
                'width' => 20,
                'value' =>'gud_birth_day1'
            ],
            [
                'name' => "Nghề nghiệp mẹ",
                'width' => 20,
                'value' =>'gud_job1'
            ],
            [
                'name' => "Họ tên bố",
                'width' => 20,
                'value' =>'gud_name2'
            ],
            [
                'name' => "SĐT bố",
                'width' => 20,
                'value' =>'gud_mobile2'
            ],
            [
                'name' => "Email bố",
                'width' => 20,
                'value' =>'gud_email2'
            ],
            [
                'name' => "Ngày sinh bố",
                'width' => 20,
                'value' =>'gud_birth_day2'
            ],
            [
                'name' => "Nghề nghiệp bố",
                'width' => 20,
                'value' =>'gud_job2'
            ],
            [
                'name' => "Cấp trường",
                'width' => 20,
                'value' =>'school_level'
            ],
            [
                'name' => "Trường học",
                'width' => 20,
                'value' =>'school'
            ],
            [
                'name' => "Tỉnh Thành Phố",
                'width' => 20,
                'value' =>'province_name'
            ],
            [
                'name' => "Quận Huyện",
                'width' => 20,
                'value' =>'district_name'
            ],
        ];
    }

    public function getLoop()
    {
        return null;
    }

}