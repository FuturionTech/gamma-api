<?php

namespace Database\Seeders;

use App\Models\Administrator;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view-dashboard',
            'manage-administrators',
            'manage-content',
            'manage-applications',
            'manage-services',
            'manage-solutions',
            'manage-partners',
            'manage-testimonials',
            'manage-blog',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'sanctum']
            );
        }

        // Create Super Admin role
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['guard_name' => 'sanctum']
        );
        $superAdminRole->syncPermissions(Permission::all());

        // Create Admin role
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'sanctum']
        );
        $adminRole->syncPermissions(['view-dashboard', 'manage-content']);

        // Create initial super admin account
        $admin = Administrator::firstOrCreate(
            ['email' => 'admin@gammaneutral.com'],
            [
                'employee_number' => 'EMP-0001',
                'email' => 'admin@gammaneutral.com',
                'first_name' => 'Super',
                'last_name' => 'Administrator',
                'phone_number' => '+1234567890',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super_admin');

        // Create Aboubacar Compaore as super admin
        $acompaore = Administrator::firstOrCreate(
            ['email' => 'acompaore@futurion.tech'],
            [
                'employee_number' => 'EMP-0002',
                'email' => 'acompaore@futurion.tech',
                'first_name' => 'Aboubacar',
                'last_name' => 'Compaore',
                'phone_number' => '+22676013887',
                'status' => 'active',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
        $acompaore->assignRole('super_admin');

        $this->command->info('Administrators created successfully.');
        $this->command->info('');
        $this->command->info('Account 1:');
        $this->command->info('Email: admin@gammaneutral.com');
        $this->command->info('Note: OTP-only authentication - no password');
        $this->command->info('');
        $this->command->info('Account 2 (Aboubacar Compaore):');
        $this->command->info('Email: acompaore@futurion.tech');
        $this->command->info('Phone: +22676013887');
        $this->command->info('Note: OTP-only authentication via email or SMS');
    }
}

