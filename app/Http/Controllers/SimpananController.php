<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpananRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
// use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class SimpananController extends Controller
{
    // public function index()
    // {
    //     $simpanan = DB::table('simpanan')->select(
    //         'simpanan.*',
    //         '_anggota.name as nama_anggota'
    //     )
    //         ->orderBy('_anggota.id', 'DESC')
    //         ->join('simpanan', 'simpanan.id', '_anggota.nama_anggota')
    //         ->paginate(5);

    //     return view('backend.simpanan.index', compact('simpanan'));
    // }

    public function index(Request $request)
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
        $namaNasabah = DB::table('_anggota')->select('id', 'name')->get();
        $jenisSimpanan = DB::table('jenis_simpanan')->select('id', 'nama')->get();

        // Mendapatkan nomor transaksi terakhir
        $lastTransaction = DB::table('simpanan')
        ->where('kodeTransaksiSimpanan', 'LIKE', 'SMP-%')
            ->orderBy('kodeTransaksiSimpanan', 'desc')
            ->first();

        // Menentukan nomor urut simpanan baru
        $newTransactionNumber = $lastTransaction ? (int) substr($lastTransaction->kodeTransaksiSimpanan, 4) + 1 : 1;

        // Membuat kode simpanan baru
        $kodeTransaksiSimpanan = 'SMP-' . str_pad($newTransactionNumber, 4, '0', STR_PAD_LEFT);

        return view('backend.simpanan.index', compact('simpanan', 'startDate', 'endDate', 'search','namaNasabah', 'kodeTransaksiSimpanan', 'jenisSimpanan'));
    }


    public function create()
    {
        $namaNasabah = DB::table('_anggota')->select('id', 'name')->get();
        $jenisSimpanan = DB::table('jenis_simpanan')->select('id', 'nama')->get();

        // Mendapatkan nomor transaksi terakhir
        $lastTransaction = DB::table('simpanan')
            ->where('kodeTransaksiSimpanan', 'LIKE', 'SMP-%')
            ->orderBy('kodeTransaksiSimpanan', 'desc')
            ->first();

        // Menentukan nomor urut simpanan baru
        $newTransactionNumber = $lastTransaction ? (int) substr($lastTransaction->kodeTransaksiSimpanan, 4) + 1 : 1;

        // Membuat kode simpanan baru
        $kodeTransaksiSimpanan = 'SMP-' . str_pad($newTransactionNumber, 4, '0', STR_PAD_LEFT);

        return view('backend.simpanan.create', compact('namaNasabah', 'kodeTransaksiSimpanan', 'jenisSimpanan'));
    }

    public function store(SimpananRequest $request)
    {
        try {
            // Cek jenis simpanan yang dipilih
            $jenisSimpanan = DB::table('jenis_simpanan')
                ->where('id', $request->id_jenis_simpanan)
                ->first();

            // Validasi jika jenis simpanan tidak ditemukan
            if (!$jenisSimpanan) {
                return redirect()->back()->withErrors(['id_jenis_simpanan' => 'Jenis simpanan tidak valid.']);
            }

            // Inisialisasi variabel jumlah simpanan
            $jml_simpanan = $request->jml_simpanan;

            // Jika jenis simpanan adalah simpanan pokok (ID 1)
            if ($jenisSimpanan->id == 1) {
                // Cek apakah anggota sudah memiliki simpanan pokok
                $existingSimpananPokok = DB::table('simpanan')
                ->where('id_anggota', $request->id_anggota)
                ->where('id_jenis_simpanan', 1) // ID 1 adalah simpanan pokok
                    ->exists();

                if ($existingSimpananPokok) {
                    return redirect()->back()->withErrors(['id_jenis_simpanan' => 'Anggota sudah memiliki simpanan pokok.']);
                }

                // Set nominal simpanan pokok menjadi 250.000
                $jml_simpanan = 250000;
            }
            // Jika jenis simpanan adalah simpanan wajib (ID 2)
            elseif ($jenisSimpanan->id == 2) {
                // Cek apakah anggota sudah memiliki simpanan wajib
                $existingSimpananWajib = DB::table('simpanan')
                ->where('id_anggota', $request->id_anggota)
                ->where('id_jenis_simpanan', 2) // ID 2 adalah simpanan wajib
                    ->exists();

                if (!$existingSimpananWajib) {
                    // Simpan simpanan wajib hanya jika belum ada
                    $jml_simpanan = 20000;

                    // Upload bukti pembayaran
                    $image = $request->file('bukti_pembayaran');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('assets/img'), $imageName);

                    // Simpan data simpanan dalam transaksi
                    DB::transaction(function () use ($request, $imageName, $jml_simpanan) {
                        // Insert simpanan wajib
                        DB::table('simpanan')->insert([
                            'kodeTransaksiSimpanan' => $request->kodeTransaksiSimpanan,
                            'tanggal_simpanan' => $request->tanggal_simpanan,
                            'id_anggota' => $request->id_anggota,
                            'id_jenis_simpanan' => 2, // ID 2 adalah simpanan wajib
                            'jml_simpanan' => $jml_simpanan,
                            'bukti_pembayaran' => $imageName,
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Hitung total simpanan anggota
                        $totalSimpanan = DB::table('simpanan')
                            ->where('id_anggota', $request->id_anggota)
                            ->sum('jml_simpanan');

                        // Update saldo anggota dengan total simpanan dan ubah status anggota jika saldo > 0
                        DB::table('_anggota')
                            ->where('id', $request->id_anggota)
                            ->update([
                                'saldo' => $totalSimpanan,
                                'status_anggota' => $totalSimpanan > 0 ? 1 : 0,
                            ]);

                        // Update atau insert ke tabel total_saldo_anggota
                        DB::table('total_saldo_anggota')->updateOrInsert(
                            [],
                            ['gradesaldo' => $totalSimpanan, 'updated_at' => now()]
                        );
                    });
                }
            }

            // Upload bukti pembayaran
            $image = $request->file('bukti_pembayaran');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img'), $imageName);

            // Simpan data simpanan dalam transaksi
            DB::transaction(function () use ($request, $imageName, $jml_simpanan) {
                // Insert simpanan
                DB::table('simpanan')->insert([
                    'kodeTransaksiSimpanan' => $request->kodeTransaksiSimpanan,
                    'tanggal_simpanan' => $request->tanggal_simpanan,
                    'id_anggota' => $request->id_anggota,
                    'id_jenis_simpanan' => $request->id_jenis_simpanan,
                    'jml_simpanan' => $jml_simpanan,
                    'bukti_pembayaran' => 'assets/img/' . $imageName,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Hitung total simpanan anggota
                $totalSimpanan = DB::table('simpanan')
                ->where('id_anggota', $request->id_anggota)
                    ->sum('jml_simpanan');

                // Update saldo anggota dengan total simpanan dan ubah status anggota jika saldo > 0
                DB::table('_anggota')
                    ->where('id', $request->id_anggota)
                    ->update([
                        'saldo' => $totalSimpanan,
                        'status_anggota' => $totalSimpanan > 0 ? 1 : 0,
                    ]);
            });

            return redirect()->route('simpanan')->with('message', 'Data Simpanan Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('simpanan')->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withErrors($e->getMessage());
        }
    }


    public function show($id)
    {
        // dd($id); 
        $detailSimpanan = DB::table('simpanan')
            ->select(
                'simpanan.jml_simpanan as jmlh',
                'simpanan.kodeTransaksiSimpanan as kode',
                'simpanan.tanggal_simpanan as tgl',
                'simpanan.bukti_pembayaran as bukti',
                'users_created.name as created_by',
                'users_updated.name as updated_by',
                '_anggota.name as anggota_name',
                '_anggota.nip as anggota_nip',
                '_anggota.image as anggota_image',
                '_anggota.telphone as anggota_telphone',
                '_anggota.alamat as anggota_alamat',
                '_anggota.pekerjaan as anggota_pekerjaan',
                '_anggota.agama as anggota_agama',
                'jenis_simpanan.nama as jenis_simpanan_nama'
            )
            ->join('_anggota', '_anggota.id', '=', 'simpanan.id_anggota')
            ->join('jenis_simpanan', 'jenis_simpanan.id', '=', 'simpanan.id_jenis_simpanan')
            ->join('users as users_created', 'users_created.id', '=', 'simpanan.created_by')
            ->leftJoin('users as users_updated', 'users_updated.id', '=', 'simpanan.updated_by')
            ->where('simpanan.id', $id)
            ->first();

        // dd($detailSimpanan);

        return view('backend.simpanan.show', compact('detailSimpanan'));
    }


    public function edit($id)
    {
        $simpanedit = DB::table('simpanan')->where('id', $id)->first();
        if (!$simpanedit) {
            return redirect()->route('simpanan')->with('error', 'Barang tidak ditemukan.');
        }
        $namaNasabah = DB::table('_anggota')->get();

        $jenisSimpanan = DB::table('jenis_simpanan')->get();
        session(['simpanan.edit' => $simpanedit]);
        return view('backend.simpanan.edit', compact('simpanedit', 'namaNasabah', 'jenisSimpanan'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'id_anggota' => 'required|exists:_anggota,id',
            'id_jenis_simpanan' => 'required|exists:jenis_simpanan,id',
            'jml_simpanan' => 'required|numeric',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Data untuk diperbarui
        $data = [
            'id_anggota' => $request->id_anggota,
            'id_jenis_simpanan' => $request->id_jenis_simpanan,
            'jml_simpanan' => $request->jml_simpanan,
            'updated_by' => auth()->user()->id,
            'updated_at' => now(),
        ];

        // Cek jika ada file bukti pembayaran yang diunggah
        if ($request->hasFile('bukti_pembayaran')) {
            // Ambil data simpanan lama
            $oldData = DB::table('simpanan')->where('id', $id)->first();

            // Hapus file lama jika ada
            if ($oldData && $oldData->bukti_pembayaran) {
                $oldFilePath = public_path($oldData->bukti_pembayaran);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Simpan file baru
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img/'), $filename);
            $data['bukti_pembayaran'] = 'assets/img/' . $filename;
        }

        // Update data di database
        DB::table('simpanan')->where('id', $id)->update($data);

        // Hitung total simpanan anggota
        $totalSimpanan = DB::table('simpanan')
            ->where('id_anggota', $request->id_anggota)
            ->sum('jml_simpanan');

        // Update saldo anggota dengan total simpanan
        DB::table('_anggota')
            ->where('id', $request->id_anggota)
            ->update(['saldo' => $totalSimpanan]);

        // Redirect ke halaman simpanan dengan pesan sukses
        return redirect()->route('simpanan')->with('message', 'Simpanan updated successfully.');
    }


    public function destroy($id)
    {
        // Ambil data simpanan
        $simpanan = DB::table('simpanan')->where('id', $id)->first();

        if (!$simpanan) {
            return redirect()->route('simpanan')->with('error', 'Simpanan not found.');
        }

        // Hapus file bukti pembayaran jika ada
        if ($simpanan->bukti_pembayaran) {
            $filePath = public_path($simpanan->bukti_pembayaran);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus data simpanan dari database
        DB::table('simpanan')->where('id', $id)->delete();

        // Hitung ulang total simpanan anggota
        $totalSimpanan = DB::table('simpanan')
            ->where('id_anggota', $simpanan->id_anggota)
            ->sum('jml_simpanan');

        // Update saldo anggota dengan total simpanan
        DB::table('_anggota')
            ->where('id', $simpanan->id_anggota)
            ->update(['saldo' => $totalSimpanan]);

        return redirect()->route('simpanan')->with('message', 'Simpanan deleted successfully.');
    }

    public function cetak(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

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

        $simpanan = $query->orderBy('simpanan.id', 'DESC')->get();

        $pdf = PDF::loadView('backend.laporan.simpanan', compact('simpanan', 'startDate', 'endDate'));

        return $pdf->download('laporan_simpanan.pdf');
    }
}
