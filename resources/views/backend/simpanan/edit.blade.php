@extends('backend.app')
@section('title', 'Edit Simpanan')
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
    <h2 class="mb-4">Edit Simpanan</h2>
    <div class="bg-light rounded h-100 p-4">
        <form method="POST" action="{{ route('simpanan.update', $simpanedit->id) }}" id="editSimpananForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="kodeTransaksiSimpanan" name="kodeTransaksiSimpanan" value="{{ $simpanedit->kodeTransaksiSimpanan }}" readonly>
                <label for="kodeTransaksiSimpanan">Kode Transaksi</label>
            </div>
            <div class="form-floating mb-3">
                <input type="date" class="form-control" value="{{ $simpanedit->tanggal_simpanan }}" id="tanggal_simpanan" readonly>
                <label for="tanggal_simpanan">Tanggal Simpanan</label>
            </div>
            <div class="form-floating mb-3">
                <select id="id_anggota" name="id_anggota" class="form-select">
                    <option value="" disabled>Pilih Anggota</option>
                    @foreach($namaNasabah as $nasabah)
                    <option value="{{ $nasabah->id }}" {{ $simpanedit->id_anggota == $nasabah->id ? 'selected' : '' }}>{{ $nasabah->name }}</option>
                    @endforeach
                </select>
                <label for="id_anggota">Nama Anggota</label>
            </div>
            <div class="form-floating mb-3">
                <select id="id_jenis_simpanan" name="id_jenis_simpanan" class="form-select">
                    <option value="" disabled>Pilih Jenis Simpanan</option>
                    @foreach($jenisSimpanan as $jenis)
                    <option value="{{ $jenis->id }}" {{ $simpanedit->id_jenis_simpanan == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama }}</option>
                    @endforeach
                </select>
                <label for="id_jenis_simpanan">Jenis Simpanan</label>
            </div>
            <div class="form-floating mb-3">
                <input type="number" class="form-control" value="{{ $simpanedit->jml_simpanan }}" id="jml_simpanan" name="jml_simpanan">
                <label for="jml_simpanan">Jumlah Simpanan</label>
            </div>
            <div class="mb-3">
                <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                <input class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" type="file" accept="image/*,application/pdf">
            </div>
            <!-- Preview area -->
            <div class="mb-3">
                <label class="form-label">Pratinjau Bukti Pembayaran</label>
                <div id="preview-container">
                    @if ($simpanedit->bukti_pembayaran)
                    @php
                    $fileExtension = pathinfo($simpanedit->bukti_pembayaran, PATHINFO_EXTENSION);
                    @endphp
                    @if (in_array($fileExtension, ['jpg', 'jpeg', 'png']))
                    <img src="{{ asset($simpanedit->bukti_pembayaran) }}" style="max-width: 300px; margin-top: 10px;">
                    @elseif ($fileExtension == 'pdf')
                    <embed src="{{ asset($simpanedit->bukti_pembayaran) }}" type="application/pdf" style="max-width: 300px; max-height: 400px; margin-top: 10px;">
                    @endif
                    @endif
                </div>
            </div>
            <button type="submit" class="btn btn-outline-primary m-2">Update</button>
        </form>
    </div>
</div>

<!-- JavaScript for previewing the uploaded file -->
<script>
    document.getElementById('bukti_pembayaran').addEventListener('change', function(event) {
        var previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = ''; // Clear any previous preview

        var file = event.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var fileType = file.type;
                var filePreview;

                if (fileType.includes('image')) {
                    filePreview = document.createElement('img');
                    filePreview.src = e.target.result;
                    filePreview.style.maxWidth = '300px';
                    filePreview.style.marginTop = '10px';
                } else if (fileType.includes('pdf')) {
                    filePreview = document.createElement('embed');
                    filePreview.src = e.target.result;
                    filePreview.type = 'application/pdf';
                    filePreview.style.maxWidth = '300px';
                    filePreview.style.maxHeight = '400px';
                    filePreview.style.marginTop = '10px';
                } else {
                    filePreview = document.createElement('p');
                    filePreview.textContent = 'File format not supported for preview';
                }

                previewContainer.appendChild(filePreview);
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection