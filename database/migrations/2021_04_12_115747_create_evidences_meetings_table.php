<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvidencesMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evidences_meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reviewer_user_id')->nullable()->comment('PK Users, user review Document');
            $table->unsignedBigInteger('meeting_id')->comment('FK Meeting');
            $table->string('folio')->comment("file's folio");
            $table->string('url')->nullable()->comment("file's url");
            $table->enum('status', [
                'APPROVED',
                'NO_APPROVED',
                'IN_REVIEW_REVIEWER',
                'IN_REVIEW_CUSTOMER',
                'UPLOAD_PENDING',
            ])->comment('1: aprobado, 2: no aprobado, 0: pendiente de revisar, 3: en revision, 4: pendiente de correción por el usuario');
            $table->text('comment')->nullable()->comment('comments');
            $table->timestamp('time_review')->comment('time limit for user upload file');
            $table->bigInteger('number_times_review')->default(0)->comment('time limit for user upload file');
            $table->foreign('meeting_id')->references('id')->on('meetings');
            $table->foreign('reviewer_user_id')->references('id')->on('users');
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
        Schema::dropIfExists('evidences_meetings');
    }
}
