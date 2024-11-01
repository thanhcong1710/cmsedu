<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 6/18/2018
 * Time: 4:48 PM
 */

namespace App\Models;

use App\Http\Controllers\ChargesController;
use App\Providers\CurlServiceProvider as curl;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

/**
 * CMS STAGING
 * Tài khoản Cyber gọi đến CMS: Cyber/@12345678
 * CYBER_SERVER_ADDRESS="http://cybersoft.com.vn:4949/api/"
 * CYBER_SERVER_AUTHORIZATION="bearer ILq5pov_qunWq1S6IZYxs_KzOBd0ZA2Ehvz9283s8I8ZdgDe9HWoNmJj_qVRp8n7_whGDRBQnyHx9R8jwOH8o-mrLUIwJBIrPuJXeoAtljz21A9s_urixzIrS5bP6bPI9WlAho56oVUKq78LR6NhlqrNUwOl03dZCiP0lGWxknE5S0sSrcsKMDulQnV3vs_2xS0hwrR4TyOfhNBHV9p9snHN1uIMEsCQhaiTsLxSKpeOOUxMFtaDavImRzDGdw1ZokGiURQSLnoa84imK4FZJAZgaBPpsZcZKHtWMWgt-8UBH8HSqF0hKNDfVhXypesqO_Xizio6JmT1VQMMNqtwCBiishegqveT4uij-5qP7xiNmswST5Fmic9ynLiffT3ZuOx-hg-pcLuw_ep5Hf2ylTMpblovCnuE9iDFELEVKLeZ4h4wl_oKlSVBE6T1OEbJO34tFkBMQwr2b2hx774NLacFLiIT6brjKhIjRldhBsxAjdvqh15ku7h83PHcdqit"
 * ----------------------------------------------------------------------------------------------
 * CMS PRODUCT;
 * Tài khoản Cyber gọi đến CMS: Cyber/sYQ4mBs28JVFedCVER5E5jfmWgswPHHf
 * CYBER_SERVER_ADDRESS="http://cybersoft.com.vn:4949/api/";
 * CYBER_SERVER_AUTHORIZATION="bearer ILq5pov_qunWq1S6IZYxs_KzOBd0ZA2Ehvz9283s8I8ZdgDe9HWoNmJj_qVRp8n7_whGDRBQnyHx9R8jwOH8o-mrLUIwJBIrPuJXeoAtljz21A9s_urixzIrS5bP6bPI9WlAho56oVUKq78LR6NhlqrNUwOl03dZCiP0lGWxknE5S0sSrcsKMDulQnV3vs_2xS0hwrR4TyOfhNBHV9p9snHN1uIMEsCQhaiTsLxSKpeOOUxMFtaDavImRzDGdw1ZokGiURQSLnoa84imK4FZJAZgaBPpsZcZKHtWMWgt-8UBH8HSqF0hKNDfVhXypesqO_Xizio6JmT1VQMMNqtwCBiishegqveT4uij-5qP7xiNmswST5Fmic9ynLiffT3ZuOx-hg-pcLuw_ep5Hf2ylTMpblovCnuE9iDFELEVKLeZ4h4wl_oKlSVBE6T1OEbJO34tFkBMQwr2b2hx774NLacFLiIT6brjKhIjRldhBsxAjdvqh15ku7h83PHcdqit";
 * ----------------------------------------------------------------------------------------------
 * Class CyberAPI
 * @package App\Models
 */
class CyberAPI extends Model
{
  private $url;
  private $key;

  public function __construct(array $attributes = [])
  {
      parent::__construct($attributes);
      $this->url = getenv('CYBER_SERVER_ADDRESS');
      $this->key = getenv("CYBER_SERVER_AUTHORIZATION");
  }

  private function callAPI(Request $request)
  {
//    if(APP_ENV !== 'product'){
//       return null;
//    }
    $log = new LogCyberRequest();
    $url = $request->api_url;
    $author = $request->api_author;
    $method = $request->api_method;
    $params = $request->api_params ? $request->api_params : [];
    $act = $request->act;
    if (isset($params) && isset($params['Ma_TTLN']) && $params['Ma_TTLN'] === 'TEST'){
        return null;
    }
    $header = [
      "Content-Type: application/json",
      "Authorization: $this->key"
    ];
    $res = null;
    // Log::info('CYBER_API:'.$url);
    // Log::info('CYBER_PARAMS:'.json_encode($params));
    try {
      $res = curl::curl($url, $method, $header, $params);
      if ($res) {
        $log->logCallingAPI($url, json_encode($request->api_params), json_encode($header), $method, date('Y-m-d H:i:s'), $res, $log->SUCCESS_STATUS, $author, $act);
      } else {
        $log->logCallingAPI($url, json_encode($request->api_params), json_encode($header), $method, date('Y-m-d H:i:s'), null, $log->FAILURE_STATUS, $author, $act);
      }
    } catch (\Exception $exception) {
      $log->logCallingAPI($url, json_encode($request->api_params), json_encode($header), $method, date('Y-m-d H:i:s'), null, $log->FAILURE_STATUS, $author, $act);
    }

    return $res;
  }

