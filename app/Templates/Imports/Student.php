<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */

namespace App\Templates\Imports;


use App\Templates\TemplateInterface;

class Student implements TemplateInterface
{
    public function getHeader($data = null)
    {
        return [
            [
                'name' => "MẪU NHẬP DANH SÁCH HỌC SINH"
            ]
        ];
    }

    public function getColumns()
    {
        return [
            ['name' => "STT", 'width' => 7, 'value' => 1],
            ['name' => "Mã trung tâm (*)", 'width' => 20, 'value' => 'C02'],
            ['name' => "Mã EC (*)", 'width' => 15, 'value' => 'G0223'],
            ['name' => "Mã học viên", 'width' => 20, 'value' => 'HV-00382'],
            ['name' => "Tên học sinh (*)", 'width' => 20, 'value' => 'Nguyễn Văn A'],
            ['name' => "Ngày sinh\n(yyyy-mm-dd)", 'width' => 15, 'value' => '2015-05-25'],
            ['name' => "Giới tính\n(1 = nam, 0 = nữ)", 'width' => 15, 'value' => 1],
            ['name' => "Số điện thoại", 'width' => 20, 'value' => '03389459404'],
            ['name' => "Tên thường gọi", 'width' => 10, 'value' => 'A Pháo'],
            ['name' => "Mã Tỉnh/Thành phố", 'width' => 20, 'value' => '24'],
            ['name' => "Mã Quận/Huyện", 'width' => 20, 'value' => '24.01'],
            ['name' => "Địa chỉ", 'width' => 20, 'value' => 'Nam Từ Liêm, Hà Nội'],
            ['name' => "Đối tượng khách hàng\n(0 = Thường, 1 = VIP)", 'width' => 15, 'value' => 1],
            ['name' => "FaceBook", 'width' => 20, 'value' => 'hotboycongnghe'],
            ['name' => "Trường học", 'width' => 20, 'value' => 'Trường tiểu học Nam Từ Liêm'],
            ['name' => "School grade", 'width' => 15, 'value' => 'grade 1'],
            ['name' => "Từ nguồn", 'width' => 20, 'value' => 'Google'],
            ['name' => "Ghi chú", 'width' => 30, 'value' => 'Trường hợp đặc biệt'],
            ['name' => "Mã CMS của anh chị em học cùng", 'width' => 15, 'value' => ''],
            ['name' => "Tên phụ huynh 1", 'width' => 15, 'value' => 'Nguyễn Văn AA'],
            ['name' => "SDT phụ huynh 1", 'width' => 15, 'value' => '09879847346'],
            ['name' => "Email phụ huynh 1", 'width' => 15, 'value' => 'phuhuynhaa@gmail.com'],
            ['name' => "Ngày sinh phụ huynh 1\n(yyyy-mm-dd)", 'width' => 15, 'value' => ''],
            ['name' => "Ngành nghề/Cơ quan công tác  phụ huynh 1", 'width' => 15, 'value' => ''],
            ['name' => "Tên phụ huynh 2", 'width' => 15, 'value' => ''],
            ['name' => "SDT phụ huynh 2", 'width' => 15, 'value' => ''],
            ['name' => "Email phụ huynh 2", 'width' => 15, 'value' => ''],
            ['name' => "Ngày sinh phụ huynh 2\n(yyyy-mm-dd)", 'width' => 15, 'value' => ''],
            ['name' => "Ngành nghề/Cơ quan công tác phụ huynh 2", 'width' => 15, 'value' => ''],
        ];
    }
}