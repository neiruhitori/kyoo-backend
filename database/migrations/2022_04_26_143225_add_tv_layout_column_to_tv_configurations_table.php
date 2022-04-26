<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTvLayoutColumnToTvConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tv_configurations', function (Blueprint $table) {
            $table->foreignId('tv_layout_id');

            $table->foreign('tv_layout_id')->references('id')->on('tv_layouts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tv_configurations', function (Blueprint $table) {
            $table->dropColumn('tv_layout_id');
        });
    }
}
