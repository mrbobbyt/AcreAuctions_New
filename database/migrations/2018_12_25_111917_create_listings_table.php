<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inner_listing_id')->nullable();
            $table->string('apn');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_verified')->default(0);
            $table->integer('seller_id')
                ->comment('connected seller table');
            $table->smallInteger('utilities');
            $table->smallInteger('zoning')
                ->comment('categories');
            $table->string('zoning_desc');
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
        Schema::dropIfExists('listings');
    }
}
