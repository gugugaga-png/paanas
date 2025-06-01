<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_segment_balances', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel 'users' (siswa)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Foreign key ke tabel 'saving_segments'
            $table->foreignId('saving_segment_id')->constrained()->onDelete('cascade');
            // Kolom 'balance' yang akan menyimpan saldo spesifik untuk kombinasi user_id dan saving_segment_id ini
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();

            // Penting: Memastikan bahwa setiap kombinasi user_id dan saving_segment_id adalah unik.
            // Ini berarti satu siswa hanya bisa memiliki satu baris saldo untuk satu segmen tertentu.
            $table->unique(['user_id', 'saving_segment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_segment_balances');
    }
};