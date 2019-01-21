<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('listing_id');
            $table->integer('price');
            $table->integer('monthly_payment')->nullable();
            $table->integer('processing_fee')->nullable();
            $table->integer('financial_term');
            $table->integer('percentage_rate');
            $table->integer('taxes')->nullable();
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
        Schema::dropIfExists('listing_prices');
    }
}
