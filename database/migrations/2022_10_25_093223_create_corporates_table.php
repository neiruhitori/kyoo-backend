<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorporatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->text('address')->nullable();
            $table->string('mobile_phone', 20)->unique();
            $table->string('country');
            $table->char('regency_id', 10);
            $table->string('lat', 50)->nullable();
            $table->string('long', 50)->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('timezone', 10)->nullable();
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
        Schema::dropIfExists('corporates');
    }
}
