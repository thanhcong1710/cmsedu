<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\UtilityServiceProvider as u;

class CsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSourceNote(Request $request)
    {
        $where = "";
        if (!empty($request->s)){
            $where  = "AND st.`branch_id` IN ($request->s)";
        }
        $sql = "SELECT st.`note` as id, st.`note` as name FROM `student_temp` st  WHERE LENGTH(st.`note`) >0 $where GROUP BY st.`note`";
        $data = u::query($sql);
        return response()->json(['data' =>$data]);
    }
    public function getSource(Request $request)
    {
        $where = "";
        if (!empty($request->s)){
            $where  = "AND st.`branch_id` IN ($request->s)";
        }
        $sql = "SELECT st.`source` as 'id' ,st.`source` as 'name' FROM `student_temp` st  WHERE LENGTH(st.`source`) >0 AND st.`source` != 'auto import' $where GROUP BY st.`source`";
        $data = u::query($sql);
        return response()->json(['data' =>$data]);
    }

    public function getCustomFields()
    {
        $data = self::contactsCustomFieldsApi();
        return response()->json(['data' =>$data]);
    }

    private function saveCustomField($custom_field_id, $custom_field_lable, $values){
        foreach ($values as $value){
            $id = $value->id;
            $lable = $value->lable;
            $total = u::first("SELECT COUNT(id) total FROM `cs_custom_fields` WHERE id = $id AND custom_field_id = $custom_field_id ")->total;
            if ($total == 0){
                $add = "INSERT  INTO `cs_custom_fields`(`custom_field_id`,`custom_field_lable`,`id`,`label`,`created_at`) 
                        VALUES ($custom_field_id,'$custom_field_lable',$id,'$lable',NOW())";
                u::query($add);
            }
        }
    }

    private  function contactsCustomFieldsApi(){
        $url = "https://api2.caresoft.vn/cms/api/v1/contacts/custom_fields";
        $header = [
            "Content-Type: application/json",
            "Authorization: Bearer 7ooSGVILKlDz8MI"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);

        $res = null;
        try {
            $res = curl_exec ($ch);
            if ($res) {
                $resJson = json_decode($res);
                if (isset($resJson->code)){
                    if ($resJson->code == "ok"){
                        $custom_fields = $resJson->custom_fields;
                        foreach ($custom_fields as $custom_field){
                            self::saveCustomField($custom_field->custom_field_id, $custom_field->custom_field_lable, $custom_field->values);
                        }
                        exit;
                    }
                }
            }
        }
        catch (\Exception $exception) {
            exit;
        }
    }

}
