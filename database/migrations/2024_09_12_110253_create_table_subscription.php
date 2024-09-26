<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSubscription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id');
            $table->foreignId('branch_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('branch_id')->references('id')->on('branches');

            $table->string('invoice');
            
            $table->string('package');
            $table->string('license_type');
            $table->integer('subs_duration');
            $table->integer('queue');
            $table->integer('max_table');
            $table->integer('max_service');
            $table->integer('kiosk');
            $table->enum('status',['pending','active','expired'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_subscription');
    }
}
