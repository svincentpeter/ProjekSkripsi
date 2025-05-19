<table>
    <thead>
        <tr>
            <th>Kode Transaksi</th>
            <th>Tanggal Penarikan</th>
            <th>Nama Anggota</th>
            <th>Jumlah Penarikan</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($penarikan as $item)
        <tr>
            <td>{{ $item->kode_transaksi }}</td>
            <td>{{ tanggal_indonesia($item->tanggal_penarikan, false) }}</td>
            <td>{{ $item->anggota_name ?? $item->name ?? '-' }}</td>
            <td>{{ $item->jumlah_penarikan }}</td>
            <td>{{ $item->keterangan }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
