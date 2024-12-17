<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogoSizeWebkioskOnWebkioskLayout4Configuration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webkiosk_layout_4_configuration', function (Blueprint $table) {
            $table->string('logo_size')->nullable()->default('60');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webkiosk_layout_4_configuration', function (Blueprint $table) {
            $table->dropColumn('logo_size');
        });
    }
}
