<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsOnAppointment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('vct_id')->after('status')->nullable();
            $table->timestamp('checkin_time')->after('vct_id')->nullable();
            $table->timestamp('served_time')->after('served_time')->nullable();

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
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign('appointments_vct_id_foreign');
            $table->dropColumn(['vct_id', 'checkin_time', 'served_time']);
        });
    }
}
