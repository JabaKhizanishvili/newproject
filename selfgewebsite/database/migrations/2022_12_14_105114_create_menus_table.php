<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->text('parent_id')->nullable();
            $table->text('slider')->nullable();
            $table->text('sliderlike')->nullable();
            $table->text('slider_order')->nullable();
            $table->text('slider1')->nullable();
            $table->text('slider1_order')->nullable();
            $table->text('partners')->nullable();
            $table->text('partners_order')->nullable();
            $table->text('reports')->nullable();
            $table->text('reports_order')->nullable();
            $table->text('subscribers')->nullable();
            $table->text('subscribers_order')->nullable();
            $table->text('blog')->nullable();
            $table->text('blog_order')->nullable();
            $table->text('layout')->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('show')->nullable();
            $table->boolean('showinfooter')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