  public function createBranch($branch, $user_id)
  {
    $request = new Request();

    $request->api_method = 'POST';
    $request->api_url = $this->url . 'DanhmucTrungTam';
    $accounting_id = "CMS-TT-$branch->id";
    $request->api_params = [
      "Ma_TTLN" => "$branch->accounting_id",
      "Ten_TTLN" => "$branch->name",
      "Ten_TTLN2" => "$branch->name",
      "User_Name" => ""
    ];
    $request->api_author = $user_id;
    $request->act = "Thêm mới trung tâm";

    $data = $this->callAPI($request);

    $res = false;
    if ($data) {
      $info = json_decode($data);
      $res = (isset($info->Data) && ($info->Data->Table[0]->Status === 'Y')) ? $accounting_id : false;
    }

    return $res;
  }

  public function updateBranch($branch, $user_id)
  {
    $request = new Request();

    $request->api_method = 'PUT';
    $request->api_url = $this->url . "DanhmucTrungTam/$branch->accounting_id";
    $request->api_params = [
      "Ma_TTLN" => "$branch->accounting_id",
      "Ten_TTLN" => "$branch->name",
      "Ten_TTLN2" => "$branch->name",
      "User_Name" => ""
    ];
    $request->api_author = $user_id;
    $request->act = "Cập nhật trung tâm";

    $this->callAPI($request);
  }

  public function createStudent($student, $user_id)
  {
    if (isset($student) && isset($student->branch_id) && $student->branch_id === 1000){
      return null;
    }
    $request = new Request();
    $accounting_id = $student->accounting_id ? $student->accounting_id : $this->genStudentCode($student->id);
    $province = Province::where('id' , '=', $student->province_id)->first();
    $provinceId = isset($province->accounting_id) ? $province->accounting_id: '';
    $district = District::where('id' , '=', $student->district_id)->first();
    $districtId = isset($district->accounting_id) ? $district->accounting_id: '';

    $request->api_method = 'POST';
    $request->api_url = $this->url . 'HocVien';
    $request->api_params = [
      "Ma_kh" => $accounting_id,
      "Ten_kh" => "$student->name",
      "Dia_chi" => "$student->address",
      "Ngay_sinh" => "$student->date_of_birth",
      // "Dien_thoai" => "$student->phone",
      "Dien_thoai" => "",
      "GT" => $student->gender === 'M' ? 1 : 0,
      "suc_khoe" => "",
      "Kha_nang" => "",
      "ten_me" => "$student->gud_name1",
      "DT_me" => "$student->gud_mobile1",
      "email_me" => "$student->gud_email1",
      "ten_bo" => "$student->gud_name2",
      "DT_bo" => "$student->gud_mobile2",
      "email_bo" => "$student->gud_email2",
      "Ma_Tp" => "$provinceId",
      "Ma_quan" => "$districtId",
      "Ma_xa" => "0",
      "Ghi_chu" => "$student->note",
      "User_Name" => ""
    ];

    $request->api_author = $user_id;
    $request->act = "Thêm mới học sinh";
    $data = $this->callAPI($request);

    $res = false;
    if ($data) {
      $info = json_decode($data);
      $res = (isset($info->Data) && ($info->Data->Table[0]->Status === 'Y')) ? $accounting_id : false;
    }
    // $res = $accounting_id;

    return $res;
  }

  public function updateStudent($student, $user_id)
  {
    if (isset($student) && isset($student->branch_id) && $student->branch_id === 1000){
          return null;
    }
    $request = new Request();

    $request->api_method = 'PUT';
    $request->api_url = $this->url . "HocVien/$student->accounting_id";
    $request->api_params = [
      "Ma_kh" => "$student->accounting_id",
      "Ten_kh" => "$student->name",
      "Dia_chi" => "$student->address",
      "Ngay_sinh" => "$student->date_of_birth",
      // "Dien_thoai" => "$student->phone",
      "Dien_thoai" => "",
      "GT" => $student->gender === 'M' ? 1 : 0,
      "suc_khoe" => "",
      "Kha_nang" => "",
      "ten_me" => "$student->gud_name1",
      // "DT_me" => "$student->gud_mobile1",
      "DT_me" => "",
      "email_me" => "$student->gud_email1",
      "ten_bo" => "$student->gud_name2",
      // "DT_bo" => "$student->gud_mobile2",
      "DT_bo" => "",
      "email_bo" => "$student->gud_email2",
      "Ma_Tp" => "$student->province_id",
      "Ma_quan" => "$student->district_id",
      "Ma_xa" => "0",
      "Ghi_chu" => "$student->note",
      "User_Name" => ""
    ];

    $request->api_author = $user_id;
    $request->act = "Cập nhật thông tin học sinh";
    $this->callAPI($request);
  }

