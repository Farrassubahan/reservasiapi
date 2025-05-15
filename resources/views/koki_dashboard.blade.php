<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Koki</title>
  @vite(['resources/css/koki.css', 'resources/js/app.js'])
</head>
<body>

<div class="sidebar">
    <h2></h2>
    <a href="#" class="active">Dashboard</a>
    <a href="#">Pesanan Masuk</a>
</div>
<div class="main">
    <div class="content">
        <h1>Dashboard Koki - Hari ini</h1>
    </div>
    <div class="information">
        <div class="card-info" style="background-color: red;">
            <h2>Belum Masuk</h2>
            <h2>5 Pesanan</h2>
        </div>
        <div class="card-info" style="background-color: yellow;">
            <h2>Masak</h2>
            <h2>5 Pesanan</h2>
        </div>
        <div class="card-info" style="background-color: green;">
            <h2>Selesai</h2>
            <h2>5 Pesanan</h2>
        </div>
    </div>
    <div class="pesanan">
    <h1>Pesanan Terbaru</h1>
    <table class="table-pesanan">
        <thead>
            <tr>
                <th>No</th>
                <th>Menu</th>
                <th>Porsi</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Nasi Goreng</td>
                <td>2</td>
                <td>10:15</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Mie Ayam</td>
                <td>1</td>
                <td>10:30</td>
            </tr>
        </tbody>
    </table>
</div>
</div>
</body>
</html>
