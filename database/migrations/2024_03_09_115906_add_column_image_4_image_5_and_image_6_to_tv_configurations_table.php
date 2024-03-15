<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnImage4Image5AndImage6ToTvConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tv_configurations', function (Blueprint $table) {
            $table->string('image_4')->nullable();
            $table->string('image_5')->nullable();
            $table->string('image_6')->nullable();
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
            $table->dropColumn('image_6');
            $table->dropColumn('image_5');
            $table->dropColumn('image_4');
        });
    }
}
