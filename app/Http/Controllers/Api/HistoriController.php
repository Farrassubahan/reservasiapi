<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;
use Illuminate\Support\Facades\Auth;

class HistoriController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();

    //     $histori = Reservasi::with(['meja', 'pesanan.menu', 'pelayan', 'ratingPelayan'])
    //         ->where('pengguna_id', $user->id)
    //         ->orderBy('tanggal', 'desc')
    //         ->get();

    //     $histori->transform(function ($reservasi) {
    //         $totalHarga = 0;
    //         foreach ($reservasi->pesanan as $pesanan) {
    //             $hargaMenu = $pesanan->menu->harga ?? 0;
    //             $jumlahPesanan = $pesanan->jumlah ?? 1;
    //             $totalHarga += $hargaMenu * $jumlahPesanan;
    //         }
    //         $reservasi->total_harga = $totalHarga;
    //         return $reservasi;
    //     });

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $histori
    //     ]);
    // }

    // public function show($id)
    // {
    //     $user = Auth::user();

    //     $reservasi = Reservasi::with([
    //         'pesanan.menu',
    //         'pengguna',
    //         'pembayaran',
    //         'meja',
    //         'pelayan',
    //         'ratingPelayan'
    //     ])
    //         ->where('id', $id)
    //         ->where('pengguna_id', $user->id)
    //         ->first();

    //     if (!$reservasi) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Data reservasi tidak ditemukan'
    //         ], 404);
    //     }

    //     // Hitung total harga
    //     $totalHarga = 0;
    //     foreach ($reservasi->pesanan as $pesanan) {
    //         $harga = $pesanan->menu->harga ?? 0;
    //         $jumlah = $pesanan->jumlah ?? 1;
    //         $totalHarga += $harga * $jumlah;
    //     }

    //     $reservasi->total_harga = $totalHarga;

    //     return response()->json([
    //         'status' => true,
    //         'data' => $reservasi
    //     ]);
    // }

    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User belum login atau token tidak valid'
            ], 401);
        }

        $histori = Reservasi::with(['meja', 'pesanan.menu', 'pelayan', 'ratingPelayan'])
            ->where('pengguna_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        $histori->transform(function ($reservasi) {
            $totalHarga = 0;
            foreach ($reservasi->pesanan as $pesanan) {
                $hargaMenu = optional($pesanan->menu)->harga ?? 0;
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
            'pesanan.menu',
            'pengguna',
            'pembayaran',
            'meja',
            'pelayan',
            'ratingPelayan'
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

        // Filter pengguna, hanya kirim data yang perlu
        if (isset($data['pengguna'])) {
            $data['pengguna'] = [
                'id' => $data['pengguna']['id'],
                'nama' => $data['pengguna']['nama'],
                'email' => $data['pengguna']['email'],

            ];
        }

        foreach ($reservasi->pesanan as $index => $pesanan) {
            $menu = $pesanan->menu;
            $harga = $menu ? (float) $menu->harga : 0;
            $jumlah = $pesanan->jumlah ?? 1;
            $totalItem = $harga * $jumlah;

            // Tambahkan properti harga langsung di pesanan
            $data['pesanan'][$index]['harga'] = $harga;

            // Tetap tambahkan total harga item (harga * jumlah)
            $data['pesanan'][$index]['total_harga_item'] = $totalItem;

            // Update menu detail juga seperti biasa
            $data['pesanan'][$index]['menu'] = [
                'id' => $menu->id ?? null,
                'nama' => $menu->nama ?? 'Menu Tidak Ditemukan',
                'harga' => $harga
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
