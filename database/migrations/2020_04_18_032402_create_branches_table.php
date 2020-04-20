<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industry_category_id');
            $table->foreignId('schedule_template_id')->nullable();
            $table->string('name');
            $table->string('email');
            $table->text('address');
            $table->text('description');
            $table->string('fixed_phone', 20)->nullable();
            $table->string('mobile_phone', 20);
            $table->decimal('lat');
            $table->decimal('long');
            $table->string('country');
            $table->char('regency_id', 10);
            $table->string('logo');
            $table->string('photo')->nullable();
            $table->integer('likes')->default(0);
            $table->boolean('is_active')->default(false);
            $table->enum('status', ['unverified', 'verified', 'rejected'])->default('unverified');
            $table->timestamps();
            $table->softdeletes();

            $table->foreign('industry_category_id')->references('id')->on('industry_categories');
            $table->foreign('schedule_template_id')->references('id')->on('schedule_templates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
