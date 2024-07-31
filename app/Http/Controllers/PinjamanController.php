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


        $pinjaman = $pinjamanQuery->orderBy('pinjaman.id', 'DESC')->paginate(5);
        // Calculate total saldo and maxPinjamanBaru
        $totalSaldo = DB::table('_anggota')->sum('saldo');
        $maxPinjaman = $totalSaldo * 1;
        $totalPinjamanSebelumnya = DB::table('pinjaman')->sum('jml_pinjam');
        $maxPinjamanBaru = $maxPinjaman - $totalPinjamanSebelumnya;

        // Generate a unique transaction code
        $kodeTransaksiPinjaman = $this->generateKodeTransaksiPinjaman();

        return view('backend.pinjaman.index', compact('pinjaman', 'maxPinjamanBaru', 'anggota', 'kodeTransaksiPinjaman'));
    }



    public function create()
    {
        $users = DB::table('users')->get();
        $anggota = DB::table('_anggota')->get();

        // Generate a unique transaction code
        $kodeTransaksiPinjaman = $this->generateKodeTransaksiPinjaman();

        // Menghitung total saldo dari semua anggota
        $totalSaldo = DB::table('_anggota')->sum('saldo');
        $maxPinjaman = $totalSaldo * 0.9;

        // Menghitung jumlah pinjaman yang sudah ada sebelumnya
        $totalPinjamanSebelumnya = DB::table('pinjaman')->sum('jml_pinjam');

        // Menghitung batas maksimal pinjaman baru yang bisa diajukan
        $maxPinjamanBaru = $maxPinjaman - $totalPinjamanSebelumnya;

        return view('backend.pinjaman.create', compact('users', 'anggota', 'kodeTransaksiPinjaman', 'maxPinjamanBaru'));
    }


    private function generateKodeTransaksiPinjaman()
    {
        $lastTransaction = DB::table('pinjaman')->orderBy('id', 'desc')->first();
        $lastId = $lastTransaction ? $lastTransaction->id + 1 : 1;

        return 'PNJ-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pinjam' => 'required|date',
            'jml_pinjam' => 'required|numeric',
            'jml_cicilan' => 'required|numeric',
            'id_anggota' => 'required|exists:_anggota,id',
            'bunga_pinjam' => 'required|numeric|min:0|max:100',
        ], [
            'tanggal_pinjam.required' => 'Tanggal Pinjam harus diisi.',
            'tanggal_pinjam.date' => 'Tanggal Pinjam harus berupa tanggal yang valid.',
            'jml_pinjam.required' => 'Jumlah Pinjam harus diisi.',
            'jml_pinjam.numeric' => 'Jumlah Pinjam harus berupa angka.',
            'jml_cicilan.required' => 'Jumlah Cicilan harus diisi.',
            'jml_cicilan.numeric' => 'Jumlah Cicilan harus berupa angka.',
            'id_anggota.required' => 'Anggota harus dipilih.',
            'id_anggota.exists' => 'Anggota yang dipilih tidak valid.',
            'bunga_pinjam.required' => 'Bunga Pinjam harus diisi.',
            'bunga_pinjam.numeric' => 'Bunga Pinjam harus berupa angka.',
            'bunga_pinjam.min' => 'Bunga Pinjam harus lebih besar atau sama dengan 0.',
            'bunga_pinjam.max' => 'Bunga Pinjam harus lebih kecil atau sama dengan 100.',
        ]);

        // Cek apakah ada pengajuan dengan status selain 3 untuk anggota tersebut
        $pendingPengajuan = DB::table('pinjaman')
        ->where('id_anggota', $request->id_anggota)
            ->where('status_pengajuan', '<>', 3)
            ->exists();

        if ($pendingPengajuan) {
            return redirect()->route('pinjaman')->with('error', 'Anda tidak dapat membuat pinjaman baru karena ada pinjaman yang belum selesai.');
        }

        $totalSaldo = DB::table('_anggota')->sum('saldo');
        $maxPinjaman = $totalSaldo * 0.9;
        $totalPinjamanSebelumnya = DB::table('pinjaman')->sum('jml_pinjam');
        $maxPinjamanBaru = $maxPinjaman - $totalPinjamanSebelumnya;

        if ($request->jml_pinjam > $maxPinjamanBaru) {
            return redirect()->route('pinjaman')->with('error', 'Jumlah pinjaman melebihi batas maksimum');
        }

        $lastTransaction = DB::table('pinjaman')->orderBy('id', 'desc')->first();
        $newTransactionNumber = $lastTransaction ? (int) substr($lastTransaction->kodeTransaksiPinjaman, 4) + 1 : 1;
        $kodeTransaksiPinjaman = 'PNJ-' . str_pad($newTransactionNumber, 4, '0', STR_PAD_LEFT);

        // Hitung jatuh tempo
        $tanggalPinjam = new \DateTime($request->tanggal_pinjam);
        $jatuhTempo = $tanggalPinjam->add(new \DateInterval('P' . $request->jml_cicilan . 'M'))->format('Y-m-d');

        DB::table('pinjaman')->insert([
            'kodeTransaksiPinjaman' => $kodeTransaksiPinjaman,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'jatuh_tempo' => $jatuhTempo,
            'jml_pinjam' => $request->jml_pinjam,
            'bunga_pinjam' => $request->bunga_pinjam,
            'jml_cicilan' => $request->jml_cicilan,
            'status_pengajuan' => 0,
            'keterangan_ditolak_pengajuan' => '',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'id_anggota' => $request->id_anggota,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('pinjaman')->with('success', 'Pinjaman berhasil ditambahkan.');
    }


    public function update(Request $request, $id)
    {
        // Validasi data input
        $request->validate([
            'jml_pinjam' => 'required|numeric',
            'jml_cicilan' => 'required|numeric',
            'jatuh_tempo' => 'required|date'
        ]);
        // Fetch the current pinjaman record to check status_pengajuan
        $pinjaman = DB::table('pinjaman')->where('id', $id)->first();
        if ($pinjaman && $pinjaman->status_pengajuan != 0) {
            return redirect()->route('pinjaman')->with('error', 'tidak bisa update pinjaman karena status tidak valid');
        }
        // Update data pinjaman menggunakan Query Builder
        DB::table('pinjaman')
            ->where('id', $id)
            ->update([
                'jml_pinjam' => $request->input('jml_pinjam'),
                'jml_cicilan' => $request->input('jml_cicilan'),
                'jatuh_tempo' => $request->input('jatuh_tempo'),
                'updated_at' => now(),
                'updated_by' => auth()->id(),
            ]);

        // Redirect ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('pinjaman')->with('success', 'Pinjaman updated successfully.');
    }

    public function terimapengajuan($id)
    {
        DB::table('pinjaman')->where('id', $id)->update([
            'status_pengajuan' => 1
        ]);

        return redirect()->route('pinjaman.show', $id)->with('success', 'Pengajuan Berhasil Diajukan');
    }

    public function tolakpengajuan(Request $request, $id)
    {
        $penolakanAP = DB::table('pinjaman')->select('keterangan_ditolak_pengajuan')->where('id', $id)->first();

        if (!empty($penolakanAP->keterangan_ditolak_pengajuan)) {
            $existingCatatan = json_decode($penolakanAP->keterangan_ditolak_pengajuan, true);
        } else {
            $existingCatatan = [];
        }

        $newCatatan = $request->catatan;

        // Tambahkan data baru ke dalam array yang ada
        $existingCatatan[] = $newCatatan;

        // Konversi array ke format JSON sebelum memperbarui database
        $mergedCatatan = json_encode($existingCatatan);

        DB::table('pinjaman')->where('id', $id)->update([
            'status_pengajuan' => 2,
            'keterangan_ditolak_pengajuan' => $mergedCatatan,
        ]);

        return redirect()->route('pinjaman.show', $id)->with('success', 'Pengajuan Berhasil Ditolak');
    }

    public function show($pinjaman_id)
    {
        // Ambil data pinjaman berdasarkan $pinjaman_id menggunakan Query Builder
        $pinjaman = DB::table('pinjaman')
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
            ->where('pinjaman.id', $pinjaman_id)
            ->first();

        if (!$pinjaman) {
            abort(404, 'Pinjaman tidak ditemukan.');
        }

        // Hitung total pinjaman yang termasuk bunga
        $bunga_persen = $pinjaman->bunga_pinjam;
        $bunga_total = ($pinjaman->jml_pinjam * $bunga_persen) / 100;
        $total_pinjaman_dengan_bunga = $pinjaman->jml_pinjam + $bunga_total;

        // Tambahkan properti total_pinjaman_dengan_bunga ke objek $pinjaman
        $pinjaman->total_pinjaman_dengan_bunga = $total_pinjaman_dengan_bunga;

        // Ambil daftar angsuran terkait pinjaman menggunakan Query Builder
        $angsuran = DB::table('angsuran')
        ->select(
            'angsuran.id as angsuran_id',
            'angsuran.kodeTransaksiAngsuran',
            'angsuran.tanggal_angsuran',
            'angsuran.jml_angsuran',
            'angsuran.sisa_pinjam as sisa_angsuran',
            'angsuran.cicilan',
            'angsuran.status',
            'angsuran.denda',
            'angsuran.keterangan',
            'angsuran.bukti_pembayaran',
            'angsuran.bunga_pinjaman',
            DB::raw('(angsuran.jml_angsuran + angsuran.bunga_pinjaman + COALESCE(angsuran.denda, 0)) as total_angsuran_dengan_bunga'), // Pastikan denda tidak null
            'users.name as created_by_name'
        )
            ->join('users', 'users.id', '=', 'angsuran.created_by')
            ->where('angsuran.id_pinjaman', $pinjaman_id)
            ->orderBy('angsuran.tanggal_angsuran', 'asc')
            ->paginate(5); // Misalnya 5 data per halaman

        // Hitung total angsuran
        $total_angsuran = DB::table('angsuran')
        ->where('angsuran.id_pinjaman', $pinjaman_id)
            ->sum(DB::raw('angsuran.jml_angsuran + angsuran.bunga_pinjaman + COALESCE(angsuran.denda, 0)'));

        return view('backend.pinjaman.show', [
            'pinjaman' => $pinjaman,
            'angsuran' => $angsuran,
            'total_angsuran' => $total_angsuran,
        ]);
    }

    public function destroy($id)
    {
        // Ambil data pinjaman
        $pinjaman = DB::table('pinjaman')->where('id', $id)->first();

        if (!$pinjaman) {
            return redirect()->route('pinjaman')->with('error', 'Pinjaman tidak ditemukan.');
        }

        // Hapus file bukti pembayaran pinjaman jika ada
        if (isset($pinjaman->bukti_pembayaran) && $pinjaman->bukti_pembayaran) {
            $filePath = public_path($pinjaman->bukti_pembayaran);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Ambil semua angsuran yang terkait dengan pinjaman
        $angsuran = DB::table('angsuran')->where('id_pinjaman', $id)->get();

        // Hapus file bukti pembayaran angsuran jika ada
        foreach ($angsuran as $ang) {
            if (isset($ang->bukti_pembayaran) && $ang->bukti_pembayaran) {
                $filePath = public_path($ang->bukti_pembayaran);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        // Hapus data angsuran yang terkait dengan pinjaman
        DB::table('angsuran')->where('id_pinjaman', $id)->delete();

        // Hapus data pinjaman dari database
        DB::table('pinjaman')->where('id', $id)->delete();

        return redirect()->route('pinjaman')->with('success', 'Pinjaman berhasil dihapus.');
    }
}
