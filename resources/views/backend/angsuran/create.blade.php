@extends('backend.app')
@section('title', 'Tambah Angsuran')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('angsuran.index') }}" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h2 class="mb-0">Tambah Angsuran</h2>
    </div>

    {{-- Notifikasi SweetAlert2 jika gagal --}}
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

    {{-- Notifikasi SweetAlert2 jika sukses --}}
    @if(session('success'))
    <script>
        window.onload = () => Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            timer: 2600,
            showConfirmButton: false
        });
    </script>
    @endif

    <div class="card shadow-sm rounded">
        <div class="card-body">
            <form action="{{ route('angsuran.store') }}" method="POST" enctype="multipart/form-data" id="createAngsuranForm">
                @csrf

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">Pilih Pinjaman <span class="text-danger">*</span></label>
                    <div class="col-md-6">
                        <select name="pinjaman_id" class="form-select @error('pinjaman_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pinjaman --</option>
                            @foreach($listPinjaman as $pinjaman)
                                <option value="{{ $pinjaman->id }}" {{ old('pinjaman_id') == $pinjaman->id ? 'selected' : '' }}>
                                    [{{ $pinjaman->kode_transaksi }}] - {{ $pinjaman->anggota_name }} (Sisa: Rp {{ number_format($pinjaman->sisa_pinjam,0,',','.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('pinjaman_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="tanggal_angsuran" class="col-md-3 col-form-label">Tanggal Angsuran <span class="text-danger">*</span></label>
                    <div class="col-md-6">
                        <input type="date" name="tanggal_angsuran" id="tanggal_angsuran"
                               class="form-control @error('tanggal_angsuran') is-invalid @enderror"
                               value="{{ old('tanggal_angsuran', date('Y-m-d')) }}" required>
                        @error('tanggal_angsuran')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="jumlah_angsuran" class="col-md-3 col-form-label">Jumlah Angsuran <span class="text-danger">*</span></label>
                    <div class="col-md-6">
                        <input type="number" step="0.01" min="0" name="jumlah_angsuran" id="jumlah_angsuran"
                               class="form-control @error('jumlah_angsuran') is-invalid @enderror"
                               value="{{ old('jumlah_angsuran') }}" required>
                        @error('jumlah_angsuran')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                {{-- Bunga, auto by backend, tapi bisa ditampilkan (readonly) --}}
                <div class="row mb-3">
                    <label for="bunga_pinjaman" class="col-md-3 col-form-label">Bunga Angsuran (%)</label>
                    <div class="col-md-6">
                        <input type="number" step="0.01" name="bunga_pinjaman" id="bunga_pinjaman"
                               class="form-control" value="" readonly>
                        <small class="text-muted">Bunga otomatis terisi setelah pinjaman dipilih</small>
                    </div>
                </div>
                {{-- Bukti Pembayaran --}}
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                    <div class="col-md-6">
                        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*"
                               class="form-control @error('bukti_pembayaran') is-invalid @enderror" required>
                        @error('bukti_pembayaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="mt-2">
                            <img id="preview-img" src="#" alt="Preview Gambar" class="img-fluid rounded shadow-sm d-none" style="max-height:160px;">
                        </div>
                    </div>
                </div>
                {{-- Keterangan (optional) --}}
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">Keterangan</label>
                    <div class="col-md-6">
                        <textarea name="keterangan" class="form-control">{{ old('keterangan') }}</textarea>
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
    // Preview gambar bukti pembayaran sebelum upload
    document.getElementById('bukti_pembayaran').onchange = function (evt) {
        const [file] = this.files;
        const img = document.getElementById('preview-img');
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                img.src = e.target.result;
                img.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            img.classList.add('d-none');
        }
    };
    // Spinner loading saat submit
    document.getElementById('createAngsuranForm').onsubmit = function() {
        document.getElementById('submitBtn').setAttribute('disabled', 'disabled');
        document.getElementById('submitSpinner').classList.remove('d-none');
    };

    // Otomatis isi bunga ketika pilih pinjaman
    document.querySelector('select[name="pinjaman_id"]').addEventListener('change', function() {
        let bunga = this.options[this.selectedIndex].getAttribute('data-bunga');
        document.getElementById('bunga_pinjaman').value = bunga ? bunga : '';
    });
</script>
@endpush
