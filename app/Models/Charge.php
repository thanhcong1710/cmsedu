<?php

namespace App\Models;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $table = 'payment';
    public $timestamps = false;

    public function updatePayment($contractId, $accountingId, $amount, $total, $debt, $note, $chargeDate, $creatorId)
    {
        $paymentCurrent = u::first("select * from payment where accounting_id = '$accountingId'");
        $paymentCurrentId = $paymentCurrent->id;
        $payments = u::query("select * from payment where contract_id = $contractId and id >= $paymentCurrentId order by id asc");
        if (count($payments) === 1) {
            return u::query("
                   UPDATE payment SET
                  `amount` = $amount, 
                  `total` = $total, 
                  `debt` = $debt, 
                  `note` = '$note', 
                  `charge_date` = '$chargeDate',
                  `updator_id` = $creatorId,
                  `updated_at` = NOW()
                  WHERE `accounting_id` = '$accountingId'
                ");
        }

        $paymentLast = $payments[count($payments) -1];
        $roundTotal = $total - $paymentLast->total;
        $roundDebt = $debt - $paymentLast->debt;
        foreach ($payments as $index => $pay) {
            if($index === 0) {
                u::query($query = "
                   UPDATE payment SET
                  `amount` = $amount, 
                  `note` = '$note', 
                  `charge_date` = '$chargeDate',
                  `updator_id` = $creatorId,
                  `updated_at` = NOW()
                   WHERE `id` = '$pay->id'
                ");
            }
            u::query($query = "
                   UPDATE payment SET
                  `total` = $pay->total + $roundTotal, 
                  `debt` = $pay->debt + $roundDebt
                  WHERE `id` = '$pay->id'
                ");
        }
    }

    public function insertPayment($contractId, $accountingId, $method, $payload, $mustCharge, $amount, $total, $debt, $hash,
                  $count, $type, $note, $chargeDate,  $creatorId){
        u::query("
                INSERT INTO payment (`contract_id`, `accounting_id`, `method`, `payload`, `must_charge`, `amount`, `total`, 
                  `debt`, `hash_key`, `count`, `type`, `note`, `charge_date`, `creator_id`, `created_at`)
                VALUES 
                  ($contractId, '$accountingId', $method, $payload, $mustCharge, $amount, $total, $debt, '$hash',
                  $count, '$type', '$note', '$chargeDate',  $creatorId, NOW())
            ");
    }
}
