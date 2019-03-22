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
            $table->smallInteger('sale_type')->nullable();
            $table->float('monthly_payment')->nullable();
            $table->integer('financial_term')->nullable();
            $table->float('percentage_rate')->nullable();
            $table->float('taxes')->nullable();
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
