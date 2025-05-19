@extends('backend.app')
@section('title', 'Detail Simpanan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Detail Simpanan</h2>
    <div class="bg-light rounded h-100 p-4">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <strong>Informasi Anggota</strong>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @if($detailSimpanan->anggota_image)
                                <img src="{{ asset($detailSimpanan->anggota_image) }}"
                                     class="rounded shadow-sm"
                                     style="max-width: 150px; max-height: 150px;">
                            @else
                                <span class="text-muted">Tidak ada foto anggota</span>
                            @endif
                        </div>
                        <table class="table">
                            <tr><th>Nama</th><td>{{ $detailSimpanan->anggota_name }}</td></tr>
                            <tr><th>NIP</th><td>{{ $detailSimpanan->anggota_nip }}</td></tr>
                            <tr><th>No HP</th><td>{{ $detailSimpanan->anggota_telphone }}</td></tr>
                            <tr><th>Agama</th><td>{{ $detailSimpanan->anggota_agama }}</td></tr>
                            <tr><th>Alamat</th><td>{{ $detailSimpanan->anggota_alamat }}</td></tr>
                            <tr><th>Pekerjaan</th><td>{{ $detailSimpanan->anggota_pekerjaan }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <strong>Informasi Simpanan</strong>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr><th>Kode</th><td>{{ $detailSimpanan->kode }}</td></tr>
                            <tr><th>Tanggal</th><td>{{ $detailSimpanan->tgl }}</td></tr>
                            <tr><th>Jenis Simpanan</th><td>{{ $detailSimpanan->jenis_simpanan_nama }}</td></tr>
                            <tr><th>Jumlah</th><td>Rp {{ number_format($detailSimpanan->jmlh,2,',','.') }}</td></tr>
                            <tr><th>Dibuat Oleh</th><td>{{ $detailSimpanan->created_by }}</td></tr>
                            <tr><th>Diperbarui Oleh</th><td>{{ $detailSimpanan->updated_by }}</td></tr>
                        </table>
                        <div class="mt-4">
                            <strong>Bukti Pembayaran</strong><br>
                            @if($detailSimpanan->bukti)
                                @php
                                    $ext = pathinfo($detailSimpanan->bukti, PATHINFO_EXTENSION);
                                @endphp
                                @if(in_array($ext, ['jpg','jpeg','png']))
                                    <img src="{{ asset($detailSimpanan->bukti) }}"
                                         id="proof-image"
                                         alt="Bukti Pembayaran"
                                         style="max-width: 50%; cursor: pointer;">
                                @elseif($ext === 'pdf')
                                    <embed src="{{ asset($detailSimpanan->bukti) }}"
                                           type="application/pdf"
                                           style="width:100%; max-width:400px; min-height:400px;">
                                @else
                                    <span class="text-muted">Format file tidak dikenali</span>
                                @endif
                            @else
                                <span class="text-muted">Tidak ada bukti pembayaran</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('simpanan.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Image Modal --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modal-image" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Klik gambar preview modal
    const img = document.getElementById('proof-image');
    if (img) {
        img.addEventListener('click', function() {
            document.getElementById('modal-image').src = this.src;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        });
    }
    // Alert timeout
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(a => new bootstrap.Alert(a).close());
    }, 5000);
});
</script>
@endsection
