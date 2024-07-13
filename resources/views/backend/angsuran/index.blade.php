@extends('backend.app')

@section('title', 'Data Angsuran')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4">Data Angsuran</h2>

    <!-- Alert Success -->
    @if(Session::has('message'))
    <div id="successAlert" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-check-circle"></i> Sukses!</h5>
        {{ Session('message') }}
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
                



                <!-- Form Laporan Tanggal -->
                <div class="d-flex align-items-center ms-2">
                    <span class="me-2">Report</span>
                    <form id="reportForm" action="{{ route('angsuran') }}" method="GET" class="d-flex align-items-center">
                        <input type="date" name="start_date" class="form-control me-2" value="{{ request()->get('start_date') }}" onchange="document.getElementById('reportForm').submit()">
                        <span class="me-2">To</span>
                        <input type="date" name="end_date" class="form-control me-2" value="{{ request()->get('end_date') }}" onchange="document.getElementById('reportForm').submit()">

                    </form>
                    <a href="{{ route('angsuran.cetak', ['start_date' => request()->get('start_date'), 'end_date' => request()->get('end_date')]) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i>
                    </a>
                </div>

                <!-- Form Pencarian -->
                <div class="d-flex align-items-center ms-2">
                    <form action="{{ route('angsuran') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari Kode Transaksi/Nama Anggota" value="{{ request()->get('search') }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Kode Angsuran</th>
                        <th scope="col">Cicikan ke</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Nasabah</th>
                        <th scope="col">Pinjaman Pokok</th>

                        <th scope="col">Jumlah Angsuran</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($angsuran as $angs)
                    <tr>
                        <td>{{ $angs->kode_transaksi_angsuran }}</td>
                        <td>{{ $angs->angsuran_ke }}</td>
                        <td>{{ $angs->tanggal_angsuran }}</td>
                        <td>{{ $angs->nasabah }}</td>
                        <td>Rp {{ number_format($angs->pinjaman_pokok, 0, ',', '.') }}</td>
                        <!-- <td>Rp {{ number_format($angs->bunga, 0, ',', '.') }}</td> -->
                        <td>Rp {{ number_format($angs->jml_angsuran, 0, ',', '.') }}</td>
                        <td>
                            @if ($angs->status == 0)
                            <span class="text-warning">Belum Lunas</span>
                            @elseif ($angs->status == 1)
                            <span class="text-success">Lunas</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('pinjaman.show', $angs->angsuran_id) }}" class="btn btn-outline-info" title="Show">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('angsuran.edit', $angs->angsuran_id) }}" class="btn btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Form for delete action -->
                            <form action="{{ route('angsuran.destroy', $angs->angsuran_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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
            @if($angsuran->isEmpty())
            <p class="text-center">Tidak Ada Data Angsuran</p>
            @endif

            <!-- Pagination Links -->
            <div class="float-right">
                {{ $angsuran->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script untuk menutup alert secara otomatis -->
<script>
    // Menutup alert secara otomatis setelah 5 detik
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            new bootstrap.Alert(alert).close();
        });
    }, 5000); // 5000 milidetik = 5 detik

    // Membuat animasi alert muncul di depan tabel
    $(document).ready(function() {
        $(".custom-alert").each(function(index) {
            $(this).delay(300 * index).fadeIn("slow");
        });
    });
</script>
@endsection