<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_id')->unique()->nullable()->after('id');
            $table->date('birth_date')->nullable()->after('student_id');
            $table->string('blood_type', 5)->nullable()->after('birth_date');
            $table->string('emergency_contact_name')->nullable()->after('blood_type');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_number')->nullable()->after('emergency_contact_relationship');
            $table->string('student_signature')->nullable()->after('emergency_contact_number');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'student_id',
                'birth_date',
                'blood_type',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_number',
                'student_signature',
            ]);
        });
    }
};
