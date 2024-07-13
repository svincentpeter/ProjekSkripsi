@extends('backend.app')

@section('title', 'Pinjaman')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Data Pinjaman</h2>

    <!-- Alert Success -->
    @if(Session::has('success'))
    <div id="successAlert" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-check-circle"></i> Sukses!</h5>
        {{ Session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Alert Error -->
    @if(Session::has('error'))
    <div id="errorAlert" class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-times-circle"></i> Error!</h5>
        {{ Session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="bg-light rounded h-100 p-4">
        <div class="table-responsive">
            <div class="mb-3 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-primary rounded-pill m-3" data-bs-toggle="modal" data-bs-target="#buatPenarikan">
                    <i class="f	fas fa-dollar-sign"></i> Tambah
                </button>

                <div class="modal fade" id="buatPenarikan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Buat Pinjaman</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="card text-center">
                                    <div class="card-header">
                                        Informasi
                                    </div>
                                    <div class="card-body">
                                        Jumlah saldo anda saat ini adalah <span id="saldoText">0</span>.
                                    </div>
                                </div><br>
                                <form method="POST" action="{{ route('penarikan.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('id_anggota') is-invalid @enderror" id="id_anggota" name="id_anggota" required>
                                            <option value="">Pilih Anggota</option>
                                            @foreach($anggota as $member)
                                            <option value="{{ $member->id }}" data-saldo="{{ $member->saldo }}" {{ old('id_anggota') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="id_anggota">Anggota</label>
                                        @error('id_anggota')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('tanggal_penarikan') is-invalid @enderror" id="tanggal_penarikan" name="tanggal_penarikan" value="{{ old('tanggal_penarikan') }}" required>
                                        <label for="tanggal_pinjam">Tanggal Pinjam</label>
                                        @error('tanggal_penarikan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('jumlah_penarikan') is-invalid @enderror" id="jumlah_penarikan" name="jumlah_penarikan" value="{{ old('jumlah_penarikan') }}" required>
                                        <label for="jumlah_penarikan">Jumlah Penarikan</label>
                                        @error('jumlah_penarikan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror" placeholder="beri keterangan" name="keterangan" value="{{ old('keterangan') }}" id="keterangan" style="height: 150px;"></textarea>
                                        <label for="keterangan">Keterangan</label>
                                        @error('keterangan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Laporan Tanggal -->
                <div class="d-flex align-items-center ms-2">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('penarikan') }}" method="GET" class="d-flex align-items-center">
                        <input type="date" name="start_date" class="form-control me-2" value="{{ request()->get('start_date') }}" onchange="document.getElementById('reportForm').submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2" value="{{ request()->get('end_date') }}" onchange="document.getElementById('reportForm').submit()">
                    </form>
                    <a href="{{ route('penarikan.cetak', ['start_date' => request()->get('start_date'), 'end_date' => request()->get('end_date')]) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                </div>

                <!-- Form Pencarian -->
                <div class="d-flex align-items-center mr-2">
                    <form id="searchForm" action="{{ route('penarikan') }}" method="GET" class="input-group">
                        <div class="form-outline" data-mdb-input-init>
                            <input id="search-focus" type="search" name="search" id="form1" class="form-control" placeholder="Search" value="{{ request()->get('search') }}" />
                        </div>
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Kode Penarikan</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Nasabah</th>
                        <th scope="col">Jumlah Penarikan</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penarikan as $tarik)
                    <tr>
                        <td>{{ $tarik->kodeTransaksiPenarikan }}</td>
                        <td>{{ tanggal_indonesia($tarik->tanggal_penarikan) }}</td>
                        <td>{{ $tarik->name }}</td>
                        <td>Rp {{ number_format($tarik->jumlah_penarikan, 2, ',', '.') }}</td>
                        <td>{{ $tarik->keterangan }}</td>
                        <td>
                            <a href="{{ route('penarikan.edit', $tarik->penarikan_id) }}" class="btn btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('penarikan.destroy', $tarik->penarikan_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($penarikan->isEmpty())
            <p class="text-center">Tidak Ada Transaksi penarikan</p>
            @endif

            <div class="float-right">
                {{ $penarikan->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan skrip di akhir konten -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const anggotaSelect = document.getElementById('id_anggota');
        const saldoText = document.getElementById('saldoText');

        anggotaSelect.addEventListener('change', function() {
            const selectedOption = anggotaSelect.options[anggotaSelect.selectedIndex];
            const saldo = selectedOption.getAttribute('data-saldo') || 0;
            saldoText.textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(saldo);
        });
    });
</script>
@endsection