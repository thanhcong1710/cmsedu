<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\UtilityServiceProvider as u;

class District extends Model
{
    //
    protected $table = 'districts';
    public $timestamps = false;

    public function getList($params){
        $where = "";
        if(isset($params['province_id'])){
            $where.="province_id = {$params['province_id']}";
        }
        if(!empty($where)){
            $where = "where ".$where;
        }
        $query = "select * from districts $where";
        return u::query($query);
    }
}
