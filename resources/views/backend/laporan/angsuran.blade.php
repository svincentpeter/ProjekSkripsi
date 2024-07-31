<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kop Surat Koperasi Simpan Pinjam "Open Source"</title>
    <style>
        body {
            font-family: "Arial", Times, serif;
        }

        h1,
        h2,
        h4,
        h3,
        h5,
        p,
        h6 {
            text-align: center;
            margin: 0;
            line-height: 1.2;
        }

        .row {
            display: flex;
            margin-top: 10px;
        }

        .keclogo {
            font-size: 3vw;
        }

        .kablogo {
            font-size: 2vw;
        }

        .alamatlogo {
            font-size: 1.5vw;
        }

        .kodeposlogo {
            font-size: 1.7vw;
        }

        .garis1 {
            border-top: 3px solid black;
            height: 2px;
            border-bottom: 1px solid black;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        #logo {
            width: 100px;
            height: 100px;
        }

        #tls {
            text-align: right;
        }

        .alamat-tujuan {
            margin-left: 50%;
        }

        #tempat-tgl {
            margin-left: 120px;
        }

        #camat {
            text-align: center;
        }

        #nama-camat {
            margin-top: 50px;
            text-align: center;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            position: relative;
        }

        .table,
        .table th,
        .table td {
            border: 1px solid black;
            font-size: 14px;
        }

        .table th,
        .table td {
            padding: 10px;
            text-align: center;
        }

        #laporan-title {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
            line-height: 1;
        }

        .info {
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .info table {
            width: 100%;
            border: none;
        }

        .info th,
        .info td {
            text-align: left;
            padding: 5px;
            border: none;
            font-size: 14px;
        }

        .info th {
            width: 200px;
        }

        .total {
            text-align: left;

            font-size: 14px;
        }

        .total p {
            text-align: left;
            margin-top: 10px;
            font-size: 14px;
        }

        .total strong {

            font-size: 16px;
        }
    </style>
</head>

<body>
    <div>
        <header>
            <table width="100%">
                <tr>
                    <td width="15%" align="center">
                        <img src="https://pasla.jambiprov.go.id/wp-content/uploads/2023/02/lambang-koperasi.png" width="90%">
                    </td>
                    <td width="70%" align="center">
                        <h3>LAPORAN ANGSURAN PINJAMAN</h1>
                            <h4>KOPERASI SIMPAN PINJAM "OPEN SOURCE"</h1>
                                <p class="alamatlogo">Jl. A Yani No. 1 A Tambak Rejo, Wonodadi, Kec. Pringsewu</p>
                                <p class="kodeposlogo">Pringsewu, Lampung 35372</p>
                    </td>
                    <td width="15%" align="center">
                        <img src="https://kopkarindu.wordpress.com/wp-content/uploads/2014/05/koperasi-logo-baru-indonesia-vector.jpg" width="90%">
                    </td>
                </tr>
            </table>
            <hr class="garis1">
        </header><br>
        <div class="info">
            <table>
                <tr>
                    <th>NO. ANGGOTA</th>
                    <td>: {{ $anggota->anggota_id }}</td>
                </tr>
                <tr>
                    <th>NAMA ANGGOTA</th>
                    <td>: {{ $anggota->anggota_name }}</td>
                </tr>
                <tr>
                    <th>ALAMAT</th>
                    <td>: {{ $anggota->anggota_alamat }}</td>
                </tr>
                <tr>
                    <th>BESAR PINJAMAN</th>
                    <td>: {{ number_format($pinjaman->jml_pinjam, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>TANGGAL PENCAIRAN</th>
                    <td>: {{ tanggal_indonesia($pinjaman->tanggal_pinjam,false) }}</td>
                </tr>
                <tr>
                    <th>TANGGAL JATUH TEMPO</th>
                    <td>: {{ tanggal_indonesia($pinjaman->jatuh_tempo,false) }}</td>
                </tr>
                <tr>
                    <th>JATUH TEMPO</th>
                    <td>: {{ $pinjaman->jml_cicilan }} Bulan</td>
                </tr>
            </table>
        </div>

        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Angsuran Pinjaman</th>
                        <th>Tanggal Pembayaran</th>
                        <th>Angsuran (Rp.)</th>
                        <th>Bunga (Rp.)</th>
                        <th>Denda (Rp.)</th>
                        <th>Sisa Hutang Pokok (Rp.)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan as $index => $laporanItem)
                    <tr>
                        <td>Angsuran ke- {{ $index + 1 }}</td>
                        <td>{{ tanggal_indonesia($laporanItem->tanggal_angsuran,false) }}</td>
                        <td>Rp.{{ number_format($laporanItem->jml_angsuran, 2, ',', '.') }}</td>
                        <td>Rp.{{ number_format($laporanItem->bunga_pinjaman, 2, ',', '.') }}</td>
                        <td>Rp.{{ number_format($laporanItem->denda, 2, ',', '.') }}</td>
                        <td>Rp.{{ number_format($laporanItem->sisa_angsuran, 2, ',', '.') }}</td>
                        <td>
                            @if ($laporanItem->status_angsuran == 0)
                            <span>Belum Lunas</span>
                            @elseif ($laporanItem->status_angsuran == 1)
                            <span>Lunas</span>
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="total">
            <p>Jumlah Total Angsuran: <strong>{{ number_format($totalAngsuran, 2, ',', '.') }}</strong></p>
        </div>
    </div>
    <table width="100%">
        <tr>
            <td width="15%" align="center"><img src="" width="90%"></td>
            <td width="55%" align="center"><img src="" width="90%"></td>
            <td width="40%" align="center">

                <p class="alamatlogo">Pringsewu, {{ tanggal_indonesia(\Carbon\Carbon::now(), false) }}</p>
                <p class="kodeposlogo">Kepala Koperasi</p>
                <br><br><br>
                <p class="kodeposlogo">{{ auth()->user()->name}}</p>

            </td>
        </tr>
    </table>
</body>

</html>