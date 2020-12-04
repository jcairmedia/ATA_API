<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoomRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id')->nullable()->comment('Id meeting');
            $table->string('join_url')->nullable()->comment('Url meeting');
            $table->string('idmeetingzoom')->nullable()->comment('Meeting´s id zoom');
            $table->string('password')->nullable()->comment('password meeting');
            $table->timestamp('start_time')->nullable()->comment('start time meeting');
            $table->string('timezone')->nullable()->comment('time zone zoom');
            $table->text('json')->nullable()->comment('struct json');
            $table->boolean('state_request')->default(true)->comment('1. success, 0. failed');
            $table->foreign('meeting_id')->references('id')->on('meetings');

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
        Schema::dropIfExists('zoom_requests');
    }
}
