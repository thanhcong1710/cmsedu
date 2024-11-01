<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoringGuidelines extends Model
{
    protected $table = 'scoring_guidelines';
    protected $fillable = ['score','guideline','explanation','status'];
    public $timestamps = false;
}
