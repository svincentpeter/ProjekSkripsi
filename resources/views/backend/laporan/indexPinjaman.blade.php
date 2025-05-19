@extends('backend.app')

@section('title', 'Laporan Pinjaman')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Laporan Pinjaman</h2>
    <div class="bg-light rounded h-100 p-4">
        <div class="table-responsive">
            <div class="mb-3 d-flex justify-content-between">
                {{-- Filter Tanggal --}}
                <div class="d-flex align-items-center">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('laporanPinjaman') }}" method="GET" class="d-flex">
                        <input type="date" name="start_date" class="form-control me-2"
                               value="{{ request('start_date') }}"
                               onchange="this.form.submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2"
                               value="{{ request('end_date') }}"
                               onchange="this.form.submit()">
                    </form>
                    <a href="{{ route('pinjaman.cetak', request()->only(['start_date','end_date'])) }}"
                       class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                </div>

                {{-- Pencarian --}}
                <div class="d-flex align-items-center">
                    <form action="{{ route('laporanPinjaman') }}" method="GET" class="input-group">
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
                        <th>Kode Pinjam</th>
                        <th>Tanggal</th>
                        <th>Nasabah</th>
                        <th>Jumlah Dipinjam</th>
                        <th>Durasi</th>
                        <th>Bunga</th>
                        <th>Status</th>
                        <th>Pengelola</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pinjaman as $pinjam)
                    <tr>
                        <td>{{ $pinjam->kode_transaksi }}</td>
                        <td>{{ tanggal_indonesia($pinjam->tanggal_pinjam, false) }}</td>
                        <td>{{ $pinjam->anggota_name }}</td>
                        <td>Rp {{ number_format($pinjam->jumlah_pinjam, 2, ',', '.') }}</td>
                        <td>{{ $pinjam->tenor }} Bulan</td>
                        <td>{{ $pinjam->bunga }} %</td>
                        <td>
                            @switch($pinjam->status)
                                @case('PENDING')
                                    <span class="text-primary">Dibuat</span>
                                    @break
                                @case('DISETUJUI')
                                    <span class="text-success">Disetujui</span>
                                    @break
                                @case('DITOLAK')
                                    <span class="text-danger">Ditolak</span>
                                    @break
                                @case('SELESAI')
                                    <span class="text-info">Selesai</span>
                                    @break
                                @default
                                    {{ $pinjam->status }}
                            @endswitch
                        </td>
                        <td>{{ $pinjam->created_by_name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak Ada Transaksi Pinjaman</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="float-end">
                {{ $pinjaman->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
