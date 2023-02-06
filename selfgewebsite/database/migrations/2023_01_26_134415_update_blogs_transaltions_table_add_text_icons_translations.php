<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBlogsTransaltionsTableAddTextIconsTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('blogs_translations', function (Blueprint $table) {
            $table->string('icon1_text')->nullable();
            $table->string('icon2_text')->nullable();
            $table->string('icon3_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('blogs_translations', function (Blueprint $table) {
            $table->dropColumn('icon1_text');
            $table->dropColumn('icon2_text');
            $table->dropColumn('icon3_text');
        });
    }
}
