<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            DB::statement("ALTER TABLE appointments DROP CONSTRAINT appointments_status_check");
            $types = ['book', 'check in', 'no show', 'served', 'end served'];
            $result = join( ', ', array_map(function( $value ){ return sprintf("'%s'::character varying", $value); }, $types) );
            DB::statement("ALTER TABLE appointments add CONSTRAINT appointments_status_check CHECK (status::text = ANY (ARRAY[$result]::text[]))");
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
