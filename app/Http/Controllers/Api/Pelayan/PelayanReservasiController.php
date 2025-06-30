<?php

namespace App\Http\Controllers\Api\Pelayan;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

class PelayanReservasiController extends Controller
{
    // Ambil daftar reservasi + daftar meja tersedia
    public function index()
    {
        $reservasi = Reservasi::whereDoesntHave('meja') // filter: belum ada entri di tabel pivot
            ->with('pengguna:id,nama')
            ->orderBy('tanggal', 'desc')
            ->orderBy('sesi', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_pengguna' => $item->pengguna->nama ?? 'Tidak diketahui',
                    'sesi' => $item->sesi,
                    'tanggal' => $item->tanggal,
                    'jumlah_tamu' => $item->jumlah_tamu,
                    'status' => $item->status,
                ];
            });

        // Ambil semua data meja, tidak peduli status
        $meja = Meja::orderBy('nomor')->get();

        return response()->json([
            'status' => true,
            'message' => 'Data reservasi dan daftar meja untuk pelayan',
            'data' => [
                'reservasi' => $reservasi,
                'meja' => $meja,
            ],
        ]);
    }

    public function show($reservasiId)
    {
        $reservasi = Reservasi::with(['pengguna:id,nama', 'meja', 'pesanan.menu'])->find($reservasiId);

        if (!$reservasi) {
            return response()->json([
                'status' => false,
                'message' => 'Reservasi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail reservasi',
            'data' => [
                'id' => $reservasi->id,
                'kode_reservasi' => $reservasi->kode_reservasi,
                'nama_pengguna' => $reservasi->pengguna->nama ?? 'Tidak diketahui',
                'sesi' => $reservasi->sesi,
                'tanggal' => $reservasi->tanggal,
                'jumlah_tamu' => $reservasi->jumlah_tamu,
                'status' => $reservasi->status,
                'meja' => $reservasi->meja->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'nama' => $m->nama,
                        'kapasitas' => $m->kapasitas,
                        'status' => $m->status,
                    ];
                }),
                'pesanan' => $reservasi->pesanan->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'menu_id' => $p->menu_id,
                        'nama_menu' => $p->menu->nama ?? 'Tidak diketahui',
                        'harga' => $p->menu->harga ?? null,
                        'jumlah' => $p->jumlah,
                        'catatan' => $p->catatan,
                        'status' => $p->status,
                    ];
                }),
            ],
        ]);
    }

    // public function konfirmasiMeja(Request $request, $reservasiId)
    // {
    //     $request->validate([
    //         'meja_id' => 'required|exists:meja,id',
    //     ]);

    //     $mejaId = $request->input('meja_id');

    //     $reservasi = Reservasi::find($reservasiId);
    //     if (!$reservasi) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Reservasi tidak ditemukan',
    //         ], 404);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         // Simpan relasi meja ke reservasi (pivot)
    //         $reservasi->meja()->syncWithoutDetaching([$mejaId]);

    //         // Jangan update status reservasi di sini
    //         // $reservasi->status = 'diterima';
    //         // $reservasi->save();

    //         // Update status meja jadi 'digunakan'
    //         $meja = Meja::find($mejaId);
    //         if ($meja->status !== 'digunakan') {
    //             $meja->status = 'digunakan';
    //             $meja->save();
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Meja berhasil dikonfirmasi untuk reservasi',
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Gagal mengonfirmasi meja: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function konfirmasiMeja(Request $request, $reservasiId)
    {
        $request->validate([
            'meja_id' => 'required|exists:meja,id',
        ]);

        $mejaId = $request->input('meja_id');

        $reservasi = Reservasi::find($reservasiId);
        if (!$reservasi) {
            return response()->json([
                'status' => false,
                'message' => 'Reservasi tidak ditemukan',
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Simpan relasi meja ke reservasi (pivot)
            $reservasi->meja()->syncWithoutDetaching([$mejaId]);

            // Simpan pelayan_id jika belum ada
            if (is_null($reservasi->pelayan_id)) {
                $reservasi->pelayan_id = Auth::id(); // pelayan yang login
                $reservasi->save();
            }

            // Update status meja jadi 'digunakan'
            $meja = Meja::find($mejaId);
            if ($meja->status !== 'digunakan') {
                $meja->status = 'digunakan';
                $meja->save();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Meja berhasil dikonfirmasi untuk reservasi',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengonfirmasi meja: ' . $e->getMessage(),
            ], 500);
        }
    }


    // public function verifikasiKehadiran($reservasiId)
    // {
    //     $reservasi = Reservasi::find($reservasiId);
    //     if (!$reservasi) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Reservasi tidak ditemukan',
    //         ], 404);
    //     }

    //     // Update status reservasi jadi "hadir" atau "diterima"
    //     $reservasi->status = 'diterima'; // atau 'hadir' sesuai kebutuhan
    //     $reservasi->save();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Reservasi telah diverifikasi kehadirannya',
    //     ]);
    // }

    public function verifikasiKehadiran($reservasiId)
    {
        $reservasi = Reservasi::find($reservasiId);
        if (!$reservasi) {
            return response()->json([
                'status' => false,
                'message' => 'Reservasi tidak ditemukan',
            ], 404);
        }

        $statusLama = $reservasi->status;
        $statusBaru = 'diterima';

        if ($statusLama !== $statusBaru) {
            $reservasi->status = $statusBaru;
            $reservasi->save();

            // ✅ Buat notifikasi ke user
            Notifikasi::create([
                'pengguna_id' => $reservasi->pengguna_id,
                'judul' => 'Reservasi Dikonfirmasi Saat Hadir',
                'pesan' => 'Reservasi Anda telah dikonfirmasi saat kedatangan. Selamat menikmati layanan kami!',
                'dibaca' => false
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Reservasi telah diverifikasi kehadirannya',
        ]);
    }
}
