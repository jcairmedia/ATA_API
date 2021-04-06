<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsDocumentsCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments_documents_cases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documents_cases_id')->comment('FK documents_cases, comment of document');
            $table->unsignedBigInteger('reviewer_user_id')->comment('FK users review document');
            $table->text('comment');
            $table->foreign('documents_cases_id')->references('id')->on('documents_cases');
            $table->foreign('reviewer_user_id')->references('id')->on('users');

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
        Schema::dropIfExists('comments_documents_cases');
    }
}
