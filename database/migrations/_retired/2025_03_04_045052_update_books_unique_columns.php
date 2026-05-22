<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->unique('barcode'); // Add unique constraint to barcode
            $table->unique('rfid'); // Add unique constraint to rfid
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropUnique(['barcode']); // Remove unique constraint
            $table->dropUnique(['rfid']); // Remove unique constraint
        });
    }
};