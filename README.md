# SIPAFKA (Sistem Informasi Peminjaman Fasilitas Kampus)
### Program Studi Ilmu Komputer — Fakultas Matematika dan Ilmu Pengetahuan Alam
### Universitas Halu Oleo (UHO) Kendari

---

## 📌 Deskripsi Aplikasi
**SIPAFKA** adalah aplikasi web operasional untuk mengelola peminjaman dan pengembalian fasilitas kampus di lingkungan Program Studi Ilmu Komputer Universitas Halu Oleo. Sistem ini memfasilitasi peminjaman barang penunjang perkuliahan (proyektor, kabel HDMI, terminal kontak) serta izin penggunaan laboratorium komputer secara daring, tertib, dan transparan.

Aplikasi ini mengintegrasikan **Token Digital QR Code** dengan masa berlaku **15 menit** dan **Pemindai Kamera HTML5** di sisi dosen untuk memvalidasi penyerahan dan pengembalian fasilitas secara instan, aman, serta mencegah bentrok jadwal maupun kehilangan inventaris.

---

## ⚙️ Persyaratan Sistem
* **PHP**: Versi 8.2 atau lebih baru (direkomendasikan PHP 8.4)
* **Composer**: Versi 2.x
* **Node.js & NPM**: Node.js v18+ dan NPM 9+
* **Web Server & Database**: Laragon (Apache/Nginx & MySQL 8.x/MariaDB)
* **Ekstensi PHP Aktif**: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`

---

## 🚀 Panduan Instalasi & Menjalankan di Laragon

### 1. Kloning / Buka Direktori Proyek
Pastikan folder proyek berada di dalam folder root Laragon (misal: `C:\laragon\www\PROJEK-RPL` atau di direktori kerja Anda):
```bash
cd "d:\SEMS 3\REKAYASA PERANGKAT LUNAK\PROJEK-RPL"
```

### 2. Instalasi Dependensi Backend (Composer)
```bash
composer install
```

### 3. Instalasi Dependensi Frontend & Kompilasi Aset (Tailwind CSS & Vite)
```bash
npm install
npm run build
```

### 4. Konfigurasi Environment (`.env`)
Salin file `.env.example` ke `.env` (jika belum ada) dan sesuaikan konfigurasi database Laragon:
```env
APP_NAME=SIPAFKA
APP_ENV=local
APP_KEY=base64:LHWjBo6liOTrAYnqgtu0vNbFmhjh99T7ttgLoRCPbyg=
APP_DEBUG=true
APP_TIMEZONE=Asia/Makassar
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipafka
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Buat Database & Jalankan Migrasi
Pastikan service MySQL di Laragon sudah dalam keadaan **"Start All"** (hijau). Buat database bernama `sipafka` (bisa lewat HeidiSQL di Laragon atau terminal), lalu jalankan:
```bash
php artisan migrate:fresh --force
```

### 6. Jalankan Database Seeder (Memuat Akun Demo & Fasilitas)
```bash
php artisan db:seed --force
```

### 7. Jalankan Server Aplikasi
Anda dapat menjalankannya langsung melalui artisan server:
```bash
php artisan serve
```
Buka browser dan akses: **`http://127.0.0.1:8000`**

---

## 🔑 Akun Demo Pengujian
Semua akun demo menggunakan **satu kata sandi yang sama**:
> **Password untuk semua akun:** `password123`

| Peran (Role) | Nama Lengkap | Identitas Akun (NIM / NIP / Email) | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Mahasiswa** | Adam Muzakir Eni | `mahasiswa@sipafka.test` atau `F1G125001` | Mencari fasilitas, mengajukan pinjam barang/lab, menampilkan QR Code, dan mengajukan pengembalian. |
| **Dosen** | Ilham Julian Efendi, S.T., M.T. | `dosen@sipafka.test` atau `198507152010121002` | Memeriksa pengajuan, menyetujui/menolak, scan QR penyerahan & pengembalian, periksa kondisi barang. |
| **Admin** | Administrator SIPAFKA | `admin@sipafka.test` atau `ADM-ILKOM-01` | Mengelola data fasilitas (CRUD barang & lab), memantau transaksi 6-tahap, riwayat arsip, kelola pengguna. |

---

## 📋 Penjelasan Fitur Setiap Role

