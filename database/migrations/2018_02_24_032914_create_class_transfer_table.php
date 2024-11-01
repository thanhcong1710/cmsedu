<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_transfer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id');
            $table->integer('from_class_id');
            $table->integer('from_branch_id')->nullable()->default(NULL);
            $table->integer('to_class_id')->nullable()->default(NULL);
            $table->integer('to_branch_id')->nullable()->default(NULL);
            $table->integer('amount');
            $table->integer('session');
            $table->integer('creator_id');
            $table->datetime('create_date');
            $table->tinyInteger('status')->comment('0 - chờ duyệt đi, 1 - chờ duyệt bên nhận, 2 - đã duyệt, 3 - từ chối');
            $table->string('note', 255)->nullable()->default(NULL);
            $table->date('transfer_date')->nullable()->default(NULL);
            $table->tinyInteger('type')->default(0);
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
        Schema::dropIfExists('class_transfer');
    }
}
