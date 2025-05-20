<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = Pengguna::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Email atau password salah']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $role = strtolower($user->role);

        if ($role === 'koki') {
            return redirect()->route('koki.dashboard');
        } elseif ($role === 'Pelayan') {
            return redirect()->route('pelayan.dashboard');
        } elseif ($role === 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        Auth::logout();
        return redirect()->route('login')->withErrors(['role' => 'Role tidak dikenali']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
