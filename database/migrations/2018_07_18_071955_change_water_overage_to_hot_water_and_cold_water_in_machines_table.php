<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeWaterOverageToHotWaterAndColdWaterInMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn('water_overage');
            $table->integer('hot_water_overage')->unsigned()->after('status');
            $table->integer('cold_water_overage')->unsigned()->after('hot_water_overage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn('cold_water_overage');
            $table->dropColumn('hot_water_overage');
            $table->integer('water_overage')->unsigned()->after('status');
        });
    }
}
