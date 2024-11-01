<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Branch;
use App\Models\CyberAPI;
use App\Models\LogWorkExperienceUser;
use App\Models\Mail;
use App\Models\ProcessExcel;
use App\Models\Response;
use App\Models\User;
use App\Models\TermUserBranch;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as x;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//use PhpOffice\PhpSpreadsheet\Writer;
// use App\Http\Controllers\EffectAPIController;

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

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
class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return [];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function query($request, $keyword = '', $filters = null, $orders = null, $page = 1, $limit = 5)
    {
        $selected_page = $request->page ? (int)$request->page : 0;
        $page = $selected_page > 0 ? $selected_page : $page;
        $limited_page = $request->limit ? (int)$request->limit : $limit;
        $limit = $limited_page > 0 ? $limited_page : $limit;
        $keyword = $request->search ? (string)$request->search : '';
        $query = '';
        $total = 0;
        if ($session = json_decode($request->authorized)) {
            $user_id = $session->id;
            $roles = $session->roles_detail;
            $all = false;
            $ec_list = [];
            $branch_list = [];
            $region_list = [];
            $student_list = [];
            $ec_id = 0;
            $ec_leader_id = 0;
            $ceo_region_id = 0;
            $ceo_branch_id = 0;
            if ($roles) {
                foreach ($roles as $role) {
                    if (in_array($role->role_id, [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS])) {
                        $all = true;
                        break;
                    } elseif ($role->role_id == ROLE_REGION_CEO && !in_array($role->region_id, $region_list)) {
                        $ceo_region_id = $user_id;
                        $region_list[] = $role->region_id;
                    } elseif ($role->role_id == ROLE_BRANCH_CEO) {
                        $ceo_branch_id = $user_id;
                    } elseif (in_array($role->role_id, [ROLE_CM, ROLE_OM]) && !in_array($role->branch_id, $branch_list)) {
                        $branch_list[] = $role->branch_id;
                    } elseif ($role->role_id == ROLE_EC) {
                        $ec_id = $user_id;
                    } elseif ($role->role_id == ROLE_EC_LEADER) {
                        $ec_leader_id = $user_id;
                    }
                }
            }
            $orderby = '';
            if ($orders) {
                $ways = ['ASC', 'DESC'];
                foreach ($orders as $order => $by) {
                    if ($order && in_array($by, $ways)) {
                        $orderby .= " $order $by,";
                    }
                }
                $orderby = $orderby ? " ORDER BY " . substr($orderby, 0, -1) : "";
            }
            $offset = $page == 1 ? 0 : $limit * ($page - 1);
            $branch_list = $branch_list ? implode(',', $branch_list) : '';
            $where = '';
            if (!$all) {
                if ($ceo_branch_id) {
                    $where .= " OR t.ceo_branch_id = $ceo_branch_id";
                }
                if (count($branch_list)) {
                    $branch_list = $branch_list ? implode(',', $branch_list) : '';
                    $where .= " OR t.branch_id IN ($branch_list)";
                }
                if ($ec_id) {
                    $where .= " OR t.ec_id = $ec_id";
                }
                if ($ec_leader_id) {
                    $ec_list = DB::select(DB::raw("SELECT id FROM users WHERE superior_id = (SELECT hrm_id FROM users WHERE id = $ec_leader_id)"));
                    $ec_list = $ec_list ? implode(',', $ec_list) : '';
                    $where .= " OR t.ec_id IN ($ec_list)";
                }
                if (count($region_list) && $ceo_region_id) {
                    $where .= " OR t.ceo_region_id = $ceo_region_id OR t.region_id IN ($region_list)";
                }
            }
            if ($keyword) {
                $where .= " AND (u.hrm_id LIKE '$keyword%' OR u.accounting_id LIKE '$keyword%' OR u.full_name LIKE '%$keyword%' OR u.username LIKE '%$keyword%' OR u.email LIKE '%$keyword%')";
            }
            if ($filters) {
                foreach ($filters as $field => $key) {
                    if ($field) {
                        $key = is_string($key) ? "'$key'" : (int)$key;
                        $where .= " AND `u.$field` = $key";
                    }
                }
            }
            $limitation = $limit > 0 ? "LIMIT $offset, $limit" : "";
            $query = "SELECT
                          u.id AS user_id,
                          u.hrm_id AS hrm_id,
                          u.accounting_id AS accounting_id,
                          u.full_name AS full_name,
                          u.username AS username,
                          u.email AS email,
                          u.avatar AS avatar,
                          u.status AS `status`,
                          CONCAT(u.hrm_id, '~', u.accounting_id) AS mixing_id,
                          o.name AS title,
                          o.description AS title_description,
                          u1.id AS boss_id,
                          u1.full_name AS boss,
                          b.name AS branch,
                          r.name AS region
                        FROM
                          users AS u
                          LEFT JOIN term_user_branch AS t ON t.user_id = u.id
                          LEFT JOIN users AS u1 ON u.superior_id = u1.hrm_id
                          LEFT JOIN branches AS b ON t.branch_id = b.id
                          LEFT JOIN regions AS r ON b.region_id = r.id
                          LEFT JOIN roles AS o ON t.role_id = o.id
                        WHERE
                          u.id > 0 $where $orderby $limitation";
            $count_query = "SELECT COUNT(u.id) AS total
                        FROM
                          users AS u
                          LEFT JOIN term_user_branch AS t ON t.user_id = u.id
                          LEFT JOIN users AS u1 ON u.superior_id = u1.hrm_id
                          LEFT JOIN branches AS b ON t.branch_id = b.id
                          LEFT JOIN regions AS r ON b.region_id = r.id
                          LEFT JOIN roles AS o ON t.role_id = o.id
                        WHERE
                          u.id > 0 $where";
            $total = DB::select(DB::raw($count_query));
            $total = $total[0]->total;
        }
        return [
            'base_query' => $query,
            'search' => $keyword,
            'filter' => $filters,
            'orders' => $orders,
            'total' => $total,
            'limit' => $limit,
            'page' => $page
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $response = [
            'code' => 300,
            'message' => 'Invalid token'
        ];
        $query_information = self::query($request);
        $users_query = $query_information['base_query'];
        if ($users_query) {
            $users = DB::select(DB::raw($users_query));
            $information = (object)[];
            if ($users) {
                foreach ($users as $user) {
                    if ($user->avatar == '' || !apax_ada_check_avatar($user->avatar)) {
                        $user->avatar = '/static/img/avatars/noavatar.png';
                    }
                }
                $information->filter = $query_information['filter'];
                $information->search = $query_information['search'];
                $information->orders = $query_information['orders'];
                $cpage = $query_information['page'];
                $limit = $query_information['limit'];
                $total = $query_information['total'];
                $lpage = $total <= $limit ? 1 : (int)round(ceil($total / $limit));
                $ppage = $cpage > 0 ? $cpage - 1 : 0;
                $npage = $cpage < $lpage ? $cpage + 1 : $lpage;
                $response['done'] = true;
                $response['pagination'] = [
                    'spage' => 1,
                    'ppage' => $ppage,
                    'cpage' => $cpage,
                    'npage' => $npage,
                    'lpage' => $lpage,
                    'limit' => $limit,
                    'total' => $total
                ];
                $response['code'] = 200;
                $response['users'] = $users;
                $response['information'] = $information;
                $response['message'] = 'successful';
            }
        }
        return response()->json($response);
    }

    public function resetPassword(Request $request)
    {
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $response = new Response();
        $mail = new Mail();
        $data->success = false;
        $data->message = "Tài khoản này không tồn tại.";
        $new_pass = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(9 / strlen($x)))), 1, 9);
        if ($email = $request->email) {
            $hrm_id = $request->hrm;
            $hrm_id = strtolower(substr(trim($hrm_id), 0, 1)) == 'g' ? $hrm_id : "G$hrm_id";
            $user_info = u::first("SELECT id, full_name, username, email FROM users WHERE email = '$email' AND hrm_id = '$hrm_id'");
            if ($user_info) {
                $data->success = true;
                $new_password = bcrypt($new_pass);
                $username = $user_info->username;

                //#send_mail
                if (APP_ENV == 'product') {
                    $subject = "Thông báo reset mật khẩu";
                    $web_url = APP_URL;
                    $content = "Dear $user_info->full_name,<br><br>";
                    $content .= "Hệ thống CRM gửi anh/ chị thông tin đăng nhập <a href='$web_url'>$web_url</a><br><br>";
                    $content .= "<b>Username</b>: $username<br><br>";
                    $content .= "<b>Password</b>: $new_pass<br><br>";
                    $content .= "Trân trọng!";
                    $mail->sendSingleMail(
                        [
                            'address' => $email,
                            'name' => $user_info->full_name
                        ],
                        $subject,
                        $content
                    );
                }

                $timeUpdate = now();
                u::query("UPDATE users SET password = '$new_password',last_change_password_date = '$timeUpdate' WHERE id = '$user_info->id'");
                $data->message = "Mật khẩu mới đã được gửi tới hòm thư của bạn, vui lòng check email để xem chi tiết.";
            }
        }
        return $response->formatResponse($code, $data);
    }

    public function updateProfile(Request $request, $user_id)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        $mail = new Mail();
        $token = $request->headers->get('Authorization');
        if ($session = $request->users_data) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            $data->success = false;
            $data->message = "Tài khoản người dùng này không tồn tại.";
            $post = (Object)$request->input();
            $sql = "SELECT
                    DISTINCT(u.id), u.hrm_id, u.full_name,
                    u.username, u.phone, u.email, u.start_date,
                    tub.branch_id, tub.role_id, u.superior_id,
                    b.hrm_id AS branch_hrm_id, u.status, u.password
                  FROM users as u
                  LEFT JOIN term_user_branch as tub on tub.user_id = u.id
                  lEFT JOIN branches AS b ON tub.branch_id = b.id
                  WHERE u.id = $user_id";
            $user_info = u::first($sql);

            if ($user_info && $post) {
                $role_cm = ROLE_CM;
                $role_om = ROLE_OM;
                $role_ec = ROLE_EC;
//                $is_leader = (boolean)$post->is_leader;
                $role_ec_leader = ROLE_EC_LEADER;
                $avatar = null;
                if (is_object((Object)$request->avatar)) {
                    $avatar = ada()->upload($request->avatar, 'avatars/users');
                }
                if (!empty(u::get($post, 'old_password'))) {
                    if (!Hash::check(u::get($post, 'old_password'), $user_info->password)) {
                        $code = APICode::PERMISSION_DENIED;
                        return $response->formatResponse($code, null, 'Mật khẩu không đúng!');
                    }
                    if (empty(u::get($post, 'new_password')) || u::get($post, 'new_password') !== u::get($post, 'confirm_password')) {
                        $code = APICode::PERMISSION_DENIED;
                        return $response->formatResponse($code, null, 'Mật khẩu nhập lại không khớp!');
                    }

                    $user_info->new_password = bcrypt(trim(u::get($post, 'new_password')));
                }
                $user_info->ex_hrm = $user_info->hrm_id;
                $user_info->full_name = u::get($post, 'full_name', u::get($post, 'full_name'));
                $user_info->phone = u::get($post, 'phone', $user_info->phone);
                $user_info->email = u::get($post, 'email', $user_info->email);
                $user_info->hrm_id = u::get($post, 'hrm_id', $user_info->hrm_id);
                $user_info->superior_id = u::get($post, 'superior_id', $user_info->superior_id);

                $req = new Request();
                $req->api_url = "effect/saleinfo?ma_crm=$user_info->hrm_id";
                $req->api_method = 'GET';
                // $effectAPI = new EffectAPIController();
                // $responseData = json_decode($effectAPI->callAPI($req, $token));
                $user_info->is_new = u::get($response, 'message') != 'ok';

                $effect_api_response = $this->updateEffectUser($token, $user_info);
                $effect_api_response = $effect_api_response ? json_decode($effect_api_response) : null;
                $effect_api_success = false;
                if (isset($effect_api_response->code) && (int)$effect_api_response->code == 0) {
                    $effect_api_success = true;
                    $effect_data = $effect_api_response->data;
                    $user_info->accounting_id = $effect_data->accounting_id;
                }
                if (isset($post->is_leader) && $post->is_leader) {
                    $user_info->superior_id = $user_info->hrm_id;
                }
                $uid = $session->id;
                $update_accounting_id = isset($user_info->accounting_id) ? " `accounting_id` = '$user_info->accounting_id', " : "";
                $update_hrm_id = isset($user_info->hrm_id) ? " `hrm_id` = '$user_info->hrm_id', " : "";
                $update_name = u::get($user_info, 'full_name') ? " `full_name` = '$user_info->full_name', " : "";
                $update_account = u::get($user_info, 'username') ? " `username` = '$user_info->username', " : "";
                $update_superior_id = isset($user_info->superior_id) ? " `superior_id` = '$user_info->superior_id', " : "";
                $update_start_date = $user_info->start_date ? " `start_date` = '$user_info->start_date', " : "";
                $update_phone = $user_info->phone ? " `phone` = '$user_info->phone', " : "";
                $update_email = isset($user_info->email) ? " `email` = '$user_info->email', " : "";
                $update_avatar = $avatar ? "`avatar` = '$avatar', " : "";
                $update_status = isset($user_info->status) ? "`status` = " . (int)$user_info->status . ", " : "";
                $update_password = isset($user_info->new_password) ? "`password` = '$user_info->new_password', " : "";
                $res = (Object)[
                    "success" => true,
                    "message" => ""
                ];
                u::query("UPDATE users SET
                    $update_accounting_id
                    $update_hrm_id
                    $update_name
                    $update_account
                    $update_superior_id
                    $update_start_date
                    $update_phone
                    $update_email
                    $update_avatar
                    $update_status
                    $update_password
                    `editor_id` = $uid,
                    `updated_at` = NOW()
                    WHERE id = $user_id"
                );
                if ($user_info->status == 1) {
                    if (in_array($user_info->role_id, [$role_cm, $role_om, $role_ec, $role_ec_leader]) && !isset($post->action_self_update_profile)) {
                        $data = (Object)[
                            'effect_api' => $effect_api_success,
                            'branch_id' => (int)$user_info->branch_id,
                            'role_id' => (int)$user_info->role_id,
                            'old_superior_id' => $user_info->superior_id,
                            'user_id' => (int)$user_info->id,
                            'branch_hrm_id' => $user_info->branch_hrm_id
                        ];
                        $res = $this->processStudent($data, $token);
                    }
                    if ($res->success && !isset($post->action_self_update_profile)) {
                        $time = date('Y-m-d H:i:s');
                        u::query("UPDATE term_user_branch SET status = 0, updated_at = '$time', editor_id = $session->id WHERE user_id = $user_info->id");
                        u::query("UPDATE regions SET ceo_id = null, updated_at = '$time' WHERE ceo_id = $user_info->id");
                        $the_last_history = u::first("SELECT * FROM log_work_experience_user WHERE user_id = $user_info->id AND status > 0 ORDER BY start_date DESC, id DESC LIMIT 0, 1");
                        u::query("UPDATE log_work_experience_user SET `end_date` = NOW(), `updated_at` = NOW(), `updator_id` = $session->id WHERE id = $the_last_history->id");
                    }
                }
                if ($res->success) {
                    try {
                        $subject = "ApaxEnlish CRM thông tin tài khoản $user_info->full_name ($user_info->username) vừa được cập nhật";
                        $content = "Chào bạn!</br></br>";
                        $content .= "Thông tin hồ sơ tài khoản trên hệ thống ApaxEnlish CRM của bạn vừa được cập nhật lại thành công, chi tiết:<br><br>";
                        $content .= isset($post->hrm_id) && $post->hrm_id != $user_info->hrm_id ? "Mã HRM mới vừa được cập nhật từ: '<b><i>$user_info->hrm_id</i></b>' thành '<b style='color:red'>$post->hrm_id</b>'<br>" : "";
                        $content .= u::get($post, 'full_name') && u::get($post, 'full_name') != u::get($user_info, 'full_name') ? "Tên người dùng mới vừa được cập nhật từ: '<b><i>$user_info->full_name</i></b>' thành '<b style='color:red'>$post->full_name</b>'<br>" : "";
                        $content .= isset($post->username) && $post->username != $user_info->username ? "Tài khoản đăng nhập mới vừa được cập nhật từ: '<b><i>$user_info->username</i></b>' thành '<b style='color:red'>$post->username/b>'<br>" : "";
                        $content .= isset($post->email) && $post->email != $user_info->email ? "Địa chỉ email mới vừa được cập nhật từ: '<b><i>$user_info->email</i></b>' thành '<b style='color:red'>$post->email</b>'<br>" : "";
                        $content .= $post->phone && $post->phone != $user_info->phone ? "Số điện thoại liên lạc mới vừa được cập nhật từ: '<b><i>$user_info->phone</i></b>' thành '<b style='color:red'>$post->phone</b>'<br>" : "";
                        $content .= u::get($post, 'start_date') && u::get($post, 'start_date') != u::get($user_info, 'start_date') ? "Ngày bắt đầu làm việc mới vừa được cập nhật từ: '<b><i>$user_info->start_date</i></b>' thành '<b style='color:red'>$post->start_date</b>'<br>" : "";
                        $content .= "<br><b><i>Nếu bạn không phải người thực hiện các thay đổi này và chưa được thông báo về các thông tin cập nhật như trên, xin vui lòng liên hệ ngay với khối vận hành.</i></b><br></br>";
                        $content .= "Mọi thắc mắc hay yêu cầu hỗ trợ kỹ thuật, xin vui lòng liên hệ với Khối Công Nghệ.<br><br>";
                        $content .= "Trân trọng!";

                        if (APP_ENV == 'product') {
                            if (isset($post->email)) {
                                $mail->sendSingleMail(
                                    [
                                        'address' => $post->email,
                                        'name' => "Nhân viên $post->full_name"
                                    ],
                                    $subject,
                                    $content
                                );
                            }
                        } else {
                            if (isset($post->email)) {
                                $mail->sendSingleMail(
                                    [
                                        'address' => Mail::STAGING_EMAIL,
                                        'name' => Mail::STAGING_FULL_NAME
                                    ],
                                    $subject,
                                    $content
                                );
                            }
                        }

                        $data->success = true;
                        $data->message = "Thông tin tài khoản đã được cập nhật thành công!";
                    } catch (\Mockery\Exception $e) {
                        $data->success = false;
                        $code = 500;
                    }
                } else {
                    $data->success = $res->success;
                    $data->message = $res->message;
                }
            }
        }
        return $response->formatResponse($code, $data);
    }

    private function updateEffectUser($token, $user_info)
    {
        $req = new Request();
        $req->api_url = 'effect/sales';
        $req->api_method = $user_info->is_new ? 'POST' : 'PUT';
        $req->api_params = json_encode([
            "ten" => "$user_info->full_name",
            "ma_crm" => "$user_info->hrm_id",
            "crm_bo_phan" => "$user_info->branch_hrm_id",
            "crm_leader" => "$user_info->superior_id",
            "gioitinh" => "F",
            "dia_chi" => "",
            "phone" => "$user_info->phone",
            "disable" => $user_info->status ? false : true,
        ]);
        $req->fake = (Object)[
            'status' => $user_info->status,
            'uemail' => $user_info->email,
            'usname' => $user_info->full_name,
            'userid' => $user_info->id,
            'supeid' => $user_info->superior_id,
            'accoun' => $user_info->username,
            'startd' => $user_info->start_date,
            'hrm_id' => $user_info->hrm_id,
            'ex_hrm' => $user_info->ex_hrm
        ];
        // $effectAPI = new EffectAPIController();
        // return $effectAPI->callAPI($req, $token);
    }

    public function _progressChangeRoleBranch($user, $request, $session, $lists_branch)
    {
        $oldBranch = $user->branch_id;
        $newBranch = $request->branch_id;
        $oldRole = $user->role_id;
        $newRole = $request->role_id;
        $userID = $user->id;
        $filterData = NULL;
        $role = $oldRole;
        if ($oldBranch != $newBranch) {
            $role = $newRole;
            if ($oldRole == User::ROLE_CM or $oldRole == User::ROLE_OM) {
                $column = 'cm_id';
                if ($oldRole == User::ROLE_OM) {
                    $filterData = DB::table('users as u')
                        ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                        ->where('u.superior_id', $user->hrm_id)
                        ->where('tub.branch_id', $oldBranch)
                        ->whereNotIn('u.id', [$userID])->where('u.status', 1)->first();
                } else if ($oldRole == User::ROLE_CM) {
                    if ($user->superior_id != null) {
                        $filterData = DB::table('users as u')
                            ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                            ->where('u.hrm_id', $user->superior_id)->where('tub.branch_id', $oldBranch)
                            ->whereNotIn('u.id', [$userID])
                            ->where('u.status', 1)->first();
                    } else {
                        $filterData = DB::table('users as u')
                            ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                            ->where('tub.branch_id', $oldBranch)->where('tub.role_id', User::ROLE_CM)
                            ->where('u.status', 1)->where('tub.branch_id', $oldBranch)
                            ->whereNotIn('u.id', [$userID])->inRandomOrder()->first();
                    }
                }
            }
            if ($oldRole == User::ROLE_EC or $oldRole == User::ROLE_EC_LEADER) {
                $column = 'ec_id';
                if ($oldRole == User::ROLE_EC_LEADER) {
                    $filterData = DB::table('users as u')
                        ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                        ->where('u.superior_id', $user->hrm_id)->where('tub.branch_id', $oldBranch)
                        ->whereNotIn('u.id', [$userID])->where('u.status', 1)->first();
                } else if ($oldRole == User::ROLE_EC) {
                    if ($user->superior_id != null and $user->superior_id != 'G0000') {
                        $filterData = DB::table('users as u')
                            ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                            ->where('u.hrm_id', $user->superior_id)
                            ->where('tub.branch_id', $oldBranch)->where('u.status', 1)
                            ->whereNotIn('u.id', [$userID])->first();
                    } else {
                        $leaders = DB::table('users as u')
                            ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                            ->where('tub.branch_id', $oldBranch)->where('tub.role_id', User::ROLE_EC_LEADER)
                            ->where('u.hrm_id', '!=', 'G0000')->where('u.status', 1)->whereNotIn('u.id', [$userID])
                            ->inRandomOrder()->first();
                        if ($leaders) {
                            $filterData = $leaders;
                        } else {
                            $filterData = DB::table('users as u')
                                ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                                ->where('tub.branch_id', $oldBranch)->where('tub.role_id', User::ROLE_EC)
                                ->where('u.status', 1)->whereNotIn('u.id', [$userID])
                                ->inRandomOrder()->first();
                        }
                    }
                }
            }
        } else {
            if (($oldRole == User::ROLE_EC AND $newRole == User::ROLE_EC_LEADER) OR
                ($newRole == User::ROLE_EC AND $oldRole == User::ROLE_EC_LEADER) OR
                ($oldRole == User::ROLE_CM AND $newRole == User::ROLE_OM) OR
                ($newRole == User::ROLE_CM AND $oldRole == User::ROLE_OM)
            ) {

            } else {
                if ($oldRole != $newRole) {
                    $role = $newRole;
                    if ($oldRole == User::ROLE_CM or $oldRole == User::ROLE_OM) {
                        $column = 'cm_id';
                        if ($oldRole == User::ROLE_OM) {
                            $filterData = DB::table('users as u')
                                ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                                ->where('u.superior_id', $user->hrm_id)
                                ->where('tub.branch_id', $oldBranch)
                                ->whereNotIn('u.id', [$userID])->where('u.status', 1)->first();
                        } else if ($oldRole == User::ROLE_CM) {
                            if ($user->superior_id != null) {
                                $filterData = DB::table('users as u')
                                    ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                                    ->where('u.hrm_id', $user->superior_id)->where('tub.branch_id', $oldBranch)
                                    ->whereNotIn('u.id', [$userID])
                                    ->where('u.status', 1)->first();
                            } else {
                                $filterData = DB::table('users as u')
                                    ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                                    ->where('tub.branch_id', $oldBranch)->where('tub.role_id', User::ROLE_CM)
                                    ->where('u.status', 1)->where('tub.branch_id', $oldBranch)
                                    ->whereNotIn('u.id', [$userID])->inRandomOrder()->first();
                            }
                        }
                    } else if ($oldRole == User::ROLE_EC or $oldRole == User::ROLE_EC_LEADER) {
                        $column = 'ec_id';
                        if ($oldRole == User::ROLE_EC_LEADER) {
                            $filterData = DB::table('users as u')
                                ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                                ->where('u.superior_id', $user->hrm_id)->where('tub.branch_id', $oldBranch)
                                ->whereNotIn('u.id', [$userID])->where('u.status', 1)->first();
                        } else if ($oldRole == User::ROLE_EC) {
                            if ($user->superior_id != null and $user->superior_id != 'G0000') {
                                $filterData = DB::table('users as u')
                                    ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                                    ->where('u.hrm_id', $user->superior_id)
                                    ->where('tub.branch_id', $oldBranch)->where('u.status', 1)
                                    ->whereNotIn('u.id', [$userID])->first();
                            } else {
                                $leaders = DB::table('users as u')
                                    ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                                    ->where('tub.branch_id', $oldBranch)->where('tub.role_id', User::ROLE_EC_LEADER)
                                    ->where('u.hrm_id', '!=', 'G0000')->where('u.status', 1)->whereNotIn('u.id', [$userID])
                                    ->inRandomOrder()->first();
                                if ($leaders) {
                                    $filterData = $leaders;
                                } else {
                                    $filterData = DB::table('users as u')
                                        ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                                        ->where('tub.branch_id', $oldBranch)->where('tub.role_id', User::ROLE_EC)
                                        ->where('u.status', 1)->whereNotIn('u.id', [$userID])
                                        ->inRandomOrder()->first();
                                }
                            }
                        }
                    } else {
                        if ($oldRole != $newRole) {
                            // Progress Branch
                            DB::table('term_user_branch')->where('user_id', $userID)->update([
                                'status' => 0,
                                'editor_id' => $session->id
                            ]);
                            foreach ($lists_branch as $item) {
                                DB::table('term_user_branch')->insert([
                                    'user_id' => $userID,
                                    'branch_id' => $item,
                                    'role_id' => $newRole,
                                    'status' => 1,
                                    'editor_id' => $session->id,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }
                        }
                    }
                }
            }
        }

        if (isset($column) AND $filterData) {
            $tsu = DB::table('term_student_user')->where($column, $userID)->get();
            $dataUpdateTsu = [$column => $filterData->user_id];
            DB::table('term_student_user')->where($column, $userID)->update($dataUpdateTsu);
            $logMT = [];
            foreach ($tsu as $item) {
                $logMT[] = [
                    'student_id' => $item->student_id,
                    'from_' . $column => $userID,
                    'to_' . $column => $filterData->user_id,
                    'from_branch_id' => $user->branch_id,
                    'to_branch_id' => $user->branch_id,
                    'updated_by' => $session->id,
                    'date_transfer' => date('Y-m-d'),
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ];

            }
            DB::table('log_manager_transfer')->insert($logMT);
        }

        if (($oldRole != $newRole) OR ($oldBranch != $newBranch)) {
            // Progress Branch
            DB::table('term_user_branch')->where('user_id', $userID)->update([
                'status' => 0,
                'editor_id' => $session->id
            ]);
            DB::table('term_user_branch')->insert([
                'user_id' => $userID,
                'branch_id' => $newBranch,
                'role_id' => $role,
                'status' => 1,
                'editor_id' => $session->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }


    }

    public function _progressHistory($request, $user, $session)
    {
        $region_ids = $request->region_ids;
        $lists_branch = $lists_branch_regions = $lists_branch_branch = [];
        if (count($region_ids) > 0) {
            $lists_branch_regions = Branch::whereIn('region_id', $region_ids)->select(['id'])->pluck('id')->toArray();
        }

        $lists_branch_branch = array_map(function ($arr) {
            return $arr['id'];
        }, $request->branch_ids);

        $lists_branch = array_merge($lists_branch_branch, $lists_branch_regions);
        $lists_branch[] = $request->branch_id;
        $lists_branch = array_unique($lists_branch);

        if ($request->history_id == 0) {
            $dataInsert = [
                'branch_id' => $request->branch_id,
                'user_id' => $request->user_id,
                'superior_id' => $request->superior_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'role_id' => $request->role_id,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1,
            ];
            DB::table('log_work_experience_user')->insert($dataInsert);

        } else {
            $this->_progressChangeRoleBranch($user, $request, $session, $lists_branch);
            $log = LogWorkExperienceUser::find($request->history_id);
            if ($log) {
                $log->superior_id = $request->superior_id;
                $log->branch_id = $request->branch_id;
                $log->role_id = $request->role_id;
                $log->start_date = $request->start_date;
                $log->end_date = $request->end_date;
                $log->updated_at = now();
                $log->save();
            }
        }

    }

    public function updateHistory(Request $request, $user_id)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        $mail = new Mail();
        if ($session = $request->users_data) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            $data->success = false;
            $data->message = "Tài khoản người dùng này không tồn tại hoặc đã nghỉ việc.";
            $post = (Object)$request->input();

            $sql = "
                      SELECT
                        DISTINCT(u.id), u.hrm_id, u.full_name,
                        u.username, u.phone, u.email, u.start_date,
                        tub.branch_id, tub.role_id, u.superior_id , b.hrm_id as branch_hrm_id
                      FROM users as u
                      LEFT JOIN term_user_branch as tub on tub.user_id = u.id
                      LEFT JOIN branches as b ON tub.branch_id = b.id
                      WHERE u.id = $user_id AND u.status=1
                    ";

            $user_info = DB::select(DB::raw($sql));
            if ($user_info) {
                $user_info = $user_info[0];
            }

            if ($user_info && $post) {
                $uid = $session->id;
                if (md5($post->mode) == md5('update_log')) {
                    $startdate = isset($post->start_date) ? ", `start_date` = '$post->start_date'" : "";
                    $enddate = isset($post->end_date) ? ", `end_date` = '$post->end_date'" : "";
                    $superior = isset($post->superior_id) ? ", `superior_id` = '$post->superior_id'" : "";
                    if (!in_array($post->role_id, [ROLE_EC, ROLE_CM])) {
                        $superior = ", `superior_id` = ''";
                    }
                    u::query("UPDATE log_work_experience_user SET `role_id` = $post->role_id, `branch_id` = $post->branch_id, `updated_at` = NOW(), `updator_id` = $uid$startdate$enddate$superior WHERE id = $post->history_id");

                    $data->list = u::query("SELECT l.id, l.user_id, l.branch_id, l.role_id, l.superior_id, l.status, l.count_change, l.meta_data, DATE_FORMAT(l.start_date, '%Y-%m-%d') start_date, DATE_FORMAT(l.end_date, '%Y-%m-%d') end_date, b.name branch_name, r.name title FROM log_work_experience_user l LEFT JOIN branches b ON l.branch_id = b.id LEFT JOIN roles r ON l.role_id = r.id WHERE l.user_id = $user_id ORDER BY l.start_date DESC, l.id DESC");
                    $data->success = true;
                    $data->message = "Thông tin lịch sử làm việc đã được cập nhật thành công!";
                } elseif (md5($post->mode) == md5('update_all')) {
                    $startdate = isset($post->start_date) ? ", `start_date` = '$post->start_date'" : "";
                    $superior = isset($post->superior_id) ? ", `superior_id` = '$post->superior_id'" : "";
                    if (!in_array($post->role_id, [ROLE_EC, ROLE_CM])) {
                        $superior = ", `superior_id` = ''";
                    }
                    u::query("UPDATE log_work_experience_user SET `role_id` = $post->role_id, `branch_id` = $post->branch_id, `updated_at` = NOW(), `updator_id` = $uid$startdate$superior WHERE id = $post->history_id");
                    $ex_history = u::first("SELECT * FROM log_work_experience_user WHERE id = $post->history_id");
                    u::query("UPDATE term_user_branch SET `branch_id` = '$post->branch_id', `role_id` = $post->role_id, `updated_at` = NOW(), `editor_id` = $uid WHERE `id` = $post->term_id");
                    // Logic update term_user_branch
                    u::query("UPDATE users SET superior_id = '$post->superior_id' WHERE id=$user_id");

                    $data->list = u::query("SELECT l.id, l.user_id, l.branch_id, l.role_id, l.superior_id, l.status, l.count_change, l.meta_data, DATE_FORMAT(l.start_date, '%Y-%m-%d') start_date, DATE_FORMAT(l.end_date, '%Y-%m-%d') end_date, b.name branch_name, r.name title FROM log_work_experience_user l LEFT JOIN branches b ON l.branch_id = b.id LEFT JOIN roles r ON l.role_id = r.id WHERE l.user_id = $user_id ORDER BY l.start_date DESC, l.id DESC");
                    $data->success = true;
                    $data->message = "Thông tin lịch sử làm việc đã được cập nhật thành công!";

                } elseif (md5($post->mode) == md5('insert_log_update_term')) {
                    $role = (int)$post->role_id;

                    $post->user_id = $user_id;
                    $post->editor_id = $uid;
                    $post->old_superior_id = $user_info->superior_id;
                    $post->hrm_id = $user_info->hrm_id;
                    $post->branch_hrm_id = $user_info->branch_hrm_id;
                    $token = $request->headers->get('Authorization');

                    $res = $this->processStudent($post, $token);
                    if ($res->success) {
                        if ($this->inGroupRoles1($role)) {

                            $res2 = $this->insertGroupRoles1($post);

                        } elseif ($this->inGroupRoles2($role)) {

                            $res2 = $this->insertGroupRoles2($post);

                        } elseif ($this->inGroupRoles3($role)) {

                            $res2 = $this->insertGroupRoles3($post);

                        } elseif ($this->inGroupRoles4($role)) {

                            $res2 = $this->insertGroupRoles4($post);

                        } elseif ($this->inGroupRoles5($role)) {

                            $res2 = $this->insertGroupRoles5($post);

                        }

                        if ($res2->success) {
                            $data->list = u::query("SELECT l.id, l.user_id, l.branch_id, l.role_id, l.superior_id, l.status, l.count_change, l.meta_data, DATE_FORMAT(l.start_date, '%Y-%m-%d') start_date, DATE_FORMAT(l.end_date, '%Y-%m-%d') end_date, b.name branch_name, r.name title FROM log_work_experience_user l LEFT JOIN branches b ON l.branch_id = b.id LEFT JOIN roles r ON l.role_id = r.id WHERE l.user_id = $user_id ORDER BY l.start_date DESC, l.id DESC");
                            $data->success = true;
                            $data->message = "Thông tin lịch sử làm việc đã được cập nhật thành công!";
                        } else {
                            $data->message = $res2->message;
                        }
                    } else {
                        $data->message = $res->message;
                    }
                }
            }
        }
        return $response->formatResponse($code, $data);
    }

    private function inGroupRoles1($role)
    {
        return in_array($role, [ROLE_TEACHER, ROLE_CASHIER]);
    }

    private function inGroupRoles2($role)
    {
        return in_array($role, [ROLE_EC_LEADER, ROLE_EC, ROLE_OM, ROLE_CM]);
    }

    private function inGroupRoles3($role)
    {
        return in_array($role, [ROLE_BRANCH_CEO]);
    }

    private function inGroupRoles4($role)
    {
        return in_array($role, [ROLE_REGION_CEO]);
    }

    private function inGroupRoles5($role)
    {
        return in_array($role, [ROLE_ZONE_CEO, ROLE_ADMINISTRATOR, ROLE_MANAGERS, ROLE_SUPER_ADMINISTRATOR]);
    }

    private function insertGroupRoles1($data)
    {
        $the_last_history = u::first("SELECT * FROM log_work_experience_user WHERE user_id = $data->user_id AND status > 0 ORDER BY start_date DESC, id DESC LIMIT 0, 1");

        $total_change_times = u::first("SELECT COUNT(id) total FROM log_work_experience_user WHERE user_id = $data->user_id AND status > 0");
        $total_change = (int)$total_change_times->total;

        u::query("UPDATE log_work_experience_user SET `end_date` = NOW(), `updated_at` = NOW(), `updator_id` = $data->editor_id WHERE id = $the_last_history->id");
        u::query("INSERT INTO log_work_experience_user (`user_id`, `superior_id`, `branch_id`, `role_id`, `start_date`, `status`, `count_change`, `created_at`, `creator_id`) VALUES ($data->user_id, null, $data->branch_id, $data->role_id, NOW() + INTERVAL 1 DAY, 1, $total_change, NOW(), $data->editor_id)");

        u::query("UPDATE term_user_branch SET `branch_id` = '$data->branch_id', `role_id` = $data->role_id, `updated_at` = NOW(), `editor_id` = $data->editor_id WHERE `id` = $data->term_id");
        u::query("UPDATE users SET superior_id = '' WHERE id = $data->user_id");

        return (Object)[
            "success" => true,
            "message" => ""
        ];
    }

    private function insertGroupRoles2($data)
    {
        $the_last_history = u::first("SELECT * FROM log_work_experience_user WHERE user_id = $data->user_id AND status > 0 ORDER BY start_date DESC, id DESC LIMIT 0, 1");

        $total_change_times = u::first("SELECT COUNT(id) total FROM log_work_experience_user WHERE user_id = $data->user_id AND status > 0");
        $total_change = (int)$total_change_times->total;

        if ($data->superior_id !== 'G0000') {
            if (in_array($data->role_id, [ROLE_CM, ROLE_EC])) {
                $superior = u::first("SELECT u.id FROM users AS u LEFT JOIN term_user_branch AS t ON u.id = t.user_id WHERE u.hrm_id = '$data->superior_id' AND u.status=1 AND t.branch_id = $data->branch_id AND t.status=1");
                if (empty($superior) || !$superior) {
                    return (Object)[
                        "success" => false,
                        "message" => "Mã thủ trưởng không hợp lệ hoặc nhân viên đã nghỉ việc"
                    ];
                }
            }
        }

        u::query("UPDATE log_work_experience_user SET `end_date` = NOW(), `updated_at` = NOW(), `updator_id` = $data->editor_id WHERE id = $the_last_history->id");
        u::query("INSERT INTO log_work_experience_user (`user_id`, `superior_id`, `branch_id`, `role_id`, `start_date`, `status`, `count_change`, `created_at`, `creator_id`, `updated_at`, `updator_id`) VALUES ('$data->user_id', '$data->superior_id', '$data->branch_id', '$data->role_id', NOW() + INTERVAL 1 DAY, 1, '$total_change', NOW(), $data->editor_id, NOW(), $data->editor_id)");


        u::query("UPDATE term_user_branch SET `branch_id` = '$data->branch_id', `role_id` = $data->role_id, `updated_at` = NOW(), `editor_id` = $data->editor_id WHERE `id` = $data->term_id");
        u::query("UPDATE users SET superior_id = '$data->superior_id' WHERE id=$data->user_id");

        return (Object)[
            "success" => true,
            "message" => ""
        ];
    }

    private function insertGroupRoles3($data)
    {
        $user_id = $data->user_id;
        $editor_id = $data->editor_id;

        //xử lý log_work_experience_user
        $the_last_history = u::first("SELECT * FROM log_work_experience_user WHERE user_id = $data->user_id AND status > 0 ORDER BY start_date DESC, id DESC LIMIT 0, 1");

        $total_change_times = u::first("SELECT COUNT(id) total FROM log_work_experience_user WHERE user_id = $data->user_id AND status > 0");
        $total_change = (int)$total_change_times->total;

        u::query("UPDATE log_work_experience_user SET `end_date` = NOW(), `updated_at` = NOW(), `updator_id` = $data->editor_id WHERE id = $the_last_history->id");

        $meta_data = json_encode($data);

        u::query("INSERT INTO log_work_experience_user (`user_id`, `superior_id`, `branch_id`, `role_id`, `start_date`, `status`, `count_change`, `created_at`, `creator_id`, `updated_at`, `updator_id`, meta_data) VALUES ('$data->user_id', '$data->superior_id', '$data->branch_id', '$data->role_id', NOW() + INTERVAL 1 DAY, 1, '$total_change', NOW(), $data->editor_id, NOW(), $data->editor_id, '$meta_data')");

        //xử lý term_user_branch
        $role_ceo_branch_with_branches = [$data->branch_id];

        if (!empty($data->branch_ids)) {
            foreach ($data->branch_ids as $b) {
                $role_ceo_branch_with_branches[] = $b['id'];
            }
        }

        $role_ceo_region_with_regions = [];
        $role_ceo_region_with_branches = [];
        if (!empty($data->region_ids)) {
            foreach ($data->region_ids as $r) {
                $role_ceo_region_with_regions[] = $r['id'];
            }
            $regions_string = implode(',', $role_ceo_region_with_regions);
            $branches_in_regions = u::query("SELECT id FROM branches WHERE region_id IN ($regions_string)");
            foreach ($branches_in_regions as $br) {
                $role_ceo_region_with_branches[] = $br->id;
            }
        }

        $values = [];

        foreach ($role_ceo_branch_with_branches as $rc) {
            $role = ROLE_BRANCH_CEO;
            $hash_key = md5($user_id . $rc . $role);
            $time = date('Y-m-d H:i:s');
            $values[] = "('$hash_key',$user_id,$rc,$role,1,'$time',$editor_id)";
        }

        foreach ($role_ceo_region_with_branches as $rr) {
            $role = ROLE_REGION_CEO;
            $hash_key = md5($user_id . $rr . $role);
            $time = date('Y-m-d H:i:s');
            $values[] = "('$hash_key',$user_id,$rr,$role,1,'$time',$editor_id)";
        }

        $values_string = implode(', ', $values);

//        var_dump($values_string);die;

        u::query("UPDATE term_user_branch SET status = 0 WHERE user_id = $user_id");
        u::query("INSERT INTO term_user_branch (hash_key, user_id, branch_id, role_id, status, created_at, creator_id) VALUES $values_string ON DUPLICATE KEY UPDATE status = 1, updated_at = VALUES(created_at), editor_id = VALUES(creator_id)");
        u::query("UPDATE regions SET ceo_id = null WHERE ceo_id = $user_id");

        if (!empty($data->region_ids)) {
            u::query("UPDATE regions SET ceo_id = $user_id WHERE id IN ($regions_string)");
        }

        u::query("UPDATE users SET superior_id = null WHERE id = $user_id");

        return (Object)[
            "success" => true,
            "message" => ""
        ];
    }

    private function insertGroupRoles4($data)
    {
        $user_id = $data->user_id;
        $editor_id = $data->editor_id;

        //xử lý log_work_experience_user
        $the_last_history = u::first("SELECT * FROM log_work_experience_user WHERE user_id = $data->user_id AND status > 0 ORDER BY start_date DESC, id DESC LIMIT 0, 1");

        $total_change_times = u::first("SELECT COUNT(id) total FROM log_work_experience_user WHERE user_id = $data->user_id AND status > 0");
        $total_change = (int)$total_change_times->total;

        u::query("UPDATE log_work_experience_user SET `end_date` = NOW(), `updated_at` = NOW(), `updator_id` = $data->editor_id WHERE id = $the_last_history->id");

        $meta_data = json_encode($data);

        u::query("INSERT INTO log_work_experience_user (`user_id`, `superior_id`, `branch_id`, `role_id`, `start_date`, `status`, `count_change`, `created_at`, `creator_id`, `updated_at`, `updator_id`, meta_data) VALUES ('$data->user_id', '$data->superior_id', '$data->branch_id', '$data->role_id', NOW() + INTERVAL 1 DAY, 1, '$total_change', NOW(), $data->editor_id, NOW(), $data->editor_id, '$meta_data')");

        //xử lý term_user_branch
        $role_ceo_region_with_regions = [];
        $role_ceo_region_with_branches = [];
        if (!empty($data->region_ids)) {
            foreach ($data->region_ids as $r) {
                $role_ceo_region_with_regions[] = $r['id'];
            }
            $regions_string = implode(',', $role_ceo_region_with_regions);
            $branches_in_regions = u::query("SELECT id FROM branches WHERE region_id IN ($regions_string)");
            foreach ($branches_in_regions as $br) {
                $role_ceo_region_with_branches[] = $br->id;
            }
        }

        $values = [];

        foreach ($role_ceo_region_with_branches as $rr) {
            $role = ROLE_REGION_CEO;
            $hash_key = md5($user_id . $rr . $role);
            $time = date('Y-m-d H:i:s');
            $values[] = "('$hash_key',$user_id,$rr,$role,1,'$time',$editor_id)";
        }

        $values_string = implode(', ', $values);

//        var_dump($values_string);die;

        u::query("UPDATE term_user_branch SET status = 0 WHERE user_id = $user_id");
        u::query("INSERT INTO term_user_branch (hash_key, user_id, branch_id, role_id, status, created_at, creator_id) VALUES $values_string ON DUPLICATE KEY UPDATE status = 1, updated_at = VALUES(created_at), editor_id = VALUES(creator_id)");
        u::query("UPDATE regions SET ceo_id = null WHERE ceo_id = $user_id");

        if (!empty($data->region_ids)) {
            u::query("UPDATE regions SET ceo_id = $user_id WHERE id IN ($regions_string)");
        }

        u::query("UPDATE users SET superior_id = null WHERE id = $user_id");

        return (Object)[
            "success" => true,
            "message" => ""
        ];
    }

    private function insertGroupRoles5($data)
    {

    }

    private function processStudent($data, $token)
    {
        $the_last_history = u::first("SELECT * FROM log_work_experience_user WHERE user_id = $data->user_id AND status > 0 ORDER BY start_date DESC, id DESC LIMIT 0, 1");

        $success = false;
        $message = 'Có lỗi xảy ra. Vui lòng thử lại sau!';

        if (!empty($the_last_history) && $the_last_history) {
            $role_cm = ROLE_CM;
            $role_om = ROLE_OM;
            $role_ec = ROLE_EC;
            $role_ec_leader = ROLE_EC_LEADER;
            $time = date('Y-m-d H:i:s');
            switch ($the_last_history->role_id) {
                case $role_cm:
                    if (($the_last_history->branch_id != $data->branch_id) || (!in_array($data->role_id, [$role_cm, $role_om]))) {
                        if ($data->old_superior_id != 'G0000') {
                            $om = u::first("SELECT id FROM users WHERE hrm_id = '$data->old_superior_id'");
                            if ($om) {
                                $res = u::query("UPDATE term_student_user SET cm_id=$om->id, updated_at = '$time' WHERE cm_id = $data->user_id");
                                u::query("UPDATE classes SET cm_id = $om->id, updated_at = '$time' WHERE cm_id = $data->user_id AND cls_enddate >= CURDATE() AND cls_iscancelled = 'no'");

                                $success = true;
                                $message = "";
                            } else {
                                $cm = u::first("SELECT user_id FROM term_user_branch WHERE branch_id=$data->branch_id AND role_id=$role_cm AND status=1");
                                u::query("UPDATE classes SET cm_id = $cm->id, updated_at = '$time' WHERE cm_id = $data->user_id AND cls_enddate >= CURDATE() AND cls_iscancelled = 'no'");
                                if ($cm) {
                                    $res = u::query("UPDATE term_student_user SET cm_id=$cm->user_id, updated_at = '$time' WHERE cm_id = $data->user_id");

                                    $success = true;
                                    $message = "";
                                } else {
                                    $message = "Không tìm thấy CM nào khác để quản lý học sinh";
                                }
                            }
                        } else {
                            $cm = u::first("SELECT user_id FROM term_user_branch WHERE branch_id=$data->branch_id AND role_id=$role_cm AND status=1");
                            if ($cm) {
                                $res = u::query("UPDATE term_student_user SET cm_id=$cm->user_id, updated_at = '$time' WHERE cm_id = $data->user_id");
                                u::query("UPDATE classes SET cm_id = $cm->id, updated_at = '$time' WHERE cm_id = $data->user_id AND cls_enddate >= CURDATE() AND cls_iscancelled = 'no'");

                                $success = true;
                                $message = "";
                            } else {
                                $message = "Không tìm thấy CM nào khác để quản lý học sinh";
                            }
                        }
                    } else {
                        $success = true;
                        $message = "";
                    }

                    break;
                case $role_om:
                    if (($the_last_history->branch_id == $data->branch_id) && ($data->role_id == $role_om)) {
                        $success = true;
                        $message = "";
                    } else {
                        $cms = u::query("SELECT COUNT(id) AS total FROM users WHERE superior_id = '$data->hrm_id' AND status = 1");
                        if (((int)$cms->total) == 0) {
//                            $res = u::query("UPDATE users SET superior_id = 'G0000', updated_at = '$time', editor_id = $data->editor_id WHERE superior_id = '$data->hrm_id'");
//                            if($res){
                            if ($data->role_id != $role_cm) {
                                $cm = u::first("SELECT user_id FROM term_user_branch WHERE branch_id=$data->branch_id AND role_id=$role_cm AND status=1");
                                if ($cm) {
                                    $res = u::query("UPDATE term_student_user SET cm_id=$cm->user_id, updated_at = '$time' WHERE cm_id = $data->user_id");
                                    u::query("UPDATE classes SET cm_id = $cm->id, updated_at = '$time' WHERE cm_id = $data->user_id AND cls_enddate >= CURDATE() AND cls_iscancelled = 'no'");

                                    $success = true;
                                    $message = "";

                                } else {
                                    $message = "Không tìm thấy CM nào khác để quản lý học sinh";
                                }
                            } else {
                                $success = true;
                                $message = "";
                            }
//                            }else{
//                                $message = "Lỗi kết nối. Vui lòng thử lại sau!";
//                            }
                        } else {
                            $message = "Vui lòng cập nhật người quản lý mới cho các CM trước khi thay đổi công tác";
                        }
                    }
                    break;
                case $role_ec:
                    if (($the_last_history->branch_id != $data->branch_id) || (!in_array($data->role_id, [$role_ec, $role_ec_leader]))) {
                        if ($data->old_superior_id != 'G0000') {
                            $ec_leader = u::first("SELECT id FROM users WHERE hrm_id = '$data->old_superior_id'");
                            if ($ec_leader) {
                                $students = u::query("SELECT crm_id FROM students WHERE id IN (SELECT student_id FROM term_student_user WHERE ec_id = $data->user_id) AND status=1");
                                if (!empty($students)) {
                                    foreach ($students as $student) {
                                        $this->updateStudentInfo($student->crm_id, $data->old_superior_id, $data->branch_hrm_id, $token);
                                    }
                                }

                                $res = u::query("UPDATE term_student_user SET ec_id=$ec_leader->id, updated_at = '$time' WHERE ec_id = $data->user_id");

                                $success = true;
                                $message = "";

                            } else {
                                $ec = u::first("SELECT u.id as user_id, u.hrm_id FROM users AS u LEFT JOIN term_user_branch AS t ON u.id = t.user_id WHERE t.branch_id=$data->branch_id AND t.role_id=$role_ec AND t.status=1");
                                if ($ec) {
                                    $students = u::query("SELECT crm_id FROM students WHERE id IN (SELECT student_id FROM term_student_user WHERE ec_id = $data->user_id) AND status=1");
                                    if (!empty($students)) {
                                        foreach ($students as $student) {
                                            $this->updateStudentInfo($student->crm_id, $ec->hrm_id, $data->branch_hrm_id, $token);
                                        }
                                    }

                                    $res = u::query("UPDATE term_student_user SET ec_id=$ec->user_id, updated_at = '$time' WHERE ec_id = $data->user_id");

                                    $success = true;
                                    $message = "";

                                } else {
                                    $message = "Không tìm thấy CM nào khác để quản lý học sinh";
                                }
                            }
                        } else {
                            $ec = u::first("SELECT u.id as user_id, u.hrm_id FROM users AS u LEFT JOIN term_user_branch AS t ON u.id = t.user_id WHERE t.branch_id=$data->branch_id AND t.role_id=$role_ec AND t.status=1");
                            if ($ec) {
                                $students = u::query("SELECT crm_id FROM students WHERE id IN (SELECT student_id FROM term_student_user WHERE ec_id = $data->user_id) AND status=1");
                                if (!empty($students)) {
                                    foreach ($students as $student) {
                                        $this->updateStudentInfo($student->crm_id, $ec->hrm_id, $data->branch_hrm_id, $token);
                                    }
                                }

                                $res = u::query("UPDATE term_student_user SET ec_id=$ec->user_id, updated_at = '$time' WHERE ec_id = $data->user_id");

                                $success = true;
                                $message = "";

                            } else {
                                $message = "Không tìm thấy CM nào khác để quản lý học sinh";
                            }
                        }
                    } else {
                        $success = true;
                        $message = "";
                    }

                    break;
                case $role_ec_leader:
                    if (($the_last_history->branch_id == $data->branch_id) && ($data->role_id == $role_ec_leader)) {
                        $success = true;
                        $message = "";
                    } else {
                        $cms = u::query("SELECT COUNT(id) AS total FROM users WHERE superior_id = '$data->hrm_id' AND status = 1");
                        if (((int)$cms->total) == 0) {
//                            $res = u::query("UPDATE users SET superior_id = 'G0000', updated_at = '$time', editor_id = $data->editor_id WHERE superior_id = '$data->hrm_id'");
//                            if($res){
                            if ($data->role_id != $role_ec) {
                                $ec = u::first("SELECT u.id as user_id, u.hrm_id FROM users AS u LEFT JOIN term_user_branch AS t ON u.id = t.user_id WHERE t.branch_id=$data->branch_id AND t.role_id=$role_ec AND t.status=1");
                                if ($ec) {
                                    $students = u::query("SELECT crm_id FROM students WHERE id IN (SELECT student_id FROM term_student_user WHERE ec_id = $data->user_id) AND status=1");
                                    if (!empty($students)) {
                                        foreach ($students as $student) {
                                            $this->updateStudentInfo($student->crm_id, $ec->hrm_id, $data->branch_hrm_id, $token);
                                        }
                                    }

                                    $res = u::query("UPDATE term_student_user SET ec_id=$ec->user_id, updated_at = '$time' WHERE ec_id = $data->user_id");

                                    $success = true;
                                    $message = "";

                                } else {
                                    $message = "Không tìm thấy CM nào khác để quản lý học sinh";
                                }
                            } else {
                                $success = true;
                                $message = "";
                            }
//                            }else{
//                                $message = "Lỗi kết nối. Vui lòng thử lại sau!";
//                            }
                        } else {
                            $message = "Vui lòng cập nhật người quản lý mới cho các EC trước khi thay đổi công tác";
                        }
                    }
                    break;
                default:
                    $success = true;
                    $message = "";
                    break;
            }
        }

        return (Object)[
            'success' => $success,
            'message' => $message
        ];
    }

    private function updateStudentInfo($student_crm_id, $ec_hrm_id, $branch_hrm_id, $token)
    {
        // $effecAPI = new EffectAPIController();
        $request = new Request();

        $request->api_url = 'effect/changeSalesInCenter';
        $request->api_method = 'POST';
        $request->api_params = json_encode([
            'ma_crm' => "$student_crm_id",
            'crm_bo_phan' => "$branch_hrm_id",
            'crm_salesman' => "$ec_hrm_id"
        ]);

        // $effecAPI->callAPI($request, $token);
    }

    private function updateSale($info, $token, $status = 1)
    {
        // $effecAPI = new EffectAPIController();
        $request = new Request();

        $request->api_url = 'effect/sales';
        $request->api_method = 'PUT';
        $request->api_params = json_encode([
            'ten' => "$info->full_name",
            'ma_crm' => "$info->hrm_id",
            'crm_bo_phan' => "$info->branch_hrm_id",
            'crm_leader' => "$info->superior_id",
            'gioitinh' => 'M',
            'dia_chi' => "$info->address",
            'phone' => "$info->phone",
            'disable' => $status ? true : false
        ]);

        // $effecAPI->callAPI($request, $token);
    }

    private function updateSaleLeader($info, $token, $status = 1)
    {
        // $effecAPI = new EffectAPIController();
        $request = new Request();

        $request->api_url = 'effect/leader';
        $request->api_method = 'PUT';
        $request->api_params = json_encode([
            'ten' => "$info->full_name",
            'ma_crm' => "$info->hrm_id",
            'crm_bo_phan' => "$info->branch_hrm_id",
            'gioitinh' => 'M',
            'dia_chi' => "$info->address",
            'phone' => "$info->phone",
            'disable' => $status ? true : false
        ]);

        // $effecAPI->callAPI($request, $token);
    }

    private function getEffectSaleInfo($hrm_id, $token)
    {
        // $effecAPI = new EffectAPIController();
        $request = new Request();

        $request->api_url = "effect/saleInfo?ma_crm=$hrm_id";
        $request->api_method = 'GET';
        $request->api_params = '';

        // $res = $effecAPI->callAPI($request, $token);

    }

    private function getEffectSaleLeaderInfo($hrm_id, $token)
    {
        // $effecAPI = new EffectAPIController();
        $request = new Request();

        $request->api_url = "effect/leaderInfo?ma_crm=$hrm_id";
        $request->api_method = 'GET';
        $request->api_params = '';

        // $effecAPI->callAPI($request, $token);
    }

    public function updatePassword(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        $mail = new Mail();
        if ($session = $request->users_data) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            $data->success = false;
            $data->message = "Tài khoản người dùng này không tồn tại.";
            if ($user_ids = $request->user) {
                $user_info = u::query("SELECT id, full_name, username, email, `password` FROM users WHERE id IN ($user_ids)");
                $email = $request->email;
                if (count($user_info)) {
                    $old_pass = $request->old != '' ? trim($request->old) : '';
                    $valid = true;
                    if ($old_pass != '' && $request->mine) {
                        if (!Hash::check($old_pass, $user_info[0]->password)) {
                            $valid = false;
                        }
                    }
                    if ($valid) {
                        $new_pass = trim($request->confirm);
                        $new_password = bcrypt(trim($request->confirm));
                        $timeUpdate = now();
                        u::query("UPDATE users SET password = '$new_password',last_change_password_date = '$timeUpdate' WHERE id IN ($user_ids)");
                        foreach ($user_info as $user_item) {
                            $user_email = $user_item->email;

                            if (APP_ENV == 'product') {
                                $web_url = APP_URL;
                                $subject = "Thông báo thay đổi mật khẩu";
                                $content = "Dear $user_item->full_name,<br><br>";
                                $content .= "Hệ thống CRM gửi anh/ chị thông tin đăng nhập <a href='$web_url'>$web_url</a><br><br>";
                                $content .= "<b>Username</b>: $user_item->username<br><br>";
                                $content .= "<b>Password</b>: $new_pass<br><br>";
                                $content .= "Trân trọng!";
                                $mail->sendSingleMail(
                                    [
                                        'address' => $user_email,
                                        'name' => $user_item->full_name
                                    ],
                                    $subject,
                                    $content
                                );
                            }
                        }
                        if (md5(trim($email)) != md5(trim($user_email))) {
                            if (APP_ENV == 'product') {
                                $web_url = APP_URL;
                                $subject = "Thông báo thay đổi mật khẩu";
                                $content = "Dear $user_item->full_name,<br><br>";
                                $content .= "Hệ thống CRM gửi anh/ chị thông tin đăng nhập <a href='$web_url'>$web_url</a><br><br>";
                                $content .= "<b>Password</b>: $new_pass<br><br>";
                                $content .= "Trân trọng!";
                                $mail->sendSingleMail(
                                    [
                                        'address' => $email,
                                        'name' => 'CRM user'
                                    ],
                                    $subject,
                                    $content
                                );
                            }
                        }
                        $data->success = true;
                        $data->message = "success";
                    } else {
                        $data->success = false;
                        $data->message = "Mật khẩu cũ không chính xác, xin vui lòng nhập lại.";
                    }
                }
            }
        }
        return $response->formatResponse($code, $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->hasFile('avatar')) {
            $user->avatar = 'uploads/avatar.png';
        } else {
            $user->avatar = 'uploads/avatar2.png';
        }
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'user' => $user,
            'message' => 'Success'
        ], 200);

        // \Log::info($request->all());
        // $user = User::create($request->except('avatar'));

        // $img = $request->get('avatar');

        //remove extra part
        // $exploded = explode(',', $img);

        //Take the extention
        // if (str_contains($exploded[0], 'png')) {
        //     $ext = 'png';
        // } else if(str_contains($exploded[0], 'gif')){
        //     $ext = 'gif';
        // } else {
        //     $ext = 'jpg';
        // }
        // $decoded = base64_decode($exploded[1]);
        // $fileName = str_random()."-".$ext;

        // //path of local folder
        // $path = public_path()."/uploads/".$fileName;

        // //upload image to path


        // $user = new User();

        // $user->name = $request->name;
        // $user->email = $request->email;
        // if ($request->hasFile('avatar')) {
        //     $file = $request->file('avatar');
        //     $fileName = time().$file->getClientOriginalName();
        //     $file->storeAs('uploads', $fileName);
        //     $user->avatar = 'uploads/'.$fileName;
        // }
        // $user->password = bcrypt($request->password);
        // $user->save();
        // return response($user);
        // return $request->all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id, Request $request)
    {
        $response = [
            'code' => 300,
            'message' => 'Invalid token'
        ];
        if ($session = json_decode($request->authorized)) {
            $query = "SELECT
                          u.id AS user_id,
                          u.hrm_id AS hrm_id,
                          u.accounting_id AS accounting_id,
                          u.full_name AS full_name,
                          u.username AS username,
                          u.email AS email,
                          u.avatar AS avatar,
                          u.status AS `status`,
                          CONCAT(u.hrm_id, '~', u.accounting_id) AS mixing_id,
                          o.name AS title,
                          o.description AS title_description,
                          u1.id AS boss_id,
                          u1.full_name AS boss,
                          b.name AS branch,
                          r.name AS region
                        FROM
                          users AS u
                          LEFT JOIN term_user_branch AS t ON t.user_id = u.id
                          LEFT JOIN users AS u1 ON u.superior_id = u1.hrm_id
                          INNER JOIN branches AS b ON t.branch_id = b.id
                          INNER JOIN regions AS r ON b.region_id = r.id
                          INNER JOIN roles AS o ON t.role_id = o.id
                        WHERE
                          u.id = $id";
            $user = DB::select(DB::raw($query));
            if ($user) {
                $response['code'] = 200;
                $response['user'] = $user[0];
                $response['message'] = 'successful';
            }
        }
        return $response;
    }

    public function password(Request $request, $pass)
    {
        die(bcrypt($pass));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $pass)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required'
        ]);

        $user = User::find($id);

        if ($user->count()) {
            $user->update($request->all());
            return response()->json(['status' => 'success', 'message' => 'Successfully Updated']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Unsuccessfully Updated']);
        }

    }

    public function upload(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        return redirect('/');
    }

    public function remove($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json('Successfully Deleted');
    }

    public function uploadExcel(Request $request)
    {
        $userData = $request->users_data;
        $code = APICode::SUCCESS;
        $data = null;
        $response = new Response();

        $attachedFile = $request->attached_file;
        if (!$attachedFile) {
            $code = APICode::PERMISSION_DENIED;
            return $response->formatResponse($code, $data);
        }

        // SAVE FILES TO SERVER
        $explod = explode(',', $attachedFile);
        $decod = base64_decode($explod[1]);
        if (str_contains($explod[0], 'spreadsheetml')) {
            $extend = 'xlsx';
        } else {
            $code = APICode::PERMISSION_DENIED;
            return $response->formatResponse($code, $data);
        }
        $fileAttached = md5($request->attached_file . str_random()) . '.' . $extend;
        $p = FOLDER . DS . 'doc\\other\\' . $fileAttached;
        file_put_contents($p, $decod);

        $reader = new x();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($p);
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $dataXslx = $sheet->toArray();
        //END

        unset($dataXslx[0]);
        $validationFile = $this->validateContentExcel($dataXslx);
        if ($validationFile == false) {
            $code = APICode::PERMISSION_DENIED;
            return $response->formatResponse($code, $data);
        }

        $dataResult = [];
        DB::beginTransaction();
        try {
            if ($validationFile == 'block') {
                $dataResult = $this->blockUserFromExcel($dataXslx, $userData);
                $excel = 0;
            }
            if ($validationFile == 'insert') {
                $dataResult = $this->insertUsersFromExcel($dataXslx, $userData);
                $excel = 1;
            }
            DB::commit();
            $result = [
                'excel' => $excel,
                'data' => $dataResult
            ];
            return $response->formatResponse($code, $result);
        } catch (\Mockery\Exception $e) {
            DB::rollback();
            $code = 500;
            return $response->formatResponse($code, $data);
        }
    }

    private function insertUsersFromExcel($datas, $userData)
    {
        $mail = new Mail();
        $dataSuccess = $dataError = [];
        $cyberAPI = new CyberAPI();
        foreach ($datas as $row) {
            if ($row[0] != null) {
                if ($row[1] != null
                    and $row[3] != null
                    and $row[4] != null
                    and $row[5] != null
                    and $row[8] != null
                    and (!in_array((int)$row[5], [ROLE_EC_LEADER, ROLE_EC])) || ($row[6] != null and $row[7] != null)) {

                    $checkUser = DB::table('users')->where('email', $row[3])->count();
                    if ($checkUser > 0) {
                        $row[10] = 0;
                        $row[11] = 'Email đã tồn tại trên hệ thống';
                        $dataError[] = $row;
                        continue;
                    }
                    $checkHrm = DB::table('users')->where('hrm_id', $row[0])->count();
                    if ($checkHrm > 0) {
                        $row[10] = 0;
                        $row[11] = 'HrmID đã tồn tại trên hệ thống';
                        $dataError[] = $row;
                        continue;
                    }
                    $userName = explode('@', $row[3]);
                    $dataInsertUser = [
                        'hrm_id' => ($checkHrm > 0) ? "G0000" : trim(str_replace("\r", '', str_replace("\n", '', $row[0]))),
                        'accounting_id' => $row[6] ? trim(str_replace("\r", '', str_replace("\n", '', $row[6]))) : null,
                        'full_name' => trim(str_replace("\r", '', str_replace("\n", '', $row[1]))),
                        'phone' => ($row[2]) ? $row[2] : '',
                        'email' => $row[3] ? trim(str_replace("\r", '', str_replace("\n", '', $row[3]))) : null,
                        'username' => strtoupper($userName[0]),
                        'password' => bcrypt('@12345678'),
                        'start_date' => $this->_convertDateFromExcel($row[4]),
                        'superior_id' => ($row[7]) ? trim(str_replace("\r", '', str_replace("\n", '', $row[7]))) : '',
                        'created_at' => now(),
                        'updated_at' => now(),
                        'status' => 1
                    ];
                    $idUserCreated = DB::table('users')->insertGetID($dataInsertUser);
                    $branch = DB::table('branches')->select(['id', 'accounting_id'])->where('id', $row[8])->first();

//                    if(in_array($row[5], [ROLE_CM, ROLE_OM, ROLE_CS_CASHIER, ROLE_CS_LEADER_CASHIER, ROLE_EC, ROLE_EC_LEADER]) && $idUserCreated){
//                      if(!$dataInsertUser['accounting_id']){
//
//                        $sale = (Object)[
//                          'id' => $idUserCreated,
//                          'full_name' => $dataInsertUser['full_name'],
//                          'phone' => $dataInsertUser['phone'],
//                          'branch_accounting_id' => $branch->accounting_id
//                        ];
//                        $cyber_id = $cyberAPI->createSale($sale, $userData->id);
//
//                        if($cyber_id){
//                          u::query("UPDATE users SET accounting_id = '$cyber_id' WHERE id = $idUserCreated");
//                        }
//                      }
//                    }

                    $dataInsertTBU = [
                        'user_id' => $idUserCreated,
                        'branch_id' => ($branch) ? $branch->id : 0,
                        'role_id' => $row[5],
                        'created_at' => now(),
                        'updated_at' => now(),
                        'status' => 1
                    ];
                    // if ($row[5] == 68 AND $row[6] == null) {
                    //   $dataInsertTBU['status'] = 0;
                    // }
                    DB::table('term_user_branch')->insert($dataInsertTBU);

                    $dataInserLogWEU = [
                        'user_id' => $idUserCreated,
                        'branch_id' => ($branch) ? $branch->id : 0,
                        'role_id' => $row[5],
                        'start_date' => $this->_convertDateFromExcel($row[4]),
                        'count_change' => 1,
                        'creator_id' => $userData->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'superior_id' => ($row[7]) ? trim(str_replace("\r", '', str_replace("\n", '', $row[7]))) : ''
                    ];
                    if ($row[5] == 36) {
                        $idTeacherCreated = DB::table('teachers')->insertGetID(array(
                            'user_id' => $idUserCreated,
                            'ins_name' => trim(str_replace("\r", '', str_replace("\n", '', $row[1]))),
                            'created_at' => now(),
                            'updated_at' => now(),
                            'meta_data' => json_encode(array('user_id' => $idUserCreated)),
                        ));
                        DB::table('term_teacher_branch')->insertGetID(array(
                            'teacher_id' => $idTeacherCreated,
                            'branch_id' => ($branch) ? $branch->id : 0,
                            'is_head_teacher' => 0,
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ));
                        $content = "
                                <!DOCTYPE html>
                                  <html>
                                  <head>
                                    <meta charset='utf-8'>
                                    <meta name='viewport' content='width=device-width'>
                                    <style type='text/css'>
                                      body{ font-family: 'Times New Roman', Times, serif; font-style: italic; }
                                      p{ font-style: normal; }
                                    </style>
                                  </head>
                                  <body>
                                    <p>Kính gửi: {$row[1]}</p>
                                    <p>CMS gửi tới anh/chị thông tin tài khoản:</p>
                                    <br>
                                    <p>1. Tài khoản CRM:</p>
                                    <p>- Tài khoản đăng nhập: <b>{$row[0]}</b></p>
                                    <p>- Mật khẩu: <b>@12345678</b></p>
                                    <p>- Link đăng nhập: <a href='https://account.cmsedu.vn'>https://account.cmsedu.vn</a></p> 
                                    <p> Link hướng dẫn: <a href='https://drive.google.com/drive/folders/1XO-3nVOd-4FrYCh_0xkFV7MK8I1IXPDt?usp=sharing'>https://drive.google.com/drive/folders/1XO-3nVOd-4FrYCh_0xkFV7MK8I1IXPDt?usp=sharing</a></p>
                                    <br>
                                    <p>2. Tài khoản LMS:</p>
                                    <p>- Tài khoảng đăng nhập: <b>{$userName[0]}</b></p>
                                    <p>- Mật khẩu: <b>@12345678</b></p>
                                    <p>- Link đăng nhập: <a href='https://lms-vn.cmsedu.net'>https://lms-vn.cmsedu.net</a></p>
                                    <br>
                                    <p>Trân trọng cảm ơn!</p>
                                  </body>
                                  </html>
                               ";
                    }else{
                        $content = "
                                <!DOCTYPE html>
                                  <html>
                                  <head>
                                    <meta charset='utf-8'>
                                    <meta name='viewport' content='width=device-width'>
                                    <style type='text/css'>
                                      body{ font-family: 'Times New Roman', Times, serif; font-style: italic; }
                                      p{ font-style: normal; }
                                    </style>
                                  </head>
                                  <body>
                                  <strong>Dear {$row[1]},</strong><br/><br/>
                                  <strong>Hệ thống CRM gửi anh/ chị Thông tin đăng nhập <a href='https://account.cmsedu.vn/'>https://account.cmsedu.vn/</a></strong><br/><br/>
                                  <i>Username:</i> <b>{$row[0]}</b><br/>
                                  <i>Password:</i> <b>@12345678</b><br/>
                                  <i>Link hướng dẫn: <a href='https://drive.google.com/drive/folders/1XO-3nVOd-4FrYCh_0xkFV7MK8I1IXPDt?usp=sharing'>https://drive.google.com/drive/folders/1XO-3nVOd-4FrYCh_0xkFV7MK8I1IXPDt?usp=sharing</a></i>
                                  <br/><br/>
                                  <i>Trân trọng cảm ơn !</i>
                                  </body>
                                  </html>
                               ";
                    }
                    DB::table('log_work_experience_user')->insert($dataInserLogWEU);
                    $row[10] = 1;
                    $dataSuccess[] = $row;
                    
                    $subject = "Thông báo user đăng nhập hệ thống CRM";
                    if (APP_ENV == 'product') {
                        $mail->sendSingleMail(
                            [
                                'address' => $row[3],
                                'name' => strtoupper($userName[0])
                            ],
                            $subject,
                            $content
                        );
                    } else {
                        $mail->sendSingleMail(
                            [
                                'address' => Mail::STAGING_EMAIL,
                                'name' => strtoupper($userName[0])
                            ],
                            $subject,
                            $content
                        );
                    }

                } else {
                    $row[10] = 0;
                    $row[11] = 'Thiếu thông tin';
                    $dataError[] = $row;
                }
            }
        }
        return array_merge($dataSuccess, $dataError);
    }

    private function blockUserFromExcel($datas, $userData)
    {
        $dataBlock = [];
        foreach ($datas as $row) {
            if ($row[0] != null) {
                if ($row[1] != null)
                    $end_date = $this->_convertDateFromExcel($row[1]);
                else
                    $end_date = date('Y-m-d');

                $checkUser = DB::table('users')->where('hrm_id', $row[0])->first();
                if ($checkUser) {
                    if ($checkUser->status == 1) {
                        DB::table('term_user_branch')->where('user_id', $checkUser->id)->update(['status' => 0]);
                        DB::table('regions')->where('ceo_id', $checkUser->id)->update(['ceo_id' => null]);
                        $dataUpdate = ['status' => 0, 'end_date' => $end_date];
                        DB::table('users')->where('hrm_id', $row[0])->update($dataUpdate);
                        $this->progressBlock($checkUser->hrm_id, $userData);
                        $row[2] = 1;
                        $dataBlock[] = $row;
                        DB::table('term_teacher_branch as tb')->leftJoin('teachers as t', 't.id', 'tb.teacher_id')
                            ->where('t.user_id', $checkUser->id)->update(['status' => 0]);
                    } else {
                        $row[2] = 0;
                        $row[3] = 'User này đã bị khóa trước đây';
                        $dataBlock[] = $row;
                    }
                } else {
                    $row[2] = 0;
                    $row[3] = 'User không tồn tại trên hệ thống';
                    $dataBlock[] = $row;
                }
            }
        }
        return $dataBlock;
    }

    private function progressBlock($hrm_id, $userData)
    {
        $user = DB::table('term_user_branch as tub')
            ->select(['tub.role_id', 'tub.user_id', 'u.superior_id', 'tub.branch_id', 'u.hrm_id', 'tub.branch_id'])
            ->join('users as u', 'u.id', 'tub.user_id')
            ->where('u.hrm_id', $hrm_id)
            ->first();
        if (!$user)
            return true;
        $filterData = null;
        if ($user->role_id == User::ROLE_CM or $user->role_id == User::ROLE_OM) {
            $column = 'cm_id';
            if ($user->role_id == User::ROLE_OM) {
                $filterData = DB::table('users as u')
                    ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                    ->where('u.superior_id', $user->hrm_id)
                    ->where('tub.branch_id', $user->branch_id)
                    ->whereNotIn('u.id', [$user->user_id])->where('u.status', 1)->first();
            } else if ($user->role_id == User::ROLE_CM) {
                if ($user->superior_id != null) {
                    $filterData = DB::table('users as u')
                        ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                        ->where('u.hrm_id', $user->superior_id)->where('tub.branch_id', $user->branch_id)
                        ->whereNotIn('u.id', [$user->user_id])
                        ->where('u.status', 1)->first();
                } else {
                    $filterData = DB::table('users as u')
                        ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                        ->where('tub.branch_id', $user->branch_id)->where('tub.role_id', User::ROLE_CM)
                        ->where('u.status', 1)->where('tub.branch_id', $user->branch_id)
                        ->whereNotIn('u.id', [$user->user_id])->inRandomOrder()->first();
                }
            }
        }

        if ($user->role_id == User::ROLE_EC or $user->role_id == User::ROLE_EC_LEADER) {
            $column = 'ec_id';
            if ($user->role_id == User::ROLE_EC_LEADER) {
                $filterData = DB::table('users as u')
                    ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                    ->where('u.superior_id', $user->hrm_id)->where('tub.branch_id', $user->branch_id)
                    ->whereNotIn('u.id', [$user->user_id])->where('u.status', 1)->first();
            } else if ($user->role_id == User::ROLE_EC) {
                if ($user->superior_id != null and $user->superior_id != 'G0000') {
                    $filterData = DB::table('users as u')
                        ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                        ->where('u.hrm_id', $user->superior_id)
                        ->where('tub.branch_id', $user->branch_id)->where('u.status', 1)
                        ->whereNotIn('u.id', [$user->user_id])->first();
                } else {
                    $leaders = DB::table('users as u')
                        ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                        ->where('tub.branch_id', $user->branch_id)->where('tub.role_id', User::ROLE_EC_LEADER)
                        ->where('u.hrm_id', '!=', 'G0000')->where('u.status', 1)->whereNotIn('u.id', [$user->user_id])
                        ->inRandomOrder()->first();
                    if ($leaders) {
                        $filterData = $leaders;
                    } else {
                        $filterData = DB::table('users as u')
                            ->join('term_user_branch as tub', 'tub.user_id', 'u.id')
                            ->where('tub.branch_id', $user->branch_id)->where('tub.role_id', User::ROLE_EC)
                            ->where('u.status', 1)->whereNotIn('u.id', [$user->user_id])
                            ->inRandomOrder()->first();
                    }
                }
            }
        }

        // Update
        if (isset($column) && $filterData) {
            $tsu = DB::table('term_student_user')->where($column, $user->user_id)->get();
            $dataUpdateTsu = [$column => $filterData->user_id];
            DB::table('term_student_user')->where($column, $user->user_id)->update($dataUpdateTsu);
            $logMT = [];
            foreach ($tsu as $item) {
                $logMT[] = [
                    'student_id' => $item->student_id,
                    'from_' . $column => $user->user_id,
                    'to_' . $column => $filterData->user_id,
                    'from_branch_id' => $user->branch_id,
                    'to_branch_id' => $user->branch_id,
                    'updated_by' => $userData->id,
                    'date_transfer' => date('Y-m-d'),
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ];

//          $dataUpdateTsu = [
//            $column => $filterData->user_id
//          ];
//          DB::table('term_student_user')->where('student_id',$item->student_id)->update($dataUpdateTsu);
            }
            DB::table('log_manager_transfer')->insert($logMT);
        }
    }

    private function validateContentExcel($datas)
    {
        if (isset($datas[1][4]) and $datas[1][4] != null)
            return 'insert';
        else
            return 'block';
    }

    public function _convertDateFromExcel($dateTime, $format = 'Y-m-d')
    {
        if (is_numeric($dateTime) == false) {
            return date($format, strtotime($dateTime));
        } else {
            $excel_date = $dateTime;
            $unix_date = ($excel_date - 25569) * 86400;
            $excel_date = 25569 + ($unix_date / 86400);
            $unix_date = ($excel_date - 25569) * 86400;
            $result = date($format, $unix_date);
            return $result;
        }
    }

    public function getUserInfo($id)
    {
        $q = "SELECT u.*,
                o.id as role_id,
                o.name as role_name,
                br.id as branch_id,
                br.name as branch_name
              FROM users AS u
              LEFT JOIN term_user_branch AS tu ON u.id = tu.user_id
              LEFT JOIN users AS superior ON u.hrm_id = superior.superior_id
              LEFT JOIN branches as br ON br.id = tu.branch_id
              INNER JOIN roles AS o ON tu.role_id = o.id
              WHERE u.id = $id ORDER BY tu.id DESC
          ";

        $user = DB::selectOne(DB::raw($q));
        return response()->json($user);
    }

    public function getUserInformation(Request $request, $user_id)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            $info = u::first("SELECT u1.id, u1.full_name, u1.username, u1.hrm_id, u1.accounting_id,
                                        u1.superior_id, u1.phone, u1.email, u1.start_date, u1.end_date, u1.status, u1.meta_data,
                                        COALESCE(u1.avatar, '/static/img/avatars/users/noavatar.png') avatar,
                                        COALESCE(CONCAT(u2.full_name, ' - ', u2.hrm_id), '') superior_name,
                                        (SELECT MAX(role_id) FROM term_user_branch WHERE user_id = u1.id AND status = 1 ) role_id,
                                        (SELECT `name` FROM roles WHERE id IN (SELECT MAX(role_id) FROM term_user_branch WHERE status > 0 AND user_id = u1.id) LIMIT 0, 1) title,
                                        (SELECT id FROM term_user_branch WHERE user_id = u1.id AND role_id IN (SELECT MAX(role_id) FROM term_user_branch WHERE status > 0 AND user_id = u1.id) LIMIT 0, 1) term_id,
                                        (SELECT branch_id FROM term_user_branch WHERE user_id = u1.id AND role_id IN (SELECT MAX(role_id) FROM term_user_branch WHERE status > 0 AND user_id = u1.id) LIMIT 0, 1) branch_id,
                                        (SELECT zone_id FROM branches WHERE id IN (SELECT branch_id FROM term_user_branch WHERE user_id = u1.id AND role_id IN (SELECT MAX(role_id) FROM term_user_branch WHERE status > 0 AND user_id = u1.id)) LIMIT 0, 1) zone_id,
                                        (SELECT region_id FROM branches WHERE id IN (SELECT branch_id FROM term_user_branch WHERE user_id = u1.id AND role_id IN (SELECT MAX(role_id) FROM term_user_branch WHERE status > 0 AND user_id = u1.id)) LIMIT 0, 1) region_id
                                      FROM users u1
                                        LEFT JOIN users u2 ON u1.superior_id = u2.hrm_id
                                      WHERE u1.id = $user_id GROUP BY u1.id");
            $roleSQL = "SELECT t.id, t.`role_id`,t.`branch_id`, b.`name` branch_name, r.`name` role_name, t.created_at 
                        FROM `term_user_branch` t
                        LEFT JOIN `branches` b ON t.`branch_id` = b.`id`
                        LEFT JOIN `roles` r ON t.`role_id` = r.id
                        WHERE t.user_id = $user_id ORDER BY t.role_id DESC";
            $data->role_list = u::query($roleSQL);
            $link_path_avatar = $info->avatar;
            $real_path_avatar = DIRECTORY . str_replace('/', DS, $link_path_avatar);
            $info->avatar = file_exists($real_path_avatar) ? $link_path_avatar : AVATAR_LINK . 'avatar.png';
            $data->user = $info;
            $data->own_branches = u::query("SELECT b.id, b.name FROM branches b LEFT JOIN term_user_branch t ON t.branch_id = b.id WHERE t.user_id = $user_id GROUP BY b.id");
            $data->own_regions = u::query("SELECT r.id, r.name FROM regions r LEFT JOIN branches b ON b.region_id = r.id LEFT JOIN term_user_branch t ON t.branch_id = b.id WHERE t.user_id = $user_id GROUP BY r.id");
            $data->roles = u::query("SELECT id, name FROM roles WHERE `status` > 0 AND id <= $session->role_id");
            // $data->zones = u::query("SELECT z.id, z.name FROM zones z RIGHT JOIN branches b ON b.zone_id = z.id WHERE z.status > 0 AND b.id IN ($session->branches_ids) GROUP BY z.id");
            // $data->regions = u::query("SELECT r.id, r.name FROM regions r RIGHT JOIN branches b ON b.region_id = r.id WHERE r.status > 0 AND b.id IN ($session->branches_ids) GROUP BY r.id");
            // $data->branches = u::query("SELECT id, name FROM branches WHERE `status` > 0 AND id IN ($session->branches_ids)");
            $data->zones = u::query("SELECT z.id, z.name FROM zones z RIGHT JOIN branches b ON b.zone_id = z.id WHERE z.status > 0 GROUP BY z.id");
            $data->regions = u::query("SELECT r.id, r.name FROM regions r RIGHT JOIN branches b ON b.region_id = r.id WHERE r.status > 0 GROUP BY r.id");
            $data->branches = u::query("SELECT id, name FROM branches WHERE `status` > 0");
            $data->history = u::query("SELECT l.id, l.user_id, l.branch_id, l.role_id, l.superior_id, l.status, l.count_change, l.meta_data, DATE_FORMAT(l.start_date, '%Y-%m-%d') start_date, DATE_FORMAT(l.end_date, '%Y-%m-%d') end_date, b.name branch_name, r.name title FROM log_work_experience_user l LEFT JOIN branches b ON l.branch_id = b.id LEFT JOIN roles r ON l.role_id = r.id WHERE l.user_id = $user_id ORDER BY l.start_date DESC, l.id DESC");
        }
        return $response->formatResponse($code, $data);
    }

    public function getUsersManagement(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            // $users_info = u::query("SELECT
            //   u.id AS user_id,
            //   u.hrm_id AS hrm_id,
            //   u.accounting_id AS accounting_id,
            //   u.full_name AS full_name,
            //   u.username AS username,
            //   u.email AS email,
            //   COALESCE(u.avatar, '/static/img/avatars/users/noavatar.png') avatar,
            //   u.status AS `status`,
            //   CONCAT(u.hrm_id, '~', u.accounting_id) AS mixing_id,
            //   o.name AS title,
            //   o.description AS title_description,
            //   u1.id AS boss_id,
            //   u1.full_name AS boss,
            //   u1.hrm_id as boss_hrm_id,
            //   b.name AS branch,
            //   r.name AS region
            // FROM
            //   users AS u
            //   LEFT JOIN term_user_branch AS t ON t.user_id = u.id
            //   LEFT JOIN users AS u1 ON u.superior_id = u1.hrm_id
            //   INNER JOIN branches AS b ON t.branch_id = b.id
            //   INNER JOIN regions AS r ON b.region_id = r.id
            //   INNER JOIN roles AS o ON t.role_id = o.id
            // WHERE u.id > 0 AND u.status = 1 AND r.id <= $session->role_id
            //   AND t.branch_id IN ($session->branches_ids)
            // GROUP BY u.id ORDER BY u.id DESC");
            // $users = $users_info;
            // $users = [];
            // if ($users_info) {
            //   foreach ($users_info as $info) {
            //     $link_path_avatar = $info->avatar;
            //     $real_path_avatar = DIRECTORY.str_replace('/', DS, $link_path_avatar);
            //     $info->avatar = file_exists($real_path_avatar) ? $link_path_avatar : AVATAR_LINK.'avatar.png';
            //     $users[] = $info;
            //   }
            // }
            // $data->users = $users;
            $data->roles = u::query("SELECT id, name FROM roles WHERE `status` > 0 AND id <= $session->role_id");
            $data->branches = u::query("SELECT id, name FROM branches WHERE `status` > 0 AND id IN ($session->branches_ids)");
        }
        return $response->formatResponse($code, $data);
    }

    public function search(Request $request)
    {
        $data = null;
        $code = APICode::PERMISSION_DENIED;
        $response = new Response();
        if ($session = $request->users_data) {
            $data = (Object)[];
            $code = APICode::SUCCESS;
            $post = (Object)$request->input();
            $search = (Object)$post->searchdatas;
            $pagination = (Object)$post->pagination;
            $where = null;
            $hrm_id = $search->hrm_id;
            if ($hrm_id) {
                $where .= " AND u.hrm_id like '%$hrm_id%'";
            }
            $branch = isset($search->branch) ? $search->branch : 0;
            if ($branch) {
                $where .= " AND t.branch_id = '$branch'";
            }
            $superior_id = $search->superior_hrm_id;
            if ($superior_id) {
                $where .= " AND u.superior_id like '%$superior_id%'";
            }
            $name = $search->name;
            if ($name) {
                $where .= " AND u.full_name like '%$name%'";
            }

            $role = $search->role;
//            $role_ids = [];
//            foreach ($roles as $role) {
//                $role_ids[] = $role['id'];
//            }
//            $role_ids_string = implode(',', $role_ids);

            if (is_numeric($role) && $role > 0) {
                $where .= " AND t.role_id IN ($role)";
            }

            $email = $search->email;
            if ($email) {
                $where .= " AND u.email like '%$email%'";
            }
            $phone = $search->phone;
            if ($phone) {
                $where .= " AND u.phone like '%$phone%'";
            }
            $st = $search->status;
            if ($st) {
                $status = (int)$st;
                if ($status == 1) {
                    $status = 1;
                }
                if ($status == -1) {
                    $status = 0;
                }
                $where .= " AND u.status = $status";
            }
            $start_date = $search->start_date;
            if ($start_date) {
                $where .= " AND u.start_date >= '$start_date'";
            }
            $end_date = $search->end_date;
            if ($end_date) {
                $where .= " AND u.end_date <= '$end_date'";
            }
            $order = "";
            $limit = "";
            if ($pagination->cpage && $pagination->limit) {
                $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
                $limit .= " LIMIT $offset, $pagination->limit";
            } else {
                $limit .= " LIMIT 0, $pagination->limit";
            }
            $list = u::query("SELECT
                                          u.id AS user_id,
                                          u.hrm_id AS hrm_id,
                                          u.accounting_id AS accounting_id,
                                          u.full_name AS full_name,
                                          u.username AS username,
                                          u.phone,
                                          u.email AS email,
                                          u.avatar AS avatar,
                                          u.status AS `status`,
                                          u.start_date,
                                          u.end_date,
                                          if(u.status = 1, 'Đang làm việc', 'Đã nghỉ việc') as status,
                                          CONCAT(u.hrm_id, '~', u.accounting_id) AS mixing_id,
                                          o.name AS title,
                                          o.description AS title_description,
                                          u1.id AS boss_id,
                                          u1.full_name AS boss,
                                          u1.hrm_id as boss_hrm_id,
                                          b.name AS branch,
                                          r.name AS region,
                                          u.superior_id as superior_id
                                        FROM
                                          users AS u
                                          LEFT JOIN term_user_branch AS t ON t.user_id = u.id
                                          LEFT JOIN users AS u1 ON u.superior_id = u1.hrm_id
                                          LEFT JOIN branches AS b ON t.branch_id = b.id
                                          LEFT JOIN regions AS r ON b.region_id = r.id
                                          LEFT JOIN roles AS o ON t.role_id = o.id
                                        WHERE u.id > 0 AND o.id <= $session->role_id
                                        $where AND t.branch_id IN ($session->branches_ids)
                                        GROUP BY u.id ORDER BY u.id DESC $limit");
            $total = u::first("SELECT COUNT(*) AS count FROM (SELECT u.id FROM users AS u
                                          LEFT JOIN term_user_branch AS t ON t.user_id = u.id
                                          LEFT JOIN users AS u1 ON u.superior_id = u1.hrm_id
                                          LEFT JOIN roles AS o ON t.role_id = o.id
                                        WHERE u.id > 0 AND o.id <= $session->role_id
                                        $where AND t.branch_id IN ($session->branches_ids) GROUP BY u.id ) x");
            $data->users = $list;
            $data->duration = $pagination->limit * 10;
            $data->pagination = apax_get_pagination($pagination, (int)$total->count);
        }
        return $response->formatResponse($code, $data);
    }

    public function search2(Request $request)
    {
        $where = null;
        $hrm_id = $request->hrm_id;
        if ($hrm_id) {
            $where .= " AND u.hrm_id like '%$hrm_id%'";
        }
        $branch = $request->branch;
        if ($branch and $branch != 'undefined') {
            $where .= " AND t.branch_id = '$branch'";
        }
        $superior_id = $request->superior_hrm_id;
        if ($superior_id) {
            $where .= " AND u.superior_id like '%$superior_id%'";
        }
        $name = $request->name;
        if ($name) {
            $where .= " AND u.full_name like '%$name%'";
        }

        $email = $request->email;
        if ($email) {
            $where .= " AND u.email like '%$email%'";
        }
        $phone = $request->phone;
        if ($phone) {
            $where .= " AND u.phone like '%$phone%'";
        }
        $st = $request->status;
        if ($st) {
            $status = (int)$st;
            if ($status == 1) {
                $status = 1;
            }
            if ($status == -1) {
                $status = 0;
            }
            $where .= " AND u.status = $status";
        }
        $start_date = $request->start_date;
        if ($start_date) {
            $where .= " AND u.start_date >= '$start_date'";
        }
        $end_date = $request->end_date;
        if ($end_date) {
            $where .= " AND u.end_date <= '$end_date'";
        }

        $query = "
                SELECT
                    u.id AS user_id,
                    u.hrm_id AS hrm_id,
                    u.accounting_id AS accounting_id,
                    u.full_name AS full_name,
                    u.username AS username,
                    u.phone,
                    u.email AS email,
                    u.avatar AS avatar,
                    u.status AS `status`,
                    u.start_date,
                    u.end_date,
                    if(u.status = 1, 'Đang làm việc', 'Đã nghỉ việc') as status,
                    CONCAT(u.hrm_id, '~', u.accounting_id) AS mixing_id,
                    o.name AS title,
                    o.description AS title_description,
                    u1.id AS boss_id,
                    u1.full_name AS boss,
                    u1.hrm_id as boss_hrm_id,
                    b.name AS branch,
                    r.name AS region,
                    u.superior_id as superior_id
                  FROM
                    users AS u
                    LEFT JOIN term_user_branch AS t ON t.user_id = u.id
                    LEFT JOIN users AS u1 ON u.superior_id = u1.hrm_id
                    LEFT JOIN branches AS b ON t.branch_id = b.id
                    LEFT JOIN regions AS r ON b.region_id = r.id
                    LEFT JOIN roles AS o ON t.role_id = o.id
                  WHERE u.id > 0
                  $where
                  GROUP BY u.id ORDER BY u.id DESC
                ";
        $data = DB::select(DB::raw($query));
        return $data;
    }

    public function getUsersList()
    {
        // $data = null;
        // $code = APICode::PERMISSION_DENIED;
        // $response = new Response();
        // $session = $request->users_data;
        // $roles = $session->roles_detail;
        // $branch_list = [];

        $q = "SELECT
                  u.id AS user_id,
                  u.hrm_id AS hrm_id,
                  u.accounting_id AS accounting_id,
                  u.full_name AS full_name,
                  u.username AS username,
                  u.email AS email,
                  u.avatar AS avatar,
                  u.status AS `status`,
                  CONCAT(u.hrm_id, '~', u.accounting_id) AS mixing_id,
                  o.name AS title,
                  o.description AS title_description,
                  u1.id AS boss_id,
                  u1.full_name AS boss,
                  u1.hrm_id as boss_hrm_id,
                  b.name AS branch,
                  r.name AS region
                FROM
                  users AS u
                  LEFT JOIN term_user_branch AS t ON t.user_id = u.id
                  LEFT JOIN users AS u1 ON u.superior_id = u1.hrm_id
                  INNER JOIN branches AS b ON t.branch_id = b.id
                  INNER JOIN regions AS r ON b.region_id = r.id
                  INNER JOIN roles AS o ON t.role_id = o.id
                WHERE
                  u.id > 0 AND u.status = 1
                GROUP BY u.id ORDER BY u.id DESC
              ";

        $users = DB::select(DB::raw($q));

        return response()->json($users);
    }

    public function saveUserInfo(Request $request)
    {
        // dd($request->all());
        $user = $request->user;
        $user_id = $user['id'];
        // dd($user);

        // $role1 = $user['role1'];
        $role_id1 = $user['role_id'];
        // dd($role_id1);

        $role2 = $user['role2'];
        $role_id2 = $role2['id'];

        $role3 = $user['role3'];
        $role_id3 = $role3['id'];

        $role4 = $user['role4'];
        $role_id4 = $role4['id'];

        $role5 = $user['role5'];
        $role_id5 = $role5['id'];
        // dd($role1);

        $start_date1 = $user['start_date1'];
        $end_date1 = $user['end_date1'];

        $start_date2 = $user['start_date2'];
        $end_date2 = $user['end_date2'];

        $start_date3 = $user['start_date3'];
        $end_date3 = $user['end_date3'];

        $start_date4 = $user['start_date5'];
        $end_date4 = $user['end_date5'];

        $start_date5 = $user['start_date5'];
        $end_date5 = $user['end_date5'];

        // dd($start_date2);


        $branch1 = $user['branch1'];
        $branch_id1 = $branch1['id'];

        $branch2 = $user['branch2'];
        $branch_id2 = $branch2['id'];

        $branch3 = $user['branch3'];
        $branch_id3 = $branch3['id'];
        // dd($branch_id3);
        $branch4 = $user['branch4'];
        $branch_id4 = $branch4['id'];

        $branch5 = $user['branch5'];
        $branch_id5 = $branch5['id'];


        $userData = $request->user;
        $workEx = $request->workEx;
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->full_name = $userData['name'];
            $user->hrm_id = $userData['hrm_id'];
            $user->phone = $userData['phone'];
            $user->superior_id = $userData['superior_id'];
            $user->save();

            if ($request->selectedBranches) {
                //DELETE OLD BRANCH

                // UPDATE NEW BRANCH
                foreach ($request->selectedBranches as $br) {
                    $dataInsert = [
                        'branch_id' => $br,
                        'user_id' => $user->id,
                        'role_id' => $request->role->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    //      DB::table('term_user_branch')->insert($dataInsert);
                }
            }

            // Remove Old History
            DB::table('log_work_experience_user')->where('user_id', $user->id)->delete();
            foreach ($workEx as $work) {
                // Insert History new

            }
        }
    }

    public function updateUserInfo(Request $request, $id)
    {
        $editor = $request->users_data;

    }

    public function updateUserTransfer(Request $request)
    {
        $data = ['error_code' =>0, 'msg' =>'Lưu thành công'];
        $user_id = (int)$request->user_id;
        $old_branch_id = $request->old_branch_id;
        $new_branch_id = $request->new_branch_id;
        $old_role_id = $request->old_role_id;
        $new_role_id = (int)$request->new_role_id;
        $update_term_user_branch = false;

        $info = u::first("SELECT created_at FROM `term_user_branch` WHERE user_id = $user_id AND branch_id = $old_branch_id AND role_id= $old_role_id ");
        $startDate = !empty($info->created_at) ? $info->created_at : null;
        if ($old_branch_id == $new_branch_id){
            if ($old_role_id != $new_role_id)
                $update_term_user_branch = true;
        }
        else {
            $roles = [55,56,68,69];
            if (in_array($old_role_id,$roles)){
                $termStudentUser = u::first("SELECT COUNT(id) AS total FROM `term_student_user` WHERE (ec_id = $user_id OR cm_id = $user_id) AND branch_id = $old_branch_id ");
                if ($termStudentUser->total > 0){
                    $data = ['error_code' =>1, 'msg' =>'Lỗi !!! Bạn cần phải chuyển người quản lý học sinh ở trung tâm cũ.'];
                    return response()->json(['data' =>$data]);
                }
                else
                    $update_term_user_branch = true;
            }
            else
                $update_term_user_branch = true;

        }

        if ($update_term_user_branch){
             $sql_1 = "UPDATE `term_user_branch` SET role_id = $new_role_id,branch_id = $new_branch_id 
                           WHERE user_id = $user_id AND branch_id = $old_branch_id AND role_id= $old_role_id ";
            if ($startDate){
                $log = "INSERT INTO `log_user_branch`(`user_id`,`branch_id`,`role_id`,`start_date`,`end_date`)".
                    " VALUES ($user_id,$old_branch_id,$old_role_id,'$startDate',NOW())";
                u::query($log);
            }
            u::query($sql_1);
        }
        return response()->json(['data' =>$data]);

    }

    private function validateUserInfo($info)
    {
        $resp = (Object)[
            "code" => APICode::WRONG_PARAMS,
            "message" => ""
        ];

        if (!isset($info->full_name) || !$info->full_name) {
            $resp->message = "Vui lòng nhập tên nhân viên";
        } else {
            if (!isset($info->full_name) || !$info->full_name) {

            }
        }
    }

    public function exportUserToExcel(Request $request)
    {
        $request->request->add(['system_extend' => true]);
        $dataResult = [];
        $dataResult = $this->search2($request);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'DANH SÁCH NHÂN VIÊN');
        $sheet->mergeCells('A1:K1');
        ProcessExcel::styleCells($spreadsheet, "A1:K1", "FFFFFF", "black", 16, 0, 3, "center", "center", true);

        $sheet->getRowDimension('1')->setRowHeight(30);

        $sheet->setCellValue('A2', 'STT');
        $sheet->setCellValue('B2', 'MÃ NV');
        $sheet->setCellValue('C2', 'MÃ SUPERIOR');
        $sheet->setCellValue('D2', 'TÊN NV');
        $sheet->setCellValue('E2', 'ROLE');
        $sheet->setCellValue('F2', 'TRUNG TÂM');
        $sheet->setCellValue('G2', 'ĐIỆN THOẠI');
        $sheet->setCellValue('H2', 'EMAIL');
        $sheet->setCellValue('I2', 'NGÀY BẮT ĐẦU');
        $sheet->setCellValue('J2', 'NGÀY NGHỈ VIỆC');
        $sheet->setCellValue('K2', 'TRẠNG THÁI');

        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(10);
        $sheet->getColumnDimension("C")->setWidth(10);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(20);
        $sheet->getColumnDimension("F")->setWidth(25);
        $sheet->getColumnDimension("G")->setWidth(15);
        $sheet->getColumnDimension("H")->setWidth(30);
        $sheet->getColumnDimension("I")->setWidth(15);
        $sheet->getColumnDimension("J")->setWidth(15);
        $sheet->getColumnDimension("K")->setWidth(24);


        $x = 3;
        foreach ($dataResult as $row) {

            $sheet->setCellValue("A$x", $x - 2);
            $sheet->setCellValue("B$x", $row->hrm_id);
            $sheet->setCellValue("C$x", $row->superior_id);
            $sheet->setCellValue("D$x", $row->full_name);
            $sheet->setCellValue("E$x", $row->title);
            $sheet->setCellValue("F$x", $row->branch);
            $sheet->setCellValue("G$x", $row->phone);
            $sheet->setCellValue("H$x", $row->email);
            $sheet->setCellValue("I$x", $row->start_date);
            $sheet->setCellValue("J$x", $row->end_date);
            $sheet->setCellValue("K$x", $row->status);

            $x++;
        }


        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Danh sách nhân sự.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }

    public function downloadExample()
    {
        // Excel Import
        $spreadsheet1 = new Spreadsheet();
        $sheet1 = $spreadsheet1->getActiveSheet();
        $sheet1->setCellValue('A1', 'MÃ NV');
        $sheet1->setCellValue('B1', 'Họ Tên NV');
        $sheet1->setCellValue('C1', 'Phone');
        $sheet1->setCellValue('D1', 'Email');
        $sheet1->setCellValue('E1', 'Start Date');
        $sheet1->setCellValue('F1', 'Role');
        $sheet1->setCellValue('G1', 'Mã Cyber');
        $sheet1->setCellValue('H1', 'Superior ID');
        $sheet1->setCellValue('I1', 'Branch');
//        $sheet1->setCellValue('I1', 'Status');
        ProcessExcel::styleCells($spreadsheet1, "A1:I1", "FFFFFF", "black", 10, 0, 3, "center", "center", true);

        $sheet1->getColumnDimension("A")->setWidth(8);
        $sheet1->getColumnDimension("B")->setWidth(10);
        $sheet1->getColumnDimension("C")->setWidth(10);
        $sheet1->getColumnDimension("D")->setWidth(20);
        $sheet1->getColumnDimension("E")->setWidth(20);
        $sheet1->getColumnDimension("F")->setWidth(25);
        $sheet1->getColumnDimension("G")->setWidth(30);
        $sheet1->getColumnDimension("H")->setWidth(15);
        $sheet1->getColumnDimension("I")->setWidth(30);
//        $sheet1->getColumnDimension("I")->setWidth(15);

//        $sheet1->setCellValue('A2', 'G0000');
//        $sheet1->setCellValue('B2', 'Nguyen Van A');
//        $sheet1->setCellValue('C2', '01676443344');
//        $sheet1->setCellValue('D2', 'example@example.com');
//        $sheet1->setCellValue('E2', '12/01/2011');
//        $sheet1->setCellValue('F2', '58');
//        $sheet1->setCellValue('G2', 'G0000');
//        $sheet1->setCellValue('H2', '1');
//        $sheet1->setCellValue('I2', '1');


//        $sheet1->setCellValue('A3', 'G0000');
//        $sheet1->setCellValue('B3', 'Nguyen Van A');
//        $sheet1->setCellValue('C3', '01676443344');
//        $sheet1->setCellValue('D3', 'example@example.com');
//        $sheet1->setCellValue('E3', '12/01/2011');
//        $sheet1->setCellValue('F3', '58');
//        $sheet1->setCellValue('G3', 'G0000');
//        $sheet1->setCellValue('H3', '1');
//        $sheet1->setCellValue('I3', '1');

        $writer = new Xlsx($spreadsheet1);


        try {
            // Sheet 1
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="File_Import_Users.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");

        } catch (Exception $exception) {
            throw $exception;
        }
        exit;

    }

    public function downloadExampleQuit()
    {
        // Excel Import Quit
        $spreadsheet2 = new Spreadsheet();
        $sheet2 = $spreadsheet2->getActiveSheet();
        $sheet2->setCellValue('A1', 'MÃ NV');
        $sheet2->setCellValue('B1', 'END Date');
        ProcessExcel::styleCells($spreadsheet2, "A1:B1", "FFFFFF", "black", 10, 0, 3, "center", "center", true);

        $sheet2->setCellValue('A2', 'G0000');
        $sheet2->setCellValue('B2', '20/12/2030');

        $sheet2->setCellValue('A3', 'G0000');
        $sheet2->setCellValue('B3', '20/12/2030');

        $writer2 = new Xlsx($spreadsheet2);

        try {
            // Sheet 2
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="File_Import_Quit_Users.xlsx"');
            header('Cache-Control: max-age=0');
            $writer2->save("php://output");

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function downloadUsingGuide(Request $request, $role_id)
    {
        // $user = $request->users_data;
        $fileNameUrl = "";
        if ($role_id == 55 || $role_id == 56) {
            $fileNameUrl = DOCUMENT . DS . "docx" . DS . "HDSD_CRM_Vai_tro_CM,OM.docx";
        } else if ($role_id == 68 || $role_id == 69) {
            $fileNameUrl = DOCUMENT . DS . "docx" . DS . "HDSD_CRM_Vai_tro_EC,EC_Leader.docx";
        } else if ($role_id == 83) {
            $fileNameUrl = DOCUMENT . DS . "docx" . DS . "HDSD_CRM_Vai_tro_thu_ngan.docx";
        } else {
            return ("Dữ liệu không tồn tại");
        }
        if (file_exists($fileNameUrl)) {
            return response()->download($fileNameUrl);
        }
    }

    public function getWorkingHistoryList($user_id)
    {
        $qr = "SELECT lu.*, r.id as role_id, b.id as branch_id, lu.count_change, b.name as branch_name, r.name as role_name
                      FROM log_work_experience_user as lu
                      LEFT JOIN branches as b on b.id = lu.branch_id
                      LEFT JOIN roles as r on r.id = lu.role_id
                      where lu.user_id = $user_id
            ";
        $rs = DB::select(DB::raw($qr));

        return response()->json($rs);
    }


    public function getUserWorkingHistoryDetail($user_id)
    {
        $data = (Object)[];

        $query_user = "SELECT u.* from users as u where id = $user_id";

        $user = DB::select(DB::raw($query_user));

        $qr_history = "SELECT lu.* FROM log_work_experience_user as lu where lu.user_id = $user_id ORDER BY lu.id DESC";
        $history = DB::selectOne(DB::raw($qr_history));

        $data->user = $user;

        $data->history = $history;

        return response()->json($data);
    }

    public function saveUserWorkingHistory(Request $request)
    {
        // dd($request->all());
        $creator = $request->users_data;
        $creator_id = $creator->id;
        $user = $request->user;
        $user_id = $user['id'];
        $branch_id = $request->branch;
        $role_id = $request->role;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $count_change = 1;
        $data = null;

        $branch = DB::selectOne(DB::raw("select * from branches where id = $branch_id"));
        // dd($branch);
        $branch_name = $branch->name;

        $update_term = DB::update('update term_user_branch set status = 0 where user_id = ?', $user_id);

        $term = [
            'user_id' => $user_id,
            'branch_id' => $branch_id,
            'role_id' => $role_id,
            'creator_id' => $creator_id,
            'hash_key' => md5(time()),
            'editor_id' => $creator_id,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('term_user_branch')
            ->insert($term);

        $history = [
            'user_id' => $user_id,
            'branch_id' => $branch_id,
            'role_id' => $role_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => 1,
            'count_change' => $count_change,
            'creator_id' => $creator_id,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('log_work_experience_user')
            ->insert($history);

        $data = $history;
        $data['branch_name'] = $branch_name;

        // dd($data);

        return response()->json($data);
    }


    public function editUserWorkingHistory(Request $request, $user_id, $item)
    {
        $user_id = $user_id;

        $history_id = $item;

        $qr = "SELECT u.* FROM users as u WHERE u.id = $user_id";

        $history_qr = "SELECT * FROM log_work_experience_user where id = $item";

        $history = DB::selectOne(DB::raw($history_qr));

        // dd($history);

        $data = DB::selectOne(DB::raw($qr));

        $data->history = $history;

        // dd($data);

        return response()->json($data);
    }

    public function removeUserWorkingHistory(Request $request, $item)
    {
        $log_id = $item;
        $log = DB::table('log_work_experience_user')
            ->where('id', $log_id)
            ->delete();

        return response()->json($log);
    }

    public function updateUserWorking(Request $request)
    {
        $status = !empty($request->working) ? (int)$request->working : 0;
        $userId = !empty($request->user_id) ? (int)$request->user_id : 0;

        $sql_1 = "UPDATE `term_user_branch` SET STATUS = $status WHERE user_id = $userId";
        $sql_2 = "UPDATE `users` SET STATUS = $status WHERE id = $userId";
        u::query($sql_1);
        u::query($sql_2);
        return response()->json(['error_code'=>0, 'msg' =>'Cập nhật thành công.']);
    }

    public function updateUserWorkingHistory(Request $request)
    {
        $editor = $request->users_data;
        $editor_id = $editor->id;
        // dd($editor_id);
        $branch_id = $request->branch;
        $user_id = $request->user_id;
        $history_id = $request->history_id;
        $role_id = $request->role;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        // $user = $
        // dd($request->all());
        $count_change = 1;

        $history = [
            'user_id' => $user_id,
            'branch_id' => $branch_id,
            'role_id' => $role_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'count_change' => $count_change,
            'creator_id' => $editor_id,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('log_work_experience_user')
            ->where('id', $history_id)
            ->update($history);
    }

    public function getUserRankList(Request $request)
    {
        $q = "SELECT * FROM users";

        $data = DB::select(DB::raw($q));

        return response()->json($data);
    }

    public function createEffectSale(Request $request, $user_id)
    {
        $response = new Response();
        $code = APICode::PERMISSION_DENIED;
        $data = null;

        if ($user_id) {
            $user = User::find($user_id);
            $info = u::first("SELECT role_id, branch_id FROM term_user_branch AS t WHERE t.user_id = $user_id AND t.status = 1");
            if (!empty($user) && $user && !empty($info) && $info && !$user->accounting_id) {
                $branch = Branch::find($info->branch_id);
                // $effectAPI = new EffectAPIController();
                $token = $request->headers->get('Authorization');

                $req = new Request();

                $req->api_method = 'POST';
                $req->api_header = json_encode([]);
                $res = null;

                if ($info->role_id == ROLE_EC) {
                    $req->api_url = 'effect/sales';
                    $req->api_params = json_encode([
                        "ten" => "$user->full_name",
                        "ma_crm" => "$user->hrm_id",
                        "crm_bo_phan" => "$branch->hrm_id",
                        "crm_leader" => $user->superior_id ? "$user->superior_id" : "G0000",
                        "gioitinh" => "M",
                        "dia_chi" => "",
                        "phone" => "$user->phone"
                    ]);

                    // $res =  $effectAPI->callAPI($req, $token);
                } elseif ($info->role_id == ROLE_EC_LEADER) {
                    $req->api_url = 'effect/leader';
                    $req->api_params = json_encode([
                        "ten" => "$user->full_name",
                        "ma_crm" => "$user->hrm_id",
                        "crm_bo_phan" => "$branch->hrm_id",
                        "gioitinh" => "M",
                        "phone" => "$user->phone"
                    ]);

                    // $res =  $effectAPI->callAPI($req, $token);
                }

                if ($res) {
                    $da = json_decode($res);
                    if ($da->code == APICode::SUCCESS) {
                        $effect = json_decode($da->data);
                        if ($effect->code == APICode::SUCCESS) {
                            $accounting_id = $effect->data->ma;
                            u::query("UPDATE users SET accounting_id = '$accounting_id' WHERE id = $user_id");
                            $code = APICode::SUCCESS;
                            $data = [
                                'accounting_id' => $accounting_id
                            ];
                        }
                    }
                }
            }
        }

        return $response->formatResponse($code, $data);
    }

    public function createEffectSaleLeader(Request $request, $user_id)
    {

    }

    public function getUserRolesDetail($userId)
    {
        $model = new User();
        $data = $model->getRoleDetail($userId);
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    public function addNew(Request $request)
    {
        $msg = '';
        $error_code = 0;
        $email = $request->email;
        $phone = !empty($request->phone) ? $request->phone : '';
        $where = "WHERE (email = '$email'";
        if ($phone)
            $where .= " OR phone = '$phone'";

        $where .= ")";
        $user_info = u::query("SELECT email, phone FROM users $where");
        if (count($user_info) == 1){
           $uEmail = $user_info[0]->email;
           $uPhone = $user_info[0]->phone;
           if ($phone && $uEmail == $email && $uPhone == $phone){
               $msg = 'Email và số điện thoại đã tồn tại';
           }
           else if($phone && $uPhone == $phone){
               $msg = 'Số điện thoại đã tồn tại';
           }
           else if($uEmail == $email){
               $msg = 'Email đã tồn tại';
           }
           $error_code = 1;
        }

        if ($email && !strpos($email,'cmsedu.vn')){
            $msg = 'Email không phải của CMS EDU';
            $error_code = 1;
        }
        $data = [
            'error_code' =>$error_code,
            'message' =>$msg
        ];

        if ($error_code == 0){
            $username = strtoupper(str_replace("@cmsedu.vn","",$email));
            $branch = [];
            if ($request->role_id == 84){
                $all = Branch::all();
                foreach ($all as $al){
                    $branch[] = $al->id;
                }
            }

            $user = new User();
            $user->full_name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->hrm_id = $request->hrm_id;
            $user->accounting_id = $request->accounting_id;
            $user->superior_id = $request->superior_id;
            $user->username = $username;
            $user->password = $request->password ? bcrypt($request->password) : '$2y$10$Um3J8PaKm4mcTHsDEREbiujzvG5qe.E54SICZsOhlx1mlX2o5DBzK';
            $defaultPassword = $request->password ? ($request->password) : '@12345678';
            $user->save();

            if ($user->id){
                $uid = $user->id;
                $roleId = $request->role_id;
                if ($branch){
                    foreach ($branch as $br){
                        self::addMoreRole($uid, $br, $roleId);
                    }
                }
                else{
                    self::addMoreRole($uid, $request->branch_id, $request->role_id);
                }
                $mail = new Mail();
                $content = "
                                <!DOCTYPE html>
                                  <html>
                                  <head>
                                    <meta charset='utf-8'>
                                    <meta name='viewport' content='width=device-width'>
                                    <style type='text/css'>
                                      body{ font-family: 'Times New Roman', Times, serif; font-style: italic; }
                                      p{ font-style: normal; }
                                    </style>
                                  </head>
                                  <body>
                                  <strong>Dear {$request->name},</strong><br/><br/>
                                  <strong>Hệ thống CRM gửi anh/ chị Thông tin đăng nhập <a href='https://account.cmsedu.vn/'>https://account.cmsedu.vn/</a></strong><br/><br/>
                                  <i>Username:</i> <b>{$user->hrm_id}</b><br/>
                                  <i>Password:</i> <b>{$defaultPassword}</b><br/>
                                  <i>Link hướng dẫn: <a href='https://drive.google.com/drive/folders/1XO-3nVOd-4FrYCh_0xkFV7MK8I1IXPDt?usp=sharing'>https://drive.google.com/drive/folders/1XO-3nVOd-4FrYCh_0xkFV7MK8I1IXPDt?usp=sharing</a></i>
                                  <br/></br>
                                  <i>Trân trọng cảm ơn !</i>
                                  </body>
                                  </html>
                               ";
                $subject = "Thông báo user đăng nhập hệ thống CRM";
                // if (APP_ENV == 'product') {
                    $mail->sendSingleMail(
                        [
                            'address' => $request->email,
                            'name' => strtoupper($username)
                        ],
                        $subject,
                        $content
                    );
                // }
            }
        }
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, $data);
    }

    private function addMoreRole($uid, $br, $roleId){
        $term =  new TermUserBranch();
        $term->user_id = $uid;
        $term->branch_id = $br;
        $term->role_id = $roleId;
        $term->created_at = date('Y-m-d');
        $term->updated_at = date('Y-m-d');
        $term->status = 1;
        $term->save();
    }


    public function editNew($uid, Request $request)
    {
        $response = new Response();
        return $response->formatResponse(APICode::SUCCESS, [$uid]);
    }
}
