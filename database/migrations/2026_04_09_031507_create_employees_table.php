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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // relasi ke user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // relasi ke client
            $table->foreignId('client_id')->constrained()->onDelete('cascade');

            // identitas utama
            $table->string('employee_id')->unique(); // NIK
            $table->string('full_name');
            $table->string('nik_ktp')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // pekerjaan
            $table->string('position')->nullable();
            $table->string('division')->nullable();
            $table->string('placement')->nullable(); // lokasi kerja

            // kontrak outsource
            $table->date('join_date'); // mulai kerja
            $table->date('contract_start');
            $table->date('contract_end');
            $table->integer('contract_extension_count')->default(0);

            $table->boolean('absent_using_distance')->default(false);
            // status
            $table->enum('status', ['active', 'inactive', 'resigned'])
                ->default('active');

            $table->text('notes')->nullable();
            $table->longText('face_descriptor')->nullable();

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
        Schema::dropIfExists('employees');
    }
};
