@extends('backend.app')
@section('title', 'Detail Pinjaman')
@section('content')

<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Detail Pinjaman</h2>

    <!-- Alert Success -->
    @if(session('success'))
    <div id="successAlert" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-check-circle"></i> Sukses!</h5>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
    <div id="errorAlert" class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-times-circle"></i> Error!</h5>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="bg-light rounded h-100 p-4 shadow">
        <h4 class="mb-4">Informasi Pinjaman</h4>

        <div class="mb-3">
    @if($pinjaman->status == 'PENDING')
        @can('approve_pinjaman')
        <a href="{{ route('terima_pengajuan', $pinjaman->pinjaman_id) }}" class="btn btn-success btn-sm me-2">Terima</a>
        @endcan
        @can('tolak_pinjaman')
        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#tolakpengajuan">Tolak Pengajuan</button>
        @endcan
    @endif
</div>

        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th>Kode Pinjaman</th>
                        <td>{{ $pinjaman->kode_transaksi }}</td>
                    </tr>
                    <tr>
                        <th>Nama Nasabah</th>
                        <td>{{ $pinjaman->anggota_name }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pinjam</th>
                        <td>{{ tanggal_indonesia($pinjaman->tanggal_pinjam, false) }}</td>
                    </tr>
                    <tr>
                        <th>Jatuh Tempo</th>
                        <td>{{ tanggal_indonesia($pinjaman->jatuh_tempo, false) }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Pinjam</th>
                        <td>Rp {{ number_format($pinjaman->jumlah_pinjam, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th>Lama (bulan)</th>
                        <td>{{ $pinjaman->tenor }} Bulan</td>
                    </tr>
                    <tr>
                        <th>Bunga (%)</th>
                        <td>{{ $pinjaman->bunga }}%</td>
                    </tr>
                    <tr>
                        <th>Total + Bunga</th>
                        <td>Rp {{ number_format($pinjaman->total_dengan_bunga, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($pinjaman->status == 'PENDING')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($pinjaman->status == 'DISETUJUI')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif($pinjaman->status == 'DITOLAK')
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Dibuat Oleh</th>
                        <td>{{ $pinjaman->created_by_name }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Modal Tolak Pengajuan --}}
        <div class="modal fade" id="tolakpengajuan" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('tolak_pengajuan', ['id' => $pinjaman->pinjaman_id]) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Alasan Penolakan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <textarea name="catatan" class="form-control" rows="5" placeholder="Alasan Penolakan" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Bayar Angsuran --}}
        @can('angsuran-create')
        <button type="button" class="btn btn-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#bayarAngsuran">
            Bayar Angsuran
        </button>
        @endcan

        <!-- Modal Bayar Angsuran -->
<div class="modal fade" id="bayarAngsuran" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('angsuran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="pinjaman_id" value="{{ $pinjaman->pinjaman_id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Form Angsuran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal_angsuran">Tanggal Angsuran</label>
                        <input type="date" id="tanggal_angsuran" name="tanggal_angsuran" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_angsuran">Jumlah Angsuran</label>
                        <input type="number" id="jumlah_angsuran" name="jumlah_angsuran" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                        <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Bayar</button>
                </div>
            </form>
        </div>
    </div>
</div>

        {{-- Modal Edit Angsuran --}}
        <div class="modal fade" id="editAngsuran" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="" class="form-edit-angsuran" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Angsuran</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_jumlah_angsuran">Jumlah Angsuran</label>
                                <input type="number" id="edit_jumlah_angsuran" name="jumlah_angsuran" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_bunga_pinjaman">Bunga Angsuran (%)</label>
                                <input type="number" id="edit_bunga_pinjaman" name="bunga_pinjaman" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="edit_bukti_pembayaran">Bukti Pembayaran</label>
                                <input type="file" id="edit_bukti_pembayaran" name="bukti_pembayaran" class="form-control">
                                <img id="edit_image_preview" src="#" style="display:none; max-width:50%; margin-top:10px;">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Daftar Angsuran --}}
        <div class="card mt-4">
            <div class="card-header"><h5>Daftar Angsuran</h5></div>
            <div class="card-body">
                @can('angsuran-create')
                <button class="btn btn-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#bayarAngsuran">
                    Bayar Angsuran
                </button>
                @endcan

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>Sisa Pokok</th>
                                <th>Bunga</th>
                                <th>Cicilan Ke-</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($angsuran as $ang)
                            <tr>
                                <td>{{ $ang->kode_transaksi }}</td>
                                <td>{{ tanggal_indonesia($ang->tanggal_angsuran, false) }}</td>
                                <td>Rp {{ number_format($ang->sisa_angsuran, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($ang->bunga_pinjaman, 0, ',', '.') }}</td>
                                <td>{{ $ang->cicilan }}</td>
                                <td>
                                    @if($ang->status == 0)
                                        <span class="text-warning">Belum Lunas</span>
                                    @else
                                        <span class="text-success">Lunas</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($ang->total_angsuran, 0, ',', '.') }}</td>
                                <td class="text-nowrap">
                                    @can('angsuran-edit')
                                    <button class="btn btn-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#editAngsuran"
                                            data-id="{{ $ang->angsuran_id }}"
                                            data-jumlah_angsuran="{{ $ang->jumlah_angsuran }}"
                                            data-bunga_pinjaman="{{ $ang->bunga_pinjaman }}"
                                            data-bukti="{{ asset('assets/img/'.$ang->bukti_pembayaran) }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endcan
                                    @can('angsuran-delete')
                                    <form action="{{ route('angsuran.destroy', $ang->angsuran_id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus angsuran?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6">Total Angsuran</th>
                                <th colspan="2">Rp {{ number_format($totalAngsuran, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                    {{ $angsuran->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================== SCRIPTS ================== --}}
@push('scripts')
{{-- Cropper.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css" />

<script>
    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(a => new bootstrap.Alert(a).close());
    }, 5000);

    // Animate alerts
    document.querySelectorAll('.custom-alert').forEach((el, i) =>
        setTimeout(() => $(el).fadeIn('slow'), 300 * i)
    );
</script>

<script>
let cropper;
const buktiEl = document.getElementById('bukti_pembayaran');
if (buktiEl) {
    buktiEl.addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = () => {
            const img = document.getElementById('crop-image');
            img.src = reader.result;
            document.getElementById('crop-container').style.display = 'block';
            if (cropper) cropper.destroy();
            cropper = new Cropper(img, { aspectRatio:1, viewMode:1, scalable:true, zoomable:true });
            document.getElementById('crop-button').style.display = 'inline-block';
        };
        reader.readAsDataURL(e.target.files[0]);
    });
    document.getElementById('crop-button').addEventListener('click', () => {
        const canvas = cropper.getCroppedCanvas();
        const prev = document.getElementById('image-preview');
        prev.src = canvas.toDataURL();
        prev.style.display = 'block';
        document.getElementById('crop-container').style.display = 'none';
        document.getElementById('crop-button').style.display = 'none';
    });
}
</script>

<script>
const bungaPinjamEl = document.getElementById('bunga_pinjam');
if (bungaPinjamEl) {
    const bungaPinjam = parseFloat(bungaPinjamEl.value);
    document.getElementById('jumlah_angsuran').addEventListener('input', updateBunga);
    document.getElementById('tanggal_angsuran').addEventListener('change', calculateDenda);

    function updateBunga() {
        const j = parseFloat(this.value)||0;
        document.getElementById('bunga_angsuran').value = (j * bungaPinjam/100).toFixed(2);
        calculateDenda();
    }
    function calculateDenda() {
        const tA = new Date(document.getElementById('tanggal_angsuran').value);
        const jT = new Date(document.getElementById('jatuh_tempo').value);
        let d = 0;
        if (tA>jT) {
            const diff = Math.ceil((tA-jT)/(1000*3600*24));
            d = Math.abs((parseFloat(document.getElementById('jumlah_angsuran').value)||0) * 0.01 * diff);
        }
        document.getElementById('denda').value = d.toFixed(2);
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('editAngsuran');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', e => {
        const btn = e.relatedTarget;
        const id = btn.dataset.id;
        const jml = btn.dataset.jumlah_angsuran;
        const bunga = btn.dataset.bunga_pinjaman;
        const bukti = btn.dataset.bukti;
        const form = modal.querySelector('.form-edit-angsuran');

        form.action = `/angsuran/${id}`;
        form.querySelector('#edit_jumlah_angsuran').value = jml;
        form.querySelector('#edit_bunga_pinjaman').value = bunga;
        const img = form.querySelector('#edit_image_preview');
        if (bukti) { img.src = bukti; img.style.display = 'block'; }
        else img.style.display = 'none';

        form.querySelector('#edit_jumlah_angsuran').addEventListener('input', function(){
            form.querySelector('#edit_bunga_pinjaman').value = (this.value * 0.02).toFixed(2);
        });
    });

    const editBukti = document.getElementById('edit_bukti_pembayaran');
    if (editBukti) {
        editBukti.addEventListener('change', e => {
            const reader = new FileReader();
            reader.onload = () => {
                const out = document.getElementById('edit_image_preview');
                out.src = reader.result;
                out.style.display = 'block';
            };
            reader.readAsDataURL(e.target.files[0]);
        });
    }
});
</script>
@endpush

@endsection
