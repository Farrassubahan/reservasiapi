<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('admin_createmenu', compact('menus'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:makanan,minuman,snack',
            'harga' => 'required|numeric',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Jika ada file gambar di-upload
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('img/gambar_menu'); // simpan ke folder public/img/gambar_menu

            // Buat folder jika belum ada
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Pindahkan file ke folder tujuan
            $file->move($destinationPath, $filename);

            // Simpan path relatif untuk disimpan di database
            $validated['gambar'] = 'img/gambar_menu/' . $filename;
        }

        $validated['status'] = 'tersedia';

        Menu::create($validated);

        // Response sukses
        return response()->json([
            'message' => 'Menu berhasil ditambahkan.'
        ]);
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        return response()->json($menu);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:makanan,minuman,snack',
            'harga' => 'required|numeric',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($menu->gambar && file_exists(public_path($menu->gambar))) {
                File::delete(public_path($menu->gambar));
            }

            $file = $request->file('gambar');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('img/gambar_menu'); // ⬅️ simpan ke sini

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            // Simpan path relatif untuk database
            $validated['gambar'] = 'img/gambar_menu/' . $filename;
        }

        $menu->update($validated);

        return response()->json([
            'message' => 'Menu berhasil diperbarui.'
        ]);
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        // Hapus file gambar dari public/gambar_menu
        if ($menu->gambar && file_exists(public_path($menu->gambar))) {
            File::delete(public_path($menu->gambar));
        }

        $menu->delete();

        return redirect()->back()->with('success', 'Menu berhasil dihapus.');
    }

    public function ubahStok(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:tersedia,kosong'
        ]);

        $menu = Menu::findOrFail($id);
        $menu->status = $request->status;
        $menu->save();

        return back()->with('success', 'Status stok berhasil diperbarui.');
    }
}
