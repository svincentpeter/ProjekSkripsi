<?php

namespace App\Http\Controllers;

use App\Exports\PenarikanExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PenarikanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua anggota (bisa difilter status_anggota jika diperlukan)
        $anggota = DB::table('anggota')->get();

        // Query penarikan
        $query = DB::table('penarikan')
            ->select([
                'penarikan.id            as penarikan_id',
                'penarikan.kode_transaksi',
                'penarikan.tanggal_penarikan',
                'penarikan.jumlah_penarikan',
                'penarikan.keterangan',
                'anggota.name            as anggota_name',
                'anggota.saldo           as anggota_saldo',
            ])
            ->leftJoin('anggota', 'anggota.id', '=', 'penarikan.anggota_id');

        // Filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('penarikan.tanggal_penarikan', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $term = "%{$request->search}%";
            $query->where(function($q) use($term) {
                $q->where('penarikan.kode_transaksi', 'like', $term)
                  ->orWhere('anggota.name',            'like', $term);
            });
        }

        $penarikan = $query->orderBy('penarikan.id', 'desc')->paginate(5);

        return view('backend.penarikan.index', compact('penarikan', 'anggota'));
    }

    public function store(Request $request)
{
    $request->validate([
        'anggota_id'         => 'required|exists:anggota,id',
        'tanggal_penarikan'  => 'required|date',
        'jumlah_penarikan'   => 'required|numeric|min:1',
        'keterangan'         => 'nullable|string',
    ]);

    $anggota = DB::table('anggota')->where('id', $request->anggota_id)->first();
    if (!$anggota) {
        return back()->with('error', 'Anggota tidak ditemukan.');
    }

    // Validasi saldo cukup
    if ($request->jumlah_penarikan > $anggota->saldo) {
        return back()->withInput()->with('error', 'Jumlah penarikan melebihi saldo anggota!');
    }

    // Cek pinjaman aktif
    $hasLoan = DB::table('pinjaman')
        ->where('anggota_id', $request->anggota_id)
        ->where('status', '!=', 'SELESAI')
        ->exists();

    if ($hasLoan) {
        return back()->withInput()->with('error', 'Saldo tidak bisa ditarik karena masih ada pinjaman berjalan.');
    }

    // Simpan pakai transaksi
    DB::transaction(function() use ($request, $anggota) {
        // Generate kode penarikan baru
        $last = DB::table('penarikan')->orderByDesc('id')->first();
        $next = $last ? ($last->id + 1) : 1;
        $kode = 'PNR-' . str_pad($next, 4, '0', STR_PAD_LEFT);

        // Insert penarikan
        DB::table('penarikan')->insert([
            'anggota_id'         => $request->anggota_id,
            'tanggal_penarikan'  => $request->tanggal_penarikan,
            'jumlah_penarikan'   => $request->jumlah_penarikan,
            'keterangan'         => $request->keterangan,
            'kode_transaksi'     => $kode,
            'created_by'         => Auth::id(),
            'updated_by'         => Auth::id(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Update saldo anggota
        DB::table('anggota')
            ->where('id', $request->anggota_id)
            ->decrement('saldo', $request->jumlah_penarikan);

        // Update status anggota jika saldo <= 0
        $sisa = DB::table('anggota')
            ->where('id', $request->anggota_id)
            ->value('saldo');
        if ($sisa <= 0) {
            DB::table('anggota')
                ->where('id', $request->anggota_id)
                ->update(['status_anggota' => '0']);
        }
    });

    return redirect()->route('penarikan.index')
        ->with('success', 'Penarikan berhasil disimpan.');
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_penarikan' => 'required|numeric|min:0',
            'keterangan'       => 'nullable|string',
        ]);

        $penarikan = DB::table('penarikan')->find($id);
        if (!$penarikan) {
            return back()->with('error', 'Data penarikan tidak ditemukan.');
        }

        // Hitung selisih
        $selisih = $request->jumlah_penarikan - $penarikan->jumlah_penarikan;

        // Update penarikan
        DB::table('penarikan')->where('id', $id)->update([
            'jumlah_penarikan' => $request->jumlah_penarikan,
            'keterangan'       => $request->keterangan,
            'updated_by'       => Auth::id(),
            'updated_at'       => now(),
        ]);

        // Koreksi saldo anggota
        DB::table('anggota')
            ->where('id', $penarikan->anggota_id)
            ->increment('saldo', -$selisih);

        // Perbarui status_anggota
        $sisa = DB::table('anggota')
            ->where('id', $penarikan->anggota_id)
            ->value('saldo');

        DB::table('anggota')
            ->where('id', $penarikan->anggota_id)
            ->update(['status_anggota' => $sisa > 0 ? '1' : '0']);

        return back()->with('success', 'Penarikan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penarikan = DB::table('penarikan')->find($id);
        if (!$penarikan) {
            return back()->with('error', 'Data penarikan tidak ditemukan.');
        }

        // Kembalikan saldo anggota
        DB::table('anggota')
            ->where('id', $penarikan->anggota_id)
            ->increment('saldo', $penarikan->jumlah_penarikan);

        // Hapus penarikan
        DB::table('penarikan')->where('id', $id)->delete();

        // Perbarui status_anggota
        $sisa = DB::table('anggota')
            ->where('id', $penarikan->anggota_id)
            ->value('saldo');

        DB::table('anggota')
            ->where('id', $penarikan->anggota_id)
            ->update(['status_anggota' => $sisa > 0 ? '1' : '0']);

        return redirect()->route('penarikan')
            ->with('success', 'Data penarikan berhasil dihapus.');
    }

    public function excel(Request $request)
{
    // Query/filter sesuai index (pastikan sama logicnya)
    $query = DB::table('penarikan')
        ->select([
            'penarikan.kode_transaksi',
            'penarikan.tanggal_penarikan',
            'penarikan.jumlah_penarikan',
            'penarikan.keterangan',
            'anggota.name as anggota_name'
        ])
        ->leftJoin('anggota', 'anggota.id', '=', 'penarikan.anggota_id');

    // Apply filter tanggal jika ada
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('penarikan.tanggal_penarikan', [$request->start_date, $request->end_date]);
    }
    // Apply search jika ada
    if ($request->filled('search')) {
        $term = "%{$request->search}%";
        $query->where(function($q) use($term) {
            $q->where('penarikan.kode_transaksi', 'like', $term)
                ->orWhere('anggota.name', 'like', $term);
        });
    }

    $penarikan = $query->orderBy('penarikan.id', 'desc')->get();

    // Export ke excel
    return Excel::download(new PenarikanExport($penarikan), 'data-penarikan.xlsx');
}

public function showAjax($id)
{
    $penarikan = DB::table('penarikan')
        ->select(
            'penarikan.*',
            'anggota.name as anggota_name',
            'anggota.nip',
            'anggota.telphone',
            'anggota.saldo as saldo_anggota'
        )
        ->leftJoin('anggota', 'anggota.id', '=', 'penarikan.anggota_id')
        ->where('penarikan.id', $id)
        ->first();

    if (!$penarikan) {
        return response()->json(['error' => 'Data tidak ditemukan.'], 404);
    }

    // Format tanggal & angka di sini jika mau (atau di blade nanti)
    return response()->json($penarikan);
}

}
