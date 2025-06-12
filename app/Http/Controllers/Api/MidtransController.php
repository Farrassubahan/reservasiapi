<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // Tambahkan ini
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Midtrans\Snap;
use Midtrans\Config;

class MidtransController extends Controller
{
    public function getSnapToken(Request $request)
    {
        // Validasi input
        $request->validate([
            'reservasi_id' => 'required|exists:reservasi,id',
            'name' => 'required|string',
            'email' => 'required|email',
        ]);

        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'ORDER-' . time();

        // Ambil semua pesanan + relasi menu dari reservasi
        $pesanan = Pesanan::with('menu')
            ->where('reservasi_id', $request->reservasi_id)
            ->get();

        // Siapkan item details dan hitung total
        $itemDetails = [];
        $total = 0;

        foreach ($pesanan as $item) {
            if (!$item->menu) {
                continue; // skip kalau menu null
            }

            $harga = $item->menu->harga;
            $jumlah = $item->jumlah;
            $namaMenu = $item->menu->nama;

            $subtotal = $harga * $jumlah;
            $total += $subtotal;

            $itemDetails[] = [
                'id' => $item->menu->id,
                'price' => $harga,
                'quantity' => $jumlah,
                'name' => $namaMenu,
            ];
        }

        // Simpan pembayaran ke database
        $pembayaran = Pembayaran::create([
            'reservasi_id' => $request->reservasi_id,
            'metode' => 'qris',
            'gateway' => 'midtrans',
            'status' => 'dikonfirmasi',
            'jumlah' => $total,
        ]);

        // Buat parameter Snap
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $request->name,
                'email' => $request->email,
            ],
        ];

        // Ambil Snap Token
        $snapToken = Snap::getSnapToken($params);

        // Simpan order ID dari Midtrans
        $pembayaran->update([
            'bukti' => $orderId,
        ]);

        return response()->json([
            'snapToken' => $snapToken,
            'order_id' => $orderId,
        ]);
    }

    public function handleNotification(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');

        // Ambil notifikasi dari Midtrans
        $notif = new \Midtrans\Notification();

        $orderId = $notif->order_id;
        $transactionStatus = $notif->transaction_status;

        // Cari data pembayaran berdasarkan order_id
        $pembayaran = Pembayaran::where('bukti', $orderId)->first();

        if ($pembayaran) {
            $pembayaran->update([
                'status' => $transactionStatus
            ]);
        }

        return response()->json(['message' => 'OK']);
    }
}
