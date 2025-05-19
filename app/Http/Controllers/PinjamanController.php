<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $search    = $request->get('search');

        // Semua anggota
        $anggota = DB::table('anggota')->get();

        $query = DB::table('pinjaman')
            ->select([
                'pinjaman.id as pinjaman_id',
                'pinjaman.kode_transaksi',
                'pinjaman.tanggal_pinjam',
                'pinjaman.jatuh_tempo',
                'pinjaman.jumlah_pinjam',
                'pinjaman.tenor',
                'pinjaman.bunga',
                'pinjaman.status',             // enum('PENDING','DISETUJUI','DITOLAK')
                'users.name as created_by_name',
                'anggota.name as anggota_name',
            ])
            ->join('users',   'users.id',   '=', 'pinjaman.created_by')
            ->join('anggota','anggota.id', '=', 'pinjaman.anggota_id')
            ->orderByDesc('pinjaman.id');

        if ($startDate && $endDate) {
            $query->whereBetween('pinjaman.tanggal_pinjam', [$startDate, $endDate]);
        }

        if ($search) {
            $term = "%{$search}%";
            $query->where(function($q) use($term) {
                $q->where('pinjaman.kode_transaksi', 'like', $term)
                  ->orWhere('anggota.name',           'like', $term);
            });
        }

        $pinjaman = $query->paginate(5);

        // Hitung maksimum pinjaman baru
        $totalSaldo              = DB::table('anggota')->sum('saldo');
        $maxPinjaman             = $totalSaldo * 1;          // atau 0.9 sesuai logika
        $totalPinjamanSekarang   = DB::table('pinjaman')->sum('jumlah_pinjam');
        $maxPinjamanBaru         = $maxPinjaman - $totalPinjamanSekarang;

        // Kode transaksi baru
        $last = DB::table('pinjaman')->orderByDesc('id')->first();
        $next = $last ? ($last->id + 1) : 1;
        $kodeTransaksi = 'PNJ-' . str_pad($next, 4, '0', STR_PAD_LEFT);

        return view('backend.pinjaman.index', compact(
            'pinjaman',
            'maxPinjamanBaru',
            'anggota',
            'kodeTransaksi'
        ));
    }

    public function create()
    {
        $users   = DB::table('users')->get();
        $anggota = DB::table('anggota')->get();

        $last = DB::table('pinjaman')->orderByDesc('id')->first();
        $next = $last ? ($last->id + 1) : 1;
        $kodeTransaksi = 'PNJ-' . str_pad($next, 4, '0', STR_PAD_LEFT);

        $totalSaldo            = DB::table('anggota')->sum('saldo');
        $maxPinjaman           = $totalSaldo * 0.9;
        $totalPinjamanExist    = DB::table('pinjaman')->sum('jumlah_pinjam');
        $maxPinjamanBaru       = $maxPinjaman - $totalPinjamanExist;

        return view('backend.pinjaman.create', compact(
            'users',
            'anggota',
            'kodeTransaksi',
            'maxPinjamanBaru'
        ));
    }

    private function generateKodeTransaksiPinjaman()
    {
        $last = DB::table('pinjaman')->orderByDesc('id')->first();
        $next = $last ? ($last->id + 1) : 1;
        return 'PNJ-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pinjam'  => 'required|date',
            'jumlah_pinjam'   => 'required|numeric',
            'tenor'           => 'required|numeric',
            'anggota_id'      => 'required|exists:anggota,id',
            'bunga'           => 'required|numeric|min:0|max:100',
        ]);

        // Cek pinjaman aktif
        $hasLoan = DB::table('pinjaman')
            ->where('anggota_id', $request->anggota_id)
            ->where('status', '!=', 'DITOLAK')
            ->exists();

        if ($hasLoan) {
            return redirect()->route('pinjaman')
                ->with('error', 'Anda belum menyelesaikan pinjaman sebelumnya.');
        }

        $totalSaldo          = DB::table('anggota')->sum('saldo');
        $maxPinjaman         = $totalSaldo * 0.9;
        $jumlahPinjamanNow   = DB::table('pinjaman')->sum('jumlah_pinjam');
        $maxPinjamanBaru     = $maxPinjaman - $jumlahPinjamanNow;

        if ($request->jumlah_pinjam > $maxPinjamanBaru) {
            return redirect()->route('pinjaman')
                ->with('error', 'Jumlah pinjaman melebihi batas maksimum.');
        }

        // Kode & jatuh tempo
        $kodeTransaksi = $this->generateKodeTransaksiPinjaman();
        $tenor = (int) $request->tenor;

        $jatuhTempo = now()->parse($request->tanggal_pinjam)
                        ->addMonths($tenor)
                        ->format('Y-m-d');

        DB::table('pinjaman')->insert([
            'kode_transaksi'             => $kodeTransaksi,
            'tanggal_pinjam'             => $request->tanggal_pinjam,
            'jatuh_tempo'                => $jatuhTempo,
            'jumlah_pinjam'              => $request->jumlah_pinjam,
            'bunga'                      => $request->bunga,
            'tenor'                      => $request->tenor,
            'status'                     => 'PENDING',
            'keterangan_ditolak_pengajuan'=> null,
            'created_by'                 => auth()->id(),
            'updated_by'                 => auth()->id(),
            'anggota_id'                 => $request->anggota_id,
            'created_at'                 => now(),
            'updated_at'                 => now(),
        ]);

        return redirect()->route('pinjaman')
            ->with('success', 'Pinjaman berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_pinjam' => 'required|numeric',
            'tenor'         => 'required|numeric|min:1|max:60',
            'jatuh_tempo'   => 'required|date',
        ]);

        $pinjaman = DB::table('pinjaman')->find($id);
        if (!$pinjaman || $pinjaman->status !== 'PENDING') {
            return redirect()->route('pinjaman')
                ->with('error', 'Tidak bisa memperbarui—status pinjaman tidak valid.');
        }

        DB::table('pinjaman')->where('id', $id)->update([
            'jumlah_pinjam' => $request->jumlah_pinjam,
            'tenor'         => $request->tenor,
            'jatuh_tempo'   => $request->jatuh_tempo,
            'updated_by'    => auth()->id(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('pinjaman')
            ->with('success', 'Pinjaman berhasil diperbarui.');
    }

    public function terima($id)
    {
        DB::table('pinjaman')->where('id', $id)->update(['status' => 'DISETUJUI']);
        return redirect()->route('pinjaman.show', $id)
            ->with('success', 'Pinjaman disetujui.');
    }

    public function tolak(Request $request, $id)
    {
        $pin = DB::table('pinjaman')->find($id);
        $notes = $pin->keterangan_ditolak_pengajuan
               ? json_decode($pin->keterangan_ditolak_pengajuan, true)
               : [];

        $notes[] = $request->input('catatan');
        DB::table('pinjaman')->where('id', $id)->update([
            'status'                       => 'DITOLAK',
            'keterangan_ditolak_pengajuan' => json_encode($notes),
            'updated_by'                   => auth()->id(),
            'updated_at'                   => now(),
        ]);

        return redirect()->route('pinjaman.show', $id)
            ->with('success', 'Pinjaman ditolak.');
    }

    public function show($pinjaman_id)
    {
        $pinjaman = DB::table('pinjaman')
            ->select([
                'pinjaman.id as pinjaman_id',
                'pinjaman.kode_transaksi',
                'pinjaman.tanggal_pinjam',
                'pinjaman.jatuh_tempo',
                'pinjaman.jumlah_pinjam',
                'pinjaman.tenor',
                'pinjaman.bunga',
                'pinjaman.status',
                'users.name as created_by_name',
                'anggota.name as anggota_name'
            ])
            ->join('users',  'users.id',   '=', 'pinjaman.created_by')
            ->join('anggota','anggota.id', '=', 'pinjaman.anggota_id')
            ->where('pinjaman.id', $pinjaman_id)
            ->first();

        abort_if(!$pinjaman, 404);

        // total dengan bunga
        $totalBunga = ($pinjaman->jumlah_pinjam * $pinjaman->bunga) / 100;
        $pinjaman->total_dengan_bunga = $pinjaman->jumlah_pinjam + $totalBunga;

        $angsuran = DB::table('angsuran')
            ->select([
                'angsuran.id as angsuran_id',
                'angsuran.kode_transaksi',
                'angsuran.tanggal_angsuran',
                'angsuran.jumlah_angsuran',
                'angsuran.sisa_pinjam as sisa_angsuran',
                'angsuran.cicilan',
                'angsuran.status',
                'angsuran.denda',
                'angsuran.keterangan',
                'angsuran.bukti_pembayaran',
                'angsuran.bunga_pinjaman',
                DB::raw('(angsuran.jumlah_angsuran + angsuran.bunga_pinjaman + COALESCE(angsuran.denda,0)) as total_angsuran'),
                'users.name as created_by_name',
            ])
            ->join('users','users.id','=','angsuran.created_by')
            ->where('angsuran.pinjaman_id', $pinjaman_id)
            ->orderBy('angsuran.tanggal_angsuran')
            ->paginate(5);

        $totalAngsuran = DB::table('angsuran')
            ->where('pinjaman_id', $pinjaman_id)
            ->sum(DB::raw('jumlah_angsuran + bunga_pinjaman + COALESCE(denda,0)'));

        return view('backend.pinjaman.show', compact(
            'pinjaman',
            'angsuran',
            'totalAngsuran'
        ));
    }

    public function destroy($id)
    {
        $pinjaman = DB::table('pinjaman')->find($id);
        abort_if(!$pinjaman, 404);

        // hapus bukti di filesystem
        if ($pinjaman->bukti_pembayaran) {
            @unlink(public_path($pinjaman->bukti_pembayaran));
        }

        // hapus semua angsuran terkait
        DB::table('angsuran')->where('pinjaman_id', $id)->delete();
        DB::table('pinjaman')->where('id', $id)->delete();

        return redirect()->route('pinjaman')
            ->with('success', 'Pinjaman berhasil dihapus.');
    }
}
