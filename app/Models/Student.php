<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\UtilityServiceProvider as u;
class Student extends Model
{
    protected $table = 'students';
    public $timestamps = false;

    public function pending(){
        return $this->hasMany('App\Models\Pending');
    }

    public function class_transfer(){
        return $this->hasMany('App\Models\ClassTransfer');
    }

    public function contracts(){
        return $this->hasMany('App\Models\Contract');
    }

    public function reserves()
    {
        return $this->hasMany('App\Models\Reserve');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }

    public static function getSqlStudentFullfee($startDate = null, $endDate = null, $cond = [], $whereQr, $wherePP)
    {
      //
      $sql = "
          SELECT count( * ) FROM
          ( SELECT DISTINCT
            st.id AS s_id,
            st.branch_id AS b_id 
            FROM students AS st
            LEFT JOIN contracts AS ct ON ct.student_id = st.id
            LEFT JOIN payment AS p ON p.contract_id = ct.id
            LEFT JOIN pendings AS pd ON pd.contract_id = ct.id 
            WHERE st.type = 0 AND ct.type in (1,2,3,4,5) AND ct.status in (3,4,5,6) and pd.id IS NOT NULL
            AND (p.created_at BETWEEN '%s' AND '%s' ) %s 
              UNION 
            SELECT DISTINCT
            st.id AS s_id,
            st.branch_id AS b_id 
            FROM students AS st
            LEFT JOIN contracts AS ct ON ct.student_id = st.id
            LEFT JOIN payment AS p ON p.contract_id = ct.id
            LEFT OUTER JOIN pendings AS pd ON pd.contract_id = ct.id 
            WHERE st.type = 0 AND ct.type in (1,2,3,4,5) AND ct.status in (3,4,5,6) and pd.id IS NULL 
            AND (p.created_at BETWEEN '%s' AND '%s' ) %s 
              UNION 
            SELECT DISTINCT
            st.id AS s_id,
            st.branch_id AS b_id 
            FROM students AS st 
            INNER JOIN contracts as ct on ct.student_id = st.id 
            WHERE st.type = 1 and (ct.start_date BETWEEN '%s' AND '%s' )  %s 
          ) AS br 
        WHERE %s
      ";

      $resultSql = vsprintf($sql,[
                      $startDate,$endDate,$wherePP,
                      $startDate,$endDate,$wherePP,
                      $startDate,$endDate,$wherePP,
                      $whereQr
      ]);
      return $resultSql;
    }

    public static function getSqlStudentFullfee2($date, $where)
    {
      $q = "
        SELECT
              COUNT(DISTINCT(c.student_id))
       FROM
              contracts c 
              LEFT JOIN enrolments e ON c.enrolment_id = e.id
              LEFT JOIN pendings p ON p.contract_id = c.id
       WHERE
              c.debt_amount = 0 
              AND c.type > 0 
              $where
              AND (( DATE( c.created_at ) <= '$date' AND DATE( c.end_date ) >= '$date' 
                    AND ( c.type <> 5 OR c.status = 6 ) ) 
              OR ( DATE( c.start_date ) <= '$date' AND c.type = 5 ) 
              OR ( DATE( c.created_at ) <= '$date' AND c.status = 6 ) 
          ) 
          AND c.total_charged > 0
              AND c.debt_amount = 0
              AND IF (c.enrolment_id, e.status > 0, true)
          AND IF (p.id, ((p.id IS NOT NULL AND p.status = 1 AND DATE( p.`end_date`) >= '$date') 
            OR
                  (p.id IS NULL OR p.status = 0 OR DATE( p.end_date) < '$date')), true)
      ";
      return $q;
    }

