<?php

namespace App\Http\Controllers\Koki;

use App\Models\Pesanan;
use App\Models\Notifikasi;
use App\Models\RatingKoki;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DapurController extends Controller
{
    public function index()
    {
        $pesanan = Pesanan::with(['menu', 'pengguna'])
            ->whereNotIn('status', ['disajikan', 'dibatalkan', 'siap'])
            ->orderBy('created_at')
            ->get();

        return view('koki_dashboard', compact('pesanan'));
    }

    public function pesananMasuk()
    {
        $pesanan = Pesanan::with(['menu', 'pengguna'])
            ->whereNotIn('status', ['disajikan', 'dibatalkan', 'siap', 'menunggu'])
            ->orderBy('created_at')
            ->get();

        return view('koki_pesanan', compact('pesanan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu,diproses,siap,disajikan,dibatalkan'
        ]);

        $pesanan = Pesanan::with(['reservasi', 'pengguna'])->findOrFail($id);
        $pesanan->status = $validated['status'];
        $pesanan->save();

        // Simpan koki_id di reservasi kalau belum ada
        if (is_null($pesanan->reservasi->koki_id)) {
            $pesanan->reservasi->koki_id = Auth::id();
            $pesanan->reservasi->save();
        }

        // Buat notifikasi
        $judul = 'Status Pesanan';
        $namaMenu = optional($pesanan->menu)->nama ?? 'Pesanan';

        switch ($validated['status']) {
            case 'diproses':
                $judul = "Pesanan Diproses";
                $pesan = "Pesanan Anda untuk $namaMenu saat ini sedang diproses oleh tim dapur kami.";
                break;
            case 'siap':
                $judul = "Pesanan Siap Disajikan";
                $pesan = "Pesanan Anda untuk $namaMenu telah selesai disiapkan dan akan segera disajikan.";
                break;
            case 'disajikan':
                $judul = "Pesanan Telah Disajikan";
                $pesan = "Pesanan Anda untuk $namaMenu telah disajikan. Selamat menikmati sajian kami.";
                break;
            case 'dibatalkan':
                $judul = "Pesanan Dibatalkan";
                $pesan = "Kami mohon maaf, pesanan Anda untuk $namaMenu telah dibatalkan oleh pihak dapur.";
                break;
            default:
                $judul = "Status Pesanan Diperbarui";
                $pesan = "Status pesanan Anda untuk $namaMenu telah diperbarui menjadi: {$validated['status']}.";
        }

        Notifikasi::create([
            'pengguna_id' => $pesanan->pengguna_id,
            'judul'       => $judul,
            'pesan'       => $pesan,
            'dibaca'      => false
        ]);

        return redirect()->route('koki.pesanan')->with('success', 'Status berhasil diperbarui');
    }

    public function filterPesanan(Request $request)
    {
        $statusList = ['selesai', 'menunggu', 'siap', 'disajikan', 'dibatalkan'];

        $query = Pesanan::with(['menu', 'pengguna'])
            ->whereDate('created_at', Carbon::today());

        if ($request->filled('status') && in_array($request->status, $statusList)) {
            $query->where('status', $request->status);
        }

        $pesanan = $query->orderBy('created_at', 'desc')->get();

        return view('koki_pesanan', compact('pesanan'));
    }
}
