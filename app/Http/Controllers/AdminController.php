<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalBarang = Barang::count();
        $totalRuangan = Ruangan::count();
        $totalStokFasilitas = Barang::sum('stok_total');
        $totalFasilitasTersedia = Barang::sum('stok_tersedia');

        $totalPeminjamanBerjalan = Peminjaman::whereIn('status', [
            'MENUNGGU PERSETUJUAN',
            'DISETUJUI',
            'MENUNGGU PENYERAHAN',
            'DIPINJAM',
            'MENUNGGU PENGEMBALIAN'
        ])->count();

        $totalPengajuan = Peminjaman::count();

        // 5 Transaksi berjalan terbaru
        $transaksiTerbaru = Peminjaman::with(['user', 'barang', 'ruangan', 'persetujuanTerakhir.dosen'])
            ->latest('id_peminjaman')
            ->take(7)
            ->get();

        // Statistik bulanan peminjaman (6 bulan terakhir)
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('M Y');
            $chartData[] = Peminjaman::whereYear('tanggal_pengajuan', $month->year)
                ->whereMonth('tanggal_pengajuan', $month->month)
                ->count();
        }

        return view('admin.dashboard', compact(
            'totalBarang',
            'totalRuangan',
            'totalStokFasilitas',
            'totalFasilitasTersedia',
            'totalPeminjamanBerjalan',
            'totalPengajuan',
            'transaksiTerbaru',
            'chartLabels',
            'chartData'
        ));
    }

    public function kelolaFasilitas(Request $request)
    {
        $tab = $request->input('tab', 'semua');
        $search = $request->input('search');

        $barangQuery = Barang::query();
        $ruanganQuery = Ruangan::query();

        if ($search) {
            $barangQuery->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });

            $ruanganQuery->where(function ($q) use ($search) {
                $q->where('nama_ruangan', 'like', "%{$search}%")
                  ->orWhere('kode_ruangan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $barangList = ($tab === 'lab') ? collect() : $barangQuery->latest('id_barang')->get();
        $ruanganList = ($tab === 'barang') ? collect() : $ruanganQuery->latest('id_ruangan')->get();

        return view('admin.kelola-fasilitas', compact('barangList', 'ruanganList', 'tab', 'search'));
    }

    public function storeBarang(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'unique:barang,kode_barang', 'max:50'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'stok_total' => ['required', 'integer', 'min:0'],
            'kondisi' => ['required', 'in:BAIK,RUSAK,TIDAK LENGKAP'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'kode_barang.required' => 'Kode fasilitas wajib diisi.',
            'kode_barang.unique' => 'Kode fasilitas sudah digunakan.',
            'nama_barang.required' => 'Nama fasilitas wajib diisi.',
            'stok_total.required' => 'Stok total wajib ditentukan.',
        ]);

        Barang::create([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'kategori' => $validated['kategori'],
            'stok_total' => $validated['stok_total'],
            'stok_tersedia' => $validated['stok_total'], // Awalnya tersedia = total
            'kondisi' => $validated['kondisi'],
            'status' => $validated['status'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return redirect()->route('admin.kelola-fasilitas', ['tab' => 'barang'])
            ->with('success', "Barang [{$validated['nama_barang']}] berhasil ditambahkan ke inventaris.");
    }

    public function updateBarang(Request $request, int $id)
    {
        $barang = Barang::findOrFail($id);

        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:50', 'unique:barang,kode_barang,' . $barang->id_barang . ',id_barang'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'stok_total' => ['required', 'integer', 'min:0'],
            'stok_tersedia' => ['required', 'integer', 'min:0'],
            'kondisi' => ['required', 'in:BAIK,RUSAK,TIDAK LENGKAP'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $barang->update($validated);

        return redirect()->route('admin.kelola-fasilitas', ['tab' => 'barang'])
            ->with('success', "Data barang [{$barang->nama_barang}] berhasil diperbarui.");
    }

    public function destroyBarang(int $id)
    {
        $barang = Barang::findOrFail($id);

        // Validasi transaksi aktif: tidak boleh menghapus jika sedang dipinjam
        $transaksiAktif = Peminjaman::where('id_barang', $barang->id_barang)
            ->whereIn('status', ['MENUNGGU PERSETUJUAN', 'DISETUJUI', 'MENUNGGU PENYERAHAN', 'DIPINJAM', 'MENUNGGU PENGEMBALIAN'])
            ->count();

        if ($transaksiAktif > 0) {
            return back()->with('error', "Barang [{$barang->nama_barang}] tidak dapat dihapus karena memiliki {$transaksiAktif} transaksi peminjaman yang sedang aktif.");
        }

        $barang->delete();

        return redirect()->route('admin.kelola-fasilitas', ['tab' => 'barang'])
            ->with('success', "Barang [{$barang->nama_barang}] berhasil dihapus dari sistem.");
    }

    public function storeRuangan(Request $request)
    {
        $validated = $request->validate([
            'kode_ruangan' => ['required', 'string', 'unique:ruangan,kode_ruangan', 'max:50'],
            'nama_ruangan' => ['required', 'string', 'max:255'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'lokasi' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Tersedia,Perlu Izin,Aktif,Nonaktif'],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'kode_ruangan.required' => 'Kode ruangan wajib diisi.',
            'kode_ruangan.unique' => 'Kode ruangan sudah digunakan.',
            'nama_ruangan.required' => 'Nama ruangan wajib diisi.',
            'kapasitas.required' => 'Kapasitas ruangan wajib diisi.',
            'lokasi.required' => 'Lokasi ruangan wajib diisi.',
        ]);

        Ruangan::create($validated);

        return redirect()->route('admin.kelola-fasilitas', ['tab' => 'lab'])
            ->with('success', "Laboratorium / Ruangan [{$validated['nama_ruangan']}] berhasil ditambahkan.");
    }

    public function updateRuangan(Request $request, int $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $validated = $request->validate([
            'kode_ruangan' => ['required', 'string', 'max:50', 'unique:ruangan,kode_ruangan,' . $ruangan->id_ruangan . ',id_ruangan'],
            'nama_ruangan' => ['required', 'string', 'max:255'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'lokasi' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Tersedia,Perlu Izin,Aktif,Nonaktif'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $ruangan->update($validated);

        return redirect()->route('admin.kelola-fasilitas', ['tab' => 'lab'])
            ->with('success', "Data ruangan [{$ruangan->nama_ruangan}] berhasil diperbarui.");
    }

    public function destroyRuangan(int $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        // Validasi transaksi aktif
        $transaksiAktif = Peminjaman::where('id_ruangan', $ruangan->id_ruangan)
            ->whereIn('status', ['MENUNGGU PERSETUJUAN', 'DISETUJUI', 'MENUNGGU PENYERAHAN', 'DIPINJAM', 'MENUNGGU PENGEMBALIAN'])
            ->count();

        if ($transaksiAktif > 0) {
            return back()->with('error', "Ruangan [{$ruangan->nama_ruangan}] tidak dapat dihapus karena sedang ada {$transaksiAktif} peminjaman berjalan.");
        }

        $ruangan->delete();

        return redirect()->route('admin.kelola-fasilitas', ['tab' => 'lab'])
            ->with('success', "Ruangan [{$ruangan->nama_ruangan}] berhasil dihapus dari sistem.");
    }

    public function transaksiBerjalan(Request $request)
    {
        $status = $request->input('status', 'semua');
        $search = $request->input('search');
        $selectedId = $request->input('selected_id');

        $query = Peminjaman::whereIn('status', [
            'MENUNGGU PERSETUJUAN',
            'DISETUJUI',
            'MENUNGGU PENYERAHAN',
            'DIPINJAM',
            'MENUNGGU PENGEMBALIAN'
        ])->with(['user', 'barang', 'ruangan', 'persetujuanTerakhir.dosen', 'diserahkanOleh']);

        if ($status === 'menunggu_serah') {
            $query->whereIn('status', ['DISETUJUI', 'MENUNGGU PENYERAHAN']);
        } elseif ($status === 'dipinjam') {
            $query->where('status', 'DIPINJAM');
        } elseif ($status === 'menunggu_pengembalian') {
            $query->where('status', 'MENUNGGU PENGEMBALIAN');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('barang', fn($b) => $b->where('nama_barang', 'like', "%{$search}%"))
                  ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%{$search}%"));
            });
        }

        $transaksiList = $query->latest('id_peminjaman')->get();

        $selectedTransaksi = null;
        if ($selectedId) {
            $selectedTransaksi = Peminjaman::with([
                'user', 'barang', 'ruangan', 'persetujuan.dosen', 'qrCodes', 'pengembalian.dosen', 'diserahkanOleh'
            ])->find($selectedId);
        } elseif ($transaksiList->isNotEmpty()) {
            $selectedTransaksi = $transaksiList->first();
        }

        return view('admin.transaksi-berjalan', compact('transaksiList', 'selectedTransaksi', 'status', 'search'));
    }

    public function riwayatTransaksi(Request $request)
    {
        $search = $request->input('search');
        $jenis = $request->input('jenis');
        $status = $request->input('status');
        $dariTanggal = $request->input('dari_tanggal');
        $sampaiTanggal = $request->input('sampai_tanggal');

        $query = Peminjaman::with([
            'user', 'barang', 'ruangan', 'persetujuanTerakhir.dosen', 'diserahkanOleh', 'pengembalian.dosen'
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('barang', fn($b) => $b->where('nama_barang', 'like', "%{$search}%"))
                  ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%{$search}%"));
            });
        }

        if ($jenis && in_array($jenis, ['BARANG', 'RUANGAN'])) {
            $query->where('jenis_fasilitas', $jenis);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($dariTanggal) {
            $query->where('tanggal_peminjaman', '>=', $dariTanggal);
        }

        if ($sampaiTanggal) {
            $query->where('tanggal_peminjaman', '<=', $sampaiTanggal);
        }

        $riwayatList = $query->latest('id_peminjaman')->paginate(10);

        return view('admin.riwayat-transaksi', compact('riwayatList', 'search', 'jenis', 'status', 'dariTanggal', 'sampaiTanggal'));
    }

    public function kelolaPengguna(Request $request)
    {
        $role = $request->input('role', 'semua');
        $search = $request->input('search');

        $query = User::query();

        if ($role !== 'semua') {
            $query->where('role', $role);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        $users = $query->latest('id_user')->paginate(10);

        return view('admin.pengguna', compact('users', 'role', 'search'));
    }
}
