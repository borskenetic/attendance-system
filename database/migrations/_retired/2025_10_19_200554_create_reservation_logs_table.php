<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('reservation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->nullable()->constrained('room_reservations')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // created, updated, approved, rejected, cancelled
            $table->text('meta')->nullable(); // JSON text for extra info
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservation_logs');
    }
};
