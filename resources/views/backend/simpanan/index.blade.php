@extends('backend.app')
@section('title', 'Simpanan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Data Simpanan</h2>

    {{-- Notifikasi --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('message'))
    <div id="successAlert" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div id="errorAlert" class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
        <i class="fas fa-times-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="bg-light rounded h-100 p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            {{-- Tambah --}}
            @can('simpanan-create')
            <a href="{{ route('simpanan.create') }}" class="btn btn-outline-primary rounded-pill">
                <i class="fas fa-plus-circle"></i> Tambah Simpanan
            </a>
            {{-- Atau modal --}}
            {{-- <button ... > --}}
            @endcan

            {{-- Report --}}
            <form id="reportForm"
                action="{{ route('laporanSimpanan') }}"
                method="GET"
                class="d-flex align-items-center gap-2">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                <span>s/d</span>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                @can('laporan_simpanan')
                <a href="{{ route('simpanan.cetak', ['start_date'=>request('start_date'),'end_date'=>request('end_date')]) }}"
                   class="btn btn-outline-success">
                    <i class="fas fa-print"></i> Cetak PDF
                </a>
                @endcan
            </form>

            {{-- Search --}}
            <form action="{{ route('simpanan.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <input type="search" name="search" class="form-control" placeholder="Cari Kode/Nama" value="{{ request('search') }}">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        {{-- Modal Create jika pakai modal --}}
        @include('backend.simpanan.modal.modalCreate')

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Anggota</th>
                        <th>Jumlah</th>
                        <th>Jenis</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = ($simpanan->currentPage() - 1) * $simpanan->perPage() + 1; @endphp
                    @forelse($simpanan as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $item->kode_transaksi ?? '-' }}</td>
                        <td>{{ $item->tanggal_simpanan }}</td>
                        <td>{{ $item->anggota_name }}</td>
                        <td>Rp {{ number_format($item->jumlah_simpanan, 2, ',', '.') }}</td>
                        <td>{{ $item->jenis_simpanan_nama }}</td>
                        <td>
                            <a href="{{ route('simpanan.show', $item->simpanan_id) }}" class="btn btn-outline-info btn-sm" title="Detail">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('simpanan.edit', $item->simpanan_id) }}" class="btn btn-outline-warning btn-sm" title="Edit">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('simpanan.destroy', $item->simpanan_id) }}" method="POST" class="d-inline"
          onsubmit="return confirm('Yakin ingin menghapus data ini?');">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Data belum tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{ $simpanan->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => new bootstrap.Alert(a).close());
}, 5000);
</script>
@endsection
