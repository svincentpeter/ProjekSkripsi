<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function indexPenarikan(Request $request)
    {
        $query = DB::table('penarikan')
            ->select([
                'penarikan.id               as penarikan_id',
                'penarikan.kode_transaksi',
                'penarikan.tanggal_penarikan',
                'penarikan.jumlah_penarikan',
                'penarikan.keterangan',
                'anggota.name               as anggota_name',
                'users.name                 as created_by_name',
            ])
            ->join('users',   'users.id',    '=', 'penarikan.created_by')
            ->leftJoin('anggota','anggota.id','=', 'penarikan.anggota_id');

        // filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('penarikan.tanggal_penarikan', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        // filter search
        if ($request->filled('search')) {
            $query->where(function($q) use($request) {
                $term = "%{$request->search}%";
                $q->where('penarikan.kode_transaksi', 'like', $term)
                  ->orWhere('anggota.name',           'like', $term);
            });
        }

        $penarikan = $query->orderBy('penarikan.id', 'desc')->paginate(5);

        return view('backend.laporan.indexPenarikan', compact('penarikan'));
    }

    public function indexSimpanan(Request $request)
    {
        $query = DB::table('simpanan')
            ->select([
                'simpanan.id                    as simpanan_id',
                'simpanan.kode_transaksi',
                'simpanan.tanggal_simpanan',
                'simpanan.jumlah_simpanan',
                'jenis_simpanan.nama            as jenis_simpanan_nama',
                'anggota.name                   as anggota_name',
                'users.name                     as created_by_name',
            ])
            ->join('users',          'users.id',          '=', 'simpanan.created_by')
            ->join('anggota',        'anggota.id',        '=', 'simpanan.anggota_id')
            ->join('jenis_simpanan', 'jenis_simpanan.id', '=', 'simpanan.jenis_simpanan_id');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('simpanan.tanggal_simpanan', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        if ($request->filled('search')) {
            $term = "%{$request->search}%";
            $query->where(function($q) use($term) {
                $q->where('simpanan.kode_transaksi', 'like', $term)
                  ->orWhere('anggota.name',           'like', $term);
            });
        }

        $simpanan = $query->orderBy('simpanan.id','desc')->paginate(5);

        return view('backend.laporan.indexSimpanan', compact('simpanan'));
    }

    public function indexPinjaman(Request $request)
    {
        $query = DB::table('pinjaman')
            ->select([
                'pinjaman.id                 as pinjaman_id',
                'pinjaman.kode_transaksi',
                'pinjaman.tanggal_pinjam',
                'pinjaman.jatuh_tempo',
                'pinjaman.jumlah_pinjam',
                'pinjaman.tenor',
                'pinjaman.bunga',
                'pinjaman.status',
                'anggota.name                as anggota_name',
                'users.name                  as created_by_name',
            ])
            ->join('users',   'users.id',    '=', 'pinjaman.created_by')
            ->join('anggota','anggota.id',   '=', 'pinjaman.anggota_id')
            ->orderBy('pinjaman.id','desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('pinjaman.tanggal_pinjam', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        if ($request->filled('search')) {
            $term = "%{$request->search}%";
            $query->where(function($q) use($term) {
                $q->where('pinjaman.kode_transaksi', 'like', $term)
                  ->orWhere('anggota.name',           'like', $term);
            });
        }

        $pinjaman = $query->paginate(5);

        return view('backend.laporan.indexPinjaman', compact('pinjaman'));
    }

    public function laporanAngsuran($id_pinjaman)
    {
        $pinjaman = DB::table('pinjaman')->find($id_pinjaman);
        abort_if(!$pinjaman, 404, 'Pinjaman tidak ditemukan');

        $laporan = DB::table('pinjaman as p')
            ->select([
                'anggota.id                as anggota_id',
                'anggota.name              as anggota_name',
                'anggota.nip               as anggota_nip',
                'anggota.image             as anggota_image',
                'anggota.telphone          as anggota_telphone',
                'anggota.alamat            as anggota_alamat',
                'anggota.pekerjaan         as anggota_pekerjaan',
                'anggota.agama             as anggota_agama',
                'p.jumlah_pinjam           as jumlah_pinjam',
                'p.tanggal_pinjam',
                'p.jatuh_tempo',
                'p.tenor                   as cicilan',
                'a.tanggal_angsuran',
                'a.jumlah_angsuran',
                'a.bunga_pinjaman',
                'a.cicilan',
                'a.denda',
                'a.sisa_pinjam             as sisa_angsuran',
                'a.status                  as status_angsuran',
            ])
            ->join('anggota',       'anggota.id', '=', 'p.anggota_id')
            ->leftJoin('angsuran as a','p.id',     '=', 'a.pinjaman_id')
            ->where('p.id', $id_pinjaman)
            ->get();

        $anggota       = $laporan->first();
        $totalAngsuran = $laporan->sum(function($row) {
            return $row->jumlah_angsuran + $row->denda + $row->bunga_pinjaman;
        });

        $pdf = PDF::loadView(
            'backend.laporan.angsuran',
            compact('anggota','pinjaman','laporan','totalAngsuran')
        );

        return $pdf->download('laporan_pinjamanAngsuran.pdf');
    }

    public function laporanPinjaman(Request $request)
    {
        $query = DB::table('pinjaman')
            ->select([
                'pinjaman.id',
                'pinjaman.kode_transaksi',
                'pinjaman.tanggal_pinjam',
                'pinjaman.jatuh_tempo',
                'pinjaman.jumlah_pinjam',
                'pinjaman.tenor',
                'pinjaman.bunga',
                'pinjaman.status',
                'anggota.name as anggota_name',
                'users.name   as created_by_name',
            ])
            ->join('users',   'users.id',    '=', 'pinjaman.created_by')
            ->join('anggota','anggota.id',   '=', 'pinjaman.anggota_id');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('pinjaman.tanggal_pinjam', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        $pinjaman = $query->orderBy('pinjaman.id','desc')->get();

        $pdf = PDF::loadView(
            'backend.laporan.pinjaman',
            compact('pinjaman')
        );

        return $pdf->download('laporan_pinjaman.pdf');
    }

    public function laporanPenarikan(Request $request)
    {
        $query = DB::table('penarikan')
            ->select([
                'penarikan.id',
                'penarikan.kode_transaksi',
                'penarikan.tanggal_penarikan',
                'penarikan.jumlah_penarikan',
                'penarikan.keterangan',
                'anggota.name            as anggota_name',
                'users.name              as created_by_name',
            ])
            ->join('users',   'users.id',    '=', 'penarikan.created_by')
            ->join('anggota','anggota.id',   '=', 'penarikan.anggota_id');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('penarikan.tanggal_penarikan', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        $penarikan = $query->orderBy('penarikan.id','desc')->get();

        $pdf = PDF::loadView(
            'backend.laporan.penarikan',
            compact('penarikan')
        );

        return $pdf->download('laporan_penarikan.pdf');
    }
}
