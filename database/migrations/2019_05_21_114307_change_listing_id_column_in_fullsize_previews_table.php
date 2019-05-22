<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeListingIdColumnInFullsizePreviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fullsize_previews', function (Blueprint $table) {
            $table->integer('listing_id')->nullable()->change();
        });
    }
}
