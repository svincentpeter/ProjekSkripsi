<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    // Isi fillable sesuai kolom pada tabel
    protected $fillable = [
    'user_id', 'name', 'nip', 'telphone', 'agama', 'jenis_kelamin', 'tgl_lahir',
    'pekerjaan', 'alamat', 'image', 'status_anggota', 'saldo', 'created_by', 'updated_by'
];
    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}
}
