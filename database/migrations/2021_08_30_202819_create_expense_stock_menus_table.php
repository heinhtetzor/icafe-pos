<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseStockMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_stock_menus', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('expense_id')->unsigned();
            $table->foreign('expense_id')->references('id')->on('expenses');
            $table->bigInteger('stock_menu_id')->unsigned();
            $table->foreign('stock_menu_id')->references('id')->on('stock_menus');
            $table->decimal('cost', 8, 2);
            $table->integer('quantity');
            $table->string('unit');

            $table->index(['stock_menu_id', 'created_at', 'expense_id']);
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
        Schema::dropIfExists('expense_stock_menus');
    }
}
