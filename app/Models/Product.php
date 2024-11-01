<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $table = 'products';
    // public $timestamps = false;


    public function programs(){
    	return $this->hasMany('App\Models\Program');
    }

    public function classes(){
    	return $this->hasMany('App\Models\LmsClass', 'product_id');
    }

    public function tuition_fees(){
    	return $this->hasMany('App\Models\TuitionFee', 'product_id');
    }

}
