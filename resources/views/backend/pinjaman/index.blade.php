@extends('backend.app')

@section('title', 'Pinjaman')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Data Pinjaman</h2>

    {{-- Alerts --}}
    @if(session('success'))
    <div id="successAlert" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-check-circle"></i> Sukses!</h5>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div id="errorAlert" class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-times-circle"></i> Error!</h5>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="bg-light rounded h-100 p-4">
        <div class="table-responsive">
            <div class="mb-3 d-flex justify-content-between">
                @can('pinjaman-create')
                <button class="btn btn-outline-primary rounded-pill m-3" data-bs-toggle="modal" data-bs-target="#buatPinjaman">
                    <i class="fas fa-plus-circle"></i> Tambah
                </button>
                @endcan

                @include('backend.pinjaman.modal.modalCreate')
                @include('backend.pinjaman.modal.modalEdit')

                {{-- Report Form --}}
                <div class="d-flex align-items-center">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('pinjaman') }}" method="GET" class="d-flex">
                        <input type="date" name="start_date" class="form-control me-2"
                            value="{{ request('start_date') }}"
                            onchange="this.form.submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2"
                            value="{{ request('end_date') }}"
                            onchange="this.form.submit()">
                    </form>
                    @can('laporan_pinjaman')
                    <a href="{{ route('pinjaman.cetak', request()->only('start_date','end_date')) }}"
                        class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                    @endcan
                </div>

                {{-- Search Form --}}
                <div class="d-flex align-items-center">
                    <form action="{{ route('pinjaman') }}" method="GET" class="input-group">
                        <input type="search" name="search" class="form-control"
                            placeholder="Cari kode atau nama"
                            value="{{ request('search') }}">
                        <button class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>

            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Nasabah</th>
                        <th>Jumlah</th>
                        <th>Lama (bln)</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pinjaman as $pinjam)
                    <tr>
                        <td>{{ $pinjam->kode_transaksi }}</td>
                        <td>{{ tanggal_indonesia($pinjam->tanggal_pinjam) }}</td>
                        <td>{{ $pinjam->anggota_name }}</td>
                        <td>Rp {{ number_format($pinjam->jumlah_pinjam,2,',','.') }}</td>
                        <td>{{ $pinjam->tenor }} Bulan</td>
                        <td>
                            @switch($pinjam->status)
                            @case('PENDING')<span class="badge bg-warning">Pending</span>@break
                            @case('DISETUJUI')<span class="badge bg-success">Disetujui</span>@break
                            @case('DITOLAK')<span class="badge bg-danger">Ditolak</span>@break
                            @default<span class="badge bg-secondary">{{ $pinjam->status }}</span>
                            @endswitch
                        </td>
                        <td class="text-nowrap">
                            @can('pinjaman-edit')
                            <button class="btn btn-sm btn-outline-warning"
                                data-bs-toggle="modal" data-bs-target="#editPinjaman"
                                data-id="{{ $pinjam->pinjaman_id }}"
                                data-tanggal_pinjam="{{ $pinjam->tanggal_pinjam }}"
                                data-jumlah_pinjam="{{ $pinjam->jumlah_pinjam }}"
                                data-tenor="{{ $pinjam->tenor }}"
                                data-jatuh_tempo="{{ $pinjam->jatuh_tempo }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            @endcan

                            @can('pinjaman-detail')
                            <a href="{{ route('pinjaman.show', $pinjam->pinjaman_id) }}"
                                class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan

                            @can('laporan_angsuran')
                            <a href="{{ route('laporan.angsuran', $pinjam->pinjaman_id) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-print"></i>
                            </a>
                            @endcan

                            @can('pinjaman-delete')
                            <form action="{{ route('pinjaman.destroy', $pinjam->pinjaman_id) }}"
                                method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin dihapus?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data pinjaman.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $pinjaman->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Auto-dismiss alerts & animate --}}
@push('scripts')
<script>
    setTimeout(() => document.querySelectorAll('.custom-alert')
        .forEach(a => new bootstrap.Alert(a).close()), 5000);

    document.querySelectorAll('.custom-alert').forEach((el, i) =>
        setTimeout(() => $(el).fadeIn('slow'), 300 * i)
    );
</script>
@endpush
@endsection