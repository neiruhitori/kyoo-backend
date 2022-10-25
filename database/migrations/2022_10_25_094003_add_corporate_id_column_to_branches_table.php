<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCorporateIdColumnToBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->unsignedBigInteger('corporate_id')->nullable();
            $table->foreign('corporate_id')->references('id')->on('corporates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign('branches_corporate_id_foreign');
            $table->dropColumn('corporate_id');
        });
    }
}
