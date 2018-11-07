<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFloorIntoInstallationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->string('floor')->nullable()->after('hotel_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn('floor');
        });
    }
}
