<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("external_category_id")->comment("Category entry");
            $table->string("title")->comment("Title");
            $table->string("description")->comment("short description");
            $table->string("body")->comment("Body");
            $table->string("url_img_main")->comment("Main img's url");
            $table->string("name_img_main")->comment("Main img's name");
            $table->enum("status", ["PUBLISHED", "DRAFT"])->default("DRAFT");
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('external_category_id')->references('id')->on('external_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_entries');
    }
}
