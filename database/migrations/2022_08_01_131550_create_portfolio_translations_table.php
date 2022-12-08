<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfolioTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('portfolio_translations', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

        Schema::create('portfolio_translations', function (Blueprint $table) {
            // $table->id();
            // $table->timestamps();
            $table->bigIncrements('id');
            $table->bigInteger('portfolio_id')->unsigned();
            $table->string('locale')->index();

            $table->string('name')->nullable();
            $table->string('costumer')->nullable();
            $table->string('duration')->nullable();
            $table->string('about_project')->nullable();
            $table->string('directions')->nullable();
            $table->string('design')->nullable();
            $table->string('video_url')->nullable();
            $table->string('animation')->nullable();
            $table->string('music')->nullable();
            // $table->string('position')->nullable();

            $table->unique(['portfolio_id', 'locale']);
            $table->foreign('portfolio_id')
                ->references('id')
                ->on('portfolio')
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
        Schema::dropIfExists('portfolio_translations');
    }
}
