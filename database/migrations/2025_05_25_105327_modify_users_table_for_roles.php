<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create roles table if it doesn't exist
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });

            // Insert default roles
            DB::table('roles')->insert([
                ['name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'murid', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'guru', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Modify users table - only if column doesn't exist
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')
                      ->nullable()
                      ->after('password')
                      ->constrained()
                      ->onDelete('set null');
            }

            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });

        // Set default role for existing users
        $muridRoleId = DB::table('roles')->where('name', 'murid')->value('id');
        if ($muridRoleId) {
            DB::table('users')->whereNull('role_id')->update(['role_id' => $muridRoleId]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->after('password')->nullable();
            }
        });

        // Optional: uncomment if you want to drop roles table on rollback
        // Schema::dropIfExists('roles');
    }
};