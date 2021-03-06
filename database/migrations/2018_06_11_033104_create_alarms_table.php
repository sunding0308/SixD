<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlarmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alarms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('machine_id')->unsigned()->index();
            $table->string('position_change_alarm');
            $table->string('service_alarm_status');
            $table->string('sterilization_alarm');
            $table->string('filter_alarm');
            $table->string('water_shortage_alarm');
            $table->string('filter_anti_counterfeiting_alarm');
            $table->string('slave_mobile_alarm');
            $table->string('dehumidification_tank_full_water_alarm');
            $table->string('malfunction_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alarms');
    }
}
