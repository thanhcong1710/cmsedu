<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogTuitionTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_tuition_transfer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tuition_transfer_id')->nullable()->default(NULL);
            $table->tinyInteger('status')->nullable()->default(NULL);
            $table->integer('creator_id')->nullable()->default(NULL);
            $table->datetime('created_date')->nullable()->default(NULL);
            $table->string('comment', 255)->nullable()->default(NULL);
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
        Schema::dropIfExists('log_tuition_transfer');
    }
}
