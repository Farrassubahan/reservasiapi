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
<div class="layout">
  <div class="sidebar">
    <h2>Menu</h2>
    <a href="#">Dashboard</a>
    <a class="active" href="#">Reservasi</a>
    <a href="#">Pelanggan</a>
    <a href="#">Menu</a>
    <a href="#">Meja</a>
    <a href="#">Laporan</a>
    <a href="#">Manajemen User</a>
  </div>

  <div class="main">
    <div class="header">
        <div>
            <input type="search" placeholder="Search">
        </div>
        <div>
            <select>
                <option value="" disabled selected hidden>Status</option>
                <option value="selesai">Selesai</option>
                <option value="dimasak">Batal</option>
                <option value="dimasak">Aktif</option>
            </select>
        </div>
    </div>
    <div class="content-reservasi">
        <div class="header-row">
            <h2 class="col-name">Nama Pelanggan</h2>
            <h2 class="col-date">Tanggal & Waktu</h2>
            <h2 class="col-people">Jumlah Orang</h2>
            <h2 class="col-status">Status</h2>
            <h2 class="col-action">Aksi</h2>
        </div>

        <div class="card-reservasi">
            <div class="col-name">raden ajeng senopati niharga miharja kusuma sanagara indonesia</div>
            <div class="col-date">01 Mei 2024</div>
            <div class="col-people">10 orang</div>
            <div class="col-status">Selesai</div>
            <div class="col-action">
                <div class="dropdown">
                    <button class="dropdown-toggle">⋮</button>
                    <div class="dropdown-menu">
                    <button>Edit</button>
                    <button>Hapus</button>
                    <button>No Meja</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
  document.addEventListener('click', function(e) {
    const isToggle = e.target.matches('.dropdown-toggle');
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
      if (!menu.parentElement.contains(e.target)) {
        menu.style.display = 'none';
      }
    });

    if (isToggle) {
      const menu = e.target.nextElementSibling;
      menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }
  });
</script>
</body>
</html>
