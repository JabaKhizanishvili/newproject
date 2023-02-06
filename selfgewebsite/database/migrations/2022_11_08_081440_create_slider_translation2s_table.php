<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSliderTranslation2sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slider_translation2s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('slider2_id')->unsigned();
            $table->string('locale')->index();

            $table->string('title')->nullable();
            $table->longText('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();

            $table->unique(['slider2_id', 'locale']);
            $table->foreign('slider2_id')
                ->references('id')
                ->on('slider2s')
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
        Schema::dropIfExists('slider_translation2s');
    }
}
