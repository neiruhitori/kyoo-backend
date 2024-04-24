<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAppointmentOnsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointment_onsites', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable();
            $table->text('address', 255)->nullable();
            $table->string('emergency_number', 20)->nullable();
            $table->string('passport_number')->nullable();
            $table->text('reason_for_visit', 255)->nullable();
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
            $table->dropColumn('date_of_birth');
            $table->dropColumn('address');
            $table->dropColumn('emergency_number');
            $table->dropColumn('passport_number');
            $table->dropColumn('reason_for_visit');
        });
    }
}
