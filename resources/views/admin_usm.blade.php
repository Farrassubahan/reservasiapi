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
            <a href="#">Dashboard</a>
            <a href="#">Reservasi</a>
            <a href="#">Pelanggan</a>
            <a href="#">Menu</a>
            <a href="#">Meja</a>
            <a href="#">Laporan</a>
            <a class="active" href="#">Manajemen User</a>
        </div>

        <div class="main">
            <div class="header-usm">
                <input type="search" name="search" placeholder="Nama Pegawai">
                <button class="tambah-menu" id="tambahMenuBtn">Tambah Pegawai</button>
            </div>
            <div class="content-menu">
                <div class="header-row-usm">
                    <h2 class="col-usn">Nama</h2>
                    <h2 class="col-email">Email</h2>
                    <h2 class="col-jabatan">Jabatan</h2>
                    <h2 class="col-action">Aksi</h2>
                </div>

                <!-- Dummy Data USM -->
                <div class="card-usm">
                    <div class="col-usn">Ajar</div>
                    <div class="col-email">ajar@email.com</div>
                    <div class="col-jabatan">TUHAN</div>
                    <div class="col-action">
                        <button class="btn-hapus"><i class="fa-solid fa-trash text-red-600"></i></button>
                        <a href="#" class="btn-edit" data-id="1">
                            <i class="fa-solid fa-pencil text-blue-600"></i>
                        </a>
                    </div>
                </div>
                <div class="modal" id="formModal">
                    <div class="modal-content">
                        <span class="close-button" onclick="tutupForm()">&times;</span>
                        <h2>Tambah Menu</h2>

                        <form id="menuForm">
                            <label for="nama">Nama</label>
                            <input type="text" id="nama" name="nama" required>

                            <label for="harga">Email</label>
                            <input type="number" id="harga" name="harga" required>

                            <label for="kategori">Jabatan</label>
                            <input type="text" id="kategori" name="kategori" required>
                            <div class="button-group">
                                <button type="button" id="batalBtn" onclick="tutupForm()">Batal</button>
                                <button type="submit">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Edit Menu -->
                <div class="modal" id="editModal" style="display:none;">
                    <div class="modal-content">
                        <span class="close-button" onclick="tutupEditForm()">&times;</span>
                        <h2>Edit Menu</h2>

                        <form id="menuEditForm" enctype="multipart/form-data">
                            <input type="hidden" id="edit_menu_id" name="menu_id">
                            <label for="edit_nama">Nama</label>
                            <input type="text" id="edit_nama" name="nama" required>

                            <label for="edit_harga">Email</label>
                            <input type="number" id="edit_harga" name="harga" required>

                            <label for="edit_kategori">Jabatan</label>
                            <input type="text" id="edit_kategori" name="kategori" required>
                            
                            <div class="button-group">
                                <button type="button" id="batalEditBtn" onclick="tutupEditForm()">Batal</button>
                                <button type="submit">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Script -->
                <script>
                    const modal = document.getElementById("formModal");
                    const tambahBtn = document.getElementById("tambahMenuBtn");

                    tambahBtn.addEventListener("click", function () {
                        modal.style.display = "block";
                    });

                    function tutupForm() {
                        modal.style.display = "none";
                        document.getElementById("menuForm").reset();
                    }

                    window.addEventListener("click", function (event) {
                        if (event.target === modal) {
                            tutupForm();
                        }
                    });

                    function previewGambar(input) {
                        const file = input.files[0];
                        const previewContainer = document.getElementById('imagePreview');
                        const imgElement = previewContainer.querySelector('.preview-img');
                        const textElement = previewContainer.querySelector('span');

                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
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

                    function tutupEditForm() {
                        document.getElementById("editModal").style.display = "none";
                    }

                    function previewEditGambar(input) {
                        const file = input.files[0];
                        const previewContainer = document.getElementById('editImagePreview');
                        const imgElement = previewContainer.querySelector('.preview-img');
                        const textElement = previewContainer.querySelector('span');

                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
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
            </div>
        </div>
    </div>
</body>

</html>
