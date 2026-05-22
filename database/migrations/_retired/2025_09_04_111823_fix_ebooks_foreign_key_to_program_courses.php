<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ebooks', function (Blueprint $table) {
            // Drop wrong foreign key
            $table->dropForeign('ebooks_course_id_foreign');

            // Add correct foreign key to program_courses
            $table->foreign('course_id')
                  ->references('id')->on('program_courses')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ebooks', function (Blueprint $table) {
            $table->dropForeign(['course_id']);

            // Revert back (NOT recommended but required for down)
            $table->foreign('course_id')
                  ->references('id')->on('courses')
                  ->onDelete('set null');
        });
    }
};
