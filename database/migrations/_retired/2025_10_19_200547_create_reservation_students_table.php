<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('reservation_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('room_reservations')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservation_students');
    }
};