  public function createProgram($program, $user_id)
  {
    $request = new Request();

    $request->api_method = 'POST';
    $request->api_params = [
      "M_Cp_Name" => "Cp_WUpdateDmNhVvFromCRM",
      "M_StrPara" => ""
    ];

    $parts = [$this->key];

    foreach ($program as $property) {
      $parts[] = $property;
    }

    $params = implode("#", $parts);

    $request->api_author = $user_id;
    $request->act = "Thêm mới chương trình";
    $request->api_params["M_StrPara"] = $params;
    $data = $this->callAPI($request);

    $res = false;
    if ($data) {
      $info = json_decode($data);
      $res = $info;
    }

    return $res;
  }

  public function createTuitionFee($tuition_fee, $user_id)
  {
    $request = new Request();

    $accounting_id = "CMS-GP-$tuition_fee->id";
    $request->api_method = 'POST';
    $request->api_url = $this->url . "GoiHoc";
    $request->api_params = [
      "Ma_vv" => $accounting_id,
      "Ten_vv" => "$tuition_fee->name",
      "Ten_vv2" => "$tuition_fee->name",
      "nh_vv1" => "",
      "Tg_hoc" => round(($tuition_fee->session / 4), 1),
      "SL_Buoi" => $tuition_fee->session,
      "User_Name" => ""
    ];

    $request->api_author = $user_id;
    $request->act = "Thêm mới gói phí";
    $data = $this->callAPI($request);

    $res = false;
    if ($data) {
      $info = json_decode($data);
      $res = (isset($info->Data) && ($info->Data->Table[0]->Status === 'Y')) ? $accounting_id : false;
    }

    return $res;

  }

  public function updateTuitionFee($tuition_fee, $user_id)
  {
    $request = new Request();

    $request->api_method = 'PUT';
    $request->api_url = $this->url . "GoiHoc/$tuition_fee->accounting_id";
    $request->api_params = [
      "Ma_vv" => "$tuition_fee->accounting_id",
      "Ten_vv" => "$tuition_fee->name",
      "Ten_vv2" => "$tuition_fee->name",
      "nh_vv1" => "",
      "Tg_hoc" => round(($tuition_fee->session / 4), 1),
      "SL_Buoi" => $tuition_fee->session,
      "User_Name" => ""
    ];

    $request->api_author = $user_id;
    $request->act = "Cập nhật thông tin gói phí";
    $this->callAPI($request);
  }

  public function createSale($sale, $user_id)
  {
    $request = new Request();

    $accounting_id = "$sale->hrm_id";
    $request->api_method = 'POST';
    $request->api_url = $this->url . "NhanVien";
    $request->api_params = [
      "Ma_Hs" => "$accounting_id",
      "Ten_hs" => "$sale->full_name",
      "Dia_chi" => "",
      "Ngay_sinh" => "1900-01-01",
      "Dien_thoai" => "$sale->phone",
      "Gioi_tinh" => 1,
      "CMND" => "",
      "Ngay_CMND" => "1900-01-01",
      "Noi_cap" => "",
      "So_BaoHiem" => "",
      "Ma_TTLN" => "$sale->branch_accounting_id",
      "Dan_toc" => "",
      "Quoc_tich" => "",
      "Ton_giao" => "",
      "User_Name" => ""
    ];

    $request->api_author = $user_id;
    $request->act = "Thêm mới nhân viên";
    $data = $this->callAPI($request);

    $res = false;
    if ($data) {
      $info = json_decode($data);
      $res = (isset($info->Data) && ($info->Data->Table[0]->Status === 'Y')) ? $accounting_id : false;
    }
    // $res = $accounting_id;

    return $res;
  }

  public function updateSale($sale, $user_id)
  {
    $request = new Request();
    $request->api_method = 'PUT';
    $request->api_url = $this->url . "NhanVien/$sale->accounting_id";
    $request->api_params = [
      "Ma_Hs" => "$sale->accounting_id",
      "Ten_hs" => "$sale->full_name",
      "Dia_chi" => "",
      "Ngay_sinh" => "1900-01-01",
      "Dien_thoai" => "$sale->phone",
      "Gioi_tinh" => 1,
      "CMND" => "",
      "Ngay_CMND" => "1900-01-01",
      "Noi_cap" => "",
      "So_BaoHiem" => "",
      "Ma_TTLN" => "$sale->branch_accounting_id",
      "Dan_toc" => "",
      "Quoc_tich" => "",
      "Ton_giao" => "",
      "User_Name" => ""
    ];

    $request->api_author = $user_id;
    $request->act = "Cập nhật thông tin nhân viên";
    $this->callAPI($request);
  }

