<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenpayCustomerCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openpay_customer_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('FK open pay customer');
            $table->foreign('user_id')->references('id')->on('openpay_customers');
            $table->string('id_card_open_pay')->comment('id card in open pay');
            $table->string('card_number')->comment('card´s last 4 number ');
            $table->text('response')->comment('struct json');
            $table->boolean('active')->default(true)->comment('0: no active, 1: active');
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
        Schema::dropIfExists('openpay_customer_cards');
    }
}
