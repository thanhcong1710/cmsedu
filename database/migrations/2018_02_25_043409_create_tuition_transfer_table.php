<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTuitionTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tuition_transfer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_student_id')->nullable()->default(NULL);
            $table->integer('to_student_id')->nullable()->default(NULL);
            $table->tinyInteger('type')->nullable()->default(0)->comment('0 - chuyển phí, 1 - chuyển buổi');
            $table->integer('amount')->nullable()->default(NULL);
            $table->integer('session')->nullable()->default(NULL);
            $table->tinyInteger('status')->nullable()->default(NULL)->comment('0 - chờ duyệt đi, 1 - chờ duyệt bên nhận, 2 - đã duyệt, 3 - từ chối');
            $table->datetime('created_date')->nullable()->default(NULL);
            $table->integer('creator_id')->nullable()->default(NULL);
            $table->string('note', 255)->nullable()->default(NULL);
            $table->date('transfer_date')->nullable()->default(NULL);
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
        Schema::dropIfExists('tuition_transfer');
    }
}
