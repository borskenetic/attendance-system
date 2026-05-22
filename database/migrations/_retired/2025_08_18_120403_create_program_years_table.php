<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('program_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('year_level'); // 1,2,3,4,5,6...
            $table->timestamps();

            $table->unique(['program_id', 'year_level']); // prevent duplicate Year 1 for the same program
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_years');
    }
};
