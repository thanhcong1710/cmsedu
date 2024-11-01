<?php

namespace App\Providers;
use Illuminate\Support\Facades\Route;
use App\Providers\UtilityServiceProvider as u;
use App\Models\Contract;
use Illuminate\Support\Facades\DB;
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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('provinces/list', 'ProvincesController@getList');
Route::get('district/list', 'DistrictsController@getList');
u::load(['authentication' => [
    'method' => 'post',
    'router' => 'login',
    'action' => 'submit'
  ],
  'products', 'books', 'discounts', 'roles', 'classes', 'ranks', 'shifts', 'sessions','reserves', 'branches', 'programs', 'regions', 'zones','ranks', 'contacts', 'semesters', 'termProgramProducts', 'tuitionFees','tuitionTransfers', 'customerCares','termStudentRanks','termTeacherBranches','termStudentUsers', 'termUserBranches', 'publicHolidays', 'provinces','schoolGrades', 'teachers', 'rooms', 'reasons', 'configs', 'scores', 'programCodes', 'pendingRegulations', 'discountsForms','qualities']);
Route::get('/all/roles', 'RolesController@getAll');
Route::get('/all/get-roles', 'RolesController@getSelectAll')->middleware('Authentication');
Route::get('role/get-role-for-user/{userId}','RolesController@getRoleForUser');
Route::get('/role/list', 'RolesController@getList');
Route::get('/role/{id}', 'RolesController@getRoleDetail');
Route::put('/role/{id}', 'RolesController@updateRole');
Route::put('/role/{id}/status', 'RolesController@updateRoleStatus');
Route::get('/migrate/contracts', 'MigrateController@getContracts');
Route::get('/products/{id}/program_codes', 'ProductsController@program_codes')->middleware('Authentication');
Route::get('scope/branch/{branch_name}/transfer/{excepted_branch_id}/{limited}', 'ScopeController@getBranch')->middleware('Authentication');
Route::get('scope/branch/{branch_name}', 'ScopeController@index')->middleware('Authentication');
// Provinces
Route::get('scope/test/func/{params}/{ses}/{date}', 'ScopeController@test')->middleware('Authentication');
Route::get('scope/test/transfer/{params}', 'ScopeController@transferTest')->middleware('Authentication');
Route::put('update_sessions/{class_id}','SessionsController@update')->middleware('Authentication');
/** Start Students Router ========================================================================================================== */
Route::get('students/report-by-date','StudentsController@reportByDate')->middleware('Authentication');
Route::get('students/export-by-date','StudentsController@exportByDate')->middleware('Authentication');
Route::get('students/export-studying','StudentsController@exportStudying');
Route::get('students/list/load/eccms/filter/{branch_id}', 'StudentsController@loadECCM')->middleware('Authentication');
Route::get('students/list/{pagination}/{search}/{sort}', 'StudentsController@list')->middleware('Authentication');
Route::get('students/suggest/{key}/{branch_id}', 'StudentsController@suggest')->middleware('Authentication');
Route::get('students/filter/{branch_id}', 'StudentsController@filter')->middleware('Authentication');
Route::get('students/sibling/{id}', 'StudentsController@sibling')->middleware('Authentication');
Route::get('students/detail/{id}', 'StudentsController@detail')->middleware('Authentication');
Route::post('students/check', 'StudentsController@checkStudentExist')->middleware('Authentication');
Route::get('students/check/{hash}/{id}', 'StudentsController@checkStudentExistEdit')->middleware('Authentication');
Route::get('students/check-phone-parent/{phone}', 'StudentsController@checkPhoneExist')->middleware('Authentication');
Route::get('students/check-phone-parent-edit/{phone}/{student_id}/{branch_id}', 'StudentsController@checkPhoneExistEdit')->middleware('Authentication');
Route::get('students/get/ec/of/users/branch', 'StudentsController@getECCMAndBranches')->middleware('Authentication');
Route::get('students/get/ec/of/a/branch/{id}', 'StudentsController@getUsersByBranch')->middleware('Authentication');
Route::get('students/{student_id}', 'StudentsController@detail')->middleware('Authentication');
Route::get('students/get-enrolment-history/{id}', 'StudentsController@getEnrolmentHistory')->middleware('Authentication');
Route::get('students/get-pending-history/{id}', 'StudentsController@getPendingHistoryList')->middleware('Authentication');
Route::get('students/{student_id}/contracts', 'StudentsController@getContractsByStudent')->middleware('Authentication');
Route::get('students/{student_id}/reserves', 'StudentsController@getReservesByStudent')->middleware('Authentication');
Route::get('students/{student_id}/class_transfers', 'StudentsController@getClassTransferHistoryList')->middleware('Authentication');
Route::post('students/update/trial/comment', 'StudentsController@updateTrialComment')->middleware('Authentication');
Route::get('students/detail-care/{id}', 'StudentsController@studentDetail');
Route::get('students/filter/{branch_id}', 'StudentsController@filter')->middleware('Authentication');
Route::get('students/{crm_id}/sibling', 'StudentsController@findSiblingByHrm')->middleware('Authentication');
Route::get('students/{st_name}/{st_gud_name}/{st_gud_mobile}', 'StudentsController@checkStudentExist')->middleware('Authentication');
Route::get('students/{id}/reserves', 'ReservesController@getReservesByStudent')->middleware('Authentication');
Route::get('students/{student_id}/get-charge-list-by-student', 'StudentsController@getChargeListByStudent')->middleware('Authentication');
Route::post('students/add', 'StudentsController@store')->middleware('Authentication');
Route::post('students/update/{id}', 'StudentsController@update')->middleware('Authentication');
Route::get('students/{id}/meta','StudentsController@metadata')->middleware('Authentication');
Route::post('students/check-edit-exist-information','StudentsController@checkEditExistInformation')->middleware('Authentication');
Route::get('students/count-students/{branch_id}','StudentsController@countStudents');
Route::get('students/detail-new/{id}','StudentsController@getDetail');

Route::get('student/branch/{id}', 'StudentsController@studentByBranch');
Route::get('get-student-update/{id}', 'StudentsController@getStudentUpdate')->middleware('Authentication');
/** End Students Router ============================================================================================================ */

Route::post('videos/post', 'ServicesController@storeVideo')->middleware('Authentication');
Route::post('notification/facebook', 'ServicesController@facebookNotification');
Route::post('users/reset/account/password', 'UsersController@resetPassword');
Route::post('users/update/account/password', 'UsersController@updatePassword')->middleware('Authentication');
Route::get('users/profile/information/{user_id}', 'UsersController@getUserInformation')->middleware('Authentication');

/** Start Contracts Router ========================================================================================================= */
Route::get('contracts/list/{pagination}/{search}/{sort}', 'ContractsController@list')->middleware('Authentication');
Route::get('contracts/list/list_waitcharged/{pagination}/{search}/{sort}', 'ContractsController@listWaitcharged')->middleware('Authentication');
Route::get('contracts/list/list_charged/{pagination}/{search}/{sort}', 'ContractsController@listCharged')->middleware('Authentication');
Route::get('contracts/list/list_waiting/{pagination}/{search}/{sort}', 'ContractsController@listWaiting')->middleware('Authentication');
Route::get('contracts/update/staff/{user_id}/{id}/{type}', 'ContractsController@update')->middleware('Authentication');
Route::get('contracts/filter/{branch_id}', 'ContractsController@filter')->middleware('Authentication');
Route::get('contracts/remove/{contract_id}', 'ContractsController@remove')->middleware('Authentication');
Route::get('contracts/products/{branch_id}/{student_id}', 'ContractsController@products')->middleware('Authentication');
Route::get('contracts/search/students/{branch_id}/{keyword}', 'ContractsController@search')->middleware('Authentication');
Route::get('contracts/load/student/{student_id}', 'ContractsController@loadStudent')->middleware('Authentication');
Route::get('contracts/{id}', 'ContractsController@show')->middleware('Authentication');
Route::get('contracts/ecs', 'ContractsController@ecs')->middleware('Authentication');
Route::post('contracts/add', 'ContractsController@store')->middleware('Authentication');
Route::post('contracts/quit', 'ContractsController@quit')->middleware('Authentication');
Route::get('type-student','ContractsController@getType')->middleware('Authentication');
/** End Contracts Router =========================================================================================================== */

/** Start Charges Router ========================================================================================================= */
Route::get('charges/list/{pagination}/{search}/{sort}', 'ChargesController@list')->middleware('Authentication');
Route::get('charges/students/{keyword}/{branch}', 'ChargesController@suggest')->middleware('Authentication');
Route::get('charges/quit/{id}', 'ChargesController@quit')->middleware('Authentication');
Route::get('charges/{id}', 'ChargesController@detail')->middleware('Authentication');
Route::post('charges/add', 'ChargesController@store')->middleware('Authentication');
/** End Charges Router =========================================================================================================== */

Route::post('payment/bycontract','PaymentController@getPaymentByContract')->middleware('Authentication');

/** Start Waitcharges Router ========================================================================================================= */
Route::get('waitcharges/list/{pagination}/{search}/{sort}', 'WaitchargesController@list')->middleware('Authentication');
Route::get('waitcharges/{id}', 'WaitchargesController@detail')->middleware('Authentication');
/** End Waitcharges Router =========================================================================================================== */

