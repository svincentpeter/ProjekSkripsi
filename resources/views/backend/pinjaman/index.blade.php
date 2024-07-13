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
                <button type="button" class="btn btn-outline-primary rounded-pill m-3" data-bs-toggle="modal" data-bs-target="#buatPinjaman">
                    <i class="f	fas fa-dollar-sign"></i> Tambah
                </button>


                <div class="modal fade" id="buatPinjaman" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        Jumlah maksimal pinjaman baru adalah Rp {{ number_format($maxPinjamanBaru, 0, ',', '.') }}.
                                    </div>
                                </div><br>
                                <form method="POST" action="{{ route('pinjaman.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="tanggal_pinjam">Tanggal Pinjam</label>
                                        <input type="date" class="form-control @error('tanggal_pinjam') is-invalid @enderror" id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam') }}" required>
                                        @error('tanggal_pinjam')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="jml_cicilan">Lama/bulan</label>
                                        <input type="number" class="form-control @error('jml_cicilan') is-invalid @enderror" id="jml_cicilan" name="jml_cicilan" value="{{ old('jml_cicilan') }}" required>
                                        @error('jml_cicilan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="jatuh_tempo">Jatuh Tempo</label>
                                        <input type="text" class="form-control @error('jatuh_tempo') is-invalid @enderror" id="jatuh_tempo" name="jatuh_tempo" value="{{ old('jatuh_tempo') }}" readonly>
                                        @error('jatuh_tempo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="jml_pinjam">Jumlah Pinjam</label>
                                        <input type="number" class="form-control @error('jml_pinjam') is-invalid @enderror" id="jml_pinjam" name="jml_pinjam" value="{{ old('jml_pinjam') }}" required>
                                        @error('jml_pinjam')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="id_anggota">Anggota</label>
                                        <select class="form-select @error('id_anggota') is-invalid @enderror" id="id_anggota" name="id_anggota" required>
                                            <option value="">Pilih Anggota</option>
                                            @foreach($anggota as $member)
                                            <option value="{{ $member->id }}" {{ old('id_anggota') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('id_anggota')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary  " data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success  ">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Laporan Tanggal -->
                <div class="d-flex align-items-center ms-2">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('pinjaman') }}" method="GET" class="d-flex align-items-center">
                        <input type="date" name="start_date" class="form-control me-2" value="{{ request()->get('start_date') }}" onchange="document.getElementById('reportForm').submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2" value="{{ request()->get('end_date') }}" onchange="document.getElementById('reportForm').submit()">

                    </form>
                    <a href="{{ route('pinjaman.cetak', ['start_date' => request()->get('start_date'), 'end_date' => request()->get('end_date')]) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                </div>

                <!-- Form Pencarian -->
                <div class="d-flex align-items-center mr-2">
                    <form id="searchForm" action="{{ route('pinjaman') }}" method="GET" class="input-group">
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
                        <th scope="col">Kode Pinjam</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Nasabah</th>
                        <th scope="col">Jumlah Dipinjam</th>
                        <th scope="col">Durasi</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pinjaman as $pinjam)
                    <tr>
                        <td>{{ $pinjam->kodeTransaksiPinjaman }}</td>
                        <td>{{ tanggal_indonesia($pinjam->tanggal_pinjam),false }}</td>
                        <td>{{ $pinjam->anggota_name }}</td>
                        <td>Rp {{ number_format($pinjam->jml_pinjam, 2, ',', '.') }}</td>
                        <td>{{ $pinjam->jml_cicilan  }} Bulan</td>
                        <td>
                            @if ($pinjam->status_pengajuan == 0)
                            <span class="text-primary">Dibuat</span>
                            @elseif ($pinjam->status_pengajuan == 1)
                            <span class="text-success">Disetujui</span>
                            @elseif ($pinjam->status_pengajuan == 3)
                            <span class="text-info">Selesai</span>
                            @else
                            <span class="text-danger">Ditolak</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('pinjaman.show', $pinjam->pinjaman_id) }}" class="btn btn-outline-info" title="Show">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('laporan.angsuran', $pinjam->pinjaman_id) }}" class="btn btn-outline-primary" title="cetak">
                                <i class="bi bi-printer-fill"></i>
                            </a>

                            <form action="{{ route('pinjaman.destroy', $pinjam->pinjaman_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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
            @if($pinjaman->isEmpty())
            <p class="text-center">Tidak Ada Transaksi Pinjaman</p>
            @endif

            <div class="float-right">
                {{ $pinjaman->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    // Menutup alert secara otomatis setelah 5 detik
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            new bootstrap.Alert(alert).close();
        });
    }, 5000);

    // Membuat animasi alert muncul di depan tabel
    $(document).ready(function() {
        $(".custom-alert").each(function(index) {
            $(this).delay(300 * index).fadeIn("slow");
        });
    });

    // Menghitung dan menampilkan jatuh tempo otomatis
    document.getElementById('jml_cicilan').addEventListener('input', function() {
        const tanggalPinjam = document.getElementById('tanggal_pinjam').value;
        const jmlCicilan = parseInt(this.value);

        if (tanggalPinjam && jmlCicilan) {
            // Mengubah tanggal pinjam menjadi objek Date
            const tanggalPinjamDate = new Date(tanggalPinjam);

            // Menambahkan jumlah bulan cicilan ke tanggal pinjam
            tanggalPinjamDate.setMonth(tanggalPinjamDate.getMonth() + jmlCicilan);

            // Mengubah kembali objek Date menjadi string dengan format yyyy-mm-dd
            const year = tanggalPinjamDate.getFullYear();
            const month = String(tanggalPinjamDate.getMonth() + 1).padStart(2, '0'); // Menambahkan 1 karena bulan di Date dimulai dari 0
            const day = String(tanggalPinjamDate.getDate()).padStart(2, '0');
            const jatuhTempo = `${year}-${month}-${day}`;

            // Menampilkan jatuh tempo di input jatuh_tempo
            document.getElementById('jatuh_tempo').value = jatuhTempo;
        }
    });

    // Memperbarui jatuh tempo saat tanggal pinjam diubah
    document.getElementById('tanggal_pinjam').addEventListener('change', function() {
        const jmlCicilan = parseInt(document.getElementById('jml_cicilan').value);
        const tanggalPinjam = this.value;

        if (tanggalPinjam && jmlCicilan) {
            // Mengubah tanggal pinjam menjadi objek Date
            const tanggalPinjamDate = new Date(tanggalPinjam);

            // Menambahkan jumlah bulan cicilan ke tanggal pinjam
            tanggalPinjamDate.setMonth(tanggalPinjamDate.getMonth() + jmlCicilan);

            // Mengubah kembali objek Date menjadi string dengan format yyyy-mm-dd
            const year = tanggalPinjamDate.getFullYear();
            const month = String(tanggalPinjamDate.getMonth() + 1).padStart(2, '0'); // Menambahkan 1 karena bulan di Date dimulai dari 0
            const day = String(tanggalPinjamDate.getDate()).padStart(2, '0');
            const jatuhTempo = `${year}-${month}-${day}`;

            // Menampilkan jatuh tempo di input jatuh_tempo
            document.getElementById('jatuh_tempo').value = jatuhTempo;
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tanggalPinjamInput = document.getElementById('tanggal_pinjam');
        const jmlCicilanInput = document.getElementById('jml_cicilan');
        const jatuhTempoInput = document.getElementById('jatuh_tempo');

        function updateJatuhTempo() {
            const tanggalPinjam = tanggalPinjamInput.value;
            const jmlCicilan = parseInt(jmlCicilanInput.value);

            if (tanggalPinjam && jmlCicilan) {
                const tanggalPinjamDate = new Date(tanggalPinjam);
                tanggalPinjamDate.setMonth(tanggalPinjamDate.getMonth() + jmlCicilan);

                const year = tanggalPinjamDate.getFullYear();
                const month = String(tanggalPinjamDate.getMonth() + 1).padStart(2, '0');
                const day = String(tanggalPinjamDate.getDate()).padStart(2, '0');
                const jatuhTempo = `${year}-${month}-${day}`;

                jatuhTempoInput.value = jatuhTempo;
            }
        }

        jmlCicilanInput.addEventListener('input', updateJatuhTempo);
        tanggalPinjamInput.addEventListener('change', updateJatuhTempo);
    });
</script>


@endsection