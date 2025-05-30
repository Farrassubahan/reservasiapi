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
                 <h2>{{ $reservasiHariIni->count() }}</h2>
             </div>
             <div class="card-info">
                 <h2>Total Reservasi Minggu Ini</h2>
                 <h2>{{ $reservasiMingguIni->count() }}</h2>
             </div>
             <div class="card-info">
                 <h2>Pelanggan Baru</h2>
                 <h2>{{ $pelangganBaru }}</h2>
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
                         @foreach ($menuTerlaris as $item)
                             <tr>
                                 <td>{{ $item->nama_menu }}</td>
                                 <td>{{ $item->total_terjual }}</td>
                             </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>
         </div>
         <div class="title">
             <h2>Performa Pelayanan & Koki</h2>
         </div>
         <div class="performa-section" style="width: fit-content; margin-left: 50;">
             <h2>Performa Koki</h2>
             <table>
                 <thead>
                     <tr>
                         <th>Nama</th>
                         <th>Jumlah Pesanan</th>
                         <th>Rata-Rata Rating</th>
                     </tr>
                 </thead>
                 <tbody>
                     @forelse ($koki as $pengguna)
                         <tr>
                             <td>{{ $pengguna['nama'] }}</td>
                             <td>{{ $pengguna['jumlah_pesanan'] }}</td>
                             <td>{{ $pengguna['rata_rating'] !== null ? $pengguna['rata_rating'] : '-' }}</td>
                         </tr>
                     @empty
                         <tr>
                             <td colspan="3">Tidak ada data koki.</td>
                         </tr>
                     @endforelse
                 </tbody>
             </table>

             <br>

             <h2>Performa Pelayan</h2>
             <table>
                 <thead>
                     <tr>
                         <th>Nama</th>
                         <th>Jumlah Reservasi</th>
                         <th>Rata-Rata Rating</th>
                     </tr>
                 </thead>
                 <h1>pelayan nih bos</h1>
             </table>
         </div>
         <div class="performa-controls">
             {{-- <label for="bulan">Pilih Bulan:</label> --}}
             <form method="GET" action="{{ route('admin.laporan') }}">
                 <label for="bulan">Pilih Bulan:</label>
                 <select name="bulan" id="bulan" onchange="this.form.submit()">
                     <option value="1" {{ $bulanDipilih == 1 ? 'selected' : '' }}>Jan</option>
                     <option value="2" {{ $bulanDipilih == 2 ? 'selected' : '' }}>Feb</option>
                     <option value="3" {{ $bulanDipilih == 3 ? 'selected' : '' }}>Mar</option>
                     <option value="4" {{ $bulanDipilih == 4 ? 'selected' : '' }}>Apr</option>
                     <option value="5" {{ $bulanDipilih == 5 ? 'selected' : '' }}>Mei</option>
                     <option value="6" {{ $bulanDipilih == 6 ? 'selected' : '' }}>Jun</option>
                     <option value="7" {{ $bulanDipilih == 7 ? 'selected' : '' }}>Jul</option>
                     <option value="8" {{ $bulanDipilih == 8 ? 'selected' : '' }}>Agu</option>
                     <option value="9" {{ $bulanDipilih == 9 ? 'selected' : '' }}>Sep</option>
                     <option value="10" {{ $bulanDipilih == 10 ? 'selected' : '' }}>Okt</option>
                     <option value="11" {{ $bulanDipilih == 11 ? 'selected' : '' }}>Nov</option>
                     <option value="12" {{ $bulanDipilih == 12 ? 'selected' : '' }}>Des</option>
                 </select>
             </form>


             <button>Export PDF</button>
         </div>
     </div>
     </div>
     <script>
         const reservasiCtx = document.getElementById('reservasiChart').getContext('2d');

         const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

         new Chart(reservasiCtx, {
             type: 'bar',
             data: {
                 labels: labels,
                 datasets: [{
                     label: 'Jumlah Reservasi per Bulan',
                     data: @json($totalsPerBulan),
                     backgroundColor: 'rgba(54, 162, 235, 0.5)',
                     borderColor: 'rgba(54, 162, 235, 1)',
                     borderWidth: 1
                 }]
             },
             options: {
                 responsive: true,
                 scales: {
                     y: {
                         beginAtZero: true,
                         stepSize: 1,
                         precision: 0
                     }
                 }
             }
         });
     </script>

 </body>

 </html>
