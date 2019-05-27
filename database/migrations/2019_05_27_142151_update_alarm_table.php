<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAlarmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alarms', function (Blueprint $table) {
            $table->string('position_change_alarm')->nullable()->change();
            $table->string('service_alarm_status')->nullable()->change();
            $table->string('sterilization_alarm')->nullable()->change();
            $table->string('filter_alarm')->nullable()->change();
            $table->string('water_shortage_alarm')->nullable()->change();
            $table->string('filter_anti_counterfeiting_alarm')->nullable()->change();
            $table->string('slave_mobile_alarm')->nullable()->change();
            $table->string('dehumidification_tank_full_water_alarm')->nullable()->change();
            $table->string('malfunction_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
