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
            $table->float('water_overage', 8, 2);
            $table->float('oxygen_overage', 8, 2);
            $table->float('air_overage', 8, 2);
            $table->float('humidity_overage', 8, 2);
            $table->string('filter1_lifespan');
            $table->string('filter2_lifespan');
            $table->string('filter3_lifespan');
            $table->string('g_status');
            $table->string('wifi_status');
            $table->string('bluetooth_status');
            $table->string('temperature');
            $table->string('humidity');
            $table->string('pm2_5');
            $table->string('oxygen_concentration');
            $table->string('total_produce_water_time');
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
