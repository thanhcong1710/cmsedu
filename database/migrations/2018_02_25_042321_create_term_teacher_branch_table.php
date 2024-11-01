<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermTeacherBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_teacher_branch', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id')->nullable()->default(NULL);
            $table->integer('branch_id')->nullable()->default(NULL);
            $table->tinyInteger('is_head_teacher')->nullable()->default(0)->comment('0 - false, 1 - true');
            $table->tinyInteger('status')->nullable()->default(1)->comment('0 - nghỉ việc, 1 - đang làm việc');
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
        Schema::dropIfExists('term_teacher_branch');
    }
}
