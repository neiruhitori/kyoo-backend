<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industry_category_id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('country');
            $table->string('phone', 20);
            $table->text('address');
            $table->char('regency_id', 10);
            $table->boolean('is_email_verified')->default(false);
            $table->timestamps();
            $table->softdeletes();

            $table->foreign('industry_category_id')->references('id')->on('industry_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_branches');
    }
}
