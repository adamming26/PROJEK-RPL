<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\Persetujuan;
use App\Models\QRCode;
use App\Models\Pengembalian;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        // 1. Akun Pengguna Demo
        $mahasiswa = User::create([
            'nama' => 'Adam Muzakir Eni',
            'email' => 'mahasiswa@sipafka.test',
            'password' => $defaultPassword,
            'role' => 'mahasiswa',
            'nomor_induk' => 'F1G125001',
            'nomor_hp' => '081234567890',
            'prodi' => 'Ilmu Komputer',
            'status' => 'aktif',
        ]);

        $mahasiswa2 = User::create([
            'nama' => 'Cinta Aprilianti Hartono',
            'email' => 'cinta@sipafka.test',
            'password' => $defaultPassword,
            'role' => 'mahasiswa',
            'nomor_induk' => 'F1G125028',
            'nomor_hp' => '081298765432',
            'prodi' => 'Ilmu Komputer',
            'status' => 'aktif',
        ]);

        $dosen = User::create([
            'nama' => 'Ilham Julian Efendi, S.T., M.T.',
            'email' => 'dosen@sipafka.test',
            'password' => $defaultPassword,
            'role' => 'dosen',
            'nomor_induk' => '198507152010121002',
            'nomor_hp' => '085241000111',
            'prodi' => 'Ilmu Komputer',
            'status' => 'aktif',
        ]);

        $admin = User::create([
            'nama' => 'Administrator SIPAFKA',
            'email' => 'admin@sipafka.test',
            'password' => $defaultPassword,
            'role' => 'admin',
            'nomor_induk' => 'ADM-ILKOM-01',
            'nomor_hp' => '081140001234',
            'prodi' => 'Ilmu Komputer',
            'status' => 'aktif',
        ]);

        // 2. Data Barang Fasilitas
        $proyektor = Barang::create([
            'kode_barang' => 'BRG-01',
            'nama_barang' => 'Proyektor',
            'kategori' => 'Perangkat Pendukung Perkuliahan',
            'deskripsi' => 'Proyektor perkuliahan dan presentasi program studi.',
            'stok_total' => 2,
            'stok_tersedia' => 2,
            'kondisi' => 'BAIK',
            'status' => 'Aktif',
        ]);

        $hdmi = Barang::create([
            'kode_barang' => 'BRG-02',
            'nama_barang' => 'Kabel HDMI',
            'kategori' => 'Perangkat Pendukung Perkuliahan',
            'deskripsi' => 'Kabel HDMI untuk konektivitas display proyektor dan perangkat presentasi.',
            'stok_total' => 2,
            'stok_tersedia' => 2,
            'kondisi' => 'BAIK',
            'status' => 'Aktif',
        ]);

        $terminal = Barang::create([
            'kode_barang' => 'BRG-03',
            'nama_barang' => 'Kabel Terminal Cabang',
            'kategori' => 'Perangkat Pendukung Perkuliahan',
            'deskripsi' => 'Kabel terminal kontak cabang untuk sumber daya perangkat perkuliahan.',
            'stok_total' => 5,
            'stok_tersedia' => 5,
            'kondisi' => 'BAIK',
            'status' => 'Aktif',
        ]);

        // 3. Data Ruangan Fasilitas
        $lab1 = Ruangan::create([
            'kode_ruangan' => 'RNG-01',
            'nama_ruangan' => 'Lab Komputer Basis Data',
            'kapasitas' => 50,
            'lokasi' => 'Gedung Laboratorium FMIPA UHO Lantai 2',
            'deskripsi' => 'Laboratorium komputer basis data kapasitas 50 komputer.',
            'status' => 'Perlu Izin',
        ]);

        $lab2 = Ruangan::create([
            'kode_ruangan' => 'RNG-02',
            'nama_ruangan' => 'Lab Komputer Unit Teknologi Informasi',
            'kapasitas' => 50,
            'lokasi' => 'Gedung Laboratorium FMIPA UHO Lantai 2',
            'deskripsi' => 'Laboratorium komputer unit teknologi informasi kapasitas 50 komputer.',
            'status' => 'Perlu Izin',
        ]);

        // 4. Data Peminjaman Awal untuk Demonstrasi
        // Contoh 1: Selesai (Riwayat)
        $pjm1 = Peminjaman::create([
            'kode_peminjaman' => 'PJM-' . Carbon::now()->subDays(2)->format('Ymd') . '-001',
            'id_user' => $mahasiswa->id_user,
            'jenis_fasilitas' => 'BARANG',
            'id_barang' => $hdmi->id_barang,
            'jumlah' => 1,
            'tanggal_pengajuan' => Carbon::now()->subDays(2)->toDateString(),
            'tanggal_peminjaman' => Carbon::now()->subDays(2)->toDateString(),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'durasi' => '2 Jam',
            'waktu_pengembalian_rencana' => Carbon::now()->subDays(2)->setTime(10, 0, 0),
            'tujuan' => 'Presentasi Tugas Besar Rekayasa Perangkat Lunak',
            'status' => 'SELESAI',
            'diserahkan_oleh' => $dosen->id_user,
            'diserahkan_pada' => Carbon::now()->subDays(2)->setTime(8, 5, 0),
        ]);

        Persetujuan::create([
            'id_peminjaman' => $pjm1->id_peminjaman,
            'id_dosen' => $dosen->id_user,
            'keputusan' => 'DISETUJUI',
            'catatan' => 'Disetujui untuk presentasi matakuliah RPL.',
            'tanggal' => Carbon::now()->subDays(2)->setTime(7, 45, 0),
        ]);

        Pengembalian::create([
            'id_peminjaman' => $pjm1->id_peminjaman,
            'id_dosen' => $dosen->id_user,
            'waktu_kembali' => Carbon::now()->subDays(2)->setTime(10, 5, 0),
            'jumlah_kembali' => 1,
            'kondisi_barang' => 'BAIK',
            'catatan' => 'Kabel dikembalikan lengkap dan berfungsi baik.',
        ]);

        // Contoh 2: Menunggu Persetujuan
        Peminjaman::create([
            'kode_peminjaman' => 'PJM-' . Carbon::now()->format('Ymd') . '-002',
            'id_user' => $mahasiswa->id_user,
            'jenis_fasilitas' => 'RUANGAN',
            'id_ruangan' => $lab1->id_ruangan,
            'jumlah' => 25,
            'tanggal_pengajuan' => Carbon::now()->toDateString(),
            'tanggal_peminjaman' => Carbon::now()->addDay()->toDateString(),
            'jam_mulai' => '13:00:00',
            'jam_selesai' => '15:00:00',
            'durasi' => '2 Jam',
            'waktu_pengembalian_rencana' => Carbon::now()->addDay()->setTime(15, 0, 0),
            'tujuan' => 'Pelatihan Git dan GitHub untuk Himpunan Mahasiswa Ilmu Komputer (HMIK)',
            'status' => 'MENUNGGU PERSETUJUAN',
        ]);

        // Contoh 3: Disetujui (Menunggu Penyerahan dengan QR Code aktif)
        $pjm3 = Peminjaman::create([
            'kode_peminjaman' => 'PJM-' . Carbon::now()->format('Ymd') . '-003',
            'id_user' => $mahasiswa->id_user,
            'jenis_fasilitas' => 'BARANG',
            'id_barang' => $proyektor->id_barang,
            'jumlah' => 1,
            'tanggal_pengajuan' => Carbon::now()->toDateString(),
            'tanggal_peminjaman' => Carbon::now()->toDateString(),
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '12:00:00',
            'durasi' => '2 Jam',
            'waktu_pengembalian_rencana' => Carbon::now()->setTime(12, 0, 0),
            'tujuan' => 'Kuliah Pengganti Pemrograman Berorientasi Objek di Ruang 3.2',
            'status' => 'DISETUJUI',
        ]);

        Persetujuan::create([
            'id_peminjaman' => $pjm3->id_peminjaman,
            'id_dosen' => $dosen->id_user,
            'keputusan' => 'DISETUJUI',
            'catatan' => 'Silakan ambil proyektor di ruang dosen / lab.',
            'tanggal' => Carbon::now()->subMinutes(2),
        ]);

        // Buat QR Code peminjaman aktif berlaku 15 menit
        QRCode::create([
            'id_peminjaman' => $pjm3->id_peminjaman,
            'token' => 'QR-PJM-' . strtoupper(Str::random(24)),
            'jenis_qr' => 'PEMINJAMAN',
            'expires_at' => Carbon::now()->addMinutes(15),
            'status' => 'AKTIF',
        ]);
    }
}
