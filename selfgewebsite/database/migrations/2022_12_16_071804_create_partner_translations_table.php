<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerTranslationsTable extends Migration
{

    public function up()
    {
        Schema::create('partner_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('partner_id')->unsigned();
            $table->string('locale')->index();

            $table->string('name')->nullable();

            $table->unique(['partner_id', 'locale']);
            $table->foreign('partner_id')
                ->references('id')
                ->on('partners')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('partner_translations');
    }
}
