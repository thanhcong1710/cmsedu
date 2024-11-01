<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lms_stu_id')->nullable()->default(NULL);
            $table->string('accounting_id', 30)->nullable()->default(NULL);
            $table->string('name', 50);
            $table->string('nick', 20)->nullable()->default(NULL);
            $table->string('email', 255)->nullable()->default(NULL);
            $table->string('phone', 255)->nullable()->default(NULL);
            $table->char('gender', 1)->nullable()->default(NULL)->comment('F - nữ, M - nam');
            $table->tinyInteger('type')->nullable()->default(0)->comment('0 - bình thường, 1 - VIP');
            $table->date('date_of_birth')->nullable()->default(NULL)->comment('định dạng YYYY-mm-dd');
            $table->string('gud_mobile1', 20)->nullable()->default(NULL);
            $table->string('gud_name1', 50)->nullable()->default(NULL);
            $table->string('gud_email1', 255)->nullable()->default(NULL);
            $table->string('gud_card1', 255)->nullable()->default(NULL);
            $table->string('gud_mobile2', 20)->nullable()->default(NULL);
            $table->string('gud_name2', 50)->nullable()->default(NULL);
            $table->string('gud_email2', 255)->nullable()->default(NULL);
            $table->string('gud_card2', 255)->nullable()->default(NULL);
            $table->string('address', 255)->nullable()->default(NULL);
            $table->integer('province_id')->nullable()->default(NULL)->comment('id của tỉnh/thành phố');
            $table->integer('district_id')->nullable()->default(NULL)->comment('id của quận/huyện');
            $table->string('school', 50)->nullable()->default(NULL);
            $table->string('school_grade')->nullable()->default(NULL)->comment('xem API 16 LMS');
            $table->datetime('created_date')->nullable()->default(NULL)->comment('định dạng: YYYY-mm-dd H:m:i');
            $table->integer('creator_id')->nullable()->default(NULL);
            $table->datetime('updated_date')->nullable()->default(NULL)->comment('định dạng: YYYY-mm-dd H:m:i');
            $table->integer('editor_id')->nullable()->default(NULL);
            $table->string('hash_key', 255)->nullable()->default(NULL);
            $table->string('current_classes', 255)->nullable()->default(NULL);
            $table->string('used_student_ids', 255)->nullable()->default(NULL);
            $table->text('avatar')->nullable()->default(NULL);
            $table->text('note')->nullable()->default(NULL);
            $table->string('attached_file', 255)->nullable()->default(NULL);
            $table->string('facebook', 255)->nullable()->default(NULL);
            $table->integer('branch_id')->nullable()->default(NULL);
            $table->text('meta_data')->nullable()->default(NULL);
            $table->tinyInteger('tracking')->nullable()->default(NULL);
            $table->tinyInteger('status')->nullable()->default(NULL);
            $table->tinyInteger('source')->nullable()->default(1);
            $table->tinyInteger('tracking')->nullable()->default(1);
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
