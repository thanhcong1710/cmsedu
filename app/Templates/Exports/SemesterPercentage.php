<?php

namespace App\Templates\Exports;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class SemesterPercentage implements ExportTemplateInterface
{
    /**
     * @param null $data
     * @return array
     */
    public function getHeader($data = null)
    {
        return array_merge([
            [
                'name' => "BÁO CÁO TỈ LỆ THEO CHƯƠNG TRÌNH HỌC",
                'weight' => true
            ]
        ], $data ?: []);
    }

    public function getColumns()
    {
        return [
            [
                'name' => "Tên trung tâm",
                'width' => 20,
                'value' => "vl",
            ],
        ];
    }

    public function getLoop()
    {
        return "detail";
    }

}