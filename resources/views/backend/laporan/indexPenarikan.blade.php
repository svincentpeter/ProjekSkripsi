@extends('backend.app')

@section('title', 'Pinjaman')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Laporan Penarikan</h2>
    <div class="bg-light rounded h-100 p-4">
        <div class="table-responsive">
            <div class="mb-3 d-flex justify-content-between">
                <!-- Form Laporan Tanggal -->
                <div class="d-flex align-items-center ms-2">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('laporanPenarikan') }}" method="GET" class="d-flex align-items-center">
                        <input type="date" name="start_date" class="form-control me-2" value="{{ request()->get('start_date') }}" onchange="document.getElementById('reportForm').submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2" value="{{ request()->get('end_date') }}" onchange="document.getElementById('reportForm').submit()">
                    </form>
                    <a href="{{ route('penarikan.cetak', ['start_date' => request()->get('start_date'), 'end_date' => request()->get('end_date')]) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                </div>

                <!-- Form Pencarian -->
                <div class="d-flex align-items-center mr-2">
                    <form id="searchForm" action="{{ route('laporanPenarikan') }}" method="GET" class="input-group">
                        <div class="form-outline" data-mdb-input-init>
                            <input id="search-focus" type="search" name="search" id="form1" class="form-control" placeholder="Search" value="{{ request()->get('search') }}" />
                        </div>
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Kode Penarikan</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Nasabah</th>
                        <th scope="col">Jumlah Penarikan</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Pengelola</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penarikan as $tarik)
                    <tr>
                        <td>{{ $tarik->kodeTransaksiPenarikan }}</td>
                        <td>{{ tanggal_indonesia($tarik->tanggal_penarikan) }}</td>
                        <td>{{ $tarik->name }}</td>
                        <td>Rp {{ number_format($tarik->jumlah_penarikan, 2, ',', '.') }}</td>
                        <td>{{ $tarik->keterangan }}</td>
                        <td>{{ $tarik->created_by_name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($penarikan->isEmpty())
            <p class="text-center">Tidak Ada Transaksi penarikan</p>
            @endif

            <div class="float-right">
                {{ $penarikan->links() }}
            </div>
        </div>
    </div>
</div>


@endsection