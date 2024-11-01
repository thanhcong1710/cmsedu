<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */

namespace App\Templates\Imports;


use App\Templates\TemplateInterface;

class StudentTemp implements TemplateInterface
{
    public function getHeader($data = null)
    {
        return [
            [
                'name' => "MẪU NHẬP DANH SÁCH HỌC SINH (DỮ LIỆU THÔ)"
            ]
        ];
    }

    public function getColumns()
    {
        return [
            ['name' => "STT", 'width' => 7, 'value' => 1],
            ['name' => "Mã trung tâm (*)", 'width' => 20, 'value' => 'C02'],
            ['name' => "Mã EC (*)", 'width' => 15, 'value' => 'G0223'],
            ['name' => "Ngày có dữ liệu\n(dd/mm/yyyy)", 'width' => 15, 'value' => '01/02/2019'],
            ['name' => "Họ và tên", 'width' => 20, 'value' => 'Nguyễn Văn A'],
            ['name' => "Ngày sinh\n(dd/mm/yyyy)", 'width' => 15, 'value' => '01/01/2011'],
            ['name' => "Giới tính\n(nam/nữ)", 'width' => 15, 'value' => 'nam'],
            [
                'name' => "Phụ huynh 1",
                'children' => [
                    ['name' => "Họ tên (*)", 'width' => 15, 'value' => 'Họ tên bố'],
                    ['name' => "Ngày sinh\n(dd/mm/yyyy)", 'width' => 15, 'value' => "01/02/2019"],
                    ['name' => "Nghề ngiệp", 'width' => 15, 'value' => 'Thợ hồ'],
                    ['name' => "Số điện thoại (*)", 'width' => 15, 'value' => '19001010', 'data_type' => 'string'],
                    ['name' => "Email", 'width' => 15, 'value' => 'email@gmail.com'],
                ]
            ],
            [
                'name' => "Phụ huynh 2",
                'children' => [
                    ['name' => "Họ tên", 'width' => 15, 'value' => 'Họ tên mẹ'],
                    ['name' => "Ngày sinh\n(dd/mm/yyyy)", 'width' => 15, 'value' => "01/02/2019"],
                    ['name' => "Nghề ngiệp", 'width' => 15, 'value' => 'Thợ may'],
                    ['name' => "Số điện thoại", 'width' => 15, 'value' => '19001010'],
                    ['name' => "Email", 'width' => 15, 'value' => 'email@gmail.com'],
                ]
            ],
            ['name' => "Tỉnh/Thành phố", 'width' => 20, 'value' => '24'],
            ['name' => "Quận/Huyện", 'width' => 20, 'value' => '24.01'],
            ['name' => "Địa chỉ", 'width' => 20, 'value' => 'Nam Từ Liêm, Hà Nội'],
            ['name' => "Từ nguồn", 'width' => 20, 'value' => 'Google'],
            ['name' => "Ghi chú", 'width' => 30, 'value' => 'Trường hợp đặc biệt'],
        ];
    }
}