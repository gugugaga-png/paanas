<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_picture')->nullable()->after('email'); // Tambahkan kolom gambar profil
            $table->text('bio')->nullable()->after('profile_picture');    // Contoh kolom tambahan
            // Anda bisa menambahkan kolom lain sesuai kebutuhan, misalnya:
            // $table->string('phone')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_picture', 'bio']); // Hapus kolom jika migrasi di-rollback
            // $table->dropColumn(['profile_picture', 'bio', 'phone']); // Hapus semua kolom yang ditambahkan
        });
    }
};