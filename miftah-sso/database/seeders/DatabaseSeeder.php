<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('admin');

        // Create teacher user
        $teacher = User::factory()->create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
        ]);
        $teacher->assignRole('teacher');

        // Create student user
        $student = User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@example.com',
        ]);
        $student->assignRole('student');
    }
}
