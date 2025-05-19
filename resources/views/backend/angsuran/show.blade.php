@extends('backend.app')
@section('title', 'Detail Angsuran')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .badge-status { font-size: 0.95em; padding: .45em 1.1em; }
    .img-thumbnail { cursor: pointer; transition: .2s; }
    .img-thumbnail:hover { box-shadow: 0 2px 20px #3e6cdce6; }
</style>
@endpush

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('angsuran.index') }}" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h2 class="mb-0">Detail Angsuran</h2>
        <a href="{{ route('angsuran.cetak', ['id' => $angsuran->id]) }}" target="_blank" class="btn btn-outline-primary ms-auto">
            <i class="fas fa-print"></i> Cetak/Download
        </a>
    </div>

    {{-- Notifikasi SweetAlert2 --}}
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

    <div class="card shadow rounded">
        <div class="card-body">
            <h5 class="mb-4">Informasi Angsuran</h5>
            <div class="row">
                <div class="col-md-7">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th>Kode Angsuran</th>
                                <td>{{ $angsuran->kode_transaksi }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Angsuran</th>
                                <td>{{ tanggal_indonesia($angsuran->tanggal_angsuran, false) }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah Angsuran</th>
                                <td>Rp {{ number_format($angsuran->jumlah_angsuran, 0, ',', '.') }}</td>
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
                                <td>
                                    @if($angsuran->status === 'PENDING' || $angsuran->status == 0)
                                        <span class="badge bg-warning text-dark badge-status">Belum Lunas</span>
                                    @elseif($angsuran->status === 'LUNAS' || $angsuran->status == 1)
                                        <span class="badge bg-success badge-status">Lunas</span>
                                    @else
                                        <span class="badge bg-secondary badge-status">{{ $angsuran->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Bunga Pinjaman</th>
                                <td>{{ number_format($angsuran->bunga_pinjaman, 2) }}%</td>
                            </tr>
                            <tr>
                                <th>Denda</th>
                                <td>Rp {{ number_format($angsuran->denda ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Oleh</th>
                                <td>{{ $angsuran->created_by_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $angsuran->keterangan ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-5 d-flex flex-column align-items-center justify-content-center">
                    @if($angsuran->bukti_pembayaran)
                        <div class="text-center">
                            <span class="mb-2 d-block">Bukti Pembayaran:</span>
                            <img src="{{ asset('assets/img/' . $angsuran->bukti_pembayaran) }}" 
                                 alt="Bukti Pembayaran"
                                 class="img-thumbnail shadow mb-2" 
                                 style="max-width:220px; max-height:180px"
                                 data-bs-toggle="modal" data-bs-target="#imgModal">
                        </div>
                        {{-- Modal Preview Bukti Pembayaran --}}
                        <div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <img src="{{ asset('assets/img/' . $angsuran->bukti_pembayaran) }}"
                                         class="img-fluid rounded" alt="Bukti Pembayaran">
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">Tidak ada bukti pembayaran</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Modal gambar klik buka besar
    document.querySelectorAll('.img-thumbnail[data-bs-toggle="modal"]').forEach(img => {
        img.onclick = function() {
            new bootstrap.Modal(document.getElementById('imgModal')).show();
        }
    });
</script>
@endpush
