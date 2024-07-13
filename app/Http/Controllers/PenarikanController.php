<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PenarikanController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data anggota yang aktif (status_anggota = 1)
        $anggota = DB::table('_anggota')->where('status_anggota', 1)->get();

        // Mengambil data penarikan
        $penarikan = DB::table('penarikan')
            ->select('penarikan.id as penarikan_id', 'penarikan.*', '_anggota.*')
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
        return view('backend.penarikan.index', compact('penarikan', 'anggota'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_anggota' => 'required',
            'tanggal_penarikan' => 'required|date',
            'jumlah_penarikan' => 'required|numeric|min:0',
        ]);

        // Periksa status pinjaman anggota 
        $pinjaman = DB::table('pinjaman')
            ->where('id_anggota', $request->id_anggota)
            ->first();

        if ($pinjaman && $pinjaman->status_pengajuan == 3) {
            Session::flash('error', 'Saldo tidak bisa ditarik karena status pinjaman belum selesai.');
            return redirect()->route('penarikan.index');
        }

        // Generate kode transaksi penarikan
        $kodeTransaksiPenarikan = $this->generateKodeTransaksiPenarikan();

        // Simpan data penarikan
        DB::table('penarikan')->insert([
            'id_anggota' => $request->id_anggota,
            'tanggal_penarikan' => $request->tanggal_penarikan,
            'jumlah_penarikan' => $request->jumlah_penarikan,
            'keterangan' => $request->keterangan,
            'kodeTransaksipenarikan' => $kodeTransaksiPenarikan,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update saldo anggota
        DB::table('_anggota')
            ->where('id', $request->id_anggota)
            ->decrement('saldo', $request->jumlah_penarikan);

        // Periksa saldo anggota setelah penarikan
        $anggota = DB::table('_anggota')->where('id', $request->id_anggota)->first();
        if ($anggota->saldo <= 0) {
            DB::table('_anggota')
                ->where('id', $request->id_anggota)
                ->update(['status_anggota' => 0]);
        }
        Session::flash('success', 'Penarikan berhasil disimpan.');
        return redirect()->route('penarikan');
    }

    private function generateKodeTransaksiPenarikan()
    {
        $lastTransaction = DB::table('penarikan')->orderBy('id', 'desc')->first();
        $lastId = $lastTransaction ? $lastTransaction->id + 1 : 1;

        return 'PNR-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
    }

    public function destroy($id)
    {
        // Ambil data penarikan
        $penarikan = DB::table('penarikan')->where('id', $id)->first();

        if (!$penarikan) {
            Session::flash('error', 'Data penarikan tidak ditemukan.');
            return redirect()->route('penarikan');
        }

        // Kembalikan saldo anggota
        DB::table('_anggota')
            ->where('id', $penarikan->id_anggota)
            ->increment('saldo', $penarikan->jumlah_penarikan);

        // Hapus data penarikan
        DB::table('penarikan')->where('id', $id)->delete();

        // Periksa saldo anggota setelah pengembalian
        $anggota = DB::table('_anggota')->where('id', $penarikan->id_anggota)->first();
        if ($anggota->saldo > 0) {
            DB::table('_anggota')
                ->where('id', $penarikan->id_anggota)
                ->update(['status_anggota' => 1]);
        }

        Session::flash('success', 'Data penarikan berhasil dihapus.');
        return redirect()->route('penarikan');
    }
}
