<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Role; // Import Role model

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Call RoleSeeder FIRST to ensure roles exist
        $this->call(RoleSeeder::class);

        // Get the ID of the 'murid' (student) role
        $muridRole = Role::where('name', 'murid')->first();

        // Ensure the murid role exists before creating users
        if ($muridRole) {
            // Create a test user and assign the 'murid' role_id
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role_id' => $muridRole->id, // <--- Add this line
            ]);

            // If you have multiple users via factory()->count(X)->create(),
            // you can assign the role to all of them like this:
            User::factory()->count(9)->create([
                'role_id' => $muridRole->id, // All these 9 users will be 'murid'
            ]);

            // Example for a Guru user (if you want one created by seeder)
            $guruRole = Role::where('name', 'guru')->first();
            if ($guruRole) {
                 User::factory()->create([
                    'name' => 'Guru User',
                    'email' => 'guru@example.com',
                    'role_id' => $guruRole->id,
                ]);
            }

        } else {
            $this->command->warn("Warning: 'murid' role not found. Skipping user creation.");
        }
    }
}