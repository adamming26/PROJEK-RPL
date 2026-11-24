<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persetujuan extends Model
{
    use HasFactory;

    protected $table = 'persetujuan';
    protected $primaryKey = 'id_persetujuan';

    protected $fillable = [
        'id_peminjaman',
        'id_dosen',
        'keputusan',
        'catatan',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
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