/** Start Enrolments Router ========================================================================================================= */
Route::get('enrolments/semesters', 'EnrolmentsController@loadSemesters')->middleware('Authentication');
Route::get('enrolments/contracts/{pagination}/{search}', 'EnrolmentsController@contracts')->middleware('Authentication');
Route::get('enrolments/classes/{branch_id}/{semester_id}', 'EnrolmentsController@loadClasses')->middleware('Authentication');
Route::get('enrolments/classes/all/{branch_id}/{semester_id}', 'EnrolmentsController@loadAllClasses')->middleware('Authentication');
Route::get('enrolments/programes/{branch_id}/{semester_id}', 'EnrolmentsController@loadprogram')->middleware('Authentication');
Route::get('enrolments/nick/{class_id}/{student_id}/{nick}', 'EnrolmentsController@checkNick')->middleware('Authentication');
Route::get('enrolments/class/{class_id}', 'EnrolmentsController@loadClassInfo')->middleware('Authentication');
Route::get('enrolments/classInfo/{class_id}', 'EnrolmentsController@loadClassDetail')->middleware('Authentication');
Route::get('enrolments/class/load/schedule/{class_id}', 'EnrolmentsController@loadSchedule')->middleware('Authentication');
Route::get('enrolments/class_extend/{class_id}', 'EnrolmentsController@loadClassExtendInfo')->middleware('Authentication');
Route::get('enrolments/withdraw/{id}', 'EnrolmentsController@withdraw')->middleware('Authentication');
Route::get('enrolments/branches', 'EnrolmentsController@loadBranches')->middleware('Authentication');
Route::get('enrolments/classes/semesters', 'EnrolmentsController@loadClassesSemesters')->middleware('Authentication');
Route::get('enrolments/semester/up/contracts/{search}', 'EnrolmentsController@semesterUpContracts')->middleware('Authentication');
Route::post('enrolments/add', 'EnrolmentsController@addContracts')->middleware('Authentication');
/** End Registers Router =========================================================================================================== */
Route::get('registers/classes/semesters', 'RegistersController@loadClassesSemesters')->middleware('Authentication');
Route::get('registers/classes/all/{branch_id}/{semester_id}', 'RegistersController@loadAllClasses')->middleware('Authentication');
Route::get('rooms/branch/{id}', 'RoomsController@getRoomsByBranch')->middleware('Authentication');
Route::put('program/add-ielts/{branch_id}', 'ProgramsController@addProgramIelts')->middleware('Authentication');
Route::get('shifts/branch/{branch_id}', 'ShiftsController@getShiftsByBranch')->middleware('Authentication');
Route::get('registers/class_extend/{class_id}', 'RegistersController@loadClassExtendInfo')->middleware('Authentication');
/** Start Attendance Router ========================================================================================================= */
Route::get('attendances/semesters/{branch_id}', 'AttendancesController@loadSemesters')->middleware('Authentication');
Route::get('attendances/contracts/{pagination}/{search}', 'AttendancesController@contracts')->middleware('Authentication');
Route::get('attendances/classes/{branch_id}/{semester_id}', 'AttendancesController@loadClasses')->middleware('Authentication');
Route::get('attendances/students/{class_id}/{date}', 'AttendancesController@getStudents')->middleware('Authentication');
Route::get('attendances-new/students/{class_id}/{date}', 'AttendancesController@getStudentsNew')->middleware('Authentication');
Route::get('attendances/classes/all/{branch_id}/{semester_id}', 'AttendancesController@loadAllClasses')->middleware('Authentication');
Route::get('attendances/programes/{branch_id}/{semester_id}', 'AttendancesController@loadprogram')->middleware('Authentication');
Route::get('attendances/nick/{class_id}/{student_id}/{nick}', 'AttendancesController@checkNick')->middleware('Authentication');
Route::get('attendances/class/{class_id}', 'AttendancesController@loadClassInfo')->middleware('Authentication');
Route::get('attendances/class/load/schedule/{class_id}', 'AttendancesController@loadSchedule')->middleware('Authentication');
Route::get('attendances/class_extend/{class_id}', 'AttendancesController@loadClassExtendInfo')->middleware('Authentication');
Route::get('attendances/withdraw/{id}', 'AttendancesController@withdraw')->middleware('Authentication');
Route::get('attendances/branches', 'AttendancesController@loadBranches')->middleware('Authentication');
Route::get('attendances/classes/semesters', 'AttendancesController@loadClassesSemesters')->middleware('Authentication');
Route::get('attendances/semester/up/contracts/{search}', 'AttendancesController@semesterUpContracts')->middleware('Authentication');
Route::post('attendances/save', 'AttendancesController@saveAttendances')->middleware('Authentication');
/** End Attendance Router =========================================================================================================== */

/** Start Report Router ====================================================================================================== */
Route::post('reports/form-01a1', 'ReportsController@report01a1')->middleware('Authentication');
Route::get('export/report01a1', 'ExelController@export01a1')->middleware('Authentication');
Route::get('full-fee-active/collect/{month}/{branches}', 'ReportsController@collectFullFeeActive');
Route::get('report-get-user/collect/{month}', 'ReportsController@collectReportGetUser');
Route::get('report-pending/collect/{month}', 'ReportsController@collectReportPending');
Route::get('report-reserve/collect/{month}', 'ReportsController@collectReportReserve');
Route::post('reports/form-01a', 'ReportsController@report01a')->middleware('Authentication');
Route::get('export/report01a', 'ExelController@export01a')->middleware('Authentication');
Route::post('reports/form-01b1', 'ReportsController@report01b1')->middleware('Authentication');
Route::get('export/report01b1', 'ExelController@export01b1')->middleware('Authentication');
Route::post('reports/form-01b2', 'ReportsController@report01b2')->middleware('Authentication');
Route::get('export/report01b2', 'ExelController@export01b2')->middleware('Authentication');
Route::post('reports/form-01b3', 'ReportsController@report01b3')->middleware('Authentication');
Route::get('export/report01b3', 'ExelController@export01b3')->middleware('Authentication');
Route::post('reports/form-02a', 'ReportsController@report02a')->middleware('Authentication');
Route::get('export/report02a', 'ExelController@export02a')->middleware('Authentication');
Route::post('reports/form-02b', 'ReportsController@report02b')->middleware('Authentication');
Route::get('export/report02b', 'ExelController@export02b')->middleware('Authentication');

Route::post('reports/form-04', 'ReportsController@report04')->middleware('Authentication');
Route::get('export/report04', 'ExelController@export04')->middleware('Authentication');
Route::post('reports/form-28', 'ReportsController@report28')->middleware('Authentication');
Route::get('export/report28', 'ReportsController@export28')->middleware('Authentication');
Route::post('reports/form-29', 'ReportsController@report29')->middleware('Authentication');
Route::get('export/report29', 'ExelController@export29')->middleware('Authentication');
Route::get('export/student-care', 'ExelExportController@studentCare');
Route::post('reports/r01', 'ReportsController@report_r01')->middleware('Authentication');
Route::get('export/report_r01', 'ExelController@report_r01')->middleware('Authentication');
Route::post('reports/r02', 'ReportsController@report_r02')->middleware('Authentication');
Route::get('export/report_r02', 'ExelController@report_r02')->middleware('Authentication');
Route::post('reports/r04', 'ReportsController@report_r04')->middleware('Authentication');
Route::get('export/report_r04', 'ExelController@report_r04')->middleware('Authentication');
Route::post('reports/r05', 'ReportsController@report_r05')->middleware('Authentication');
Route::get('export/report_r05', 'ExelController@report_r05')->middleware('Authentication');
Route::post('reports/r06', 'ReportsController@report_r06')->middleware('Authentication');
Route::get('export/report_r06', 'ExelController@report_r06')->middleware('Authentication');
Route::post('reports/r07', 'ReportsController@report_r07')->middleware('Authentication');
Route::get('export/report_r07', 'ExelController@report_r07')->middleware('Authentication');
Route::post('reports/r08', 'ReportsController@report_r08')->middleware('Authentication');
Route::get('export/report_r08', 'ExelController@report_r08')->middleware('Authentication');
Route::post('reports/r09', 'ReportsController@report_r09')->middleware('Authentication');
Route::get('export/report_r09', 'ExelController@report_r09')->middleware('Authentication');
Route::post('reports/r10', 'ReportsController@report_r10')->middleware('Authentication');
Route::get('export/report_r10', 'ExelController@report_r10')->middleware('Authentication');
Route::post('reports/r11', 'ReportsController@report_r11')->middleware('Authentication');
Route::get('export/report_r11', 'ExelController@report_r11')->middleware('Authentication');
Route::post('reports/r12', 'ReportsController@report_r12')->middleware('Authentication');
Route::get('export/report_r12', 'ExelController@report_r12')->middleware('Authentication');
Route::post('reports/r13', 'ReportsController@report_r13')->middleware('Authentication');
Route::get('export/report_r13', 'ExelController@report_r13')->middleware('Authentication');

