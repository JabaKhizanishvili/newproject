<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('team_id')->unsigned();
            $table->string('locale')->index();

            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('title')->nullable();
            $table->string('short_description')->nullable();
            $table->string('description')->nullable();
            $table->string('whattolearn')->nullable();
            $table->string('course_includes')->nullable();
            $table->string('starts')->nullable();

            $table->unique(['team_id', 'locale']);
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
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
        Schema::dropIfExists('team_translations');
    }
}
