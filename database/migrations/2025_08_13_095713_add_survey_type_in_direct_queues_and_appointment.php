<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSurveyTypeInDirectQueuesAndAppointment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('direct_queues', function (Blueprint $table) {
            $table->string('survey_type')->nullable();
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('survey_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('direct_queues', function (Blueprint $table) {
            $table->dropColumn('survey_type');
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('survey_type');
        });
    }
}
