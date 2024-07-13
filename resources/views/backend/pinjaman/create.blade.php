@extends('backend.app')
@section('title', 'Tambah Pinjaman')
@section('content')
<div class="container-fluid pt-4 px-4">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <h2 class="mb-4">Tambah Pinjaman</h2>
    <div class="bg-light rounded h-100 p-4">
        <div class="alert alert-info">
            <strong>Informasi:</strong> Jumlah maksimal pinjaman baru adalah Rp {{ number_format($maxPinjamanBaru, 0, ',', '.') }}.
        </div>
        <form method="POST" action="{{ route('pinjaman.store') }}">
            @csrf

          

            <div class="form-group">
                <label for="tanggal_pinjam">Tanggal Pinjam</label>
                <input type="date" class="form-control @error('tanggal_pinjam') is-invalid @enderror" id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam') }}" required>
                @error('tanggal_pinjam')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="jml_pinjam">Jumlah Pinjam</label>
                <input type="number" class="form-control @error('jml_pinjam') is-invalid @enderror" id="jml_pinjam" name="jml_pinjam" value="{{ old('jml_pinjam') }}" required>
                @error('jml_pinjam')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="jml_cicilan">Lama/bulan</label>
                <input type="number" class="form-control @error('jml_cicilan') is-invalid @enderror" id="jml_cicilan" name="jml_cicilan" value="{{ old('jml_cicilan') }}" required>
                @error('jml_cicilan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="id_anggota">Anggota</label>
                <select class="form-control @error('id_anggota') is-invalid @enderror" id="id_anggota" name="id_anggota" required>
                    <option value="">Pilih Anggota</option>
                    @foreach($anggota as $member)
                    <option value="{{ $member->id }}" {{ old('id_anggota') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                    @endforeach
                </select>
                @error('id_anggota')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection