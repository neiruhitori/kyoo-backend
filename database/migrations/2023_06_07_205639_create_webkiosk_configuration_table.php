<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebkioskConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webkiosk_configuration', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('layout_id')->nullable();
            $table->timestamps();


            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('layout_id')->references('id')->on('webkiosk_layout');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webkiosk_configuration');
    }
}
