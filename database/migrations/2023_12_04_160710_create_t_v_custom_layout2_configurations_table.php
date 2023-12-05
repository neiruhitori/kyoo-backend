<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTVCustomLayout2ConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tv_custom_layout_2_configuration', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_configuration_id')->nullable();

            $table->enum('background_type', ['color', 'image'])->default('color');
            $table->string('background_image')->nullable();
            $table->string('background_color')->default('#192db3');

            $table->string('datetime_color')->default('#FFFF');
            $table->string('sidebar_subtitle_color')->default('#FFFF');
            $table->string('waiting_list_card_color')->default('#FFFF');
            $table->string('waiting_list_font_color')->default('#0000');
            $table->string('calling_card_header_color')->default('#FFFF');
            $table->string('calling_card_body_color')->default('#233c8c');
            $table->string('calling_card_font_header_color')->default('#233c8c');
            $table->string('font_queue_first_letter_color')->default('#0000');
            $table->string('font_queue_color')->default('#FFFF');

            $table->string('running_text_color')->default('#FFFF');

            $table->timestamps();

            $table->foreign('tv_configuration_id')->references('id')->on('tv_configurations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_v_custom_layout2_configurations');
    }
}
