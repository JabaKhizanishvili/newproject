<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIronTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iron_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('iron_id')->unsigned();
            $table->string('locale')->index();

            $table->string('name')->nullable();
            // $table->longText('description')->nullable();

            $table->unique(['iron_id', 'locale']);
            $table->foreign('iron_id')
                ->references('id')
                ->on('irons')
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
        Schema::dropIfExists('iron_translations');
    }
}
