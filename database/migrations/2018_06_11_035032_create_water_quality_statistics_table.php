<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaterQualityStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_quality_statistics', function (Blueprint $table) {
            $table->integer('machine_id')->unsigned()->index();
            $table->integer('raw_water_tds')->unsigned();
            $table->integer('pure_water_tds')->unsigned();
            $table->integer('salt_rejection_rate')->unsigned();
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
        Schema::dropIfExists('water_quality_statistics');
    }
}
