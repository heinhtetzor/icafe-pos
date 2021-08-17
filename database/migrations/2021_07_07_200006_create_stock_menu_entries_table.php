<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMenuEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_menu_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_menu_id')->constrained();
            $table->decimal('purchase_cost')->default(0);
            $table->unsignedInteger('in')->default(0);
            $table->unsignedInteger('out')->default(0);
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
        Schema::dropIfExists('stock_menu_entries');
    }
}
