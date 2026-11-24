<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\QRCode;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MahasiswaController extends Controller
{
    protected QRCodeService $qrService;

    public function __construct(QRCodeService $qrService)
    {
        $this->qrService = $qrService;
    }

    public function dashboard()
    {
        $user = Auth::user();

        $totalPeminjaman = Peminjaman::where('id_user', $user->id_user)->count();
        $menungguPersetujuan = Peminjaman::where('id_user', $user->id_user)
            ->where('status', 'MENUNGGU PERSETUJUAN')
            ->count();
        $peminjamanAktif = Peminjaman::where('id_user', $user->id_user)
            ->whereIn('status', ['DISETUJUI', 'MENUNGGU PENYERAHAN', 'DIPINJAM', 'MENUNGGU PENGEMBALIAN'])
            ->count();
        $peminjamanSelesai = Peminjaman::where('id_user', $user->id_user)
            ->where('status', 'SELESAI')
            ->count();

        // Fasilitas yang tersedia
        $barangTersedia = Barang::where('status', 'Aktif')->take(6)->get();
        $ruanganTersedia = Ruangan::where('status', '!=', 'Nonaktif')->take(4)->get();

        // Pengajuan terbaru
        $pengajuanTerbaru = Peminjaman::where('id_user', $user->id_user)
            ->with(['barang', 'ruangan', 'persetujuanTerakhir.dosen'])
            ->latest('id_peminjaman')
            ->take(5)
            ->get();

        // QR Code aktif (jika ada peminjaman berstatus DISETUJUI atau MENUNGGU PENGEMBALIAN)
        $qrAktif = QRCode::whereHas('peminjaman', function ($q) use ($user) {
            $q->where('id_user', $user->id_user);
        })
        ->where('status', 'AKTIF')
        ->where('expires_at', '>', Carbon::now())
        ->with(['peminjaman.barang', 'peminjaman.ruangan'])
        ->latest('id_qr')
        ->first();

        return view('mahasiswa.dashboard', compact(
            'user',
            'totalPeminjaman',
            'menungguPersetujuan',
            'peminjamanAktif',
            'peminjamanSelesai',
            'barangTersedia',
            'ruanganTersedia',
            'pengajuanTerbaru',
            'qrAktif'
        ));
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

        return view('mahasiswa.fasilitas', compact('barangList', 'ruanganList', 'kategori', 'search'));
    }

    public function formPeminjaman(string $jenis, ?int $id = null)
    {
        $jenis = strtoupper($jenis);
        if (!in_array($jenis, ['BARANG', 'RUANGAN'])) {
            $jenis = 'BARANG';
        }

        $barangList = Barang::where('status', 'Aktif')->where('stok_tersedia', '>', 0)->get();
        $ruanganList = Ruangan::where('status', '!=', 'Nonaktif')->get();

        $selectedBarang = ($jenis === 'BARANG' && $id) ? Barang::find($id) : null;
        $selectedRuangan = ($jenis === 'RUANGAN' && $id) ? Ruangan::find($id) : null;

        return view('mahasiswa.form-peminjaman', compact(
            'jenis',
            'barangList',
            'ruanganList',
            'selectedBarang',
            'selectedRuangan'
        ));
    }

    public function storePeminjaman(Request $request)
    {
        $jenis = $request->input('jenis_fasilitas');

        if ($jenis === 'BARANG') {
            $validated = $request->validate([
                'id_barang' => ['required', 'exists:barang,id_barang'],
                'jumlah' => ['required', 'integer', 'min:1'],
                'tanggal_peminjaman' => ['required', 'date', 'after_or_equal:today'],
                'jam_mulai' => ['required'],
                'durasi' => ['required', 'string'],
                'tujuan' => ['required', 'string', 'min:5'],
            ], [
                'id_barang.required' => 'Pilih barang yang ingin dipinjam.',
                'jumlah.required' => 'Jumlah barang wajib ditentukan.',
                'jumlah.min' => 'Jumlah barang minimal 1 unit.',
                'tanggal_peminjaman.required' => 'Tanggal peminjaman wajib diisi.',
                'tanggal_peminjaman.after_or_equal' => 'Tanggal peminjaman tidak boleh di masa lampau.',
                'jam_mulai.required' => 'Waktu / jam mulai wajib diisi.',
                'durasi.required' => 'Durasi peminjaman wajib dipilih.',
                'tujuan.required' => 'Tujuan peminjaman wajib dijelaskan.',
            ]);

            $barang = Barang::findOrFail($validated['id_barang']);
            if ($barang->stok_tersedia < $validated['jumlah']) {
                return back()->withInput()->with('error', "Maaf, stok tersedia untuk {$barang->nama_barang} hanya {$barang->stok_tersedia} unit.");
            }

            // Hitung estimasi jam selesai berdasarkan durasi
            $jamMulai = Carbon::createFromFormat('H:i', $validated['jam_mulai']);
            $durasiJam = (int) filter_var($validated['durasi'], FILTER_SANITIZE_NUMBER_INT) ?: 2;
            $jamSelesai = $jamMulai->copy()->addHours($durasiJam)->format('H:i:s');
            $waktuKembaliRencana = Carbon::parse($validated['tanggal_peminjaman'] . ' ' . $jamSelesai);

            $kode = 'PJM-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            Peminjaman::create([
                'kode_peminjaman' => $kode,
                'id_user' => Auth::user()->id_user,
                'jenis_fasilitas' => 'BARANG',
                'id_barang' => $barang->id_barang,
                'jumlah' => $validated['jumlah'],
                'tanggal_pengajuan' => Carbon::now()->toDateString(),
                'tanggal_peminjaman' => $validated['tanggal_peminjaman'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $jamSelesai,
                'durasi' => $validated['durasi'],
                'waktu_pengembalian_rencana' => $waktuKembaliRencana,
                'tujuan' => $validated['tujuan'],
                'status' => 'MENUNGGU PERSETUJUAN',
            ]);

            return redirect()->route('mahasiswa.peminjaman-saya')
                ->with('success', 'Pengajuan peminjaman barang berhasil diajukan dan sedang menunggu persetujuan Dosen.');

        } elseif ($jenis === 'RUANGAN') {
            $validated = $request->validate([
                'id_ruangan' => ['required', 'exists:ruangan,id_ruangan'],
                'jumlah' => ['required', 'integer', 'min:1'], // jumlah peserta
                'tanggal_peminjaman' => ['required', 'date', 'after_or_equal:today'],
                'jam_mulai' => ['required'],
                'durasi' => ['required', 'string'],
                'tujuan' => ['required', 'string', 'min:5'],
            ], [
                'id_ruangan.required' => 'Pilih ruangan / laboratorium yang ingin dipinjam.',
                'jumlah.required' => 'Jumlah peserta wajib diisi.',
                'tanggal_peminjaman.required' => 'Tanggal penggunaan ruangan wajib diisi.',
                'jam_mulai.required' => 'Jam mulai wajib diisi.',
                'durasi.required' => 'Durasi peminjaman wajib dipilih.',
                'tujuan.required' => 'Tujuan penggunaan lab / ruangan wajib dijelaskan.',
            ]);

            $ruangan = Ruangan::findOrFail($validated['id_ruangan']);

            // Hitung jam selesai
            $jamMulai = Carbon::createFromFormat('H:i', $validated['jam_mulai']);
            $durasiJam = (int) filter_var($validated['durasi'], FILTER_SANITIZE_NUMBER_INT) ?: 2;
            $jamSelesai = $jamMulai->copy()->addHours($durasiJam)->format('H:i:s');
            $waktuKembaliRencana = Carbon::parse($validated['tanggal_peminjaman'] . ' ' . $jamSelesai);

            // Deteksi bentrok jadwal secara otomatis
            $bentrok = $this->qrService->checkJadwalBentrok(
                $ruangan->id_ruangan,
                $validated['tanggal_peminjaman'],
                $validated['jam_mulai'],
                $jamSelesai
            );

            if ($bentrok) {
                return back()->withInput()->with('error', "Jadwal bentrok! {$ruangan->nama_ruangan} pada tanggal {$validated['tanggal_peminjaman']} telah dibooking oleh {$bentrok->user->nama} ({$bentrok->jam_mulai} - {$bentrok->jam_selesai}). Silakan pilih waktu lain.");
            }

            $kode = 'PJM-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            Peminjaman::create([
                'kode_peminjaman' => $kode,
                'id_user' => Auth::user()->id_user,
                'jenis_fasilitas' => 'RUANGAN',
                'id_ruangan' => $ruangan->id_ruangan,
                'jumlah' => $validated['jumlah'],
                'tanggal_pengajuan' => Carbon::now()->toDateString(),
                'tanggal_peminjaman' => $validated['tanggal_peminjaman'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $jamSelesai,
                'durasi' => $validated['durasi'],
                'waktu_pengembalian_rencana' => $waktuKembaliRencana,
                'tujuan' => $validated['tujuan'],
                'status' => 'MENUNGGU PERSETUJUAN',
            ]);

            return redirect()->route('mahasiswa.peminjaman-saya')
                ->with('success', 'Pengajuan peminjaman laboratorium/ruangan berhasil dikirim. Menunggu verifikasi dan izin Dosen.');
        }

        return back()->with('error', 'Jenis fasilitas tidak valid.');
    }

    public function peminjamanSaya(Request $request)
    {
        $status = $request->input('status', 'semua');
        $search = $request->input('search');

        $query = Peminjaman::where('id_user', Auth::user()->id_user)
            ->with(['barang', 'ruangan', 'persetujuanTerakhir.dosen', 'qrCodes']);

        if ($status !== 'semua') {
            if ($status === 'menunggu') {
                $query->where('status', 'MENUNGGU PERSETUJUAN');
            } elseif ($status === 'disetujui') {
                $query->whereIn('status', ['DISETUJUI', 'MENUNGGU PENYERAHAN']);
            } elseif ($status === 'dipinjam') {
                $query->whereIn('status', ['DIPINJAM', 'MENUNGGU PENGEMBALIAN']);
            } elseif ($status === 'ditolak') {
                $query->where('status', 'DITOLAK');
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%")
                  ->orWhereHas('barang', fn($b) => $b->where('nama_barang', 'like', "%{$search}%"))
                  ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%{$search}%"));
            });
        }

        $peminjamanList = $query->latest('id_peminjaman')->paginate(10);

        // QR Code aktif mahasiswa (jika ada)
        $qrAktif = QRCode::whereHas('peminjaman', function ($q) {
            $q->where('id_user', Auth::user()->id_user);
        })
        ->where('status', 'AKTIF')
        ->where('expires_at', '>', Carbon::now())
        ->with(['peminjaman.barang', 'peminjaman.ruangan'])
        ->latest('id_qr')
        ->first();

        return view('mahasiswa.peminjaman-saya', compact('peminjamanList', 'status', 'search', 'qrAktif'));
    }

    public function detailPeminjaman(int $id)
    {
        $peminjaman = Peminjaman::where('id_user', Auth::user()->id_user)
            ->with(['barang', 'ruangan', 'persetujuan.dosen', 'qrCodes', 'pengembalian.dosen', 'diserahkanOleh'])
            ->findOrFail($id);

        $activeQr = $peminjaman->qrCodes()
            ->where('status', 'AKTIF')
            ->where('expires_at', '>', Carbon::now())
            ->latest('id_qr')
            ->first();

        return view('mahasiswa.detail-peminjaman', compact('peminjaman', 'activeQr'));
    }

    public function showQrCode(int $id)
    {
        $peminjaman = Peminjaman::where('id_user', Auth::user()->id_user)
            ->with(['barang', 'ruangan', 'persetujuanTerakhir.dosen'])
            ->findOrFail($id);

        if (!in_array($peminjaman->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN', 'DIPINJAM', 'MENUNGGU PENGEMBALIAN', 'KEDALUWARSA'])) {
            return back()->with('error', 'Status peminjaman tidak memenuhi syarat untuk menampilkan QR Code.');
        }

        $jenisQr = in_array($peminjaman->status, ['DIPINJAM', 'MENUNGGU PENGEMBALIAN']) ? 'PENGEMBALIAN' : 'PEMINJAMAN';

        $qr = $peminjaman->qrCodes()
            ->where('jenis_qr', $jenisQr)
            ->latest('id_qr')
            ->first();

        $isExpired = false;

        if ($peminjaman->status === 'KEDALUWARSA') {
            $isExpired = true;
        } elseif ($qr) {
            // Periksa apakah masa tenggang 15 menit telah habis
            if ($qr->status === 'KEDALUWARSA' || Carbon::now()->greaterThan($qr->expires_at)) {
                if ($qr->status !== 'KEDALUWARSA') {
                    $qr->update(['status' => 'KEDALUWARSA']);
                }
                if (in_array($peminjaman->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN'])) {
                    $peminjaman->update(['status' => 'KEDALUWARSA']);
                }
                $isExpired = true;
            }
        } else {
            // Jika belum ada QR sama sekali (pertama kali setelah disetujui Dosen)
            if (in_array($peminjaman->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN'])) {
                $qr = $this->qrService->generatePeminjamanQR($peminjaman);
            } elseif (in_array($peminjaman->status, ['DIPINJAM', 'MENUNGGU PENGEMBALIAN'])) {
                $qr = $this->qrService->generatePengembalianQR($peminjaman);
            }
        }

        return view('mahasiswa.qr-code', compact('peminjaman', 'qr', 'jenisQr', 'isExpired'));
    }

    public function generateQr(int $id)
    {
        $peminjaman = Peminjaman::where('id_user', Auth::user()->id_user)
            ->findOrFail($id);

        // Jika masa tenggang 15 menit habis, tidak boleh buat QR baru, tapi harus ajukan ulang
        return redirect()->route('mahasiswa.form-peminjaman', [
            'jenis' => $peminjaman->jenis_fasilitas,
            'id' => $peminjaman->id_barang ?? $peminjaman->id_ruangan
        ])->with('error', 'Masa tenggang QR Code 15 menit telah habis. Sesuai ketentuan, Anda harus melakukan pengajuan peminjaman ulang.');
    }

    public function ajukanPengembalian(int $id)
    {
        $peminjaman = Peminjaman::where('id_user', Auth::user()->id_user)
            ->whereIn('status', ['DIPINJAM', 'MENUNGGU PENGEMBALIAN'])
            ->findOrFail($id);

        $qr = $this->qrService->generatePengembalianQR($peminjaman);

        return redirect()->route('mahasiswa.qr-code', $peminjaman->id_peminjaman)
            ->with('success', 'Pengajuan pengembalian berhasil! Tunjukkan QR Code pengembalian berikut kepada Dosen saat mengembalikan barang.');
    }

    public function riwayat(Request $request)
    {
        $search = $request->input('search');
        $jenis = $request->input('jenis');
        $status = $request->input('status');
        $dariTanggal = $request->input('dari_tanggal');

        $query = Peminjaman::where('id_user', Auth::user()->id_user)
            ->with(['barang', 'ruangan', 'persetujuanTerakhir.dosen', 'pengembalian.dosen']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%")
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

        $riwayatList = $query->latest('id_peminjaman')->paginate(10);

        return view('mahasiswa.riwayat', compact('riwayatList', 'search', 'jenis', 'status', 'dariTanggal'));
    }
}
