<?php

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
        Schema::enableForeignKeyConstraints();
        Schema::create("files", function (Blueprint $table) {
            $table->increments("id");
            $table->string("code");
            $table->string("location");
            $table->string("mimetype");
            $table->string("type");
            $table->boolean("visible")->default(true);
            $table->unsignedInteger("size");
            $table->unsignedInteger("size_optimized")->nullable();
            $table->unsignedInteger("views")->default(0);
            $table->string("crc_original");
            $table->string("crc");
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger("user_id");

            // Infromation
            // These can be in their own tables, but then it becomes quite cumbersome to join them with Eloquent
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->decimal('vid_fps')->nullable();
            $table->string('vid_codec')->nullable();

            $table->index("code");
            //$table->index("location");
            $table->foreign("user_id")->references("id")->on("users")->onConflict("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("files");
    }
}
