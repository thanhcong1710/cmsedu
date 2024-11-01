<?php

namespace App\Http\Controllers;

use App\Models\Calculator;
use App\Models\CyberAPI;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\APICode;
use App\Models\Response;
use App\Models\Student;
use App\Providers\UtilityServiceProvider as u;
use Mockery\Exception;
use PhpOffice\PhpSpreadsheet\Exception as ExceptionSpreadSheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Redis;
use App\Models\Sms;
class ServicesController extends Controller
{

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public static function checkWithdrawStatus()
    {
        // self::withDrawLMS();
        DB::table('contracts')
            ->where('last_date', '<', NOW())
            ->andWhere('status', '=', 1)
            ->update([
                'status' => 0,
                'enrolment_withdraw_date' => NOW(),
                'enrolment_withdraw_reason' => 'Kết thúc thời gian học'
            ]);
        DB::table('contracts')
            ->where('last_date', '<', NOW())
            ->andWhere('status', '=', 1)
            ->update([
                'status' => 7,
                'withdraw_date' => NOW(),
                'withdraw_reason' => 'Kết thúc thời gian học'
            ]);
        // self::withDrawLMS();
        return response()->json("Update successfully!");
    }

    public static function withDrawLMS()
    {
        $query = "
            SELECT
                s.stu_id, e.cstd_id
            FROM
                enrolments as e
                LEFT JOIN students AS s ON e.student_id = s.id
            WHERE
                e.last_date <= CURDATE()
                AND e.status = 1
        ";
        $students = DB::select(DB::raw($query));
        // $lmsApiController = new LMSAPIController();
        $request = new Request();
        $request->api_url = 'modify_stu_cls_status';
        $request->api_method = 'POST';
        $request->api_header = '';
        foreach ($students as $student) {
            $da = [
                "command" => "stu_cls_status",
                "stu_id" => "$student->stu_id",
                "cstd_id" => "$student->cstd_id",
                "cstd_status" => "Withdrawal"
            ];

            $request->api_params = json_encode($da);

            // $lmsApiController->callAPI($request, 1);
        }
    }

    public static function demo()
    {
        //wrire something here
        echo "hello World";
    }

