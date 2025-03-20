<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoiceConfigSignageToBranchConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_configurations', function (Blueprint $table) {
            $table->enum('signage_vo_format', ['wav', 'mp3'])->default('wav');
            $table->string('vo_call_style')->default('standard');
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
            $table->dropColumn(['signage_vo_format', 'vo_call_style']);
        });
    }
}
