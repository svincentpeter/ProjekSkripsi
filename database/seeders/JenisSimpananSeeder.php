<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSimpanan;     // import model
use App\Models\User;

class JenisSimpananSeeder extends Seeder   // nama kelas matching file
{
    public function run(): void
    {
        // Ambil admin pertama sebagai pembuat
        $adminId = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))
                       ->value('id') ?? 1;

        $data = [
            [
                'nama'        => 'Simpanan Pokok',
                'deskripsi'   => 'Syarat resmi jadi anggota koperasi',
                'created_by'  => $adminId,
                'updated_by'  => $adminId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nama'        => 'Simpanan Wajib',
                'deskripsi'   => 'Simpanan wajib setiap bulan',
                'created_by'  => $adminId,
                'updated_by'  => $adminId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nama'        => 'Simpanan Sukarela',
                'deskripsi'   => 'Simpanan bebas kapan saja',
                'created_by'  => $adminId,
                'updated_by'  => $adminId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        JenisSimpanan::insert($data);
    }
}
