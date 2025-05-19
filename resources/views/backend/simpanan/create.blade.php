@extends('backend.app')
@section('title', 'Tambah Simpanan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Tambah Simpanan</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-light rounded h-100 p-4">
        <form
            id="formSimpanan"
            method="POST"
            action="{{ route('simpanan.store') }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="form-floating mb-3">
                <input
                    type="text"
                    class="form-control"
                    id="kode_transaksi"
                    name="kode_transaksi"
                    value="{{ $kodeTransaksi }}"
                    readonly
                >
                <label for="kode_transaksi">Kode Transaksi</label>
            </div>

            <div class="form-floating mb-3">
                <input
                    type="date"
                    class="form-control @error('tanggal_simpanan') is-invalid @enderror"
                    id="tanggal_simpanan"
                    name="tanggal_simpanan"
                    value="{{ old('tanggal_simpanan') }}"
                    required
                >
                <label for="tanggal_simpanan">Tanggal Simpanan</label>
                @error('tanggal_simpanan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-floating mb-3">
                <select
                    id="anggota_id"
                    name="anggota_id"
                    class="form-select @error('anggota_id') is-invalid @enderror"
                    required
                >
                    <option value="" disabled {{ old('anggota_id') ? '' : 'selected' }}>Pilih Anggota</option>
                    @foreach($anggotaList as $nasabah)
                        <option
                            value="{{ $nasabah->id }}"
                            {{ old('anggota_id') == $nasabah->id ? 'selected' : '' }}
                        >{{ $nasabah->name }}</option>
                    @endforeach
                </select>
                <label for="anggota_id">Nama Anggota</label>
                @error('anggota_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-floating mb-3">
                <select
                    id="jenis_simpanan_id"
                    name="jenis_simpanan_id"
                    class="form-select @error('jenis_simpanan_id') is-invalid @enderror"
                    required
                >
                    <option value="" disabled {{ old('jenis_simpanan_id') ? '' : 'selected' }}>Pilih Jenis Simpanan</option>
                    @foreach($jenisList as $jenis)
                        <option
                            value="{{ $jenis->id }}"
                            data-nominal="{{ $jenis->id == 1 ? 250000 : ($jenis->id == 2 ? 20000 : 0) }}"
                            {{ old('jenis_simpanan_id') == $jenis->id ? 'selected' : '' }}
                        >{{ $jenis->nama }}</option>
                    @endforeach
                </select>
                <label for="jenis_simpanan_id">Jenis Simpanan</label>
                @error('jenis_simpanan_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-floating mb-3">
                <input
                    type="number"
                    class="form-control @error('jumlah_simpanan') is-invalid @enderror"
                    id="jumlah_simpanan"
                    name="jumlah_simpanan"
                    value="{{ old('jumlah_simpanan') }}"
                    required
                >
                <label for="jumlah_simpanan">Jumlah Simpanan</label>
                @error('jumlah_simpanan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                <input
                    type="file"
                    class="form-control @error('bukti_pembayaran') is-invalid @enderror"
                    id="bukti_pembayaran"
                    name="bukti_pembayaran"
                    accept="image/*,application/pdf"
                    required
                >
                @error('bukti_pembayaran')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-outline-primary">Submit</button>
        </form>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisSelect = document.getElementById('jenis_simpanan_id');
    const jumlahInput = document.getElementById('jumlah_simpanan');

    function syncNominal() {
        const opt = jenisSelect.options[jenisSelect.selectedIndex];
        const defaultNominal = opt?.getAttribute('data-nominal') || '0';
        jumlahInput.value = defaultNominal;
        jumlahInput.readOnly = defaultNominal !== '0';
    }

    // initialize on load
    syncNominal();
    jenisSelect.addEventListener('change', syncNominal);
});
</script>
@endsection

@endsection
