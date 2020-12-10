<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_tests', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('questionnaire_id');
            $table->unsignedBigInteger('meeting_id');
            $table->boolean('active')->default(true)->comment('1:active, 0 : inactive');
            $table->boolean('answered')->default(false)->comment('1:anwered, 0 : no answered');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('questionnaire_id')->references('id')->on('questionnaires');
            $table->foreign('meeting_id')->references('id')->on('meetings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_tests');
    }
}
