<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTvConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tv_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('image_3')->nullable();
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
        Schema::dropIfExists('tv_configurations');
    }
}
