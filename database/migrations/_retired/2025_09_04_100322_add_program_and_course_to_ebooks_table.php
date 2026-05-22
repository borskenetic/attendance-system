<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ebooks', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id')->nullable()->after('link');
            $table->unsignedBigInteger('course_id')->nullable()->after('program_id');
    
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('ebooks', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropForeign(['course_id']);
            $table->dropColumn(['program_id', 'course_id']);
        });
    }

};
