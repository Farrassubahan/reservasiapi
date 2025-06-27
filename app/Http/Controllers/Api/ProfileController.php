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
                'nama'    => $user->nama,
                'email'   => $user->email,
                'telepon' => $user->telepon,
                'foto'    => $user->foto ? url('img/foto_profile/' . $user->foto) : null,
            ]
        ], 200);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama'    => 'required|string|max:255',
            'telepon' => 'required|digits_between:10,15',
            'foto'    => 'nullable|string',
        ]);

        $user->nama = $validated['nama'];
        $user->telepon = $validated['telepon'];

        if (!empty($validated['foto'])) {
            preg_match("/^data:image\/(\w+);base64,/", $validated['foto'], $type);
            $image = substr($validated['foto'], strpos($validated['foto'], ',') + 1);
            $image = base64_decode($image);
            $extension = $type[1] ?? 'jpg';

            $fileName = md5($user->id . time()) . '.' . $extension;

            // Hapus foto lama
            if ($user->foto && file_exists(public_path('img/foto_profile/' . $user->foto))) {
                unlink(public_path('img/foto_profile/' . $user->foto));
            }

            // Simpan ke folder public/img/foto profile/
            $path = public_path('img/foto_profile');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            file_put_contents($path . '/' . $fileName, $image);
            $user->foto = $fileName;
        }

        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data' => [
                'nama'    => $user->nama,
                'telepon' => $user->telepon,
                'foto'    => $user->foto ? url('img/foto_profile/' . $user->foto) : null,
            ]
        ]);
    }
}
