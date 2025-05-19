<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    use HasFactory;

    protected $table = 'simpanan';

    protected $fillable = [
        'kode_transaksi',
        'tanggal_simpanan',
        'anggota_id',
        'jenis_simpanan_id',
        'jumlah_simpanan',
        'bukti_pembayaran',
        'created_by',
        'updated_by',
    ];
}
