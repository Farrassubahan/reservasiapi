<!DOCTYPE html>
<html>
<head>
    <title>Laporan Bulanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <h2>Laporan Bulan {{ $bulanDipilih }} Tahun {{ $tahunDipilih }}</h2>

    <p><strong>Total Reservasi:</strong> {{ $reservasiBulanIni }}</p>
    <p><strong>Pelanggan Baru:</strong> {{ $pelangganBaru }}</p>
    <p><strong>Total Pendapatan:</strong> Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>

    <h3>Menu Terlaris</h3>
    <table>
        <tr>
            <th>Nama Menu</th>
            <th>Jumlah Terjual</th>
        </tr>
        @foreach ($menuTerlaris as $item)
            <tr>
                <td>{{ $item->nama_menu }}</td>
                <td>{{ $item->total_terjual }}</td>
            </tr>
        @endforeach
    </table>

    <h3>Performa Koki</h3>
    <table>
        <tr>
            <th>Nama</th>
            <th>Jumlah Rating</th>
            <th>Rata-rata</th>
        </tr>
        @foreach ($koki as $k)
            <tr>
                <td>{{ $k['nama'] }}</td>
                <td>{{ $k['jumlah_rating'] }}</td>
                <td>{{ $k['rata_rating'] }}</td>
            </tr>
        @endforeach
    </table>

    <h3>Performa Pelayan</h3>
    <table>
        <tr>
            <th>Nama</th>
            <th>Jumlah Rating</th>
            <th>Rata-rata</th>
        </tr>
        @foreach ($pelayan as $p)
            <tr>
                <td>{{ $p['nama'] }}</td>
                <td>{{ $p['jumlah_rating'] }}</td>
                <td>{{ $p['rata_rating'] }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
