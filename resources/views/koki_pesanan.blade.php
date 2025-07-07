<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Koki</title>
    @vite(['resources/css/koki.css', 'resources/js/app.js'])
</head>

<body>

    <div class="sidebar">
        <h2></h2>
        {{-- <a href="#">Dashboard</a> --}}
        <a href="{{ route('koki.dashboard') }}">Dashboard</a>
        <a href="#" class="active">Pesanan Masuk</a>
        <form method="POST" action="{{ route('logout') }}" id="logout-form" class="logout-form">
            @csrf
            <button type="submit" id="logout-btn">Logout</button>
        </form>
    </div>
    <div class="main">
        <div class="head">
            <h1>Daftar Pesanan Masuk</h1>
            <form method="GET" action="{{ route('koki.pesanan.filter') }}">
                <select name="status" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="siap" {{ request('status') == 'siap' ? 'selected' : '' }}>Siap</option>
                    <option value="disajikan" {{ request('status') == 'disajikan' ? 'selected' : '' }}>Disajikan</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </form>
        </div>
        <div class="pesanan">
            <table class="table-pesanan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Menu</th>
                        <th>Porsi</th>
                        <th>Jam</th>
                        <th>Catatan Customer</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pesanan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->pengguna->nama }}</td>
                            <td>{{ $item->menu->nama }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ $item->created_at->format('H:i') }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td id="status-{{ $item->id }}">{{ ucfirst($item->status) }}</td>
                            <td>
                                <form action="{{ url('koki/pesanan/' . $item->id . '/status') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" style="font-size: 12px; padding: 2px;">
                                        <option value="menunggu" {{ $item->status == 'menunggu' ? 'selected' : '' }}>
                                            Menunggu</option>
                                        <option value="diproses" {{ $item->status == 'diproses' ? 'selected' : '' }}>
                                            Diproses</option>
                                        <option value="siap" {{ $item->status == 'siap' ? 'selected' : '' }}>Siap
                                        </option>
                                        <option value="disajikan" {{ $item->status == 'disajikan' ? 'selected' : '' }}>
                                            Disajikan</option>
                                        <option value="dibatalkan"
                                            {{ $item->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                    <button type="submit" style="font-size: 12px; padding: 3px 6px;">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('logout-btn').addEventListener('click', function(e) {
            e.preventDefault();

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
