<?php

namespace App\Templates\Exports;

//use App\Providers\UtilityServiceProvider as u;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class StudentActiveList implements ExportTemplateInterface
{

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
                    return $index+1;
                },
            ],
            [
                'name' => "Mã CRM",
                'width' => 20,
                'value' => 'crm_id',
            ],
            [
                'name' => "Mã Cyber",
                'width' => 20,
                'value' => 'accounting_id',
            ],
            [
                'name' => "Tên học sinh",
                'width' => 30,
                'value' => 'student_name',
            ],
            [
                'name' => "Trung Tâm",
                'width' => 40,
                'value' => 'branch_name',
            ],
            [
                'name' => "Sản Phẩm",
                'width' => 20,
                'value' => 'seme_name',
            ],
            [
                'name' => "Lớp Học",
                'width' => 40,
                'value' => 'cls_name',
            ],
            [
                'name' => "Ca Học",
                'width' => 30,
                'value' => 'shift_name',
            ],
            [
                'name' => "Ngày học",
                'width' => 20,
                'value' => 'day_name',
            ],
            [
                'name' => "Ngày bắt đầu",
                'width' => 20,
                'value' => 'enrolment_start_date',
            ],
            [
                'name' => "Ngày kết thúc",
                'width' => 20,
                'value' => 'enrolment_last_date',
            ],
            [
                'name' => "Trạng thái",
                'width' => 35,
                'value' => 'status1',
            ],
        ];
    }

    public function getLoop()
    {
        return [];
    }

    public function getStyle()
    {
        return false;
    }

}