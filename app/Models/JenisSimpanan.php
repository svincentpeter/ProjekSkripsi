<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSimpanan extends Model
{
    use HasFactory;

    /**
     * Nama tabel sesuai migration
     */
    protected $table = 'jenis_simpanan';

    /**
     * Kolom yang boleh di‐mass assign
     */
    protected $fillable = [
        'nama',
        'deskripsi',
        'created_by',
        'updated_by',
    ];
}
