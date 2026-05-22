<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('program_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_year_id')->constrained()->cascadeOnDelete();
            $table->string('course_code', 20);   // e.g., CS101
            $table->string('course_name');       // e.g., Intro to Programming
            $table->timestamps();

            // Avoid duplicate codes within the same year grid:
            $table->unique(['program_year_id', 'course_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_courses');
    }
};
