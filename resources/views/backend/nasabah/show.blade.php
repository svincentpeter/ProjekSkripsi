@extends('backend.app')
@section('title', 'Detail Nasabah')
@section('content')
<div class="container-fluid pt-4 px-4">
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <h2 class="mb-4">Detail Nasabah</h2>
    <div class="bg-light rounded h-100 p-4">
        <div class="row">
            {{-- Foto --}}
            <div class="col-md-4">
                <div class="card mb-4">
                    @if($anggota->image)
                    <img src="{{ asset('assets/backend/img/' . $anggota->image) }}"
                         class="card-img-top" alt="Foto Nasabah">
                    @else
                    <div class="card-body text-center">
                        <span class="text-muted">Tidak ada foto</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Data --}}
            <div class="col-md-8">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $anggota->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $anggota->user ? $anggota->user->email : '-' }}</td>
                        </tr>
                        <tr>
                            <th>NIP</th>
                            <td>{{ $anggota->nip }}</td>
                        </tr>
                        <tr>
                            <th>Telepon</th>
                            <td>{{ $anggota->telphone }}</td>
                        </tr>
                        <tr>
                            <th>Agama</th>
                            <td>{{ $anggota->agama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>
                                @if($anggota->jenis_kelamin == 'L')
                                    Laki-Laki
                                @elseif($anggota->jenis_kelamin == 'P')
                                    Perempuan
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Lahir</th>
                            <td>{{ $anggota->tgl_lahir ? \Carbon\Carbon::parse($anggota->tgl_lahir)->format('d-m-Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Pekerjaan</th>
                            <td>{{ $anggota->pekerjaan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $anggota->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status Anggota</th>
                            <td>
                                @if($anggota->status_anggota == 1)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Non-Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Saldo</th>
                            <td>{{ format_rupiah($anggota->saldo) }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $anggota->created_at ? $anggota->created_at->format('d-m-Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate</th>
                            <td>{{ $anggota->updated_at ? $anggota->updated_at->format('d-m-Y H:i') : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
                <a href="{{ route('nasabah.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </div>
        </div>
    </div>
</div>
<script>
    setTimeout(() => {
        document.querySelectorAll('.custom-alert').forEach(a => new bootstrap.Alert(a).close());
    }, 5000);
</script>
@endsection
