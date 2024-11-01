<?php

namespace App\Models;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    //
    protected $table = 'discount_codes';

    public function getList($params, $pagination)
    {
        $condition = "";
        if (!empty($params->keyword)) {
            $keyword = $params->keyword;
            $condition .= "( 
            code LIKE '$keyword%'
            OR name LIKE '%$keyword%' )";
        }
        if(!empty($params->start_date)){
            $condition .= empty($condition)? " start_date >= '$params->start_date'" : " AND start_date >= '$params->start_date'";
        }
        if(!empty($params->end_date)){
            $condition .= empty($condition)? " end_date <= '$params->start_date'" : " AND end_date <= '$params->end_date'";
        }
        if(isset($params->status) && is_numeric($params->status)){
            $status = (int) $params->status;
            $condition .= empty($condition)? " status = $status" : " AND status = $status";
        }
        if(!empty($condition)){
            $condition = " WHERE $condition";
        }
        $limit = "";
        if ($pagination->cpage && $pagination->limit) {
            $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
            $limit .= " LIMIT $offset, $pagination->limit";
        }
        $query = "SELECT * FROM {$this->table} $condition ORDER BY (created_at) DESC $limit";
        $total = u::query("SELECT COUNT(1) as total FROM {$this->table} $condition");
        $data = u::query($query);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, ['total' => $total[0]->total, 'data' => $data]);

    }

    public function getAvailableDiscountCodes($roleId = 0)
    {

        $query = "SELECT * FROM {$this->table} where status = 1 AND (CURRENT_DATE() BETWEEN start_date AND end_date)";
        if ($roleId != 0)
            $query = "SELECT * FROM {$this->table} where status = 1";
        return u::query($query);
    }

    public function getAvailableDiscountCodesNew($roleId = 0, $feeId = 0, $zoneId = 0)
    {
        /*
        $query = "SELECT * FROM {$this->table} where status = 1 AND (CURRENT_DATE() BETWEEN start_date AND end_date)";
        if ($roleId != 0)
            $query = "SELECT * FROM {$this->table} where status = 1";
        */
        $dataNew = [];

        if ($feeId != 0){
            $sql = "SELECT price FROM `tuition_fee` WHERE id = $feeId";
            $price = u::first($sql);
            $query = "SELECT * FROM discount_codes WHERE STATUS = 1 AND (CURRENT_DATE() BETWEEN start_date AND end_date) AND (zone_id = $zoneId OR zone_id = 0)";
            /*
             * if (!u::query($query)){
                $query = "SELECT * FROM {$this->table} where status = 1";
            }
            */

            $data = u::query($query);

            if ($data && $price){
                foreach ($data as $obj){
                    if ($price->price == $obj->price){
                        $feeIds = explode(',',$obj->fee_ids);
                        if (in_array($feeId, $feeIds)){
                            $dataNew[] = $obj;
                        }
                    }
                }
            }
        }

        return $dataNew;
    }

}
