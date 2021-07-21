<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkstationVctsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workstation_vcts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workstation_id');
            $table->foreignId('vct_id');
            $table->timestamps();

            $table->foreign('workstation_id')->references('id')->on('workstations');
            $table->foreign('vct_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workstation_vcts');
    }
}
