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
         <h2>Menu</h2>
         <a class="active" href="{{ route('admin.dashboard') }}">Dashboard</a>
         <a href="{{ route('reservasi.index') }}">Reservasi</a>
         <a href="{{ route('admin.pelanggan.index') }}">Pelanggan</a>
         <a href="{{ route('menu.index') }}">Menu</a>
         <a href="#">Meja</a>
         <a href="#">Laporan</a>
         <a href="#">Manajemen User</a>
     </div>
     <div class="main">
         <div class="information">
             <div class="card-info">
                 <h2>Total Reservasi Hari Ini</h2>
                 <h2>1</h2>
             </div>
             <div class="card-info">
                 <h2>Total Reservasi Minggu Ini</h2>
                 <h2>2</h2>
             </div>
             <div class="card-info">
                 <h2>Pelanggan Baru</h2>
                 <h2>4</h2>
             </div>
             <div class="card-info">
                 <h2>Pendapatan Bulanan</h2>
                 <h2 style="color: green;">4</h2>
             </div>
         </div>
         <div class="stat">
             <!-- Card 1: Reservasi -->
             <div class="card-stat">
                 <h2>Reservasi</h2>
                 <canvas id="reservasiChart"></canvas>
             </div>

            <div class="card-tabel">
                <h2>Menu Terlaris</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nama Menu</th>
                            <th>Jumlah Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Es Kopi Susu</td>
                            <td>120</td>
                        </tr>
                        <tr>
                            <td>Americano</td>
                            <td>85</td>
                        </tr>
                        <tr>
                            <td>Latte</td>
                            <td>95</td>
                        </tr>
                    </tbody>
                </table>
            </div>
         </div>
        <div class="title">
            <h2>Performa Pelayanan & Koki</h2>
        </div>
<div class="performa-section">
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jumlah Reservasi</th>
                <th>Rata-Rata Rating</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Koki A</td>
                <td>45</td>
                <td>4.5</td>
            </tr>
            <tr>
                <td>Pelayan B</td>
                <td>60</td>
                <td>4.2</td>
            </tr>
        </tbody>
    </table>

    <div class="performa-controls">
        <label for="bulan">Pilih Bulan:</label>
        <select id="bulan">
            <option>Jan</option>
            <option>Feb</option>
            <option>Mar</option>
            <option>Apr</option>
            <option>Mei</option>
            <option>Jun</option>
            <option>Jul</option>
            <option>Agu</option>
            <option>Sep</option>
            <option>Okt</option>
            <option>Nov</option>
            <option>Des</option>
        </select>
        <button>Export PDF</button>
    </div>
</div>
    </div>
<script>
    // Chart 1: Reservasi
    const reservasiCtx = document.getElementById('reservasiChart').getContext('2d');
    new Chart(reservasiCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Jumlah Reservasi per Bulan',
                data: [10, 12, 5, 8, 15, 9, 7, 13, 6, 11, 4, 10], // Dummy data
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
</script>

 </body>

 </html>
