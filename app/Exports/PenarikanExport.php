<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;

class PenarikanExport implements FromView
{
    protected $request;
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $query = DB::table('penarikan')
            ->leftJoin('anggota', 'anggota.id', '=', 'penarikan.anggota_id')
            ->select(
                'penarikan.kode_transaksi',
                'penarikan.tanggal_penarikan',
                'penarikan.jumlah_penarikan',
                'penarikan.keterangan',
                'anggota.name as anggota_name'
            );
        // Filter sesuai index
        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $query->whereBetween('penarikan.tanggal_penarikan', [
                $this->request->start_date,
                $this->request->end_date
            ]);
        }
        if ($this->request->filled('search')) {
            $term = "%{$this->request->search}%";
            $query->where(function($q) use($term) {
                $q->where('penarikan.kode_transaksi', 'like', $term)
                  ->orWhere('anggota.name', 'like', $term);
            });
        }
        $penarikan = $query->orderBy('penarikan.id', 'desc')->get();

        return view('backend.penarikan.excel', compact('penarikan'));
    }
}
