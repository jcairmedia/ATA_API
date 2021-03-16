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
            $table->string('curp')->nullable()->after('phone')->comment("curp");
            $table->unsignedBigInteger('idcp')->nullable()->after('curp')->comment('FK to postalcodes');
            $table->string('street')->nullable()->after('idcp')->comment("street's name");
            $table->string('out_number')->nullable()->after('street')->comment('outdoor number');
            $table->string('int_number')->nullable()->after('out_number')->nullable()->comment('interior number');


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
