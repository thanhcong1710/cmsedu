<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermTeacherBranch extends Model
{
    protected $table = 'term_teacher_branch';
    public $timestamps = false;

    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher');
    }
}
