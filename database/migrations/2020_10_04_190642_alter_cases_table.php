<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->unsignedBigInteger('services_id');
            $table->foreign('services_id')->references('id')->on('services');

            $table->unsignedBigInteger('users_id')->comment('Foreign key User id: user in charge of case');
            $table->foreign('users_id')->references('id')->on('users');

            $table->unsignedBigInteger('customer_id')->comment('Foreign key User id: customer of case');
            $table->foreign('customer_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropForeign('cases_services_id_foreign');
            $table->dropColumn('services_id');

            $table->dropForeign('cases_users_id_foreign');
            $table->dropColumn('users_id');

            $table->dropForeign('cases_customer_id_foreign');
            $table->dropColumn('customer_id');
        });
    }
}
