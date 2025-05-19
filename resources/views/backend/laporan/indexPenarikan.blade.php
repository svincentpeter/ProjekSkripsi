@extends('backend.app')

@section('title', 'Laporan Penarikan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Laporan Penarikan</h2>
    <div class="bg-light rounded h-100 p-4">
        <div class="table-responsive">
            <div class="mb-3 d-flex justify-content-between">
                {{-- Form Filter Tanggal --}}
                <div class="d-flex align-items-center">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('laporanPenarikan') }}" method="GET" class="d-flex">
                        <input type="date" name="start_date" class="form-control me-2"
                               value="{{ request('start_date') }}"
                               onchange="this.form.submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2"
                               value="{{ request('end_date') }}"
                               onchange="this.form.submit()">
                    </form>
                    <a href="{{ route('penarikan.cetak', request()->only(['start_date','end_date'])) }}"
                       class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                </div>

                {{-- Form Pencarian --}}
                <div class="d-flex align-items-center">
                    <form action="{{ route('laporanPenarikan') }}" method="GET" class="input-group">
                        <input type="search" name="search" class="form-control"
                               placeholder="Cari Nasabah / Kode" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Kode Penarikan</th>
                        <th>Tanggal</th>
                        <th>Nasabah</th>
                        <th>Jumlah Penarikan</th>
                        <th>Keterangan</th>
                        <th>Pengelola</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penarikan as $tarik)
                    <tr>
                        <td>{{ $tarik->kode_transaksi }}</td>
                        <td>{{ tanggal_indonesia($tarik->tanggal_penarikan) }}</td>
                        <td>{{ $tarik->anggota_name }}</td>
                        <td>Rp {{ number_format($tarik->jumlah_penarikan, 2, ',', '.') }}</td>
                        <td>{{ $tarik->keterangan }}</td>
                        <td>{{ $tarik->created_by_name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak Ada Transaksi Penarikan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="float-end">
                {{ $penarikan->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
