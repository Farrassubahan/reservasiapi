<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Admin Reservasi</title>
    @vite(['resources/css/koki.css', 'resources/js/app.js'])
</head>

<body>
    <div class="layout">
        <div class="sidebar">
            <h2>Menu</h2>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="active" href="{{ route('reservasi.index') }}">Reservasi</a>
            <a href="{{ route('admin.pelanggan.index') }}">Pelanggan</a>
            <a href="{{ route('menu.index') }}">Menu</a>
            <a href="{{ route('meja.index') }}">Meja</a>
            <a href="{{ route('admin.laporan') }}">Laporan</a>
            <a href="{{ route('admin.usm.index') }}">Manajemen User</a>
            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="logout-form">
                @csrf
                <button type="submit" id="logout-btn">Logout</button>
            </form>
        </div>

        <div class="main">


            <form method="GET" action="{{ route('reservasi.index') }}">
                <div class="header">
                    <div>
                        <input type="search" name="search" placeholder="Search" value="{{ request('search') }}">
                    </div>
                    <div>
                        <select name="status" onchange="this.form.submit()">
                            <option value=""{{ request('status') ? '' : 'selected' }}>Tampilkan Semua
                            </option>
                            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu
                            </option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima
                            </option>
                            <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>
                                Dibatalkan</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai
                            </option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="content-reservasi">
                <div class="header-row">
                    <h2 class="col col-name">Nama Pelanggan</h2>
                    <h2 class="col col-codereservasi">Kode Reservasi</h2>
                    <h2 class="col col-date">Tanggal & Waktu</h2>
                    <h2 class="col col-people">Jumlah Orang</h2>
                    <h2 class="col col-status">Bukti Transfer</h2>
                    <h2 class="col col-status">Status</h2>
                    <h2 class="col col-action">Aksi</h2>
                </div>

                @foreach ($reservasi as $item)
                    <div class="card-reservasi">
                        <div class="col col-name" title="{{ $item->pengguna->nama ?? '-' }}">
                            {{ Str::limit($item->pengguna->nama ?? '-', 30) }}
                        </div>

                        <div class="col col-codereservasi">
                            {{ $item->kode_reservasi }}
                        </div>

                        <div class="col col-date">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }} {{ $item->waktu }}
                        </div>

                        <div class="col col-people">
                            {{ $item->jumlah_tamu }} orang
                        </div>
                        <div class="col col-bukti">
                            @if ($item->pembayaran && $item->pembayaran->bukti)
                                <img src="{{ $item->pembayaran && $item->pembayaran->bukti ? asset($item->pembayaran->bukti) : asset('image/default.jpg') }}"
                                    alt="Bukti Pembayaran" width="60" height="60"
                                    style="cursor: pointer; border-radius: 6px;"
                                    onclick="openModal('{{ asset($item->pembayaran->bukti) }}')">
                            @else
                                <span style="font-size: 12px; color: gray;">Tidak ada bukti</span>
                            @endif
                        </div>


                        <div class="col col-status" style="width: 120px;">
                            <form method="POST" action="{{ route('reservasi.update', $item->id) }}">
                                @csrf
                                @method('PUT')
                                <select name="status" onchange="this.form.submit()"
                                    style="width: 100%; padding: 4px; border-radius: 6px;">
                                    @foreach (['menunggu', 'diterima', 'dibatalkan', 'selesai'] as $status)
                                        <option value="{{ $status }}"
                                            {{ $item->status == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        <div class="col col-action">
                            <div class="dropdown">
                                <button class="dropdown-toggle">⋮</button>
                                <div class="dropdown-menu">
                                    <form method="POST" action="{{ route('reservasi.destroy', $item->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Modal Gambar -->
            <div id="buktiModal"
                style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%;
           background-color: rgba(0, 0, 0, 0.7); justify-content: center; align-items: center;">
                <span onclick="closeModal()"
                    style="position: absolute; top: 20px; right: 30px; font-size: 40px; color: white; cursor: pointer;">&times;</span>
                <img id="modalImage" src="" style="max-width: 90%; max-height: 90%; border-radius: 10px;">
            </div>

            <script>
                function openModal(src) {
                    document.getElementById('modalImage').src = src;
                    document.getElementById('buktiModal').style.display = 'flex';
                }

                function closeModal() {
                    document.getElementById('buktiModal').style.display = 'none';
                    document.getElementById('modalImage').src = '';
                }

                document.getElementById('buktiModal').addEventListener('click', function(e) {
                    if (e.target === this) closeModal();
                });
            </script>




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

            <script>
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: '{{ session('success') }}',
                        timer: 3000,
                        showConfirmButton: false
                    });
                @endif

                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: '{{ session('error') }}',
                        timer: 3000,
                        showConfirmButton: false
                    });
                @endif

                @if (session('warning'))
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: '{{ session('warning') }}',
                        timer: 3000,
                        showConfirmButton: false
                    });
                @endif

                @if (session('info'))
                    Swal.fire({
                        icon: 'info',
                        title: 'Info',
                        text: '{{ session('info') }}',
                        timer: 3000,
                        showConfirmButton: false
                    });
                @endif
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


</body>

</html>
