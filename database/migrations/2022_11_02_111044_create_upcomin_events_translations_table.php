<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpcominEventsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upcomin_events_translations', function (Blueprint $table) {

            
            $table->bigIncrements('id');
            $table->bigInteger('upcoming_event_id')->unsigned();
            $table->string('locale')->index();

            $table->string('locations')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->unique(['upcoming_event_id', 'locale']);
            $table->foreign('upcoming_event_id')
                ->references('id')
                ->on('upcoming_events')
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
        Schema::dropIfExists('upcomin_events_translations');
    }
}
