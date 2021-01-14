<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsForAllUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications_for_all_users', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment("notification's title");
            $table->string('body')->comment("notification's body");
            $table->unsignedBigInteger('user_session_id')->comment('FK table users. user´s created');

            $table->timestamps();
            $table->foreign('user_session_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications_for_all_users');
    }
}
