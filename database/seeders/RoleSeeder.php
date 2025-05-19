<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan semua permissions sudah ada!
        $permissions = Permission::pluck('name')->toArray();

        // 1. Role Admin - dapat semua permission
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        // 2. Role Petugas - permission tertentu
        $petugasPermissions = [
            'user-list', 'nasabah-list', 'nasabah-create', 'nasabah-edit',
            'simpanan-list', 'simpanan-create', 'simpanan-edit',
            'pinjaman-list', 'pinjaman-create', 'pinjaman-edit',
            'angsuran-list', 'angsuran-create', 'angsuran-edit',
            // tambah sesuai kebutuhan
        ];
        $petugasRole = Role::firstOrCreate(['name' => 'Petugas', 'guard_name' => 'web']);
        $petugasRole->syncPermissions($petugasPermissions);

        // 3. Role Anggota - hanya lihat data
        $anggotaPermissions = [
            'nasabah-list', 'simpanan-list', 'pinjaman-list', 'angsuran-list',
            // tambah jika ada permission lihat laporan, dsb.
        ];
        $anggotaRole = Role::firstOrCreate(['name' => 'Anggota', 'guard_name' => 'web']);
        $anggotaRole->syncPermissions($anggotaPermissions);
    }
}
