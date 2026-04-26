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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->date('date');

            // CHECK IN
            $table->time('check_in')->nullable();
            $table->decimal('check_in_lat', 10, 7)->nullable();
            $table->decimal('check_in_long', 10, 7)->nullable();
            $table->string('check_in_photo')->nullable();

            // CHECK OUT
            $table->time('check_out')->nullable();
            $table->decimal('check_out_lat', 10, 7)->nullable();
            $table->decimal('check_out_long', 10, 7)->nullable();
            $table->string('check_out_photo')->nullable();

            // RESULT
            $table->integer('working_minutes')->nullable(); // biar fleksibel
            $table->text('task')->nullable(); // timesheet isi di sini

            // VALIDATION
            $table->boolean('is_within_radius')->default(false);
            $table->boolean('is_face_valid')->default(false);

            $table->timestamps();

            $table->unique(['employee_id', 'date']); // 1 hari 1 absen
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
