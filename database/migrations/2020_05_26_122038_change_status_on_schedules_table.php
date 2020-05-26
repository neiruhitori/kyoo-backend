<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            DB::statement("ALTER TABLE schedules DROP CONSTRAINT schedules_status_check");
            $types = ['open', 'closed', 'fullday'];
            $result = join( ', ', array_map(function( $value ){ return sprintf("'%s'::character varying", $value); }, $types) );
            DB::statement("ALTER TABLE schedules add CONSTRAINT schedules_status_check CHECK (status::text = ANY (ARRAY[$result]::text[]))");
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
