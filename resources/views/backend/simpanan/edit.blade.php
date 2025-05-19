@extends('backend.app')
@section('title', 'Edit Simpanan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Edit Simpanan</h2>

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
            id="editSimpananForm"
            method="POST"
            action="{{ route('simpanan.update', $item->id) }}"
            enctype="multipart/form-data"
        >
            @csrf @method('PUT')

            <div class="form-floating mb-3">
                <input
                    type="text"
                    class="form-control"
                    id="kode_transaksi"
                    name="kode_transaksi"
                    value="{{ $item->kode_transaksi }}"
                    readonly
                >
                <label for="kode_transaksi">Kode Transaksi</label>
            </div>

            <div class="form-floating mb-3">
                <input
                    type="date"
                    class="form-control"
                    id="tanggal_simpanan"
                    name="tanggal_simpanan"
                    value="{{ $item->tanggal_simpanan }}"
                    readonly
                >
                <label for="tanggal_simpanan">Tanggal Simpanan</label>
            </div>

            <div class="form-floating mb-3">
                <select
                    id="anggota_id"
                    name="anggota_id"
                    class="form-select @error('anggota_id') is-invalid @enderror"
                    required
                >
                    <option value="" disabled>Pilih Anggota</option>
                    @foreach($anggotaList as $nasabah)
                    <option
                        value="{{ $nasabah->id }}"
                        {{ $item->anggota_id == $nasabah->id ? 'selected' : '' }}
                    >{{ $nasabah->name }}</option>
                    @endforeach
                </select>
                <label for="anggota_id">Nama Anggota</label>
                @error('anggota_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-floating mb-3">
                <select
                    id="jenis_simpanan_id"
                    name="jenis_simpanan_id"
                    class="form-select @error('jenis_simpanan_id') is-invalid @enderror"
                    required
                >
                    <option value="" disabled>Pilih Jenis Simpanan</option>
                    @foreach($jenisList as $jenis)
                    <option
                        value="{{ $jenis->id }}"
                        data-nominal="{{ $jenis->id == 1 ? 250000 : ($jenis->id == 2 ? 20000 : 0) }}"
                        {{ $item->jenis_simpanan_id == $jenis->id ? 'selected' : '' }}
                    >{{ $jenis->nama }}</option>
                    @endforeach
                </select>
                <label for="jenis_simpanan_id">Jenis Simpanan</label>
                @error('jenis_simpanan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-floating mb-3">
                <input
                    type="number"
                    class="form-control @error('jumlah_simpanan') is-invalid @enderror"
                    id="jumlah_simpanan"
                    name="jumlah_simpanan"
                    value="{{ old('jumlah_simpanan', $item->jumlah_simpanan) }}"
                    required
                >
                <label for="jumlah_simpanan">Jumlah Simpanan</label>
                @error('jumlah_simpanan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                <input
                    type="file"
                    class="form-control @error('bukti_pembayaran') is-invalid @enderror"
                    id="bukti_pembayaran"
                    name="bukti_pembayaran"
                    accept="image/*,application/pdf"
                >
                @error('bukti_pembayaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Pratinjau Bukti Pembayaran</label>
                <div id="preview-container">
                    @if ($item->bukti_pembayaran)
                        @php
                            $ext = pathinfo($item->bukti_pembayaran, PATHINFO_EXTENSION);
                        @endphp
                        @if (in_array($ext, ['jpg','jpeg','png']))
                            <img
                                src="{{ asset($item->bukti_pembayaran) }}"
                                style="max-width:300px; margin-top:10px;"
                            >
                        @elseif ($ext==='pdf')
                            <embed
                                src="{{ asset($item->bukti_pembayaran) }}"
                                type="application/pdf"
                                style="max-width:300px; max-height:400px; margin-top:10px;"
                            >
                        @endif
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-outline-primary">Update</button>
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
        const nominal = opt?.getAttribute('data-nominal') || '0';
        // Only override if the original was the default or empty
        if (!jumlahInput.value || jumlahInput.value == '{{ $item->jumlah_simpanan }}') {
            jumlahInput.value = nominal;
        }
        jumlahInput.readOnly = nominal !== '0';
    }
    jenisSelect.addEventListener('change', syncNominal);

    // live preview for new upload
    document.getElementById('bukti_pembayaran').addEventListener('change', function(e) {
        const container = document.getElementById('preview-container');
        container.innerHTML = '';
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = evt => {
            let el;
            if (file.type.startsWith('image/')) {
                el = document.createElement('img');
                el.src = evt.target.result;
                el.style.maxWidth = '300px';
                el.style.marginTop = '10px';
            } else if (file.type === 'application/pdf') {
                el = document.createElement('embed');
                el.src = evt.target.result;
                el.type = 'application/pdf';
                el.style.maxWidth = '300px';
                el.style.maxHeight = '400px';
                el.style.marginTop = '10px';
            } else {
                el = document.createElement('p');
                el.textContent = 'Preview tidak tersedia untuk jenis file ini';
            }
            container.appendChild(el);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection

@endsection
