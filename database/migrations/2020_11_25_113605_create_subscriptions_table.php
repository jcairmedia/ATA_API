<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cases_id')->comment('FK table cases');

            $table->string('id_card_openpay')->comment('customer card id in open pay');
            $table->string('id_suscription_openpay')->comment('subscription id in open pay');
            $table->string('id_customer_openpay')->comment('customer id in open pay');

            $table->boolean('active')->default(true)->comment('1: active, 0: cancelled ');
            $table->timestamp('dt_cancelation')->nullable()->comment('Cancelation date');
            $table->timestamps();

            $table->index('id_card_openpay');
            $table->index('id_suscription_openpay');
            $table->index('id_customer_openpay');
            $table->foreign('cases_id')->references('id')->on('cases');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
