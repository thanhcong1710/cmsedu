<?php
require dirname(__DIR__) . '/vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();
if (!defined('DS')) : define('DS', DIRECTORY_SEPARATOR); endif;
if (!defined('ROOT')) : define('ROOT', dirname(__DIR__) . DS); endif;
if (!defined('DIRECTORY')) : define('DIRECTORY', ROOT . 'public'); endif;
if (!defined('FOLDER')) : define('FOLDER', DIRECTORY . DS . 'static'); endif;
if (!defined('IMAGES')) : define('IMAGES', FOLDER . DS . 'img'); endif;
if (!defined('DOCUMENT')) : define('DOCUMENT', FOLDER . DS . 'doc'); endif;
if (!defined('AVATAR')) : define('AVATAR', IMAGES . DS . 'avatars'); endif;
if (!defined('UPLOAD')) : define('UPLOAD', FOLDER . DS . 'uploads'); endif;
if (!defined('PROVIDER')) : define('PROVIDER', ROOT . 'app' . DS . 'providers'); endif;
if (!defined('CONTROLLER')) : define('CONTROLLER', ROOT . 'app' . DS . 'Http' . DS . 'Controllers'); endif;
if (!defined('IMAGES_LINK')) : define('IMAGES_LINK', '/static/img/'); endif;
if (!defined('AVATAR_LINK')) : define('AVATAR_LINK', '/static/img/avatars/'); endif;
if (!defined('UPLOAD_LINK')) : define('UPLOAD_LINK', '/static/uploads/'); endif;
if (!defined('DOCUMENT_LINK')) : define('DOCUMENT_LINK', '/static/doc/'); endif;
if (!defined('ENVIRONMENT')) : define('ENVIRONMENT', env('APP_ENV')); endif;

if (!function_exists('apax_ada_get_page_size')) {
    function apax_ada_get_page_size()
    {
        return [10, 20, 30, 50];
    }
}

if (!function_exists('apax_ada_gen_pass')) {
    function apax_ada_gen_pass($p)
    {
        return bcrypt($p);
    }
}

if (!function_exists('apax_ada_clarea')) {
    function apax_ada_clarea($r)
    {
        return $r * $r * 3.1415926536;
    }
}

if (!function_exists('apax_ada_check_avatar')) {
    function apax_ada_check_avatar($img)
    {
        return ($img && file_exists(str_replace(DS . 'static', FOLDER, str_replace('/', DS, $img))));
    }
}

if (!function_exists('apax_ada_sort_items')) {
    function apax_ada_sort_items($o)
    {
        usort($o, function ($b, $a) {
            return strcmp($a['id'], $b['id']);
        });
        return $o;
    }
}

if (!function_exists('apax_ada_mssql_escape')) {
    function apax_ada_mssql_escape($unsafe_str)
    {
        if (get_magic_quotes_gpc()) {
            $unsafe_str = stripslashes($unsafe_str);
        }
        return $escaped_str = str_replace("'", "''", $unsafe_str);
    }
}

if (!function_exists('apax_ada_get_options')) {
    function apax_ada_get_options($array, $parent = 0, $indent = "", $forget = null)
    {
        // Return variable
        $return = [];
        for ($i = 0; $i < count($array); $i++) {
            $val = $array[$i];
            if ($val->parent_id == $parent && $val->id != $forget) {
                $return["x" . $val->id] = $indent . $val->name;
                $return = array_merge($return, get_options($array, $val->id, "--" . $indent, $forget));
            }
        }
        return $return;
    }
}

if (!function_exists('isJson')) {
    function isJson($string) 
    {
        if (is_string($string)) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        } else return false;
    }
}

if (!function_exists('apax_remove_dublicates')) {
    function apax_remove_dublicates($array = [])
    {
        $resp = $array;
        if (count($array)) {
            $buff = [];
            $list = [];
            foreach ($array as $item) {
                if (!in_array(md5(json_encode($item)), $buff)) {
                    $list[] = $item;
                    $buff[] = md5(json_encode($item));
                }
            }
            $resp = $list;
        }
        return $resp;
    }
}

