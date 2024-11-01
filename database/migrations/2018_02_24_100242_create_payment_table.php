<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id');
            $table->tinyInteger('method')->comment('0 - tiền mặt, 1 - chuyển khoản');
            $table->tinyInteger('payload')->comment('0 - 1 lần, 1 - nhiều lần');
            $table->integer('must_charge');
            $table->integer('amount');
            $table->integer('total');
            $table->integer('dept');
            $table->datetime('created_at');
            $table->integer('creator_id');
            $table->integer('count');
            $table->longText('note');
            $table->tinyInteger('type');
            $table->string('hash_key', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment');
    }
}
