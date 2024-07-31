@extends('backend.app')

@section('title', 'Home')

@section('content')
<!-- Sale & Revenue Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-user-friends fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Anggota</p>
                    <h6 class="mb-0">{{$counttotalanggota}}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-piggy-bank fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Simpanan</p>
                    <h6 class="mb-0">Rp {{ number_format($countSimpanan, 2, ',', '.') }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-dollar-sign fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Pinjaman</p>
                    <h6 class="mb-0">Rp {{ number_format($countpinjaman, 2, ',', '.') }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-money-bill-wave fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Penarikan</p>
                    <h6 class="mb-0">Rp {{ number_format($countpenarikan, 2, ',', '.') }}</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-8">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">Transaksi Koperasi Simpan Pinjam</h6>
                <canvas id="transaksiKoperasi" width="608" height="302" style="display: block; box-sizing: border-box; height: 223.704px; width: 450.37px;"></canvas>
            </div>
        </div>

        <div class="col-sm-12 col-xl-4">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">Status Anggota</h6>
                <canvas id="doughnut-chart" width="608" height="608" style="display: block; box-sizing: border-box; height: 450.37px; width: 450.37px;"></canvas>
            </div>
        </div>
    </div>
</div>
<!-- Chart Section End -->
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data anggota dari controller
    var aktif = @json($aktif);
    var nonAktif = @json($nonAktif);

    // Membuat Doughnut Chart
    var ctx6 = document.getElementById("doughnut-chart").getContext("2d");
    var myChart6 = new Chart(ctx6, {
        type: "doughnut",
        data: {
            labels: ["Aktif", "Non-Aktif"],
            datasets: [{
                backgroundColor: [
                    "rgba(0, 100, 0, .7)", // Hijau untuk Aktif
                    "rgba(255, 0, 0, .7)" // Merah untuk Non-Aktif
                ],
                data: [aktif, nonAktif]
            }]
        },
        options: {
            responsive: true
        }
    });
    // Data transaksi dari controller
    var years = @json($chartData['years']);
    var simpananData = @json($chartData['simpanan']);
    var pinjamanData = @json($chartData['pinjaman']);
    var penarikanData = @json($chartData['penarikan']);

    // Membuat Bar Chart untuk Transaksi
    var ctx1 = document.getElementById("transaksiKoperasi").getContext("2d");
    var myChart1 = new Chart(ctx1, {
        type: "bar",
        data: {
            labels: years,
            datasets: [{
                    label: "Simpanan",
                    data: simpananData,
                    backgroundColor: "rgba(0, 156, 255, .7)"
                },
                {
                    label: "Pinjaman",
                    data: pinjamanData,
                    backgroundColor: "rgba(0, 156, 255, .5)"
                },
                {
                    label: "Penarikan",
                    data: penarikanData,
                    backgroundColor: "rgba(0, 156, 255, .3)"
                }
            ]
        },
        options: {
            responsive: true
        }
    });
</script>
@endsection