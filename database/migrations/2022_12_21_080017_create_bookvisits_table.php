<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookvisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookvisits', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->text('company')->nullable();
            $table->text('position')->nullable();
            $table->text('phone')->nullable();
            $table->text('mail')->nullable();
            $table->text('quantity')->nullable();
            $table->text('contact')->nullable();
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
        Schema::dropIfExists('bookvisits');
    }
}
