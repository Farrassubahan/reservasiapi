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
            <a href="#">Dashboard</a>
            <a class="active" href="#">Reservasi</a>
            <a href="#">Pelanggan</a>
            <a href="#">Menu</a>
            <a href="#">Meja</a>
            <a href="#">Laporan</a>
            <a href="#">Manajemen User</a>
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
                    <h2 class="col col-status">Status</h2>
                    <h2 class="col col-action">Aksi</h2>
                </div>

                @foreach ($reservasi as $item)
                    <div class="card-reservasi">
                        <div class="col col-name" title="{{ $item->pengguna->nama ?? '-' }}">
                            {{ Str::limit($item->pengguna->nama ?? '-', 30) }}
                        </div>
                        <div class="col col-codereservasi">{{ $item->kode_reservasi }} </div>
                        <div class="col col-date">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }} {{ $item->waktu }}
                        </div>
                        <div class="col col-people">{{ $item->jumlah_tamu }} orang</div>
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
                                    {{-- <form method="POST" action="{{ route('reservasi.update', $item->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="selesai">
                                        <button type="submit">Edit</button>
                                    </form> --}}

                                    <form method="POST" action="{{ route('reservasi.destroy', $item->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                    </form>

                                    {{-- <button>No Meja</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
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

</body>

</html>
