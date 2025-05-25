<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tambahkan kolom 'status' dengan default 'pending'
            // Enum cocok untuk nilai status yang terbatas
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('amount');
            // Bisa juga menambahkan 'teacher_id' yang memvalidasi
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');
            $table->dropColumn('status');
        });
    }
};