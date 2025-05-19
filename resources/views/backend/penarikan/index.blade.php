@extends('backend.app')
@section('title', 'Penarikan')

@push('styles')
<style>
    .table th, .table td { vertical-align: middle !important; }
    .badge-jumlah { font-size: 1em; }
    .table-hover tbody tr:hover { background: #f1f5fa; }
    .custom-alert { z-index: 9999; position: fixed; top: 75px; right: 28px; min-width: 280px; }
    .btn-action { margin-right: .25rem; }
    .pagination { margin-bottom: 0; }
    .table th.sortable { cursor: pointer; }
    .sort-arrow { font-size: 1em; margin-left: 2px; }
</style>
@endpush

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h2 class="mb-2 mb-md-0 fw-bold"><i class="fas fa-wallet me-2"></i>Data Penarikan</h2>
        <div class="d-flex flex-wrap gap-2">
            @can('penarikan-create')
            <button class="btn btn-primary rounded-pill btn-sm" data-bs-toggle="modal" data-bs-target="#buatPenarikan">
                <i class="fas fa-plus me-1"></i>Tambah Penarikan
            </button>
            @endcan
            @can('laporan_penarikan')
            <a href="{{ route('penarikan.cetak', request()->only(['start_date','end_date'])) }}"
                class="btn btn-outline-success rounded-pill btn-sm" target="_blank">
                <i class="fas fa-print me-1"></i>Cetak PDF
            </a>
            <a href="{{ route('penarikan.excel', request()->only(['start_date','end_date','search'])) }}"
                class="btn btn-success ms-2 btn-sm rounded-pill">
                <i class="fas fa-file-excel"></i> Download Excel
            </a>
            @endcan
            <a href="{{ route('penarikan') }}" class="btn btn-outline-secondary rounded-pill btn-sm {{ (request()->has('search') || request()->has('start_date')) ? '' : 'd-none' }}">
                <i class="fas fa-redo"></i> Reset Filter
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="bg-light rounded shadow-sm h-100 p-4">
        <form action="{{ route('penarikan') }}" method="GET" class="row g-2 align-items-end mb-3">
            <input type="hidden" name="sort" value="{{ request('sort','tanggal_penarikan') }}">
            <input type="hidden" name="dir" value="{{ request('dir','desc') }}">
            <div class="col-md-3">
                <label class="form-label mb-0" for="search"><i class="fas fa-search"></i> Cari Kode/Nama</label>
                <input type="search" name="search" class="form-control form-control-sm"
                    value="{{ request('search') }}" placeholder="Cari kode/nama anggota">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-0" for="start_date"><i class="fas fa-calendar-day"></i> Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-0" for="end_date"><i class="fas fa-calendar-check"></i> Tanggal Akhir</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </form>

        <div class="table-responsive rounded">
            <table class="table table-bordered table-hover align-middle mb-1">
                <thead class="table-light">
                    <tr class="align-middle">
                        @php
                        $sort = request('sort','tanggal_penarikan');
                        $dir = request('dir','desc');
                        function arrow($col, $sort, $dir) {
                            if ($col !== $sort) return '';
                            return $dir == 'asc' ? '<span class="sort-arrow">&#9650;</span>' : '<span class="sort-arrow">&#9660;</span>';
                        }
                        @endphp
                        <th class="sortable" data-sort="kode_transaksi" style="width:130px;">
                            Kode Penarikan {!! arrow('kode_transaksi', $sort, $dir) !!}
                        </th>
                        <th class="sortable" data-sort="tanggal_penarikan" style="width:110px;">
                            Tanggal {!! arrow('tanggal_penarikan', $sort, $dir) !!}
                        </th>
                        <th class="sortable" data-sort="anggota_name">
                            Nama Anggota {!! arrow('anggota_name', $sort, $dir) !!}
                        </th>
                        <th class="sortable text-end" data-sort="jumlah_penarikan" style="width:140px;">
                            Jumlah {!! arrow('jumlah_penarikan', $sort, $dir) !!}
                        </th>
                        <th>Keterangan</th>
                        <th class="text-center" style="width:160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penarikan as $item)
                    <tr @if(session('highlight_id')==$item->penarikan_id ?? $item->id) class="table-success" @endif>
                        <td>
                            <a href="javascript:void(0)" class="fw-bold link-primary show-penarikan"
                                data-id="{{ $item->penarikan_id ?? $item->id }}">
                                {{ $item->kode_transaksi }}
                            </a>
                        </td>
                        <td>{{ tanggal_indonesia($item->tanggal_penarikan, false) }}</td>
                        <td>
                            <span class="fw-semibold"><i class="fas fa-user me-1"></i>{{ $item->anggota_name ?? $item->name }}</span>
                        </td>
                        <td class="text-end">
                            <span class="badge bg-primary bg-opacity-10 border border-primary text-primary badge-jumlah">
                                Rp {{ number_format($item->jumlah_penarikan, 2, ',', '.') }}
                            </span>
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="text-center">
                            <div class="d-flex flex-wrap justify-content-center gap-1">
                                <button type="button" class="btn btn-outline-info btn-sm btn-action show-penarikan"
                                    data-id="{{ $item->penarikan_id ?? $item->id }}" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-action history-penarikan"
                                    data-id="{{ $item->penarikan_id ?? $item->id }}" title="Riwayat">
                                    <i class="fas fa-history"></i>
                                </button>
                                @can('penarikan-edit')
                                <button type="button" class="btn btn-outline-warning btn-sm btn-action"
                                    data-bs-toggle="modal" data-bs-target="#editPenarikan"
                                    data-id="{{ $item->penarikan_id ?? $item->id }}"
                                    data-jumlah="{{ $item->jumlah_penarikan }}"
                                    data-keterangan="{{ $item->keterangan }}"
                                    data-saldo="{{ $item->anggota_saldo ?? $item->saldo ?? 0 }}"
                                    title="Edit"><i class="fas fa-edit"></i>
                                </button>
                                @endcan
                                @can('penarikan-delete')
                                <form action="{{ route('penarikan.destroy', $item->penarikan_id ?? $item->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin hapus data penarikan ini?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm btn-action" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada data penarikan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3 d-flex justify-content-end">
            {{ $penarikan->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- Include modal create & edit --}}
@include('backend.penarikan.modal.modalCreate')
@include('backend.penarikan.modal.modalEdit')

{{-- Modal detail show penarikan --}}
<div class="modal fade" id="detailPenarikanModal" tabindex="-1" aria-labelledby="detailPenarikanLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailPenarikanLabel">Detail Penarikan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="showPenarikanContent">
        {{-- AJAX content here --}}
        <div class="text-center text-secondary py-5">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal riwayat log --}}
