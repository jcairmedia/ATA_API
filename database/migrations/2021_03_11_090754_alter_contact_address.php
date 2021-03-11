<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterContactAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('idcp')->comment('FK to postalcodes');
            $table->string('street')->comment("street's name");
            $table->string('out_number')->comment('outdoor number');
            $table->string('int_number')->nullable()->comment('interior number');
            $table->timestamps();

            $table->foreign('idcp')->references('id')->on('postalcodes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('idcp');
            $table->dropColumn('street');
            $table->dropColumn('out_number');
            $table->dropColumn('int_number');
            $table->dropForeign('contacts_idcp_foreign');
        });
    }
}
