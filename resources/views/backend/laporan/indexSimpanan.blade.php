@extends('backend.app')

@section('title', 'Simpanan')

@section('content')
<div class="container-fluid pt-4 px-4">
    
    <h2 class="mb-4">Laporan Simpanan</h2>

    
    <div class="bg-light rounded h-100 p-4">
        <div class="table-responsive">
            <div class="mb-3 d-flex justify-content-between">
                <!-- Form Laporan Tanggal -->
                <div class="d-flex align-items-center ms-2">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('laporanSimpanan') }}" method="GET" class="d-flex align-items-center">
                        <input type="date" name="start_date" class="form-control me-2" value="{{ request()->get('start_date') }}" onchange="document.getElementById('reportForm').submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2" value="{{ request()->get('end_date') }}" onchange="document.getElementById('reportForm').submit()">

                    </form>
                    <a href="{{ route('simpanan.cetak', ['start_date' => request()->get('start_date'), 'end_date' => request()->get('end_date')]) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                </div>
                <!-- Form Pencarian -->
                <div class="d-flex align-items-center mr-2">
                    <form id="searchForm" action="{{ route('laporanSimpanan') }}" method="GET" class="input-group">
                        <div class="form-outline" data-mdb-input-init>
                            <input id="search-focus" type="search" name="search" id="form1" class="form-control" placeholder="Search" value="{{ request()->get('search') }}" />
                        </div>
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Form Pencarian
                <div class="d-flex align-items-center ms-2">
                    <form id="searchForm" action="{{ route('simpanan') }}" method="GET" class="d-flex">

                        <input type="text" name="search" class="form-control me-2" placeholder="Search" value="{{ request()->get('search') }}" oninput="document.getElementById('searchForm').submit()">


                    </form>
                </div> -->
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Kode Transaksi</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Nasabah</th>
                        <th scope="col">Transaksi</th>
                        <th scope="col">Jenis Simpanan</th>
                        <th scope="col">Pengelola</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $rowNumber = ($simpanan->currentPage() - 1) * $simpanan->perPage() + 1;
                    @endphp

                    @foreach($simpanan as $tabungan)
                    <tr>
                        <td scope="row">{{ $rowNumber++ }}</td>
                        <td>{{ $tabungan->kodeTransaksiSimpanan }}</td>
                        <td>{{ $tabungan->tanggal_simpanan }}</td>
                        <td>{{ $tabungan->anggota_name }}</td>
                        <td>Rp {{ number_format($tabungan->jml_simpanan, 2, ',', '.') }}</td>
                        <td>{{ $tabungan->jenis_simpanan_nama }}</td>
                        <td>{{ $tabungan->created_by_name }}</td>


                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="float-right">
                {{ $simpanan->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>



@endsection