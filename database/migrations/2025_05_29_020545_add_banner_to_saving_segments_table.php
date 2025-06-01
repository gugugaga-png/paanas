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
        Schema::table('saving_segments', function (Blueprint $table) {
            // Menambahkan kolom 'banner' setelah kolom 'description'
            // Kolom ini akan menyimpan path/URL gambar banner
            $table->string('banner')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saving_segments', function (Blueprint $table) {
            // Menghapus kolom 'banner'
            $table->dropColumn('banner');
        });
    }
};  