<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{


    public function index()
    {
        $menu = Menu::latest()->paginate(5);

        return new MenuResource(true, 'List Data Menu', $menu);
    }



    public function store(Request $request)
    {

        $validasi = Validator::make($request->all(), [
            'nama'     => 'required',
            'kategori'   => 'required',
            'deskripsi'   => 'required',
            'foto'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        if ($validasi->fails()) {
            return response()->json($validasi->errors(), 422);
        }

        $foto = $request->file('foto');
        $foto->storeAs('public/menus', $foto->hashName());

        $menu = Menu::create([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'foto' => $foto->hashName()
        ]);


        return new MenuResource(true, 'Menu Berhasil Ditambahkan', $menu);
    }

    public function show(Menu $menu)
    {
        return new MenuResource(true, 'Data Menu', $menu);
    }


    public function update(Request $request, Menu $menu)
    {


        $validasi = Validator::make($request->all(), [
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
        ]);


        if ($validasi->fails()) {

            return response()->json($validasi->errors(), 422);
        }


        //jika gambar ada
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $foto->storeAs('public/menus', $foto->hashName());

            Storage::delete('public/menus/' . $menu->foto);

            $menu->update([
                'nama' => $request->nama,
                'kategori' => $request->kategori,
                'deskripsi' => $request->deskripsi,
                'foto' => $foto->hashName()
            ]);
        } else {
            $menu->update([
                'nama' => $request->nama,
                'kategori' => $request->kategori,
                'deskripsi' => $request->deskripsi,
            ]);
        }


        return new MenuResource(true, 'data menu berhasil di ubah', $menu);
    }


    public function destroy(Menu $menu)
    {

        Storage::delete('public/menus/' . $menu->foto);

        $menu->delete();

        return new MenuResource(true, 'Data berhasil di hapus', null);
    }
}
