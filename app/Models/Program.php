<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs';
    public $timestamps = false;

    public function product()
    {
    	return $this->belongsTo('App\Models\Product');
    }

    public function classes()
    {
    	return $this->hasMany('App\Models\LmsClass', 'program_id');
    }
}
