<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

class UsmController extends Controller
{
    public function index()
    {
        $pengguna = Pengguna::where('role', '!=', 'pelanggan')->get();
        return view('admin_usm', compact('pengguna'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:pengguna,email',
            'telepon' => ['required', 'regex:/^(\+62|62|08)\d{8,11}$/'],
            'password' => ['required', 'min:6', 'max:16', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&!?])[A-Za-z\d@#$%^&!?]{6,16}$/'],
            'role' => 'required'
        ], [
            'telepon.regex' => 'Nomor telepon harus diawali +62 dan memiliki 10-13 digit angka.',
            'password.regex' => 'Password harus 6–16 karakter dan mengandung huruf besar, huruf kecil, angka, dan simbol (@#$%^&*!?/).',
        ]);

        Pengguna::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan.');
    }


    public function show($id)
    {
        $user = Pengguna::findOrFail($id);
        return response()->json($user);
    }



    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $pengguna->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $pengguna->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return response()->json(['message' => 'Pengguna berhasil diperbarui.']);
    }


    public function destroy($id)
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
