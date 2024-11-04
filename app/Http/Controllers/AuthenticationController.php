<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use App\Providers\UtilityServiceProvider as u;
use Mockery\Exception;
use App\Models\APICode;
use App\Models\Response;

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:*/
/**
 * ========================================================== *
 * --------------------- Apax ERP System --------------------
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package Apax ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */

class AuthenticationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return response()->json((object)array());
    }

    public function logout()
    {
      $done = false;
      $message = '';
      $session = u::session();
      if ($session) {
        Redis::del($session->key);
        $done = true;
        $message = 'Tạm Biệt';
      }
      return response()->json([
          'done' => $done,
          'message' => $message
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request)
    {
      $this->validate($request, [
          'username' => 'required',
          'password' => 'required'
      ]);

      $token = '';
      $rules = [];
      $rights = [];
      $main_role = null;
      $branch_ids = [];
      $roles_list = [];
      $branches_ids = [];
      $information = (object) array();
      $message = 'Thông tin đăng nhập không chính xác!';
      $ip = $request->ip();
      $agent = $request->server('HTTP_USER_AGENT');
      // $mac = shell_exec("arp -a ".escapeshellarg($request->server('REMOTE_ADDR'))." | grep -o -E '(:xdigit:{1,2}:){5}:xdigit:{1,2}'");
      // print_r($_SERVER);
      // die("MAC: ".$_SERVER['REMOTE_ADDR']);
      $username = trim($request->username);
      $password = trim($request->password);
      $user = u::first("SELECT u.*, b.name branch_name, z.name zone_name FROM users u LEFT JOIN term_user_branch t ON u.id = t.user_id 
        LEFT JOIN branches b ON t.branch_id = b.id LEFT JOIN zones z ON b.zone_id = z.id WHERE (LOWER(u.email) = '".strtolower($username)."'
        OR LOWER(u.username) = '".strtolower($username)."') AND t.status = 1");

      if ($user) {
        if (Hash::check($password, $user->password)) {
          if ($user->status == 0) {
            $information = null;
            $message = 'Tài khoản này đang bị khóa.';
          } else {
            $config = u::config();
            $superoles = isset($config->superoles) ? json_decode($config->superoles) : [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS];
            $roles = u::query("SELECT r.id AS role_id, r.name AS role, r.status AS role_status, r.functions, t.branch_id,
              b.name AS branch_name, b.zone_id, b.region_id, z.name AS zone_name FROM roles as r LEFT JOIN term_user_branch AS t ON r.id = t.role_id
              LEFT JOIN branches AS b ON t.branch_id = b.id LEFT JOIN zones AS z ON b.zone_id = z.id WHERE t.user_id = $user->id AND t.status = 1");
            if (in_array($roles[0]->role_id, $superoles)) {
              $super_admin = $roles[0];
              $roles = u::query("SELECT '$super_admin->role_id' AS role_id, '$super_admin->role' AS role, 
               '$super_admin->role_status' AS role_status, '$super_admin->functions' AS functions, id AS branch_id,
               `name` AS branch_name, zone_id, region_id FROM branches WHERE status > 0");
            }
            if ($roles) {
              foreach ($roles as $role) {
                $roles_list[] = ['id' => $role->role_id, 'role' => $role->role];
                if ($role->branch_id && !in_array($role->branch_id, $branch_ids)) {
                  $branch_ids[] = ['id'=>$role->branch_id, 'name'=>$role->branch_name, 'role'=>$role->role_id, 'title'=>$role->role];
                  $branches_ids[] = $role->branch_id;
                }
              }
              $branches_ids = implode(',', $branches_ids);
            }

            $client_info = (Object)[];
            $remote_address = $_SERVER['REMOTE_ADDR'];
            // $client_info->address = $remote_address;
            $client_info->agent = $_SERVER['HTTP_USER_AGENT'];
            $client_info->language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $roles_list = apax_ada_sort_items($roles_list);
            $main_role = $roles_list[0];
            $session = (object) array();
            $salt = md5($user->password);
            $mask = md5(json_encode($client_info).$salt);
            $time = time();
            $session->id = $user->id;
            $session->hrm_id = trim($user->hrm_id);
            $session->accounting_id = isset($user->accounting_id) ? trim($user->accounting_id) : '';
            $session->role_id = $main_role['id'];
            $session->role_name = implode(", ", array_values(array_unique(array_column(is_array($roles_list)? $roles_list: [], 'role'))));
            $session->roles_detail = $roles;
            $session->branches = $branch_ids;
            $session->branches_ids = $branches_ids;
            $session->code = "$user->id-".trim($user->hrm_id).'-'.trim($session->accounting_id);
            $session->superior_id = trim($user->superior_id);
            $session->name = trim($user->full_name);
            $session->nick = trim($user->username);
            $session->avatar = $user->avatar;
            $session->status = $user->status;
            $session->email = trim($user->email);
            $session->phone = trim($user->phone);
            $session->start_date = $user->start_date;
            $session->started = date("Y-m-d H:i:s");
            $session->branch = $user->branch_name;
            $session->zone = $user->zone_name;
            $session->roles = u::authen($roles);
            $session->ip = $ip;
            $session->mac = $mask;
            $session->salt = $salt;
            $session->agent = $agent;
            $session->client_info = $client_info;
            $session->life = (int)(3600*24);
            $session->start_at = $time;
            $session->expire_at = $time + $session->life;
            $session->first_change_password = $user->first_change_password;
            $session->last_change_password_date = $user->last_change_password_date;
            $payload = json_encode($session);
            $alias = strtoupper("$session->hrm_id~$session->nick");
            $hash = hash_hmac('sha512', "$payload$salt$time", $user->password);
            $hkey = "$alias@$remote_address|".date('YmdHis')."-$mask:$hash";
            $session->key = $hash;
            $session->status = 1;
            Redis::set($hkey, json_encode($session));
            Redis::expire($hkey, $session->life);
            $token = $hash;
            $rules = [];
            $rights = [];
            foreach ($roles as $ro){
                $rules = array_values(array_unique( array_merge($rules, u::get($ro, 'rules', []))));
                foreach (u::get($ro, 'rights', []) as $key => $ri){
                    $k = isset($rights[$key])? $rights[$key] ?: []: [];
                    $rights[$key] = array_values(array_unique( array_merge( $k, $ri?:[])));
                }
            }
            $rights = (Object) $rights;
            $message = "Hi! $user->full_name!";
            // $boss = User::find($user->superior_id);
            // $information->boss = $boss->user_name;
            $title_id = 9;
            if ($session->role_id >= ROLE_REGION_CEO && $session->role_id <= ROLE_ADMINISTRATOR) {
              $title_id = 1;
            } elseif (in_array($session->role_id, [ROLE_BRANCH_CEO, ROLE_EC_LEADER, ROLE_OM])) {
              $title_id = 4;
            } elseif ($session->role_id == ROLE_EC) {
              $title_id = 2;
            } elseif ($session->role_id == ROLE_CM) {
              $title_id = 3;
            }
            $information->id = $session->id;
            $information->title_id = $title_id;
            $information->hrm_id = $session->hrm_id;
            $information->accounting_id = isset($session->accounting_id) ? $session->accounting_id : '';
            $information->superior_id = $session->superior_id;
            $information->status = $session->status;
            $information->start_date = $session->start_date;
            $information->role_id = $session->role_id;
            $information->title = $session->role_name;
            $information->branches = $branch_ids;
            $information->branch_id = $branches_ids;
            $information->code = $session->code;
            $information->name = $session->name;
            $information->nick = $session->nick;
            $information->zone = $session->zone;
            $information->email = $session->email;
            $information->phone = $session->phone;
            $information->avatar = $session->avatar;
            $information->branch = $session->branch;
            $information->started = $session->started;
            $information->first_change_password = $session->first_change_password;
            $information->last_change_password_date = $session->last_change_password_date;
          }
        }
      }

      if ($main_role && $branches_ids) {
        $role_ec = ROLE_EC;
        $role_ec_leader = ROLE_EC_LEADER;
        $role_cm = ROLE_CM;
        $role_om = ROLE_OM;
        $current_branch_info = $branch_ids[0];
        $current_branch_id = isset($current_branch_info['id']) ? (int)$current_branch_info['id'] : 1;
        $current_branch = $main_role['id'] >= ROLE_MANAGERS ? 1 : $current_branch_id;
        $users = u::query("SELECT * FROM (SELECT u.id, u.hrm_id, u.full_name, u.username, u.phone, u.avatar, u.email, u.start_date, MAX(r.id) role_id, GROUP_CONCAT(r.`name` SEPARATOR ' kiêm ') title FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id LEFT JOIN roles r ON t.role_id = r.id WHERE t.status = 1 AND t.branch_id = $current_branch GROUP BY u.id) x ORDER BY role_id DESC");
        $school_grades = u::query("SELECT id AS class_id, `name` AS class_name FROM school_grades WHERE `status` > 0");
        $holidays = u::query("SELECT h.id, h.`name`, h.start_date, h.end_date FROM public_holiday h LEFT JOIN branches b ON h.zone_id = b.zone_id WHERE b.id =  $current_branch");
        $branches = u::query("SELECT name, id, brch_id, area_id, zone_id, region_id FROM branches WHERE `status` = 1");
        $products = u::query("SELECT name, id, max_student FROM products WHERE `status` = 1");
        $programs = u::query("SELECT p.name, p.id, p.program_id, p.parent_id FROM programs p LEFT JOIN term_program_product t ON t.program_id = p.id LEFT JOIN products pd ON t.product_id = pd.id LEFT JOIN semesters s ON p.semester_id = s.id WHERE p.status = 1 AND t.status = 1 AND s.status = 1 AND s.end_date >= CURDATE() AND p.branch_id = $current_branch ORDER BY p.program_id ASC");
        $semeters = u::query("SELECT name, id, sem_id, start_date, end_date FROM semesters WHERE `status` = 1 AND end_date >= CURDATE()");
        $tuitions = u::query("SELECT name, id, session, price, discount, expired_date FROM tuition_fee WHERE `status` = 1 AND expired_date >= CURDATE() AND available_date <= CURDATE() AND (branch_id LIKE '$current_branch,%' OR branch_id LIKE '%,$current_branch,%' OR branch_id LIKE '%,$current_branch')");
        $classes = u::query("SELECT c.cls_name name, c.id, c.teacher_id, c.cls_startdate start_date, c.cls_enddate end_date, c.cls_iscancelled is_cancelled FROM classes c LEFT JOIN semesters s ON c.semester_id = s.id WHERE c.branch_id = $current_branch AND s.end_date >= CURDATE() AND s.status = 1");
        $ecslist = u::query("SELECT u.id, CONCAT(u.full_name, ' - ', u.username) `name`, r.`name` title, IF(t.role_id = $role_ec_leader, 1, 0) leader FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id LEFT JOIN roles r ON t.role_id = r.id WHERE u.`status` > 0 AND t.`status` = 1 AND (t.role_id = $role_ec OR t.role_id = $role_ec_leader) AND t.branch_id = $current_branch");
        $cmslist = u::query("SELECT u.id, CONCAT(u.full_name, ' - ', u.username) `name`, r.`name` title, IF(t.role_id = $role_om, 1, 0) leader FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id LEFT JOIN roles r ON t.role_id = r.id WHERE u.`status` > 0 AND t.`status` = 1 AND (t.role_id = $role_cm OR t.role_id = $role_om) AND t.branch_id = $current_branch");
        $rooms = u::query("SELECT room_name name, id, room_id FROM rooms WHERE `status` = 1 AND branch_id = $current_branch");
        $extra = (Object)[];
        $extra->current_branch = $current_branch;
        $extra->school_grades = $school_grades;
        $extra->users_list = $users;
        $extra->holidays = $holidays;
        $extra->branches = $branches;
        $extra->products = $products;
        $extra->programs = $programs;
        $extra->semeters = $semeters;
        $extra->tuitions = $tuitions;
        $extra->ecslist = $ecslist;
        $extra->cmslist = $cmslist;
        $extra->rooms = $rooms;
        $extra->secret = base64_encode(ada()->secret());

        $log = "insert into `log_user_login` (`username`, `ip_address`, `info`) 
                values('$username','$ip','$agent')";
        u::query($log);
        
        return response()->json([
          'access-token' => $token,
          'roles' => $rules,
          'rights' => $rights,
          'information' => $information,
          'extra' => $extra,
          'message' => $message
        ], 200);
      } else {
        return response()->json([
          'success' => false,
          'message' => $message,
          'access-token' => ''
        ], 200);
      }
    }

    public function autoChangePassword(Request $request) {
      if( $request->password != $request->password_confirm ) {
        return response()->error('Mật khẩu không trùng khớp',200);
      }
      $userData = $request->users_data;
      $user = User::find($userData->id);
      if (!Hash::check($request->password, $user->password)) {
        return response()->error('Mật khẩu không đúng',200);
      }
      try{
        $user->password = bcrypt($request->password);
        $user->first_change_password = 1;
        $user->last_change_password_date = now();
        $user->updated_at = now();
        $user->save();
        return response()->success('OK');
      }catch (Exception $e) {
        return response()->error('Có lỗi xảy ra, Vui lòng thử lại',200);
      }
    }
    public function singleSignOn(Request $request){
      $hrm_id = $request->hrm_id;
      $token = $request->token;
      $key ="CMS@abcd1234";
      if($token == md5($key.$hrm_id)){
        $token = '';
        $rules = [];
        $rights = [];
        $main_role = null;
        $branch_ids = [];
        $roles_list = [];
        $branches_ids = [];
        $information = (object) array();
        $message = 'Thông tin đăng nhập không chính xác!';
        $ip = $request->ip();
        $agent = $request->server('HTTP_USER_AGENT');
        $user = u::first("SELECT u.*, b.name branch_name, z.name zone_name FROM users u LEFT JOIN term_user_branch t ON u.id = t.user_id 
        LEFT JOIN branches b ON t.branch_id = b.id LEFT JOIN zones z ON b.zone_id = z.id WHERE (LOWER(u.hrm_id) = '".strtolower($hrm_id)."') AND t.status = 1");

      if ($user) {
        $username = $user->username;
        if ($user->status == 0) {
          $information = null;
          $message = 'Tài khoản này đang bị khóa.';
        } else {
          $config = u::config();
          $superoles = isset($config->superoles) ? json_decode($config->superoles) : [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS];
          $roles = u::query("SELECT r.id AS role_id, r.name AS role, r.status AS role_status, r.functions, t.branch_id,
            b.name AS branch_name, b.zone_id, b.region_id, z.name AS zone_name FROM roles as r LEFT JOIN term_user_branch AS t ON r.id = t.role_id
            LEFT JOIN branches AS b ON t.branch_id = b.id LEFT JOIN zones AS z ON b.zone_id = z.id WHERE t.user_id = $user->id AND t.status = 1");
          if (in_array($roles[0]->role_id, $superoles)) {
            $super_admin = $roles[0];
            $roles = u::query("SELECT '$super_admin->role_id' AS role_id, '$super_admin->role' AS role, 
              '$super_admin->role_status' AS role_status, '$super_admin->functions' AS functions, id AS branch_id,
              `name` AS branch_name, zone_id, region_id FROM branches WHERE status > 0");
          }
          if ($roles) {
            foreach ($roles as $role) {
              $roles_list[] = ['id' => $role->role_id, 'role' => $role->role];
              if ($role->branch_id && !in_array($role->branch_id, $branch_ids)) {
                $branch_ids[] = ['id'=>$role->branch_id, 'name'=>$role->branch_name, 'role'=>$role->role_id, 'title'=>$role->role];
                $branches_ids[] = $role->branch_id;
              }
            }
            $branches_ids = implode(',', $branches_ids);
          }

          $client_info = (Object)[];
          $remote_address = $_SERVER['REMOTE_ADDR'];
          // $client_info->address = $remote_address;
          $client_info->agent = $_SERVER['HTTP_USER_AGENT'];
          $client_info->language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
          $roles_list = apax_ada_sort_items($roles_list);
          $main_role = $roles_list[0];
          $session = (object) array();
          $salt = md5($user->password);
          $mask = md5(json_encode($client_info).$salt);
          $time = time();
          $session->id = $user->id;
          $session->hrm_id = trim($user->hrm_id);
          $session->accounting_id = isset($user->accounting_id) ? trim($user->accounting_id) : '';
          $session->role_id = $main_role['id'];
          $session->role_name = implode(", ", array_values(array_unique(array_column(is_array($roles_list)? $roles_list: [], 'role'))));
          $session->roles_detail = $roles;
          $session->branches = $branch_ids;
          $session->branches_ids = $branches_ids;
          $session->code = "$user->id-".trim($user->hrm_id).'-'.trim($session->accounting_id);
          $session->superior_id = trim($user->superior_id);
          $session->name = trim($user->full_name);
          $session->nick = trim($user->username);
          $session->avatar = $user->avatar;
          $session->status = $user->status;
          $session->email = trim($user->email);
          $session->phone = trim($user->phone);
          $session->start_date = $user->start_date;
          $session->started = date("Y-m-d H:i:s");
          $session->branch = $user->branch_name;
          $session->zone = $user->zone_name;
          $session->roles = u::authen($roles);
          $session->ip = $ip;
          $session->mac = $mask;
          $session->salt = $salt;
          $session->agent = $agent;
          $session->client_info = $client_info;
          $session->life = (int)(3600*24);
          $session->start_at = $time;
          $session->expire_at = $time + $session->life;
          $session->first_change_password = $user->first_change_password;
          $session->last_change_password_date = $user->last_change_password_date;
          $payload = json_encode($session);
          $alias = strtoupper("$session->hrm_id~$session->nick");
          $hash = hash_hmac('sha512', "$payload$salt$time", $user->password);
          $hkey = "$alias@$remote_address|".date('YmdHis')."-$mask:$hash";
          $session->key = $hash;
          $session->status = 1;
          Redis::set($hkey, json_encode($session));
          Redis::expire($hkey, $session->life);
          $token = $hash;
          $rules = [];
          $rights = [];
          foreach ($roles as $ro){
              $rules = array_values(array_unique( array_merge($rules, u::get($ro, 'rules', []))));
              foreach (u::get($ro, 'rights', []) as $key => $ri){
                  $k = isset($rights[$key])? $rights[$key] ?: []: [];
                  $rights[$key] = array_values(array_unique( array_merge( $k, $ri?:[])));
              }
          }
          $rights = (Object) $rights;
          $message = "Hi! $user->full_name!";
          // $boss = User::find($user->superior_id);
          // $information->boss = $boss->user_name;
          $title_id = 9;
          if ($session->role_id >= ROLE_REGION_CEO && $session->role_id <= ROLE_ADMINISTRATOR) {
            $title_id = 1;
          } elseif (in_array($session->role_id, [ROLE_BRANCH_CEO, ROLE_EC_LEADER, ROLE_OM])) {
            $title_id = 4;
          } elseif ($session->role_id == ROLE_EC) {
            $title_id = 2;
          } elseif ($session->role_id == ROLE_CM) {
            $title_id = 3;
          }
          $information->id = $session->id;
          $information->title_id = $title_id;
          $information->hrm_id = $session->hrm_id;
          $information->accounting_id = isset($session->accounting_id) ? $session->accounting_id : '';
          $information->superior_id = $session->superior_id;
          $information->status = $session->status;
          $information->start_date = $session->start_date;
          $information->role_id = $session->role_id;
          $information->title = $session->role_name;
          $information->branches = $branch_ids;
          $information->branch_id = $branches_ids;
          $information->code = $session->code;
          $information->name = $session->name;
          $information->nick = $session->nick;
          $information->zone = $session->zone;
          $information->email = $session->email;
          $information->phone = $session->phone;
          $information->avatar = $session->avatar;
          $information->branch = $session->branch;
          $information->started = $session->started;
          $information->first_change_password = $session->first_change_password;
          $information->last_change_password_date = $session->last_change_password_date;
        }
      }

      if ($main_role && $branches_ids) {
        $role_ec = ROLE_EC;
        $role_ec_leader = ROLE_EC_LEADER;
        $role_cm = ROLE_CM;
        $role_om = ROLE_OM;
        $current_branch_info = $branch_ids[0];
        $current_branch_id = isset($current_branch_info['id']) ? (int)$current_branch_info['id'] : 1;
        $current_branch = $main_role['id'] >= ROLE_MANAGERS ? 1 : $current_branch_id;
        $users = u::query("SELECT * FROM (SELECT u.id, u.hrm_id, u.full_name, u.username, u.phone, u.avatar, u.email, u.start_date, MAX(r.id) role_id, GROUP_CONCAT(r.`name` SEPARATOR ' kiêm ') title FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id LEFT JOIN roles r ON t.role_id = r.id WHERE t.status = 1 AND t.branch_id = $current_branch GROUP BY u.id) x ORDER BY role_id DESC");
        $school_grades = u::query("SELECT id AS class_id, `name` AS class_name FROM school_grades WHERE `status` > 0");
        $holidays = u::query("SELECT h.id, h.`name`, h.start_date, h.end_date FROM public_holiday h LEFT JOIN branches b ON h.zone_id = b.zone_id WHERE b.id =  $current_branch");
        $branches = u::query("SELECT name, id, brch_id, area_id, zone_id, region_id FROM branches WHERE `status` = 1");
        $products = u::query("SELECT name, id, max_student FROM products WHERE `status` = 1");
        $programs = u::query("SELECT p.name, p.id, p.program_id, p.parent_id FROM programs p LEFT JOIN term_program_product t ON t.program_id = p.id LEFT JOIN products pd ON t.product_id = pd.id LEFT JOIN semesters s ON p.semester_id = s.id WHERE p.status = 1 AND t.status = 1 AND s.status = 1 AND s.end_date >= CURDATE() AND p.branch_id = $current_branch ORDER BY p.program_id ASC");
        $semeters = u::query("SELECT name, id, sem_id, start_date, end_date FROM semesters WHERE `status` = 1 AND end_date >= CURDATE()");
        $tuitions = u::query("SELECT name, id, session, price, discount, expired_date FROM tuition_fee WHERE `status` = 1 AND expired_date >= CURDATE() AND available_date <= CURDATE() AND (branch_id LIKE '$current_branch,%' OR branch_id LIKE '%,$current_branch,%' OR branch_id LIKE '%,$current_branch')");
        $classes = u::query("SELECT c.cls_name name, c.id, c.teacher_id, c.cls_startdate start_date, c.cls_enddate end_date, c.cls_iscancelled is_cancelled FROM classes c LEFT JOIN semesters s ON c.semester_id = s.id WHERE c.branch_id = $current_branch AND s.end_date >= CURDATE() AND s.status = 1");
        $ecslist = u::query("SELECT u.id, CONCAT(u.full_name, ' - ', u.username) `name`, r.`name` title, IF(t.role_id = $role_ec_leader, 1, 0) leader FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id LEFT JOIN roles r ON t.role_id = r.id WHERE u.`status` > 0 AND t.`status` = 1 AND (t.role_id = $role_ec OR t.role_id = $role_ec_leader) AND t.branch_id = $current_branch");
        $cmslist = u::query("SELECT u.id, CONCAT(u.full_name, ' - ', u.username) `name`, r.`name` title, IF(t.role_id = $role_om, 1, 0) leader FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id LEFT JOIN roles r ON t.role_id = r.id WHERE u.`status` > 0 AND t.`status` = 1 AND (t.role_id = $role_cm OR t.role_id = $role_om) AND t.branch_id = $current_branch");
        $rooms = u::query("SELECT room_name name, id, room_id FROM rooms WHERE `status` = 1 AND branch_id = $current_branch");
        $extra = (Object)[];
        $extra->current_branch = $current_branch;
        $extra->school_grades = $school_grades;
        $extra->users_list = $users;
        $extra->holidays = $holidays;
        $extra->branches = $branches;
        $extra->products = $products;
        $extra->programs = $programs;
        $extra->semeters = $semeters;
        $extra->tuitions = $tuitions;
        $extra->ecslist = $ecslist;
        $extra->cmslist = $cmslist;
        $extra->rooms = $rooms;
        $extra->secret = base64_encode(ada()->secret());

        $log = "insert into `log_user_login` (`username`, `ip_address`, `info`) 
                values('$username','$ip','$agent')";
        u::query($log);
        
        return response()->json([
          'access-token' => $token,
          'roles' => $rules,
          'rights' => $rights,
          'information' => $information,
          'extra' => $extra,
          'message' => $message
        ], 200);
      } 
      }else{
        return response()->json(['error' => 'Unauthorized'], 401);
      }
    }
    public function switchSystem(Request $request){
      $key ="CMS@abcd1234";
      if(APP_ENV === "product"){
        $tmp_link = "https://account.logiclab.vn/#/single-sign-on/";
      }else{
        $tmp_link = "https://stg-account.logiclab.vn/#/single-sign-on/";
      }
      $data = $tmp_link.$request->users_data->hrm_id."/".md5($key.$request->users_data->hrm_id);
      $code = APICode::SUCCESS;
      $response = new Response;
      
      return $response->formatResponse($code, $data);
    }
    public function getLoginRedirect(){
        if(env('APP_ENV', 'staging')=='product'){
            $data = 'https://account.logiclab.vn/#/login';
        }else{
            $data = 'https://stg-account.logiclab.vn/#/login';
        }
        $response = new Response;
        $code = APICode::SUCCESS;
        return $response->formatResponse($code, $data);
    }
}
