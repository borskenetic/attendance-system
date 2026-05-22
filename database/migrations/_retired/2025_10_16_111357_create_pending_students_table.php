<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // database/migrations/xxxx_xx_xx_create_pending_employees_table.php
        Schema::create('pending_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('department');
            $table->string('position')->nullable();
            $table->string('employee_number')->unique(); // ← matches the form input
            $table->date('birth_date')->nullable();
            $table->string('sex', 20)->nullable();
            $table->string('civil_status', 50)->nullable();
            $table->string('blood_type', 5)->nullable();
            $table->string('tin_id_number')->nullable();
            $table->string('philhealth_number')->nullable();
            $table->string('sss_number')->nullable();
            $table->string('hdmf_number')->nullable();
            $table->string('qrcode')->unique()->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->text('address')->nullable();
            $table->string('employee_signature')->nullable();
            $table->timestamps();
        
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_students');
    }
};
