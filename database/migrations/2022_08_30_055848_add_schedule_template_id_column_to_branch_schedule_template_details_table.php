<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduleTemplateIdColumnToBranchScheduleTemplateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_schedule_template_details', function (Blueprint $table) {
            $table->bigInteger('schedule_template_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_schedule_template_details', function (Blueprint $table) {
            $table->dropColumn('schedule_template_id');
        });
    }
}
