<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnSubServiceIdOnDirectQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('direct_queues', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_service_id')->nullable();
            $table->foreign('sub_service_id')
                  ->references('id')
                  ->on('sub_services')
                  ->onDelete('set null');
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
            $table->dropForeign(['sub_service_id']);
            $table->dropColumn('sub_service_id');
        });
    }
}