    public static function sqlCountStudentFullfeeActive($startDate, $endDate,$where)
    {
      $sql = "
        SELECT DISTINCT
            st.id AS s_id,
            st.branch_id AS b_id ,
            ct.cm_id as cm_id
            FROM students AS st 
            LEFT JOIN contracts as ct on ct.student_id = st.id 
            WHERE st.type = 1 and (ct.start_date BETWEEN '%s' AND '%s' )  %s 
            GROUP by st.id
        UNION 
        SELECT DISTINCT
            st.id AS s_id,
            st.branch_id AS b_id,
            ct.cm_id as cm_id
            FROM students AS st
            LEFT JOIN contracts AS ct ON ct.student_id = st.id
            LEFT JOIN payment AS p ON p.contract_id = ct.id
            LEFT JOIN pendings AS pd ON pd.contract_id = ct.id 
            WHERE st.type = 0 AND ct.type = 1 AND ct.status = 5 AND pd.id IS NULL
            AND (p.created_at BETWEEN '%s' AND '%s' ) %s 
            GROUP by st.id
      ";
      $resultSql = vsprintf($sql, [
        $startDate, $endDate, $where,
        $startDate, $endDate, $where
      ]);
      return $resultSql;
    }

  public static function sqlCountStudentFullfeePending($date,$where)
  {
    $sql = "
        SELECT
          count(DISTINCT p.contract_id)
        FROM
          pendings AS p
          LEFT JOIN students AS s ON p.student_id = s.id
          LEFT JOIN contracts AS c ON p.contract_id = c.id
        WHERE
          p.STATUS = 1 
          AND p.end_date >= '$date' 
          $where
      ";

    return $sql;
  }

  public static function sqlStudentVip($startDate, $endDate,$where)
  {
    $sql = "
            SELECT DISTINCT
            st.id AS s_id,
            st.branch_id AS b_id 
            FROM students AS st 
            INNER JOIN contracts as ct on ct.student_id = st.id 
            WHERE st.type = 1 and (ct.start_date BETWEEN '%s' AND '%s' )  %s 
          ";

    $resultSql = vsprintf($sql, [
      $startDate, $endDate, $where
    ]);

    return $resultSql;
  }

  public static function sqlStudentRenew($startDate, $fromDate, $where)
  {
     $sql = "
        SELECT GROUP_CONCAT( CONCAT( COALESCE ( z.total, 0 ), ',', COALESCE ( k.total, 0 ) ) ) AS renew 
        FROM branches AS b2
	      LEFT JOIN 
	      ( SELECT branch_id, COUNT( * ) AS total 
	        FROM (
		        SELECT c.branch_id, COUNT( c.id ) 
		        FROM contracts AS c
			      LEFT JOIN enrolments AS e ON c.enrolment_id = e.id 
		        WHERE
			      c.type > 0 
			      AND ( e.last_date BETWEEN '$startDate' AND '$fromDate' ) 
			      AND c.debt_amount = 0  AND c.status > 2 
		        GROUP BY c.student_id  ) AS x 
	      GROUP BY x.branch_id 
	      ) AS z ON z.branch_id = b2.id
	      LEFT JOIN ( SELECT branch_id, COUNT( * ) AS total 
	      FROM
		    ( SELECT c.branch_id, COUNT( c.id ) 
		      FROM contracts AS c 
			    LEFT JOIN enrolments AS e ON c.enrolment_id = e.id 
		      WHERE c.type > 0  AND ( e.last_date BETWEEN '2018-05-01' AND '2018-05-31' ) 
			    AND c.count_recharge > 0  AND c.STATUS > 2 
		      GROUP BY c.student_id 
		    ) AS x 
	      GROUP BY x.branch_id 
	      ) AS k ON k.branch_id = b2.id 
        WHERE b2.status = 1 $where
     ";

     return $sql;
  }


  public static function sqlCountStudentPending($date, $where)
  {
      $sql = "
        SELECT
            count(DISTINCT p.contract_id)
        FROM
            pendings AS p
        LEFT JOIN students AS s ON p.student_id = s.id
        LEFT JOIN contracts AS c ON p.contract_id = c.id
        LEFT JOIN branches AS b2 ON p.branch_id = b2.id
        LEFT JOIN products AS prd ON p.product_id = prd.id
        LEFT JOIN programs AS prg ON p.program_id = prg.id
        LEFT JOIN tuition_fee AS tf ON c.tuition_fee_id = tf.id
        WHERE p.end_date >= '$date' 
        $where
      ";
      return $sql;
  }

