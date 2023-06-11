<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreIdToAdminAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->before('username')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_accounts', function (Blueprint $table) {
            $table->dropForeign('admin_accounts_store_id_foreign');
            $table->dropColumn('store_id');
        });
    }
}
