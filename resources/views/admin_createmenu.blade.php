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
            <a  href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('reservasi.index') }}">Reservasi</a>
            <a href="{{ route('admin.pelanggan.index') }}">Pelanggan</a>
            <a class="active" href="{{ route('menu.index') }}">Menu</a>
            <a href="{{ route('meja.index') }}">Meja</a>
            <a href="#">Laporan</a>
            <a href="{{ route('admin.usm.index') }}">Manajemen User</a>
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
                    <h2 class="col-Deskripsi">Deskripsi</h2>
                    <h2 class="col-stok">Stok</h2>
                    <h2 class="col-action">Aksi</h2>
                </div>
                @foreach ($menus as $menu)
                    <div class="card-menu" style="text-align: center">
                        <div class="col-gambar">
                            <img src="{{ $menu->gambar ? asset('storage/' . $menu->gambar) : asset('image/default.jpg') }}"
                                alt="gambar" class="gambar-seragam">
                        </div>
                        <div class="col-name-menu">{{ $menu->nama }}</div>
                        <div class="col-harga">Rp {{ number_format($menu->harga, 0, ',', '.') }}</div>
                        <div class="col-kategori">{{ ucfirst($menu->kategori) }}</div>
                        <div class="col-deskripsi">{{ $menu->deskripsi }}</div>
                        <form class="col-stok" action="{{ route('menu.ubah-stok', $menu->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="tersedia" onchange="this.form.submit()">
                                <option value="tersedia" {{ $menu->tersedia == 'tersedia' ? 'selected' : '' }}>Tersedia
                                </option>
                                <option value="kosong" {{ $menu->tersedia == 'kosong' ? 'selected' : '' }}>Kosong
                                </option>
                            </select>
                        </form>

                        <div class="col-action">
                            <form method="POST" action="{{ route('menu.destroy', $menu->id) }}" class="form-hapus"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-hapus" data-nama="{{ $menu->nama }}"> <i
                                        class="fa-solid fa-trash text-red-600"></i></button>
                            </form>
                            <a href="#" class="btn-edit" data-id="{{ $menu->id }}">
                                <i class="fa-solid fa-pencil text-blue-600"></i>
                            </a>

                        </div>
                    </div>
                @endforeach


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

                // untuk create/ bikin menu lek kuuh
                document.getElementById("menuForm").addEventListener("submit", async function(e) {
                    e.preventDefault();

                    const form = e.target;
                    const formData = new FormData(form);

                    try {
                        const response = await fetch("{{ route('menu.store') }}", {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (response.ok) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: result.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            tutupForm();
                            form.reset();
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: result.message || 'Terjadi kesalahan.',
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menyimpan.',
                        });
                    }
                });
            </script>


            {{-- js buat edit --}}
            <script>
                const editModal = document.getElementById("editModal");
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                document.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.preventDefault();
                        const id = btn.dataset.id;

                        fetch(`/admin/menu/${id}/edit`)
                            .then(res => res.json())
                            .then(data => {
                                editModal.style.display = "block";

                                document.getElementById('edit_menu_id').value = data.id;
                                document.getElementById('edit_nama').value = data.nama;
                                document.getElementById('edit_harga').value = data.harga;
                                document.getElementById('edit_kategori').value = data.kategori;
                                // document.getElementById('edit_stok').value = data.stok;
                                document.getElementById('edit_deskripsi').value = data.deskripsi ?? '';



                                const imgPreview = document.querySelector('#editImagePreview .preview-img');
                                const imgText = document.querySelector('#editImagePreview span');

                                if (data.gambar) {
                                    imgPreview.src = `/storage/${data.gambar}`;
                                    imgPreview.style.display = 'block';
                                    imgText.style.display = 'none';
                                } else {
                                    imgPreview.style.display = 'none';
                                    imgText.style.display = 'block';
                                }
                            })
                            .catch(() => {
                                Swal.fire('Error', 'Gagal mengambil data menu.', 'error');
                            });
                    });
                });

                document.getElementById('menuEditForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const id = document.getElementById('edit_menu_id').value;
                    const formData = new FormData(this);
                    formData.append('_method', 'PUT');

                    fetch(`/admin/menu/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: formData,
                        })
                        .then(res => {
                            if (!res.ok) throw res;
                            return res.json();
                        })
                        .then(data => {
                            Swal.fire('Berhasil!', data.message, 'success');
                            tutupEditForm();
                            setTimeout(() => location.reload(), 1000);
                        })
                        .catch(async err => {
                            let msg = 'Terjadi kesalahan.';
                            try {
                                const errorData = await err.json();
                                if (errorData.errors) {
                                    msg = Object.values(errorData.errors).flat().join('<br>');
                                }
                            } catch {
                                // fallback kalau gagal parse json
                            }
                            Swal.fire('Gagal!', msg, 'error');
                        });
                });

                function previewEditGambar(input) {
                    const previewContainer = document.querySelector('#editImagePreview');
                    const imgPreview = previewContainer.querySelector('.preview-img');
                    const imgText = previewContainer.querySelector('span');

                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imgPreview.src = e.target.result;
                            imgPreview.style.display = 'block';
                            imgText.style.display = 'none';
                        }
                        reader.readAsDataURL(input.files[0]);
                    } else {
                        imgPreview.src = '';
                        imgPreview.style.display = 'none';
                        imgText.style.display = 'block';
                    }
                }


                function tutupEditForm() {
                    editModal.style.display = "none";
                    const form = document.getElementById('menuEditForm');
                    form.reset();

                    const imgPreview = document.querySelector('#editImagePreview .preview-img');
                    imgPreview.style.display = 'none';
                    imgPreview.src = '';
                    document.querySelector('#editImagePreview span').style.display = 'block';
                }
            </script>


            <script>
                document.querySelectorAll('.form-hapus').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault(); // Cegah submit langsung

                        const namaMenu = form.querySelector('.btn-hapus').dataset.nama;

                        Swal.fire({
                            title: 'Yakin ingin menghapus?',
                            text: `Menu "${namaMenu}" akan dihapus secara permanen!`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#e3342f',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // Submit form setelah konfirmasi
                            }
                        });
                    });
                });
            </script>



</body>

</html>
