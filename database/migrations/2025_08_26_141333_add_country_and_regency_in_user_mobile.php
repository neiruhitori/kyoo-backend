<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryAndRegencyInUserMobile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_mobile', function (Blueprint $table) {
           $table->unsignedBigInteger('regency')->nullable();
           $table->string('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_mobile', function (Blueprint $table) {
             $table->dropColumn('regency');
             $table->dropColumn('country');
        });
    }
}
