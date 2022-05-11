<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQueueVoiceColumnInBranchConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_configurations', function (Blueprint $table) {
            $table->boolean('queue_voice')->default(false)->after('allow_transfer');
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
            $table->dropColumn('queue_voice');
        });
    }
}
