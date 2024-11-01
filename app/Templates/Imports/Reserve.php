<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 3:24 PM
 */

namespace App\Templates\Imports;


use App\Templates\TemplateInterface;

class Reserve implements TemplateInterface
{
    public function getHeader($data = null)
    {
        return [
            [
                'name' => "MẪU NHẬP DANH SÁCH BAO LƯU"
            ]
        ];
    }

    public function getColumns()
    {
        return [
            ['name' => "STT", 'width' => 7, 'value' => 1],
            ['name' => "Mã học viên (*)", 'width' => 20, 'value' => 'HV00635'],
            ['name' => "Ngày bắt đầu bảo lưu (*)\nyyyy-mm-dd", 'width' => 20, 'value' => '2019-08-08'],
            ['name' => "Số buổi bảo lưu (*)", 'width' => 20, 'value' => '1'],
            ['name' => "Loại bảo lưu (*)\n 1 = giữ chỗ, 0 = không giữ chỗ", 'width' => 20, 'value' => '1'],
            ['name' => "Ghi chú (*)", 'width' => 30, 'value' => 'note']
        ];
    }
}