<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->tinyInteger('maximum_recall')->default(2)->comment('maximum recall for direct queue service');
            $table->tinyInteger('maximum_requeue_count')->default(2)->comment('maximum requeue count for direct queue service');
            $table->boolean('allow_transfer')->default(false)->comment('allow transfer for direct queue service');
            $table->timestamps();
            $table->foreign('branch_id')->references('id')->on('branches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_configurations');
    }
}
