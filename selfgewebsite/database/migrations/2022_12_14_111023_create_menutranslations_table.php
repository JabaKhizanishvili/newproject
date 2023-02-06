<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenutranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menutranslations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('menu_id')->unsigned();
            $table->string('locale')->index();

            $table->string('name')->nullable();
            $table->boolean('visible')->nullable();

            $table->string("meta_title")->nullable();
            $table->longText("meta_description")->nullable();
            $table->longText("meta_keyword")->nullable();
            $table->longText("meta_og_title")->nullable();
            $table->longText("meta_og_description")->nullable();

            $table->unique(['menu_id', 'locale']);
            $table->foreign('menu_id')
                ->references('id')
                ->on('menus')
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
        Schema::dropIfExists('menutranslations');
    }
}
