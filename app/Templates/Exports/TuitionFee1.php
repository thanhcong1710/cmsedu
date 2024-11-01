<?php

namespace App\Templates\Exports;

use App\Providers\UtilityServiceProvider as u;

/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/30/2019
 * Time: 11:39 AM
 */
class TuitionFee1 implements ExportTemplateInterface
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
                'name' => "branch_id",
                'width' => 20,
                'value' => "branch_id",
            ],
            [
                'name' => "branch_name",
                'width' => 20,
                'value' => "branch_name",
            ],
            [
                'name' => "charge_date",
                'width' => 20,
                'value' => "charge_date",
            ],
            [
                'name' => "class_id",
                'width' => 20,
                'value' => "class_id",
            ],
            [
                'name' => "enrolment_last_date",
                'width' => 20,
                'value' => "enrolment_last_date",
            ],
            [
                'name' => "enrolment_start_date",
                'width' => 20,
                'value' => "enrolment_start_date",
            ],
            [
                'name' => "id",
                'width' => 20,
                'value' => "id",
            ],
            [
                'name' => "status",
                'width' => 20,
                'value' => "status",
            ],
            [
                'name' => "student_id",
                'width' => 20,
                'value' => "student_id",
            ],
            [
                'name' => "total_charged",
                'width' => 20,
                'value' => "total_charged",
            ],
            [
                'name' => "tuition_fee_accounting_id",
                'width' => 20,
                'value' => "tuition_fee_accounting_id",
            ],
            [
                'name' => "tuition_fee_id",
                'width' => 20,
                'value' => "tuition_fee_id",
            ],
        ];
    }

    public function getLoop()
    {
        return null;
    }

}