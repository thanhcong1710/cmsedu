<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\Classes;
use App\Models\Contract;
use App\Models\CyberAPI;
use App\Models\DiscountCode;
use App\Models\Product;
use App\Models\Program;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TuitionFee;
use App\Models\User;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ExcelRead;

//use PhpOffice\PhpSpreadsheet\Writer;
class UploadController extends Controller
{
    protected $_data_cell;
    protected $_extension = ['xlsx', 'xls', 'csv', 'xlsm', 'Xml', 'Ods'];
    protected $_path = '/static/doc/other/';

    public function __construct()
    {
        // echo FOLDER . DS . 'doc\\other\\';die;
    }

    public function execImportStudent(Request $request)
    {
        set_time_limit(-1);
        return $this->importStudentV2($request, true);
    }

    public function execImportContracts(Request $request)
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(-1);
        return $this->importContracts($request, true);
    }

    public function execImportClasses(Request $request)
    {
        set_time_limit(-1);
        ini_set("memory_limit", "-1");
        return $this->importClasses($request, true);
    }

    public function importClasses(Request $request, $execImport = false)
    {
        set_time_limit(-1);
        ini_set("memory_limit", "-1");
        $userData = $request->users_data;
        if (empty($userData) || (int)$userData->role_id !== 999999999) {
            return response()->json(['data' => ['message' => 'Permission denied !']]);
        }
        $this->validate($request, ['file' => 'required']);
        if (!$request->hasFile('file')) {
            return response()->json(['data' => ['message' => 'File is empty !']]);
        }

        $extension = $request->file->extension();
        if (!in_array($extension, $this->_extension)) {
            return response()->json(['data' => ['message' => 'Format file something wrong !']]);
        }

        $filename = 'classes-' . time() . '.' . $extension;
        $request->file->storeAs($this->_path, $filename);
        $file = FOLDER . DS . 'doc/other/' . $filename;
        $reader = new ExcelRead();
        $spreadsheet = $reader->load($file);
        $data = [];
        $branches = u::convertArrayToObject(self::getAllBranchWithEC(), function ($branch) {
            return strtolower(str_replace(' ', '', u::utf8convert($branch->accounting_id)));
        });

        $semesters = u::convertArrayToObject(Semester::query()->get(), function ($semester) {
            return strtolower(str_replace(' ', '', u::utf8convert($semester->name)));
        });

        $rooms = u::convertArrayToObject(Room::query()->get(), function ($room) {
            return strtolower(str_replace(' ', '', u::utf8convert($room->room_name . $room->branch_id)));
        });

        $programs = u::convertArrayToObject(Program::query()->get(), function ($program) {
            return strtolower(str_replace(' ', '', u::utf8convert("{$program->name}{$program->branch_id}")));
        });

        $shifts = u::convertArrayToObject(Shift::query()->get(), function ($shift) {
            return strtolower(str_replace(' ', '', u::utf8convert($shift->name)));
        });

        $success = true;
        foreach ($spreadsheet->getAllSheets() as $key => $sheet) {
            $dataXslx = $sheet->toArray();
            $clazz = self::getClassInExcelData($dataXslx);
            $validate = self::validateClass($clazz, $branches, $semesters, $rooms, $shifts);
            if (!empty($validate)) {
                $success = false;
            }
            $clazz['message'] = $validate;

            $students = self::getStudentsInExcelData($dataXslx);
            foreach ($students as &$student) {
                $vali = self::validateStudent($student);
                if (!empty($vali)) {
                    $success = false;
                }
                $student['message'] = empty($vali) ? null : $vali;
                unset($student);
            }

            $data[] = [
                'class' => $clazz,
                'students' => $students
            ];
        }

        if ($execImport) {
            foreach ($data as &$item) {
                $clazz = &$item['class'];
                $classInsert = $this->generateClass($request, $clazz, $branches, $semesters, $rooms, $programs, $shifts);
                if ($classInsert['id'] > 0) {
                    $clazz['message'] = isset($classInsert['message']) ? $classInsert['message'] : "Insert success";
                    $students = &$item['students'];
                    if (empty($students)) continue;

                    $classInfo = (array)self::getClassInfo($classInsert['id']);
                    $schedules = u::convertArrayToObject(Schedule::where('class_id', '=', $classInsert['id'])->get(), function ($schedule) {
                        return strtolower(str_replace(' ', '', u::utf8convert($schedule->cjrn_classdate)));
                    });
                    foreach ($students as &$student) {
                        $status = $this->assignStudentToClass($request, $classInsert, $classInfo, $student, $schedules);
                        $student['message'] = !empty($status) ? $status : "Insert success";

                        $days = [];
                        foreach ($student as $key => $value) {
                            $date = strtotime($key);
                            if ($date) {
                                $days[$key] = $value;
                            }
                        }
                        $currentUser = $request->users_data;
                        $this->attendanceForStudent($days, $student, $currentUser, $classInfo, $classInsert);
                        unset($student);
                    }
                    unset($students);
                } else {
                    $clazz['message'] = "Insert error";
                }
                unset($clazz);
                unset($item);
            }
        }

        $response = [
            'data' => ['data' => $data, 'message' => 'success!', 'success' => $success]
        ];

        return response()->json($response);
    }

    private function convertStatusAttendance($status)
    {
        switch (strtolower(str_replace(' ', '', u::utf8convert($status)))) {
            case 'x':
                return [
                    'id' => 1,
                    'name' => Attendances::statusOptions[1]
                ];
            case 'nghi':
                return [
                    'id' => 2,
                    'name' => Attendances::statusOptions[2]
                ];
            case 'muon':
                return [
                    'id' => 3,
                    'name' => Attendances::statusOptions[3]
                ];
        }
    }

    private function attendanceForStudent($days, $student, $currentUser, $classInfo, $classInsert)
    {
        if (empty($days)) return;

        $student = Student::where('accounting_id', '=', $student['code'])->first();
        if (empty($student)) return;

        $contract = Contract::where([['class_id', '=', $classInfo['id']], ['student_id', '=', $student->id]])->first();
        if (empty($contract) || empty($classInfo) || empty($classInsert)) return;

        foreach ($days as $day => $value) {
            if (empty($value)) continue;
            $data = [];
            $data['attendance_date'] = $day;
            $data['attendance_id'] = null;
            $data['attendance_status'] = null;
            $data['attendance_status_drop'] = $this->convertStatusAttendance($value);
            $data['attendance_status_id'] = $data['attendance_status_drop']['id'];
            $data['attendance_type'] = -1;
            $data['branch_id'] = $classInsert['branch_id'];
            $data['class_id'] = $classInfo['id'];
            $data['product_id'] = $classInfo['product_id'];
            $data['program_id'] = $classInsert['program_id'];
            $data['contract_id'] = $contract->id;
            $data['enrolment_id'] = $contract->id;
            $data['type'] = $contract->type;
            $data['reason'] = "";
            $data['note'] = "";
            $data['homework'] = 0;
            $data['student_id'] = $student->id;

            $attendance = Attendances::where([
                ['student_id', '=', $student->id],
                ['class_id', '=', $classInfo['id']],
                ['attendance_date', '=', $day]
            ])->first();

            if (isset($attendance->id)) {
                $data['attendance_id'] = $attendance->id;
                Attendances::updateAttendances((Object)$data, $currentUser);
            } else {
                Attendances::insertAttendances((Object)$data, $currentUser);
            }
        }
    }


    private function getClassInfo($class_id)
    {
        return u::first("SELECT
          c.id,
          c.id AS class_id,
          c.product_id,
          c.cls_name AS class_name,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          u.status AS cm_status,
          CONCAT(u.full_name, ' - ', u.username) AS cm_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', COALESCE(t.avatar, 'avatar.png'), '#', t.ins_name) SEPARATOR '@ ') FROM teachers AS t
            LEFT JOIN `sessions` AS s ON s.teacher_id = t.user_id WHERE s.class_id = $class_id) AS teachers_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', r.room_name) SEPARATOR '@ ') FROM rooms AS r
            LEFT JOIN `sessions` AS s ON s.room_id = r.id WHERE s.class_id = $class_id) AS rooms_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', s.class_day) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN classes AS c ON s.class_id = c.id WHERE s.class_id = $class_id) AS weekdays,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', f.start_time, '-', f.end_time) SEPARATOR ', ') FROM `sessions` AS s
            LEFT JOIN shifts AS f ON s.shift_id = f.id WHERE s.class_id = $class_id) AS shifts_name,
          (SELECT t.id FROM `sessions` AS s LEFT JOIN `teachers` AS t ON s.teacher_id = t.user_id LEFT JOIN users u ON u.id = t.user_id WHERE u.status > 0 AND s.class_id = $class_id LIMIT 1) AS teacher_id,
          (SELECT s.room_id FROM `sessions` AS s WHERE s.class_id = $class_id LIMIT 1) AS room_id,
          (SELECT s.shift_id FROM `sessions` AS s WHERE s.class_id = $class_id LIMIT 1) AS shift_id,
          (SELECT GROUP_CONCAT(s.class_day SEPARATOR ',') FROM `sessions` AS s WHERE s.class_id = $class_id) AS arr_weekdays,
           (SELECT count(cjrn_id) FROM schedules WHERE class_id=$class_id) AS num_session
        FROM classes AS c
          LEFT JOIN users AS u ON c.cm_id = u.id
        WHERE c.id = $class_id");
    }

    private function getContractInfo($branch_id, $class_id, $student_id)
    {
        $select = "SELECT
                    c.id,
                    c.product_id,
                    c.id AS contract_id,
                    c.code AS contract_code,
                    s.id AS student_id,
                    s.cms_id AS student_cms_id,
                    c.type AS customer_type,
                    s.name AS student_name,
                    s.nick,
                    c.count_recharge,
                    c.type,
                    CONCAT(s.name, ' - ', COALESCE(s.stu_id, s.crm_id)) AS label,
                    s.school AS student_school,
                    g.name AS student_school_grade,
                    s.phone AS student_phone,
                    (SELECT file FROM trial_reports WHERE student_id = c.student_id AND session_no = 9 ORDER BY id DESC LIMIT 1) attached_file, 
                    c.start_date AS contract_start_date,
                    t.name AS tuition_fee_name,
                    t.price AS tuition_fee_price,
                    t.receivable AS tuition_fee_receivable,
                     CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
                    c.debt_amount,
                    c.processed, 
                    c.branch_id, 
                    c.status, 
                    c.total_charged AS charged_total,
                    c.real_sessions AS available_sessions,
                    0 AS checked,
                    '' AS class_date";
        $query = "FROM contracts AS c
                    LEFT JOIN students AS s ON c.student_id = s.id
                    LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
                    LEFT JOIN school_grades AS g ON s.school_grade = g.id
                    LEFT JOIN users AS u0 ON c.cm_id = u0.id
                    LEFT JOIN users AS u1 ON c.ec_id = u1.id
                    LEFT JOIN reserves AS r ON c.student_id = r.student_id
                    LEFT JOIN pendings AS p ON c.student_id = p.student_id
                    LEFT JOIN classes AS cl ON cl.program_id = c.program_id                    
                    JOIN ( SELECT student_id, Min( count_recharge ) AS m, id FROM contracts 
                      WHERE branch_id = $branch_id 
                        AND `status` < 6
                        AND ((type > 0 AND status IN (1,2,3,4,5)) OR (type = 0 AND status IN (1,3,5)))
                        AND real_sessions > 0
                      GROUP BY student_id ) AS b ON ( c.id = b.id AND c.count_recharge = b.m )
                    WHERE c.branch_id = $branch_id 
                        AND c.`status` < 6
                        AND ((c.type > 0 AND c.status IN (1,2,3,4,5)) OR (c.type = 0 AND c.status IN (1,3,5)))
                        AND (c.product_id = (SELECT se.product_id FROM semesters se RIGHT JOIN classes cls ON cls.sem_id = se.sem_id WHERE cls.id = $class_id)) 
                        AND (cl.id = $class_id OR cl.id IS NULL)
                        AND ((c.real_sessions > 0 AND c.type IN (1,2,3,4,5,6,7)) OR (c.type IN (0,8)))
	                    AND ((c.total_charged > 0 AND c.type IN (1,2,3,4,5,6,7)) OR (c.type IN (0,8)))
                        AND c.student_id = $student_id 
                        GROUP BY c.student_id";
        /**
         *  AND c.`status` < 6
         * AND ((c.type > 0 AND c.status IN (1,2,3,4,5)) OR (c.type = 0 AND c.status IN (1,3,5)))
         * AND (c.product_id = (SELECT se.product_id FROM semesters se RIGHT JOIN classes cls ON cls.sem_id = se.sem_id WHERE cls.id = $class_id))
         * AND (cl.id = $class_id OR cl.id IS NULL)
         * AND ((c.real_sessions > 0 AND c.type IN (1,2,3,4,5,6,7)) OR (c.type IN (0,8)))
         * AND ((c.total_charged > 0 AND c.type IN (1,2,3,4,5,6,7)) OR (c.type IN (0,8)))
         * AND c.student_id = $student_id GROUP BY c.student_id
         */
        return u::query("$select $query");
    }

    /**
     * 'branch_id' => isset($branch->branch_id) ? $branch->branch_id : null,
     * 'class_name' => $clazz['class_name'],
     * 'cm_id' => null,
     * 'max_students' => 16,
     * 'num_session' => 48,
     * 'program_id' => isset($program->id) ? $program->id : null,
     * 'room_id' => isset($room->id) ? $room->id : null,
     * 'semester_id' => isset($semester->id) ? $semester->id : null,
     * 'shift_id' => isset($shift->id) ? $shift->id : null,
     * 'start_date' => isset($clazz['start_date']) ? $clazz['start_date'] : null,
     * 'status' => 0,
     * 'teacher_id' => u::get($teacher, 'id'),
     * 'weekdays' => [$dayOfWeek],
     * 'is_import' => 1
     * @param $clazz
     * @param $student
     * @return bool
     */
    private function assignStudentToClass($request, $clazz, $classInfo, $student, $schedules)
    {
        if (empty($student) || empty($clazz)) {
            return "Học sinh không có dữ liệu";
        }
        $s = Student::where('accounting_id', '=', $student['code'])->first();
        if (empty($s)) return "Học sinh không có hợp đồng";

        $contracts = self::getContractInfo($clazz['branch_id'], $clazz['id'], $s->id);
//        dd($schedules, $student);
        $schedule = u::get($schedules, $student['start_date']);
        if (empty($schedule)) {
            return "Ngày bắt đầu học của học sinh không có trong lịch học của lớp";
        }

        foreach ($contracts as $contract) {
            $contract->{"class_date"} = "{$schedule->cjrn_id}~{$schedule->cjrn_classdate}";
        }
        if (empty($contracts)) {
            if (Contract::where([['status', '=', 6], ['class_id', '=', $clazz['id']], ['student_id', '=', $s->id]])->exists()) {
                return "Học sinh đã được xếp vào lớp";
            }
            return "Không tìm thấy hợp đồng xếp lớp";
        }
        $data = [
            'branch' => $clazz['branch_id'],
            'class' => $clazz['id'],
            'class_info' => $classInfo,
            'contracts' => $contracts,
            'programs' => '',
            'semester' => $clazz['semester_id'],
            'users_data' => $request->users_data,
            'is_import' => 1
        ];
        $req = new Request();
        $req->replace($data);
        $er = new EnrolmentsController();
        $r = $er->addContracts($req);
        $r = json_decode($r);
        return $r->code === 200 ? null : "Insert error";
    }

    /**
     *            'branch_code' => isset($branchCode) ? $branchCode : "",
     * 'semester_code' => isset($semesterCode) ? $semesterCode : "",
     * 'class_name' => $className,
     * 'school_shift' => $schoolShift,
     * 'room_name' => isset($roomName) ? $roomName : '',
     * 'start_date' => $start_date,
     * 'teacher_name' => isset($teacherName) ? $teacherName : null,
     * 'day_of_week' => isset($day_of_week) ? $day_of_week : null
     *
     * branch_id: 1
     * class_name: "UCREA - Tiếng Anh - Test 1"
     * cm_id: 6
     * max_students: 15
     * num_session: 48
     * program_id: 1
     * room_id: 2
     * semester_id: 1
     * shift_id: 5
     * start_date: "2019-06-14"
     * status: 0
     * teacher_id: 1
     * weekdays: [2]
     * 0: 2
     * @param $clazz
     */
    private function generateClass($request, $clazz, $branches, $semesters, $rooms, $programs, $shifts)
    {
        $branchCode = strtolower(str_replace(' ', '', u::utf8convert($clazz['branch_code'])));
        $branch = isset($branches[$branchCode]) ? $branches[$branchCode] : null;
        $programName = $clazz['semester_code'] . "- Tiếng Việt" . u::get($branch, 'branch_id');
        $programName = strtolower(str_replace(' ', '', u::utf8convert($programName)));
        $program = isset($programs[$programName]) ? $programs[$programName] : null;
        $roomName = strtolower(str_replace(' ', '', u::utf8convert($clazz['room_name'])));
        $room = isset($rooms["$roomName$branch->branch_id"]) ? $rooms["$roomName$branch->branch_id"] : null;
        $semesterName = strtolower(str_replace(' ', '', u::utf8convert($clazz['semester_code'])));
        $semester = isset($semesters[$semesterName]) ? $semesters[$semesterName] : null;
        $schoolShift = isset($clazz['school_shift_name']) ? $clazz['school_shift_name'] : null;
        $shift = strtolower(str_replace(' ', '', u::utf8convert($schoolShift)));
        $shift = $shifts[$shift];
        $teacher = Teacher::where('ins_name', '=', u::get($clazz, 'teacher_name', ''))->first();
        $dayOfWeek = (int)u::get($clazz, 'day_of_week', 0);
        $classInsert = [
            'branch_id' => isset($branch->branch_id) ? $branch->branch_id : null,
            'class_name' => $clazz['class_name'],
            'cm_id' => null,
            'max_students' => 16,
            'num_session' => 48,
            'program_id' => isset($program->id) ? $program->id : null,
            'room_id' => isset($room->id) ? $room->id : null,
            'semester_id' => isset($semester->id) ? $semester->id : null,
            'shift_id' => isset($shift->id) ? $shift->id : null,
            'start_date' => isset($clazz['start_date']) ? $clazz['start_date'] : null,
            'status' => 0,
            'teacher_id' => u::get($teacher, 'id'),
            'weekdays' => [$dayOfWeek],
            'is_import' => 1

        ];
        $dbClass = Classes::where('cls_name', '=', $clazz['class_name'])->first();
        if (empty($dbClass)) {
            Input::replace($classInsert);
            $sessionController = new SessionsController();
            $classId = $sessionController->store($request);
            $classInsert['id'] = $classId;
        } else {
            $classInsert['id'] = $dbClass->id;
            $classInsert['message'] = "Lớp đã tồn tại trong hệ thống";
        }
        return $classInsert;
    }

    /**
     * @param $student ['stt', 'update_date', 'code', 'name', 'birthday', 'year_of_birthday',
     * "mother_name", "mother_phone", "mother_email", "tuition_month", "number_of_session", "cs_name", "status",
     * 'so_buoi_da_hoc', "so_buoi_da_bao_luu", "so_buoi_con_lại", "start_date", "end_date"]
     * @return array
     */
    private function validateStudent($student)
    {
        if (empty($student)) {
            return ["Học sinh không có dữ liệu"];
        }

        $res = [];
        if (empty($student['code'])) {
            $res[] = "Chưa nhập mã học viên";
        } else {
            if (!Student::where('accounting_id', '=', $student['code'])->exists()) {
                $res[] = "Học viên này không có trong hệ thống";
            }
        }

        if (empty($student['start_date'])) {
            $res[] = "Chưa nhập ngày bắt đầu học hoặc ngày bắt đầu học không đúng định dạng (dd/mm/yyyy)";
        }

        foreach ($student as $key => $value) {
            if ($key < 3) continue;
            if (!in_array(strtolower(str_replace(' ', '', u::utf8convert($value))), ['', 'x', 'nghi', 'muon'])) {
                $res[] = "Điểm danh ngày $key không đúng định dạng";
            }
        }

        return $res;
    }


    private function getStudentsInExcelData($data)
    {
        $res = [];
        if (count($data) < 8) {
            return $res;
        }
        $fields = ['stt', 'code', "start_date"];

        $headers = $data[8];
        foreach ($headers as $index => $header) {
            if ($index < 3) continue;
            $h = trim($header);
            if (!empty($h)) {
                $date = date_create_from_format("d/m/Y", $h);
                $f = date("Y-m-d", $date->getTimestamp());
                $fields[] = $f;
            }
        }

        foreach ($data as $index => $item) {
            if ($index < 9) continue;

            $code = $item[2];
            if (empty($code)) {
                continue;
            }

            $student = [];
            foreach ($item as $key => $value) {
                $field = isset($fields[$key]) ? $fields[$key] : null;
                if ($field) {
                    $student[$field] = trim($value);
                }
            }
            $date = date_create_from_format("d/m/Y", $student['start_date']);
            if ($date) {
                $start_date = date("Y-m-d", $date->getTimestamp());
                $student['start_date'] = $start_date;
            } else {
                $student['start_date'] = null;
            }
            $res[] = $student;
        }
        return $res;
    }

    /**
     * 'branch_code' => $branchCode,
     * 'semester_code' => $semesterCode,
     * 'class_name' => $className,
     * 'school_shift' => $schoolShift,
     * 'room_name' => $roomName,
     * 'start_date' => $start_date,
     * 'teacher_name' => $teacherName,
     * 'day_of_week' => $day_of_week
     * @param $clazz
     * @return array
     */
    private function validateClass($clazz, $branches, $semesters, $rooms, $shifts)
    {
        if (!$clazz) {
            return ["Không có thông tin lớp học"];
        }
        $res = [];

        $branchCode = strtolower(str_replace(' ', '', u::utf8convert($clazz['branch_code'])));
        if (empty($branchCode)) {
            $res[] = "Chưa có thông tin chung tâm (Tên lớp phải có thông tin trung tâm ví dụ Lớp A (Mã trung tâm, ...)";
        } else {
            $branch = $branches[$branchCode] ?: null;
            if (empty($branch)) {
                $res[] = "Mã trung tâm không tồn tại trong hệ thống";
            }
        }

        $semesterCode = strtolower(str_replace(' ', '', u::utf8convert($clazz['semester_code'])));
        if (empty($semesterCode)) {
            $res[] = "Chưa nhập học kỳ";
        } else {
            $semester = isset($semesters[$semesterCode]) ? $semesters[$semesterCode] : null;
            if (empty($semester)) {
                $res[] = "Học kỳ không tồn tại trong hệ thống";
            }
        }

        $roomName = strtolower(str_replace(' ', '', u::utf8convert(isset($clazz['room_name']) ? $clazz['room_name'] : null)));
        if (empty($roomName)) {
            $res[] = "Chưa nhập thông tin phòng học";
        } else {
            $branchId = isset($branch->branch_id) ? $branch->branch_id : -1;
            if (!isset($rooms["$roomName$branchId"])) {
                $res[] = "Phòng học không có trong hệ thống";
            }
        }

        $teacherName = trim($clazz['teacher_name']);
        if (empty($teacherName)) {
            $res[] = "Chưa nhập tên giáo viên";
        } else {
            if (!Teacher::where('ins_name', '=', $teacherName)->exists()) {
                $res[] = "Giáo viên không có trong hệ thống";
            }
        }

        $shift = isset($clazz['school_shift']) ? $clazz['school_shift'] : null;
        $shiftName = isset($clazz['school_shift_name']) ? $clazz['school_shift_name'] : null;
        if (empty($shift) || empty($shiftName)) {
            $res[] = "Chưa nhập ca học";
        } else {
            $shift = strtolower(str_replace(' ', '', u::utf8convert($shiftName)));
            if (!isset($shifts[$shift])) {
                $res[] = "Ca học không có trong hệ thống";
            }
        }

        $startDate = u::get($clazz, 'start_date');
        if (empty($startDate)) {
            $res[] = "Chưa nhập ngày bắt đầu";
        }

        $dayOfWeek = u::get($clazz, 'day_of_week');
        if (!isset($dayOfWeek)) {
            $res[] = "Không tìm thấy ngày học trong tuần";
        }

        return $res;

    }

    /**
     * @param $data
     * @return array
     */
    private function getClassInExcelData($data)
    {
        $className = $data[0][0];
//        // Mã trung tâm và kỳ học
//        $re = '/\(([a-zA-Z]{1,10}[0-9]{1,10})\.\s{0,100}([a-zA-Z]{1,100})/m';
//        preg_match_all($re, $className, $matches, PREG_SET_ORDER, 0);
//        if (!empty($matches)) {
//            $branchCode = $matches[0][1];
//            $semesterCode = $matches[0][2];
//        } else {
//            $semesterCode = null;
//        }
        // Lớp học
        $re = '/(^Lớp:)(.{1,500})/m';
        preg_match_all($re, $className, $matches, PREG_SET_ORDER, 0);
        if (!empty($matches)) {
            $className = $matches[0][2];
        }
        // Trung tâm
        $branchName = $data[6][0];
        $re = '/(^Trung tâm:)(.{1,500})/m';
        preg_match_all($re, $branchName, $matches, PREG_SET_ORDER, 0);
        if (!empty($matches)) {
            $branchCode = $matches[0][2];
        }
        // Học kỳ
        $hocky = $data[7][0];
        $re = '/(^Học kỳ:)(.{1,500})/m';
        preg_match_all($re, $hocky, $matches, PREG_SET_ORDER, 0);
        if (!empty($matches)) {
            $semesterCode = $matches[0][2];
        }
        // Ca học
        $schoolShift = $data[1][0];
        $re = '/(\d+).(\d+)\s{0,10}.\s{0,10}(\d+).(\d+)/m';
        preg_match($re, $schoolShift, $matches, PREG_OFFSET_CAPTURE, 0);
        if (count($matches) >= 5) {
            $times = [$matches[1][0], $matches[2][0], $matches[3][0], $matches[4][0]];
            $schoolShift = ['start_time' => "{$times[0]}:{$times[1]}", 'end_time' => "{$times[2]}:{$times[3]}"];
            $semesterName = strtolower(str_replace(' ', '', u::utf8convert($semesterCode ?: "")));
            $schoolShiftName = $semesterName === "ucrea" ? "u" : "ib";
            $st = (int)$times[0];
            $et = (int)$times[2];
            $schoolShiftName = "$schoolShiftName: {$st}:{$times[1]} - {$et}:{$times[3]}";
        }
        // Phòng học
        $re = '/(^Phòng học:)(.{1,500})/m';
        preg_match_all($re, $data[2][0], $matches, PREG_SET_ORDER, 0);
        if (!empty($matches)) {
            $roomName = trim($matches[0][2]);
        }
        // Ngày khai giảng
        $re = '/(^Ngày khai giảng:)(.{1,500})/m';
        preg_match_all($re, $data[3][0], $matches, PREG_SET_ORDER, 0);
        $date = null;
        if (!empty($matches)) {
            $start_date = trim($matches[0][2]);
            $date = date_create_from_format("d/m/Y", $start_date);
        }
        if ($date) {
            $start_date = date("Y-m-d", $date->getTimestamp());
        } else {
            $start_date = null;
        }

        // Ngày học trong tuần
        $re = '/(^Ngày học trong tuần:)(.{1,500})/m';
        preg_match_all($re, $data[4][0], $matches, PREG_SET_ORDER, 0);
        $day_of_week = !empty($matches) ? $matches[0][2] : null;

        // Teacher Name
        $re = '/(^Giáo viên chính:)(.{1,500})/m';
        preg_match_all($re, $data[5][0], $matches, PREG_SET_ORDER, 0);
        if (!empty($matches)) {
            $teacherName = trim($matches[0][2]);
        }
        return [
            'branch_code' => isset($branchCode) ? trim($branchCode) : "",
            'semester_code' => isset($semesterCode) ? trim($semesterCode) : "",
            'class_name' => trim($className),
            'school_shift' => $schoolShift,
            'school_shift_name' => isset($schoolShiftName) ? trim($schoolShiftName) : null,
            'room_name' => isset($roomName) ? trim($roomName) : '',
            'start_date' => $start_date,
            'teacher_name' => isset($teacherName) ? trim($teacherName) : null,
            'day_of_week' => isset($day_of_week) ? $day_of_week : null
        ];
    }

    public function importContracts(Request $request, $execImport = false)
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(-1);
        $userData = $request->users_data;
        if (empty($userData) || (int)$userData->role_id !== 999999999) {
            return response()->json(['data' => ['message' => 'Permission denied !']]);
        }
        $this->validate($request, ['file' => 'required']);
        if (!$request->hasFile('file')) {
            return response()->json(['data' => ['message' => 'File is empty !']]);
        }

        $extension = $request->file->extension();
        if (!in_array($extension, $this->_extension)) {
            return response()->json(['data' => ['message' => 'Format file something wrong !']]);
        }

        $filename = 'contract-' . time() . '.' . $extension;
        $request->file->storeAs($this->_path, $filename);
        $file = FOLDER . DS . 'doc/other/' . $filename;
        $reader = new ExcelRead();
