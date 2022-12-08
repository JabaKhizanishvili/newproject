<?php
/**
 *  database/migrations/2021_06_10_055237_create_files_table.php
 *
 * Date-Time: 10.06.21
 * Time: 09:54
 * @author Insite LLC <hello@insite.international>
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('fileable_type')->nullable();
            $table->integer('fileable_id')->nullable();
            $table->string('title')->nullable();
            $table->string('path')->nullable();
            $table->string('format')->nullable();
            $table->integer('type')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
