// database/migrations/YYYY_MM_DD_HHMMSS_create_user_saving_segment_table.php
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
       Schema::create('user_saving_segment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Student's ID
            $table->foreignId('saving_segment_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at')->nullable(); // Optional, but good to record
            $table->timestamps(); // For created_at and updated_at on the pivot table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_saving_segment');
    }
};