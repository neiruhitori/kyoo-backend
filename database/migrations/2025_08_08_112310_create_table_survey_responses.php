<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSurveyResponses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')
                    ->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('survey_config_id');
            $table->foreign('survey_config_id')->references('id')
                    ->on('survey_configurations')->onDelete('cascade');
            $table->unsignedBigInteger('direct_queue_id')->nullable();
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->text('queue_type');
            $table->text('name');
            $table->text('email');
            $table->integer('value');
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
        Schema::dropIfExists('table_survey_responses');
    }
}
