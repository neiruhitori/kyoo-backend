<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlotServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slot_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slot_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('max_slots');
            $table->timestamps();

            $table->foreign('slot_id')->references('id')->on('slots');
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
        Schema::dropIfExists('slot_services');
    }
}
