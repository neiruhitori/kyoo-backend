<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQueueTypeOnRegisterBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registration_branches', function (Blueprint $table) {
            $table->string('queue_type', 100)->default('direct_queue');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registration_branches', function (Blueprint $table) {
            $table->dropColumn('queue_type');
        });
    }
}