Route::post('reports/form-02', 'ReportsController@reportForm02')->middleware('Authentication');
Route::post('reports/form-03', 'ReportsController@reportForm03')->middleware('Authentication');
Route::post('reports/form-05', 'ReportsController@reportForm05')->middleware('Authentication');
Route::post('reports/form-06', 'ReportsController@reportForm06')->middleware('Authentication');
Route::post('reports/form-07', 'ReportsController@reportForm07')->middleware('Authentication');
Route::post('reports/form-08', 'ReportsController@reportForm08')->middleware('Authentication');
Route::post('reports/form-09', 'ReportsController@reportForm09')->middleware('Authentication');
Route::post('reports/form-10', 'ReportsController@reportForm10')->middleware('Authentication');
Route::post('reports/form-11', 'ReportsController@reportForm11')->middleware('Authentication');
Route::post('reports/form-12', 'ReportsController@reportForm12')->middleware('Authentication');
Route::post('reports/form-13', 'ReportsController@reportForm13')->middleware('Authentication');
Route::post('reports/form-14', 'ReportsController@reportForm14')->middleware('Authentication');
Route::post('reports/form-15', 'ReportsController@reportForm15')->middleware('Authentication');
Route::post('reports/form-16', 'ReportsController@reportForm16')->middleware('Authentication');
Route::post('reports/form-17a', 'ReportsController@reportForm17a')->middleware('Authentication');
Route::post('reports/form-17b', 'ReportsController@reportForm17b')->middleware('Authentication');
Route::post('reports/form-17c', 'ReportsController@reportForm17c')->middleware('Authentication');
Route::post('reports/form-17d', 'ReportsController@reportForm17d')->middleware('Authentication');
Route::post('reports/form-18', 'ReportsController@reportForm18')->middleware('Authentication');
Route::post('reports/form-19', 'ReportsController@reportForm19')->middleware('Authentication');
Route::post('reports/form-20', 'ReportsController@reportForm20')->middleware('Authentication');
Route::get('reports/check-role', 'BranchesController@checkRole')->middleware('Authentication');
Route::post('reports/reserves', 'ReportsController@reserves')->middleware('Authentication');
Route::post('reports/registers', 'ReportsController@registers')->middleware('Authentication');
Route::post('reports/tuitiontransfers', 'ReportsController@tuitionTransfers')->middleware('Authentication');
Route::post('reports/renewals', 'ReportsController@renewals')->middleware('Authentication');
Route::post('reports/branchtransfer', 'ReportsController@branchTransfers')->middleware('Authentication');
Route::post('reports/students', 'ReportsController@students')->middleware('Authentication');
Route::post('reports/student-withdraw', 'ReportsController@studentWithdraw')->middleware('Authentication');
Route::get('reports/student-active', 'ReportsController@studentActive')->middleware('Authentication');
Route::get('export/student-active-by-date','ReportsController@exportStudentActiveByDate')->middleware('Authentication');
Route::get('export/tuition-fee','ReportsController@exportTuitionFeeByDate')->middleware('Authentication');
Route::post('reports/branch_info/{type}', 'ReportsController@getBranchesInfo')->middleware('Authentication');
Route::post('reports/form-30', 'ReportsController@form30')->middleware('Authentication');
Route::post('reports/form-31', 'ReportsController@form31')->middleware('Authentication');
Route::post('reports/form-32', 'ReportsController@form32')->middleware('Authentication');
Route::post('reports/form-33', 'ReportsController@report33')->middleware('Authentication');
Route::post('reports/form-34', 'ReportsController@report34')->middleware('Authentication');
Route::post('reports/form-34b2', 'ReportsController@report34b2')->middleware('Authentication');
Route::get('reports/tuition-fee', 'ReportsController@tuitionFee')->middleware('Authentication');
Route::get('reports/student-history', 'ReportsController@studentHistory')->middleware('Authentication');
Route::get('export/student-history', 'ReportsController@studentHistoryByDate')->middleware('Authentication');
Route::get('reports/tuition-percentage', 'ReportsController@percentage')->middleware('Authentication');
Route::get('export/tuition-percentage', 'ReportsController@exportPercentage')->middleware('Authentication');
Route::get('reports/semester-percentage', 'ReportsController@semester')->middleware('Authentication');
Route::get('export/semester-percentage', 'ReportsController@exportSemester')->middleware('Authentication');
Route::get('reports/student-renewals', 'ReportsController@studentRenewals')->middleware('Authentication');
Route::get('export/student-renewals', 'ReportsController@exportRenewals')->middleware('Authentication');
Route::get('reports/student-quantity-report', 'ReportsController@studentQuantityReport')->middleware('Authentication');
Route::get('export/student-quantity-report', 'ReportsController@studentQuantityExport')->middleware('Authentication');

/** End report Router ======================================================================================================= */

Route::get('recharges/search/students/{branch_id}/{keyword}', 'RechargesController@search')->middleware('Authentication');
Route::get('recharges/list/{pagination}/{search}/{sort}', 'RechargesController@list')->middleware('Authentication');
Route::get('recharges/list/list_waitcharged/{pagination}/{search}/{sort}', 'RechargesController@listWaitcharged')->middleware('Authentication');
Route::get('recharges/list/list_charged/{pagination}/{search}/{sort}', 'RechargesController@listCharged')->middleware('Authentication');
Route::get('recharges/list/list_waiting/{pagination}/{search}/{sort}', 'RechargesController@listWaiting')->middleware('Authentication');
Route::get('recharges/update/ec/{ec_id}/{id}', 'RechargesController@update')->middleware('Authentication');
Route::get('recharges/filter/{branch_id}', 'RechargesController@filter')->middleware('Authentication');
Route::get('recharges/remove/{contract_id}', 'RechargesController@remove')->middleware('Authentication');
Route::get('recharges/products/{branch_id}/{student_id}', 'RechargesController@products')->middleware('Authentication');
Route::get('recharges/{id}', 'RechargesController@show')->middleware('Authentication');
Route::get('recharges/ecs', 'RechargesController@ecs')->middleware('Authentication');
Route::post('recharges/add', 'RechargesController@store')->middleware('Authentication');

/** End Recharges Router =========================================================================================================== */

Route::get('logout', 'AuthenticationController@logout')->middleware('Authentication');
Route::resource('/tuition-fees', 'TuitionFeesController')->only('store', 'index', 'show', 'update', 'delete');

Route::get('contracts', 'ContractsController@index')->middleware('Authentication');
// Route::get('contracts/{id}', 'ContractsController@show');
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('users/pass/{pass}', 'UsersController@password');
Route::get('users/list/{page}/{search}', 'UsersController@list')->middleware('Authentication');
Route::post('users/{user_id}/update-users-profile','UsersController@updateProfile')->middleware('Authentication');
Route::post('users/{user_id}/update-users-history','UsersController@updateHistory')->middleware('Authentication');
Route::put('users/{user_id}/sale','UsersController@createEffectSale')->middleware('Authentication');

Route::get('user/{id}', 'UsersController@detail')->middleware('Authentication');
Route::post('user/upload', 'UsersController@upload')->middleware('Authentication');
Route::post('user/uploadExcel','UsersController@uploadExcel')->middleware('Authentication');
Route::get('user_excel/exportExcel','UsersController@exportUserToExcel');
Route::get('user_excel/downloadExample','UsersController@downloadExample');
Route::get('user_excel/downloadExampleQuit','UsersController@downloadExampleQuit');
Route::get('users/download-using-guide/{role_id}','UsersController@downloadUsingGuide');

Route::get('/all/products/', 'ProductsController@showAll');
Route::get('products/semester-id/{semester_id}', 'ProductsController@getProductBySemesterId');
Route::get('books/list/{page}/{search}/{filter}', 'BooksController@list')->middleware('Authentication');
Route::get('qualities/list/{page}/{search}/{filter}', 'QualitiesController@list')->middleware('Authentication');

Route::get('programCodes/list/{page}/{search}/{filter}', 'ProgramCodesController@list')->middleware('Authentication');
Route::get('zones/list/{page}/{search}/{filter}', 'ZonesController@list')->middleware('Authentication');
Route::get('rooms/lists/{page}', 'RoomsController@list')->middleware('Authentication');
Route::get('sessions/list/{page}/{search}/{filter}', 'SessionsController@list')->middleware('Authentication');
Route::get('pendingRegulations/list/{page}/{search}/{filter}', 'PendingRegulationsController@list')->middleware('Authentication');
Route::get('discounts/list/{page}/{search}/{filter}', 'DiscountsController@list')->middleware('Authentication');
Route::get('configs/list/{page}/{search}/{filter}', 'ConfigsController@list')->middleware('Authentication');
Route::get('reasons/list/{page}/{search}/{filter}', 'ReasonsController@list')->middleware('Authentication');

Route::get('program/child/{branch_id}', 'ProgramsController@programByChild')->middleware('Authentication');
Route::get('program/child/{branch_id}/semester/{semester_id}', 'ProgramsController@programByChild')->middleware('Authentication');


Route::get('/reserves', 'ReservesController@getList')->middleware('Authentication');
Route::get('/reserves/requests', 'ReservesController@getRequests');
Route::get('/reserves/suggest/{key}/{branch_id}', 'ReservesController@suggest')->middleware('Authentication');
Route::put('/reserves/{id}/first-approve', 'ReservesController@handleFirstApprove')->middleware('Authentication');
Route::put('/reserves/{id}/final-approve', 'ReservesController@handleFinalApprove')->middleware('Authentication');
Route::put('/reserves/{id}/deny', 'ReservesController@deny')->middleware('Authentication');
Route::get('/reserves/download-template-import', 'ReservesController@downloadTemplateImport');
Route::post('/reserves/validate-import', 'ReservesController@validateImport')->middleware('Authentication');
Route::post('/reserves/exec-import', 'ReservesController@executeImport')->middleware('Authentication');
Route::get('/reserves/{user_id}', 'ReservesController@getListByUserID');
Route::get('/reserves/edit/{id}', 'ReservesController@getReserveForEdit')->middleware('Authentication');
Route::post('/reserves/get-result-edit/{id}', 'ReservesController@getResultEdit')->middleware('Authentication');
Route::post('/reserves/save-edit/{id}', 'ReservesController@saveEdit')->middleware('Authentication');
Route::get('/reserves/cancel_reserve/{id}', 'ReservesController@cancelReserve')->middleware('Authentication');
Route::post('/reserves', 'ReservesController@create')->middleware('Authentication');
Route::get('/reserves/{id}/detail', 'ReservesController@detail')->middleware('Authentication');
Route::get('/reserves/info/{branch_id}', 'ReservesController@getAllData')->middleware('Authentication');
Route::get('/reserves/students/{class_id}', 'ReservesController@getStudentsForMultiReserve')->middleware('Authentication');
Route::post('/reserves/multiple', 'ReservesController@createMultiReserve')->middleware('Authentication');
Route::get('/info/{branch_id}/holidays','InfoController@getHolidays');
Route::get('/info/{class_id}/classdays','InfoController@getClassDays');
Route::get('/info/reasons/pendings','InfoController@getPendingReasons');

Route::get('/reserves-info/requests','ReservesController@getRequests')->middleware('Authentication');
Route::get('/reserves-info/print/{id}','ReservesController@getPrintData');
Route::get('/reserves-info/{id}','ReservesController@getListByUserID')->middleware('Authentication');

/*------------------Pending Routes--------------------------------------------------------*/
Route::get('/pendings', 'PendingsController@getList')->middleware('Authentication');
Route::get('/pendings/requests', 'PendingsController@getRequests');
Route::get('/pendings/suggest/{key}/{branch_id}', 'PendingsController@suggest')->middleware('Authentication');
Route::put('/pendings/{id}/approve', 'PendingsController@approve')->middleware('Authentication');
Route::put('/pendings/{id}/deny', 'PendingsController@deny')->middleware('Authentication');
Route::get('/pendings/{user_id}', 'PendingsController@getListByUserID');
Route::post('/pendings', 'PendingsController@create')->middleware('Authentication');
Route::get('/pendings-info/requests','PendingsController@getRequests')->middleware('Authentication');
Route::get('/pendings-info/{id}','PendingsController@getListByUserID');

// Route::get('classes/students/{id}', 'ClassesController@getStudentByClass');
Route::get('classes/{id}/enrolments', 'ClassesController@getEnrolmentByClass');
Route::get('classes_level/get_all_data_level', 'ClassesController@getAllDataLevel')->middleware('Authentication');

