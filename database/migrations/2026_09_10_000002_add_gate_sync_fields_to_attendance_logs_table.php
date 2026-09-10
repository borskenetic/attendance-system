<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->uuid('client_uuid')->nullable()->unique()->after('id');
            $table->foreignId('gate_device_id')->nullable()->after('client_uuid')->constrained('gate_devices')->nullOnDelete();
            $table->string('source', 32)->default('web')->after('gate_device_id');
            $table->string('kiosk_name', 120)->nullable()->after('section');
            $table->index('kiosk_name');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropIndex(['kiosk_name']);
            $table->dropConstrainedForeignId('gate_device_id');
            $table->dropColumn(['client_uuid', 'source', 'kiosk_name']);
        });
    }
};
