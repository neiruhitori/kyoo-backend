<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_type_id');
            $table->integer('prices');
            $table->enum('billing_types',['lite','premium','custom']);
            $table->timestamps();

            $table->foreign('branch_type_id')->references('id')->on('branch_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_prices');
    }
}
