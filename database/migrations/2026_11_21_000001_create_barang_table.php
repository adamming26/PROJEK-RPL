<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('kategori')->default('Perangkat Pendukung Perkuliahan');
            $table->text('deskripsi')->nullable();
            $table->integer('stok_total')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->enum('kondisi', ['BAIK', 'RUSAK', 'TIDAK LENGKAP'])->default('BAIK');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
