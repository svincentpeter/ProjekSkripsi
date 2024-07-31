<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function indexPenarikan(Request $request)
    {
        // Mengambil data penarikan
        $penarikan = DB::table('penarikan')
            ->select('penarikan.id as penarikan_id', 'penarikan.*', '_anggota.*', 'users.name as created_by_name',)
            ->join('users', 'users.id', '=', 'penarikan.created_by')
            ->leftJoin('_anggota', '_anggota.id', '=', 'penarikan.id_anggota');

        // Filter berdasarkan tanggal penarikan jika tersedia
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            if ($startDate && $endDate) {
                $penarikan = $penarikan->whereBetween('tanggal_penarikan', [$startDate, $endDate]);
            }
        }

        // Filter berdasarkan pencarian
        if ($request->has('search')) {
            $search = $request->get('search');
            $penarikan = $penarikan->where(function ($query) use ($search) {
                $query->where('kodeTransaksipenarikan', 'like', "%{$search}%")
                    ->orWhere('_anggota.name', 'like', "%{$search}%");
            });
        }

        // Paginate hasil query
        $penarikan = $penarikan->paginate(5);

        // Mengirim data ke view
        return view('backend.laporan.indexPenarikan', compact('penarikan'));
    }

    public function indexSimpanan(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');

        $query = DB::table('simpanan')
            ->select(
                'simpanan.id as simpanan_id',
                'simpanan.kodeTransaksiSimpanan',
                'simpanan.tanggal_simpanan',
                'simpanan.jml_simpanan',
                'jenis_simpanan.nama as jenis_simpanan_nama',
                'users.name as created_by_name',
                '_anggota.name as anggota_name'
            )
            ->join('users', 'users.id', '=', 'simpanan.created_by')
            ->join('_anggota', '_anggota.id', '=', 'simpanan.id_anggota')
            ->join('jenis_simpanan', 'jenis_simpanan.id', '=', 'simpanan.id_jenis_simpanan');

        if ($startDate && $endDate) {
            $query->whereBetween('simpanan.tanggal_simpanan', [$startDate, $endDate]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('simpanan.kodeTransaksiSimpanan', 'LIKE', "%$search%")
                    ->orWhere('_anggota.name', 'LIKE', "%$search%");
            });
        }

        $simpanan = $query->orderBy('simpanan.id', 'DESC')->paginate(5);

        return view('backend.laporan.indexSimpanan', compact('simpanan', 'startDate', 'endDate', 'search'));
    }
    public function indexPinjaman(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');

        $anggota = DB::table('_anggota')->get();
        $pinjamanQuery = DB::table('pinjaman')
            ->select(
                'pinjaman.id as pinjaman_id',
                'pinjaman.kodeTransaksiPinjaman',
                'pinjaman.tanggal_pinjam',
                'pinjaman.jatuh_tempo',
                'pinjaman.jml_pinjam',
                'pinjaman.jml_cicilan',
                'pinjaman.bunga_pinjam',
                'pinjaman.status_pengajuan',
                'users.name as created_by_name',
                '_anggota.name as anggota_name'
            )
            ->join('users', 'users.id', '=', 'pinjaman.created_by')
            ->join('_anggota', '_anggota.id', '=', 'pinjaman.id_anggota')
            ->orderBy('pinjaman.id', 'DESC');

        if ($startDate && $endDate) {
            $pinjamanQuery->whereBetween('pinjaman.tanggal_pinjam', [$startDate, $endDate]);
        }

        if ($search) {
            $pinjamanQuery->where(function ($query) use ($search) {
                $query->where('pinjaman.kodeTransaksiPinjaman', 'like', "%{$search}%")
                    ->orWhere('_anggota.name', 'like', "%{$search}%");
            });
        }

        $pinjaman = $pinjamanQuery->paginate(5);

        return view('backend.laporan.indexPinjaman', compact('pinjaman', 'anggota'));
    }

    public function laporanAngsuran($id_pinjaman)
    {
        // Ambil data pinjaman
        $pinjaman = DB::table('pinjaman')->where('id', $id_pinjaman)->first();
        if (!$pinjaman) {
            return response()->json(['error' => 'Pinjaman tidak ditemukan'], 404);
        }

        // Gabungkan data anggota, pinjaman, dan angsuran menggunakan join
        $laporan = DB::table('pinjaman as p')
            ->select(
                '_anggota.id as anggota_id',
                '_anggota.name as anggota_name',
                '_anggota.nip as anggota_nip',
                '_anggota.image as anggota_image',
                '_anggota.telphone as anggota_telphone',
                '_anggota.alamat as anggota_alamat',
                '_anggota.pekerjaan as anggota_pekerjaan',
                '_anggota.agama as anggota_agama',
                'p.jml_pinjam as jml_pinjam',
                'p.tanggal_pinjam as tanggal_pinjam',
                'p.jatuh_tempo as jatuh_tempo',
                'p.jml_cicilan as jml_cicilan',
                'a.tanggal_angsuran as tanggal_angsuran',
                'a.jml_angsuran as jml_angsuran',
                'a.bunga_pinjaman as bunga_pinjaman',
                'a.cicilan as cicilan',
                'a.denda as denda',
                'a.sisa_pinjam as sisa_angsuran',
                'a.status as status_angsuran' // tambahan kolom status
            )
            ->join('_anggota', '_anggota.id', '=', 'p.id_anggota')
            ->leftJoin('angsuran as a', 'p.id', '=', 'a.id_pinjaman')
            ->where('p.id', $id_pinjaman) // Pencarian berdasarkan id_pinjaman dari tabel pinjaman
            ->get();

        // Ambil data anggota
        $anggota = $laporan->first();

        // Hitung total angsuran dengan menjumlahkan jml_angsuran, denda, dan bunga_pinjaman
        $totalAngsuran = $laporan->sum(function ($item) {
            return $item->jml_angsuran + $item->denda + $item->bunga_pinjaman;
        });

        // Render view untuk PDF
        $pdf = PDF::loadView('backend.laporan.angsuran', compact('anggota', 'pinjaman', 'laporan', 'totalAngsuran'));

        // Unduh file PDF
        return $pdf->download('laporan_pinjamanAngsuran.pdf');
    }

    public function laporanPinjaman(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = DB::table('pinjaman')
            ->select(
                'pinjaman.id as pinjaman_id',
                'pinjaman.kodeTransaksiPinjaman',
                'pinjaman.tanggal_pinjam',
                'pinjaman.jatuh_tempo',
                'pinjaman.jml_pinjam',
                'pinjaman.jml_cicilan',
                'pinjaman.bunga_pinjam',
                'pinjaman.status_pengajuan',
                'users.name as created_by_name',
                '_anggota.name as anggota_name'
            )
            ->join('users', 'users.id', '=', 'pinjaman.created_by')
            ->join('_anggota', '_anggota.id', '=', 'pinjaman.id_anggota');

        if ($startDate && $endDate) {
            $query->whereBetween('pinjaman.tanggal_pinjam', [$startDate, $endDate]);
        }

        $pinjaman = $query->orderBy('pinjaman.id', 'DESC')->get();

        $pdf = PDF::loadView('backend.laporan.pinjaman', compact('pinjaman', 'startDate', 'endDate'));

        return $pdf->download('laporan_pinjaman.pdf');
    }

    public function laporanPenarikan(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = DB::table('penarikan')
            ->select(
                'penarikan.id as penarikan_id',
                'penarikan.kodeTransaksiPenarikan',
                'penarikan.tanggal_penarikan',
                'penarikan.jumlah_penarikan',
                'penarikan.keterangan',
                'users.name as created_by_name',
                '_anggota.name as anggota_name',
                '_anggota.saldo as anggota_saldo'

            )
            ->join('users', 'users.id', '=', 'penarikan.created_by')
            ->join('_anggota', '_anggota.id', '=', 'penarikan.id_anggota');

        if ($startDate && $endDate) {
            $query->whereBetween('penarikan.tanggal_penarikan', [$startDate, $endDate]);
        }

        $penarikan = $query->orderBy('penarikan.id', 'DESC')->get();

        $pdf = PDF::loadView('backend.laporan.penarikan', compact('penarikan', 'startDate', 'endDate'));

        return $pdf->download('laporan_penarikan.pdf');
    }
}
