<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruangan', function (Blueprint $table) {
            $table->id('id_ruangan');
            $table->string('kode_ruangan')->unique();
            $table->string('nama_ruangan');
            $table->integer('kapasitas')->default(30);
            $table->string('lokasi');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['Tersedia', 'Perlu Izin', 'Aktif', 'Nonaktif'])->default('Tersedia');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};
