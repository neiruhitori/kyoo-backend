<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleTemplateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_template_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_template_id');
            $table->date('date');
            $table->string('description');
            $table->timestamps();

            $table->foreign('schedule_template_id')->references('id')->on('schedule_templates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_template_details');
    }
}
