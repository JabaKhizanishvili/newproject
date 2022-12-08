<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_translations', function (Blueprint $table) {
            // $table->id();
            // $table->timestamps();
            $table->bigIncrements('id');
            $table->bigInteger('staff_id')->unsigned();
            $table->string('locale')->index();

            $table->string('name')->nullable();
            // $table->string('position')->nullable();

            $table->unique(['staff_id', 'locale']);
            $table->foreign('staff_id')
                ->references('id')
                ->on('staffs')
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
        Schema::dropIfExists('staff_translations');
    }
}
