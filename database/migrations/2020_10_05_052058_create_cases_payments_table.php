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
            $table->string('folio')->comment('Folio');
            $table->enum('type_paid', ['ONLINE', 'OFFLINE'])->comment('type paid');
            $table->string('type_target')->nullable()->comment('Type target');
            $table->string('bank')->nullable()->comment('bank ´s name');
            $table->string('currency')->nullable()->comment('currency');
            $table->string('brand')->nullable()->comment('brand');
            $table->string('authorization')->nullable()->comment('authorization');

            $table->unsignedBigInteger('cases_id')->comment('FK');
            $table->foreign('cases_id')->references('id')->on('cases');
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
        Schema::dropIfExists('cases_payments');
    }
}
