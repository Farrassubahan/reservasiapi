<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a class="active" href="{{ route('admin.pelanggan.index') }}">Pelanggan</a>
            <a href="{{ route('menu.index') }}">Menu</a>
            <a href="{{ route('meja.index') }}">Meja</a>
            <a href="#">Laporan</a>
            <a href="{{ route('admin.usm.index') }}">Manajemen User</a>
        </div>

        <div class="main">
            <div class="header">
                <div>
                    <form action="{{ route('admin.pelanggan.search') }}" method="POST">
                        @csrf
                        <input type="search" name="search" placeholder="Cari Nama Pelanggan..."
                            value="{{ old('search') }}">
                    </form>
                    @if ($pelanggan->isEmpty())
                        <p style="text-align: center; padding: 20px;">Tidak ada pelanggan ditemukan.</p>
                    @endif
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
                <div class="header-row" style="text-align: center">
                    <h2 class="col-no">No</h2>
                    <h2 class="col-name">Nama Pelanggan</h2>
                    <h2 class="col-status">No Hp</h2>
                    <h2 class="col-people">Jumlah Reservasi</h2>
                    <h2 class="col-date">Terakhir Reservasi</h2>
                    <h2 class="col-action">Aksi</h2>
                </div>

                @foreach ($pelanggan as $index => $item)
                    <div class="card-reservasi" style="text-align: center">
                        <div class="col-no">{{ $index + 1 }}</div>
                        <div class="col-name">{{ $item->nama }}</div>
                        <div class="col-status">{{ $item->telepon }}</div>
                        <div class="col-people">{{ $item->reservasi_count ?? '0' }}</div>
                        <div class="col-date">{{ $item->created_at->format('d M Y') }}</div>
                        <div class="col-action">
                            <div class="dropdown">


                                <div class="col-action" style="display: flex; justify-content: center; gap: 10px;">
                                    <!-- Tombol Edit -->
                                    <form action="{{ route('admin.pelanggan.update', $item->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="nama" value="{{ $item->nama }}">
                                        <input type="hidden" name="email" value="{{ $item->email }}">
                                        <input type="hidden" name="telepon" value="{{ $item->telepon }}">
                                        <!-- Tombol Edit: memicu modal -->
                                        <button type="button" title="Edit"
                                            style="background:none; border:none; cursor:pointer;"
                                            onclick="openEditModal('{{ $item->id }}', '{{ $item->nama }}', '{{ $item->email }}', '{{ $item->telepon }}')">
                                            <i class="fas fa-pen" style="color: gray;"></i>
                                        </button>
                                    </form>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('admin.pelanggan.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus akun ini?')"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus"
                                            style="background:none; border:none; cursor:pointer;">
                                            <i class="fas fa-trash" style="color: gray;"></i>
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Modal Edit -->
            <div id="editModal"
                style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000;">
                <div style="background: white; padding: 20px; border-radius: 8px; width: 400px;">
                    <h3>Edit Pelanggan</h3>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="text" name="nama" id="editNama" placeholder="Nama" required><br><br>
                        <input type="email" name="email" id="editEmail" placeholder="Email" required><br><br>
                        <input type="text" name="telepon" id="editTelepon" placeholder="Telepon" required><br><br>
                        <div style="text-align: right;">
                            <button type="button" onclick="closeEditModal()">Batal</button>
                            <button type="submit">Simpan</button>
                        </div>
                    </form>
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

            @if (session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        showConfirmButton: true,
                        timer: 3000,
                        timerProgressBar: true
                    });
                </script>
            @endif

            @if (session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: '{{ session('error') }}',
                        showConfirmButton: true,
                        timer: 3000,
                        timerProgressBar: true
                    });
                </script>
            @endif

            <script>
                function openEditModal(id, nama, email, telepon) {
                    const form = document.getElementById('editForm');
                    form.action = `/admin/pelanggan/${id}`; // Pastikan URL sesuai route update

                    document.getElementById('editNama').value = nama;
                    document.getElementById('editEmail').value = email;
                    document.getElementById('editTelepon').value = telepon;

                    document.getElementById('editModal').style.display = 'flex';
                }

                function closeEditModal() {
                    document.getElementById('editModal').style.display = 'none';
                }
            </script>

        </div>
    </div>
</body>

</html>
