<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2/19/2020
 * Time: 5:26 PM
 */

namespace App\Services;
use App\Providers\UtilityServiceProvider as u;

class LogService
{
    public static function logContract($id = 0){
        $latest_contract = u::first("SELECT * FROM contracts WHERE id = '$id'");
        $previous_hashkey = md5("$latest_contract->id$latest_contract->created_at$latest_contract->updated_at$latest_contract->hash_key");
        $current_hashkey = $previous_hashkey;
        if ($latest_contract && isset($latest_contract->id)) {
            $insert_log_contract = "INSERT INTO log_contracts_history
              (`contract_id`,
              `code`,
              `type`,
              `payload`,
              `student_id`,
              `branch_id`,
              `ceo_branch_id`,
              `region_id`,
              `ceo_region_id`,
              `ec_id`,
              `ec_leader_id`,
              `cm_id`,
              `om_id`,
              `product_id`,
              `program_label`,
              `tuition_fee_id`,
              `receivable`,
              `must_charge`,
              `total_discount`,
              `debt_amount`,
              `description`,
              `bill_info`,
              `start_date`,
              `end_date`,
              `total_sessions`,
              `real_sessions`,
              `bonus_sessions`,
              `summary_sessions`,
              `expected_class`,
              `passed_trial`,
              `status`,
              `created_at`,
              `creator_id`,
              `hash_key`,
              `updated_at`,
              `editor_id`,
              `only_give_tuition_fee_transfer`,
              `reservable`,
              `reservable_sessions`,
              `sibling_discount`,
              `discount_value`,
              `after_discounted_fee`,
              `tuition_fee_price`,
              `count_recharge`,
              `continue_class`,
              `note`,
              `previous_hashkey`,
              `current_hashkey`,
              `version_no`,
              `enrolment_start_date`,
              `enrolment_last_date`,
              `coupon`
              )
              VALUES
              ('".(int)$latest_contract->id."',
              '$latest_contract->code',
                '$latest_contract->type',
                '$latest_contract->payload',
                '$latest_contract->student_id',
                '$latest_contract->branch_id',
                '$latest_contract->ceo_branch_id',
                '$latest_contract->region_id',
                '$latest_contract->ceo_region_id',
                '$latest_contract->ec_id',
                '$latest_contract->ec_leader_id',
                '$latest_contract->cm_id',
                '$latest_contract->om_id',
                '$latest_contract->product_id',
                '$latest_contract->program_label',
                '$latest_contract->tuition_fee_id',
                '$latest_contract->receivable',
                '$latest_contract->must_charge',
                '$latest_contract->total_discount',
                '$latest_contract->debt_amount',
                '$latest_contract->description',
                '$latest_contract->bill_info',
                '$latest_contract->start_date',
                '$latest_contract->end_date',
                '$latest_contract->total_sessions',
                '$latest_contract->real_sessions',
                '$latest_contract->bonnus_sessions',
                '$latest_contract->summary_sessions',
                '$latest_contract->expected_class',
                '$latest_contract->passed_trial',
                '$latest_contract->status',
                 NOW(),
                '1',
                '$latest_contract->hash_key',
                '$latest_contract->updated_at',
                '1',
                '$latest_contract->only_give_tuition_fee_transfer',
                '$latest_contract->reservable',
                '$latest_contract->reservable_sessions',
                '$latest_contract->sibling_discount',
                '$latest_contract->discount_value',
                '$latest_contract->after_discounted_fee',
                '$latest_contract->tuition_fee_price',
                '$latest_contract->count_recharge',
                '$latest_contract->continue_class',
                '$latest_contract->note',
                '$previous_hashkey',
                '$current_hashkey',
                '1',
                '$latest_contract->enrolment_start_date',
                '$latest_contract->enrolment_last_date',
                '$latest_contract->coupon')";

            u::query($insert_log_contract);
        }
    }

}
