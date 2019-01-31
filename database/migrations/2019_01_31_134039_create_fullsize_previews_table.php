<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFullsizePreviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fullsize_previews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('listing_id');
            $table->integer('fullsize_id');
            $table->integer('preview_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fullsize_previews');
    }
}
