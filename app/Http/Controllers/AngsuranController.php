<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AngsuranController extends Controller
{
    // INDEX: List, Filter, Search
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $status     = $request->input('status');
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');

        $query = DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->join('anggota', 'pinjaman.anggota_id', '=', 'anggota.id')
            ->select(
                'angsuran.id as angsuran_id',
                'angsuran.kode_transaksi as kode_transaksi_angsuran',
                'pinjaman.id as pinjaman_id',
                'pinjaman.kode_transaksi as kode_pinjaman',
                'anggota.name as nasabah',
                'pinjaman.jumlah_pinjam as pinjaman_pokok',
                'angsuran.jumlah_angsuran',
                'angsuran.sisa_pinjam',
                'angsuran.cicilan as angsuran_ke',
                'angsuran.status',
                'angsuran.tanggal_angsuran',
                'angsuran.bukti_pembayaran'
            )
            ->orderBy('angsuran.tanggal_angsuran', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pinjaman.kode_transaksi', 'like', "%$search%")
                  ->orWhere('anggota.name', 'like', "%$search%")
                  ->orWhere('angsuran.kode_transaksi', 'like', "%$search%");
            });
        }
        if ($status && in_array($status, ['PENDING','LUNAS'])) {
            $query->where('angsuran.status', $status);
        }
        if ($start_date && $end_date) {
            $query->whereBetween('angsuran.tanggal_angsuran', [$start_date, $end_date]);
        }

        $angsuran = $query->paginate(10);

        return view('backend.angsuran.index', compact('angsuran'));
    }

    // SHOW: Detail Angsuran
    public function show($id)
    {
        $angsuran = DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->join('anggota', 'pinjaman.anggota_id', '=', 'anggota.id')
            ->join('users', 'angsuran.created_by', '=', 'users.id')
            ->select(
                'angsuran.*',
                'anggota.name as anggota_name',
                'users.name as created_by_name',
                'pinjaman.kode_transaksi as kode_pinjaman'
            )
            ->where('angsuran.id', $id)
            ->first();

        if (!$angsuran) abort(404);

        return view('backend.angsuran.show', compact('angsuran'));
    }

    // CREATE: Form Tambah Angsuran
    public function create()
    {
        $listPinjaman = DB::table('pinjaman')
            ->join('anggota', 'pinjaman.anggota_id', '=', 'anggota.id')
            ->select(
                'pinjaman.id',
                'pinjaman.kode_transaksi',
                'anggota.name as anggota_name',
                'pinjaman.sisa_pinjam',
                'pinjaman.bunga'
            )
            ->where('pinjaman.sisa_pinjam', '>', 0)
            ->where('pinjaman.status', 'DISETUJUI')
            ->get();

        return view('backend.angsuran.create', compact('listPinjaman'));
    }

    // STORE: Simpan Angsuran Baru
    public function store(Request $request)
    {
        $request->validate([
            'pinjaman_id'      => 'required|exists:pinjaman,id',
            'tanggal_angsuran' => 'required|date',
            'jumlah_angsuran'  => 'required|numeric|min:1',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $pinjaman = DB::table('pinjaman')->where('id', $request->pinjaman_id)->first();
            if (!$pinjaman || $pinjaman->status != 'DISETUJUI') {
                return back()->with('error', 'Pinjaman tidak valid atau belum disetujui.');
            }

            // Sisa pinjaman = pinjaman - total angsuran yang sudah dibayar
            $total_angsuran = DB::table('angsuran')->where('pinjaman_id', $pinjaman->id)->sum('jumlah_angsuran');
            $sisa = $pinjaman->jumlah_pinjam - $total_angsuran;

            if ($request->jumlah_angsuran > $sisa) {
                return back()->with('error', 'Jumlah angsuran melebihi sisa pinjaman.');
            }

            // Proses upload gambar
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = 'assets/img/' . $filename;
            $file->move(public_path('assets/img/'), $filename);

            // Generate kode transaksi
            $last = DB::table('angsuran')->orderByDesc('id')->first();
            $next = $last ? ($last->id + 1) : 1;
            $kode_transaksi = 'ANG-' . str_pad($next, 4, '0', STR_PAD_LEFT);

            // Hitung bunga
            $bunga_angsuran = ($request->jumlah_angsuran * $pinjaman->bunga) / 100;

            // Denda
            $denda = 0;
            $jatuhTempo = Carbon::parse($pinjaman->jatuh_tempo);
            $tglAngsuran = Carbon::parse($request->tanggal_angsuran);
            if ($tglAngsuran->greaterThan($jatuhTempo)) {
                $denda = abs(($pinjaman->jumlah_pinjam * 0.01) * $tglAngsuran->diffInDays($jatuhTempo));
            }

            // Simpan angsuran
            $angsuran_id = DB::table('angsuran')->insertGetId([
                'kode_transaksi'    => $kode_transaksi,
                'pinjaman_id'       => $pinjaman->id,
                'tanggal_angsuran'  => $request->tanggal_angsuran,
                'jumlah_angsuran'   => $request->jumlah_angsuran,
                'sisa_pinjam'       => $sisa - $request->jumlah_angsuran,
                'cicilan'           => DB::table('angsuran')->where('pinjaman_id', $pinjaman->id)->count() + 1,
                'status'            => ($sisa - $request->jumlah_angsuran) > 0 ? 'PENDING' : 'LUNAS',
                'keterangan'        => $request->keterangan,
                'bukti_pembayaran'  => $filename,
                'bunga_pinjaman'    => $bunga_angsuran,
                'denda'             => $denda,
                'created_by'        => Auth::id(),
                'updated_by'        => Auth::id(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Update sisa pinjaman & status
            DB::table('pinjaman')->where('id', $pinjaman->id)->update([
                'sisa_pinjam' => $sisa - $request->jumlah_angsuran,
                'status'      => ($sisa - $request->jumlah_angsuran) > 0 ? 'DISETUJUI' : 'LUNAS',
                'updated_at'  => now(),
            ]);

            DB::commit();
            return redirect()->route('angsuran.show', $angsuran_id)
                ->with('success', 'Angsuran berhasil ditambahkan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan angsuran: ' . $th->getMessage());
        }
    }

    // EDIT: Form Edit
    public function edit($id)
    {
        $angsuran = DB::table('angsuran')->where('id', $id)->first();
        if (!$angsuran) abort(404);
        return view('backend.angsuran.edit', compact('angsuran'));
    }

    // UPDATE: Update Data
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_angsuran'  => 'required|numeric|min:1',
            'bunga_pinjaman'   => 'nullable|numeric',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $angsuran = DB::table('angsuran')->where('id', $id)->first();
        if (!$angsuran) return back()->with('error', 'Data angsuran tidak ditemukan.');
        $pinjaman = DB::table('pinjaman')->where('id', $angsuran->pinjaman_id)->first();

        $data = [
            'jumlah_angsuran' => $request->jumlah_angsuran,
            'bunga_pinjaman'  => $request->bunga_pinjaman,
            'keterangan'      => $request->keterangan,
            'updated_by'      => Auth::id(),
            'updated_at'      => now(),
        ];

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img/'), $filename);

            // Hapus file lama
            if ($angsuran->bukti_pembayaran && file_exists(public_path('assets/img/' . $angsuran->bukti_pembayaran))) {
                @unlink(public_path('assets/img/' . $angsuran->bukti_pembayaran));
            }
            $data['bukti_pembayaran'] = $filename;
        }

        // Update status & sisa pinjam
        $total_angsuran_lain = DB::table('angsuran')
            ->where('pinjaman_id', $pinjaman->id)
            ->where('id', '!=', $id)
            ->sum('jumlah_angsuran');
        $sisa = $pinjaman->jumlah_pinjam - $total_angsuran_lain - $request->jumlah_angsuran;
        $data['sisa_pinjam'] = $sisa;
        $data['status'] = ($sisa <= 0) ? 'LUNAS' : 'PENDING';

        DB::table('angsuran')->where('id', $id)->update($data);

        DB::table('pinjaman')->where('id', $pinjaman->id)->update([
            'sisa_pinjam' => $sisa,
            'status'      => ($sisa <= 0) ? 'LUNAS' : 'DISETUJUI',
            'updated_at'  => now(),
        ]);

        return redirect()->route('angsuran.show', $id)
            ->with('success', 'Data angsuran berhasil diupdate.');
    }

    // DELETE: Hapus Angsuran
    public function destroy($id)
    {
        $angsuran = DB::table('angsuran')->where('id', $id)->first();
        if (!$angsuran) return back()->with('error', 'Data angsuran tidak ditemukan.');
        // Hapus file bukti
        if ($angsuran->bukti_pembayaran && file_exists(public_path('assets/img/' . $angsuran->bukti_pembayaran))) {
            @unlink(public_path('assets/img/' . $angsuran->bukti_pembayaran));
        }
        // Update sisa pinjam di pinjaman
        $pinjaman = DB::table('pinjaman')->where('id', $angsuran->pinjaman_id)->first();
        if ($pinjaman) {
            $newSisa = $pinjaman->sisa_pinjam + $angsuran->jumlah_angsuran;
            $newStatus = ($newSisa <= 0) ? 'LUNAS' : 'DISETUJUI';
            DB::table('pinjaman')->where('id', $angsuran->pinjaman_id)->update([
                'sisa_pinjam' => $newSisa,
                'status'      => $newStatus,
                'updated_at'  => now(),
            ]);
        }
        DB::table('angsuran')->where('id', $id)->delete();

        return redirect()->route('angsuran.index')->with('success', 'Data angsuran berhasil dihapus.');
    }

    public function cetak($id)
{
    $angsuran = DB::table('angsuran')
        ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
        ->join('anggota', 'pinjaman.anggota_id', '=', 'anggota.id')
        ->join('users', 'angsuran.created_by', '=', 'users.id')
        ->select(
            'angsuran.*',
            'anggota.name as anggota_name',
            'users.name as created_by_name',
            'pinjaman.kode_transaksi as kode_pinjaman'
        )
        ->where('angsuran.id', $id)
        ->first();

    if (!$angsuran) abort(404);

    // Silakan render ke PDF atau halaman print biasa.
    return view('backend.angsuran.cetak', compact('angsuran'));
}
}

