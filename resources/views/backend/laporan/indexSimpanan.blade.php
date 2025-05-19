@extends('backend.app')

@section('title', 'Laporan Simpanan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Laporan Simpanan</h2>
    <div class="bg-light rounded h-100 p-4">
        <div class="table-responsive">
            <div class="mb-3 d-flex justify-content-between">
                {{-- Filter Tanggal --}}
                <div class="d-flex align-items-center">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('laporanSimpanan') }}" method="GET" class="d-flex">
                        <input type="date" name="start_date" class="form-control me-2"
                               value="{{ request('start_date') }}"
                               onchange="this.form.submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2"
                               value="{{ request('end_date') }}"
                               onchange="this.form.submit()">
                    </form>
                    <a href="{{ route('simpanan.cetak', request()->only(['start_date','end_date'])) }}"
                       class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                </div>

                {{-- Pencarian --}}
                <div class="d-flex align-items-center">
                    <form action="{{ route('laporanSimpanan') }}" method="GET" class="input-group">
                        <input type="search" name="search" class="form-control"
                               placeholder="Cari Kode / Nama" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Nasabah</th>
                        <th>Transaksi</th>
                        <th>Jenis Simpanan</th>
                        <th>Pengelola</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rowNumber = ($simpanan->currentPage() - 1) * $simpanan->perPage() + 1;
                    @endphp

                    @forelse($simpanan as $tabungan)
                    <tr>
                        <td>{{ $rowNumber++ }}</td>
                        <td>{{ $tabungan->kode_transaksi }}</td>
                        <td>{{ tanggal_indonesia($tabungan->tanggal_simpanan, false) }}</td>
                        <td>{{ $tabungan->anggota_name }}</td>
                        <td>Rp {{ number_format($tabungan->jumlah_simpanan, 2, ',', '.') }}</td>
                        <td>{{ $tabungan->jenis_simpanan_nama }}</td>
                        <td>{{ $tabungan->created_by_name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak Ada Transaksi Simpanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="float-end">
                {{ $simpanan->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
