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

        $adminPassword = env('ADMIN_PASSWORD');
        $petugasPassword = env('PETUGAS_PASSWORD');
        $siswaPassword = env('SISWA_PASSWORD');
        $siswa2Password = env('SISWA_2_PASSWORD');

        if (blank($adminPassword) || blank($petugasPassword) || blank($siswaPassword) || blank($siswa2Password)) {
            throw new RuntimeException(
                'ADMIN_PASSWORD, PETUGAS_PASSWORD, SISWA_PASSWORD, dan SISWA_2_PASSWORD harus diatur pada file .env.'
            );
        }

        $adminUser = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@perpustakaan.test')],
            [
                'name' => 'Administrator',
                'password' => Hash::make($adminPassword),
            ]
        );

        $petugasUser = User::updateOrCreate(
            ['email' => env('PETUGAS_EMAIL', 'petugas@perpustakaan.test')],
            [
                'name' => 'Petugas Perpustakaan',
                'password' => Hash::make($petugasPassword),
            ]
        );

        $siswaUser = User::updateOrCreate(
            ['email' => env('SISWA_EMAIL', 'siswa@perpustakaan.test')],
            [
                'name' => 'Siswa Perpustakaan',
                'password' => Hash::make($siswaPassword),
            ]
        );

        $siswa2User = User::updateOrCreate(
            ['email' => env('SISWA_2_EMAIL', 'siswa2@perpustakaan.test')],
            [
                'name' => 'Siswa Perpustakaan 2',
                'password' => Hash::make($siswa2Password),
            ]
        );

        $adminUser->syncRoles([$admin]);
        $petugasUser->syncRoles([$petugas]);
        $siswaUser->syncRoles([$siswa]);
        $siswa2User->syncRoles([$siswa]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}