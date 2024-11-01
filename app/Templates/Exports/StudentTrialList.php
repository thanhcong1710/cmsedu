<?php

namespace App\Templates\Exports;

use App\Providers\UtilityServiceProvider as u;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class StudentTrialList implements ExportTemplateInterface
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
                'name' => "XUẤT DANH SÁCH HỌC SINH HỌC THỬ",
                'weight' => true
            ]
        ], $data ?: []);
    }

    public function getColumns()
    {
        //STT		Năm sinh	Họ tên Phụ Huynh	Số điện thoại 	Email	Địa chỉ	Tên khóa học	Thời gian học	Nguồn 	"Tình trạng
        //check in"	"Tình trạng sau
        //trải nghiệm"	NV sale phụ trách	Ghi chú
        return [
            [
                'name' => "STT",
                'width' => 7,
                'value' => function ($row, $index) {
                    return $index + 1;
                },
            ],
            [
                'name' => "Tên học viên",
                'width' => 20,
                'value' => 'name',
            ],
            [
                'name' => "Năm sinh",
                'width' => 20,
                'value' => 'date_of_birth',
            ],
            [
                'name' => "Họ tên Phụ Huynh",
                'width' => 20,
                'value' => 'gud_name1',
            ],
            [
                'name' => "Số điện thoại",
                'width' => 20,
                'value' => 'gud_mobile1'
            ],
            [
                'name' => "Email",
                'width' => 20,
                'value' => 'email'
            ],
            [
                'name' => "Địa chỉ",
                'width' => 20,
                'value' => 'address'
            ],
            [
                'name' => "Tên khóa học",
                'width' => 20,
                'value' => 'cls_name'
            ],
            [
                'name' => "Thời gian học",
                'width' => 20,
                'value' =>'enrolment_start_date'
            ],
            [
                'name' => "Nguồn",
                'width' => 20,
                'value' =>'source'
            ],
            [
                'name' => "Tình trạng check in",
                'width' => 20,
                'value' =>''
            ],
            [
                'name' => "Tình trạng sau trải nghiệm",
                'width' => 20,
                'value' =>''
            ],
            [
                'name' => "NV sale phụ trách",
                'width' => 20,
                'value' =>'cs_name'
            ],
            [
                'name' => "Ghi chú",
                'width' => 20,
                'value' =>'note'
            ],[
                'name' => "Trung tâm",
                'width' => 40,
                'value' =>'branch_name'
            ],
        ];
    }

    public function getLoop()
    {
        return null;
    }

}