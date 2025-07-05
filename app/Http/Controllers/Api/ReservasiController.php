<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Pengguna;
use App\Models\Reservasi;
use App\Models\Notifikasi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

// use Illuminate\Http\Request;
// use App\Models\Reservasi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReservasiController extends Controller
{
    public function buatReservasi(Request $request)
    {
        $request->validate([
            'pengguna_id' => 'required|exists:pengguna,id',
            'tanggal' => 'required|date',
            'sesi' => 'required|in:sarapan_1,sarapan_2,siang_1,siang_2,malam_1,malam_2',
            'jumlah_tamu' => 'required|integer|min:1',
            'pesanan' => 'required|array|min:1',
            'pesanan.*.menu_id' => 'required|exists:menu,id',
            'pesanan.*.jumlah' => 'required|integer|min:1',
            'pesanan.*.catatan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $user = Pengguna::find($request->pengguna_id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pengguna tidak ditemukan.'
                ], 404);
            }

            $kodeReservasi = strtoupper(Str::random(8));
            $reservasi = Reservasi::create([
                'pengguna_id' => $user->id,
                'kode_reservasi' => $kodeReservasi,
                'tanggal' => $request->tanggal,
                'sesi' => $request->sesi,
                'jumlah_tamu' => $request->jumlah_tamu,
                'status' => 'menunggu'
            ]);

            foreach ($request->pesanan as $item) {
                Pesanan::create([
                    'pengguna_id' => $user->id,
                    'reservasi_id' => $reservasi->id,
                    'menu_id' => $item['menu_id'],
                    'jumlah' => $item['jumlah'],
                    'catatan' => $item['catatan'] ?? null,
                    'status' => 'menunggu'
                ]);

                // Update jumlah_terjual pada menu
                // Menu::where('id', $item['menu_id'])->increment('jumlah_terjual', $item['jumlah']);
                $menu = Menu::find($item['menu_id']);
                if ($menu) {
                    $menu->jumlah_terjual = ($menu->jumlah_terjual ?? 0) + $item['jumlah'];
                    $menu->save();
                }
            }

            DB::commit();

            // 🔔 Buat Notifikasi Pengingat Kedatangan BARU
            Notifikasi::create([
                'pengguna_id' => $user->id,
                'judul' => 'Pengingat Kedatangan Reservasi',
                'pesan' => 'Anda memiliki reservasi pada hari ' . \Carbon\Carbon::parse($reservasi->tanggal)->translatedFormat('l, d F Y') . ' untuk sesi ' . str_replace('_', ' ', $reservasi->sesi) . '. Mohon hadir tepat waktu.',
                'tipe' => 'pengingat_kedatangan',
                'status' => 'menunggu',
                'dibaca' => false
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Reservasi berhasil dibuat.',
                'data' => $reservasi
            ], 201);

            return response()->json([
                'status' => true,
                'message' => 'Reservasi berhasil dibuat.',
                'data' => $reservasi
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $reservasi = Reservasi::with(['pesanan', 'pengguna'])->find($id);
        if (!$reservasi) {
            return response()->json([
                'status' => false,
                'message' => 'Reservasi tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $reservasi
        ]);
    }

    public function detailPembayaran($id)
    {
        try {
            $reservasi = Reservasi::with([
                'pengguna:id,nama,email',
                'meja:id,nomor,kapasitas',
                'pesanan.menu:id,nama,harga'
            ])->where('id', $id)->first();

            if (!$reservasi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Reservasi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Detail reservasi berhasil diambil',
                'data' => $reservasi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifikasiKehadiran(Request $request)
    {
        $request->validate([
            'kode_reservasi' => 'required|string|exists:reservasi,kode_reservasi',
        ]);

        $reservasi = Reservasi::where('kode_reservasi', $request->kode_reservasi)->first();

        if (!$reservasi) {
            return response()->json([
                'status' => false,
                'message' => 'Kode reservasi tidak ditemukan.'
            ], 404);
        }

        // Ubah status jadi "diterima"
        $reservasi->status = 'diterima';
        $reservasi->save();

        return response()->json([
            'status' => true,
            'message' => 'Reservasi berhasil diverifikasi.',
            'data' => $reservasi
        ]);
    }


    // buat ambil data jam
    public function getSesiTersedia(Request $request)
    {
        $tanggal = $request->query('tanggal');

        if (!$tanggal) {
            return response()->json([
                'status' => false,
                'message' => 'Tanggal wajib diisi'
            ], 400);
        }

        $tanggalWib = Carbon::parse($tanggal)->timezone('Asia/Jakarta')->toDateString();
        $now = Carbon::now('Asia/Jakarta');

        $sesiList = [
            [
                'label' => 'Sarapan',
                'jamList' => [
                    ['key' => 'sarapan_1', 'label' => '07:00 - 10:00', 'mulai' => '07:00', 'selesai' => '10:00'],
                    ['key' => 'sarapan_2', 'label' => '10:00 - 12:00', 'mulai' => '10:00', 'selesai' => '12:00'],
                ]
            ],
            [
                'label' => 'Makan Siang',
                'jamList' => [
                    ['key' => 'siang_1', 'label' => '12:00 - 14:00', 'mulai' => '12:00', 'selesai' => '14:00'],
                    ['key' => 'siang_2', 'label' => '14:00 - 17:00', 'mulai' => '14:00', 'selesai' => '17:00'],
                ]
            ],
            [
                'label' => 'Makan Malam',
                'jamList' => [
                    ['key' => 'malam_1', 'label' => '17:00 - 19:00', 'mulai' => '17:00', 'selesai' => '19:00'],
                    ['key' => 'malam_2', 'label' => '19:00 - 22:00', 'mulai' => '19:00', 'selesai' => '22:00'],
                ]
            ]
        ];

        $maksimalKuota = 12;
        $hasil = [];

        foreach ($sesiList as $kategori) {
            $kategoriData = [
                'label' => $kategori['label'],
                'jamList' => []
            ];

            foreach ($kategori['jamList'] as $jam) {
                $sesi = $jam['key'];
                $mulai = Carbon::parse($tanggalWib . ' ' . $jam['mulai'], 'Asia/Jakarta');
                $selesai = Carbon::parse($tanggalWib . ' ' . $jam['selesai'], 'Asia/Jakarta');

                // Lewati sesi yang sudah lewat
                if ($now->greaterThan($selesai)) {
                    continue;
                }

                // Hitung jumlah reservasi per sesi
                $jumlahReservasi = Reservasi::where('tanggal', $tanggalWib)
                    ->where('sesi', $sesi)
                    ->whereIn('status', ['menunggu', 'diterima'])
                    ->count();

                if ($jumlahReservasi < $maksimalKuota) {
                    $kategoriData['jamList'][] = [
                        'key' => $sesi,
                        'label' => $jam['label'],
                        'tersedia' => max(0, $maksimalKuota - $jumlahReservasi)
                    ];
                }
            }

            if (!empty($kategoriData['jamList'])) {
                $hasil[] = $kategoriData;
            }
        }

        return response()->json([
            'status' => true,
            'data' => $hasil
        ]);
    }

    public function absen($kode_reservasi)
    {
        $reservasi = Reservasi::where('kode_reservasi', $kode_reservasi)->first();

        if (!$reservasi) {
            return response()->json(['status' => false, 'message' => 'Reservasi tidak ditemukan.'], 404);
        }

        $reservasi->status = 'diterima';
        $reservasi->save();

        return response()->json(['status' => true, 'message' => 'Reservasi ditandai hadir.']);
    }
    public function cekStatusPesanan($id)
    {
        $reservasi = Reservasi::with('pesanan')->find($id);

        if (!$reservasi) {
            return response()->json([
                'status' => false,
                'message' => 'Reservasi tidak ditemukan.'
            ], 404);
        }

        // Ambil semua status pesanan dalam reservasi
        $statusPesanan = $reservasi->pesanan->pluck('status');

        // Contoh logika: jika semua status "siap"
        if ($statusPesanan->every(fn($s) => $s === 'siap')) {
            return response()->json([
                'status' => true,
                'message' => 'Semua pesanan sudah siap.',
                'data' => [
                    'status' => 'siap',
                    'pesanan' => $reservasi->pesanan
                ]
            ]);
        }

        // Jika masih ada yang belum siap
        return response()->json([
            'status' => true,
            'message' => 'Pesanan masih diproses.',
            'data' => [
                'status' => 'diproses',
                'pesanan' => $reservasi->pesanan
            ]
        ]);
    }
    public function getStatusPesanan($id)
    {
        $reservasi = Reservasi::find($id);

        if (!$reservasi) {
            return response()->json([
                'message' => 'Reservasi tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'data' => [
                'status' => $reservasi->status
            ]
        ]);
    }
}
