<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTestQuestionarieAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_test_questionarie_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_tests_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('answer_id');
            $table->string("question");
            $table->string("answer");
            $table->boolean("active")->default(true)->comment("1:active, 0: inactive");
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('question_id')->references('id')->on('questions');
            $table->foreign('customer_tests_id')->references('id')->on('customer_tests');
            $table->foreign('answer_id')->references('id')->on('answers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_test_questionarie_answers');
    }
}
