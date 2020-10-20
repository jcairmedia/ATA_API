<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_payments', function (Blueprint $table) {
            $table->id();

            $table->decimal('price', 8, 2)->comment('price');
            $table->string('folio')->nullable()->comment('Folio');
            $table->string('bank_auth_code')->nullable()->comment('bank authorization code');
            $table->enum('type_payment', ['ONLINE', 'OFFLINE'])->nullable()->comment('type payment: ONLINE: payment online, OFFLINE: off-site payment');
            $table->string('card_type')->nullable()->comment('for example:VISA, MASTERCARD, AMEX; etc.');
            $table->string('bank')->nullable()->comment('bank´s name');
            $table->string('currency')->nullable()->comment('currency');
            $table->string('brand')->nullable()->comment('Brand');
            $table->timestamps();

            $table->index('type_payment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_payments');
    }
}
