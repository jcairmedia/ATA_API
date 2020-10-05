<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id')->comment('Foreign key service´s id');
            $table->unsignedBigInteger('package_id')->comment('Foreign key package´s id');
            $table->decimal('price', 10, 2);
            $table->timestamps();
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('package_id')->references('id')->on('packages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages_services');
    }
}
