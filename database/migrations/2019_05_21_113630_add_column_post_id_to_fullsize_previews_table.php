<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPostIdToFullsizePreviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fullsize_previews', function (Blueprint $table) {
            $table->integer('post_id')->nullable()->after('listing_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fullsize_previews', function (Blueprint $table) {
            $table->dropColumn('post_id');
        });
    }
}
