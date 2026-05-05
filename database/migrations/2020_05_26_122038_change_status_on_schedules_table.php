<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeStatusOnSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Check if constraint exists before dropping
            $constraints = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = 'schedules' AND CONSTRAINT_NAME = 'schedules_status_check'");

            if (!empty($constraints)) {
                DB::statement("ALTER TABLE schedules DROP CONSTRAINT schedules_status_check");
            }

            $types = ['open', 'closed', 'fullday'];
            $result = join(', ', array_map(function ($value) {
                return sprintf("'%s'", $value);
            }, $types));
            DB::statement("ALTER TABLE schedules ADD CONSTRAINT schedules_status_check CHECK (status IN ($result))");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            //
        });
    }
}
