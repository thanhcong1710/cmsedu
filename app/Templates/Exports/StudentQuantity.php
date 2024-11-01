<?php

namespace App\Templates\Exports;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class StudentQuantity implements ExportTemplateInterface
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
                'name' => "",
                'width' => 30,
                'value' => "branch_id",
                'children' => [
                    ['name' => "W1",'value' => "", 'width' => 30],
                ],
            ],
            [
                'name' => "UCREA",
                'width' => 20,
                'value' => "branch_id",
                'children' => [
                    ['name' => "Số Lớp",'value' => "", 'width' => 15],
                    ['name' => "Tổng Số Học Sinh",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Đang Học",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Bảo lưu",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Chờ Lớp",'value' => "", 'width' => 15],
                ],
            ],
            [
                'name' => "BRIGHT IG",
                'width' => 20,
                'value' => "branch_id",
                'children' => [
                    ['name' => "Số Lớp",'value' => "", 'width' => 15],
                    ['name' => "Tổng Số Học Sinh",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Đang Học",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Bảo lưu",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Chờ Lớp",'value' => "", 'width' => 15],
                ],
            ],
            [
                'name' => "BLACK HOLE",
                'width' => 20,
                'value' => "branch_id",
                'children' => [
                    ['name' => "Số Lớp",'value' => "", 'width' => 15],
                    ['name' => "Tổng Số Học Sinh",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Đang Học",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Bảo lưu",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Chờ Lớp",'value' => "", 'width' => 15],
                ],
            ],
            [
                'name' => "Total",
                'width' => 20,
                'value' => "branch_id",
                'children' => [
                    ['name' => "Số Lớp",'value' => "", 'width' => 15],
                    ['name' => "Tổng Số Học Sinh",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Đang Học",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Chờ Lớp",'value' => "", 'width' => 15],
                    ['name' => "Số Hs Bảo lưu",'value' => "", 'width' => 15],
                    ['name' => "Tỷ Lệ Active",'value' => "", 'width' => 15],
                    ['name' => "Tỷ Lệ Bảo lưu",'value' => "", 'width' => 15],
                    ['name' => "Tỷ Lệ Pending",'value' => "", 'width' => 15],
                    ['name' => "Số HSTB/ Lớp",'value' => "", 'width' => 15],
                ],
            ],
        ];
    }

    public function getLoop()
    {
        return null;
    }

}