@extends('backend.app')
@section('title', 'Detail Angsuran')
@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Detail Angsuran</h2>

    @if(Session::has('success'))
    <div class="alert alert-success">
        {{ Session('success') }}
    </div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">
        {{ Session('error') }}
    </div>
    @endif

    <div class="bg-light rounded h-100 p-4">
        <h5>Informasi Angsuran</h5>
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Kode Angsuran</th>
                            <td>{{ $angsuran->kodeTransaksiAngsuran }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Angsuran</th>
                            <td>{{ $angsuran->tanggal_angsuran }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Angsuran</th>
                            <td>Rp {{ number_format($angsuran->jml_angsuran, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Sisa Pinjaman</th>
                            <td>Rp {{ number_format($angsuran->sisa_pinjam, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Cicilan Ke</th>
                            <td>{{ $angsuran->cicilan }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $angsuran->status }}</td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $angsuran->keterangan }}</td>
                        </tr>
                        <tr>
                            <th>Bunga Pinjaman</th>
                            <td>{{ $angsuran->bunga_pinjaman }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Oleh</th>
                            <td>{{ $angsuran->created_by_name }}</td>
                        </tr>
                    </tbody>
                </table>
                @if($angsuran->bukti_pembayaran)
                <img src="{{ asset('assets/img/' . $angsuran->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="img-fluid">
                @endif
            </div>
        </div>
        <a href="{{ route('angsuran.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</div>
@endsection