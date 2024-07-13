    @extends('backend.app')
    @section('title', 'Detail Pinjaman')
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
            <h4>Informasi Pinjaman</h4>
            <a href="{{ route('terima_pengajuan', $pinjaman->pinjaman_id)  }}" class="btn btn-outline-sm btn-success">Terima</a>
            <button type="button" class="btn btn-outline-sm btn-danger" data-bs-toggle="modal" data-bs-target="#tolakpengajuan">
                Tolak Pengajuan
            </button>
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th scope="row">Kode Pinjaman</th>
                                <td>{{ $pinjaman->kodeTransaksiPinjaman }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Nama Nasabah</th>
                                <td>{{ $pinjaman->anggota_name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Tanggal Pinjam</th>
                                <td>{{ tanggal_indonesia($pinjaman->tanggal_pinjam, false)}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Tanggal Jatuh Tempo</th>
                                <td>{{ tanggal_indonesia($pinjaman->jatuh_tempo, false)}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Pinjaman Pokok</th>
                                <td>Rp {{ number_format($pinjaman->jml_pinjam, 0, ',', '.') }}</td>
                            </tr>

                        </tbody>
                    </table>

                </div>
                <div class="col-md-6">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th scope="row">Jumlah Cicilan</th>
                                <td>{{ $pinjaman->jml_cicilan }} Bulan</td>
                            </tr>
                            <tr>
                                <th scope="row">Sisa Pinjaman</th>
                                <td>Rp {{ number_format($pinjaman->sisa_pinjam, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Jumlah Pinjaman Dengan Bunga</th>
                                <td>Rp {{ number_format($pinjaman->total_pinjaman_dengan_bunga, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Status Pengajuan</th>
                                <td>
                                    @if ($pinjaman->status_pengajuan == 0)
                                    <span class="text-primary">Dibuat</span>
                                    @elseif ($pinjaman->status_pengajuan == 1)
                                    <span class="text-success">Disetujui</span>
                                    @elseif ($pinjaman->status_pengajuan == 3)
                                    <span class="text-info">Selesai</span>
                                    @else
                                    <span class="text-danger">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Dibuat Oleh</th>
                                <td>{{ $pinjaman->created_by_name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>

                <!-- Modal Tolak Pengajuan -->
                <div class="modal fade" id="tolakpengajuan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Keterangan Tolak Pengajuan</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" action="{{ route('tolak_pengajuan', ['id' => $pinjaman->pinjaman_id]) }}">
                                @csrf
                                <div class="modal-body">
                                    <textarea name="catatan" id="catatan" class="form-control" rows="5" placeholder="Alasan Penolakan" required></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-outline-danger">Tolak</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Bayar Angsuran -->
                <div class="modal fade " id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Angsuran</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form untuk membayar angsuran -->
                                <form action="{{ route('angsuran.bayar', $pinjaman->pinjaman_id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="tanggal_angsuran">Tanggal Angsuran:</label>
                                        <input type="date" class="form-control" id="tanggal_angsuran" name="tanggal_angsuran" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="jml_angsuran">Jumlah Angsuran:</label>
                                        <input type="number" class="form-control" id="jml_angsuran" name="jml_angsuran" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="bunga_angsuran">Bunga Angsuran 2%:</label>
                                        <input type="number" class="form-control" id="bunga_angsuran" name="bunga_angsuran" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                                        <input class="form-control form-control-sm" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" type="file" required>
                                        @error('bukti_pembayaran')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                        <img id="image-preview" src="#" alt="Image Preview" style="display: none; max-width: 50%; height: auto; margin-top: 10px;">
                                        <div id="crop-container" style="width: 100%; max-height: 70vh; overflow: hidden; display: none;">
                                            <img id="crop-image" src="#" alt="Crop Image" style="max-width: 50%; height: auto;">
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary mt-2" id="crop-button" style="display: none;">Crop Image</button>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-outline-primary">Bayar Angsuran</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Angsuran -->

        <div class="card mt-3">
            <div class="card-header">
                <h5>Daftar Angsuran</h5>
            </div>
            <div class="card-body">

                <div>

                </div>
                <button type="button" class="btn btn-outline-primary rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                    Bayar Angsuran
                </button>

                <table class="table table-bordered mt-3">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Angsuran</th>
                            <th>Tanggal Angsuran</th>
                            <th>Sisa Hutang Pokok</th>
                            <th>Bunga</th>
                            <th>Cicilan Ke-</th>
                            <th>Status</th>
                            <th>Total Angsuran</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($angsuran as $ang)
                        <tr>
                            <td>{{ $ang->kodeTransaksiAngsuran }}</td>
                            <td>{{ tanggal_indonesia($ang->tanggal_angsuran, false) }}</td>
                            <td>Rp {{ number_format($ang->sisa_angsuran, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($ang->bunga_pinjaman, 0, ',', '.') }}</td>
                            <td>{{ $ang->cicilan }}</td>
                            <td>
                                @if ($ang->status == 0)
                                <span class="text-warning">Belum Lunas</span>
                                @elseif ($ang->status == 1)
                                <span class="text-success">Lunas</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($ang->total_angsuran_dengan_bunga, 0, ',', '.') }}</td>
                        </tr>

                        @endforeach
                    </tbody>
                    <tr>
                        <th colspan="6">Total Angsuran</th>
                        <th>Rp {{ number_format($total_angsuran, 0, ',', '.') }}</th>
                    </tr>
                </table>
                {{ $angsuran->links() }} <!-- Pagination links -->
            </div>
        </div>
    </div>

    <script>
        let cropper;
        document.getElementById('bukti_pembayaran').addEventListener('change', function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('crop-image');
                output.src = reader.result;
                document.getElementById('crop-container').style.display = 'block';

                // Initialize cropper
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(output, {
                    aspectRatio: 1,
                    viewMode: 1,
                    scalable: true,
                    zoomable: true,
                });
                document.getElementById('crop-button').style.display = 'inline-block';
            };
            reader.readAsDataURL(event.target.files[0]);
        });

        document.getElementById('crop-button').addEventListener('click', function() {
            var canvas = cropper.getCroppedCanvas();
            var output = document.getElementById('image-preview');
            output.src = canvas.toDataURL();
            output.style.display = 'block';

            document.getElementById('crop-container').style.display = 'none';
            document.getElementById('crop-button').style.display = 'none';
        });
    </script>

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

    <script>
        document.getElementById('jml_angsuran').addEventListener('input', function() {
            var jmlAngsuran = parseFloat(this.value);
            var bungaAngsuran = jmlAngsuran * 0.02; // 2% bunga
            document.getElementById('bunga_angsuran').value = bungaAngsuran.toFixed(2);
        });
    </script>

    @endsection