<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceIdOnTableDirectQueues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('direct_queues', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable();
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
        Schema::table('direct_queues', function (Blueprint $table) {
            $table->dropForeign('direct_queues_service_id_foreign');
            $table->dropColumn('service_id');
        });
    }
}
