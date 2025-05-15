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
    <a href="#">Dashboard</a>
    <a href="#" class="active">Pesanan Masuk</a>
</div>
    <div class="main">
        <div class="head">
            <h1>Daftar Pesanan Masuk</h1>
            <select>
                <option value="selesai">Selesai</option>
                <option value="dimasak">Dimasak</option>
            </select>
        </div>
    <div class="pesanan">
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
