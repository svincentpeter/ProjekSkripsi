@extends('backend.app')
@section('title', 'Detail Simpanan')
@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Detail Simpanan</h2>
    <div class="bg-light rounded h-100 p-4">
        <div class="card mb-4">
           
            <div class="card-header">
                Detail Simpanan #{{ $detailSimpanan->kode }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title">Informasi Anggota</h5>
                        <table class="table table-striped">
                            <tr>
                                <td colspan="2" class="text-center">
                                    @if ($detailSimpanan->anggota_image)
                                    <img src="{{ url('/assets/backend/img/' . $detailSimpanan->anggota_image) }}" alt="Foto Anggota" style="max-width: 100%;">
                                    @else
                                    <p>Tidak ada foto anggota</p>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Nama Anggota</th>
                                <td>{{ $detailSimpanan->anggota_name }}</td>
                            </tr>
                            <tr>
                                <th>NIP Anggota</th>
                                <td>{{ $detailSimpanan->anggota_nip }}</td>
                            </tr>
                            <tr>
                                <th>No HP Anggota</th>
                                <td>{{ $detailSimpanan->anggota_telphone }}</td>
                            </tr>
                            <tr>
                                <th>Agama Anggota</th>
                                <td>{{ $detailSimpanan->anggota_agama }}</td>
                            </tr>
                            <tr>
                                <th>Alamat Anggota</th>
                                <td>{{ $detailSimpanan->anggota_alamat }}</td>
                            </tr>
                            <tr>
                                <th>Pekerjaan Anggota</th>
                                <td>{{ $detailSimpanan->anggota_pekerjaan }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="card-title">Informasi Simpanan</h5>
                        <table class="table table-striped">
                            <tr>
                                <th>Kode Transaksi Simpanan</th>
                                <td>{{ $detailSimpanan->kode }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Simpanan</th>
                                <td>{{ $detailSimpanan->tgl}}</td>
                            </tr>
                            <tr>
                                <th>Jenis Simpanan</th>
                                <td>{{ $detailSimpanan->jenis_simpanan_nama }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah Simpanan</th>
                                <td>Rp {{ number_format($detailSimpanan->jmlh, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Oleh</th>
                                <td>{{ $detailSimpanan->created_by }}</td>
                            </tr>
                            <tr>
                                <th>Update Oleh</th>
                                <td>{{ $detailSimpanan->updated_by }}</td>
                            </tr>
                        </table>
                        <h5 class="card-title">Bukti Pembayaran</h5>
                        <div class="text-center">
                            @if ($detailSimpanan->bukti)
                            <img src="{{ asset($detailSimpanan->bukti) }}" id="proof-image" alt="Bukti Pembayaran" style="max-width: 50%; cursor: pointer;">
                            @else
                            <p>Tidak ada bukti pembayaran</p>
                            @endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('simpanan') }}" class="btn btn-secondary mt-3">Kembali</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal for displaying the payment proof image -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modal-image" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const proofImage = document.getElementById('proof-image');
        if (proofImage) {
            proofImage.addEventListener('click', function() {
                const modalImage = document.getElementById('modal-image');
                modalImage.src = proofImage.src;
                const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                imageModal.show();
            });
        }
    });
</script>
@endsection