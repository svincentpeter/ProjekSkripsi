<?php

if (!function_exists('tanggal_indonesia')) {
    function tanggal_indonesia($tgl, $tampil_hari = true)
    {
        $nama_hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
        $nama_bulan = [
            1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];

        // Memastikan input tanggal dalam format yang benar
        if (!strtotime($tgl)) {
            return "Format tanggal tidak valid";
        }

        $tahun = substr($tgl, 0, 4);
        $bulan = $nama_bulan[(int)substr($tgl, 5, 2)];
        $tanggal = substr($tgl, 8, 2);
        $text = "";

        if ($tampil_hari) {
            $urutan_hari = date('w', strtotime($tgl));
            $hari = $nama_hari[$urutan_hari];
            $text .= $hari . ", ";
        }

        $text .= $tanggal . " " . $bulan . " " . $tahun;
        return $text;
    }
}
