<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('book_program', function (Blueprint $table) {
            // Drop the existing foreign key first
            $table->dropForeign(['program_id']);

            // Make program_id nullable
            $table->unsignedBigInteger('program_id')->nullable()->change();

            // Re-add the foreign key
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::table('book_program', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->unsignedBigInteger('program_id')->nullable(false)->change();
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });
    }
};
