<?php

namespace App\Http\Controllers;

use App\Models\InfoValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TuitionTransfer;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;

class TuitionTransfersController extends Controller
{
    public function getList(Request $request)
    {
        $model = new TuitionTransfer();
        $response = new Response();
        $code = APICode::SUCCESS;
        $user_data = $request->users_data;
        $res = null;
        if ($user_data) {
            $res = $model->getList($request);
        } else {
            $code = APICode::PERMISSION_DENIED;
        }
        return $response->formatResponse($code, $res);
    }
    public function suggestSender(Request $request, $key, $branch_id)
    {
        $response = new Response();
        $transferObj = new TuitionTransfer();
        $res = $transferObj->suggestSender($key, $branch_id);
        return $response->formatResponse(APICode::SUCCESS, $res);
    }
    public function getAllSenderContracts(Request $request, $student_id)
    {
        $data = null;
        $response = new Response();
        $data = TuitionTransfer::getAllSenderContracts($student_id);
        $code = APICode::SUCCESS;
        return $response->formatResponse($code, $data);
    }
    public function suggestReceiver(Request $request, $key, $branch_id, $excepted_student_id = 0)
    {
        $response = new Response();
        $transferObj = new TuitionTransfer();
        $res = $transferObj->suggestReceiver($key, $branch_id, $excepted_student_id);
        return $response->formatResponse(APICode::SUCCESS, $res);
    }
    public function getReceiversLatestContract(Request $request, $student_id, $from_info)
    {
        $data = null;
        $response = new Response();
        $transferObj = new TuitionTransfer();
        $data = $transferObj->getReceiversLatestContract($request, $student_id, $from_info);
        $code = APICode::SUCCESS;
        return $response->formatResponse($code, $data);
    }
    public function prepareTransferData(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $submited = (object) $request->transfering_data;
            $submited->user = $session;
            $data = TuitionTransfer::prepareTransferData($submited);
            $code = APICode::SUCCESS;
        }
        return $response->formatResponse($code, $data);
    }
    public function getAllReasons(Request $request)
    {
        $response = new Response();
        $cond = $request->type ? " AND `type` = $request->type" : "";
        $res = u::query("SELECT * FROM reasons WHERE `status`=1 $cond");

        return $response->formatResponse(APICode::SUCCESS, $res);
    }
    public function storeTransfer(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $post = (object) $request->transfermation_data;
            $post->user_id = $session->id;
            $data = (object) [];
            $code = APICode::SUCCESS;
            $post->request = $request;
            $data = TuitionTransfer::storeTransferedData($post);
        }
        return $response->formatResponse($code, $data);
    }
    public function approveTuitionTransfer(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $post = (object) $request->input();
            $post->session = $session;
            $post->user_id = $session->id;
            $data = (object) [];
            $code = APICode::SUCCESS;
            $data = TuitionTransfer::approveTuitionTransfer($post, $request);
        }
        return $response->formatResponse($code, $data);
    }
    public function getPrintData($tuition_transfer_id){
        $response = new Response();
        $code = APICode::SUCCESS;
        $data = u::first("SELECT s1.name AS from_student_name,s1.crm_id AS from_crm_id, 
                (SELECT `name` FROM products WHERE id=t.from_product_id) AS from_product_name,
                (SELECT `cls_name` FROM classes WHERE id=t.from_class_id) AS from_class_name,
                t.transferred_data,t.transferred_amount,
                t.transferred_sessions,
                s1.gud_name1 AS from_gud_name,s1.gud_mobile1 AS from_gud_mobile,
                s2.name AS to_student_name,s2.crm_id AS to_crm_id,s2.gud_name1 AS to_gud_name,
                s2.gud_mobile1 AS to_gud_mobile,
                (SELECT `name` FROM products WHERE id=t.to_product_id) AS to_product_name,
                t.received_amount,
                t.note,
                (SELECT `name` FROM branches WHERE id=t.from_branch_id) AS from_branch_name
            FROM tuition_transfer_v2 AS t 
                LEFT JOIN students AS s1 ON s1.id=t.from_student_id
                LEFT JOIN students AS s2 ON s2.id=t.to_student_id
            WHERE t.id=$tuition_transfer_id");   
        if($data->transferred_data){
            $transferred_data = json_decode($data->transferred_data);
            $total_charged = 0;
            $done_sessions = 0;
            $charge_date = '';
            $contract_accounting_id = '';
            foreach($transferred_data AS $contract){
                $total_charged += $contract->total_charged;
                $done_sessions += $contract->done_sessions + $contract->done_bonus_sessions;
                $payment_info = u::first("SELECT charge_date,accounting_id FROM payment WHERE contract_id= $contract->contract_id ORDER BY charge_date DESC LIMIT 1");
                $charge_date = $payment_info ? date('d/m/Y',strtotime($payment_info->charge_date)):'';
                $contract_accounting_id = $payment_info ? $payment_info->accounting_id:'';
            }
            $data->total_charged = $total_charged;
            $data->done_sessions = $done_sessions;
            $data->charge_date = $charge_date;
            $data->contract_accounting_id = $contract_accounting_id;
            $data->transferred_amount_text = u::convert_number_to_words($data->transferred_amount)." đồng";
            $data->received_amount_text = u::convert_number_to_words($data->received_amount)." đồng";
        }
        return $response->formatResponse($code, $data);
    }
}