// Route::get('classes/program/{id}', 'ClassesController@getClassByProgram');
Route::get('classes/program/{program_id}/{branch_id}', 'ClassesController@getClassByProgramBranch')->middleware('Authentication');

Route::get('registers/get-students', 'EnrolmentsController@getStudents');
Route::get('programs/{id}/contracts', 'ProgramsController@getContractByProgram');
Route::get('/contracts/{class_id}/branch/{id}', 'ContractsController@getByBranch');

Route::get('/classes/{id}/teachers', 'TeachersController@getTeacherByClass');
Route::get('classes/get-nearest-school-day/{class_id}/{contract_id}', 'ClassesController@getNearestSchoolDayForContract');
Route::get('classes/get-nearest-school-day-of-contracts/{contract_ids}', 'ClassesController@getNearestSchoolDayForContracts');


// Route::get('programs/{id}/contracts', 'ProgramsController@getContractByProgram');
Route::get('/branch/paginate/', 'BranchesController@paginate');

Route::get('contracts', 'ContractsController@index')->middleware('Authentication');
Route::get('charges-students', 'ChargesController@getStudents')->middleware('Authentication');

Route::get('provinces/{id}/districts', 'ProvincesController@getDistrictByProvince');
Route::get('branches/{id}/ec', 'BranchesController@getEcByBranch');
Route::get('branches/{id}/cm', 'BranchesController@getCmByBranch');

Route::get('reports/branches', 'BranchesController@getBranches')->middleware('Authentication');


//get name and id product
Route::get('products/{id}/getIdNameProduct', 'ProductsController@getIdNameProduct');
Route::get('/products/{id}/tuitions/', 'ProductsController@getTuitions');
Route::get('tuitions/{id}/branches/', 'TuitionFeesController@getBranch');
Route::get('tuitions/select/{tuition_id}/load', 'SettingsController@loadInformation')->middleware('Authentication');

Route::get('withdrawals/search/students/{branch_id}/{key}', 'WithdrawalsController@searchStudent')->middleware('Authentication');
Route::get('withdrawals/all-contract/{student_id}','WithdrawalsController@getAllContractByStudent')->middleware('Authentication');
Route::post('withdrawals','WithdrawalsController@create')->middleware('Authentication');
Route::get('withdrawals','WithdrawalsController@getList')->middleware('Authentication');
Route::get('withdrawals/requests','WithdrawalsController@getRequest')->middleware('Authentication');
Route::get('withdrawals_refun/requests','WithdrawalsController@getRequestRefun')->middleware('Authentication');
Route::get('withdrawals/{id}','WithdrawalsController@getListByUserID')->middleware('Authentication');
Route::get('withdrawals/detail/{id}','WithdrawalsController@getDetail')->middleware('Authentication');
Route::put('withdrawals/{id}/approve','WithdrawalsController@approve')->middleware('Authentication');
Route::put('withdrawals/{id}/refun','WithdrawalsController@refun')->middleware('Authentication');
Route::put('withdrawals/{id}/deny','WithdrawalsController@deny')->middleware('Authentication');
Route::put('withdrawals/{id}/refun-deny','WithdrawalsController@refunDeny')->middleware('Authentication');
// Route::get('withdrawals/print/{id}', 'WithdrawalsController@getPrintData');
// Route::put('withdrawals/{id}','WithdrawalsController@update')->middleware('Authentication');

Route::get('tuition-transfers/list/{pagination}/{search}/{sort}','TuitionTransfersController@getList')->middleware('Authentication');
Route::get('tuition-transfers/suggest-sender/{key}/{branch_id}', 'TuitionTransfersController@suggestSender')->middleware('Authentication');
Route::get('tuition-transfers/contracts/sender/{student_id}', 'TuitionTransfersController@getAllSenderContracts')->middleware('Authentication');
Route::get('tuition-transfers/suggest-receiver/{key}/{branch_id}/{excepted_student_id}', 'TuitionTransfersController@suggestReceiver')->middleware('Authentication');
Route::get('tuition-transfers/contracts/receiver/{student_id}/{from_student_id}', 'TuitionTransfersController@getReceiversLatestContract')->middleware('Authentication');
Route::post('tuition-transfers/prepare-transfer-data','TuitionTransfersController@prepareTransferData')->middleware('Authentication');
Route::get('tuition-transfers/get_all_reasons', 'TuitionTransfersController@getAllReasons')->middleware('Authentication');
Route::post('tuition-transfers/save','TuitionTransfersController@storeTransfer')->middleware('Authentication');
Route::post('tuition-transfers/approve/store','TuitionTransfersController@approveTuitionTransfer')->middleware('Authentication');
Route::get('tuition-transfers/print/{id}', 'TuitionTransfersController@getPrintData');
//Route::delete('/tuition-transfers/{id}','TuitionTransfersController@delete')->middleware('Authentication');

Route::get('branch-transfers/list/{pagination}/{search}/{sort}','BranchTransfersController@getList')->middleware('Authentication');
Route::get('branch-transfers/suggest-passenger/{key}/{branch_id}', 'BranchTransfersController@suggestPassenger')->middleware('Authentication');
Route::get('branch-transfers/contracts/passenger/{student_id}', 'BranchTransfersController@getAllPassengersContracts')->middleware('Authentication');
Route::get('branch-transfers/passenger/check/{student_id}/{branch_id}', 'BranchTransfersController@checkToBranch')->middleware('Authentication');
Route::post('branch-transfers/prepare-transfer-data','BranchTransfersController@prepareTransferData')->middleware('Authentication');
Route::post('branch-transfers/save','BranchTransfersController@storeTransferedData')->middleware('Authentication');
Route::post('branch-transfers/approve/store','BranchTransfersController@approveBranchTransfer')->middleware('Authentication');
Route::get('branch-transfers/print/{branch_transfer_id}','BranchTransfersController@getPrintData')->middleware('Authentication');
//Route::delete('/branch-transfers/{id}','BranchTransfersController@delete')->middleware('Authentication');

Route::get('/class-transfers','ClassTransfersController@getList')->middleware('Authentication');
Route::get('/class-transfers/suggest/{key}/{branch_id}/{type}', 'ClassTransfersController@suggestStudent');
Route::get('/class-transfers/{branch_id}/info/trial','ClassTransfersController@getAllInfoTrial');
Route::get('/class-transfers/{branch_id}/info','ClassTransfersController@getAllInfo');
Route::get('/class-transfers/extend/{start}/{end}/{class_id}','ClassTransfersController@getClassExtendInfo');
Route::get('/class-transfers/contracts/{contract_id}', 'ClassTransfersController@getContract');
Route::get('/class-transfers/print/{id}', 'ClassTransfersController@getPrintData');
Route::put('/class-transfers/{id}/approve/{mode?}','ClassTransfersController@approve')->middleware('Authentication');
Route::put('/class-transfers/{id}/deny','ClassTransfersController@deny')->middleware('Authentication');
Route::get('/class-transfers/requests','ClassTransfersController@getRequest')->middleware('Authentication');
Route::get('/class-transfers/detail/{id}','ClassTransfersController@getDetail');
Route::get('/class-transfers/{id}','ClassTransfersController@getListByUserID');
Route::post('/class-transfers','ClassTransfersController@create')->middleware('Authentication');
Route::put('/class-transfers/{id}','ClassTransfersController@update')->middleware('Authentication');
Route::get('/class-transfers/get-student/{class_id}','ClassTransfersController@getStudent')->middleware('Authentication');
Route::post('/class-transfers/add-multi-student','ClassTransfersController@createMulti')->middleware('Authentication');
Route::post('/class-transfers/check-multi-student','ClassTransfersController@checkMulti')->middleware('Authentication');
//Route::delete('/class-transfers/{id}','ClassTransfersController@delete')->middleware('Authentication');

// Route::post('/lms/recall','LMSAPIController@reCallAPI')->middleware('Authentication');
// Route::post('/lms/{mode?}','LMSAPIController@callAPI')->middleware('Authentication');
// Route::post('/effect/recall','EffectAPIController@reCallAPI')->middleware('Authentication');
// Route::post('/effect','EffectAPIController@callAPI')->middleware('Authentication');
Route::post('/golang','golangAPIController@callAPI')->middleware('Authentication');

Route::get('/semesters-listing/current', 'SemestersController@current')->middleware('Authentication');
Route::get('/semesters-listing/obsolete', 'SemestersController@obsolete')->middleware('Authentication');

Route::get('shifts/list/{page}/{search}/{filter}', 'ShiftsController@list')->middleware('Authentication');
Route::get('teachers/lists/{page}', 'TeachersController@list')->middleware('Authentication');
Route::get('teachers/export/exportExcel', 'TeachersController@exportExcel');
Route::get('reasons/list/{page}/{search}/{filter}', 'ReasonsController@list')->middleware('Authentication');
Route::get('semesters/list/{page}/{search}/{filter}', 'SemestersController@list')->middleware('Authentication');
Route::get('schoolGrades/list/{page}/{search}/{filter}', 'SchoolGradesController@list')->middleware('Authentication');
Route::get('zones/{id}/branches', 'ZonesController@getBranch');

Route::get('scores/list/{page}/{search}/{filter}', 'ScoresController@list')->middleware('Authentication');
Route::get('ranks/list/{page}/{search}/{filter}', 'RanksController@list')->middleware('Authentication');
Route::get('products/list/{page}', 'ProductsController@list')->middleware('Authentication');
Route::get('publicHolidays/list/{page}/{search}/{filter}', 'PublicHolidaysController@list')->middleware('Authentication');
Route::get('contacts/list/{page}/{search}/{filter}', 'ContactsController@list')->middleware('Authentication');
Route::get('branches/list/{page}/{search}/{filter}', 'BranchesController@list')->middleware('Authentication');
Route::get('regions/list/{page}/{search}/{filter}', 'RegionsController@list')->middleware('Authentication');

Route::get('holyday/{holyday_id}', 'PublicHolidaysController@destroy')->middleware('Authentication');

Route::get('programs/{program_id}/term', 'ProgramsController@getTerm')->middleware('Authentication');

Route::get('/all/programs/{branch?}/{product?}', 'ProgramsController@showAll');
Route::get('get-all/regions/get-all-regions-list', 'RegionsController@getAllRegionsList')->middleware('Authentication');
Route::get('get-all/zones/get-all-zones-list', 'ZonesController@getAllZonesList')->middleware('Authentication');


