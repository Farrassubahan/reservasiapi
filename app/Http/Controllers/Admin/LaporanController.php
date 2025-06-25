<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengguna;
use App\Models\Reservasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulanDipilih = $request->input('bulan', Carbon::now()->month); // angka 1-12
        $tahunDipilih = $request->input('tahun') ?? Carbon::now()->year;

        $startOfSelectedMonth = Carbon::create($tahunDipilih, $bulanDipilih, 1)->startOfDay();
        $endOfSelectedMonth = Carbon::create($tahunDipilih, $bulanDipilih, 1)->endOfMonth()->endOfDay();

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

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
        $koki = Pengguna::where('role', 'koki')->get()->map(function ($pengguna) use ($startOfSelectedMonth, $endOfSelectedMonth) {
            $jumlahRating = DB::table('rating_pegawai')
                ->where('pegawai_id', $pengguna->id)
                ->where('tipe', 'koki')
                ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
                ->count();

            $rataRating = DB::table('rating_pegawai')
                ->where('pegawai_id', $pengguna->id)
                ->where('tipe', 'koki')
                ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
                ->avg('rating');

            $persentase = $rataRating ? round(($rataRating / 5) * 100, 2) : 0;

            return [
                'nama' => $pengguna->nama,
                'jumlah_rating' => $jumlahRating,
                'rata_rating' => $rataRating ? round($rataRating, 2) : '-',
                'persentase_rating' => $persentase . '%',
            ];
        });

        $pelayan = Pengguna::where('role', 'pelayan')->get()->map(function ($pengguna) use ($startOfSelectedMonth, $endOfSelectedMonth) {
            $jumlahRating = DB::table('rating_pegawai')
                ->where('pegawai_id', $pengguna->id)
                ->where('tipe', 'pelayan')
                ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
                ->count();

            $rataRating = DB::table('rating_pegawai')
                ->where('pegawai_id', $pengguna->id)
                ->where('tipe', 'pelayan')
                ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
                ->avg('rating');

            $persentase = $rataRating ? round(($rataRating / 5) * 100, 2) : 0;

            return [
                'nama' => $pengguna->nama,
                'jumlah_rating' => $jumlahRating,
                'rata_rating' => $rataRating ? round($rataRating, 2) : '-',
                'persentase_rating' => $persentase . '%',
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

        // === Pendapatan Bulanan ===
        $totalPendapatan = DB::table('pesanan as p')
            ->join('menu as m', 'p.menu_id', '=', 'm.id')
            ->where('p.status', 'siap')
            ->whereBetween('p.created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
            ->selectRaw('SUM(p.jumlah * m.harga) as total')
            ->value('total') ?? 0;

        return view('admin_laporan', compact(
            'reservasiHariIni',
            'reservasiMingguIni',
            'reservasiBulanIni',
            'pelangganBaru',
            'totalsPerBulan',
            'koki',
            'pelayan',
            'menuTerlaris',
            'bulanDipilih',
            'totalPendapatan'
        ));
    }

    // pdf 
    public function exportPDF(Request $request)
    {
        $bulanDipilih = $request->input('bulan', Carbon::now()->month);
        $tahunDipilih = $request->input('tahun', Carbon::now()->year);

        $startOfSelectedMonth = Carbon::create($tahunDipilih, $bulanDipilih, 1)->startOfDay();
        $endOfSelectedMonth = Carbon::create($tahunDipilih, $bulanDipilih, 1)->endOfMonth()->endOfDay();

        $reservasiBulanIni = Reservasi::whereBetween('tanggal', [$startOfSelectedMonth, $endOfSelectedMonth])
            ->where('status', '!=', 'dibatalkan')->count();

        $pelangganBaru = Pengguna::where('role', 'pelanggan')
            ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
            ->count();

        $totalPendapatan = DB::table('pesanan as p')
            ->join('menu as m', 'p.menu_id', '=', 'm.id')
            ->where('p.status', 'siap')
            ->whereBetween('p.created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
            ->selectRaw('SUM(p.jumlah * m.harga) as total')
            ->value('total') ?? 0;

        $koki = Pengguna::where('role', 'koki')->get()->map(function ($pengguna) use ($startOfSelectedMonth, $endOfSelectedMonth) {
            $jumlahRating = DB::table('rating_pegawai')
                ->where('pegawai_id', $pengguna->id)
                ->where('tipe', 'koki')
                ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
                ->count();

            $rataRating = DB::table('rating_pegawai')
                ->where('pegawai_id', $pengguna->id)
                ->where('tipe', 'koki')
                ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
                ->avg('rating');

            return [
                'nama' => $pengguna->nama,
                'jumlah_rating' => $jumlahRating,
                'rata_rating' => $rataRating ? round($rataRating, 2) : '-',
            ];
        });

        $pelayan = Pengguna::where('role', 'pelayan')->get()->map(function ($pengguna) use ($startOfSelectedMonth, $endOfSelectedMonth) {
            $jumlahRating = DB::table('rating_pegawai')
                ->where('pegawai_id', $pengguna->id)
                ->where('tipe', 'pelayan')
                ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
                ->count();

            $rataRating = DB::table('rating_pegawai')
                ->where('pegawai_id', $pengguna->id)
                ->where('tipe', 'pelayan')
                ->whereBetween('created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
                ->avg('rating');

            return [
                'nama' => $pengguna->nama,
                'jumlah_rating' => $jumlahRating,
                'rata_rating' => $rataRating ? round($rataRating, 2) : '-',
            ];
        });

        $menuTerlaris = DB::table('pesanan as p')
            ->join('menu as m', 'p.menu_id', '=', 'm.id')
            ->whereIn('p.status', ['siap', 'disajikan'])
            ->whereBetween('p.created_at', [$startOfSelectedMonth, $endOfSelectedMonth])
            ->select('m.nama as nama_menu', DB::raw('SUM(p.jumlah) as total_terjual'))
            ->groupBy('p.menu_id', 'm.nama')
            ->orderByDesc('total_terjual')
            ->limit(3)
            ->get();

        $pdf = Pdf::loadView('pdf', compact(
            'bulanDipilih',
            'tahunDipilih',
            'reservasiBulanIni',
            'pelangganBaru',
            'totalPendapatan',
            'koki',
            'pelayan',
            'menuTerlaris'
        ));
        return $pdf->download('laporan-bulanan.pdf');
    }
}
