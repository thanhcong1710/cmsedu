<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 8/17/2018
 * Time: 12:49 AM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InfoValidation extends  Model
{
    public function hasStudent($stu_id, $branch_id){
        $query = "SELECT id, `name`, accounting_id, crm_id, `type` FROM students WHERE stu_id = $stu_id AND branch_id=$branch_id";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0];
        }else{
            return false;
        }

    }

    public function hasContracts($student_id, $branch_id){
        $query = "
            SELECT id FROM contracts AS c WHERE c.student_id = $student_id AND c.branch_id = $branch_id
            AND c.id NOT IN (SELECT id FROM contracts WHERE relation_contract_id = c.id)
            ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            $resp = [];
            foreach ($res as $re){
                $resp[] = $re->id;
            }

            return $resp;
        }else{
            return false;
        }
    }
    public function hasTransferableContracts($student_id, $branch_id){
        $query = "
            SELECT id FROM contracts AS c WHERE c.student_id = $student_id AND c.branch_id = $branch_id
            AND c.id NOT IN (SELECT id FROM contracts WHERE relation_contract_id = c.id)
            AND c.type > 0
            ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            $resp = [];
            foreach ($res as $re){
                $resp[] = $re->id;
            }

            return $resp;
        }else{
            return false;
        }
    }

    public function hasJoinedClassTransfer($contract_ids){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND c.student_id NOT IN (SELECT student_id FROM class_transfer WHERE STATUS < 2 AND `type` = 0 GROUP BY student_id)
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }

    public function hasJoinedBranchTransfer($contract_ids){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND c.student_id NOT IN (SELECT student_id FROM class_transfer WHERE STATUS < 2 AND `type` = 1 GROUP BY student_id)
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }

    public function hasJoinedTuitionTransfer($contract_ids){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND c.student_id NOT IN (SELECT from_student_id FROM tuition_transfer WHERE STATUS = 0 GROUP BY from_student_id)
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }

    public function hasJoinedPending($contract_ids){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND c.student_id NOT IN (SELECT student_id FROM pendings WHERE STATUS=0 GROUP BY student_id)
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }
    public function hasJoinedReserve($contract_ids){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND c.student_id NOT IN (SELECT student_id FROM reserves WHERE STATUS=0 GROUP BY student_id)
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }

    public function hasAvailableEnrolment($contract_ids){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND (c.enrolment_start_date IS NULL OR (c.enrolment_expired_date >= CURDATE() AND c.enrolment_real_sessions > 0 AND c.status = 6))
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }

    public function hasActiveEnrolment($contract_ids){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND c.`enrolment_expired_date` > CURDATE()
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }

    public function hasAvailableContractForClassTransfer($contract_ids, $type){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND (
                      c.`type` IN (3,4,5,6) 
                      OR (c.`type` IN (1,2) AND ((c.`payload` = 0 AND c.real_sessions > 0) OR (c.`payload`=1 AND c.`total_charged` > 0)))
                      OR ($type = 1)
                    )
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }

    public function hasAvailableContract($contract_ids, $type){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND (
                      c.`type` IN (3,4,5,6) 
                      OR (c.`type` IN (1,2) AND ((c.`payload` = 0 AND c.debt_amount = 0) OR (c.`payload`=1 AND c.`total_charged` > 0)))
                      OR ($type = 1)
                    )
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }
    public function hasAvailableContractForTransferFee($contract_ids){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND (
                      c.`type` IN (3,4,5,6) 
                      OR (c.`type` IN (1,2) AND ((c.`payload` = 0 AND c.debt_amount = 0) OR (c.`payload`=1 AND c.`total_charged` > 0)))
                    )
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }

    public function hasMoreThanTwoActiveClass($contract_ids, $branch_id){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND c.student_id NOT IN (
                        SELECT t.student_id AS id
                        FROM (
                            SELECT
                            c.student_id, COUNT(e.id) AS count_enroll
                            FROM contracts AS c
                            WHERE c.enrolment_expired_date > CURDATE() AND c.status = 6 AND c.real_sessions > 0 AND c.`branch_id` = $branch_id GROUP BY c.student_id
                        ) AS t
                        WHERE t.count_enroll > 1
                    )
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }

    public function hasTrailContract($contract_ids){
        $query = "
                SELECT 
                    COUNT(c.id) AS total
                FROM
                    contracts AS c
                WHERE
                    c.id IN ($contract_ids)
                    AND c.student_id NOT IN (SELECT student_id FROM contracts WHERE `type` = 0 AND status NOT IN (0, 7) GROUP BY student_id)
                ";

        $res = DB::select(DB::raw($query));

        if(!empty($res)){
            return $res[0]->total;
        }else{
            return false;
        }
    }
}