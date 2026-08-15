<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();

            $table->text('cover_letter')->nullable();
            $table->string('status')->default('submitted');
            $table->date('interview_date')->nullable();
            $table->time('interview_time')->nullable();
            $table->string('interview_location')->nullable();

            $table->boolean('is_followed_up')->default(false);
            $table->timestamp('followed_up_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['job_id', 'applicant_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
