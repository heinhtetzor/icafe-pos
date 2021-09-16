<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdAndExpenseIdToStockMenuEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_menu_entries', function (Blueprint $table) {
            //
            $table->bigInteger('expense_stock_menu_id')->unsigned()->nullable();
            $table->foreign('expense_stock_menu_id')->references('id')->on('expense_stock_menus');

            $table->bigInteger('order_menu_id')->unsigned()->nullable();
            $table->foreign('order_menu_id')->references('id')->on('order_menus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_menu_entries', function (Blueprint $table) {
            $table->dropColumn('expense_stock_menu_id');
            $table->dropColumn('order_menu_id');
        });
    }
}
