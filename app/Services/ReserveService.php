<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 7/22/2019
 * Time: 1:58 PM
 */

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ReservesController;
use App\Models\APICode;
use App\Models\ProcessExcel;
use App\Models\Reserve;
use App\Models\Response;
use App\Models\Student;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\Input;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ExcelRead;
use PhpParser\Node\Expr\Array_;


class ReserveService extends Controller
{

    private function validateImportReserve($reserve)
    {
        $res = [];
        $studentAccountingId = $reserve['student_code'];
        $student = null;
        if (empty($studentAccountingId)) {
            $res[] = "Chưa nhập mã học viên";
        } else if (empty($student = Student::query("select id from students where accounting_id = $studentAccountingId")->first())) {
            $res[] = "Học viên không có trong hệ thống";
        };

        $startDate = $reserve['start_date'];
        if (!u::isValidDate($startDate)) {
            $res[] = "Ngày bắt đầu không đúng định dạng";
        }

        if (!is_numeric($reserve['number_of_reserves'])) {
            $res[] = "Chưa nhập số buổi bảo lưu";
        }
        if (!is_numeric($reserve['reserve_type'])) {
            $res[] = "Chưa nhập loại bảo lưu";
        }

        if ($student) {
            $reserve = new Reserve();
            $reserve->getNumberOfReservesByStudentId($student->id);
        }

        return $res;
    }

    public function validateImport($request, $pathname, $isExcute)
    {
        $reader = new ExcelRead();
        $spreadsheet = $reader->load($pathname);
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $dataXslx = $sheet->toArray();
        unset($dataXslx[0]);
        $dataCorrects = [];
        $dataInCorrects = [];

        $fields = ['stt', 'student_code', 'start_date', 'number_of_reserves', 'reserve_type', 'note'];

        foreach ($dataXslx as $row) {
            $reserve = [];
            foreach ($fields as $key => $field) {
                $reserve[$field] = isset($row[$key]) ? trim($row[$key]) : null;
            }
            $validate = self::validateImportReserve($reserve);
            if (empty($validate)) {
                $dataCorrects[] = $reserve;
            } else {
                $reserve['message'] = $validate;
                $dataInCorrects[] = $reserve;
            }
        }

        if ($isExcute) {
            foreach ($dataCorrects as $index => $item) {
                $r = (Array)json_decode(self::createReserve($request, $item));
                $dataCorrects[$index]['message'] = $r['code'] == 200 ? "Insert success" : $r['message'] ?: 'Insert error';
            }
            $response = [
                'data' => ['data' => $dataCorrects, 'message' => 'insert success!']
            ];
        } else {
            $response = [
                'data' => ['data' => array_merge($dataInCorrects, $dataCorrects), 'message' => 'success!', 'success' => count($dataInCorrects) === 0]
            ];
        }
        return response()->json($response);
    }

    public function getReserveType($max, $session)
    {
        if ($max === 0)
            return 1;

        if (($max > 0) && ($session <= $max))
            return 0;

        return 2;
    }

    /**
     * student_id: this.data.reserve.student_id,
     * contract_id: this.data.reserve.contract_id,
     * note: this.data.reserve.note,
     * start_date: this.data.reserve.start_date,
     * end_date: this.data.reserve.end_date,
     * session: this.data.reserve.session,
     * branch_id: this.data.reserve.branch_id,
     * product_id: this.data.reserve.product_id,
     * program_id: this.data.reserve.program_id,
     * class_id: this.data.reserve.class_id,
     * is_reserved: this.data.reserve.is_reserved ? 1 : 0,
     * new_end_date: this.data.reserve.new_enrol_end_date,
     * reserve_type: this.getReserveType(),
     * attached_file: this.data.reserve.attached_file,
     * meta_data: {
     * total_session: this.data.enroll.real_session,
     * total_fee: this.data.enroll.total_fee,
     * session_left: this.data.temp.number_of_session_left,
     * amount_left: this.data.temp.amount_left,
     * start_date: this.data.enroll.start_date,
     * end_date: this.data.reserve.new_enrol_end_date,
     * before_reserve_end_date: this.data.enroll.end_date,
     * number_of_session_reserved: this.data.temp.number_of_session_reserved,
     * amount_reserved: this.data.temp.amount_reserved,
     * sessions_from_start_to_reserve_date: this.data.temp.sessions_from_start_to_reserve_date,
     * amount_from_start_to_reserve_date: this.data.temp.amount_from_start_to_reserve_date,
     * new_start_date: this.data.temp.new_start_date,
     * old_enrol_end_date: this.data.temp.old_enrol_end_date,
     * special_reserved_sessions: ((this.data.reserve.session - this.data.temp.max_session) > 0)?(this.data.reserve.session - this.data.temp.max_session):0
     * }
     * @param $request
     * @param $data
     * @return false|string
     */

