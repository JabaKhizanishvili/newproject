<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfolioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolio', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('costumer');
            $table->string('duration');
            $table->string('category_id');
            $table->string('about_project');
            $table->string('video_url');
            $table->string('directions');
            $table->string('design');
            $table->string('animation');
            $table->string('music');
            $table->boolean('status');
            // $table->string('position');
            // $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portfolio');
    }
}
