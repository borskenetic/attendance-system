<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('room_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->date('date');
            $table->string('time_slot'); // e.g. "08:00-10:00"
            $table->string('patron_email');
            $table->unsignedTinyInteger('number_of_students');
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['room_id', 'date', 'time_slot']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_reservations');
    }
};
