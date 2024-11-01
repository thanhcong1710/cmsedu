<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hrm_id', 50);
            $table->string('accounting_id', 30);
            $table->string('full_name', 50);
            $table->string('superior_id', 50)->nullable()->default(NULL);
            $table->string('email', 255)->nullable()->default(NULL);
            $table->integer('branch_id')->nullable()->default(NULL);
            $table->text('avatar')->nullable()->default(NULL);
            $table->string('username', 50);
            $table->string('password', 100);
            $table->datetime('created_date')->nullable()->default(NULL);
            $table->datetime('updated_date')->nullable()->default(NULL);
            $table->date('start_date');
            $table->date('end_date')->nullable()->default(NULL);
            $table->tinyInteger('status')->nullable()->default(1);
            $table->string('provider', 255)->nullable()->default(NULL);
            $table->string('provider_id', 255)->nullable()->default(NULL);
            $table->string('hash_key', 255)->nullable()->default(NULL);
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
        Schema::dropIfExists('users');
    }
}
