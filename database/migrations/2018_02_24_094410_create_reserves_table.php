<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserves', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('student_id')->nullable()->default(NULL);
            $table->tinyInteger('type')->nullable()->default(0);
            $table->date('start_date')->nullable()->default(NULL);
            $table->integer('session')->nullable()->default(NULL);
            $table->tinyInteger('status')->nullable()->default(NULL);
            $table->integer('creator_id')->nullable()->default(NULL);
            $table->datetime('created_date')->nullable()->default(NULL);
            $table->integer('approver_id')->nullable()->default(NULL);
            $table->datetime('approved_date')->nullable()->default(NULL);
            $table->text('meta_data')->nullable()->default(NULL);
            $table->text('comment')->nullable()->default(NULL);
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
        Schema::dropIfExists('reserves');
    }
}
