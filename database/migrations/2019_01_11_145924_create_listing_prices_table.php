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
            $table->integer('monthly_payment');
            $table->integer('processing_fee');
            $table->integer('financial_term');
            $table->integer('yearly_dues');
            $table->integer('taxes');
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
