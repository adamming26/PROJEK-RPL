<?php

namespace App\Services;

use App\Models\Peminjaman;
use App\Models\QRCode;
use App\Models\Pengembalian;
use App\Models\User;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QRCodeService
{
    /**
     * Generate atau refresh QR Code untuk Peminjaman (15 Menit)
     */
    public function generatePeminjamanQR(Peminjaman $peminjaman): QRCode
    {
        // Nonaktifkan QR lama jika ada yang masih aktif
        QRCode::where('id_peminjaman', $peminjaman->id_peminjaman)
            ->where('jenis_qr', 'PEMINJAMAN')
            ->where('status', 'AKTIF')
            ->update(['status' => 'NONAKTIF']);

        return QRCode::create([
            'id_peminjaman' => $peminjaman->id_peminjaman,
            'token' => 'QR-PJM-' . strtoupper(Str::random(24)),
            'jenis_qr' => 'PEMINJAMAN',
            'expires_at' => Carbon::now()->addMinutes(15),
            'status' => 'AKTIF',
        ]);
    }

    /**
     * Generate atau refresh QR Code untuk Pengembalian (15 Menit)
     */
    public function generatePengembalianQR(Peminjaman $peminjaman): QRCode
    {
        // Ubah status peminjaman menjadi MENUNGGU PENGEMBALIAN jika masih DIPINJAM
        if ($peminjaman->status === 'DIPINJAM') {
            $peminjaman->update(['status' => 'MENUNGGU PENGEMBALIAN']);
        }

        // Nonaktifkan QR lama jenis pengembalian
        QRCode::where('id_peminjaman', $peminjaman->id_peminjaman)
            ->where('jenis_qr', 'PENGEMBALIAN')
            ->where('status', 'AKTIF')
            ->update(['status' => 'NONAKTIF']);

        return QRCode::create([
            'id_peminjaman' => $peminjaman->id_peminjaman,
            'token' => 'QR-KMB-' . strtoupper(Str::random(24)),
            'jenis_qr' => 'PENGEMBALIAN',
            'expires_at' => Carbon::now()->addMinutes(15),
            'status' => 'AKTIF',
        ]);
    }

    /**
     * Validasi Token QR Code dari pemindaian kamera / input token
     */
    public function validateToken(string $token, ?string $expectedJenis = null): array
    {
        $qr = QRCode::with(['peminjaman.user', 'peminjaman.barang', 'peminjaman.ruangan'])
            ->where('token', trim($token))
            ->first();

        if (!$qr) {
            return [
                'valid' => false,
                'status_code' => 'NOT_FOUND',
                'message' => 'QR Code tidak terdaftar dalam sistem.',
                'qr' => null,
            ];
        }

        if ($qr->status === 'DIGUNAKAN') {
            return [
                'valid' => false,
                'status_code' => 'ALREADY_USED',
                'message' => 'QR Code ini sudah pernah digunakan sebelumnya dan telah dinonaktifkan.',
                'qr' => $qr,
            ];
        }

        if ($qr->status === 'NONAKTIF') {
            return [
                'valid' => false,
                'status_code' => 'INACTIVE',
                'message' => 'QR Code ini sudah tidak aktif karena telah diterbitkan QR Code baru.',
                'qr' => $qr,
            ];
        }

        // Cek kedaluwarsa 15 menit
        if ($qr->status === 'KEDALUWARSA' || Carbon::now()->greaterThan($qr->expires_at)) {
            if ($qr->status !== 'KEDALUWARSA') {
                $qr->update(['status' => 'KEDALUWARSA']);
            }
            if ($qr->peminjaman && in_array($qr->peminjaman->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN'])) {
                $qr->peminjaman->update(['status' => 'KEDALUWARSA']);
            }
            return [
                'valid' => false,
                'status_code' => 'EXPIRED',
                'message' => 'Masa berlaku QR Code 15 menit telah habis (Kedaluwarsa). Transaksi dibatalkan dan peminjam harus melakukan pengajuan peminjaman ulang.',
                'qr' => $qr,
            ];
        }

        if ($expectedJenis && $qr->jenis_qr !== $expectedJenis) {
            $namaJenisExpected = $expectedJenis === 'PEMINJAMAN' ? 'Penyerahan Fasilitas' : 'Pengembalian Fasilitas';
            $namaJenisAktual = $qr->jenis_qr === 'PEMINJAMAN' ? 'Penyerahan Fasilitas' : 'Pengembalian Fasilitas';
            return [
                'valid' => false,
                'status_code' => 'TYPE_MISMATCH',
                'message' => "Jenis QR tidak sesuai. Anda berada pada modul {$namaJenisExpected}, namun QR yang dipindai adalah untuk {$namaJenisAktual}.",
                'qr' => $qr,
            ];
        }

        $peminjaman = $qr->peminjaman;
        if (!$peminjaman) {
            return [
                'valid' => false,
                'status_code' => 'INVALID_TRANSACTION',
                'message' => 'Data peminjaman terkait tidak ditemukan.',
                'qr' => $qr,
            ];
        }

        // Validasi status transaksi
        if ($qr->jenis_qr === 'PEMINJAMAN') {
            if (!in_array($peminjaman->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN'])) {
                return [
                    'valid' => false,
                    'status_code' => 'INVALID_STATUS',
                    'message' => "Status peminjaman saat ini '{$peminjaman->status}', tidak dapat dilakukan penyerahan.",
                    'qr' => $qr,
                ];
            }
        } elseif ($qr->jenis_qr === 'PENGEMBALIAN') {
            if (!in_array($peminjaman->status, ['DIPINJAM', 'MENUNGGU PENGEMBALIAN'])) {
                return [
                    'valid' => false,
                    'status_code' => 'INVALID_STATUS',
                    'message' => "Status peminjaman saat ini '{$peminjaman->status}', tidak dapat dilakukan pengembalian.",
                    'qr' => $qr,
                ];
            }
        }

        return [
            'valid' => true,
            'status_code' => 'VALID',
            'message' => 'QR Code valid dan siap dikonfirmasi.',
            'qr' => $qr,
        ];
    }

    /**
     * Konfirmasi Penyerahan Barang / Ruangan oleh Dosen
     */
    public function confirmPenyerahan(string $token, User $dosen): array
    {
        return DB::transaction(function () use ($token, $dosen) {
            $qr = QRCode::where('token', trim($token))
                ->lockForUpdate()
                ->first();

            if (!$qr) {
                return ['success' => false, 'message' => 'QR Code tidak ditemukan.'];
            }

            if (!$qr->isValid()) {
                return ['success' => false, 'message' => 'QR Code tidak valid atau sudah kedaluwarsa.'];
            }

            if ($qr->jenis_qr !== 'PEMINJAMAN') {
                return ['success' => false, 'message' => 'QR Code bukan untuk penyerahan peminjaman.'];
            }

            $peminjaman = Peminjaman::where('id_peminjaman', $qr->id_peminjaman)
                ->lockForUpdate()
                ->first();

            if (!in_array($peminjaman->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN'])) {
                return ['success' => false, 'message' => 'Status transaksi sudah tidak valid untuk penyerahan.'];
            }

            // Jika barang, kurangi stok tersedia
            if ($peminjaman->jenis_fasilitas === 'BARANG' && $peminjaman->id_barang) {
                $barang = Barang::where('id_barang', $peminjaman->id_barang)
                    ->lockForUpdate()
                    ->first();

                if ($barang) {
                    if ($barang->stok_tersedia < $peminjaman->jumlah) {
                        return [
                            'success' => false,
                            'message' => "Stok tersedia ({$barang->stok_tersedia}) tidak mencukupi untuk jumlah yang diminta ({$peminjaman->jumlah})."
                        ];
                    }
                    $barang->stok_tersedia -= $peminjaman->jumlah;
                    $barang->save();
                }
            }

            // Update QR Code menjadi DIGUNAKAN
            $qr->update([
                'status' => 'DIGUNAKAN',
                'used_at' => Carbon::now(),
            ]);

            // Update Peminjaman menjadi DIPINJAM
            $peminjaman->update([
                'status' => 'DIPINJAM',
                'diserahkan_oleh' => $dosen->id_user,
                'diserahkan_pada' => Carbon::now(),
            ]);

            return [
                'success' => true,
                'message' => 'Penyerahan fasilitas berhasil dikonfirmasi. Status peminjaman telah diubah menjadi DIPINJAM.',
                'peminjaman' => $peminjaman,
            ];
        });
    }

    /**
     * Konfirmasi Pengembalian Barang oleh Dosen
     */
    public function confirmPengembalian(
        string $token,
        User $dosen,
        int $jumlahKembali,
        string $kondisiBarang,
        ?string $catatan = null
    ): array {
        return DB::transaction(function () use ($token, $dosen, $jumlahKembali, $kondisiBarang, $catatan) {
            $qr = QRCode::where('token', trim($token))
                ->lockForUpdate()
                ->first();

            if (!$qr) {
                return ['success' => false, 'message' => 'QR Code tidak ditemukan.'];
            }

            if (!$qr->isValid()) {
                return ['success' => false, 'message' => 'QR Code tidak valid atau sudah kedaluwarsa.'];
            }

            if ($qr->jenis_qr !== 'PENGEMBALIAN') {
                return ['success' => false, 'message' => 'QR Code bukan untuk pengembalian.'];
            }

            $peminjaman = Peminjaman::where('id_peminjaman', $qr->id_peminjaman)
                ->lockForUpdate()
                ->first();

            if (!in_array($peminjaman->status, ['DIPINJAM', 'MENUNGGU PENGEMBALIAN'])) {
                return ['success' => false, 'message' => 'Status transaksi tidak valid untuk pengembalian.'];
            }

            // Nonaktifkan QR Code seketika setelah validasi pemindaian berhasil
            $qr->update([
                'status' => 'DIGUNAKAN',
                'used_at' => Carbon::now(),
            ]);

            // Buat catatan pengembalian
            $pengembalian = Pengembalian::create([
                'id_peminjaman' => $peminjaman->id_peminjaman,
                'id_dosen' => $dosen->id_user,
                'waktu_kembali' => Carbon::now(),
                'jumlah_kembali' => $jumlahKembali,
                'kondisi_barang' => $kondisiBarang,
                'catatan' => $catatan,
            ]);

            // Update stok barang sesuai aturan bisnis:
            // "Jangan menambah stok barang yang dinyatakan rusak atau tidak dapat digunakan. Stok tersedia harus dihitung sesuai kondisi aktual barang."
            if ($peminjaman->jenis_fasilitas === 'BARANG' && $peminjaman->id_barang) {
                $barang = Barang::where('id_barang', $peminjaman->id_barang)
                    ->lockForUpdate()
                    ->first();

                if ($barang) {
                    if ($kondisiBarang === 'BAIK') {
                        // Tambahkan kembali ke stok tersedia
                        $barang->stok_tersedia = min($barang->stok_total, $barang->stok_tersedia + $jumlahKembali);
                    } elseif ($kondisiBarang === 'RUSAK') {
                        // Barang rusak tidak dikembalikan ke stok tersedia. Kurangi stok total agar akurat.
                        $barang->stok_total = max(0, $barang->stok_total - $jumlahKembali);
                        $barang->kondisi = 'RUSAK';
                    } elseif ($kondisiBarang === 'TIDAK LENGKAP') {
                        // Hanya kembalikan jika ada unit yang utuh, tandai catatan
                        $barang->kondisi = 'TIDAK LENGKAP';
                    }
                    $barang->save();
                }
            }

            // Ubah status transaksi menjadi SELESAI
            $peminjaman->update([
                'status' => 'SELESAI',
            ]);

            return [
                'success' => true,
                'message' => 'Pengembalian fasilitas berhasil dikonfirmasi. Status transaksi telah SELESAI dan stok inventaris telah diperbarui.',
                'pengembalian' => $pengembalian,
                'peminjaman' => $peminjaman,
            ];
        });
    }

    /**
     * Helper untuk cek bentrok jadwal ruangan
     */
    public function checkJadwalBentrok(
        int $idRuangan,
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        ?int $excludeIdPeminjaman = null
    ): ?Peminjaman {
        $query = Peminjaman::where('id_ruangan', $idRuangan)
            ->whereDate('tanggal_peminjaman', $tanggal)
            ->whereNotIn('status', ['DITOLAK', 'SELESAI'])
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                // Rentang waktu saling bertabrakan: (start < other_end) AND (end > other_start)
                $q->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $jamMulai);
            });

        if ($excludeIdPeminjaman) {
            $query->where('id_peminjaman', '!=', $excludeIdPeminjaman);
        }

        return $query->with('user')->first();
    }
}
