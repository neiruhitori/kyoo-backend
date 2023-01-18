<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTextPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('branch_id');
            $table->text('text');
            $table->string('color');
            $table->string('font_size');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('text_promotions');
    }
}