//Report extract routes
Route::get('roles/{role_id}/get-all-regions', 'RegionsController@getAll')->middleware('Authentication');
Route::get('get-regions-by-role', 'RegionsController@getAllRegions')->middleware('Authentication');


Route::get('contracts/suggest/{key}/{branch_id}', 'ContractsController@suggest');
Route::get('zones/{zone_id}/get-branches', 'ZonesController@getBranchesByZone');

Route::get('exel/test/{branch?}/{product?}/{program?}/{rank?}', 'ExelController@test');
Route::get('exel/print-report-bc01a/{branch?}/{fromDate?}/{toDate?}', 'ExelController@report_acs');
Route::get('exel/print-report-bc01b1/{branch?}/{product?}/{program?}/{fromDate}/{toDate}', 'ExelController@report_renew_detail');
Route::get('exel/print-report-bc01b2/{branch?}/{zones?}/{regions?}/{fromDate}/{toDate}', 'ExelController@report_renew_general');
Route::get('exel/print-report-bc02/{branch?}/{product?}/{program?}/{fromDate?}/{toDate?}', 'ExelController@reportBC02');
Route::get('exel/print-report-bc03/{branch}/{product?}/{program?}/{fromDate?}/{toDate?}', 'ExelController@reportBC03');
Route::get('exel/print-report-bc04/{branch?}/{fromDate?}/{toDate?}', 'ExelController@reportBC04');
Route::get('exel/print-report-bc05/{branch?}/{product?}/{program?}', 'ExelController@reportBC05');
Route::get('exel/print-report-bc06/{data}', 'ExelController@report_total_student');
Route::get('exel/print-report-bc07/{branch?}/{product?}/{program?}/{fromDate?}/{toDate?}', 'ExelController@reportBC07');
Route::get('exel/print-report-bc08/{branch?}/{product?}/{program?}/{fromDate?}/{toDate?}', 'ExelController@reportBC08');
Route::get('exel/print-report-bc09/{branch}/{product?}/{program?}/{fromDate?}/{toDate?}', 'ExelController@report_withdraw');
Route::get('exel/print-report-bc10/{branch}/{product?}/{program?}/{customerType?}/{fromDate?}/{toDate?}/{type?}', 'ExelController@report_bc10');
Route::get('exel/print-report-bc10b/{branch}/{product?}/{program?}/{customerType?}/{fromDate?}/{toDate?}/{type?}', 'ExelController@reportBC10');
Route::get('exel/print-report-bc11/{branch}/{product}/{program?}/{customerType?}/{fromDate?}/{toDate?}', 'ExelController@reportBC11');
Route::get('exel/print-report-bc12/{branch}/{product}/{program?}/{customerType?}/{fromDate?}/{toDate?}', 'ExelController@reportBC12');
Route::get('exel/print-report-bc13/{data}', 'ExelController@reportBC13');
Route::get('exel/print-report-bc14/{data}', 'ExelController@reportBC14');
Route::get('exel/print-report-bc15/{branch?}/{product?}/{program?}', 'ExelController@reportBC15');
Route::get('exel/print-report-bc16/{data}', 'ExelController@reportBC16');
Route::get('exel/print-report-bc17a/{region}/{fromDate?}/{toDate?}', 'ExelController@reportBC17a');
Route::get('exel/print-report-bc17b/{branch}/{fromDate?}/{toDate?}/{best?}/{worst?}', 'ExelController@reportBC17b');
Route::get('exel/print-report-bc17c/{branch}/{fromDate?}/{toDate?}/{bestLeader?}/{worstLeader}/{bestStaff?}/{worstStaff?}', 'ExelController@reportBC17c');
Route::get('exel/print-report-bc17d/{branch}/{fromDate?}/{toDate?}/{bestLeader?}/{worstLeader}/{bestStaff?}/{worstStaff?}', 'ExelController@reportBC17d');
Route::get('exel/print-report-bc18/{branch}/{product?}/{program?}/{fromDate?}/{toDate?}', 'ExelController@reportBC18');
Route::get('exel/print-report-bc19/{branch?}/{num?}/{date?}', 'ExelController@reportBC19');
Route::get('exel/print-report-bc20/{branch?}/{fromDate?}/{toDate?}', 'ExelController@reportBC20');
Route::get('exel/print-report-bc20b/{branch?}/{fromDate?}/{toDate?}', 'ExelController@reportBC20b');
Route::get('exel/export-students/{branch_id}', 'ExelController@exportStudents');
Route::post('exel/export-students', 'ExelController@exportStudent')->middleware('Authentication');
Route::get('exel/public-holidays', 'ExelController@exportHoliday');
Route::get('exel/print-student-care/{params}', 'ExelController@exportStudentCares');

/** Start API Service Router ========================================================================================================== */
Route::get('sqlserver-get-demo', 'SqlserversController@index');
Route::get('daily-checking-withdraw-status', 'ServicesController@checkWithdrawStatus');
/** End API Service Router ============================================================================================================ */

Route::get('dashboards/', 'DashboardController@getDataAll')->middleware('Authentication');
Route::get('dashboards/invisible/{invisible}', 'DashboardController@getByInvisible')->middleware('Authentication');
Route::get('dashboards/topbranch/{rank}/{rows}', 'DashboardController@getTopBranch')->middleware('Authentication');
Route::get('dashboards/topleadear/{rank}/{rows}', 'DashboardController@topLeader')->middleware('Authentication');
Route::get('dashboards/topsale/{rank}/{rows}', 'DashboardController@topSale')->middleware('Authentication');
Route::get('dashboards/renew/{month?}/{year?}', 'DashboardController@getStudentRenew')->middleware('Authentication');
Route::get('dashboards/renew/{branch_id?}/{month?}/{year?}', 'DashboardController@getStudentRenewDetail')->middleware('Authentication');
Route::get('dashboards/students_attendances', 'DashboardController@getStudentAttendances')->middleware('Authentication');
Route::get('branch/role/', 'BranchesController@index')->middleware('Authentication');
Route::get('dashboards/get-students-status-monthly/{branch_id}', 'DashboardController@getStudentsStatusMonthly')->middleware('Authentication');
Route::get('dashboards/get-students-status-monthly-overview', 'DashboardController@getStudentsStatusMonthlyOverview')->middleware('Authentication');

Route::get('branch/renew/{month?}/{year?}', 'BranchesController@getStudentRenew')->middleware('Authentication');
Route::get('students/{id}/ranks', 'TermStudentRanksController@studentRankHistory');
Route::get('student/pending-approval/{id}', 'StudentsController@pendingApproval')->middleware('Authentication');

Route::post('termStudentRanks/add', 'TermStudentRanksController@store')->middleware('Authentication');
Route::get('cares/list/{page}/{search}/{filter}', 'CustomerCaresController@list')->middleware('Authentication');
Route::get('cares/search/students/{branch_id}/{key}', 'CustomerCaresController@suggest')->middleware('Authentication');
Route::get('cares/report-by-date', 'CustomerCaresController@reportByDate')->middleware('Authentication');
Route::get('cares/by-crm-id/{id}', 'CustomerCaresController@byCrmId')->middleware('Authentication');

Route::get('branches/{id}/user-care', 'BranchesController@getUserCare')->middleware('Authentication');
Route::get('contact/allmethod/', 'ContactsController@getallmethod');
Route::get('branch/renew/{branch_id}/{month?}/{year?}', 'BranchesController@getStudentRenewDetail')->middleware('Authentication');
// Route::post('insert-log-student-update', 'StudentsController@insertLogStudentUpdate');

/** Start Settings Router ========================================================================================================== */
Route::get('settings/exchange/{tid}/{amount}/{bid}/{pid}', 'SettingsController@tuitionFeeExchangeForTuitionTransfer');
Route::get('settings/exchange-class-transfer/{tid}/{amount}/{bid}/{pid}/{sessions}', 'SettingsController@tuitionFeeExchangeForClassTransfer');
Route::get('settings/exchange/{tid}/{amount}/{bid}/{pid}/{sessions}', 'SettingsController@tuitionFeeExchange');
Route::get('settings/tuitions/select/product/{product_id}', 'SettingsController@loadProductInformation')->middleware('Authentication');
Route::get('settings/branches/config/default', 'SettingsController@branchesConfig')->middleware('Authentication');
Route::get('settings/tuitions/config/default', 'SettingsController@tuitionsConfig')->middleware('Authentication');
Route::get('settings/holidays/{class_id}/{product_id}', 'SettingsController@holidays')->middleware('Authentication');
Route::get('settings/holidates/{class_id}/{product_id}', 'SettingsController@holidates')->middleware('Authentication');
Route::get('settings/holidays/v2/{zone_id}/{product_id}', 'SettingsController@holidays_v2')->middleware('Authentication');
Route::get('settings/branch/class/{branch_id}', 'SettingsController@classes')->middleware('Authentication');
Route::get('settings/branches/{filter}', 'SettingsController@searchBranches')->middleware('Authentication');
Route::get('settings/cms/{branch_id}', 'SettingsController@cms')->middleware('Authentication');
Route::get('settings/branches', 'SettingsController@branches')->middleware('Authentication');
Route::get('settings/tools/get/{id}', 'SettingsController@tools')->middleware('Authentication');
Route::post('settings/test', 'SettingsController@test')->middleware('Authentication');
Route::get('settings/exchange-v2/{tid}/{amount}/{bid}/{pid}/{sessions}', 'SettingsController@tuitionFeeExchangeV2');
/** End Settings Router ============================================================================================================ */

/** Router for Teacher **/
Route::get('teachers/branch/{id}', 'TeachersController@getTeachersByBranch');
Route::get('branches/{id}/teachers', 'TeachersController@getTeacherListByBranch')->middleware('Authentication');

/** Router for Classes */
Route::post('classes/update/cm','ClassesController@updateCm')->middleware('Authentication');
Route::post('classes/bycontract','ClassesController@getClassByContract')->middleware('Authentication');

