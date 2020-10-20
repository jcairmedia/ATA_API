<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Packages´s name');
            $table->string('description')->comment('Packages´s description');
            $table->enum('periodicity', ['MONTHLY', 'YEARLY'])->comment('Peridicity');
            $table->decimal('amount', 8, 2)->comment('Amount');
            $table->integer('state')->default(1)->comment('1:active, 0: no active');
            $table->timestamps();

            $table->index('periodicity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