//        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $dataXslx = $sheet->toArray();

        unset($dataXslx[0]);
        $dataCorrects = [];
        $dataInCorrects = [];
        $fields = [
            'stt', 'branch_id', 'student_accounting_id', 'accounting_id', 'ec_code', 'type', 'only_give_tuition_fee_transfer', 'product',
            'program_label', 'tuition_fee', 'register_date',
            'coupon', 'discount_value', 'voucher_value', 'other_coupon_value', 'description', 'expected_class', 'start_date', 'note',
        ];
        $products = u::convertArrayToObject(Product::query()->get(), function ($product) {
            return strtolower(str_replace(' ', '', u::utf8convert($product->name)));
        });
        $tuitionFees = TuitionFee::query()->get();
        $branches = u::convertArrayToObject(self::getAllBranchWithEC(), function ($branch) {
            return strtolower(str_replace(' ', '', u::utf8convert($branch->accounting_id)));
        });

        $countRechargeOfContract = [];
        foreach ($dataXslx as $row) {
            $contract = [];
            foreach ($fields as $key => $field) {
                $contract[$field] = isset($row[$key]) ? $row[$key] : null;
            }
            $countRechargeOfContract[$contract['student_accounting_id']][] = [
                'accounting_id' => $contract['accounting_id'],
                'ts' => strtotime($contract['register_date']),
                'tuition_fee' => $contract['tuition_fee']
            ];
            $validate = self::validateImportContract($contract, $products, $tuitionFees, $branches);
            if (empty($validate)) {
                $dataCorrects[] = $contract;
            } else {
                $contract['message'] = $validate;
                $dataInCorrects[] = $contract;
            }
        }

        $countRechargeOfContract = self::calculateCountRecharge($countRechargeOfContract);
        if ($execImport) {
            foreach ($dataCorrects as $index => $item) {
                $student = DB::table('students')->where('accounting_id', '=', $item['student_accounting_id'])->first();
//                $termStudent = DB::table('term_student_user')->where('student_id', '=', $student->id)->first();
                $ecUser = DB::table('users')->where('accounting_id', '=', $item['ec_code'])->first();
                $student->ec_id = isset($ecUser) && isset($ecUser->id) ? $ecUser->id : null;
                $student->ec_name = isset($ecUser) ? $ecUser->full_name : null;

                $branchName = strtolower(str_replace(' ', '', u::utf8convert($item['branch_id'])));
                $branch = isset($branches[$branchName]) ? $branches[$branchName] : null;
                $student->branch_name = $branch->branch_name;
                $student->branch_id =  $branch->branch_id;
                $student->cm_id = '';
                $student->om_id = '';
                $student->region_id = '';
                $student->ec_leader_id = '';
                $student->ceo_branch_id = '';
                $student->ceo_region_id = '';
                $student->student_id = $student->id;
                $key = strtolower(str_replace(' ', '', u::utf8convert($item['product'])));
                $product = isset($products[$key]) ? $products[$key] : null;
                if (!isset($product) || !isset($product->id)) {
                    $dataCorrects[$index]['message'] = 'Insert error (do không tìm thấy sản phẩm)';
                    continue;
                }
                $tuitionFee = self::getTuitionFeeByCodeAndDate($item['tuition_fee'], $item['register_date'], $product->id, $tuitionFees);
                $classDays = u::getDayOfWeek($item['start_date']);
                $holidays = u::getPublicHolidays();
                $realSession = u::getRealSessions($tuitionFee->session, $classDays, $holidays, $item['start_date']);
                if (isset($realSession->end_date)) {
                    $endDate = $realSession->end_date;
                } else {
                    $endDate = null;
//                    dd($index, $item, $tuitionFee, $classDays, $holidays, $item['start_date']);
                }
                $tuitionFeePrice = $tuitionFee->price;
                $tuitionFeeDiscount = $tuitionFee->discount;
                $tuitionFeeReceivable = $tuitionFee->receivable;
                $lowDiscount = $tuitionFeeDiscount / 100000;
                $lowPrice = $tuitionFeePrice / 100000;
                $percentage = ($lowDiscount / $lowPrice) * 100;
                $totalVoucherOther = $item['voucher_value'] + $item['other_coupon_value'];
                $sibling = 0; // Số tiền giảm trừ (anh chị em sau chiết khấu)
                $totalPointSibling = $sibling;
                $discountedAmount = $tuitionFeeReceivable;
                $mustCharge = $discountedAmount - $totalVoucherOther;
                $calculatedDiscount = $totalPointSibling > 0 ? (int)($tuitionFeeReceivable - $totalPointSibling) : $tuitionFeeReceivable;
                $totalDiscount = $totalPointSibling + $totalVoucherOther;
                $mustChargeAmount = $discountedAmount - $totalVoucherOther - (int)$item['discount_value'];
                $discountPercentage = u::pct($percentage, 1);
                if ($mustChargeAmount > 0) {
                    $discountPercentageName = u::pct($discountPercentage, 1) . '%';
                    $billInfo = "Chiết khấu ({$discountPercentageName}%): $tuitionFeeDiscount<br/>------------------------------<br/>Giá Thực Thu: $discountedAmount<br/><br/><br/>";
                    $detail = "Chiết khấu ($discountPercentageName): $discountedAmount\n------------------------------\nGiá Thực Thu: $discountedAmount\n\n\n";
                    if ($item['discount_value']) {
                        $point = $item['discount_value'];
                        $billInfo .= "Tiền mã chiết khấu: $point<br/>";
                        $detail .= "Tiền giam trừ theo mã chiết khấu: $point\n";
                    }
                    if ($sibling) {
                        $billInfo .= "Anh Chị Em: $sibling<br/>";
                        $detail .= "Giảm trừ Anh Chị Em: $sibling\n";
                    }
                    // if (parseInt(the_point.n) || parseInt(the_sibling.n)) {
                    //   this.data.bill_info += `Chiết khấu (${u.pct(this.data.discount_percentage, 1)}%): ${this.format(parseInt((this.cache.tuition_fee.tuition_fee_price - this.data.discounted_amount) / 1000 * 1000))}<br/>------------------------------<br/>Giá Thực Thu: ${this.format(parseInt(this.data.discounted_amount))}<br/><br/><br/>`
                    //   this.data.detail += `Chiết khấu (${u.pct(this.data.discount_percentage, 1)}%): ${this.format(parseInt((this.cache.tuition_fee.tuition_fee_price - this.data.discounted_amount) / 1000 * 1000))}\n------------------------------\nGiá Thực Thu: ${this.format(parseInt(this.data.discounted_amount))}\n\n\n`
                    // }
                    if ($item['voucher_value']) {
                        $voucher = $item['voucher_value'];
                        $billInfo .= "Voucher: $voucher<br/>";
                        $detail .= "Khấu trừ Voucher: $voucher\n";
                    }
                    $other = $item['other_coupon_value'];
                    if ($other) {
                        $billInfo .= "Khấu trừ khác: $other<br/><br/><br/>";
                        $detail .= "Khấu trừ Khác: $other\n";
                    }
                    $t = $totalVoucherOther + $totalPointSibling + (int)$item['discount_value'];
                    if ($t) {
                        $detail .= "------------------------------\nTổng khấu trừ: $t\n";
                        $detail .= "\nSố tiền còn lại phải đóng:\n $discountedAmount - $t\n------------------------------\n = $mustCharge}";
                    }
                }
                $contract = (Object)[];
                $contract->branch_id = $branch->branch_id;
                $contract->product = $product ? $product->id : 0;
                $contract->coupon = $item['coupon'];
                $contract->start_date = $item['start_date'];
                $contract->end_date = $endDate;
                $contract->expected_class = $item['expected_class'];
                $contract->tuition_fee = $tuitionFee->id;
                $contract->pass_trial = 0;
                $contract->sessions = $tuitionFee->session;
                $contract->continue = 0;
                $contract->point = $item['discount_value'];
                $contract->voucher = $item['voucher_value'];
                $contract->other = $item['other_coupon_value'];
                $contract->percentage = $percentage;
                $contract->discounted = $tuitionFeeDiscount;
                $contract->must_charge = $mustCharge;
                $contract->other_amount = 0;
                $contract->total_discount = $totalDiscount;
                $contract->new_price_amount = $tuitionFeeReceivable;
                $contract->discounted_amount = $discountedAmount;
                $contract->must_charge_amount = $mustChargeAmount;
                $contract->discount_percentage = u::pct($percentage, 1);
                $contract->total_point_sibling = $totalPointSibling;
                $contract->calculated_discount = $calculatedDiscount;
                $contract->total_voucher_other = $totalVoucherOther;
                $contract->note = $item['note'];
                $contract->price = $tuitionFeePrice;
                $contract->detail = isset($detail) ? $detail : null;
                $contract->bill_info = isset($billInfo) ? $billInfo : null;
                $contract->receive = $item['only_give_tuition_fee_transfer'];
                $contract->program = $item['program_label'];
                $contract->payload = $tuitionFee->type;
                $contract->sibling = $sibling;
                $countRechargeOfContractKey = "{$item['accounting_id']}{$item['tuition_fee']}";
                $contract->count_recharge = isset($countRechargeOfContract[$countRechargeOfContractKey]) ? $countRechargeOfContract[$countRechargeOfContractKey] ?: 0 : 0;
                $contract->customer_type = $item['type'] == 1 && $contract->count_recharge > 0 ? 7 : $item['type'];
                Input::replace(['contract' => $contract, 'student' => $student, 'is_import' => 1, 'accounting_id' => $item['accounting_id']]);
                $cl = new ContractsController();
                $r = $cl->store($request);
                $r = json_decode($r);
                $dataCorrects[$index]['message'] = $r->code === 200 ? "Insert success" : 'Insert error';
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

    private function calculateCountRecharge($students)
    {
        $res = [];

        foreach ($students as $student => $items) {
            usort($items, function ($a, $b) {
                return $a['ts'] > $b['ts'];
            });
            foreach ($items as $countRecharge => $item) {
                $key = "{$item['accounting_id']}{$item['tuition_fee']}";
                $res[$key] = $countRecharge;
            }
        }
        return $res;
    }

    private function validateImportContract($contract, $products, $tuitionFees, $branches)
    {
        $res = [];
        if (empty($contract)) {
            $res[] = "Chưa nhập contract";
            return $res;
        }
        $branchName = strtolower(str_replace(' ', '', u::utf8convert(u::get($contract, 'branch_id'))));
        $branch = isset($branches[$branchName]) ? $branches[$branchName] : null;
        if (empty($branchName)) {
            $res[] = 'Chưa điền mã trung tâm';
        } else {
            if (empty($branch)) {
                $res[] = "Trung tâm không tồn tại trong hệ thống";
            }
        }

        $ec = u::get($contract, 'ec_code');
        if (empty($ec)) {
            $res[] = "Chưa điền mã EC";
        }
//        else {
//            $userHrmIds = isset($branch->user_accounting_id) ? $branch->user_accounting_id : '';
//            $userHrmIds = explode(',', $userHrmIds);
//            if (!in_array($ec, $userHrmIds)) {
//                $res[] = "Mã EC không có ở trung tâm $branchName";
//            }
//        }

        if (empty(u::get($contract, 'student_accounting_id'))) {
            $res[] = "Chưa nhập mã học viên (mã khách hàng)";
        } else if (!Student::where('accounting_id', '=', $contract['student_accounting_id'])->exists()) {
            $res[] = "Mã học viên (Mã khách hàng) không có trong hệ thống";
        }

        if (empty(u::get($contract, 'accounting_id'))) {
            $res[] = "Chưa nhập mã hợp đồng";
        }

        if (!isset($contract['type'])) {
            $res[] = "Chưa nhập loại khách hàng";
        } else if (!in_array((int)$contract['type'], [0, 1])) {
            $res [] = "Nhập loại khách hàng không đúng";
        }
        if (!isset($contract['only_give_tuition_fee_transfer'])) {
            $res[] = "Chưa nhập chỉ nhận chuyển phí";
        } else if (!in_array((int)$contract['only_give_tuition_fee_transfer'], [0, 1])) {
            $res[] = "Nhập chuyển phí không đúng";
        }
        if (!isset($contract['product'])) {
            $res[] = "Chưa nhập sản phẩm";
        } else {
            $key = strtolower(str_replace(' ', '', u::utf8convert($contract['product'])));
            $product = isset($products[$key]) ? $products[$key] : null;
            if (!isset($product)) {
                $res[] = "Sản phẩm không tồn tại trong hệ thống";
            }
        }
//        if (empty($contract['program_label'])) {
//            $res[] = "Chưa nhập chương trình học";
//        }

        if (!isset($contract['register_date'])) {
            $res[] = "Chưa điền ngày đăng ký";
        } else {
            $d = explode("-", $contract['start_date']);
            if (count($d) !== 3 || $d[2] < 1 || $d[2] > 31 || $d[1] < 1 || $d[1] > 12 || $d[0] < 1970 || $d[0] > 3000) {
                $res[] = "Ngày đăng ký không đúng định dạng";

            }
        }

        if (!isset($contract['tuition_fee'])) {
            $res[] = "Chưa nhập gói phí";
        } else {
            $productId = isset($product) && isset($product->id) ? $product->id : null;
            if (empty($productId)) {
                $res[] = "Không tìm thấy gói phí do không tìm thấy sản phẩm trong hệ thống";
            } else {
                $tuitionFee = self::getTuitionFeeByCodeAndDate($contract['tuition_fee'], $contract['register_date'], $productId, $tuitionFees);
                if (empty($tuitionFee)) {
                    $res[] = "Gói phí không có trong hệ thống";
                }
            }
        }

        if (empty($contract['start_date'])) {
            $res[] = "Chưa điền ngày dự kiến học";
        } else {
            $d = explode("-", $contract['start_date']);
            if (count($d) !== 3 || $d[2] < 1 || $d[2] > 31 || $d[1] < 1 || $d[1] > 12 || $d[0] < 1970 || $d[0] > 3000) {
                $res[] = "Ngày dự kiến học không đúng định dạng";
            }
        }
        return $res;
    }

    private function getTuitionFeeByCodeAndDate($code, $pDate, $productId, $tuitionFees)
    {
        if (empty($tuitionFees) || empty($code) || empty($pDate)) {
            return null;
        }
        $date = strtotime($pDate);
        foreach ($tuitionFees as $tuitionFee) {
            if ($tuitionFee->accounting_id !== $code || $tuitionFee->product_id !== $productId) {
                continue;
            }
            $available_date = strtotime($tuitionFee->available_date);
            $expired_date = strtotime($tuitionFee->expired_date);
            if ($date >= $available_date && $date <= $expired_date) {
                return $tuitionFee;
            }
        }
        return null;
    }

    public function importStudentV2(Request $request, $execImport = false)
    {
        $userData = $request->users_data;
        if (empty($userData) || (int)$userData->role_id !== 999999999) {
            return response()->json(['data' => ['message' => 'Permission denied !']]);
        }
        $this->validate($request, ['file' => 'required']);
        if (!$request->hasFile('file')) {
            return response()->json(['data' => ['message' => 'File is empty !']]);
        }

        $extension = $request->file->extension();
        if (!in_array($extension, $this->_extension)) {
            return response()->json(['data' => ['message' => 'Format file something wrong !']]);
        }

        $filename = 'student-' . time() . '.' . $extension;
        $request->file->storeAs($this->_path, $filename);
        $file = FOLDER . DS . 'doc/other/' . $filename;
        $reader = new ExcelRead();
//        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $dataXslx = $sheet->toArray();


        $branchs = u::convertArrayToObject(self::getAllBranchWithEC(), function ($branch) {
            return strtolower(str_replace(' ', '', u::utf8convert($branch->accounting_id)));
        });
        $provinces = self::getAllProvinceDistricts();
        unset($dataXslx[0]);
        $dataCorrects = [];
        $dataInCorrects = [];
        $dataInserts = [];
        $fields = [
            'stt', 'branch_name', 'ec_code', 'accounting_id', 'name', 'birthday', 'gender', 'phone', 'nick_name', 'province',
            'district', 'address', 'student_type', 'facebook', 'school_name', 'school_grade', 'from_source', 'note',
            'sibling_code', 'parent1_name', 'parent1_phone', 'parent1_email', 'parent1_birthday', 'parent1_job',
            'parent2_name', 'parent2_phone', 'parent2_email', 'parent2_birthday', 'parent2_job'
        ];
        foreach ($dataXslx as $row) {
            $item = [];
            foreach ($fields as $key => $field) {
                $item[$field] = isset($row[$key]) ? $row[$key] : null;
            }
            $user = DB::table('users')->where(['accounting_id' => $item['ec_code']])->first();
            if (empty($user)) {
                $branchId = $branchs[strtolower(str_replace(' ', '', u::utf8convert($item['branch_name'])))]->branch_id;
                $term = u::first("select * from term_user_branch where branch_id=$branchId and role_id in (69, 676767)");
                if (!empty($term)) {
                    $user = DB::table('users')->where(['id' => $term->user_id])->first();
                    $item['ec_leader'] = $user->full_name . " ($user->hrm_id)";
                }
            }
            $validate = self::validateImportStudent($item, $branchs, $provinces);
            if (empty($validate)) {
                if ($execImport) {
                    $provinceName = $item['province'];
                    $provinceName = isset(Provinces[$provinceName]) ? Provinces[$provinceName] : null;
                    if (!empty($provinceName)) {
                        $provinceName = strtolower(str_replace(' ', '', u::utf8convert($provinceName)));
                        $province = isset($provinces[$provinceName]) ? $provinces[$provinceName] : null;
                        $provinceId = isset($province['id']) ? $province['id'] : null;
                        if (!empty($province)) {
                            $districtCode = isset($item['district']) ? $item['district'] : null;
                            $districtCode = strtolower(str_replace(' ', '', u::utf8convert($districtCode)));
                            $districtName = isset(Districts[$districtCode]) ? Districts[$districtCode] : null;
                            $districtName = strtolower(str_replace(' ', '', u::utf8convert($districtName)));
                            $districtId = isset($province['districts'][$districtName]['id']) ? $province['districts'][$districtName]['id'] : null;
                        }
                    }
                    $branchId = $branchs[strtolower(str_replace(' ', '', u::utf8convert($item['branch_name'])))]->branch_id;
                    $dataInserts[] = [
                        'name' => $item['name'],
                        'nick' => $item['nick_name'],
                        'accounting_id' => $item['accounting_id'],
                        'gender' => $item['gender'] == 1 ? 'M' : 'F',
                        'type' => $item['student_type'],
                        'date_of_birth' => $item['birthday'],
                        'gud_mobile1' => $item['parent1_phone'],
                        'gud_name1' => $item['parent1_name'],
                        'gud_email1' => $item['parent1_email'],
                        'gud_mobile2' => $item['parent2_phone'],
                        'gud_name2' => $item['parent2_name'],
                        'gud_email2' => $item['parent2_email'],
                        'address' => $item['address'],
                        'province_id' => isset($provinceId) ? $provinceId : null,
                        'district_id' => isset($districtId) ? $districtId : null,
                        'school' => $item['school_name'],
                        'school_grade' => $item['school_grade'],
                        'created_at' => now(),
                        'creator_id' => null,
                        'note' => $item['note'],
                        'branch_id' => $branchId,
                        'phone' => $item['phone'],
                        'facebook' => $item['facebook'],
                        'sibling_id' => $item['sibling_code'],
                        'source' => $item['from_source']
                    ];
                }
                $dataCorrects[] = $item;
            } else {
                $item['message'] = $validate;
                $dataInCorrects[] = $item;
            }
        }

        if ($execImport) {
            foreach ($dataInserts as $key => $student) {
                if (Student::where("accounting_id", "=", $student['accounting_id'])->exists()) {
                    $dataCorrects[$key]['message'] = 'Học sinh đã tồn tại trong hệ thống';
                    continue;
                }
                $id = DB::table('students')->insertGetId($student);
                $cms_id = '2' . str_pad((string)$id, 7, '0', STR_PAD_LEFT);
                $crm_id = "CMS$cms_id";
                $cms_id = (int)$cms_id;
                $r = u::query("UPDATE students SET cms_id = '$cms_id', crm_id = '$crm_id' WHERE id = $id");
                $branch = $branchs[strtolower(str_replace(' ', '', u::utf8convert($dataCorrects[$key]['branch_name'])))];
                $branchId = $branch->branch_id;
                $user = DB::table('users')->where(['accounting_id' => $dataCorrects[$key]['ec_code']])->first();
                $ecId = isset($user->id) ? $user->id : null;
                if (empty($ecId)) {
                    $term = u::first("select * from term_user_branch where branch_id=$branch->branch_id and role_id in (69, 676767)");
                    $ecId = $term->user_id;
                }
                $r = DB::table('term_student_user')->insert(
                    [
                        'student_id' => $id,
                        'ec_id' => $ecId,
                        'status' => 1,
                        'branch_id' => $branchId,
                        'region_id' => 0,
                        'zone_id' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );
                $dataCorrects[$key]['message'] = $r === true ? "Insert success" : 'Insert error';
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

    private function getAllProvinceDistricts()
    {
        $query = "SELECT p.name as province_name, p.id as province_id, d.id as district_id,
                         d.name as district_name FROM provinces p 
                    LEFT JOIN districts d ON d.province_id = p.id";
        $res = u::query($query);
        $data = [];
        foreach ($res as $item) {
            $provinceName = strtolower(str_replace(' ', '', u::utf8convert($item->province_name)));
            $districtName = strtolower(str_replace(' ', '', u::utf8convert($item->district_name)));
            $province = isset($data[$provinceName]) ? $data[$provinceName] : null;
            if (empty($province)) {
                $data[$provinceName] = [
                    'name' => $item->province_name,
                    'id' => $item->province_id,
                    'districts' => [
                        $districtName => [
                            'id' => $item->district_id,
                            'name' => $item->district_name
                        ]
                    ]
                ];
            } else {
                $data[$provinceName]['districts'][$districtName] = [
                    'id' => $item->district_id,
                    'name' => $item->district_name
                ];
            }
        }
        return $data;
    }

    private function getAllBranchWithEC()
    {
        $query = "SELECT b.id as branch_id, b.accounting_id, b.name as branch_name, GROUP_CONCAT(u.accounting_id) as user_accounting_id, tub.role_id FROM branches b 
                    LEFT JOIN term_user_branch tub ON b.id = tub.branch_id AND tub.role_id = 68
                    LEFT JOIN users u ON u.id = tub.user_id
                    GROUP BY branch_name";
        $branches = u::query($query);
        return $branches;
    }

    /**
     * @param $student = [
     * 'stt', 'branch_name', 'ec_code', 'accounting_id', 'name', 'birthday', 'gender', 'phone', 'nick_name', 'province',
     * 'district', 'address', 'student_type', 'facebook', 'school_name', 'school_grade', 'from_source', 'note',
     * 'sibling_code', 'parent1_name', 'parent1_phone', 'parent1_email', 'parent1_birthday', 'parent1_job',
     * 'parent2_name', 'parent2_phone', 'parent2_email', 'parent2_birthday', 'parent2_job'
     * ]
     * @param $branches
     * @param $provinces
     * @return array
     *
     */
    private function validateImportStudent($student, $branches, $provinces)
    {
        $res = [];
        if (empty($student)) {
            $res[] = "Không có dữ liệu";
            return $res;
        }
        $branchName = strtolower(str_replace(' ', '', u::utf8convert($student['branch_name'])));
        $branch = isset($branches[$branchName]) ? $branches[$branchName] : null;
        if (empty($branchName)) {
            $res[] = 'Chưa điền tên trung tâm';
        } else {
            if (empty($branch)) {
                $res[] = "Trung tâm không tồn tại trong hệ thống";
            }
        }

        $ec = $student['ec_code'];
        $userAccountingIds = isset($branch->user_accounting_id) ? $branch->user_accounting_id : '';
        $userAccountingIds = explode(',', $userAccountingIds);
        if ((empty($ec) || empty($userAccountingIds) || !in_array($ec, $userAccountingIds)) && isset($branch->user_accounting_id)) {
            $user = u::first("select * from term_user_branch where branch_id=$branch->branch_id and role_id in (69, 676767)");
            if (empty($user))
                $res[] = "Mã EC và EC leader và Phó giám đốc trung tâm không có ở trung tâm $branchName";
        }

        if (empty($student['accounting_id'])) {
            $res[] = "Chưa điền mã học viên (Mã khách hàng)";
        }

        if (empty($student['name'])) {
            $res[] = "Chưa điền tên học sinh";
        }

        $birthday = $student['birthday'];
        if (empty($birthday)) {
            $res[] = "Chưa điền ngày sinh của học sinh";
        } else {
            $d = explode("-", $birthday);
            if (count($d) !== 3 || $d[2] < 1 || $d[2] > 31 || $d[1] < 1 || $d[1] > 12 || $d[0] < 1900 || $d[0] > 2019) {
                $res[] = "Ngày sinh của học sinh không đúng";
            }
        }

        $gender = $student['gender'];
        if (!isset($gender)) {
            $res[] = "Chưa điền giới tính";
        } else {
            if (!in_array((int)$gender, [0, 1])) {
                $res[] = "Nhập giới tính không đúng";
            }
        }

        $province = $student['province'];
        if (!empty($province)) {
            $provinceName = isset(Provinces[$province]) ? Provinces[$province] : null;
            $province = strtolower(str_replace(' ', '', u::utf8convert($provinceName)));
            $province = isset($provinces[$province]) ? $provinces[$province] : null;
            if (empty($province)) {
                $res[] = "Tỉnh/Thành phố không tồn tại trong hệ thống ({$student['province']})";
            }
        }

        $district = $student['district'];
        if (!empty($district)) {
            $districtCode = strtolower(str_replace(' ', '', u::utf8convert($district)));
            $districtName = isset(Districts[$districtCode]) ? Districts[$districtCode] : null;
            $districtName = strtolower(str_replace(' ', '', u::utf8convert($districtName)));
            $district = isset($province['districts'][$districtName]) ? $province['districts'][$districtName] : null;
            if (empty($district)) {
                $res[] = "Quận huyện không tồn tại trong hệ thống ($districtCode)";
            }
        }

//        $address = $student[11];
//        if (empty($address)) {
//            $res[] = "Chưa nhập địa chỉ";
//        }

//        $school = $student[14];
//        if (empty($school)) {
//            $res[] = "Chưa nhập trường học";
//        }

//        $fromSource = $student[16];
//        if (empty($fromSource)) {
//            $res[] = "Chưa nhập từ nguồn";
//        }

//        $parentName = $student['parent1_name'];
//        if (empty($parentName)) {
//            $res[] = "Chưa nhập tên phụ huynh 1";
//        }
//
//        $parentPhone = $student['parent1_phone'];
//        if (empty($parentPhone)) {
//            $res[] = "Chưa nhập số điện thoại phụ huynh 1";
//        }

        $parentEmail = $student['parent1_email'];
//        if (empty($parentEmail)) {
//            $res[] = "Chưa nhập địa chỉ email phụ huynh 1";
//        }
        if (!empty($parentEmail) && !filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
            $res[] = "Địa chỉ email phụ huynh 1 không đúng";
        }
        $parentEmail = $student['parent2_email'];
        if (!empty($parentEmail) && !filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
            $res[] = "Địa chỉ email phụ huynh 2 không đúng";
        }

        return $res;
    }

    // Import Students from Excel file
    public function importStudent(Request $request)
    {
        $userData = $request->users_data;
        $message = '';
        if ($userData && (int)$userData->role_id === 999999999) {

            if ($request->hasFile('file')) {
                $extension = $request->file->extension();
                $filename = '';
                // dd($extension);
                if (in_array($extension, $this->_extension)) {
                    $filename = 'student-' . time() . '.' . $extension;
                    // echo $path;die;
                    $store = $request->file->storeAs($this->_path, $filename);
                    $error = 0;
                    $message = 'Upload Success ';
                } else {
                    $error = 1;
                    $message = 'Upload Fail ';
                }
                $file = FOLDER . DS . 'doc\\other\\' . $filename;
                $reader = new ExcelRead();
                // $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file);
                $sheet = $spreadsheet->setActiveSheetIndex(0);
                $sheet->getStyle('D')->getNumberFormat()->setFormatCode('Y-m-d');
                $dataXslx = $sheet->toArray();
                unset($dataXslx[0]);
                unset($dataXslx[2]);

                $datafilter = array_filter($dataXslx, function ($arr) {
                    return $arr[0] && $arr[1];
                });
                // dd($datafilter);
                $dataInsert = array_map(function ($a) {
                    $return = [
                        'accounting_id' => $a[0],
                        'name' => vn_uppercase(strtoupper($a[1])),
                        'phone' => trim($a[2]),
                        'date_of_birth' => date('Y-m-d', strtotime($a[3])),
                        'address' => trim($a[4]),
                        'gender' => (int)$a[5] ? 'M' : 'F',
                        'gud_name1' => ($a[8] != null) ? trim($a[8]) : 'Mẹ bé ' . $a[1],
                        'gud_mobile1' => trim($a[9]),
                        'gud_email1' => trim($a[10]),
                        'gud_name2' => ($a[11] != null) ? trim($a[11]) : 'Bố bé ' . $a[1],
                        'gud_mobile2' => trim($a[12]),
                        'gud_email2' => trim($a[13]),
                        'note' => 'Dữ liệu import từ phần mềm kế toán.'
                    ];
                    return $return;
                }, $datafilter);
                // dd($dataInsert);
                try {
                    if (!empty($dataInsert)) {
                        DB::table('students')->insert($dataInsert);
                        $message .= 'Dữ liệu đã được import';
                    } else {
                        $message = 'Import thất bại, Dữ liệu không đúng.';
                    }
                } catch (Exception $e) {
                }
            }
        } else {
            $message = 'Permission denied !';
        }
        $response = [
            'data' => [
                'message' => $message
            ]
        ];
        return response()->json($response);
    }
    /*************************************************************************************/
    // Import user from Excel file
    public function importUser(Request $request)
    {
        $userData = $request->users_data;
        $message = '';
        if ($userData && (int)$userData->role_id === 999999999) {
            $this->validate($request, ['file' => 'required']);

            if ($request->hasFile('file')) {
                $extension = $request->file->extension();
                $filename = '';
                // dd($extension);
                if (in_array($extension, $this->_extension)) {
                    $filename = 'user-' . time() . '.' . $extension;
                    $store = $request->file->storeAs($this->_path, $filename);
                    $error = 0;
                    $message = 'Upload Success ';
                } else {
                    $error = 1;
                    $message = 'Upload Fail ';
                }
                $file = FOLDER . DS . 'doc\\other\\' . $filename;
                $reader = new ExcelRead();
                // $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file);

                if ($spreadsheet) {
                    // Get users exits
                    $users = DB::table('users')->select('hrm_id', 'email')->get()->toArray();
                    $a_hrm = array_column($users, 'hrm_id');
                    $a_email = array_column($users, 'email');
                    $total_sheet = $spreadsheet->getSheetCount();
                    for ($i = 1; $i < $total_sheet; $i++) {
                        $sheet = $spreadsheet->setActiveSheetIndex($i);
                        $dataXslx = $sheet->toArray();
                        unset($dataXslx[0]);
                        // Filter data
                        $datafilter = array_filter($dataXslx, function ($arr) use ($a_hrm, $a_email) {
                            return $arr[2] && $arr[4] && $arr[8] && !in_array(trim($arr[2]), $a_hrm) && !in_array(trim($arr[8]), $a_email);
                        });
                        // map roleId
                        $dataMapRole = [];
                        if (!empty($datafilter)) {
                            $dataMapRole = array_map(function ($ar) {
                                $ar[5] = gen_string_to_role($ar[5]);
                                return $ar;
                            }, $datafilter);
                        }
                        // dd($dataMapRole);
                        try {
                            if (!empty($dataMapRole)) {
                                // tạm thời như này theo file
                                switch ($i) {
                                    case 1:
                                        $branch_id = 1;
                                        break;
                                    case 2:
                                        $branch_id = 3;
                                        break;
                                    case 3:
                                        $branch_id = 10;
                                        break;
                                    case 4:
                                        $branch_id = 4;
                                        break;
                                    case 5:
                                        $branch_id = 5;
                                        break;
                                    case 6:
                                        $branch_id = 7;
                                        break;
                                    default:
                                        $branch_id = 0;
                                        break;
                                }
                                foreach ($dataMapRole as $insert) {
                                    $idUserCreated = DB::table('users')->insertGetID($this->attribute($insert));
                                    $dataInsertTUB = [
                                        'user_id' => $idUserCreated,
                                        'branch_id' => $branch_id,
                                        'role_id' => $insert[5],
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                        'status' => 1
                                    ];
                                    DB::table('term_user_branch')->insert($dataInsertTUB);
                                    if ($insert[5] == 36) {
                                        $dataInsertTeacher = [
                                            'user_id' => $idUserCreated,
                                            'ins_name' => trim($insert[4]),
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                            'meta_data' => json_encode(array('user_id' => $idUserCreated)),
                                        ];
                                        $idTeacherCreated = DB::table('teachers')->insertGetID($dataInsertTeacher);
                                        $dataInsertTTB = [
                                            'teacher_id' => $idTeacherCreated,
                                            'branch_id' => $branch_id,
                                            'is_head_teacher' => 0,
                                            'status' => 1,
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ];
                                        DB::table('term_teacher_branch')->insertGetID($dataInsertTTB);
                                    }
                                }
                                $message .= 'Dữ liệu đã được import';
                            }
                        } catch (Exception $e) {
                        }
                    }

                }
            }
        } else {
            $message = 'Permission denied !';
        }
        $response = [
            'data' => [
                'message' => $message
            ]
        ];
        return response()->json($response);
    }

    /*
	** Format user data to insert
    */
    private function attribute(array $insert)
    {
        return [
            'hrm_id' => trim($insert[2]),
            // 'accounting_id' => $a[2],
            'full_name' => trim($insert[4]),
            // 'phone' => '',
            'email' => trim(strtolower($insert[8])),
            'username' => trim(strtoupper(preg_replace('/@.*?$/', '', $insert[8]))),
            'password' => bcrypt('@12345678'),
            'superior_id' => trim($insert[3]) ? trim($insert[3]) : '',
            'status' => 1
        ];
    }

    public function execImportPayment(Request $request)
    {
        set_time_limit(-1);
        return $this->importPayment($request, true);
    }

    public function importPayment(Request $request, $execImport = false)
    {
        set_time_limit(-1);
        $userData = $request->users_data;
        if (empty($userData) || (int)$userData->role_id !== 999999999) {
            return response()->json(['data' => ['message' => 'Permission denied !']]);
        }
        $this->validate($request, ['file' => 'required']);
        if (!$request->hasFile('file')) {
            return response()->json(['data' => ['message' => 'File is empty !']]);
        }

        $extension = $request->file->extension();
        if (!in_array($extension, $this->_extension)) {
            return response()->json(['data' => ['message' => 'Format file something wrong !']]);
        }

        $filename = 'payment-' . time() . '.' . $extension;
        $request->file->storeAs($this->_path, $filename);
        $file = FOLDER . DS . 'doc/other/' . $filename;
        $reader = new ExcelRead();
//        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $dataXslx = $sheet->toArray();

        unset($dataXslx[0]);
        $dataCorrects = [];
        $dataInCorrects = [];
        $fields = ['stt', 'contract_code', 'tuition_code', 'payment_code', 'creator', 'amount', 'charge_date', 'note'];

        foreach ($dataXslx as $row) {
            $payment = [];
            foreach ($fields as $key => $field) {
                $payment[$field] = isset($row[$key]) ? $row[$key] : null;
            }
            $validate = self::validateImportPayment($payment);
            if (empty($validate)) {
                $dataCorrects[] = $payment;
            } else {
                $payment['message'] = $validate;
                $dataInCorrects[] = $payment;
            }
        }

        if ($execImport) {
            $cyberApi = new CyberAPI();
            foreach ($dataCorrects as $index => $item) {
                $tuitionFees = TuitionFee::where('accounting_id', '=', $item['tuition_code'])->get();
                $tuitionFeeIds = [];
                foreach ($tuitionFees as $t) {
                    $tuitionFeeIds[] = $t->id;
                }

                Input::replace([
                    'contract_code' => $item['contract_code'],
                    'creator' => $item['creator'],
                    'payment_code' => $item['payment_code'],
                    'amount' => $item['amount'],
                    'charge_date' => $item['charge_date'],
                    'note' => $item['note'],
                    'tuition_fee_ids' => $tuitionFeeIds,
                    'is_import' => 1
                ]);
                $r = $cyberApi->createPayment($request);
                $r = json_decode($r);
                $dataCorrects[$index]['message'] = $r->code === 200 ? "Insert success" : 'Insert error';
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

    /**
     * @param $payment = ['stt', 'contract_code', 'payment_code' ,'creator', 'amount', 'charge_date', 'note'];
     * @return array
     */
    private function validateImportPayment($payment)
    {
        $res = [];
        if (empty($payment)) {
            $res[] = "Chưa nhập payment";
            return $res;
        }

        if (empty($payment['contract_code'])) {
            $res[] = "Chưa nhập số chứng từ nhập học";
        } else if (!Contract::where('accounting_id', '=', $payment['contract_code'])->exists()) {
            $res[] = "Số chứng từ nhập học không có trong hệ thống";
        }


        if (empty($payment['payment_code'])) {
            $res[] = "Chưa nhập mã phiếu thu";
        }

        if (!isset($payment['creator'])) {
            $res[] = "Chưa nhập mã nhân viên thu ngân";
        } else if (!User::where('accounting_id', '=', $payment['creator'])->exists()) {
            $res[] = "Nhân viên thu ngân không có trong hệ thống";
        }

        if (!isset($payment['amount'])) {
            $res[] = "Chưa nhập số tiền thu";
        }

        if (empty($payment['charge_date'])) {
            $res[] = "Chưa điền ngày thu";
        } else {
            $d = explode("-", $payment['charge_date']);
            if (count($d) !== 3 || $d[2] < 1 || $d[2] > 31 || $d[1] < 1 || $d[1] > 12 || $d[0] < 1970 || $d[0] > 3000) {
                $res[] = "Ngày thu không đúng định dạng";
            }
        }

        if (empty($payment['tuition_code'])) {
            $res[] = "Chưa nhập mã gói phí";
        } else {
            $tuitionFees = TuitionFee::where('accounting_id', '=', $payment['tuition_code'])->get();
            if (empty($tuitionFees)) {
                $res[] = "Không tìm thấy gói phí";
            } else {
                $ids = [];
                foreach ($tuitionFees as $t) {
                    $ids[] = $t->id;
                }
                $str = implode(',', $ids);
                $c = u::first("SELECT * FROM contracts WHERE accounting_id = '{$payment['contract_code']}' AND tuition_fee_id IN ($str)");
                if (empty($c)) {
                    $res[] = "Gói phí không có trong hợp đồng";
                }
            }
        }

        return $res;
    }

    public function execImportDiscountCodes(Request $request)
    {
        set_time_limit(-1);
        return $this->importDiscountCodes($request, true);
    }

    public function importDiscountCodes(Request $request, $execImport = false)
    {
        set_time_limit(-1);
        $userData = $request->users_data;
        if (empty($userData) || (int)$userData->role_id !== 999999999) {
            return response()->json(['data' => ['message' => 'Permission denied !']]);
        }
        $this->validate($request, ['file' => 'required']);
        if (!$request->hasFile('file')) {
            return response()->json(['data' => ['message' => 'File is empty !']]);
        }

        $extension = $request->file->extension();
        if (!in_array($extension, $this->_extension)) {
            return response()->json(['data' => ['message' => 'Format file something wrong !']]);
        }

        $filename = 'payment-' . time() . '.' . $extension;
        $request->file->storeAs($this->_path, $filename);
        $file = FOLDER . DS . 'doc/other/' . $filename;
        $reader = new ExcelRead();
//        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $dataXslx = $sheet->toArray();

        unset($dataXslx[0]);
        $dataCorrects = [];
        $dataInCorrects = [];
        $fields = ['stt', 'code', 'name', 'percent', 'start_date', 'end_date', 'price', 'discount'];

        foreach ($dataXslx as $row) {
            $discount = [];
            foreach ($fields as $key => $field) {
                $discount[$field] = isset($row[$key]) ? $row[$key] : null;
            }
            $validate = self::validateImportDiscountCode($discount);
            if (empty($validate)) {
                $dataCorrects[] = $discount;
            } else {
                $discount['message'] = $validate;
                $dataInCorrects[] = $discount;
            }
        }

        if ($execImport) {
            foreach ($dataCorrects as $index => $item) {
                $params = (Object)$item;
                $discountCode = new DiscountCode();
                $discountCode->code = $params->code;
                $discountCode->name = $params->name;
                $discountCode->percent = (int)$params->percent;
                $discountCode->start_date = $params->start_date;
                $discountCode->end_date = $params->end_date;
                $discountCode->creator = $userData->id;
                $discountCode->status = 1;
                $discountCode->price = $params->price;
                $discountCode->discount = $params->discount;
                try {
                    $res = $discountCode->save();
                    $dataCorrects[$index]['message'] = $res ? "Insert success" : 'Insert error';
                } catch (\Exception $exception) {
                    $dataCorrects[$index]['message'] = 'Insert error';
                }
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

    /**
     * @param $discount = ['stt', 'code', 'name' ,'percent', 'start_date', 'end_date', 'price', 'discount'];
     * @return array
     */
    private function validateImportDiscountCode($discount)
    {
        $res = [];
        if (empty($discount)) {
            $res[] = "Chưa nhập mã chiết khấu";
            return $res;
        }

        if (empty($discount['code'])) {
            $res[] = "Chưa nhập số chứng từ nhập học";
        } else if (DiscountCode::where('code', '=', $discount['code'])->exists()) {
            $res[] = "Mã chiết khấu đã có trong hệ thống";
        }


        if (empty($discount['name'])) {
            $res[] = "Chưa nhập tên mã chiết khấu";
        }

        if (!isset($discount['percent'])) {
            $res[] = "Chưa nhập tỷ lệ chiết khấu";
        } else if (!is_numeric($discount['percent'])) {
            $res[] = "Tỷ lệ chiết khấu nhập không đúng";
        }

        if (empty($discount['start_date'])) {
            $res = "Chưa điền ngày bắt đầu";
        } else {
            $d = explode("-", $discount['start_date']);
            if (count($d) !== 3 || $d[2] < 1 || $d[2] > 31 || $d[1] < 1 || $d[1] > 12 || $d[0] < 1970 || $d[0] > 3000) {
                $res[] = "Ngày bắt đầu không đúng định dạng";
            }
        }

        if (empty($discount['end_date'])) {
            $res = "Chưa điền ngày kết thúc";
        } else {
            $d = explode("-", $discount['end_date']);
            if (count($d) !== 3 || $d[2] < 1 || $d[2] > 31 || $d[1] < 1 || $d[1] > 12 || $d[0] < 1970 || $d[0] > 3000) {
                $res[] = "Ngày kết thúc không đúng định dạng";
            }
        }

        if (!isset($discount['price'])) {
            $res[] = "Chưa nhập giá gốc";
        } else if (!is_numeric($discount['percent'])) {
            $res[] = "Giá gốc nhập không đúng";
        }

        if (!isset($discount['discount'])) {
            $res[] = "Chưa nhập tiền chiết khấu";
        } else if (!is_numeric($discount['discount'])) {
            $res[] = "Tiền chiết khấu nhập không đúng";
        }

        return $res;
    }

}

const Provinces = [
    "01" => "Bắc Giang",
    "02" => "Bắc Kạn",
    "03" => "Bắc Ninh",
    "04" => "Cao Bằng",
    "05" => "Điện Biên",
    "06" => "Hà Giang",
    "07" => "Hà Nam",
    "08" => "Vĩnh Phúc",
    "09" => "Hải Dương",
    "10" => "Hải Phòng",
    "11" => "Hòa Bình",
    "12" => "Hưng Yên",
    "13" => "Lai Châu",
    "14" => "Lạng Sơn",
    "15" => "Lào Cai",
    "16" => "Nam Định",
    "17" => "Ninh Bình",
    "18" => "Phú Thọ",
    "19" => "Quảng Ninh",
    "20" => "Sơn La",
    "21" => "Thái Bình",
    "22" => "Thái Nguyên",
    "23" => "Tuyên Quang",
    "24" => "Hà Nội",
    "25" => "Yên Bái",
    "26" => "Bình Định",
    "27" => "Cần Thơ",
    "28" => "Đà Nẵng",
    "29" => "Đắk Lắk",
    "30" => "Hà Tĩnh",
    "31" => "Khánh Hòa",
    "32" => "Kon Tum",
    "33" => "Nghệ An",
    "34" => "Phú Yên",
    "35" => "Quảng Bình",
    "36" => "Quảng Nam",
    "37" => "Quảng Ngãi",
    "38" => "Quảng Trị",
    "39" => "Thanh Hóa",
    "40" => "Thừa Thiên - Huế",
    "41" => "An Giang",
    "42" => "Bà Rịa - Vũng Tàu",
    "43" => "Bạc Liêu",
    "44" => "Bến Tre",
    "45" => "Bình Dương",
    "46" => "Bình Phước",
    "47" => "Bình Thuận",
    "48" => "Cà Mau",
    "49" => "Đắk Nông",
    "50" => "Đồng Nai",
    "51" => "Đồng Tháp",
    "52" => "Gia Lai",
    "53" => "Hậu Giang",
    "54" => "Hồ Chí Minh",
    "55" => "Kiên Giang",
    "56" => "Lâm Đồng",
    "57" => "Long An",
    "58" => "Ninh Thuận",
    "59" => "Sóc Trăng",
    "60" => "Tây Ninh",
    "61" => "Tiền Giang",
    "62" => "Trà Vinh",
    "63" => "Vĩnh Long"
];

const Districts = [
    "01.01" => "Bắc Giang",
    "01.02" => "Yên Thế",
    "01.03" => "Tân Yên",
    "01.04" => "Lục Ngạn",
    "01.05" => "Hiệp Hòa",
    "01.06" => "Lạng Giang",
    "01.07" => "Sơn Động",
    "01.08" => "Lục Nam",
    "01.09" => "Việt Yên",
    "01.10" => "Yên Dũng",
    "02.01" => "Bắc Kạn",
    "02.02" => "Ba Bể",
    "02.03" => "Bạch Thông",
    "02.04" => "Chợ Đồn",
    "02.05" => "Chợ Mới",
    "02.06" => "Na Rì",
    "02.07" => "Ngân Sơn",
    "02.08" => "Pác Nặm",
    "03.01" => "Bắc Ninh",
    "03.02" => "Từ Sơn",
    "03.03" => "Gia Bình",
    "03.04" => "Lương Tài",
    "03.05" => "Quế Võ",
    "03.06" => "Thuận Thành",
    "03.07" => "Tiên Du",
    "03.08" => "Yên Phong",
    "04.01" => "Cao Bằng",
    "04.02" => "Bản Lạc",
    "04.03" => "Bảo Lâm",
    "04.04" => "Hạ Lang",
    "04.05" => "Hà Quảng",
    "04.06" => "Hòa An",
    "04.07" => "Nguyên Bình",
    "04.08" => "Phục Hòa",
    "04.09" => "Quảng Uyên",
    "04.10" => "Thạch An",
    "04.11" => "Thông Nông",
    "04.12" => "Trà Lĩnh",
    "04.13" => "Trùng Khánh",
    "05.01" => "Điện Biên Phủ",
    "05.02" => "Mường Lay",
    "05.03" => "Điện Biên",
    "05.04" => "Điện Biên Đông",
    "05.05" => "Mường Ảng",
    "05.06" => "Mường Chà",
    "05.07" => "Mường Nhé",
    "05.08" => "Tủa Chùa",
    "05.09" => "Tuần Giáo",
    "05.10" => "Nậm Pồ",
    "06.01" => "Bắc Mê",
    "06.02" => "Bắc Quang",
    "06.03" => "Đồng Văn",
    "06.04" => "Hoàng Su Phì",
    "06.05" => "Mèo Vạc",
    "06.06" => "Quản Bạ",
    "06.07" => "Quang Bình",
    "06.08" => "Vị Xuyên",
    "06.09" => "Xín Mần",
    "06.10" => "Yên Minh",
    "07.01" => "Phủ Lý",
    "07.02" => "Duy Tiên",
    "07.03" => "Kim Bảng",
    "07.04" => "Lý Nhân",
    "07.05" => "Thanh Liêm",
    "07.06" => "Bình Lục",
    "08.01" => "Vĩnh Yên",
    "08.02" => "Phúc Yên",
    "08.03" => "Lập Thạch",
    "08.04" => "Tam Dương",
    "08.05" => "Tam Đảo",
    "08.06" => "Bình Xuyên",
    "08.07" => "Yên Lạc",
    "08.08" => "Vĩnh Tường",
    "08.09" => "Sông Lô",
    "09.01" => "Hải Dương",
    "09.02" => "Chí Linh",
    "09.03" => "Nam Sách",
    "09.04" => "Kinh Môn",
    "09.05" => "Kim Thành",
    "09.06" => "Thanh Hà",
    "09.07" => "Cẩm Giàng",
    "09.08" => "Bình Giang",
    "09.09" => "Gia Lộc",
    "09.10" => "Tứ Kỳ",
    "09.11" => "Ninh Giang",
    "09.12" => "Thanh Miện",
    "10.01" => "Dương Kinh",
    "10.02" => "Đồ Sơn",
    "10.03" => "Hải An",
    "10.04" => "Hồng Bàng",
    "10.05" => "Kiến An",
    "10.06" => "Lê Chân",
    "10.07" => "Ngô Quyền",
    "10.08" => "An Dương",
    "10.09" => "An Lão",
    "10.10" => "Cát Hải",
    "10.11" => "Kiến Thụy",
    "10.12" => "Thủy Nguyên",
    "10.13" => "Tiên Lãng",
    "10.14" => "Vĩnh Bảo",
    "10.15" => "Bạch Long Vĩ",
    "11.01" => "Hòa Bình",
    "11.02" => "Lương Sơn",
    "11.03" => "Cao Phong",
    "11.04" => "Đà Bắc",
    "11.05" => "Kim Bôi",
    "11.06" => "Kỳ Sơn",
    "11.07" => "Lạc Sơn",
    "11.08" => "Lạc Thủy",
    "11.09" => "Mai Châu",
    "11.10" => "Tân Lạc",
    "11.11" => "Yên Thủy",
    "12.01" => "Hưng Yên",
    "12.02" => "Văn Lâm",
    "12.03" => "Văn Giang",
    "12.04" => "Yên Mỹ",
    "12.05" => "Mỹ Hào",
    "12.06" => "Ân Thi",
    "12.07" => "Khoái Châu",
    "12.08" => "Kim Động",
    "12.09" => "Tiên Lữ",
    "12.10" => "Phù Cừ",
    "13.01" => "Lai Châu",
    "13.02" => "Mường Tè",
    "13.03" => "Phong Thổ",
    "13.04" => "Sìn Hồ",
    "13.05" => "Tam Đường",
    "13.06" => "Than Uyên",
    "13.07" => "Tân Uyên",
    "13.08" => "Nậm Nhùn",
    "14.01" => "Lạng Sơn",
    "14.02" => "Bắc Sơn",
    "14.03" => "Bình Gia",
    "14.04" => "Cao Lộc",
    "14.05" => "Chi Lăng",
    "14.06" => "Đình Lập",
    "14.07" => "Hữu Lũng",
    "14.08" => "Lộc Bình",
    "14.09" => "Tràng Định",
    "14.10" => "Văn Lãng",
    "14.11" => "Văn Quan",
    "15.01" => "Lào Cai",
    "15.02" => "Bảo Thắng",
    "15.03" => "Bảo Yên",
    "15.04" => "Bát Xát",
    "15.05" => "Bắc Hà",
    "15.06" => "Mường Khương",
    "15.07" => "Sa Pa",
    "15.08" => "Si Ma Cai",
    "15.09" => "Văn Bàn",
    "16.01" => "Nam Định",
    "16.02" => "Giao Thủy",
    "16.03" => "Hải Hậu",
    "16.04" => "Mỹ Lộc",
    "16.05" => "Nam Trực",
    "16.06" => "Nghĩa Hưng",
    "16.07" => "Trực Ninh",
    "16.08" => "Vụ Bản",
    "16.09" => "Xuân Trường",
    "16.10" => "Ý Yên",
    "17.01" => "Gia Viễn",
    "17.02" => "Hoa Lư",
    "17.03" => "Kim Sơn",
    "17.04" => "Nho Quan",
    "17.05" => "Yên Khánh",
    "17.06" => "Yên Mô",
    "17.07" => "Ninh Bình",
    "17.08" => "Ninh Bình",
    "18.01" => "Việt Trì",
    "18.02" => "Phú Thọ",
    "18.03" => "Cẩm Khê",
    "18.04" => "Đoan Hùng",
    "18.05" => "Lâm Thao",
    "18.06" => "Phù Ninh",
    "18.07" => "Tam Nông",
    "18.08" => "Tân Sơn",
    "18.09" => "Thanh Ba",
    "18.10" => "Thanh Sơn",
    "18.11" => "Thanh Thủy",
    "18.12" => "Yên Lập",
    "18.13" => "Hạ Hòa",
    "19.01" => "Hạ Long",
    "19.02" => "Móng Cái",
    "19.03" => "Uông Bí",
    "19.04" => "Cẩm Phả",
    "19.05" => "Quảng Yên",
    "19.06" => "Đông Triều",
    "19.07" => "Vân Đồn",
    "19.08" => "Hoành Bồ",
    "19.09" => "Đầm Hà",
    "19.10" => "Cô Tô",
    "19.11" => "Tiên Yên",
    "19.12" => "Hải Hà",
    "19.13" => "Bình Liêu",
    "19.14" => "Ba Chẽ",
    "20.01" => "Sơn La",
    "20.02" => "Quỳnh Nhai",
    "20.03" => "Mường La",
    "20.04" => "Thuận Châu",
    "20.05" => "Phù Yên",
    "20.06" => "Bắc Yên",
    "20.07" => "Mai Sơn",
    "20.08" => "Sông Mã",
    "20.09" => "Yên Châu",
    "20.10" => "Mộc Châu",
    "20.11" => "Sốp Cộp",
    "20.12" => "Vân Hồ",
    "21.01" => "Thái Bình",
    "21.02" => "Đông Hưng",
    "21.03" => "Hưng Hà",
    "21.04" => "Kiến Xương",
    "21.05" => "Quỳnh Phụ",
    "21.06" => "Thái Thụy",
    "21.07" => "Tiền Hải",
    "21.08" => "Vũ Thư",
    "22.01" => "Thái Nguyên",
    "22.02" => "Sông Công",
    "22.03" => "Phổ Yên",
    "22.04" => "Đại Từ",
    "22.05" => "Định Hóa",
    "22.06" => "Đồng Hỷ",
    "22.07" => "Phú Bình",
    "22.08" => "Phú Lương",
    "22.09" => "Võ Nhai",
    "23.01" => "Tuyên Quang",
    "23.02" => "Chiêm Hóa",
    "23.03" => "Hàm Yên",
    "23.04" => "Lâm Bình",
    "23.05" => "Na Hang",
    "23.06" => "Sơn Dương",
    "23.07" => "Yên Sơn",
    "24.01" => "Ba Đình",
    "24.02" => "Từ Liêm",
    "24.03" => "Cầu Giấy",
    "24.04" => "Đống Đa",
    "24.05" => "Hà Đông",
    "24.06" => "Hai Bà Trưng",
    "24.07" => "Hoàn Kiếm",
    "24.08" => "Hoàng Mai",
    "24.09" => "Long Biên",
    "24.10" => "Từ Liêm",
    "24.11" => "Tây Hồ",
    "24.12" => "Thanh Xuân",
    "24.13" => "Sơn Tây",
    "24.14" => "Ba Vì",
    "24.15" => "Chương Mỹ",
    "24.16" => "Đan Phượng",
    "24.17" => "Đông Anh",
    "24.18" => "Gia Lâm",
    "24.19" => "Hoài Đức",
    "24.20" => "Mê Linh",
    "24.21" => "Mỹ Đức",
    "24.22" => "Phú Xuyên",
    "24.23" => "Phúc Thọ",
    "24.24" => "Quốc Oai",
    "24.25" => "Sóc Sơn",
    "24.26" => "Thạch Thất",
    "24.27" => "Thanh Oai",
    "24.28" => "Thanh Trì",
    "24.29" => "Thường Tín",
    "24.30" => "Ứng Hòa",
    "25.01" => "Yên Bái",
    "25.02" => "Nghĩa Lộ",
    "25.03" => "Lục Yên",
    "25.04" => "Mù Cang Chải",
    "25.05" => "Trạm Tấu",
    "25.06" => "Trấn Yên",
    "25.07" => "Văn Chấn",
    "25.08" => "Văn Yên",
    "25.09" => "Yên Bình",
    "26.01" => "Quy Nhơn",
    "26.02" => "An Nhơn",
    "26.03" => "Hoài Nhơn",
    "26.04" => "An Lão",
    "26.05" => "Hoài Ân",
    "26.06" => "Phù Mỹ",
    "26.07" => "Vĩnh Thạnh",
    "26.08" => "Tây Sơn",
    "26.09" => "Phù Cát",
    "26.10" => "Tuy Phước",
    "26.11" => "Vân Canh",
    "27.01" => "Ninh Kiều",
    "27.02" => "Bình Thủy",
    "27.03" => "Cái Răng",
    "27.04" => "Ô Môn",
    "27.05" => "Thốt Nốt",
    "27.06" => "Phong Điền",
    "27.07" => "Cờ Đỏ",
    "27.08" => "Thới Lai",
    "27.09" => "Vĩnh Thạnh",
    "28.01" => "Hải Châu",
    "28.02" => "Thanh Khê",
    "28.03" => "Sơn Trà",
    "28.04" => "Ngũ Hành Sơn",
    "28.05" => "Liên Chiểu",
    "28.06" => "Cẩm Lệ",
    "28.07" => "Hòa Vang",
    "28.08" => "Hoàng Sa",
    "29.01" => "Buôn Ma Thuột",
    "29.02" => "Buôn Hồ",
    "29.03" => "Ea Súp",
    "29.04" => "Krông Bông",
    "29.05" => "Krông Búk",
    "29.06" => "Krông Pắk",
    "29.07" => "Krông Năng",
    "29.08" => "Krông Ana",
    "29.09" => "M'Đrăk",
    "29.10" => "Lắk",
    "29.11" => "Ea Kar",
    "29.12" => "Ea H'leo",
    "29.13" => "Cư M'gar",
    "29.14" => "Cư Kuin",
    "29.15" => "Buôn Đôn",
    "30.01" => "Hà Tĩnh",
    "30.02" => "Hồng Lĩnh",
    "30.03" => "Kỳ Anh",
    "30.04" => "Cẩm Xuyên",
    "30.05" => "Can Lộc",
    "30.06" => "Đức Thọ",
    "30.07" => "Hương Khê",
    "30.08" => "Hương Sơn",
    "30.09" => "Kỳ Anh",
    "30.10" => "Nghi Xuân",
    "30.11" => "Thạch Hà",
    "30.12" => "Vũ Quang",
    "30.13" => "Lộc Hà",
    "31.01" => "Nha Trang",
    "31.02" => "Cam Ranh",
    "31.03" => "Ninh Hòa",
    "31.04" => "Vạn Ninh",
    "31.05" => "Diên Khánh",
    "31.06" => "Khánh Vĩnh",
    "31.07" => "Khánh Sơn",
    "31.08" => "Cam Lâm",
    "31.09" => "Trường Sa",
    "32.01" => "Kon Tum",
    "32.02" => "Đắk Glei",
    "32.03" => "Đắk Hà",
    "32.04" => "Đắk Tô",
    "32.05" => "Ia H'Drai",
    "32.06" => "Kon Plông",
    "32.07" => "Kon Rẫy",
    "32.08" => "Ngọc Hồi",
    "32.09" => "Sa Thầy",
    "32.10" => "Tu Mơ Rông",
    "33.01" => "Vinh",
    "33.02" => "Cửa Lò",
    "33.03" => "Hoàng Mai",
    "33.04" => "Thái Hòa",
    "33.05" => "Anh Sơn",
    "33.06" => "Con Cuông",
    "33.07" => "Diễn Châu",
    "33.08" => "Đô Lương",
    "33.09" => "Hưng Nguyên",
    "33.10" => "Quỳ Châu",
    "33.11" => "Kỳ Sơn",
    "33.12" => "Nam Đàn",
    "33.13" => "Nghi Lộc",
    "33.14" => "Nghĩa Đàn",
    "33.15" => "Quế Phong",
    "33.16" => "Quỳ Hợp",
    "33.17" => "Quỳnh Lưu",
    "33.18" => "Tân Kỳ",
    "33.19" => "Tương Dương",
    "33.20" => "Yên Thành",
    "34.01" => "Tuy Hòa",
    "34.02" => "Sông Cầu",
    "34.03" => "Đông Hòa",
    "34.04" => "Đồng Xuân",
    "34.05" => "Phú Hòa",
    "34.06" => "Sơn Hòa",
    "34.07" => "Sông Hinh",
    "34.08" => "Tuy An",
    "34.09" => "Tây Hòa",
    "35.01" => "Đồng Hới",
    "35.02" => "Ba Đồn",
    "35.03" => "Minh Hóa",
    "35.04" => "Tuyên Hóa",
    "35.05" => "Quảng Trạch",
    "35.06" => "Bố Trạch",
    "35.07" => "Quảng Ninh",
    "35.08" => "Lệ Thủy",
    "36.01" => "Tam Kỳ",
    "36.02" => "Hội An",
    "36.03" => "Điện Bàn",
    "36.04" => "Thăng Bình",
    "36.05" => "Nam Trà My",
    "36.06" => "Núi Thành",
    "36.07" => "Phước Sơn",
    "36.08" => "Tiên Phước",
    "36.09" => "Hiệp Đức",
    "36.10" => "Nông Sơn",
    "36.11" => "Đông Giang",
    "36.12" => "Nam Giang",
    "36.13" => "Đại Lộc",
    "36.14" => "Phú Ninh",
    "36.15" => "Tây Giang",
    "36.16" => "Duy Xuyên",
    "36.17" => "Quế Sơn",
    "36.18" => "Bắc Trà My",
    "37.01" => "Quảng Ngãi",
    "37.02" => "Ba Tơ",
    "37.03" => "Bình Sơn",
    "37.04" => "Đức Phổ",
    "37.05" => "Mộ Đức",
    "37.06" => "Lý Sơn",
    "37.07" => "Tư Nghĩa",
    "37.08" => "Trà Bồng",
    "37.09" => "Tây Trà",
    "37.10" => "Sơn Tịnh",
    "37.11" => "Sơn Tây",
    "37.12" => "Sơn Hà",
    "37.13" => "Nghĩa Hành",
    "38.01" => "Đông Hà",
    "38.02" => "Quảng Trị",
    "38.03" => "Cam Lộ",
    "38.04" => "Đa Krông",
    "38.05" => "Gio Linh",
    "38.06" => "Hải Lăng",
    "38.07" => "Hướng Hóa",
    "38.08" => "Triệu Phong",
    "38.09" => "Vĩnh Linh",
    "38.10" => "Cồn Cỏ",
    "39.01" => "Thanh Hóa",
    "39.02" => "Bỉm Sơn",
    "39.03" => "Sầm Sơn",
    "39.04" => "Bá Thước",
    "39.05" => "Cẩm Thủy",
    "39.06" => "Đông Sơn",
    "39.07" => "Hà Trung",
    "39.08" => "Hậu Lộc",
    "39.09" => "Hoằng Hóa",
    "39.10" => "Lang Chánh",
    "39.11" => "Mường Lát",
    "39.12" => "Nga Sơn",
    "39.13" => "Ngọc Lặc",
    "39.14" => "Như Thanh",
    "39.15" => "Như Xuân",
    "39.16" => "Nông Cống",
    "39.17" => "Quan Hóa",
    "39.18" => "Quan Sơn",
    "39.19" => "Quảng Xương",
    "39.20" => "Thạch Thành",
    "39.21" => "Thiệu Hóa",
    "39.22" => "Thọ Xuân",
    "39.23" => "Thường Xuân",
    "39.24" => "Tĩnh Gia",
    "39.25" => "Triệu Sơn",
    "39.26" => "Vĩnh Lộc",
    "39.27" => "Yên Định",
    "40.01" => "Huế",
    "40.02" => "Phú Vang",
    "40.03" => "Quảng Điền",
    "40.04" => "A Lưới",
    "40.05" => "Nam Đông",
    "40.06" => "Phong Điền",
    "40.07" => "Phú Lộc",
    "40.08" => "Hương Thủy",
    "40.09" => "Hương Trà",
    "41.01" => "Long Xuyên",
    "41.02" => "Châu Đốc",
    "41.03" => "Tân Châu",
    "41.04" => "An Phú",
    "41.05" => "Châu Phú",
    "41.06" => "Châu Thành",
    "41.07" => "Chợ Mới",
    "41.08" => "Phú Tân",
    "41.09" => "Thoại Sơn",
    "41.10" => "Tịnh Biên",
    "41.11" => "Tri Tôn",
    "42.01" => "Vũng Tàu",
    "42.02" => "Bà Rịa",
    "42.03" => "Long Điền",
    "42.04" => "Đất Đỏ",
    "42.05" => "Châu Đức",
    "42.06" => "Tân Thành",
    "42.07" => "Côn Đảo",
    "42.08" => "Xuyên Mộc",
    "43.01" => "Bạc Liêu",
    "43.02" => "Giá Rai",
    "43.03" => "Hồng Dân",
    "43.04" => "Hòa Bình",
    "43.05" => "Phước Long",
    "43.06" => "Vĩnh Lợi",
    "43.07" => "Đông Hải",
    "44.01" => "Bến Tre",
    "44.02" => "Ba Tri",
    "44.03" => "Bình Đại",
    "44.04" => "Châu Thành",
    "44.05" => "Giồng Trôm",
    "44.06" => "Mỏ Cày Bắc",
    "44.07" => "Mỏ Cày Nam",
    "44.08" => "Thạnh Phú",
    "45.01" => "Thủ Dầu Một",
    "45.02" => "Thuận An",
    "45.03" => "Dĩ An",
    "45.04" => "Tân Uyên",
    "45.05" => "Bến Cát",
    "45.06" => "Dầu Tiếng",
    "45.07" => "Phú Giáo",
    "45.08" => "Bắc Tân Uyên",
    "45.09" => "Bàu Bàng",
    "46.01" => "Đồng Xoài",
    "46.02" => "Bình Long",
    "46.03" => "Phước Long",
    "46.04" => "Bù Đăng",
    "46.05" => "Bù Đốp",
    "46.06" => "Bù Gia Mập",
    "46.07" => "Chơn Thành",
    "46.08" => "Đồng Phú",
    "46.09" => "Hớn Quản",
    "46.10" => "Lộc Ninh",
    "47.01" => "Phan Thiết",
    "47.02" => "La Gi",
    "47.03" => "Tuy Phong",
    "47.04" => "Bắc Bình",
    "47.05" => "Hàm Thuận Bắc",
    "47.06" => "Hàm Thuận Nam",
    "47.07" => "Tánh Linh",
    "47.08" => "Hàm Tân",
    "47.09" => "Đức Linh",
    "47.10" => "Phú Quý",
    "48.01" => "Cà Mau",
    "48.02" => "Đầm Dơi",
    "48.03" => "Ngọc Hiển",
    "48.04" => "Cái Nước",
    "48.05" => "Trần Văn Thời",
    "48.06" => "U Minh",
    "48.07" => "Thới Bình",
    "48.08" => "Năm Căn",
    "48.09" => "Phú Tân",
    "49.01" => "Gia Nghĩa",
    "49.02" => "Cư Jút",
    "49.03" => "Đắk Glong",
    "49.04" => "Đắk Mil",
    "49.05" => "Đắk R'Lấp",
    "49.06" => "Đắk Song",
    "49.07" => "Krông Nô",
    "49.08" => "Tuy Đức",
    "50.01" => "Biên Hòa",
    "50.02" => "Long Thành",
    "50.03" => "Nhơn Trạch",
    "50.04" => "Trảng Bom",
    "50.05" => "Thống Nhất",
    "50.06" => "Vĩnh Cửu",
    "50.07" => "Cẩm Mỹ",
    "50.08" => "Xuân Lộc",
    "50.09" => "Tân Phú",
    "50.10" => "Định Quán",
    "51.01" => "Cao Lãnh",
    "51.02" => "Sa Đéc",
    "51.03" => "Cao Lãnh",
    "51.04" => "Châu Thành",
    "51.05" => "Hồng Ngự",
    "51.06" => "Lai Vung",
    "51.07" => "Lấp Vò",
    "51.08" => "Tam Nông",
    "51.09" => "Tân Hồng",
    "51.10" => "Thanh Bình",
    "51.11" => "Tháp Mười",
    "52.01" => "Pleiku",
    "52.02" => "An Khê",
    "52.03" => "Ayun Pa",
    "52.04" => "Chư Păh",
    "52.05" => "Chư Prông",
    "52.06" => "Chư Sê",
    "52.07" => "Đắk Đoa",
    "52.08" => "Chư Pưh",
    "52.09" => "Phú Thiện",
    "52.10" => "Mang Yang",
    "52.11" => "Krông Pa",
    "52.12" => "Kông Chro",
    "52.13" => "K'Bang",
    "52.14" => "Ia Pa",
    "52.15" => "Ia Grai",
    "52.16" => "Đức Cơ",
    "52.17" => "Đak Pơ",
    "53.01" => "Vị Thanh",
    "53.02" => "Long Mỹ",
    "53.03" => "Ngã Bảy",
    "53.04" => "Châu Thành",
    "53.05" => "Châu Thành A",
    "53.06" => "Long Mỹ",
    "53.07" => "Phụng Hiệp",
    "53.08" => "Vị Thủy",
    "54.01" => "Quận 1",
    "54.02" => "Quận 12",
    "54.03" => "Thủ Đức",
    "54.04" => "Quận 9",
    "54.05" => "Gò Vấp",
    "54.06" => "Bình Thạnh",
    "54.07" => "Tân Bình",
    "54.08" => "Tân Phú",
    "54.09" => "Phú Nhuận",
    "54.10" => "Quận 2",
    "54.11" => "Quận 3",
    "54.12" => "Quận 10",
    "54.13" => "Quận 11",
    "54.14" => "Quận 4",
    "54.15" => "Quận 5",
    "54.16" => "Quận 6",
    "54.17" => "Quận 8",
    "54.18" => "Bình Tân",
    "54.19" => "Quận 7",
    "54.20" => "Củ Chi",
    "54.21" => "Hóc Môn",
    "54.22" => "Bình Chánh",
    "54.23" => "Nhà Bè",
    "54.24" => "Cần Giờ",
    "55.01" => "Rạch Giá",
    "55.02" => "Hà Tiên",
    "55.03" => "An Biên",
    "55.04" => "An Minh",
    "55.05" => "Châu Thành",
    "55.06" => "Giồng Riềng",
    "55.07" => "Giang Thành",
    "55.08" => "Gò Quao",
    "55.09" => "Hòn Đất",
    "55.10" => "U Minh Thượng",
    "55.11" => "Kiên Lương",
    "55.12" => "Tân Hiệp",
    "55.13" => "Vĩnh Thuận",
    "55.14" => "Kiên Hải",
    "55.15" => "Phú Quốc",
    "56.01" => "Đà Lạt",
    "56.02" => "Bảo Lộc",
    "56.03" => "Bảo Lâm",
    "56.04" => "Cát Tiên",
    "56.05" => "Di Linh",
    "56.06" => "Đam Rông",
    "56.07" => "Đạ Huoai",
    "56.08" => "Đạ Tẻh",
    "56.09" => "Đơn Dương",
    "56.10" => "Lạc Dương",
    "56.11" => "Lâm Hà",
    "56.12" => "Đức Trọng",
    "57.01" => "Tân An",
    "57.02" => "Kiến Tường",
    "57.03" => "Bến Lức",
    "57.04" => "Cần Đước",
    "57.05" => "Cần Giuộc",
    "57.06" => "Châu Thành",
    "57.07" => "Đức Huệ",
    "57.08" => "Mộc Hóa",
    "57.09" => "Tân Hưng",
    "57.10" => "Tân Thạnh",
    "57.11" => "Tân Trụ",
    "57.12" => "Thạnh Hóa",
    "57.13" => "Thủ Thừa",
    "57.14" => "Vĩnh Hưng",
    "57.15" => "Đức Hòa",
    "58.01" => "Phan Rang - Tháp Chàm",
    "58.02" => "Bác Ái",
    "58.03" => "Ninh Hải",
    "58.04" => "Ninh Phước",
    "58.05" => "Ninh Sơn",
    "58.06" => "Thuận Bắc",
    "58.07" => "Thuận Nam",
    "59.01" => "Sóc Trăng",
    "59.02" => "Vĩnh Châu",
    "59.03" => "Ngã Năm",
    "59.04" => "Long Phú",
    "59.05" => "Kế Sách",
    "59.06" => "Mỹ Tú",
    "59.07" => "Mỹ Xuyên",
    "59.08" => "Trần Đề",
    "59.09" => "Thạnh Trị",
    "59.10" => "Châu Thành",
    "59.11" => "Cù Lao Dung",
    "60.01" => "Tây Ninh",
    "60.02" => "Tân Biên",
    "60.03" => "Tân Châu",
    "60.04" => "Dương Minh Châu",
    "60.05" => "Châu Thành",
    "60.06" => "Hòa Thành",
    "60.07" => "Bến Cầu",
    "60.08" => "Gò Dầu",
    "60.09" => "Trảng Bàng",
    "61.01" => "Mỹ Tho",
    "61.02" => "Gò Công",
    "61.03" => "Cai Lậy",
    "61.04" => "Cái Bè",
    "61.05" => "Gò Công Đông",
    "61.06" => "Gò Công Tây",
    "61.07" => "Chợ Gạo",
    "61.08" => "Châu Thành",
    "61.09" => "Tân Phước",
    "61.10" => "Cai Lậy",
    "61.11" => "Tân Phú Đông",
    "62.01" => "Trà Vinh",
    "62.02" => "Duyên Hải",
    "62.03" => "Càng Long",
    "62.04" => "Tiểu Cần",
    "62.05" => "Châu Thành",
    "62.06" => "Cầu Ngang",
    "62.07" => "Trà Cú",
    "62.08" => "Duyên Hải",
    "63.01" => "Vĩnh Long",
    "63.02" => "Bình Minh",
    "63.03" => "Bình Tân",
    "63.04" => "Long Hồ",
    "63.05" => "Mang Thít",
    "63.06" => "Tam Bình",
    "63.07" => "Trà Ôn",
    "63.08" => "Vũng Liêm"
];
