<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StudentSegmentBalance; 

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saving_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID Guru yang membuat
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unique_code')->unique(); // Kode unik untuk diinput murid
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saving_segments');
    }
};