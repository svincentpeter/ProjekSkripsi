<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $countSimpanan = DB::table('_anggota')->sum('saldo');
        $countpenarikan = DB::table('penarikan')->sum('jumlah_penarikan');
        $countpinjaman = DB::table('pinjaman')->sum('jml_pinjam');
        $counttotalanggota = DB::table('_anggota')->get('id')->count();
        return view('backend.home.index', compact(
            'countSimpanan',
            'countpenarikan',
            'countpinjaman',
            'counttotalanggota',
        ));
    }

    public function chartBalok()
    {
        // Mengambil data total per tahun dari tabel penarikan, pinjaman, dan simpanan
        $dataSimpanan = DB::table('_anggota')
            ->select(DB::raw('YEAR(created_at) as year'), DB::raw('SUM(saldo) as total'))
            ->groupBy('year')
            ->pluck('total', 'year');

        $dataPenarikan = DB::table('penarikan')
            ->select(DB::raw('YEAR(tanggal_penarikan) as year'), DB::raw('SUM(jumlah_penarikan) as total'))
            ->groupBy('year')
            ->pluck('total', 'year');

        $dataPinjaman = DB::table('pinjaman')
            ->select(DB::raw('YEAR(tanggal_pinjam) as year'), DB::raw('SUM(jml_pinjam) as total'))
            ->groupBy('year')
            ->pluck('total', 'year');

        // Menyiapkan data untuk dikirim ke view
        $years = range(2020, 2026);
        $simpanan = [];
        $penarikan = [];
        $pinjaman = [];

        foreach ($years as $year) {
            $simpanan[] = $dataSimpanan[$year] ?? 0;
            $penarikan[] = $dataPenarikan[$year] ?? 0;
            $pinjaman[] = $dataPinjaman[$year] ?? 0;
        }

        return response()->json(compact('years', 'simpanan', 'penarikan', 'pinjaman'));
    }
}
