<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'contracts';
    public $timestamps = false;

    public function student(){
    	return $this->belongsTo('App\Models\Student');
    }

    public static function createContract($data){
        $contract = new Contract();

        $contract->student_id = $data['student_id'];
        $contract->type = $data['type'];
        $contract->ec_id = $data['ec_id'];
        $contract->cm_id = $data['cm_id'];
        $contract->ceo_brand_id = $data['ceo_brand_id'];
        $contract->ceo_region_id = $data['ceo_region_id'];
        $contract->tuition_free_id = $data['tuition_free_id'];
        $contract->bill_info = $data['bill_info'];
        $contract->description = $data['description'];
        $contract->receivable = $data['receivable'];
        $contract->created_date = $data['created_date'];
        $contract->creator_id = $data['creator_id'];
        $contract->hash_key = $data['hash_key'];
        $contract->changed_fields = $data['changed_fields'];

        if($contract->save()){
            return APICode::SUCCESS;
        }else{
            return false;
        }
    }
}
