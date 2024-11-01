<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lms_program_id')->nullable()->default(NULL)->comment('id do LMS quy định');
            $table->string('name', 50);
            $table->integer('product_id');
            $table->datetime('created_id');
            $table->datetime('update_date')->nullable()->default(NULL);
            $table->tinyInteger('status')->default(1);
            $table->integer('parent_id')->nullable()->default(NULL);
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
        Schema::dropIfExists('programs');
    }
}
