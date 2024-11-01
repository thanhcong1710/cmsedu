<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogClassTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_class_transfer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('class_transfer_id');
            $table->tinyInteger('status');
            $table->integer('creator_id');
            $table->datetime('created_date');
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
        Schema::dropIfExists('log_class_transfer');
    }
}