/** Issues Routes Teacher **/
Route::get('issues/scores/list', 'IssuesController@getScoresList')->middleware('Authentication');
Route::get('issues/books/list', 'IssuesController@getBooksList')->middleware('Authentication');
Route::get('issues/{id}/detail', 'IssuesController@show')->middleware('Authentication');
Route::get('issues/{id}/add', 'IssuesController@getStudentIssue')->middleware('Authentication');
Route::get('issues/{id}/edit', 'IssuesController@edit')->middleware('Authentication');
Route::post('issues', 'IssuesController@store')->middleware('Authentication');
Route::delete('issues/{id}', 'IssuesController@destroy')->middleware('Authentication');
Route::put('issues/{id}', 'IssuesController@update')->middleware('Authentication');
Route::get('issues/class/{class_id}', 'IssuesController@loadClassInfo')->middleware('Authentication');
Route::get('issues/print/{id}', 'IssuesController@getStudentIssueById');
Route::get('issues/mail/{student_id}/{id}', 'IssuesController@sendMail')->middleware('Authentication');
Route::get('issues/{student_id}/issue', 'IssuesController@getIssueByStudent')->middleware('Authentication');
Route::get('issues/{student_id}/issue-list', 'IssuesController@getIssueListByStudent')->middleware('Authentication');
Route::get('issues/{book_id}/books/check-available-book', 'IssuesController@checkAvailableBook')->middleware('Authentication');
Route::get('termstudent/{student_id}/rank', 'IssuesController@getTermByStudent')->middleware('Authentication');
Route::get('issues/contracts/{pagination}/{search}', 'IssuesController@contracts')->middleware('Authentication');
Route::get('issues/classes/{branch_id}/{semester_id}', 'IssuesController@loadClasses')->middleware('Authentication');
Route::get('issues/class/{class_id}', 'IssuesController@getClassInfo')->middleware('Authentication');
Route::get('issues/branches', 'IssuesController@loadBranches')->middleware('Authentication');
Route::get('issues/semesters', 'IssuesController@loadSemesters')->middleware('Authentication');
Route::get('issues/{student_id}/check-issue', 'IssuesController@checkIssueExist')->middleware('Authentication');
Route::get('issues/students/{class_id}/{date}', 'IssuesController@getStudentsByClass')->middleware('Authentication');
Route::get('issues/students/{class_id}', 'IssuesController@getStudentsByClass')->middleware('Authentication');

/** Router Feedback **/
Route::post('feedback', 'IssuesController@createFeedback')->middleware('Authentication');
Route::put('feedback/{id}', 'IssuesController@updateFeedback')->middleware('Authentication');
Route::get('feedback/{id}', 'IssuesController@getFeedback')->middleware('Authentication');
Route::get('feedback/print/{class_id}/{student_id}/{date}', 'IssuesController@getPrintFeedback')->middleware('Authentication');

Route::get('holidays/start', 'PublicHolidaysController@getStart')->middleware('Authentication');
Route::get('holidays/filter/{search}', 'PublicHolidaysController@filter')->middleware('Authentication');

/** Router ScoringGuidelines **/
Route::get('scoring_guidelines/', 'ScoringGuidelinesController@lists')->middleware('Authentication');
Route::get('scoring_guidelines/{id}', 'ScoringGuidelinesController@detail')->middleware('Authentication');
Route::put('scoring_guidelines/edit', 'ScoringGuidelinesController@update')->middleware('Authentication');
Route::post('scoring_guidelines/add', 'ScoringGuidelinesController@create')->middleware('Authentication');
Route::delete('scoring_guidelines/{id}', 'ScoringGuidelinesController@remove')->middleware('Authentication');
Route::get('scoring_guidelines/list/{page}/{search}/{filter}', 'ScoringGuidelinesController@list')->middleware('Authentication');

/** Router Student TransferFee **/
Route::post('trasfer_fee/student/new','TransferFeeController@toNewStudent')->middleware('Authentication');
Route::post('trasfer_fee/student/old','TransferFeeController@toOldStudent')->middleware('Authentication');
// Router services cronjob
Route::get('auto-update-count_days-in-school/', 'AutoCountDaysInSchoolController@updateCountDaysInSchool');
Route::get('regions/{ids}/branches', 'BranchesController@getAllBranches');
Route::get('services/check_enrol_fld','ServicesController@checkEnrolByFinalLastDate');
Route::get('services/update-learning-time/{student_id}/{branch_id}','ServicesController@updateLearningTime')->middleware('Authentication');
Route::get('services/recalculate/{branch_id}', 'ServicesController@reCalcLearningTime')->middleware('Authentication');
route::get('services/eff_top_sales/{date}','ServicesController@getEffTopSales');
route::get('services/eff_top_products/{date}','ServicesController@getEffTopProducts');
Route::get('services/dashboard/getDashboard/{date}','ServicesController@getEffDashboard');

/** Ec of branch */
Route::get('users/get-ec-cm-list', 'BranchesEcController@index')->middleware('Authentication');

/** Router Manager TransferFee **/
Route::get('users/{branch}/get-ec-cm-list', 'ManagerTransfersController@getEcCmList')->middleware('Authentication');
Route::get('users/{branch_id}/get-new-ec-cm-list', 'ManagerTransfersController@getNewEcCmList')->middleware('Authentication');
Route::get('users/{branch}/{ec}/get-new-ec-list', 'ManagerTransfersController@getNewEcList')->middleware('Authentication');
Route::post('users/get-new-ec-list', 'ManagerTransfersController@getNewManagerList')->middleware('Authentication');
Route::get('users/{branch}/{cm}/get-new-cm-list', 'ManagerTransfersController@getNewCmList')->middleware('Authentication');
Route::post('users/get-student-list-by-user', 'ManagerTransfersController@getStudentListByUser')->middleware('Authentication');
Route::post('users/transfer-manager', 'ManagerTransfersController@transferManager')->middleware('Authentication');
Route::get('users/get-users-list', 'UsersController@getUsersList')->middleware('Authentication');
Route::get('users/get-users-management', 'UsersController@getUsersManagement')->middleware('Authentication');
Route::get('users/{id}/get-user-info', 'UsersController@getUserInfo')->middleware('Authentication');
Route::post('users/save-user-info', 'UsersController@saveUserInfo')->middleware('Authentication');
Route::post('users/{id}/update-user-info', 'UsersController@updateUserInfo')->middleware('Authentication');
Route::post('users/search-users-multi-keyword', 'UsersController@search')->middleware('Authentication');
// Route::get('users/{branch}/get-cm-list', 'ManagersController@getCmList')->middleware('Authentication');
/** Router CRM API **/
Route::get('semesters', 'SemestersController@getList')->middleware('Authentication');
Route::get('classes/{branch_id}/{semester_id}', 'EnrolmentsController@loadClasses')->middleware('Authentication');
Route::get('student-info/lms/{lms_id}', 'StudentsController@getStudentName')->middleware('Authentication');
Route::get('student-info/{class_id}', 'EnrolmentsController@getClassExtendInfo')->middleware('Authentication');
Route::post('test', 'SettingsController@testFunc');
Route::get('ctp/class/{class_id}', 'CTPController@loadClassInfo')->middleware('Authentication');
Route::get('ctp/{class_id}/get-ctp-by-class', 'CTPController@getCTPByClass')->middleware('Authentication');
Route::get('ctp/{student_id}/{ctp_id}/get-ctp-info', 'CTPController@getCTPInfo')->middleware('Authentication');

/** Log Manager Transfer */
Route::post('log_manager_transfer/lists','LogManagerTransferController@lists');
Route::get('zones/{zone_name}/check-zone-exist','ZonesController@checkExist');
Route::get('log_manager_transfer/export_excel/{branch_id}/{from_date}/{to_date}/{sources}','LogManagerTransferController@exportExcel');

Route::get('users/{user_id}/get-working-history-list', 'UsersController@getWorkingHistoryList')->middleware('Authentication');
Route::get('users/{user_id}/get-user-working-history-detail', 'UsersController@getUserWorkingHistoryDetail')->middleware('Authentication');
Route::post('users/save-user-working-history', 'UsersController@saveUserWorkingHistory')->middleware('Authentication');
Route::get('users/{user_id}/{item}/edit-user-working-history', 'UsersController@editUserWorkingHistory')->middleware('Authentication');
Route::delete('users/{item}/remove-user-working-history', 'UsersController@removeUserWorkingHistory')->middleware('Authentication');
Route::put('users/update-user-working-history', 'UsersController@updateUserWorkingHistory')->middleware('Authentication');
Route::put('users/update-user-working', 'UsersController@updateUserWorking')->middleware('Authentication');
Route::get('users/get-user-rank-list', 'UsersController@getUserRankList')->middleware('Authentication');

/** Users Rate */
Route::get('rate/users/show/{id}','RankUserRateController@show')->middleware('Authentication');
Route::post('rate/users/lists','RankUserRateController@lists')->middleware('Authentication');
Route::post('rate/users/uploadExcel','RankUserRateController@uploadRateExcel')->middleware('Authentication');
Route::get('rate/users/exportExcel','RankUserRateController@exportExcel');
Route::get('rate/users/exportExcelExample','RankUserRateController@exportExcelExample');
Route::post('rate/users/remove/{id}','RankUserRateController@remove')->middleware('Authentication');
Route::get('get-rank/ranks/get-ranks-list', 'RankUserRateController@getRanksList')->middleware('Authentication');
Route::post('search/search-users-rank-by-multi-keyword', 'RankUserRateController@searchRanksList')->middleware('Authentication');
Route::post('products/search-products', 'ProductsController@searchProducts')->middleware('Authentication');
Route::post('rate/users/update/{id}','RankUserRateController@update')->middleware('Authentication');

/** Banks **/
Route::get('banks/all/data','BanksController@getAll')->middleware('Authentication');
Route::resource('banks','BanksController')->middleware('Authentication');
Route::post('banks/{id}','BanksController@update')->middleware('Authentication');

/** Ranks */
Route::get('ranks/get-by-type/{type}','RanksController@getByType')->middleware('Authentication');
Route::get('grades/check-create-grade/{name}','RanksController@checkCreateGrade')->middleware('Authentication');
Route::post('/check-semester-lms-exist','SemestersController@checkSemesterLmsExist')->middleware('Authentication');
Route::get('zones/{id}/remove-branch','ZonesController@removeZoneIdOfBranch')->middleware('Authentication');
Route::get('zones/{id}/get-all-branches','ZonesController@getAllBranches')->middleware('Authentication');