    public function checkEnrolByFinalLastDate()
    {
        $dataEnrols = DB::select(DB::raw("
            SELECT * FROM enrolments
            WHERE final_last_date = :today AND last_date != 0 AND final_last_date > last_date
        "), ['today' => date('Y-m-d')]);

        if (count($dataEnrols) == 0)
            return response()->json("No Enrolments!");

        try {
            foreach ($dataEnrols as $enrol) {
                $contract = DB::select(
                    DB::raw("
                        SELECT id,branch_id,real_sessions
                        FROM contracts WHERE student_id = :student_id ORDER BY id limit 1
                    "), ['student_id' => $enrol->student_id]
                );
                if ($contract) {
                    self::createEnrolments($contract[0], $enrol);
                }
            }
            return response()->json("Update successfully!");
        } catch (Exception $e) {
            return response()->json("Error!");
        }
    }

    public function createEnrolments($contract, $oldEnrol)
    {
        $classDay = Session::where('class_id', $oldEnrol->class_id)->select('class_day')->groupBy(['class_day'])->pluck('class_day')->toArray();
        $class_info = u::first("SELECT product_id FROM classes WHERE id = $oldEnrol->class_id");
        $product_id = $class_info->product_id;
        $holiDay = u::getPublicHolidays($oldEnrol->class_id, 0, $product_id);
        $startDate = u::calcNewStartDate($oldEnrol->last_date, $classDay, $holiDay);
        $endDate = u::getRealSessions($contract->real_sessions, $classDay, $holiDay, $startDate);
        $dataCreate = [
            'contract_id' => $contract->id,
            'student_id' => $oldEnrol->student_id,
            'class_id' => $oldEnrol->class_id,
            'start_date' => $startDate,
            'end_date' => $endDate->end_date,
            'created_at' => now(),
            'hash_key' => '',
            'status' => $oldEnrol->status,
            'real_sessions' => $contract->real_sessions,
            'last_date' => end($endDate->dates),
            'type' => $oldEnrol->type,
        ];
        return DB::table('enrolments')->insert($dataCreate);
    }

    public function storeVideo(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        $session = $request->users_data;
        $post = $request->input();
        if ($session && is_array($post) && count($post)) {
            $code = APICode::SUCCESS;
            $data = (object)['success' => true];
            $list = [];
            $date = date('Y-m-d');
            foreach ($post as $video) {
                if ($video['class_id']) {
                    $name = "Video - Book " . $video['book_id'] . " (" . count($video['student']) . " students " . $date . ") ";
                    $hash = md5($name . json_encode($video));
                    $list[] = "'$hash'";
                }
            }
            $check = u::query("SELECT hash FROM videos WHERE hash IN (" . implode(',', $list) . ")");
            $valid = [];
            $existed = [];
            if (count($check)) {
                foreach ($check as $ck) {
                    if (isset($ck->hash)) {
                        $existed[] = $ck->hash;
                    }
                }
            }
            if (count($existed)) {
                foreach ($post as $video) {
                    $name = "Video - Book " . $video['book_id'] . " (" . count($video['student']) . " students " . $date . ") ";
                    $h = md5($name . json_encode($video));
                    if (!in_array($h, $existed)) {
                        $valid[] = $video;
                    }
                }
            } else $valid = $post;
            if (count($valid)) {
                $query = "INSERT INTO videos (`video`, `thumbnail`, `url`, `source`, `class_id`, `book`, `students`, `created_at`, `created_by`, `updated_at`, `updated_by`, `hash`) VALUES ";
                $i = 0;
                foreach ($valid as $video) {
                    $i++;
                    $name = "Video - Book " . $video['book_id'] . " (" . count($video['student']) . " students " . $date . ") ";
                    $hash = md5($name . json_encode($video));
                    $query .= "('$name',";
                    $query .= "'" . $video['thumbnail'] . "',";
                    $query .= "'" . $video['link_youtube'] . "',";
                    $query .= "'Youtube',";
                    $query .= "'" . $video['class_id'] . "',";
                    $query .= "'" . $video['book_id'] . "',";
                    $query .= "'[" . implode(',', $video['student']) . "]',";
                    $query .= "NOW(),";
                    $query .= "'$session->id',";
                    $query .= "NOW(),";
                    $query .= "'$session->id',";
                    $query .= "'$hash')";
                    if ($i < count($valid)) {
                        $query .= ",";
                    }
                }
                u::query($query);
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function facebookNotification(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        $notify = $request->input();
        if ($notify) {
            $code = APICode::SUCCESS;
            $data = (object)['success' => true];
            $list = [];
            $date = date('Y-m-d');
            if ($notify['student_id']) {
                $name = "Notify - Student " . $notify['student_id'] . " (" . $notify['time'] . ")";
                $hash = md5($name . json_encode($notify));
            }
            $check = u::query("SELECT hash FROM log_notification WHERE hash = '$hash'");
            $valid = null;
            $existed = [];
            if (count($check)) {
                foreach ($check as $ck) {
                    if (isset($ck->hash)) {
                        $existed[] = $ck->hash;
                    }
                }
            }
            if (count($existed)) {
                $name = "Notify - Student " . $notify['student_id'] . " (" . $notify['time'] . ")";
                $h = md5($name . json_encode($notify));
                if (!in_array($h, $existed)) {
                    $valid = $notify;
                }
            } else $valid = $notify;
            if ($valid) {
                $notify = $valid;
                $query = "INSERT INTO log_notification (`name`, `type`, `group`, `status`, `student_id`, `class_id`, `book`, `content`, `code`, `timestamp`, `created_at`, `hash`) VALUES ";
                $name = "Notify - Student " . $notify['student_id'] . " (" . $notify['time'] . ")";
                $from = isset($notify['class_id']) ? 'Youtube' : 'Facebook';
                $type = isset($notify['class_id']) ? 2 : 1;
                $class = isset($notify['class_id']) ? $notify['class_id'] : '';
                $book = isset($notify['book_id']) ? $notify['book_id'] : '';
                $content = isset($notify['content']) ? $notify['content'] : '';
                $uid = isset($notify['uid']) ? $notify['uid'] : '';
                $hash = md5($name . json_encode($notify));
                $query .= "('$name',";
                $query .= "$type,";
                $query .= "'$from',";
                $query .= "1,";
                $query .= "'" . $notify['student_id'] . "',";
                $query .= "'$class',";
                $query .= "'$book',";
                $query .= "'$content',";
                $query .= "'$uid',";
                $query .= "'" . $notify['time'] . "',";
                $query .= "NOW(),";
                $query .= "'$hash')";
                u::query($query);
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function updateLearningTime(Request $request, $student_id=0, $branch_id=0)
    {
        $response = new Response();
        $key = "holidays_$branch_id";
        $holidays_data = Redis::get($key);
        if($holidays_data && !empty($holidays)){
            $holidays = json_decode($holidays_data);
        }else{
            $holidays = u::getPublicHolidays(0, $branch_id, 9999);
            Redis::set($key, json_encode($holidays));
        }

        if ($student_id && is_numeric($student_id)) {
            $query = "
                SELECT
                    id, start_date, end_date, product_id, IF(`type` > 2, real_sessions, total_sessions) AS sessions, enrolment_id
                FROM contracts
                WHERE student_id = $student_id AND branch_id = $branch_id AND status > 0
                ORDER BY count_recharge, id ASC
            ";
            $contracts = DB::select(DB::raw($query));
            if (!empty($contracts)) {
                $enrolment_ids = [];
                foreach ($contracts as $c) {
                    if ($c->enrolment_id) {
                        $enrolment_ids[] = $c->enrolment_id;
                    }
                }

                $enrolments = [];
                if (!empty($enrolment_ids)) {
                    $enrolment_ids_string = implode(',', $enrolment_ids);
                    $query = "
                        SELECT
                            c.id, c.id contract_id, c.start_date, c.enrolment_last_date, c.enrolment_real_sessions, MIN(s1.class_day) AS class_day_1, MAX(s2.class_day) AS class_day_2
                        FROM
                            contracts AS c
                            LEFT JOIN sessions as s1 ON c.class_id = s1.class_id
                            LEFT JOIN sessions as s2 ON c.class_id = s2.class_id
                        WHERE c.id IN ($enrolment_ids_string)
                    ";
                    $da = DB::select(DB::raw($query));
                    foreach ($da as $d) {
                        $enrolments[$d->contract_id] = $d;
                    }
                }

                $start = u::getPreviousDay($contracts[0]->start_date);
                $final_last_date = null;

                $query = "
                    SELECT start_date, end_date FROM reserves WHERE student_id = $student_id AND status = 1 AND end_date >= '$start'
                ";

                $reserves = DB::select(DB::raw($query));
                foreach ($holidays as &$holiday) {
                    $holiday = array_merge($holiday, $reserves);
                }

                unset($holiday);
                $class_days = [2, 5];

                foreach ($contracts as &$c) {
                    $cal = Calculator::where('student_id',$student_id)->where('contract_id',$c->id)->first();
                    if(!$cal){
                        $cal = new Calculator();
                        $cal->student_id = $student_id;
                        $cal->branch_id = $branch_id;
                        $cal->contract_id = $c->id;
                        $cal->created_at = date('Y-m-d H:i:s');
                    }

                    $cal->contract_old_start_date = $c->start_date;
                    $cal->contract_old_end_date = $c->end_date;

                    if (!u::isGreaterThan($c->start_date, $start)) {
                        $c->start_date = u::getNextDay($start);
                    }

                    $c->end_date = u::getRealSessions($c->sessions, $class_days, $holidays[$c->product_id], $c->start_date)->end_date;
                    $cal->contract_new_start_date = $c->start_date;
                    $cal->contract_new_end_date = $c->end_date;


                    if (isset($enrolments[$c->id])) {
                        $e = $enrolments[$c->id];
                        $cal->enrolment_id = $e->id;

                        $cal->enrol_old_start_date = $e->start_date;
                        $cal->enrol_old_last_date = $e->last_date;

                        $class_days = [
                            $e->class_day_1,
                            $e->class_day_2
                        ];

                        if (!u::isGreaterThan($e->start_date, $start)) {
                            $e->start_date = u::getRealSessions(1, $class_days, $holidays[$c->product_id], u::getNextDay($start))->end_date;
                        }

                        $e->last_date = u::getRealSessions($e->real_sessions, $class_days, $holidays[$c->product_id], $e->start_date)->end_date;
                        $enrolments[$c->id] = $e;
                        $cal->enrol_new_start_date = $e->start_date;
                        $cal->enrol_new_last_date = $e->last_date;

                        if (u::isGreaterThan($c->end_date, $e->last_date)) {
                            $start = $c->end_date;
                        } else {
                            $start = $e->last_date;
                        }
                    } else {
                        $final_last_date = $c->end_date;
                        $start = $c->end_date;
                    }

                    $cal->save();
                }
            }
        }

        return $response->formatResponse(APICode::SUCCESS, null);
    }

    public function reCalcLearningTime($branch_id)
    {
        $this->middleware('throttle:1000,1');
        ini_set('max_execution_time', 3000);
        $holidays = u::getPublicHolidays(0, $branch_id, 9999);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên học sinh');
        $sheet->setCellValue('C1', 'LMS ID');
        $sheet->setCellValue('D1', 'Contract');
        $sheet->setCellValue('E1', 'Enrolment');
        $sheet->setCellValue('F1', 'Ngày bắt đầu cũ');
        $sheet->setCellValue('G1', 'Ngày bắt đầu mới');
        $sheet->setCellValue('H1', 'Ngày kết thúc cũ');
        $sheet->setCellValue('I1', 'Ngày kết thúc mới');

        $students = DB::select(DB::raw("SELECT * FROM students WHERE branch_id = $branch_id LIMIT 10"));
        if (!empty($students)) {
            $rowIndex = 2;
            foreach ($students as $student) {
                $student_id = $student->id;
                $branch_id = $student->branch_id;
                $query = "
                        SELECT
                            id, start_date, end_date, product_id, IF(`type` > 2, real_sessions, total_sessions) AS sessions, enrolment_id
                        FROM contracts
                        WHERE student_id = $student_id AND branch_id = $branch_id AND status > 0
                        ORDER BY count_recharge, id ASC
                    ";
                $contracts = DB::select(DB::raw($query));

                if (!empty($contracts)) {
                    $enrolment_ids = [];
                    foreach ($contracts as $c) {
                        if ($c->enrolment_id) {
                            $enrolment_ids[] = $c->enrolment_id;
                        }
                    }

                    $enrolments = [];
                    if (!empty($enrolment_ids)) {
                        $enrolment_ids_string = implode(',', $enrolment_ids);
                        $query = "
                                    SELECT
                                        c.id, c.id contract_id, c.enrolment_start_date, c.enrolment_last_date, c.enrolment_real_sessions, MIN(s1.class_day) AS class_day_1, MAX(s2.class_day) AS class_day_2
                                    FROM
                                        contracts AS c
                                        LEFT JOIN sessions as s1 ON c.class_id = s1.class_id
                                        LEFT JOIN sessions as s2 ON c.class_id = s2.class_id
                                    WHERE c.id IN ($enrolment_ids_string)
                                ";
                        $da = DB::select(DB::raw($query));
                        foreach ($da as $d) {
                            $enrolments[$d->contract_id] = $d;
                        }
                    }

                    $start = u::getPreviousDay($contracts[0]->start_date);
                    $final_last_date = null;

                    $query = "
                            SELECT start_date, end_date FROM reserves WHERE student_id = $student_id AND status = 1 AND end_date >= '$start'
                        ";

                    $reserves = DB::select(DB::raw($query));
                    foreach ($holidays as &$holiday) {
                        $holiday = array_merge($holiday, $reserves);
                    }

                    unset($holiday);


                    $class_days = [2, 5];

                    foreach ($contracts as &$c) {

                        $sheet->setCellValue("A$rowIndex", $rowIndex - 1);
                        $sheet->setCellValue("B$rowIndex", $student->name);
                        $sheet->setCellValue("C$rowIndex", $student->stu_id);
                        $sheet->setCellValue("D$rowIndex", $c->id);
                        $sheet->setCellValue("E$rowIndex", '');
                        $sheet->setCellValue("F$rowIndex", $c->start_date);
                        $sheet->setCellValue("H$rowIndex", $c->end_date);

                        if (!u::isGreaterThan($c->start_date, $start)) {
                            $c->start_date = u::getNextDay($start);
                        }

                        $c->end_date = u::getRealSessions($c->sessions, $class_days, $holidays[$c->product_id], $c->start_date)->end_date;

                        $sheet->setCellValue("G$rowIndex", $c->start_date);
                        $sheet->setCellValue("I$rowIndex", $c->end_date);
                        $rowIndex ++;

                        if (isset($enrolments[$c->id])) {
                            $e = $enrolments[$c->id];
                            $class_days = [
                                $e->class_day_1,
                                $e->class_day_2
                            ];

                            $sheet->setCellValue("A$rowIndex", $rowIndex - 1);
                            $sheet->setCellValue("B$rowIndex", $student->name);
                            $sheet->setCellValue("C$rowIndex", $student->stu_id);
                            $sheet->setCellValue("D$rowIndex", '');
                            $sheet->setCellValue("E$rowIndex", $e->id);
                            $sheet->setCellValue("F$rowIndex", $e->start_date);
                            $sheet->setCellValue("H$rowIndex", $e->last_date);

                            if (!u::isGreaterThan($e->start_date, $start)) {
                                $e->start_date = u::getRealSessions(1, $class_days, $holidays[$c->product_id], u::getNextDay($start))->end_date;
                            }

                            $e->last_date = u::getRealSessions($e->real_sessions, $class_days, $holidays[$c->product_id], $e->start_date)->end_date;
                            $enrolments[$c->id] = $e;

                            $sheet->setCellValue("G$rowIndex", $e->start_date);
                            $sheet->setCellValue("I$rowIndex", $e->last_date);
                            $rowIndex ++;

                            if (u::isGreaterThan($c->end_date, $e->last_date)) {
                                $start = $c->end_date;
                            } else {
                                $start = $e->last_date;
                            }
                        } else {
                            $final_last_date = $c->end_date;
                            $start = $c->end_date;
                        }
                    }
                }

            }

            $writer = new Xlsx($spreadsheet);
            try {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Tính toán end date.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save("php://output");
            } catch (ExceptionSpreadSheet $exception) {
                throw $exception;
            }
            exit;
        }
    }

    /*
     * @date : Y-m-d
     */
    public function getEffDashboard($date)
    {
      $localDate = date('Y-m-d',strtotime($date));
      $date = date('m-d-Y',strtotime($date));

      $datasEff = u::eff_query("select * from dbo.fuc_Dashboard01('$date','$date')");
      if($datasEff) {
        foreach ($datasEff as $data) {
          $dataInsert = [
            'bo_phan' => $data->bo_phan,
            'ps_no1' => $data->ps_no1,
            'ps_no1kh' => $data->ps_no1kh,
            'ps_ngay' => $data->ps_ngay,
            'hs_moi' => $data->hs_moi,
            'hs_hientai' => $data->hs_hientai,
            'hs_hethan' => $data->hs_hethan,
            'pt' => $data->pt,
            'ma_eff' => $data->ma_eff,
            'ma_crm' => $data->ma_crm,
            'ten' => $data->ten,
            'cap' => $data->cap,
            'in_dam' => $data->in_dam,
            'vung_mien' => $data->vung_mien,
            'invisible' => $data->invisible,
            'sapxep' => $data->sapxep,
            'hs_no' => $data->hs_no,
            'date' => $localDate
          ];

          $connection = DB::connection('mysql_1');
          $validate = $connection->select(DB::raw("
            SELECT id FROM dbo_fuc_dashboard01_new
            WHERE date = '{$localDate}'
            AND ten = '{$data->ten}'
          "));
          if( count($validate) > 0 ) {
              $connection->table('dbo_fuc_dashboard01_new')->where('id',$validate[0]->id)->update($dataInsert);
          } else {
              $connection->table('dbo_fuc_dashboard01_new')->insert($dataInsert);
          }
        }
      }
    }

    public function getEffTopSales($date)
    {
      try {
        $date = date('m-d-Y',strtotime($date));
        u::eff_query("EFF_APAX.dbo.sp_doanhso_team '$date'");
        $datasEff = u::eff_query("select * from view_apax.dbo.tbldoanhso_team");
        if($datasEff) {
          $connection = DB::connection('mysql_1');
          $connection->select(DB::raw("DELETE FROM tbldoanhso_team"));
          foreach ($datasEff as $item) {
            $dataInsert = [
              'cap' => $item->cap,
              'in_dam' => $item->in_dam,
              'ma_tt' => $item->ma_tt,
              'ma_nv' => $item->ma_nv,
              'ten_nv' => $item->ten_nv,
              'doanhso' => $item->doanhso,
              'ps_ngay' => $item->ps_ngay,
              'hs_moi' => $item->hs_moi,
              'hs_hientai' => $item->hs_hientai,
              'hs_hethan' => $item->hs_hethan,
              'hs_conno' => $item->hs_conno,
              'bo_phan' => $item->bo_phan,
              'leader' => $item->leader,
              'salesman' => $item->salesman
            ];
            $connection->table('tbldoanhso_team')->insert($dataInsert);
          }
        }
        return response()->success('OK');
      } catch (Exception $e) {
        throw new \Exception($e);
      }

    }

  public function getEffTopProducts($date)
  {
    try {
      $date = date('m-d-Y',strtotime($date));
      u::eff_query("EFF_APAX.dbo.sp_doanhso_sp '$date'");
      $datasEff = u::eff_query("select * from view_apax.dbo.tbldoanhso_sp");
      if($datasEff) {
        $connection = DB::connection('mysql_1');
        $connection->select(DB::raw("DELETE FROM tbldoanhso_sp"));
        foreach ($datasEff as $item) {
          $dataInsert = [
            'ma_tt'   => $item->ma_tt,
            'sanpham' => $item->sanpham,
            'doanhso' => $item->doanhso,
            'ps_ngay' => $item->ps_ngay,
            'ma_sp'   => $item->ma_sp
          ];
          $connection->table('tbldoanhso_sp')->insert($dataInsert);
        }
      }
      return response()->success('OK');
    } catch (Exception $e) {
      throw new \Exception($e);
    }
  }

  public function getToken(Request $request){
      $cyberAPI = new CyberAPI();

      return $cyberAPI->getToken($request);
  }

  public function createPayment(Request $request){
    $cyberAPI = new CyberAPI();

    return $cyberAPI->createPayment($request);
  }
    public function createStudentApax(Request $request){
        $obj_data = (object)array(
            'student_name'=>$request->student_name,
            'date_of_birth' => $request->date_of_birth,
            'gender'=>$request->gender,
            'province_id'=>$request->province_id,
            'district_id'=>$request->district_id,
            'school_level'=>$request->school_level,
            'school'=>$request->school,
            'address'=>$request->address,
            'gud_name1'=>$request->gud_name1,
            'gud_mobile1'=>$request->gud_mobile1,
            'gud_gender1'=>$request->gud_gender1,
            'gud_email1'=>$request->gud_email1,
            'branch_id'=>$request->cms_branch_id,
        );
        $unix_check_dublicate = md5($request->name . $request->gud_name1 . $request->gud_mobile1);
        $check_existed = u::first("SELECT * FROM students WHERE (md5(CONCAT(name, gud_name1, gud_mobile1)) = '$unix_check_dublicate') OR (md5(CONCAT(name, gud_name2, gud_mobile2)) = '$unix_check_dublicate') OR (md5(CONCAT(name, gud_name1, gud_mobile2)) = '$unix_check_dublicate') OR (md5(CONCAT(name, gud_name2, gud_mobile1)) = '$unix_check_dublicate')");
        $is_existed = 0;
        if ($check_existed && isset($check_existed->id) && $check_existed->id > 0) {
            $is_existed = 1;
        }
        if (!$is_existed) {
            $student_name = u::explodeName($request->student_name);
            $gud_name1 = u::explodeName($request->gud_name1);

            $student = new Student();
            $student->cms_id = 0;
            $student->crm_id = "CMS" . time();
            $student->name = $obj_data->student_name;
            $student->phone = $obj_data->gud_mobile1;
            $student->date_of_birth = $obj_data->date_of_birth;
            $student->gud_mobile1 = $obj_data->gud_mobile1;
            $student->gud_name1 = $obj_data->gud_name1;
            $student->address = $obj_data->address;
            $student->province_id = $obj_data->province_id;
            $student->district_id = $obj_data->district_id;
            $student->school = $obj_data->school;
            $student->school_level = $obj_data->school_level;
            $student->created_at = date('Y-m-d H:i:s');
            $student->updated_at = date('Y-m-d H:i:s');
            $student->branch_id = $obj_data->branch_id;
            $student->firstname = $student_name->firstname;
            $student->lastname = $student_name->lastname;
            $student->midname = $student_name->midname;
            $student->gud_firstname1 = $gud_name1->firstname;
            $student->gud_lastname1 = $gud_name1->lastname;
            $student->gud_midname1 = $gud_name1->midname;
            $student->gud_gender1 = $obj_data->gud_gender1;
            $student->gud_email1 = $obj_data->gud_email1;
            $student->gender = $obj_data->gender;
            $student->status = 1;
            $student->checked = 1;
            $student->source = 33;

            $student->save();
            $lastInsertedId = $student->id;
            $cms_id = '2' . str_pad((string)$lastInsertedId, 7, '0', STR_PAD_LEFT);
            $crm_id = "CMS$cms_id";
            $cms_id = (int)$cms_id;
            u::query("UPDATE students SET cms_id = '$cms_id', crm_id = '$crm_id' WHERE id = $lastInsertedId");

            $ec_id = 730;
            $csLeader = u::first("SELECT u.id FROM term_user_branch AS t LEFT JOIN users AS u ON u.id = t.user_id 
                WHERE t.status=1 AND u.status=1 AND t.role_id=56 AND t.branch_id = $obj_data->branch_id");
            $cs_leader_id = $csLeader ? $csLeader->id :0;

            DB::table('term_student_user')->insert(
                [
                    'student_id' => $lastInsertedId,
                    'ec_id' => $ec_id,
                    'cm_id' => $cs_leader_id,
                    'status' => 1,
                    'ec_leader_id' => $ec_id,
                    'om_id' => $cs_leader_id,
                    'branch_id' => $obj_data->branch_id,
                    'region_id' => 0,
                    'zone_id' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
            CheckinController::createContractTrial($lastInsertedId,0);
            $data = ['code'=>200,'status' => 1 , 'message'=>"ok",'cms_student_id'=>$lastInsertedId];
        }else{
            $data = ['code'=>200,'status' => 0 , 'message'=>"Học sinh đã tồn tại trên  hệ thống"];
        }

        echo json_encode($data);
        exit();
    }
    public function createContractApax(Request $request){
        $obj_data = (object)array(
            'student_id'=>$request->cms_student_id,
            'tuition_fee_name' => $request->cms_tuition_fee_name,
            'tuition_fee_id' => $request->cms_tuition_fee_id,
            'must_charge'=>$request->must_charge,
            'total_discount'=>$request->total_discount,
            'start_date' =>$request->start_date,
            'bonus_sessions' =>$request->bonus_sessions,
            'note'=>$request->note
        );
        $student_info = u::first("SELECT s.id,t.branch_id,t.ec_id,t.om_id,t.cm_id,t.ec_leader_id,
                s.name AS student_name,
                (SELECT name FROM branches WHERE id=t.branch_id) AS branch_name,
                (SELECT full_name FROM users WHERE id=t.ec_id) AS ec_name 
            FROM term_student_user AS t 
                LEFT JOIN students AS s ON s.id=t.student_id 
            WHERE s.id= $obj_data->student_id");
        $tuition_fee_info = u::first("SELECT * FROM tuition_fee WHERE id='$obj_data->tuition_fee_id'");
        if($student_info && $tuition_fee_info){
            $action = "Contracts_Create";
            $contract_code = apax_ada_gen_contract_code($student_info->student_name, $student_info->ec_name, $student_info->branch_name);
            $contract_type= 1;
            $payload_type = 0;
            $student_id = $student_info->id;
            $branch_id = $student_info->branch_id;
            $ec_id = $student_info->ec_id;
            $ec_leader_id = $student_info->ec_leader_id;
            $cm_id = $student_info->cm_id;
            $om_id = $student_info->om_id;
            $product_id = $tuition_fee_info->product_id;
            $program_label = "";
            $tuition_fee_id = $tuition_fee_info->id;
            $receivable = $tuition_fee_info->receivable;
            $must_charge = $obj_data->must_charge;
            $total_discount = $obj_data->total_discount;
            $debt_amount = $obj_data->must_charge;
            $start_date = $obj_data->start_date;
            $total_sessions = (int)$tuition_fee_info->session  ;
            $bonus_sessions = (int)$obj_data->bonus_sessions;
            $holidays = u::getPublicHolidays(0, $branch_id, $product_id);
            $classdays = [2];
            $schedule = u::calEndDate($total_sessions + $bonus_sessions, $classdays, $holidays, $start_date);
            $end_date = $schedule->end_date;
            $status =1 ;
            $createdAt = date('Y-m-d H:i:s');
            $user_created = 730;
            $tuition_fee_price = $tuition_fee_info->price;
            $count_recharge =0;
            $note = $obj_data->note;
            $coupon = "APAX";
            $strMd5 = $student_id.$contract_type.$start_date.$end_date.$product_id.$program_label.$tuition_fee_id.$branch_id;
            $hash_key = md5($strMd5);
            $insert_query = "INSERT INTO contracts
            (`code`,
            `type`,
            `payload`,
            `student_id`,
            `branch_id`,
            `ec_id`,
            `ec_leader_id`,
            `cm_id`,
            `om_id`,
            `product_id`,
            `tuition_fee_id`,
            `receivable`,
            `must_charge`,
            `total_discount`,
            `debt_amount`,
            `start_date`,
            `end_date`,
            `total_sessions`,
            `status`,
            `created_at`,
            `creator_id`,
            `updated_at`,
            `editor_id`,
            `tuition_fee_price`,
            `count_recharge`,
            `note`,
            `coupon`,
            `bonus_sessions`,
            `action`,
            `hash_key`)
            VALUES
            ('$contract_code',
            $contract_type,
            $payload_type,
            $student_id,
            $branch_id,
            $ec_id,
            $ec_leader_id,
            $cm_id,
            $om_id,
            $product_id,
            $tuition_fee_id,
            $receivable,
            $must_charge,
            $total_discount,
            $debt_amount,
            '$start_date',
            '$end_date',
            $total_sessions,
            $status,
            '$createdAt',
            $user_created,
            '$createdAt',
            $user_created,
            $tuition_fee_price,
            $count_recharge,
            '$note',
            '$coupon',
            $bonus_sessions,
            '$action',
            '$hash_key')";
            $r =  u::query($insert_query);

            $latest_contract = u::first("SELECT id, created_at, updated_at, hash_key FROM contracts WHERE hash_key = '$hash_key' ORDER BY id DESC LIMIT 1");
            $previous_hashkey = md5("$latest_contract->id$latest_contract->created_at$latest_contract->updated_at$latest_contract->hash_key");
            $current_hashkey = $previous_hashkey;
            if ($latest_contract && isset($latest_contract->id)) {
              $insert_log_contract_history_query = "INSERT INTO log_contracts_history
              (`contract_id`,
              `code`,
              `type`,
              `payload`,
              `student_id`,
              `branch_id`,
              `ec_id`,
              `ec_leader_id`,
              `cm_id`,
              `om_id`,
              `product_id`,
              `tuition_fee_id`,
              `receivable`,
              `must_charge`,
              `total_discount`,
              `debt_amount`,
              `start_date`,
              `end_date`,
              `total_sessions`,
              `status`,
              `created_at`,
              `creator_id`,
              `updated_at`,
              `editor_id`,
              `tuition_fee_price`,
              `count_recharge`,
              `note`,
              `coupon`,
              `previous_hashkey`,
              `current_hashkey`,
              `version_no`,
              `bonus_sessions`,
              `action`,
              `hash_key`)
              VALUES
              ('".(int)$latest_contract->id."',
              '$contract_code',
              $contract_type,
              $payload_type,
              $student_id,
              $branch_id,
              $ec_id,
              $ec_leader_id,
              $cm_id,
              $om_id,
              $product_id,
              $tuition_fee_id,
              $receivable,
              $must_charge,
              $total_discount,
              $debt_amount,
              '$start_date',
              '$end_date',
              $total_sessions,
              $status,
              '$latest_contract->created_at',
              $user_created,
              '$latest_contract->updated_at',
              $user_created,
              $tuition_fee_price,
              $count_recharge,
              '$note',
              '$coupon',
              '$previous_hashkey',
              '$current_hashkey',
              '1',
              $bonus_sessions,
              '$action',
              '$hash_key')";
              u::query($insert_log_contract_history_query);
            }
            $this->createCyberContractApax($latest_contract->id, $user_created);
            $data = ['code'=>200,'status' => 1 , 'message'=>"ok", 'cms_contract_id'=>$latest_contract->id];
        }elseif(!$student_info){
            $data = ['code'=>200,'status' => 0 , 'message'=>"Học sinh chưa tồn tại trên hệ thống CMS"];
        }elseif(!$tuition_fee_info){
            $data = ['code'=>200,'status' => 0 , 'message'=>"Gói phí chưa tồn tại trên hệ thống CMS"];
        }
        echo json_encode($data);
        exit();
    }
    public function createCyberContractApax($contract_id, $user_id){
        $query = "SELECT c.created_at, c.id, (SELECT accounting_id FROM branches WHERE id = c.branch_id) AS branch_accounting_id,
                            s.accounting_id AS student_accounting_id,
                            s.gud_name1 AS parent,
                            c.bill_info,c.note,c.ref_code,
                            t.accounting_id AS tuition_fee_accounting_id,
                            t.receivable AS tuition_fee_receivable,
                            c.total_sessions,
                            c.tuition_fee_price,
                            t.discount AS tuition_fee_discount,
                            c.must_charge AS tien_cl,
                            c.debt_amount,
                            c.total_discount,
                            s.date_of_birth,
                            c.start_date,
                            (SELECT accounting_id FROM users WHERE id = c.ec_id) AS sale_accounting_id,
                            c.sibling_discount,
                            c.discount_value,
                            c.coupon,
                            c.bonus_sessions,
                            c.bonus_amount
                    FROM contracts c LEFT JOIN students s ON c.student_id = s.id LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
                    WHERE c.id = $contract_id AND s.status > 0 ";
        $res = u::first($query);
        $res->tuition_fee_discount = 0;
        $cyberAPI = new CyberAPI();
        $res = $cyberAPI->createContract($res, $user_id);
        if($res){
            u::query("UPDATE contracts SET accounting_id = '$res' WHERE id = $contract_id");
            u::query("UPDATE log_contracts_history SET accounting_id = '$res' WHERE contract_id = $contract_id");
        }
    }
    public function logPaymentApax(Request $request){
        $meta_data = json_encode($request->all(), JSON_UNESCAPED_UNICODE);
        $obj_data = (object)array(
            'contract_id'=>$request->cms_contract_id,
            'charge_date' => $request->charge_date,
            'must_charge'=>$request->must_charge,
            'total'=>$request->total,
            'debt' =>$request->debt,
            'amount'=>$request->amount,
            'created_at' =>date('Y-m-d H:i:s')
        );
        $contract_info = u::first("SELECT * FROM contracts WHERE id = $obj_data->contract_id");
        if($contract_info){
            DB::table('apax_log_payment')->insert(
                [
                    'student_id' => $contract_info->student_id,
                    'contract_id' => $obj_data->contract_id,
                    'charge_date' => $obj_data->charge_date,
                    'must_charge' => $obj_data->must_charge,
                    'total' => $obj_data->total,
                    'debt' => $obj_data->debt,
                    'amount' => $obj_data->amount,
                    'meta_data' => $meta_data,
                    'created_at' => date('Y-m-d H:i:s'),
                ]
            );
            $data = ['code'=>200,'status' => 1 , 'message'=>"ok"];
            $sms_info = u::first("SELECT s.gud_mobile1,s.name AS student_name, (SELECT name FROM branches WHERE id= c.branch_id) AS branch_name, (SELECT name FROM products WHERE id= c.product_id) AS product_name 
                FROM contracts AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE c.id=$obj_data->contract_id");
            $sms_phone=$sms_info->gud_mobile1;
            $sms_content="CMSEdu TB Quy phu huynh da nop ".number_format($obj_data->amount)." dong cho hoc sinh $sms_info->student_name - CT ".u::convert_name($sms_info->product_name).". Hotline CSKH 1800646805.";
            $sms =new Sms();
            $sms->sendSms($sms_phone,$sms_content,2,0,1);
        }else{
            $data = ['code'=>200,'status' => 0 , 'message'=>"Không tồn tại contractID trên hệ thống CMS"];
        }
       
        echo json_encode($data);
        exit();
    }
    public function getAllDataApax(Request $request){
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $cond ="";
        if($from_date){
            $cond.=" AND p.charge_date>='$from_date'";
        }
        if($from_date){
            $cond.=" AND p.charge_date<='$to_date' ";
        }
        $data = u::query("SELECT s.id AS cms_student_id, s.crm_id AS cms_id, s.accounting_id AS cyber_id, s.name AS student_name, 
            (SELECT name FROM branches WHERE id=c.branch_id ) AS cms_branch_name,
            (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id ) AS cms_tuition_fee_name,
            (SELECT price FROM tuition_fee WHERE id=c.tuition_fee_id ) AS cms_tuition_fee_price,
            c.must_charge, p.charge_date,
            p.total AS total_charged, p.debt AS debt_amount, p.id AS cms_payment_id,
            (SELECT full_name FROM users WHERE id=c.ec_id ) AS cms_ec_name,
            (SELECT full_name FROM users WHERE id=c.ec_leader_id ) AS cms_ec_leader_name,
            (SELECT full_name FROM users WHERE id=c.cm_id ) AS cms_om_name,
            'NEW' AS cms_status
            FROM  payment AS p LEFT JOIN contracts AS c ON c.id=p.contract_id LEFT JOIN students AS s ON s.id=c.student_id 
            WHERE s.source = 33 AND c.count_recharge = 0 $cond");

        
        $return = ['code'=>200,'data' => $data , 'message'=>"ok"];
       
        echo json_encode($return);
        exit();
    }
    public function getAllDataCMS(Request $request){
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $cond ="";
        if($from_date){
            $cond.=" AND s.created_at >= '$from_date 00:00:00'";
        }
        if($to_date){
        }
        $data = u::query("SELECT b.name AS trung_tam,s.province_id AS ma_tinh, p.name AS ten_tinh,
                    s.district_id AS ma_huyen, d.name AS ten_huyen,s.crm_id AS ma_hoc_sinh, s.name AS ten_hoc_sinh,
                    s.gud_mobile1 AS so_dien_thoai, s.gud_name1 AS ten_phu_huynh, s.date_of_birth AS ngay_sinh,
                    s.address AS dia_chi, s.school AS truong_hoc, s.created_at AS ngay_tao 
                FROM students AS s LEFT JOIN branches AS b ON b.id =s.branch_id
                LEFT JOIN provinces AS p ON p.id=s.province_id
                LEFT JOIN districts AS d ON d.id=s.district_id
            WHERE s.status>0 AND (SELECT count(id) FROM contracts WHERE status!=7 AND student_id=s.id) =0 AND   
            (SELECT count(id) FROM contracts WHERE student_id=s.id) >0 $cond ORDER BY s.id DESC");

        
        $return = ['code'=>200,'data' => $data , 'message'=>"ok"];
       
        echo json_encode($return);
        exit();
    }
}
