<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengguna;


class ProfileController extends Controller
{
    /**
     * Tampilkan profil user yang sedang login
     */
    public function show()
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => [
                'nama'      => $user->nama,
                'email'     => $user->email,
                'telepon'   => $user->telepon,
                'role'      => $user->role,
                'foto'      => $user->foto_url,   // gunakan accessor foto_url
                'google_id' => $user->google_id,
            ]
        ], 200);
    }

    /**
     * Update profil user yang sedang login
     */
    public function update(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        
    // Simpan nama
    // ✅ Tambahkan ini: validasi input
    $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'foto' => 'nullable|string', // base64 string
    ]);

    // Jika ada foto baru (base64)
    if (!empty($validated['foto'])) {
        // Ekstrak base64
        preg_match("/^data:image\/(\w+);base64,/", $validated['foto'], $type);
        $image = substr($validated['foto'], strpos($validated['foto'], ',') + 1);
        $image = base64_decode($image);
        $extension = $type[1] ?? 'jpg';

        // Buat nama file unik
        $fileName = 'foto_' . time() . '.' . $extension;

        // Simpan ke storage/app/public/profile
        Storage::disk('public')->put("profile/$fileName", $image);


        // Simpan nama file ke DB
        $user->foto = $fileName;
    }

    $user->save();

    return response()->json([
        'message' => 'Profil berhasil diperbarui',
        'data' => [
            'nama' => $user->nama,
            'foto' => $user->foto_url,
        ]
    ]);
}
}
