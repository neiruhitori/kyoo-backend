<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeIntervalAndMaxSlotsToBranchConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_configurations', function (Blueprint $table) {
            $table->integer('time_interval')->default(3);
            $table->integer('max_slots')->default(10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_configurations', function (Blueprint $table) {
            //
        });
    }
}