if (!function_exists('apax_ada_gen_contract_code')) {
    function apax_ada_gen_contract_code($student = '', $ec = '', $branch = '')
    {
        $student = str_replace(' ', '', apax_ada_convert_unicode($student));
        $student = strlen($student) > 2 ? substr($student, 0, 2) : 'SN';
        $ec = str_replace(' ', '', apax_ada_convert_unicode($ec));
        $ec = strlen($ec) > 2 ? substr($ec, 0, 2) : 'EC';
        $branch = str_replace(' ', '', apax_ada_convert_unicode($branch));
        $branch = strlen($branch) > 2 ? substr($branch, 15, 2) : 'BR';
        $timestamp = time();
        return strtoupper("$student$ec$branch$timestamp");
    }
}

if (!function_exists('apax_ada_get_weekday')) {
    function apax_ada_get_weekday($weekday = 0)
    {
        $weekdays = (int)$weekday;
        $response = 'Sunday';
        switch ($weekday) {
            case 1:
                $response = 'Monday';
                break;
            case 2:
                $response = 'Tuesday';
                break;
            case 3:
                $response = 'Wednesday';
                break;
            case 4:
                $response = 'Thursday';
                break;
            case 5:
                $response = 'Friday';
                break;
            case 6:
                $response = 'Saturday';
                break;
        }
        return $response;
    }
}

if (!function_exists('apax_get_tree_data')) {
    function apax_get_tree_data($array = [], $parent = 'parent_id', $note = 'id')
    {
        $resp = array();
        foreach ($array as $sub) {
            $resp[$sub->$parent][] = $sub;
        }
        $fnBuilder = function ($siblings) use (&$fnBuilder, $resp, $note) {
            foreach ($siblings as $k => $sibling) {
                $id = $sibling->$note;
                if (isset($resp[$id])) {
                    $sibling->icon = 'fa fa-folder-open';
                    $sibling->children = $fnBuilder($resp[$id]);
                }
                $siblings[$k] = $sibling;
            }
            return $siblings;
        };
        $tree = count($resp) > 0 && isset($resp[0]) ? $fnBuilder($resp[0]) : null;
        return $tree;
    }
}

if (!function_exists('apax_get_pagination')) {
    function apax_get_pagination($pagination, $total = 0)
    {
        if ($total) {
            $pagination->total = (int)$total;
            $pagination->lpage = (int)$total <= $pagination->limit ? 1 : (int)round(ceil($total / $pagination->limit));
            $pagination->ppage = $pagination->cpage > 0 ? $pagination->cpage - 1 : 0;
            $pagination->npage = $pagination->cpage < $pagination->lpage ? $pagination->cpage + 1 : $pagination->lpage;
        }
        return $pagination;
    }
}

if (!function_exists('apax_ada_get')) {
    function apax_ada_get($data, $field, $default = null)
    {
        $resp = null;
        if ($data) {
            $data = is_array($data) ? $data[0] : $data;
            $resp = property_exists($data, $field) ? $data->$field : $default;
        }
        return $resp;
    }
}

if (!function_exists('apax_ada_unique_object_array')) {
    function apax_ada_unique_object_array($array, $key)
    {
        $temp = array();
        $unique = array_filter($array, function ($v) use (&$temp, $key) {
            if (is_object($v)) $v = (array)$v;
            if (!array_key_exists($key, $v)) return false;
            if (in_array($v[$key], $temp)) {
                return false;
            } else {
                array_push($temp, $v[$key]);
                return true;
            }
        });
        return $unique;
    }
}

if (!function_exists('apax_ada_convert_unicode')) {
    function apax_ada_convert_unicode($str,$ws = true)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        if($ws){
            $str = str_replace(" ", "", str_replace("&*#39;", "", $str));
        }
        
        return $str;
    }
}

if (!function_exists('apax_ada_get_in_array')) {
    function apax_ada_get_in_array($id, $arr, $indent = "")
    {
        $result = null;
        for ($i = 0; $i < count($arr); $i++) {
            if ($id == $indent . $arr[$i]->id) {
                $result = $arr[$i];
                break;
            }
        }
        return $result;
    }
}

