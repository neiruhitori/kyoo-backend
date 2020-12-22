<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direct_queues', function (Blueprint $table) {
            $table->id();
            $table->integer('queue_no')->unsigned();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('vct_id');
            $table->foreignId('workstation_id');
            $table->foreignId('service_id');
            $table->string('name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('direct_queue_channel', 100)->default('web')->comment('where the direct queue created from (web / apps)');
            $table->string('status', 20)->default('waiting')->comment('statuses are waiting, call, requeue, unattend, done');
            $table->smallInteger('recall_count')->default(0);
            $table->smallInteger('requeue_count')->default(0);
            $table->timestamp('called_at')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('vct_id')->references('id')->on('users');
            $table->foreign('workstation_id')->references('id')->on('workstations');
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
        Schema::dropIfExists('direct_queues');
    }
}
