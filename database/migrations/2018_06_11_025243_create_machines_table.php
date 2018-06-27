<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->increments('id');
            $table->macAddress('device')->unique()->index();
            $table->string('registration_id')->unique()->index();
            $table->string('status');
            $table->integer('water_overage')->unsigned();
            $table->integer('oxygen_overage')->unsigned();
            $table->integer('air_overage')->unsigned();
            $table->integer('humidity_overage')->unsigned();
            $table->string('filter1_lifespan');
            $table->string('filter2_lifespan');
            $table->string('filter3_lifespan');
            $table->string('g_status');
            $table->string('wifi_status');
            $table->string('bluetooth_status');
            $table->integer('temperature');
            $table->integer('humidity')->unsigned();
            $table->integer('pm2_5')->unsigned();
            $table->integer('oxygen_concentration')->unsigned();
            $table->integer('total_produce_water_time')->unsigned();
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
        Schema::dropIfExists('machines');
    }
}
