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
    Schema::create('attendance_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('student_id');
        $table->enum('status', ['in', 'out']);
        $table->dateTime('scanned_at');
        $table->timestamps();

        $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
