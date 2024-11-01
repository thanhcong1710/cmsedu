<?php
namespace App\Http\Controllers;

use function _\size;
use App\Models\Attendances;
use App\Models\LMSRequest;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\APICode;
use App\Models\Response;
use App\Providers\UtilityServiceProvider as u;
use App\Models\Reserve;

class AttendancesController extends Controller
{
  /**
   * Display a listing of the resource .
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }

  public function loadBranches(Request $request) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $data = [];
      $code = APICode::SUCCESS;
      $user_id = $session->id;
      $role_id = $session->role_id;
      $branches = $session->branches_ids;
      $where = "id > 0 AND status > 0";
      if ($role_id < 80000000) {
        $where.= " AND id IN ($branches)";
      }
      $query = "SELECT id, `name` FROM branches WHERE $where";
      $data = DB::select(DB::raw($query));
    }
    return $response->formatResponse($code, $data);
  }

  public function loadSemesters(Request $request, $branch_id = 0) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $code = APICode::SUCCESS;
      $data = Attendances::getSemesters($branch_id, $session);
    }
    return $response->formatResponse($code, $data);
  }

  public function loadClassesSemesters(Request $request) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $data = [];
      $code = APICode::SUCCESS;
      $user_id = $session->id;
      $role_id = $session->role_id;
      $branches = $session->branches_ids;
      $where = "id > 0 AND status > 0";
      $query = "SELECT id, `name` FROM semesters WHERE $where ORDER BY start_date DESC";
      $data = u::query($query);
    }
    return $response->formatResponse($code, $data);
  }

  public function semesterUpContracts(Request $request, $search) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $data = [];
      $code = APICode::SUCCESS;
      $search = json_decode($search);
      $current_id = $search->semester;
      $user_id = $session->id;
      $role_id = $session->role_id;
      $branches = $session->branches_ids;
      $where = "id > 0 AND status > 0";
      $query = "SELECT id, `name` FROM semesters WHERE $where";
      $data = u::query($query);
    }
    return $response->formatResponse($code, $data);
  }

  public function loadClasses(Request $request, $branch_id, $semester_id) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $data = [];
      $code = APICode::SUCCESS;
      $user_id = $session->id;
      $role_id = $session->role_id;
      $branches = $branch_id ? $branch_id : $session->branches_ids;
      $where = "id > 0 AND status > 0 AND branch_id IN (0,$branches) AND semester_id = $semester_id";
      $query = "SELECT id, id AS item_id, 'program' AS item_type, `name` AS `text`, parent_id, 'fa fa-folder' AS icon FROM programs WHERE $where
                  UNION ALL
                  SELECT CONCAT(999, c.id) AS id, c.id AS item_id, 'class' AS item_type, c.cls_name AS `text`, c.program_id AS parent_id, IF(c.cm_id > 0, 'fa fa-file-text-o', 'fa fa-user-times') AS icon 
                  FROM classes AS c INNER JOIN programs AS p ON c.program_id = p.id
                  WHERE c.cls_iscancelled = 'no' AND p.status > 0 AND p.branch_id IN (0,$branches) AND p.semester_id = $semester_id";
      $class = u::query($query);
      if (count($class)) {
        foreach ($class as $item) {
          $item->value = $item->id;
          $item->opened = true;
          $item->selected = false;
          $item->disabled = false;
          $item->loading = false;
          $item->children = [];
        }
        $classes = apax_get_tree_data($class);
        if ($classes) {
          foreach ($classes as $cls) {
            if ($cls) {
              $data[] = $cls;
            }
          }
        }
      }
    }
    return $response->formatResponse($code, $data);
  }

  public function loadAllClasses(Request $request, $branch_id, $semester_id) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $data = [];
      $code = APICode::SUCCESS;
      $user_id = $session->id;
      $role_id = $session->role_id;
      $branches = $session->branches_ids;
      $where = " status > 0 AND branch_id IN (0,$branch_id) AND semester_id = $semester_id";
      $query = "SELECT id, id AS item_id, 'program' AS item_type, 'no' AS cls_iscancelled, `name` AS `text`, parent_id, 'fa fa-folder' AS icon FROM programs WHERE $where
                  UNION ALL
                  SELECT CONCAT(999, c.id) AS id, c.id AS item_id, 'class' AS item_type, c.cls_iscancelled, c.cls_name AS `text`, c.program_id AS parent_id, IF(c.cm_id > 0, IF (c.cls_iscancelled = 'yes', 'fa fa-window-close-o', 'fa fa-file-text-o'), IF (c.cls_iscancelled = 'yes', 'fa fa-close', 'fa fa-user-times')) AS icon
                  FROM classes AS c INNER JOIN programs AS p ON c.program_id = p.id
                  WHERE p.status > 0 AND p.branch_id IN (0,$branch_id) AND p.semester_id = $semester_id";
      $class = u::query($query);
      if (count($class)) {
        foreach ($class as $item) {
          $item->value = $item->id;
          $item->opened = true;
          $item->selected = false;
          $item->disabled = false;
          $item->loading = false;
          $item->children = [];
        }
        $classes = apax_get_tree_data($class);
        if ($classes) {
          foreach ($classes as $cls) {
            if ($cls) {
              $data[] = $cls;
            }
          }
        }
      }
    }
    return $response->formatResponse($code, $data);
  }

  public function loadSchedule(Request $request, $class_id) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $class_id = (int)$class_id;
      $code = APICode::SUCCESS;
      $data = [];
      $class_info = u::first("SELECT id, product_id, cls_startdate, cls_enddate FROM classes WHERE id = $class_id");
      $classdays = u::getClassDays($class_id);
      $holidays = u::getPublicHolidays($class_id, 0, $class_info->product_id);
      $class_start_date = date('Y-m-d', strtotime($class_info->cls_startdate));
      $class_end_date = date('Y-m-d', strtotime($class_info->cls_enddate));
      $x = new \Moment\Moment(strtotime(date('Y-m-d')));
      $c = new \Moment\Moment(strtotime($class_start_date));
      $z = new \Moment\Moment(strtotime($class_end_date));
      $start_date = $x->from($c)->getDays() <= 0 ? date('Y-m-d') : $class_start_date;
      $s = new \Moment\Moment(strtotime($start_date));
      $end_date = $s->addDays(25)->format('Y-m-d');
      $e = new \Moment\Moment(strtotime($end_date));
      $end_date = $s->from($e)->getDays() <= 0 ? $end_date : $class_end_date;
      $schedule_info = u::calcDoneSessions($start_date, $end_date, $holidays, $classdays);
      if (isset($schedule_info->dates)) {
        foreach($schedule_info->dates as $date) {
          $p = new \Moment\Moment(strtotime($date));
          $info = (object)[];
          $info->date = $date;
          $info->blocks = u::getClassBlockByDate($class_id, $date);
          $data[]= $info;
        }
      }
    }
    return $response->formatResponse($code, $data);
  }

  public function checkNick(Request $request, $class_id, $student_id, $nick) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $code = APICode::SUCCESS;
      $data = (Object)[];
      $nick = strtoupper(apax_ada_convert_unicode($nick));
      $check = u::first("SELECT COUNT(s.nick) AS existed FROM students AS s
                                LEFT JOIN contracts AS c ON c.student_id = s.id
                                LEFT JOIN classes AS cl ON c.class_id = cl.id
                                WHERE c.class_id = $class_id
                                  AND c.enrolment_start_date > cl.cls_startdate
                                  AND c.enrolment_start_date < cl.cls_enddate
                                  AND s.nick = '$nick'");
      if ($check && isset($check->existed) && $check->existed > 0) {
        $data->update = false;
      } else {
        u::query("UPDATE students SET nick = '$nick' WHERE id = '$student_id' AND nick IS NULL");
        $data->update = true;
      }
    }
    return $response->formatResponse($code, $data);
  }

  public function addContracts(Request $request) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $data = [];
      $code = APICode::SUCCESS;
      $user_id = $session->id;
      $role_id = $session->role_id;
      $branches = $session->branches_ids;
      $post = $request->input();
      if ($post) {
        $class_id = $post['class'];
        $contracts = isset($post['contracts']) ? $post['contracts'] : [];
        if (count($contracts)) {
          $prepare_student_ids = [];
          $prepare_student_nicks = [];
          $prepare_student_cheks = [];
          foreach ($contracts as $contract) {
            $prepare_student_ids[] = $contract['student_id'];
          }
          $prepare_student_matched_ids = implode(',', $prepare_student_ids);
          $prepare_student_info = u::query("SELECT id, nick FROM students WHERE id IN ($prepare_student_matched_ids)");
          if (count($prepare_student_info)) {
            foreach ($prepare_student_info as $student_info) {
              if ($student_info->nick != '') {
                $prepare_student_cheks[] = $student_info->id;
                $prepare_student_nicks[] = "'$student_info->nick'";
              }
            }
          }
          $exept_student_ids = [];
          if (count($prepare_student_nicks)) {
            $prepare_student_nicks = implode(',', $prepare_student_nicks);
            $prepare_student_chekx = implode(',', $prepare_student_cheks);
            $duplicate_nicks = u::query("SELECT s.id FROM students AS s
                          LEFT JOIN contracts AS c ON c.contract_id = s.id
                          LEFT JOIN classes AS cl ON c.class_id = cl.id
                          WHERE c.class_id = $class_id
                            AND c.enrolment_start_date > cl.cls_startdate
                            AND c.enrolment_start_date < cl.cls_enddate
                            AND c.type > 0
                            AND c.status = 6
                            AND s.nick IN ($prepare_student_nicks)
                            AND s.id NOT IN ($prepare_student_chekx)");
            if (count($duplicate_nicks)) {
              foreach ($duplicate_nicks as $duplicate_nick) {
                if (!in_array($duplicate_nick->id, $exept_student_ids)) {
                  $exept_student_ids[] = $duplicate_nick->id;
                }
              }
            }
          }
          $program_info = u::first("SELECT program_id FROM classes WHERE id = $class_id LIMIT 1");
          $program_id = $program_info && isset($program_info->program_id) ? (int)$program_info->program_id : 0;
          $contractx = [];
          $contractz = [];

          $class_id = $post['class'];
          $class_information = $post['class_info'];

          $product_id = (int)$class_information['product_id'];
          if(!$product_id){
            $product = u::getProductByProgram($program_id);
            $product_id = $product->id;
          }

          $class_start_date = date('Y-m-d', strtotime($class_information['class_start_date']));
          $class_end_date = date('Y-m-d', strtotime($class_information['class_end_date']));
          $classdays = u::getClassDays($class_id);
          $holidays = u::getPublicHolidays($class_id, 0, $product_id);

          foreach ($contracts as $contract) {
            if (in_array($contract['student_id'], $prepare_student_cheks) &&
              !in_array($contract['student_id'], $exept_student_ids) && (int)$contract['processed'] < 3) {
              $contractx[] = $contract;
            } elseif ((int)$contract['processed'] == 3) {
              $contractz[] = $contract;
            }

            $cm_id = $class_information['cm_id'];

            if ($contractz) {
              foreach ($contractz as $ncontract) {
                $ex_enrolment = u::first("SELECT * FROM  WHERE student_id = ".$ncontract['student_id']." ORDER BY last_date DESC LIMIT 1");
                $check_transferable = u::can_transfer((int)$ex_enrolment->class_id, (int)$post['class']);
                if ($ex_enrolment && isset($ex_enrolment->class_id) && $check_transferable) {
                  u::query("UPDATE contracts SET status = 7 WHERE id = $ex_enrolment->contract_id");
                  // u::query("UPDATE enrolments SET status = 0 WHERE id = $ex_enrolment->id");
                  $hash_key = md5($class_id.$ncontract['contract_id'].$ncontract['student_id'].$ex_enrolment->start_date.$ex_enrolment->end_date);
                  $ncontract = (Object)$ncontract;
                  if ((int)$ex_enrolment->class_id == (int)$post['class']) {
                    $classdate = explode('~', $ncontract->class_date);
                    $start_date = count($classdate) ? $classdate[1] : date('Y-m-d');
                    $available_sessions = $ncontract->available_sessions;
                    $information = u::getRealSessions($available_sessions, $classdays, $holidays, $start_date);
                    $clone_enrolment_query = "INSERT INTO enrolments ( 
                        `contract_id`,
                        `student_id`,
                        `class_id`,
                        `start_date`,
                        `end_date`,
                        `start_cjrn_id`,
                        `status`,
                        `real_sessions`,
                        `left_sessions`,
                        `last_date`,
                        `created_at`,
                        `creator_id`,
                        `updated_at`,
                        `editor_id`,
                        `hash_key`
                      ) VALUES (
                        $ncontract->id,
                        $ncontract->student_id,
                        $ex_enrolment->class_id,
                        $ex_enrolment->start_date,
                        '".$information->end_date."',
                        $ex_enrolment->start_cjrn_id,
                        1,
                        ".($available_sessions + $ex_enrolment->real_sessions).",
                        $ex_enrolment->left_sessions,
                        '".$information->end_date."',
                        NOW(),
                        $user_id,
                        NOW(),
                        $user_id,
                        '".$hash_key."'
                      )";
                  } else {
                    if ($product_id < 4) {
                      $info_class_date = explode('~', $ncontract->class_date);
                      $start_cjrn_id = count($info_class_date) > 1 ? (int)$info_class_date[0] : '';
                      $ex_class_info = u::query("SELECT * FROM classes WHERE id = $ex_enrolment->class_id");
                      $lms_params = (Object)[
                        "command" => "stu_cls_transfer",
                        "stu_id" => $ncontract->student_lms_id,
                        "from_cls_id" => $ex_class_info->cls_id,
                        "to_cls_id" => $class_information['class_id'],
                        "to_cls_start_cjrn_id" => $start_cjrn_id
                      ];
                      $lmsApiController = new LMSAPIController();
                      $lmsrq = new Request();
                      $lmsrq->api_url = 'http://api.cdiwise.com/sync/set_stu_cls_transfer.aspx';
                      $lmsrq->api_method = "POST";
                      $lmsrq->api_params = json_encode($lms_params);
                      $lmsInfo = $lmsApiController->callAPI($lmsrq, 1);
                    }
                  }
                } elseif ($ex_enrolment && isset($ex_enrolment->class_id) && $product_id < 4) {
                  $this->withdrawLMS($ex_enrolment->id);
                  $contractx[] = $ncontract;
                }
              }
            }
          }
          $existed = [];
          $validat = [];
          if (count($contractx)) {
            foreach($contractx as $contr) {
              if (!in_array($contr['student_nick'], $existed)) {
                $existed[] = $contr['student_nick'];
                $validat[] = $contr;
              } else {
                $contr['student_nick'] = ada()->nick($contr['student_nick'], $existed);
                $validat[] = $contr;
              }
            }
          }
          $contractx = $validat;
          if (count($contractx)) {
            $reupdate_nicks = [];
            $nick_names = [];
            foreach ($contractx as $contra) {
//                if (is_numeric(substr($contra['student_nick'], -1, 1))) {
              $obj = (object)['id'=>$contra['student_id'], 'nick'=>$contra['student_nick']];
              $reupdate_nicks[] = $obj;
              $nick_names[$contra['student_id']] = $contra['student_nick'];
//                }
            }
            if (count($reupdate_nicks)) {
              $reupdate_query = "INSERT INTO students (`id`, `nick`) VALUES ";
              $z = 0;
              foreach ($reupdate_nicks as $reupdate) {
                $z++;
                $reupdate_query.= "($reupdate->id, '$reupdate->nick')";
                if ($z < (int)count($reupdate_nicks)) {
                  $reupdate_query.= ",";
                }
              }
              $reupdate_query.= " ON DUPLICATE KEY UPDATE `nick` = VALUES(`nick`)";
              u::query($reupdate_query);
            }
            $i = 0;
            $insert_query = "INSERT INTO enrolments (
                `contract_id`,
                `student_id`,
                `class_id`,
                `start_date`,
                `end_date`,
                `start_cjrn_id`,
                `status`,
                `real_sessions`,
                `left_sessions`,
                `last_date`,
                `final_last_date`,
                `created_at`,
                `creator_id`,
                `updated_at`,
                `editor_id`,
                `hash_key`
              ) VALUES ";
            $hash_list = [];
            $update_pending_students_ids = [];
            foreach ($contractx as $contract) {
              $available_sessions = (int)$contract['available_sessions'] ? (int)$contract['available_sessions'] : 3;
              $info_class_date = explode('~', $contract['class_date']);
              $start_cjrn_id = count($info_class_date) > 1 ? (int)$info_class_date[0] : '';
              $start_date = count($info_class_date) > 1 ? $info_class_date[1] : '';
              $left_infor = u::calcDoneSessions($start_date, $class_end_date, $holidays, $classdays);
              $left_sesssions = md5(date('Y-m-d', strtotime($start_date))) === md5(date('Y-m-d', strtotime($class_end_date))) ? 1 : (int)$left_infor->total - 1; //hack code to fix addition 1 session when enrolment
              $left_sesssions = (int)$left_sesssions == 0 ? 1 : $left_sesssions;
              $information = u::getRealSessions($available_sessions, $classdays, $holidays, $start_date);
              $previous_final_last_date = u::first("SELECT final_last_date FROM enrolments WHERE final_last_date IS NOT NULL AND student_id = ".$contract['student_id']." ORDER BY id DESC LIMIT 0, 1");
              $previous_final_last_date = $previous_final_last_date && isset($previous_final_last_date->final_last_date) ? "'$previous_final_last_date->final_last_date'" : "NULL";
              /**
               * Calculate the final last date in case without enrolment of multi-contracts
               */
              $recharge_counter = (int)$contract['count_recharge'];
              if ($recharge_counter == 0) {
                $student_id = $contract['student_id'];
                $total_real_session = u::first("SELECT COALESCE(SUM(real_sessions), 0) AS total FROM contracts WHERE student_id = $student_id AND count_recharge > 0 AND real_sessions > 0");
                $special_start_date = u::getRealSessions(2, $classdays, $holidays, $information->end_date);
                $special_last_date = u::getRealSessions((int)$total_real_session->total, $classdays, $holidays, $special_start_date->end_date);
                $previous_final_last_date = isset($special_last_date->end_date) ? "'$special_last_date->end_date'" : $previous_final_last_date;
              } elseif ($recharge_counter > 0) {
                $student_id = $contract['student_id'];
                $checkHigher = u::query("SELECT id FROM contracts WHERE student_id = $student_id AND count_recharge > $recharge_counter");
                if (count($checkHigher) == 0) {
                  $previous_final_last_date = "NULL";
                }
              }
              // dd($previous_final_last_date);
              // dd($start_date, $information, $available_sessions);
              $hash_key = md5($class_id.$contract['contract_id'].$contract['student_id'].$class_start_date.$class_end_date.$left_sesssions.$available_sessions);
              $i++;
              $update_pending_students_ids[]= $contract['student_id'];
              $hash_list[] = "'$hash_key'";
              $insert_query.= "(";
              $insert_query.= "'".$contract['contract_id']."',";
              $insert_query.= "'".$contract['student_id']."',";
              $insert_query.= "'$class_id',";
              $insert_query.= "'$start_date',";
              $insert_query.= "'$class_end_date',";
              $insert_query.= "'$start_cjrn_id',";
              $insert_query.= "'1',";
              $insert_query.= "'$available_sessions',";
              $insert_query.= "'$left_sesssions',";
              $insert_query.= "'$information->end_date',";
              $insert_query.= "$previous_final_last_date,";
              $insert_query.= "NOW(),";
              $insert_query.= "'$user_id',";
              $insert_query.= "NOW(),";
              $insert_query.= "'$user_id',";
              $insert_query.= "'$hash_key'";
              $insert_query.= ")";
              if ($i < (int)count($contractx)) {
                $insert_query.= ",";
              }
            }
            if ($insert_query) {
              u::query($insert_query);
              $j = 0;
              $hash_list = implode(',', $hash_list);
              $inserted_enrolments = u::query("SELECT 
                              c.id AS enrolment_id, c.id contract_id, t.cm_id AS ex_cm, t.id AS term_id, s.id AS student_id, s.branch_id, s.stu_id, c.start_cjrn_id, c.cls_id, 
                              (SELECT count_recharge FROM contracts WHERE id = c.id LIMIT 1) AS count_recharge
                            FROM 
                                contracts as c 
                                LEFT JOIN students AS s ON c.student_id = s.id 
                                LEFT JOIN term_student_user AS t ON t.student_id = s.id 
                                LEFT JOIN classes AS c ON c.class_id = c.id
                            WHERE 
                                c.hash_key IN ($hash_list) ORDER BY c.id DESC");
              $insert_log_query = "INSERT INTO log_manager_transfer (student_id, from_branch_id, to_branch_id, from_cm_id, to_cm_id, date_transfer, updated_by, note, created_at, updated_at) VALUES ";
              $update_term_query = "INSERT INTO term_student_user (id, cm_id, updated_at) VALUES ";
              $update_contract_query = "INSERT INTO contracts (id, enrolment_id, product_id, program_id, updated_at) VALUES ";
              $current_time = date("Y-m-d H:i:s");
              foreach ($inserted_enrolments as $contract) {
                $j++;
                $insert_log_query.= "($contract->student_id, $contract->branch_id, $contract->branch_id, $contract->ex_cm, $cm_id, '$current_time', $user_id, 'Từ quá trình xếp lớp', '$current_time', '$current_time')";
                $update_term_query.= "($contract->term_id, $cm_id, '$current_time')";
                $update_contract_query.= "($contract->contract_id, $contract->enrolment_id, $product_id, $program_id, '$current_time')";
                if ($j < (int)count($inserted_enrolments)) {
                  $insert_log_query.= ",";
                  $update_term_query.= ",";
                  $update_contract_query.= ",";
                }
                // Khi sử dụng API LMS thì bỏ comment dòng dưới
                $nick = isset($nick_names[$contract->student_id])?$nick_names[$contract->student_id]:'';
                if ($product_id < 4) {
                  $this->registerLMS($contract->stu_id, $contract->cls_id, $contract->start_cjrn_id, $contract->enrolment_id, $product_id, $nick, $class_id, $contract->count_recharge);
                }
              }
              $update_term_query.= " ON DUPLICATE KEY UPDATE cm_id = VALUES(cm_id), updated_at = '$current_time'";
              $update_contract_query.= " ON DUPLICATE KEY UPDATE enrolment_id = VALUES(enrolment_id), product_id = VALUES(product_id), program_id = VALUES(program_id), updated_at = '$current_time'";
              $update_pending_students_ids = implode(',', $update_pending_students_ids);
              $update_pendings_query = "UPDATE pendings SET end_date = SUBDATE(DATE('$start_date'),1) WHERE student_id IN ($update_pending_students_ids)";
              u::query($insert_log_query);
              u::query($update_term_query);
              u::query($update_contract_query);
              u::query($update_pendings_query);
              $data = $contractx;
            } else {
              $code = APICode::SUCCESS;
              $data = "Update false with start_date = $start_date & left_sesssions = $left_sesssions & available_sessions = $available_sessions";
            }
          }
        }
      }
    }
    return $response->formatResponse($code, $data);
  }

  private function registerLMS($stu_id, $cls_id, $cjrn_id, $enrolment_id, $product_id, $nick_name = '', $class_id = 0, $count_recharge = -1){
    if (ENVIRONMENT == 'product' && $stu_id) {
      //active học sinh
      $lmsWeb = new LMSRequest();
      $lmsWeb->activeStudent($stu_id);

      //#update nick name cho hoc sinh
      $student = u::first("SELECT s.*, sg.name AS school_grade_name, b.brch_id FROM students AS s LEFT JOIN school_grades AS sg ON (s.school_grade * 1) = sg.id LEFT JOIN branches as b ON s.branch_id = b.id WHERE s.stu_id = $stu_id");

      $da = json_encode([
        "command" => "stu_modify",
        "stu_id" => $stu_id,
        "preferred_brch_id" => $student->brch_id,
        "name" => $student->name,
        "stu_email" => $student->email,
        "home_phone" => "$student->phone",
        "school" => $student->school,
        "nick_name" => $nick_name?$nick_name:($student->nick?$student->nick:$student->name),
        "date_of_birth" => $student->date_of_birth,
        "gender" => $student->gender,
        "address" => $student->address,
        "school_grade" => $student->school_grade_name,
        "gud_name1" => $student->gud_name1,
        "gud_mobile1" => $student->gud_mobile1,
        "gud_name2" => $student->gud_name2,
        "gud_mobile2" => $student->gud_mobile2,
        "gud_email" => $student->email,
        "sale_stf_id" => LMS_STF_ID,
        "assigned_stf_id" => LMS_STF_ID,
        "note" => "",
        "trial_stu" => 0,
        "trial_class_start" => "",
        "trial_class_end" => "",
        "crm_stu_id" => $student->crm_id,
        "prod_code" => (int)$product_id?$product_id:2
      ]);

      $requestUpdateNick = new Request();
      $requestUpdateNick->api_url = 'modify_stu_info';
      $requestUpdateNick->api_method = 'POST';
      $requestUpdateNick->api_params = $da;
      $requestUpdateNick->api_header = '';
      $lmsModel = new LMSAPIController();
      $res = $lmsModel->callAPI($requestUpdateNick, 1);
      if($res){
        try{
          $data = json_decode($res);
          if(isset($data->code) && ($data->code == APICode::SUCCESS)){
            $start_cjrn_id = $cjrn_id;
            $previous_enrolment = u::first("SELECT `start_cjrn_id`, class_id FROM contracts WHERE student_id = $student->id ORDER BY id DESC LIMIT 1,1");
            if($student->branch_id != 100){
              if(!empty($previous_enrolment)){
                if(($previous_enrolment->class_id == $class_id) && ($previous_enrolment->start_cjrn_id) && ($previous_enrolment->start_cjrn_id !== '0000-00-00')){
                  $start_cjrn_id = $previous_enrolment->start_cjrn_id;
                }
              }
            }else{
              if($count_recharge > 0){
                $start_cjrn_id = u::suggestStartCjrnIdForRenew($class_id, $cjrn_id);
              }
            }
            //call API 22 LMS
            $request = new Request();
            $studentInfo = [
              "command" => "stu_cls_placement",
              "stu_id" => "$stu_id",
              "cls_id" => "$cls_id",
              "start_cjrn_id" => "$start_cjrn_id"
            ];
            $request->api_url = 'set_stu_placement';
            $request->api_method = 'POST';
            $request->api_params = json_encode($studentInfo);
            $request->api_header = '';
            $lmsModel = new LMSAPIController();
            $response = $lmsModel->callAPI($request, 1);
            if($response){
              try{
                $data = json_decode($response);
                if(isset($data->code) && ($data->code == APICode::SUCCESS)){
                  $lmsData = is_object($data->data)?$data->data:json_decode($data->data);
                  $cstd_id = (int)(trim(str_replace("cstd_id:","",$lmsData->msg)));
                  $query = "UPDATE contracts SET cstd_id = $cstd_id, start_cjrn_id=$start_cjrn_id WHERE id=$enrolment_id";
                  u::query($query);
                }
              }catch (\Exception $e){

              }
            }
          }
        }catch (\Exception $e){

        }
      }
    }
  }

  public function withdraw(Request $request, $id) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $code = APICode::SUCCESS;
      $user_id = $session->id;
      if ($id) {
        $info_change = '';
        $current_contract = u::first("SELECT * FROM contracts WHERE enrolment_id = $id");
        if ($current_contract && isset($current_contract->type) && $current_contract->type === 0) {
          $check_enrolment = u::first("SELECT COUNT(id) do_no_thing FROM contracts WHERE enrolment_last_date < CURDATE() AND id = $id");
          if ((int)$check_enrolment->do_no_thing === 0) {
            u::query("UPDATE contracts SET status = 7, editor_id = $user_id, updated_at = NOW(), enrolment_withdraw_date = NOW(), enrolment_withdraw_reason = 'Rời khỏi lớp học trải nghiệm', end_date = CURDATE() WHERE enrolment_id = $id");
            $info_change = 'Withdraw học sinh ra khỏi lớp học trải nghiệm và cập nhật lại last date.';
          } else {
            u::query("UPDATE contracts SET status = 7, editor_id = $user_id, updated_at = NOW(), enrolment_withdraw_date = NOW(), enrolment_withdraw_reason = 'Rời khỏi lớp học trải nghiệm' WHERE enrolment_id = $id");
            $info_change = 'Withdraw học sinh ra khỏi lớp học trải nghiệm và không cập nhật lại last date.';
          }
        } else {
          u::query("UPDATE contracts SET status = 7, editor_id = $user_id, updated_at = NOW(), enrolment_withdraw_date = NOW(), enrolment_withdraw_reason = 'Rời khỏi lớp học trải nghiệm' WHERE enrolment_id = $id");
          $info_change = 'Withdraw học sinh do quá hạn số buổi học';
        }
        if ($current_contract) {
          $current_time = date('Y-m-d H:i:s');
          DB::table('log_student_update')->insert(
            [
              'student_id' => $current_contract->student_id,
              'updated_by' => $user_id,
              'cm_id' => $current_contract->cm_id,
              'status' => 1,
              'branch_id' => $current_contract->branch_id,
              'ceo_branch_id' => 0,
              'content' => $info_change,
              'updated_at' => $current_time
            ]
          );
        }
        $data = $info_change;
        $this->withdrawLMS($id);
      }
    }
    return $response->formatResponse($code, $data);
  }

  public function withdrawLMS($enrolment_id){
    $query = "
                SELECT 
                    s.stu_id, c.cstd_id
                FROM
                    contracts as c
                    LEFT JOIN students AS s ON c.student_id = s.id
                WHERE
                    c.id = $enrolment_id
            ";
    $students = DB::select(DB::raw($query));
    $lmsApiController = new LMSAPIController();
    $request = new Request();
    $request->api_url = 'modify_stu_cls_status';
    $request->api_method = 'POST';
    $request->api_header = '';
    foreach ($students as $student){
      $da = [
        "command"=> "stu_cls_status",
        "stu_id"=> "$student->stu_id",
        "cstd_id"=> "$student->cstd_id",
        "cstd_status"=> "Withdrawal"
      ];
      $request->api_params = json_encode($da);
      $lmsApiController->callAPI($request,1);
    }
  }

  public function contracts(Request $request, $pagination, $search) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $pagination = json_decode($pagination);
      $search = json_decode($search);
      $class_id = isset($search->class) ? (int)$search->class : 0;
      $branch_id = isset($search->branch) ? (int)$search->branch : 0;
      $keyword = isset($search->keyword) ? $search->keyword : '';
      $where = '';
      $limit = '';
      if ($pagination->cpage && $pagination->limit) {
        $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
        $limit.= " LIMIT $offset, $pagination->limit";
      }
      if ($keyword != '') {
        $where.= " AND
            ( s.crm_id LIKE '$keyword%'
            OR s.stu_id LIKE '$keyword%'
            OR s.accounting_id LIKE '$keyword%'
            OR s.name LIKE '%$keyword%'
            OR s.nick LIKE '%$keyword%'
            OR s.email LIKE '%$keyword%'
            OR s.phone LIKE '$keyword%'
            OR s.gender LIKE '$keyword')";
      }
      $class_info = u::first("SELECT product_id, program_id FROM classes WHERE id = $class_id");
      $program_id = $class_info->program_id;
      $program_info = u::first("SELECT product_id FROM term_program_product WHERE program_id = $program_id");
      $product_id = $program_info->product_id;
      if ($product_id == 4) {
        $where.= " AND s.extra = 1 ";
      } else {
        $where.= " AND s.stu_id != '' AND s.stu_id IS NOT NULL ";
      }
      $special_condition = ENVIRONMENT == 'product' ? " AND s.stu_id != '' " : '';
      $available_students = u::first("SELECT ((SELECT max_students FROM classes WHERE id = $class_id)
            -
            (SELECT COUNT(*) AS total FROM (SELECT c.id AS total FROM contracts AS c
            LEFT JOIN students AS s ON c.student_id = s.id
          WHERE c.class_id = $class_id 
            AND c.enrolment_last_date >= CURDATE() AND c.status = 6
            AND c.enrolment_last_date >= c.enrolment_start_date GROUP BY c.student_id) AS a)) AS total");
      $total_available = (int)$available_students->total;
      $code = APICode::SUCCESS;
      $pending_condition = " AND COALESCE(p.id, (p.start_date > CURDATE() OR p.end_date < CURDATE()), true) ";
      $pending_condition = "";
      $data = (Object)[];
      $select = "SELECT
                    c.id,
                    c.product_id,
                    c.id AS contract_id,
                    c.code AS contract_code,
                    s.id AS student_id,
                    s.stu_id AS student_lms_id,
                    s.accounting_id AS student_accounting_id,
                    c.type AS customer_type,
                    s.name AS student_name,
                    s.nick AS student_nick,
                    c.count_recharge,
                    c.type,
                    CONCAT(s.name, ' - ', COALESCE(s.stu_id, s.crm_id)) AS label,
                    s.school AS student_school,
                    g.name AS student_school_grade,
                    s.phone AS student_phone,
                    (SELECT file FROM trial_reports WHERE student_id = c.student_id AND session_no = 9 AND approved_by > 0 ORDER BY id DESC LIMIT 1) attached_file, 
                    c.start_date AS contract_start_date,
                    t.name AS tuition_fee_name,
                    t.price AS tuition_fee_price,
                    t.receivable AS tuition_fee_receivable,
                    CONCAT(u0.full_name, ' - ', u0.username) AS cm_name,
                    CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
                    c.debt_amount,
                    c.processed, 
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
                    JOIN ( SELECT student_id, Min( count_recharge ) AS m FROM contracts 
                    WHERE branch_id = $branch_id 
                    AND ( enrolment_id IS NULL OR enrolment_id = 0 ) 
                    AND ((type > 0 AND status IN (1,2,3,4,5,6)) OR (type = 0 AND status IN (1,5,6)))
                    AND real_sessions > 0
                    GROUP BY student_id ) AS b ON ( c.student_id = b.student_id AND c.count_recharge = b.m )
                  WHERE c.branch_id = $branch_id $special_condition 
                    AND (c.enrolment_id IS NULL OR c.enrolment_id = 0)
                    AND ((c.type > 0 AND c.status IN (1,2,3,4,5,6)) OR (c.type = 0 AND c.status IN (1,5,6)))
                    AND (c.product_id = (SELECT se.product_id FROM semesters se RIGHT JOIN classes cls ON cls.sem_id = se.sem_id WHERE cls.id = $class_id)  OR c.product_id = 0) 
                    AND (cl.id = $class_id OR cl.id IS NULL)
                    AND c.real_sessions > 0
                    AND COALESCE(r.id, (r.start_date > CURDATE() OR r.end_date < CURDATE()), true)
                    AND (s.id NOT IN (SELECT student_id FROM contracts WHERE `status` = 6 AND student_id IS NOT NULL GROUP BY student_id) OR ((SELECT enrolment_last_date FROM contracts WHERE status = 6 AND student_id = c.student_id AND id = c.id) <= CURDATE() AND c.processed = 3)) $pending_condition $where GROUP BY c.student_id";
      $classdate = u::query("SELECT cls_id, cjrn_id, cjrn_classdate FROM schedules WHERE class_id = $class_id AND cjrn_classdate >= CURDATE() AND `status` > 0 ORDER BY cjrn_classdate ASC");
      $holidays = u::getPublicHolidays($class_id, 0, $product_id);
      $classdates = [];
      if ($classdate) {
        foreach ($classdate as $startdate) {
          if (!u::checkInHolydays($startdate->cjrn_classdate, $holidays)) {
            $classdates[]= $startdate;
          }
        }
      }
      $total = u::first("SELECT COUNT(*) AS total FROM ($select $query) AS z");
      $total = (int)$total->total;
      $list = u::query("$select $query ORDER BY status DESC, student_name ASC $limit");
      $data->contracts = $list;
      $data->search = $search;
      $data->available = $total_available;
      $data->class_dates = $classdates;
      $data->duration = $pagination->limit * 10;
      $data->pagination = apax_get_pagination($pagination, $total);
    }
    return $response->formatResponse($code, $data);
  }

  public function loadprogram(Request $request, $branch_id, $semester_id){
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $data = [];
      $code = APICode::SUCCESS;
      $user_id = $session->id;
      $role_id = $session->role_id;
      $branches = $branch_id ? $branch_id : $session->branches_ids;
      $where = "id > 0 AND status > 0 AND branch_id IN (0,$branches) AND semester_id = $semester_id";
      $query = "SELECT id, id AS item_id, 'program' AS item_type, `name` AS `text`, parent_id, 'fa fa-folder' AS icon FROM programs WHERE $where
                  UNION ALL
                  SELECT CONCAT(999, c.id) AS id, c.id AS item_id, 'class' AS item_type, c.cls_name AS `text`, c.program_id AS parent_id, 'fa fa-file-text-o' AS icon
                  FROM classes AS c INNER JOIN programs AS p ON c.program_id = p.id
                  WHERE c.cls_iscancelled = 'no' AND p.status > 0 AND (c.cm_id IS NOT NULL AND c.cm_id > 0) AND p.branch_id IN ($branches) AND p.semester_id = $semester_id";
      $class = DB::select(DB::raw($query));
      if (count($class)) {
        foreach ($class as $item) {
          $item->value = $item->id;
          $item->opened = true;
          $item->selected = false;
          $item->disabled = false;
          $item->loading = false;
          $item->children = [];
        }
      }
    }
    return $response->formatResponse($code, $data);
  }

  public function loadClassExtendInfo(Request $request, $class_id) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $class_id = (int)$class_id;
      $data = (Object)[];
      $class_info = u::first("SELECT
          c.cls_id AS class_id,
          c.product_id,
          c.program_id,
          c.cls_name AS class_name,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          IF(((c.cls_enddate < CURDATE()) OR (c.cls_iscancelled = 'yes')), 0, 1) as can_update,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          c.brch_id AS brch_id,
          c.branch_id as branch_id,
          CONCAT(u.full_name, ' - ', u.username) AS cm_name,
          u.status AS cm_status,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', COALESCE(t.avatar, 'avatar.png'), '#', t.ins_name) SEPARATOR '@ ') FROM teachers AS t
            LEFT JOIN `sessions` AS s ON s.teacher_id = t.id WHERE s.class_id = $class_id) AS teachers_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', r.room_name) SEPARATOR '@ ') FROM rooms AS r
            LEFT JOIN `sessions` AS s ON s.room_id = r.id WHERE s.class_id = $class_id) AS rooms_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', s.class_day) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN classes AS c ON s.class_id = c.id WHERE s.class_id = $class_id) AS weekdays,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', f.start_time, '-', f.end_time) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN shifts AS f ON s.shift_id = f.id WHERE s.class_id = $class_id) AS shifts_name
        FROM classes AS c
          LEFT JOIN users AS u ON c.cm_id = u.id
        WHERE c.id = $class_id");
      $students_list = u::query("SELECT
            c.id AS id,
            c.student_id,
            s.name AS student_name,
            s.nick AS student_nick,
            s.stu_id AS lms_id,
            t.name AS tuition_fee_name,
            t.receivable AS tuition_fee_price,
            t.`session` AS tuition_fee_sessions,
            cl.cls_name AS class,
            c.start_date AS start_date,
            c.end_date AS end_date,
            c.total_sessions AS total_sessions,
            c.bill_info,
            p.method AS payment_method,
            c.must_charge, 
            c.type AS customer_type,
            c.total_charged AS charged_total,
            IF(c.status = 6 AND c.enrolment_last_date <= CURDATE(), 1, 0) as withdraw,
            IF(c.status = 6 AND c.enrolment_last_date <= CURDATE(), 2, IF(c.status != 5, 3, 1)) ordering,
            c.enrolment_real_sessions AS available_sessions,
            c.status AS enrolment_status,
            c.enrolment_last_date,
            CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
            CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
            u3.hrm_id AS ec_leader_id
          FROM contracts AS c
            LEFT JOIN students AS s ON c.student_id = s.id
            LEFT JOIN payment AS p ON c.payment_id = p.id
            LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
            LEFT JOIN classes AS cl ON c.class_id = cl.id
            LEFT JOIN users AS u1 ON c.ec_id = u1.id
            LEFT JOIN users AS u2 ON c.cm_id = u2.id
            LEFT JOIN users AS u3 ON c.ec_leader_id = u3.id
          WHERE c.class_id = $class_id AND c.enrolment_start_date <= c.enrolment_end_date ORDER BY ordering");
      $count_students = u::first("SELECT COUNT(*) AS total 
        FROM (SELECT c.id AS total FROM contracts AS c
          LEFT JOIN students AS s ON ecstudent_id = s.id
        WHERE c.class_id = $class_id
          AND c.enrolment_last_date >= CURDATE() AND c.status = 1
          AND c.enrolment_last_date >= c.enrolment_start_date GROUP BY c.student_id) AS a");
      $total_students = $count_students->total;
      $teachers = [];
      $weekdays = [];
      $namrooms = [];
      $nashifts = [];
      $sessions = (Object)[];
      $weekdays_text = '';
      $namrooms_text = '';
      $nashifts_text = '';
      $teachers_text = '';
      $displayed = [];
      if ($class_info->weekdays) {
        $weekdays = explode('@', $class_info->weekdays);
        if ($weekdays) {
          $i = 0;
          foreach ($weekdays as $weekday) {
            $weekday_info = explode('|', $weekday);
            $i++;
            if ($weekday_info) {
              $sid = "session_$weekday_info[0]";
              $inf = $weekday_info[1];
              $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
              $display = apax_ada_get_weekday($inf);
              if (!in_array(md5($display), $displayed)) {
                $sessions->$sid->weekday = apax_ada_get_weekday($inf);
                $sessions->$sid->information = apax_ada_get_weekday($inf);
                $weekdays_text.= $display;
                $displayed[] = md5($display);
                if ($i < count($weekdays)) {
                  $weekdays_text.= ', ';
                }
              }
            }
          }
        }
      }
      if ($class_info->shifts_name) {
        $nashifts = explode('@', $class_info->shifts_name);
        if ($nashifts) {
          $i = 0;
          foreach ($nashifts as $nashift) {
            $nashift_info = explode('|', $nashift);
            $i++;
            if ($nashift_info) {
              $sid = "session_$nashift_info[0]";
              $inf = $nashift_info[1];
              $inf = explode('-', $inf);
              $sta = $inf[0];
              $end = $inf[1];
              $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
              $lab = substr($sta, 0, 5).'~'.substr($end, 0, 5);
              $sessions->$sid->shift = $lab;
              $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '.$lab : $lab;
              $nashifts_text.= $lab;
              $displayed[] = md5($lab);
              if ($i < count($nashifts)) {
                $nashifts_text.= ', ';
              }
            }
          }
        }
      }
      if ($class_info->rooms_name) {
        $namrooms = explode('@', $class_info->rooms_name);
        if ($namrooms) {
          $i = 0;
          foreach ($namrooms as $namroom) {
            $namroom_info = explode('|', $namroom);
            $i++;
            if ($namroom_info) {
              $sid = "session_$namroom_info[0]";
              $inf = $namroom_info[1];
              $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
              if (!in_array(md5($inf), $displayed)) {
                $sessions->$sid->room = "Room: $inf";
                $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '."Room: $inf" : "Room: $inf";
                $namrooms_text.= $inf;
                $displayed[] = md5($inf);
                if ($i < count($namrooms)) {
                  $namrooms_text.= ', ';
                }
              }
            }
          }
        }
      }
      if ($class_info->teachers_name) {
        $teachers = explode('@', $class_info->teachers_name);
        if ($teachers) {
          $i = 0;
          foreach ($teachers as $teacher) {
            $teacher_info = explode('|', $teacher);
            $i++;
            if ($teacher_info) {
              $sid = "session_$teacher_info[0]";
              $inf = $teacher_info[1];
              $inf = explode('#', $inf);
              $ava = $inf[0];
              $ten = $inf[1];
              $lab = md5($ava) == md5('avatar.png') ? '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/avatar.png"/> '.$ten : '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/teachers/'.$ava.'"/> '.$ten;
              $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
              if (!in_array(md5($lab), $displayed)) {
                $sessions->$sid->teacher_avatar = $ava;
                $sessions->$sid->teacher_name = $ten;
                $sessions->$sid->teacher_label = $lab;
                $teachers_text.= $lab;
                $displayed[] = md5($lab);
                if ($i < count($teachers)) {
                  $teachers_text.= ', ';
                }
              }
            }
          }
        }
      }
      $time_place = '';
      if ($sessions) {
        $i = 0;
        foreach ($sessions as $s => $o) {
          if (isset($o->information) && strlen($o->information) > 12) {
            $i++;
            $time_place.= $o->information;
            if ($i < count((array)$sessions)) {
              $time_place.= '<br/>';
            }
          }
        }
      }
      // if (count($students_list)) {
      // $classdays = u::getClassDays($class_id);
      // $class_info = u::first("SELECT product_id FROM classes WHERE id = $class_id");
      // $product_id = $class_info->product_id;
      // $holidays = u::getPublicHolidays($class_id, 0, $product_id);
      //   foreach ($students_list as $student) {
      //     $information = u::getRealSessions($student->available_sessions, $classdays, $holidays);
      //     $student->available_sessions = $information && isset($information->total) ? $information->total : 0;
      //     $student->last_date = $information && isset($information->end_date) ? $information->end_date : '';
      //   }
      // }
      $class_info->total_students = $total_students;
      $class_info->sessions = $sessions;
      $class_info->class_time = "$class_info->class_start_date ~ $class_info->class_end_date";
      $class_info->teachers_name = $teachers_text;
      $class_info->shifts_name = $nashifts_text;
      $class_info->rooms_name = $namrooms_text;
      $class_info->weekdays = $weekdays_text;
      $class_info->time_and_place = $time_place;
      $data->class = $class_info;
      $data->students = $students_list;
      $code = APICode::SUCCESS;
    }
    return $response->formatResponse($code, $data);
  }

  public function getClassExtendInfo(Request $request, $class_id) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $class_id = (int)$class_id;
      $data = (Object)[];
      $class_info = u::first("SELECT
          c.cls_id AS class_id,
          c.product_id,
          c.program_id,
          c.cls_name AS class_name,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          c.brch_id AS brch_id,
          CONCAT(u.full_name, ' - ', u.username) AS cm_name,
          u.status AS cm_status,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', COALESCE(t.avatar, 'avatar.png'), '#', t.ins_name) SEPARATOR '@ ') FROM teachers AS t
            LEFT JOIN `sessions` AS s ON s.teacher_id = t.id WHERE s.class_id = $class_id) AS teachers_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', r.room_name) SEPARATOR '@ ') FROM rooms AS r
            LEFT JOIN `sessions` AS s ON s.room_id = r.id WHERE s.class_id = $class_id) AS rooms_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', s.class_day) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN classes AS c ON s.class_id = c.id WHERE s.class_id = $class_id) AS weekdays,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', f.start_time, '-', f.end_time) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN shifts AS f ON s.shift_id = f.id WHERE s.class_id = $class_id) AS shifts_name
        FROM classes AS c
          LEFT JOIN users AS u ON c.cm_id = u.id
        WHERE c.id = $class_id");
      $students_list = u::query("SELECT
            c.student_id AS student_id,
            s.name AS student_name,
            s.nick AS student_nick,
            s.stu_id AS lms_id
          FROM contracts AS e
            LEFT JOIN students AS s ON c.student_id = s.id
            LEFT JOIN classes AS cl ON c.class_id = cl.id
          WHERE c.class_id = $class_id AND c.enrolment_start_date <= c.enrolment_end_date AND c.status = 6");
      $count_students = u::first("SELECT COUNT(*) AS total FROM (SELECT c.id AS total FROM contracts AS c
          LEFT JOIN students AS s ON c.student_id = s.id
        WHERE c.class_id = $class_id
          AND c.enrolment_last_date >= CURDATE() AND c.status = 6
          AND c.enrolment_last_date >= c.enrolment_start_date GROUP BY c.student_id) AS a");
      $total_students = $count_students->total;
      $teachers = [];
      $weekdays = [];
      $namrooms = [];
      $nashifts = [];
      $sessions = (Object)[];
      $weekdays_text = '';
      $namrooms_text = '';
      $nashifts_text = '';
      $teachers_text = '';
      $displayed = [];
      if ($class_info->weekdays) {
        $weekdays = explode('@', $class_info->weekdays);
        if ($weekdays) {
          $i = 0;
          foreach ($weekdays as $weekday) {
            $weekday_info = explode('|', $weekday);
            $i++;
            if ($weekday_info) {
              $sid = "session_$weekday_info[0]";
              $inf = $weekday_info[1];
              $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
              $display = apax_ada_get_weekday($inf);
              if (!in_array(md5($display), $displayed)) {
                $sessions->$sid->weekday = apax_ada_get_weekday($inf);
                $sessions->$sid->information = apax_ada_get_weekday($inf);
                $weekdays_text.= $display;
                $displayed[] = md5($display);
                if ($i < count($weekdays)) {
                  $weekdays_text.= ', ';
                }
              }
            }
          }
        }
      }
      if ($class_info->shifts_name) {
        $nashifts = explode('@', $class_info->shifts_name);
        if ($nashifts) {
          $i = 0;
          foreach ($nashifts as $nashift) {
            $nashift_info = explode('|', $nashift);
            $i++;
            if ($nashift_info) {
              $sid = "session_$nashift_info[0]";
              $inf = $nashift_info[1];
              $inf = explode('-', $inf);
              $sta = $inf[0];
              $end = $inf[1];
              $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
              $lab = substr($sta, 0, 5).'~'.substr($end, 0, 5);
              $sessions->$sid->shift = $lab;
              $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '.$lab : $lab;
              $nashifts_text.= $lab;
              $displayed[] = md5($lab);
              if ($i < count($nashifts)) {
                $nashifts_text.= ', ';
              }
            }
          }
        }
      }
      if ($class_info->rooms_name) {
        $namrooms = explode('@', $class_info->rooms_name);
        if ($namrooms) {
          $i = 0;
          foreach ($namrooms as $namroom) {
            $namroom_info = explode('|', $namroom);
            $i++;
            if ($namroom_info) {
              $sid = "session_$namroom_info[0]";
              $inf = $namroom_info[1];
              $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
              if (!in_array(md5($inf), $displayed)) {
                $sessions->$sid->room = "Room: $inf";
                $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '."Room: $inf" : "Room: $inf";
                $namrooms_text.= $inf;
                $displayed[] = md5($inf);
                if ($i < count($namrooms)) {
                  $namrooms_text.= ', ';
                }
              }
            }
          }
        }
      }
      if ($class_info->teachers_name) {
        $teachers = explode('@', $class_info->teachers_name);
        if ($teachers) {
          $i = 0;
          foreach ($teachers as $teacher) {
            $teacher_info = explode('|', $teacher);
            $i++;
            if ($teacher_info) {
              $sid = "session_$teacher_info[0]";
              $inf = $teacher_info[1];
              $inf = explode('#', $inf);
              $ava = $inf[0];
              $ten = $inf[1];
              $lab = md5($ava) == md5('avatar.png') ? '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/avatar.png"/> '.$ten : '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/teachers/'.$ava.'"/> '.$ten;
              $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
              if (!in_array(md5($lab), $displayed)) {
                $sessions->$sid->teacher_avatar = $ava;
                $sessions->$sid->teacher_name = $ten;
                $sessions->$sid->teacher_label = $lab;
                $teachers_text.= $lab;
                $displayed[] = md5($lab);
                if ($i < count($teachers)) {
                  $teachers_text.= ', ';
                }
              }
            }
          }
        }
      }
      $time_place = '';
      if ($sessions) {
        $i = 0;
        foreach ($sessions as $s => $o) {
          if (isset($o->information) && strlen($o->information) > 12) {
            $i++;
            $time_place.= $o->information;
            if ($i < count((array)$sessions)) {
              $time_place.= '<br/>';
            }
          }
        }
      }
      // if (count($students_list)) {
      // $classdays = u::getClassDays($class_id);
      // $class_info = u::first("SELECT product_id FROM classes WHERE id = $class_id");
      // $product_id = $class_info->product_id;
      // $holidays = u::getPublicHolidays($class_id, 0, $product_id);
      //   foreach ($students_list as $student) {
      //     $information = u::getRealSessions($student->available_sessions, $classdays, $holidays);
      //     $student->available_sessions = $information && isset($information->total) ? $information->total : 0;
      //     $student->last_date = $information && isset($information->end_date) ? $information->end_date : '';
      //   }
      // }
      $class_info->total_students = $total_students;
      $class_info->sessions = $sessions;
      $class_info->class_time = "$class_info->class_start_date ~ $class_info->class_end_date";
      $class_info->teachers_name = $teachers_text;
      $class_info->shifts_name = $nashifts_text;
      $class_info->rooms_name = $namrooms_text;
      $class_info->weekdays = $weekdays_text;
      $class_info->time_and_place = $time_place;
      $data->class = $class_info;

      $students = [];
      if(!empty($students_list)){
        foreach ($students_list as $s){
          $students[$s->student_id] = $s;
        }
      }
      $data->students = $students;
      $code = APICode::SUCCESS;
    }
    return $response->formatResponse($code, $data);
  }

  public function loadClassInfo(Request $request, $class_id) {
    $data = null;
    $code = APICode::PERMISSION_DENIED;
    $response = new Response();
    if ($session = $request->users_data) {
      $class_id = (int)$class_id;
      $data = (Object)[];
      $class_info = u::first("SELECT
          c.id,
          c.product_id,
          c.program_id,
          c.cls_name AS class_name,
          c.cls_startdate AS class_start_date,
          c.cls_enddate AS class_end_date,
          c.cls_iscancelled AS class_is_cancelled,
          c.max_students AS class_max_students,
          c.cm_id AS cm_id,
          (select name from `programs` where id = c.program_id) level_name,
          u.status AS cm_status,
          c.semester_id,
          CONCAT(u.full_name, ' - ', u.username) AS cm_name,
          (SELECT ins_name FROM teachers WHERE user_id = c.teacher_id) AS teachers_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', r.room_name) SEPARATOR '@ ') FROM rooms AS r
            LEFT JOIN `sessions` AS s ON s.room_id = r.id WHERE s.class_id = $class_id) AS rooms_name,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', s.class_day) SEPARATOR '@ ') FROM `sessions` AS s
            LEFT JOIN classes AS c ON s.class_id = c.id WHERE s.class_id = $class_id) AS weekdays,
          (SELECT GROUP_CONCAT(CONCAT(s.id, '|', f.start_time, '-', f.end_time) SEPARATOR ', ') FROM `sessions` AS s
            LEFT JOIN shifts AS f ON s.shift_id = f.id WHERE s.class_id = $class_id) AS shifts_name
        FROM classes AS c
          LEFT JOIN users AS u ON c.cm_id = u.id
        WHERE c.id = $class_id");

      $semester = u::first("SELECT end_date, status FROM semesters WHERE id = $class_info->semester_id");
      $class_info->semester_end_date = $semester->end_date;
      $class_info->semester_status = $semester->status;

      $classdays = u::getClassDays($class_id);
      $holidays = u::getPublicHolidays($class_id, 0, $class_info->product_id);
      $students_list = u::query("SELECT
            c.id AS enrolment_id,
            c.student_id,
            c.id AS contract_id, 
            s.name AS student_name,
            s.nick AS student_nick,
            s.crm_id AS crm_id,
            s.accounting_id,
            t.name AS tuition_fee_name,
            t.receivable AS tuition_fee_price,
            t.`session` AS tuition_fee_sessions,
            cl.cls_name AS class,
            c.enrolment_start_date AS start_date,
            c.enrolment_end_date AS end_date,
            c.total_sessions AS total_sessions,
            c.bill_info,
            COALESCE(p.method, 0) AS payment_method,
            c.type AS customer_type,
            COALESCE(c.debt_amount, c.must_charge - c.total_charged) AS debt_amount,
            c.total_charged AS charged_total,
            IF(c.status = 6 AND c.enrolment_last_date <= CURDATE(), 1, 0) as withdraw,
            c.enrolment_real_sessions AS available_sessions,
            c.status AS enrolment_status,
            c.enrolment_last_date,
            c.enrolment_last_date,
            CONCAT(u1.full_name, ' - ', u1.username) AS ec_name,
            CONCAT(u2.full_name, ' - ', u2.username) AS cm_name,
            (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE hrm_id = u1.superior_id LIMIT 1) AS ec_leader_name
          FROM contracts AS c
            LEFT JOIN students AS s ON c.student_id = s.id
            LEFT JOIN payment AS p ON c.payment_id = p.id
            LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
            LEFT JOIN classes AS cl ON c.class_id = cl.id
            LEFT JOIN users AS u1 ON c.ec_id = u1.id
            LEFT JOIN users AS u2 ON c.cm_id = u2.id
          WHERE c.class_id = $class_id AND c.enrolment_last_date >= CURDATE() AND c.status = 1
            AND c.enrolment_start_date <= c.enrolment_end_date AND c.enrolment_last_date >= c.enrolment_start_date GROUP BY c.student_id");
      $count_students = u::first("SELECT COUNT(*) AS total FROM (SELECT c.id AS total FROM contracts AS c
          LEFT JOIN students AS s ON c.student_id = s.id
        WHERE c.class_id = $class_id 
          AND c.status = 6
          AND c.enrolment_last_date >= CURDATE() 
          AND c.enrolment_start_date <= c.enrolment_end_date 
          AND c.enrolment_last_date >= c.enrolment_start_date 
          GROUP BY c.student_id) AS a");
      $total_students = $count_students->total;
      $teachers = [];
      $weekdays = [];
      $namrooms = [];
      $nashifts = [];
      $sessions = (Object)[];
      $weekdays_text = '';
      $namrooms_text = '';
      $nashifts_text = '';
      $teachers_text = '';
      $displayed = [];
      if ($class_info->weekdays) {
        $weekdays = explode('@', $class_info->weekdays);
        if ($weekdays) {
          $i = 0;
          foreach ($weekdays as $weekday) {
            $weekday_info = explode('|', $weekday);
            $i++;
            if ($weekday_info) {
              $sid = "session_$weekday_info[0]";
              $inf = $weekday_info[1];
              $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
              $display = apax_ada_get_weekday($inf);
              if (!in_array(md5($display), $displayed)) {
                $sessions->$sid->weekday = apax_ada_get_weekday($inf);
                $sessions->$sid->information = apax_ada_get_weekday($inf);
                $weekdays_text.= $display;
                $displayed[] = md5($display);
                if ($i < count($weekdays)) {
                  $weekdays_text.= ', ';
                }
              }
            }
          }
        }
      }
      if ($class_info->shifts_name) {
        $nashifts = explode('@', $class_info->shifts_name);
        if ($nashifts) {
          $i = 0;
          foreach ($nashifts as $nashift) {
            $nashift_info = explode('|', $nashift);
            $i++;
            if ($nashift_info) {
              $sid = "session_$nashift_info[0]";
              $inf = $nashift_info[1];
              $inf = explode('-', $inf);
              $sta = $inf[0];
              $end = $inf[1];
              $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
              $lab = substr($sta, 0, 5).'~'.substr($end, 0, 5);
              $sessions->$sid->shift = $lab;
              $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '.$lab : $lab;
              $nashifts_text.= $lab;
              $displayed[] = md5($lab);
              if ($i < count($nashifts)) {
                $nashifts_text.= ', ';
              }
            }
          }
        }
      }
      if ($class_info->rooms_name) {
        $namrooms = explode('@', $class_info->rooms_name);
        if ($namrooms) {
          $i = 0;
          foreach ($namrooms as $namroom) {
            $namroom_info = explode('|', $namroom);
            $i++;
            if ($namroom_info && count($namroom_info)) {
              $sid = "session_$namroom_info[0]";
              $inf = $namroom_info[1];
              $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
              if (!in_array(md5($inf), $displayed)) {
                $sessions->$sid->room = "Room: $inf";
                $sessions->$sid->information = property_exists($sessions->$sid, 'information') ? $sessions->$sid->information.' '."Room: $inf" : "Room: $inf";
                $namrooms_text.= $inf;
                $displayed[] = md5($inf);
                if ($i < count($namrooms)) {
                  $namrooms_text.= ', ';
                }
              }
            }
          }
        }
      }
      // if ($class_info->teachers_name) {
      //   $teachers = explode('@', $class_info->teachers_name);
      //   if ($teachers) {
      //     $i = 0;
      //     foreach ($teachers as $teacher) {
      //       $teacher_info = explode('|', $teacher);
      //       $i++;
      //       if ($teacher_info) {
      //         $sid = "session_$teacher_info[0]";
      //         $inf = $teacher_info[1];
      //         $inf = explode('#', $inf);
      //         $ava = $inf[0];
      //         $ten = $inf[1];
      //         $lab = md5($ava) == md5('avatar.png') ? '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/avatar.png"/> '.$ten : '<img style="border-radius:50%;width:30px;height: 30px;" src="./static/img/avatars/teachers/'.$ava.'"/> '.$ten;
      //         $sessions->$sid = $sessions && property_exists($sessions, $sid) ? $sessions->$sid : (Object)[];
      //         if (!in_array(md5($lab), $displayed)) {
      //           $sessions->$sid->teacher_avatar = $ava;
      //           $sessions->$sid->teacher_name = $ten;
      //           $sessions->$sid->teacher_label = $lab;
      //           $teachers_text.= $lab;
      //           $displayed[] = md5($lab);
      //           if ($i < count($teachers)) {
      //             $teachers_text.= ', ';
      //           }
      //         }
      //       }
      //     }
      //   }
      // }
      $time_place = '';
      if ($sessions) {
        $i = 0;
        foreach ($sessions as $s => $o) {
          if (isset($o->information) && strlen($o->information) > 12) {
            $i++;
            $time_place.= $o->information;
            if ($i < count((array)$sessions)) {
              $time_place.= '<br/>';
            }
          }
        }
      }

      $schedules = Schedule::getSchedules($class_id, true);

      $class_info->total_students = $total_students;
      $class_info->sessions = $sessions;
      $class_info->class_time = substr($class_info->class_start_date,0,10) ." ~ ". substr($class_info->class_end_date,0,10);
      // $class_info->teachers_name = $teachers_text;
      $class_info->shifts_name = $nashifts_text;
      $class_info->rooms_name = $namrooms_text;
      $class_info->weekdays = $weekdays_text;
      $class_info->time_and_place = $time_place;
      $schedulesNew = [];
      foreach ($schedules as $schedule){
         if($schedule->cjrn_classdate >= date('Y-m-d', strtotime('today - 30 days'))){
             $schedulesNew[] = $schedule;
         }
      }
      $class_info->schedules = $schedulesNew;
      $data->class = $class_info;
      $data->students = $students_list;
      $code = APICode::SUCCESS;
    }
    return $response->formatResponse($code, $data);
  }

    public function getStudents(Request $request, $class_id, $date){
        $response = new Response();
        $code = APICode::SUCCESS;

        $query = "SELECT c.id AS enrolment_id,
                    s.id AS student_id,
                    s.name AS student_name,
                    s.crm_id AS crm_id,
                    s.accounting_id AS student_accounting_id,
                    s.nick AS student_nick,
                    s.gud_name1,
                    s.gud_mobile1,
                    c.class_id,
                    c.enrolment_start_date,
                    c.enrolment_end_date,
                    c.enrolment_last_date,
                    c.id AS contract_id,
                    c.branch_id,
                    c.product_id,
                    c.program_id,
                    c.type,
                    c.total_sessions,
                    c.real_sessions as enrolment_real_sessions,
                    IF(a.id, a.type, -1) AS attendance_type,
                    a.status AS attendance_status,
                    a.reason,
                    a.note,
                    a.attendance_date,
                    IF(a.homework,1,0) AS homework,
                    a.id AS attendance_id
              FROM contracts c
              LEFT JOIN students s ON c.student_id = s.id
              LEFT JOIN attendances a ON a.enrolment_id = c.id
              AND a.attendance_date = '$date'
              WHERE 
                s.status >0 AND  
                c.enrolment_last_date >= '$date'
                AND c.class_id = $class_id AND c.count_recharge >= 0
                AND c.`id` IN (SELECT c.id AS total FROM contracts AS c
                LEFT JOIN students AS s ON c.student_id = s.id
                WHERE c.class_id = $class_id 
                  AND c.status = 6
                  AND c.enrolment_last_date >= CURDATE() 
                  AND c.enrolment_start_date <= c.enrolment_end_date 
                  AND c.enrolment_last_date >= c.enrolment_start_date 
                  GROUP BY c.student_id)
                ";

        $students = u::query($query);
        $holidays = u::getPublicHolidays($class_id);
        $classdays = u::getClassDays($class_id);
        foreach ($students as $key => $student){
            $students[$key]->holydays = $holidays;
            $students[$key]->classdays = $classdays;
        }

        $students = $this->attendanceForStudentReserved($students,$date);
        return $response->formatResponse($code, $students);
    }

  public function getStudentsNew(Request $request, $class_id, $date){
    $response = new Response();
    $code = APICode::SUCCESS;

    $query = "SELECT c.id AS enrolment_id,
                    s.id AS student_id,
                    s.name AS student_name,
                    s.crm_id AS crm_id,
                    s.accounting_id AS student_accounting_id,
                    s.nick AS student_nick,
                    s.gud_name1,
                    s.gud_mobile1,
                    c.class_id,
                    c.enrolment_start_date,
                    c.enrolment_end_date,
                    c.enrolment_last_date,
                    c.id AS contract_id,
                    c.branch_id,
                    c.product_id,
                    c.program_id,
                    c.type,
                    c.total_sessions,
                    c.real_sessions as enrolment_real_sessions,
                    (select name from `school_grades` where id = s.school_grade and status = 1) school_grade_name,
                    IF(a.id, a.type, -1) AS attendance_type,
                    a.status AS attendance_status,
                    a.reason,
                    a.note,
                    a.attendance_date,
                    IF(a.homework,1,0) AS homework,
                    a.id AS attendance_id,
                    LEFT(s.date_of_birth,4) AS date_of_birth
              FROM contracts c
              LEFT JOIN students s ON c.student_id = s.id
              LEFT JOIN attendances a ON a.enrolment_id = c.id
              AND a.attendance_date = '$date'
              WHERE 
                s.status >0 AND 
                c.enrolment_last_date >= '$date'
                AND c.class_id = $class_id AND c.count_recharge >= 0
                AND c.`id` IN (SELECT c.id AS total FROM contracts AS c
                LEFT JOIN students AS s ON c.student_id = s.id
                WHERE c.class_id = $class_id 
                  AND c.status = 6
                  AND c.enrolment_last_date >= CURDATE() 
                  AND c.enrolment_start_date <= c.enrolment_end_date 
                  AND c.enrolment_last_date >= c.enrolment_start_date 
                  GROUP BY c.student_id)
                ";

    $students = u::query($query);
    foreach ($students as $key => $student){
    }
      $currentM = date("Y-m",strtotime($request->m));
      //$currentM = $request->m;
      $startMonth =    date("Y-m-01",strtotime($currentM." -2 Months"));
      $endMonth =    date("Y-m-t",strtotime($currentM." 0 Months")); //old 1 months

      $attendances  = $this->getCjrnClassdateNew($startMonth,$endMonth,$class_id);

      $preTwoMonth =    date("Y-m",strtotime($currentM." -2 Months"));
      $preOneMonth =    date("Y-m",strtotime($currentM." -1 Months"));
      $preZeroMonth =    $currentM;
//      $nextMonth =    date("Y-m",strtotime($currentM." 1 Months"));
      $preTwoMonthData =  $this->getCjrnClassdate($preTwoMonth,$class_id);
      $preOneMonthData =  $this->getCjrnClassdate($preOneMonth,$class_id);
      $preZeroMonthData =  $this->getCjrnClassdate($preZeroMonth,$class_id);
//      $preNextMonthData =  $this->getCjrnClassdate($nextMonth,$class_id);
      $months = [
          $preTwoMonthData,
          $preOneMonthData,
          $preZeroMonthData,
//          $preNextMonthData
          ];
      $days = [];
      $dates = [];
      $days = array_merge(
          $days,
          $preTwoMonthData['days'],
          $preOneMonthData['days'],
          $preZeroMonthData['days'],
//          $preNextMonthData['days']
      );

      $dates = array_merge(
          $dates,
          $preTwoMonthData['dates'],
          $preOneMonthData['dates'],
          $preZeroMonthData['dates'],
///          $preNextMonthData['dates']
      );

      $reserveList = $this->attendanceForStudentReserved($class_id,$students,$date);
      $studentsId = [];
      $studentsList = [];
      $studentSum = [];
      foreach ($students as $student){
          $student->attendances = $this->getAttendanceByStudent($dates, $student->student_id,$attendances,$student->enrolment_start_date,$student->enrolment_last_date);
          $studentSum[] = $this->sumAttendanceTotal($class_id,$student->student_id);
          $studentsList[] = $student;
          $studentsId[] = $student->student_id;
      }

      $sums = [];
      foreach ($dates as $date){
          $sums[] = $this->sumAttendance($date,$attendances);
      }
    return $response->formatResponse($code, [$studentsList,$months,$days,$dates,$sums,sizeof($dates),$studentSum,$studentsId]);
  }

    private static function sumAttendanceTotal($class_id,$studentId){
        $total = 0;
        $data = u::first("SELECT SUM(1) as total FROM `attendances` atd WHERE (atd.`status` = 1 OR atd.`status_make_up` = 1) AND atd.`class_id` = $class_id AND atd.`student_id` = $studentId");
        return $data ? $data->total : $total;
    }

    private static function sumAttendance($date,$attendances){
       $total = 0;
       if (isset($attendances[$date])){
           foreach ($attendances[$date] as $att){
               if ($att["status"] == 1 || $att["status_make_up"])
                   $total +=1;
           }
       }
       return ['sum'=>$total,'d'=>$date];
    }

  private static function getAttendanceByStudent($dates, $studentId,$attendances,$start_date,$last_date){
      $list = [];
      foreach ($dates as $date){
          if ($date >= $start_date && $date <= $last_date){
              $list[] = [
                  'status'=> isset($attendances[$date][$studentId]['status']) ? $attendances[$date][$studentId]['status'] : '',
                  'status_make_up'=> isset($attendances[$date][$studentId]['status_make_up']) ? $attendances[$date][$studentId]['status_make_up'] : '',
                  'date_make_up'=> isset($attendances[$date][$studentId]['date_make_up']) ? $attendances[$date][$studentId]['date_make_up'] : '',
                  'note'=> isset($attendances[$date][$studentId]['note']) ? $attendances[$date][$studentId]['note'] : '',
                  'date'=> $date
              ];
          }
          else{
              $list[] = [
                  'status'=> isset($attendances[$date][$studentId]['status']) ? $attendances[$date][$studentId]['status'] : -1,
                  'date'=> $date
              ];
          }
      }

      return $list;

  }

  private static function getAttendanceByStudentSum($data,$sid = 0){
      $sum = 0;
      foreach ($data as $item){
          if ($sid == 0){
              if ((isset($item['status']) && $item['status'] == 1) || (isset($item['status_make_up']) && $item['status_make_up'] == 1)){
                  $sum += 1;
              }
          }
          else{
              if ((isset($item[$sid]['status']) && $item[$sid]['status'] == 1) || (isset($item[$sid]['status_make_up']) && $item[$sid]['status_make_up'] == 1)){
                  $sum += 1;
              }
          }
      }
      return $sum;
  }

    private function getCjrnClassdate($month, $classId= 0){
        $query = "SELECT cjrn_classdate FROM `schedules` WHERE class_id = $classId AND cjrn_classdate LIKE  '$month%' ORDER BY cjrn_classdate ASC";
        $schedule = u::query($query);
        $dates = [];
        $days = [];
        if ($schedule){
            foreach ($schedule as $sh){
                $dates[] = $sh->cjrn_classdate;
                $day = explode('-',$sh->cjrn_classdate);
                $days[] = $day[2].'/'.$day[1];
            }
        }
        else{
            $days[] = '';
            $dates[] = '';
        }
        return [
            'd' => substr($month,5,7).'/'.substr($month,0,4),
            'dates' => $dates,
            'days' => $days,
            'size' => sizeof($dates)
        ];
    }

    private function getCjrnClassdateNew($startMonth,$endMonth, $classId= 0, $arrayDate = null){
        $query = "SELECT att.`attendance_date`,att.`student_id`,att.`status`, att.`status_make_up`,att.`date_make_up`,att.`note`
                FROM `attendances` att WHERE att.`class_id` = $classId 
                AND att.`attendance_date` >= '$startMonth' AND att.`attendance_date` <= '$endMonth'";

        if ($arrayDate){
            $query = "SELECT att.`attendance_date`,att.`student_id`,att.`status`, att.`status_make_up`,att.`date_make_up`,att.`note` 
                FROM `attendances` att WHERE att.`class_id` = $classId 
                AND att.`attendance_date` IN ($arrayDate) ";
        }
        $data = u::query($query);
        $attendanceStatus = [];
        if ($data){
            foreach ($data as $sh){
                $attendanceStatus[$sh->attendance_date][$sh->student_id]['status'] = $sh->status;
                $attendanceStatus[$sh->attendance_date][$sh->student_id]['status_make_up'] = $sh->status_make_up;
                $attendanceStatus[$sh->attendance_date][$sh->student_id]['date_make_up'] = $sh->date_make_up;
                $attendanceStatus[$sh->attendance_date][$sh->student_id]['note'] = $sh->note;
            }
        }

        return $attendanceStatus;
    }
  /**
   * Điểm danh những học sinh bảo lưu dữ chỗ
   * @param $students
   * @param $date
   * @return array|null
   */
  private function getListReservesList($reserves,$studentId){
     // echo $studentId.':';
        $list = null;
        foreach ($reserves as $reserve){
            if ($reserve->student_id == $studentId){
                $list[] =  [$reserve->start_date,$reserve->end_date];
            }

        }
        return $list;
  }
  public function attendanceForStudentReserved($class_id,$students){
    if(empty($students)){
      return [];
    }
    $data = [];
      $indexStudentByIds = array_column($students,"student_id" );
      $reserveModel = new Reserve();
      $reserves = $reserveModel->getReservedByStudentId($indexStudentByIds, now(), 1,$class_id);
      foreach ($indexStudentByIds as $studentId){
          $data[$studentId] = $this->getListReservesList($reserves, $studentId);
      }
      return $data;
        /*
        $studentsNotAttendance = array_filter($students, function ($student){
          return $student->attendance_status !== Attendances::STATUS_VANG_MAT_DO_BAO_LUU_DU_CHO;
        });
        $studentIds = array_column($studentsNotAttendance, 'student_id');
        $reserveModel = new Reserve();
        $reserves = $reserveModel->getReservedByStudentId($studentIds, $date, 1);
        if(empty($reserves)) return $students;

        $attendanceModel = new Attendances();
        foreach ($reserves as $reserve){
          $attendanceModel->updateStatusAttendanceByStudentId($reserve->student_id, Attendances::STATUS_VANG_MAT_DO_BAO_LUU_DU_CHO);
          $position = array_search($reserve->student_id, $indexStudentByIds);
          if ($position !== false) {
            $students[$position]->attendance_status = Attendances::STATUS_VANG_MAT_DO_BAO_LUU_DU_CHO;
          }
        }*/
  }

  public function saveAttendances(Request $request)
  {
      $response = new Response();
      $code = APICode::SUCCESS;
      $data = null;

      $student = (object)$request->data;
      $currentUser = $request->users_data;
      if ($student->attendance_id) {
        Attendances::updateAttendances($student,$currentUser);
      } else {
        Attendances::insertAttendances($student,$currentUser);
      }

      return $response->formatResponse($code, $data);
  }


    public function saveAttendancesNew(Request $request)
    {
        $response = new Response();
        $code = APICode::SUCCESS;
        $data = null;
        if (empty($request->dates))
            return $response->formatResponse($code, $data);
        $status = $request->data;
        $sids = (array)$request->s;
        $dates = $request->dates;
        $classId = $request->class_id;
        $creatorId = $request->users_data->id;
        list($studentId, $date) = explode("_",$request->id);
        $attendanceModel = new Attendances();
        $type = isset($request->type) ? $request->type : null;
        if ($type && $type == "makeup"){
            $attendanceModel->updateStatusMakeup($classId,$status,$studentId,$date,$creatorId);
        }
        else if ($type && $type == "note"){
            $attendanceModel->updateNoteMakeup($classId,$request->note,$studentId,$date,$creatorId);
        }
        else{
            $attendanceModel->preInsertAttendances($studentId,$classId,$status,$date,$creatorId);
        }

        $arrayDate = implode(",", array_map(function($string) {
            return '"' . $string . '"';
        }, $dates));

        $sums = [];
        $sumStudent = [];
        if ($type != "note"){
            $attendances = $this->getCjrnClassdateNew(null,null, $request->class_id, $arrayDate);
            foreach ($dates as $date){
                $sums[] = $this->sumAttendance($date,$attendances);
            }

            foreach ($sids as $sid){
                $sumStudent[] = $this->sumAttendanceTotal($classId,$sid);
            }
        }
        return $response->formatResponse($code, ['sum' =>$sums,'sumStudent' =>$sumStudent]);
    }

}
