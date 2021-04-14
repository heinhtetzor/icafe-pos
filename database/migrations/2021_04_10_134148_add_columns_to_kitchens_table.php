<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToKitchensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kitchens', function (Blueprint $table) {
            $table->integer('panel_size')->nullable()->after('color');
            $table->integer('font_size')->nullable()->after('panel_size');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kitchens', function (Blueprint $table) {
            $table->dropColumn('panel_size');
            $table->dropColumn('font_size');
        });
    }
}
