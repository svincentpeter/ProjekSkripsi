@extends('backend.app')
@section('title', 'Data Angsuran')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .img-thumb { cursor: pointer; transition: .2s; }
    .img-thumb:hover { transform: scale(1.12); box-shadow: 0 2px 12px rgba(0,0,0,.15);}
</style>
@endpush

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h2 class="mb-0">Data Angsuran</h2>
        <a href="{{ route('angsuran.create') }}" class="btn btn-success shadow-sm"><i class="fas fa-plus"></i> Tambah Angsuran</a>
    </div>

    {{-- SweetAlert2 Flash Message --}}
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

    <div class="bg-light rounded shadow-sm p-4 mb-4">
        <form class="row g-3 mb-3" method="get">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama/Kode" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Status</option>
                    <option value="PENDING" {{ request('status')=='PENDING'?'selected':'' }}>Belum Lunas</option>
                    <option value="LUNAS" {{ request('status')=='LUNAS'?'selected':'' }}>Lunas</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3 d-grid d-md-flex">
                <button type="submit" class="btn btn-primary me-2"><i class="fas fa-search"></i> Filter</button>
                {{-- Placeholder Export --}}
                <a href="#" class="btn btn-outline-secondary d-none" title="Export (Coming Soon)" disabled>
                    <i class="fas fa-file-export"></i>
                </a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr class="text-center align-middle">
                        <th>Kode</th>
                        <th>Cicilan</th>
                        <th>Tanggal</th>
                        <th>Nasabah</th>
                        <th>Pinjaman</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($angsuran as $ang)
                    <tr>
                        <td>{{ $ang->kode_transaksi_angsuran }}</td>
                        <td class="text-center">{{ $ang->angsuran_ke }}</td>
                        <td>{{ tanggal_indonesia($ang->tanggal_angsuran ?? $ang->created_at, false) }}</td>
                        <td>{{ $ang->nasabah }}</td>
                        <td>Rp {{ number_format($ang->pinjaman_pokok, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($ang->jumlah_angsuran, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($ang->status === 'PENDING' || $ang->status == '0')
                                <span class="badge bg-warning text-dark">Belum Lunas</span>
                            @elseif($ang->status === 'LUNAS' || $ang->status == '1')
                                <span class="badge bg-success">Lunas</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($ang->bukti_pembayaran)
                                <img src="{{ asset('assets/img/'.$ang->bukti_pembayaran) }}" 
                                    class="img-thumb rounded" alt="Bukti" width="40"
                                    data-img="{{ asset('assets/img/'.$ang->bukti_pembayaran) }}"
                                    onclick="previewImage(this)">
                            @else
                                <span class="text-muted small fst-italic">-</span>
                            @endif
                        </td>
                        <td class="text-nowrap text-center">
                            <a href="{{ route('angsuran.show', $ang->angsuran_id) }}" 
                                class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('angsuran.edit', $ang->angsuran_id) }}" 
                                class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" 
                                onclick="deleteAngsuran('{{ route('angsuran.destroy', $ang->angsuran_id) }}')" 
                                data-bs-toggle="tooltip" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">Tidak ada data angsuran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="float-end mt-2">
                {{ $angsuran->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal Preview Gambar --}}
<div class="modal fade" id="modalPreviewImg" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow border-0">
            <div class="modal-body p-0">
                <img id="preview-img" src="" class="img-fluid rounded" alt="Preview Bukti">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Tooltip
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(e=>new bootstrap.Tooltip(e));

    // Modal preview gambar
    window.previewImage = (el) => {
        document.getElementById('preview-img').src = el.dataset.img;
        new bootstrap.Modal(document.getElementById('modalPreviewImg')).show();
    };

    // SweetAlert2 delete
    function deleteAngsuran(url) {
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data angsuran yang dihapus tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#e3342f'
        }).then((result) => {
            if (result.isConfirmed) {
                // create a form & submit
                let form = document.createElement('form');
                form.action = url;
                form.method = 'POST';
                form.style.display = 'none';
                let csrf = document.createElement('input');
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                let method = document.createElement('input');
                method.name = '_method';
                method.value = 'DELETE';
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
