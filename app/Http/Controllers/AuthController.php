<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengguna;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
// use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;



class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pengguna',
            'password' => 'required|string|min:6|confirmed',
            'telepon' => 'required|string|max:15',
        ]);

        $pengguna = Pengguna::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil',
            'pengguna' => $pengguna,
        ], 201);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // public function handleGoogleCallback()
    // {
    //     try {
    //         $googleUser = Socialite::driver('google')->stateless()->user();

    //         $user = Pengguna::where('google_id', $googleUser->getId())
    //             ->orWhere('email', $googleUser->getEmail())
    //             ->first();

    //         if (!$user) {
    //             $user = Pengguna::create([
    //                 'nama' => $googleUser->getName(),
    //                 'email' => $googleUser->getEmail(),
    //                 'telepon' => '',
    //                 'google_id' => $googleUser->getId(),
    //                 'password' => Hash::make(Str::random(16)),
    //             ]);
    //         } else {
    //             if (!$user->google_id) {
    //                 $user->google_id = $googleUser->getId();
    //                 $user->save();
    //             }
    //         }

    //         $token = $user->createToken('auth_token')->plainTextToken;

    //         // Redirect ke frontend Ionic (pastikan login.page.ts menangani token)
    //         $frontendUrl = 'http://localhost:8100/login';
    //         return redirect()->away("{$frontendUrl}?token={$token}&nama=" . urlencode($user->nama) . "&email=" . urlencode($user->email));
    //     } catch (\Exception $e) {
    //         $errorUrl = 'http://localhost:8100/login?error=' . urlencode('Gagal login dengan Google: ' . $e->getMessage());
    //         return redirect()->away($errorUrl);
    //     }
    // }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = Pengguna::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if (!$user) {
                $user = Pengguna::create([
                    'nama' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'telepon' => '',
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(16)),
                ]);
            } else {
                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                    $user->save();
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            // Render halaman yang mengirim token ke opener dan menutup popup
            return response()->view('google_callback_success', [
                'token' => $token,
                'nama' => $user->nama,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            return response()->view('google_callback_error', [
                'message' => 'Gagal login dengan Google: ' . $e->getMessage()
            ]);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = Pengguna::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email Atau Password Salah '], 401);
        }

        // Selalu cek password, entah Google user atau bukan
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email Atau Password Salah'], 401);
        }

        $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }



    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:pengguna,email',
            'new_password' => 'required|min:6',
        ]);

        $user = \App\Models\Pengguna::where('email', $request->email)->first();

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diperbarui.'
        ]);
    }
}
