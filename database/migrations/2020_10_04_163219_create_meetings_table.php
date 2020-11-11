<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->comment('uuid');
            $table->enum('category', ['FREE', 'PAID'])->comment('category');
            $table->enum('type_meeting', ['CALL', 'VIDEOCALL', 'PRESENTIAL'])->comment('Type meeting');
            $table->text('url_meeting')->nullable()->comment('url meeting');
            $table->dateTime('dt_start')->comment('date time beginning meeting');
            $table->dateTime('dt_end')->nullable()->comment('date time end meeting');
            $table->decimal('price', 8, 2)->comment('price');
            $table->boolean('record_state')->default(1)->comment('record´s state: open = 1/ close = 0');
            $table->boolean('paid_state')->default(0)->comment('payment´s state: paid = 1/ not paid = 0');
            $table->dateTime('dt_cancellation')->nullable()->comment('date time cancellation meeting');
            $table->dateTime('dt_close')->nullable()->comment('date time close meeting');
            $table->timestamps();

            $table->index('folio');
            $table->index('category');
            $table->index('type_meeting');
            $table->index('dt_start');
            $table->index('dt_end');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meetings');
    }
}
