<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketLogoToWebkioskLayout4ConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webkiosk_layout_4_configuration', function (Blueprint $table) {
            $table->string('ticket_logo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webkiosk_layout_4_configuration', function (Blueprint $table) {
            $table->dropColumn('ticket_logo');
        });
    }
}
