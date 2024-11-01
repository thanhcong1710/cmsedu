<?php

namespace App\Templates\Exports;

use App\Providers\UtilityServiceProvider as u;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class StudentActive implements ExportTemplateInterface
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
                'name' => "XUẤT DANH SÁCH HỌC SINH THỰC HỌC",
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
                    return $index[0]+1;
                },
            ],
            [
                'name' => "Tên trung tâm",
                'width' => 20,
                'value' => "branch_name",
            ],
            [
                'name' => "Ucrea",
                'width' => 7,
                'loop'=> true,
                'sub'=>'A',
                'value' => function ($row, $index) {
                    return $row->detail;
                },
                'children' => [
                    ['name' => "Tên lớp",'value' => "cls_name", 'width' => 15,'col'=>3],
                    ['name' => "Số lượng học sinh",'value' => "total", 'width' => 15,'col'=>4],
                    ['name' => "Ngày học", 'value' => "class_day",'width' => 15,'col'=>5],
                    ['name' => "Ca học",'value' => "shift_name", 'width' => 15,'col'=>6],
                ]
            ],
            [
                'name' => "Bright IG",
                'width' => 7,
                'loop'=> true,
                'sub'=>'B',
                'value' => function ($row, $index) {
                    return $row->detail;
                },
                'children' => [
                    ['name' => "Tên lớp",'value' => "cls_name", 'width' => 15,'col'=>7],
                    ['name' => "Số lượng học sinh",'value' => "total", 'width' => 15,'col'=>8],
                    ['name' => "Ngày học", 'value' => "class_day",'width' => 15,'col'=>9],
                    ['name' => "Ca học",'value' => "shift_name", 'width' => 15,'col'=>10],
                ]
            ],
            [
                'name' => "Black Hole",
                'width' => 7,
                'loop'=> true,
                'sub'=>'C',
                'value' => function ($row, $index) {
                    return $row->detail;
                },
                'children' => [
                    ['name' => "Tên lớp",'value' => "cls_name", 'width' => 15,'col'=>11],
                    ['name' => "Số lượng học sinh",'value' => "total", 'width' => 15,'col'=>12],
                    ['name' => "Ngày học", 'value' => "class_day",'width' => 15,'col'=>13],
                    ['name' => "Ca học",'value' => "shift_name", 'width' => 15,'col'=>14],
                ]
            ],
            [
                'name' => "Tổng học sinh thực học",
                'width' => 20,
                'value' => 'branch_name',
            ],
            [
                'name' => "Tổng lớp",
                'width' => 20,
                'value' => 'branch_name'
            ],
            [
                'name' => "Tổng ca học",
                'width' => 20,
                'value' => 'branch_name'
            ]
        ];
    }

    public function getLoop()
    {
        return "detail";
    }

}