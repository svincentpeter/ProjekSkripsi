<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BerandaController extends Controller
{
    public function index()
    {
        // Total simpanan (semua transaksi simpanan)
        $countSimpanan = DB::table('simpanan')->count();

        // Total penarikan (semua transaksi penarikan)
        $countpenarikan = DB::table('penarikan')->count();

        // Total pinjaman (semua record pinjaman)
        $countpinjaman = DB::table('pinjaman')->count();

        // Total anggota
        $counttotalanggota = DB::table('anggota')->count();

        // Dari tabel pinjaman, hitung yang berstatus 'DITOLAK'
        $countDitolakPengajuan = DB::table('pinjaman')
            ->where('status', 'DITOLAK')
            ->count();

        // Dari tabel pinjaman, hitung yang berstatus 'DISETUJUI'
        $countDiterimaPengajuan = DB::table('pinjaman')
            ->where('status', 'DISETUJUI')
            ->count();

        return view('backend.home.index', compact(
            'countSimpanan',
            'countpenarikan',
            'countpinjaman',
            'counttotalanggota',
            'countDitolakPengajuan',
            'countDiterimaPengajuan'
        ));
    }
}
