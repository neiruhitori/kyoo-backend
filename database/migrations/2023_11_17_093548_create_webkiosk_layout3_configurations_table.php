<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebkioskLayout3ConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webkiosk_layout_3_configuration', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webkios_configuration_id')->nullable();

            $table->enum('primary_background_type', ['color', 'image'])->default('color');
            $table->string('primary_background_image')->nullable();
            $table->string('primary_background_color')->default('#FFFF');

            $table->enum('secondary_background_type', ['color', 'image'])->default('color');
            $table->string('secondary_background_image')->nullable();
            $table->string('secondary_background_color')->default('#0C61A2');

            $table->string('button_background_color')->default('#0C30A8');
            $table->string('botton_border_color')->default('#FFFF');
            $table->string('font_color')->default('#FFFF');

            $table->timestamps();

            $table->foreign('webkios_configuration_id')->references('id')->on('webkiosk_configuration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webkiosk_layout3_configurations');
    }
}
