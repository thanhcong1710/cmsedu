<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermStudentRankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_student_rank', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id');
            $table->integer('rank_id');
            $table->text('comment')->nullable()->default(NULL);
            $table->datetime('rating_date')->comment('ngày đánh giá (có thể đánh giá học sinh trước thời điểm hiện tại)');
            $table->datatime('created_date');
            $table->integer('creator_id');
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
        Schema::dropIfExists('term_student_rank');
    }
}
