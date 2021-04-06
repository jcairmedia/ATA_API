<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents_cases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reviewer_user_id')->nullable()->comment('PK Users, user review Document');
            $table->unsignedBigInteger('case_id')->comment('FK cases');
            $table->string('folio')->comment("file's folio");
            $table->string('url')->nullable()->comment("file's url");
            $table->enum('status', [
                'APPROVED',
                'NO_APPROVED',
                'IN_REVIEW_REVIEWER',
                'IN_REVIEW_CUSTOMER',
                'UPLOAD_PENDING',
            ])->comment('1: aprobado, 2: no aprobado, 0: pendiente de revisar, 3: en revision, 4: pendiente de correción por el usuario');
            $table->timestamp('time_review')->comment('time limit for user upload file');
            $table->bigInteger('number_times_review')->default(0)->comment('time limit for user upload file');
            $table->foreign('case_id')->references('id')->on('cases');
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
        Schema::dropIfExists('documents_cases');
    }
}
