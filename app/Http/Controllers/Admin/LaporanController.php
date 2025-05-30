<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengguna;
use App\Models\Reservasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil bulan dari request, default ke bulan saat ini
        $bulanDipilih = $request->input('bulan', Carbon::now()->month); // angka 1-12
        $tahunDipilih = $request->input('tahun') ?? Carbon::now()->year; // default tahun sekarang


        // Range tanggal berdasarkan bulan yang dipilih
        $startOfSelectedMonth = Carbon::create($tahunDipilih, $bulanDipilih, 1)->startOfDay();
        $endOfSelectedMonth = Carbon::create($tahunDipilih, $bulanDipilih, 1)->endOfMonth()->endOfDay();


        // Hari ini
        $today = Carbon::today();

        // Awal dan akhir minggu ini
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        // Logging untuk debugging
        Log::info('Hari ini:', ['today' => $today]);
        $latestReservasi = Reservasi::latest()->first();
        if ($latestReservasi) {
            Log::info('Tanggal Reservasi Terbaru:', $latestReservasi->toArray());
        } else {
            Log::info('Belum ada data reservasi.');
        }

        // === Reservasi ===
        $reservasiHariIni = Reservasi::whereBetween('tanggal', [
            $startOfSelectedMonth,
            $endOfSelectedMonth
        ])->where('status', '!=', 'dibatalkan')->get();

        // Contoh filter minggu: bisa kamu sesuaikan jika mau ikut filter bulan juga
        $reservasiMingguIni = Reservasi::whereBetween('tanggal', [
            $startOfSelectedMonth,
            $endOfSelectedMonth
        ])->where('status', '!=', 'dibatalkan')->get();

        $reservasiBulanIni = Reservasi::whereBetween('tanggal', [$startOfSelectedMonth, $endOfSelectedMonth])
            ->where('status', '!=', 'dibatalkan')->get();

        // === Pelanggan Baru ===
        $pelangganBaru = Pengguna::where('role', 'pelanggan')
            ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
            ->count();

        // === Grafik Reservasi Per Bulan ===
        $reservasiPerBulan = Reservasi::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->whereYear('tanggal', $tahunDipilih)
            ->where('status', '!=', 'dibatalkan')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $totalsPerBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $totalsPerBulan[] = $reservasiPerBulan->get($i, 0);
        }

        // === Performa Koki ===
        $koki = Pengguna::where('role', 'koki')
            ->get()
            ->map(function ($pengguna) use ($startOfSelectedMonth, $endOfSelectedMonth) {
                $jumlahPesanan = DB::table('rating_kokis')
                    ->where('koki_id', $pengguna->id)
                    ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
                    ->count();

                $rataRating = DB::table('rating_kokis')
                    ->where('koki_id', $pengguna->id)
                    ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
                    ->avg('rating');

                return [
                    'nama' => $pengguna->nama,
                    'jumlah_pesanan' => $jumlahPesanan,
                    'rata_rating' => $rataRating ? round($rataRating, 1) : '-',
                ];
            });

        // === Performa Pelayan ===
        $pelayan = Pengguna::where('role', 'pelayan')
            ->get()
            ->map(function ($pengguna) use ($startOfSelectedMonth, $endOfSelectedMonth) {
                // Hitung jumlah rating sebagai proxy jumlah pelayanan
                $jumlahPelayanan = DB::table('rating_pelayans')
                    ->where('pelayan_id', $pengguna->id)
                    ->whereBetween('tanggal', [$startOfSelectedMonth, $endOfSelectedMonth])
                    ->count();

                $rataRating = DB::table('rating_pelayans')
                    ->where('pelayan_id', $pengguna->id)
                    ->whereBetween('tanggal', [$startOfSelectedMonth, $endOfSelectedMonth])
                    ->avg('rating');

                return [
                    'nama' => $pengguna->nama,
                    'jumlah_reservasi' => $jumlahPelayanan,
                    'rata_rating' => $rataRating ? round($rataRating, 1) : '-',
                ];
            });


        // === Menu Terlaris ===
        $menuTerlaris = DB::table('pesanan as p')
            ->join('menu as m', 'p.menu_id', '=', 'm.id')
            ->whereIn('p.status', ['siap', 'disajikan'])
            ->whereBetween('p.created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
            ->select('m.nama as nama_menu', DB::raw('SUM(p.jumlah) as total_terjual'))
            ->groupBy('p.menu_id', 'm.nama')
            ->orderByDesc('total_terjual')
            ->limit(3)
            ->get();

        return view('admin_laporan', compact(
            'reservasiHariIni',
            'reservasiMingguIni',
            'reservasiBulanIni',
            'pelangganBaru',
            'totalsPerBulan',
            'koki',
            'pelayan',
            'menuTerlaris',
            'bulanDipilih' // Agar bisa ditandai di <select>
        ));
    }
}
