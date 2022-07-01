<?php

use App\Models\LicenseType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLicenseTypeIdColumnToBranchTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $general_license = LicenseType::where('name', 'Umum')->first();

        Schema::table('branch_types', function (Blueprint $table) use ($general_license) {
            $table->unsignedBigInteger('license_type_id')->default($general_license->id);

            $table->foreign('license_type_id')->references('id')->on('license_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_types', function (Blueprint $table) {
            $table->dropForeign(['license_type_id']);
        });
    }
}
