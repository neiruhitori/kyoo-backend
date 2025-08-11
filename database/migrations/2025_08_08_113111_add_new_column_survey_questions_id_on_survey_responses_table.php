<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnSurveyQuestionsIdOnSurveyResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->text('survey_type')->nullable();
            $table->unsignedBigInteger('survey_question_id');
            $table->foreign('survey_question_id')->references('id')
                    ->on('survey_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropForeign(['survey_question_id']);
            $table->dropColumn('survey_question_id');
            $table->dropColumn('survey_type');
        });
    }
}
