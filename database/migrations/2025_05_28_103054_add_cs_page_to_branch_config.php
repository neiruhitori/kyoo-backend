<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCsPageToBranchConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_configurations', function (Blueprint $table) {
            $table->string('cs_page')->default('style-1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_configurations', function (Blueprint $table) {
            $table->dropColumn('cs_page');
        });
    }
}
