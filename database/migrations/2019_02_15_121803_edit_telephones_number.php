<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditTelephonesNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telephones', function (Blueprint $table) {
            $table->dropColumn('number');
        });

        Schema::table('telephones', function (Blueprint $table) {
            $table->string('number')->after('entity_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telephones', function (Blueprint $table) {
            $table->dropColumn('number');
        });

        Schema::table('telephones', function (Blueprint $table) {
            $table->integer('number')->after('entity_type');
        });
    }
}
