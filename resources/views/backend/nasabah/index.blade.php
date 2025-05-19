@extends('backend.app')
@section('title', 'Data Nasabah')
@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Data Nasabah</h2>

    @if(session('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="bg-light rounded h-100 p-4">
        {{-- Tombol Tambah --}}
        <div class="mb-3">
            <a href="{{ route('nasabah.create') }}" class="btn btn-outline-primary rounded-pill">
                <i class="fas fa-user-plus"></i> Tambah Nasabah
            </a>
        </div>

        {{-- Tabel Nasabah --}}
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIP</th>
                        <th>Jenis Kelamin</th>
                        <th>Pekerjaan</th>
                        <th>Saldo</th>
                        <th>Status</th>
                        <th>Terakhir Diupdate</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nasabah as $i => $item)
                        <tr>
                            <td>{{ $nasabah->firstItem() + $i }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->user ? $item->user->email : '-' }}</td>
                            <td>{{ $item->nip }}</td>
                            <td>
                                @if($item->jenis_kelamin == 'L')
                                    Laki-Laki
                                @elseif($item->jenis_kelamin == 'P')
                                    Perempuan
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->pekerjaan }}</td>
                            <td>{{ format_rupiah($item->saldo) }}</td>
                            <td>
                                <span class="badge bg-{{ $item->status_anggota == 1 ? 'success' : 'danger' }}">
                                    {{ $item->status_anggota == 1 ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td>
                                {{ $item->updated_at ? $item->updated_at->format('d-m-Y H:i') : '-' }}
                            </td>
                            <td>
                                <a href="{{ route('nasabah.show', $item->id) }}" class="btn btn-info btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('nasabah.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('nasabah.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Belum ada data nasabah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="float-end">
                {{ $nasabah->links() }}
            </div>
        </div>
    </div>
</div>
<script>
    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach(a => new bootstrap.Alert(a).close());
    }, 5000);
</script>
@endsection
