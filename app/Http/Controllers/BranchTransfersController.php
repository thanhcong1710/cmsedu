<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InfoValidation;
use Illuminate\Http\Request;
use App\Models\BranchTransfer;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;


class BranchTransfersController
{
  public function getList(Request $request, $pagination, $search, $sort)
  {
    $response = new Response();
    $model = new BranchTransfer();
    $code = APICode::PERMISSION_DENIED;
    $data = null;
    if ($session = $request->users_data) {
      $code = APICode::SUCCESS;
      $data = $model->getList($request, $pagination, $search, $sort);
    }
    return $response->formatResponse($code, $data);
  }

  public function suggestPassenger(Request $request, $key, $branch_id)
  {
    $response = new Response();
    $code = APICode::PERMISSION_DENIED;
    $data = null;
    if ($session = $request->users_data) {
      $code = APICode::SUCCESS;
      $model = new BranchTransfer();
      $data = $model->suggestPassenger($key, $branch_id);
    }
    return $response->formatResponse($code, $data);
  }
  public function getAllPassengersContracts(Request $request, $student_id)
  {
    $response = new Response();
    $code = APICode::PERMISSION_DENIED;
    $data = null;
    if ($session = $request->users_data) {
      $code = APICode::SUCCESS;
      $model = new BranchTransfer();
      $data = $model->getAllContracts($student_id);
    }
    return $response->formatResponse($code, $data);
  }
  public function checkToBranch(Request $request, $student_id, $branch_id)
  {
    $response = new Response();
    $code = APICode::PERMISSION_DENIED;
    $data = null;
    if ($session = $request->users_data) {
      $code = APICode::SUCCESS;
      $model = new BranchTransfer();
      $data = $model->checkTargetBranch($branch_id, $student_id);
    }
    return $response->formatResponse($code, $data);
  }
  public function prepareTransferData(Request $request)
  {
    $response = new Response();
    $code = APICode::PERMISSION_DENIED;
    $data = null;
    if ($session = $request->users_data) {
      $code = APICode::SUCCESS;
      $post = (object) $request->input();
      $model = new BranchTransfer();
      $data = $model->prepareTransferData((object) $post->transfering_data);
    }
    return $response->formatResponse($code, $data);
  }
  public function storeTransferedData(Request $request)
  {
    $response = new Response();
    $code = APICode::PERMISSION_DENIED;
    $data = null;
    if ($session = $request->users_data) {
      $code = APICode::SUCCESS;
      $post = (object) $request->transfermation_data;
      $post->user_id = $session->id;
      $model = new BranchTransfer();
      $data = $model->storeTransferedData((object) $post);
    }
    return $response->formatResponse($code, $data);
  }

  public function approveBranchTransfer(Request $request)
  {
    $response = new Response();
    $code = APICode::PERMISSION_DENIED;
    $data = null;
    if ($session = $request->users_data) {
      $code = APICode::SUCCESS;
      $post = (object) $request->input();
      $post->user_id = $session->id;
      $post->role_name = $session->role_name;
      $post->user_name = $session->name;
      $post->token = $request->headers->get('Authorization');
      $model = new BranchTransfer();
      $data = $model->approveBranchTransfer($post, $request);
    }
    return $response->formatResponse($code, $data);
  }
  public function getPrintData($branch_transfer_id)
  {
    $response = new Response();
    $code = APICode::SUCCESS;
    $data = u::first("SELECT s.name AS student_name,s.crm_id, s.accounting_id, s.gud_name1, s.gud_mobile1, 
          (SELECT name FROM branches WHERE id= bt.from_branch_id) AS from_branch_name,
          (SELECT name FROM branches WHERE id= bt.to_branch_id) AS to_branch_name,
          bt.note,bt.transfer_date, bt.transferred_data, bt.transferred_amount, bt.transferred_sessions,
          (SELECT name FROM products WHERE id = bt.from_product_id) AS product_name,
          '' AS done_sessions,
          '' AS charge_date,
          '' AS total_charged,
          (SELECT full_name FROM users WHERE id=bt.creator_id) AS creator_name,
          (SELECT full_name FROM users WHERE id=bt.accounting_approver_id) AS accounting_name,
          (SELECT full_name FROM users WHERE id=bt.to_approver_id) AS to_approver_name,
          DATE_FORMAT(bt.created_at , '%H:%i %d/%m/%Y') AS created_date,
          DATE_FORMAT(bt.accounting_approved_at , '%H:%i %d/%m/%Y') AS account_approved_date,
          DATE_FORMAT(bt.to_approved_at , '%H:%i %d/%m/%Y') AS to_approved_date
        FROM  branch_transfer AS bt 
          LEFT JOIN students AS s ON s.id=bt.student_id 
        WHERE bt.id=$branch_transfer_id");
    $data->transfer_date = date('d/m/Y',strtotime($data->transfer_date));
    $data->transferred_amount_text = u::convert_number_to_words($data->transferred_amount)." đồng";
    if($data->transferred_data){
      $data->done_sessions = 0;
      $transferred_data = (object)json_decode($data->transferred_data);
      $contracts = $transferred_data->contracts;
      foreach($contracts AS $contract){
        $contract = (object)$contract;
        $data->done_sessions +=$contract->done_sessions;
        if($contract->total_charged>0){
          $data->total_charged =(int)$data->total_charged + $contract->total_charged;
          $charge_date_info = u::first("SELECT charge_date FROM payment WHERE contract_id=$contract->contract_id ORDER BY charge_date DESC LIMIT 1 ");
          $data->charge_date = $charge_date_info ? date('d/m/Y',strtotime($charge_date_info->charge_date)):'';
        }
      }
    }
    return $response->formatResponse($code, $data);
  }
}
