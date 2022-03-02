<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExhibitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exhibitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('vct_id')->nullable();
            $table->foreignId('slot_id');
            $table->string('booking_code', 5);
            $table->date('date');
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->enum('status', ['book', 'no show', 'end served'])->default('book');
            $table->timestamp('end_served_time')->nullable();
            $table->integer('queue_order')->default(0);
            $table->enum('channel', ['apps', 'VCT web'])->default('apps');
            $table->text('notes')->nullable();
            $table->boolean('is_liked')->default(false);
            $table->smallInteger('rating')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('vct_id')->references('id')->on('users');
            $table->foreign('slot_id')->references('id')->on('slots');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exhibition');
    }
}
