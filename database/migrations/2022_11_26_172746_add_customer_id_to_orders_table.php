<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCustomerIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('paid_amount')->after('total');
            $table->bigInteger('customer_id')->unsigned()->nullable()->after('paid_amount');
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        //update paid amount columns to be the same as total
        $sql = "UPDATE `orders` SET `paid_amount` = `total` WHERE 1";
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
}
