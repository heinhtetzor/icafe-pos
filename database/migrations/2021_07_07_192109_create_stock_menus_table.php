<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained();
            $table->decimal('purchase_cost')->default(0);
            $table->decimal('sales_price')->default(0);
            $table->unsignedInteger('balance')->default(0);
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
        Schema::dropIfExists('stock_menus');
    }
}
