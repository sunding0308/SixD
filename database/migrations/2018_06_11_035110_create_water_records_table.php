<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaterRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_records', function (Blueprint $table) {
            $table->integer('machine_id')->unsigned()->index();
            $table->dateTime('date');
            $table->string('time');
            $table->string('flow');
            $table->string('total_flow');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('water_records');
    }
}
