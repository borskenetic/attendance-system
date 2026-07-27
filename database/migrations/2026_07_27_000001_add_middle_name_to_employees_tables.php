<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('firstname');
            }
        });

        Schema::table('pending_employees', function (Blueprint $table) {
            if (! Schema::hasColumn('pending_employees', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('firstname');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'middle_name')) {
                $table->dropColumn('middle_name');
            }
        });

        Schema::table('pending_employees', function (Blueprint $table) {
            if (Schema::hasColumn('pending_employees', 'middle_name')) {
                $table->dropColumn('middle_name');
            }
        });
    }
};