if (!function_exists('apax_ada_cate_parent')) {
    function apax_ada_cate_parent($data, $parent = 0, $str = "", $select = 0)
    {
        foreach ($data as $key => $val) {
            $id = $val['id'];
            $name = $val['name'];
            if ($val['parent_id'] == $parent) {
                if ($select != 0 && $id == $select) {
                    echo "<option selected='selected' value='$id'>$str $name</option>";
                } else {
                    echo "<option value='$id'>$str $name</option>";
                }
                cate_parent($data, $id, $str . "--");
            }
        }
    }
}

if (!function_exists('apax_ada_get_voucher')) {
    function apax_ada_get_voucher($key = [])
    {
        $data = [];
        // dd($key);
        foreach ($key as $value) {
            if ($value == 1) {
                $data += ["tích điểm" => 300000];
            } else if ($value == 2) {
                $data += ["voucher" => 5000000];
            } else if ($value == 3) {
                $data += ["chiết khấu" => 500000];
            } else if ($value == 4) {
                $data += ["khác" => 500000];
            }
        }
        // dd($data);
        return $data;
    }
}

if (!function_exists('apax_ada_format_number')) {
    function apax_ada_format_number($number)
    {
        $ps = '';
        // return $ps_no1;
        $p = (string)$number;
        $dem = 0;
        $x = strlen($p);
        for ($i = $x - 1; $i >= 0; $i--) {
            if ($dem % 3 == 0) {
                $x = ',' . $ps;
                $ps = $x;
            }
            $x = $p[$i] . $ps;
            $ps = $x;
            $dem++;
        }
        $ps = rtrim($ps, ',');
        return $ps;
    }
}

