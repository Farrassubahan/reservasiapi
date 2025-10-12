<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // Tambahkan ini
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Str;

class MidtransController extends Controller
{
    public function getSnapToken(Request $request)
    {
        // Validasi input
        $request->validate([
            'reservasi_id' => 'required|exists:reservasi,id',
            'name' => 'required|string',
            'email' => 'required|email',
            'metode' => 'required|in:qris,transfer,cod', // tambahkan metode
        ]);
 
        $metode = $request->metode;

        // Ambil semua pesanan + relasi menu dari reservasi
        $pesanan = Pesanan::with('menu')
            ->where('reservasi_id', $request->reservasi_id)
            ->get();

        // Siapkan item details dan hitung total
        $itemDetails = [];
        $total = 0;

        foreach ($pesanan as $item) {
            if (!$item->menu) {
                continue;
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
            'metode' => $metode,
            'gateway' => $metode === 'cod' ? null : 'midtrans',
            'status' => 'menunggu',
            'jumlah' => $total,
        ]);

        // Jika metode COD, tidak perlu Snap Token
        if ($metode === 'cod') {
            return response()->json([
                'message' => 'Pembayaran COD berhasil dicatat.',
                'data' => $pembayaran,
            ]);
        }

        // Proses Snap Midtrans untuk QRIS dan Transfer
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'ORDER-' . time();

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

        // Jika metode QRIS saja, limit payment type
        if ($metode === 'qris') {
            $params['enabled_payments'] = ['qris'];
        }

        // Ambil Snap Token
        $snapToken = Snap::getSnapToken($params);

        // Simpan order ID Midtrans
        $pembayaran->update([
            'bukti' => $orderId,
        ]);

        return response()->json([
            'snapToken' => $snapToken,
            'order_id' => $orderId,
        ]);
    }

    public function uploadBuktiManual(Request $request)
    {
        $request->validate([
            'pembayaran_id' => 'required|exists:pembayaran,id',
            'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pembayaran = Pembayaran::findOrFail($request->pembayaran_id);

        // Tambahkan validasi status
        if ($pembayaran->status !== 'menunggu') {
            return response()->json([
                'status' => false,
                'message' => 'Bukti hanya bisa diupload jika status masih menunggu.',
            ], 403);
        }

        // Simpan file ke public/img/bukti_transaksi
        $file = $request->file('bukti');
        $hashName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('img/bukti_transaksi');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $hashName);

        // Update data pembayaran
        $pembayaran->update([
            'bukti' => 'img/bukti_transaksi/' . $hashName,
            'status' => 'dikonfirmasi',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Bukti transaksi berhasil diunggah dan status diperbarui.',
            'data' => $pembayaran,
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