    private function createReserve($request, $data)
    {
        $model = new Reserve();
        $studentCode = $data['student_code'];
        $student = u::first("select * from students where accounting_id='$studentCode'");
        $contract = $model->getContractForReserveByStudentId($student->id);

        if ($contract == null) {
            $response  = new Response();
            return $response->formatResponse(APICode::WRONG_PARAMS, null, "Không tìm thấy contract phù hợp");
        }
        $contract->total_fee = $contract->total_charged;
        $reserve = (Object)[];
        $data = (Object)$data;
        $metadata = (Object)[];
        $reserve_end_date = u::calEndDate($data->number_of_reserves, $contract->class_days, $contract->holidays, $data->start_date)->end_date;
        $new_reserve_range = (Object)[
            'start_date' => $data->start_date,
            'end_date' => $reserve_end_date
        ];
        $holidays = $contract->holidays;
        $holidays[] = $new_reserve_range;
        $contract->holidays = $holidays;
        $metadata->total_session = $contract->real_sessions;
        $metadata->total_fee = $contract->total_fee;
        $passed_session = strtotime($contract->start_date) < time() ? u::calSessions($contract->start_date, date('Y-m-d'), $contract->holidays, $contract->class_days)->total : 0;
        $metadata->session_left = (int)$contract->real_sessions - $passed_session;
        $metadata->amount_left = ceil($metadata->session_left * $metadata->total_fee / $contract->real_sessions);
        $metadata->start_date = $contract->start_date;
        $new_enrol_end_date = u::calEndDate(((int)$data->number_of_reserves + 1), $contract->class_days, $contract->holidays, $contract->end_date)->end_date;
        $metadata->end_date = $new_enrol_end_date;
        $metadata->before_reserve_end_date = $contract->end_date;
        $done_sessions = u::calSessions($contract->start_date, u::subtractDays($data->start_date, 1), $contract->holidays, $contract->class_days)->total;
        if ($data->start_date > $contract->start_date) {
            $metadata->number_of_session_reserved = $contract->real_sessions - $done_sessions;
            $metadata->amount_reserved = ceil($metadata->number_of_session_reserved * $contract->total_fee / $contract->real_sessions);
            $metadata->amount_from_start_to_reserve_date = ceil($done_sessions * $contract->total_fee / $contract->real_sessions);
        } else {
            $metadata->number_of_session_reserved = $contract->real_sessions;
            $metadata->amount_reserved = $contract->total_fee;
            $metadata->amount_from_start_to_reserve_date = 0;
        }
        $max_session = $contract->reservable_sessions - $contract->reserved_sessions;
        $metadata->sessions_from_start_to_reserve_date = $done_sessions;
        $metadata->new_start_date = u::calEndDate(2, $contract->class_days, $contract->holidays, $reserve_end_date)->end_date;
        $metadata->old_enrol_end_date = ($done_sessions == 0) ? $contract->start_date : u::calEndDate($done_sessions, $contract->class_days, $contract->holidays, $contract->start_date)->end_date;
        $metadata->special_reserved_sessions = (($data->number_of_reserves - $max_session) > 0) ? ($data->number_of_reserves - $max_session) : 0;

        $reserve->student_id = $student->id;
        $reserve->contract_id = $contract->contract_id;
        $reserve->note = $data->note;
        $reserve->end_date = $reserve_end_date;
        $reserve->start_date = $data->start_date;
        $reserve->session = $data->number_of_reserves;
        $reserve->branch_id = $contract->branch_id;
        $reserve->product_id = $contract->product_id;
        $reserve->program_id = $contract->program_id;
        $reserve->class_id = $contract->class_id;
        $reserve->is_reserved = $data->reserve_type;
        $reserve->new_end_date = $new_enrol_end_date;
        $reserve->reserve_type = self::getReserveType($max_session, $data->number_of_reserves);
        $reserve->meta_data = (Array)$metadata;

        $controller = new ReservesController();
        Input::replace((Array)$reserve);
        $res = $controller->create($request);
        return $res;

    }
}