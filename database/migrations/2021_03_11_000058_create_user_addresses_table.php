<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('users_id')->comment('PK from Users');
            $table->primary('users_id');
            $table->unsignedBigInteger('idcp')->comment('FK to postalcodes');
            $table->string('street')->comment("street's name");
            $table->string('out_number')->comment('outdoor number');
            $table->string('int_number')->nullable()->comment('interior number');
            $table->timestamps();

            $table->foreign('idcp')->references('id')->on('postalcodes');
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
        Schema::dropIfExists('user_addresses');
    }
}
