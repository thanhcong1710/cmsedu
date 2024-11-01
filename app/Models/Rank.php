<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    protected $table = 'ranks';
    public $timestamps = false;
    public function termStudentRanks(){
    	return $this->hasMany('App\Models\TermStudentRank', 'rank_id');
    }
}
