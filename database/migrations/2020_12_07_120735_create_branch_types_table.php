<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100);
            $table->string('name', 100);
            $table->boolean('isPremium')->default(false);
            $table->boolean('isAppointment')->default(false);
            $table->boolean('isDirectQueue')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_types');
    }
}
