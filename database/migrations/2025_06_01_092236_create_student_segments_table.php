<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_segments', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('saving_segment_id')->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'saving_segment_id']); // Composite primary key
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_segments');
    }
};
