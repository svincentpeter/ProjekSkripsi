<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'user-list',
            'role-list', 'role-create', 'role-edit', 'role-delete',
            'nasabah-list', 'nasabah-create', 'nasabah-detail', 'nasabah-edit', 'nasabah-delete',
            'simpanan-list', 'simpanan-create', 'simpanan-detail', 'simpanan-edit', 'simpanan-delete',
            'penarikan-list', 'penarikan-create', 'penarikan-edit', 'penarikan-delete',
            'pinjaman-list', 'pinjaman-create', 'pinjaman-detail', 'pinjaman-edit', 'pinjaman-delete',
            'laporan_list', 'laporan_simpanan', 'laporan_pinjaman', 'laporan_angsuran', 'laporan_penarikan',
            'angsuran-create', 'angsuran-edit', 'angsuran-delete',
            'approve_penarikan', 'approve_pinjaman', 'tolak_penarikan', 'tolak_pinjaman',
            'angsuran-list',
    'angsuran-create', 'angsuran-edit', 'angsuran-delete',
    'approve_penarikan', 'approve_pinjaman', 'tolak_penarikan', 'tolak_pinjaman',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name'       => $permission, 'guard_name' => 'web']
            );
        }
    }
}
