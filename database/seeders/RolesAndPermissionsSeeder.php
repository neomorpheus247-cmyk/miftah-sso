<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Course permissions
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'enroll in courses',

            // User management
            'manage users',
            'view users',

            // Role management
            'manage roles',

            // System settings
            'access settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create roles
        $adminRole   = Role::findOrCreate('admin');
        $teacherRole = Role::findOrCreate('teacher');
        $studentRole = Role::findOrCreate('student');

        // Assign permissions
        $adminRole->givePermissionTo(Permission::all());
        $teacherRole->givePermissionTo([
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'view users',
        ]);
        $studentRole->givePermissionTo([
            'view courses',
            'enroll in courses',
        ]);

        // ✅ Create or update the default admin user safely
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@example.com'], // unique field
            [
                'name'              => 'Admin User',
                'password'          => bcrypt('password'), // ⚠️ change in prod
                'email_verified_at' => now(),
            ]
        );

        // Ensure admin role is assigned
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }
    }
}
