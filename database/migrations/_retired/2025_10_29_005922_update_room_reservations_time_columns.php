<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            // Add new columns
            $table->time('start_time')->after('date');
            $table->time('end_time')->after('start_time');

            // Remove old one if it exists
            $table->dropColumn('time_slot');
        });
    }

    public function down(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            $table->string('time_slot')->nullable();
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
};
