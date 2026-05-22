<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'normalized_name')) {
                $table->string('normalized_name', 255)->nullable()->after('lastname');
                $table->index('normalized_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'normalized_name')) {
                $table->dropIndex(['normalized_name']);
                $table->dropColumn('normalized_name');
            }
        });
    }
};
