<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkstationServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workstation_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workstation_id');
            $table->foreignId('service_id');
            $table->smallInteger('priority')->default(1);
            $table->timestamps();
            
            $table->foreign('workstation_id')->references('id')->on('workstations');
            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workstation_services');
    }
}
