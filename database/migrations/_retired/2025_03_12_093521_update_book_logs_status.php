<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('book_logs', function (Blueprint $table) {
            $table->string('status')->change(); // Change to store "Checked In" / "Checked Out"
        });
    }

    public function down()
    {
        Schema::table('book_logs', function (Blueprint $table) {
            $table->string('status')->change(); // Rollback to previous status if needed
        });
    }
};
