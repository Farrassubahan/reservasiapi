 <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Admin</title>
    @vite(['resources/css/koki.css', 'resources/js/app.js'])
</head>

<body>
    <div class="sidebar">
        <h2></h2>
        <a href="#">Dashboard</a>
        <a href="#">Reservasi</a>
        <a href="#">Pelanggan</a>
        <a href="#">Menu</a>
        <a href="#">Meja</a>
        <a href="#">Laporan</a>
        <a href="#">Manajemen User</a>
    </div>
    <div class="main">
        <div class="head">
            <input type="search" placeholder="Search">
        </div>
        <div class="information">
            <div class="card-info">
                <h2>Reservasi Hari Ini</h2>
                <h2>{{ $hariIni }}</h2>
            </div>
            <div class="card-info">
                <h2>Reservasi Minggu Ini</h2>
                <h2>{{ $mingguIni }}</h2>
            </div>
            <div class="card-info">
                <h2>Reservasi Bulan Ini</h2>
                <h2>{{ $bulanIni }}</h2>
            </div>
        </div>
        <div class="stat">
            <!-- Card 1: Reservasi -->
            <div class="card-stat">
                <h2>Reservasi</h2>
                <canvas id="reservasiChart"></canvas>
            </div>

            <!-- Card 2: Status Reservasi -->
            <div class="card-stat">
                <h2>Status Reservasi</h2>
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        <h1 style="margin-top: 2rem">Daftar Reservasi Hari Ini</h1>
        <div class="table-pesanan" style="margin: 2rem 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>nama user</th>
                        <th>Kode Reservasi</th>
                        <th>Sesi</th>
                        <th>Tanggal</th>
                        <th>Jumlah Orang</th>
                        <th>Status</th>
                       
                        <th>Aksi</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataReservasiHariIniLengkap as $reservasi)
                        <tr>
                            <td>{{ $reservasi->pengguna->nama }}</td>
                            <td>{{ $reservasi->kode_reservasi }}</td>
                            <td>{{ $reservasi->sesi }}</td>
                            <td>{{ $reservasi->tanggal }}</td>
                            <td>{{ $reservasi->jumlah_tamu }}</td>
                            <td>{{ ucfirst($reservasi->status) }}</td>
                            <td>
                                <!-- Hapus -->
                                <form action="{{ route('admin.reservasi.hapus', $reservasi->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Yakin ingin menghapus reservasi ini?')"
                                        style="border: none; background: none; cursor: pointer;">
                                        <i class="fa-solid fa-trash text-red-600"></i>
                                    </button>
                                </form>

                                <!-- Edit -->
                                <a href="{{ route('admin.reservasi.edit', $reservasi->id) }}">
                                    <i class="fa-solid fa-pencil text-blue-600"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center;">Tidak ada reservasi hari ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
    <script>
        // Chart 1: Reservasi
        const reservasiCtx = document.getElementById('reservasiChart').getContext('2d');
        new Chart(reservasiCtx, { 
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!}, // ['Jan', 'Feb', ...]
                datasets: [{
                    label: 'Jumlah Reservasi per Bulan',
                    data: {!! json_encode($data) !!}, // [10, 12, 5, 0, ...]
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Chart 2: Status Reservasi
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Menunggu', 'Diterima', 'Dibatalkan', 'Selesai'],
                datasets: [{
                    label: 'Status',
                    data: [
                        {{ $statusMenunggu }},
                        {{ $statusDiterima }},
                        {{ $statusDibatalkan }},
                        {{ $statusSelesai }}
                    ],
                    backgroundColor: [
                        'rgba(255, 205, 86, 0.7)', // Menunggu
                        'rgba(75, 192, 192, 0.7)', // Diterima
                        'rgba(255, 99, 132, 0.7)', // Dibatalkan
                        'rgba(75, 192, 75, 0.7)' // Selesai
                    ],
                    borderColor: [
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>

</html>
