<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kop Surat Koperasi Simpan Pinjam "Open Source"</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
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
            width: 140px;
            height: 160px;
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

        .table,
        .table th,
        .table td {
            border: 1px solid black;
        }

        .table th,
        .table td {
            padding: 10px;
            text-align: left;
        }

        #laporan-title {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
            line-height: 1;
        }
    </style>
</head>
<body>
    <header>
        <table width="100%">
            <tr>
                <td width="15%" align="center">
                    <img src="https://pasla.jambiprov.go.id/wp-content/uploads/2023/02/lambang-koperasi.png" width="90%">
                </td>
                <td width="70%" align="center">
                    <h3>LAPORAN PENARIKAN</h3>
                    <h4>KOPERASI SIMPAN PINJAM "OPEN SOURCE"</h4>
                    <p class="alamatlogo">Jl. A Yani No. 1 A Tambak Rejo, Wonodadi, Kec. Pringsewu</p>
                    <p class="kodeposlogo">Pringsewu, Lampung 35372</p>
                </td>
                <td width="15%" align="center">
                    <img src="https://kopkarindu.wordpress.com/wp-content/uploads/2014/05/koperasi-logo-baru-indonesia-vector.jpg" width="90%">
                </td>
            </tr>
        </table>
        <hr class="garis1">
    </header>

    <div id="laporan-title">
        <h4>Laporan Penarikan</h4>
        <p>Periode: {{ tanggal_indonesia($startDate, false) }} – {{ tanggal_indonesia($endDate, false) }}</p>
    </div>

    <div class="table-container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nasabah</th>
                    <th>Kode Penarikan</th>
                    <th>Jumlah Penarikan</th>
                    <th>Sisa Saldo</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penarikan as $tarik)
                <tr>
                    <td>{{ tanggal_indonesia($tarik->tanggal_penarikan, false) }}</td>
                    <td>{{ $tarik->anggota_name }}</td>
                    <td>{{ $tarik->kode_transaksi }}</td>
                    <td>Rp {{ number_format($tarik->jumlah_penarikan, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($tarik->anggota_saldo, 2, ',', '.') }}</td>
                    <td>{{ $tarik->keterangan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak Ada Data Penarikan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <footer>
        <table width="100%">
            <tr>
                <td width="60%"></td>
                <td width="40%" align="center">
                    <p class="alamatlogo">Pringsewu, {{ tanggal_indonesia(\Carbon\Carbon::now(), false) }}</p>
                    <p class="kodeposlogo">Kepala Koperasi</p><br><br><br>
                    <p class="kodeposlogo">{{ auth()->user()->name }}</p>
                </td>
            </tr>
        </table>
    </footer>
</body>
</html>