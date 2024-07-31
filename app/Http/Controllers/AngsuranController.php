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

        // Hitung bunga berdasarkan persentase bunga pinjaman
        $bunga_angsuran = ($jml_angsuran * $pinjaman->bunga_pinjam) / 100;

        // Hitung sisa pinjaman berdasarkan data angsuran sebelumnya
        $total_dibayar = DB::table('angsuran')
        ->where('id_pinjaman', $pinjaman_id)
            ->sum('jml_angsuran');

        $sisa_pinjam = $pinjaman->jml_pinjam - $total_dibayar;

        // Periksa jika angsuran melebihi jumlah pinjaman yang tersisa
        if ($jml_angsuran > $sisa_pinjam) {
            return redirect()->back()->with('error', 'Pembayaran melebihi jumlah yang harus dibayarkan.');
        }

        // Hitung jumlah cicilan yang sudah dibayar
        $jumlah_cicilan_sudah_dibayar = DB::table('angsuran')->where('id_pinjaman', $pinjaman_id)->count();

        // Periksa jika ini adalah cicilan terakhir dan pembayaran kurang dari sisa pinjaman
        $cicilan_terakhir = $pinjaman->jml_cicilan - $jumlah_cicilan_sudah_dibayar;
        if ($cicilan_terakhir == 1 && $jml_angsuran < $sisa_pinjam) {
            return redirect()->back()->with('error', 'Anda harus melunasi pinjaman karena ini adalah cicilan terakhir dan sisa pinjaman harus dibayarkan.');
        }

        // Hitung denda jika tanggal angsuran melewati jatuh tempo
        $jatuh_tempo = \Carbon\Carbon::parse($pinjaman->jatuh_tempo);
        $tanggal_angsuran = \Carbon\Carbon::parse($request->tanggal_angsuran);
        $denda = 0;

        if ($tanggal_angsuran->greaterThan($jatuh_tempo)) {
            $hari_terlambat = $tanggal_angsuran->diffInDays($jatuh_tempo);
            $denda = abs(($pinjaman->jml_pinjam * 0.01) * $hari_terlambat);
        }

        // Total angsuran dengan bunga dan denda
        $total_angsuran = $jml_angsuran + $bunga_angsuran + $denda;

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
            'jml_angsuran' => $jml_angsuran,
            'sisa_pinjam' => $sisa_pinjam - $jml_angsuran,
            'cicilan' => $jumlah_cicilan_sudah_dibayar + 1,
            'status' => ($sisa_pinjam - $jml_angsuran > 0) ? '0' : '1',
            'bunga_pinjaman' => $bunga_angsuran,
            'denda' => $denda,
           
            'bukti_pembayaran' => $imageName, // Simpan nama file bukti pembayaran
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update jumlah pinjaman dan sisa pinjaman di tabel 'pinjaman'
        DB::table('pinjaman')->where('id', $pinjaman_id)->update([
            'status_pengajuan' => ($sisa_pinjam - $jml_angsuran > 0) ? $pinjaman->status_pengajuan : '3',
            'updated_at' => now(),
        ]);

        return redirect()->route('pinjaman.show', $pinjaman_id)
            ->with('success', 'Angsuran berhasil dilakukan. Total angsuran yang harus dibayar: Rp ' . number_format($total_angsuran, 0, ',', '.') . '. Denda: Rp ' . number_format($denda, 0, ',', '.'));
    }




    public function update(Request $request, $id)
    {
        $request->validate([
            'jml_angsuran' => 'required|numeric',
            'bunga_pinjaman' => 'nullable|numeric',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Temukan Angsuran berdasarkan ID
        $angsuran = DB::table('angsuran')->where('id', $id)->first();

        if (!$angsuran) {
            return redirect()->back()->with('error', 'Angsuran tidak ditemukan.');
        }

        // Temukan Pinjaman yang terkait
        $pinjaman = DB::table('pinjaman')->where('id', $angsuran->id_pinjaman)->first();

        if (!$pinjaman) {
            return redirect()->back()->with('error', 'Pinjaman tidak ditemukan.');
        }

        $jumlahAngsuranBaru = $request->input('jml_angsuran');
        $jumlahAngsuranLama = $angsuran->jml_angsuran;

        // Validasi bahwa jumlah angsuran baru tidak melebihi sisa saldo pinjaman yang tersisa ditambah jumlah angsuran lama
        if ($jumlahAngsuranBaru > $pinjaman->sisa_pinjam + $jumlahAngsuranLama) {
            return redirect()->back()->with('error', 'Jumlah angsuran tidak boleh melebihi sisa pinjaman.');
        }

        // Hitung selisih antara angsuran baru dan lama
        $selisihAngsuran = $jumlahAngsuranBaru - $jumlahAngsuranLama;

        // Persiapkan data untuk pembaruan
        $data = [
            'jml_angsuran' => $jumlahAngsuranBaru,
            'bunga_pinjaman' => $request->input('bunga_pinjaman'),
            'sisa_pinjam' => $angsuran->sisa_angsuran - $selisihAngsuran,
            'updated_by' => auth()->user()->id,
            'updated_at' => now(),
        ];

        // Periksa jika ada file bukti pembayaran baru yang diunggah
        if ($request->hasFile('bukti_pembayaran')) {
            // Hapus file lama jika ada
            if ($angsuran->bukti_pembayaran && file_exists(public_path($angsuran->bukti_pembayaran))) {
                unlink(public_path($angsuran->bukti_pembayaran));
            }

            // Simpan file baru
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img/'), $filename);
            $data['bukti_pembayaran'] = 'assets/img/' . $filename;
        }

        // Perbarui Angsuran
        DB::table('angsuran')->where('id', $id)->update($data);

        // Perbarui saldo pinjaman yang tersisa di tabel pinjaman
        $sisaSaldo = $pinjaman->sisa_pinjam - $selisihAngsuran;

        // Cek dan perbarui status angsuran dan pinjaman
        if ($data['sisa_pinjam'] > 0) {
            $data['status'] = 0; // Belum lunas
            DB::table('pinjaman')->where('id', $pinjaman->id)->update([
                'sisa_pinjam' => $sisaSaldo,
                'status_pengajuan' => 1, // Belum lunas
                'updated_at' => now(),
            ]);
        } else {
            $data['status'] = 1; // Lunas
            DB::table('pinjaman')->where('id', $pinjaman->id)->update([
                'sisa_pinjam' => $sisaSaldo,
                'status_pengajuan' => 3, // Selesai
                'updated_at' => now(),
            ]);
        }

        // Perbarui Angsuran
        DB::table('angsuran')->where('id', $id)->update($data);

        // Redirect ke halaman detail pinjaman
        return redirect()->route('pinjaman.show', ['id' => $pinjaman->id])
            ->with('success', 'Angsuran berhasil diperbarui.');
    }



    public function destroy($id)
    {
        // Temukan Angsuran berdasarkan ID
        $angsuran = DB::table('angsuran')->where('id', $id)->first();

        if (!$angsuran) {
            return redirect()->back()->with('error', 'Angsuran tidak ditemukan.');
        }

        // Temukan Pinjaman yang terkait
        $pinjaman = DB::table('pinjaman')->where('id', $angsuran->id_pinjaman)->first();

        if (!$pinjaman) {
            return redirect()->back()->with('error', 'Pinjaman tidak ditemukan.');
        }

        // Hapus file bukti pembayaran jika ada
        if ($angsuran->bukti_pembayaran && file_exists(public_path($angsuran->bukti_pembayaran))) {
            unlink(public_path($angsuran->bukti_pembayaran));
        }

        // Hitung sisa pinjaman yang baru
        $sisaPinjamBaru = $pinjaman->sisa_pinjam + $angsuran->jml_angsuran;

        // Update pinjaman
        DB::table('pinjaman')->where('id', $pinjaman->id)->update([
            'sisa_pinjam' => $sisaPinjamBaru,
            'status_pengajuan' => ($sisaPinjamBaru > 0) ? 1 : 3,
            'updated_at' => now(),
        ]);

        // Hapus angsuran dari database
        DB::table('angsuran')->where('id', $id)->delete();

        return redirect()->route('pinjaman.show', ['id' => $pinjaman->id])
            ->with('success', 'Angsuran berhasil dihapus.');
    }
}
