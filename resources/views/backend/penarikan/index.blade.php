@extends('backend.app')

@section('title', 'Pinjaman')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Data Penarikan</h2>

    <!-- Alert Success -->
    @if(Session::has('success'))
    <div id="successAlert" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-check-circle"></i> Sukses!</h5>
        {{ Session('success') }}
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
                @can('penarikan-create')
                <button type="button" class="btn btn-outline-primary rounded-pill m-3" data-bs-toggle="modal" data-bs-target="#buatPenarikan">
                    <i class="f	fas fa-dollar-sign"></i> Tambah
                </button>
                @endcan
                @include('backend.penarikan.modal.modalCreate')
                @include('backend.penarikan.modal.modalEdit')


                <!-- Form Laporan Tanggal -->
                <div class="d-flex align-items-center ms-2">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('penarikan') }}" method="GET" class="d-flex align-items-center">
                        <input type="date" name="start_date" class="form-control me-2" value="{{ request()->get('start_date') }}" onchange="document.getElementById('reportForm').submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2" value="{{ request()->get('end_date') }}" onchange="document.getElementById('reportForm').submit()">
                    </form>
                    @can('laporan_penarikan')
                    <a href="{{ route('penarikan.cetak', ['start_date' => request()->get('start_date'), 'end_date' => request()->get('end_date')]) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                    @endcan

                </div>

                <!-- Form Pencarian -->
                <div class="d-flex align-items-center mr-2">
                    <form id="searchForm" action="{{ route('penarikan') }}" method="GET" class="input-group">
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
                        @can('penarikan-edit')
                        <th scope="col">Aksi</th>
                        @endcan
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
                        <td>
                            <!-- Edit Button -->
                            @can('penarikan-edit')
                            <button type="button" class="btn btn-outline-warning btn-sm m-1" data-bs-toggle="modal" data-bs-target="#editPenarikan" data-id="{{ $tarik->penarikan_id }}" data-jumlah_penarikan="{{ $tarik->jumlah_penarikan }}" data-keterangan="{{ $tarik->keterangan }}" data-saldo="{{ $tarik->saldo }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            @endcan
                            @can('penarikan-delete')
                            <form action="{{ route('penarikan.destroy', $tarik->penarikan_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm m-1" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
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

<!-- Tambahkan skrip di akhir konten -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const anggotaSelect = document.getElementById('id_anggota');
        const saldoText = document.getElementById('saldoText');

        anggotaSelect.addEventListener('change', function() {
            const selectedOption = anggotaSelect.options[anggotaSelect.selectedIndex];
            const saldo = selectedOption.getAttribute('data-saldo') || 0;
            saldoText.textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(saldo);
        });
    });
</script>
@endsection