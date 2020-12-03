<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMeetingRescheduler01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dateTime('dt_start_rescheduler')->nullable()->comment('date time beginning resheduler meeting');
            $table->dateTime('dt_end_rescheduler')->nullable()->comment('date time end resheduler meeting');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn('dt_start_rescheduler');
            $table->dropColumn('dt_end_rescheduler');
        });
    }
}
