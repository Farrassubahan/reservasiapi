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
    <a href="#">Reservasi</a>
    <a href="#">Pelanggan</a>
    <a class="active" href="#">Menu</a>
    <a href="#">Meja</a>
    <a href="#">Laporan</a>
    <a href="#">Manajemen User</a>
  </div>

  <div class="main">
    <div class="header-menu">
        <div class="data-menu">Data Menu</div>
        <button class="tambah-menu" id="tambahMenuBtn">Tambah Menu</button>
    </div>
    <div class="content-menu">
        <div class="header-row-menu" style="text-align: center">
            <h2 class="col-gambar">Gambar</h2>
            <h2 class="col-name-menu">Nama Menu</h2>
            <h2 class="col-harga">Harga</h2>
            <h2 class="col-kategori">Kategori</h2> 
            <h2 class="col-stok">Stok</h2>
            <h2 class="col-action">Aksi</h2>
        </div>
        <div class="card-menu" style="text-align: center">
            <div class="col-gambar"><img src="{{ asset('image/download (3).jpg') }}" alt="gambar" class="gambar-seragam"></div>
            <div class="col-name-menu">Cumi Kesukaan Pria</div>
            <div class="col-harga">10.000</div>
            <div class="col-kategori">Makanan</div>
            <div class="col-stok">10</div>
            <div class="col-action">
                <div class="dropdown"><i class="fa-solid fa-trash text-red-600"></i><i class="fa-solid fa-pencil text-blue-600"></i></div>
            </div>
        </div>
                <div class="card-menu" style="text-align: center">
            <div class="col-gambar"><img src="{{ asset('image/download (3).jpg') }}" alt="gambar" class="gambar-seragam"></div>
            <div class="col-name-menu">Cumi Kesukaan Pria</div>
            <div class="col-harga">10.000</div>
            <div class="col-kategori">Makanan</div>
            <div class="col-stok">10</div>
            <div class="col-action">
                <div class="dropdown"><i class="fa-solid fa-trash text-red-600"></i><i class="fa-solid fa-pencil text-blue-600"></i></div>
            </div>
        </div>
        <div class="modal" id="formModal">
        <div class="modal-content">
            <span class="close-button" onclick="tutupForm()">&times;</span>
            <h2>Tambah Menu</h2>

            <form id="menuForm">
            <label for="nama">Nama Menu</label>
            <input type="text" id="nama" name="nama" required>

            <label for="harga">Harga</label>
            <input type="number" id="harga" name="harga" required>

            <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required style="width: 29rem; height: 3rem;">
                    <option value="">Pilih Kategori</option>
                    <option value="makanan">Makanan</option>
                    <option value="minuman">Minuman</option>
                </select>


            <div class="horizontal-group">
                <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" id="stok" name="stok" required>
                </div>
                <div class="form-group">
                <label for="gambar">Gambar</label>
                <div class="image-upload-row">
                <div class="image-preview" id="imagePreview" onclick="document.getElementById('gambar').click()">
                    <span>Pratinjau</span>
                    <img src="" alt="Preview" class="preview-img" style="display: none;">
                </div>
                <span class="pilih-gambar-text" onclick="document.getElementById('gambar').click()">Pilih Gambar</span>
                <input type="file" id="gambar" name="gambar" accept="image/*" onchange="previewGambar(this)" style="display: none;">
                </div>
                </div>
            </div>
            <div class="button-group">
                <button type="button" id="batalBtn" onclick="tutupForm()">Batal</button>
                <button type="submit">Simpan</button>
            </div>
            </form>
        </div>
        </div>
    </div>
<script>
  const modal = document.getElementById("formModal");
  const tambahBtn = document.getElementById("tambahMenuBtn");

  // Tampilkan form saat tombol tambah ditekan
  tambahBtn.addEventListener("click", function () {
    modal.style.display = "block";
  });

  // Fungsi untuk menutup modal
  function tutupForm() {
    modal.style.display = "none";
    document.getElementById("menuForm").reset(); // Reset form jika diperlukan
  }

  // Tutup modal jika user klik di luar area konten
  window.addEventListener("click", function (event) {
    if (event.target === modal) {
      tutupForm();
    }
  });

  // (Opsional) Tangani submit form
  document.getElementById("menuForm").addEventListener("submit", function (e) {
    e.preventDefault();
    // Lakukan proses simpan data di sini
    alert("Data berhasil disimpan!");
    tutupForm();
  });
  function previewGambar(input) {
    const previewContainer = document.getElementById('imagePreview');
    const imgElement = previewContainer.querySelector('.preview-img');
    const textElement = previewContainer.querySelector('span');

    const file = input.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        imgElement.src = e.target.result;
        imgElement.style.display = "block";
        textElement.style.display = "none";
      }
      reader.readAsDataURL(file);
    } else {
      imgElement.src = "";
      imgElement.style.display = "none";
      textElement.style.display = "block";
    }
  }
</script>


</body>
</html>
