<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('uploaded_files')->default(0)->nullable();
            $table->unsignedInteger('uploaded_files_total')->default(0)->nullable();
            $table->unsignedInteger('visible_files')->default(0)->nullable();
            $table->unsignedInteger('hidden_files')->default(0)->nullable();
            $table->unsignedInteger('trashed_files')->default(0)->nullable();
            $table->unsignedInteger('deleted_files_total')->default(0)->nullable();
            $table->unsignedInteger('used_disk_space_files')->default(0)->nullable();
            $table->unsignedInteger('used_disk_space_thumbs')->default(0)->nullable();
            $table->unsignedInteger('optimized_files_total')->default(0)->nullable();
            $table->unsignedInteger('optimized_files_savings')->default(0)->nullable();
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
        Schema::drop('statistics');
    }
}
