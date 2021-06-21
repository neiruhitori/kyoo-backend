<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkstationIdOnTableDirectQueues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('direct_queues', function (Blueprint $table) {
            $table->foreignId('workstation_id')->nullable();
            $table->foreign('workstation_id')->references('id')->on('workstations');
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
            $table->dropForeign('direct_queues_workstation_id_foreign');
            $table->dropColumn('workstation_id');
        });
    }
}
