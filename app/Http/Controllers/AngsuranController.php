<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AngsuranController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $query = DB::table('angsuran')
            ->join('pinjaman', 'angsuran.id_pinjaman', '=', 'pinjaman.id')
            ->join('_anggota', 'pinjaman.id_anggota', '=', '_anggota.id')
            ->select(
                'angsuran.kodeTransaksiAngsuran as kode_transaksi_angsuran',
                'pinjaman.kodeTransaksiPinjaman as kode_pinjaman',
                '_anggota.name as nasabah',
                'pinjaman.jml_pinjam as pinjaman_pokok',
                'pinjaman.sisa_pinjam as sisa_pinjam',
                'angsuran.bunga_pinjaman as bunga',
                'angsuran.cicilan as angsuran_ke',
                'angsuran.status as status',
                'angsuran.id as angsuran_id',
                'pinjaman.status_pengajuan',
                'angsuran.jml_angsuran',
                'angsuran.tanggal_angsuran',
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pinjaman.kodeTransaksiPinjaman', 'like', '%' . $search . '%')
                    ->orWhere('_anggota.name', 'like', '%' . $search . '%')
                    ->orWhere('angsuran.kodeTransaksiAngsuran', 'like', '%' . $search . '%');
            });
        }

        if ($start_date && $end_date) {
            $query->whereBetween('angsuran.tanggal_angsuran', [$start_date, $end_date]);
        }

        $angsuran = $query->paginate(10);

        return view('backend.angsuran.index', compact('angsuran'));
    }

    public function bayarAngsuran(Request $request, $pinjaman_id)
    {
        // Validasi input
        $request->validate([
            'tanggal_angsuran' => 'required|date',
            'jml_angsuran' => 'required|numeric|min:0',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi bukti pembayaran sebagai gambar
        ]);

        // Ambil data pinjaman
        $pinjaman = DB::table('pinjaman')->where('id', $pinjaman_id)->first();

        if (!$pinjaman) {
            return redirect()->back()->with('error', 'Pinjaman tidak ditemukan.');
        }

        // Cek status pengajuan pinjaman
        if ($pinjaman->status_pengajuan == 0 || $pinjaman->status_pengajuan == 2) {
            return redirect()->back()->with('error', 'Tidak bisa membayar angsuran karena status pengajuan pinjaman tidak valid.');
        }

        // Jumlah angsuran yang dibayar
        $jml_angsuran = $request->jml_angsuran;
        $bunga_angsuran = $jml_angsuran * 0.02; // Hitung bunga 2%
        $total_angsuran = $jml_angsuran + $bunga_angsuran; // Total angsuran dengan bunga

        // Periksa jika angsuran melebihi jumlah pinjaman yang tersisa
        if ($total_angsuran > $pinjaman->sisa_pinjam + $bunga_angsuran) {
            return redirect()->back()->with('error', 'Pembayaran melebihi jumlah yang harus dibayarkan.');
        }

        // Hitung jumlah cicilan yang sudah dibayar
        $jumlah_cicilan_sudah_dibayar = DB::table('angsuran')->where('id_pinjaman', $pinjaman_id)->count();

        // Periksa jika ini adalah cicilan terakhir dan pembayaran kurang dari sisa pinjaman
        $cicilan_terakhir = $pinjaman->jml_cicilan - $jumlah_cicilan_sudah_dibayar;
        if ($cicilan_terakhir == 1 && $jml_angsuran < $pinjaman->sisa_pinjam) {
            return redirect()->back()->with('error', 'Anda harus melunasi pinjaman karena ini adalah cicilan terakhir dan sisa pinjaman harus dibayarkan.');
        }

        // Upload bukti pembayaran
        $image = $request->file('bukti_pembayaran');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('assets/img'), $imageName);

        // Mendapatkan nomor transaksi terakhir
        $lastTransaction = DB::table('angsuran')
            ->where('kodeTransaksiAngsuran', 'LIKE', 'ANG-%')
            ->orderBy('kodeTransaksiAngsuran', 'desc')
            ->first();
        // Menentukan nomor urut angsuran baru
        $newTransactionNumber = $lastTransaction ? (int) substr($lastTransaction->kodeTransaksiAngsuran, 4) + 1 : 1;
        // Simpan data angsuran ke dalam tabel 'angsuran'
        $kodeTransaksiAngsuran = 'ANG-' . str_pad($newTransactionNumber, 4, '0', STR_PAD_LEFT);

        DB::table('angsuran')->insert([
            'kodeTransaksiAngsuran' => $kodeTransaksiAngsuran,
            'id_pinjaman' => $pinjaman_id,
            'tanggal_angsuran' => $request->tanggal_angsuran,
            'jml_angsuran' =>$request->jml_angsuran,
            'sisa_angsuran' => $pinjaman->sisa_pinjam - $jml_angsuran,
            'cicilan' => $jumlah_cicilan_sudah_dibayar + 1,
            'status' => ($pinjaman->sisa_pinjam - $jml_angsuran > 0) ? '0' : '1',
            'bunga_pinjaman' => $bunga_angsuran,
            'bukti_pembayaran' => $imageName, // Simpan nama file bukti pembayaran
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update jumlah pinjaman dan sisa pinjaman di tabel 'pinjaman'
        DB::table('pinjaman')->where('id', $pinjaman_id)->update([
            'sisa_pinjam' => $pinjaman->sisa_pinjam - $jml_angsuran,
            'status_pengajuan' => ($pinjaman->sisa_pinjam - $jml_angsuran > 0) ? $pinjaman->status_pengajuan : '3',
            'updated_at' => now(),
        ]);

        return redirect()->route('pinjaman.show', $pinjaman_id)
            ->with('success', 'Angsuran berhasil dilakukan. Total angsuran yang harus dibayar: Rp ' . number_format($total_angsuran, 0, ',', '.'));
    }


    public function destroy($id)
    {
        // Ambil data angsuran
        $angsuran = DB::table('angsuran')->where('id', $id)->first();

        if (!$angsuran) {
            return redirect()->back()->with('error', 'Angsuran tidak ditemukan.');
        }

        // Ambil data pinjaman terkait
        $pinjaman = DB::table('pinjaman')->where('id', $angsuran->id_pinjaman)->first();

        if (!$pinjaman) {
            return redirect()->back()->with('error', 'Pinjaman terkait tidak ditemukan.');
        }

        // Hapus file bukti pembayaran jika ada
        if ($angsuran->bukti_pembayaran) {
            $filePath = public_path('assets/img/' . $angsuran->bukti_pembayaran);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Update sisa pinjaman di tabel pinjaman
        $sisaPinjamBaru = $pinjaman->sisa_pinjam + $angsuran->jml_angsuran;
        DB::table('pinjaman')->where('id', $angsuran->id_pinjaman)->update([
            'sisa_pinjam' => $sisaPinjamBaru,
            'status_pengajuan' => 1,
            'updated_at' => now(),
        ]);

        // Update sisa pinjaman di tabel angsuran dan ubah status menjadi 0
        DB::table('angsuran')->where('id', $id)->update([
            'status' => 0,
            'sisa_angsuran' => $sisaPinjamBaru,
            'updated_at' => now(),
        ]);

        // Hapus data angsuran dari database
        DB::table('angsuran')->where('id', $id)->delete();

        return redirect()->route('angsuran')->with('message', 'Angsuran berhasil dihapus dan sisa pinjaman diperbarui.');
    }


}
