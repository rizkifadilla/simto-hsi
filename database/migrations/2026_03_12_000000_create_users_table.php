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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // basic auth
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            // role system
            $table->enum('role', ['admin', 'talent acquisition', 'employee'])
                ->default('employee');

            // relasi ke karyawan
            $table->string('employee_id')->unique()->nullable();

            // perusahaan outsource
            $table->string('company')->nullable();

            // status user (penting buat nonaktifin tanpa delete)
            $table->boolean('is_active')->default(true);

            // audit
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
