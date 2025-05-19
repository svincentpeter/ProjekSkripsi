@extends('backend.app')
@section('title', 'Edit Angsuran')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('angsuran.index') }}" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h2 class="mb-0">Edit Angsuran</h2>
    </div>

    @if(session('success'))
    <script>
        window.onload = () => Swal.fire({
            icon: 'success',
            title: 'Sukses',
            text: @json(session('success')),
            timer: 2600,
            showConfirmButton: false
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        window.onload = () => Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: @json(session('error')),
            timer: 3200,
            showConfirmButton: false
        });
    </script>
    @endif

    <div class="card shadow-sm rounded">
        <div class="card-body">
            <form action="{{ route('angsuran.update', $angsuran->id) }}" method="POST" enctype="multipart/form-data" id="editAngsuranForm">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">Kode Angsuran</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control-plaintext" value="{{ $angsuran->kode_transaksi }}" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">Tanggal Angsuran</label>
                    <div class="col-md-6">
                        <input type="date" class="form-control" value="{{ $angsuran->tanggal_angsuran }}" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="jumlah_angsuran" class="col-md-3 col-form-label">Jumlah Angsuran <span class="text-danger">*</span></label>
                    <div class="col-md-6">
                        <input type="number" name="jumlah_angsuran" id="jumlah_angsuran" class="form-control @error('jumlah_angsuran') is-invalid @enderror" value="{{ old('jumlah_angsuran', $angsuran->jumlah_angsuran) }}" required>
                        @error('jumlah_angsuran')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="bunga_pinjaman" class="col-md-3 col-form-label">Bunga Angsuran (%)</label>
                    <div class="col-md-6">
                        <input type="number" step="0.01" name="bunga_pinjaman" id="bunga_pinjaman" class="form-control @error('bunga_pinjaman') is-invalid @enderror" value="{{ old('bunga_pinjaman', $angsuran->bunga_pinjaman) }}" readonly>
                        @error('bunga_pinjaman')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                {{-- Gambar Bukti Pembayaran --}}
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">Bukti Pembayaran</label>
                    <div class="col-md-6">
                        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*" class="form-control @error('bukti_pembayaran') is-invalid @enderror">
                        @error('bukti_pembayaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="mt-2">
                            <img id="preview-img" src="{{ asset('assets/img/'.$angsuran->bukti_pembayaran) }}" 
                                alt="Bukti Pembayaran" class="img-fluid rounded shadow-sm" style="max-height:160px;">
                        </div>
                    </div>
                </div>
                {{-- Optional Field: Keterangan --}}
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">Keterangan</label>
                    <div class="col-md-6">
                        <textarea name="keterangan" class="form-control">{{ old('keterangan', $angsuran->keterangan) }}</textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-9 offset-md-3">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span id="submitSpinner" class="spinner-border spinner-border-sm d-none"></span>
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('angsuran.index') }}" class="btn btn-secondary ms-2">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('bukti_pembayaran').onchange = function (evt) {
    const [file] = this.files;
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('preview-img').src = e.target.result;
        reader.readAsDataURL(file);
    }
};
// Spinner loading saat submit
document.getElementById('editAngsuranForm').onsubmit = function() {
    document.getElementById('submitBtn').setAttribute('disabled', 'disabled');
    document.getElementById('submitSpinner').classList.remove('d-none');
};
</script>
@endpush
