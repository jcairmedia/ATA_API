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

            $table->decimal('price', 10, 2)->comment('price');
            $table->string('folio')->nullable()->comment('Folio');
            $table->string('authorization_bank')->nullable()->comment('authorization bank');
            $table->enum('type_payment', ['ONLINE', 'OFFLINE'])->nullable()->comment('type payment');
            $table->string('type_target')->nullable()->comment('type target');
            $table->string('bank')->nullable()->comment('nombre del banco');
            $table->string('currency')->nullable()->comment('currency');
            $table->string('brand')->nullable()->comment('Brand');
            $table->string('payment_gateway')->nullable()->comment('Payment gateway');
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
        Schema::dropIfExists('meeting_payments');
    }
}
