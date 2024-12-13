<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnLogoSizeToTvCustomLayout2Configuration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tv_custom_layout_2_configuration', function (Blueprint $table) {
            $table->string('logo_size')->nullable()->default('2.3');
            $table->string('text_time_size')->nullable()->default('1');
            $table->string('running_text_size')->nullable()->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tv_custom_layout_2_configuration', function (Blueprint $table) {
            $table->dropColumn('logo_size');
            $table->dropColumn('text_time_size');
            $table->dropColumn('running_text_size');
        });
    }
}
