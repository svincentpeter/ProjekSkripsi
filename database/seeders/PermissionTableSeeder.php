<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'nasabah-list',
            'nasabah-create',
            'nasabah-detail',
            'nasabah-edit',
            'nasabah-delete',

            'simpanan-list',
            'simpanan-create',
            'simpanan-edit',
            'simpanan-delete',
            'simpanan-detail',

            'penarikan-list',
            'penarikan-create',
            'penarikan-edit',
            'penarikan-delete',

            'pinjaman-list',
            'pinjaman-create',
            'pinjaman-edit',
            'pinjaman-delete',
            'pinjaman-detail',

            'laporan_list',
            'laporan_simpanan',
            'laporan_pinjaman',
            'laporan_angsuran',
            'laporan_penarikan',

            'angsuran-create',
            'angsuran-edit',
            'angsuran-delete',

            'approve_penarikan',
            'approve_pinjaman',
            'tolak_penarikan',
            'tolak_pinjaman',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
