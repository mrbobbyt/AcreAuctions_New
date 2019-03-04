<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditRegisterTokensTokenLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_tokens', function (Blueprint $table) {
            $table->string('token', 350)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('register_tokens', function (Blueprint $table) {
            $table->string('token', 330)->change();
        });
    }
}
