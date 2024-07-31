@extends('backend.app')

@section('title', 'Simpanan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Data Simpanan</h2>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <!-- Alert Success -->
    @if(Session::has('message'))
    <div id="successAlert" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-check-circle"></i> Sukses!</h5>
        {{ Session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Alert Error -->
    @if(Session::has('error'))
    <div id="errorAlert" class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-times-circle"></i> Error!</h5>
        {{ Session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="bg-light rounded h-100 p-4">
        <div class="table-responsive">
            <div class="mb-3 d-flex justify-content-between">
                @can('simpanan-create')
                <button type="button" class="btn btn-outline-primary rounded-pill m-3" data-bs-toggle="modal" data-bs-target="#buatSimpanan">
                    <i class="fas fa-dollar-sign"></i> Tambah
                </button>
                @endcan

                @include('backend.simpanan.modal.modalCreate')


                <!-- <a href="{{ route('simpanan.create') }}" class="btn btn-outline-primary rounded-pill m-3"><i class="fas fa-dollar-sign"></i> Tambah</a> -->

                <!-- Form Laporan Tanggal -->
                <div class="d-flex align-items-center ms-2">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('simpanan') }}" method="GET" class="d-flex align-items-center">
                        <input type="date" name="start_date" class="form-control me-2" value="{{ request()->get('start_date') }}" onchange="document.getElementById('reportForm').submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2" value="{{ request()->get('end_date') }}" onchange="document.getElementById('reportForm').submit()">

                    </form>
                    @can('laporan_simpanan')
                    <a href="{{ route('simpanan.cetak', ['start_date' => request()->get('start_date'), 'end_date' => request()->get('end_date')]) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                    @endcan
                </div>
                <!-- Form Pencarian -->
                <div class="d-flex align-items-center mr-2">
                    <form id="searchForm" action="{{ route('simpanan') }}" method="GET" class="input-group">
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
                        @can('simpanan-edit')
                        <th scope="col">Aksi</th>
                        @endcan
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
                        <td>
                            @can('simpanan-detail')
                            <a href="{{ route('simpanan.show', $tabungan->simpanan_id) }}" class="btn btn-outline-info" title="Show">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan
                            @can('simpanan-edit')
                            <a href="{{ route('simpanan.edit', $tabungan->simpanan_id) }}" class="btn btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            <!-- Form for delete action -->
                            @can('simpanan-delete')
                            <form action="{{ route('simpanan.destroy', $tabungan->simpanan_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
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

<!-- Bootstrap JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script untuk menutup alert secara otomatis -->
<script>
    // Menutup alert secara otomatis setelah 5 detik
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            new bootstrap.Alert(alert).close();
        });
    }, 5000); // 5000 milidetik = 5 detik

    // Membuat animasi alert muncul di depan tabel
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.custom-alert').forEach(function(alert, index) {
            setTimeout(function() {
                alert.style.display = 'block';
            }, 300 * index);
        });
    });
</script>

@endsection