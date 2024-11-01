<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 9/5/2019
 * Time: 6:03 PM
 */

namespace App\Services;

use App\Models\Branch;
use App\Models\StudentTemp;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ExcelRead;

class StudentTempService
{

    public function convert_from_latin1_to_utf8_recursively($dat)
    {
        if (is_string($dat)) {
            return utf8_encode($dat);
        } elseif (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d) $ret[$i] = self::convert_from_latin1_to_utf8_recursively($d);

            return $ret;
        } elseif (is_object($dat)) {
            foreach ($dat as $i => $d) $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);

            return $dat;
        } else {
            return $dat;
        }
    }

    function isBinary($str)
    {
        return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
    }

    protected function preg_replace_mobile($str){
        $str = preg_replace('/\s/', '', $str);
        return str_replace(["'",".","x",",","0`","`",";","+"],'',$str);
    }
    /**
     * @param Request $request
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function getStudentTempFromRequestFile($request)
    {
        $fields = [
            'stt', 'branch_accounting_id', 'ec_accounting_id', 'date', 'name', 'date_of_birth', 'gender',
            'gud_name1', 'gud_date_of_birth1', 'gud_job1', 'gud_mobile1', 'gud_email1',
            'gud_name2', 'gud_date_of_birth2', 'gud_job2', 'gud_mobile2', 'gud_email2',
            'province', 'district', 'address', 'source', 'note'
        ];
        $reader = new ExcelRead();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('file'));
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $dataXslx = $sheet->toArray();
        array_splice($dataXslx, 0, 9);
        $items = [];
        foreach ($dataXslx as $data) {
            $student = [];
            foreach ($fields as $index => $field) {
                if (!isset($data[$index])) {
                    $student[$field] = null;
                } else {
                    if ($index == "gud_mobile1" || $index == "gud_mobile2"){
                        $student[$field]  = $this->preg_replace_mobile($data[$index]);
                    }
                    else{
                        $student[$field] = $data[$index];
                    }
                }
            }
            $items[] = $student;
        }
        return $items;
    }


    private function validateStudent($student, $branches, $phones)
    {
        $res = [];
        if (empty($student)) {
            $res['error'][] = "Không có dữ liệu";
            return $res;
        }
        $branchAccountingId = $student['branch_accounting_id'];
        $branch = u::get($branches, $branchAccountingId);
        if (empty($branchAccountingId)) {
            $res['error'][] = 'Chưa điền tên trung tâm';
        } else {
            if (empty($branch) && $branchAccountingId != "C100") {
                $res['error'][] = "Trung tâm không tồn tại trong hệ thống";
            }
        }

        $ecAccountingId = $student['ec_accounting_id'];
        $userAccountingIds = u::get($branch, 'user_accounting_ids', []);
        if (!empty($ecAccountingId) && !in_array($ecAccountingId, $userAccountingIds)) {
            $res['error'][] = 'EC không có trong trung tâm';
        }

        if (empty(u::get($student, 'gud_mobile1'))) {
            $res['error'][] = 'Chưa điền số điện thoại phụ huynh 1';
        }

        $gud_mobile1 = u::get($student, 'gud_mobile1');
        if (!is_numeric(u::convertMobileNumber($gud_mobile1))){
            $gud_mobile1_new = substr($gud_mobile1,3,strlen($gud_mobile1));
           if (!is_numeric($gud_mobile1_new)){
               $res['error'][] = 'Số điện thoại phụ huynh 1 nhập không đúng';
           }
        }

//        if (u::get($student, 'gud_mobile1') === 0) { //!u::validateMobileNumber(u::get($student, 'gud_mobile1'))
//            $res['error'][] = 'Số điện thoại phụ huynh 1 nhập không đúng';
//        }


        if (!empty(u::get($student, 'gud_mobile2')) && !u::validateMobileNumber(u::get($student, 'gud_mobile2'))) {
            $res['error'][] = 'Số điện thoại phụ huynh 2 nhập không đúng';
        }

        $phone1 = u::convertMobileNumber(u::get($student, 'gud_mobile1'));
        $phone2 = u::convertMobileNumber(u::get($student, 'gud_mobile2'));
        if (is_array($phones) && (in_array($phone1, $phones) || in_array($phone2, $phones))) {
            $res['warning'][] = 'Học viên đã tồn tại trong hệ thống';
        }

        return $res;
    }

    public function getPhoneExists($students)
    {
        $phones = [];
        $phonesNew = [];
        foreach ($students as $student) {
            $gud_mobile1 = u::get($student, 'gud_mobile1');
            if (!empty($gudMobile1) && strlen($gudMobile1) >= 9) {
                $phones[] = substr($gudMobile1, strlen($gudMobile1) - 9);
            }

            $gudMobile2 = u::convertMobileNumber($gud_mobile1);
            if (!empty($gudMobile2) && strlen($gudMobile2) >= 9) {
                $phones[] = substr($gudMobile2, strlen($gudMobile2) - 9);
            }

        }
        foreach ($phones as $phone){
            if (is_numeric($phone)){
                $phonesNew[] = $phone;
            }
            else{
                $phonesNew[] = 0;
            }
        }

        if (empty($phonesNew)) {
            return null;
        }

        $strPhone = implode(',', $phonesNew);
        $query = "
            select gud_mobile1, gud_mobile2 from students where substr(gud_mobile1, CHAR_LENGTH(gud_mobile1) - 8) in ($strPhone)
                                                             or substr(gud_mobile2, CHAR_LENGTH(gud_mobile2) - 8) in ($strPhone)
            union
            select gud_mobile1, gud_mobile2 from student_temp where substr(gud_mobile1, CHAR_LENGTH(gud_mobile1) - 8) in ($strPhone)
                                                                 or substr(gud_mobile2, CHAR_LENGTH(gud_mobile2) - 8) in ($strPhone)
        ";
        $data = DB::select(DB::raw($query));
        $phone1 = array_column($data, 'gud_mobile1');
        $phone2 = array_column($data, 'gud_mobile2');
        $res = array_values(array_filter(array_merge($phone1, $phone2)));
        return $res;
    }
    
    /**
     * @param Request $request
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function validateImport($request)
    {
        ini_set("memory_limit", "-1");
        $items = self::getStudentTempFromRequestFile($request);
        $phones = $this->getPhoneExists($items);
        $branchModel = new Branch();
        $branches = $branchModel->getAllBranchEcs();
        $itemsNew = [];
        $error = 0;
        foreach ($items as $i => &$item) {
            $item['message'] = $this->validateStudent($item, $branches, $phones);
            $item['sort_index'] = count(u::get($item, 'message.error', [])) * 2 + count(u::get($item, 'message.warning', []));
            if (!empty($item['message']['error']) || !empty($item['message']['warning'])) {
                //$branch_accounting_id = u::get($item, 'branch_accounting_id');
                //if ($branch_accounting_id == "C100"){
                    $itemsNew[] = $item;
                    if (!empty($item['message']['error'])){
                        $error = 1;
                    }
                //}
            }
            else{
                $itemsNew[] = $item;
            }
        }

        usort($itemsNew, function ($first, $second) {
            return $first['sort_index'] < $second['sort_index'];
        });

        $allowImport = sizeof($itemsNew) > 0 ? true : false;
        if ($error == 1){
            $allowImport = false;
        }
        return ['data' => $itemsNew, 'allow_import' => $allowImport];
    }

    public function import($request)
    {
        $students = $request->students;
        $userData = $request->users_data;

        $branchModel = new Branch();
        $model = new StudentTemp();
        $branches = $branchModel->getAllBranchEcs();
        foreach ($students as &$item) {
            if($item['message']!='warning'){
                if (!u::get($item, 'message')){
                    $item['gender'] = u::get($item, 'gender') === 'nam' ? 1 : 0;
                    if ($item['branch_accounting_id'] == "C100"){
                        $item['branch_id'] = 100;
                        $item['ec_id'] = 415;
                    }
                    else{
                        $branch = u::get($branches, $item['branch_accounting_id']);
                        $userIds = $branch->user_ids;
                        $item['branch_id'] = $branch->branch_id;
                        $item['ec_id'] = (int)u::get($userIds, u::get($item, 'ec_accounting_id'));
                    }
                    $item['date_of_birth'] = date("Y-m-d", strtotime(u::get($item, 'date_of_birth')));
                    $item['gud_date_of_birth1'] = date("Y-m-d", strtotime(u::get($item, 'gud_date_of_birth1')));
                    $item['gud_date_of_birth2'] = date("Y-m-d", strtotime(u::get($item, 'gud_date_of_birth2')));
                    $item['date'] = date("Y-m-d", strtotime(u::get($item, 'date')));
                    $item['id'] = $model->saveStudent($item, $userData);
                    if ($item['id'] > 0) {
                        $item['message']['success'] = "Lưu thành công";
                    } else {
                        $item['message']['error'][] = "Lưu thất bại";
                    }
                }
                else{
                    //C11 import data Hub HO Tele sale
                    if ($item['branch_accounting_id'] == "C100"){
                        //$branch = u::get($branches, $item['branch_accounting_id']);
                        //$userIds = $branch->user_ids;
                        $item['gender'] = u::get($item, 'gender') === 'nam' ? 1 : 0;
                        $item['branch_id'] = 100;//$branch->branch_id;
                        $item['ec_id'] = 415;//(int)u::get($userIds, u::get($item, 'ec_accounting_id'));
                        $item['date_of_birth'] = date("Y-m-d", strtotime(u::get($item, 'date_of_birth')));
                        $item['gud_date_of_birth1'] = date("Y-m-d", strtotime(u::get($item, 'gud_date_of_birth1')));
                        $item['gud_date_of_birth2'] = date("Y-m-d", strtotime(u::get($item, 'gud_date_of_birth2')));
                        $item['date'] = date("Y-m-d", strtotime(u::get($item, 'date')));
                        $item['id'] = $model->saveStudent($item, $userData);
                        /**
                         * if ($item['id'] > 0) {
                            $item['message']['success'] = "Lưu thành công";
                        } else {
                            $item['message']['error'][] = "Lưu thất bại";
                        }
                        */
                    }
                }
            }
        }
        return ['data' => $students];
    }
}