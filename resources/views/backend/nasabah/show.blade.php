@extends('backend.app')

@section('title', 'Detail Anggota')

@section('content')
<div class="container-fluid pt-4 px-4">
    @if(Session::has('error'))
    <div id="errorAlert" class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-times-circle"></i> Error!</h5>
        {{ Session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <h2 class="mb-4">Detail Anggota</h2>
    <div class="bg-light rounded h-100 p-4">

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="{{  asset('assets/backend/img/' . $anggota->image)}}" class="card-img-top" alt="Foto Anggota">

                </div>
            </div>

            <div class="col-md-8">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $anggota->name }}</td>
                        </tr>
                        <tr>
                            <th>NIP</th>
                            <td>{{ $anggota->nip }}</td>
                        </tr>
                        <tr>
                            <th>Agama</th>
                            <td>{{ $anggota->agama }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>{{ $anggota->jenis_kelamin }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Lahir</th>
                            <td>{{ $anggota->tgl_lahir }}</td>
                        </tr>
                        <tr>
                            <th>Pekerjaan</th>
                            <td>{{ $anggota->pekerjaan }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $anggota->alamat }}</td>
                        </tr>
                        <tr>
                            <th>Status Anggota</th>
                            <td>{{ $anggota->status_anggota ? 'Aktif' : 'Non Aktif' }}</td>
                        </tr>
                        <tr>
                            <th>Saldo</th>
                            <td>Rp {{ number_format($anggota->saldo, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Gabung</th>
                            <td>{{ $anggota->tgl_gabung }}</td>
                        </tr>
                    </tbody>
                </table>
                <a href="{{ route('nasabah') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection