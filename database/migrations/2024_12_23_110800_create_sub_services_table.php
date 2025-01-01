<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->string('name');
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
        Schema::table('sub_services', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
        });
        Schema::dropIfExists('sub_services');
    }
}
