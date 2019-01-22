<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingGeosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_geos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('listing_id');
            $table->double('acreage');
            $table->string('state');
            $table->string('county')->comment('district');
            $table->string('city');
            $table->string('address');
            $table->integer('zip');
            $table->smallInteger('road_access');
            $table->double('longitude');
            $table->double('latitude');
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
        Schema::dropIfExists('listing_geos');
    }
}
