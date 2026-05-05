<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateRoleColumnInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if constraint exists before dropping
            $constraints = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = 'users' AND CONSTRAINT_NAME = 'users_role_check'");

            if (!empty($constraints)) {
                DB::statement("ALTER TABLE users DROP CONSTRAINT users_role_check");
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (\"role\" IN ('admin_kyoo', 'admin_branch', 'cs', 'customer'))");
        });
    }
}
