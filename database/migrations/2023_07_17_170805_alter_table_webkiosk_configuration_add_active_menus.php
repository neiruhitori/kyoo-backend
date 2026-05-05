<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableWebkioskConfigurationAddActiveMenus extends Migration
{

    // Define the options as variables
    private $menuOptions = ['wa', 'photo', 'print'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webkiosk_configuration', function (Blueprint $table) {
            $table->mediumText('active_menus')->nullable()->after('layout_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webkiosk_configuration', function (Blueprint $table) {
            $table->dropColumn('active_menus');
        });
    }
}
