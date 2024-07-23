<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BerandaController extends Controller
{
    public function index()
    {
        $countSimpanan = DB::table('_t_r__pengajuan')->where('status_pengajuan_vendor', 2)->count();
        $countpenarikan = DB::table('_t_r__pengajuan')->where('status_pengajuan_vendor', 1)->count();
        $countpinjaman = DB::table('_detail__pengajuan')->get('id')->count();
        $counttotalanggota = DB::table('_anggota')->get('id')->count();
        $countDitolakPengajuan = DB::table('_t_r__pengajuan')->where('status_pengajuan_ap', 2)->count();
        $countDiterimaPengajuan = DB::table('_t_r__pengajuan')->where('status_pengajuan_ap', 1)->count();

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