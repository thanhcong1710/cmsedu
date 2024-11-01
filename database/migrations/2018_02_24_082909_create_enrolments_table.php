<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnrolmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrolments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('constract_id');
            $table->integer('class_id');
            $table->integer('lms_cstd_id');
            $table->date('start_date');
            $table->integer('lms_start_cjrn_id')->comment('id của ngày bắt đầu học trong 1 lớp, do LMS quy định');
            $table->date('end_date');
            $table->datetime('created_date');
            $table->integer('creator_id');
            $table->datetime('updated_date')->nullable()->default(NULL);
            $table->integer('editor_id')->nullable()->default(NULL);
            $table->string('hash_key', 255);
            $table->string('changed_fields', 255)->nullable()->default(NULL);
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
        Schema::dropIfExists('enrolments');
    }
}
