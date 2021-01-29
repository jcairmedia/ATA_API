<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEventCase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_events', function (Blueprint $table) {
            $table->string('zoom_id')->nullable()->after('url_zoom')->comment('zoom id');
            $table->string('zoom_pass')->nullable()->after('zoom_id')->comment('zoom password');
            $table->text('guests')->nullable()->default('[]')->after('date_start')->comment('guest´s emails');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('case_events', function (Blueprint $table) {
            $table->dropColumn('zoom_id');
            $table->dropColumn('zoom_pass');
            $table->dropColumn('guests');
        });
    }
}
