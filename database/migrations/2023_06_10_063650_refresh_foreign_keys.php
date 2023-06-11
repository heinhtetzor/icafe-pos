<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefreshForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //expense items
        Schema::table('expense_items', function (Blueprint $table) {
            $table->dropForeign('expense_items_expense_id_foreign');
            $table->foreign('expense_id')
            ->references('id')->on('expenses')
            ->onUpdate('cascade');
            
            $table->dropForeign('expense_items_item_id_foreign');
            $table->foreign('item_id')
            ->references('id')->on('items')
            ->onUpdate('cascade');

            $table->dropForeign('expense_items_menu_group_id_foreign');
            $table->foreign('menu_group_id')
            ->references('id')->on('menu_groups')
            ->onUpdate('cascade');
        });

        //expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_user_id_foreign');
            $table->foreign('user_id')
            ->references('id')->on('admin_accounts')
            ->onUpdate('cascade');
        });

        //expense_stock_menus
        Schema::table('expense_stock_menus', function (Blueprint $table) {
            $table->dropForeign('expense_stock_menus_expense_id_foreign');
            $table->foreign('expense_id')
            ->references('id')->on('expenses')
            ->onUpdate('cascade');
            
            $table->dropForeign('expense_stock_menus_stock_menu_id_foreign');
            $table->foreign('stock_menu_id')
            ->references('id')->on('stock_menus')
            ->onUpdate('cascade');
        });

        //items
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign('items_menu_group_id_foreign');
            $table->foreign('menu_group_id')
            ->references('id')->on('menu_groups')
            ->onUpdate('cascade');
        });

        //menu_group_kitchens
        Schema::table('menu_group_kitchens', function (Blueprint $table) {
            $table->dropForeign('menu_group_kitchens_menu_group_id_foreign');
            $table->foreign('menu_group_id')
            ->references('id')->on('menu_groups')
            ->onUpdate('cascade');
            
            // $table->dropForeign(['kitchen_id']); //delete
            $table->foreign('kitchen_id')
            ->references('id')->on('kitchens')
            ->onUpdate('cascade'); //add missing foreign key
        });

        //menus
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign('menus_menu_group_id_foreign');
            $table->foreign('menu_group_id')
            ->references('id')->on('menu_groups')
            ->onUpdate('cascade');
        });

        //order menus
        Schema::disableForeignKeyConstraints();
        Schema::table('order_menus', function (Blueprint $table) {
            // $table->dropForeign(['order_id']); //delete
            // $table->dropForeign(['waiter_id']); //delete
            // $table->dropForeign(['menu_id']); //delete

            //add missing foreign keys
            $table->foreign('order_id')
            ->references('id')->on('orders')
            ->onUpdate('cascade');

            $table->foreign('waiter_id')
            ->references('id')->on('waiters')
            ->onUpdate('cascade');

            $table->foreign('menu_id')
            ->references('id')->on('menus')
            ->onUpdate('cascade');
        });
        Schema::enableForeignKeyConstraints();

        //orders
        Schema::disableForeignKeyConstraints();
        Schema::table('orders', function (Blueprint $table) {
            // $table->dropForeign(['waiter_id']); //delete
            // $table->dropForeign(['table_id']); //delete

            //add missing foreign keys
            $table->foreign('table_id')
            ->references('id')->on('tables')
            ->onUpdate('cascade');
            $table->foreign('waiter_id')
            ->references('id')->on('waiters')
            ->onUpdate('cascade');
        });
        Schema::enableForeignKeyConstraints();

        //settings
        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign('settings_store_id_foreign');
            $table->foreign('store_id')
            ->references('id')->on('stores')
            ->onUpdate('cascade');
        });

        //stock_menu_entries
        Schema::disableForeignKeyConstraints();
        Schema::table('stock_menu_entries', function (Blueprint $table) {
            $table->dropForeign('stock_menu_entries_expense_stock_menu_id_foreign');
            $table->foreign('expense_stock_menu_id')
            ->references('id')->on('expense_stock_menus')
            ->onUpdate('cascade');
            $table->dropForeign('stock_menu_entries_order_menu_id_foreign');
            $table->foreign('order_menu_id')
            ->references('id')->on('order_menus')
            ->onUpdate('cascade');
            $table->dropForeign('stock_menu_entries_stock_menu_id_foreign');
            $table->foreign('stock_menu_id')
            ->references('id')->on('stock_menus')
            ->onUpdate('cascade');
        });
        Schema::enableForeignKeyConstraints();

        //stock_menus
        Schema::table('stock_menus', function (Blueprint $table) {
            $table->dropForeign('stock_menus_menu_id_foreign');
            $table->foreign('menu_id')
            ->references('id')->on('menus')
            ->onUpdate('cascade');
        });

        //tables
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign('tables_table_group_id_foreign');
            $table->foreign('table_group_id')
            ->references('id')->on('table_groups')
            ->onUpdate('cascade');
        });

        //table_statuses
        Schema::disableForeignKeyConstraints();
        Schema::table('table_statuses', function (Blueprint $table) {
            // $table->dropForeign(['waiter_id']); //delete
            // $table->dropForeign(['order_id']); //delete

            //add missing foreign keys
            $table->foreign('waiter_id')
            ->references('id')->on('waiters')
            ->onUpdate('cascade');
            $table->foreign('order_id')
            ->references('id')->on('orders')
            ->onUpdate('cascade');
            $table->foreign('table_id')
            ->references('id')->on('tables')
            ->onUpdate('cascade');
        });
        Schema::enableForeignKeyConstraints();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
