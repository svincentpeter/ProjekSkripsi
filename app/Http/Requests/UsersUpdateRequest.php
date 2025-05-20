<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersUpdateRequest extends FormRequest
{
    /**
     * Tentukan apakah user boleh melakukan request ini.
     */
    public function authorize(): bool
    {
        // Sesuaikan jika mau pembatasan role tertentu, default true
        return true;
    }

    /**
     * Aturan validasi untuk update user.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Ambil ID user yang di-edit dari route parameter (default 'id')
        $userId = $this->route('id');

        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users,email,'.$userId,
            'password' => 'nullable|string|min:8|max:20|confirmed',
            'roles'    => 'required|array|min:1',
            'roles.*'  => 'exists:roles,id', // pastikan ID role valid
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama harus diisi.',
            'name.max'          => 'Nama maksimal 255 karakter.',
            'email.required'    => 'Email harus diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah digunakan.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.max'      => 'Password maksimal 20 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak sesuai.',
            'roles.required'    => 'Role pengguna wajib dipilih.',
            'roles.array'       => 'Format role tidak valid.',
            'roles.*.exists'    => 'Role yang dipilih tidak valid.',
            'image.image'       => 'File harus berupa gambar.',
            'image.mimes'       => 'Format gambar hanya jpeg, png, jpg, gif.',
            'image.max'         => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}
