<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpananRequest extends FormRequest
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
            'kodeTransaksiSimpanan' => 'required|unique:simpanan,kodeTransaksiSimpanan',
            'tanggal_simpanan' => 'required|date',
            'id_anggota' => 'required|exists:_anggota,id',
            'id_jenis_simpanan' => 'required|exists:jenis_simpanan,id',
            'jml_simpanan' => 'required|numeric|min:1',
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kodeTransaksiSimpanan.required' => 'Kode Transaksi wajib diisi.',
            'kodeTransaksiSimpanan.unique' => 'Kode Transaksi sudah digunakan.',
            'tanggal_simpanan.required' => 'Tanggal Simpanan wajib diisi.',
            'tanggal_simpanan.date' => 'Tanggal Simpanan harus berupa tanggal yang valid.',
            'id_anggota.required' => 'Nama Anggota wajib dipilih.',
            'id_anggota.exists' => 'Nama Anggota tidak valid.',
            'id_jenis_simpanan.required' => 'Jenis Simpanan wajib dipilih.',
            'id_jenis_simpanan.exists' => 'Jenis Simpanan tidak valid.',
            'jml_simpanan.required' => 'Jumlah Simpanan wajib diisi.',
            'jml_simpanan.numeric' => 'Jumlah Simpanan harus berupa angka.',
            'jml_simpanan.min' => 'Jumlah Simpanan minimal adalah 1.',
            'bukti_pembayaran.required' => 'Bukti Pembayaran wajib diunggah.',
            'bukti_pembayaran.file' => 'Bukti Pembayaran harus berupa file.',
            'bukti_pembayaran.mimes' => 'Bukti Pembayaran harus berupa file dengan format: jpg, jpeg, png, atau pdf.',
            'bukti_pembayaran.max' => 'Bukti Pembayaran maksimal adalah 2MB.',
        ];
    }
}
