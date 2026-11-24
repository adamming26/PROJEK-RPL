<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id('id_pengembalian');
            $table->foreignId('id_peminjaman')->constrained('peminjaman', 'id_peminjaman')->onDelete('cascade');
            $table->foreignId('id_dosen')->constrained('users', 'id_user')->onDelete('cascade');
            $table->timestamp('waktu_kembali')->useCurrent();
            $table->integer('jumlah_kembali')->default(1);
            $table->enum('kondisi_barang', ['BAIK', 'RUSAK', 'TIDAK LENGKAP'])->default('BAIK');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
