<?php

/**
 *  database/migrations/2021_07_30_062827_create_products_table.php
 *
 * Date-Time: 30.07.21
 * Time: 10:28
 * @author Insite LLC <hello@insite.international>
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
        'title',
        'description',
        'brand',
        'category',
        'width',
        'height',
        'madein',
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->longText('specifications');
            $table->text('modelnumber');
            $table->boolean('status')->default(true);
            // $table->string('brand_id');
            // $table->string('category_id');
            // $table->string('width');
            // $table->string('height');
            // $table->string('madein');
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
        Schema::dropIfExists('products');
    }
}
