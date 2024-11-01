<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermStudentUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_student_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id');
            $table->integer('ec_id');
            $table->integer('ec_leader_id')->nullable()->default(NULL);
            $table->integer('cm_id')->nullable()->default(NULL);
            $table->integer('om_id')->nullable()->default(NULL);
            $table->integer('zone_id')->nullable()->default(NULL);
            $table->integer('region_id')->nullable()->default(NULL);
            $table->integer('branch_id')->nullable()->default(NULL);
            $table->integer('ceo_branch_id')->nullable()->default(NULL);
            $table->integer('ceo_region_id')->nullable()->default(NULL);
            $table->datetime('created_date')->nullable()->default(NULL);
            $table->datetime('updated_date')->nullable()->default(NULL);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('term_student_user');
    }
}
