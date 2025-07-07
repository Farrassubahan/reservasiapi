<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;
use Illuminate\Support\Facades\Auth;

class HistoriController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User belum login atau token tidak valid'
            ], 401);
        }

        $histori = Reservasi::with([
            'meja',
            'pesanan.menu',
            'pelayan',
            'koki',
            'ratingPelayan',
            'ratingKoki'
        ])
            ->where('pengguna_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        $histori->transform(function ($reservasi) {
            $totalHarga = 0;
            foreach ($reservasi->pesanan as $pesanan) {
                $hargaMenu = optional($pesanan->menu)->harga ?? (float)$pesanan->harga_snapshot;
                $jumlahPesanan = $pesanan->jumlah ?? 1;
                $totalHarga += $hargaMenu * $jumlahPesanan;
            }
            $reservasi->total_harga = $totalHarga;
            return $reservasi;
        });

        return response()->json([
            'status' => 'success',
            'data' => $histori
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User belum login atau token tidak valid'
            ], 401);
        }

        $reservasi = Reservasi::with([
            'pesanan',
            'pengguna',
            'pembayaran',
            'meja',
            'pelayan',
            'koki',
            'ratingPelayan',
            'ratingKoki'
        ])
            ->where('id', $id)
            ->where('pengguna_id', $user->id)
            ->first();

        if (!$reservasi) {
            return response()->json([
                'status' => false,
                'message' => 'Data reservasi tidak ditemukan'
            ], 404);
        }

        $totalHarga = 0;
        $data = $reservasi->toArray();

        // Tambahkan data pengguna
        $data['pengguna'] = [
            'id' => $reservasi->pengguna_id,
            'nama' => $reservasi->pengguna->nama ?? null,
            'email' => $reservasi->pengguna->email ?? null
        ];

        // Tambahkan data pelayan
        $data['pelayan'] = $reservasi->pelayan ? [
            'id' => $reservasi->pelayan->id,
            'nama' => $reservasi->pelayan->nama,
            'email' => $reservasi->pelayan->email
        ] : null;

        // Tambahkan data koki
        $data['koki'] = $reservasi->koki ? [
            'id' => $reservasi->koki->id,
            'nama' => $reservasi->koki->nama,
            'email' => $reservasi->koki->email
        ] : null;

        // Hitung total harga dan tampilkan snapshot data menu
        foreach ($reservasi->pesanan as $index => $pesanan) {
            $harga = (float) ($pesanan->harga_menu ?? 0);
            $jumlah = $pesanan->jumlah ?? 1;
            $totalItem = $harga * $jumlah;

            $data['pesanan'][$index]['harga'] = $harga;
            $data['pesanan'][$index]['total_harga_item'] = $totalItem;

            $data['pesanan'][$index]['menu'] = [
                'id' => $pesanan->menu_id,
                'nama' => $pesanan->nama_menu ?? '-',
                'harga' => $harga,
                'gambar' => $pesanan->gambar_menu
                    ? url('img/gambar_menu/' . $pesanan->gambar_menu)
                    : null
            ];

            $totalHarga += $totalItem;
        }

        $data['total_harga'] = $totalHarga;

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
