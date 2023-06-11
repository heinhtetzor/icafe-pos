<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('table_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict')->onUpdate('cascade');
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict')->onUpdate('cascade');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict')->onUpdate('cascade');
        });

        Schema::table('menu_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict')->onUpdate('cascade');
        });
        
        Schema::table('menus', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict')->onUpdate('cascade');
        });

        Schema::table('kitchens', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict')->onUpdate('cascade');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict')->onUpdate('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table_groups', function (Blueprint $table) {
            $table->dropForeign('table_groups_store_id_foreign');
            $table->dropColumn('store_id');
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign('tables_store_id_foreign');
            $table->dropColumn('store_id');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign('items_store_id_foreign');
            $table->dropColumn('store_id');
        });

        Schema::table('menu_groups', function (Blueprint $table) {
            $table->dropForeign('menus_groups_store_id_foreign');
            $table->dropColumn('store_id');
        });
        
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign('menus_store_id_foreign');
            $table->dropColumn('store_id');
        });

        Schema::table('kitchens', function (Blueprint $table) {
            $table->dropForeign('kitchens_store_id_foreign');
            $table->dropColumn('store_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_store_id_foreign');
            $table->dropColumn('store_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_store_id_foreign');
            $table->dropColumn('store_id');
        });
    }
}
