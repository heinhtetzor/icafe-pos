<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartedAtAndWaiterIdToTableStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('table_statuses', function (Blueprint $table) {   
            $table->dateTime('started_at')->nullable()->after('status');
            $table->foreignId('waiter_id')->nullable()->after('started_at');   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table_statuses', function (Blueprint $table) {
            $table->dropColumn('started_at');
            $table->dropColumn('waiter_id');
        });
    }
}
