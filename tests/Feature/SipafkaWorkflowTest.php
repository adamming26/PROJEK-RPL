<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\QRCode;
use App\Services\QRCodeService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SipafkaWorkflowTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_halaman_beranda_dan_katalog_dapat_diakses_publik(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SIPAFKA');

        $responseFasilitas = $this->get('/fasilitas');
        $responseFasilitas->assertStatus(200);
    }

    public function test_autentikasi_dan_redireksi_sesuai_role(): void
    {
        // 1. Mahasiswa login
        $mahasiswa = User::where('email', 'mahasiswa@sipafka.test')->first();
        $this->assertNotNull($mahasiswa);

        $response = $this->post('/login', [
            'login' => 'mahasiswa@sipafka.test',
            'password' => 'password123',
        ]);
        $response->assertRedirect(route('mahasiswa.dashboard'));

        // 2. Proteksi role: Mahasiswa tidak boleh akses admin/dashboard
        $this->actingAs($mahasiswa);
        $forbidden = $this->get('/admin/dashboard');
        $forbidden->assertRedirect(route('mahasiswa.dashboard'));

        // 3. Dosen login
        $dosen = User::where('email', 'dosen@sipafka.test')->first();
        $this->actingAs($dosen);
        $dosenDashboard = $this->get('/dosen/dashboard');
        $dosenDashboard->assertStatus(200);

        // 4. Admin login
        $admin = User::where('email', 'admin@sipafka.test')->first();
        $this->actingAs($admin);
        $adminDashboard = $this->get('/admin/dashboard');
        $adminDashboard->assertStatus(200);
    }

    public function test_alur_lengkap_peminjaman_barang_hingga_pengembalian_dan_update_stok(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa@sipafka.test')->first();
        $dosen = User::where('email', 'dosen@sipafka.test')->first();

        // Buat barang khusus untuk pengujian
        $barang = Barang::create([
            'kode_barang' => 'TEST-01',
            'nama_barang' => 'Proyektor Uji Coba',
            'stok_total' => 3,
            'stok_tersedia' => 3,
            'kondisi' => 'BAIK',
            'status' => 'Aktif',
        ]);

        // 1. Mahasiswa mengajukan peminjaman
        $this->actingAs($mahasiswa);
        $responseAjukan = $this->post('/mahasiswa/pinjam', [
            'jenis_fasilitas' => 'BARANG',
            'id_barang' => $barang->id_barang,
            'jumlah' => 1,
            'tanggal_peminjaman' => Carbon::now()->addDay()->toDateString(),
            'jam_mulai' => '09:00',
            'durasi' => '2 Jam',
            'tujuan' => 'Praktikum Mandiri RPL',
        ]);
        $responseAjukan->assertRedirect(route('mahasiswa.peminjaman-saya'));

        $peminjaman = Peminjaman::where('id_barang', $barang->id_barang)->latest('id_peminjaman')->first();
        $this->assertNotNull($peminjaman);
        $this->assertEquals('MENUNGGU PERSETUJUAN', $peminjaman->status);

        // 2. Dosen menyetujui pengajuan
        $this->actingAs($dosen);
        $responseSetuju = $this->post("/dosen/pengajuan/{$peminjaman->id_peminjaman}/putuskan", [
            'keputusan' => 'DISETUJUI',
            'catatan' => 'Disetujui untuk kegiatan praktikum.',
        ]);
        $responseSetuju->assertRedirect(route('dosen.pengajuan-masuk'));

        $peminjaman->refresh();
        $this->assertEquals('DISETUJUI', $peminjaman->status);

        // Cek QR Code peminjaman terbit dan aktif
        $qrPeminjaman = $peminjaman->getActiveQrCode('PEMINJAMAN');
        $this->assertNotNull($qrPeminjaman);
        $this->assertEquals('AKTIF', $qrPeminjaman->status);
        $this->assertTrue($qrPeminjaman->isValid());

        // 3. Dosen memindai dan konfirmasi penyerahan barang
        $qrService = app(QRCodeService::class);
        $hasilSerah = $qrService->confirmPenyerahan($qrPeminjaman->token, $dosen);
        $this->assertTrue($hasilSerah['success']);

        $peminjaman->refresh();
        $barang->refresh();
        $qrPeminjaman->refresh();

        $this->assertEquals('DIPINJAM', $peminjaman->status);
        $this->assertEquals('DIGUNAKAN', $qrPeminjaman->status); // QR peminjaman langsung dinonaktifkan
        $this->assertEquals(2, $barang->stok_tersedia); // Stok berkurang dari 3 menjadi 2

        // 4. Mahasiswa mengajukan pengembalian barang
        $this->actingAs($mahasiswa);
        $responseKembali = $this->post("/mahasiswa/peminjaman/{$peminjaman->id_peminjaman}/kembalikan");
        $responseKembali->assertRedirect(route('mahasiswa.qr-code', $peminjaman->id_peminjaman));

        $peminjaman->refresh();
        $this->assertEquals('MENUNGGU PENGEMBALIAN', $peminjaman->status);

        $qrPengembalian = $peminjaman->getActiveQrCode('PENGEMBALIAN');
        $this->assertNotNull($qrPengembalian);
        $this->assertEquals('AKTIF', $qrPengembalian->status);

        // 5. Dosen memindai QR pengembalian & konfirmasi kondisi BAIK
        $this->actingAs($dosen);
        $hasilKembali = $qrService->confirmPengembalian(
            $qrPengembalian->token,
            $dosen,
            1,
            'BAIK',
            'Kondisi barang mulus dan kabel lengkap.'
        );
        $this->assertTrue($hasilKembali['success']);

        $peminjaman->refresh();
        $barang->refresh();
        $qrPengembalian->refresh();

        $this->assertEquals('SELESAI', $peminjaman->status);
        $this->assertEquals('DIGUNAKAN', $qrPengembalian->status); // QR pengembalian dinonaktifkan
        $this->assertEquals(3, $barang->stok_tersedia); // Stok dikembalikan utuh ke 3

        // Bersihkan data uji
        $peminjaman->delete();
        $barang->delete();
    }

    public function test_deteksi_otomatis_bentrok_jadwal_ruangan(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa@sipafka.test')->first();
        $ruangan = Ruangan::first();

        $qrService = app(QRCodeService::class);

        // Buat peminjaman ruangan pertama
        $peminjaman1 = Peminjaman::create([
            'kode_peminjaman' => 'TEST-RNG-01',
            'id_user' => $mahasiswa->id_user,
            'jenis_fasilitas' => 'RUANGAN',
            'id_ruangan' => $ruangan->id_ruangan,
            'jumlah' => 20,
            'tanggal_pengajuan' => Carbon::now()->toDateString(),
            'tanggal_peminjaman' => '2026-12-01',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'durasi' => '2 Jam',
            'tujuan' => 'Workshop Pemrograman Web',
            'status' => 'DISETUJUI',
        ]);

        // Cek bentrok dengan waktu tumpang tindih (09:00 - 11:00)
        $bentrok = $qrService->checkJadwalBentrok(
            $ruangan->id_ruangan,
            '2026-12-01',
            '09:00:00',
            '11:00:00'
        );

        $this->assertNotNull($bentrok);
        $this->assertEquals($peminjaman1->id_peminjaman, $bentrok->id_peminjaman);

        // Cek tidak bentrok untuk waktu setelahnya (10:00 - 12:00)
        $tidakBentrok = $qrService->checkJadwalBentrok(
            $ruangan->id_ruangan,
            '2026-12-01',
            '10:00:00',
            '12:00:00'
        );
        $this->assertNull($tidakBentrok);

        $peminjaman1->delete();
    }

    public function test_alur_lupa_dan_reset_password(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa@sipafka.test')->first();

        // 1. Buka halaman lupa password
        $response = $this->get('/lupa-password');
        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi?');

        // 2. Kirim permintaan verifikasi akun yang salah
        $responseSalah = $this->post('/lupa-password', [
            'email' => 'mahasiswa@sipafka.test',
            'nomor_induk' => 'NIM_SALAH_123',
        ]);
        $responseSalah->assertSessionHas('error');

        // 3. Kirim permintaan verifikasi yang benar
        $responseBenar = $this->post('/lupa-password', [
            'email' => $mahasiswa->email,
            'nomor_induk' => $mahasiswa->nomor_induk,
        ]);
        $responseBenar->assertRedirect();

        // 4. Periksa token di database
        $tokenRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $mahasiswa->email)
            ->first();
        $this->assertNotNull($tokenRecord);

        // 5. Buka halaman reset password dengan token
        $responseResetPage = $this->get('/reset-password/' . $tokenRecord->token . '?email=' . urlencode($mahasiswa->email));
        $responseResetPage->assertStatus(200);
        $responseResetPage->assertSee('Buat Kata Sandi Baru');

        // 6. Set kata sandi baru
        $responseUpdate = $this->post('/reset-password', [
            'token' => $tokenRecord->token,
            'email' => $mahasiswa->email,
            'password' => 'passwordBaru123',
            'password_confirmation' => 'passwordBaru123',
        ]);
        $responseUpdate->assertRedirect(route('login'));
        $responseUpdate->assertSessionHas('success');

        // 7. Coba login dengan password baru
        $mahasiswa->refresh();
        $this->assertTrue(Hash::check('passwordBaru123', $mahasiswa->password));

        // Kembalikan password ke default
        $mahasiswa->password = Hash::make('password123');
        $mahasiswa->save();
    }

    public function test_qr_code_kedaluwarsa_setelah_15_menit_dan_wajib_pengajuan_ulang(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa@sipafka.test')->first();
        $barang = Barang::first();

        // 1. Buat peminjaman disetujui dengan QR yang sudah kedaluwarsa (> 15 menit)
        $peminjaman = Peminjaman::create([
            'kode_peminjaman' => 'TEST-EXP-' . rand(100, 999),
            'id_user' => $mahasiswa->id_user,
            'jenis_fasilitas' => 'BARANG',
            'id_barang' => $barang->id_barang,
            'jumlah' => 1,
            'tanggal_pengajuan' => Carbon::now()->toDateString(),
            'tanggal_peminjaman' => Carbon::now()->toDateString(),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'durasi' => '2 Jam',
            'tujuan' => 'Uji Coba Kedaluwarsa QR',
            'status' => 'DISETUJUI',
        ]);

        $qrService = app(QRCodeService::class);
        $qr = QRCode::create([
            'id_peminjaman' => $peminjaman->id_peminjaman,
            'token' => 'QR-EXP-' . rand(1000, 9999),
            'jenis_qr' => 'PEMINJAMAN',
            'expires_at' => Carbon::now()->subMinutes(16), // Sudah lewat 15 menit
            'status' => 'AKTIF',
        ]);

        // 2. Validasi via service: harus mengembalikan status EXPIRED
        $validation = $qrService->validateToken($qr->token);
        $this->assertFalse($validation['valid']);
        $this->assertEquals('EXPIRED', $validation['status_code']);

        // 3. Mahasiswa mengakses halaman QR: harus tampil KEDALUWARSA dan tombol Ajukan Peminjaman Ulang
        $this->actingAs($mahasiswa);
        $response = $this->get(route('mahasiswa.qr-code', $peminjaman->id_peminjaman));
        $response->assertStatus(200);
        $response->assertSee('KEDALUWARSA');
        $response->assertSee('Ajukan Peminjaman Ulang');
        $response->assertDontSee('Perbarui / Buat Ulang QR Code Baru');

        // 4. Jika coba generate QR baru, sistem harus menolak dan mengarahkan ke form pengajuan ulang
        $responseRenew = $this->post(route('mahasiswa.generate-qr', $peminjaman->id_peminjaman));
        $responseRenew->assertRedirect(route('mahasiswa.form-peminjaman', ['BARANG', $barang->id_barang]));
        $responseRenew->assertSessionHas('error');

        $peminjaman->delete();
    }
}
