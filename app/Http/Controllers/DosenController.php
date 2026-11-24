<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Persetujuan;
use App\Models\QRCode;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DosenController extends Controller
{
    protected QRCodeService $qrService;

    public function __construct(QRCodeService $qrService)
    {
        $this->qrService = $qrService;
    }

    public function dashboard()
    {
        $user = Auth::user();

        $totalPengajuanMasuk = Peminjaman::count();
        $menungguKeputusan = Peminjaman::where('status', 'MENUNGGU PERSETUJUAN')->count();
        $disetujui = Persetujuan::where('id_dosen', $user->id_user)->where('keputusan', 'DISETUJUI')->count();
        $ditolak = Persetujuan::where('id_dosen', $user->id_user)->where('keputusan', 'DITOLAK')->count();

        $menungguPenyerahan = Peminjaman::whereIn('status', ['DISETUJUI', 'MENUNGGU PENYERAHAN'])->count();
        $menungguPengembalian = Peminjaman::whereIn('status', ['DIPINJAM', 'MENUNGGU PENGEMBALIAN'])->count();

        // Pengajuan yang butuh tindakan segera
        $pengajuanMenunggu = Peminjaman::where('status', 'MENUNGGU PERSETUJUAN')
            ->with(['user', 'barang', 'ruangan'])
            ->latest('id_peminjaman')
            ->take(5)
            ->get();

        // Riwayat aktivitas keputusan terakhir dosen ini
        $riwayatTerakhir = Persetujuan::where('id_dosen', $user->id_user)
            ->with(['peminjaman.user', 'peminjaman.barang', 'peminjaman.ruangan'])
            ->latest('id_persetujuan')
            ->take(5)
            ->get();

        return view('dosen.dashboard', compact(
            'user',
            'totalPengajuanMasuk',
            'menungguKeputusan',
            'disetujui',
            'ditolak',
            'menungguPenyerahan',
            'menungguPengembalian',
            'pengajuanMenunggu',
            'riwayatTerakhir'
        ));
    }

    public function pengajuanMasuk(Request $request)
    {
        $status = $request->input('status', 'menunggu');
        $search = $request->input('search');

        $query = Peminjaman::with(['user', 'barang', 'ruangan', 'persetujuanTerakhir.dosen']);

        if ($status === 'menunggu') {
            $query->where('status', 'MENUNGGU PERSETUJUAN');
        } elseif ($status === 'disetujui') {
            $query->whereIn('status', ['DISETUJUI', 'MENUNGGU PENYERAHAN', 'DIPINJAM']);
        } elseif ($status === 'ditolak') {
            $query->where('status', 'DITOLAK');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('nama', 'like', "%{$search}%")->orWhere('nomor_induk', 'like', "%{$search}%"))
                  ->orWhereHas('barang', fn($b) => $b->where('nama_barang', 'like', "%{$search}%"))
                  ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%{$search}%"));
            });
        }

        $pengajuanList = $query->latest('id_peminjaman')->paginate(10);

        return view('dosen.pengajuan-masuk', compact('pengajuanList', 'status', 'search'));
    }

    public function detailPengajuan(int $id)
    {
        $peminjaman = Peminjaman::with(['user', 'barang', 'ruangan', 'persetujuan.dosen', 'qrCodes'])
            ->findOrFail($id);

        $bentrokJadwal = null;
        if ($peminjaman->jenis_fasilitas === 'RUANGAN' && $peminjaman->id_ruangan) {
            $bentrokJadwal = $this->qrService->checkJadwalBentrok(
                $peminjaman->id_ruangan,
                $peminjaman->tanggal_peminjaman->toDateString(),
                $peminjaman->jam_mulai,
                $peminjaman->jam_selesai,
                $peminjaman->id_peminjaman
            );
        }

        return view('dosen.detail-pengajuan', compact('peminjaman', 'bentrokJadwal'));
    }

    public function putuskanPengajuan(Request $request, int $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $user = Auth::user();

        $validated = $request->validate([
            'keputusan' => ['required', 'in:DISETUJUI,DITOLAK'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['keputusan'] === 'DITOLAK' && empty(trim($validated['catatan'] ?? ''))) {
            return back()->withInput()->with('error', 'Alasan penolakan wajib ditulis jika pengajuan ditolak.');
        }

        // Simpan catatan persetujuan
        Persetujuan::create([
            'id_peminjaman' => $peminjaman->id_peminjaman,
            'id_dosen' => $user->id_user,
            'keputusan' => $validated['keputusan'],
            'catatan' => $validated['catatan'] ?? null,
            'tanggal' => Carbon::now(),
        ]);

        if ($validated['keputusan'] === 'DISETUJUI') {
            $peminjaman->update([
                'status' => 'DISETUJUI',
            ]);

            // Terbitkan otomatis QR Code peminjaman berlaku 15 menit
            $this->qrService->generatePeminjamanQR($peminjaman);

            return redirect()->route('dosen.pengajuan-masuk')
                ->with('success', "Pengajuan [{$peminjaman->kode_peminjaman}] berhasil DISETUJUI. Token QR Code peminjaman (15 menit) telah dibuat otomatis.");
        } else {
            $peminjaman->update([
                'status' => 'DITOLAK',
                'catatan_penolakan' => $validated['catatan'],
            ]);

            return redirect()->route('dosen.pengajuan-masuk')
                ->with('success', "Pengajuan [{$peminjaman->kode_peminjaman}] telah DITOLAK dengan alasan tersimpan.");
        }
    }

    public function pindaiQr(Request $request)
    {
        $token = $request->input('token');
        $tab = $request->input('tab', 'penyerahan'); // 'penyerahan' atau 'pengembalian'

        $validationResult = null;
        if ($token) {
            $expectedJenis = ($tab === 'penyerahan') ? 'PEMINJAMAN' : 'PENGEMBALIAN';
            $validationResult = $this->qrService->validateToken($token, $expectedJenis);
        }

        return view('dosen.pindai-qr', compact('token', 'tab', 'validationResult'));
    }

    public function validateQrAjax(Request $request)
    {
        $token = $request->input('token');
        $tab = $request->input('tab', 'penyerahan');
        $expectedJenis = ($tab === 'penyerahan') ? 'PEMINJAMAN' : 'PENGEMBALIAN';

        $result = $this->qrService->validateToken($token, $expectedJenis);

        $data = [
            'valid' => $result['valid'],
            'status_code' => $result['status_code'],
            'message' => $result['message'],
        ];

        if ($result['qr']) {
            $peminjaman = $result['qr']->peminjaman;
            $data['peminjaman'] = [
                'id_peminjaman' => $peminjaman->id_peminjaman,
                'kode' => $peminjaman->kode_peminjaman,
                'peminjam' => $peminjaman->user->nama ?? '-',
                'nim' => $peminjaman->user->nomor_induk ?? '-',
                'fasilitas' => $peminjaman->nama_fasilitas,
                'jenis' => $peminjaman->jenis_fasilitas,
                'jumlah' => $peminjaman->jumlah,
                'waktu' => $peminjaman->tanggal_peminjaman->format('d/m/Y') . ' (' . $peminjaman->jam_mulai . ' - ' . $peminjaman->jam_selesai . ')',
                'tujuan' => $peminjaman->tujuan,
                'status_transaksi' => $peminjaman->status,
                'remaining_seconds' => $result['qr']->remaining_seconds,
            ];
        }

        return response()->json($data);
    }

    public function prosesPenyerahan(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ], [
            'token.required' => 'Token QR Code wajib diisi atau dipindai.',
        ]);

        $res = $this->qrService->confirmPenyerahan($request->input('token'), Auth::user());

        if (!$res['success']) {
            return back()->withInput()->with('error', $res['message']);
        }

        return redirect()->route('dosen.pindai-qr', ['tab' => 'penyerahan'])
            ->with('success', $res['message']);
    }

    public function prosesPengembalian(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'jumlah_kembali' => ['required', 'integer', 'min:1'],
            'kondisi_barang' => ['required', 'in:BAIK,RUSAK,TIDAK LENGKAP'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'token.required' => 'Token QR Code pengembalian wajib ada.',
            'jumlah_kembali.required' => 'Jumlah barang yang dikembalikan wajib ditentukan.',
            'kondisi_barang.required' => 'Kondisi barang saat kembali wajib dipilih.',
        ]);

        $res = $this->qrService->confirmPengembalian(
            $request->input('token'),
            Auth::user(),
            (int) $request->input('jumlah_kembali'),
            $request->input('kondisi_barang'),
            $request->input('catatan')
        );

        if (!$res['success']) {
            return back()->withInput()->with('error', $res['message']);
        }

        return redirect()->route('dosen.pindai-qr', ['tab' => 'pengembalian'])
            ->with('success', $res['message']);
    }

    public function riwayatKeputusan(Request $request)
    {
        $search = $request->input('search');
        $keputusan = $request->input('keputusan', 'semua');

        $query = Persetujuan::where('id_dosen', Auth::user()->id_user)
            ->with(['peminjaman.user', 'peminjaman.barang', 'peminjaman.ruangan', 'peminjaman.pengembalian']);

        if ($keputusan !== 'semua') {
            $query->where('keputusan', $keputusan);
        }

        if ($search) {
            $query->whereHas('peminjaman', function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('barang', fn($b) => $b->where('nama_barang', 'like', "%{$search}%"))
                  ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%{$search}%"));
            });
        }

        $riwayatList = $query->latest('id_persetujuan')->paginate(10);

        return view('dosen.riwayat-keputusan', compact('riwayatList', 'search', 'keputusan'));
    }
}
