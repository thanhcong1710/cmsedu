<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 255);
            $table->tinyInteger('type')->nullable()->comment('1 - trail (contract Học trải nghiệm)
                                                              2 - official (contract chính thức)
                                                              3 - newest (contract mới nhập chưa có CM)
                                                              4 - transfer_fee (contract sinh ra do chuyển phí)
                                                              5 - transfer_class (contract sinh ra do chuyển lớp)
                                                              6 - transfer_branch (contract sinh ra do chuyển trung tâm)');
            $table->tinyInteger('payload', 1)->nullable()->comment('0 - đóng phí 1 lần, 1 - đóng phí nhiều lần');
            $table->integer('student_id');
            $table->integer('relation_contract_id');
            $table->integer('enrolment_id');
            $table->integer('branch_id');
            $table->integer('ceo_branch_id');
            $table->integer('region_id');
            $table->integer('ceo_region_id');
            $table->integer('ec_id');
            $table->integer('ec_leader_id');
            $table->integer('cm_id');
            $table->integer('om_id');
            $table->integer('product_id');
            $table->integer('program_id');
            $table->integer('tuition_fee_id');
            $table->integer('receivable');
            $table->integer('payment_id');
            $table->integer('must_charge');
            $table->integer('total_discount');
            $table->integer('debt_amount');
            $table->longText('description');
            $table->string('bill_info', 255);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_sessions');
            $table->integer('left_sessions');
            $table->string('expected_class', 255);
            $table->tinyInteger('only_give_tuition_fee_transfer', 0)->nullable()->comment('1 - chỉ nhận chuyển phí');
            $table->tinyInteger('passed_trial', 1)->nullable()->comment('0 - chưa Học trải nghiệm, 1 - đã Học trải nghiệm');
            $table->tinyInteger('status')->nullable()->comment('0 - deleted, 1 - active');
            $table->date('created_at');
            $table->date('updated_at');
            $table->integer('creator_id');
            $table->integer('editor_id');
            $table->string('hash_key', 255);
            $table->string('note', 255)->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
