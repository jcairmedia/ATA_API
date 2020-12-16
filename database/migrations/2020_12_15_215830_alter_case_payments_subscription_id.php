<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCasePaymentsSubscriptionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cases_payments', function (Blueprint $table) {
            $table->decimal('amount', 8, 2)->nullable()->after('folio')->comment('Amount charged');
            $table->unsignedBigInteger('subscription_id')->after('cases_id')->nullable()->comment('subscription Id table subscriptions');
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cases_payments', function (Blueprint $table) {
            $table->dropForeign('cases_payments_subscription_id_foreign');
            $table->dropColumn('subscription_id');
            $table->dropColumn('amount');
        });
    }
}
