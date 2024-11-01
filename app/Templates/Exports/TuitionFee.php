<?php

namespace App\Templates\Exports;

use App\Providers\UtilityServiceProvider as u;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class TuitionFee implements ExportTemplateInterface
{
    /**
     * @param null $data
     * @return array
     */
    public function getHeader($data = null)
    {
        return array_merge([
            [
                'name' => "BÁO CÁO THỐNG KÊ SỐ LƯỢNG GÓI HỌC",
                'weight' => true
            ]
        ], $data ?: []);
    }

    public function getColumns()
    {
        return [
            [
                'name' => "GÓI HỌC ĐĂNG KÝ",
                'width' => 20,
                'value' => "branch_name",
                'children' => [
                    ['name' => "TRUNG TÂM",'value' => "total", 'width' => 15],
                    ['name' => "TRUNG TÂM",'value' => "revenue", 'width' => 15],
                ],
                'fields'=>['SỐ LƯỢNG','DOANH THU']
            ],
            [
                'name' => "Tổng Số Học Sinh Đăng Ký",
                'width' => 20,
                'value' => "branch_name",
            ],
            [
                'name' => "UCREA",
                'width' => 7,
                'loop'=> true,
                'sub'=>'A',
                'value' => function ($row, $index) {
                    return u::get($row, "branch_name");
                },
                'children' => [
                    ['name' => "UCREA01",'value' => "", 'width' => 15],
                    ['name' => "UCREA02",'value' => "", 'width' => 15],
                    ['name' => "UCREA03", 'value' => "",'width' => 15],
                    ['name' => "UCREA04",'value' => "", 'width' => 15],
                    ['name' => "UCREA05",'value' => "", 'width' => 15],
                    ['name' => "UCREA06",'value' => "", 'width' => 15],
                    ['name' => "UCREA12",'value' => "", 'width' => 15],
                    ['name' => "UCREA24",'value' => "", 'width' => 15],
                ]
            ],
            [
                'name' => "BRIGHT",
                'width' => 7,
                'loop'=> true,
                'sub'=>'B',
                'value' => function ($row, $index) {
                    return u::get($row, "branch_name");
                },
                'children' => [
                    ['name' => "BRIGHT01",'value' => "", 'width' => 15],
                    ['name' => "BRIGHT02",'value' => "", 'width' => 15],
                    ['name' => "BRIGHT03", 'value' => "",'width' => 15],
                    ['name' => "BRIGHT04",'value' => "", 'width' => 15],
                    ['name' => "BRIGHT05",'value' => "", 'width' => 15],
                    ['name' => "BRIGHT06",'value' => "", 'width' => 15],
                    ['name' => "BRIGHT12",'value' => "", 'width' => 15],
                    ['name' => "BRIGHT24",'value' => "", 'width' => 15],
                ]
            ],
            [
                'name' => "BLACKHOLE",
                'width' => 7,
                'loop'=> true,
                'sub'=>'C',
                'value' => function ($row, $index) {
                    return u::get($row, "branch_name");
                },
                'children' => [
                    ['name' => "BLACKHOLE01",'value' => "", 'width' => 15],
                    ['name' => "BLACKHOLE02",'value' => "", 'width' => 15],
                    ['name' => "BLACKHOLE03", 'value' => "",'width' => 15],
                    ['name' => "BLACKHOLE04",'value' => "", 'width' => 15],
                    ['name' => "BLACKHOLE05",'value' => "", 'width' => 15],
                    ['name' => "BLACKHOLE06",'value' => "", 'width' => 15],
                    ['name' => "BLACKHOLE12",'value' => "", 'width' => 15],
                    ['name' => "BLACKHOLE24",'value' => "", 'width' => 15],
                ]
            ]
        ];
    }

    public function getLoop()
    {
        return "detail";
    }

}