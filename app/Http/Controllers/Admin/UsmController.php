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
        $pengguna = Pengguna::all();
        return view('admin_usm', compact('pengguna'));
    }

    public function store(Request $request)
    {
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
        $pengguna = Pengguna::findOrFail($id);
        return response()->json($pengguna);
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
