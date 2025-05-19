@extends('backend.app')
@section('title', 'Tambah Pinjaman')
@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4"><i class="fas fa-plus-circle me-2"></i>Tambah Pinjaman</h2>
    <div class="bg-light rounded h-100 p-4 shadow">
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="alert alert-info shadow-sm">
            <strong>Informasi:</strong> Jumlah maksimal pinjaman baru adalah
            <span class="fw-bold text-primary">Rp {{ number_format($maxPinjamanBaru, 0, ',', '.') }}</span>
        </div>

        <form method="POST" action="{{ route('pinjaman.store') }}">
            @csrf
            {{-- Tanggal Pinjam --}}
            <div class="mb-3">
                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                <input type="date"
                    class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                    id="tanggal_pinjam"
                    name="tanggal_pinjam"
                    value="{{ old('tanggal_pinjam') }}"
                    required>
                @error('tanggal_pinjam')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- Jumlah Pinjam --}}
            <div class="mb-3">
                <label for="jumlah_pinjam" class="form-label">Jumlah Pinjam</label>
                <input type="number" step="0.01"
                    class="form-control @error('jumlah_pinjam') is-invalid @enderror"
                    id="jumlah_pinjam"
                    name="jumlah_pinjam"
                    value="{{ old('jumlah_pinjam') }}"
                    required>
                @error('jumlah_pinjam')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- Tenor (bulan) --}}
            <div class="mb-3">
                <label for="tenor" class="form-label">Durasi (bulan)</label>
                <input type="number"
                    class="form-control @error('tenor') is-invalid @enderror"
                    id="tenor"
                    name="tenor"
                    value="{{ old('tenor') }}"
                    min="1"
                    required>
                @error('tenor')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- Bunga (%) --}}
            <div class="mb-3">
                <label for="bunga" class="form-label">Bunga (%)</label>
                <input type="number" step="0.01"
                    class="form-control @error('bunga') is-invalid @enderror"
                    id="bunga"
                    name="bunga"
                    value="{{ old('bunga') }}"
                    min="0"
                    required>
                @error('bunga')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- Anggota --}}
            <div class="mb-3">
                <label for="anggota_id" class="form-label">Anggota</label>
                <select
                    class="form-select @error('anggota_id') is-invalid @enderror"
                    id="anggota_id"
                    name="anggota_id"
                    required>
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($anggota as $member)
                    <option value="{{ $member->id }}" {{ old('anggota_id') == $member->id ? 'selected' : '' }}>
                        {{ $member->name }}
                    </option>
                    @endforeach
                </select>
                @error('anggota_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('pinjaman') }}" class="btn btn-secondary me-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
