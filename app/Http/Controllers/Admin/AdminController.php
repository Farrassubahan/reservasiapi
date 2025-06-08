<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard() 
    {
        // Grafik per bulan 
        $labels = [];
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('M');
            $data[] = Reservasi::whereYear('tanggal', now()->year)
                ->whereMonth('tanggal', $i)
                ->count();
        }

        // Statistik
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $hariIni = Reservasi::whereDate('tanggal', $today)->count();
        $mingguIni = Reservasi::whereBetween('tanggal', [$weekStart, $weekEnd])->count();
        $bulanIni = Reservasi::whereBetween('tanggal', [$monthStart, $monthEnd])->count();

        $statusMenunggu = Reservasi::where('status', 'menunggu')->count();
        $statusDiterima = Reservasi::where('status', 'diterima')->count();
        $statusDibatalkan = Reservasi::where('status', 'dibatalkan')->count();
        $statusSelesai = Reservasi::where('status', 'selesai')->count();

        $dataReservasiHariIniLengkap = $this->getDataReservasiHariIniLengkap();

        return view('admin_dashboard', compact(
            'labels',
            'data',
            'hariIni',
            'mingguIni',
            'bulanIni',
            'statusMenunggu',
            'statusDiterima',
            'statusDibatalkan',
            'statusSelesai',
            'dataReservasiHariIniLengkap'
        ));
    }
 

    private function getDataReservasiHariIniLengkap()
    {
        $today = Carbon::today();

        return Reservasi::whereDate('tanggal', $today)->get();
    }

    public function editReservasi($id)
    {
        $reservasi = Reservasi::with('pengguna')->findOrFail($id);
        return view('admin.reservasi.edit', compact('reservasi'));
    }

    public function hapusReservasi($id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $reservasi->delete();

        return redirect()->back()->with('success', 'Reservasi berhasil dihapus.');
    }
}
