<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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

        // Create roles and assign permissions
        
        // Admin role
        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo(Permission::all());

        // Teacher role
        $teacherRole = Role::findOrCreate('teacher');
        $teacherRole->givePermissionTo([
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'view users',
        ]);

        // Student role
        $studentRole = Role::findOrCreate('student');
        $studentRole->givePermissionTo([
            'view courses',
            'enroll in courses',
        ]);
    }
}