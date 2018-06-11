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
            $table->string('unique_code')->unique()->index();
            $table->string('registration_id')->unique()->index();
            $table->string('status');
            $table->string('overage');
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
