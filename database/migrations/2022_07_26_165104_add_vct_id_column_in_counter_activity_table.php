<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVctIdColumnInCounterActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('counter_activity', function (Blueprint $table) {
            $table->unsignedBigInteger('vct_id');

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
        Schema::table('counter_activity', function (Blueprint $table) {
            $table->dropForeign('counter_activity_vct_id_foreign');
            $table->dropColumn('vct_id');
        });
    }
}