  public function createTuitionFeeExpire($tuition_fee, $user_id)
  {
    $request = new Request();

    $request->api_method = 'POST';
    $request->api_url = $this->url . "Danhmuchieulucgoi";
    $request->api_params = [
      "Ma_vv" => "$tuition_fee->accounting_id",
      "Gt1" => $tuition_fee->price,
      "Gt2" => round(($tuition_fee->discount * 100 / $tuition_fee->price), 1),
      "Ngay_hl" => "$tuition_fee->available_date",
      "Ngay_hl1" => "$tuition_fee->expired_date",
      "Ma_thue" => "",
      "Thue_suat" => 0,
      "Thue" => 0,
      "User_Name" => ""
    ];

    $request->api_author = $user_id;
    $request->act = "Thêm hiệu lực gói phí";
    $this->callAPI($request);
  }

  public function updateTuitionFeeExpire($tuition_fee, $user_id)
  {
    $request = new Request();

    $request->api_method = 'PUT';
    $request->api_url = $this->url . "Danhmuchieulucgoi/$tuition_fee->accounting_id";
    $request->api_params = [
      "Ma_vv" => "$tuition_fee->accounting_id",
      "Gt1" => $tuition_fee->price,
      "Gt2" => round(($tuition_fee->discount * 100 / $tuition_fee->price), 1),
      "Ngay_hl" => "$tuition_fee->available_date",
      "Ngay_hl1" => "$tuition_fee->expired_date",
      "Ma_thue" => "",
      "Thue_suat" => 0,
      "Thue" => 0,
      "User_Name" => ""
    ];

    $request->api_author = $user_id;
    $request->act = "Cập nhật hiệu lực gói phí";
    $this->callAPI($request);
  }

  public function createEnrolment($contract, $user_id)
  {
    $totalSession = $contract->summary_sessions;
    $request = new Request();

    $request->api_method = 'POST';
    $code = $this->genEnrolmentCode($contract->id, $contract->branch_accounting_id);
    $request->api_url = $this->url . "PhieuHoc";
    $request->api_params = [
      "ngay_ct" => $contract->enrolment_start_date,
      "So_ctPNH" => $contract->accounting_id,
      "So_ct" => str_replace('PNH', 'PVH', $contract->accounting_id), //$code,
      "Ma_TTLN" => "$contract->branch_accounting_id",
      "Ma_KH" => "$contract->student_accounting_id",
      "ong_ba" => "$contract->parent",
      "dien_giai" => "$contract->bill_info",
      "So_seri" => "",
      "So_HD" => "",
      "Ma_vv_i" => "$contract->tuition_fee_accounting_id",
      "Tg_hoc" => round(($totalSession / 4), 1),
      "SL_Buoi" => $totalSession,
      "Gt1" => $contract->tuition_fee_price,
      "CK_NT" => $contract->tuition_fee_price - $contract->must_charge,
      "TIEN_CL" => !empty($contract->total_charged) ? $contract->total_charged : $contract->must_charge, //$contract->must_charge,
      "GT_PB" => "",
      "Ma_thue" => "",
      "Thue_suat" => "",
      "thue_nt" => "",
      "MA_Phi_I" => "",
      "User_name" => ""
    ];

    $request->api_author = $user_id;
    $request->act = "Thêm mới đăng ký lớp";
    $data = $this->callAPI($request);

    $res = false;
    if ($data) {
      $info = json_decode($data);
      $res = (isset($info->Data) && ($info->Data->Table[0]->Status === 'Y')) ? $code : false;
    }
    // $res = $code;

    return $res;
  }

