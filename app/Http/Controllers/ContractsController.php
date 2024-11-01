<?php

namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Contract;
use App\Models\CyberAPI;
use App\Models\DiscountCode;
use App\Models\ProcessExcel;
use App\Models\Response;
use App\Models\Student;
use App\Providers\ApiServiceProvider;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function GuzzleHttp\json_decode;

\Moment\Moment::setDefaultTimezone('Asia/Bangkok');
class ContractsController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getContracts()
    {
      // $contracts = Contract::paginate(10);
      // return response()->json($contracts);
      $contracts = DB::table('contracts')->get();
      return response()->json($contracts);
    }

    public function loadStudent($student_id, Request $request)
	{
		$code = APICode::PERMISSION_DENIED;
		$data = null;
		$response = new Response();
		$information = (object)[];
		$session = $request->users_data;
		// Fix lại theo yêu cầu trong báo bug #79
        // [Đề xuất] Logic: KQMM: Chỉ cần học sinh đó có thông tin anh em học cùng (bảng student có thông tin trường sibling_id) thì khi thêm mới nhập học và tái phí nhập học chính thức sẽ hiển thị trường " Số tiền giảm trừ anh em" trong mọi trường hợp
        // ------------------------------------------------------------------------------------------------------------------------------------------------
        // Điều kiện query trước khi fix lại
        // IF((SELECT COUNT(*) FROM contracts WHERE student_id = s.sibling_id AND type > 0 AND status IN (2,5,6)) > 0, 1, 0) sibling,
		if ($student_id && $session) {
			$code = APICode::SUCCESS;
			$user_id = $session->id;
			$role_branch_ceo = ROLE_BRANCH_CEO;
			$role_region_ceo = ROLE_REGION_CEO;
			$student = u::first("SELECT
				s.id AS student_id,
				s.crm_id as crm_id,
				s.cms_id AS cms_id,
				s.name,
				s.email AS stu_email,
				s.phone AS home_phone,
				s.gender,
				s.branch_id, 
				s.type,
				COALESCE(s.sibling_id, 0) AS sibling,
				g.name AS school_grade,
				s.date_of_birth,
				COALESCE(c.trial_done, 0) AS trail_done,
				CONCAT(s.name, ' - ', s.cms_id) AS label,
				COALESCE(s.gud_name1, s.gud_name2) AS parent_name,
				COALESCE(s.gud_mobile1, s.gud_mobile2) AS parent_mobile,
				COALESCE(s.gud_email1, s.gud_email2) AS parent_email,
				s.gud_name1,
				s.gud_mobile1,
				s.gud_name2,
				s.gud_mobile2,
				s.address,
				s.school,
				t.status AS student_status,
				s.branch_id AS branch_id,
				b.name AS branch_name,
				b.brch_id AS branch_lms_id,
				b.hrm_id AS branch_hrm_id,
				r.id AS region_id,
				r.name AS region_name,
				u0.id AS cm_id,
				u0.full_name AS cm_name,
				u1.id AS ec_id,
				u1.hrm_id AS ec_hrm_id,
				u1.full_name AS ec_name,
				u2.id AS ec_leader_id,
				u2.full_name AS ec_leader_name,
				u3.id AS om_id,
				u3.full_name AS om_name,
				(SELECT u4.id FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo' AND tu.status = 1 LIMIT 1) AS ceo_branch_id,
				(SELECT u4.full_name FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo' AND tu.status = 1 LIMIT 1) AS ceo_branch_name,
				(SELECT u5.id FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_ceo' AND tu.status = 1 LIMIT 1) AS ceo_region_id,
				(SELECT u5.full_name FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_ceo' AND tu.status = 1 LIMIT 1) AS ceo_region_name
				FROM students AS s
				LEFT JOIN term_student_user AS t ON t.student_id = s.id
				LEFT JOIN branches AS b ON t.branch_id = b.id
				LEFT JOIN regions AS r ON b.region_id = r.id
				LEFT JOIN school_grades AS g ON s.school_grade = g.id
				LEFT JOIN users AS u0 ON t.cm_id = u0.id
				LEFT JOIN users AS u1 ON t.ec_id = u1.id
				LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id
				LEFT JOIN users AS u3 ON u0.superior_id = u3.hrm_id
				LEFT JOIN (
				SELECT ct.id AS id, ct.type AS `type`, ct.passed_trial AS trial_done
				FROM contracts AS ct
				LEFT JOIN students AS st ON ct.student_id = st.id
				WHERE ct.type = 0 AND st.status >0) AS c ON c.id = s.id
				WHERE s.status >0 AND s.id = $student_id LIMIT 0, 1");
			if ($student && isset($student->branch_id)) {
				$id = (int)$student->branch_id;
				$query = "SELECT
					p.prod_code AS product_lms_id,
					p.`name` AS product_name,
					p.id AS product_id,
					t.session AS tuition_fee_session,
					t.price AS tuition_fee_price,
          t.price_min AS tuition_fee_price_min,
					t.discount AS tuition_fee_discount,
					t.receivable AS tuition_fee_receivable,
					t.id AS tuition_fee_id,
					t.type AS tuition_fee_type,
					t.`name` AS tuition_fee_name
				FROM
					products AS p
					LEFT JOIN tuition_fee AS t ON t.product_id = p.id
				WHERE
					p.`status` > 0 AND t.`status` > 0
					AND (t.available_date <= CURDATE() AND t.expired_date >= CURDATE())
					AND (t.branch_id LIKE '$id,%' OR t.branch_id LIKE '%,$id,%' OR t.branch_id LIKE '%,$id' OR t.branch_id = '$id' )";
				$basedata = u::query($query);
				if ($basedata) {
					$products = [];
					$products_ids = [];
					foreach ($basedata as $item) {
						if (!in_array($item->product_id, $products_ids)) {
							$products_ids[] = $item->product_id;
							$products['product_id' . $item->product_id] = [
								'product_id' => $item->product_id,
								'product_name' => $item->product_name,
								'product_lms_id' => $item->product_lms_id
							];
						}
					}
					if ($products) {
						foreach ($products as $i => $product) {
							$tuition_fees = [];
							$tuition_fees_ids = [];
							foreach ($basedata as $item) {
								if ($item->product_id == $product['product_id'] && !in_array($item->tuition_fee_id, $tuition_fees_ids)) {
									$tuition_fees_ids[] = $item->tuition_fee_id;
									$tuition_fees['tuition_fee_id' . $item->tuition_fee_id] = [
										'tuition_fee_id' => $item->tuition_fee_id,
										'tuition_fee_type' => $item->tuition_fee_type,
										'tuition_fee_name' => $item->tuition_fee_name,
										'tuition_fee_price' => $item->tuition_fee_price,
										'tuition_fee_session' => $item->tuition_fee_session,
										'tuition_fee_discount' => $item->tuition_fee_discount,
										'tuition_fee_receivable' => $item->tuition_fee_receivable
									];
								}
							}
							if ($tuition_fees) {
								$products['product_id' . $product['product_id']]['tuition_fees'] = $tuition_fees;
							}
						}
					}
					if ($products) {
						// Fix lại theo yêu cầu trong báo bug #79
						// [Đề xuất] Logic: KQMM: Chỉ cần học sinh đó có thông tin anh em học cùng (bảng student có thông tin trường sibling_id) thì khi thêm mới nhập học và tái phí nhập học chính thức sẽ hiển thị trường " Số tiền giảm trừ anh em" trong mọi trường hợp
						// ------------------------------------------------------------------------------------------------------------------------------------------------
						// if ($student && $student->sibling) {
						// 	$td = new \Moment\Moment(date('Y-m-d'));
						// 	$check_sibling = u::first("SELECT status, withdraw_date FROM enrolments WHERE student_id = $student->sibling ORDER BY id DESC LIMIT 0, 1");
						// 	if ($check_sibling && isset($check_sibling->withdraw_date)) {
						// 		$ca = $td->from(strtotime($check_sibling->withdraw_date));
						// 		if ((int)$check_sibling->status === 0 && $ca->getDays() <= 0) {
						// 			$student->sibling = '';
						// 		}
						// 	}
						// }
						$data = (object)[];
						$data->student = $student;
						$data->products = $products;
						$data->branch_id = $id;
					}
				}
			}
		}
		return $response->formatResponse($code, $data);
	}

    public function products($branch_id, $student_id, Request $request)
    {
      $code = APICode::PERMISSION_DENIED;
      $data = null;
      $response = new Response();
      $information = (object) [];
      $session = $request->users_data;
      if ($branch_id && $student_id && $session) {
        $code = APICode::SUCCESS;
        $id = (int)$branch_id;
        $user_id = $session->id;
        $query = "SELECT
          p.prod_code AS product_cms_id,
          p.`name` AS product_name,
          p.id AS product_id,
          t.session AS tuition_fee_session,
          t.price AS tuition_fee_price,
          t.discount AS tuition_fee_discount,
          t.receivable AS tuition_fee_receivable,
          t.id AS tuition_fee_id,
          t.type AS tuition_fee_type,
          t.`name` AS tuition_fee_name,
          t.`number_of_months`,
          t.price_min AS tuition_fee_price_min
        FROM
				  products AS p
				LEFT JOIN tuition_fee AS t ON t.product_id = p.id
				WHERE
				p.`status` > 0 AND t.`status` > 0
				AND (t.available_date <= CURDATE() AND t.expired_date >= CURDATE())
				AND (t.branch_id LIKE '$id,%' OR t.branch_id LIKE '%,$id,%' OR t.branch_id LIKE '%,$id' OR t.branch_id = '$id' )
				AND t.`id` NOT IN (SELECT GROUP_CONCAT(tuition_fee_id) FROM contracts WHERE student_id = $student_id GROUP BY tuition_fee_id HAVING COUNT(*) >2)";

        $basedata = u::query($query);
        if ($basedata) {
          $products = [];
          $products_ids = [];
          foreach ($basedata as $item) {
            if (!in_array($item->product_id, $products_ids)) {
              $products_ids[] = $item->product_id;
              $products['product_id'.$item->product_id] = [
                'product_id'=>$item->product_id,
                'product_name'=>$item->product_name,
                'product_cms_id'=>$item->product_cms_id
              ];
            }
          }
          if ($products) {
            foreach ($products as $i => $product) {
              $tuition_fees = [];
              $tuition_fees_ids = [];
              foreach ($basedata as $item) {
                if ($item->product_id == $product['product_id'] && !in_array($item->tuition_fee_id, $tuition_fees_ids)) {
                  $tuition_fees_ids[] = $item->tuition_fee_id;
                  $tuition_fees['tuition_fee_id' . $item->tuition_fee_id] = [
                    'tuition_fee_id' => $item->tuition_fee_id,
                    'tuition_fee_type' => $item->tuition_fee_type,
                    'tuition_fee_name' => $item->tuition_fee_name,
                    'tuition_fee_price' => $item->tuition_fee_price,
                    'tuition_fee_price_min' => $item->tuition_fee_price_min,
                    'tuition_fee_session' => $item->tuition_fee_session,
                    'tuition_fee_discount' => $item->tuition_fee_discount,
                    'tuition_fee_receivable' => $item->tuition_fee_receivable,
                    'number_of_months' => $item->number_of_months
                  ];
                }
              }
              if ($tuition_fees) {
                $products['product_id' . $product['product_id']]['tuition_fees'] = $tuition_fees;
              }
            }
          }
          $role_branch_ceo = ROLE_BRANCH_CEO;
          $role_region_ceo = ROLE_REGION_CEO;
          $student = u::first("SELECT
            s.id student_id,
            s.cms_id,
            s.crm_id,
            s.name,
            s.email stu_email,
            s.phone home_phone,
            s.gender,
            s.type,
            COALESCE(s.sibling_id, 0) sibling,
            g.name school_grade,
            s.date_of_birth,
            COALESCE(c.trial_done, 0) trail_done,
            CONCAT(s.name, ' - ', s.cms_id) AS label,
            COALESCE(s.gud_name1, s.gud_name2) parent_name,
            COALESCE(s.gud_mobile1, s.gud_mobile2) parent_mobile,
            COALESCE(s.gud_email1, s.gud_email2) parent_email,
            s.gud_name1,
            s.gud_mobile1,
            s.gud_name2,
            s.gud_mobile2,
            s.address,
            s.school,
            t.status student_status,
            s.branch_id branch_id,
            b.name branch_name,
            b.brch_id branch_cms_id,
            b.hrm_id branch_hrm_id,
            r.id region_id,
            r.name region_name,
            u0.id cm_id,
            u0.full_name cm_name,
            u1.id ec_id,
            u1.hrm_id ec_hrm_id,
            u1.full_name ec_name,
            u2.id ec_leader_id,
            u2.full_name ec_leader_name,
            u3.id om_id,
            u3.full_name om_name,(SELECT CONCAT(`code`,'*',school_name,'*',COALESCE(address,''),'*',COALESCE(personal_name,'')) FROM collaborators WHERE `code` =  s.ref_code) AS ref_info,
            (SELECT u4.id FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo' AND tu.status = 1 ORDER BY tu.updated_at ASC LIMIT 1) ceo_branch_id,
            (SELECT u4.full_name FROM users u4 LEFT JOIN term_user_branch tu ON u4.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_branch_ceo' AND tu.status = 1 ORDER BY tu.updated_at LIMIT 1) ceo_branch_name,
            (SELECT u5.id FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_ceo' AND tu.status = 1 ORDER BY tu.updated_at LIMIT 1) ceo_region_id,
            (SELECT u5.full_name FROM users u5 LEFT JOIN term_user_branch tu ON u5.id = tu.user_id WHERE tu.branch_id = t.branch_id AND tu.role_id = '$role_region_ceo' AND tu.status = 1 ORDER BY tu.updated_at LIMIT 1) ceo_region_name
            FROM students s
            LEFT JOIN term_student_user t ON t.student_id = s.id
            LEFT JOIN branches b ON t.branch_id = b.id
            LEFT JOIN regions r ON b.region_id = r.id
            LEFT JOIN school_grades g ON s.school_grade = g.id
            LEFT JOIN users u0 ON t.cm_id = u0.id
            LEFT JOIN users u1 ON t.ec_id = u1.id
            LEFT JOIN users u2 ON u1.superior_id = u2.hrm_id
            LEFT JOIN users u3 ON u0.superior_id = u3.hrm_id
            LEFT JOIN (
              SELECT ct.id id, ct.type `type`, ct.passed_trial trial_done
              FROM contracts ct
              LEFT JOIN students st ON ct.student_id = st.id
              WHERE ct.type = 0 AND st.status > 0 ) c ON c.id = s.id
            WHERE s.id = $student_id AND s.status > 0  LIMIT 0, 1");
          if ($products) {
            $latest_contract_info = u::first("SELECT id, type, passed_trial, status, only_give_tuition_fee_transfer, start_date, end_date, count_recharge, program_label, enrolment_last_date, enrolment_last_date, COALESCE(enrolment_last_date, enrolment_last_date) latest_date, class_id, enrolment_start_date, enrolment_end_date, enrolment_schedule, DATE(updated_at) updated_at FROM contracts WHERE student_id = $student_id AND ( type = 0 OR (type > 0 AND debt_amount > 0 AND count_recharge = 0)) AND status >= 0 ORDER BY id DESC limit 0, 1");
            $latest_date = date('Y-m-d');
            if ($latest_contract_info && isset($latest_contract_info->latest_date)) {
              $latest_date = $latest_contract_info->latest_date;
            } elseif (isset($latest_contract_info->end_date) && isset($latest_contract_info->status) && $latest_contract_info->status > 0) {
              $latest_date = $latest_contract_info->end_date;
            }
            if (isset($latest_contract_info->updated_at) && isset($latest_contract_info->status) && in_array((int)$latest_contract_info->status, [0, 7, 8])) {
              $latest_date = $latest_contract_info->updated_at;
            }
            $m = $latest_date && $latest_date != '0000-00-00' ? new \Moment\Moment($latest_date) : date('Y-m-d');
            $latest_date = $latest_date && $latest_date != '0000-00-00' ? $m->format('Y-m-d') : date('Y-m-d');
            $relation_info = (object)[
              'latest_date' => $latest_date,
              'latest_contract' => $latest_contract_info ? $latest_contract_info : null
            ];
            // if (isset($student->sibling) && $student->sibling) {
            //   $td = new \Moment\Moment(date('Y-m-d'));
            //   $check_sibling = u::first("SELECT status, enrolment_withdraw_date FROM contracts WHERE student_id = $student->sibling ORDER BY id DESC LIMIT 0, 1");
            //   if ($check_sibling && isset($check_sibling->status)) {
            //     $ca = $td->from(strtotime($check_sibling->enrolment_withdraw_date));
            //     if ((int)$check_sibling->status == 7 && $ca->getDays() <= 0) {
            //       $student->sibling = '';
            //     }
            //   }
            // }
            $data = (object)[];
            $data->student = $student;
            $data->products = $products;
            $data->information = $relation_info ;
          }
        }
      }
      $validate = self::validateContract($student_id);
      $data->has_error = $validate->has_error;
      $data->message = $validate->message;
      return $response->formatResponse($code, $data);
    }
    private static function validateContract($student_id){
      $has_error=0;
      $message= '';
      $data = u::first("SELECT * FROM students WHERE id = $student_id");

      if($data){
          switch ($data->waiting_status) {
          case 1:
            $has_error = 1;
            $message ="Học sinh đang chờ duyệt chuyển phí";
            break;
          case 2:
            $has_error = 1;
            $message ="Học sinh đang chờ duyệt nhận phí";
            break;
          case 3:
            $has_error = 1;
            $message ="Học sinh đang chờ duyệt chuyển trung tâm";
            break;
          case 4:
            $has_error = 1;
            $message ="Học sinh đang chờ duyệt bảo lưu";
            break;
          case 5:
            $has_error = 1;
            $message ="Học sinh đang chờ duyệt chuyển lớp";
            break;
          default:
            $has_error = 0;
            $message ="";
        }
      }
      return (Object)[
        'has_error' => $has_error,
        'message' => $message,
      ];
    }

     /**
     * Display a listing of the programs and tuition fees for filter.
     *
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request, $branch_id)
    {
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();
      if ($branch_id == 0) {
        $session = $request->users_data;
        $branches = $session->branches;
        $branch_id = $branches[0]->id;
      }
      if ($branch_id) {
        $data = (object)[];
        $programs_query = "SELECT id, program_id, parent_id, `name`, branch_id, IF(parent_id = 0, CONCAT('',`name`), CONCAT('|_', `name`)) label FROM programs WHERE `status` > 0 AND branch_id IN ($branch_id) AND semester_id IN (SELECT id FROM semesters WHERE end_date > CURRENT_DATE())";
        $tuition_fees_query = "SELECT t.id,
          CONCAT(p.name, ' (', t.name, ')') full_name,
          t.name, t.session, t.price, t.receivable, t.type
          FROM tuition_fee t LEFT JOIN products p ON t.product_id = p.id WHERE t.`status` > 0 AND (t.branch_id LIKE '$branch_id,%' OR t.branch_id LIKE '%,$branch_id,%' OR t.branch_id LIKE '%,$branch_id' OR t.branch_id = '$branch_id')";
      }
      $programs = u::query($programs_query);
      $tuition_fees = u::query($tuition_fees_query);
      $data->programs = array_merge([['id' => '', 'label' => 'Lọc theo chương trình']], $programs);
      $data->tuition_fees = array_merge([['id' => '', 'full_name' => 'Lọc theo gói phí']], $tuition_fees);
      return $response->formatResponse($code, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, $pagination, $search, $sort){
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $sort = json_decode($sort);
        $search = json_decode($search);
        $pagination = json_decode($pagination);
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $list = $this->data($request);
        $data->list = $list->data;
        $data->sort = $sort;
        $data->search = $search;
        $data->duration = $pagination->limit * 10;
        $data->pagination = ada()->paging($pagination, $list->total);
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listWaitcharged(Request $request, $pagination, $search, $sort){
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $sort = json_decode($sort);
        $search = json_decode($search);
        $pagination = json_decode($pagination);
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $list = $this->data($request, 1);
        $data->list = $list->data;
        $data->sort = $sort;
        $data->search = $search;
        $data->duration = $pagination->limit * 10;
        $data->pagination = ada()->paging($pagination, $list->total);
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listCharged(Request $request, $pagination, $search, $sort){
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $sort = json_decode($sort);
        $search = json_decode($search);
        $pagination = json_decode($pagination);
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $list = $this->data($request, 2);
        $data->list = $list->data;
        $data->sort = $sort;
        $data->search = $search;
        $data->duration = $pagination->limit * 10;
        $data->pagination = ada()->paging($pagination, $list->total);
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listWaiting(Request $request, $pagination, $search, $sort){
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $sort = json_decode($sort);
        $search = json_decode($search);
        $pagination = json_decode($pagination);
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $list = $this->data($request, 3);
        $data->list = $list->data;
        $data->sort = $sort;
        $data->search = $search;
        $data->duration = $pagination->limit * 10;
        $data->pagination = ada()->paging($pagination, $list->total);
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Build select for query by parameters from request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function select($select = "list") {
      $query = "SELECT
          DISTINCT(c.id),
          c.code code,
          c.accounting_id, 
          c.payload contract_payload,
          c.type contract_type,
          c.status contract_status,
          c.bill_info,
          c.start_date,
          c.receivable,
          c.description,
          c.must_charge,
          c.total_charged,
          c.total_sessions,
          c.bonus_sessions,
          c.summary_sessions,
          c.total_discount,
          c.after_discounted_fee,
          c.only_give_tuition_fee_transfer,
          c.expected_class,
          c.program_label,
          c.debt_amount,
          c.creator_id,
          (select `name` from `programs` where c.program_id = id) as program_name,
          u1.full_name contract_ec_name,
          u2.full_name contract_cm_name,
          IF(((c.type = 0 AND (c.enrolment_start_date IS NULL )) OR (c.type IN (1,7,8) AND (c.enrolment_start_date IS NULL ))), 1, 0) delete_able,
          (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE hrm_id = u1.superior_id LIMIT 0, 1) contract_ec_leader_name,
          c.student_id,
          s.cms_id,
          s.crm_id,  
          CONCAT('CMS', s.cms_id) mix_id,
          r.name region_name,
          b.name branch_name,
          s.name student_name,
          s.date_of_birth birthday,
          s.nick student_nick,
          s.address,
          s.school,
          s.waiting_status,
          COALESCE(s.gud_name1, s.gud_name2) parent_name,
          COALESCE(s.gud_mobile1, s.gud_mobile2) parent_mobile,
          COALESCE(s.gud_email1, s.gud_email2) parent_email,
          s.gender student_gender,
          prd.id product_id,
          prd.name product_name,
          c.real_sessions,
          t.id tuition_fee_id,
          t.name tuition_fee_name,
          t.type tuition_fee_type,
          t.session tuition_fee_session,
          t.price tuition_fee_price,
          t.receivable tuition_fee_receivable,
          p.amount payment_amount";
      return $query;
    }

    /**
     * Build base query by parameters from request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function query($select = "list") {
      $query = "FROM
          contracts c
          LEFT JOIN students s ON c.student_id = s.id
          LEFT JOIN branches b ON c.branch_id = b.id
          LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
          LEFT JOIN payment p ON p.contract_id = c.id
          LEFT JOIN products prd ON c.product_id = prd.id
          LEFT JOIN users u1 ON u1.id = c.ec_id
          LEFT JOIN users u2 ON u2.id = c.cm_id
          LEFT JOIN regions r ON r.id = b.region_id";
      return $query;
    }

    /**
     * Build select for query by parameters from request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function where($request, $type = 0) {
      $search = json_decode($request->search);
      $session = $request->users_data;



      $user_id = $session->id;
      $role_id = $session->role_id;
      $branches = $search->branch ? $search->branch : $session->branches_ids;
      $where = "WHERE c.count_recharge = 0 AND s.branch_id = c.branch_id AND c.status > 0 ";
      $where.= "AND c.branch_id IN ($branches)";
//       if ($role_id == ROLE_REGION_CEO) {
//         $where.= " AND c.ceo_region_id = $user_id";
//       }
//       if ($role_id == ROLE_BRANCH_CEO) {
//         $where.= " AND c.ceo_branch_id = $user_id";
//       }
      if ($role_id == ROLE_EC_LEADER) {
          $where .= " AND (c.ec_leader_id = $user_id OR c.ec_id = $user_id OR c.ec_id IN (SELECT id FROM users WHERE superior_id IN (SELECT hrm_id FROM users WHERE id = $user_id)))";
      }
      if ($role_id == ROLE_EC) {
        $where.= " AND c.ec_id = $user_id";
      }
      if ($role_id == 1200) {
        $where.= " AND s.source IN(26)";
      }
      if ($search->customer_type != '') {
        $where.= " AND c.type = $search->customer_type";
      }
      if ($search->program != '') {
        $where.= " AND c.program_id = $search->program";
      }
      if ($search->tuition_fee != '') {
        $where.= " AND c.tuition_fee_id = $search->tuition_fee";
      }
      if(!empty($search->created_start_date)){
        $where.= " AND c.created_at >= '$search->created_start_date'";
      }
      if(!empty($search->created_end_date)){
        $created_end_date = "$search->created_end_date 23:59:59";
        $where.= " AND c.created_at <= '$created_end_date'";
      }
      if ($search->keyword != '') {
        $keyword = trim($search->keyword);
        $where.= " AND
          ( c.code LIKE '$keyword%'
          OR s.crm_id LIKE '%$keyword%'
          OR s.cms_id LIKE '%$keyword%'
          OR s.name LIKE '%$keyword%'
          OR s.email LIKE '%$keyword%'
          OR s.accounting_id = '$keyword'
          OR s.phone LIKE '$keyword%')";
      }
      switch ($type) {
        case 1: {
          $where.= " AND (c.total_charged = 0 OR c.total_charged IS NULL) AND c.type > 0 AND c.debt_amount > 0";
        } break;
        case 2: {
          $where.= " AND c.total_charged > 0 AND c.type > 0 AND c.total_charged <= c.must_charge AND c.debt_amount >= 0";
        } break;
        case 3: {
          $where.= " AND c.`status` < 6
          AND ((c.type > 0 AND c.status IN (1,2,3,4,5)) OR (c.type = 0 AND c.status IN (1,3,5)))
          AND ((c.real_sessions > 0 AND c.type IN (1,2,3,4,5,6,7)) OR (c.type IN (0,8,85)) OR c.type = 10)
          AND ((c.total_charged > 0 AND c.type IN (1,2,3,4,5,6,7)) OR (c.type IN (0,8,85)) OR c.type = 10)
          AND (SELECT count(id) FROM reserves WHERE student_id=c.student_id AND `status`=1 AND `start_date` < CURDATE() AND `end_date` > CURDATE()) =0
          AND (s.id NOT IN (SELECT student_id FROM contracts WHERE `status` = 6 AND student_id IS NOT NULL GROUP BY student_id) OR ((SELECT enrolment_last_date FROM contracts WHERE `status` = 1 AND student_id = c.student_id AND id = c.id) <= CURDATE())) 
          AND s.waiting_status=0
          AND c.`debt_amount` = 0";
        } break;
      }
      return "$where GROUP BY c.id";
    }

    /**
     * Build base query by parameters from request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function data($request, $type = 0) {
      $pagination = json_decode($request->pagination);
      $sort = json_decode($request->sort);
      $session = $request->users_data;
      $order = "";
      $limit = "";
      if ($sort->by && $sort->to) {
        $order.= " ORDER BY $sort->by $sort->to";
      }
      if ($pagination->cpage && $pagination->limit) {
        $offset = ((int)$pagination->cpage - 1) * (int)$pagination->limit;
        $limit.= " LIMIT $offset, $pagination->limit";
      }
      $total = "SELECT COUNT(o.id) total FROM (SELECT c.id ";
      $select = $this->select();
      $query = $this->query();
      $where = $this->where($request, $type);
      $quary = "SELECT * FROM ($select $query WHERE c.id IN (SELECT * FROM (SELECT c.id FROM contracts c LEFT JOIN students s ON c.student_id = s.id $where ORDER BY c.id DESC $limit ) x ) GROUP BY c.id ORDER BY c.id DESC ) temp";
      if ($type) {
        $quary = "SELECT * FROM ($select $query WHERE c.id IN (SELECT * FROM (SELECT c.id FROM contracts c LEFT JOIN students s ON c.student_id = s.id $where ORDER BY c.id DESC $limit ) x ) GROUP BY c.id ORDER BY c.id DESC ) temp";
      }
      $data = u::query($quary);
      if (count($data)) {
        foreach ($data as $item) {
          $check_delete = false;
          if ($item->delete_able && isset($item->creator_id)) {
            $check_delete = (int)$item->creator_id == (int)$session->id;
            if ($session->role_id >= ROLE_BRANCH_CEO) {
              $check_delete = true;
            }
          }
          $item->delete_able = $check_delete;
        }
      }
      $total = u::first("$total $query $where) AS o");
      $total = is_object($total) ? $total->total : 0;
      $result = (Object)['data' => $data, 'total' => $total];
      return $result;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $code = APICode::SUCCESS;
        $data = null;
        $response = new Response();
        $post = $request->input();
        $user_data = $request->users_data;
        if ($post && $user_data) {
          $is_import = isset($request->is_import) ? $request->is_import : null;
          $user_id = (int)$user_data->id;
          $user_created = $is_import == 1 ? "NULL": $user_id;
          $student = isset($request->student) ? (object)$request->student : null;
          $contract = isset($request->contract) ? (object)$request->contract : null;
          $apis = isset($request->apis) ? (object)$request->apis : null;
          $shift = $contract->shift;
          $note = isset($contract->note) ? $contract->note : $contract->note;
          $note = str_replace(["'","\r\n",'"'],"",$note);
          $ref_code = isset($contract->ref_code) ? $contract->ref_code : '';
          $createdAt = !empty($contract->back_date) ? $contract->back_date.date(' H:i:s') : date('Y-m-d H:i:s');
          $student_id = (int)$student->student_id;
          $only_give_tuition_fee_transfer = (int)$contract->receive;
          $expected_class = $contract->expected_class;
          $contract_type = (int)$contract->customer_type;
          $coupon = trim(strtoupper(isset($contract->coupon) ? $contract->coupon: ''));
          $ec_id = (int)$student->ec_id;
          $cm_id = (int)$student->cm_id;
          $om_id = (int)$student->om_id;
          $branch_id = (int)$student->branch_id;
          $region_id = (int)$student->region_id;
          $ec_leader_id = (int)$student->ec_leader_id;
          $ceo_branch_id = (int)$student->ceo_branch_id;
          $ceo_region_id = (int)$student->ceo_region_id;
          $product_id = (int)$contract->product;
          $tuition_fee_price = (int)str_replace('đ','',str_replace(',','',$contract->price));
          $after_discounted_fee = 0;
          $price_must_charge = 0;
          $sibling_discount = 0;
          $discount_value = 0;
          $tuition_fee_id = 0;
          $total_discount = 0;
          $total_sessions = 0;
          $real_sessions = 0;
          $payload_type = 0;
          $passed_trial = 1;
          $receivable = 0;
          $description = '';
          $bill_info = '';
          $start_date ='';
          $end_date ='';
          $real_sessions = $contract_type == 0 ? 1 : 0;
          $total_sessions = $contract_type == 0 ? 1 : (int)$contract->sessions;
          $bonus_sessions = (int)$request->bonus_sessions;
          $bonus_amount = (int)$request->bonus_amount;
          if(isset($contract->count_recharge)){
              $count_recharge = $contract->count_recharge;
          }else{
              $count_recharge = $contract_type == 0 ? -1 : 0;
          }
          $continue = 0;
          $program_label = "'$contract->program'";
          if ($only_give_tuition_fee_transfer == 0) {
            $continue = (int)$contract->continue;
            $start_date = $contract->start_date;
            $end_date = $contract->end_date;
            $payload_type = (int)$contract->payload;
            $tuition_fee_id = (int)$contract->tuition_fee;
            $price_must_charge =$student->type == 1 ? 0 : (int)$contract->must_charge_amount;
            $description = ada()->quote($contract->detail);
            // $bill_info = ada()->quote($contract->bill_info);
            $bill_info = ada()->quote(str_replace("\n","<br/>",$contract->detail));
            $receivable = (int)$contract->new_price_amount;
            // $total_discount = (int)$contract->total_discount;
            $total_discount = (int)$contract->total_voucher_other;
            $sibling_discount = (int)$contract->sibling;
            $discount_value = (int)$contract->point;
            $after_discounted_fee = (int)$contract->discounted_amount;
          } else {
            $start_date = $contract->start_date;
            $end_date = $start_date;
            $contract_type = 4;
          }
          if ($start_date == '0000-00-00' || !$start_date || $start_date == '') {
            $start_date = date('Y-m-d');
          }
          if ($end_date == '0000-00-00' || !$end_date || $end_date == '') {
            $m = new \Moment\Moment(strtotime($start_date));
            $classdays = [2, 5];
            if ($total_sessions) {
              $holidays = u::getPublicHolidays(0, $branch_id, $product_id);
              $schedule = u::calEndDate($total_sessions, $classdays, $holidays, $start_date);
              $end_date = $schedule->end_date;
            }
          }
          $strMd5 = $student_id.$contract_type.$start_date.$end_date.$product_id.$program_label.$tuition_fee_id.$branch_id;
          if($is_import === 1) {
              $strMd5.=$request->accounting_id;
          }
          $hash_key = md5($strMd5);
          $check = u::first("SELECT COUNT(id) existed FROM contracts WHERE hash_key = '$hash_key'");
          $check = (int)$check->existed;
          $contract_code = apax_ada_gen_contract_code($student->name, $student->ec_name, $student->branch_name);
          if ($cm_id == 0) {
            // $note.=', chưa có CM';
          }
          if ($ec_id == 0) {
            // $note.=', chưa có EC';
          }
          $debt_amount = $price_must_charge;
          $reservable_sessions = 0;
          $reservable = 0;
          $tuition_fee_info = u::first("SELECT number_of_months FROM tuition_fee WHERE id=$tuition_fee_id");
          $status = (int)$student->type == 1 && $debt_amount == 0 ? 5 : 1;
          if ((int)$student->type && $contract->other == $contract->must_charge_amount) {
            $total_discount = $after_discounted_fee;
            $debt_amount = 0;
            $reservable_sessions = $tuition_fee_info && $tuition_fee_info->number_of_months >= 12 ? 8 : ($tuition_fee_info && $tuition_fee_info->number_of_months >= 6 ? 4 : 0);
            if($reservable_sessions){
                $reservable = 1;
            }
          }
          $summary_sessions = $real_sessions;
          if ($debt_amount == 0) {
            $status = 5;
            $contract_type = $contract_type > 0 ? 8 : 0;
            $real_sessions =0 ;
            $summary_sessions = $total_sessions;
            $bonus_sessions = $total_sessions;
            $reservable_sessions = $tuition_fee_info && $tuition_fee_info->number_of_months >= 12 ? 8 : ($tuition_fee_info && $tuition_fee_info->number_of_months >= 6 ? 4 : 0);
          }
          if ($only_give_tuition_fee_transfer == 1) {
            $status = 2;
            $contract_type = 4;
            $product_id = (int)$contract->product;
          }
          if ($check == 0) {
            $action = "Contracts_Create";
            $insert_query = "INSERT INTO contracts
            (`code`,
            `type`,
            `payload`,
            `student_id`,
            `branch_id`,
            `ceo_branch_id`,
            `region_id`,
            `ceo_region_id`,
            `ec_id`,
            `ec_leader_id`,
            `cm_id`,
            `om_id`,
            `product_id`,
            `program_label`,
            `tuition_fee_id`,
            `receivable`,
            `must_charge`,
            `total_discount`,
            `debt_amount`,
            `description`,
            `bill_info`,
            `start_date`,
            `end_date`,
            `total_sessions`,
            `real_sessions`,
            `expected_class`,
            `passed_trial`,
            `status`,
            `created_at`,
            `creator_id`,
            `hash_key`,
            `updated_at`,
            `editor_id`,
            `only_give_tuition_fee_transfer`,
            `reservable`,
            `reservable_sessions`,
            `sibling_discount`,
            `discount_value`,
            `after_discounted_fee`,
            `tuition_fee_price`,
            `count_recharge`,
            `continue_class`,
            `note`,
            `coupon`,
            `ref_code`,
            `bonus_sessions`,
            `bonus_amount`,
            `summary_sessions`,
            `action`,
            shift)
            VALUES
            ('$contract_code',
            $contract_type,
            $payload_type,
            $student_id,
            $branch_id,
            $ceo_branch_id,
            $region_id,
            $ceo_region_id,
            $ec_id,
            $ec_leader_id,
            $cm_id,
            $om_id,
            $product_id,
            $program_label,
            $tuition_fee_id,
            $receivable,
            $price_must_charge,
            $total_discount,
            $debt_amount,
            '$description',
            '$bill_info',
            '$start_date',
            '$end_date',
            $total_sessions,
            $real_sessions,
            '$expected_class',
            $passed_trial,
            $status,
            '$createdAt',
            $user_created,
            '$hash_key',
            NOW(),
            $user_created,
            $only_give_tuition_fee_transfer,
            $reservable,
            $reservable_sessions,
            $sibling_discount,
            $discount_value,
            $after_discounted_fee,
            $tuition_fee_price,
            $count_recharge,
            $continue,
            '$note',
            '$coupon',
            '$ref_code',
            $bonus_sessions,
            $bonus_amount,
            $summary_sessions,
            '$action',
            '$shift')";
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
              `ceo_branch_id`,
              `region_id`,
              `ceo_region_id`,
              `ec_id`,
              `ec_leader_id`,
              `cm_id`,
              `om_id`,
              `product_id`,
              `program_label`,
              `tuition_fee_id`,
              `receivable`,
              `must_charge`,
              `total_discount`,
              `debt_amount`,
              `description`,
              `bill_info`,
              `start_date`,
              `end_date`,
              `total_sessions`,
              `real_sessions`,
              `expected_class`,
              `passed_trial`,
              `status`,
              `created_at`,
              `creator_id`,
              `hash_key`,
              `updated_at`,
              `editor_id`,
              `only_give_tuition_fee_transfer`,
              `reservable`,
              `reservable_sessions`,
              `sibling_discount`,
              `discount_value`,
              `after_discounted_fee`,
              `tuition_fee_price`,
              `count_recharge`,
              `continue_class`,
              `note`,
              `previous_hashkey`,
              `current_hashkey`,
              `version_no`,
              `coupon`,
              `bonus_sessions`,
              `bonus_amount`,
              `summary_sessions`,
              `action`,
              `shift`)
              VALUES
              ('".(int)$latest_contract->id."',
              '$contract_code',
              $contract_type,
              $payload_type,
              $student_id,
              $branch_id,
              $ceo_branch_id,
              $region_id,
              $ceo_region_id,
              $ec_id,
              $ec_leader_id,
              $cm_id,
              $om_id,
              $product_id,
              $program_label,
              $tuition_fee_id,
              $receivable,
              $price_must_charge,
              $total_discount,
              $debt_amount,
              '$description',
              '$bill_info',
              '$start_date',
              '$end_date',
              $total_sessions,
              $real_sessions,
              '$expected_class',
              $passed_trial,
              $status,
              '$latest_contract->created_at',
              $user_created,
              '$hash_key',
              '$latest_contract->updated_at',
              $user_created,
              $only_give_tuition_fee_transfer,
              $reservable,
              $reservable_sessions,
              $sibling_discount,
              $discount_value,
              $after_discounted_fee,
              $tuition_fee_price,
              $count_recharge,
              $continue,
              '$note',
              '$previous_hashkey',
              '$current_hashkey',
              '1',
              '$coupon',
              $bonus_sessions,
              $bonus_amount,
              $summary_sessions,
              '$action',
              '$shift')";
              u::query($insert_log_contract_history_query);
              if($contract->coupon_code){
                $coupon_info = u::first("SELECT * FROM coupons WHERE code='$contract->coupon_code'");
                if($coupon_info){
                  if($coupon_info->type==1 && $coupon_info->quota==1){
                    u::query("UPDATE coupons SET `status`=2, checked_date='".date('Y-m-d')."' WHERE code='$contract->coupon_code'");
                  }
                  u::query("INSERT INTO coupon_logs (coupon_id,contract_id,created_at,creator_id) VALUES 
                    ('$coupon_info->id','".(int)$latest_contract->id."', '$createdAt',$user_created)");
                }
              }
              if($count_recharge == 0){
                $student_info = u::first("SELECT source,creator_id FROM students WHERE id=$student_id");
                $role_creator = u::first("SELECT role_id FROM term_user_branch  WHERE user_id=$student_info->creator_id AND status=1");
                if(in_array($student_info->source,[27,31])||($role_creator && in_array($role_creator->role_id,[80,81]))){
                  u::query("INSERT INTO salehub_create_contract (student_id,contract_id,tuition_fee_id,branch_id,created_at,creator_id) VALUES 
                    ('$student_id','".(int)$latest_contract->id."', $tuition_fee_id,$branch_id,'$createdAt',$user_created)");
                }
              }
              if($count_recharge >= 0){
                  if($is_import === 1){
                      $accountingId = $request->accounting_id;
                      u::query("UPDATE contracts SET accounting_id = '$accountingId' WHERE id = $latest_contract->id");
                  }else {
                      $this->createCyberStudent($student_id, $user_id);
                      $this->createCyberContract($latest_contract->id, $user_id);
                  }
              }
            }
          }else{
              $code=APICode::WRONG_PARAMS;
          }
          u::query("UPDATE students SET `status` = 3, tracking = 3 WHERE id  = $student_id AND `status` = 1 AND tracking IN (1, 8)");
          $data = $student;
    }
        return $response->formatResponse($code, $data);
    }

    public function createCyberStudent($student_id, $user_id){
      $student = Student::find($student_id);
      if($student && !$student->accounting_id){
        $cyberAPI = new CyberAPI();

        $res = $cyberAPI->createStudent($student, $user_id);
        if($res){
          $student->accounting_id = $res;
          $student->save();
        }
      }
    }

    public function retryCyber(Request $request){
        $user = $request->users_data;
        $user_id = !empty($user) ? $user->id: 0;

        if($user_id){
            self::createCyberContract($request->id,$user_id);
        }
    }

    public function createCyberContract($contract_id, $user_id){
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
                        c.bonus_amount, c.accounting_id
                FROM contracts c LEFT JOIN students s ON c.student_id = s.id LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
                WHERE c.id = $contract_id AND s.status > 0 ";
      $res = u::first($query);
      $cyberAPI = new CyberAPI();
      $recall = $res->accounting_id ? 0 : 1;
      $res = $cyberAPI->createContract($res, $user_id, "Thêm mới hợp đồng", $recall);
      if($res){
        u::query("UPDATE contracts SET accounting_id = '$res' WHERE id = $contract_id");
        u::query("UPDATE log_contracts_history SET accounting_id = '$res' WHERE contract_id = $contract_id");
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
      $code = APICode::PERMISSION_DENIED;
      $data = null;
      $response = new Response();
      $id = (int)$id;
      $session = $request->users_data;
      $role_id = $session->role_id;
      $branches = $session->branches_ids;
      $role_branch_ceo = ROLE_BRANCH_CEO;
      $role_region_ceo = ROLE_REGION_CEO;
      
      if ($id && $branches) {
        $code = APICode::SUCCESS;
        $data = (object) [];
        $student = u::first("SELECT s.id student_id,
        s.type,        
        s.name,        
        s.email,
        s.phone,        
        s.source,
        s.gender,
        s.cms_id,
        s.crm_id,
        s.facebook,
        s.school_grade,
        s.date_of_birth,
        COALESCE(s.gud_name1, s.gud_name2) parent_name,
        COALESCE(s.gud_mobile1, s.gud_mobile2) parent_mobile,
        COALESCE(s.gud_email1, s.gud_email2) parent_email,
        s.gud_email1 gud_email1,
        s.gud_email2 gud_email2,
        s.gud_name1 gud_name1,
        s.gud_mobile1 gud_mobile1,
        s.gud_name2 gud_name2,
        s.gud_mobile2 gud_mobile2,
        s.gud_birth_day1 gud_birthday1,
        s.gud_birth_day2 gud_birthday2,
        b.name branch_name,
        c.branch_id branch_id,
        z.name zone_name,
        s.address,
        s.school,
        c.product_id prod_id,
        c.coupon
        FROM contracts c
          LEFT JOIN students s ON c.student_id = s.id
          LEFT JOIN branches b ON c.branch_id = b.id
          LEFT JOIN zones z ON b.zone_id = z.id
        WHERE s.status > 0  AND c.student_id IN (SELECT student_id FROM contracts WHERE id = $id) LIMIT 0, 1");
        $list_contracts_query = "SELECT
        c.*,
        u1.full_name contract_ec_name,
        CONCAT(u1.hrm_id, '-', u1.username) contract_ec_code,
        CONCAT(u3.full_name, ' - ', u3.username) contract_cm_name,
        CONCAT(u1.hrm_id, '-', u1.username) contract_ec_code,
        u1.full_name contract_ec_name,
        IF(c.type IN (1,2,4,7,8) AND c.status IN (1,2,4,5) AND c.enrolment_start_date IS NULL, 1, 0) editable_program,
        IF(c.type <= 4 AND c.status > 0 AND c.status < 7 AND c.enrolment_start_date IS NULL, 1, 0) editable_start_date,
        (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE hrm_id = u1.superior_id LIMIT 0, 1) contract_ec_leader_name,
        (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE hrm_id = u3.superior_id LIMIT 0, 1) contract_om_name,
        (SELECT CONCAT(u.full_name, ' - ', u.username) FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WHERE t.role_id = $role_branch_ceo AND t.branch_id = c.branch_id AND t.status=1 LIMIT 0, 1) contract_ceo_branch_name,
        (SELECT CONCAT(u.full_name, ' - ', u.username) FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WHERE t.role_id = $role_region_ceo AND t.branch_id = c.branch_id AND t.status=1 LIMIT 0, 1) contract_ceo_region_name,
        r.name region_name,
        b.name branch_name,
        prd.id product_id,
        prd.name product_name,
        t.id tuition_fee_id,
        t.name tuition_fee_name,
        t.type tuition_fee_type,
        t.session tuition_fee_session,
        t.price tuition_fee_price,
        t.receivable tuition_fee_receivable
      FROM contracts c
        LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
        LEFT JOIN products prd ON c.product_id = prd.id
        LEFT JOIN branches b ON c.branch_id = b.id
        LEFT JOIN users u1 ON u1.id = c.ec_id
        LEFT JOIN users u3 ON u3.id = c.cm_id
        LEFT JOIN regions r ON r.id = b.region_id
      WHERE c.student_id = $student->student_id AND (c.type = 0 OR c.count_recharge = 0) AND c.branch_id IN ($branches) ORDER BY c.id DESC";
        $contracts = u::query($list_contracts_query);
        $tmp_ec_id = 0;
        if(!empty($contracts)){
            foreach ($contracts as $contract){
                $contract->discount_code = DiscountCode::where('code', '=', $contract->coupon)->first();
                $tmp_ec_id .=",".$contract->ec_id;
            }
        }
        $where_ecs = "t.role_id = ".ROLE_EC." OR t.user_id IN($tmp_ec_id)";
        $where_cms = "t.role_id = ".ROLE_CM;
        if ($role_id >= ROLE_BRANCH_CEO || $role_id == ROLE_EC_LEADER || $role_id == ROLE_OM) {
          $where_ecs = "(t.role_id = ".ROLE_EC." OR t.role_id = ".ROLE_EC_LEADER.")";
          $where_cms = "(t.role_id = ".ROLE_CM." OR t.role_id = ".ROLE_OM.")";
        }
        $cec = u::first("SELECT
            u1.`full_name` ec_name,
            u1.id ec_id
          FROM contracts c
            LEFT JOIN term_user_branch t ON c.branch_id = t.branch_id
            LEFT JOIN users u1 ON t.user_id = u1.id AND t.role_id = ".ROLE_EC."
          WHERE c.id = $id AND u1.id > 0 GROUP BY u1.id");
        $ecid = isset($cec->ec_id) ? (int)$cec->ec_id : 0;

        $ecs = u::query("SELECT
            u.`full_name` ec_name,
            u.id ec_id
          FROM users u
            LEFT JOIN term_user_branch t ON u.id = t.user_id
          WHERE u.id > 0 AND $where_ecs AND (t.branch_id IN ($branches) OR u.id = ".$ecid.") GROUP BY u.id");
          // $sqlz = "SELECT
          //   u1.`full_name` cm_name,
          //   u1.id cm_id
          // FROM contracts c
          //   LEFT JOIN term_user_branch t ON c.branch_id = t.branch_id
          //   LEFT JOIN users u1 ON t.user_id = u1.id AND t.role_id = ".ROLE_CM."
          // WHERE c.id = $id AND u1.id > 0 GROUP BY u1.id";
          // $ccm = u::first($sqlz);
          #
          // Cover
          $ccm = DB::table('contracts')->select('users.full_name as cm_name','users.id as cm_id')
                      ->leftJoin('term_user_branch','contracts.branch_id','=','term_user_branch.branch_id')
                      ->leftJoin('users',function($ljoin){
                          $ljoin->on('term_user_branch.user_id','=','users.id')
                          ->where('term_user_branch.role_id','=',ROLE_CM);
                      })
                      ->where('contracts.id','=',$id)->where('users.id','>',0)
                      ->groupBy('users.id')
                      ->first();

        $cmid = isset($ccm->ec_id) ? (int)$ccm->cm_id : 0;
        // $cms = u::query("SELECT
        //     u.`full_name` cm_name,
        //     u.id cm_id
        //   FROM users u
        //     LEFT JOIN term_user_branch t ON u.id = t.user_id
        //   WHERE u.id > 0 AND $where_cms AND (t.branch_id IN ($branches) OR u.id = ".$cmid.") GROUP BY u.id");
        $cms = DB::table('users')->select('users.full_name as cm_name','users.id as cm_id')
                                ->leftJoin('term_user_branch','users.id','=','term_user_branch.user_id')
                                ->where('users.id','>',0)
                                ->where(function($query) use ($role_id){
                                    if($role_id >= ROLE_BRANCH_CEO || $role_id == ROLE_EC_LEADER || $role_id == ROLE_OM){
                                        $query->where('term_user_branch.role_id','=',ROLE_CM)
                                            ->orWhere('term_user_branch.role_id','=',ROLE_OM);
                                    }else{
                                        $query->where('term_user_branch.role_id','=',ROLE_CM);
                                    }
                                })
                                ->where(function($query) use ($branches,$cmid){
                                    $query->whereRaw('term_user_branch.branch_id IN ('.$branches.')')
                                        ->orWhere('users.id','=',$cmid);
                                })
                                ->groupBy('users.id')
                                ->get();
        // return response()->json($cms);
        $programs = [];
        $relation_info = null;
        $contracts_list = [];
        if (count($contracts)) {
          $current_contract = $contracts[0];
          foreach($contracts as $contract) {
            if ($contract->id == $id) {
              $current_contract = $contract;
            } else {
              $contracts_list[] = $contract;
            }
          }
          array_unshift($contracts_list, $current_contract);
          if (!$current_contract->enrolment_start_date && $current_contract->status < 7 && $current_contract->status > 0) {
            $contract_branch = $current_contract->branch_id;
            $product_id = $current_contract->product_id;
            if ($product_id) {
              $latest_contract_info = u::first("SELECT id, type, passed_trial, status, only_give_tuition_fee_transfer, start_date, end_date, count_recharge, program_label, enrolment_last_date, enrolment_last_date, COALESCE(enrolment_last_date, enrolment_last_date) latest_date, class_id, enrolment_start_date, enrolment_end_date, enrolment_schedule, DATE(updated_at) updated_at FROM contracts WHERE student_id = $current_contract->student_id AND ( type = 0 OR (type > 0 AND debt_amount > 0 AND count_recharge = 0)) AND status >= 0 ORDER BY id DESC limit 0, 1");
              $latest_date = date('Y-m-d');
              if ($latest_contract_info && isset($latest_contract_info->latest_date)) {
                $latest_date = $latest_contract_info->latest_date;
              } elseif (isset($latest_contract_info->end_date) && isset($latest_contract_info->status) && $latest_contract_info->status > 0) {
                $latest_date = $latest_contract_info->end_date;
              }
              if (isset($latest_contract_info->updated_at) && isset($latest_contract_info->status) && in_array((int)$latest_contract_info->status, [0, 7, 8])) {
                $latest_date = $latest_contract_info->updated_at;
              }
              $m = $latest_date && $latest_date != '0000-00-00' ? new \Moment\Moment($latest_date) : date('Y-m-d');
              $latest_date = $latest_date && $latest_date != '0000-00-00' ? $m->format('Y-m-d') : date('Y-m-d');
              $relation_info = (object)[
                'latest_date' => $latest_date,
                'latest_contract' => $latest_contract_info ? $latest_contract_info : null,
              ];
              $programs = u::query("SELECT x.name, x.id FROM (SELECT p.name, p.id, (SELECT COUNT(*) subs FROM programs WHERE parent_id = p.id) subs FROM programs p LEFT JOIN term_program_product t ON t.program_id = p.id LEFT JOIN semesters s ON p.semester_id = s.id WHERE p.status > 0 AND s.status > 0 AND t.status > 0 AND (CURRENT_DATE() BETWEEN s.start_date AND s.end_date) AND t.product_id = $product_id AND p.branch_id = $contract_branch ) x WHERE x.subs = 0 ORDER BY id");
            }
          }
        }
        $data->ecs = $ecs;
        $data->cms = $cms;
        $data->ccm = $ccm;
        $data->cec = $cec;
        $data->student = $student;
        $data->programs = $programs;
        $data->contracts = $contracts_list;
        $data->relation_info = $relation_info;
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contract = Contract::find($id);
        return response()->json($contract);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id, $id, $type)
    {
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();
      $user_data = $request->users_data;
      if ($user_id != 'undefined' && $id != 'undefined' && $user_id && $id) {
        $uid = $user_data->id;
        $check_leader = $type == 1 ? u::first("SELECT ec_id AS uid FROM contracts WHERE id = $id") : u::first("SELECT cm_id AS uid FROM contracts WHERE id = $id");
        $checkecid = $check_leader->uid;
        if ($checkecid != $user_id) {
          $get_leader_query = "SELECT u2.full_name FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $user_id";
          if ($type == 1) {
            u::query("UPDATE contracts SET editor_id = $uid, updated_at = NOW(), ec_id = $user_id, ec_leader_id = (SELECT u2.id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $user_id) WHERE id = $id");
          } elseif ($type == 2) {
            u::query("UPDATE contracts SET editor_id = $uid, updated_at = NOW(), program_id = $user_id WHERE id = $id");
          } elseif ($type == 3) {
            $info = json_decode($user_id);
            u::query("UPDATE contracts SET editor_id = $uid, updated_at = NOW(), start_date = '$info->start_date', end_date = '$info->end_date' WHERE id = $id");
          } else {
            u::query("UPDATE contracts SET editor_id = $uid, updated_at = NOW(), cm_id = $user_id, om_id = (SELECT u2.id FROM users AS u1 LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id WHERE u1.id = $user_id) WHERE id = $id");
          }
          if ($type == 3) {
            $data = (object) [];
            $info = json_decode($user_id);
            $data = $info;
          } elseif ($type == 2) {
            $program_name = u::first("SELECT `name` FROM programs WHERE id = $user_id");
            if ($program_name) {
              $program_name = isset($program_name->name) ? $program_name->name : '';
              $data = (object) [];
              $data->program_name = $program_name;
            }
          } else {
            $leader = u::first($get_leader_query);
            if ($leader) {
              $leader = isset($leader->full_name) ? $leader->full_name : '';
              $data = (object) [];
              $data->leader_name = $leader;
            }
          }
        }
      }
      return $response->formatResponse($code, $data);
    }

    public function remove(Request $request, $contract_id){
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($session = $request->users_data) {
        $data = (Object)[];
        $code = APICode::SUCCESS;
        $data = u::query("UPDATE contracts SET status = 0, editor_id = $session->id, updated_at = NOW() WHERE id = $contract_id");
      }
      return $response->formatResponse($code, $data);
    }

    public function quit(Request $request) {
      $code = APICode::SUCCESS;
      $data = null;
      $response = new Response();
      if ($post = (Object)$request->input()) {
        $session = $request->users_data;
        $contract_code = isset($post->code) ? $post->code : $post->id;
        if ($cid = (int)$post->id) {
          $data = (Object)['ok' => true];
          $contract = u::first("SELECT * FROM contracts WHERE id = $cid");
          $status = 0;
          if ((int)$contract->count_recharge > 0) {
            // $status = 7;
            $status = 8;
          }
          if ($contract->enrolment_start_date != '') {
            u::query("UPDATE contracts SET `status` = $status, editor_id = $session->id, updated_at = NOW() WHERE id = $cid");
          } else {
            u::query("UPDATE contracts SET `status` = $status, editor_id = $session->id, updated_at = NOW(), end_date = DATE(NOW()) WHERE id = $cid");
          }
          $data->code = $contract_code;
        }
      }
      return $response->formatResponse($code, $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $contract = Contract::find($id);
        // if ($contract->delete()){
        //     return response()->json("Successfully Deleted !");
        // }
        // return response()->json("Delete Fail");
    }

    public function getByBranch($class_id,$id){
      $contracts = Contract::groupBy('student_id')->having('branch_id', $id)->get();
      foreach ($contracts as $contract) {
        # code...
        $student = $contract->student;
        $ec = $contract->ec;
        $cm = $contract->cm;
        $tuiti = $contract->tuition;
        if (!$ec){
          $contract->ec_username = "";
        }
        else{

          $contract->ec_username = $ec->username;
        }
        if (!$cm){
          $contract->cm_username = "";
        }else{
          $contract->cm_username = $cm->username;

        }
        if (!$tuiti){
          $contract->tuition_name = "";
        }else{

          $contract->tuition_name = $tuiti->name;
        }
        $contract->student = $student;
      }

      return $contracts;
    }

    public function search(Request $request, $branch_id, $keyword) {
      $data = null;
      $code = APICode::PERMISSION_DENIED;
      $response = new Response();
      if ($keyword) {
        $keyword = trim($keyword);
        $keys = explode('-', $keyword);
        $key1 = '';
        $key2 = $keyword;
        if (count($keys) == 2) {
          $key1 = trim($keys[0]);
          $key2 = trim($keys[1]);
        }
        $query = '';
        if ($session = json_decode($request->authorized)) {
          $session = $request->users_data;
          $user_id = $session->id;
          $role_id = $session->role_id;
          $branches = $branch_id ? (int)$branch_id : $session->branches_ids;
          $where = "AND s.branch_id IN ($branches)";
          if ($role_id == ROLE_EC_LEADER) {
            $where.= " AND (u2.id = $user_id OR t.ec_id = $user_id)";
          }
          if ($role_id == ROLE_EC) {
            $where.= " AND t.ec_id = $user_id";
          }
          $key1 = $key1 ? trim($key1) : '';
          $key2 = $key2 ? trim($key2) : '';
          $where.= $key1 ? " AND ((s.id LIKE '%$key1%' OR s.crm_id LIKE '%$key1%') AND " : " AND ((s.id LIKE '%$keyword%' OR s.crm_id LIKE '%$keyword%') OR ";
          $where.= $key2 ? " s.name LIKE '$key2%' OR s.nick LIKE '$key2%' OR s.accounting_id LIKE '%$keyword%')" : ')';
          $queryNew = "SELECT
            s.id AS student_id,
            CONCAT(s.name, ' - ', s.crm_id) AS label
            FROM students AS s
            LEFT JOIN term_student_user AS t ON t.student_id = s.id
            LEFT JOIN users AS u1 ON t.ec_id = u1.id
            LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id
            WHERE s.id > 0  $where GROUP BY s.id ORDER BY `name` ASC LIMIT 0, 8";

          $query = "SELECT
            s.id AS student_id,
            CONCAT(s.name, ' - ', s.crm_id) AS label
            FROM students AS s
            LEFT JOIN term_student_user AS t ON t.student_id = s.id
            LEFT JOIN users AS u1 ON t.ec_id = u1.id
            LEFT JOIN users AS u2 ON u1.superior_id = u2.hrm_id
            WHERE s.id > 0 AND s.status >0 AND s.id NOT IN (
              SELECT student_id FROM contracts WHERE branch_id IN ( $branches ) AND count_recharge > - 1 AND `status` > 0 AND id NOT IN (SELECT c.id FROM contracts c LEFT JOIN students s ON c.student_id = s.id
              WHERE s.status > 0 AND c.id IN (SELECT max(id) FROM contracts WHERE student_id = s.id AND `status` > 0) AND c.status = 0 AND c.count_recharge > 0) GROUP BY student_id
              ) $where GROUP BY s.id ORDER BY `name` ASC LIMIT 0, 8";

          $data = u::query($query);
          $code = APICode::SUCCESS;
        }
      }
      return $response->formatResponse($code, $data);
    }

    public function suggest(Request $request, $key, $branch_id){
        $response = new Response();
        $query = "
            SELECT
                c.id contract_id,
                s.name student_name,
                s.type,
                s.id student_id,
                pr.name product_name,
                pro.name program_name,
                cls.cls_name class_name,
                c.enrolment_start_date start_date,
                c.enrolment_end_date end_date,
                c.id contract_id,
                IF(c.passed_trial = 1, c.`total_sessions`, c.`total_sessions` - 3) total_sessions,
                c.`receivable` receivable,
                tf.name tuition_fee_name,
                '' contract_name,
                c.product_id product_id,
                c.program_label,
                c.branch_id branch_id,
                c.class_id class_id
            FROM
                contracts c
                LEFT JOIN students s ON c.`student_id` = s.`id`
                LEFT JOIN payment p ON c.`id` = p.contract_id
                LEFT JOIN `tuition_transfer` t ON c.`student_id` = t.from_student_id
                LEFT JOIN products pr ON c.product_id = pr.id
                LEFT JOIN `tuition_fee` tf ON c.tuition_fee_id = tf.id
                LEFT JOIN classes cls ON c.class_id = cls.id
            WHERE
                c.`branch_id` = $branch_id
                AND p.debt = 0
                AND s.status > 0
                AND s.`type` = 0
                AND c.status > 0
                AND c.status < 7
                AND c.`type` = 1
                AND t.from_student_id IS NULL
        ";
        if($key && $key !== '_'){
            if(is_numeric($key)){
                $query .= " AND s.stu_id LIKE '%$key%'";
            }else{
                $query .= " AND s.name LIKE '%$key%'";
            }
        }

        $query .= " GROUP BY c.id";

//        var_dump($query);
        $res = DB::select(DB::raw($query));
        if(!empty($res)){
            foreach ($res as &$r){
                $r->contract_name = $r->cms_id . ' - ' . $r->student_name . ' - ' . $r->product_name . ' - ' . $r->tuition_fee_name;
            }
        }
        return $response->formatResponse(APICode::SUCCESS,$res);
    }

    public function getType()
    {
      $query = Contract::all();
      return response()->json($query);
    }

    public function downloadImportTemplate(Request $request)
    {
        $columns = [
            [
                'name' => "STT",
                'width' => 7,
                'value' => 1
            ],
            [
                'name' => "Mã trung tâm (*)",
                'width' => 20,
                'value' => 'C01'
            ],
            [
                'name' => "Mã học viên (Mã Khách hàng) (*)",
                'width' => 20,
                'value' => 'HV-00382'
            ],
            [
                'name' => "Mã học hợp đồng(*)",
                'width' => 20,
                'value' => 'C01.19.PNH.1778'
            ],
            [
                'name' => "Mã EC (*)",
                'width' => 20,
                'value' => 'KD0022'
            ],
            [
                'name' => "Loại khách hàng (0 = Trải nghiệm, 1 = chính thức) (*)",
                'width' => 20,
                'value' => 1
            ],
            [
                'name' => "Chỉ nhận chuyển phí (0 = no, 1 = yes) (*)",
                'width' => 20,
                'value' => 0
            ],
            [
                'name' => "Gói sản phẩm (*)",
                'width' => 20,
                'value' => 'UCREA'
            ],
            [
                'name' => "Chương trình học (*)",
                'width' => 20,
                'value' => 'Học trương trình UCREA'
            ],
            [
                'name' => "Gói học phí (*)",
                'width' => 20,
                'value' => 'UCREA03'
            ],
            [
                'name' => "Ngày đăng ký (YYYY-MM-DD) (*)",
                'width' => 20,
                'value' => '2018-04-13'
            ],
            [
                'name' => "Mã chiết khấu/giảm giá",
                'width' => 20,
                'value' => 'MCK'
            ],
            [
                'name' => "Số tiền chiết khấu",
                'width' => 20,
                'value' => 1000000
            ],
            [
                'name' => "Số tiền Voucher",
                'width' => 20,
                'value' => 0
            ],
            [
                'name' => "Số tiền chiết khấu Khác",
                'width' => 20,
                'value' => 0
            ],
            [
                'name' => "Chi tiết diễn giải (*)",
                'width' => 20,
                'value' => 'Chiết khấu (0%): 0đ
------------------------------
Giá Thực Thu: 5,100,000đ'
            ],
            [
                'name' => "Lớp học mong muốn",
                'width' => 20,
                'value' => 'Học lớp cơ bản'
            ],
            [
                'name' => "Ngày dự kiến học (YYYY-MM-DD) (*)",
                'width' => 20,
                'value' => '2019-06-05'
            ],
            [
                'name' => "Ghi Chú Nhập Học",
                'width' => 20,
                'value' => ''
            ],
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getRowDimension(1)->setRowHeight(50);
        foreach ($columns as $key => $value) {
            $colName = $key < 26 ? chr($key + 65) : 'A' . chr($key - 26 + 65);
            $sheet->setCellValue("{$colName}1", $value['name']);
            $sheet->setCellValue("{$colName}2", $value['value']);
            $sheet->getColumnDimension($colName)->setWidth($value['width']);
            ProcessExcel::styleCells($spreadsheet, "{$colName}1", "172270", "FFFFFF", 10, 0, 3, "center", "center", true);
            $sheet->getStyle("{$colName}1")->getAlignment()->setWrapText(true);
        }
        $writer = new Xlsx($spreadsheet);

        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="file_import_contract_template.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");

        } catch (Exception $exception) {
            throw $exception;
        }
        exit;
    }
    public function showEdit(Request $request, $id)
    {
      $code = APICode::PERMISSION_DENIED;
      $data = null;
      $response = new Response();
      $id = (int)$id;
      $session = $request->users_data;
      $role_id = $session->role_id;
      $branches = $session->branches_ids;
      $role_branch_ceo = ROLE_BRANCH_CEO;
      $role_region_ceo = ROLE_REGION_CEO;
      
      if ($id && $branches) {
        $code = APICode::SUCCESS;
        $data = (object) [];
        $student = u::first("SELECT s.id student_id,
        s.type,        
        s.name,        
        s.email,
        s.phone,        
        s.source,
        s.gender,
        s.cms_id,
        s.crm_id,
        s.facebook,
        s.school_grade,
        s.date_of_birth,
        COALESCE(s.gud_name1, s.gud_name2) parent_name,
        COALESCE(s.gud_mobile1, s.gud_mobile2) parent_mobile,
        COALESCE(s.gud_email1, s.gud_email2) parent_email,
        s.gud_email1 gud_email1,
        s.gud_email2 gud_email2,
        s.gud_name1 gud_name1,
        s.gud_mobile1 gud_mobile1,
        s.gud_name2 gud_name2,
        s.gud_mobile2 gud_mobile2,
        s.gud_birth_day1 gud_birthday1,
        s.gud_birth_day2 gud_birthday2,
        b.name branch_name,
        c.branch_id branch_id,
        z.name zone_name,
        s.address,
        s.school,
        c.product_id prod_id,
        c.coupon
        FROM contracts c
          LEFT JOIN students s ON c.student_id = s.id
          LEFT JOIN branches b ON c.branch_id = b.id
          LEFT JOIN zones z ON b.zone_id = z.id
        WHERE s.status > 0  AND c.student_id IN (SELECT student_id FROM contracts WHERE id = $id) LIMIT 0, 1");
        $list_contracts_query = "SELECT
        c.*,
        u1.full_name contract_ec_name,
        CONCAT(u1.hrm_id, '-', u1.username) contract_ec_code,
        CONCAT(u3.full_name, ' - ', u3.username) contract_cm_name,
        CONCAT(u1.hrm_id, '-', u1.username) contract_ec_code,
        u1.full_name contract_ec_name,
        IF(c.type IN (1,2,4,7,8) AND c.status IN (1,2,4,5) AND c.enrolment_start_date IS NULL, 1, 0) editable_program,
        IF(c.type <= 4 AND c.status > 0 AND c.status < 7 AND c.enrolment_start_date IS NULL, 1, 0) editable_start_date,
        (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE hrm_id = u1.superior_id LIMIT 0, 1) contract_ec_leader_name,
        (SELECT CONCAT(full_name, ' - ', username) FROM users WHERE hrm_id = u3.superior_id LIMIT 0, 1) contract_om_name,
        (SELECT CONCAT(u.full_name, ' - ', u.username) FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WHERE t.role_id = $role_branch_ceo AND t.branch_id = c.branch_id LIMIT 0, 1) contract_ceo_branch_name,
        (SELECT CONCAT(u.full_name, ' - ', u.username) FROM users u LEFT JOIN term_user_branch t ON t.user_id = u.id WHERE t.role_id = $role_region_ceo AND t.branch_id = c.branch_id LIMIT 0, 1) contract_ceo_region_name,
        r.name region_name,
        b.name branch_name,
        prd.id product_id,
        prd.name product_name,
        t.id tuition_fee_id,
        t.name tuition_fee_name,
        t.type tuition_fee_type,
        t.session tuition_fee_session,
        t.price tuition_fee_price,
        t.receivable tuition_fee_receivable
      FROM contracts c
        LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
        LEFT JOIN products prd ON c.product_id = prd.id
        LEFT JOIN branches b ON c.branch_id = b.id
        LEFT JOIN users u1 ON u1.id = c.ec_id
        LEFT JOIN users u3 ON u3.id = c.cm_id
        LEFT JOIN regions r ON r.id = b.region_id
      WHERE c.student_id = $student->student_id AND (c.type = 0 OR c.count_recharge = 0) AND c.branch_id IN ($branches) ORDER BY c.id DESC";
        $contracts = u::query($list_contracts_query);
        $tmp_ec_id = 0;
        if(!empty($contracts)){
            foreach ($contracts as $contract){
                $contract->discount_code = DiscountCode::where('code', '=', $contract->coupon)->first();
                $tmp_ec_id .=",".$contract->ec_id;
            }
        }
        $where_ecs = "t.role_id = ".ROLE_EC." OR t.user_id IN($tmp_ec_id)";
        $where_cms = "t.role_id = ".ROLE_CM;
        if ($role_id >= ROLE_BRANCH_CEO || $role_id == ROLE_EC_LEADER || $role_id == ROLE_OM) {
          $where_ecs = "(t.role_id = ".ROLE_EC." OR t.role_id = ".ROLE_EC_LEADER.")";
          $where_cms = "(t.role_id = ".ROLE_CM." OR t.role_id = ".ROLE_OM.")";
        }
        $cec = u::first("SELECT
            u1.`full_name` ec_name,
            u1.id ec_id
          FROM contracts c
            LEFT JOIN term_user_branch t ON c.branch_id = t.branch_id
            LEFT JOIN users u1 ON t.user_id = u1.id AND t.role_id = ".ROLE_EC."
          WHERE c.id = $id AND u1.id > 0 GROUP BY u1.id");
        $ecid = isset($cec->ec_id) ? (int)$cec->ec_id : 0;

        $ecs = u::query("SELECT
            u.`full_name` ec_name,
            u.id ec_id
          FROM users u
            LEFT JOIN term_user_branch t ON u.id = t.user_id
          WHERE u.id > 0 AND $where_ecs AND (t.branch_id IN ($branches) OR u.id = ".$ecid.") GROUP BY u.id");
          // $sqlz = "SELECT
          //   u1.`full_name` cm_name,
          //   u1.id cm_id
          // FROM contracts c
          //   LEFT JOIN term_user_branch t ON c.branch_id = t.branch_id
          //   LEFT JOIN users u1 ON t.user_id = u1.id AND t.role_id = ".ROLE_CM."
          // WHERE c.id = $id AND u1.id > 0 GROUP BY u1.id";
          // $ccm = u::first($sqlz);
          #
          // Cover
          $ccm = DB::table('contracts')->select('users.full_name as cm_name','users.id as cm_id')
                      ->leftJoin('term_user_branch','contracts.branch_id','=','term_user_branch.branch_id')
                      ->leftJoin('users',function($ljoin){
                          $ljoin->on('term_user_branch.user_id','=','users.id')
                          ->where('term_user_branch.role_id','=',ROLE_CM);
                      })
                      ->where('contracts.id','=',$id)->where('users.id','>',0)
                      ->groupBy('users.id')
                      ->first();

        $cmid = isset($ccm->ec_id) ? (int)$ccm->cm_id : 0;
        // $cms = u::query("SELECT
        //     u.`full_name` cm_name,
        //     u.id cm_id
        //   FROM users u
        //     LEFT JOIN term_user_branch t ON u.id = t.user_id
        //   WHERE u.id > 0 AND $where_cms AND (t.branch_id IN ($branches) OR u.id = ".$cmid.") GROUP BY u.id");
        $cms = DB::table('users')->select('users.full_name as cm_name','users.id as cm_id')
                                ->leftJoin('term_user_branch','users.id','=','term_user_branch.user_id')
                                ->where('users.id','>',0)
                                ->where(function($query) use ($role_id){
                                    if($role_id >= ROLE_BRANCH_CEO || $role_id == ROLE_EC_LEADER || $role_id == ROLE_OM){
                                        $query->where('term_user_branch.role_id','=',ROLE_CM)
                                            ->orWhere('term_user_branch.role_id','=',ROLE_OM);
                                    }else{
                                        $query->where('term_user_branch.role_id','=',ROLE_CM);
                                    }
                                })
                                ->where(function($query) use ($branches,$cmid){
                                    $query->whereRaw('term_user_branch.branch_id IN ('.$branches.')')
                                        ->orWhere('users.id','=',$cmid);
                                })
                                ->groupBy('users.id')
                                ->get();
        // return response()->json($cms);
        $programs = [];
        $relation_info = null;
        $contracts_list = [];
        if (count($contracts)) {
          $current_contract = $contracts[0];
          foreach($contracts as $contract) {
            if ($contract->id == $id) {
              $current_contract = $contract;
            } else {
              $contracts_list[] = $contract;
            }
          }
          array_unshift($contracts_list, $current_contract);
          if (!$current_contract->enrolment_start_date && $current_contract->status < 7 && $current_contract->status > 0) {
            $contract_branch = $current_contract->branch_id;
            $product_id = $current_contract->product_id;
            if ($product_id) {
              $latest_contract_info = u::first("SELECT id, type, passed_trial, status, only_give_tuition_fee_transfer, start_date, end_date, count_recharge, program_label, enrolment_last_date, enrolment_last_date, COALESCE(enrolment_last_date, enrolment_last_date) latest_date, class_id, enrolment_start_date, enrolment_end_date, enrolment_schedule, DATE(updated_at) updated_at FROM contracts WHERE student_id = $current_contract->student_id AND ( type = 0 OR (type > 0 AND debt_amount > 0 AND count_recharge = 0)) AND status >= 0 ORDER BY id DESC limit 0, 1");
              $latest_date = date('Y-m-d');
              if ($latest_contract_info && isset($latest_contract_info->latest_date)) {
                $latest_date = $latest_contract_info->latest_date;
              } elseif (isset($latest_contract_info->end_date) && isset($latest_contract_info->status) && $latest_contract_info->status > 0) {
                $latest_date = $latest_contract_info->end_date;
              }
              if (isset($latest_contract_info->updated_at) && isset($latest_contract_info->status) && in_array((int)$latest_contract_info->status, [0, 7, 8])) {
                $latest_date = $latest_contract_info->updated_at;
              }
              $m = $latest_date && $latest_date != '0000-00-00' ? new \Moment\Moment($latest_date) : date('Y-m-d');
              $latest_date = $latest_date && $latest_date != '0000-00-00' ? $m->format('Y-m-d') : date('Y-m-d');
              $relation_info = (object)[
                'latest_date' => $latest_date,
                'latest_contract' => $latest_contract_info ? $latest_contract_info : null,
              ];
              $programs = u::query("SELECT x.name, x.id FROM (SELECT p.name, p.id, (SELECT COUNT(*) subs FROM programs WHERE parent_id = p.id) subs FROM programs p LEFT JOIN term_program_product t ON t.program_id = p.id LEFT JOIN semesters s ON p.semester_id = s.id WHERE p.status > 0 AND s.status > 0 AND t.status > 0 AND (CURRENT_DATE() BETWEEN s.start_date AND s.end_date) AND t.product_id = $product_id AND p.branch_id = $contract_branch ) x WHERE x.subs = 0 ORDER BY id");
            }
          }
        }
        $data->ecs = $ecs;
        $data->cms = $cms;
        $data->ccm = $ccm;
        $data->cec = $cec;
        $data->student = $student;
        $data->programs = $programs;
        $data->contracts = $contracts_list;
        $data->relation_info = $relation_info;
        $query = "SELECT
          p.prod_code AS product_cms_id,
          p.`name` AS product_name,
          p.id AS product_id,
          t.session AS tuition_fee_session,
          t.price AS tuition_fee_price,
          t.discount AS tuition_fee_discount,
          t.receivable AS tuition_fee_receivable,
          t.id AS tuition_fee_id,
          t.type AS tuition_fee_type,
          t.`name` AS tuition_fee_name,
          t.`number_of_months`,
          t.price_min AS tuition_fee_price_min
        FROM
				  products AS p
				LEFT JOIN tuition_fee AS t ON t.product_id = p.id
				WHERE
				p.`status` > 0 AND t.`status` > 0
				AND (t.available_date <= CURDATE() AND t.expired_date >= CURDATE())
				AND (t.branch_id LIKE '$student->branch_id,%' OR t.branch_id LIKE '%,$student->branch_id,%' OR t.branch_id LIKE '%,$student->branch_id' OR t.branch_id = '$student->branch_id' )
				";

        $basedata = u::query($query);
        if ($basedata) {
          $products = [];
          $products_ids = [];
          foreach ($basedata as $item) {
            if (!in_array($item->product_id, $products_ids)) {
              $products_ids[] = $item->product_id;
              $products['product_id'.$item->product_id] = [
                'product_id'=>$item->product_id,
                'product_name'=>$item->product_name,
                'product_cms_id'=>$item->product_cms_id
              ];
            }
          }
          if ($products) {
            foreach ($products as $i => $product) {
              $tuition_fees = [];
              $tuition_fees_ids = [];
              foreach ($basedata as $item) {
                if ($item->product_id == $product['product_id'] && !in_array($item->tuition_fee_id, $tuition_fees_ids)) {
                  $tuition_fees_ids[] = $item->tuition_fee_id;
                  $tuition_fees['tuition_fee_id' . $item->tuition_fee_id] = [
                    'tuition_fee_id' => $item->tuition_fee_id,
                    'tuition_fee_type' => $item->tuition_fee_type,
                    'tuition_fee_name' => $item->tuition_fee_name,
                    'tuition_fee_price' => $item->tuition_fee_price,
                    'tuition_fee_price_min' => $item->tuition_fee_price_min,
                    'tuition_fee_session' => $item->tuition_fee_session,
                    'tuition_fee_discount' => $item->tuition_fee_discount,
                    'tuition_fee_receivable' => $item->tuition_fee_receivable,
                    'number_of_months' => $item->number_of_months
                  ];
                }
              }
              if ($tuition_fees) {
                $products['product_id' . $product['product_id']]['tuition_fees'] = $tuition_fees;
              }
            }
          }
        }
        $data->products = $products;
        $coupon_info = u::first("SELECT * FROM discount_codes WHERE code='$student->coupon'");
        $coupon_code = u::first("SELECT c.* FROM coupon_logs AS l LEFT JOIN coupons AS c ON c.id=l.coupon_id WHERE l.contract_id = $id");
        $data->coupon = $coupon_info;
      }
      return $response->formatResponse($code, $data);
    }
    public function updateEdit(Request $request,$id)
    {
        $code = APICode::SUCCESS;
        $data = null;
        $response = new Response();
        $post = $request->input();
        $user_data = $request->users_data;
        $contract_info = u::first("SELECT * FROM contracts WHERE id= $id");
        if ($post && $user_data) {
          $is_import = isset($request->is_import) ? $request->is_import : null;
          $user_id = (int)$user_data->id;
          $user_created = $is_import == 1 ? "NULL": $user_id;
          $student = isset($request->student) ? (object)$request->student : null;
          $contract = isset($request->contract) ? (object)$request->contract : null;
          $apis = isset($request->apis) ? (object)$request->apis : null;
          $shift = $contract->shift;
          $note = isset($contract->note) ? $contract->note : $contract->note;
          $note = str_replace(["'","\r\n",'"'],"",$note);
          $ref_code = isset($contract->ref_code) ? $contract->ref_code : '';
          $createdAt = !empty($contract->back_date) ? $contract->back_date.date(' H:i:s') : date('Y-m-d H:i:s');
          $student_id = (int)$student->student_id;
          $only_give_tuition_fee_transfer = (int)$contract->receive;
          $expected_class = $contract->expected_class;
          $contract_type = (int)$contract->customer_type;
          $coupon = trim(strtoupper(isset($contract->coupon) ? $contract->coupon: ''));
          $branch_id = (int)$student->branch_id;
          $product_id = (int)$contract->product_id;
          $tuition_fee_price = (int)str_replace('đ','',str_replace(',','',$contract->price));
          $after_discounted_fee = 0;
          $price_must_charge = 0;
          $sibling_discount = 0;
          $discount_value = 0;
          $tuition_fee_id = 0;
          $total_discount = 0;
          $total_sessions = 0;
          $real_sessions = 0;
          $payload_type = 0;
          $passed_trial = 1;
          $receivable = 0;
          $description = '';
          $bill_info = '';
          $start_date ='';
          $end_date ='';
          $real_sessions = $contract_type == 0 ? 1 : 0;
          $total_sessions = $contract_type == 0 ? 1 : (int)$contract->sessions;
          $bonus_sessions = (int)$request->bonus_sessions;
          $bonus_amount = (int)$request->bonus_amount;
          if(isset($contract->count_recharge)){
              $count_recharge = $contract->count_recharge;
          }else{
              $count_recharge = $contract_type == 0 ? -1 : 0;
          }
          $continue = 0;
          $program_label = "'$contract->program'";
          if ($only_give_tuition_fee_transfer == 0) {
            $continue = (int)$contract->continue;
            $start_date = $contract->start_date;
            $end_date = $contract->end_date;
            $payload_type = (int)$contract->payload;
            $tuition_fee_id = (int)$contract->tuition_fee;
            $price_must_charge =$student->type == 1 ? 0 : (int)$contract->must_charge_amount;
            $description = ada()->quote($contract->detail);
            // $bill_info = ada()->quote($contract->bill_info);
            $bill_info = ada()->quote(str_replace("\n","<br/>",$contract->detail));
            $receivable = (int)$contract->new_price_amount;
            // $total_discount = (int)$contract->total_discount;
            $total_discount = (int)$contract->total_voucher_other;
            $sibling_discount = (int)$contract->sibling;
            $discount_value = (int)$contract->point;
            $after_discounted_fee = (int)$contract->discounted_amount;
          } else {
            $start_date = $contract->start_date;
            $end_date = $start_date;
            $contract_type = 4;
          }
          if ($start_date == '0000-00-00' || !$start_date || $start_date == '') {
            $start_date = date('Y-m-d');
          }
          if ($end_date == '0000-00-00' || !$end_date || $end_date == '') {
            $m = new \Moment\Moment(strtotime($start_date));
            $classdays = [2, 5];
            if ($total_sessions) {
              $holidays = u::getPublicHolidays(0, $branch_id, $product_id);
              $schedule = u::calEndDate($total_sessions, $classdays, $holidays, $start_date);
              $end_date = $schedule->end_date;
            }
          }
          $strMd5 = $student_id.$contract_type.$start_date.$end_date.$product_id.$program_label.$tuition_fee_id.$branch_id;
          if($is_import === 1) {
              $strMd5.=$request->accounting_id;
          }
          
          $debt_amount = $price_must_charge;
          $reservable_sessions = 0;
          $reservable = 0;
          $tuition_fee_info = u::first("SELECT number_of_months FROM tuition_fee WHERE id=$tuition_fee_id");
          $status = (int)$student->type == 1 && $debt_amount == 0 ? 5 : 1;
          if ((int)$student->type && $contract->other == $contract->must_charge_amount) {
            $total_discount = $after_discounted_fee;
            $debt_amount = 0;
            $reservable_sessions = $tuition_fee_info && $tuition_fee_info->number_of_months >= 12 ? 8 : ($tuition_fee_info && $tuition_fee_info->number_of_months >= 6 ? 4 : 0);
            if($reservable_sessions){
                $reservable = 1;
            }
          }
          $summary_sessions = $real_sessions;
          if ($debt_amount == 0) {
            $status = 5;
            $contract_type = $contract_type > 0 ? 8 : 0;
            $real_sessions =0 ;
            $summary_sessions = $total_sessions;
            $bonus_sessions = $total_sessions;
            $reservable_sessions = $tuition_fee_info && $tuition_fee_info->number_of_months >= 12 ? 8 : ($tuition_fee_info && $tuition_fee_info->number_of_months >= 6 ? 4 : 0);
          }
          if ($only_give_tuition_fee_transfer == 1) {
            $status = 2;
            $contract_type = 4;
            $product_id = (int)$contract->product;
          }
          $temp_contract_info = u::first("SELECT * FROM contracts WHERE id=$id");
          DB::table('contracts')->where('id', $id)->update ([
            'type'=>$contract_type,
            'product_id'=>$product_id,
            'program_label'=>$program_label,
            'tuition_fee_id'=>$tuition_fee_id,
            'receivable'=>$reservable,
            'must_charge'=>$price_must_charge,
            'total_discount'=>$total_discount,
            'debt_amount'=>$debt_amount,
            'description'=>$description,
            'bill_info'=>$bill_info,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'total_sessions'=>$total_sessions,
            'real_sessions'=>$real_sessions,
            'expected_class'=>$expected_class,
            'passed_trial'=>$passed_trial,
            'status'=>$status,
            'created_at'=>$createdAt,
            'updated_at'=> NOW(),
            'editor_id'=>$user_created,
            'only_give_tuition_fee_transfer'=>$only_give_tuition_fee_transfer,
            'reservable'=>$reservable,
            'reservable_sessions'=>$reservable_sessions,
            'sibling_discount'=>$sibling_discount,
            'discount_value'=>$discount_value,
            'after_discounted_fee'=>$after_discounted_fee,
            'tuition_fee_price'=>$tuition_fee_price,
            'note'=>$note,
            'coupon'=>$coupon,
            'ref_code'=>$ref_code,
            'bonus_sessions'=> $bonus_sessions,
            'bonus_amount'=>$bonus_amount,
            'summary_sessions'=>$summary_sessions,
            'shift'=>$shift
          ]);

          DB::table('log_contracts_history')->where('contract_id', $id)->update ([
            'type'=>$contract_type,
            'product_id'=>$product_id,
            'program_label'=>$program_label,
            'tuition_fee_id'=>$tuition_fee_id,
            'receivable'=>$reservable,
            'must_charge'=>$price_must_charge,
            'total_discount'=>$total_discount,
            'debt_amount'=>$debt_amount,
            'description'=>$description,
            'bill_info'=>$bill_info,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'total_sessions'=>$total_sessions,
            'real_sessions'=>$real_sessions,
            'expected_class'=>$expected_class,
            'passed_trial'=>$passed_trial,
            'status'=>$status,
            'created_at'=>$createdAt,
            'updated_at'=> NOW(),
            'editor_id'=>$user_created,
            'only_give_tuition_fee_transfer'=>$only_give_tuition_fee_transfer,
            'reservable'=>$reservable,
            'reservable_sessions'=>$reservable_sessions,
            'sibling_discount'=>$sibling_discount,
            'discount_value'=>$discount_value,
            'after_discounted_fee'=>$after_discounted_fee,
            'tuition_fee_price'=>$tuition_fee_price,
            'note'=>$note,
            'coupon'=>$coupon,
            'ref_code'=>$ref_code,
            'bonus_sessions'=> $bonus_sessions,
            'bonus_amount'=>$bonus_amount,
            'summary_sessions'=>$summary_sessions,
            'shift'=>$shift
          ]);
          self::updatePayment($id);
          self::callCyberContract($id,$user_created);
          $contract_info = u::first("SELECT * FROM contracts WHERE id=$id");
          $log_meta_data = json_encode($temp_contract_info);
			    $log_change_data = json_encode($contract_info);
          $data = $student;
          u::query("INSERT INTO log_update_fee (contract_id,creator_id,created_at,meta_data,change_data)
				    VALUES ($id,$user_created,'$createdAt','$log_meta_data','$log_change_data')");
    }
      return $response->formatResponse($code, $data);
  }
  public function updatePayment($contract_id){
    $contract_info = u::first("SELECT must_charge, product_id, tuition_fee_id FROM contracts WHERE id=$contract_id");
    $list_payment = u::query("SELECT id, total FROM payment WHERE contract_id=$contract_id");
    $product_id = $contract_info->product_id;
    $tuition_fee_id = $contract_info->tuition_fee_id;
        $must_charge = $contract_info->must_charge;
    foreach($list_payment AS $payment){
      $debt_amount = $must_charge - $payment->total;
      u::query("UPDATE payment SET must_charge = $must_charge, debt = $debt_amount, tuition_fee_id = $tuition_fee_id
            WHERE id = $payment->id");
    }
    return true;
  }
  public static function callCyberContract($contract_id, $user_id){
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
                      c.bonus_amount,
                      c.total_charged,
                      c.accounting_id
              FROM contracts c LEFT JOIN students s ON c.student_id = s.id LEFT JOIN tuition_fee t ON c.tuition_fee_id = t.id
              WHERE c.id = $contract_id AND s.status > 0 ";
    $res = u::first($query);
    if($res->total_charged >0){
      $arr_tmp = explode(".",$res->accounting_id);
      $res->accounting_id = $arr_tmp[0].".".$arr_tmp[1].".".$arr_tmp[2].".".($res->id+900000);
    }
    $cyberAPI = new CyberAPI();
    $resp = $cyberAPI->createContract($res, $user_id, "Thêm mới hợp đồng",1);
    u::query("UPDATE contracts SET accounting_id = '$res->accounting_id' WHERE id= $res->id");
    u::query("UPDATE log_contracts_history SET accounting_id = '$res->accounting_id' WHERE contract_id= $res->id");
  }
  public function removeContract(Request $request,$contract_id){
    $contract_info =u::first("SELECT * FROM contracts WHERE id= $contract_id");
    if($contract_info){
      u::query("DELETE FROM contracts WHERE id=$contract_id");
      u::query("DELETE FROM log_contracts_history WHERE contract_id=$contract_id");
      u::query("DELETE FROM salehub_create_contract WHERE contract_id=$contract_id");
      DB::table('log_delete_contracts')->insert([
        'contract_id'=>$contract_id,
        'meta_data'=>json_encode($contract_info),
        'creator_id'=>$request->users_data->id,
        'created_at'=>date('Y-m-d H:i:s'),
      ]);
    }
    return "ok";
  }
}
