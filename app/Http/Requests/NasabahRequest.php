<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NasabahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    $nasabahId = $this->route('nasabah');
    $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

    $rules = [
        'name'            => 'required|string|max:255',
        'nip'             => 'required|string|max:255|unique:anggota,nip' . ($isUpdate ? ',' . $nasabahId : ''),
        'telphone'        => 'required|string|max:255',
        'agama'           => 'nullable|string|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
        'jenis_kelamin'   => 'nullable|in:L,P',
        'tgl_lahir'       => 'nullable|date',
        'pekerjaan'       => 'nullable|string|max:255',
        'alamat'          => 'nullable|string|max:255',
        'image'           => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        'status_anggota'  => 'nullable|in:0,1',
    ];

    if ($isUpdate) {
        $anggota = \App\Models\Anggota::find($nasabahId);
        $userId = $anggota?->user_id;
        $rules['email'] = 'required|email|unique:users,email,' . $userId;
        $rules['password'] = 'nullable|min:6|confirmed';
    } else {
        $rules['email'] = 'required|email|unique:users,email';
        $rules['password'] = 'required|min:6|confirmed';
    }

    return $rules;
}


    public function messages(): array
    {
        return [
            'name.required'           => 'Nama wajib diisi.',
            'name.max'                => 'Nama maksimal :max karakter.',
            'nip.required'            => 'NIP wajib diisi.',
            'nip.max'                 => 'NIP maksimal :max karakter.',
            'nip.unique'              => 'NIP sudah terdaftar.',
            'telphone.required'       => 'Telepon wajib diisi.',
            'telphone.max'            => 'Telepon maksimal :max karakter.',
            'agama.in'                => 'Pilihan agama tidak valid.',
            'jenis_kelamin.in'        => 'Pilihan jenis kelamin tidak valid.',
            'tgl_lahir.date'          => 'Tanggal lahir harus tanggal yang valid.',
            'pekerjaan.max'           => 'Pekerjaan maksimal :max karakter.',
            'alamat.max'              => 'Alamat maksimal :max karakter.',
            'image.image'             => 'File harus gambar.',
            'image.mimes'             => 'Format gambar harus jpeg, jpg, atau png.',
            'image.max'               => 'Ukuran gambar maksimal 2MB.',
            'status_anggota.in'       => 'Status anggota hanya 0 (non-aktif) atau 1 (aktif).',
            'email.required'          => 'Email wajib diisi.',
            'email.email'             => 'Format email tidak valid.',
            'email.unique'            => 'Email sudah digunakan.',
            'password.required'       => 'Password wajib diisi.',
            'password.min'            => 'Password minimal 6 karakter.',
            'password.confirmed'      => 'Konfirmasi password tidak sama.',
        ];
    }
}
