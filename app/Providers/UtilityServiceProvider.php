<?php

namespace App\Providers;

use App\Models\APICode;
use App\Models\Contract;
use App\Models\Log_contracts_history;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
\Moment\Moment::setDefaultTimezone('Asia/Bangkok');

class UtilityServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public static function weekOfMonth($when = null) {
        if ($when === null) $when = time();
        $week = date('W', $when); // note that ISO weeks start on Monday
        $firstWeekOfMonth = date('W', strtotime(date('Y-m-01', $when)));
        return 1 + ($week < $firstWeekOfMonth ? $week : $week - $firstWeekOfMonth);
    }

    public static function updateContractNew($data) {
        $resp = false;
        if($data && isset($data->id)) {
            $contract = Contract::find($data->id);
            if ($contract) {
                $contract->hash_key = md5(json_encode($data));
                foreach ($data as $key => $value) {
                    if ($key != 'id') {
                        $contract->$key = $value;
                    }
                }
                $contract->save();
                $resp = true;
            }
        }
        return $resp;
    }

    public static function updateContract($data) {
        $resp = false;
        if($data && isset($data->id)) {
            $contract = Contract::find($data->id);
            if ($contract) {
                foreach($data as $key => $value) {
                    if ($key != 'id') {
                        $contract->$key = $value !== '0000-00-00' && $value !== '0000-00-00 00:00:00' ? $value : NULL;
                    }
                }
                $contract->save();
                $contract_info= self::first("SELECT * FROM contracts WHERE id=$data->id");
                $log_contracts_history_old = Log_contracts_history::where('contract_id', $data->id)->orderby('id', 'desc')->first();
                $key_curent = md5(json_encode($data).microtime());
                $previous_hashkey = $log_contracts_history_old ? $log_contracts_history_old->current_hashkey : $key_curent;
                $current_hashkey = $log_contracts_history_old ? md5($previous_hashkey.json_encode($data).microtime()) : $key_curent;
                $version = $log_contracts_history_old ? (int)$log_contracts_history_old->version_no + 1 : 0;
                $log_contracts_history = new Log_contracts_history();
                $log_contracts_history->contract_id = $contract->id;
                foreach($contract_info as $key => $value) {
                    if ($key != 'id') {
                        $log_contracts_history->$key = $value;
                    }
                }
                $log_contracts_history->changed_content = json_encode($data);
                $log_contracts_history->previous_hashkey = $previous_hashkey;
                $log_contracts_history->current_hashkey = $current_hashkey;
                $log_contracts_history->version_no = $version;
                $log_contracts_history->save();
                $resp = true;
            }
        }
        return $resp;
    }

    public static function updateMultiContracts($data = null) {
        if ($data) {
            $table_contracts = new Contract();
            $contracts_column_names = Schema::getColumnListing($table_contracts->getTable());
            $insert_contracts_query = "INSERT INTO contracts (";
            $tmp_insert_contracts_query_value = " VALUES ";
            $tmp_insert_contracts_query = " ON DUPLICATE KEY UPDATE ";
            $contract_ids = [];
            foreach($data[0] as $key => $value) {
                if(in_array($key,$contracts_column_names)){
                    $insert_contracts_query.=" `$key`,";
                    $tmp_insert_contracts_query.=" `$key` = VALUES($key),";
                }
            }
            $insert_contracts_query = substr($insert_contracts_query, 0, -1);
            $tmp_insert_contracts_query = substr($tmp_insert_contracts_query, 0, -1);
            $insert_contracts_query.=" ) ";
            foreach($data as $row) {
                $tmp_insert_contracts_query_value.=" (";
                foreach($row as $key => $value) {
                    if(in_array($key,$contracts_column_names)){
                        $tmp_insert_contracts_query_value.=$value !== '0000-00-00' && $value !== '0000-00-00 00:00:00' ?( $value===NULL ? " NULL,":" '$value',"):" NULL,";
                    }
                }
                $tmp_insert_contracts_query_value = substr($tmp_insert_contracts_query_value, 0, -1)."),";
                $contract_ids[] = $row->id;
            }
            $tmp_insert_contracts_query_value = substr($tmp_insert_contracts_query_value, 0, -1);
            $insert_contracts_query.= $tmp_insert_contracts_query_value.$tmp_insert_contracts_query;
            $rs_insert_contract = self::query($insert_contracts_query);

            $get_data_contract_query = "SELECT c.*, 
                (SELECT IFNULL(lc.version_no, 0) FROM `log_contracts_history` AS lc WHERE lc.contract_id=c.id ORDER BY id DESC LIMIT 1) AS version_no,
                (SELECT lc.current_hashkey FROM `log_contracts_history` AS lc WHERE lc.contract_id=c.id ORDER BY id DESC LIMIT 1) AS previous_hashkey
                FROM `contracts` AS c 
                WHERE c.id IN (".implode(',', $contract_ids).")";
            $contracts = self::query($get_data_contract_query);

            $insert_log_contract_history_query = "INSERT INTO log_contracts_history (";
            $insert_log_contract_history_query_value = " VALUES ";
            $tmp_query="";

            foreach ( $contracts as $contract) {
                $contract->contract_id = $contract->id;
                $contract->version_no = $contract->version_no+1;
                $contract->previous_hashkey = $contract->previous_hashkey;
                $contract->current_hashkey = md5($contract->previous_hashkey.json_encode($contract).date("Y-m-d H:i:s"));
                $insert_log_contract_history_query_value.=" (";
                if($tmp_query ==""){
                    foreach($contract as $key => $value) {
                        if($key!='id'){
                            $insert_log_contract_history_query_value.= $value !== '0000-00-00' && $value !== '0000-00-00 00:00:00' ?( $value===NULL ? " NULL,":" '$value',"):" NULL,";
                            $tmp_query.=" `$key`,";
                        }
                    }
                }else{
                    foreach($contract as $key => $value) {
                        if($key!='id'){
                            $insert_log_contract_history_query_value.= $value !== '0000-00-00' && $value !== '0000-00-00 00:00:00' ?( $value===NULL ? " NULL,":" '$value',"):" NULL,";
                        }
                    }
                }
                $insert_log_contract_history_query_value = substr($insert_log_contract_history_query_value, 0, -1)."),";
            }
            $insert_log_contract_history_query.= substr( $tmp_query, 0, -1).") ";
            $insert_log_contract_history_query_value = substr($insert_log_contract_history_query_value, 0, -1);
            $insert_log_contract_history_query.=$insert_log_contract_history_query_value;

            $rs_insert_contract_history = self::query($insert_log_contract_history_query);
            if ($rs_insert_contract_history && $rs_insert_contract) {
                return true;
            }
        }
        return false;
    }
    public static function createContract($data, $test = null) {
        $contract = new Contract();
        foreach($data as $key => $value) {
            if ($key != 'id') {
                $contract->$key = $value !== '0000-00-00' && $value !== '0000-00-00 00:00:00' ? $value : NULL;
            }
        }
        $contract->save();
        $contract_info= self::first("SELECT * FROM contracts WHERE id=$contract->id");
        $log_contracts_history_old = Log_contracts_history::where('contract_id', $contract->id)->orderby('id', 'desc')->first();
        $key_curent = md5(json_encode($data).microtime());
        $previous_hashkey = $log_contracts_history_old ? $log_contracts_history_old->current_hashkey : $key_curent;
        $current_hashkey = $log_contracts_history_old ? md5($previous_hashkey.json_encode($data).microtime()) : $key_curent;
        $version = $log_contracts_history_old ? (int)$log_contracts_history_old->version_no + 1 : 0;
        $log_contracts_history = new Log_contracts_history();
        $log_contracts_history->contract_id = $contract->id;
        foreach($contract_info as $key => $value) {
            if ($key != 'id') {
                $log_contracts_history->$key = $value;
            }
        }
        $log_contracts_history->changed_content = json_encode($data);
        $log_contracts_history->previous_hashkey = $previous_hashkey;
        $log_contracts_history->current_hashkey = $current_hashkey;
        $log_contracts_history->version_no = $version;
        $log_contracts_history->save();
        return $contract;
    }

    public static function checkDateIn($date, $start_date, $end_date)
    {
        $resp = false;
        if (strtotime($date) && strtotime($start_date) && strtotime($end_date)) {
            $m = new \Moment\Moment(strtotime($date));
            $s = new \Moment\Moment(strtotime($start_date));
            $e = new \Moment\Moment(strtotime($end_date));
            if ($m >= $s && $e >= $m) {
                $resp = true;
            }
        }
        return $resp;
    }


    public static function subtractDays($date, $day)
    {
        $m = new \Moment\Moment(strtotime($date));
        $m->subtractDays($day);
        return $m->format("Y-m-d");
    }

    public static function getClassDays($class_id)
    {
        $resp = [];
        if ((int)$class_id) {
            $list = self::query("SELECT class_day FROM `sessions` WHERE class_id = $class_id AND status > 0");
            if (count($list)) {
                foreach ($list as $k => $v) {
                    if (!in_array($v->class_day, $resp)) {
                        $resp[] = $v->class_day;
                    }
                }
                sort($resp);
            }
        }
        return $resp;
    }

    public static function getPublicHolidays($class_id = 0, $branch_id = 0, $product = 0)
    {
        $resp = [];
        $where = ($product && $product !== 9999) ? "AND (h.products LIKE '[$product,%' OR h.products LIKE '%,$product]' OR h.products LIKE '%,$product,%' OR h.products LIKE '[$product]') AND h.`status` > 0" : ' AND h.`status` > 0 ';
        if ((int)$class_id) {
            $resp = self::query("SELECT h.start_date, h.end_date, h.products FROM public_holiday AS h
                          LEFT JOIN branches AS b ON h.zone_id = b.zone_id
                          LEFT JOIN classes AS c ON c.branch_id = b.id
                          WHERE c.id = $class_id $where");
        } elseif ((int)$branch_id) {
            $resp = self::query("SELECT h.start_date, h.end_date, h.products FROM public_holiday AS h
                          LEFT JOIN branches AS b ON h.zone_id = b.zone_id
                          WHERE b.id = $branch_id $where");
        }
        if (count($resp)) {
            usort($resp, function ($a, $b) {
                return strcmp($a->start_date, $b->start_date);
            });
            if($product === 9999){
                $products = self::query("SELECT id FROM products WHERE status = 1");
                $holidays = [];

                foreach ($products as $p){
                    $holidays[$p->id] = [];
                }

                foreach ($resp as $re){
                    $product_ids = explode(',',str_replace('[','',str_replace(']','',$re->products)));
                    foreach ($holidays as $key => $holiday){
                        if(in_array($key, $product_ids)){
                            $holidays[$key][] = (Object)[
                                'start_date' => $re->start_date,
                                'end_date' => $re->end_date
                            ];
                        }
                    }
                }

                $resp = $holidays;
            }else{
                foreach ($resp as &$re){
                    $re = (Object)[
                        'start_date' => $re->start_date,
                        'end_date' => $re->end_date
                    ];
                }
                unset($re);
            }
        }
        return $resp;
    }

    public static function checkInHolydays($date = '', $holidays = [], $class_id = 0)
    {
        $resp = false;
        if ($class_id && !$holidays) {
            $holidays = $holidays ? $holidays : self::query("SELECT h.start_date, h.end_date FROM public_holiday AS h
                                                      LEFT JOIN branches AS b ON h.zone_id = b.zone_id
                                                      LEFT JOIN classes AS c ON c.branch_id = b.id
                                                      WHERE c.id = $class_id");
        }
        // $m = strtotime($date) ? new \Moment\Moment(strtotime($date)) : new \Moment\Moment();
        if ($holidays && is_array($holidays) && count($holidays) > 1) {
            usort($holidays, function ($a, $b) {
                return strcmp($a->start_date, $b->start_date);
            });
            foreach ($holidays as $holiday) {
                if (strtotime($holiday->start_date) && strtotime($holiday->end_date) && !$resp) {
                    if (self::checkDateIn($date, $holiday->start_date, $holiday->end_date)) {
                        // echo("CD: $date ST: $holiday->start_date ED: $holiday->end_date Check = ".(int)self::checkDateIn($date, $holiday->start_date, $holiday->end_date)."\n\n");
                        $resp = true;
                        break;
                    }
                }
            }
        } elseif ($holidays) {
            $holidays = is_array($holidays) ? $holidays[0] : $holidays;
            if (strtotime($holidays->start_date) && strtotime($holidays->end_date)) {
                $resp = self::checkDateIn($date, $holidays->start_date, $holidays->end_date);
            }
        }
        return $resp;
    }

    public static function validClassdays($classdays = [])
    {
        $resp = count($classdays) ? $classdays : [2, 5];
        if (count($resp)) {
            $resp = array_unique($resp);
            sort($resp);
            if ($resp[0] == 0) {
                array_shift($resp);
                $resp[] = 0;
            }
        }
        return $resp;
    }
    public static function calEndDate($sessions, $classdays = [], $holidays = [], $date = '') {
        $resp = null;
        usort($holidays, function ($a, $b) {
            return strcmp($a->start_date, $b->start_date);
        });
            $classdays = self::validClassdays($classdays);
            if ($sessions && $classdays && is_array($classdays) && count($classdays) > 0) {
                $sdt = strtotime($date) ? $date : date('Y-m-d');
                $ckd = strtotime($sdt) ? new \Moment\Moment(strtotime($date)) : new \Moment\Moment();
                $swd = $ckd->getWeekday();
                $dtl = [];
                $nxd = 0;
                $stp = 0;
                $cwd = 0;
                while ($sessions > 0) {
                    $swd = $swd == 7 ? 0 : $swd;
                    $cwd = array_search($swd, $classdays);
                    $crd = $classdays[$cwd];
                    if ($cwd > -1) {
                        if (!self::checkInHolydays($sdt, $holidays)) {
                            $sessions --;
                            $dtl[] = $sdt;
                        }
                        $nxd = $cwd == (count($classdays) - 1) ? $classdays[0] : $classdays[($cwd + 1)];
                        $stp = (int)$nxd > (int)$crd ? (int)($nxd - $crd) : 7 - (int)($crd - $nxd);
                        $sdt = $ckd->addDays($stp)->format('Y-m-d');
                    } else {
                        $sdt = $ckd->addDays(1)->format('Y-m-d');
                    }
                    $ckd = new \Moment\Moment(strtotime($sdt));
                    $swd = $ckd->getWeekday();
                }
                $dtl = array_unique($dtl);
                $resp = (Object)[];
                $resp->dates = $dtl;
                $resp->total = count($dtl);
                $resp->end_date = end($dtl);
            }
            return $resp;
    }
    public static function calSessions($start, $end, $holidays = [], $classdays = []) {
        $resp = null;
        usort($holidays, function ($a, $b) {
            return strcmp($a->start_date, $b->start_date);
        });
            $classdays = self::validClassdays($classdays);
            if (strtotime($start) && strtotime($end) && is_array($classdays) && count($classdays) > 0) {
                $ckd = $start;
                $sdt = new \Moment\Moment(strtotime($ckd));
                $edt = new \Moment\Moment(strtotime($end));
                $swd = $sdt->getWeekday();
                $dtl = [];
                $nxd = 0;
                $stp = 0;
                $cwd = 0;
                while ((int)floor($edt->from($ckd)->getDays()) <= 0) {
                    $swd = $swd == 7 ? 0 : $swd;
                    $cwd = array_search($swd, $classdays);
                    $crd = $classdays[$cwd];
                    if ($cwd > -1) {
                        $nxd = $cwd == (count($classdays) - 1) ? $classdays[0] : $classdays[($cwd + 1)];
                        $stp = (int)$nxd > (int)$crd ? (int)($nxd - $crd) : 7 - (int)($crd - $nxd);
                        if (($holidays && !self::checkInHolydays($ckd, $holidays)) || !$holidays) {
                            $dtl[] = $ckd;
                        }
                    } else {
                        $stp = 1;
                    }
                    $ckd = $sdt->addDays($stp)->format('Y-m-d');
                    $sdt = new \Moment\Moment(strtotime($ckd));
                    $swd = $sdt->getWeekday();
                }
                $dtl = array_unique($dtl);
                $resp = (Object)[];
                $resp->dates = $dtl;
                $resp->total = count($dtl);
                $resp->end_date = end($dtl);
            }
            return $resp;
    }

//    public static function getRealSessions($sessions, $classdays = [], $holidays = [], $date = '')
//    {
//        $resp = null;
//        if ($sessions && $classdays && is_array($classdays) && count($classdays) > 0) {
//            $classdays = self::validClassdays($classdays);
//            $date = strtotime($date) ? $date : date('Y-m-d');
//            $m = strtotime($date) ? new \Moment\Moment(strtotime($date)) : new \Moment\Moment();
//            $this_weekday = $m->getWeekday();
//            $this_weekday = $this_weekday == 7 ? 0 : $this_weekday;
//            $first_weekday = $this_weekday;
//            $checked_date = $date;
//            $days = [];
//            $step_days = 0;
//            $buff_weekday = 0;
//            $next_weekday = 0;
//            usort($holidays, function ($a, $b) {
//                return strcmp($a->start_date, $b->start_date);
//            });
//            if (in_array($this_weekday, $classdays) || (min($classdays) == 0 && $this_weekday == 0)) {
//                $days[] = $date;
//                $sessions--;
//                $n = new \Moment\Moment(strtotime($date));
//                $checked_date = $n->addDays(1)->format('Y-m-d');
//            }
//            while ($sessions > 0) {
//                $this_weekday = $this_weekday == 7 ? 0 : $this_weekday;
//                foreach ($classdays as $classday) {
//                    $next_weekday = $classday;
//                    $buff_weekday = $this_weekday;
//                    $this_weekday = $next_weekday;
//                    if ($buff_weekday == $next_weekday) {
//                        $step_days = 0;
//                    } elseif ($next_weekday > $buff_weekday) {
//                        $step_days = $next_weekday - $buff_weekday;
//                    } elseif ($next_weekday == 0 && $buff_weekday > 0) {
//                        $step_days = 7 - $buff_weekday;
//                    } elseif ($first_weekday == $buff_weekday && ($classdays[1] > $first_weekday || $classdays[1] == 0)) {
//                        $step_days = $classdays[1] == 0 ? 7 - $first_weekday : (int)$classdays[1] - $first_weekday;
//                        $this_weekday = $classdays[1];
//                    } else {
//                        $step_days = 7 - ($buff_weekday - $next_weekday);
//                    }
//                    $current_date_checked = $checked_date;
//                    $n = new \Moment\Moment(strtotime($checked_date));
//                    $step_days = $step_days == 0 ? 1 : $step_days;
//                    $checking_date = $n->addDays($step_days)->format('Y-m-d');
//                    $checked_date = $checking_date;
//                    if (!self::checkInHolydays($checking_date, $holidays) && !in_array($checking_date, $days) && $sessions > 0) {
//                        // echo("Pick| TW: $buff_weekday ND: $next_weekday TD: $current_date_checked ST: $step_days CD: $checking_date - WD: $this_weekday | Check: ".(int)self::checkInHolydays($checking_date, $holidays)." <br>");
//                        $days[] = $checking_date;
//                        $sessions--;
//                    }
//                }
//            }
//            $days = array_unique($days);
//            $resp = (Object)[];
//            $resp->dates = $days;
//            $resp->total = count($days);
//            $resp->end_date = end($days);
//        }
//        return $resp;
//    }

    public static function getRealSessions1($sessions, $classdays = [], $holidays = [], $date = '')
    {
        $resp = null;
        if ($sessions && $classdays && is_array($classdays) && count($classdays) > 0) {
            $classdays = self::validClassdays($classdays);
            $date = strtotime($date) ? $date : date('Y-m-d');
            $m = strtotime($date) ? new \Moment\Moment(strtotime($date)) : new \Moment\Moment();
            $this_weekday = $m->getWeekday();
            $this_weekday = $this_weekday == 7 ? 0 : $this_weekday;
            $first_weekday = $this_weekday;
            $checked_date = $date;
            $days = [];
            $step_days = 0;
            $buff_weekday = 0;
            $next_weekday = 0;
            usort($holidays, function ($a, $b) {
                return strcmp($a->start_date, $b->start_date);
            });
            if (in_array($this_weekday, $classdays) || (min($classdays) == 0 && $this_weekday == 0)) {
                $days[] = $date;
                $sessions--;
                $n = new \Moment\Moment(strtotime($date));
                $checked_date = $n->addDays(1)->format('Y-m-d');
            }
            while ($sessions > 0) {
                $this_weekday = $this_weekday == 7 ? 0 : $this_weekday;
                foreach ($classdays as $classday) {
                    $next_weekday = $classday;
                    $buff_weekday = $this_weekday;
                    $this_weekday = $next_weekday;
                    if ($buff_weekday == $next_weekday) {
                        $step_days = 0;
                    } elseif ($next_weekday > $buff_weekday) {
                        $step_days = $next_weekday - $buff_weekday;
                    } elseif ($next_weekday == 0 && $buff_weekday > 0) {
                        $step_days = 7 - $buff_weekday;
                    } elseif ($first_weekday == $buff_weekday && ($classdays[1] > $first_weekday || $classdays[1] == 0)) {
                        $step_days = $classdays[1] == 0 ? 7 - $first_weekday : (int)$classdays[1] - $first_weekday;
                        $this_weekday = $classdays[1];
                    } else {
                        $step_days = 7 - ($buff_weekday - $next_weekday);
                    }
//          $current_date_checked = $checked_date;
                    $n = new \Moment\Moment(strtotime($checked_date));
                    $step_days = $step_days == 0 ? 1 : $step_days;
                    $checking_date = $n->addDays($step_days)->format('Y-m-d');
                    $checked_date = $checking_date;
                    $getWeekday = (int)$n->getWeekday();
                    if ($getWeekday===7) $getWeekday = 0;
                    if (!self::checkInHolydays($checking_date, $holidays) && !in_array($checking_date, $days) && $sessions > 0 && (int) $getWeekday === (int)$classday) {
//             echo("Pick| TW: $buff_weekday ND: $next_weekday TD: $current_date_checked ST: $step_days CD: $checking_date - WD: $this_weekday | Check: ".(int)self::checkInHolydays($checking_date, $holidays)." <br>");
                        $days[] = $checking_date;
                        $sessions--;
                    }
                }
            }
            $days = array_unique($days);
            $resp = (Object)[];
            $resp->dates = $days;
            $resp->total = count($days);
            $resp->end_date = end($days);
        }
        return $resp;
    }

    /**
     * @param \Moment\Moment $date
     * @return int
     */
    private static function getDayOfWeekByDate($date)
    {
        $dateOfWeek = (int)$date->getWeekday();
        return $dateOfWeek === 7 ? 0 : $dateOfWeek;
    }

    public static function getRealSessions($sessions, $classdays = [], $holidays = [], $dateString = '')
    {
        $resp = null;
        if (!$sessions || !$classdays || !is_array($classdays) || count($classdays) === 0) {
            return $resp;
        }
        $daysOfWeek = self::validClassdays($classdays);
        $days = [];
        $date = new \Moment\Moment(strtotime($dateString));
        $lengthDaysOfWeek = count($daysOfWeek);
        $index = 0;
        while ($sessions > 0) {
            $dayOfWeek = $daysOfWeek[$index];
            $dateOfWeek = self::getDayOfWeekByDate($date);
            $step = null;
            if ($dayOfWeek === $dateOfWeek) {
                $step = 0;
            } else if ($dayOfWeek > $dateOfWeek) {
                $step = $dayOfWeek - $dateOfWeek;
            } else {
                $date = $date->addDays(1);
                $index = $index < $lengthDaysOfWeek - 1 ? ++$index : 0;
                continue;
            }
            $date = $date->addDays($step);
            $dateString = $date->format("Y-m-d");
            if (!self::checkInHolydays($dateString, $holidays) && !in_array($dateString, $days)) {
                $days[] = $dateString;
                $sessions--;
            }
            $index = $index < $lengthDaysOfWeek - 1 ? ++$index : 0;
            $date = $date->addDays(1);
        }

        $days = array_unique($days);
        $resp = (Object)[];
        $resp->dates = $days;
        $resp->total = count($days);
        $resp->end_date = end($days);
        return $resp;
    }

  /**
   * Cần kiểm tra lại tính đúng đắn của hàm getRealSession
   * @param $sessions
   * @param array $classdays
   * @param array $holidays
   * @param string $date
   * @return object|null
   * @throws \Moment\MomentException
   */
    public static function getRealSessionsClone($sessions, $classdays = [], $holidays = [], $date = '')
    {
        $resp = null;
        if ($sessions && $classdays && is_array($classdays) && count($classdays) > 0) {
            $classdays = self::validClassdays($classdays);
            $date = strtotime($date) ? $date : date('Y-m-d');
            $m = strtotime($date) ? new \Moment\Moment(strtotime($date)) : new \Moment\Moment();
            $this_weekday = $m->getWeekday();
            $this_weekday = $this_weekday == 7 ? 0 : $this_weekday;
            $first_weekday = $this_weekday;
            $checked_date = $date;
            $days = [];
            $step_days = 0;
            $buff_weekday = 0;
            $next_weekday = 0;
            usort($holidays, function ($a, $b) {
                return strcmp($a->start_date, $b->start_date);
            });
            if (in_array($this_weekday, $classdays) || (min($classdays) == 0 && $this_weekday == 0)) {
                $days[] = $date;
                $sessions--;
                $n = new \Moment\Moment(strtotime($date));
                $checked_date = $n->addDays(1)->format('Y-m-d');
            }
            while ($sessions > 0) {
                $this_weekday = $this_weekday == 7 ? 0 : $this_weekday;
                foreach ($classdays as $classday) {
                    $next_weekday = $classday;
                    $buff_weekday = $this_weekday;
                    $this_weekday = $next_weekday;
                    if ($buff_weekday == $next_weekday) {
                        $step_days = 0;
                    } elseif ($next_weekday > $buff_weekday) {
                        $step_days = $next_weekday - $buff_weekday;
                    } elseif ($next_weekday == 0 && $buff_weekday > 0) {
                        $step_days = 7 - $buff_weekday;
                    } elseif ($first_weekday == $buff_weekday && ($classdays[1] > $first_weekday || $classdays[1] == 0)) {
                        $step_days = $classdays[1] == 0 ? 7 - $first_weekday : (int)$classdays[1] - $first_weekday;
                        $this_weekday = $classdays[1];
                    } else {
                        $step_days = 7 - ($buff_weekday - $next_weekday);
                    }
//          $current_date_checked = $checked_date;
                    $n = new \Moment\Moment(strtotime($checked_date));
                    $step_days = $step_days == 0 ? 1 : $step_days;
                    $checking_date = $n->addDays($step_days)->format('Y-m-d');
                    $checked_date = $checking_date;
                    $getWeekday = (int)$n->getWeekday();
                    if ($getWeekday===7) $getWeekday = 0;
                    if (!self::checkInHolydays($checking_date, $holidays) && !in_array($checking_date, $days) && $sessions > 0 && (int) $getWeekday === (int)$classday) {
//             echo("Pick| TW: $buff_weekday ND: $next_weekday TD: $current_date_checked ST: $step_days CD: $checking_date - WD: $this_weekday | Check: ".(int)self::checkInHolydays($checking_date, $holidays)." <br>");
                        $days[] = $checking_date;
                        $sessions--;
                    }
                }
            }
            $days = array_unique($days);
            $resp = (Object)[];
            $resp->dates = $days;
            $resp->total = count($days);
            $resp->end_date = end($days);
        }
        return $resp;
    }

    public static function calcDoneSessions($start, $end, $holidays = [], $classdays = [])
    {
        $resp = null;
        if (strtotime($start) && strtotime($end) && is_array($classdays) && count($classdays) > 0) {
            $classdays = self::validClassdays($classdays);
            usort($holidays, function ($a, $b) {
                return strcmp($a->start_date, $b->start_date);
            });
            $checked_date = $start;
            $days = [];
            $step_days = 0;
            $buff_weekday = 0;
            $next_weekday = 0;
            $s = new \Moment\Moment(strtotime($start));
            $m = new \Moment\Moment(strtotime($end));
            $this_weekday = $s->getWeekday();
            $this_weekday = $this_weekday == 7 ? 0 : $this_weekday;
            $first_weekday = $this_weekday;
            $hold = 0;
            if (in_array($this_weekday, $classdays) || (min($classdays) == 0 && $this_weekday == 0)) {
                $days[] = $start;
            }
            while ((int)floor($m->from($checked_date)->getDays()) <= 0 && $hold < 365) {
                $this_weekday = $this_weekday == 7 ? 0 : $this_weekday;
                foreach ($classdays as $classday) {
                    $next_weekday = $classday;
                    $buff_weekday = $this_weekday;
                    $this_weekday = $next_weekday;
                    if ($buff_weekday == $next_weekday) {
                        $step_days = 0;
                    } elseif ($next_weekday > $buff_weekday) {
                        $step_days = $next_weekday - $buff_weekday;
                    } elseif ($next_weekday === 0 && $buff_weekday > 0) {
                        $step_days = 7 - $buff_weekday;
                    } elseif ($first_weekday == $buff_weekday && ($classdays[1] > $first_weekday || $classdays[1] === 0)) {
                        $step_days = $classdays[1] === 0 ? 7 - $first_weekday : (int)$classdays[1] - $first_weekday;
                        $this_weekday = $classdays[1];
                    } else {
                        $step_days = 7 - ($buff_weekday - $next_weekday);
                    }
                    $n = new \Moment\Moment(strtotime($checked_date));
                    $checking_date = $n->addDays($step_days)->format('Y-m-d');
                    $checked_date = $checking_date;
                    if (self::checkInHolydays($checked_date, $holidays) == false && (int)floor($m->from($checked_date)->getDays()) <= 0) {
                        $days[] = $checked_date;
                    } else {
                        $hold++;
                    }
                }
            }
            $days = array_unique($days);
            $resp = (Object)[];
            $resp->dates = $days;
            $resp->total = count($days);
            $resp->end_date = end($days);
        }
        return $resp;
    }

    public static function calcLeftSessions($class_id, $contract_id, $date = '')
    {
        $classdays = self::query("SELECT class_day AS `no` FROM `sessions` WHERE class_id = $class_id");
        $available = self::first("SELECT (CEIL((c.total_discount + c.must_charge - c.debt_amount)
                            /
                            (t.receivable / t.`session`))) AS num
                            FROM contracts AS c
                            LEFT JOIN tuition_fee AS t ON c.tuition_fee_id = t.id
                            WHERE c.class_id = $class_id");
        $holidays = self::query("SELECT h.start_date, h.end_date FROM public_holiday AS h
                            LEFT JOIN contracts AS c ON c.branch_id = h.branch_id
                            WHERE c.id = $class_id");
        $sessions = $available && isset($available->num) ? (int)$available->num : 0;
        return self::getRealSessions($sessions, $classdays, $date, $holidays);
    }

    public static function left($date)
    {
        $resp = 0;
        if (strtotime($date)) {
            $m = new \Moment\Moment($date);
            $resp = floor($m->fromNow()->getDays());
        }
        return $resp;
    }

    public static function calcNewStartDate($end, $classdays = [2, 5], $holidays = [])
    {
        $resp = null;
        if (strtotime($end)) {
            $step_days = 0;
            $buff_weekday = 0;
            $next_weekday = 0;
            $classdays = self::validClassdays($classdays);
            usort($holidays, function ($a, $b) {
                return strcmp($a->start_date, $b->start_date);
            });
            $e = new \Moment\Moment(strtotime($end));
            $end_week_day = $e->getWeekday();
            $this_weekday = $end_week_day == 7 ? 0 : $end_week_day;
            $first_weekday = $this_weekday;
            foreach ($classdays as $classday) {
                $next_weekday = $classday;
                $buff_weekday = $this_weekday;
                $this_weekday = $next_weekday;
                if ($buff_weekday == $next_weekday) {
                    $step_days = 0;
                } elseif ($next_weekday > $buff_weekday) {
                    $step_days = $next_weekday - $buff_weekday;
                } elseif ($next_weekday == 0 && $buff_weekday > 0) {
                    $step_days = 7 - $buff_weekday;
                } elseif ($first_weekday == $buff_weekday && ($classdays[1] > $first_weekday || $classdays[1] == 0)) {
                    // $step_days = $classdays[1] == 0 ? 7 - $first_weekday : (int)$classdays[1] - $first_weekday;
                    $step_days = $classdays[1];
                } else {
                    $step_days = 7 - ($buff_weekday - $next_weekday);
                }
            }
        }
        $resp = $e->addDays($step_days)->format('Y-m-d');
        return self::checkInHolydays($resp, $holidays) == false ? $resp : self::calcNewStartDate($resp, $classdays, $holidays);
    }

    public static function getClassBlockByDate($class_id, $date = '')
    {
        $resp = [];
        $date = $date ? $date : date('Y-m-d');
        if (strtotime($date) && $class_id) {
            $date = date('Y-m-d', strtotime($date));
            $data = self::query("SELECT
                CONCAT(c.student_id, '˜', c.type, '˜', s.name, '˜', s.nick, '˜', c.enrolment_real_sessions, '˜', c.enrolment_start_date, '˜', COALESCE(c.enrolment_last_date, c.enrolment_last_date)) AS block
            FROM contracts AS c
                LEFT JOIN students AS s ON c.student_id = s.id
            WHERE c.class_id = $class_id 
                /* AND c.status = 5 */
                AND c.enrolment_end_date >= '$date'  
                AND c.enrolment_last_date >= '$date'  
                AND c.enrolment_start_date <= c.enrolment_end_date 
                AND c.enrolment_last_date >= c.enrolment_start_date                 
                GROUP BY c.student_id");
            if (count($data)) {
                foreach ($data as $dat) {
                    $resp[] = $dat->block;
                }
            }
        }
        return $resp;
    }

    public static function logStudentAction($log)
    {
        $check = self::first("SELECT * FROM `log_students_actions` WHERE `student_id` = $log->student_id ORDER BY id DESC LIMIT 0, 1");
        $sumar = $check['summary'] && $check['summary'] != '' ? $check['summary'] . '-' . $obact->id : $obact->id;
        $obact = ada()->action($log->action);
        $hashk = md5(json_encode($log) . $sumar . time());
        $query = " INSERT INTO `log_students_actions` (
            `action`,
            `type`,
            `student_id`,
            `code`,
            `sumary`,
            `relation`,
            `content`,
            `meta`,
            `note`,
            `hash`,
            `updated_id`,
            `created_at`
        ) VALUES (
            '$log->action',
            '$obact->type',
            '$log->student_id',
            '$obact->id',
            '$sumar',
            '$log->relation',
            '$log->content',
            '$log->meta',
            '$log->note',
            '$hashk',
            '$log->updated_id',
            NOW()
        )";
        self::query($query);
    }

    public static function query($query, $print = false)
    {
        $resp = null;
        $query = trim($query);
        $upperQuery = strtoupper(substr($query, 0, 6));
        if ($print) {
            dd('\n-------------------------------------------------------------\n', $query, '\n-------------------------------------------------------------\n');
        } else {
            if ($upperQuery == ('SELECT')) {
                $resp = DB::select(DB::raw($query));
            } elseif ($upperQuery == ('INSERT')) {
                $resp = DB::insert(DB::raw($query));
            } elseif ($upperQuery == ('UPDATE')) {
                $resp = DB::update(DB::raw($query));
            } elseif ($upperQuery == ('DELETE')) {
                $resp = DB::delete(DB::raw($query));
            } else {
                $resp = DB::statement(DB::raw($query));
            }
        }
        return $resp;
    }

    public static function msquery($query, $print = false)
    {
        $resp = null;
        if ($print) {
            dd('\n-------------------------------------------------------------\n', $query, '\n-------------------------------------------------------------\n');
        } else {
            $connection = DB::connection('mysql_1');
            if (md5(strtoupper(substr($query, 0, 6))) == md5('SELECT')) {
                $resp = $connection->select(DB::raw($query));
            } elseif (md5(strtoupper(substr($query, 0, 6))) == md5('INSERT')) {
                $resp = $connection->insert(DB::raw($query));
            } elseif (md5(strtoupper(substr($query, 0, 6))) == md5('UPDATE')) {
                $resp = $connection->update(DB::raw($query));
            } elseif (md5(strtoupper(substr($query, 0, 6))) == md5('DELETE')) {
                $resp = $connection->delete(DB::raw($query));
            } else {
                $resp = $connection->statement(DB::raw($query));
            }
        }
        return $resp;
    }

    public static function eff_query($query, $print = false)
    {
      $resp = null;
      if ($print) {
        dd('\n-------------------------------------------------------------\n', $query, '\n-------------------------------------------------------------\n');
      } else {
        $connection = DB::connection('sqlsrv');
        if (md5(strtoupper(substr($query, 0, 6))) == md5('SELECT')) {
          $resp = $connection->select(DB::raw($query));
        } elseif (md5(strtoupper(substr($query, 0, 6))) == md5('INSERT')) {
          $resp = $connection->insert(DB::raw($query));
        } elseif (md5(strtoupper(substr($query, 0, 6))) == md5('UPDATE')) {
          $resp = $connection->update(DB::raw($query));
        } elseif (md5(strtoupper(substr($query, 0, 6))) == md5('DELETE')) {
          $resp = $connection->delete(DB::raw($query));
        } else {
          $resp = $connection->statement(DB::raw($query));
        }
      }
      return $resp;
    }

    public static function first($query, $print = false)
    {
        $resp = self::query($query, $print);
        return $resp && is_array($resp) && count($resp) >= 1 ? $resp[0] : $resp;
    }

    public static function msfirst($query, $print = false)
    {
        $resp = self::msquery($query, $print);
        return $resp && is_array($resp) && count($resp) == 1 ? $resp[0] : $resp;
    }

    public static function authorize($token = '', $action = '')
    {
        $result = (object)array();
        $result->authen = false;
        $result->data = null;
        if ($token) {
            $session = Redis::get($token);
            if ($session) {
                $result->authen = true;
                $result->data = json_decode($session);
            }
        }
        return $result;
    }

    public static function session()
    {
        $result = null;
        $token = app('request')->header('session-token');
        if ($token) {
            $session = Redis::get($token);
            if ($session) {
                $result = json_decode($session);
            }
        }
        return $result;
    }

    public static function import($method, $router, $caller)
    {
        $class = strpos($caller, '@') > -1 ? substr($caller, 0, (int)strpos($caller, '@')) : $caller;
        $path = CONTROLLER . DS . $class . '.php';
        $func = strpos($caller, '@') > -1 ? substr($caller, (int)strpos($caller, '@') + 1) : 'index';
        if (file_exists($path) && is_file($path)) {
            $source = file_get_contents($path);
            if (strpos($source, 'class ' . $class . ' extends') && strpos($source, 'function ' . $func . '(')) {
                // echo("Router: $router | Method: $caller \n");
                // Route::$method($router, $caller)->middleware('Authentication');
                Route::$method($router, $caller);
            }
        }
    }

    public static function request()
    {
        $resp = (object)[];
        $resp->router = \Route::current()->uri();
        $resp->method = \Route::current()->methods();
        $resp->action = $resp->method[0];
        $resp->detail = \Route::current();
        return $resp;
    }

    private static function auth($func, $routers, $rights)
    {
        $resp = (object)[];
        if (strpos($func, ':') > -1) {
            $buffer = explode(':', $func);
            $actsar = $buffer[1];
            $pageid = $buffer[0];
            $pages = [];
            $mode = substr($pageid, 0, strpos($pageid, '.'));
            if (strpos($pageid, '.0') > -1) {
                foreach ($routers as $id => $router) {
                    // echo($id." = ".$pageid. " - ".substr($id,0, strpos($pageid, '.'))."\n");
                    if (substr($id, 0, strpos($pageid, '.')) == $mode || (strpos($pageid, '.') == false && $id == $mode)) {
                        $pages[] = $router;
                    }
                }
            } else {
                foreach ($routers as $id => $router) {
                    if (substr($pageid, 0, strpos($pageid, '.')) == $mode && !in_array($routers->$mode, $pages)) {
                        $pages[] = $routers->$mode;
                    }
                }
                if (!in_array($routers->$pageid, $pages)) {
                    $pages[] = $routers->$pageid;
                }
            }
            $roles = [];
            if (strlen($actsar) == 1 && (int)$actsar === 0) {
                foreach ($rights as $id => $act) {
                    $roles[] = $act;
                }
            } else {
                $actsids = explode('.', $actsar);
                if (count($actsids)) {
                    foreach ($actsids as $actsid) {
                        $roles[] = $rights->$actsid;
                    }
                }
            }
            $resp->pages = $pages;
            $resp->acts = $roles;
        }
        return $resp;
    }

    private static function permission($info, $rights, $routers)
    {
        $resp = $info;
        if (isset($info->functions) && is_array(json_decode($info->functions))) {
            $funcs = json_decode($info->functions);
            $rule = "role_$info->role_id";
            $obj = $info;
            $obj->rules = [];
            $resp->rules = [];
            $resp->rights = (object)[];
            if (count($funcs) > 1) {
                foreach ($funcs as $i => $func) {
                    $auth = self::auth($func, $routers, $rights);
                    $pages = $auth->pages;
                    $acts = $auth->acts;
                    if (count($pages)) {
                        foreach ($pages as $page) {
                            $resp->rules[] = $page;
                            $resp->rights->$page = $acts;
                        }
                    }
                }
            } elseif (md5($info->functions) == md5('["0.0:0"]')) {
                $roles = [];
                foreach ($rights as $id => $act) {
                    $roles[] = $act;
                }
                foreach ($routers as $id => $page) {
                    $resp->rules[] = $page;
                    $resp->rights->$page = $roles;
                }
            } else {
                $func = $funcs[0];
                $auth = self::auth($func, $routers, $rights);
                $pages = $auth->pages;
                $acts = $auth->acts;
                if (count($pages)) {
                    foreach ($pages as $page) {
                        $resp->rules[] = $page;
                        $resp->rights->$page = $acts;
                    }
                }
            }
        }
        //$resp->rules = self::filterRules($resp->rules, $info->role_id);
        return $resp;
    }

    private static function filterRules($data= [],$roleId = 0){
        if (in_array($roleId,[80,81])){
            if (($key = array_search("students", $data)) !== false) {
                unset($data[$key]);
            }
        }
        return $data;
    }
    private static function mix($obj, $k, $v)
    {
        $resp = (object)[];
        if (isset($obj->$k) && isset($obj->$v)) {
            $key = $obj->$k;
            $val = $obj->$v;
            $resp->$key = $val;
        }
        return $resp;
    }

    public static function config($branch_id = 0) {
        $resp = (Object)[];
        $config = self::query("SELECT `key`, `value` FROM config WHERE branch IN ($branch_id)");
        if (count($config)) {
            foreach ($config as $conf) {
                $key = $conf->key;
                $val = $conf->value;
                $resp->$key = $val;
            }
        }
        return $resp;
    }

    public static function authen($scope = [])
    {
        $resp = (object)[];
        if (count($scope)) {
            $rolesdata = self::query("SELECT SUBSTR(`key` FROM 5) AS id, `value` AS `action` FROM `config` WHERE `key` LIKE 'role%'");
            $modesdata = self::query("SELECT SUBSTR(`key` FROM 4) AS id, `value` AS `router` FROM `config` WHERE `key` LIKE 'mod%'");
            if (count($rolesdata) && count($modesdata)) {
                $rights = (object)[];
                $routers = (object)[];
                foreach ($rolesdata as $role) {
                    $obj = self::mix($role, 'id', 'action');
                    $key = key((array)$obj);
                    $rights->$key = $obj->$key;
                }
                foreach ($modesdata as $mode) {
                    $obj = self::mix($mode, 'id', 'router');
                    $key = key((array)$obj);
                    $routers->$key = $obj->$key;
                }
                $added = [];
                foreach ($scope as $roles) {
                    $key = "role_$roles->role_id";
                    if (!in_array($key, $added)) {
                        $resp->$key = self::permission($roles, $rights, $routers);
                        $added[] = $key;
                    }
                }
            }
        }

       // var_dump($resp);exit;
        return $resp;
    }

    public static function roles($scope = [], $deep = false)
    {
        $resp = (object)[];
        if (count($scope)) {
            $rules = DB::select(DB::raw("SELECT SUBSTR(`key` FROM 5) AS id, `value` AS `action` FROM `config` WHERE `key` LIKE 'role%'"));
            $pages = DB::select(DB::raw("SELECT SUBSTR(`key` FROM 4) AS id, `value` AS `router` FROM `config` WHERE `key` LIKE 'mod%'"));
            foreach ($scope as $roles) {
                if ($roles->functions && is_array(json_decode($roles->functions))) {
                    $rule = "role_$roles->role_id";
                    $resp->$rule = $roles;
                    $functions = json_decode($roles->functions);
                    $obj = $roles;
                    $obj->rules = [];
                    $list = $deep ? (object)[] : [];
                    if (count($functions) > 1) {
                        foreach ($functions as $function) {
                            $dat = explode(':', $function);
                            $sub = $dat[0];
                            $act = $dat[1];
                            if (strpos($sub, '.0') > -1) {
                                $par = substr($sub, 0, strpos($sub, '.'));
                                foreach ($pages as $page) {
                                    if (md5(substr($page->id, 0, strpos((string)$page->id, '.'))) == md5($par) || (md5($page->id) == md5($par))) {
                                        $key = $page->router;
                                        if ($deep) {
                                            $list->$key = [];
                                            if (md5($act) == md5(0)) {
                                                $list->$key = ['list', 'search', 'detail', 'add', 'edit', 'delete', 'report', 'upload', 'download'];
                                            } elseif (strpos($act, '.') > -1) {
                                                $acts = explode('.', $act);
                                                $acs = [];
                                                foreach ($acts as $atc) {
                                                    foreach ($rules as $rul) {
                                                        if ((int)$rul->key == (int)$atc) {
                                                            $acs[] = $rul->action;
                                                        }
                                                    }
                                                }
                                                $list->$key = $acs;
                                            }
                                        } elseif (!in_array($key, $list)) {
                                            $list[] = $key;
                                        }
                                    }
                                }
                            } else {
                                foreach ($pages as $page) {
                                    if (md5($page->id) == md5($sub)) {
                                        $key = $page->router;
                                        if ($deep) {
                                            $list->$key = [];
                                            if (md5($act) == md5(0)) {
                                                $list->$key = ['list', 'search', 'detail', 'add', 'edit', 'delete', 'report', 'upload', 'download'];
                                            } elseif (strpos($act, '.') > -1) {
                                                $acts = explode('.', $act);
                                                $acs = [];
                                                foreach ($acts as $atc) {
                                                    foreach ($rules as $rul) {
                                                        if ((int)$rul->key == (int)$atc) {
                                                            $acs[] = $rul->action;
                                                        }
                                                    }
                                                }
                                                $list->$key = $acs;
                                            }
                                        }
                                    } elseif (!$deep && !in_array($key, $list)) {
                                        $list[] = $key;
                                    }
                                }
                            }
                        }
                        if ($deep) {
                            $list->dashboard = ['list', 'search', 'detail', 'add', 'edit', 'delete', 'report', 'upload', 'download'];
                        } else {
                            $list[] = 'dashboard';
                        }
                    } elseif (md5($roles->functions) == md5('["0.0:0"]')) {
                        foreach ($pages as $page) {
                            $key = $page->router;
                            if ($deep) {
                                $list->$key = ['list', 'search', 'detail', 'add', 'edit', 'delete', 'report', 'upload', 'download'];
                            } else {
                                $list[] = $key;
                            }
                        }
                        if ($deep) {
                            $list->dashboard = ['list', 'search', 'detail', 'add', 'edit', 'delete', 'report', 'upload', 'download'];
                        } else {
                            $list[] = 'dashboard';
                        }
                    } else {
                        $function = json_decode($roles->functions)[0];
                        $dat = explode(':', $function);
                        $sub = $dat[0];
                        $act = $dat[1];
                        $par = substr($sub, 0, strpos($sub, '.'));
                        foreach ($pages as $page) {
                            if ((int)$page->id == (int)$par) {
                                $key = $page->router;
                                if ($deep) {
                                    $list->$key = [];
                                    if (md5($act) == md5(0)) {
                                        $list->$key = ['list', 'search', 'detail', 'add', 'edit', 'delete', 'report', 'upload', 'download'];
                                    } elseif (strpos($act, '.') > -1) {
                                        $acts = explode('.', $act);
                                        $acs = [];
                                        foreach ($acts as $atc) {
                                            foreach ($rules as $rul) {
                                                if ((int)$rul->key == (int)$atc) {
                                                    $acs[] = $rul->action;
                                                }
                                            }
                                        }
                                        $list->$key = $acs;
                                    }
                                } else {
                                    $list[] = $key;
                                }
                            }
                        }
                    }
                    if ($deep) {
                        $list->dashboard = ['list', 'search', 'detail', 'add', 'edit', 'delete', 'report', 'upload', 'download'];
                    } else {
                        $list[] = 'dashboard';
                    }
                    $obj->rules = $list;
                    $resp->$rule = $obj;
                }
            }
        }
        return $resp;
    }

    public static function load($pages = [], $actions = [])
    {
        $r = new Route();
        foreach ($pages as $page => $object) {
            $method = 'get';
            $target = is_string($object) ? $object : $page;
            $router = $target;
            $action = 'index';
            $caller = ucfirst($target) . 'Controller@' . $action;
            if (is_string($page) && is_array($object)) {
                $method = isset($object['method']) ? $object['method'] : $method;
                $router = isset($object['router']) ? $object['router'] : $router;
                $action = isset($object['action']) ? md5($object['action']) == md5('@') ? '' : '@' . $object['action'] : '@' . $action;
                $caller = isset($object['caller']) ? $object['caller'] : ucfirst($target) . 'Controller' . $action;
                self::import($method, $router, $caller);
            } else {
                if (!$actions) {
                    $actions = json_decode('[{
					"method": "resource",
					"router": "*",
					"action": "@"
					},{
					"method": "get",
					"router": "*",
					"action": "index"
					},{
					"method": "get",
					"router": "*/search/{field}&{keyword}&{pageSize}",
					"action": "search"
					},{
					"method": "get",
					"router": "*/{id}",
					"action": "show"
					},{
					"method": "get",
					"router": "*/{id}/edit",
					"action": "show"
					},{
					"method": "post",
					"router": "*",
					"action": "store"
					},{
					"method": "delete",
					"router": "*/{id}",
					"action": "destroy"
					},{
					"method": "put",
					"router": "*/{id}",
					"action": "update"
					}]');
                }
                foreach ($actions as $do) {
                    $method = (property_exists($do, 'method') && isset($do->method)) ? $do->method : $method;
                    $router = (property_exists($do, 'router') && isset($do->router)) ? str_replace('*', $target, $do->router) : $router;
                    $action = (property_exists($do, 'action') && isset($do->action)) ? $do->action == '@' ? '' : '@' . $do->action : '@' . $action;
                    $caller = ucfirst($target) . 'Controller' . $action;
                    // echo("$method, $router, $caller\n");
                    self::import($method, $router, $caller);
                }
            }
        }
    }

    public static function transferTuitionFeeSpecial($tftfdt, $rbrkid, $rprdid)
    {
        $resp = 0;
        $rczo = self::first("SELECT zone_id FROM branches WHERE id = $rbrkid");
        if (in_array($tftfdt->id, [1, 3, 4, 17, 19]) && $rprdid == 2 && $rczo->zone_id == 2) {
            switch ($tftfdt->id) {
                case 1:
                    $resp = 2;
                    break;
                case 3:
                    $resp = 66;
                    break;
                case 4:
                    $resp = 7;
                    break;
                case 17:
                    $resp = 36;
                    break;
                case 19:
                    $resp = 67;
                    break;
            }
        }
        return $resp;
    }

    public static function calcTransferTuitionFee($ttfid, $transfer_amount, $rbrkid, $rprdid, $transfer_sessions = 0)
    {
        $resp = (object)[];
        if ($ttfid && (int)$transfer_amount >= 0) {
            $available_tuiotion_fee_ids = self::query("SELECT exchange_tuition_fee_id FROM tuition_fee_relation WHERE tuition_fee_id = $ttfid AND status = 1");
            if (count($available_tuiotion_fee_ids)) {
                $available_ids = [];
                foreach ($available_tuiotion_fee_ids as $id) {
                    $available_ids[] = (int)$id->exchange_tuition_fee_id;
                }
                $same_mode = false;
                $available_ids = implode(',', $available_ids);
                $tftfdt = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.id = $ttfid");
                $rctfdt = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.product_id = $rprdid AND (t.branch_id LIKE '%,$rbrkid' OR t.branch_id 
                                    LIKE '%,$rbrkid,%' OR t.branch_id LIKE '$rbrkid,%' OR t.branch_id = '$rbrkid') AND t.id IN ($available_ids)");
                if ((int)$tftfdt->type === 1 && in_array($rbrkid, explode(',', $tftfdt->branch_id))) {
                    $rctfdt = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.product_id = $rprdid AND (t.branch_id LIKE '%,$rbrkid' OR t.branch_id 
                                    LIKE '%,$rbrkid,%' OR t.branch_id LIKE '$rbrkid,%' OR t.branch_id = '$rbrkid') AND t.type = 1 AND t.id IN ($available_ids)");
                }
                if ($tftfdt->id == $rctfdt->id) {
                    $same_mode = true;
                }
                $special = self::transferTuitionFeeSpecial($tftfdt, $rbrkid, $rprdid);
                $resp->special = $special;
                $sprctf = 0;
                $receiv = 0;
                if ($special) {
                    $rctfdt = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.id = $ttfid");
                    $tfdisc = $tftfdt->discount > 1000 ? (int)$tftfdt->discount : $tftfdt->discount;
                    $tfdisc = $tfdisc < 100 ? (int)(ceil(round($tfdisc * ($tftfdt->price / 100)) / 1000) * 1000) : $tfdisc;
                    $receiv = $rctfdt->price - $tfdisc;
                    $resp->sessions = ceil($transfer_amount / ($receiv / $rctfdt->session));
                    $resp->special = 1;
                } elseif ($rctfdt) {
                    $discnt = $rctfdt->discount > 1000 ? (int)$rctfdt->discount : $rctfdt->discount;
                    $discnt = $discnt < 100 ? (int)(ceil(round($discnt * ($rctfdt->price / 100)) / 1000) * 1000) : $discnt;
                    $receiv = $rctfdt->price - $discnt;
                    $resp->sessions = ceil($transfer_amount / ($receiv / $rctfdt->session));
                }
                if ($same_mode && $transfer_sessions) {
                    $resp->sessions = $transfer_sessions;
                }
                $sprctf = ceil($receiv / $rctfdt->session);
                $resp->transfer_tuition_fee = $tftfdt;
                $resp->receive_tuition_fee = $rctfdt;
                $resp->transfer_amount = $transfer_amount;
                $resp->single_price = round($sprctf / 1000) * 1000;
            }
        }
        return $resp;
    }

  public static function calcTransferTuitionFeeForTuitionTransfer($ttfid, $transfer_amount, $rbrkid, $rprdid)
  {
    $resp = (object)[];
    if ($ttfid && (int)$transfer_amount >= 0) {
      $available_tuiotion_fee_ids = self::query("SELECT exchange_tuition_fee_id FROM tuition_fee_relation WHERE tuition_fee_id = $ttfid AND status = 1");
      if (count($available_tuiotion_fee_ids)) {
        $available_ids = [];
        foreach ($available_tuiotion_fee_ids as $id) {
          $available_ids[] = (int)$id->exchange_tuition_fee_id;
        }
        $available_ids = implode(',', $available_ids);
        $tftfdt = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.id = $ttfid");
        $rctfdt = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.product_id = $rprdid AND (t.branch_id LIKE '%,$rbrkid' OR t.branch_id 
                                    LIKE '%,$rbrkid,%' OR t.branch_id LIKE '$rbrkid,%' OR t.branch_id = '$rbrkid') AND t.id IN ($available_ids)");
        if ((int)$tftfdt->type === 1 && in_array($rbrkid, explode(',', $tftfdt->branch_id))) {
          $rctfdt = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.product_id = $rprdid AND (t.branch_id LIKE '%,$rbrkid' OR t.branch_id 
                                    LIKE '%,$rbrkid,%' OR t.branch_id LIKE '$rbrkid,%' OR t.branch_id = '$rbrkid') AND t.type = 1 AND t.id IN ($available_ids)");
        }

        $special = 0;
        $resp->special = $special;
        $receiv = 0;
        if ($special) {
          $rctfdt = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.id = $ttfid");
          $receiv = $rctfdt->price;
          $resp->sessions = ceil($transfer_amount / ($receiv / $rctfdt->session));
          $resp->special = 1;
        } elseif ($rctfdt) {
          $receiv = $rctfdt->price;
          $resp->sessions = ceil($transfer_amount / ($receiv / $rctfdt->session));
        }

        $sprctf = ceil($receiv / $rctfdt->session);
        $resp->transfer_tuition_fee = $tftfdt;
        $resp->receive_tuition_fee = $rctfdt;
        $resp->transfer_amount = $transfer_amount;
        $resp->single_price = round($sprctf / 1000) * 1000;
      }
    }
    return $resp;
  }
    public static function calcTransferTuitionFeeV2($old_tuition_fee_id, $transfer_amount, $new_branch_id, $new_product_id, $transfer_sessions = 0) {
        $resp = (object)[
            'code' => APICode::WRONG_PARAMS,
            'message' => '',
            'special' => 0,
            'sessions' => 0,
            'transfer_tuition_fee' => [],
            'receive_tuition_fee' => [],
            'transfer_amount' => 0,
            'single_price' => 0
        ];
        if ($old_tuition_fee_id) {
            if ((int)$transfer_amount >= 0) {
                if ((int)$transfer_sessions > 0) {
                    $available_tuiotion_fee_ids = self::query("SELECT exchange_tuition_fee_id FROM tuition_fee_relation WHERE tuition_fee_id = $old_tuition_fee_id AND status = 1");
                    if (count($available_tuiotion_fee_ids)) {
                        $available_ids = [];
                        foreach ($available_tuiotion_fee_ids as $id) {
                            $available_ids[] = (int)$id->exchange_tuition_fee_id;
                        }
                        $same_mode = false;
                        $available_ids = implode(',', $available_ids);
                        $old_tuition_fee = self::first("SELECT t.*, p.name product_name FROM tuition_fee t LEFT JOIN products p ON t.product_id = p.id WHERE t.id = $old_tuition_fee_id");
                        $new_tuition_fee = self::first("SELECT t.*, p.name product_name FROM tuition_fee t LEFT JOIN products p ON t.product_id = p.id WHERE t.product_id = $new_product_id AND (t.branch_id LIKE '%,$new_branch_id' OR t.branch_id  LIKE '%,$new_branch_id,%' OR t.branch_id LIKE '$new_branch_id,%' OR t.branch_id = '$new_branch_id') AND t.id IN ($available_ids)");
                        if($old_tuition_fee && $new_tuition_fee){
                            if ((int)$old_tuition_fee->type === 1 && in_array($new_branch_id, explode(',', $old_tuition_fee->branch_id))) {
                                $new_tuition_fee = self::first("SELECT t.*, p.name product_name FROM tuition_fee t LEFT JOIN products p ON t.product_id = p.id WHERE t.product_id = $new_product_id AND (t.branch_id LIKE '%,$new_branch_id' OR t.branch_id
                                                    LIKE '%,$new_branch_id,%' OR t.branch_id LIKE '$new_branch_id,%' OR t.branch_id = '$new_branch_id') AND t.type = 1 AND t.id IN ($available_ids)");
                            }
                            if($new_tuition_fee){
                                if ($old_tuition_fee->id == $new_tuition_fee->id) {
                                    $same_mode = true;
                                }
                                if ($old_tuition_fee->product_id == $new_product_id) {
                                    $same_mode = true;
                                }
                                $special = self::transferTuitionFeeSpecial($old_tuition_fee, $new_branch_id, $new_product_id);
                                $resp->special = $special;

                                $receiv = 0;
                                if ($special) {
                                    $new_tuition_fee = self::first("SELECT t.*, p.name product_name FROM tuition_fee t LEFT JOIN products p ON t.product_id = p.id WHERE t.id = $special");
                                    $new_receivable = $new_tuition_fee->price - $new_tuition_fee->discount;
                                    $old_receivable = $old_tuition_fee->price - $old_tuition_fee->discount;
                                    $resp->sessions = ceil($transfer_sessions * ($old_receivable / $old_tuition_fee->session) / ($new_receivable / $new_tuition_fee->session));
                                    $resp->special = 1;

                                    $new_single_price = ceil($new_receivable / $new_tuition_fee->session);
                                    $resp->transfer_tuition_fee = $old_tuition_fee;
                                    $resp->receive_tuition_fee = $new_tuition_fee;
                                    $resp->transfer_amount = $transfer_amount;
                                    $resp->single_price = round($new_single_price / 1000) * 1000;
                                } elseif ($new_tuition_fee) {
                                    $new_tuition_fee_discount = $new_tuition_fee->discount > 1000 ? (int)$new_tuition_fee->discount : $new_tuition_fee->discount;
                                    $new_receivable = $new_tuition_fee->price - $new_tuition_fee_discount;
                                    $old_receivable = $old_tuition_fee->price - $old_tuition_fee->discount;
                                    $resp->sessions = ceil($transfer_sessions * ($old_receivable / $old_tuition_fee->session) / ($new_receivable / $new_tuition_fee->session));

                                    $new_single_price = ceil($new_receivable / $new_tuition_fee->session);
                                    $resp->transfer_tuition_fee = $old_tuition_fee;
                                    $resp->receive_tuition_fee = $new_tuition_fee;
                                    $resp->transfer_amount = $transfer_amount;
                                    $resp->single_price = round($new_single_price / 1000) * 1000;
                                }
                                if ($same_mode && $transfer_sessions) {
                                    $resp->sessions = $transfer_sessions;
                                }
                                $resp->code = APICode::SUCCESS;
                            }else{
                                $resp->message = 'Thiếu thông tin sản phẩm hoặc gói phí quy đổi không áp dụng tại trung tâm. Vui lòng liên hệ CRM để được hỗ trợ';
                            }
                        }else{
                            $resp->message = 'Thiếu thông tin sản phẩm hoặc gói phí quy đổi không áp dụng tại trung tâm. Vui lòng liên hệ CRM để được hỗ trợ';
                        }
                    }else{
                        $resp->message = 'Không tìm thấy gói phí để quy đổi. Vui lòng liên hệ CRM để được hỗ trợ';
                    }
                }else{
                    $resp->message = 'Số buổi không hợp lệ';
                }
            }else{
                $resp->message = 'Số tiền không hợp lệ';
            }
        }else{
            $resp->message = 'Gói phí không hợp lệ. Vui lòng kiểm tra lại thông tin gói phí';
        }
        return $resp;
    }
    public static function checkExit($hash, $table) {
		$check = self::first("SELECT COUNT(id) `exists` FROM $table WHERE `hash_key` = '$hash'");
		return (int)$check->exists > 0;
    }
    public static function unix($number = 6) {
		return $number > 15 ? strtoupper(uniqid()) : substr(strrev(hexdec((string)uniqid())),0, (int)$number);
	}
  public static function exchangeFee($old_tuition_fee_id, $transfer_amount, $new_branch_id, $new_product_id)
  {
    $resp = (object)[
      'code' => APICode::WRONG_PARAMS,
      'message' => '',
      'special' => 0,
      'sessions' => 0,
      'transfer_tuition_fee' => [],
      'receive_tuition_fee' => [],
      'transfer_amount' => 0,
      'single_price' => 0
    ];
    if ($old_tuition_fee_id) {
      if((int)$transfer_amount >= 0){
        $available_tuiotion_fee_ids = self::query("SELECT exchange_tuition_fee_id FROM tuition_fee_relation WHERE tuition_fee_id = $old_tuition_fee_id AND status = 1");
        if (count($available_tuiotion_fee_ids)) {
          $available_ids = [];
          foreach ($available_tuiotion_fee_ids as $id) {
            $available_ids[] = (int)$id->exchange_tuition_fee_id;
          }

          $available_ids = implode(',', $available_ids);
          $old_tuition_fee = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.id = $old_tuition_fee_id");
          $new_tuition_fee = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.product_id = $new_product_id AND (t.branch_id LIKE '%,$new_branch_id' OR t.branch_id 
                                  LIKE '%,$new_branch_id,%' OR t.branch_id LIKE '$new_branch_id,%' OR t.branch_id = '$new_branch_id') AND t.id IN ($available_ids)");
          if($old_tuition_fee && $new_tuition_fee){
            if ((int)$old_tuition_fee->type === 1 && in_array($new_branch_id, explode(',', $old_tuition_fee->branch_id))) {
              $new_tuition_fee = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.product_id = $new_product_id AND (t.branch_id LIKE '%,$new_branch_id' OR t.branch_id 
                                  LIKE '%,$new_branch_id,%' OR t.branch_id LIKE '$new_branch_id,%' OR t.branch_id = '$new_branch_id') AND t.type = 1 AND t.id IN ($available_ids)");
            }

            if ($new_tuition_fee) {
              $resp->sessions = ceil($transfer_amount / ($new_tuition_fee->price / $new_tuition_fee->session));
              $new_single_price = ceil($new_tuition_fee->price / $new_tuition_fee->session);
              $resp->transfer_tuition_fee = $old_tuition_fee;
              $resp->receive_tuition_fee = $new_tuition_fee;
              $resp->transfer_amount = $transfer_amount;
              $resp->single_price = round($new_single_price / 1000) * 1000;
            }
            $resp->code = APICode::SUCCESS;
          }else{
            $resp->message = 'Thiếu thông tin sản phẩm hoặc gói phí quy đổi không áp dụng tại trung tâm. Vui lòng liên hệ CMS để được hỗ trợ';
          }
        }else{
          $resp->message = 'Không tìm thấy gói phí để quy đổi. Vui lòng liên hệ CMS để được hỗ trợ';
        }
      }else{
        $resp->message = 'Số tiền không hợp lệ';
      }
    }else{
      $resp->message = 'Gói phí không hợp lệ. Vui lòng kiểm tra lại thông tin gói phí';
    }
    return $resp;
  }

  public static function exchangeFeeForClassTransfer($old_tuition_fee_id, $transfer_amount, $new_branch_id, $new_product_id, $sessions)
  {
    $resp = (object)[
      'code' => APICode::WRONG_PARAMS,
      'message' => '',
      'special' => 0,
      'sessions' => 0,
      'transfer_tuition_fee' => [],
      'receive_tuition_fee' => [],
      'transfer_amount' => 0,
      'single_price' => 0
    ];
    if ($old_tuition_fee_id) {
      if((int)$transfer_amount >= 0){
        $available_tuiotion_fee_ids = self::query("SELECT exchange_tuition_fee_id FROM tuition_fee_relation WHERE tuition_fee_id = $old_tuition_fee_id AND status = 1");
        if (count($available_tuiotion_fee_ids)) {
          $available_ids = [];
          foreach ($available_tuiotion_fee_ids as $id) {
            $available_ids[] = (int)$id->exchange_tuition_fee_id;
          }

          $available_ids = implode(',', $available_ids);
          $old_tuition_fee = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.id = $old_tuition_fee_id");
          $new_tuition_fee = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.product_id = $new_product_id AND (t.branch_id LIKE '%,$new_branch_id' OR t.branch_id 
                                  LIKE '%,$new_branch_id,%' OR t.branch_id LIKE '$new_branch_id,%' OR t.branch_id = '$new_branch_id') AND t.id IN ($available_ids)");
          if($old_tuition_fee && $new_tuition_fee){
            if ((int)$old_tuition_fee->type === 1 && in_array($new_branch_id, explode(',', $old_tuition_fee->branch_id))) {
              $new_tuition_fee = self::first("SELECT t.*, p.name AS product_name FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id WHERE t.product_id = $new_product_id AND (t.branch_id LIKE '%,$new_branch_id' OR t.branch_id 
                                  LIKE '%,$new_branch_id,%' OR t.branch_id LIKE '$new_branch_id,%' OR t.branch_id = '$new_branch_id') AND t.type = 1 AND t.id IN ($available_ids)");
            }

            if ($new_tuition_fee) {
              if($transfer_amount > 0){
                $resp->sessions = ceil($transfer_amount / ($new_tuition_fee->receivable / $new_tuition_fee->session));
              }else{
                $resp->sessions = $sessions;
              }

              $new_single_price = ceil($new_tuition_fee->receivable / $new_tuition_fee->session);
              $resp->transfer_tuition_fee = $old_tuition_fee;
              $resp->receive_tuition_fee = $new_tuition_fee;
              $resp->transfer_amount = $transfer_amount;
              $resp->single_price = round($new_single_price / 1000) * 1000;
            }
            $resp->code = APICode::SUCCESS;
          }else{
            $resp->message = 'Thiếu thông tin sản phẩm hoặc gói phí quy đổi không áp dụng tại trung tâm. Vui lòng liên hệ CMS để được hỗ trợ';
          }
        }else{
          $resp->message = 'Không tìm thấy gói phí để quy đổi. Vui lòng liên hệ CMS để được hỗ trợ';
        }
      }else{
        $resp->message = 'Số tiền không hợp lệ';
      }
    }else{
      $resp->message = 'Gói phí không hợp lệ. Vui lòng kiểm tra lại thông tin gói phí';
    }
    return $resp;
  }

    public static function getBranchIds($users_data)
    {
        $roles_branches = $users_data->branches;

        $branch_ids = [];
        foreach ($roles_branches as $item) {
            $branch_ids[] = $item->id;
        }

        return $branch_ids;
    }

    /*
     * Function get weekday of date
     * */
    public static function getWdayOfDate()
    {
        $weekday = date("l");
        $weekday = strtolower($weekday);
        switch ($weekday) {
            case 'monday':
                $Wday = 1;
                break;
            case 'tuesday':
                $Wday = 2;
                break;
            case 'wednesday':
                $Wday = 3;
                break;
            case 'thursday':
                $Wday = 4;
                break;
            case 'friday':
                $Wday = 5;
                break;
            case 'saturday':
                $Wday = 6;
                break;
            default:
                $Wday = 0;
                break;
        }
        return $Wday;
    }

    public static function getDayName($day)
    {
        switch ($day) {
            case 0:
                $Wday = 'Sunday';
                break;
            case 1:
                $Wday = 'Monday';
                break;
            case 2:
                $Wday = 'Tuesday';
                break;
            case 3:
                $Wday = 'Wednesday';
                break;
            case 4:
                $Wday = 'Thursday';
                break;
            case 5:
                $Wday = 'Friday';
                break;
            case 6:
                $Wday = 'Saturday';
                break;
            default:
                $Wday = '';
                break;
        }
        return $Wday;
    }

    public static function getDayNameVi($day)
    {
        switch ($day) {
            case 0:
                $Wday = 'Chủ nhật';
                break;
            case 1:
                $Wday = 'Thứ 2';
                break;
            case 2:
                $Wday = 'Thứ 3';
                break;
            case 3:
                $Wday = 'Thứ 4';
                break;
            case 4:
                $Wday = 'Thứ 5';
                break;
            case 5:
                $Wday = 'Thứ 6';
                break;
            case 6:
                $Wday = 'Thứ 7';
                break;
            default:
                $Wday = '';
                break;
        }
        return $Wday;
    }

    public static function saveFile($attached_file)
    {
        $explod = explode(',', $attached_file);
        $decod = base64_decode($explod[1]);
        if (str_contains($explod[0], 'pdf')) {
            $extend = 'pdf';
        } else if (str_contains($explod[0], 'png')) {
            $extend = 'png';
        } else if (str_contains($explod[0], 'doc')) {
            $extend = 'doc';
        } else if (str_contains($explod[0], 'docx')) {
            $extend = 'docx';
        } else {
            $extend = 'jpg';
        }


        $fileAttached = md5($attached_file . str_random()) . '.' . $extend;
        $p = FOLDER . DS . 'upload' . DS . $fileAttached;
        file_put_contents($p, $decod);

        return "static/upload/$fileAttached";
    }

    public static function isValidDate($date, $format = "Y-m-d")
    {
        return $date === date($format, strtotime($date));
    }

    public static function updateDate($old_end_date = '', $new_end_date = '', $student_id = 0, $holidays = [], $class_days = [], $exchange_info = [])
    {
        $resp = (Object)[
            "final_last_date" => null,
            "contracts" => []
        ];

        if ($old_end_date && $new_end_date && $student_id) {
            $contracts = self::getNextContracts($old_end_date, $student_id);
            if (!empty($contracts)) {
                $list_contract = [];
                $start = $new_end_date;
                $final_last_date = null;
                foreach ($contracts as $c) {
                    $sessions = $c->real_sessions;
                    if (!empty($exchange_info)) {
                        $sessions = self::calcTransferTuitionFee(
                            $c->tuition_fee_id,
                            self::calAmountReceived($c),
                            $exchange_info['branch_id'],
                            $c->product_id,
                            $c->real_sessions
                        )->sessions;
                    }
                    if (!self::isGreaterThan($c->start_date, $start)) {
                        $start_date = self::getNextDay($start);
                        $class_days = self::getDayOfWeek($start_date);
                        $end_date = self::calEndDate($sessions, $class_days, $holidays[$c->product_id], $start_date)->end_date;
                        $start = $end_date;
                    } else {
                        $start = $c->start_date;
                        $start_date = $c->start_date;
                        $class_days = self::getDayOfWeek($start_date);
                        $end_date = self::calEndDate($sessions, $class_days, $holidays[$c->product_id], $start_date)->end_date;
                    }
                    $list_contract[$c->id] = (Object)[
                        "id" => $c->id,
                        "start" => $start_date,
                        "end" => $end_date,
                        "detail" => $c
                    ];
                    if (($c->payload == 0 && $c->debt_amount == 0) || ($c->payload == 1 && $c->total_charged > 0) || ($c->must_charge == $c->total_discount)) {
                        $final_last_date = $end_date;
                    }
                }
                $resp = (Object)[
                    "final_last_date" => $final_last_date,
                    "contracts" => $list_contract
                ];
            }
        }

        return $resp;
    }

    public static function getDayOfWeek($date_string){
      $date = strtotime($date_string);
      $day = getdate($date);

      return [$day['wday']];
    }

    public static function getNextContracts($date, $student_id)
    {
        $query = "
                SELECT * FROM contracts WHERE end_date > '$date' AND student_id = $student_id AND `status` > 0 AND real_sessions > 0 AND status < 7 ORDER BY end_date ASC
           ";

        $contracts = DB::select(DB::raw($query));

        return $contracts;
    }

    public static function getNextDay($date)
    {
        return date('Y-m-d', strtotime('+1 day', strtotime($date)));
    }

    public static function getPreviousDay($date)
    {
        return date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }

    public function getFinalDate($student_id)
    {
        $query = "
             SELECT 
                    IF(c.id IS NULL, c.`end_date`, c.`enrolment_last_date`) as end_date
                FROM 
                    contracts AS c
                where
                    c.`student_id` = 1301
                    AND 
                    (
                        (c.enrolment_start_date IS NULL AND c.`real_sessions` > 0)
                        OR (c.enrolment_start_date IS NOT NULL AND c.`real_sessions` > 0 AND c.`status` = 6)
                    )
                    AND c.status > 0
                ORDER BY end_date DESC
                LIMIT 0,1
        ";

        $res = DB::select(DB::raw($query));

        if (!empty($res)) {
            return $res[0]->end_date;
        } else {
            return false;
        }
    }

    public static function isGreaterThan($date_1, $date_2)
    {
        $time_1 = strtotime($date_1);
        $time_2 = strtotime($date_2);

        return ($time_1 > $time_2);
    }

    public static function isLessThan($date_1, $date_2)
    {
        $time_1 = strtotime($date_1);
        $time_2 = strtotime($date_2);

        return ($time_1 < $time_2);
    }

    public static function calAmountReceived($info){
        $new_receivable = $info->tuition_fee_price?(($info->tuition_fee_price - $info->discount_value) * $info->receivable / $info->tuition_fee_price):0;
        $amount_received = $new_receivable?($info->total_charged * $info->receivable / $new_receivable):0;

        return $amount_received;
    }

    public static function getExchangeProgram($program_id, $to_branch_id){
        $query = "
            SELECT
                t.program_id
            FROM 
                term_program_product AS t LEFT JOIN programs AS p ON t.program_id = p.id
            WHERE 
                p.branch_id = $to_branch_id 
                AND t.program_code_id IN (SELECT program_code_id FROM term_program_product WHERE program_id = $program_id)
                AND p.semester_id IN (SELECT semester_id FROM programs WHERE id = $program_id)
        ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->program_id;
        }else{
            return 0;
        }
    }

    public static function getStartCjrnId($class_id, $start_date){
        $query = "SELECT MIN(cjrn_id) AS cjrn_id FROM schedules WHERE class_id=$class_id AND cjrn_classdate >= '$start_date'";
        $res = $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->cjrn_id;
        }else{
            return 0;
        }
    }

    public static function getStuId($student_id){
        $query = "
            SELECT stu_id FROM students WHERE id = $student_id
        ";
        $res = DB::select(DB::raw($query));
        if($res){
            return $res[0]->stu_id;
        }else{
            return 0;
        }
    }

    //A ∩ B
    public static function setIntersection(array $setA,array $setB){
        $intersection = [];

        foreach ($setA as $a){
            if(in_array($a, $setB)){
                $intersection[] = $a;
            }
        }

        return $intersection;
    }

    //A ∪ B
    public static function setUnion(array $setA, array $setB){
        if(empty($setA)){
            return $setB;
        }elseif (empty($setB)){
            return $setA;
        }else{
            $union = [];
            foreach ($setA as $a) {
                if(!in_array($a, $union)){
                    $union[] = $a;
                }
            }

            foreach ($setB as $b) {
                if(!in_array($b, $union)){
                    $union[] = $b;
                }
            }

            return $union;
        }
    }

    //A \ B
    public static function setComplement(array $setA, array $setB){
        $complement = [];
        if(!empty($setA)){
            foreach ($setA as $a){
                if(!in_array($a, $setB)){
                    $complement[] = $a;
                }
            }
        }
        return $complement;
    }

    //(A \ B) ∪ (B \ A)
    public static function setSymmetricDifference(array $setA, array $setB){
        $symmetricDifference = [];
        foreach ($setA as $a){
            if(!in_array($a, $setB)){
                $symmetricDifference[] = $a;
            }
        }
        foreach ($setB as $b){
            if(!in_array($b, $setA)){
                $symmetricDifference[] = $b;
            }
        }

        return $symmetricDifference;
    }

  public static function getMultiClassDays($class_ids){
    $resp = [];

    if(!empty($class_ids)){
      $valid_classes = [];
      foreach ($class_ids as $class_id) {
        if(is_numeric($class_id) && $class_id > 0){
          $valid_classes[] = $class_id;
        }
      }

      if(!empty($valid_classes)){
        $class_ids_tring = implode(",",$valid_classes);
        $query = "SELECT class_day, class_id FROM sessions WHERE class_id IN ($class_ids_tring) GROUP BY class_id, class_day";

        $res = self::query($query);

        foreach ($res as $re){
          if(isset($resp[$re->class_id])){
            $resp[$re->class_id][] = $re->class_day;
          }else{
            $resp[$re->class_id] = [$re->class_day];
          }
        }
      }
    }


    return $resp;
  }

  public static function getDefaultClassDays($product_id){
    return [2];
  }

  public static function hasSchedules($class_id, $current_date){
    $query = "SELECT MIN(cjrn_classdate) AS cjrn_classdate FROM schedules WHERE class_id = $class_id AND cjrn_classdate >= '$current_date'";
    $res = self::first($query);
    if (!empty($res) && $res->cjrn_classdate) {
      return true;
    }else{
      return false;
    }
  }

  public static function getProducts(){
    $products = DB::select(DB::raw("SELECT id, `name` FROM products"));

    $res = [];
    foreach ($products as $product){
      $res[$product->id] = $product;
    }

    return $res;
  }

  public static function getTuitionFees($ids = []){
    $res = [];

    if(!empty($ids)){
      $ids_string = implode(",",$ids);
      $tuition_fees = DB::select(DB::raw("SELECT id, `name` FROM tuition_fee WHERE id IN ($ids_string)"));

      foreach ($tuition_fees as $tuition_fee){
        $res[$tuition_fee->id] = $tuition_fee;
      }
    }

    return $res;
  }
  public static function getPrograms($ids = []){
    $res = [];

    if(!empty($ids)){
      $ids_string = implode(",",$ids);
      $programs = DB::select(DB::raw("SELECT id, `name` FROM programs WHERE id IN ($ids_string)"));
      if(!empty($programs)){
        foreach ($programs as $program){
          $res[$program->id] = $program;
        }
      }
    }

    return $res;
  }
  public static function getClasses($ids = []){
    $res = [];

    if(!empty($ids)){
      $ids_string = implode(",",$ids);
      $classes = DB::select(DB::raw("SELECT id, `cls_name` FROM classes WHERE id IN ($ids_string)"));
      if(!empty($classes)){
        foreach ($classes as $class){
          $res[$class->id] = $class;
        }
      }
    }

    return $res;
  }

  public static function dumpJson($obj){
      echo json_encode($obj);
      die;
  }

    public static function get($object, $key, $defaultValue = null){
        if(empty($object)) {
            return $defaultValue;
        }
        if(is_object($object)) {
            $keys = explode('.', $key);
            foreach ($keys as $k) {
                if (!isset($object->{$k})) {
                    return $defaultValue;
                }
                $object = $object->{$k};
            }
        }else{
            $keys = explode('.', $key);
            foreach ($keys as $k) {
                if (!isset($object[$k])) {
                    return $defaultValue;
                }
                $object = $object[$k];
            }
        }
        return $object;
    }

    public static function utf8convert($str)
    {
        if (!$str) return '';
        $utf8 = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd' => 'đ|Đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i' => 'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];
        foreach ($utf8 as $ascii => $uni) $str = preg_replace("/($uni)/i", $ascii, $str);
        return $str;
    }
    public static function convertArrayToObject($array, $key = 'id', $push = false)
    {
        if (empty($array)) return [];

        $res = [];
        foreach ($array as $index => $item) {
            $k = is_callable($key) ? call_user_func($key, $item, $index) : (is_object($item)? $item->{$key}: $item[$key]);
            if($push){
                $res[$k][] = $item;
            }else {
                $res[$k] = $item;
            }
        }
        return $res;
    }
    public static function pct($number, $precision = 1)
    {
        $factor = pow(10, $precision);
        return round($number * $factor) / $factor;
    }

    public static function formatCurrency($currency){
        return number_format($currency)." VND";
    }

    public static function validateMobileNumber($str)
    {
        $str = preg_replace('/\s/', '', $str);
        $re = '/^(\(?([0-9]{3,4})\)?|\)?(\+84)\)?[-. ]?([0-9]{3}))[-. ]?([0-9]{3})[-. ]?([0-9]{3})$/m';
//        return preg_match($re, $str) === 1;

        $phoneDigits1 = ['120','121','122','126','128','123','124','125','127','129','199','162','163','164','165','166','167','168','169'];
        $phoneDigits2 = ['99','86','96','97','98','32','33','34','35','36','37','38','39','89','90','93','70','79','77','76','78','88','91','94','83','84','85','81','82','92','56','58'];

        if (substr($str,0,1) == '0'){
            $str = substr($str,1,strlen($str));
        }else if(substr($str,0,2) == '84'){
            $str = substr($str,2,strlen($str));
        }
        else if(substr($str,0,3) == '+84'){
            $str = substr($str,3,strlen($str));
        }

        return 1;
        if (preg_match($re, $str) == 1){
            if (strlen($str) < 9)
                return 0;
            else
            {
                if (strlen($str) == 10){
                    //if (in_array(substr($str,0,3), $phoneDigits1))
                        return 1;
                    //else
                        //return 0;
                }else if(strlen($str) == 9){
                    if (in_array(substr($str,0,2), $phoneDigits2))
                        return 1;
                    else
                        return 0;
                }else
                    return 0;
            }
        }
        else
            return 0;

    }

    public static function convertMobileNumber($str)
    {
        $str = preg_replace('/\s/', '', $str);
        $str = str_replace(["'",".","x",","],'',$str);
        $phoneDigitsConverted = ['162'=>'32','163'=>'33','164'=>'34','165'=>'35','166'=>'36','167'=>'37','168'=>'38','169'=>'39','12'=>'70','121'=>'79','122'=>'77','126'=>'76','128'=>'78','123'=>'83','124'=>'84','125'=>'85','127'=>'81','129'=>'82','199'=>'59'];
        if (substr($str,0,1) == '0'){
            $str = substr($str,1,strlen($str));
        }else if(substr($str,0,2) == '84'){
            $str = substr($str,2,strlen($str));
        }
        else if(substr($str,0,3) == '+84'){
            $str = substr($str,3,strlen($str));
        }

        if (strlen($str) == 10){
            if (isset($phoneDigitsConverted[substr($str,0,3)])){
                $str = $phoneDigitsConverted[substr($str,0,3)].substr($str,3,strlen($str));
            }
        }
        return $str ? '0'.$str : null;
    }

    public static function getDaysWeekInOnceMonth($start_date, $end_date){
        $listDays = [];
        $next = null;
        $sunday = static::getSundaysBetweenTwoDate($start_date,$end_date);
        foreach ($sunday as $i => $s){
            if ($i == 0){
                if ($s == $start_date)
                    $listDays[$i] = ['from'=>$start_date, 'to'=>$start_date];
                else
                    $listDays[$i] = ['from'=>$start_date, 'to'=>$s];
            }
            else{
                $next = date('Y-m-d', strtotime($sunday[$i-1] .' +1 day'));
                $listDays[$i] = ['from'=>$next, 'to'=>$s];

            }
        }

        if (end($sunday) < $end_date){
            array_push($listDays,['from'=>date('Y-m-d', strtotime(end($sunday) .' +1 day')),'to'=>$end_date]);
        }
        return $listDays;
    }

    public static function getSundaysBetweenTwoDate($from, $to)
    {
        $timestamp1 = strtotime($from);
        $timestamp2 = strtotime($to);
        $sundays    = array();
        $oneDay     = 60*60*24;

        for($i = $timestamp1; $i <= $timestamp2; $i += $oneDay) {
            $day = date('N', $i);

            // If sunday
            if($day == 7) {
                // Save sunday in format YYYY-MM-DD, if you need just timestamp
                // save only $i
                $sundays[] = date('Y-m-d', $i);

                // Since we know it is sunday, we can simply skip
                // next 6 days so we get right to next sunday
                $i += 6 * $oneDay;
            }
        }

        return $sundays;
    }

    public static function getTotalDayLearning($student_id,$startDate,$endDate,$allHoliday,$class_day){
        $inDate = date('Y-m-d',strtotime($endDate)+24*60*60*360);
        $days =  self::getDaysBetweenTwoDate($startDate, $inDate, $class_day); # 2x Friday
        $totalSessions = 0;
        $inDateList = ['start' =>$startDate, 'end' =>$endDate];
        $reserveDate = self::getReserveSessionDate($inDateList,$student_id);
        $dayOff = 0;
        if ($reserveDate){
            if ($reserveDate[1] >= $inDate){
                $dayOff = sizeof(self::getDaysBetweenTwoDate($reserveDate[0], $inDate, $class_day));
            }
            else{
                $dayOff =  self::getReserveSession($inDateList,$student_id);
            }
        }

        foreach($days as $day){
            if (!in_array($day,$allHoliday)){
                $totalSessions += 1;
            }
        }

        return ($totalSessions - $dayOff);
    }

    public static function getDaysBetweenTwoDate($from, $to, $day = 5){
        //date_default_timezone_set("Asia/Bangkok");
        $days = [];
        $endDate = strtotime($to);
        for($i = strtotime(self::getDayName($day), strtotime($from)); $i <= $endDate; $i = strtotime('+1 week', $i)){
            $days[] = date('Y-m-d', $i);
        }
        return $days;
    }

    public static function ckPublicHoliday($day) {
        $sql = "SELECT count(id) as total FROM `public_holiday` WHERE start_date <= '{$day}' AND end_date >='{$day}'";
        $total = self::query($sql)[0]->total;
        if ($total == 0)
            return 1;
        else
            return 0;
    }

    public static function ckPublicHolidayCheck($day, $holiday) {

        if (!in_array($day,$holiday))
            return 1;
        else
            return 0;
    }

    public static function getReserveSession($date = null, $studentId  = 0) {
        if (!$date){
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        }
        else{
            $startDate = $date['start'];
            $endDate = $date['end'];
        }

        $sql = "SELECT `session` FROM `reserves` WHERE student_id = $studentId AND end_date <= '$endDate' AND end_date >= '$startDate' AND STATUS = 2";
        $data = self::query($sql);
        if ($data)
            return (int) $data[0]->session;
        else
            return 0;
    }

    public static function getReserveSessionDate($date = null, $studentId  = 0) {
        if (!$date){
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        }
        else{
            $startDate = $date['start'];
            $endDate = $date['end'];
        }

        $sql = "SELECT start_date,end_date FROM `reserves` WHERE student_id = $studentId AND end_date <= '$endDate' AND end_date >= '$startDate' AND STATUS = 2";
        $data = self::query($sql);
        if ($data)
            return [$data[0]->start_date,$data[0]->end_date];
        else
            return [];
    }

    public static function getPublicHolidayAll($branchId = 0) {
        $sql = "SELECT p.start_date, p.end_date
                FROM `public_holiday` p WHERE p.status =1 AND p.`zone_id` 
                IN (SELECT zone_id FROM branches WHERE id = $branchId)";
        $data = self::query($sql);
        $array = [];
        foreach ($data as $date){
            if ($date->start_date < $date->end_date){

                $endDate = date('Y-m-d',strtotime($date->end_date)+60*60*24);
                $period = new \DatePeriod(
                    new \DateTime($date->start_date),
                    new \DateInterval('P1D'),
                    new \DateTime($endDate)
                );

                foreach ($period as $key => $value) {
                    $array[] = $value->format('Y-m-d');

                }
            }
            else{
                $array[] = $date->start_date;
            }
        }
        return $array;
    }

    public static function getContractStatusName($status)
    {

        switch ($status) {
            case 0:
                $name = 'Đã xóa';
                break;
            case 1:
                $name = 'Đã active nhưng chưa đóng phí';
                break;
            case 2:
                $name = 'Đã active và đặt cọc nhưng chưa thu đủ phí hoặc đang chờ nhận chuyển phí';
                break;
            case 3:
                $name = 'Đã active và đã thu đủ phí nhưng chưa được xếp lớp';
                break;
            case 4:
                $name = 'Đang bảo lưu không giữ chỗ hoặc pending';
                break;
            case 5:
                $name = 'Đang được nhận học bổng hoặc VIP';
                break;
            case 6:
                $name = 'Đã được xếp lớp và đang đi học';
                break;
            case 7:
                $name = 'Đã bị withdraw';
                break;
            case 8:
                $name = 'Đã bỏ cọc';
                break;
            default:
                $name = '';
                break;
        }
        return $name;
    }

    public static function getContractTypeName($type)
    {

        switch ($type) {
            case 0:
                $name = 'Học thử';
                break;
            case 1:
                $name = 'Chính thức';
                break;
            case 2:
                $name = 'Tái phí bình thường';
                break;
            case 3:
                $name = 'Tái phí do nhận chuyển phí';
                break;
            case 4:
                $name = 'Chỉ nhận chuyển phí';
                break;
            case 5:
                $name = 'Chuyển trung tâm';
                break;
            case 6:
                $name = 'Chuyển lớp';
                break;
            case 7:
                $name = 'Sinh do tái phí chưa full phí';
                break;
            case 8:
                $name = 'Được nhận học bổng';
                break;
            case 9:
                $name = 'Loại hợp đồng đặc biệt';
                break;
            case 10:
                $name = 'Sinh ra do bảo lưu không giữ chỗ';
                break;
            case 11:
                $name = 'Đã thực hiện quy đổi';
                break;
            case 85:
                $name = 'Học bổng chuyển trung tâm';
                break;
            case 86:
                $name = 'Học bổng chuyển lớp';
                break;
            default:
                $name = '';
                break;
        }
        return $name;
    }

    public static function generateRandomStringOrNumber($length = 10, $number = false) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($number)
            $characters = '1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if ($number && substr($randomString,0,1) == 0){
            $randomString = (int) (substr($randomString,1,strlen($randomString))."0");

        }
        return $randomString;
    }

    public static function getStudentSourceName($key = null){
        $list = [
            1 =>'Google',
            2 =>'FACEBOOK',
            3 =>'Video trực tuyến',
            4 =>'QC ngoài trời',
            5 =>'QC trên taxi',
            6 =>'QC thang máy',
            7 =>'QC trực tuyến',
            8 =>'QC trên đài PT',
            9 =>'Hội thảo/Sự kiện',
            10 =>'Bạn bè giới thiệu',
            11 =>'KOLs/Hot Mom/Dad',
            12 =>'Bài viết trên báo',
            13 =>'Email/Ứng dụng',
            14 =>'B2B-BUSINESS',
            15 =>'B2B-SCHOOL',
            16 =>'B2B-BUSINESS',
            17 =>'EGROUP',
            18 =>'TELE - HO',
            19 =>'TELE - Center',
            20 =>'CTV',
            21 =>'Bán hàng trực tiếp',
            22 =>'Walk in',
            23 =>'Khác'
        ];

        if (!empty($list[$key]))
            return $list[$key];
        else
            return 'Khác';
    }
    public static function convert_number_to_words( $number )
    {
        $hyphen = ' ';
        $conjunction = '  ';
        $separator = ' ';
        $negative = 'âm ';
        $decimal = ' phẩy ';
        $dictionary = array(
            0 => 'Không',
            1 => 'Một',
            2 => 'Hai',
            3 => 'Ba',
            4 => 'Bốn',
            5 => 'Năm',
            6 => 'Sáu',
            7 => 'Bảy',
            8 => 'Tám',
            9 => 'Chín',
            10 => 'Mười',
            11 => 'Mười một',
            12 => 'Mười hai',
            13 => 'Mười ba',
            14 => 'Mười bốn',
            15 => 'Mười năm',
            16 => 'Mười sáu',
            17 => 'Mười bảy',
            18 => 'Mười tám',
            19 => 'Mười chín',
            20 => 'Hai mươi',
            30 => 'Ba mươi',
            40 => 'Bốn mươi',
            50 => 'Năm mươi',
            60 => 'Sáu mươi',
            70 => 'Bảy mươi',
            80 => 'Tám mươi',
            90 => 'Chín mươi',
            100 => 'trăm',
            1000 => 'ngàn',
            1000000 => 'triệu',
            1000000000 => 'tỷ',
            1000000000000 => 'nghìn tỷ',
            1000000000000000 => 'ngàn triệu triệu',
            1000000000000000000 => 'tỷ tỷ'
        );

        if( !is_numeric( $number ) )
        {
            return false;
        }

        if( ($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX )
        {
            // overflow
            trigger_error( 'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING );
            return false;
        }

        if( $number < 0 )
        {
            return $negative . self::convert_number_to_words( abs( $number ) );
        }

        $string = $fraction = null;

        if( strpos( $number, '.' ) !== false )
        {
            list( $number, $fraction ) = explode( '.', $number );
        }

        switch (true)
        {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if( $units )
                {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if( $remainder )
                {
                    $string .= $conjunction . self::convert_number_to_words( $remainder );
                }
                break;
            default:
                $baseUnit = pow( 1000, floor( log( $number, 1000 ) ) );
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::convert_number_to_words( $numBaseUnits ) . ' ' . $dictionary[$baseUnit];
                if( $remainder )
                {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::convert_number_to_words( $remainder );
                }
                break;
        }

        if( null !== $fraction && is_numeric( $fraction ) )
        {
            $string .= $decimal;
            $words = array( );
            foreach( str_split((string) $fraction) as $number )
            {
                $words[] = $dictionary[$number];
            }
            $string .= implode( ' ', $words );
        }

        return $string;
    }
    public static function calculatorSessions($start, $end, $holidays = [], $classdays = [], $onlyTotal = false) {
        $resp = (Object)[
            "date" => [],
            "total" => 0,
            "end_date" => null
        ];
        $startTime = strtotime(date("Y-m-d", strtotime($start)));
        $endTime =   strtotime(date("Y-m-d", strtotime($end)));
        if (!$startTime || !$endTime || !is_array($classdays) || !count($classdays)) {
            return $resp;
        }
        $classdays = self::validClassdays($classdays);
        $classdays = array_values(array_sort($classdays));
        $holidays = self::stringToTimestampHolidays($holidays, $startTime, $endTime);
        if ($startTime > $endTime) {
            return $resp;
        }
        $days = self::getDaysForCalcSession($startTime,$endTime, $classdays, $holidays, $onlyTotal);
        $resp->dates = $days;
        $resp->total = $onlyTotal? $days: count($days);
        $resp->end_date = $onlyTotal ? null: end($days);
        return $resp;
    }
    public static function calculatorSessionsByNumberOfSessions($start, $numberOfSessions, $holidays = [], $classdays = [], $onlyEndDate = false) {
        $startTime = strtotime(date("Y-m-d", strtotime($start)));
        if ($numberOfSessions<=0 || !$startTime || !is_array($classdays) || !count($classdays)) {
            return null;
        }
        $classdays = self::validClassdays($classdays);
        $classdays = array_values(array_sort($classdays));
        $holidays = self::stringToTimestampHolidays($holidays, $startTime, PHP_INT_MAX);
        $sessions = self::getSessionsByNumberOfSessions($startTime,$numberOfSessions, $classdays, $holidays, $onlyEndDate);
        if ($onlyEndDate) {
            return $sessions;
        }
        $resp = new \stdClass();
        $resp->dates = $sessions;
        $resp->total = count($sessions);
        $resp->end_date = end($sessions);
        return $resp;
    }
    public static function getDaysForCalcSession ($startTime, $endTime, $classdays, $holidays, $onlyTotal=false) {
        $weekday = (int) date('N', $startTime);
        if ($weekday === 7) {
            $weekday = 0;
        }
        $timeOfDay = 24 * 60 * 60;
        $maxLength = count($classdays) - 1;
        $days = [];
        $total = 0;
        while ($startTime <= $endTime) {
            foreach ($classdays as $key => $classday) {
                if ($weekday > $classday) {
                    if ($key >= $maxLength) {
                        $startTime += (7 - $weekday) * $timeOfDay;
                        $weekday = 0;
                    }
                    continue;
                }
                $startTime += ($classday - $weekday) * $timeOfDay;
                if ($startTime > $endTime) {
                    return $onlyTotal ? $total: $days;
                }
                if (!self::checkInHolidayByTimestampBinarySearch($startTime, $holidays)) {
                    if ($onlyTotal){
                        ++$total;
                    }else {
                        $days[] = date("Y-m-d", $startTime);
                    }
                }
                $weekday = $classday;
                if ($key >= $maxLength) {
                    $weekday = 0;
                    $startTime += (7 - $classday) * $timeOfDay;
                }
            }
        }
        return $onlyTotal ? $total: $days;
    }
    public static function stringToTimestampHolidays($holidays, $startTime, $endTime) {
        if(!$holidays) return null;
        $res = [];
        foreach ($holidays as $holiday) {
            $hStart = strtotime(date("Y-m-d", strtotime($holiday->start_date)));
            $hEnd = strtotime(date("Y-m-d", strtotime($holiday->end_date)));
            $res[] = [
                'start_date' => $hStart,
                'end_date' => $hEnd,
            ];
        }
        usort($res,function($first,$second){
            return $first['start_date'] > $second['start_date'];
        });
        $res = self::mergeHolidays($res, $startTime, $endTime);
        return $res;
    }
    public static function mergeHolidays($holidays, $pStart, $pEnd) {
        if(!$holidays || count($holidays) <= 1) return $holidays;
        $res = [];
        foreach ($holidays as $holiday) {
            if ($holiday['end_date']>= $pStart ) {
                $res[] = $holiday;
            }
        }
        return $res;
    }
    public static function getSessionsByNumberOfSessions ($startTime, $numberOfSessions, $classdays, $holidays, $onlyEndDate=false){
        $weekday = (int) date('N', $startTime);
        if ($weekday === 7) {
            $weekday = 0;
        }
        $timeOfDay = 24 * 60 * 60;
        $maxLength = count($classdays) - 1;
        $days = [];
        while ($numberOfSessions >= 0) {
            foreach ($classdays as $key => $classday) {
                if ($weekday > $classday) {
                    if ($key >= $maxLength) {
                        $startTime += (7 - $weekday) * $timeOfDay;
                        $weekday = 0;
                    }
                    continue;
                }
                $startTime += ($classday - $weekday) * $timeOfDay;
                if($numberOfSessions<=0){
                    if($onlyEndDate){
                        $l = count($days);
                        return $l> 0 ? $days[$l - 1] : null;
                    }
                    return $days;
                }
                if (!self::checkInHolidayByTimestampBinarySearch($startTime, $holidays)) {
                    $days[] = date("Y-m-d", $startTime);
                    --$numberOfSessions;
                }
                $weekday = $classday;
                if ($key >= $maxLength) {
                    $weekday = 0;
                    $startTime += (7 - $classday) * $timeOfDay;
                }
            }
        }
        if ($onlyEndDate) {
            $l = count($days);
            return $l> 0 ? $days[$l - 1] : null;
        }
        return $days;
    }
    public static function checkInHolidayByTimestampBinarySearch($date, $holidays) {
        if(!$holidays) return false;
        foreach ($holidays as $holiday) {
            if ($date>=$holiday['start_date'] && $date <= $holiday['end_date']) {
                return true;
            }
        }
        return false;
    }
    public static function escapeJsonString($value) {
        # list from www.json.org: (\b backspace, \f formfeed)    
        $escapers =     array("\\",     "/",   "\"",  "\n",  "\r",  "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t",  "\\f",  "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }
    public static function convert_name($str) {
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
		// $str = preg_replace("/(\“|\”|\‘|\’|\,|\!|\&|\;|\@|\#|\%|\~|\`|\=|\_|\'|\]|\[|\}|\{|\)|\(|\+|\^)/", '-', $str);
		// $str = preg_replace("/( )/", '-', $str);
		return $str;
    }
    public static function getDayNameViKhongDau($day)
    {
        switch ($day) {
            case 0:
                $Wday = 'chu nhat';
                break;
            case 1:
                $Wday = 'thu 2';
                break;
            case 2:
                $Wday = 'thu 3';
                break;
            case 3:
                $Wday = 'thu 4';
                break;
            case 4:
                $Wday = 'thu 5';
                break;
            case 5:
                $Wday = 'thu 6';
                break;
            case 6:
                $Wday = 'thu 7';
                break;
            default:
                $Wday = '';
                break;
        }
        return $Wday;
    }
    public static function explodeName($text){
        $data = (object)[
            'firstname'=>"",
            'midname'=>"",
            'lastname'=>"",
        ];
        $arr_text = explode(" ",$text);
        if(count($arr_text)==1){
            $data->lastname = $arr_text[0];
        }elseif(count($arr_text)==2){
            $data->firstname = $arr_text[0];
            $data->lastname = $arr_text[1];
        }elseif(count($arr_text)==3){
            $data->firstname = $arr_text[0];
            $data->lastname = $arr_text[2];
            $data->midname = $arr_text[1];
        }else{
            $data->firstname = $arr_text[0];
            $data->lastname = $arr_text[count($arr_text)-1];
            foreach($arr_text AS $k=> $row){
                if($k!=0 && $k!=count($arr_text)-1){
                    $data->midname.= $data->midname?" ".$row:$row;
                }
            }
        }
        return $data;
    }
}

$constants = [
    'ROLE_SUPER_ADMINISTRATOR',
    'ROLE_ADMINISTRATOR',
    'ROLE_MANAGERS',
    'ROLE_ZONE_CEO',
    'ROLE_REGION_CEO',
    'ROLE_BRANCH_CEO',
    'ROLE_CASHIER',
    'ROLE_EC_LEADER',
    'ROLE_EC',
    'ROLE_OM',
    'ROLE_CS_LEADER_CASHIER',
    'ROLE_CS_CASHIER',
    'ROLE_CM',
    'ROLE_TEACHER',
    'APP_URL',
    'APP_ENV',
    'LMS_STF_ID'
];

if ($constants) {
    foreach ($constants as $const) {
        $val = env($const, '');
        $val = is_numeric($val) ? (int)$val : $val;
        if (!defined($const)) : define($const, $val); endif;
    }
    if (!defined('ENVIRONMENT')) : define('ENVIRONMENT', env('APP_ENV')); endif;
}
