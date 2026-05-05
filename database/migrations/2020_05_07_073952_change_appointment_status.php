<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeAppointmentStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Check if constraint exists before dropping
            $constraints = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = 'appointments' AND CONSTRAINT_NAME = 'appointments_status_check'");

            if (!empty($constraints)) {
                DB::statement("ALTER TABLE appointments DROP CONSTRAINT appointments_status_check");
            }

            $types = ['book', 'check in', 'no show', 'served', 'end served'];
            $result = join(', ', array_map(function ($value) {
                return sprintf("'%s'", $value);
            }, $types));
            DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN ($result))");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            //
        });
    }
}
