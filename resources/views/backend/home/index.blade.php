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
                    <h6 class="mb-0">{{ $counttotalanggota }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-chart-bar fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Simpanan</p>
                    <h6 class="mb-0">{{ $countSimpanan }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-chart-area fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Penarikan</p>
                    <h6 class="mb-0">{{ $countpenarikan }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-chart-pie fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Pinjaman</p>
                    <h6 class="mb-0">{{ $countpinjaman }}</h6>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6">
            <div class="bg-light text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Worldwide Sales</h6>
                    <a href="">Show All</a>
                </div>
                <canvas id="worldwide-sales" width="634" height="316" style="display: block; box-sizing: border-box; height: 210.667px; width: 422.667px;"></canvas>
            </div>
        </div>
        <div class="col-sm-12 col-xl-4">
            <div class="bg-light text-center rounded p-4">
                <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $.get("{{ route('chart.data') }}", function(data) {
            var years = data.years;
            var simpanan = data.simpanan;
            var penarikan = data.penarikan;
            var pinjaman = data.pinjaman;

            // Worldwide Sales Chart
            var ctx1 = $("#worldwide-sales").get(0).getContext("2d");
            var myChart1 = new Chart(ctx1, {
                type: "bar",
                data: {
                    labels: years,
                    datasets: [{
                            label: "Simpanan",
                            data: simpanan,
                            backgroundColor: "rgba(0, 156, 255, .7)"
                        },
                        {
                            label: "Penarikan",
                            data: penarikan,
                            backgroundColor: "rgba(0, 156, 255, .5)"
                        },
                        {
                            label: "Pinjaman",
                            data: pinjaman,
                            backgroundColor: "rgba(0, 156, 255, .3)"
                        }
                    ]
                },
                options: {
                    responsive: true
                }
            });

            // Other Chart
            var xValues = ["Italy", "France", "Spain", "USA", "Argentina"];
            var yValues = [55, 49, 44, 24, 15];
            var barColors = [
                "#b91d47",
                "#00aba9",
                "#2b5797",
                "#e8c3b9",
                "#1e7145"
            ];

            new Chart("myChart", {
                type: "doughnut",
                data: {
                    labels: xValues,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: "World Wide Wine Production 2018"
                    }
                }
            });
        });
    });
</script>
@endsection