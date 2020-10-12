<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('User´s name');
            $table->string('last_name1')->comment('User´s last name');
            $table->string('last_name2')->nullable()->comment('User´s last name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password')->nullable()->comment('Este campo en nullo cuando el usuario se autentico con facebook');

            $table->string('url_image')->nullable()->comment('User´s url image');
            $table->string('phone')->nullable()->comment('User´s phone');

            $table->string('facebook_user_id')->nullable()->comment('User´s facebook folio ');
            $table->boolean('state')->default(true)->comment('User´s state: true => active, false => inactive');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
