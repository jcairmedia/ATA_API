<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasesPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cases_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cases_id')->comment('FK');
            $table->string('folio')->comment('Folio');
            $table->enum('type_paid', ['ONLINE', 'OFFLINE'])->comment('type paid');
            $table->string('card_type')->nullable()->comment('PE: AMEX, AMERICAN, VISA');
            $table->string('bank')->nullable()->comment('bank ´s name');
            $table->string('currency')->nullable()->comment('currency');
            $table->string('brand')->nullable()->comment('brand');
            $table->string('bank_auth_code')->nullable()->comment('authorization');
            $table->timestamps();

            $table->foreign('cases_id')->references('id')->on('cases');
            $table->index('type_paid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cases_payments');
    }
}
