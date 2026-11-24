<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('id_peminjaman');
            $table->string('kode_peminjaman')->unique();
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->enum('jenis_fasilitas', ['BARANG', 'RUANGAN']);
            $table->foreignId('id_barang')->nullable()->constrained('barang', 'id_barang')->onDelete('set null');
            $table->foreignId('id_ruangan')->nullable()->constrained('ruangan', 'id_ruangan')->onDelete('set null');
            $table->integer('jumlah')->default(1);
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_peminjaman');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('durasi')->default('2 Jam');
            $table->dateTime('waktu_pengembalian_rencana')->nullable();
            $table->text('tujuan');
            $table->enum('status', [
                'MENUNGGU PERSETUJUAN',
                'DISETUJUI',
                'DITOLAK',
                'MENUNGGU PENYERAHAN',
                'DIPINJAM',
                'MENUNGGU PENGEMBALIAN',
                'SELESAI',
                'KEDALUWARSA'
            ])->default('MENUNGGU PERSETUJUAN');
            $table->text('catatan_penolakan')->nullable();
            $table->foreignId('diserahkan_oleh')->nullable()->constrained('users', 'id_user')->onDelete('set null');
            $table->timestamp('diserahkan_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
