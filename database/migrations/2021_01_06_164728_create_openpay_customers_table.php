<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenpayCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openpay_customers', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->comment('relation one to one with table users');
            $table->foreign('id')->references('id')->on('users');
            $table->primary('id');
            $table->string('id_open_pay')->comment('open pay´s token identifier');
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
        Schema::dropIfExists('openpay_customers');
    }
}
