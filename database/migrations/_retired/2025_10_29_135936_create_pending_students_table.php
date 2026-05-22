<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->nullable(); // optional internal ID
            $table->string('id_number')->nullable(); // can be provided or generated
            $table->string('firstname');
            $table->string('lastname');
            $table->string('course');
            $table->string('year')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('qrcode')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('student_signature')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_students');
    }
};
