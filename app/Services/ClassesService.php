<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\LmsClass;

/*
* Created by HoiLN
*/
class ClassesService
{
    public function updateCm($classID, $cmID)
    {
        $class = LmsClass::find($classID);
        $class->cm_id = $cmID;
        $class->save();
        return true;
    }
}