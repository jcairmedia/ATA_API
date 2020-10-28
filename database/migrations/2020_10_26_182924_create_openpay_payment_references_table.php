<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenpayPaymentReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openpay_payment_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id')->comment('Foreign key Meeting id');
            $table->string('description')->nullable()->comment('Description case');
            $table->string('error_message')->nullable()->comment('Any message of error');
            $table->string('authorization')->nullable()->comment('');
            $table->decimal('amount', 8, 2)->comment('Amount paid');
            $table->string('operation_type')->nullable()->comment('is in');
            $table->string('payment_type')->nullable()->comment('it is store');
            $table->string('payment_reference')->comment('it is a number alphanumeric');
            $table->string('payment_barcode_url')->nullable()->comment('url of stipes code');
            $table->string('order_id')->nullable()->comment('any number');
            $table->string('transaction_type')->nullable()->comment('this value is charge');
            $table->string('creation_date')->nullable()->comment('it is Y:m:dTH:i:s');
            $table->string('currency')->nullable()->comment('this value is MXN');
            $table->string('status')->nullable()->comment('this value is in_progress');
            $table->string('method')->nullable()->comment('this value is store');
            $table->text('json_create_reference')->nullable()->comment('json or struct out create reference open pay');
            $table->text('json_complete_refence')->nullable()->comment('json or struct out complete reference open pay');
            $table->timestamps();
            $table->foreign('meeting_id')->references('id')->on('meetings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('openpay_payment_references');
    }
}
