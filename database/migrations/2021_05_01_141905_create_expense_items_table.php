<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('expense_id')->unsigned();
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('CASCADE');
            $table->bigInteger('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('items');
            $table->bigInteger('menu_group_id')->unsigned()->nullable();
            $table->foreign('menu_group_id')->references('id')->on('menu_groups');
            $table->integer('is_general_item')->default(0);
            $table->decimal('cost', 8, 2);
            $table->integer('quantity');
            $table->string('unit');

            $table->index(['menu_group_id', 'created_at', 'expense_id']);
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
        Schema::dropIfExists('expense_items');
    }
}
