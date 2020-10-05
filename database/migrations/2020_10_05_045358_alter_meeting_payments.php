<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMeetingPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meeting_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('meeting_id')->comment('');
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
        Schema::table('meeting_payments', function (Blueprint $table) {
            $table->dropForeign('meeting_payments_meeting_id_foreign');
            $table->dropColumn('meeting_id');
        });
    }
}
