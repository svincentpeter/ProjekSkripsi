<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersUpdateRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email', // Validasi email dan memastikan email unik
            'password' => 'nullable|string|min:8|max:10|confirmed',
            'password_confirmation' => 'nullable|string|min:8|max:10|confirmed',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:10048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Kolom nama harus diisi.',
            'name.max' => 'Nama terlalu panjang.',
            'email.required' => 'Kolom email harus diisi.',
            'email.email' => 'Email harus berformat email yang valid.',
            'password.nullable' => 'Kolom password harus diisi.',
            'password.min' => 'Password terlalu pendek.',
            'password.max' => 'Password terlalu panjang.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai dengan password.',
            'image.required' => 'Gambar harus diunggah.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'File gambar harus berformat jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran file gambar tidak boleh lebih dari 5MB.',
        ];
    }
}
