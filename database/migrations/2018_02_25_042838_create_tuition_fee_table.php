<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTuitionFeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tuition_fee', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('product_id');
            $table->integer('zone_id');
            $table->integer('session');
            $table->integer('price');
            $table->double('discount');
            $table->integer('receivable');
            $table->datetime('created_date')->nullable()->default(NULL);
            $table->integer('creator_id')->nullable()->default(NULL);
            $table->datetime('updated_date')->nullable()->default(NULL);
            $table->integer('editor_id')->nullable()->default(NULL);
            $table->datetime('available_date');
            $table->datetime('expired_date');
            $table->string('hash_key', 255);
            $table->string('changed_fields', 255)->nullable()->default(NULL);
            $table->tinyInteger('status')->default(0);
            $table->integer('branch_id')->nullable()->default(NULL);
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
        Schema::dropIfExists('tuition_fee');
    }
}
