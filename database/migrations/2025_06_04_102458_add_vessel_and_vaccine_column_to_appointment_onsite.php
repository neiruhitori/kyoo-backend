<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVesselAndVaccineColumnToAppointmentOnsite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointment_onsites', function (Blueprint $table) {
           $table->string('agent')->nullable();
           $table->string('vaccine')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointment_onsites', function (Blueprint $table) {
            $table->dropColumn(['agent','vaccine']);
        });
    }
}
