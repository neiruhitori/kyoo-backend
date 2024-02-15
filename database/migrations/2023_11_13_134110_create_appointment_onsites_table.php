<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentOnsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_onsites', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email');
            $table->string('phone', 20);
            $table->string('fcm_id')->nullable();
            $table->string('client_id')->nullable();
            $table->string('booking_code', 6)->unique();
            $table->foreignId('service_id');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->date('date');
            $table->boolean('is_used')->default(false);

            $table->foreign('service_id')->references('id')->on('services');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointment_onsites');
    }
}
