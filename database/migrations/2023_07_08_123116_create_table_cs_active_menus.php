<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCsActiveMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cs_active_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->foreignId('feature_id');
            $table->timestamps();


            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('feature_id')->references('id')->on('menu_features');

            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cs_active_menus');
    }
}
