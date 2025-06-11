<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // Tambahkan ini
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use Midtrans\Snap;
use Midtrans\Config;

class MidtransController extends Controller
{
    public function getSnapToken(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'ORDER-' . time();
        $amount = $request->amount;

        // Simpan ke DB (status default pending)
        $pembayaran = Pembayaran::create([
            'reservasi_id' => $request->reservasi_id,
            'metode' => 'midtrans',
            'status' => 'pending'
        ]);

        // Siapkan parameter untuk Snap
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'email' => $request->email,
            ],
        ];

        // Ambil Snap Token dari Midtrans
        $snapToken = Snap::getSnapToken($params);

        // Simpan order_id Midtrans ke kolom 'bukti'
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
