<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('case_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id')->comment('FK table cases');
            $table->string('subject')->comment('Event subject ');
            $table->text('description')->comment('Event description ');
            $table->string('url_zoom')->nullable()->comment('Url zoom');
            $table->timestamp('date_start')->nullable()->comment('Event date ');

            $table->timestamps();
            $table->foreign('case_id')->references('id')->on('cases');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('case_events');
    }
}
