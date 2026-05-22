<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'id_number')) {
                $table->string('id_number')->unique()->nullable()->after('id');
            }

            if (!Schema::hasColumn('students', 'middle_initial')) {
                $table->string('middle_initial')->nullable()->after('firstname');
            }

            if (!Schema::hasColumn('students', 'birthday')) {
                $table->date('birthday')->nullable()->after('middle_initial');
            }
            
            if (!Schema::hasColumn('students', 'student_signature')) {
                $table->string('student_signature')->nullable()->after('birthday');;
            }
            
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columns = [];

            foreach (['id_number', 'middle_initial', 'birthday'] as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $columns[] = $col;
                }
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
