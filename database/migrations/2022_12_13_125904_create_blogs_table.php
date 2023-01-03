<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->nullable();
            $table->boolean('visible')->nullable();
            $table->text('category')->nullable();
            $table->string('vaccancylink')->nullable();
            $table->string('vaccancylink1')->nullable();
            $table->string('vaccancylink2')->nullable();
            $table->string('vaccancylink3')->nullable();
            $table->date('publish_start')->nullable();
            $table->date('publish_end')->nullable();
            $table->string('btncolor')->nullable();
            // $table->longText('icons')->nullable();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('blogs');
    }
}
