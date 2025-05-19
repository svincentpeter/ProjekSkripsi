<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? null;

        return [
            'kode_transaksi'        => 'required|unique:simpanan,kode_transaksi' . ($id ? ",{$id}" : ''),
            'tanggal_simpanan'      => 'required|date',
            'anggota_id'            => 'required|exists:anggota,id',
            'jenis_simpanan_id'     => 'required|exists:jenis_simpanan,id',
            'jumlah_simpanan'       => 'required|numeric|min:1',
            'bukti_pembayaran'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_transaksi.required'        => 'Kode Transaksi wajib diisi.',
            'kode_transaksi.unique'          => 'Kode Transaksi sudah digunakan.',
            'tanggal_simpanan.required'      => 'Tanggal Simpanan wajib diisi.',
            'tanggal_simpanan.date'          => 'Tanggal Simpanan harus berupa tanggal yang valid.',
            'anggota_id.required'            => 'Anggota wajib dipilih.',
            'anggota_id.exists'              => 'Anggota tidak valid.',
            'jenis_simpanan_id.required'     => 'Jenis Simpanan wajib dipilih.',
            'jenis_simpanan_id.exists'       => 'Jenis Simpanan tidak valid.',
            'jumlah_simpanan.required'       => 'Jumlah Simpanan wajib diisi.',
            'jumlah_simpanan.numeric'        => 'Jumlah Simpanan harus berupa angka.',
            'jumlah_simpanan.min'            => 'Jumlah Simpanan minimal adalah :min.',
            'bukti_pembayaran.required'      => 'Bukti Pembayaran wajib diunggah.',
            'bukti_pembayaran.file'          => 'Bukti Pembayaran harus berupa file.',
            'bukti_pembayaran.mimes'         => 'Bukti Pembayaran harus berupa format: jpg, jpeg, png, atau pdf.',
            'bukti_pembayaran.max'           => 'Ukuran Bukti Pembayaran maksimal :max KB.',
        ];
    }
}
