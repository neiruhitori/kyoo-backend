<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCounterActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counter_activity', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('workstation_id');
            $table->foreignId('service_id');
            $table->unsignedBigInteger('operation_duration');
            $table->datetime('last_login');
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
        Schema::dropIfExists('counter_activity');
    }
}
