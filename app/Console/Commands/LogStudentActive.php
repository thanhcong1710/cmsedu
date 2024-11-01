<?php

namespace App\Console\Commands;

use App\Models\StudentTemp;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class LogStudentActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logstudentactive:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'logstudentactive';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        $sql_1 = "UPDATE `log_student_active` lo 
        SET lo.`status` = (SELECT STATUS FROM contracts WHERE lo.`contract_id` = id),
        lo.`enrolment_withdraw_date` = (SELECT enrolment_withdraw_date FROM contracts WHERE lo.`contract_id` = id AND `status` = 7)
        WHERE lo.`status` = 6";
        u::query($sql_1);
        sleep(1);

        $sql_2 = "INSERT INTO `log_student_active` (
                    crm_id, 
                    accounting_id,
                    cyber_id,
                    enrolment_start_date,
                    enrolment_last_date,
                    `name`,
                    cls_name,
                    date_of_birth,
                    gud_name1,
                    gud_mobile1,
                    branch_name,
                    product_name,
                    tuition_name,
                    ec_name,
                    tuition_fee_price,
                    discount_value,
                    coupon,
                    must_charge,
                    debt_amount,
                    total_charged,
                    `status`,
                    `type`,
                    tuition_fee_id,
                    enrolment_schedule,
                    student_id,branch_id,
                    contract_id
                )
                SELECT st.crm_id, st.`accounting_id`,c.accounting_id AS cyber_id, c.`enrolment_start_date` enrolment_start_date,c.`enrolment_last_date` enrolment_last_date,st.`name`,
                (SELECT cls_name FROM classes WHERE id = c.class_id) AS cls_name,
                st.`date_of_birth`,
                st.`gud_name1`,st.`gud_mobile1`,
                (SELECT NAME FROM branches WHERE c.branch_id = id) AS branch_name,
                (SELECT NAME FROM `products` WHERE c.product_id = id) product_name,
                (SELECT NAME FROM `tuition_fee` WHERE c.`tuition_fee_id` = id) tuition_name,
                (SELECT full_name FROM `users` WHERE c.`ec_id` = id) ec_name,
                c.`tuition_fee_price`, c.`discount_value`, c.`coupon`, c.`must_charge`,c.`debt_amount`,c.`total_charged`,
                c.`status`,c.`type`,c.`tuition_fee_id`,c.`enrolment_schedule`, c.`student_id`,c.branch_id,c.id contract_id
                FROM contracts c 
                LEFT JOIN students st ON st.id = c.`student_id` 
                WHERE c.`status` = 6 AND c.branch_id NOT IN (100, 101) 
                AND c.`enrolment_last_date` >= CURDATE() 
                AND c.`enrolment_start_date` >DATE_SUB(CURDATE(),INTERVAL 2 DAY)
                AND c.`student_id` NOT IN (SELECT student_id FROM log_student_active WHERE enrolment_withdraw_date IS NULL)";

        u::query($sql_2);
        return "ok";
    }
    
}
