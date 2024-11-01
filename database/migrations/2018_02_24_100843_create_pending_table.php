<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id');
            $table->integer('creator_id');
            $table->date('created_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('reason_id');
            $table->integer('approver_id')->nullable()->default(NULL);
            $table->datetime('approved_date')->nullable()->default(NULL);
            $table->text('note')->nullable()->default(NULL);
            $table->text('comment')->nullable()->default(NULL);
            $table->tinyInteger('status');
            $table->string('hash_key', 255);
            $table->string('changed_fields', 255)->nullable()->default(NULL);
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
        Schema::dropIfExists('pending');
    }
}
