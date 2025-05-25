<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID Murid yang menabung
            $table->foreignId('saving_segment_id')->constrained()->onDelete('cascade'); // ID segmen tabungan
            $table->decimal('amount', 10, 2); // Jumlah tabungan
            $table->string('type')->default('deposit'); // Tipe transaksi (deposit/withdrawal - jika ada)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};