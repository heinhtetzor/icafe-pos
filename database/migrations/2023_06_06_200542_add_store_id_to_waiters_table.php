<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreIdToWaitersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('waiters', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->before('name')->nullable();
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
        Schema::table('waiters', function (Blueprint $table) {
            $table->dropForeign('waiters_store_id_foreign');
            $table->dropColumn('store_id');
        });
    }
}
