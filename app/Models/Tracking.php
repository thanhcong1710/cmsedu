<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 12/3/2018
 * Time: 3:15 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $table = 'tracking';

    public static function log($transaction_id, $object_id, $params, $module, $step, $query, $result, $creator_id, $execute_time = null, $description = null, $binding=[]){
        if(!empty($binding)){
            $sql = self::genQuery($query, $binding);
        }else{
            $sql = $query;
        }

        $id = self::insertGetId([
            'transaction_id' => $transaction_id,
            'object_id' => $object_id,
            'module' => $module,
            'step' => $step,
            'params' => json_encode($params),
            'query' => $sql,
            'result' => !is_bool($result)?$result:($result?'success':'fail'),
            'creator_id' => $creator_id,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 1,
            'execute_time' => $execute_time,
            'description' => $description
        ]);

        return $id;
    }

    public static function genQuery($form, $dataBinding){
        $parts = explode("?",$form);

        $query = "";

        foreach ($parts as $key => $part){
            $query .= $part . (isset($dataBinding[$key])?$dataBinding[$key]:"");
        }

        return $query;
    }
}
