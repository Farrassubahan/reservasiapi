<!DOCTYPE html>
<html lang="en">

<head>
    {{-- <meta charset="UTF-8"> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Admin Meja</title>
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
            <a class="active" href="{{ route('meja.index') }}">Meja</a>
            <a href="{{ route('admin.laporan') }}">Laporan</a>
            <a href="{{ route('admin.usm.index') }}">Manajemen User</a>
            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="logout-form">
                @csrf
                <button type="submit" id="logout-btn">Logout</button>
            </form>
        </div>
        <div class="header" style="justify-content: center !important;">
            <form method="GET" action="{{ route('meja.index') }}">
                <input type="search" name="search" placeholder="Search nomor meja" value="{{ request('search') }}">
            </form>
            <form method="GET" action="{{ url('/admin/meja') }}" id="formFilter">
                <select name="status" onchange="document.getElementById('formFilter').submit()">
                    <option value="" {{ request('status') == null ? 'selected' : '' }}>Semua Status</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dipesan" {{ request('status') == 'dipesan' ? 'selected' : '' }}>Dipesan</option>
                    <option value="digunakan" {{ request('status') == 'digunakan' ? 'selected' : '' }}>Digunakan
                    </option>
                </select>
            </form>
        </div>
        <div class="main">
            @if (session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: '{{ session('success') }}',
                        timer: 2000,
                        showConfirmButton: false
                    });
                </script>
            @endif
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
                @foreach ($mejas as $meja)
                    @php
                        $reservasiMenunggu = $meja->reservasi->firstWhere('status', 'menunggu');
                        $fallbackReservasi = $meja->reservasi->sortByDesc('created_at')->first();
                        $reservasiAktif = $reservasiMenunggu ?? $fallbackReservasi;
                    @endphp
                    <div class="card-menu" style="text-align: center">
                        <div class="col-gambar">{{ $meja->nomor }}</div>
                        <div class="col-name-menu">{{ $meja->kapasitas }} orang</div>
                        <div class="col-harga">{{ ucfirst($meja->status) }}</div>
                        <div class="col-kategori">
                            {{ $reservasiAktif && $reservasiAktif->pengguna ? $reservasiAktif->pengguna->nama : '-' }}
                        </div>
                        <div class="col-deskripsi">
                            {{ $reservasiAktif ? $reservasiAktif->jumlah_tamu : '-' }} orang
                        </div>
                        <div class="col-action">
                            <form action="{{ route('meja.destroy', $meja->id) }}" method="POST" class="form-hapus"
                                style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-hapus" data-id="{{ $meja->id }}">
                                    <i class="fa-solid fa-trash text-red-600"></i>
                                </button>
                            </form>

                            <button type="button" class="btn-edit" data-id="{{ $meja->id }}">
                                <i class="fa-solid fa-pen text-blue-600"></i>
                            </button>

                        </div>
                    </div>
                @endforeach
                {{-- Modal Create Meja --}}
                <div class="modal" id="formModal">
                    <div class="modal-content">
                        <span class="close-button" onclick="tutupForm()">&times;</span>
                        <h2>Tambah Menu</h2>

                        <form id="mejaForm">
                            <label for="nomor">Nomor Meja</label>
                            <input type="text" id="nomor" name="nomor" required>

                            <label for="area">Area</label>
                            <input type="text" id="area" name="area" required>

                            <label for="kapasitas">Kapasitas</label>
                            <input type="number" id="kapasitas" name="kapasitas" required>

                            <label for="status">Status</label>
                            <select id="status" name="status" style="height: 3rem;" required>
                                <option value="">Pilih Status</option>
                                <option value="tersedia">Tersedia</option>
                                <option value="dipesan">Dipesan</option>
                                <option value="digunakan">Digunakan</option>
                            </select>

                            <div class="button-group">
                                <button type="button" id="batalBtn" onclick="tutupForm()">Batal</button>
                                <button type="submit">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- modal edit --}}
                <div class="modal" id="editModal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-button" onclick="tutupEditForm()">&times;</span>
                        <h2>Edit Meja</h2>

                        <form id="editMejaForm">
                            <input type="hidden" id="edit_id" name="id">

                            <label for="edit_nomor">Nomor Meja</label>
                            <input type="text" id="edit_nomor" name="nomor" required>

                            <label for="edit_area">Area</label>
                            <input type="text" id="edit_area" name="area" required>

                            <label for="edit_kapasitas">Kapasitas</label>
                            <input type="number" id="edit_kapasitas" name="kapasitas" required>

                            <label for="edit_status">Status</label>
                            <select id="edit_status" name="status" style="height: 3rem;" required>
                                <option value="">Pilih Status</option>
                                <option value="tersedia">Tersedia</option>
                                <option value="dipesan">Dipesan</option>
                                <option value="digunakan">Digunakan</option>
                            </select>
                            <div class="button-group">
                                <button type="button" onclick="tutupEditForm()">Batal</button>
                                <button type="submit">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- script buat create --}}
            <script>
                const modal = document.getElementById("formModal");
                const tambahBtn = document.getElementById("tambahMenuBtn");

                tambahBtn.addEventListener("click", function() {
                    modal.style.display = "block";
                });

                function tutupForm() {
                    modal.style.display = "none";
                    document.getElementById("mejaForm").reset();
                }

                document.getElementById('mejaForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const form = e.target;
                    const data = new FormData(form);

                    fetch("{{ route('meja.store') }}", {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                // hapus 'Accept': 'application/json',
                            },
                            body: data
                        })
                        .then(async response => {
                            let resData = await response.json();

                            if (!response.ok) {
                                return Promise.reject(resData);
                            }
                            return resData;
                        })
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: data.message || 'Meja berhasil ditambahkan',
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            tutupForm();
                            setTimeout(() => {
                                location.reload();
                            }, 2100);
                        })
                        .catch(error => {
                            if (error.errors) {
                                const messages = Object.values(error.errors).flat().join('<br>');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    html: messages,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.message || 'Terjadi kesalahan.',
                                });
                            }
                        });
                });
            </script>
            
            {{-- script buat edit brow --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const editModal = document.getElementById("editModal");

                    // Fungsi untuk tutup modal
                    function tutupEditForm() {
                        editModal.style.display = "none";
                    }

                    // Saat tombol edit diklik
                    document.querySelectorAll('.btn-edit').forEach(button => {
                        button.addEventListener('click', function() {
                            const id = this.getAttribute('data-id');

                            fetch(`/admin/meja/${id}`)
                                .then(res => {
                                    if (!res.ok) throw new Error('Gagal mengambil data');
                                    return res.json();
                                })
                                .then(data => {
                                    // Isi form dengan data
                                    document.getElementById('edit_id').value = data.id;
                                    document.getElementById('edit_nomor').value = data.nomor;
                                    document.getElementById('edit_area').value = data.area;
                                    document.getElementById('edit_kapasitas').value = data.kapasitas;
                                    document.getElementById('edit_status').value = data.status;

                                    // Tampilkan modal
                                    editModal.style.display = "block";
                                    // Kalau pakai Bootstrap modal:
                                    // const modal = new bootstrap.Modal(editModal);
                                    // modal.show();
                                })
                                .catch(err => {
                                    alert('Gagal memuat data meja: ' + err.message);
                                });
                        });
                    });

                    // Saat form submit
                    document.getElementById('editMejaForm').addEventListener('submit', function(e) {
                        e.preventDefault();

                        const id = document.getElementById('edit_id').value;
                        const formData = new FormData(this);

                        fetch(`/admin/meja/${id}`, {
                                method: 'POST', // tetap POST karena kita override ke PUT
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-HTTP-Method-Override': 'PUT',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                            .then(res => {
                                if (!res.ok) return res.json().then(err => Promise.reject(err));
                                return res.json();
                            })
                            .then(data => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message || 'Data meja berhasil diperbarui',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                tutupEditForm();
                                setTimeout(() => location.reload(), 2100);
                            })
                            .catch(err => {
                                let pesan = 'Terjadi kesalahan.';
                                if (err.errors) {
                                    pesan = Object.values(err.errors).flat().join('<br>');
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    html: pesan
                                });
                            });
                    });
                });
            </script>


            {{-- allert hapus --}}
            <script>
                document.querySelectorAll('.form-hapus').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Yakin ingin menghapus?',
                            text: "Data meja akan dihapus permanen.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#aaa',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            </script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
</body>

</html>
