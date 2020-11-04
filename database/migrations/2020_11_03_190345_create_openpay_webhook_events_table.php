<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenpayWebhookEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openpay_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string("type")->nullable()->comment("type charge");
            $table->string("status")->nullable()->comment("status example completed...");
            $table->string("hook_id")->nullable()->comment("reference field id in json");
            $table->string("order_id")->nullable()->comment("order id");
            $table->text("json")->comment("response json");
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
        Schema::dropIfExists('openpay_webhook_events');
    }
}
