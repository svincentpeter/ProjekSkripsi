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
                @can('pinjaman-create')
                <button type="button" class="btn btn-outline-primary rounded-pill m-3" data-bs-toggle="modal" data-bs-target="#buatPinjaman">
                    <i class="f	fas fa-dollar-sign"></i> Tambah
                </button>
                @endcan
                @include('backend.pinjaman.modal.modalCreate')
                @include('backend.pinjaman.modal.modalEdit')

                <!-- Form Laporan Tanggal -->
                <div class="d-flex align-items-center ms-2">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('pinjaman') }}" method="GET" class="d-flex align-items-center">
                        <input type="date" name="start_date" class="form-control me-2" value="{{ request()->get('start_date') }}" onchange="document.getElementById('reportForm').submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2" value="{{ request()->get('end_date') }}" onchange="document.getElementById('reportForm').submit()">

                    </form>
                    @can('laporan_pinjaman')
                    <a href="{{ route('pinjaman.cetak', ['start_date' => request()->get('start_date'), 'end_date' => request()->get('end_date')]) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                    @endcan
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

                            <!-- Edit Button -->
                            @can('pinjaman-edit')
                            <button type="button" class="btn btn-outline-warning btn-sm m-1" data-bs-toggle="modal" data-bs-target="#editPinjaman" data-id="{{ $pinjam->pinjaman_id }}" data-tanggal_pinjam="{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('Y-m-d') }}" data-jml_pinjam="{{ $pinjam->jml_pinjam }}" data-jml_cicilan="{{ $pinjam->jml_cicilan }}" data-jatuh_tempo="{{ \Carbon\Carbon::parse($pinjam->jatuh_tempo)->format('Y-m-d') }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            @endcan
                            @can('pinjaman-detail')
                            <a href="{{ route('pinjaman.show', $pinjam->pinjaman_id) }}" class="btn btn-outline-info" title="Show">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan
                            @can('laporan_angsuran')
                            <a href="{{ route('laporan.angsuran', $pinjam->pinjaman_id) }}" class="btn btn-outline-primary" title="cetak">
                                <i class="bi bi-printer-fill"></i>
                            </a>
                            @endcan
                            @can('pinjaman-delete')
                            <form action="{{ route('pinjaman.destroy', $pinjam->pinjaman_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endcan
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