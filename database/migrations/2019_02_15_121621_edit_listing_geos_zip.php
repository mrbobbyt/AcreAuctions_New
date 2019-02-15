<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditListingGeosZip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listing_geos', function (Blueprint $table) {
            $table->dropColumn('zip');
        });

        Schema::table('listing_geos', function (Blueprint $table) {
            $table->string('zip')->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listing_geos', function (Blueprint $table) {
            $table->dropColumn('zip');
        });

        Schema::table('listing_geos', function (Blueprint $table) {
            $table->integer('zip')->after('address');
        });
    }
}
