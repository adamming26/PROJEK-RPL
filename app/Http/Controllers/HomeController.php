<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $barangList = Barang::where('status', 'Aktif')->take(6)->get();
        $ruanganList = Ruangan::where('status', '!=', 'Nonaktif')->take(4)->get();

        return view('home', compact('barangList', 'ruanganList'));
    }

    public function fasilitas(Request $request)
    {
        $kategori = $request->input('kategori', 'semua');
        $search = $request->input('search');

        $barangQuery = Barang::where('status', 'Aktif');
        $ruanganQuery = Ruangan::where('status', '!=', 'Nonaktif');

        if ($search) {
            $barangQuery->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });

            $ruanganQuery->where(function ($q) use ($search) {
                $q->where('nama_ruangan', 'like', "%{$search}%")
                  ->orWhere('kode_ruangan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $barangList = ($kategori === 'lab') ? collect() : $barangQuery->get();
        $ruanganList = ($kategori === 'barang') ? collect() : $ruanganQuery->get();

        return view('fasilitas.index', compact('barangList', 'ruanganList', 'kategori', 'search'));
    }
}
