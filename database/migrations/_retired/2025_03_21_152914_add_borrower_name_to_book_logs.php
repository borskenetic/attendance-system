<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('book_logs', function (Blueprint $table) {
            $table->string('borrower_name')->nullable()->after('book_id'); // Add borrower_name column
        });
    }
    
    public function down()
    {
        Schema::table('book_logs', function (Blueprint $table) {
            $table->dropColumn('borrower_name');
        });
    }
};