  public static function sqlCountRenewStudent($fromDate, $toDate, $where = '', $renewSuccess = false )
  {
      $whereSuccess = '';
      if( $renewSuccess ) {
        $whereSuccess = " AND e.final_last_date > '$toDate' ";
      }
      $q = "
        SELECT
         count(DISTINCT e.student_id) 
        FROM
          enrolments AS e
          LEFT JOIN contracts AS c ON e.contract_id = c.id
          LEFT JOIN students AS s ON c.student_id = s.id
          LEFT JOIN tuition_transfer AS tff ON tff.from_contract_id = c.id 
        WHERE
          e.id IN (
          SELECT
            MAX( e.id ) 
          FROM
            enrolments AS e
            LEFT JOIN contracts AS c ON e.contract_id = c.id
            LEFT JOIN students AS s ON c.student_id = s.id 
          WHERE
            s.branch_id = c.branch_id 
          GROUP BY
            s.id 
          ) 
          AND e.last_date >= '$fromDate' 
          AND e.last_date <= '$toDate' 
          AND tff.id IS NULL 
          $where
          $whereSuccess
      ";
      return $q;
  }
  
  public function getStudentInfo($filter){
    $auth = $filter->users_data;
    $user_id = $auth->id;
    $role_id = $auth->role_id;
    $pagination = json_decode($filter->pagination);
    $sort = json_decode($filter->sort);
    $search = json_decode($filter->search);
    $order = '';
    $limit = '';
    $where = '';
    $select = '';

    if ($sort->by && $sort->to) {
      $order .= " ORDER BY $sort->by $sort->to";
    }
    if ($pagination->cpage && $pagination->limit) {
      $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
      $limit .= " LIMIT $offset, $pagination->limit";
    }

    $branches = $search->branch ? (int)$search->branch : $auth->branches_ids;
    $where = " AND s.branch_id IN ($branches)";
    if($role_id){
      if ($role_id == ROLE_EC_LEADER) {
        $where .= " AND ( t.ec_id = $user_id OR t.ec_id IN (SELECT u1.id FROM users u1 LEFT JOIN users u2 ON u1.superior_id = u2.hrm_id WHERE u2.id = $user_id))";
      }
      if ($role_id == ROLE_EC) {
        $where .= " AND t.ec_id = $user_id";
      }
    }
    if ($search->gender && $search->gender != '') {
      $where .= " AND s.gender = '$search->gender'";
    }
    if (isset($search->type) && $search->type != '') {
      $where .= " AND s.type = $search->type";
    }
    if (isset($search->ec) && $search->ec != '') {
      $where .= " AND t.ec_id = '$search->ec'";
    }
    // if (date('H')<27) {
    //   $where .= " AND t.ec_id = 0";
    // }
    if($user_id==415){
      $where .= " AND s.source IN(31,27)";
    }
    if($role_id==1200){
      $where .= " AND s.source IN(26)";
    }
    if($role_id==1300){
      $where .= " AND s.source IN(32)";
    }
    $keyword = isset($search->keyword) ? trim($search->keyword) : '';
    if (isset($keyword) && $keyword != '') {
        if ($search->field == '0' || $search->field == '') {
          $where .= " AND
              ( s.name LIKE '%$keyword%'
              OR s.crm_id LIKE '%$keyword%'
              OR s.email LIKE '%$keyword%'
              OR s.gud_email1 LIKE '%$keyword%'
              OR s.accounting_id LIKE '%$keyword%'
              OR s.gud_name1 LIKE '%$keyword%' OR s.gud_name2 LIKE '%$keyword%'
              OR s.phone LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.gud_mobile2 LIKE '%$keyword%'
              OR s.address LIKE '%$keyword%'
              OR s.phone LIKE '$keyword%'
              OR s.school LIKE '%$keyword%')
          ";
        }
        if ($search->field == '1') {
            $where .= " AND (s.name LIKE '%$keyword%') ";
        }
        if ($search->field == '2') {
            $where .= " AND (s.crm_id LIKE '%$keyword%') ";
        }
        if ($search->field == '3') {
            $where .= " AND (s.accounting_id LIKE '%$keyword%') ";
        }
        if ($search->field == '4') {
            $where .= " AND (s.phone LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.gud_mobile2 LIKE '%$keyword%') ";
        }
        if ($search->field == '5') {
            $where .= " AND (s.gud_name1 LIKE '%$keyword%' OR s.gud_name2 LIKE '%$keyword%') ";
        }
        if ($search->field == '6') {
            $where .= " AND (s.address LIKE '%$keyword%') ";
        }
        if ($search->field == '7') {
            $where .= " AND (s.school LIKE '%$keyword%' ) ";
        }
    }
    if ($role_id == 80 || $role_id == 81)
      $where .= " AND s.creator_id = $user_id";

    if(isset($search->status) && $search->status==4){
      $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND type>0)>0 AND 
        (
          (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7)=0
          OR (
                (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7 AND debt_amount>0 AND total_charged=0)>0
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`=7)>0
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7 AND (debt_amount=0 OR total_charged>0))=0
            )
        )
      )";
    }elseif(isset($search->status) && $search->status==3){
      $where.=" AND ( (SELECT count(id) FROM reserves 
        WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)>0 
        AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7)>0)";
    }elseif(isset($search->status) && $search->status=='2.4'){
      $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND  class_id IS NOT NULL AND `type`= 10)>0 
        AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 )";
    }elseif(isset($search->status) && $search->status=='2.3'){
      $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND  class_id IS NOT NULL AND `type` IN(5,85))>0 
        AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 )";
    }elseif(isset($search->status) && $search->status=='2.2'){
      $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND  class_id IS NOT NULL AND `type` IN(6,86))>0 
        AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 )";
    }elseif(isset($search->status) && $search->status=='2.1'){
      $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND  enrolment_start_date <= CURRENT_DATE AND class_id IS NOT NULL AND `type` NOT IN(5,6,86,85,10))>0 
        AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 )";
    }elseif(isset($search->status) && $search->status=='2.5'){
      $where.=" AND ( (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND enrolment_start_date > CURRENT_DATE AND  class_id IS NOT NULL AND `type` NOT IN(5,6,86,85,10))>0 
        AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  `start_date` <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 )";
    }elseif(isset($search->status) && $search->status=='1.4'){
      $where.=" AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  start_date <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 
        AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NOT NULL)=0
        AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NULL AND `type` IN (10))>0";
    }elseif(isset($search->status) && $search->status=='1.3'){
      $where.=" AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  start_date <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 
        AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NOT NULL)=0
        AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NULL AND `type` IN (5,85))>0";
    }elseif(isset($search->status) && $search->status=='1.2'){
      $where.=" AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  start_date <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 
        AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NOT NULL)=0
        AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NULL AND debt_amount=0 )>0";
    }elseif(isset($search->status) && $search->status=='1.1'){
      $where.=" AND (SELECT count(id) FROM reserves WHERE student_id=s.id AND `status`=2 AND  start_date <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 
        AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NOT NULL)=0
        AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND class_id IS NULL AND `type` NOT IN (5,85) AND debt_amount>0 AND total_charged>0)>0";
    }elseif(isset($search->status) && $search->status=='5'){
      $where.=" AND (SELECT count(id) FROM contracts WHERE student_id=s.id)=0";
    }elseif(isset($search->status) && $search->status=='6'){
      $where.=" AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND `type`>0 AND `debt_amount`>0 AND (total_charged=0 OR total_charged IS NULL))>0
      AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `status`!=7 AND `type`>0 AND (total_charged>0 OR debt_amount=0))=0";
    }
    $select = "SELECT
              DISTINCT(s.id), s.accounting_id, s.cms_id,s.crm_id, s.source, s.status,
              (SELECT so.name FROM sources so WHERE so.`id`  = s.source) AS source_name,
              s.type_product,
              s.type student_type,
              s.name student_name,
              s.email student_email,
              s.phone student_phone,
              s.gender student_gender,
              s.address student_address,
              s.date_of_birth student_birthday,
              COALESCE(s.phone, s.gud_name1) phone,
              COALESCE(s.gud_name1, s.gud_name2) parent_name,
              COALESCE(s.avatar, 'noavatar.png') student_avatar,
              COALESCE(s.gud_email1, s.gud_email2) parent_email,
              COALESCE(s.gud_mobile1, s.gud_mobile2) parent_mobile,
              (SELECT name FROM branches WHERE id=s.branch_id) AS branch_name,
              s.school student_school, s.attached_file student_profile, s.school_grade student_school_grade,
              (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE id=t.ec_id) ec_name,
              (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE id=t.cm_id) cm_name,
              (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE id=s.creator_id) creator_name,
              (SELECT COUNT(id) FROM `customer_care` WHERE crm_id = s.crm_id) AS total_care,
              (SELECT cl.cls_name FROM classes cl LEFT JOIN contracts c ON c.class_id = cl.id WHERE c.student_id = s.id ORDER BY c.id DESC LIMIT 0, 1) class_name,
              (SELECT e.status FROM enrolments e WHERE e.student_id = s.id ORDER BY e.id DESC LIMIT 0, 1) class_status,
              (SELECT p.charge_date FROM payment AS p LEFT JOIN contracts AS ct ON ct.id=p.contract_id WHERE ct.student_id=s.id ORDER BY p.charge_date DESC LIMIT 1) AS last_charge_date,
              (SELECT `name` FROM students WHERE id=s.sibling_id ) AS sibling_name";
    $query = " FROM students s
        LEFT JOIN term_student_user t ON t.student_id = s.id
      WHERE s.id > 0";
    $final_query = "$select $query AND s.status >0 $where $order $limit";

    $total = "SELECT COUNT(DISTINCT(s.id)) total";
    $data = [];
    $i_total = 0;
    try{
      $data = u::query($final_query);
      $o_total = u::first("$total $query AND s.status >0 $where");
      $i_total = $o_total->total;
    }catch(Exception $ex){ throw $ex;}
    if ($data) {
      $data_map = array_map(function($arr){
        $arr->student_avatar = file_exists(AVATAR.DS.str_replace('/', DS, AVATAR_LINK.$arr->student_avatar)) ? AVATAR_LINK.$arr->student_avatar : AVATAR_LINK.'noavatar.png';
        $student_status = self::getStatusStudent($arr->id);
        $arr->status_id = $student_status->status;
        $arr->status_name = $student_status->status_name;
        $arr->parent_mobile = str_replace(substr($arr->parent_mobile,4,3),'***',$arr->parent_mobile);
      }, $data);
    }
    $result = (Object)['data' => $data, 'total' => $i_total];
    return $result;
  }

    /**
     * Kiểm tra xem một học sinh có phải là học sinh tiềm năng hay không
     * (học sinh tiềm năng là học sinh không có gói phí nào hoặc có gói phí đã được sử dụng hết)
     * @param $studentId
     * @return bool
     */
    public function isPotentialStudent($studentId)
    {
        $query = "SELECT c.student_id FROM contracts AS c WHERE c.student_id = $studentId && c.status > 0 AND c.status < 7 AND c.`type` >= 0 AND c.real_sessions > 0 GROUP BY c.student_id
                UNION 
                SELECT student_id FROM class_transfer WHERE status < 2 AND student_id = $studentId GROUP BY student_id
                UNION 
                SELECT to_student_id as student_id FROM tuition_transfer WHERE status = 0 AND to_student_id = $studentId GROUP BY to_student_id
      ";
        $r = u::query($query);
        return empty($r);
    }
    public static function getStatusStudent($student_id){
      $tmp_status = (object)array(
        'status'=>0,
        'status_name'=>''
      );
      $check_potential = u::first("SELECT count(id) AS total FROM students AS s 
      WHERE s.id=$student_id AND (SELECT count(id) FROM contracts WHERE student_id=s.id)=0 ");
      if($check_potential->total>0){
        $tmp_status->status = 5;
        // $tmp_status->status_name = 'Potential';
        $tmp_status->status_name = 'Học sinh tiềm năng';
      }else{
        $check_withdraw = u::first("SELECT count(id) AS total FROM students AS s 
          WHERE s.id=$student_id AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND type>0)>0 AND 
            ((SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7)=0
              OR (
                (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7 AND debt_amount>0 AND total_charged=0)>0
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`=7)>0
                AND (SELECT count(id) FROM contracts WHERE student_id=s.id AND `type`>0 AND `status`!=7 AND (debt_amount=0 OR total_charged>0))=0
            ) )");
        if($check_withdraw->total>0){
          $tmp_status->status = 4;
          // $tmp_status->status_name = 'Withdraw';
          $tmp_status->status_name = 'Học sinh ngừng học';
        }else{
          $check_baoluu = u::first("SELECT count(id) AS total FROM reserves 
            WHERE student_id=$student_id AND `status`=2 AND  start_date <= CURRENT_DATE AND end_date >=CURRENT_DATE");
          if($check_baoluu->total>0){
            $tmp_status->status = 3;
            // $tmp_status->status_name = 'Reserves';
            $tmp_status->status_name = 'Học sinh đang bảo lưu';
          }else{
            $check_active = u::first("SELECT c.type,c.enrolment_start_date FROM contracts AS c 
              WHERE c.student_id=$student_id AND c.status!=7 AND c.class_id IS NOT NULL 
                AND (SELECT count(id) AS total FROM reserves WHERE student_id=c.student_id AND `status`=2 AND  start_date <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 ORDER BY c.count_recharge");
            if(!empty($check_active)){
              if(in_array($check_active->type,array(10))){
                $tmp_status->status = '2.4';
                // $tmp_status->status_name = 'Reserves Active';
                $tmp_status->status_name = 'Học sinh bảo lưu quay lại học';
              }elseif(in_array($check_active->type,array(5,85))){
                $tmp_status->status = '2.3';
                // $tmp_status->status_name = 'Branch Tranfer Active';
                $tmp_status->status_name = 'Học sinh chuyển trung tâm đã xếp lớp';
              }elseif(in_array($check_active->type,array(6,86))){
                $tmp_status->status = '2.2';
                // $tmp_status->status_name = 'Class Transfer Active';
                $tmp_status->status_name = 'Học sinh chuyển lớp';
              }else{
                if($check_active->enrolment_start_date > date('Y-m-d')){
                  $tmp_status->status = '2.5';
                  // $tmp_status->status_name = 'Active pending';
                  $tmp_status->status_name = 'Học sinh được xếp lớp trước ngày học';
                }else{
                  $tmp_status->status = '2.1';
                  // $tmp_status->status_name = 'Active';
                  $tmp_status->status_name = 'Học sinh đang học';
                }
              }
            }else{
              $check_pending =  u::first("SELECT c.type ,c.debt_amount,c.total_charged FROM contracts AS c 
              WHERE c.student_id=$student_id AND c.status!=7 AND c.class_id IS NULL 
                AND (SELECT count(id) AS total FROM reserves WHERE student_id=c.student_id AND `status`=2 AND  start_date <= CURRENT_DATE AND end_date >=CURRENT_DATE)=0 
                AND (SELECT count(id) FROM contracts WHERE student_id=c.student_id AND `status`!=7 AND class_id IS NOT NULL)=0
                ORDER BY c.count_recharge");
              if(!empty($check_pending)){
                if(in_array($check_pending->type,array(10))){
                  $tmp_status->status = '1.4';
                  // $tmp_status->status_name = 'Pending Reserves';
                  $tmp_status->status_name = 'Học sinh hết hạn bảo lưu chưa được xếp lớp';
                }elseif(in_array($check_pending->type,array(5,85))){
                  $tmp_status->status = '1.3';
                  // $tmp_status->status_name = 'Pending Branch Tranfer';
                  $tmp_status->status_name = 'Học sinh chuyển trung tâm chưa được xếp lớp';
                }elseif($check_pending->debt_amount==0 ){
                  $tmp_status->status = '1.2';
                  // $tmp_status->status_name = 'Pending Full';
                  $tmp_status->status_name = 'Học sinh đủ phí chưa xếp lớp';
                }elseif($check_pending->debt_amount>0 && $check_pending->total_charged>0){
                  $tmp_status->status = '1.1';
                  // $tmp_status->status_name = 'Pending Deposit';
                  $tmp_status->status_name = 'Học sinh chưa xếp lớp do cọc';
                }else{
                  $check_phat_sinh = u::first("SELECT count(c.id) As total FROM contracts AS c WHERE c.student_id=$student_id AND c.status!=7 AND c.type>0 AND c.debt_amount>0 AND (c.total_charged=0 OR c.total_charged IS NULL)");
                  if($check_phat_sinh->total){
                    $tmp_status->status = '6';
                    // $tmp_status->status_name = 'Pending Deposit';
                    $tmp_status->status_name = 'Học sinh phát sinh gói phí nhưng chưa có tiền';
                  }
                }
              }
            }
          }
        }
      }
      
      return $tmp_status;
    }
}
