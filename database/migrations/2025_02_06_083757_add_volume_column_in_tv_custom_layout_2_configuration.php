<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVolumeColumnInTvCustomLayout2Configuration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tv_custom_layout_2_configuration', function (Blueprint $table) {
            $table->string('youtube_volume')->default('50');
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
            $table->dropColumn('youtube_volume');
        });
    }
}
