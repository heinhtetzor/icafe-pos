<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrintJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('store_id')->unsigned()->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->string('type');

            $table->bigInteger('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('CASCADE')->onUpdate('CASCADE');
            
            $table->bigInteger('order_menu_id')->unsigned()->nullable();
            $table->foreign('order_menu_id')->references('id')->on('order_menus')->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->bigInteger('expense_id')->unsigned()->nullable();
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->integer('status')->default(0);

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
        Schema::dropIfExists('print_jobs');
    }
}
