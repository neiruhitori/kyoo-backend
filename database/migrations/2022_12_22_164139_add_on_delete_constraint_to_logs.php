<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnDeleteConstraintToLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropForeign('logs_user_id_foreign');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropForeign('logs_user_id_foreign');

            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete()->change();
        });
    }
}
