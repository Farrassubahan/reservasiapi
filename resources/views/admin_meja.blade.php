<!DOCTYPE html>
<html lang="en">

<head>
    {{-- <meta charset="UTF-8"> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Admin</title>
    @vite(['resources/css/koki.css', 'resources/js/app.js'])
</head>

<body>
    <div class="layout">
        <div class="sidebar">
            <h2>Menu</h2>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('reservasi.index') }}">Reservasi</a>
            <a href="{{ route('admin.pelanggan.index') }}">Pelanggan</a>
            <a href="{{ route('menu.index') }}">Menu</a>
            <a class="active"href="#">Meja</a>
            <a href="#">Laporan</a>
            <a href="#">Manajemen User</a>
        </div>
        <div class="header" style="justify-content: center !important;">
            <div>
                <input type="search" name="search" placeholder="Search" value="{{ request('search') }}">
            </div>
            <div>
                <select>
                    <option value="" disabled selected hidden>Terbuka</option>
                    <option value="selesai">Tersedia</option>
                    <option value="dimasak">Dipesan</option>
                    <option value="dimasak">Digunakan</option>
                </select>
            </div>
        </div>
        <div class="main">
            <div class="header-menu">
                <div class="data-menu">Daftar Meja</div>
                <button class="tambah-menu" id="tambahMenuBtn">Tambah Meja</button>
            </div>
            <div class="content-menu">
                <div class="header-row-menu" style="text-align: center">
                    <h2 class="col-gambar">No Meja</h2>
                    <h2 class="col-name-menu">Kapasitas</h2>
                    <h2 class="col-harga">Status Meja</h2>
                    <h2 class="col-kategori">Nama Pelanggan</h2>
                    <h2 class="col-Deskripsi">Jumlah Orang</h2>
                    <h2 class="col-action">Aksi</h2>
                </div>
                <!-- Contoh data statis, ganti/duplikat sesuai kebutuhan -->
                <div class="card-menu" style="text-align: center">
                    <div class="col-gambar">1</div>
                    <div class="col-name-menu">2 orang</div>
                    <div class="col-harga">Kosong</div>
                    <div class="col-kategori">Hikanjut</div>
                    <div class="col-deskripsi">2 orang</div>
                    <div class="col-action">
                        <button class="btn-hapus"><i class="fa-solid fa-trash text-red-600"></i></button>
                        <a href="#" class="btn-edit"><i class="fa-solid fa-pencil text-blue-600"></i></a>
                    </div>
                </div>


                {{-- Modal Create Menu --}}
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
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="4" required style="width: 100%;"></textarea>
                            <div class="horizontal-group">
                                <div class="form-group">
                                    <label for="gambar">Gambar</label>
                                    <div class="image-upload-row">
                                        <div class="image-preview" id="imagePreview"
                                            onclick="document.getElementById('gambar').click()">
                                            <span>Pratinjau</span>
                                            <img src="" alt="Preview" class="preview-img"
                                                style="display: none;">
                                        </div>
                                        <span class="pilih-gambar-text"
                                            onclick="document.getElementById('gambar').click()">Pilih Gambar</span>
                                        <input type="file" id="gambar" name="gambar" accept="image/*"
                                            onchange="previewGambar(this)" style="display: none;">
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


            {{-- Modal buat Edit Menu --}}
            <div class="modal" id="editModal" style="display:none;">
                <div class="modal-content">
                    <span class="close-button" onclick="tutupEditForm()">&times;</span>
                    <h2>Edit Menu</h2>

                    <form id="menuEditForm" enctype="multipart/form-data">
                        <input type="hidden" id="edit_menu_id" name="menu_id">
                        <input type="hidden" name="_method" value="PUT">

                        <label for="edit_nama">Nama Menu</label>
                        <input type="text" id="edit_nama" name="nama" required>

                        <label for="edit_harga">Harga</label>
                        <input type="number" id="edit_harga" name="harga" required>

                        <label for="edit_kategori">Kategori</label>
                        <select id="edit_kategori" name="kategori" required style="width: 29rem; height: 3rem;">
                            <option value="">Pilih Kategori</option>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                        </select>

                        <label for="edit_deskripsi">Deskripsi</label>
                        <textarea id="edit_deskripsi" name="deskripsi" required rows="4" style="resize: vertical; width: 100%;"></textarea>

                        <div class="horizontal-group">
                            <div class="form-group">
                                <label for="edit_gambar">Gambar</label>
                                <div class="image-upload-row">
                                    <div class="image-preview" id="editImagePreview"
                                        onclick="document.getElementById('edit_gambar').click()">
                                        <span>Pratinjau</span>
                                        <img src="" alt="Preview" class="preview-img"
                                            style="display: none;">
                                    </div>
                                    <span class="pilih-gambar-text"
                                        onclick="document.getElementById('edit_gambar').click()">Pilih Gambar</span>
                                    <input type="file" id="edit_gambar" name="gambar" accept="image/*"
                                        onchange="previewEditGambar(this)" style="display: none;">
                                </div>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="button" id="batalEditBtn" onclick="tutupEditForm()">Batal</button>
                            <button type="submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>


            {{-- script buat create --}}
            <script>
                const modal = document.getElementById("formModal");
                const tambahBtn = document.getElementById("tambahMenuBtn");

                // Tampilkan form saat tombol tambah ditekan
                tambahBtn.addEventListener("click", function() {
                    modal.style.display = "block";
                });

                // Fungsi untuk menutup modal
                function tutupForm() {
                    modal.style.display = "none";
                    document.getElementById("menuForm").reset(); // Reset form jika diperlukan
                }

                // Tutup modal jika user klik di luar area konten
                window.addEventListener("click", function(event) {
                    if (event.target === modal) {
                        tutupForm();
                    }
                });

                // nampilin prtinjau gambar
                function previewGambar(input) {
                    const file = input.files[0];
                    const previewContainer = document.getElementById('imagePreview');
                    const imgElement = previewContainer.querySelector('.preview-img');
                    const textElement = previewContainer.querySelector('span');

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imgElement.src = e.target.result;
                            imgElement.style.display = 'block';
                            textElement.style.display = 'none';
                        }
                        reader.readAsDataURL(file);
                    } else {
                        imgElement.src = '';
                        imgElement.style.display = 'none';
                        textElement.style.display = 'block';
                    }
                }
</script>



</body>

</html>