  public function createReserve($reserveId, $user_id)
  {
    $reserve = u::first("SELECT r.start_date, r.end_date, r.note, b.accounting_id as branch_accounting_id,
                                      c.accounting_id as contract_accounting_id, p.accounting_id as product_accounting_id, 
                                      t.accounting_id as tuition_fee_accounting_id,
                                      s.accounting_id as student_accounting_id
                               FROM reserves r
                               LEFT JOIN branches b ON b.id = r.branch_id
                               LEFT JOIN contracts c ON c.id = r.contract_id
                               LEFT JOIN products p ON p.id = r.product_id
                               LEFT JOIN tuition_fee t ON t.id = c.tuition_fee_id
                               LEFT JOIN students s ON s.id = r.student_id
                               WHERE r.id = $reserveId AND c.status = 6 AND s.status >0 ");
    if(empty($reserve)){
      return false;
    }
    $request = new Request();
    $request->api_method = 'POST';
    $request->api_url = $this->url . "Baoluu";
    $request->api_params = [
      "Ma_TTLN" => "$reserve->branch_accounting_id",                        // Mã trung tâm
      "So_ct_PNH" => $reserve->contract_accounting_id,                       // Số phiếu đăng ký học (Mã hợp đồng)
      "Ma_kh" => "$reserve->student_accounting_id",                         // Mã học viên
      "Ma_vv" => "$reserve->tuition_fee_accounting_id",                         // Mã gói học
      "Ngay_thoi" => "$reserve->start_date",                                // Ngày bảo lưu
      "Ngay_DH" => "$reserve->end_date",                                    // Ngày quay lại học
      "Ghichu" => $reserve->note ?: "",                                           // Ghi chú
      "User_name" => ""
    ];

    $request->api_author = $user_id;
    $request->act = "Bảo lưu";
    $data = $this->callAPI($request);
    $res = false;
    if ($data) {
      $info = json_decode($data);
      $res = (isset($info->Data) && ($info->Data->Table[0]->Status === 'Y'));
    }
    // $res = true;

    return $res;
  }

  public function createPaymentRecord($info, $user_id)
  {
    $request = new Request();

    $request->api_method = 'POST';
    $request->api_params = [
      "M_Cp_Name" => "CP_ConvertPTHFromCRM",
      "M_StrPara" => ""
    ];

    $parts = [$this->key];

    foreach ($info as $property) {
      $parts[] = $property;
    }

    $params = implode("#", $parts);

    $request->api_author = $user_id;
    $request->act = "Thêm mới bản ghi thu phí";
    $request->api_params["M_StrPara"] = $params;
    $data = $this->callAPI($request);

    $res = false;
    if ($data) {
      $info = json_decode($data);
      $res = $info;
    }

    return $res;
  }

  public function createContract($contract, $user_id, $is_changed_contract = 0, $act = "Thêm mới hợp đồng",$re_call=0)
  {
    $request = new Request();

    $tuitionFeePrice = (int)$contract->tuition_fee_price;
    $tuitionFeeDiscount = (int) $contract->tuition_fee_discount;
    $baseDiscountPercent = $tuitionFeePrice > 0 ? round(($tuitionFeeDiscount *100) / $tuitionFeePrice,1): 0;
    $request->api_method = 'POST';
    if($act=="Chuyển trung tâm"){
      $Ma_post =9;
      $code = $contract->accounting_id;
    }elseif($act=="Đóng hợp đồng do chuyển trung tâm"){
      $Ma_post =1;
      $code = $contract->accounting_id;
    }else{
      if($re_call){
        $Ma_post =9;
        $code = $contract->accounting_id;
      }else{
        $Ma_post =9;
        $code = $this->genContractCode($contract->id, $contract->branch_accounting_id);
      }
    }
    $request->api_url = $this->url . "ThongTinDangKyHoc";
    $createdAt = gmdate("Y-m-d", strtotime($contract->created_at));
    if(isset($contract->coupon)){
       $discount = u::first("select percent from discount_codes WHERE code = '$contract->coupon'");
    }
    $dien_giai = (!empty($contract->note)) ? $contract->note : $contract->bill_info;
    $ref_code = (!empty($contract->ref_code)) ? $contract->ref_code : '';
    $request->api_params = [
      "TOKENKEY"=>"ADMIN@CMS",
      "ngay_ct" => "$createdAt",
      "Ma_post" => $Ma_post, // 1 - Hủy, 3 - Lập chứng từ, 9 - Duyệt
      "So_ct" => "$code",
      "Ma_TTLN" => "$contract->branch_accounting_id",
      "Ma_KH" => "$contract->student_accounting_id",
      "ong_ba" => "$contract->parent",
      "Ma_Hs" => "$contract->sale_accounting_id",
      "ma_Td2" => "$ref_code",
      "Nguoi_don" => "",
      "Ngay_sinh" => "$contract->date_of_birth",
      "dien_giai" => "$dien_giai",
      "Is_Doi_Goi" => $is_changed_contract,
      "Ma_vv_i" => "$contract->tuition_fee_accounting_id",
      "Gt1" => $contract->tuition_fee_price,
      "Gt2" => $baseDiscountPercent,
      "Tien_CK" => $contract->tuition_fee_discount,
      "Phai_nop" => $contract->tuition_fee_receivable,
      "MA_TD1_I" => "$contract->coupon",
      "TL_CK" => isset($discount) && isset($discount->percent) ? $discount->percent: 0,
      "TIEN_NT" => $contract->discount_value,
      "TIEN_CK2" =>$contract->sibling_discount + $contract->total_discount,
      "TIEN_CL" => $contract->tien_cl,
      "MA_CD_I" => "",
      "MA_DVCS" =>"",
      "User_name" => "",
      "So_buoiKM" => $contract->bonus_sessions,
      "So_thangKM" => round($contract->bonus_sessions/4),
      "Tien_KM" => $contract->bonus_amount,
    ];

    $request->api_author = $user_id;
    $request->act = $act;
    $data = $this->callAPI($request);

    $res = false;
    if ($data) {
      $info = json_decode($data);
      $res = (isset($info->Data) && isset($info->Data->Table[0]->Status) && ($info->Data->Table[0]->Status === 'Y')) ? $code : false;
    }
    // $res = $code;

    return $res;
  }

  public function terminateContract($contract_id, $user_id, $is_changed_contract = 0, $act = "Đóng hợp đồng"){
    $query = "SELECT c.created_at, c.id, (SELECT accounting_id FROM branches WHERE id = c.branch_id) AS branch_accounting_id,
                        s.accounting_id AS student_accounting_id,
                        s.gud_name1 AS parent,
                        c.bill_info,
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
                        c.accounting_id
                FROM contracts c LEFT JOIN students s ON c.student_id = s.id LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
                WHERE c.id = $contract_id";
    $contract = u::first($query);

    $request = new Request();
    $request->api_method = 'PUT';
    $request->api_url = $this->url . "ThongTinDangKyHoc?So_Ct=$contract->accounting_id&Ma_TTLN=$contract->branch_accounting_id";
    $request->api_params = [
      "ngay_ct" => "$contract->start_date",
      "So_ct" => "$contract->accounting_id",
      "Ma_TTLN" => "$contract->branch_accounting_id",
      "Ma_KH" => "$contract->student_accounting_id",
      "ong_ba" => "$contract->parent",
      "Ma_Hs" => "$contract->sale_accounting_id",
      "ma_Td2" => "",
      "Nguoi_don" => "",
      "Ngay_sinh" => "$contract->date_of_birth",
      "dien_giai" => "$contract->bill_info",
      "Is_Doi_Goi" => $is_changed_contract,
      "Ma_vv_i" => "$contract->tuition_fee_accounting_id",
      "Gt1" => $contract->tuition_fee_price,
      "Gt2" => $contract->tuition_fee_price > 0 ? round(($contract->tuition_fee_discount * 100 / $contract->tuition_fee_price), 1) : 0,
      "Tien_CK" => $contract->tuition_fee_discount,
      "Phai_nop" => $contract->tuition_fee_receivable,
      "MA_TD1_I" => "$contract->coupon",
      "TL_CK" => 0,
      "TIEN_NT" => $contract->sibling_discount + $contract->discount_value,
      "TIEN_CK2" => $contract->total_discount,
      "TIEN_CL" => $contract->tien_cl,
      "MA_CD_I" => "",
      "User_name" => "",
      "Ma_post" => 1 // 1 - Hủy, 3 - Lập chứng từ, 9 - Duyệt
    ];

    $request->api_author = $user_id;
    $request->act = $act;
    $data = $this->callAPI($request);

    $res = false;
    if ($data) {
      $info = json_decode($data);
      $res = (isset($info->Data) && ($info->Data->Table[0]->Status === 'Y')) ? $contract->accounting_id : false;
    }
    // $res = $contract->accounting_id;

    return $res;
  }

  public function charge($payment, $user_id)
  {
    $request = new Request();

    $request->api_method = 'POST';
    $code = "CMS-PT-$payment->id";
    $request->api_url = $this->url . "PhieuThu";
    $request->api_params = [
      "ngay_ct" => "$payment->created_at",
      "So_ct" => "$code",
      "Ma_TTLN" => "$payment->branch_accounting_id",
      "Ma_KH" => "$payment->student_accounting_id",
      "ong_ba" => "$payment->parent",
      "dia_chi" => "$payment->address",
      "So_ct_PNH" => "$payment->contract_accounting_id",
      "dien_giai" => "$payment->bill_info",
      "HT_TT" => $this->setPaymentMethod($payment->method),
      "Ma_TD4" => "",
      "Kieu_TT" => "",
      "ngay_bd" => "$payment->start_date",
      "ngay_kt" => "$payment->end_date",
      "Ma_vv_i" => "$payment->tuition_fee_accounting_id",
      "Gt1" => $payment->tuition_fee_price,
      "CK_NT" => $payment->tuition_fee_discount,
      "TIEN_CL" => $payment->must_charge,
      "DA_NOP" => $payment->total_charged,
      "TIEN_NT" => $payment->amount,
      "PT_PHi" => 0,
      "PHi_NH" => 0,
      "TG_HOC" => 0,
      "User_name" => ""
    ];

    $request->api_author = $user_id;
    $request->act = "Đóng phí";
    $data = $this->callAPI($request);

    $res = false;
    if ($data) {
      $info = json_decode($data);
      $res = (isset($info->Data) && ($info->Data->Table[0]->Status === 'Y')) ? $code : false;
    }
    // $res = $code;

    return $res;
  }

  public function getToken(Request $request){
    $response = new Response();
    $code = APICode::PAGE_NOT_FOUND;
    $message = 'Tài khoản không tồn tại hoặc đã bị xóa';
    $data = null;
    $username = strtoupper($request->us);
    $password = $request->pa;
    $branch_ids = [];
    $ip = $request->ip();
    $agent = $request->server('HTTP_USER_AGENT') ? $request->server('HTTP_USER_AGENT') : NULL;
    $roles_list = [];
    $branches_ids = [];

    $user = User::where('username',$username)->first();
    if($user){
      if (Hash::check($password, $user->password)) {
        if ($user->status == 0) {
          $code = APICode::PAGE_NOT_FOUND;
          $message = 'Tài khoản này đang bị khóa.';
        } else {
          $config = u::config();
          $superoles = isset($config->superoles) ? json_decode($config->superoles) : [ROLE_SUPER_ADMINISTRATOR, ROLE_ADMINISTRATOR, ROLE_MANAGERS];
          $roles = u::query("SELECT r.id AS role_id, r.name AS role, r.status AS role_status, r.functions, t.branch_id, b.name AS branch_name, b.zone_id, b.region_id, z.name AS zone_name FROM roles as r LEFT JOIN term_user_branch AS t ON r.id = t.role_id LEFT JOIN branches AS b ON t.branch_id = b.id LEFT JOIN zones AS z ON b.zone_id = z.id WHERE t.user_id = $user->id");
          if (in_array($roles[0]->role_id, $superoles)) {
            $super_admin = $roles[0];
            $roles = u::query("SELECT '$super_admin->role_id' AS role_id, '$super_admin->role' AS role, '$super_admin->role_status' AS role_status, '$super_admin->functions' AS functions, id AS branch_id, `name` AS branch_name, zone_id, region_id FROM branches WHERE status > 0");
          }
          if ($roles) {
            foreach ($roles as $role) {
              $roles_list[] = ['id' => $role->role_id, 'role' => $role->role];
              if ($role->branch_id && !in_array($role->branch_id, $branch_ids)) {
                $branch_ids[] = ['id' => $role->branch_id, 'name' => $role->branch_name, 'role' => $role->role_id, 'title' => $role->role];
                $branches_ids[] = $role->branch_id;
              }
            }
            $branches_ids = implode(',', $branches_ids);
          }

          $client_info = (Object)[];
          $remote_address = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:NULL;
          $client_info->agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
          $client_info->language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE']:NULL;
          $roles_list = apax_ada_sort_items($roles_list);
          $main_role = $roles_list[0];
          $session = (object)array();
          $salt = md5($user->password);
          $mask = md5(json_encode($client_info) . $salt);
          $time = time();
          $session->id = $user->id;
          $session->hrm_id = trim($user->hrm_id);
          $session->accounting_id = isset($user->accounting_id) ? trim($user->accounting_id) : '';
          $session->role_id = $main_role['id'];
          $session->role_name = $main_role['role'];
          $session->roles_detail = $roles;
          $session->branches = $branch_ids;
          $session->branches_ids = $branches_ids;
          $session->code = "$user->id-" . trim($user->hrm_id) . '-' . trim($session->accounting_id);
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
          $session->life = (int)(3600 * 24);
          $session->start_at = $time;
          $session->expire_at = $time + $session->life;
          $session->first_change_password = $user->first_change_password;
          $session->last_change_password_date = $user->last_change_password_date;
          $payload = json_encode($session);
          $alias = strtoupper("$session->hrm_id~$session->nick");
          $hash = hash_hmac('sha512', "$payload$salt$time", $user->password);
          $hkey = "$alias@$remote_address|" . date('YmdHis') . "-$mask:$hash";
          $session->key = $hash;
          Redis::set($hkey, json_encode($session));
          Redis::expire($hkey, $session->life);
          $data = $hash;
          $message = "success!";
          $code = APICode::SUCCESS;
        }
      } else {
        $code = APICode::PAGE_NOT_FOUND;
        $message = "Tài khoản hoặc mật khẩu không chính xác";
      }
    }

    return $response->formatResponse($code, $data, $message);
  }

  private function logRequestFromCyber($request){
      try {
          $host = $request->getSchemeAndHttpHost();
          $logCyberRequest = new LogCyberRequest();
          $logCyberRequest->logCallingAPI($host, json_encode($request->all()), json_encode($request->header()), "{$request->method()}", date('Y-m-d H:i:s'), null, 0, 0, "Cyber gọi api payment của CMS");
      }catch (\Exception $e){
      }
  }

    private function getContractForPayment($contractCode, $tuitionFeeIds, $isImport)
    {
        $query = "SELECT id, accounting_id, total_charged, must_charge, debt_amount, 
               (SELECT MAX(`count`) FROM payment WHERE contract_id = c.id) AS charge_time
                FROM
                contracts c
              WHERE accounting_id = '$contractCode'";
        if ($isImport && !empty($tuitionFeeIds)) {
            $str = implode(",", $tuitionFeeIds);
            $query .= " AND tuition_fee_id IN ($str)";
        }
        return u::first($query);
    }

    public function createPayment(Request $request)
    {
        self::logRequestFromCyber($request);
        $response = new Response();
        $paymentCode = $request->payment_code;
        $contractCode = $request->contract_code;
        $tuition_fee_ids = !empty($request->tuition_fee_ids) ? $request->tuition_fee_ids : null;
        $is_import = !empty($request->is_import) ? $request->is_import : null;
        if (empty($paymentCode) || empty($contractCode)) {
            return $response->formatResponse(APICode::WRONG_PARAMS, null, "Dữ liệu không chính xác.");
        }
        $contract = self::getContractForPayment($contractCode, $tuition_fee_ids, $is_import);
        
        if (!$contract) {
            return $response->formatResponse( APICode::PAGE_NOT_FOUND, null, '');
        }

        $isEdit = $request->is_edit == 1;
        $creator = User::where('accounting_id', $request->creator)->first();
        if (isset($creator->id)) {
            $request->users_data->id = $creator->id;
        }

        $chargeAmount = (int)$request->amount;
        $editOldPayment = u::first("select * from payment where accounting_id = '$paymentCode' order by id desc limit 1");
        
        if ($editOldPayment){
            return $response->formatResponse(APICode::SUCCESS,[]);
            exit;
        }
        
        $next = true;
        if ($isEdit) {
            $oldPayment = u::first("select * from payment where accounting_id = '$paymentCode'");
            if (empty($oldPayment)) {
                return $response->formatResponse( APICode::PAGE_NOT_FOUND, null, 'Bản ghi không tồn tại!');
            }
            $oldAmount = $oldPayment->amount;
            $totalCharge =  $chargeAmount + (int)$contract->total_charged - $oldAmount;
            $debtAmount = (int)$contract->must_charge - (int)$totalCharge;
        }else{
            if ($contract->debt_amount > 0){
                $totalCharge =  $chargeAmount + (int)$contract->total_charged;
                $debtAmount = (int)$contract->debt_amount - (int)$request->amount;
                $debtAmount = $debtAmount < 0 ? 0 : $debtAmount;
            }
            else{
                $next = false;
                $totalCharge =  (int)$contract->total_charged;
                $debtAmount = (int)$contract->debt_amount;
            }
            $oldAmount = 0;
        }
        
        if (!$next){
            return $response->formatResponse(APICode::SUCCESS,[]);exit;
        }
        $chargeDate =trim("$request->charge_date");
        if(!u::isValidDate($chargeDate)) {
            if (is_numeric($chargeDate)&& strlen($chargeDate) === 8) {
                $str = substr($chargeDate,0,4);
                $str.="-";
                $str.=substr($chargeDate,4,2);
                $str.="-";
                $str.=substr($chargeDate,6,2);
                if(u::isValidDate($str)){
                    $chargeDate = $str;
                }else{
                    $response->formatResponse(APICode::WRONG_PARAMS, null, "Ngày thu phí không chính xác.");
                }
            }else{
                return $response->formatResponse(APICode::WRONG_PARAMS, null, "Ngày thu phí không chính xác.");
            }
        }

        $update = [
            'charge_amount' => $chargeAmount,
            'total_charged' => $totalCharge,
            'debt_amount' => $debtAmount,
            'method' => 0,
            'charge_date' => $chargeDate,
            'note' => $request->note,
            'payment_code' => $request->payment_code
        ];

        $request->merge([
            'contract_id' => $contract->id,
            'must_charge' => $contract->must_charge,
            'charge_time' => (int)$contract->charge_time + 1,
            'payload' => 0,
            'update' => $update,
            'old_amount' =>$oldAmount
        ]);

        $charge = new ChargesController();
        return $charge->store($request);
    }

  private function setPaymentMethod($method)
  {
    switch ($method) {
      case 1:
        $payment_method = 'Chuyển khoản';
        break;
      case 2:
        $payment_method = 'Thanh toán thẻ tín dụng';
        break;
      default:
        $payment_method = 'Tiền mặt';
    }

    return $payment_method;
  }

  private function genStudentCode($student_id){
    $string = "$student_id";

    $len = strlen($string);
    $maxLen = 5;
    $need = $maxLen - $len;
    $source = "0000";

    $needMore = substr($source, 0, $need);
    $code = "HS-" . $needMore . $string;

    return $code;
  }

  private function genContractCode($contract_id, $branch_accounting_id){
    $year = date('y');
    return "$branch_accounting_id.$year.PNH.$contract_id";
  }

  private function genEnrolmentCode($enrolment_id, $branch_accounting_id){
    $year = date('y');
    return "$branch_accounting_id.$year.PVH.$enrolment_id";
  }

//  private function genContractCode($contract_id, $branch_accounting_id){
//    $year = date('y');
//    $str_contract_id = str_pad($contract_id, 5, '0', STR_PAD_LEFT);
//    return "$branch_accounting_id.$year.PNH$str_contract_id";
//  }
//  private function genEnrolmentCode($enrolment_id, $branch_accounting_id){
//    $year = date('y');
//    $str_enrolment_id = str_pad($enrolment_id, 5, '0', STR_PAD_LEFT);
//    return "$branch_accounting_id.$year.PVH$str_enrolment_id";
//  }
////
}
