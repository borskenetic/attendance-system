<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_code', 20)->unique();  // e.g., BSCS
            $table->string('program_name');                // e.g., Bachelor of Science in CS
            $table->unsignedTinyInteger('total_years'); 
            // keep total_years for reference (1–6). We'll still create ProgramYears separately.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
