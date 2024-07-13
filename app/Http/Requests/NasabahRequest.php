<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NasabahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'telphone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'nip' => 'required|string|max:255',
            'password' => 'required|string|min:8', // Add password validation
            'agama' => 'required|string|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu', // Add agama validation
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'tgl_lahir' => 'nullable|date',
            'pekerjaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5048', // Update max file size to 2MB (2048 KB)
            'status_anggota' => 'nullable|string|max:255',
            'tgl_gabung' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nama.max' => 'Panjang teks untuk Nama maksimal :max karakter.',
            'telphone.required' => 'Nomor telepon wajib diisi.',
            'telphone.max' => 'Panjang teks untuk Nomor Telepon maksimal :max karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.max' => 'Panjang teks untuk Alamat Email maksimal :max karakter.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.max' => 'Panjang teks untuk NIP maksimal :max karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal :min karakter.',
            'agama.required' => 'Agama wajib dipilih.',
            'agama.in' => 'Pilihan untuk agama tidak valid.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan untuk jenis kelamin tidak valid.',
            'tgl_lahir.date' => 'Tanggal lahir harus dalam format tanggal yang valid.',
            'pekerjaan.max' => 'Panjang teks untuk Pekerjaan maksimal :max karakter.',
            'alamat.max' => 'Panjang teks untuk Alamat maksimal :max karakter.',
            'image.image' => 'File harus berupa gambar (jpeg, jpg, png).',
            'image.mimes' => 'File harus berupa gambar dengan format jpeg, jpg, atau png.',
            'image.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
            'status_anggota.max' => 'Panjang teks untuk Status Anggota maksimal :max karakter.',
            'tgl_gabung.required' => 'Tanggal gabung wajib diisi.',
            'tgl_gabung.date' => 'Tanggal gabung harus dalam format tanggal yang valid.',
        ];
    }

}