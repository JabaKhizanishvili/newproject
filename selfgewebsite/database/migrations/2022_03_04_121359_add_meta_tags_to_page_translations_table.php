<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetaTagsToPageTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('page_translations', function (Blueprint $table) {
            $table->string('meta_keyword')->nullable()->after("meta_description");
            $table->string('meta_og_title')->nullable()->after("meta_keyword");
            $table->string('meta_og_description')->nullable()->after("meta_og_title");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page_translations', function (Blueprint $table) {
            $table->dropColumn("meta_keyword");
            $table->dropColumn("meta_og_title");
            $table->dropColumn("meta_og_description");
        });
    }
}
