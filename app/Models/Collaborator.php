<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\UtilityServiceProvider as u;
class Collaborator extends Model
{
    public $timestamps = false;
    protected $table = 'collaborators';

    public static function createCollaborator($data){
        $collaborator = new Collaborator();
        $code = !empty($data['code']) ? $data['code'] : '';
        $exist = u::first("select id from `collaborators` where code = '$code'");
        $error = ['error_code' => 0, 'message' =>''];
        if ($exist)
            return ['error_code' => 1, 'message' => 'Mã cộng tác viên đã tồn tại vui lòng tạo mã CTV khác.'];

        $collaborator->code = $data['code'];
        $collaborator->school_name = $data['school'];
        $collaborator->address = $data['address'];
        $collaborator->personal_name = $data['name'];
        $collaborator->phone_number = $data['phone'];
        $collaborator->email = $data['email'];
        $collaborator->status = (String)$data['status'];
        $collaborator->created_at = date('Y-m-d H:i:s');

        if($collaborator->save()){
            return $error;
        }else{
            return false;
        }
    }

    public static function getCollaborator($id = 0){
        return u::first("select * from `collaborators` where id = $id");
    }

    public static function updateCollaborator($params= []){
        $collaborator = self::where('id', '=', $params['id'])->first();
        $collaborator->school_name = $params['school_name'];
        $collaborator->address = $params['address'];
        $collaborator->personal_name = $params['personal_name'];
        $collaborator->phone_number = $params['phone_number'];
        $collaborator->email = $params['email'];
        $collaborator->status = (String)$params['status'];
        $error = ['error_code' => 0, 'message' =>''];

        try {
            $res =  $collaborator->save();
            if ($res)
                return $error;
        } catch (\Exception $exception) {
            return $error;
        }
    }

    public function getList($params, $pagination)
    {
        $condition = "";
        if(!empty($condition)){
            $condition = " WHERE $condition";
        }
        $limit = "";
        if ($pagination->cpage && $pagination->limit) {
            $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
            $limit .= " LIMIT $offset, $pagination->limit";
        }

        $query = "SELECT * FROM {$this->table} $condition ORDER BY (created_at) DESC $limit";
        $total = u::query("SELECT COUNT(1) as total FROM {$this->table} $condition");
        $data = u::query($query);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, ['total' => $total[0]->total, 'data' => $data]);

    }

}