/** Export config */
Route::get('export/config/product', 'ConfigExportController@exportProducts');
Route::get('books/{name}/check-exist-book', 'BooksController@checkExistBook')->middleware('Authentication');

/** Auto Change password */
Route::post('auth/auto_change_password','AuthenticationController@autoChangePassword')->middleware('Authentication');

/** Test */
Route::get('test/start','TestingController@start');
Route::post('test/cmd','TestingController@exeCmd');
Route::get('test/time','TestingController@getTime');
Route::post('test/do_some_thing','TestingController@doSomeThing');

Route::get('check/class-transfer/{stu_id}/{branch_id}', 'ClassTransfersController@check');
Route::get('check/branch-transfer/{stu_id}/{branch_id}', 'BranchTransfersController@check');
Route::get('check/tuition-transfer/sender/{stu_id}/{branch_id}', 'TuitionTransfersController@checkSender');
Route::get('check/tuition-transfer/receiver/{stu_id}/{branch_id}', 'TuitionTransfersController@checkReceiver');

Route::get('re-update/{branch_id}','SettingsController@reUpdate');

/** Payment */
Route::post('payment/bycontract','PaymentController@getPaymentByContract')->middleware('Authentication');
/** Import Excel*/
Route::post('upload/students','UploadController@importStudentV2')->middleware('Authentication');
Route::post('import/students','UploadController@execImportStudent')->middleware('Authentication');
Route::post('upload/users','UploadController@importUser')->middleware('Authentication');
Route::get('cm/branch/{id}', 'BranchesController@getAllCmByBranch')->middleware('Authentication');
Route::get('ec/branch/{id}', 'BranchesController@getAllEcByBranch')->middleware('Authentication');
Route::get('quality/allquality', 'QualitiesController@getAllQuality')->middleware('Authentication');
Route::post('qualities', 'QualitiesController@store')->middleware('Authentication');
Route::post('qualities/{id}', 'QualitiesController@update')->middleware('Authentication');
Route::get('student/download-template','StudentsController@downloadStudentTemplate');
Route::get('contract/download-template','ContractsController@downloadImportTemplate');
Route::post('upload/contracts','UploadController@importContracts')->middleware('Authentication');
Route::post('import/contracts','UploadController@execImportContracts')->middleware('Authentication');
Route::post('upload/classes','UploadController@importClasses')->middleware('Authentication');
Route::post('import/classes','UploadController@execImportClasses')->middleware('Authentication');

Route::get('payment/download-template','PaymentController@downloadImportTemplate');
Route::post('upload/payment','UploadController@importPayment')->middleware('Authentication');
Route::post('import/payment','UploadController@execImportPayment')->middleware('Authentication');

Route::get('discount-codes/download-template','DiscountCodesController@downloadImportTemplate');
Route::post('upload/discount-codes','UploadController@importDiscountCodes')->middleware('Authentication');
Route::post('import/discount-codes','UploadController@execImportDiscountCodes')->middleware('Authentication');

/** Cyber API */
Route::post('/get-token','ServicesController@getToken');
Route::post('/cyber-charge','ServicesController@createPayment')->middleware('Authentication');
/** APAX API */
Route::post('/apax-create-student','ServicesController@createStudentApax')->middleware('Authentication');
Route::post('/apax-get-data','ServicesController@getAllDataApax')->middleware('Authentication');
// Route::post('/apax-create-contract','ServicesController@createContractApax')->middleware('Authentication');
// Route::post('/apax-log-payment','ServicesController@logPaymentApax')->middleware('Authentication');

//Schedule
Route::get('schedules/class-id/{classId}', 'SchedulesController@getSchedulesByClassId');

//Migrate
Route::post('migrate/province/accounting-id', 'ProvincesController@migrateAccountingId')->middleware('Authentication');
Route::post('migrate/district/accounting-id', 'DistrictsController@migrateAccountingId')->middleware('Authentication');

//Discount codes
Route::post('discount-codes/add', 'DiscountCodesController@store')->middleware('Authentication');
Route::post('discount-codes/{id}/edit', 'DiscountCodesController@update')->middleware('Authentication');
Route::get('discount-codes/list/{search}/{paging}', 'DiscountCodesController@list')->middleware('Authentication');
Route::get('discount-codes/available', 'DiscountCodesController@getAvailableDiscountCodes')->middleware('Authentication');
Route::get('discount-codes/available/{id}/{branch_id}', 'DiscountCodesController@getAvailableDiscountCodesFee')->middleware('Authentication');
Route::get('discount-codes/{id}', 'DiscountCodesController@getById')->middleware('Authentication');
Route::get('notification/get-message', 'AppController@getNotificationMessage')->middleware('Authentication');
Route::get('stats', 'AppController@getStats')->middleware('Authentication');
Route::post('term-user-branch/{user_id}/{branch_id}', 'TermUserBranchesController@updateRoleForUser')->middleware('Authentication');
Route::get('student-temp/template', 'StudentTempController@getTemplate');

//Student temp
Route::get('student-temp/list', 'StudentTempController@getList');
Route::post('student-temp/validate-import', 'StudentTempController@validateImport')->middleware('Authentication');
Route::post('student-temp/import', 'StudentTempController@import')->middleware('Authentication');
Route::put('student-temp/delete', 'StudentTempController@delete')->middleware('Authentication');
Route::post('student-temp', 'StudentTempController@save')->middleware('Authentication');
Route::put('student-temp', 'StudentTempController@updateStudent')->middleware('Authentication');
Route::get('student-temp/{id}', 'StudentTempController@getStudent')->middleware('Authentication');
Route::post('student-temp/care-add', 'StudentTempController@careAdd')->middleware('Authentication');
Route::put('student-temp/care-delete', 'StudentTempController@careDelete')->middleware('Authentication');
//Student care new

Route::get('student-care/list', 'StudentCareController@getList')->middleware('Authentication');
Route::get('collaborator/list', 'StudentCareController@collaborator')->middleware('Authentication');
Route::post('collaborator/add', 'CollaboratorController@add')->middleware('Authentication');
Route::get('collaborator-detail1/{id}', 'CollaboratorController@detail1')->middleware('Authentication');
Route::get('collaborator-list/{search}/{paging}', 'CollaboratorController@list')->middleware('Authentication');
Route::post('collaborator/{id}/edit', 'CollaboratorController@edit')->middleware('Authentication');

Route::get('reports/student-withdraw', 'ReportsController@studentWithdrawNew')->middleware('Authentication');
Route::get('export/student-withdraw', 'ReportsController@studentWithdrawExport')->middleware('Authentication');
Route::get('support/change-enrolment-start-date', 'SupportsController@changeEnrolmentStartDate')->middleware('Authentication');
Route::post('support/change-enrolment-start-date', 'SupportsController@saveChangeEnrolmentStartDate')->middleware('Authentication');

//support
Route::post('student-temp/validate-import1', 'StudentTempController@validateImport1')->middleware('Authentication');
Route::post('student-temp/validate-import2', 'StudentTempController@validateImport2')->middleware('Authentication');
Route::post('tool/branch_transfer', 'SupportsController@branchTransfer')->middleware('Authentication');
Route::get('branches/studying/{id}', 'SupportsController@studentStudying')->middleware('Authentication');
Route::post('tool/update-last-date/branch', 'SupportsController@branchUpdateEnrolmentLastDate')->middleware('Authentication');

Route::post('user-add/add', 'UsersController@addNew')->middleware('Authentication');
Route::post('user-add/add/:id/edit', 'UsersController@editNew')->middleware('Authentication');
Route::post('users/update-user-transfer', 'UsersController@updateUserTransfer')->middleware('Authentication');
Route::get('tuition-fee/list','TuitionFeesController@list')->middleware('Authentication');
Route::get('tuition-fee/list-name','TuitionFeesController@listName')->middleware('Authentication');
Route::post('contracts/retry-cyber','ContractsController@retryCyber')->middleware('Authentication');

/** CareSoft */
Route::post('care-soft/customer-info','SupportsController@customerInfo');
Route::post('care-soft/apps-callback','SupportsController@appsCallback');
Route::get('care-soft/cron-push-contact','SupportsController@cronPushContact');
Route::get('care-soft/get-custom-fields','CsController@getCustomFields');

/** export new */

Route::get('export/student-list-export', 'ReportsController@studentListExport')->middleware('Authentication');
Route::get('export/student-trial-learn', 'ReportsController@studentTrialExport')->middleware('Authentication');

Route::get('export/student-active-report', 'ReportsController@studentActiveReport')->middleware('Authentication');
Route::get('export/student-pending-report', 'ReportsController@studentPendingReport')->middleware('Authentication');

Route::get('cron-job/log-student-active','SupportsController@logStudentActive');
Route::get('student-temp-ext/get-source-note', 'CsController@getSourceNote')->middleware('Authentication');
Route::get('student-temp-ext/get-source', 'CsController@getSource')->middleware('Authentication');

Route::post('enrolments/student-extend', 'EnrolmentsController@studentExtend')->middleware('Authentication');
Route::get('/student-search/suggest-sender/{key}/{branch_id}', 'SuggestController@suggestSender')->middleware('Authentication');
Route::get('/tuition-convert/contracts/sender/{student_id}', 'SuggestController@getAllTuition')->middleware('Authentication');
Route::get('support/transfer-all-class/{branch_id}/{program_id}', 'SuggestController@getClassAvailable')->middleware('Authentication');