<div class="modal fade" id="historyPenarikanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Perubahan Penarikan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="historyPenarikanContent">
                <div class="text-center text-secondary py-5">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => {
        document.querySelectorAll('.custom-alert').forEach(a => new bootstrap.Alert(a).close());
    }, 4000);

    // Sorting klik header kolom
    document.querySelectorAll('th.sortable').forEach(th => {
        th.onclick = function() {
            const sort = this.getAttribute('data-sort');
            const url = new URL(window.location.href);
            let dir = url.searchParams.get('dir') || 'desc';
            if (url.searchParams.get('sort') === sort) {
                dir = dir === 'asc' ? 'desc' : 'asc';
            } else {
                dir = 'asc';
            }
            url.searchParams.set('sort', sort);
            url.searchParams.set('dir', dir);
            window.location.href = url.toString();
        }
    });

    // Show detail penarikan (AJAX)
    document.querySelectorAll('.show-penarikan').forEach(btn => {
        btn.onclick = function() {
            const id = this.dataset.id;
            const content = document.getElementById('showPenarikanContent');
            content.innerHTML = '<div class="text-center text-secondary py-5"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
            $('#detailPenarikanModal').modal('show');
            fetch(`/penarikan/show/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        content.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                        return;
                    }
                    content.innerHTML = `
                    <table class="table table-borderless">
                      <tbody>
                        <tr>
                          <th>Kode Transaksi</th>
                          <td>${data.kode_transaksi}</td>
                        </tr>
                        <tr>
                          <th>Tanggal Penarikan</th>
                          <td>${data.tanggal_penarikan}</td>
                        </tr>
                        <tr>
                          <th>Nama Anggota</th>
                          <td>${data.anggota_name}</td>
                        </tr>
                        <tr>
                          <th>NIP</th>
                          <td>${data.nip ?? '-'}</td>
                        </tr>
                        <tr>
                          <th>No. Telepon</th>
                          <td>${data.telphone ?? '-'}</td>
                        </tr>
                        <tr>
                          <th>Jumlah Penarikan</th>
                          <td>Rp ${Number(data.jumlah_penarikan).toLocaleString('id-ID')}</td>
                        </tr>
                        <tr>
                          <th>Saldo Anggota Setelah Penarikan</th>
                          <td>Rp ${Number(data.saldo_anggota).toLocaleString('id-ID')}</td>
                        </tr>
                        <tr>
                          <th>Keterangan</th>
                          <td>${data.keterangan ?? '-'}</td>
                        </tr>
                      </tbody>
                    </table>`;
                }).catch(() => content.innerHTML = '<div class="alert alert-danger">Gagal memuat detail.</div>');
        }
    });

    // Show riwayat penarikan (AJAX)
    document.querySelectorAll('.history-penarikan').forEach(btn => {
        btn.onclick = function() {
            const id = this.dataset.id;
            const content = document.getElementById('historyPenarikanContent');
            content.innerHTML = '<div class="text-center text-secondary py-5"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
            $('#historyPenarikanModal').modal('show');
            fetch(`/penarikan/${id}/history`)
                .then(res => res.text())
                .then(html => content.innerHTML = html)
                .catch(() => content.innerHTML = '<div class="alert alert-danger">Gagal memuat riwayat.</div>');
        }
    });
</script>
@endpush

@endsection
