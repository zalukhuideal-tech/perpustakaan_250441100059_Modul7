<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.admin',
            'dashboard.petugas',
            'books.view',
            'books.manage',
            'users.manage',
            'users.assign-roles',
            'loans.manage',
            'returns.manage',
            'loans.request',
            'loans.approve',
            'returns.record',
            'loans.view-own',
            'loans.history-own',
            'reports.view',
            'profile.view-own',
            'profile.update-own',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $petugas = Role::firstOrCreate([
            'name' => 'petugas',
            'guard_name' => 'web',
        ]);

        $siswa = Role::firstOrCreate([
            'name' => 'siswa',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions([
            'dashboard.admin',
            'books.view',
            'books.manage',
            'users.manage',
            'users.assign-roles',
            'loans.manage',
            'returns.manage',
            'loans.request',
            'loans.approve',
            'returns.record',
            'loans.view-own',
            'loans.history-own',
            'reports.view',
            'profile.view-own',
            'profile.update-own',
        ]);

        $petugas->syncPermissions([
            'dashboard.petugas',
            'books.view',
            'books.manage',
            'loans.manage',
            'returns.manage',
            'loans.request',
            'loans.approve',
            'returns.record',
            'reports.view',
            'loans.view-own',
            'loans.history-own',
            'profile.view-own',
            'profile.update-own',
        ]);

        $siswa->syncPermissions([
            'books.view',
            'loans.request',
            'loans.view-own',
            'loans.history-own',
            'profile.view-own',
            'profile.update-own',
        ]);

        // $adminUser = User::updateOrCreate(
        //     ['email' => env('ADMIN_EMAIL', 'admin@perpustakaan.test')],
        //     [
        //         'name' => 'Administrator',
        //         'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
        //     ],
        // );

        $adminPassword = env('ADMIN_PASSWORD');

        if (blank($adminPassword)) {
            throw new RuntimeException('ADMIN_PASSWORD harus diatur pada file .env.');
        }

        $adminUser = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@perpustakaan.test')],
            [
                'name' => 'Administrator',
                'password' => Hash::make($adminPassword),
            ]
        );

        $adminUser->assignRole($admin);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}