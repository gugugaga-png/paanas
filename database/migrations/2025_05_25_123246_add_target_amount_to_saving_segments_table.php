<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saving_segments', function (Blueprint $table) {
            // Add the new column
            $table->decimal('target_amount', 15, 2)->after('unique_code')->default(0); // Adjust precision/scale as needed
            // You might need to set a default value or make it nullable if existing rows won't have a value initially
        });
    }

    public function down(): void
    {
        Schema::table('saving_segments', function (Blueprint $table) {
            // Drop the column if rolling back
            $table->dropColumn('target_amount');
        });
    }
};