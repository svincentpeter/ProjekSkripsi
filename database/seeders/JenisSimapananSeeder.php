<?php

namespace Database\Seeders;

use App\Models\jenis_Simpanan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisSimapananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inTerakhir = DB::table('users')->latest('id')->first();

        //ELEQUENT
        $data = [
            [
                'nama' => 'Simpanan Pokok',
                'deskripsi' => 'Syarat resmi jadi anggota koperasi',
                'created_by' => $inTerakhir->id,
                'updated_by' => $inTerakhir->id,

            ],
            [
                'nama' => 'Simpanan Wajib',
                'deskripsi' => 'Simpanan wajib setiap bulan',
                'created_by' => 1,
                'updated_by' => 1,

            ],
            [
                'nama' => 'Simpanan Sukarela',
                'deskripsi' => 'Simpanan Bebas kapan saja',
                'created_by' => 1,
                'updated_by' => 1,

            ],
        ];

        // JenisBarang adalah MOdel
        // Pastikan Model JenisBarang Sudah di Generate
        jenis_Simpanan::insert($data);
    
    }
}
