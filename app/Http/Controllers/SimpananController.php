<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpananRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class SimpananController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $search    = $request->get('search');

        $query = DB::table('simpanan')
            ->select([
                'simpanan.id as simpanan_id',
                'simpanan.kode_transaksi',
                'simpanan.tanggal_simpanan',
                'simpanan.jumlah_simpanan',
                'jenis_simpanan.nama as jenis_simpanan_nama',
                'users.name as created_by_name',
                'anggota.name as anggota_name',
            ])
            ->join('users',          'users.id',             '=', 'simpanan.created_by')
            ->join('anggota',        'anggota.id',           '=', 'simpanan.anggota_id')
            ->join('jenis_simpanan', 'jenis_simpanan.id',    '=', 'simpanan.jenis_simpanan_id');

        if ($startDate && $endDate) {
            $query->whereBetween('simpanan.tanggal_simpanan', [$startDate, $endDate]);
        }

        if ($search) {
            $term = "%{$search}%";
            $query->where(function($q) use($term) {
                $q->where('simpanan.kode_transaksi', 'like', $term)
                  ->orWhere('anggota.name',           'like', $term);
            });
        }

        $simpanan    = $query->orderByDesc('simpanan.id')->paginate(5);
        $anggotaList = DB::table('anggota')->select('id', 'name')->get();
        $jenisList   = DB::table('jenis_simpanan')->select('id', 'nama')->get();

        // Generate kode transaksi otomatis
        $last = DB::table('simpanan')
            ->where('kode_transaksi', 'like', 'SMP-%')
            ->orderByDesc('kode_transaksi')
            ->first();
        $nextNumber    = $last ? ((int) substr($last->kode_transaksi, 4)) + 1 : 1;
        $kodeTransaksi = 'SMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('backend.simpanan.index', compact(
            'simpanan', 'startDate', 'endDate', 'search',
            'anggotaList', 'jenisList', 'kodeTransaksi'
        ));
    }

    public function create()
    {
        $anggotaList = DB::table('anggota')->select('id', 'name')->get();
        $jenisList   = DB::table('jenis_simpanan')->select('id', 'nama')->get();

        // Generate kode transaksi
        $last = DB::table('simpanan')
            ->where('kode_transaksi', 'like', 'SMP-%')
            ->orderByDesc('kode_transaksi')
            ->first();
        $nextNumber    = $last ? ((int) substr($last->kode_transaksi, 4)) + 1 : 1;
        $kodeTransaksi = 'SMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('backend.simpanan.create', compact(
            'anggotaList', 'jenisList', 'kodeTransaksi'
        ));
    }

    // STORE: Perhatikan field pada $request harus sama dengan form name
    public function store(SimpananRequest $request)
    {
        DB::transaction(function() use($request) {
            // Kode transaksi tetap digenerate, walaupun sudah dari form (bisa diambil dari $request->kode_transaksi juga)
            $last = DB::table('simpanan')
                ->where('kode_transaksi', 'like', 'SMP-%')
                ->orderByDesc('kode_transaksi')
                ->first();
            $nextNumber    = $last ? ((int) substr($last->kode_transaksi, 4)) + 1 : 1;
            $kodeTransaksi = 'SMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Upload file bukti pembayaran
            $bukti = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $file   = $request->file('bukti_pembayaran');
                $bukti  = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/img'), $bukti);
            }

            // Insert data
            DB::table('simpanan')->insert([
                'kode_transaksi'      => $kodeTransaksi,
                'tanggal_simpanan'    => $request->tanggal_simpanan,
                'anggota_id'          => $request->anggota_id,
                'jenis_simpanan_id'   => $request->jenis_simpanan_id,
                'jumlah_simpanan'     => $request->jumlah_simpanan,
                'bukti_pembayaran'    => $bukti ? 'assets/img/' . $bukti : null,
                'created_by'          => Auth::id(),
                'updated_by'          => Auth::id(),
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            // Update saldo anggota
            $total = DB::table('simpanan')
                ->where('anggota_id', $request->anggota_id)
                ->sum('jumlah_simpanan');

            DB::table('anggota')
                ->where('id', $request->anggota_id)
                ->update([
                    'saldo'           => $total,
                    'status_anggota'  => $total > 0 ? '1' : '0',
                ]);

            // Update total_saldo_anggota
            DB::table('total_saldo_anggota')->updateOrInsert(
                ['anggota_id' => $request->anggota_id],
                ['gradesaldo'  => $total, 'updated_at' => now()]
            );
        });

        return redirect()->route('simpanan.index')
                         ->with('message', 'Data Simpanan Berhasil Disimpan');
    }

    public function show($id)
    {
        $detail = DB::table('simpanan')
            ->select([
                'simpanan.id',
                'simpanan.kode_transaksi as kode',
                'simpanan.tanggal_simpanan as tgl',
                'simpanan.jumlah_simpanan as jmlh',
                'simpanan.bukti_pembayaran as bukti',
                'users_created.name as created_by',
                'users_updated.name as updated_by',
                'anggota.name as anggota_name',
                'anggota.nip as anggota_nip',
                'anggota.image as anggota_image',
                'anggota.telphone as anggota_telphone',
                'anggota.alamat as anggota_alamat',
                'anggota.pekerjaan as anggota_pekerjaan',
                'anggota.agama as anggota_agama',
                'jenis_simpanan.nama as jenis_simpanan_nama',
            ])
            ->join('anggota',        'anggota.id',         '=', 'simpanan.anggota_id')
            ->join('jenis_simpanan', 'jenis_simpanan.id',  '=', 'simpanan.jenis_simpanan_id')
            ->join('users as users_created', 'users_created.id', '=', 'simpanan.created_by')
            ->leftJoin('users as users_updated', 'users_updated.id', '=', 'simpanan.updated_by')
            ->where('simpanan.id', $id)
            ->first();

        return view('backend.simpanan.show', ['detailSimpanan' => $detail]);
    }

    public function edit($id)
    {
        $item = DB::table('simpanan')->find($id);
        if (!$item) {
            return redirect()->route('simpanan.index')
                             ->with('error', 'Data tidak ditemukan.');
        }
        $anggotaList = DB::table('anggota')->select('id', 'name')->get();
        $jenisList   = DB::table('jenis_simpanan')->select('id', 'nama')->get();

        return view('backend.simpanan.edit', compact('item', 'anggotaList', 'jenisList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'anggota_id'          => 'required|exists:anggota,id',
            'jenis_simpanan_id'   => 'required|exists:jenis_simpanan,id',
            'jumlah_simpanan'     => 'required|numeric',
            'bukti_pembayaran'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = [
            'anggota_id'         => $request->anggota_id,
            'jenis_simpanan_id'  => $request->jenis_simpanan_id,
            'jumlah_simpanan'    => $request->jumlah_simpanan,
            'updated_by'         => Auth::id(),
            'updated_at'         => now(),
        ];

        if ($request->hasFile('bukti_pembayaran')) {
            $old = DB::table('simpanan')->find($id);
            if ($old && $old->bukti_pembayaran && file_exists(public_path($old->bukti_pembayaran))) {
                unlink(public_path($old->bukti_pembayaran));
            }
            $file     = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img'), $filename);
            $data['bukti_pembayaran'] = 'assets/img/' . $filename;
        }

        DB::table('simpanan')->where('id', $id)->update($data);

        // Recalculate saldo
        $total = DB::table('simpanan')
            ->where('anggota_id', $request->anggota_id)
            ->sum('jumlah_simpanan');

        DB::table('anggota')
            ->where('id', $request->anggota_id)
            ->update(['saldo' => $total]);

        return redirect()->route('simpanan.index')
                         ->with('message', 'Data Simpanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = DB::table('simpanan')->find($id);
        if (!$item) {
            return redirect()->route('simpanan.index')
                             ->with('error', 'Data tidak ditemukan.');
        }

        if ($item->bukti_pembayaran && file_exists(public_path($item->bukti_pembayaran))) {
            unlink(public_path($item->bukti_pembayaran));
        }

        DB::table('simpanan')->where('id', $id)->delete();

        // Recalculate saldo
        $total = DB::table('simpanan')
            ->where('anggota_id', $item->anggota_id)
            ->sum('jumlah_simpanan');

        DB::table('anggota')
            ->where('id', $item->anggota_id)
            ->update(['saldo' => $total]);

        return redirect()->route('simpanan.index')
                         ->with('message', 'Data Simpanan berhasil dihapus.');
    }

    public function cetak(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $query = DB::table('simpanan')
            ->select([
                'simpanan.id as simpanan_id',
                'simpanan.kode_transaksi',
                'simpanan.tanggal_simpanan',
                'simpanan.jumlah_simpanan',
                'jenis_simpanan.nama as jenis_simpanan_nama',
                'users.name as created_by_name',
                'anggota.name as anggota_name',
            ])
            ->join('users',          'users.id',           '=', 'simpanan.created_by')
            ->join('anggota',        'anggota.id',         '=', 'simpanan.anggota_id')
            ->join('jenis_simpanan', 'jenis_simpanan.id',  '=', 'simpanan.jenis_simpanan_id');

        if ($startDate && $endDate) {
            $query->whereBetween('simpanan.tanggal_simpanan', [$startDate, $endDate]);
        }

        $data = $query->orderByDesc('simpanan.id')->get();

        $pdf = PDF::loadView('backend.laporan.simpanan', [
            'simpanan'  => $data,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);

        return $pdf->download('laporan_simpanan.pdf');
    }
}
