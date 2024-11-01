<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Response;
use App\Models\StudentTemp;
use App\Models\CustomerCare;
use App\Services\StudentTempService;
use App\Services\TemplateExportService;
use App\Services\SupportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Exception;
use App\Providers\UtilityServiceProvider as u;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ExcelRead;

class StudentTempController extends Controller
{

    public function getTemplate(\App\Templates\Imports\StudentTemp $template, TemplateExportService $service)
    {
        try {
            $service->export($template, "student_temp_import_template");
        } catch (Exception $e) {
        }
    }

    public function getList(Request $request, StudentTemp $model)
    {
        $params = $request->all();
        $data = $model->getStudents($params);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    /**
     * @param Request $request
     * @param StudentTempService $service
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function validateImport(Request $request, StudentTempService $service)
    {
        $userData = $request->users_data;
        if (empty($userData) || !in_array((int)$userData->role_id,[68,69,999999999,686868])) {
            return response()->json(['data' => ['message' => 'Permission denied !']]);
        }
        $this->validate($request, ['file' => 'required']);
        if (!$request->hasFile('file')) {
            return response()->json(['data' => ['message' => 'File is empty !']]);
        }

        $extension = $request->file->extension();
        if ($extension !== "xlsx") {
            return response()->json(['data' => ['message' => 'Format file something wrong !']]);
        }
        $response = new Response();
        $data = $service->validateImport($request);
        return $response->formatResponse(APICode::SUCCESS, $data);
    }
    /**
     * @param Request $request
     * @param StudentTempService $service
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function import(Request $request, StudentTempService $service)
    {
        $userData = $request->users_data;
        if (empty($userData) || !in_array((int)$userData->role_id,[68,69,999999999,686868])) {
            return response()->json(['data' => ['message' => 'Permission denied !']]);
        }
        set_time_limit(-1);
        ini_set("memory_limit", "-1");
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $service->import($request));
    }

    /**
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function delete(Request $request)
    {
        $userData = $request->users_data;
        if (empty($userData) || !in_array((int)$userData->role_id,[68,69,999999999,686868])) {
            return response()->json(['data' => ['message' => 'Permission denied !']]);
        }
        $id = (int)$request->input('id');
        $r = StudentTemp::destroy($id);
        StudentTemp::destroyCareTemp($id);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, ['status' => $r]);
    }

    /**
     * @param Request $request
     * @param StudentTemp $model
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function save(Request $request, StudentTemp $model)
    {
        $userData = $request->users_data;
        if (empty($userData) || !in_array((int)$userData->role_id,[68,69,999999999,686868])) {
            return response()->json(['data' => ['message' => 'Permission denied !']]);
        }
        $params = $request->all();
        $r = $model->saveStudent($params, $userData);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, ['status' => $r]);
    }
    /**
     * @param Request $request
     * @param StudentTemp $model
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function updateStudent(Request $request, StudentTemp $model)
    {
        $userData = $request->users_data;
        if (empty($userData) || !in_array((int)$userData->role_id,[68,69,999999999,686868])) {
            return response()->json(['data' => ['message' => 'Permission denied !']]);
        }
        $params = $request->all();
        $r = $model->updateStudent($params, $userData);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, ['status' => $r]);
    }
    /**
     * @param Request $request
     * @param StudentTemp $model
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function getStudent(Request $request, $id)
    {
//         $data = StudentTemp::where('id', $id)->first();
        $data = StudentTemp::getStudentById($id);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, ['data' => $data]);
    }


    public function careAdd(Request $request)
    {
        if ($request->student_temp_id > 0){
            if ($request->student_temp_id && $request->ec_id){
                u::query("update `student_temp` set ec_id = '$request->ec_id' where id = $request->student_temp_id");
            }
        }

        $customer_care = new CustomerCare();
        if ($request->care_id >0){
            $customer_care_edit = $customer_care::find($request->care_id);
            if ($customer_care_edit){
                $customer_care_edit->contact_method_id = $request->contact_method_id;
                $customer_care_edit->contact_quality_id = $request->contact_quality_id;
                $customer_care_edit->note = $request->care_note;
                $customer_care_edit->creator_id = $request->ec_id;
                if ($request->care_date){
                    $customer_care_edit->created_at = $request->care_date;
                }
                $customer_care->updated_at = now();
                $customer_care_edit->save();
                return response()->json($customer_care_edit);
            }
        }
        else {
            $customer_care->contact_method_id = $request->contact_method_id;
            $customer_care->note = $request->care_note;
            $customer_care->creator_id = $request->ec_id;
            if ($request->student_temp_id >0){
                $customer_care->std_temp_id = $request->std_temp_id;
                $customer_care->status = 0;
            }
            else{
                $customer_care->status = 1;
                $customer_care->crm_id = $request->crm_id;
            }

            $customer_care->created_at = $request->care_date;
            $customer_care->stored_date = now();
            $customer_care->contact_quality_id = $request->contact_quality_id;
            $customer_care->save();
            return response()->json($customer_care);
        }
    }

    public function careDelete(Request $request)
    {
        $delete = u::query("delete from `customer_care` where id = $request->temp_care_id");
        return response()->json($delete);
    }

    public function validateImport1(Request $request){
        $reader = new ExcelRead();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('file'));
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $dataXslx = $sheet->toArray();
        array_splice($dataXslx, 0, 1);
        $items = [];
        foreach ($dataXslx as $data) {
            $crmId = !empty($data[0]) ? $data[0] : 0;
            $to = !empty($data[3]) ? $data[3] : null;
            $from = !empty($data[4]) ? $data[4] : null;
            $item = SupportService::getBranchTransferInfo($crmId,$from,$to);
            if ($item){
                $item->from = $from;
                $item->to = $to;
                $items[] = $item;
            }
            else{
                $items[] = (object)['crm_id'=>$crmId,"status" =>10,"from"=>$from,"to"=>$to];
            }
        }
        //0 - chờ duyệt đi, 1 - chờ duyệt bên nhận, 2 - chờ kế toán hội sở duyệt, 3 - đã duyệt, 4 - từ chối duyệt đi, 5 - từ chối duyệt đến, 6 - kế toán hội sở từ chối, 7 - đã xóa
        $itemsNew = [];
        foreach ($items as $itemNew){
            if ($itemNew->status == 0){
                $itemNew->status = 'chờ duyệt đi';
            }
            else if($itemNew->status == 1){
                $itemNew->status = 'chờ duyệt bên nhận';
            }
            else if($itemNew->status == 2){
                $itemNew->status = 'chờ kế toán hội sở duyệt';
            }
            else if($itemNew->status == 3){
                $itemNew->status = 'đã duyệt';
            }
            else if($itemNew->status == 10){
                $itemNew->status = 'chờ chuyển trung tâm';
            }
            $itemsNew[] = $itemNew;
        }

        //var_dump($itemsNew);exit;
        SupportService::export1($items);
    }

    public function validateImport2(Request $request){
        $reader = new ExcelRead();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('file'));
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $dataXslx = $sheet->toArray();
        //array_splice($dataXslx, 0, 1);
        $items = [];
        foreach ($dataXslx as $data) {
            $crmId = !empty($data[0]) ? $data[0] : 0;
            //var_dump($crmId);exit;
            $to = !empty($data[3]) ? $data[3] : null;
            $from = !empty($data[4]) ? $data[4] : null;
            $item = SupportService::getBranchTransferInfo($crmId,$from,$to);
            if ($item){
                $item->from = $from;
                $item->to = $to;
                $items[] = $item;
            }
            else{
                $items[] = (object)['crm_id'=>$crmId,"status" =>10,"from"=>$from,"to"=>$to];
            }
        }
        //0 - chờ duyệt đi, 1 - chờ duyệt bên nhận, 2 - chờ kế toán hội sở duyệt, 3 - đã duyệt, 4 - từ chối duyệt đi, 5 - từ chối duyệt đến, 6 - kế toán hội sở từ chối, 7 - đã xóa
        $itemsNew = [];
        foreach ($items as $itemNew){
            if ($itemNew->status == 0){
                $itemNew->status = 'chờ duyệt đi';
            }
            else if($itemNew->status == 1){
                $itemNew->status = 'chờ duyệt bên nhận';
            }
            else if($itemNew->status == 2){
                $itemNew->status = 'chờ kế toán hội sở duyệt';
            }
            else if($itemNew->status == 3){
                $itemNew->status = 'đã duyệt';
            }
            else if($itemNew->status == 10){
                $itemNew->status = 'chờ chuyển trung tâm';
            }
            $itemsNew[] = $itemNew;
        }

        SupportService::export1($items);
    }
}
