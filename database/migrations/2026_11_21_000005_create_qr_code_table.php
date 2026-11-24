<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_code', function (Blueprint $table) {
            $table->id('id_qr');
            $table->foreignId('id_peminjaman')->constrained('peminjaman', 'id_peminjaman')->onDelete('cascade');
            $table->string('token', 100)->unique()->index();
            $table->enum('jenis_qr', ['PEMINJAMAN', 'PENGEMBALIAN']);
            $table->timestamp('expires_at');
            $table->enum('status', ['AKTIF', 'DIGUNAKAN', 'KEDALUWARSA', 'NONAKTIF'])->default('AKTIF');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_code');
    }
};
