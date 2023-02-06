<?php

/**
 *  database/migrations/2021_07_29_132707_create_category_translations_table.php
 *
 * Date-Time: 29.07.21
 * Time: 17:29
 * @author Insite LLC <hello@insite.international>
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('category_id')->unsigned();
            $table->string('locale')->index();

            $table->string('name')->nullable();
            // $table->string('video_url')->nullable();
            // $table->longText('description')->nullable();

            $table->unique(['category_id', 'locale']);
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_translations');
    }
}
