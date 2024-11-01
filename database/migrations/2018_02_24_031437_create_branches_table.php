<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 225);
            $table->integer('lms_brch_id');
            $table->string('hrm_id', 20);
            $table->integer('zone_id');
            $table->integer('region_id');
            $table->date('opened_date');
            $table->datetime('created_date');
            $table->tinyInteger('status')->nullable()->default(1)->comment('0 - bị đình chỉ, 1 - đang hoạt động');
            $table->datetime('updated_date')->nullable()->default(NULL);
            $table->string('accounting_id', 20)->nullable()->default(NULL);
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
        Schema::dropIfExists('branches');
    }
}