if (!class_exists('ada')) {
    class ada
    {
        
        function __construct()
        {
            return $this;
        }

        public static $instance = null;

        public static function getInstance()
        {
            if (self::$instance == null) {
                self::$instance = new ada();
            }
            return self::$instance;
        }

        const me = 'Ada Library';

        public static $ext = [
            'doc' => ['pdf' => 'pdf', 'doc' => 'msword', 'docx' => 'wordprocessingml', 'xls' => 'data:;base64', 'xlsx' => 'spreadsheetml'],
            'img' => ['png' => 'png', 'gif' => 'gif', 'jpg' => 'jpg', 'jpeg' => 'jpeg']
        ];

        public static $init;

        public static function meta()
        {
            return (object)[
                'trial_comment' => [
                    'date1' => '',
                    'date2' => '',
                    'date3' => '',
                    'note1' => '',
                    'note2' => '',
                    'note3' => '',
                    'comt1' => '',
                    'comt2' => '',
                    'comt3' => '',
                    'file1' => '',
                    'file2' => '',
                    'file3' => ''
                ]
            ];
        }

        public static function action($code)
        {
            $action = (object)[
                'signup' => ['id' => 1, 'type' => 'signup'],
                'contract' => ['id' => 2, 'type' => 'contract'],
                'deposit' => ['id' => 3, 'type' => 'payment'],
                'refusal' => ['id' => 4, 'type' => 'payment'],
                'fullfee' => ['id' => 5, 'type' => 'payment'],
                'enrolment' => ['id' => 6, 'type' => 'enrolment'],
                'promotion' => ['id' => 7, 'type' => 'enrolment'],
                'reserve' => ['id' => 8, 'type' => 'reserve'],
                'pending' => ['id' => 9, 'type' => 'reserve'],
                'retention' => ['id' => 10, 'type' => 'contract'],
                'class' => ['id' => 11, 'type' => 'movement'],
                'transfer' => ['id' => 12, 'type' => 'transfer'],
                'receive' => ['id' => 13, 'type' => 'transfer'],
                'branch' => ['id' => 14, 'type' => 'movement'],
                'withdraw' => ['id' => 15, 'type' => 'withdraw'],
            ];
            return isset($action->$code) ? $action->$code : false;
        }

        public static function ext($k)
        {
            return isset(self::$ext[$k]) ? self::$ext[$k] : null;
        }

        public static function uniless($str)
        {
            $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
            $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
            $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
            $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
            $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
            $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
            $str = preg_replace("/(đ)/", 'd', $str);
            $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
            $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
            $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
            $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
            $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
            $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
            $str = preg_replace("/(Đ)/", 'D', $str);
            $str = str_replace(" ", "", str_replace("&*#39;", "", $str));
            return $str;
        }

        public static function quote($str = '')
        {
            return addslashes($str);
        }

        public static function escape($str = '')
        {
            return stripslashes($str);
        }

        public static function upload($file = null, $path = '', $detail = false)
        {
            $resp = $file !== null && is_string($file) ? $file : '';
            if (isset($file['data']) && $file['data'] != '') {
                $resp = (object)[];
                $data = base64_decode($file['data']);
                $extn = $file['ext'];
                $type = $file['sign'];
                $name = $file['alias'];
                $name = md5(strtolower(substr($name, 0, 5))) == md5('null.') ? '' : $name;
                $uploaded = $name ? $name : md5($file['name'] . time() . str_random()) . '.' . $extn;
                $filelink = '';
                $filepath = '';
                if ($path) {
                    if ($type == 'doc') {
                        $filepath = UPLOAD . DS . str_replace('/', DS, $path) . DS . $uploaded;
                        $filelink = UPLOAD_LINK . "$path/$uploaded";
                    } elseif ($type == 'img') {
                        $filepath = IMAGES . DS . str_replace('/', DS, $path) . DS . $uploaded;
                        $filelink = IMAGES_LINK . "$path/$uploaded";
                    }
                } else {
                    if ($type == 'doc') {
                        $filepath = DOCUMENT . DS . $extn . DS . $uploaded;
                        $filelink = DOCUMENT_LINK . "$extn/$uploaded";
                    } elseif ($type == 'img') {
                        $filepath = AVATAR . DS . $uploaded;
                        $filelink = AVATAR_LINK . $uploaded;
                    } elseif ($type == 'transfer_file') {
                        if (in_array($extn, ['doc', 'docx', 'pdf', 'xls', 'xlsx', 'txt', 'svg', 'xml'])) {
                            $filepath = DOCUMENT . DS . $extn . DS . $uploaded;
                            $filelink = DOCUMENT_LINK . "$extn/$uploaded";
                        } elseif (in_array($extn, ['gif', 'jpg', 'jpeg', 'png'])) {
                            $filepath = IMAGES . DS . "others/$uploaded";
                            $filelink = IMAGES_LINK . "others/$uploaded";
                        }
                    }
                }
                if ($filepath && $data) {
                    $filesize = $file['size'];
                    $filename = $file['name'];
                    file_put_contents($filepath, $data);
                    if ($detail) {
                        $resp->file_root = $filename;
                        $resp->file_name = $uploaded;
                        $resp->file_path = $filepath;
                        $resp->file_link = $filelink;
                        $resp->file_size = $filesize;
                        $resp->file_ext = $extn;
                    } else {
                        $resp = $filelink;
                    }
                }
            }
            return $resp;
        }

        public static function paging($pagination, $total = null)
        {
            if (isset($total)) {
                $pagination->total = (int)$total;
                $pagination->lpage = (int)$total <= $pagination->limit ? 1 : (int)round(ceil($total / $pagination->limit));
                $pagination->ppage = $pagination->cpage > 0 ? $pagination->cpage - 1 : 0;
                $pagination->npage = $pagination->cpage < $pagination->lpage ? $pagination->cpage + 1 : $pagination->lpage;
            }
            return $pagination;
        }

        public static function nick($nick, $list)
        {
            $resp = $nick;
            if (in_array($nick, $list)) {
                $subfix = is_numeric(substr($nick, -1, 1)) ? (int)substr($nick, -1, 1) + 1 : 1;
                $prefix = is_numeric(substr($nick, -1, 1)) ? substr($nick, 0, (strlen($nick) - 1)) : $nick;
                $resp = $prefix . $subfix;
            }
            return !in_array($resp, $list) ? $resp : self::nick($resp, $list);
        }

        public static function percent($n1, $n2, $decimals = 0)
        {
            if($n2){
                return number_format((100 * $n1/$n2),$decimals) . "%";
            }else{
                return "0%";
            }
        }

        public static function ratio($n1, $n2, $decimals = 0)
        {
            if($n2){
                return number_format(($n1/$n2),$decimals);
            }else{
                return 0;
            }
        }

        public static function encrypt($string, $pass = "ada")
        {
            $jsondata = json_decode($string, true);
            $salt = hex2bin($jsondata["s"]);
            $ct = base64_decode($jsondata["ct"]);
            $iv  = hex2bin($jsondata["iv"]);
            $concatedPassphrase = $pass.$salt;
            $md5 = array();
            $md5[0] = md5($concatedPassphrase, true);
            $result = $md5[0];
            for ($i = 1; $i < 3; $i++) {
                $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
                $result .= $md5[$i];
            }
            $key = substr($result, 0, 32);
            $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
            return json_decode($data, true);
        }
        
        public static function decrypt($value, $pass = "ada")
        {
            $salt = openssl_random_pseudo_bytes(8);
            $salted = '';
            $dx = '';
            while (strlen($salted) < 48) {
                $dx = md5($dx.$pass.$salt, true);
                $salted .= $dx;
            }
            $key = substr($salted, 0, 32);
            $iv = substr($salted, 32, 16);
            $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
            $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
            return json_encode($data);
        }

        public static function secret($length = 29)
        {
            return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#$!@^~&*()-+=_{}[]<>?,./:;'), 0, $length);
        }

    }

    if (!function_exists('ada')) {
        function ada()
        {
            return new ada();
        }
    }


}
/** Uppercase VietNamese*/
if (!function_exists('vn_uppercase')) {
    function vn_uppercase($string = ''){
        if(!$string) return '';
        $string = preg_replace('/\s[\s]+/',' ',$string);
        $search = [
            "à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ", "ặ", "ẳ", "ẵ",
            "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ",
            "ì", "í", "ị", "ỉ", "ĩ",
            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ",
            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
            "ỳ", "ý", "ỵ", "ỷ", "ỹ",
            "đ"
        ];
        $replace = [
            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
            "Ì", "Í", "Ị", "Ỉ", "Ĩ",
            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ", "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
            "Đ"
        ];
        return str_replace($search, $replace, $string);
    }
}
/** Lowercase VietNamese*/
if (!function_exists('vn_lowercase')) {
    function vn_lowercase($string = ''){
        if(!$string) return '';
        $string = preg_replace('/\s[\s]+/',' ',$string);
        $replace = [
            "à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ", "ặ", "ẳ", "ẵ",
            "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ",
            "ì", "í", "ị", "ỉ", "ĩ",
            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ",
            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
            "ỳ", "ý", "ỵ", "ỷ", "ỹ",
            "đ"
        ];
        $search = [
            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
            "Ì", "Í", "Ị", "Ỉ", "Ĩ",
            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ", "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
            "Đ"
        ];
        return str_replace($search, $replace, $string);
    }
}
/** Generate string to roleId from excel import*/
if (!function_exists('gen_string_to_role')) {
    function gen_string_to_role($string = ''){
        $role = 68;
        if(!$string) return 68;
        $string = trim(str_replace('quyen', '', strtolower(apax_ada_convert_unicode($string,false))));
        switch ($string) {
            case strtolower(apax_ada_convert_unicode('Chuyên viên chăm sóc khách hàng',false)):
                $role = 55;
                break;
            case strtolower(apax_ada_convert_unicode('Chuyên viên tư vấn',false)):
                $role = 68;
                break;
            case strtolower(apax_ada_convert_unicode('Giáo viên',false)):
                $role = 36;
                break;
            case strtolower(apax_ada_convert_unicode('giám đốc trung tâm',false)):
                $role = 686868;
                break;
            case strtolower(apax_ada_convert_unicode('Trưởng nhóm kinh doanh',false)):
                $role = 69;
                break;
            case strtolower(apax_ada_convert_unicode('Trưởng nhóm giáo viên',false)):
                $role = 36;
                break;
            default:
                $role = 68;
                break;
        }
        return $role;
    }
}
?>