### 1. Peran Mahasiswa
* **Katalog Fasilitas**: Melihat daftar barang dan lab dengan indikator stok aktual dan status perizinan.
* **Pengajuan Peminjaman Barang**: Memilih barang, menentukan kuantitas (tidak boleh melebihi stok tersedia), tanggal, jam mulai, durasi, dan tujuan.
* **Pengajuan Peminjaman Ruangan (Lab)**: Memilih lab, jumlah peserta, jadwal penggunaan, serta deteksi otomatis bentrok jadwal.
* **Peminjaman Saya**: Memantau status transaksi secara langsung (*Menunggu*, *Disetujui*, *Dipinjam*, *Ditolak*).
* **QR Code Peminjaman (15 Menit)**: Diterbitkan otomatis begitu pengajuan disetujui dosen, dilengkapi hitung mundur (*countdown*) real-time. Jika kedaluwarsa, tersedia tombol perbarui QR.
* **Pengajuan Pengembalian**: Menghasilkan QR Code pengembalian baru berdurasi 15 menit untuk ditunjukkan kepada dosen saat barang dikembalikan.
* **Riwayat Peminjaman**: Riwayat seluruh transaksi yang telah selesai maupun ditolak beserta catatan verifikasi.
* **Profil**: Mengubah profil diri dan memperbarui kata sandi.

### 2. Peran Dosen
* **Dashboard Dosen**: Statistik pengajuan masuk, menunggu keputusan, transaksi menunggu penyerahan, dan pengembalian.
* **Pemeriksaan Pengajuan**: Melihat rincian pemohon, kelayakan stok, status bentrok jadwal, lalu menentukan keputusan (**Setuju** atau **Tolak** dengan alasan wajib).
* **Pemindai QR Code Kamera**: Memanfaatkan kamera perangkat (webcam komputer / smartphone) melalui HTML5 Camera Scanner untuk memindai QR Code mahasiswa secara langsung.
* **Validasi Token Digital**: Sistem memeriksa keaslian token, masa berlaku 15 menit, jenis QR, dan status transaksi di backend.
* **Konfirmasi Penyerahan**: Mengubah status peminjaman menjadi `DIPINJAM` dan memotong stok barang tersedia secara atomik.
* **Konfirmasi Pengembalian**: Memeriksa kondisi barang (*Baik*, *Rusak*, *Tidak Lengkap*), mencatat jumlah unit yang kembali, catatan kondisi, memperbarui status menjadi `SELESAI`, dan memulihkan stok inventaris sesuai kondisi aktual.
* **Riwayat Keputusan**: Mencatat seluruh keputusan persetujuan dan riwayat pemindaian QR.

### 3. Peran Administrator
* **Dashboard Eksekutif**: Ringkasan statistik fasilitas, kapasitas, total stok, stok tersedia, grafik tren peminjaman bulanan, dan transaksi berjalan.
* **Kelola Fasilitas**:
  * Menambah, mengedit, dan menonaktifkan barang dan laboratorium.
  * Validasi integritas: fasilitas yang sedang aktif dipinjam tidak dapat dihapus dari database.
* **Monitoring Transaksi Berjalan**: Memantau 6 tahapan alur transaksi dari pengajuan, persetujuan, penyerahan, hingga pengembalian.
* **Riwayat & Arsip Transaksi**: Filter multi-kriteria berdasarkan rentang tanggal, jenis fasilitas, status, dan pencarian kata kunci.
* **Kelola Pengguna**: Direktori seluruh pengguna terdaftar di lingkungan prodi.

---

## 🔒 Fitur Keamanan & Arsitektur Sistem
1. **Keamanan Transaksi Database**: Menggunakan `DB::transaction()` dan pesimistic row locking (`lockForUpdate()`) untuk mencegah race-condition atau pemindaian ganda (*double spending token*).
2. **QR Code Dinamis**: Token digital di-generate secara acak berbasis entropy tinggi (`Str::random(24)`), berbatas waktu 15 menit (`expires_at`), dan seketika dinonaktifkan (`status = DIGUNAKAN`) setelah pemindaian berhasil.
3. **Pencegahan Bentrok Jadwal**: Peminjaman ruangan secara otomatis memeriksa irisan jam `(start < other_end) AND (end > other_start)` pada tanggal yang sama.
4. **Validasi Server-Side**: Semua input divalidasi ketat pada Controller Laravel; otorisasi dibatasi oleh `RoleMiddleware`.
5. **Enkripsi Kata Sandi**: Kata sandi di-hash menggunakan algoritma Bcrypt standar industri.
6. **Desain UI/UX Modern**: Mengikuti panduan warna Navy Blue (`#0c1a40`) dan Biru, tipografi Inter, kartu dengan sudut membulat, badge warna status yang konsisten, dan responsif untuk mobile maupun desktop.

---

## 🧪 Pengujian Otomatis (Automated Testing)
Seluruh fungsionalitas inti telah diuji menggunakan PHPUnit:
```bash
php artisan test
```
**Hasil Uji:** 6 test suites dan 38 assertions lulus (100% Passed).

---
&copy; 2026 SIPAFKA. Program Studi Ilmu Komputer, FMIPA Universitas Halu Oleo, Kendari.
