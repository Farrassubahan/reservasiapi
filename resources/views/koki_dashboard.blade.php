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
        <a href="#" class="active">Dashboard</a>
        <a href="{{ route('koki.pesanan') }}">Pesanan Masuk</a>
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit">Logout</button>
        </form>

    </div>
    <div class="main">
        <div class="content">
            <h1>Dashboard Koki - Hari ini</h1>
        </div>
        <div class="information">
            <div class="card-info" style="background-color: red;">
                <h2>Belum Masuk</h2>
                <h2>{{ $pesanan->where('status', 'menunggu')->count() }} Pesanan</h2>
            </div>
            <div class="card-info" style="background-color: yellow;">
                <h2>Masak</h2>
                <h2>{{ $pesanan->where('status', 'diproses')->count() }} Pesanan</h2>
            </div>
            <div class="card-info" style="background-color: green;">
                <h2>Selesai</h2>
                <h2>{{ $pesanan->whereIn('status', ['siap', 'disajikan'])->count() }} Pesanan</h2>
            </div>
        </div>
        <div class="pesanan">
            <h1>Pesanan Terbaru</h1>
            <table class="table-pesanan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Menu</th>
                        <th>Porsi</th>
                        <th>Jam</th>
                        <th>Catatan Custemer</th>
                        {{-- <th>Status</th>
                        <th>Aksi</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pesanan->whereNotIn('status', ['disajikan', 'dibatalkan']) as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->pengguna->nama }}</td>
                            <td>{{ $item->menu->nama }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ $item->created_at->format('H:i') }}</td>
                            <td>{{ $item->catatan }}</td>
                            {{-- <td id="status-{{ $item->id }}">{{ ucfirst($item->status) }}</td>
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
                            </td> --}}
                        </tr>
                    @endforeach


                </tbody>
            </table>

        </div>
    </div>

    </div>
</body>

</html>
