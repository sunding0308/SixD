<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalStockIntoVendingMachineStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vending_machine_stocks', function (Blueprint $table) {
            $table->integer('total_stock_in')->unsigned()->after('quantity');
            $table->integer('total_stock_out')->unsigned()->after('total_stock_in');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vending_machine_stocks', function (Blueprint $table) {
            $table->dropColumn('total_stock_in');
            $table->dropColumn('total_stock_out');
        });
    }
}
