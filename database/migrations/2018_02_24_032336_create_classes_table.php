<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lms_cls_id');
            $table->string('lms_cls_name', 255);
            $talbe->datetime('lms_cls_startdate');
            $talbe->datetime('lms_cls_enddate');
            $table->string('lms_cls_iscancelled', 10)->default('no');
            $table->integer('brch_id');
            $table->integer('sem_id');
            $table->integer('program_id');
            $table->integer('product_id');
            $table->string('class_day', 255)->nullable()->default(NULL)->comment('dạng json');
            $table->integer('max_students')->default(16);
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
        Schema::dropIfExists('classes');
    }
}
