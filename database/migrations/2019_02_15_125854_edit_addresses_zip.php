<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditAddressesZip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('zip');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->string('zip')->after('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('zip');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->integer('zip')->after('state');
        });
    }
}
