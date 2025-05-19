<?php

if (!function_exists('format_rupiah')) {
    /**
     * Format angka ke Rupiah.
     * @param int|float $angka
     * @return string
     */
    function format_rupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('tanggal_indonesia')) {
    /**
     * Format tanggal ke format Indonesia.
     * @param string|\Carbon\Carbon $tanggal
     * @param bool $withTime
     * @return string
     */
    function tanggal_indonesia($tanggal, $withTime = true)
    {
        if (!$tanggal) {
            return '-';
        }
        try {
            $carbon = $tanggal instanceof \Carbon\Carbon ? $tanggal : \Carbon\Carbon::parse($tanggal);
            $format = $withTime ? 'd-m-Y H:i' : 'd-m-Y';
            return $carbon->format($format);
        } catch (\Exception $e) {
            return $tanggal;
        }
    }
}

if (!function_exists('status_anggota_label')) {
    /**
     * Label status anggota.
     */
    function status_anggota_label($status)
    {
        if ($status == 1) {
            return '<span class="badge bg-success">Aktif</span>';
        } else {
            return '<span class="badge bg-danger">Non-Aktif</span>';
        }
    }
}
