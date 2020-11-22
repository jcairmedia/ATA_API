<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendarEventMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_event_meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meetings_id')->comment('FK');
            $table->String('idevent')->comment("Event's Id");
            $table->String('idcalendar')->comment("Calendar's Id");
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
        Schema::dropIfExists('calendar_event_meetings');
    }
}
