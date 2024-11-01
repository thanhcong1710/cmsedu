<?php
namespace App\Http\Controllers;

use App\Models\APICode;
use App\Models\Contract;
use App\Models\Enrolment;
use App\Models\Response;
use App\Models\Session;
use App\Models\Student;
use App\Models\TuitionTransfer;
use App\Providers\UtilityServiceProvider;
use Illuminate\Http\Request;
use Mockery\Exception;

class TransferFeeController extends Controller
{
    public function toNewStudent(Request $request)
    {
        $code = APICode::SUCCESS;
        $data = null;
        $response = new Response();
        $tuition_transfer_id = $request->tuition_transfer_id;
        $tuitionTransfer = TuitionTransfer::find($tuition_transfer_id);
        if( !$tuitionTransfer )
            return null;

        $this->handleNewStudent($request->users_data,$tuitionTransfer,$tuitionTransfer->to_contract_id);
        return $response->formatResponse($code, $data);
    }

    public function toOldStudent( Request $request )
    {
        $code = APICode::SUCCESS;
        $data = null;
        $response = new Response();
        $tuition_transfer_id = $request->tuition_transfer_id;
        $tuitionTransfer = TuitionTransfer::find($tuition_transfer_id);
        if( !$tuitionTransfer )
            return null;
        
        $this->handleOldStudent($request->users_data,$tuitionTransfer,$tuitionTransfer->to_contract_id);
        return $response->formatResponse($code, $data);
    }

    public function handleOldStudent($users_data, $tuitionTransfer,$to_contract_id)
    {
        $contract = Contract::find($to_contract_id);
        $this->_createNewContract($users_data, $tuitionTransfer,$contract);
    }

    public function handleNewStudent($users_data, $tuitionTransfer,$to_contract_id)
    {
        $contract = Contract::find($to_contract_id);
        $student  = $contract->student;
        // Update Contract
        $contract->receivable       = $tuitionTransfer->amount_received;
        $contract->total_charged    = $tuitionTransfer->amount_received;
        $contract->must_charge      = 0;
        $contract->total_discount   = 0;
        $contract->debt_amount      = 0;
        $contract->end_date         = ( $student->session_received / 2 );
        $contract->total_sessions   = $tuitionTransfer->session_received;
        $contract->real_sessions    = $tuitionTransfer->session_received;
        $contract->status           = 3;
        $contract->updated_at       = now();
        $contract->editor_id        = $users_data->id;
        $contract->reservable       = 1;
        $contract->update();

        // Handle Transferee
        $this->handleTransferee($tuitionTransfer);
    }

    public function _createNewContract($users_data, $tuitionTransfer, $contract)
    {
        $lastEnrolment  = Contract::select(['class_id','enrolment_expired_date'])->where('student_id',$contract->student_id)
                                ->orderBy('id',SORT_DESC)->first();
        $classDay       = Session::where('class_id',$lastEnrolment->class_id)->select('enrolment_schedule')
                                ->groupBy(['enrolment_schedule'])->pluck('enrolment_schedule')->toArray();
        $holiDay        = UtilityServiceProvider::getPublicHolidays($lastEnrolment->class_id,$contract->branch_id);
        $startDate      = UtilityServiceProvider::calcNewStartDate($lastEnrolment->last_date,$classDay,$holiDay);
        $endDate        = UtilityServiceProvider::getRealSessions($tuitionTransfer->session_received,$classDay,$holiDay,$startDate);

        $dataCreateContract = [
            'code'          => apax_ada_gen_contract_code(),
            'type'          => 3,
            'payload'       => 0,
            'student_id'    => $tuitionTransfer->to_student_id,
            'ceo_branch_id' => $contract->ceo_branch_id,
            'ceo_region_id' => $contract->ceo_region_id,
            'ec_id'         => $contract->ec_id,
            'ec_leader_id'  => $contract->ec_leader_id,
            'cm_id'         => $contract->cm_id,
            'om_id'         => $contract->om_id,
            'branch_id'     => $contract->branch_id,
            'region_id'     => $contract->region_id,
            'product_id'    => $contract->product_id,
            'program_id'    => $contract->program_id,
            'info_available'=> $contract->info_available,
            'receivable'    => $tuitionTransfer->amont_received,
            'total_charged' => $tuitionTransfer->amont_received,
            'must_charge'   => 0,
            'total_discount'=> 0,
            'debt_amount'   => 0,
            'total_sessions'=> $tuitionTransfer->session_received,
            'real_sessions' => $tuitionTransfer->session_received,
            'passed_trial'  => 1,
            'status'        => 3,
            'created_at'    => now(),
            'creator_id'    => $users_data->id,
            'reservable'    => 1,
            'tuition_fee_id'=> $tuitionTransfer->to_tuition_fee_id,
            'start_date'    => $startDate,
            'end_date'      => $endDate->end_date,
            'class_id'      => $lastEnrolment->class_id,
            'enrolment_start_date'    => $dataCreateContract['start_date'],
            'enrolment_end_date'    => $lastEnrolment->last_date,
            'enrolment_updated_at'  => now(),
            'status'        => 5,
            'enrolment_real_sessions'=> $tuitionTransfer->session_received,
            'enrolment_left_sessions'=> $tuitionTransfer->session_received,
        ];
        //Create new Contract
        $idContractCreated = Contract::insertGetId($dataCreateContract);

        // Handle Transferee
        $this->handleTransferee($tuitionTransfer);
    }

    public function handleTransferee($tuitionTransfer)
    {
        $contract = Contract::find($tuitionTransfer->from_contract_id);
        if( $contract->enrolment_start_date == null ) {
            // Update Only Contract
            $contract->real_sessions = 0;
            $contract->save();
        } else {
            // Update Contract
            $real_session = ( $contract->real_session - $tuitionTransfer->session_transferred );
            $contract->real_sessions = $real_session;
            $contract->end_date = $tuitionTransfer->transfer_date;
            $contract->save();

            // Update Enrolment
            $enrolmentData = Contract::find($contract->id);
            $enrolmentData->enrolment_real_sessions = $contract->real_sessions;
            $enrolmentData->enrolment_last_date     = $contract->real_sessions;
            $enrolmentData->save();
        }
    }

}