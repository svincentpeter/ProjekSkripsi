<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'image'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi opsional: 1 User bisa punya 1 anggota (kalau ingin)
    public function anggota()
    {
        return $this->hasOne(Anggota::class);
    }

    // Helper untuk gambar user (null-safe)
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('assets/backend/img/' . $this->image);
        }
        return asset('assets/backend/img/default-user.png');
    }
}
