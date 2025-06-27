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
            <a href="{{ route('meja.index') }}">Meja</a>
            <a href="{{ route('admin.laporan') }}">Laporan</a>
            <a class="active" href="{{ route('admin.usm.index') }}">Manajemen User</a>
            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="logout-form">
                @csrf
                <button type="submit" id="logout-btn">Logout</button>
            </form>
        </div>

        <div class="main">
            @if (session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: '{{ session('success') }}',
                        timer: 2000,
                        showConfirmButton: false
                    });
                </script>
            @endif

            <div class="header-usm">
                <input type="search" name="search" placeholder="Nama Pegawai">
                <button class="tambah-menu" id="tambahMenuBtn">Tambah Pegawai</button>
            </div>
            <div class="content-menu">
                <div class="header-row-usm">
                    <h2 class="col-usn">Nama</h2>
                    <h2 class="col-email">Email</h2>
                    <h2 class="col-email">No Telepon</h2>
                    <h2 class="col-jabatan">Jabatan</h2>
                    <h2 class="col-action">Aksi</h2>
                </div>

                @foreach ($pengguna as $user)
                    <div class="card-usm">
                        <div class="col-usn">{{ $user->nama }}</div>
                        <div class="col-email">{{ $user->email }}</div>
                        <div class="col-email">{{ $user->telepon }}</div>
                        <div class="col-jabatan">{{ $user->role }}</div>
                        <div class="col-action">
                            <form action="{{ route('admin.usm.destroy', $user->id) }}" method="POST"
                                class="form-hapus" data-nama="{{ $user->nama }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-hapus">
                                    <i class="fa-solid fa-trash text-red-600"></i>
                                </button>
                            </form>
                            <a href="#" class="btn-edit" data-id="{{ $user->id }}">
                                <i class="fa-solid fa-pencil text-blue-600"></i>
                            </a>
                        </div>
                    </div>
                @endforeach

                {{-- modal tambah Akun --}}
                <div class="modal" id="formModal">
                    <div class="modal-content">
                        <span class="close-button" onclick="tutupForm()">&times;</span>
                        <h2>Tambah Akun</h2>

                        <form id="menuForm" action="{{ route('admin.usm.store') }}" method="POST">
                            @csrf
                            <label for="nama">Nama</label>
                            <input type="text" id="nama" name="nama" required>

                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror

                            <label for="telepon">Telepon</label>
                            <input type="number" id="telepon" name="telepon" required>

                            <label for="password">Password</label>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <small style="font-size: 0.8rem; color: gray;">
                                *Minimal 6 karakter, wajib huruf besar, kecil, angka, dan simbol.
                            </small>

                            <div style="position: relative; width: 100%;">
                                <input type="password" id="password" name="password" required
                                    style="width: 100%; padding-right: 40px;">
                                <button type="button" id="togglePassword"
                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                                    <i class="fa-solid fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>

                            <label for="role">Jabatan</label>
                            <select id="role" name="role" class="select2" style="height: 3.3rem;" required>
                                <option value="">-- Pilih Jabatan --</option>
                                <option value="Pelanggan">Pelanggan</option>
                                <option value="Pelayan">Pelayan</option>
                                <option value="Koki">Koki</option>
                                <option value="Admin">Admin</option>
                            </select>


                            <div class="button-group">
                                <button type="button" id="batalBtn" onclick="tutupForm()">Batal</button>
                                <button type="submit">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Modal Edit Pengguna -->
                <div class="modal" id="editModal" style="display:none;">
                    <div class="modal-content">
                        <span class="close-button" onclick="tutupEditForm()">&times;</span>
                        <h2>Edit Akun</h2>

                        <form id="menuEditForm">
                            <input type="hidden" id="edit_id" name="id">

                            <label for="edit_nama">Nama</label>
                            <input type="text" id="edit_nama" name="nama" required>

                            <label for="edit_email">Email</label>
                            <input type="email" id="edit_email" name="email" required>

                            <label for="edit_telepon">Telepon</label>
                            <input type="text" id="edit_telepon" name="telepon" required>

                            <label for="edit_role">Jabatan</label>
                            <select id="edit_role" name="role" style="height: 3.3rem;" required>
                                <option value="">-- Pilih Jabatan --</option>
                                <option value="Pelanggan">Pelanggan</option>
                                <option value="Pelayan">Pelayan</option>
                                <option value="Koki">Koki</option>
                                <option value="Admin">Admin</option>
                            </select>

                            <label for="edit_password">Password (Opsional)</label>
                            <input type="password" id="edit_password" name="password">

                            <div class="button-group">
                                <button type="button" onclick="tutupEditForm()">Batal</button>
                                <button type="submit">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Script -->
                <script>
                    const modal = document.getElementById("formModal");
                    const tambahBtn = document.getElementById("tambahMenuBtn");

                    tambahBtn.addEventListener("click", function() {
                        modal.style.display = "block";
                    });

                    function tutupForm() {
                        modal.style.display = "none";
                        document.getElementById("menuForm").reset();
                    }

                    window.addEventListener("click", function(event) {
                        if (event.target === modal) {
                            tutupForm();
                        }
                    });

                    // SweetAlert feedback success
                    @if (session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: '{{ session('success') }}',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            tutupForm();
                        });
                    @endif
                </script>


                {{-- Allert Hapus --}}
                <script>
                    document.querySelectorAll('.form-hapus').forEach(form => {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            let nama = this.dataset.nama;

                            Swal.fire({
                                title: 'Yakin hapus pengguna ini?',
                                text: "Pengguna: " + nama,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Ya, hapus!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    this.submit();
                                }
                            });
                        });
                    });

                    // tombol mata buat liat pw
                    const togglePassword = document.getElementById('togglePassword');
                    const passwordInput = document.getElementById('password');
                    const eyeIcon = document.getElementById('eyeIcon');

                    togglePassword.addEventListener('click', function() {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);

                        // Ubah ikon antara mata dan mata dicoret
                        eyeIcon.classList.toggle('fa-eye');
                        eyeIcon.classList.toggle('fa-eye-slash');
                    });
                </script>


                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const editModal = document.getElementById("editModal");

                        // Tombol edit (loop semua tombol)
                        document.querySelectorAll('.btn-edit').forEach(button => {
                            button.addEventListener('click', async function(e) {
                                e.preventDefault();
                                const id = this.getAttribute('data-id');

                                try {
                                    const res = await fetch(`/admin/user-management/${id}`);
                                    if (!res.ok) throw new Error("Gagal mengambil data pengguna.");

                                    const user = await res.json();

                                    // Isi form
                                    document.getElementById('edit_id').value = user.id;
                                    document.getElementById('edit_nama').value = user.nama;
                                    document.getElementById('edit_email').value = user.email;
                                    document.getElementById('edit_telepon').value = user.telepon;
                                    document.getElementById('edit_role').value = user.role;

                                    // Tampilkan modal
                                    editModal.style.display = "block";
                                } catch (error) {
                                    Swal.fire('Error', error.message, 'error');
                                }
                            });
                        });

                        // Tombol tutup modal
                        window.tutupEditForm = function() {
                            editModal.style.display = "none";
                            document.getElementById("menuEditForm").reset();
                        }

                        // Form submit update
                        document.getElementById("menuEditForm").addEventListener("submit", async function(e) {
                            e.preventDefault();
                            const id = document.getElementById("edit_id").value;
                            const formData = new FormData(this);

                            try {
                                const response = await fetch(`/admin/user-management/update/${id}`, {
                                    method: "POST",
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: formData
                                });

                                const result = await response.json();

                                if (response.ok) {
                                    Swal.fire('Berhasil', result.message || 'Data berhasil diperbarui!', 'success')
                                        .then(() => window.location.reload());
                                } else {
                                    throw new Error(result.message || 'Gagal memperbarui data.');
                                }
                            } catch (error) {
                                Swal.fire('Gagal', error.message, 'error');
                            }
                        });
                    });
                </script>

                <script>
                    document.getElementById('logout-btn').addEventListener('click', function(e) {
                        e.preventDefault(); // cegah submit langsung

                        Swal.fire({
                            title: 'Apakah Anda yakin ingin logout?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Logout',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('logout-form').submit();
                            }
                        });
                    });
                </script>

                @if ($errors->has('email') || $errors->has('telepon') || $errors->has('password'))
                    <script>
                        let pesan = "";

                        @error('email')
                            pesan += "- {{ $message }}<br>";
                        @enderror

                        @error('telepon')
                            pesan += "- {{ $message }}<br>";
                        @enderror

                        @error('password')
                            pesan += "- {{ $message }}<br>";
                        @enderror

                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            html: pesan,
                            confirmButtonText: 'OK'
                        });

                        document.getElementById("formModal").style.display = "block";
                    </script>
                @endif


            </div>
        </div>
    </div>
</body>

</html>
