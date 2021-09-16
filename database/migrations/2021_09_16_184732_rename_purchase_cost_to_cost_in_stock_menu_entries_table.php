<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePurchaseCostToCostInStockMenuEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_menu_entries', function (Blueprint $table) {
            $table->renameColumn('purchase_cost', 'cost');
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
            $table->renameColumn('cost', 'purchase_cost');
        });
    }
}