Route::post('/all-class-transfers','ClassTransfersController@allClassTransfers')->middleware('Authentication');
/** Api tools */
Route::get('tools/copy-log-contract/{log_contract_id}', 'ToolsController@copyLogContract')->middleware('Authentication');
Route::get('tools/convert-contract/{cms_id}', 'ToolsController@convertContractByStudent')->middleware('Authentication');
Route::get('tools/convert-tuition-transfer', 'ToolsController@convertDataTuitionTransfer');
Route::get('tools/convert-branch-transfer', 'ToolsController@convertDataBranchTransfer');
Route::get('tools/recall-cyber-create-contract/{contract_id}', 'ToolsController@recallApiCyberCreateContract')->middleware('Authentication');
Route::get('tools/recall-cyber-enrolment-summer', 'ToolsController@recallApiCyberEnrolmentSummer')->middleware('Authentication');
Route::get('tools/insert-report-full-fee-active/{cms_id}/{report_month}/{contract_id}', 'ToolsController@insertReportFullFeeActive')->middleware('Authentication');
Route::get('tools/recall-cyber-branch-transfer', 'ToolsController@recallApiCyberBranchTransfer')->middleware('Authentication');
Route::get('tools/withdraw_all', 'ToolsController@withdrawAll')->middleware('Authentication');
Route::get('tools/recall-cyber-create-enrolment/{contract_id}', 'ToolsController@recallApiCyberCreateEnrolment')->middleware('Authentication');
Route::post('tools/send_sms', 'ToolsController@sendSms')->middleware('Authentication');
Route::get('tools/call_cyber_hocvien', 'ToolsController@callCyberHocvien')->middleware('Authentication');
Route::get('tools/call_cyber_goiphi', 'ToolsController@callCyberGoiphi')->middleware('Authentication');
Route::get('tools/call_cyber_xeplop', 'ToolsController@callCyberXeplop')->middleware('Authentication');
Route::get('tools/action_tool_13', 'ToolsController@actionTool13')->middleware('Authentication');
Route::get('tools/action_tool_14', 'ToolsController@actionTool14')->middleware('Authentication');
Route::get('tools/action_tool_15', 'ToolsController@actionTool15')->middleware('Authentication');
/** End Api tools */

Route::post('attendances-new/save', 'AttendancesController@saveAttendancesNew')->middleware('Authentication');
Route::post('checkin/add', 'CheckinController@add')->middleware('Authentication');

Route::get('checkin/get-trial-class/{branch_id}', 'CheckinController@getTrialClass')->middleware('Authentication');
Route::get('get-student-checkin/{id}', 'CheckinController@getCheckinDetail')->middleware('Authentication');
Route::put('checkin/{id}/update', 'CheckinController@updateStudent')->middleware('Authentication');
Route::get('checkin/list/{pagination}/{search}/{sort}', 'CheckinController@list')->middleware('Authentication');
Route::get('sources', 'CheckinController@sources')->middleware('Authentication');
Route::get('sources_detail', 'CheckinController@sourcesDetail')->middleware('Authentication');
Route::get('get_all_user_by_role', 'CheckinController@getAllUserByRole')->middleware('Authentication');
Route::get('school-grades/list', 'CheckinController@schoolGrades')->middleware('Authentication');
Route::get('shift/list/{branch_id}', 'CheckinController@shiftList')->middleware('Authentication');
Route::put('checkin/{id}/approve', 'CheckinController@approver')->middleware('Authentication');
Route::post('checkin/convert-student/{id}', 'CheckinController@convertStudent')->middleware('Authentication');
Route::post('checkin/branch-transfer', 'CheckinController@transferCheckin')->middleware('Authentication');
Route::get('checkin/branch-transfer/{id}/{branch_id}', 'CheckinController@transferCheckinStatus')->middleware('Authentication');
Route::get('checkin/history-care/{crm_id}', 'CheckinController@historyCare')->middleware('Authentication');
Route::get('checkin-import/list/{page}/{search}/{filter}', 'CheckinController@listImport')->middleware('Authentication');
Route::post('check-checkin-import/check-checkin-import-excel', 'CheckinController@checkCheckinImport')->middleware('Authentication');
Route::get('checkin-import/export/{checkin_import_id}', 'CheckinController@checkCheckinImportExportLog')->middleware('Authentication');

Route::get('jobs/index', 'JobsController@index');
Route::get('jobs/update', 'JobsController@update');
Route::get('jobs/update-tools-renew-report', 'JobsController@updateToolsRenewReport');
Route::get('jobs/send-mail-create-contract-sale-hub', 'JobsController@sendMailCreateContractToSalehub');
Route::get('jobs/send-mail-create-contract-transfer-branch', 'JobsController@sendMailCreateContractTransferBranch');
Route::get('jobs/test-sms', 'JobsController@testSms');
Route::get('jobs/process-mail', 'JobsController@processQueuesMail');
Route::get('jobs/process-sms', 'JobsController@processQueuesSms');
Route::get('jobs/send-mail-renew/{report_month}', 'JobsController@sendMailRenew');
Route::get('jobs/send-mail-renew-next-month/{report_month}', 'JobsController@sendMailRenewNextMonth');

Route::get('exchange/search/students/{branch_id}/{keyword}', 'ExchangeController@searchStudent')->middleware('Authentication');
Route::get('exchange/all-contract/{student_id}', 'ExchangeController@getAllContractByStudent')->middleware('Authentication');
Route::post('exchange/result', 'ExchangeController@getResult')->middleware('Authentication');
Route::post('exchange/save', 'ExchangeController@save')->middleware('Authentication');
Route::get('exchange', 'ExchangeController@list')->middleware('Authentication');

Route::post('partner_voucher/get_data_voucher', 'CouponsController@getDataVoucher')->middleware('Authentication');
Route::get('checkin/update_checked/{student_id}', 'CheckinController@updateChecked')->middleware('Authentication');
Route::get('checkin/delete_checked/{student_id}', 'CheckinController@deleteChecked')->middleware('Authentication');
Route::get('export/checkin-list-export', 'ExelController@checkinListExport')->middleware('Authentication');

Route::post('sms/get-student', 'SmsController@getStudent')->middleware('Authentication');
Route::get('sms/get_all_sms_template', 'SmsController@getAllSmsTemplate')->middleware('Authentication');
Route::post('sms/send', 'SmsController@send')->middleware('Authentication');
Route::get('sms/template/lists/{page}', 'SmsController@listsTemplate')->middleware('Authentication');
Route::post('sms/template/add', 'SmsController@addTempalte')->middleware('Authentication');
Route::get('sms_template/{template_id}', 'SmsController@detailTemplate')->middleware('Authentication');
Route::post('sms/template/update/{template_id}', 'SmsController@updateTempalte')->middleware('Authentication');
Route::get('sms/campaign/lists/{page}', 'SmsController@listsCampaign')->middleware('Authentication');
Route::post('reserves/send_qlcl', 'ReservesController@sendQlcl')->middleware('Authentication');
Route::get('get_php_info', 'AppController@getPhpInfo');

Route::get('contracts/show-edit/{contract_id}','ContractsController@showEdit')->middleware('Authentication');
Route::post('contracts/edit/{contract_id}','ContractsController@updateEdit')->middleware('Authentication');
Route::get('remove-contract/{contract_id}','ContractsController@removeContract')->middleware('Authentication');
Route::get('get/{province_id}/{district_id}/{school_level}/schools', 'StudentsController@getSchools');
Route::get('gud_job/all_list', 'StudentsController@getAllJobs');

Route::get('get_done_sessions', 'ToolsController@getDoneSessions');
Route::get('process_reserve_transfer_online', 'ToolsController@processReserveTransferOnline');
Route::post('single-sign-on','AuthenticationController@singleSignOn');
Route::get('switch-system','AuthenticationController@switchSystem')->middleware('Authentication');
Route::post('leads-create-checkin','CheckinController@addCheckinByLead');
Route::post('leads-update-checkin','CheckinController@updateCheckinByLead');
Route::post('leads-update-student-info','CheckinController@updateStudentInfoByLead');
Route::post('leads-update-parent-info','CheckinController@updateParentInfoByLead');
Route::get('get-login-redirect', 'AuthenticationController@getLoginRedirect');
Route::get('process-transfer-all-branch', 'ToolsController@processTransferAllBranch');
Route::post('process_update_coupon', 'ToolsController@processUpdateCoupon');
Route::post('issues/add-student-care', 'IssuesController@addStudentCare')->middleware('Authentication');
Route::get('issues/get-student-care/{student_id}', 'IssuesController@getStudentsCare')->middleware('Authentication');
Route::post('issues/edit-student-care', 'IssuesController@updateStudentCare')->middleware('Authentication');
Route::get('gen_report_week', 'ToolsController@genReportWeek');
Route::get('insert_student_report', 'ReportStudentsController@insertStudentReport');
Route::get('report_student/info/{branch_id}', 'ReportStudentsController@getAllTeacherByBranch')->middleware('Authentication');
Route::get('report_student/class_info/{teacher_id}', 'ReportStudentsController@getAllClassByTeacher')->middleware('Authentication');
Route::get('report_student/student_info/{report_week_id}/{class_id}', 'ReportStudentsController@getAllStudentByClass')->middleware('Authentication');
Route::get('report_student/get_report_weeks', 'ReportStudentsController@getReportWeeks')->middleware('Authentication');
Route::post('report_student/update_data', 'ReportStudentsController@updateData')->middleware('Authentication');
Route::get('report_student/get_report_month','ReportStudentsController@getReportMonth')->middleware('Authentication');
Route::get('report_student/lock_class/{report_week_id}/{class_id}', 'ReportStudentsController@lockClass')->middleware('Authentication');
Route::get('send-mail-salehub-over/{branch_id}', 'JobsController@sendMailSalehubOver');
Route::get('get-data-source-detail', 'JobsController@updateSourceDetail');
Route::post('students/upload_file/{student_id}', 'StudentsController@uploadFile')->middleware('Authentication');
Route::get('students/load_file/{student_id}', 'StudentsController@loadFile')->middleware('Authentication');
Route::post('students/remove_file/{student_id}', 'StudentsController@removeFile')->middleware('Authentication');
Route::get('report_student/student_info_by_class/{class_id}', 'ReportStudentsController@getStudentByClass')->middleware('Authentication');
Route::post('report_student/update_data_score', 'ReportStudentsController@updateDataReportScore')->middleware('Authentication');
Route::get('report_student/lock_class_new/{class_id}', 'ReportStudentsController@lockClassNew')->middleware('Authentication');
Route::get('add-schedule', 'SessionsController@addSchedule');
Route::post('/get-data-transfer','ServicesController@getAllDataCMS')->middleware('Authentication');

Route::get('apax-get-data', 'APAXAPIController@getAllData');

Route::get('process-tmp-update-class-lms', 'LMSAPIController@processTmpUpdateClassLms');
Route::get('check_api_lms', 'LMSAPIController@checkAPI');
Route::get('update_api_lms/{type}', 'LMSAPIController@updateLms');
Route::get('lms/get_list_student', 'LMSAPIController@getListStudent');