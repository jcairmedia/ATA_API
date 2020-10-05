<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMeetingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->unsignedBigInteger('contacts_id')->comment('Foreign key Contact id');
            $table->foreign('contacts_id')->references('id')->on('contacts');

            $table->unsignedBigInteger('users_id')->comment('Foreign key User id');
            $table->foreign('users_id')->references('id')->on('users');
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
            $table->dropForeign('meetings_contacts_id_foreign');
            $table->dropColumn('contacts_id');
            $table->dropForeign('meetings_users_id_foreign');
            $table->dropColumn('users_id');
        });
    }
}
