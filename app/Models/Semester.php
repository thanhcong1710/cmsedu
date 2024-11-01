<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Semester extends Model
{
    protected $table = 'semesters';
    // public $timestamps = false;

    public static function getList(){
        $query = "SELECT id, name, start_date, end_date FROM semesters WHERE status > 0";

        $res = DB::select(DB::raw($query));

        return $res;
    }
}
