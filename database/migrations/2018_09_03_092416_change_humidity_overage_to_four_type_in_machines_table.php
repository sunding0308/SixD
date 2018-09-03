<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeHumidityOverageToFourTypeInMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn('humidity_overage');
            $table->integer('humidity_add_overage')->unsigned()->after('air_overage');
            $table->integer('humidity_minus_overage')->unsigned()->after('humidity_add_overage');
            $table->integer('humidity_child_overage')->unsigned()->after('humidity_minus_overage');
            $table->integer('humidity_adult_overage')->unsigned()->after('humidity_child_overage');
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
            $table->dropColumn('humidity_add_overage');
            $table->dropColumn('humidity_minus_overage');
            $table->dropColumn('humidity_child_overage');
            $table->dropColumn('humidity_adult_overage');
            $table->integer('humidity_overage')->unsigned()->after('air_overage');
        });
    }
}
