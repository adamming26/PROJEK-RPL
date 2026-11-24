<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';
    protected $primaryKey = 'id_pengembalian';

    protected $fillable = [
        'id_peminjaman',
        'id_dosen',
        'waktu_kembali',
        'jumlah_kembali',
        'kondisi_barang',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'waktu_kembali' => 'datetime',
        ];
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'id_dosen', 'id_user');
    }
}
